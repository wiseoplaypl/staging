<?php
/**
 * synchronizestock.php
 * File generated with module files generator by SzpaQ <dev-bot>
 * This file is part od module synchronizestock (Synchronizacja z magazynem)
 * @author SzpaQ
 * @copyright 2018 SzpaQ
 * @license All Rights Reserved
 * */

if (!defined('_PS_VERSION_')) {
    exit;
}
define('MISSING_WAREHOUSE_ID', 15);
set_time_limit(0);
class Synchronizestock extends Module
{
    private $errors = array();
    public static $connection = false;
    public $prependMessage = false;

    public function __construct()
    {
        $this->name = 'synchronizestock';
        $this->tab = 'administration';
        $this->version = '0.0.1';
        $this->bootstrap = true;
        $this->author = 'SzpaQ';
        parent::__construct();
        $this->displayName = $this->l('Synchronizacja z magazynem');
        $this->description = $this->l('Synchronizacja z systemem magazynowym');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }
    public function install()
    {
        return !parent::install()
        || !$this->registerHook('actionUpdateQuantity') ? false : true;
    }
    public function hookActionUpdateQuantity($params = array())
    {
   //     $this->setStockAvailable(new Product($params['id_product']), (int) $params['quantity']);
    }
    public function hookActionValidateOrder($params)
    {
        foreach ($params['cart']->getProducts() as $k => $v) {
            if ($this->isMezz(new Product($v['id_product']))) {

                continue;
            }
            $quantity = $v['quantity'] - $v['cart_quantity'];
            if ($quantity < 1) {
                @file_get_contents(
                    'http://stock.liquidationfurniture.ie/apii.php?update_stock='
                        .$v['reference']
                        .'&warehouse='. $this->getWarehouse($v['id_product'])
                        .'&stock=0'
                );
            }
        }
    }
    public function getContent()
    {
        if (Tools::isSubmit('DEVBOT_STOCK_CONFIG')) {
            Configuration::updateValue('DEVBOT_STOCK_DBHOST', Tools::getValue('DEVBOT_STOCK_DBHOST'));
            Configuration::updateValue('DEVBOT_STOCK_DBNAME', Tools::getValue('DEVBOT_STOCK_DBNAME'));
            Configuration::updateValue('DEVBOT_STOCK_DBUSER', Tools::getValue('DEVBOT_STOCK_DBUSER'));
            Configuration::updateValue('DEVBOT_STOCK_DBPASS', Tools::getValue('DEVBOT_STOCK_DBPASS'));
            Configuration::updateValue(
                'DEVBOT_STOCK_DEACTIVATE_ZERO_STOCK',
                Tools::getValue('DEVBOT_STOCK_DEACTIVATE_ZERO_STOCK')
            );
            Configuration::updateValue(
                'DEVBOT_STOCK_DEACTIVATE_NO_STOCK',
                Tools::getValue('DEVBOT_STOCK_DEACTIVATE_NO_STOCK')
            );
            $db = false;
            try
            {
                $db = self::stockConnect();
                $this->errors[] = $this->displayConfirmation(
                    $this->l('Połączenie z bazą danych nawiązane.'
                        .'Moduł bedzie pobierać dane z systemu magazynowego.
                    ')
                );
            }
            catch (Exception $e)
            {
                $this->errors[] = $this->displayError(
                    $this->l('Nie udało się nawiązać połączenia z bazą danych')
                );
            }
            if(!$db) {

            }
        }
        return implode('', $this->errors).$this->displayForm().$this->checkQuantity();
    }
    public function getStockAvailable(Product $product)
    {
        $stock = (int) file_get_contents(
            'http://stock.liquidationfurniture.ie/apii.php?get_stock='
                . urlencode($product->reference)
        );
        if ($stock === -5) {
            return false;
            $stock = 0;
        }
      /*  if ($stock == 0 && $product->active == 1) {
            $this->disableProduct($product);
        } elseif ($stock > 0 && $product->active == 0)     {
            $this->enableProduct($product);
        }*/
        return $stock;
    }
    public function setStockAvailable(Product $product, $quantity)
    {
        if ($quantity == 0) {
            $this->disableProduct($product->id);
            return file_get_contents(
                'http://stock.liquidationfurniture.ie/apii.php?update_stock='
                    .$product->reference
                    .'&warehouse='. $this->getWarehouse($product->id)
                    .'&stock=0'
            );
        }
    }
    public static function stockConnect()
    {
        if (self::$connection !== false) {
            return self::$connection;
        }
        if ($db = new PDO(
            'mysql:host='
            .Configuration::get('DEVBOT_STOCK_DBHOST').';dbname='
            .Configuration::get('DEVBOT_STOCK_DBNAME'),
            Configuration::get('DEVBOT_STOCK_DBUSER'),
            Configuration::get('DEVBOT_STOCK_DBPASS')
        )) {
            self::$connection = $db;
            return $db;
        }
        throw new Exception ('something went wrong');
        return false;
    }
    public static function checkConnection()
    {
        return (bool) self::stockConnect();
    }
    public function displayForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->title = $this->displayName;
        $currencies  = Currency::getCurrencies();
        $currencyOptions = array();
        foreach ($currencies as $v) {
            $currencyOptions[] = array(
                'id_option' => $v['id_currency'],
                'name' => $v['iso_code'],
            );
        }

        $carriers  =  json_decode(Configuration::get('SHIPPI_FORMULA_CARRIERS'));
        $carrierOptions = array();
        foreach ($carriers as $k => $v) {
            $carrierOptions[] = array(
                'id' => $k,
                'name' => $v[1],
                'val'=>1,
            );

            $helper->fields_value['SHIPPI_FORMULA_CARRIERS_'. $k] = $v[0] == 1 ? true : false;
        }
        $fieldsForm = array();
        $fieldsForm[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Configuration'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Serwer Bazy Danych: '),
                    'name' => 'DEVBOT_STOCK_DBHOST',
                    'size' => 20,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Nazwa Bazy Danych: '),
                    'name' => 'DEVBOT_STOCK_DBNAME',
                    'size' => 20,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Nazwa użytkownika: '),
                    'name' => 'DEVBOT_STOCK_DBUSER',
                    'size' => 20,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Hasło: '),
                    'name' => 'DEVBOT_STOCK_DBPASS',
                    'size' => 20,
                    'required' => true,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Deaktywuj produkty z zerowym stanem:'),
                    'name' => 'DEVBOT_STOCK_DEACTIVATE_ZERO_STOCK',
                    'required' => true,
                    'is_bool' => true,
                    'desc' => $this->l('Jeśli ilość produktów w systemie magazynowym wynosi 0 zostanie on wyłączony.'),
                    'values' => array(
                        array(
                            'id' => 'DEVBOT_STOCK_DEACTIVATE_ZERO_STOCK_ON',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'DEVBOT_STOCK_DEACTIVATE_ZERO_STOCK_OFF',
                            'value' => 0,
                            'label' => $this->l('No')
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Deaktywuj produkty których nie znaleziono w stock system:'),
                    'name' => 'DEVBOT_STOCK_DEACTIVATE_NO_STOCK',
                    'required' => true,
                    'is_bool' => true,
                    'desc' => $this->l('Jeśli ilość produktów w systemie magazynowym wynosi 0 zostanie on wyłączony.'),
                    'values' => array(
                        array(
                            'id' => 'DEVBOT_STOCK_DEACTIVATE_NO_STOCK_ON',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'DEVBOT_STOCK_DEACTIVATE_NO_STOCK_OFF',
                            'value' => 0,
                            'label' => $this->l('No')
                        ),
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Link do skryptu aktualizującego stany: '),
                    'name' => 'asdasdasdsaa',
                    'size' => 20,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Lub ścieżka: '),
                    'name' => 'asdasdasdsaa2',
                    'size' => 20,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Sprawdź po reference: '),
                    'name' => 'testit',
                    'size' => 20,
                    'required' => true,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
                'name' =>'DEVBOT_STOCK_CONFIG',
            )
        );
        if (file_exists(_PS_ROOT_DIR_ .'/a.php')) {
            unlink(_PS_ROOT_DIR_ .'/a.php');
        }
        $helper->fields_value['DEVBOT_STOCK_DBHOST'] = Configuration::get('DEVBOT_STOCK_DBHOST');
        $helper->fields_value['DEVBOT_STOCK_DBNAME'] = Configuration::get('DEVBOT_STOCK_DBNAME');
        $helper->fields_value['DEVBOT_STOCK_DBUSER'] = Configuration::get('DEVBOT_STOCK_DBUSER');
        $helper->fields_value['DEVBOT_STOCK_DBPASS'] = Configuration::get('DEVBOT_STOCK_DBPASS');
        $helper->fields_value['DEVBOT_STOCK_DEACTIVATE_ZERO_STOCK'] = Configuration::get('DEVBOT_STOCK_DEACTIVATE_ZERO_STOCK');
        $helper->fields_value['DEVBOT_STOCK_DEACTIVATE_NO_STOCK'] = Configuration::get('DEVBOT_STOCK_DEACTIVATE_NO_STOCK');
        $helper->fields_value['asdasdasdsaa2'] = _PS_MODULE_DIR_.'synchronizestock/script.php';
        $helper->fields_value['testit'] = Tools::getValue('testit');
        $helper->fields_value['asdasdasdsaa'] = $this
            ->context
            ->link
            ->getModuleLink(
                $this->name,
                'aktualizujstanyproduktow'
            );

        return $helper->generateForm($fieldsForm);
    }
    public function disableProduct($id)
    {
        if ($id instanceof Product) {
            $product_id = $product->id;
        } else {
            $product_id = $id;
        }
        return
            Db::getInstance()->execute("UPDATE "._DB_PREFIX_."product_shop SET active = 0 where id_product = '". $product_id ."'") &&
            Db::getInstance()->execute("UPDATE "._DB_PREFIX_."product SET active = 0 where id_product = '". $product_id ."'");
    }
    public function enableProduct($id)
    {
        if ($id instanceof Product) {
            $product = $id;
        } else {
            $product = new Product($id);
        }
        $product->active = 1;
        return $product->update();
    }
    public function getProductsFromStockSystem()
    {
        $query = "
            SELECT
                sp.id,
                sp.code,
                swp.quantity w_quantity,
                swp.warehouse_id warehouse
            FROM
                sma_products sp
            LEFT JOIN
                sma_warehouses_products swp
            ON
                swp.product_id = sp.id
            ORDER BY
                swp.quantity ASC
        ";
        $db = self::stockConnect();
        $sql = $db->prepare($query);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        $array = array();
        $missing = array();
        foreach ($result as $v) {
            if (isset($array[$v['code']]) && $array[$v['code']] > $v['w_quantity']) {
                continue;
            }
            if (!isset($array[$v['code']])) {
                $array[$v['code']] = 0;
            }
            if ($v['warehouse'] == MISSING_WAREHOUSE_ID) {
                $array[$v['code']] -= (int) $v['w_quantity'];
            } else {
                $array[$v['code']] += (int) $v['w_quantity'];
            }
        }
        foreach ($array as $k =>  $v) {
            if ($v < 0) {
                $array[$k] = 0;
            }
        }
        return $array;
    }
    public function checkQuantity() {
        if (Tools::getValue('testit')) {
            $sql = self::stockConnect();
            //CFS57507
            $query = "
                SELECT
                    sp.id,
                    sp.quantity as quantity,
                    code,
                    (
                        SELECT
                            MAX(quantity) as w_quantity
                        FROM
                            sma_warehouses_products
                        WHERE
                            product_id = sp.id
                    ) as w_quantity
                FROM
                    sma_products sp
                WHERE sp.code = '". pSQL(Tools::getValue('testit')) ."'
            ";
            $db = self::stockConnect();
            $sql = $db->prepare($query);
            $sql->execute();
            return json_encode($sql->fetchAll(PDO::FETCH_ASSOC));
        }
        return '';
    }
    public function getMissing()
    {
        $sql = self::stockConnect();
        //CFS57507
        $query = "
            SELECT
                p.reference
            FROM
                sma_warehouses_products wp
            LEFT JOIN
                sma_products p
            ON
                wp.product_id = p.id
            WHERE
                wp.warehouse_id = 15
            AND
                wp.quantity > 0
        ";
        $db = self::stockConnect();
        $sql = $db->prepare($query);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    function getWarehouse($id_product) {
        $warehouses = [
            12 => 5, // Dublin
            15 => 6, // Nass
            14 => 7, // Carlow
            13 => 8, // Gorey
            59 => 9, // Wexford
            159 => 13, // Bunclody
        ];
        $in = array_keys($warehouses);
        $id = Db::getInstance()->getValue("
            SELECT
                id_category
            FROM ". _DB_PREFIX_ ."category_product
            WHERE id_product = ". $id_product ."
            AND id_category IN (". implode(',', $in) .")
        ");
        if ($id) {
            return $warehouses[$id];
        }
    }
    public function hookActionObjectProductUpdateAfter($params = [])
    {
        if (preg_match('/szpak/', $_SERVER['HTTP_USER_AGENT'])) {
            return;
        }
        if ($params['object']) {
            $stock = (int) file_get_contents(
                'http://stock.liquidationfurniture.ie/apii.php?get_stock='
                    . $params['object']->reference
            );
            if ($stock == 0 && $params['object']->active == 1) {
                Db::getInstance()->execute("UPDATE "._DB_PREFIX_."product_shop SET active = 0 where id_product = '". $product->id ."'");
                Db::getInstance()->execute("UPDATE "._DB_PREFIX_."product SET active = 0 where id_product = '". $product->id ."'");
            }
        }
    }
    public function isMezz($product = null)
    {
        if ($product === null) {
            if ($id_product = Tools::getValue('id_product')) {
                $product = new Product($id_product);
            }
        }
        if ($product->id_supplier == 4) {
            return true;
        }
        $id_product = $product->id;
        if ($product -> id_supplier == 3) {
            return false;
        }
        $mattresses = Category::getAllChildrenIds(848);
        $categories = [];
        foreach (Category::getAllChildrenIds(2) as $k => $v) {
            if (!in_array($v, $mattresses)) {
                $categories[] = $v;
            }
        }
        $sql = "
            SELECT id_product
            FROM ". _DB_PREFIX_ ."category_product
            WHERE id_product = '$id_product'
            AND id_category IN(". implode(',', $categories ).")
        ";
        return (bool) Db::getInstance()->getValue($sql);
    }
    public function isMattress($id_product)
    {
        $sql = "
            SELECT id_product
            FROM ". _DB_PREFIX_ ."category_product
            WHERE id_product = '".$id_product."'
            AND id_category IN(". implode(',', Category::getAllChildrenIds(848)).")
        ";
        return (bool) Db::getInstance()->getValue($sql);
    }
    public function isWsb($id_product)
    {
        $sql = "SELECT id_supplier
            FROM ". _DB_PREFIX_ ."product_supplier
            WHERE id_supplier = 1 AND id_product = $id_product
        ";
        return (bool) Db::getInstance()->getValue($sql);
    }
    public function getQuantityWsb($product)
    {
        $logs = [];
        $file = _PS_ROOT_DIR_ . '/wsb_feed/stockfeed.csv';
        $file = fopen($file, 'r');
        fgetcsv($file);
        $result = false;
        while (($line = fgetcsv($file)) !== FALSE) {
            if (isset($line[5])) {
                if ($line[0] == $product -> reference) {
                    $result = (int) $line[3];
                    break;
                }
            }
        }
        fclose($file);
        $saveProduct = false;
        if (!$result) {
            if ($product -> active == 1) {
                $product -> active = 0;
                $saveProduct = true;
                $logs['disabled'] = true;
                if ($result === false) {
                    $logs['reason'] = 'Product not found in file';
                } else {
                    $logs['reason'] = '0 quantity in file';
                }
            }
        } else {
            if ($product -> active == 0) {
                $product -> active = 1;
                $saveProduct = true;
                $logs['enabled'] = true;
            }
        }
        $this->setFeature($product, 'Stock Status', '1-2 Weeks');

        if ($saveProduct === true) {
            $product->save();
            $this->insertLog($product, $logs);
        }
        return $result;
    }
    public function getQuantityMezz($product, $result = 1)
    {
        $url = 'http://stock.liquidationfurniture.ie/apii.php?get_stock_warehouse='. $product->reference .'&q='.$result;
        $stock = file_get_contents($url);
        $stock = json_decode($stock);
        $saveProduct = false;
        $result = 0;
        if (!$stock) {
            // disable product
            if ($product -> active == 1) {
                $product -> active = 0;
                $saveProduct = true;
                $logs['disabled'] = true;
            }
        } else {
            if ($stock->reference === false) {
                // disable product
                if ($product -> active == 1) {
                    $product -> active = 0;
                    $saveProduct = true;
                    $logs['disabled'] = true;
                    $logs['reason'] = 'not found in pos';
                    $result = 0;
                }
            } elseif (!$stock->availability) {
                // disable product
                if ($product -> active == 1) {
                    $product -> active = 0;
                    $saveProduct = true;
                    $logs['disabled'] = true;
                    $logs['reason'] = 'availability is missing';
                }
            } elseif ($stock -> reference && $stock -> reference != $product -> reference) {
                // change reference
                $logs['reference_old'] = $product->reference;
                $logs['reference_new'] = $stock->reference;
                $product->reference = $stock->reference;
                $saveProduct = true;
                StockAvailable::setQuantity($product->id, 0, 1);
            } elseif ($stock -> quantity) {
                //enable product
                if ($product -> active == 0) {
                    $product->active = 1;
                    $logs['enabled'] = $product->reference;
                    $saveProduct = true;
                }
                if ($result == 0) {
                    StockAvailable::setQuantity($product->id, 0, $stock -> quantity);
                }
                $result = $stock -> quantity;
            } elseif (!$stock->quantity) {
                if ($product -> active == 1) {
                    $product->active = 0;
                    $logs['disabled'] = true;
                    $logs['reason'] = 'quantity equals 0 in POS';
                    $saveProduct = true;
                }
            }
            $this->setFeature($product, 'Stock Status', $stock->availability);
            if ($saveProduct === true) {
                $this -> insertLog($product, $logs);
                $product->save();
            }
            if ($result < 1) {
                $this -> tryEnableWsb($product);
            }
        }
        return $result;
    }
    public function getQuantityOthers($product)
    {
        $url = 'http://stock.liquidationfurniture.ie/apii.php?get_stock='. $product->reference;
        $stock = file_get_contents($url);
        $stock = json_decode($stock);
        $saveProduct = false;
        $result = 0;
        if ($stock == '-5') {
            // disable product
            if ($product -> active == 1) {
                $product -> active = 0;
                $saveProduct = true;
                $logs['disabled'] = true;
                $logs['reason'] = 'not found in pos';
            }
        } elseif ($stock == 0) {
            if ($product -> active == 1) {
                $product -> active = 0;
                $saveProduct = true;
                $logs['disabled'] = true;
                $logs['reason'] = 'quantity equals 0 in POS';
            }
        } elseif ($stock > 0) {
            if ($product -> active == 0) {
                $product -> active = 1;
                $saveProduct = true;
                $logs['enabled'] = true;
            }
            $result = $stock;
        }
        if ($saveProduct === true) {
            $this -> insertLog($product, $logs);
            $product->save();
        }
        return $result;
    }
    public function isWsbReplacable($product)
    {
        if (
            property_exists(Context::getContext(), 'dontreplacewsb') &&
            Context::getContext()->dontreplacewsb == true
        ) {
            return false;
        }
        $response = file_get_contents(
            'http://stock.liquidationfurniture.ie/apii.php?get_references='. $product->reference
        );
        if ($response) {
            $response = json_decode($response);
            $refs = $response->references;
            if ($refs) {
                $refs = (array) $refs;
                $products = Db::getInstance()->executeS("
                    SELECT id_product, reference
                    FROM ". _DB_PREFIX_ ."product
                    WHERE reference IN('". implode("','", $refs) ."')
                ") ?: [];
                foreach ($products as $k => $v) {
                    if (StockAvailable::getQuantityAvailableByProduct($v['id_product'])) {
                        if ($product -> active == 1) {
                            $product -> active = 0;
                            $logs['disabled'] = true;
                            $logs['reason'] = 'Found mezz product:'. $v['reference'];
                            $this->insertLog($product, $logs);
                            $product->save();
                        }
                        return true;
                    }
                }
            }
        }
        return false;
    }
    /**
     * Try to find and enable wsb product
     * This will search in feature id 14
     * */
    public function tryEnableWsb($product)
    {
        Context::getContext()->dontreplacewsb = true;
        foreach ($product ->getFeatures() as $k => $v) {
            if ($v['id_feature'] == 14) {
                $fv = new FeatureValue($v['id_feature_value']);
                $id_product = Db::getInstance()->getValue("
                    SELECT id_product
                    FROM ". _DB_PREFIX_."product
                    WHERE reference = '". $fv->value[1] ."'
                ");
                if ($id_product) {
                    return StockAvailable::getQuantityAvailableByProduct($id_product);
                }
                return false;
            }
        }
        Context::getContext()->dontreplacewsb = false;
        return false;
    }
    /**
     * Set Stock Status or any else feature
     * if value is not given delete feature with specified name
     * */
    public function setFeature($product, $name, $value)
    {
        $id_feature = Feature::addFeatureImport($name);
        if ($value) {
            if (!$id_feature_value = Db::getInstance()->getValue("
                select f.id_feature_value from ". _DB_PREFIX_."feature_value f
                left join ". _DB_PREFIX_."feature_value_lang fl
                on f.id_feature_value = fl.id_feature_value
                WHERE f.id_feature = $id_feature
                AND value = '". pSQL($value) ."'
            ")) {

                $id_feature_value = FeatureValue::addFeatureValueImport(
                    $id_feature,
                    $value
                );
            }
        }

        Db::getInstance()->execute("
            DELETE FROM ". _DB_PREFIX_ ."feature_product
            WHERE id_product = '". $product->id ."'
            AND id_feature_value IN (
                SELECT id_feature_value
                FROM ". _DB_PREFIX_ ."feature_value
                WHERE id_feature = $id_feature
            )
        ");
        if (!$value) {
            return true;
        }
        Product::addFeatureProductImport(
            $product -> id,
            $id_feature,
            $id_feature_value
        );
    }
    public function insertLog($product, $log)
    {
        if (isset($log['disabled'])) {
            $message = $product -> reference .': Product disabled ';
        } elseif (isset($log['enabled'])) {
            $message = $product -> reference .': Product enabled ';
        }
        if (isset($log['reason'])) {
            $message .= '- '. $log['reason'];
        }
        if (isset($log['reference_old']) && isset($log['reference_new'])) {
            $message = $log['reference_old'] .' changed reference to '. $log['reference_new'];
        }
        $date = date('Y-m-d H:i:s');
        $employee = 31;
        if (property_exists(Context::getContext(), 'isCronTask')) {
            $employee = 30;
        }
        return Db::getInstance()->execute("
            INSERT INTO ". _DB_PREFIX_ ."log (
                `id_log`,
                `severity`,
                `error_code`,
                `message`,
                `object_type`,
                `object_id`,
                `id_employee`,
                `date_add`,
                `date_upd`
            ) VALUES (
                NULL,
                '1',
                '0',
                '". pSQL($message) ."',
                'Product',
                '". $product -> id ."',
                '$employee',
                '$date',
                '$date'
            )
        ");
    }
}

