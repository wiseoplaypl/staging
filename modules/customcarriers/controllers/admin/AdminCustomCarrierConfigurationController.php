<?php
/**
 * AdminCustomCarrierConfigurationController.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module customcarrier (Custom Carrier)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */

if (!defined('_PS_VERSION_')) {
    exit;
}
class AdminCustomCarrierConfigurationController extends ModuleAdminController
{
    public $obj = false;
    public function __construct()
    {
        parent::__construct();
        $this->module = Module::getInstanceByName('customcarriers');
        $this->bootstrap = true;
        $this->name = $this->l("CustomCarrierConfiguration");
        $this->displayName = $this->l("CustomCarrierConfiguration");
        $this->toolbar_title = $this->l("CustomCarrierConfiguration");
    }
    public function initContent()
    {
        parent::initContent();
        $content = '';
        if (Tools::getIsset('add') || Tools::getIsset('updatecustom_carrier')) {
            $content = $this->renderFormAddCarrier();
        } else {
            $content = $this->renderFormConfiguration().$this->renderListCustomCarrierList();
        }
        $this->context->smarty->assign(array(
            'content' => $content,
        ));
    }
    public function renderListCustomCarrierList()
    {
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->no_link = false;
        $helper->identifier = 'id_custom_carrier';
        $helper->show_toolbar = false;
        $helper->module = $this->module;
        $helper->table ='custom_carrier';
        $helper->title = $this->displayName;
        $helper->token = Tools::getAdminTokenLite('AdminCustomCarrierConfiguration');
        $page = (int) Tools::getValue('submitFilter'. $helper->table) ?: 1;
        if ($page < 1) {
            $page = 1;
        }
        $perPage = (int) Tools::getValue($helper->table .'_pagination') ?: 50;
        $limit = (($page * $perPage) - $perPage) .','. $perPage;
        $query = "
            SELECT
                id_custom_carrier,
                id_carrier,
                feature_multiplier,
                is_price_feature
            FROM
                ". _DB_PREFIX_ ."custom_carrier
        ";
        $fields = array(
            'id_custom_carrier' => array(
                'title' => $this->l('#'),
                'type' => 'string',
                'orderby' => false,
                'search' => false,
            ),
            'id_carrier' => array(
                'title' => $this->l('Carrier id'),
                'type' => 'string',
                'orderby' => false,
                'search' => false,
            ),
            'is_price_feature' => array(
                'title' => $this->l('Price depend on feature'),
                'type' => 'string',
                'orderby' => false,
                'search' => false,
            ),
            'feature_multiplier' => array(
                'title' => $this->l('Feature Multiplier'),
                'type' => 'string',
                'orderby' => false,
                'search' => false,
            ),
        );
        $where = array();
        $order = '';
        foreach ($fields as $k => $v) {
            if ($v['search'] && Tools::getValue($helper->table.'Filter_'. $k)) {
                $where[] = $k . ' LIKE \'%'. pSQL(Tools::getValue($helper->table.'Filter_'. $k)) .'%\'';
            }
            if (Tools::getValue($helper->table.'Orderby')
                && Tools::getValue($helper->table.'Orderway')
            ) {
                $order = ' ORDER BY '.Tools::getValue($helper->table.'Orderby') .' '
                    . (Tools::getValue($helper->table.'Orderway') == 'desc' ? 'DESC' : 'ASC');
            }
        }
        if (count($where)) {
            $query .= ' WHERE '. implode(' AND ', $where);
        }
        $helper->listTotal = Db::getInstance()->getValue(
            preg_replace('/SELECT ([^FROM]*)/', "SELECT COUNT(*) ", $query)
        );
        $query .= $order . ' LIMIT '. $limit;
        $helper->currentIndex = Context::getContext()->link->getAdminLink('AdminCustomCarrierConfiguration', false);
        $helper->actions = array('view', 'edit', 'delete');
        $helper->toolbar_btn['new'] = array(
            'href' => $helper->currentIndex. '&add'.
            '&token='.Tools::getAdminTokenLite('AdminCustomCarrierConfiguration'),
            'desc' => $this->l('Add new...')
        );
        return $helper->generateList(Db::getInstance()->executeS($query), $fields);
    }
    public function renderFormConfiguration()
    {

        $this->helper = new HelperForm();
        $this->helper->module = $this;
        $this->helper->token = Tools::getAdminTokenLite('CustomCarrierConfiguration');
        $this->helper->title = $this->displayName;
        $form = array();
        $form['legend'] = array('title' => $this->l('Configuration'));
        $form['input'] = array(
            array(
                'type' => 'text',
                'label' => $this->l('Default Price'),
                'required' => true,
                'name' => 'customCarriersDefaultPrice',
            ),
        );
        $form['submit'] = array(
            'title' => $this->l('Save'),
            'class' => 'btn btn-default pull-right',
            'name' => 'Configuration_Submit',
        );
        $this->helper->fields_value = array(
            'customCarriersDefaultPrice' => (float) Configuration::get('customCarriersDefaultPrice')
        );
        return $this->helper->generateForm(array(array('form' => $form)));
    }
    public function renderFormAddCarrier()
    {
        $this->helper = new HelperForm();
        $this->helper->module = $this;
        $this->helper->token = Tools::getAdminTokenLite('CustomCarrierConfiguration');
        $this->helper->title = $this->displayName;
        $form = array();
        $form['legend'] = array('title' => $this->l('AddCarrier'));
        $form['input'] = array(
            array(
                'type' => 'select',
                'label' => $this->l('Carrier'),
                'required' => false,
                'name' => 'idcarrier',
                'options' => array(
                    'query' => $this->getidcarrierOptions(),
                    'id' => 'id',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Priority'),
                'required' => true,
                'name' => 'priority',
            ),
            array(
                'type' => 'html',
                'label' => $this->l(''),
                'required' => false,
                'name' => '<h1>Filters</h1>',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Filter by category'),
                'required' => false,
                'name' => 'filtercategory',
                'values' => array(
                     array(
                         'id' => 'filtercategory_ON',
                         'value' => 1,
                         'label' => $this->l('Yes'),
                     ),
                     array(
                         'id' => 'filtercategory_OFF',
                         'value' => 0,
                         'label' => $this->l('Yes'),
                     ),
                 ),
            ),
            array(
                'type' => 'categories',
                'label' => $this->l('Categories'),
                'required' => false,
                'name' => 'idcategory',
                'tree' => array(
                    'id' => 'categories',
                    'use_checkbox' => true,
                    'selected_categories' => $this->obj ? $this->obj->getCategoriesId() : Tools::getValue('idcategory', [])
                )
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Filter by product'),
                'required' => false,
                'name' => 'filterproduct',
                'values' => array(
                     array(
                         'id' => 'filterproduct_ON',
                         'value' => 1,
                         'label' => $this->l('Yes'),
                     ),
                     array(
                         'id' => 'filterproduct_OFF',
                         'value' => 0,
                         'label' => $this->l('Yes'),
                     ),
                 ),
            ),
            array(
                'type' => 'swap',
                'label' => $this->l('Products'),
                'required' => false,
                'name' => 'idproduct',
                'options' => array(
                    'query' => $this->getidproductOptions(),
                    'id' => 'id',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Filter by feature'),
                'required' => false,
                'name' => 'filterfeature',
                'values' => array(
                     array(
                         'id' => 'filterfeature_ON',
                         'value' => 1,
                         'label' => $this->l('Yes'),
                     ),
                     array(
                         'id' => 'filterfeature_OFF',
                         'value' => 0,
                         'label' => $this->l('Yes'),
                     ),
                 ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Feature'),
                'required' => false,
                'name' => 'idfeature',
                'options' => array(
                    'query' => $this->getidfeatureOptions(),
                    'id' => 'id',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => 'html',
                'label' => $this->l(''),
                'required' => false,
                'name' => '<h1>Prices</h1>',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('By feature'),
                'required' => false,
                'name' => 'pricebyfeature',
                'values' => array(
                     array(
                         'id' => 'pricebyfeature_ON',
                         'value' => 1,
                         'label' => $this->l('Yes'),
                     ),
                     array(
                         'id' => 'pricebyfeature_OFF',
                         'value' => 0,
                         'label' => $this->l('Yes'),
                     ),
                 ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Feature'),
                'required' => false,
                'name' => 'idfeature',
                'options' => array(
                    'query' => $this->getidfeatureOptions(),
                    'id' => 'id',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Feature multiplier'),
                'required' => false,
                'name' => 'featuremultiplier',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Static'),
                'required' => false,
                'name' => 'price',
                'values' => array(
                     array(
                         'id' => 'price_ON',
                         'value' => 1,
                         'label' => $this->l('Yes'),
                     ),
                     array(
                         'id' => 'price_OFF',
                         'value' => 0,
                         'label' => $this->l('Yes'),
                     ),
                 ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Static Price'),
                'required' => false,
                'name' => 'price',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Each product'),
                'required' => false,
                'name' => 'price_each_product',
                'values' => array(
                     array(
                         'id' => 'price_each_product_ON',
                         'value' => 1,
                         'label' => $this->l('Yes'),
                     ),
                     array(
                         'id' => 'price_each_product_OFF',
                         'value' => 0,
                         'label' => $this->l('Yes'),
                     ),
                 ),
            ),
        );
        $form['submit'] = array(
            'title' => $this->l('Save'),
            'class' => 'btn btn-default pull-right',
            'name' => 'AddCarrier_Submit',
        );
        $this->helper->fields_value = array(
            'idcarrier' => $this->obj
                ? $this->obj->id_carrier
                : Tools::getValue('idcarrier'),
            'filtercategory' => $this->obj
                ? $this->obj->is_category
                : Tools::getValue('filtercategory'),
            'idfeature' => $this->obj
                ? $this->obj->id_feature
                : Tools::getValue('idfeature'),
            'featuremultiplier' => $this->obj
                ? $this->obj->feature_multiplier
                : Tools::getValue('featuremultiplier', 1),
            'filterproduct' => $this->obj
                ? $this->obj->filterproduct
                : Tools::getValue('filterproduct'),
            'filterfeature' => $this->obj
                ? $this->obj->filterfeature
                : Tools::getValue('filterfeature'),
            'pricebyfeature' => $this->obj
                ? $this->obj->is_price_feature
                : Tools::getValue('pricebyfeature', 1),
            'price' => $this->obj
                ? $this->obj->price
                : Tools::getValue('price'),
            'priority' => $this->obj
                ? $this->obj->priority
                : Tools::getValue('priority', 1),
            'price_each_product' => $this->obj
                ? $this->obj->price_each_product
                : Tools::getValue('price_each_product', 1),
        );
        return $this->helper->generateForm(array(array('form' => $form)));
    }
    public function processAddCarrier()
    {

    }
    private function getidcarrierOptions() : array
    {
        /** return must be an array of rows. Row must have id and name column */
        $carriers = [];
        $c = Db::getInstance()->executeS("
            SELECT c.id_carrier id, c.name FROM ". _DB_PREFIX_ ."carrier c
            WHERE deleted = 0
        ");

        return $c;// array();
    }
    private function gettypeOptions() : array
    {
        /** return must be an array of rows. Row must have id and name column */
        return array();
    }
    private function getidproductOptions() : array
    {
        /** return must be an array of rows. Row must have id and name column */
        return array();
    }
    private function getidfeatureOptions() : array
    {
        /** return must be an array of rows. Row must have id and name column */
        $c = Db::getInstance()->executeS("
            SELECT id_feature id, name FROM ". _DB_PREFIX_ ."feature_lang
            WHERE id_lang = '". Context::getContext()->language->id ."'
        ");
        return $c;
        return array();
    }
    public function postProcess()
    {
        if (Tools::getIsset('Configuration_Submit')) {
            Configuration::updateValue(
                'customCarriersDefaultPrice',
                (float) Tools::getValue('customCarriersDefaultPrice')
            );
        }
        if (Tools::getIsset('id_custom_carrier')) {
            $this->obj = new CustomCarrier((int) Tools::getValue('id_custom_carrier'));
        }
        if (Tools::getIsset('deletecustom_carrier') && $this->obj->id) {
            $this->obj->delete();
            $this->obj = false;
        }
        if ((Tools::getIsset('add') && Tools::getIsset('AddCarrier_Submit')) || Tools::getIsset('updatecustom_carrier') ) {
            $this->processFormAddCarrier();
        }
    }
    public function processFormAddCarrier()
    {
        $carrier = new Carrier((int) Tools::getValue('idcarrier'));
        if ($carrier->id) {
            $carrier -> is_module = 1;
            $carrier -> shipping_handling = 0;
            $carrier -> shipping_method = 2;
            $carrier -> need_range = 1;
            $carrier -> need_range = 1;
            $carrier -> external_module_name = $this->module->name;
            $carrier -> save();
            $customCarrier = $this->obj ?: new CustomCarrier;
            $customCarrier -> id_carrier = (int) $carrier -> id;
            $customCarrier -> price = number_format((float) Tools::getValue('price'), 6, '.', '');
            $customCarrier -> is_price_feature = Tools::getValue('pricebyfeature') ?: 0;
            $customCarrier -> id_feature = Tools::getValue('idfeature') ?: 0;
            $customCarrier -> feature_multiplier = Tools::getValue('featuremultiplier') ?: 1;
            $customCarrier -> priority = Tools::getValue('priority') ?: 1;
            $customCarrier -> is_category = Tools::getValue('filtercategory') ?: 0;
            $customCarrier -> price_each_product = Tools::getValue('price_each_product') ?: 0;
            $customCarrier->save();
            if ($customCarrier->is_category == 1) {
                $customCarrier->setCategories(Tools::getValue('idcategory'));
            }
            /**/
        }
        /**
        header('Content-Type: application/json');
        echo json_encode($_POST);
        exit;
        /**/
    }
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->module->setMedia('Admin');
    }
}
