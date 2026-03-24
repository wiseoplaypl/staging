<?php
/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class Webpgenerator
 */
class Webpgenerator extends Module
{
    public $tabs = array(
        array(
            'name' => 'Regenerate WebP',
            'class_name' => 'AdminWebpgeneratorRegenerate',
            'visible' => true,
            'parent_class_name' => 'AdminAdvancedParameters',
        ),
        array(
            'name' => 'WebP Config',
            'class_name' => 'AdminWebpgeneratorConfig',
            'visible' => true,
            'parent_class_name' => 'AdminParentModulesSf',
        ),
    );

    public function __construct()
    {
        $this->name = 'webpgenerator';
        $this->tab = 'administration';
        $this->version = '1.0.23';
        $this->author = 'PrestaChamps';
        $this->need_instance = 1;
        $this->module_key = '60ec7e647642dd601875b3b04e1d318e';

        parent::__construct();

        $this->displayName = $this->l('WebPGenerator');
        $this->description = $this->l('WebP lossless images are 26% smaller in size compared to PNGs.');
        $this->description .=
            $this->l(
                'WebP lossy images are 25-34% smaller than comparable JPEG images at equivalent SSIM quality index.'
            );
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        require_once $this->getLocalPath() . 'vendor/autoload.php';
    }

    public function copyOverrideFolder()
    {
        if ((bool)version_compare(_PS_VERSION_, '8.1', '>=')) {
            $version_override_folder = _PS_MODULE_DIR_.$this->name.'/override_81';
        } else {
            $version_override_folder = _PS_MODULE_DIR_.$this->name.'/override_17';
        }

        $override_folder = _PS_MODULE_DIR_.$this->name.'/override';

        if (file_exists($override_folder) && is_dir($override_folder)) {
            $this->recursiveRmdir($override_folder);
        }

        if (is_dir($version_override_folder)) {
            $this->copyDir($version_override_folder, $override_folder);
        }

        return true;
    }

