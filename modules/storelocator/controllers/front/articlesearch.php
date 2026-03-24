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

class StorelocatorArticlesearchModuleFrontController extends ModuleFrontController
{
    public $useSSL = true;

    public function init()
    {
        parent::init();
        $this->context = Context::getContext();
        $this->ajax = Tools::getValue('ajax', true);
        if (Tools::usingSecureMode()) {
            $this->useSSL = true;
        }
    }

    public function initContent()
    {
        parent::initContent();
        if (!$this->ajax) {
            return $this->context->controller->errors[] = $this->module->translation['invalid_request'];
        }
    }

    public function displayAjaxSearchStoreProduct()
    {
        $searchResults = array();
        $query = Tools::replaceAccentedChars(urldecode(Tools::getValue('s')));
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        if (!empty($query) && $query) {
            $searchResults = Locator::findProduct($query, $id_lang, $id_shop);
        }
        if (Tools::version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            die($this->ajaxRender(json_encode($searchResults)));
        }
        else {
            die(Tools::jsonEncode($searchResults));
        }
    }
}
