<?php
/**
 * SeoAltImages
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright Copyright 2018 © FMM Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  FMM Modules
 * @package   SeoAltImages
*/

if (!defined('_PS_VERSION_')) {
	exit;
}
if (!defined('_MYSQL_ENGINE_')) {
	define('_MYSQL_ENGINE_', 'MyISAM'); 
}
include_once(dirname(__FILE__).'/classes/SeoAltImagesModel.php');
class SeoAltImages extends Module {

	public function __construct()
	{
		$this->name = 'seoaltimages';
		$this->tab = 'seo';
		$this->version = '1.0.0';
		$this->author = 'FMM Modules';
		$this->displayName = $this->l('SEO Alt Images');
		$this->description = $this->l('Alternate text for product images for better SEO.');
		$this->module_key = '61bdc2bfd39e4c38906bf75b79b8b142';
		$this->bootstrap = true;
		$this->tabClass = 'AdminSeoAlt';
		$this->author_address = '0xcC5e76A6182fa47eD831E43d80Cd0985a14BB095';
		parent::__construct();
	}

	public function install()
	{
		return parent::install()
		&& $this->registerHook('displaySeoAltImages')
		&& $this->installTab()
		&& $this->installDb();
	}
	
	public function uninstall()
	{
		if (!parent::uninstall()) {
			return false;
		}
		$this->uninstallTab();
		$this->dropDatabase();
		return true;
	}

	private function installDb()
	{
		$return = true;
		$return = Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'seoaltimages` (
				`id_seoaltimages` int(10) NOT NULL auto_increment,
				`rule` varchar(255) NOT NULL,
				`title` varchar(255) NOT NULL,
				`type` int(10) unsigned NOT NULL,
				`status` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id_seoaltimages`))');
		$return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'seoaltimages_shop` (
                    `id_seoaltimages` int(10) NOT NULL,
                    `id_shop` int(10) NOT NULL,
                    PRIMARY KEY (`id_seoaltimages`, `id_shop`),
                    KEY `id_shop` (`id_shop`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
		$return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'seoaltimages_lang` (
                    `id_seoaltimages` int(10) NOT NULL,
                    `id_lang` int(10) NOT NULL,
                    PRIMARY KEY (`id_seoaltimages`, `id_lang`),
                    KEY `id_shop` (`id_lang`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
		$return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'seoaltimages_products` (
                    `id_seoaltimages_products` int(10) NOT NULL auto_increment,
                    `id_seoaltimages` int(10) NOT NULL,
                    `id_product` int(10) NOT NULL,
                    PRIMARY KEY (`id_seoaltimages_products`, `id_seoaltimages`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
		$return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'seoaltimages_categories` (
                    `id_seoaltimages_categories` int(10) NOT NULL auto_increment,
                    `id_seoaltimages` int(10) NOT NULL,
                    `id_category` int(10) NOT NULL,
                    PRIMARY KEY (`id_seoaltimages_categories`, `id_seoaltimages`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
		return $return;
	}

	private function dropDatabase()
	{
		return (Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'seoaltimages`')
				&& Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'seoaltimages_shop`')
				&& Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'seoaltimages_lang`')
				&& Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'seoaltimages_products`')
				&& Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'seoaltimages_categories`'));
	}

	private function installTab()
	{
		$tab = new Tab();
		$tab->class_name = $this->tabClass;
		$tab->id_parent = 0;
		$tab->module = $this->name;
		$tab->name[(int)(Configuration::get('PS_LANG_DEFAULT'))] = $this->l('SEO Alt Images');
		$tab->add();
		$tab_i = new Tab();
		$tab_i->class_name = 'AdminSeoAltImages';
		$tab_i->id_parent = Tab::getIdFromClassName($this->tabClass);
		$tab_i->module = $this->name;
		$tab_i->name[(int)(Configuration::get('PS_LANG_DEFAULT'))] = $this->l('SEO Alt Images');
		$tab_i->add();
		return true;
	}
	
