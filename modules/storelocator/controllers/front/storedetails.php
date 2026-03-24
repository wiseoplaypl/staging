<?php
/**
* DISCLAIMER
*
* Do not edit or add to this file.
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FMM Modules
*  @copyright FME Modules 2021
*  @license   Single domainn
*/

class StorelocatorStoredetailsModuleFrontController extends ModuleFrontController
{
    protected $tpl = 'detail.tpl';

    public function init()
    {
        parent::init();
        $this->context = Context::getContext();

        if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->tpl = 'module:storelocator/views/templates/front/detail-17.tpl';
        } else {
            $this->display_column_right = false;
        }
    }

    public function initContent()
    {
        parent::initContent();
        $id_store = (int) Tools::getValue('id');
        $product_array = array();
        $id_language = (int) Context::getContext()->language->id;
        $store = $this->module->getStore($id_store);
        $def_zoom = (int) Configuration::get('FMESL__STORE_DETAIL_ZOOM_VALUE');
        $def_zoom = ($def_zoom <= 0) ? 10 : $def_zoom;
        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $api_key = Configuration::get('FMESL_KEY');
        $default_country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
        $ps_version = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<') == true) ? 1 : 0;

        if ($store['related_products'] && !empty($store['related_products'])) {
            $related_products = explode(',', $store['related_products']);
            foreach ($related_products as $product) {
                $prd = new Product((int) $product, true, (int) $id_language);
                $prd->id_product_attribute = (int) Product::getDefaultAttribute($prd->id) > 0 ? (int) Product::getDefaultAttribute($prd->id) : 0;
                $_cover = ((int) $prd->id_product_attribute > 0) ? Product::getCombinationImageById((int) $prd->id_product_attribute, $id_language) : Product::getCover($prd->id);
                if (!is_array($_cover)) {
                    $_cover = Product::getCover($prd->id);
                }
                $prd->id_image = $_cover['id_image'];
                array_push($product_array, $prd);
            };
            $store['related_products'] = $product_array;
        }

        $this->context->smarty->assign(array(
            'store' => $store,
            'id_store' => $id_store,
            'def_zoom' => (int) $def_zoom,
            'searchUrl' => $this->context->link->getModuleLink('storelocator', 'storefinder', array(), true),
            'img_ps_dir' => $protocol_link . Tools::getMediaServer(_PS_IMG_) . _PS_IMG_,
            'logo_store' => Configuration::get('PS_STORES_ICON'),
            'FMESL_GLOBAL_ICON' => (int) Configuration::get('FMESL_GLOBAL_ICON'),
            'protocol_link' => $protocol_link,
            'api_key' => $api_key,
            'region' => Tools::substr($default_country->iso_code, 0, 2),
            '_ps_ver' => (int) $ps_version,
        ));

        $this->setTemplate($this->tpl);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        Media::addJsDef(array('map_theme' => $this->module->getMapStyles(Configuration::get('FMESL_MAIN_MAP_THEME'))));
        $this->addCss(__PS_BASE_URI__ . 'modules/storelocator/views/css/detail.css');
        $this->addJs(__PS_BASE_URI__ . 'modules/storelocator/views/js/detail.js');
        $this->addJs(__PS_BASE_URI__ . 'modules/storelocator/views/js/owl.js');
    }

    public function getBreadcrumbLinks()
    {
        $title_store = Meta::getMetaByPage('stores', $this->context->language->id);
        $title_store = $title_store['title'];
        $breadcrumb = parent::getBreadcrumbLinks();
        $meta_title = $title_store;
        $breadcrumb['links'][] = array(
            'title' => $meta_title,
            'url' => $this->context->link->getPageLink('stores'),
        );
        return $breadcrumb;
    }
}
