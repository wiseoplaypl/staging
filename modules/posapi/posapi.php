<?php
/**
 * posapi.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module posapi (POS api)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_ROOT_DIR_ .'/import/autoload.php';
require_once dirname(__FILE__) .'/classes/OrderProductSupplier.php';
require_once dirname(__FILE__) .'/classes/PriceChangeLog.php';
require_once 'classes/App/Config.php';
require_once 'classes/App/Files.php';

class Posapi extends Module
{
    private $errors = array();
    public static $lockedProducts = null;
    public function __construct()
    {
        $this->name = 'posapi';
        $this->tab = 'administration';
        $this->version = '0.0.1';
        $this->author = 'SzpaQ';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('POS api');
        $this->description = $this->l('Display extra content on product tab');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->registerHook('actionValidateOrder');
        OrderProductSupplier::installTable();
        PriceChangeLog::installTable();
    }
    public function getContent()
    {
        $this->postProcess();
        $link = new Link;
        $this->helper = new HelperForm();
        $this->helper->title = $this->displayName;
        $form = array();
        $form['legend'] = array('title' => $this->l('AddEvent'));
        $form['input'] = array(
            array(
                'type' => 'text',
                'label' => $this->l('Brw Path'),
                'required' => true,
                'name' => 'POSApi_Brw',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Halmar Path'),
                'required' => true,
                'name' => 'POSApi_Halmar',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Wsb Path'),
                'required' => true,
                'name' => 'POSApi_Wsb',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Decor Path'),
                'required' => true,
                'name' => 'POSApi_Decor',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Dream World'),
                'required' => true,
                'name' => 'POSApi_DreamWorld',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Natural Sleep'),
                'required' => true,
                'name' => 'POSApi_NaturalSleep',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('TCS'),
                'required' => true,
                'name' => 'POSApi_Tcs',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Mezz'),
                'required' => true,
                'name' => 'POSApi_Mezz',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Annaghmore'),
                'required' => true,
                'name' => 'POSApi_Annaghmore',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('GIE'),
                'required' => true,
                'name' => 'POSApi_GIE',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Gannons'),
                'required' => true,
                'name' => 'POSApi_Gannons',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Gerbor'),
                'required' => true,
                'name' => 'POSApi_Gerbor',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('World Furniture'),
                'required' => true,
                'name' => 'POSApi_WorldFurniture',
            ),
        );
        $form['submit'] = array(
            'title' => $this->l('Save'),
            'class' => 'btn btn-default pull-right',
            'name' => 'AddEvent_Submit',
        );
        $this->helper->fields_value = array(
            'POSApi_Brw' => Configuration::get('POSApi_Brw'),
            'POSApi_Halmar' => Configuration::get('POSApi_Halmar'),
            'POSApi_Wsb' => Configuration::get('POSApi_Wsb'),
            'POSApi_Decor' => Configuration::get('POSApi_Decor'),
            'POSApi_DreamWorld' => Configuration::get('POSApi_DreamWorld'),
            'POSApi_NaturalSleep' => Configuration::get('POSApi_NaturalSleep'),
            'POSApi_Tcs' => Configuration::get('POSApi_Tcs'),
            'POSApi_Mezz' => Configuration::get('POSApi_Mezz'),
            'POSApi_Annaghmore' => Configuration::get('POSApi_Annaghmore'),
            'POSApi_GIE' => Configuration::get('POSApi_GIE'),
            'POSApi_Gannons' => Configuration::get('POSApi_Gannons'),
            'POSApi_Gerbor' => Configuration::get('POSApi_Gerbor'),
            'POSApi_WorldFurniture' => Configuration::get('POSApi_WorldFurniture'),
        );
        foreach ($this->getOtherImporters() as $k => $v) {
            $form['input'][$k] = array(
                'type' => 'text',
                'label' => $v,
                'required' => true,
                'name' => 'POSApi_'.$k,
            );
            $this->helper->fields_value['POSApi_'.$k] = Configuration::get('POSApi_'.$k);
        }

        return $link->getModuleLink('posapi', 'api'). $this->helper->generateForm(array(array('form' => $form)));
    }
    public function postProcess()
    {
        if (Tools::getIsset('AddEvent_Submit')) {
            Configuration::updateValue('POSApi_Brw', Tools::getValue('POSApi_Brw'));
            Configuration::updateValue('POSApi_Halmar', Tools::getValue('POSApi_Halmar'));
            Configuration::updateValue('POSApi_Wsb', Tools::getValue('POSApi_Wsb'));
            Configuration::updateValue('POSApi_Decor', Tools::getValue('POSApi_Decor'));
            Configuration::updateValue('POSApi_DreamWorld', Tools::getValue('POSApi_DreamWorld'));
            Configuration::updateValue('POSApi_NaturalSleep', Tools::getValue('POSApi_NaturalSleep'));
            Configuration::updateValue('POSApi_Tcs', Tools::getValue('POSApi_Tcs'));
            Configuration::updateValue('POSApi_Mezz', Tools::getValue('POSApi_Mezz'));
            Configuration::updateValue('POSApi_Annaghmore', Tools::getValue('POSApi_Annaghmore'));
            Configuration::updateValue('POSApi_GIE', Tools::getValue('POSApi_GIE'));
            Configuration::updateValue('POSApi_Gannons', Tools::getValue('POSApi_Gannons'));
            Configuration::updateValue('POSApi_Gerbor', Tools::getValue('POSApi_Gerbor'));
            Configuration::updateValue('POSApi_WorldFurniture', Tools::getValue('POSApi_WorldFurniture'));
            foreach ($this->getOtherImporters() as $k => $v) {
                Configuration::updateValue('POSApi_'.$k, Tools::getValue('POSApi_'.$k));
            }

        }
    }
    public function hookDisplayFooter()
    {
     //   @file_get_contents('http://stock.liquidationfurniture.ie/orderSystem/getfrommf');
    }
    /**
     * hookActionValidateOrder()
     * @var array $params depends on current page (see prestashop documentation hooks)
     * */
    public function hookActionValidateOrder($params = array())
    {
        $products = [];
        foreach ($params['cart']->getProducts() as $v) {
            $supplier = new Supplier($v['id_supplier']);
            $reference = $v['reference'];
            $object = OrderProductSupplier::AddRow(
                $v['reference'],
                $params['order'] -> reference,
                (int) $v['id_supplier'],
                (int) $v['id_product'],
                (int) $params['order'] -> id
            );
        }
    }
    public function getOtherImporters($name = null)
    {
        new App\Config(dirname(__FILE__).'/importers.ini');
        $importers = App\Config::Get('Importers');
        if ($name !== null) {
            return isset($importers -> $name)
                ? $importers -> $name
                : false;
        }
        return $importers ?: [];
    }
    public function getAllImporters()
    {
        new App\Config(dirname(__FILE__).'/importers.ini');
        $importers = [];
        foreach (App\Config::Get('OtherImporters') as $k => $v) {
            $importers[$k] = $v;
        }
        foreach (App\Config::Get('Importers') as $k => $v) {
            $importers[$k] = $v;
        }
        return $importers;
    }
    public function isProductLocked($reference)
    {
        if (self::$lockedProducts === null) {
            self::$lockedProducts = json_decode(
                Configuration::get(
                    'ImportLockedProducts'
                ) ?: '[]',
                true
            );
        }
        return isset(self::$lockedProducts[$reference]);
    }
}
