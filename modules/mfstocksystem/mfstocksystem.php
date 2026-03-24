<?php
/**
 * mfstocksystem.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module mfstocksystem ([MF] Stock system)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */

if (!defined('_PS_VERSION_')) {
    exit;
}


class Mfstocksystem extends Module
{
    private $errors = array();
    public function __construct()
    {
        $this->name = 'mfstocksystem';
        $this->tab = 'administration';
        $this->version = '0.0.1';
        $this->author = 'SzpaQ';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('[MF] Stock system');
        $this->description = $this->l('Connection to POS');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }
    public function install()
    {
        return !parent::install() || !$this->registerHook('actionValidateOrder')
            ? false
            : true;
    }
    public function getContent()
    {
        return $this->renderFormSettingsSync();
    }
    public function hookActionValidateOrder($params = array())
    {
        ini_set('display_errors', 'Off');error_reporting(E_ALL);
        $order = $params['order'];
        $details = Db::getInstance()->executeS("SELECT * FROM ". _DB_PREFIX_ ."order_detail WHERE id_order = '". $order->id ."'");
        $products = [];
        $status = preg_match('/humm/', $order -> payment)
            ? 'Sold Online Humm'
            : 'Sold Online';
        $arrContextOptions=array(
            'http' => array('ignore_errors' => true),
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );
        foreach ($details as $v) {
            if ($this->isMezz($v['product_id'])) {
                file_get_contents(
                    'http://stock.liquidationfurniture.ie/apii.php?status='. urlencode($status) .'&setStatus='. urlencode($v['product_reference']) .'&quantity='. (int) $v['product_quantity'],
                    false,
                    stream_context_create($arrContextOptions)
                );
                StockAvailable::getQuantityAvailableByProduct($v['product_id']);
            }
        }
    }
    public function isMezz($id_product, $id_supplier = 2)
    {
        return (bool) Db::getInstance()->getValue("
            SELECT id_product
            FROM ". _DB_PREFIX_ ."product_supplier
            WHERE id_product = '". $id_product ."'
            AND id_supplier = '". $id_supplier ."'
        ");
    }
    public function getQuantity($id_product)
    {
        StockAvailable::getQuantityAvailableByProduct($id_product);
    }
    /**
     * renderFormSettingsSync() : string
     * @description render form SettingsSync
     * @return String generated form
     * */
    public function renderFormSettingsSync()
    {
        $this->processFormSettingsSync();
        $this->helper = new HelperForm();
        $this->helper->module = $this->module;
        $this->helper->token = Tools::getAdminTokenLite('');
        $this->helper->title = $this->l('SettingsSync');
        $form = array(
            'title' => $this->l('SettingsSync'),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submitSettingsSync',
            ),
            'input' => array(
                'skip_supplier' => array(
                    'label' => $this->l('Skip Supplier'),
                    'desc' => $this->l(''),
                    'name' => 'skip_supplier',
                    'type' => 'swap',
                    'required' => false,
                    'options' => array(
                        'query' => $this->getOptionsSkipsupplier(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                'all_locations_check' => array(
                    'label' => $this->l('Check in all locations'),
                    'desc' => $this->l(''),
                    'name' => 'all_locations_check',
                    'type' => 'swap',
                    'required' => false,
                    'options' => array(
                        'query' => $this->getOptionsSkipsupplier(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'desc' => $this->l('By default, quantities are synchronized with mezz and pre order mezz locations. Pick supplier to check in all locations (Mezz, Pre Order Mezz, Carlow, Dublin, Naas, Gorey, Wexford).'),
                ),
            ),
        );
        $this->helper->fields_value = $this->valuesFormSettingsSync();
        return $this->helper->generateForm(array(array('form' => $form)));
    }
    public function getOptionsSkipsupplier()
    {
        /** return array of elements, element should be an array with properties: id, name */
        return Db::getInstance()->executeS("SELECT id_supplier id, name FROM ". _DB_PREFIX_ ."supplier");
    }
    /**
     * processFormsettingsSync() : null
     * @description process form SettingsSync
     * @return null
     * */
    public function processFormSettingsSync()
    {
        /** Do somethning with your form **/
        if (Tools::getIsset("submitSettingsSync")) {
            Configuration::updateValue('skip_supplier', json_encode(Tools::getValue('skip_supplier_selected')));
            Configuration::updateValue('all_locations_check', json_encode(Tools::getValue('all_locations_check_selected')));
        }
    }
    /**
     * valuesFormsettingsSync() : array
     * @description return fields_values for helperform SettingsSync
     * @return array $helperForm->field_values
     * */
    public function valuesFormSettingsSync() : array
    {
        $values = array();
        /** @swap skip_supplier **/
        $values['skip_supplier'] = json_decode(Configuration::get('skip_supplier') ?: '[]', true);
        $values['all_locations_check'] = json_decode(Configuration::get('all_locations_check') ?: '[]', true);
        return (array) $values;
    }
}