	public function uninstallTab()
	{
		$id_tab = (int)Tab::getIdFromClassName('AdminSeoAltImages');
		$id_tab_ii = (int)Tab::getIdFromClassName($this->tabClass);
		if ($id_tab_ii) {
			$tab = new Tab($id_tab);
			$tab_ii = new Tab($id_tab_ii);
			$tab->delete();
			return $tab_ii->delete();
		}
		else {
			return true;
		}
	}

	public function getContent()
    {
		$this->html = $this->display(__FILE__, 'views/templates/hook/info.tpl');
        return $this->postProcess().$this->html.$this->renderForm();
    }
	
	public function renderForm()
    {
		if (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=') == true) {
            $status_admin = array(
                'type' => 'switch',
                'label' => $this->l('Enable SEO Alt Images'),
                'name' => 'SEOALTIMG_ENABLE',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'seo_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                        ),
                    array(
                        'id' => 'seo_off',
                        'value' => 0,
                        'label' => $this->l('No')
                        )
                    ),
                );
        } else {
            $status_admin = array(
                'type' => 'radio',
                'label' => $this->l('Enable SEO Alt Images'),
                'name' => 'SEOALTIMG_ENABLE',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled')
                        ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled')
                        )
                    ),
                );
        }
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    $status_admin
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSeoAltImages';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
	}
	
	public function getConfigFieldsValues()
    {
        $fields = array();
        $fields['SEOALTIMG_ENABLE'] = (int)Configuration::get('SEOALTIMG_ENABLE');
        return $fields;
    }
	
	public function postProcess()
    {
        if (Tools::isSubmit('submitSeoAltImages')) {
            Configuration::updateValue('SEOALTIMG_ENABLE', (int)Tools::getValue('SEOALTIMG_ENABLE'));
            return $this->displayConfirmation($this->l('The settings have been updated.'));
        }
        return '';
    }
	
	public static function getProductName($product_id)
	{
		$context = Context::getContext();
		$id_lang = (int)$context->language->id;
		$ptitle = Db::getInstance()->getRow('SELECT l.`name` AS `pname` 
			FROM `'._DB_PREFIX_.'product` AS p
			INNER JOIN `'._DB_PREFIX_.'product_lang` AS l ON (l.`id_product` = p.`id_product`)
			AND l.`id_lang` = '.(int)$id_lang.' WHERE p.`id_product` = '.(int)$product_id );
		return $ptitle['pname'];
	}
	
	public static function getProductCategory($product_id)
	{
		$context = Context::getContext();
		$id_lang = (int)$context->language->id;
		$category_name = Db::getInstance()->getRow('SELECT c.`name` AS `cname` 
			FROM `'._DB_PREFIX_.'product` AS p
			INNER JOIN `'._DB_PREFIX_.'category_lang` AS c ON (p.`id_category_default` = c.`id_category`)
			WHERE c.id_lang = '.(int)$id_lang.' AND p.`id_product` = '.(int)$product_id );
		return $category_name['cname'];
	}
	
	public static function fmeProductTitleByType($product_id, $type)
	{
		$fmeTitle = '';
		$context = Context::getContext();
		$id_lang = (int)$context->language->id;
		$FME_PRODUCT_META_TITLE = $type;
		$product = new Product((int)$product_id, true, (int)$id_lang);
		$productname = self::getProductName($product_id);
		$productcategory = self::getProductCategory($product_id);
		$productdesc = Tools::getDescriptionClean($product->description);
		$productshortdesc = Tools::getDescriptionClean($product->description_short);
		$shoptitle = Configuration::get('PS_SHOP_NAME');
		$productreference = $product->reference;
		$productmanufacturer = $product->manufacturer_name;
		$productfeature = $product->getFeaturesStatic($product_id);
		$productfeatures_collection = array();
		$productretailpricewithtax = Product::getPriceStatic($product_id, true, null, 2, null, false, false);
		$productretailpricewithtax = Tools::displayPrice($productretailpricewithtax);
		$productretailpricewithouttax = Product::getPriceStatic($product_id, false, null, 2, null, false, false);
		$productretailpricewithouttax = Tools::displayPrice($productretailpricewithouttax);
		$productspecificpricewithtax = Product::getPriceStatic($product_id, true, null, 2);
		$productspecificpricewithtax = Tools::displayPrice($productspecificpricewithtax);
		$productspecificpricewithouttax = Product::getPriceStatic($product_id, false, null, 2);
		$productspecificpricewithouttax = Tools::displayPrice($productspecificpricewithouttax);
		$productreduction = Product::getPriceStatic($product_id, true, null, 2, null, true);
		$productreduction = Tools::displayPrice($productreduction);
		if (!empty($productfeature)) {
			foreach ($productfeature as $feature) {
				$feature_one = Feature::getFeature($id_lang, $feature['id_feature']);
				$feature_two = new FeatureValue($feature['id_feature_value'], $id_lang);
				$feature_complete = $feature_one['name'].':'.$feature_two->value;
				array_push($productfeatures_collection, $feature_complete);
			}
			$productfeature = implode(',', $productfeatures_collection);
		}
		else {
			$productfeature = '';
		}
		$search_arr = array('productname',
							'productcategory',
							'shoptitle',
							'productdesc',
							'productshortdesc',
							'productreference',
							'productmanufacturer',
							'productfeature',
							'productretailpricewithtax',
							'productretailpricewithouttax',
							'productspecificpricewithtax',
							'productspecificpricewithouttax',
							'productreduction');
		$replace_arr = array($productname,
							 $productcategory,
							 $shoptitle,
							 $productdesc,
							 $productshortdesc,
							 $productreference,
							 $productmanufacturer,
							 $productfeature,
							 $productretailpricewithtax,
							 $productretailpricewithouttax,
							 $productspecificpricewithtax,
							 $productspecificpricewithouttax,
							 $productreduction);
		$fmeTitle = str_replace($search_arr, $replace_arr, $FME_PRODUCT_META_TITLE);
		return $fmeTitle;
	}
	
	public function hookDisplaySeoAltImages($params)
    {
		$enable_disable = (int)Configuration::get('SEOALTIMG_ENABLE');
		$seo = new SeoAltImagesModel;
		$rule_value = '';
		$id_product = (int)Tools::getValue('id_product');
		$id_language = (int)$this->context->language->id;
		$id_shop = (int)Context::getContext()->shop->id;
		$id_product = ($id_product <= 0) ? (int)$params['id_product'] : $id_product;
		if ($enable_disable > 0) {
			$rule_count = $seo->getActiveRulesCount($id_shop, $id_language);
			if ($rule_count > 0) {
				$all_products_rule = $seo->getRuleGlobal($id_shop, $id_language);
				//All products rule if any exists its preference is no.1
				if (!empty($all_products_rule)) {
					$rule_value = $this->fmeProductTitleByType($id_product, $all_products_rule);
				}
				elseif (empty($all_products_rule)) {
					//Single product rule preference is 2nd
					$single_product_rule = $seo->getSingleProductRule($id_shop, $id_language, $id_product);
					if (!empty($single_product_rule)) {
						$rule_value = $this->fmeProductTitleByType($id_product, $single_product_rule);
					}
					elseif (empty($single_product_rule)) {
						//Last preference is for category rule
						$categories_rule_count = $seo->getCategoriesRuleCount($id_shop, $id_language);
						if ($categories_rule_count > 0) {
							$product = new Product((int)$id_product, true, $this->context->language->id);
							$category_data = $product->getCategories();
							foreach ($category_data as $category) {
								$categories_rule = $seo->getCategoriesRule($id_shop, $id_language, $category);
								if (!empty($categories_rule)) {
									$rule_value = $this->fmeProductTitleByType($id_product, $categories_rule);
								}
							}
						}
					}
				}
				$this->smarty->assign(array(
					'fmm_rule_value' => $rule_value
				));
				return $this->display(__FILE__, 'views/templates/hook/alt.tpl');
			}
		}
    }
}
