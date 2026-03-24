<?php
/**
 * deliverytime.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module deliverytime (Delivery Time)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */

if (!defined('_PS_VERSION_')) {
    exit;
}


class Deliverytime extends Module
{
    private $errors = array();
    public static $isUpdated = false;
    public static $longestTime = false;

    public function __construct()
    {
        $this->name = 'deliverytime';
        $this->tab = 'administration';
        $this->version = '0.0.1';
        $this->author = 'SzpaQ';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Delivery Time');
        $this->description = $this->l('Connects supplier with delivery time. Update automatically delivery time if supplier was changed)');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->registerHook('displayProductPriceBlock');
    }
    public function install()
    {
        return !parent::install() || !$this->registerHook('actionProductUpdate') || !$this->registerHook('displayProductButtons') ? false : true;
    }
    public function getContent()
    {
        return $this->renderFormSuplierMap().$this->renderFormSuppliersSelect();
    }
    public function hookActionProductUpdate($params = [])
    {
        $this->getDeliveryTime(new Product($params['id_product']));
    }
    public function hookDisplayProductButtons($params = [])
    {
        $product = $params['product'];
        if (is_array($product)) {
            if (isset($product['id_product'])) {
                $product = new Product((int) $product['id_product']);
            } elseif (isset($product['id'])) {
                $product = new Product((int) $product['id']);
            } elseif (isset($product['product_id'])) {
                $product = new Product((int) $product['product_id']);
            }
        }
        $product = $this->getDeliveryTime($product);
        $brand = new Manufacturer($product->id_manufacturer);
        if (!$brand->id) {
            return;
        }
        if (trim($brand->name) == 'In Stock') {
            $lang = ''; //$this->l('Delivery time: ');
        } else {
            $lang = $this->l('The delivery time for this product is ');
        }
        return isset($params['mail_template'])
            ? '<div style="font-weight: bold" class="delivery-time-mf mt-1 text-center">'.$lang . $brand -> name .'.</div>'
            : '<div style="" class="delivery-time-mf mt-1 text-center">'.$lang . '<strong>' .$brand -> name .'.</strong></div>';
    }
    public function hookDisplayEmailLine($params = [])
    {
        if (isset($params['order'])) {
            if (self::$longestTime === false || !isset(self::$longestTime[$params['order']->id])) {
                self::$longestTime[$params['order']->id] = ['number' => 0, 'name' => ''];
                foreach ($params['order']->getOrderDetailList() as $k => $v) {
                    $product = new Product((int) $v['product_id']);

                    $product = $this->getDeliveryTime($product);
                    $brand = new Manufacturer($product->id_manufacturer);
                    $s = str_split(trim(preg_replace("/[^0-9\-\.]/", "", $brand->name)));
                    $first_number = '';
                    foreach ($s as $char) {
                        if (is_numeric($char)) {
                            $first_number .= $char;
                        } else {
                            break;
                        }
                    }
                    if ($first_number > self::$longestTime[$params['order']->id]['number']) {
                        self::$longestTime[$params['order']->id]['number'] = $first_number;
                        self::$longestTime[$params['order']->id]['name'] = $brand->name;
                    }
                }
            }

            return '<div><small>Delivery / Collection: '. self::$longestTime[$params['order']->id]['name'] .'.</small></div>';
        }
        $product = new Product((int) $params['id_product']);
        $product = $this->getDeliveryTime($product);
        $brand = new Manufacturer($product->id_manufacturer);
        if (self::$longestTime == 0) {
            self::$longestTime = $brand->name;
        }

        return '<div><small>Delivery / Collection: '. $brand -> name .'.</small></div>';
    }
    public function getDeliveryTime($product)
    {
        if (is_array($product)) {
            if (isset($product['id_product'])) {
                $product = new Product((int) $product['id_product']);
            } elseif (isset($product['id'])) {
                $product = new Product((int) $product['id']);
            } elseif (isset($product['product_id'])) {
                $product = new Product((int) $product['product_id']);
            }
        }

        $name = 'Switch_delivery_time_'. $product ->id_supplier;
        if ($id_manufacturer =  Configuration::get($name)) {
            if ($product -> id_manufacturer != $id_manufacturer) {
                $product = new Product($product->id);
                if (!$product->id) {
                    $product = new Product(Tools::getValue('id_product'));
                }
                if ($product->id) {
                    if ($product -> id_manufacturer != $id_manufacturer) {
                        $product -> id_manufacturer = (int) $id_manufacturer;
                        $product->save();
                    }
                }
            }
        }
        return $product;
    }
    public function renderFormSuplierMap()
    {
        $this->processSuplierMap();
        $this->helper = new HelperForm();
        $this->helper->module = $this;
        $this->helper->token = Tools::getAdminTokenLite('SupplierMap');
        $this->helper->title = $this->displayName;
        $form = array();
        $form['legend'] = array('title' => $this->l('SuplierMap'));
        $form['input'] = array();
        $suppliers = Db::getInstance()->executeS("select * from ". _DB_PREFIX_ ."supplier");
        $values = [];
        $suppliersActive = json_decode(Configuration::get('suppliers_switch') ?: '[]', true);
        foreach ($suppliers as $v) {
            $name = 'Switch_delivery_time_'. $v['id_supplier'];
            $values[$name] = Configuration::get($name);
            $form['input'][] = array(
                'type' => 'select',
                'label' => $v['name'],
                'required' => false,
                'name' => $name,
                'desc' => '',
                'options' => array(
                    'query' => $this->getselectOptions(),
                    'id' => 'id',
                    'name' => 'name',
                ),
            );
           /* $form['input'][] =  array(
                    'label' => $this->l(''),
                    'name' => 'select_supplier_'.$v['id_supplier'],
                    'type' => 'switch',
                    'required' => false,
                    'values' => array(
                        array(
                            'id' => 'select_supplier_ON',
                            'value' => 1,
                            'label' => $this->l('Enable'),
                        ),
                        array(
                            'id' => 'select_supplier_OFF',
                            'value' => 0,
                            'label' => $this->l('Disable'),
                        ),
                    ),
                );
            $form['input'][] =  array(
                'label' => $this->l('Pos name'),
                'name' => 'select_supplier_pos_name_'.$v['id_supplier'],
                'type' => 'text',
                'required' => false,
            );
            $form['input'][] =  array(
                'label' => '',
                'name' => '<hr>',
                'type' => 'html',
                'required' => false,
            );*/
            $values['select_supplier_'.$v['id_supplier']] = in_array($v['id_supplier'], $suppliersActive);
            $values['select_supplier_pos_name_'.$v['id_supplier']] = Configuration::get('select_supplier_pos_name_'. $v['id_supplier']);
        }
        $form['submit'] = array(
            'title' => $this->l('Save'),
            'class' => 'btn btn-default pull-right',
            'name' => 'SuplierMap_Submit',
        );
        $this->helper->fields_value = $values;
        return $this->helper->generateForm(array(array('form' => $form)));
    }
    public function processSuplierMap()
    {
        if (Tools::getIsset('SuplierMap_Submit')) {
            $suppliers = Db::getInstance()->executeS("select * from ". _DB_PREFIX_ ."supplier");
            $suppliersActive = [];
            foreach ($suppliers as $v) {
                $name = 'Switch_delivery_time_'. $v['id_supplier'];
                Configuration::updateValue($name, Tools::getValue($name));
                if (Tools::getValue('select_supplier_'.$v['id_supplier'])) {
                    $suppliersActive[] = $v['id_supplier'];
                }
                Configuration::updateValue('select_supplier_pos_name_'. $v['id_supplier'], Tools::getValue('select_supplier_pos_name_'. $v['id_supplier']));
            }
            Configuration::updateValue('suppliers_switch', json_encode($suppliersActive));
        }
    }
    private function getselectOptions() : array
    {
        $array = Db::getInstance()->executeS("
            SELECT id_manufacturer id, name
            FROM ". _DB_PREFIX_ ."manufacturer
        ");
        $non = [['id' => 0, 'name' => 'Nothing']];
        return array_merge($non, $array);
    }
       /**
     * renderFormSuppliersSelect() : string
     * @description render form SuppliersSelect
     * @return String generated form
     * */
    public function renderFormSuppliersSelect()
    {
        $this->processFormSuppliersSelect();
        $this->helper = new HelperForm();
        $this->helper->module = $this->module;
        $this->helper->token = Tools::getAdminTokenLite('');
        $this->helper->title = $this->l('SuppliersSelect');
        $form = array(
            'title' => $this->l('Map POS suppliers'),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submitSuppliersSelect',
            ),
            'input' => [
                [
                    'label' => 'Mezz',
                    'name' => 'supplier[Mezz]',
                    'type' => 'select',
                    'required' => false,
                    'desc' => $this->l('Select Mezz supplier. This must be selected'),
                    'options' => array(
                        'query' => $this->getSuppliers(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ]
            ]
        );
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );
        $suppliers = json_decode(file_get_contents(
            'https://stock.murphyfurniture.ie/apii.php?get_suppliers=1',
            false,
            stream_context_create($arrContextOptions)
        ));
        $values = [];
        sort($suppliers);
        foreach ($suppliers as $v) {
            $form['input'][] = array(
                'label' => $v,
                'name' => 'supplier[' . $v . ']',
                'type' => 'select',
                'required' => false,
                'options' => array(
                    'query' => $this->getSuppliers(),
                    'id' => 'id',
                    'name' => 'name',
                ),
            );
            $values['supplier[' . $v . ']'] = StockAvailable::mapSupplier($v);
        }
        $values['supplier[Mezz]'] = StockAvailable::mapSupplier('Mezz');
        $this->helper->fields_value = $values;
        return $this->helper->generateForm(array(array('form' => $form)));
    }
    public function getSuppliers()
    {
        $suppliers = Db::getInstance()->executeS("
            SELECT id_supplier id, name
            FROM " . _DB_PREFIX_ ."supplier
        ");
        $empty = [
            'id' => 0,
            'name' => '---',
        ];
        array_unshift($suppliers, $empty);
        return $suppliers;
    }
    public function getOptionsSelectsupplier()
    {
        /** return array of elements, element should be an array with properties: id, name */
    }
    /**
     * processFormsuppliersSelect() : null
     * @description process form SuppliersSelect
     * @return null
     * */
    public function processFormSuppliersSelect()
    {
        $configuration = json_decode(Configuration::get('suppliers_map_switch') ?: '[]', true);
        /** Do somethning with your form **/
        if (Tools::getIsset("submitSuppliersSelect")) {
            foreach (Tools::getValue('supplier') as $k => $v) {
                $configuration[$k] = $v;
            }
            Configuration::updateValue('suppliers_map_switch', json_encode($configuration));
        }
    }
    /**
     * valuesFormsuppliersSelect() : array
     * @description return fields_values for helperform SuppliersSelect
     * @return array $helperForm->field_values
     * */
    public function valuesFormSuppliersSelect() : array
    {
        $values = array();
        /** @select select_supplier **/
        $values['select_supplier'] = Tools::getValue('select_supplier');
        return (array) $values;
    }
    public function getDeliveryTimeName($product)
    {
        $product = (array) $product;
        if (preg_match('/szpak/', $_SERVER['HTTP_USER_AGENT'])) {
             //   error_reporting(E_ALL);ini_set('display_errors', 'On');

        }
        $product = new Product($product['id']);
        if (preg_match('/szpak/', $_SERVER['HTTP_USER_AGENT'])) {

            /**
            header('Content-Type:application/json');
            echo json_encode([$product]);
            exit;
            /**/
        }
        if ($product -> id_manufacturer) {
            $brand = new Manufacturer($product->id_manufacturer);
            return $brand->id ? $brand->name : null;
        }

    }
    public function hookDisplayProductPriceBlock($params)
    {
        if ($params['type'] == 'after_price') {

        //    return $this->hookDisplayProductButtons($params);
        }
        if (preg_match('/szpak/', $_SERVER['HTTP_USER_AGENT'])) {
            /**header('Content-Type: application/json');echo json_encode($params['product']);exit;/**/
        }
    }
}
