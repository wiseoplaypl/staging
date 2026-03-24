<?php
/**
 * cartquantities.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module cartquantities (Allowed Quantities)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */

if (!defined('_PS_VERSION_')) {
    exit;
}


class Cartquantities extends Module
{
    private $errors = array();
    public function __construct()
    {
        $this->name = 'cartquantities';
        $this->tab = 'front_office_features';
        $this->version = '0.0.1';
        $this->author = 'SzpaQ';
        parent::__construct();
        $this->displayName = $this->l('Allowed Quantities');
        $this->description = $this->l('Allow only specified quantities to be added to cart (pairs etc)');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }
    public function install()
    {
        return !parent::install()
        || !$this->installTabs() ? false : true;
    }
    public function getContent()
    {
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminConfiguracjaQuantites'));
    }
    public function getQuantity($id_product)
    {
        if ($feature = Configuration::get($this->name.'idfeature')) {

            $featureValue = Db::getInstance()->getValue("
                SELECT
                    value
                FROM
                    ". _DB_PREFIX_ ."feature_value_lang
                WHERE
                    id_feature_value = (
                        SELECT
                            id_feature_value
                        FROM
                            ". _DB_PREFIX_ ."feature_product
                        WHERE
                            id_product = ". (int) $id_product ."
                        AND
                            id_feature = ". (int) $feature ."
                    )
            ");
            preg_match_all('/([0-9]+)/', $featureValue, $test);
            if (isset($test[1]) && count($test[1]) > 1) {
                return 1;
            }
            if ($featureValue) {
                return (int) $featureValue;
            }
        }
        return 1;
    }
    public function getQuantityUp($value, $min = 1)
    {
        if (is_array($min)) {
            $tmpMin = false;
            foreach ($min as $v) {
                $check = $this->getQuantityUp($v);
                if ($tmpMin == false) {
                    $tmpMin = $check;
                } else {
                    $tmpMin = min(array($tmpMin, $check));
                }
            }
            $min = $tmpMin;
        }
        if ($min < 1) {
            $min=1;
        }
        if ($value % $min == 0) {
            return $value;
        } else {
            $value++;
            return $this->getQuantityUp($value, $min);
        }
    }
    public function getQuantityDown($value, $min=1)
    {
        if ($min < 1) {
            $min=1;
        }
        if ($value < $min) {
            return $min;
        }
        if ($value % $min == 0) {
            return $value;
        } else {
            $value--;
            return $this->getQuantityUp($value, $min);
        }
    }
    public function installTabs()
    {
        $tabs = array(
            'AdminConfiguracjaQuantites',
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
    public function checkQuantityAfterUpdate()
    {
        $controller = strtolower(Tools::getValue('controller'));
        if (($controller == 'order' || $controller == 'cart') && Tools::getValue('summary')) {
            return false;
        } else {
            return true;
        }
    }
    public function minValue($array)
    {
        return is_array($array) ? min($array) : $array;
    }
}
