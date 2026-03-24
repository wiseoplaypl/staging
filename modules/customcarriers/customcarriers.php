<?php
/**
 * customcarrier.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module customcarrier (Custom Carrier)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__) .'/classes/CustomCarrier.php';
require_once dirname(__FILE__) .'/classes/CustomCarrierFeature.php';
require_once dirname(__FILE__) .'/classes/CustomCarrierAttribute.php';
require_once dirname(__FILE__) .'/classes/CustomCarrierCategory.php';
require_once dirname(__FILE__) .'/classes/CustomCarrierProduct.php';

class Customcarriers extends CarrierModule
{
    private $errors = array();
    public function __construct()
    {
        $this->name = 'customcarriers';
        $this->tab = 'administration';
        $this->version = '0.0.1';
        $this->author = 'SzpaQ';
        parent::__construct();
        $this->displayName = $this->l('Custom Carrier');
        $this->description = $this->l('Create custom carriers depends on: price, features, category, product, attributes etc');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        CustomCarrier::installTable();
        $this->registerHook('actionCarrierUpdate');
    }
    public function install()
    {
        return !$this->installModels()
        || !parent::install()
        || !$this->registerHook('actionUpdateCarrier')
        || !$this->installTabs() ? false : true;
    }
    public function getContent()
    {
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminCustomCarrierConfiguration'));
    }
    public function hookActionCarrierUpdate($params = array())
    {

        /**
        header('Content-Type: application/json');
        echo json_encode($params);
        exit;
        /**/
        /**
        header('Content-Type: application/json');
        echo json_encode($params);
        exit;
        /**/
    }
    private function installModels()
    {
        $models = array(
            'CustomCarrier',
            'CustomCarrierFeature',
            'CustomCarrierAttribute',
            'CustomCarrierCategory',
            'CustomCarrierProduct',
        );
        foreach ($models as $v) {
            if (!$v::installTable()) {
                return false;
            }
        }
        return true;
    }
    public function getOrderShippingCost($params, $shipping_cost)
    {
        $carriers = CustomCarrier::getCarriers();
        if ($params->id_carrier) {
            $c = new Carrier((int) $params->id_carrier);
            if (!$c ->active) {
                return false;
            }
        }
        $cost = 0;
        $products = $params->getProducts();
        $calculated = [];
        foreach ($carriers as $carrier) {
            if ($carrier->is_price_feature) {
                $features = [];
                $tmpfeatures = array_map(function($product) {
                    return $product['features'];
                }, $products);
                foreach ($tmpfeatures as $v) {
                        foreach ($v as $feature) {
                        if ($feature['id_feature'] == $carrier->id_feature) {
                            $fv = new FeatureValue($feature['id_feature_value']);
                            $features[$feature['id_feature_value']] = (float) $fv->value[Context::getContext()->language->id];
                        }
                    }
                }
                foreach ($products as $v) {
                    if (isset($calculated[$v['id_product'].$v['id_product_attribute']])) {
                        continue;
                    }
                    if ($carrier -> is_category && $carrier -> isProductCategory($v['id_product']) == false) {
                        continue;
                    }
                    foreach ($v['features'] as $feature) {
                        if ($feature['id_feature'] == $carrier->id_feature) {
                            $calculated[$v['id_product'].$v['id_product_attribute']] = true;
                            if ($carrier -> price_each_product) {
                                $cost += $v['quantity'] * ($carrier -> feature_multiplier * $features[$feature['id_feature_value']]);
                                $calculated[$v['id_product'].$v['id_product_attribute']] = $v['quantity'] * ($carrier -> feature_multiplier * $features[$feature['id_feature_value']]);
                            }
                        }
                    }
                }

            } elseif ($carrier->price) {
                foreach ($products as $v) {
                    if ($carrier -> is_category && $carrier -> isProductCategory($v['id_product']) == false) {
                        continue;
                    }
                    if (!isset($calculated[$v['id_product'].$v['id_product_attribute']])) {
                        $calculated[$v['id_product'].$v['id_product_attribute']] = $v['quantity'] * $carrier -> price;
                    }
                }
            }
        }
        if (count($calculated)) {
            $cost = array_sum($calculated);
        }
        if (!$cost) {
            return Configuration::Get('customCarriersDefaultPrice') ?: false;
        }
        return $cost;
    }
    public function getOrderShippingCostExternal($params)
    {
        return 100;
    }
    public function installCarrier()
    {
        // Get all services availables for this group
        $rateServiceList = Db::getInstance()->executeS('
            SELECT * FROM `'._DB_PREFIX_.'ups_rate_service_code` WHERE `id_ups_rate_service_group` = '.(int)($id_ups_rate_service_group)
        );
        foreach ($rateServiceList as $rateService)
            if (!$rateService['id_carrier'])
            {
                $config = array(
                    'name' => $rateService['service'],
                    'id_tax_rules_group' => 0,
                    'active' => true,
                    'deleted' => 0,
                    'shipping_handling' => false,
                    'range_behavior' => 0,
                    'delay' => array('fr' => $rateService['service'], 'en' => $rateService['service'], Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')) => $rateService['service']),
                    'id_zone' => 1,
                    'is_module' => true,
                    'shipping_external' => true,
                    'external_module_name' => $this->_moduleName,
                    'need_range' => true
                );
                $id_carrier = $this->installExternalCarrier($config);
                Db::getInstance()->autoExecute(_DB_PREFIX_.'ups_rate_service_code', array('id_carrier' => (int)($id_carrier), 'id_carrier_history' => (int)($id_carrier)), 'UPDATE', '`id_ups_rate_service_code` = '.(int)($rateService['id_ups_rate_service_code']));
            }
    }
    public function installTabs()
    {
        $tabs = array(
            'AdminCustomCarrierConfiguration',
        );
        Db::getInstance()->execute("
            DELETE FROM ". _DB_PREFIX_ ."tab WHERE module = '". $this->name ."'
        ");
        $languages = Language::getLanguages();
        foreach ($tabs as $class_name) {
            $tab = new Tab();
            $tab -> class_name = $class_name;
            $tab -> id_parent = -1;
            $tab -> active = 1;
            $tab -> module = $this->name;
            $tab -> name = array();
            foreach ($languages as $v) {
                $tab->name[$v['id_lang']] = $class_name;
            }
            if (!$tab->save()) {
                Db::getInstance()->execute("
                    DELETE FROM ". _DB_PREFIX_ ."tab WHERE module = '". $this->name ."'
                ");
                return false;
            }
            return true;
        }
    }
    public function setMedia($location = false)
    {
        if ($location === 'Admin') {
                $this->context->controller->addCSS($this->_path.'views/css/admin.css', 'all');
            $this->context->controller->addJS($this->_path.'views/js/admin.js', 'all');
        }
    }
}
