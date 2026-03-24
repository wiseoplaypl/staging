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

class PosapiApiModuleFrontController extends ModuleFrontController
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
        ini_set('display_errors', 'Off');error_reporting(0);
        parent::initContent();
        /** File generated with Module generator made by SzpaQ */
        if (Tools::getIsset('getSupplierOrder') && Tools::getIsset('product_reference')) {
            $obj = OrderProductSupplier::getByReferences(
                Tools::getValue('product_reference'),
                Tools::getValue('getSupplierOrder')
            );
            if ($obj) {
                exit($obj -> supplier_name);
            }
            exit();
        } elseif (Tools::getIsset('getCategories')) {
            $this->getCategories();
        } elseif (Tools::getIsset('setOrder')) {
            /**/
            header('Content-Type: application/json');
            echo json_encode(self::insertOrder((int) Tools::getValue('setOrder')));
            exit;
            /**/
        } elseif (Tools::getIsset('getProducts')) {
            Context::getContext()->wsb_supplier_name = 1;
            $this->getProducts(Tools::getValue('Supplier'), (bool) Tools::getIsset('Full'));
        } elseif (Tools::getIsset('getProductsNew')) {
            Context::getContext()->wsb_supplier_name = 1;
            $this->getProductsNew(Tools::getValue('Supplier'));
        } elseif (Tools::getIsset('getPrestashopProducts')) {
            $this->getPrestashopProducts(Tools::getValue('Supplier'));
        } elseif (Tools::getIsset('updatePrice')) {
            $this->updatePrice(Tools::getValue('reference'), Tools::getValue('newPrice'));
        } elseif (Tools::getIsset('updateCost')) {
            $this->updateCost(Tools::getValue('reference'), Tools::getValue('newPrice'));
        } elseif (Tools::getIsset('getSupplierMultipliers')) {
            $this->getSupplierMultipliers();
        } elseif (Tools::getIsset('SetSupplierMultiplier')) {
            $this->actionSetSupplierMultiplier();
        } elseif (Tools::getIsset('updateQuantityKey')) {
            $this->updateQuantityKey();
        } elseif (Tools::getIsset('updateQuantity')) {
            $this->updateQuantity(Tools::getValue('reference'));
        } elseif (Tools::getIsset('getProductPrices')) {
            $this->getProductPrices();
        } elseif (Tools::getIsset('getFileinfo')) {
            $this->actionGetFileInfo(Tools::getValue('getFileinfo'));
        } elseif (Tools::getIsset('getOrders')) {
            $this->getOrders(Tools::getValue('getOrders'));
            $this->result['products'] = [];
        } elseif (Tools::getIsset('action') && method_exists($this, 'action' . Tools::getValue('action'))) {
            $this->{'action' . Tools::getValue('action')}();
        }
       /// $this->getOrders(Tools::getValue('id_order'));
        $this->showResult();
    }
    public function getOrders($date = '2021-07-30')
    {
        self::installTable();
        $details = Db::getInstance()->executeS("
            SELECT * FROM ". _DB_PREFIX_ ."order_detail WHERE id_order IN(
                SELECT id_order FROM ". _DB_PREFIX_ ."orders WHERE date_add >= '". $date ."' AND
                current_state IN(2,21,23, 28, 32)
            )
        ");
        /** check if its supplier */

        /** END check if its supplier */
        if ($details) {
            $this->loadProductsCache();
        }
        $this->result['orders'] = [];
        foreach ($details as $detail) {
         //   if (isset($this->result['products'][$detail['product_reference']])) {
              //  $this->result['products'][$detail['product_reference']]['name'] .= ' ( '. $detail['product_name'] .' )';
                $order = new Order($detail['id_order']);
                $customerDetails = null;
                $orderDetails = null;
                $orderPayment = null;
                $product = new Product($detail['product_id']);
                if (Tools::getIsset('customer_details')) {
                    $customerDetails = [
                        'customer' => new Customer($order->id_customer),
                        'address_delivery' => new Address($order->id_address_delivery),
                        'address_invoice' => new Address($order->id_address_invoice),
                    ];
                }
                if (Tools::getIsset('order_details')) {
                    $orderDetails = Db::getInstance()->executeS("
                        SELECT
                            product_quantity,
                            unit_price_tax_incl,
                            total_price_tax_incl,
                            product_name,
                            product_reference,
                            product_id
                        FROM ". _DB_PREFIX_ ."order_detail
                        WHERE id_order = '". $order->id ."'
                    ");
                    foreach ($orderDetails as $k => $v) {
                        $product = new Product($v['product_id']);
                        $orderDetails[$k]['supplier'] = Db::getInstance()->getValue("
                            SELECT name FROM ". _DB_PREFIX_ ."supplier
                            WHERE id_supplier = '". $product -> id_supplier ."'
                        ");
                    }
                    $orderPayment = Db::getInstance()->getRow("SELECT * FROM ". _DB_PREFIX_ ."order_payment WHERE order_reference = '". $order->reference ."'");
                }
                $orderPayment['total_discount'] = $order->total_discounts_tax_incl;
                $this->result['orders'][] = [
                    'id_order' => $order->id,
                    'payment' => $order->payment,
                    'order_state' => $order->current_state,
                    'order_reference' => $order->reference,
                    'total_discount' => $order->total_discounts_tax_incl,
                    'quantity' => $detail['product_quantity'],
                    'product' => new Product($detail['product_id']),
                    'prestashop_name' => $detail['product_name'],
                    'customer_details' =>  $customerDetails,
                    'payment_details' =>  $orderPayment,
                    'orderDetails' =>  $orderDetails,
                    'supplier' => Db::getInstance()->getValue("
                        SELECT name FROM ". _DB_PREFIX_ ."supplier
                        WHERE id_supplier = '". $product -> id_supplier ."'
                    "),
                    'total_shipping' => $order->total_shipping
                ];
         //  }
         //   self::insertOrder($detail['id_order']);
        }
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );
        if (!Tools::getIsset('topos')) {
            $file = json_decode(file_get_contents('http://stock.liquidationfurniture.ie/apii.php?get_orders='. $date, false, stream_context_create($arrContextOptions)));

           // $this->result['orders'] =[];
            foreach ($file as $posOrder) {
                foreach ($posOrder->sale_items as $item) {
                        $id_product = false;
                        $subname = false;
                        $id_product_attribute = Db::getInstance()->getValue("
                            SELECT id_product_attribute FROM ". _DB_PREFIX_ ."product_attribute WHERE reference = '". $item -> product_code ."'
                        ");
                        if ($id_product_attribute) {
                            $combination = new Combination($id_product_attribute);
                            $subname = [];
                            foreach ($combination->getAttributesName(Configuration::get('PS_LANG_DEFAULT')) as $v) {
                                $subname[] = $v['name'];
                            }
                            $subname = implode(', ', $subname);
                            $id_product = $combination -> id_product;
                        } else {
                            $id_product = Db::getInstance()->getValue("
                                SELECT id_product FROM ". _DB_PREFIX_ ."product WHERE reference = '". $item -> product_code ."'
                            ");
                        }
                        if ($id_product) {
                            $product = new Product($id_product);
                            $item->product_name = $product->name[Configuration::get('PS_LANG_DEFAULT')];
                            if ($subname) {
                                $item->product_name .= ' - '. $subname;
                            }
                        }

                    $this->result['orders'][] = [
                        'id_order' => '9999999' . $posOrder->id,
                        'order_state' => 2,
                        'order_reference' => 'POS_'. $posOrder->reference_no,
                        'quantity' => $item->quantity,
                        'product' => $this->result['products'][$item->product_code],
                        'prestashop_name' => $item->product_name,
                        'ref' => $item->product_code
                    ];
                }
            }
        }

        /**
        header('Content-Type: application/json');
        echo json_encode([$this->result]);
        exit;
        /**/
        /**
        header('Content-Type: application/json');
        echo json_encode($this->result['orders']);
        exit;
        /**/

        /**
        header('Content-Type: application/json');
        echo json_encode($this->result['orders']);
        exit;
        /**/
    }
    public static function installTable()
    {
        return Db::getInstance()->execute(
            "CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."order_max_order (
                `id_order` INT(10) NULL,
                PRIMARY KEY (`id_order`)
             )"

        );
    }
    public static function insertOrder($id_order)
    {
        return Db::getInstance()->execute(
            "INSERT IGNORE INTO "._DB_PREFIX_."order_max_order (
                `id_order`
            ) VALUES('". pSQL($id_order) ."')"
        );
    }
    public function loadProductsCache()
    {
        return;
        $suppliers = ['Brw', 'Halmar', 'Wsb', 'Decor', 'NaturalSleep', 'DreamWorld'];
        $file = dirname(__FILE__) .'/../../products3.cache';
        if (file_exists($file)) {
            if (filemtime($file) > time() - 3600) {
                $this->result['products'] = json_decode(file_get_contents($file), true);
                return;
            }
        }
        foreach ($suppliers as $v) {
            $this->getProducts($v);
        }
        file_put_contents($file, json_encode($this->result['products']));
    }
    public function getProductsFull($supplier = null)
    {

    }
    public function getProductsNew($class = 'Brw', $full = true)
    {

        $supplier = Tools::getValue('supplier') ?: Tools::getValue('Supplier');
        $importer = new ImportPOS($supplier);
        $rows = [];
        while ($r = $importer->getRow()) {
            $row = $importer->getRowPOS();
            $rows[$row['reference']] = $row;
            if (count($rows) > 10) {

                /**/header('Content-Type: application/json');echo json_encode($rows);exit;/**/
            }
        }
        /**/header('Content-Type: application/json');echo json_encode($importer);exit;/**/
    }
    public function getProducts($class = 'Brw', $full = true)
    {
        if (isset(self::$supplierConnectMap[$class])) {
            $class = self::$supplierConnectMap[$class];

        }
        if (class_exists($class) && method_exists($class, 'loadProducts')) {
            if (!Configuration::get('POSApi_'. $class)) {
                if (file_exists(_PS_ROOT_DIR_ .'/brw_feed/'. $class)) {
                    Configuration::updateValue('POSApi_'. $class, _PS_ROOT_DIR_ .'/brw_feed/'. $class);
                } else {
                    $this->throwError('003');
                }
            }
            $object = new $class(Configuration::get('POSApi_'. $class));
            $object -> loadProducts();
            if (preg_match('/mf-debug/', $_SERVER['HTTP_USER_AGENT'])) {
                /**/header('Content-Type: application/json');echo json_encode($object->products);exit;/**/
            }
            if ($full === true) {
                $this->loadPrestashopProducts($class);
                $products = [];
                foreach ($object->products as $k => $v) {
                    $v = (object) $v;
                    if (!$v->reference) {
                        continue;
                    }
                    if (!isset($this->products[$v->reference])) {
                        $this->products[$v->reference] = $this->getProductFromCombination($v->reference);
                    }
                    $our_price = '';
                    if (isset($v->price) && $v->price) {
                        $our_price = $v->price;
                    }

                    $this->result['products'][$v->reference] = [
                        'reference' => $v->reference,
                        'name' => $v->name,
                        'supplier' => isset($object->supplier_name) ? $object->supplier_name : $class,
                        'prestashop_name' => isset($this->products[$v->reference]) ? $this->products[$v->reference]['name'] : '',
                        'supplier_cost' => $v->price_before,
                        'prestashop_price' => isset($this->products[$v->reference]) && $this->products[$v->reference]['price']
                            ? $this->products[$v->reference]['price']
                            : (isset($v->prestashop_price) ? $v->prestashop_price : 0),
                        'price_default' => isset($this->products[$v->reference]['price_default'])
                            ? $this->products[$v->reference]['price_default']
                            : '',
                        'quantity' => $v->quantity,
                        'prestashop_quantity' => isset($this->products[$v->reference]) ? $this->products[$v->reference]['quantity'] : '',
                        'is_prestashop' => (bool) isset($this->products[$v->reference]),
                        'default_our_price' => $our_price,
                        'price' => Configuration::get($class.'ExchangeRate')
                            ? number_format($v->price_before * ((100 - Configuration::get($class.'Discount')) /  100) * Configuration::get($class.'ExchangeRate'), 2, '.', '')
                            : $v->price_before,
                        'supplier_cost_rated' => Configuration::get($class.'ExchangeRate')
                            ? number_format(($v->price_before * ((100 - Configuration::get($class.'Discount')) /  100) * Configuration::get($class.'ExchangeRate')), 2, '.', '')
                            : $v->price_before,
                        'is_locked' => $this->module->isProductLocked($v->reference),
                        'categories' => isset($this->products[$v->reference]) && $this->products[$v->reference]['categories']
                            ? $this->products[$v->reference]['categories']
                            : $this->getProductCategoriesByReference($v->reference),
                    ];
                }
                //$id_supplier = $this->getSupplierId($class);
              //  $this->result['products'] = $products;
                return;
            }
            foreach ($object -> products as $v) {
                //$this->result['products'][$v['reference']] = $v;
            }

        } else {
            $this->throwError('004');
        }
    }
    public function getProductFromCombination($reference)
    {
        $id_product_attribute = Db::getInstance()->getValue("
            SELECT id_product_attribute
            FROM ". _DB_PREFIX_ ."product_attribute
            WHERE reference = '". $reference ."'
        ");
        if ($id_product_attribute) {
            $combination = new Combination($id_product_attribute);

            /**
            header('Content-Type: application/json');
            echo json_encode();
            exit;
            /**/
            $product = new Product($combination->id_product);
            $name = $product->name[Context::getContext()->language->id];
            foreach ($combination->getAttributesName(Context::getContext()->language->id) as $v) {
                $name .= ' - '. $v['name'];
            }
            return [
                'name' => $name,//$product->name[Context::getContext()->language->id],
                'price' => $product->getPrice(true, $combination->id),//$combination->price,
                'price_default' => $product->getPrice(true, $combination->id, 6, null, false, false),//$combination->price,
                'quantity' => StockAvailable::getQuantityAvailableByProduct($product->id, $combination->id),
            ];
            /**
            header('Content-Type: application/json');
            echo json_encode();
            exit;
            /**/
        }
        return [
            'name' => '',
            'price' => '',
            'quantity' => '',
        ];
    }
    public function getPrestashopProducts($supplier = null)
    {
        $this->result['products'] = $this->loadPrestashopProducts($supplier);
    }
    public function loadPrestashopProducts($supplier)
    {

        $this->products = [];
        if ($supplier) {
            if (isset($this->supplierMap[$supplier])) {
                $supplier = $this->supplierMap[$supplier];
            }
            $subquery = '';
            if (isset(self::$supplierConnectMap[$supplier])) {
                $subquery .= " OR name LIKE '";
                $subquery .= implode("' OR name LIKE '", self::$supplierConnectMap[$supplier]);
                $subquery .= "'";
            } elseif ($supplier_name = $this->module->getOtherImporters($supplier)) {
                $subquery .= " OR name LIKE '" . $supplier_name ."'";
            }
            $id_suppliers = Db::getInstance()->executeS("SELECT id_supplier FROM ". _DB_PREFIX_ ."supplier WHERE name LIKE '". $supplier ."'". $subquery);
            $suppliers_id = [];
            foreach ($id_suppliers as $v) {
                $suppliers_id[] = $v['id_supplier'];
            }
            if (empty($suppliers_id)) {
                $this->result['supplier_checked'] = $supplier;
                $this->throwError('004');
            }

            $products = Db::getInstance()->executeS("
                SELECT id_product FROM ". _DB_PREFIX_ ."product WHERE id_product IN(
                    SELECT id_product FROM ". _DB_PREFIX_ ."product_supplier
                    WHERE id_supplier IN(2, ". implode(',', $suppliers_id) .")
                )
            ");
            foreach ($products as $v) {
                $product = new Product($v['id_product'], Configuration::Get('PS_LANG_DEFAULT'));
                $this->products[$product->reference] = [
                    'reference' => $product -> reference,
                    'name' => array_shift($product->name),
                    'price' => $product->getPrice(true),
                    'price_default' => $product->getPrice(true, null, 6, null, false, false),
                    'quantity' => StockAvailable::getQuantityAvailableByProduct($product->id),
                    'categories' => $this->getProductCategoriesByReference($product->reference),
                    'is_combination' => false,
                ];
            }
            $products = Db::getInstance()->executeS("
                SELECT id_product, reference, price, id_product_attribute FROM ". _DB_PREFIX_ ."product_attribute WHERE id_product IN(
                    SELECT id_product FROM ". _DB_PREFIX_ ."product_supplier
                    WHERE id_supplier IN(2, ". implode(',', $suppliers_id) .")
                )
            ");
            foreach ($products as $v) {
                $product = new Product($v['id_product'], Configuration::Get('PS_LANG_DEFAULT'));
                $this->products[$v['reference']] = [
                    'reference' => '!'. $v['reference'],
                    'name' => array_shift($product->name),
                    'price' => $product->getPrice(true),
                    'price_default' => $product->getPrice(true, $v['product_attribute'], 6, null, false, false),
                    'quantity' => StockAvailable::getQuantityAvailableByProduct($product->id, $v['product_attribute']),
                    'categories' => $this->getProductCategoriesByReference($product->reference),
                    'is_combination' => true,
                ];

            }
        } else {
            $products = Db::getInstance()->executeS("SELECT id_product FROM ". _DB_PREFIX_."product");
            foreach ($products as $v) {
                $product = new Product($v['id_product']);
                $this->products[$product->reference] = [
                    'reference' => $product -> reference,
                    'name' => array_shift($product->name),
                    'price' => $product->getPrice(true),
                    'price_default' => $product->getPrice(true, null, 6, null, false, false),
                    'quantity' => StockAvailable::getQuantityAvailableByProduct($product->id),
                ];
            }
            /*
             *$tax = true,
        $id_product_attribute = null,
        $decimals = 6,
        $divisor = null,
        $only_reduc = false,
        $usereduc = true,
             *
             * */
        }
        return $this->products;
    }
    public function loadPrestashopProductByReference($ref)
    {
        $v = Db::getInstance()->getRow("SELECT id_product FROM ". _DB_PREFIX_."product WHERE reference LIKE '" . pSQL($ref) ."'");
        if ($v && isset($v['id_product'])) {
            $product = new Product($v['id_product']);
            $this->products[$product->reference] = [
                'reference' => $product -> reference,
                'name' => array_shift($product->name),
                'price' => $product->getPrice(true),
                'price_default' => $product->getPrice(true, null, 6, null, false, false),
                'quantity' => StockAvailable::getQuantityAvailableByProduct($product->id),
            ];
        }

        return $this->products;
    }
    public function throwError($code = '001')
    {
        if (!isset($this->errorCodes[$code])) {
            return $this->throwError('001');
        }
        $this->result['status'] = 'ERROR';
        $this->result['error_code'] = $code;
        $this->result['error_message'] = $this->errorCodes[$code];
        /**/
        header('Content-Type: application/json');
        echo json_encode($this->result);
        exit;
        /**/
    }
    public function showResult($data = [])
    {

        $this->result = array_merge($this->result, $data);
        /**/
        header('Content-Type: application/json');
        echo json_encode($this->result);
        exit;
        /**/
    }
    public function isReferenceCombination($reference)
    {
        return Db::getInstance()->getValue('
            SELECT id_product_attribute
            FROM '. _DB_PREFIX_ .'product_attribute
            WHERE reference LIKE \''. $reference .'\'
        ');
    }
    public function updateCombinationPrice($reference, $price)
    {
        $combination = new Combination($this->isReferenceCombination($reference));
        $row = [];
        $row['original_price'] = $combination -> price;
        if ($combination->price != $price) {
            $combination->price = $price;
            $combination->save();
            Migrate::insertLog($this->product, ' Changed price from OrderMax. Old: '. $priceBefore .', new: '. $this->product->price);
            PriceChangeLog::addChangelog($priceBefore, $price, $this->sources[Tools::getValue('apiKey')]);
            $this->showResult();
        }

    }
    public function updatePrice($reference, $newPrice = null)
    {
        if (!Tools::getIsset('apiKey')){
            $this->throwError('005');
        }
        if (!isset($this->sources[Tools::getValue('apiKey')])) {
            $this->throwError('006');
        }
        if (!Tools::getIsset('reference') || !trim(Tools::getValue('reference'))) {
            $this->throwError('007');
        }
        $this->product = new Product(
            (int) Db::getInstance()->getValue(
                "SELECT id_product FROM ". _DB_PREFIX_ ."product WHERE reference = '". pSQL(Tools::getValue('reference')) ."'"
            )
        );
        $price = (float) str_replace(',', '.', Tools::getValue('price'));

        if (!$this->product->id && $this->isReferenceCombination($reference)) {

            return $this->updateCombinationPrice($reference, $price);
        }
        if (!$this->product->id || $this->product->reference != Tools::getValue('reference')) {
            $this->throwError('007');
        }
        $row = $this->getProductPrices($this->product->reference, true);
        if (isset($row['original_price']) && $row['original_price']) {
            $diff = $price - $row['price'];
            $price = $row['original_price'] + $diff;
        }
        if (!$price) {
            $this->throwError('008');
        }
        if ($this->product->price == $price) {
            $this->showResult();
        }
        $priceBefore = $this->product->price;
        $this->product->price = $price;
        if ($this->product->save()) {
            Migrate::insertLog($this->product, ' Changed price from OrderMax. Old: '. $priceBefore .', new: '. $this->product->price);
            PriceChangeLog::addChangelog($priceBefore, $price, $this->sources[Tools::getValue('apiKey')]);
            $this->showResult();
        } else {
            $this->throwError();
        }
    }
    public function updateCost($reference, $newPrice = null)
    {
        if (!Tools::getIsset('apiKey')){
            $this->throwError('005');
        }
        if (!isset($this->sources[Tools::getValue('apiKey')])) {
            $this->throwError('006');
        }
        if (!Tools::getIsset('reference') || !trim(Tools::getValue('reference'))) {
            $this->throwError('007');
        }
        if (!Tools::getIsset('supplier') || !trim(Tools::getValue('supplier'))) {
            $this->throwError('011');
        }
        if (!Tools::getIsset('old_price') || !trim(Tools::getValue('old_price'))) {
            $this->throwError('012');
        }
        $this->product = new Product(
            (int) Db::getInstance()->getValue(
                "SELECT id_product FROM ". _DB_PREFIX_ ."product WHERE reference = '". pSQL(Tools::getValue('reference')) ."'"
            )
        );
        if (!$this->product->id || $this->product->reference != Tools::getValue('reference')) {
            $this->throwError('007');
        }
        $price = (float) trim(str_replace(',', '.', Tools::getValue('price')));
        $old_price = (float) trim(str_replace(',', '.', Tools::getValue('old_price')));
        $row = $this->getProductPrices($this->product->reference, true);

        if (!$price) {
            $this->throwError('008');
        }
        if ($this->product->price == $price) {
            $this->showResult();
        }
        $priceBefore = $this->product->price;
        $this->product->price = $price;
        $class = Tools::getValue('supplier');
        if (isset(self::$supplierConnectMap[$class])) {
            $class = self::$supplierConnectMap[$class];
        }
        if (class_exists($class) && method_exists($class, 'loadProducts')) {
            if (!Configuration::get('POSApi_'. $class)) {
                if (file_exists(_PS_ROOT_DIR_ .'/brw_feed/'. $class)) {
                    Configuration::updateValue('POSApi_'. $class, _PS_ROOT_DIR_ .'/brw_feed/'. $class);
                } else {
                    $this->throwError('003');
                }
            }
        }
        $importer = new $class(Configuration::get('POSApi_'. $class));
        $importer->updatePriceFile($reference, $old_price, $price);
        $this->showResult();
        if ($this->product->save()) {
            Migrate::insertLog($this->product, ' Changed price from OrderMax. Old: '. $priceBefore .', new: '. $this->product->price);
            PriceChangeLog::addChangelog($priceBefore, $price, $this->sources[Tools::getValue('apiKey')]);
            $this->showResult();
        } else {
            $this->throwError();
        }
    }
    public function updateQuantity($reference, $newPrice = null)
    {
        if (!Tools::getIsset('apiKey')){
            $this->throwError('005');
        }
        if (!isset($this->sources[Tools::getValue('apiKey')])) {
            $this->throwError('006');
        }
        if (!Tools::getIsset('reference') || !trim(Tools::getValue('reference'))) {
            $this->throwError('007');
        }
        if (!Tools::getIsset('supplier') || !trim(Tools::getValue('supplier'))) {
            $this->throwError('011');
        }
        if (!Tools::getIsset('quantity') || !trim(Tools::getValue('quantity'))) {
            $this->throwError('013');
        }
        $class = Tools::getValue('supplier');
        if (isset(self::$supplierConnectMap[$class])) {
            $class = self::$supplierConnectMap[$class];
        }
        if (class_exists($class) && method_exists($class, 'loadProducts')) {
            if (!Configuration::get('POSApi_'. $class)) {
                if (file_exists(_PS_ROOT_DIR_ .'/brw_feed/'. $class)) {
                    Configuration::updateValue('POSApi_'. $class, _PS_ROOT_DIR_ .'/brw_feed/'. $class);
                } else {
                    $this->throwError('003');
                }
            }
        }
        $importer = new $class(Configuration::get('POSApi_'. $class));

        /**
        header('Content-Type:application/json');
        echo json_encode('ok');
        exit;
        /**/
        $importer->updateQuantityFile($reference, Tools::getValue('quantity'));
        $this->showResult();
    }
    public function updateQuantityKey()
    {
        if (!Tools::getIsset('apiKey')){
            $this->throwError('005');
        }
        if (!isset($this->sources[Tools::getValue('apiKey')])) {
            $this->throwError('006');
        }
        if (!Tools::getIsset('supplier') || !trim(Tools::getValue('supplier'))) {
            $this->throwError('011');
        }
        $this->product = new Product(
            (int) Db::getInstance()->getValue(
                "SELECT id_product FROM ". _DB_PREFIX_ ."product WHERE reference = '". pSQL(Tools::getValue('reference')) ."'"
            )
        );
        $class = Tools::getValue('supplier');
        if (isset(self::$supplierConnectMap[$class])) {
            $class = self::$supplierConnectMap[$class];
        }
        if (class_exists($class) && method_exists($class, 'loadProducts')) {
            if (!Configuration::get('POSApi_'. $class)) {
                if (file_exists(_PS_ROOT_DIR_ .'/brw_feed/'. $class)) {
                    Configuration::updateValue('POSApi_'. $class, _PS_ROOT_DIR_ .'/brw_feed/'. $class);
                } else {
                    $this->throwError('003');
                }
            }
        }
        $importer = new $class(Configuration::get('POSApi_'. $class));
        $importer->updateQuantityKey();
        $this->showResult();

        /**/
        header('Content-Type:application/json');
        echo json_encode('ok');
        exit;
        /**/
    }
    public function getProductPrices($reference = null, $return = false)
    {
        if (!$reference) {
            $reference = Tools::getIsset('reference')
                ? Tools::getValue('reference')
                : false;
        }
        if ($reference) {
            $this->result['reference'] = $reference;
            $row = Db::getInstance()->getRow("
                SELECT p.reference, p.price, sp.reduction_type, sp.reduction
                FROM " . _DB_PREFIX_ . "product p
                LEFT JOIN " . _DB_PREFIX_ . "specific_price sp
                ON sp.id_product = p.id_product
                WHERE p.reference = '". pSQL($reference) . "'
            ");
            if ($row['reduction']) {
                $row['original_price'] = $row['price'];
                $row['price'] = $row['reduction_type'] == 'amount'
                    ? $row['price'] - $row['reduction']
                    : $row['price'] * $row['redution'];

            }
            $this->result['price'] = $row['price'];
            $this->result['original_price'] = $row['original_price'];
            if ($return === true) {
                return $this->result;
            }
            $this->showResult();
            if (!$this->result['price']) {
                $this->throwError('010');
            } else {
                $this->showResult();
            }
        } else {
            $products = Db::getInstance()->executeS("
                SELECT p.reference, p.price, sp.reduction_type, sp.reduction
                FROM " . _DB_PREFIX_ . "product p
                LEFT JOIN " . _DB_PREFIX_ . "specific_price sp
                ON sp.id_product = p.id_product
            ");
            $products = array_map(function($row){
                if ($row['reduction']) {
                    $row['original_price'] = $row['price'];
                    $row['price'] = $row['reduction_type'] == 'amount'
                        ? $row['price'] - $row['reduction']
                        : $row['price'] * $row['redution'];

                }
                return [
                    'reference' => $row['reference'],
                    'price' => $row['price'],
                    'original_price' => $row['original_price'],
                ];
            }, $products);
            $this->result['products'] = $products;
        }
    }
    public function actionUpdateDiscount()
    {
        $this->validateX(['apiKey', 'supplier']);
        $supplier = Tools::getValue('supplier');
        if (!Tools::getIsset('discount')) {
            $this->throwError('014');
        }
        Configuration::updateValue($supplier . 'Discount', abs((int) Tools::getValue('discount')));
        Configuration::updateValue($supplier . 'ExchangeRate', abs(Tools::getValue('rate')));
        $this->showResult();
    }
    public function actionUpdateExchangeRate()
    {
        $this->validateX(['apiKey', 'supplier']);
        $supplier = Tools::getValue('supplier');
        if (!Tools::getIsset('rate') || !trim(Tools::getValue('rate'))) {
            $this->throwError('015');
        }
        Configuration::updateValue($supplier . 'ExchangeRate', (float) str_replace(',', '.', Tools::getValue('rate')));

        /**/
        header('Content-Type:application/json');
        echo json_encode($supplier);
        exit;
        /**/
    }
    public function actionGetSupplierOptions()
    {
        $this->validateX(['apiKey']);
        $posapi = Module::getInstanceByName('posapi');
        $result = ['options' => []];
        foreach ($posapi -> getAllImporters() as $supplier => $v) {
            $result['options'][$supplier] = [
                'rate' => (float) Configuration::get($supplier . 'ExchangeRate') ?: 1,
                'discount' => (float) Configuration::get($supplier . 'Discount'),
            ];
        }
        $this->showResult($result);
    }
    public function actionLockProduct()
    {
        $this->validateX(['apiKey']);
        $locked = json_decode(
            Configuration::get(
                'ImportLockedProducts'
            ) ?: '[]',
            true
        );
        if (isset($locked[Tools::getValue('reference')])) {
            unset($locked[Tools::getValue('reference')]);
        } else {
            $locked[Tools::getValue('reference')] = Tools::getValue('reference');
        }

        Configuration::updateValue('ImportLockedProducts', json_encode($locked));

    }
    public function actionGetLockedProducts()
    {
        $this->validateX(['apiKey']);
        $locked = json_decode(
            Configuration::get(
                'ImportLockedProducts'
            ) ?: '[]',
            true
        );

        /**/
        header('Content-Type:application/json');
        echo json_encode($locked);
        exit;
        /**/
    }

    public function supplierExists($supplier)
    {
        $posapi = Module::getInstanceByName('posapi');
        foreach ($posapi -> getAllImporters() as $k => $v) {
            if ($k == $supplier) {
                return true;
            }
        }
        return false;
    }
    public function validateX($array)
    {
        if (in_array('apiKey', $array)) {
            if (!Tools::getIsset('apiKey')){
                $this->throwError('005');
            }
            if (!isset($this->sources[Tools::getValue('apiKey')])) {
                $this->throwError('006');
            }
        }
        if (in_array('supplier', $array)) {
            if (!Tools::getIsset('supplier') || !trim(Tools::getValue('supplier'))) {
                $this->throwError('011');
            }
            if (!$this->supplierExists(trim(Tools::getValue('supplier')))) {
                $this->throwError('004');
            }
        }
    }
    public function getSupplierId($supplier)
    {
        $subquery = '';
        if (isset(self::$supplierConnectMap[$supplier])) {
            $subquery .= " OR name LIKE '";
            $subquery .= implode("' OR name LIKE '", self::$supplierConnectMap[$supplier]);
            $subquery .= "'";
        } elseif ($supplier_name = $this->module->getOtherImporters($supplier)) {
            $subquery .= " OR name LIKE '" . $supplier_name ."'";
        }
        if (isset($this->supplierMap[$supplier])) {
            $supplier = $this->supplierMap[$supplier];
        }
        $subquery = '';
        if (isset(self::$supplierConnectMap[$supplier])) {
            $subquery .= " OR name LIKE '";
            $subquery .= implode("' OR name LIKE '", self::$supplierConnectMap[$supplier]);
            $subquery .= "'";
        } elseif ($supplier_name = $this->module->getOtherImporters($supplier)) {
            $subquery .= " OR name LIKE '" . $supplier_name ."'";
        }
        $id_suppliers = Db::getInstance()->executeS("SELECT id_supplier FROM ". _DB_PREFIX_ ."supplier WHERE name LIKE '". $supplier ."'". $subquery);
        if (empty($id_suppliers)) {
            $this->result['supplier_checked'] = $supplier;
            $this->throwError('004');
        }
        $suppliers = [];
        foreach ($id_suppliers as  $v) {
            if (isset($v['id_supplier']) && $v['id_supplier']) {
                $suppliers[] = $v['id_supplier'];
            }
        }
        if (preg_match('/szpak/', $_SERVER['HTTP_USER_AGENT'])) {
            if (empty($suppliers)) {
                $this->throwError('004');
            }
            $products = Db::getInstance()->executeS("
                SELECT id_product, reference FROM ". _DB_PREFIX_ ."product WHERE id_product IN(
                    SELECT id_product FROM ". _DB_PREFIX_ ."product_supplier
                    WHERE id_supplier IN(". implode(',', $suppliers) .")
                )
            ");
            /**/
            header('Content-Type:application/json');
            echo json_encode($products);
            exit;
            /**/
        }
    }
    public function getReferenceCombinationProducts()
    {

    }
    public function addPrestashopProductsToResult()
    {

    }
    public function actionGetLastFilesDate()
    {
        $importers = [];
        foreach ($this->module->getAllImporters() as $k => $v) {
            $class = $k;
            if (isset(self::$supplierConnectMap[$class])) {
                $class = self::$supplierConnectMap[$class];
            }

            if (class_exists($class) && method_exists($class, 'loadProducts')) {
                $file = new $class;
                $path = $file->getFilePath();
                $display_path = explode('/', $path, 7);
                $display_path = array_pop($display_path);

                $importers[$k] = [
                    'supplier' => $k,
                    'file' => $display_path,
                    'filemtime' => !$path || !file_exists($path) ? false : filemtime($file->getFilePath()),
                    'date' => !$path || !file_exists($path) ? false : date('Y-m-d H:i:s', filemtime($file->getFilePath()))
                ];
            }
        }

                /**/
                header('Content-Type:application/json');
                echo json_encode($importers);
                exit;
                /**/

        /**
        header('Content-Type:application/json');
        echo json_encode($this->module->getAllImporters());
        exit;
        /**/
    }
    public function actionSetSupplierMultiplier()
    {
        if (!Tools::getIsset('supplier') || !Tools::getIsset('multipliers')) {
            return false;
        }
        $supplier = Tools::getValue('supplier');
        $multipliers = Tools::getValue('multipliers');
        if (!is_array($multipliers)) {
            return false;
        }
        $config = [];
        foreach ($multipliers as $v) {
            /*/header('Content-Type: application/json');echo json_encode($v);exit;/**/
            if (!isset($v['from']) || !isset($v['to']) || !isset($v['multiplier'])) {
                continue;
            }
            $config[] = ['from' => $v['from'], 'to' => $v['to'], 'multiplier' => $v['multiplier']];
        }
        Configuration::updateValue($supplier.'Multipliers', json_encode($config));
    }
    public function getSupplierMultipliers()
    {
        /**/header('Content-Type: application/json');
        echo Configuration::get(Tools::getValue('supplier').'Multipliers');
        exit;/**/
    }
    public function actionGetMultipliers($supplier)
    {
        if (Configuration::Get($supplier.'MultiplierMore')) {
            /**/header('Content-Type: application/json');echo Configuration::Get($supplier.'MultiplierMore') ;exit;/**/
        }
    }
    public function getProductCategories($id_product)
    {
        $c = Db::getInstance()->executeS("
            SELECT id_category, name FROM ". _DB_PREFIX_ ."category_lang WHERE id_category IN(
                SELECT id_category FROM ". _DB_PREFIX_ ."category_product WHERE id_product = '". $id_product ."'
            )
        ");
        return $c;
    }
    public function getProductCategoriesByReference($reference)
    {
        $c = Db::getInstance()->executeS("
            SELECT id_category, name FROM ". _DB_PREFIX_ ."category_lang WHERE id_category IN(
                SELECT id_category FROM ". _DB_PREFIX_ ."category_product WHERE id_product IN(
                    SELECT id_product FROM ". _DB_PREFIX_ ."product WHERE reference LIKE '". pSQL($reference) ."'
                )
            ) AND id_lang = 1
        ");
        return $c ?: [];
    }
    public function getCategories()
    {
        $c = Db::getInstance()->executeS("
            SELECT id_category, name FROM ". _DB_PREFIX_ ."category_lang WHERE id_category IN(
                SELECT id_category FROM ". _DB_PREFIX_ ."category_product
            ) GROUP BY id_category
        ");
        $this->result['categories'] = $c;
    }
    public function actionGetFileInfo()
    {
   //     ini_set('display_errors', 'On');error_reporting(E_ALL);
        $supplier = preg_replace("/[^a-zA-Z0-9]/", "", Tools::getValue('getFileinfo'));
        if (!$supplier) {
            if (Tools::getValue('supplier')) {
                $supplier = preg_replace("/[^a-zA-Z0-9]/", "", Tools::getValue('supplier'));
            }
        }
        $importer = new ImporterNew($supplier);
        $this->result['file'] = $importer;
    }
    public function actionCheckApi()
    {

        /**/header('Content-Type: application/json');echo json_encode(Tools::getValue('supplier'));exit;/**/
    }
    public function actionMapKey()
    {
        $key = Tools::getValue('key');
        $header = Tools::getValue('header');
        $supplier = Tools::getValue('supplier');
        if ($key && $supplier) {
            $importer = new ImporterNew($supplier);
            if ($importer->file) {


                $importer->mapKey($key, $header);
                $this->result['file'] = $importer;
            }
        }
    }
    public function actionMapKeyStock()
    {
        $key = Tools::getValue('key');
        $header = Tools::getValue('header');
        $supplier = Tools::getValue('supplier');

        if ($key && $supplier) {
            $importer = new ImporterNew($supplier);
            if ($importer->file) {
                $importer->mapKeyStock($key, $header);
                $this->result['file'] = $importer;
            }
        }
    }
    public function actionSetDefaultValue()
    {
        $key = Tools::getValue('key');
        $value = Tools::getValue('value');
        $supplier = Tools::getValue('supplier');

        if ($key && $supplier) {
            $importer = new ImporterNew($supplier);
            if ($importer->file) {
                $importer->setDefaultValue($key, $value);
                $this->result['file'] = $importer;
            }
        }
    }
    public function actionSetImportOption()
    {
        $key = Tools::getValue('key');
        $value = Tools::getValue('value');
        $where = Tools::getValue('where');
        $supplier = Tools::getValue('supplier');
        if ($key && $supplier) {
            $importer = new ImporterNew($supplier);
            if ($importer->file) {
                $importer->setImportOption($key, $value, $where);
                $this->result['file'] = $importer;
            }
        }
    }
    public function actionSetOption()
    {
        $key = Tools::getValue('key');
        $value = Tools::getValue('value');
       $supplier = Tools::getValue('supplier');
        if ($key && $supplier) {
            $importer = new ImporterNew($supplier);
            if ($importer->file) {
                $importer->setOption($key, $value);
                $this->result['file'] = $importer;
            }
        }
    }
    public function actionSetMultiplierRanges()
    {
        $ranges = Tools::getValue('ranges');
        $supplier = Tools::getValue('supplier');
        if ($supplier) {
            $importer = new ImporterNew($supplier);
            if ($importer->file) {
                $importer->setMultiplierRanges($ranges);
                $this->result['file'] = $importer;
            }
        }
    }
    public function actionResetHeaderStock()
    {
        $supplier = Tools::getValue('supplier');
        if ($supplier) {
            $importer = new ImporterNew($supplier);
            $importer->resetHeaderStock();
            $this->result['file'] = $importer;
        }
    }
    public function actionResetHeader()
    {
        $supplier = Tools::getValue('supplier');
        if ($supplier) {
            $importer = new ImporterNew($supplier);
            $importer->resetHeader();
            $this->result['file'] = $importer;
        }
    }
    public function actionGetRow()
    {

        $supplier = Tools::getValue('supplier') ?: Tools::getValue('Supplier');
        $row_number = (int) Tools::getValue('row_number');
        if ($supplier) {
            $importer = new ImporterNew($supplier);
            if ($importer->file) {
                if ($row_number) {
                    $row_number -= 1;
                    for ($i = 0; $i < $row_number; $i++) {
                        $importer->skipRow();
                    }
                }
                $this->result['row'] = $importer->getRow(true);
                if (!$this->result['row']) {
                    $this->result['errors']  = $importer->errors;
                    $this->result['importer']  = $importer->options;
                    $this->result['supplier']  = $importer->supplier;
                }
            } else {
                $importer->validateMap();
                $this->result['errors']  = $importer->errors;
            }

        }
    }
    public function actionGetRowPOS()
    {
        $supplier = Tools::getValue('supplier') ?: Tools::getValue('Supplier');
        $row_number = (int) Tools::getValue('row_number');
        if ($supplier) {
            $importer = new ImportPOS($supplier);
            if ($importer->file) {
                if ($row_number) {
                    $row_number -= 1;
                }
                for ($i = 0; $i < $row_number; $i++) {
                    $row = $importer->skipRow();
                }
                $row = $importer->getRow();
                $this->result['row'] = $importer->getRowPOS(true);
                if (!$this->result['row']) {
                    $this->result['errors']  = $importer->errors;
                }
                        $this->result['file'] = json_encode($importer);

            } else {
                $importer->validateMap();
                $this->result['errors']  = $importer->errors;
            }
        }
    }
    public function actionImportRow()
    {
        $supplier = Tools::getValue('supplier');
        $row_number = (int) Tools::getValue('row_number');
        if ($supplier) {
            $importer = new ImporterNew($supplier);
            if ($importer->file) {
                if ($row_number) {
                    $row_number -= 1;
                }
                for ($i = 0; $i < $row_number; $i++) {
                    $importer->skipRow();
                }
                $row = $importer->getRow();
                $this->result['import_result'] = $importer->importRow();
                $this->result['error'] = Migrate::$importError;
            }
        }
        /**/header('Content-Type: application/json');echo json_encode($this->result);exit;/**/
    }
    public function actionImportRowPOS()
    {
        $supplier = Tools::getValue('supplier');
        $row_number = (int) Tools::getValue('row_number');
        if ($supplier) {
            $importer = new ImportPOS($supplier);
            if ($importer->file) {
                if ($row_number) {
                    $row_number -= 1;
                }
                for ($i = 0; $i < $row_number; $i++) {
                    $row = $importer->skipRow();
                }
                $row = $importer->getRow();

                $import = $importer->importRowPOS();
                $this->result['import_result'] = $import;
                $this->result['error'] = Migrate::$importError;
            }
        }
        /**/header('Content-Type: application/json');echo json_encode($this->result);exit;/**/
    }
    public function actionSetMultiplier()
    {
        $supplier = Tools::getValue('supplier');
        if ($supplier) {
            $importer = new ImporterNew($supplier);
            $importer->setMultiplier(
                Tools::getValue('multiplier')
            );
            $this->result['file'] = $importer;

        }
    }
    public function actionSetDiscount()
    {
        $supplier = Tools::getValue('supplier');
        if ($supplier) {
            $importer = new ImporterNew($supplier);
            $importer->setDiscount(
                Tools::getValue('discount')
            );
            $this->result['file'] = $importer;
        }
    }
    public function actionSetExchangeRate()
    {
        $supplier = Tools::getValue('supplier');
        if ($supplier) {
            $importer = new ImporterNew($supplier);
            $importer->setExchangeRate(
                Tools::getValue('exchange_rate')
            );
            $this->result['file'] = $importer;
        }
    }
    public function actionGetProductsNew($class = 'Brw', $full = true)
    {
        $class = Tools::getValue('supplier');
        /*/header('Content-Type: application/json');echo json_encode('ok');exit;/**/
        if (isset(self::$supplierConnectMap[$class])) {
            $class = self::$supplierConnectMap[$class];
        }
        $object = new ImporterNew($class);
            $object -> loadProducts();
            if ($full === true) {
                $this->loadPrestashopProducts($class);
                $products = [];
                $discount = $object->options['discount'] ?: 0;
                $exchange_rate = $object->options['exchange_rate'] ?: 1;
                foreach ($object->products as $k => $v) {
                    $v = (object) $v;
                    if (!$v->reference) {
                        continue;
                    }
                    if (!isset($this->products[$v->reference])) {
                        $this->products[$v->reference] = $this->getProductFromCombination($v->reference);
                    }
                    $our_price = '';
                    if (isset($v->price) && $v->price) {
                        $our_price = $v->price;
                    }
                    $v->price_before = $v->price;
                    $this->result['products'][$v->reference] = [
                        'reference' => $v->reference,
                        'name' => $v->name,
                        'supplier' => isset($object->options->supplier_name) ? $object->options->supplier_name : $class,
                        'prestashop_name' => isset($this->products[$v->reference]) ? $this->products[$v->reference]['name'] : '',
                        'supplier_cost' => $v->supplier_price,
                        'prestashop_price' => isset($this->products[$v->reference]) && $this->products[$v->reference]['price']
                            ? $this->products[$v->reference]['price']
                            : (isset($v->prestashop_price) ? $v->prestashop_price : 0),
                        'price_default' => isset($this->products[$v->reference]['price_default']) ? $this->products[$v->reference]['price_default'] : '',
                        'quantity' => $v->quantity,
                        'prestashop_quantity' => isset($this->products[$v->reference]) ? $this->products[$v->reference]['quantity'] : '',
                        'is_prestashop' => (bool) isset($this->products[$v->reference]),
                        'default_our_price' => $our_price,
                        'price' =>
                            $exchange_rate != 1
                            ? number_format($v->supplier_price * ((100 - $discount) /  100) * $exchange_rate, 2, '.', '')
                            : $v->supplier_price,
                        'supplier_cost_rated' => $exchange_rate != 1
                            ? number_format(($v->supplier_price * ((100 - $discount) /  100) * $exchange_rate), 2, '.', '')
                            : $v->supplier_price,
                        'is_locked' => $this->module->isProductLocked($v->reference),
                        'categories' => isset($this->products[$v->reference]) && $this->products[$v->reference]['categories']
                            ? $this->products[$v->reference]['categories']
                            : $this->getProductCategoriesByReference($v->reference),
                    ];
                }
                //$id_supplier = $this->getSupplierId($class);
              //  $this->result['products'] = $products;
                return;
            }
            foreach ($object -> products as $v) {
                //$this->result['products'][$v['reference']] = $v;
            }
    }
    public function actionLoadProducts()
    {
       // ini_set('display_errors', 'On');error_reporting(E_ALL);
        $supplier = Tools::getValue('supplier');
        $importer = new ImportPOS($supplier);
        $rows = [];
        while ($r = $importer->getRow()) {
            $row = $importer->getRowPOS();
            $rows[$row['reference']] = $row;
            if (count($rows) > 1000) {

                /*/header('Content-Type: application/json');echo json_encode($rows);exit;/**/
            }
        }
        $this->result['products'] = $rows;
        /**/header('Content-Type: application/json');echo json_encode($this->result);exit;/**/
    }
    public function actionLockQuantity()
    {
        $reference = Tools::getValue('reference');
        if ($reference) {
            ImporterNew::lockQuantity($reference, (bool) Tools::getIsset('pos'));
        }
        /**/header('Content-Type: application/json');echo json_encode(ImporterNew::isLockQuantity($reference));exit;/**/
    }
    public function actionChangeQuantity()
    {
        $product = Db::getInstance()->getRow(
            "SELECT id_product, id_product_attribute, reference FROM ". _DB_PREFIX_ ."product_attribute WHERE reference = '". pSQL(Tools::getValue('reference')) ."'"
        ) ?: Db::getInstance()->getRow(
            "SELECT id_product, reference, 0 AS id_product_attribute  FROM ". _DB_PREFIX_ ."product WHERE reference = '". pSQL(Tools::getValue('reference')) ."'"
        );

        StockAvailable::setQuantity($product['id_product'], $product['id_product_attribute'], (int) Tools::getValue('quantity'));
        if (!ImporterNew::isLockQuantity($product['reference'], false)) {
            ImporterNew::lockQuantity($product['reference'], (bool) Tools::getIsset('pos'));
        }


    }
}