    protected function copyDir($src, $dst)
    {
        if (is_dir($src)) {
            $dir = opendir($src);
            @mkdir($dst);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($src.'/'.$file)) {
                        $this->copyDir($src.'/'.$file, $dst.'/'.$file);
                    } else {
                        copy($src.'/'.$file, $dst.'/'.$file);
                    }
                }
            }
            closedir($dir);
        }
    }

    protected function recursiveRmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        $this->recursiveRmdir($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /**
     * Install the required tabs, configs and stuff
     *
     * @return bool
     * @throws PrestaShopException
     *
     * @throws PrestaShopDatabaseException
     * @since 0.0.1
     *
     */
    public function install()
    {
        $this->copyOverrideFolder();

        Configuration::updateValue(WebPGeneratorConfig::CRON_KEY, bin2hex(openssl_random_pseudo_bytes(32)));
        Configuration::updateValue(WebPGeneratorConfig::CONFIG_COMMON_QUALITY, 70);
        Configuration::updateValue(WebPGeneratorConfig::CONFIG_COMMON_META_DATA, 'none');
        Configuration::updateValue(WebPGeneratorConfig::CONFIG_COMMON_METHOD, 3);
        Configuration::updateValue(WebPGeneratorConfig::CONFIG_COMMON_LOW_MEMORY, 0);
        Configuration::updateValue(WebPGeneratorConfig::CONFIG_COMMON_LOSSLESS, 0);
        Configuration::updateValue(WebPGeneratorConfig::CONVERTER_TO_USE, 'cwebp');
        Configuration::updateValue(WebPGeneratorConfig::CONVERTER_CWEBP_USE_NICE, 1);
        Configuration::updateValue(WebPGeneratorConfig::CONVERTER_CWEBP_TRY_COMMON_SYSTEM_PATHS, 1);
        Configuration::updateValue(WebPGeneratorConfig::CONVERTER_CWEBP_TRY_SUPPLIED_BINARY, 1);
        Configuration::updateValue(WebPGeneratorConfig::CONVERTER_CWEBP_AUTO_FILTER, 0);
        Configuration::updateValue(WebPGeneratorConfig::DEMO_MODE, 1);
        Configuration::updateValue(WebPGeneratorConfig::CONVERTER_CWEBP_CMD_OPTIONS, '-short');

        if (!self::isPs17()) {
            /**
             * Create tabs for retro-compatibility
             */
            foreach ($this->tabs as $tabItem) {
                $tab = new Tab();
                $tab->name = array();
                foreach (Language::getLanguages(true) as $lang) {
                    $tab->name[$lang['id_lang']] = $tabItem['name'];
                }
                $tab->class_name = $tabItem['class_name'];
                $tab->id_parent = Tab::getIdFromClassName($tabItem['parent_class_name']);
                $tab->module = $this->name;
                $tab->position = 0;
                $tab->active = $tabItem['visible'] ? 1 : 0;
                $tab->save();
            }
        }
        try {
            // Give proper permissions for the included binaries
            $recursiveIterator = new RecursiveDirectoryIterator(
                $this->getLocalPath() . '/vendor/rosell-dk/webp-convert/src/Converters/Binaries'
            );
            foreach ($recursiveIterator as $item) {
                /**
                 * @var $item SplFileInfo
                 */
                if ($item->isFile()) {
                    @chmod($item->getPathname(), 0755);
                }
            }
        } catch (Exception $exception) {
            PrestaShopLogger::addLog(
                empty($exception->getMessage()) ? 'Unknown error' : $exception->getMessage(),
                1,
                $exception->getCode(),
                'webpgenerator',
                null,
                true
            );
        }

        return parent::install() &&
            $this->registerHook('actionHtaccessCreate') &&
            $this->registerHook('actionOnImageResizeAfter') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayAdminProductsExtra');
    }

    /**
     * Check if the current PrestaShop installation is version 1.7 or below
     *
     * @return bool
     */
    public static function isPs17()
    {
        return (bool)version_compare(_PS_VERSION_, '1.7', '>=');
    }

    /**
     * Redirect to the custom config controller
     *
     * @throws PrestaShopException
     */
    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminWebpgeneratorConfig'));
    }

    public function hookDisplayHeader()
    {
        if (Configuration::get(WebPGeneratorConfig::LOAD_POLYFILL) == '1') {
            return $this->context->smarty->fetch($this->local_path . '/views/templates/hook/_polyfill.tpl');
        }

        return '';
    }

    public function hookActionOnImageResizeAfter($params)
    {
        try {
            $source = $params['dst_file'];
            $destination = str_replace(array('.jpg', '.png', '.JPG', '.jpeg'), '.webp', $params['dst_file']);
            \WebPConvert\WebPConvert::convert($source, $destination, WebPGeneratorConfig::getConverterSettings());
        } catch (Exception $exception) {
            PrestaShopLogger::addLog(
                empty($exception->getMessage()) ? 'Unknown error' : $exception->getMessage(),
                1,
                $exception->getCode(),
                'WebP Convert',
                null,
                true
            );
        }
    }

    public function hookActionHtaccessCreate()
    {
        $webpRules = new \PrestaChamps\WebPGenerator\Factories\HtaccessRulesFactory();
        $webpRules->generateProductImageEntries();
        $webpRules->addToHtaccess();
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        if ('AdminProducts' == $this->context->controller->controller_name) {
            Media::addJsDef(
                array(
                    'ajaxUrl' => $this->context->link->getAdminLink('AdminWebpgeneratorRegenerate'),
                )
            );

            if (version_compare(_PS_VERSION_, '1.7.7', '<') === true) {
                $this->context->controller->addJquery();
            }

            $this->context->controller->addJS($this->_path . 'views/js/regenerateProductsExtra.js');
            $this->context->controller->addCSS($this->_path . 'views/css/toastr.css');
            $this->context->controller->addJS($this->_path . 'views/js/toastr.min.js');
        }
    }

    /**
     * @param $params
     *
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        $productId = isset($params['id_product']) ? $params['id_product'] : Tools::getValue('id_product');
        $productImageId = array();
        if (Validate::isLoadedObject($product = new Product($productId))) {
            if (version_compare(_PS_VERSION_, '1.7', '<') === true) {
                $productImages = $product->getImages((int)$this->context->cookie->id_lang);
            } else {
                $productImages = Image::getImages($this->context->language->id, $params['id_product']);
            }
            foreach ($productImages as $image) {
                $productImageId[] = $image['id_image'];
            }
            $this->context->smarty->assign(array(
                'productImages' => json_encode($productImageId),
                'regenerateConfigLink' => $this->context->link->getAdminLink('AdminWebpgeneratorConfig'),
            ));

            return $this->display(__FILE__, 'views/templates/hook/_product.tpl');
        }
    }
}
