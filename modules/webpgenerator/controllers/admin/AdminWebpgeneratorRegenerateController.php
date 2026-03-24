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

use \PrestaChamps\WebPGenerator\Services\ImageResizeService;
use \PrestaChamps\WebPGenerator\Services\ImageDeleteService;

/**
 * Class AdminWebpgeneratorRegenerateController
 *
 */
class AdminWebpgeneratorRegenerateController extends ModuleAdminController
{
    public $bootstrap = true;
    const IMAGE_TYPE_SINGULAR = array(
        'category' => 'categories',
        'manufacturer' => 'manufacturers',
        'supplier' => 'suppliers',
        'product' => 'products',
        'store' => 'stores',
        'others' => 'others',
    );

    const IMG_DIR = array(
        array('type' => 'category', 'dir' => _PS_CAT_IMG_DIR_),
        array('type' => 'manufacturer', 'dir' => _PS_MANU_IMG_DIR_),
        array('type' => 'supplier', 'dir' => _PS_SUPP_IMG_DIR_),
        array('type' => 'product', 'dir' => _PS_PROD_IMG_DIR_),
        array('type' => 'store', 'dir' => _PS_STORE_IMG_DIR_),
    );

    /**
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function initContent()
    {
        $images = $this->getImages();

        $productProgress = WebPGeneratorConfig::getRegenerationProgress('product');
        $categoryProgress = WebPGeneratorConfig::getRegenerationProgress('category');
        $supplierProgress = WebPGeneratorConfig::getRegenerationProgress('supplier');
        $manufacturerProgress = WebPGeneratorConfig::getRegenerationProgress('manufacturer');
        $storeProgress = WebPGeneratorConfig::getRegenerationProgress('store');
        $otherProgress = WebPGeneratorConfig::getRegenerationProgress('other');

        $imagesToRegenerate = array(
            array(
                'imageType'        => 'product',
                'imageCount'       => count($images['product']['todo']),
                'fileSystemSource' => false,
            ),
            array(
                'imageType'        => 'category',
                'imageCount'       => count($images['category']['todo']),
                'fileSystemSource' => false,
            ),
            array(
                'imageType'        => 'supplier',
                'imageCount'       => count($images['supplier']['todo']),
                'fileSystemSource' => false,
            ),
            array(
                'imageType'        => 'store',
                'imageCount'       => count($images['store']['todo']),
                'fileSystemSource' => false,
            ),
            array(
                'imageType'        => 'manufacturer',
                'imageCount'       => count($images['manufacturer']['todo']),
                'fileSystemSource' => false,
            ),
            array(
                'imageType'        => 'cms',
                'imageCount'       => count($images['others']['todo']),
                'fileSystemSource' => true,
            ),
            array(
                'imageType'        => 'theme',
                'imageCount'       => count($images['others']['todo']),
                'fileSystemSource' => true,
            ),
            array(
                'imageType'        => 'modules',
                'imageCount'       => count($images['others']['todo']),
                'fileSystemSource' => true,
            ),
        );


        $this->context->smarty->assign(array(
            'productImageCount' => count($images['product']['todo']),
            'categoryImageCount' => count($images['category']['todo']),
            'supplierImageCount' => count($images['supplier']['todo']),
            'manufacturerImageCount' => count($images['manufacturer']['todo']),
            'storeImageCount' => count($images['store']['todo']),
            'otherImageCount' => count($images['others']['todo']),
            'productProgress' => WebPGeneratorConfig::getRegenerationProgress('product'),
            'categoryProgress' => WebPGeneratorConfig::getRegenerationProgress('category'),
            'supplierProgress' => WebPGeneratorConfig::getRegenerationProgress('supplier'),
            'manufacturerProgress' => WebPGeneratorConfig::getRegenerationProgress('manufacturer'),
            'storeProgress' => WebPGeneratorConfig::getRegenerationProgress('store'),
            'otherProgress' => WebPGeneratorConfig::getRegenerationProgress('other'),
            'imagesToRegenerate' => $imagesToRegenerate,
        ));

        $this->addCSS($this->module->getLocalPath() . 'views/css/toastr.css');
        $this->addJS($this->module->getLocalPath() . 'views/js/toastr.min.js');
        $this->addJS($this->module->getLocalPath() . 'views/js/ajaxq.js');
        $this->addJS($this->module->getLocalPath() . 'views/js/regenerate.js');

        $this->addCSS($this->module->getLocalPath() . 'views/css/admin-tabs.css');
        $this->addJS($this->module->getLocalPath() . 'views/js/admin-tabs.js');

        if (!function_exists('mime_content_type')) {
            $this->errors[] = $this->l('Please enable the PHP fileinfo extension');
        }
        Media::addJsDef(array(
            'ajaxUrl' => $this->context->link->getAdminLink($this->controller_name),
            'imageList' => $this->getImages(),
            'productProgress' => $productProgress,
            'categoryProgress' => $categoryProgress,
            'supplierProgress' => $supplierProgress,
            'manufacturerProgress' => $manufacturerProgress,
            'storeProgress' => $storeProgress,
            'otherProgress' => $otherProgress,
            'otherImagesUrl' => $this->context->link->getAdminLink($this->controller_name, null, null),
            'imgBasePath' => _PS_ROOT_DIR_,
        ));


        $this->context->smarty->assign(array(
            'moduleVersion'     => $this->module->version,
            'modulePath'        => $this->module->getPathUri(),
        ));

        $tabs = array(
            array(
                'id'            => 'submenu-settings',
                'title'         => $this->l('Settings'),
                'content'       => false,
                'isActive'      => false,
                'icon'          => 'icon-wrench',
                'subTabs'       => array(
                    array(
                        'aHref'         => $this->context->link->getAdminLink('AdminWebpgeneratorConfig') . '#configuration',
                        'id'            => 'configuration',
                        'title'         => $this->l('Configuration'),
                        'content'       => false,
                        'isActive'      => false,
                        'icon'          => 'icon-chevron-right',
                    ),
                    array(
                        'aHref'         => $this->context->link->getAdminLink('AdminWebpgeneratorConfig') . '#ewww',
                        'id'            => 'ewww',
                        'title'         => $this->l('EWWW cloud convert'),
                        'content'       => false,
                        'isActive'      => false,
                        'icon'          => 'icon-chevron-right',
                    ),
                ),
            ),
            array(
                'id'            => 'submenu-image-regeneration',
                'title'         => $this->l('Image regeneration'),
                'content'       => false,
                'isActive'      => true,
                'icon'          => 'icon-refresh',
                'subTabs'       => array(
                    array(
                        'id'            => 'regenerate',
                        'title'         => $this->l('Regenarate WebP images'),
                        'content'       => $this->context->smarty->fetch($this->getTemplatePath() . 'regenerate.tpl'),
                        'isActive'      => true,
                        'icon'          => 'icon-chevron-right',
                    ),
                    array(
                        'id'            => 'custom-regenerate',
                        'title'         => $this->l('Custom regenerate'),
                        'content'       => $this->context->smarty->fetch($this->getTemplatePath() . 'custom-regenerate.tpl'),
                        'isActive'      => false,
                        'icon'          => 'icon-chevron-right',
                    ),
                ),
            ),
            array(
                'id'            => 'cronjobs',
                'title'         => $this->l('Automatic Generation (Cron Jobs)'),
                'content'       => $this->context->smarty->fetch($this->getTemplatePath() . 'cronjobs.tpl'),
                'isActive'      => false,
                'icon'          => 'icon-clock-o',
            ),
            array(
                'id'            => 'banner',
                'title'         => $this->l('Documentation'),
                'content'       => $this->context->smarty->fetch($this->getTemplatePath() . 'banner.tpl'),
                'isActive'      => false,
                'icon'          => 'icon-file-text',
            ),
        );

        $this->context->smarty->assign('configTabs', $tabs);
        $this->content .= $this->context->smarty->fetch($this->getTemplatePath() . 'configure.tpl');

        parent::initContent();
    }

    /**
     * @throws PrestaShopException
     */
    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_btn['config'] = array(
            'short' => $this->l('Configure'),
            'href' => $this->context->link->getAdminLink('AdminWebpgeneratorConfig') . '#configuration',
            'icon' => 'process-icon-configure',
            'desc' => $this->l('WebP Configuration'),
        );
        $this->page_header_toolbar_btn['custom-regenerate'] = array(
            'href' => '#',
            'icon' => 'process-icon-refresh',
            'desc' => $this->l('Custom regenerate'),
        );
        $this->page_header_toolbar_btn['delete-webp-images'] = array(
            'href' => '#',
            'icon' => 'process-icon-delete',
            'desc' => $this->l('Delete all WebP images'),
        );
    }

    /**
     * @throws PrestaShopDatabaseException|PrestaShopException
     */
    public function ajaxProcessRegenerate()
    {
        $baseType = (string)Tools::getValue('type');
        $type = !in_array($baseType, array('others', 'cms', 'theme', 'modules'), true) ? ImageType::getImagesTypes(self::IMAGE_TYPE_SINGULAR[$baseType]) : "";
        $currentIndex = (int)Tools::getValue('currentIndex', 0);
        $image = Tools::getValue('image');

        if (in_array($baseType, array('others', 'cms', 'theme', 'modules'), true)) {
            $image = utf8_decode($image);
        }
        
        try {
            if ($baseType == 'product') {
                $result = ImageResizeService::resizeProductImage($image, $type);
            } elseif (in_array($baseType, array('others', 'cms', 'theme', 'modules'), true)) {
                $result = ImageResizeService::convert($image);
            } else {
                $result = ImageResizeService::resizeOtherImage($image, $baseType, $type);
            }
            if (!$result) {
                throw new RuntimeException("Can't resize image");
            }
        } catch (Exception $exception) {
            $this->ajaxDie(array('success' => false, 'error' => $exception->getMessage()));
        }
        WebPGeneratorConfig::updateRegenerationProgress($baseType, $currentIndex);
        $this->ajaxDie(array(
            'success' => true,
            'error' => null,
            'current_index' => WebPGeneratorConfig::getRegenerationProgress($baseType),
        ));
    }

    /**
     * @throws PrestaShopException
     */
    public function ajaxProcessDelete()
    {
        try {
            ImageDeleteService::clearWebPImages(_PS_ROOT_DIR_);
            WebPGeneratorConfig::updateRegenerationProgress('product', -1);
            WebPGeneratorConfig::updateRegenerationProgress('category', -1);
            WebPGeneratorConfig::updateRegenerationProgress('supplier', -1);
            WebPGeneratorConfig::updateRegenerationProgress('store', -1);
            WebPGeneratorConfig::updateRegenerationProgress('manufacturer', -1);
        } catch (Exception $exception) {
            $this->ajaxDie(array('success' => false, 'error' => $exception->getMessage()));
        }

        $this->ajaxDie(array('success' => true, 'error' => null));
    }

    /**
     * Returns an ajax response
     *
     * @param null $value
     * @param null $controller
     * @param null $method
     * @param int  $statusCode
     *
     * @throws PrestaShopException
     */
    public function ajaxDie($value = null, $controller = null, $method = null, $statusCode = 200)
    {
        header('Content-Type: application/json');
        if (!is_scalar($value)) {
            $value = json_encode($value);
        }

        http_response_code($statusCode);
        parent::ajaxDie($value, $controller, $method);
    }

    protected function getImages()
    {
        $images = Image::getAllImages();
        $list = array();
        $imageTypes = self::IMG_DIR;

        $active_languages = Language::getLanguages(true);

        $patterns = array();
        $patterns[] = '(^\d.*\.jpg$)';
        
        foreach ($active_languages as $language) {
            $patterns[] = '(^' . $language['iso_code'] . '.*\.jpg$)';
        }

        // if (version_compare(phpversion(), '8.0.0', '<') === true) {
        //     $master_pattern = implode($patterns, '|');
        // }else{
        //     $master_pattern = implode('|', $patterns);
        // }

        $master_pattern = implode('|', $patterns);

        foreach ($imageTypes as $imageType) {
            $list[$imageType['type']] = array('todo' => array(), 'done' => array(), 'errors' => array());
            if ($imageType['type'] === 'product') {
                foreach ($images as $img) {
                    $list['product']['todo'][] = $img['id_image'];
                }
            } else {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($imageType['dir']));
                foreach ($iterator as $filename) {
                    /**
                     * @var $filename SplFileInfo
                     */
                    if (preg_match('/' . $master_pattern . '/', $filename->getBasename())) {
                        $list[$imageType['type']]['todo'][] = $filename->getBasename();
                    }
                }
            }
        }

        $list['others'] = array();
        $list['others']['todo'] = array();

        $list['cms'] = array();
        $list['cms']['todo'] = array();

        $list['theme'] = array();
        $list['theme']['todo'] = array();

        return $list;
    }

    /**
     * Maps the correct path to an 'other' image type
     */
    protected function typePathMapper($type)
    {
        $mapping = array(
            'others' => array(),
            'modules' => array(_PS_MODULE_DIR_),
            'cms' => array(_PS_IMG_DIR_ . '/cms'),
            'theme' => array(_PS_THEME_DIR_),
        );

        return $mapping[$type];
    }

    public function ajaxProcessOtherImages()
    {
        $skipExisting = filter_var(Tools::getValue('skipExisting', false), FILTER_VALIDATE_BOOLEAN);
        $type = Tools::getValue('type');
        $this->ajaxDie($this->getOtherImages($type, $this->typePathMapper($type), $skipExisting));
    }

    /**
     * @param bool $skipExisting
     *
     * @return array
     */
    protected function getOtherImages($type, $path, $skipExisting = true)
    {
        $list = array();

        $list[$type] = array();
        $list[$type]['todo'] = array();

        $excludePaths = array(
            _PS_IMG_DIR_ . 'admin',
            _PS_CAT_IMG_DIR_,
            _PS_PROD_IMG_DIR_,
            _PS_MANU_IMG_DIR_,
            _PS_STORE_IMG_DIR_,
            _PS_SUPP_IMG_DIR_,
            _PS_TMP_IMG_DIR_,
            "*/logo.png",
            "*/logo.jpg",
        );

        foreach (\Nette\Utils\Finder::findFiles('*.jpg', '*.png', '*.JPG', '*.jpeg')->exclude($excludePaths)->from($path) as $file) {
            $isInExcluded = false;
            /**
             * @var $file SplFileInfo
             */
            if (!$file->isFile() || !$file->isReadable() || $file->isLink()) {
                continue;
            }

            $webPVersion = str_replace(
                ".{$file->getExtension()}",
                '.webp',
                $file->getPathname()
            );
            if ($skipExisting && file_exists($webPVersion)) {
                $isInExcluded = true;
            }
            foreach ($excludePaths as $excludePath) {
                if ($isInExcluded) {
                    continue;
                }
                if (strpos($file->getPathname(), $excludePath) === 0) {
                    $isInExcluded = true;
                }
            }
            if (!$isInExcluded) {
                /**
                 * @var $file SplFileInfo
                 */
                $list[$type]['todo'][] = utf8_encode($file->getPathname());
            }
        }

        return $list;
    }
}
