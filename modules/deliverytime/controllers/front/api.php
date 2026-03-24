<?php
/**
 * events.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module usercomevents (User.com events)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */


if (!defined('_PS_VERSION_')) {
    exit;
}

class DeliverytimeApiModuleFrontController extends ModuleFrontController
{
    public $result = [
        'status' => 'ok'
    ];
    public $errorCodes = [
        '001' => 'Unknown Error',
        '002' => 'Method not found',
        '003' => 'Path for file is not set in configuration',
        '004' => 'Supplier not found',
        '005' => 'Api key is missing',
        '006' => 'Api key is invalid',
        '007' => 'Product is missing or provided reference is not correct.',
        '008' => 'Price is not correct value.',
        '010' => 'Product not found.',
        '011' => 'Supplier is not specified.',
        '012' => 'Old price is not specified.',
        '013' => 'Quantity is not specified.',
        '014' => 'Discount is not specified.',
        '015' => 'Rate is not specified.',
    ];
    public $products = [];
    public $orders = [];
    public $supplierMap = [
        'NaturalSleep' => 'Natural Sleep',
        'GIE' => 'Gie Ireland',
        'DreamWorld' => 'Dream World',
        'Tcs' => 'TCS Royal Coil',
        'Mezz' => 'MEZZ',
        'WorldFurniture' => 'World Furniture',
    ];
    public static $supplierConnectMap = [
        'Natural Sleep' => [
            'Natural Sleep Mattresses',
            'NS Ascot Divan.Headboard',
            'NS Special Colours D/HB',
        ],
    ];

    public $sources = [
        'XC3HGJHP6ZZEKQQTAEJE6VVFLAXAJ62W9R3Z2B5A98X3CES6QGNQU9L4WCBJV6MD' => 'OrderMax'
    ];


    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }
    public function initContent()
    {
        parent::initContent();
        if (Tools::getIsset('reference')) {
            $this->getDeliveryTime();
        }
    }
    public function getDeliveryTime()
    {
        $ref = Tools::getValue('reference');
        $product = new Product(Product::getIdByReference($ref));
        if ($product->id) {
            /**/header('Content-Type: application/json');echo json_encode($this->module->getDeliveryTimeName($product));exit;/**/
        }
        /**/header('Content-Type: application/json');echo json_encode($product);exit;/**/
    }
}
