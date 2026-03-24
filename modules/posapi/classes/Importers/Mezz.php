<?php

/**
 * @author Łukasz Szpak ( info@dev-bot.pl )
 * @Copyright 2019 SzpaQ <dev-bot.pl>
 * @license ALL RIGHTS RESERVED
 * */
class Mezz extends Importer
{
    public $file;
    public $multiplier = 1;
    public $products = [];
    public static $stocks = false;
    public function loadProducts()
    {
        $this->multiplier = Configuration::get('MezzMultiplier') ?: 1;

        $products = json_decode(file_get_contents($this->file), true);
        foreach ($products as $v) {
            if (!self::isMezz($v['reference'])) {
                continue;
            }
            $v['categories'] = $v['suppliers'] = ['Mezz'];
            $this->products[] = $v;
        }
        return $this;
    }
    public static function isMezz($ref)
    {
        $result = json_decode(file_get_contents('http://stock.liquidationfurniture.ie/apii.php?is_mezz='. $ref));
        echo "CHECKING IF MEZZ: ". $ref. ' - ';
        if ($result) {
            ECHO " .................OK \n";
            return true;
        }
        echo '.................NO'."\n";
        return false;
    }
    public static function getStock($ean)
    {
        if (self::$stocks === false) {
            $file = simplexml_load_string(file_get_contents('https://halmar.pl/halmar_stock.xml'));
            $stocks = [];
            foreach ($file->children() as $k => $v) {
                switch ($v->stock)
                {
                    case 'out of stock':
                        $stock = 0;
                    break;
                    case 'available':
                        $stock = 5;
                    break;
                    case 'low stock':
                        $stock = 1;
                    break;
                }
                $stocks[$v->ean->__toString()] = $stock;
            }
            self::$stocks = $stocks;
        }
        return isset(self::$stocks[$ean]) ? self::$stocks[$ean] : 0;
    }
    public static function formatNumber($number)
    {
        if (!$number) {
            return 0;
        }
        $number = parent::formatNumber($number);
        if ($number < 5) {
            $number = 5;
        }
        $number = (round($number / 10) * 10) - 1;
        return parent::formatNumber($number);
    }
    public static function prepareFile($url, $destination)
    {
        $file = file_get_contents($url);
        if (!file_exists($destination)) {
            @mkdir($destination);
        }
        return file_put_contents($destination.'/Mezz', $file);

    }
    public static function getCategory($name)
    {
        $x = explode('-', $name, 2);
        return isset($x[1]) ? trim($x[1]) : trim($x[0]);
    }
    public static function synchro()
    {
        $id_products = Db::getInstance()->executeS("
            SELECT id_product FROM ". _DB_PREFIX_ ."product_supplier WHERE id_supplier = 2
        ");
        foreach ($id_products as $v) {
            $quantity = StockAvailable::getQuantityAvailableByProduct($v['id_product'], 0);
            if ($quantity > 0) {
                $product = new Product($v['id_product']);
                $stock = json_decode(file_get_contents('http://stock.liquidationfurniture.ie/apii.php?get_stock_warehouse='. $product->reference));
                if ($stock && $stock -> outside_supplier_reference_no) {
                    $products_id = Db::getInstance()->executeS("
                        SELECT id_product FROM ". _DB_PREFIX_ ."product WHERE reference LIKE '". pSQL($stock->outside_supplier_reference_no)."'
                    ");
                    foreach ($products_id as $w) {
                        $productNotMezz = new Product($w['id_product']);
                        if ($productNotMezz -> active) {
                            $productNotMezz -> active = 0;
                            $productNotMezz -> save();
                            Migrate::insertLog($productNotMezz, 'disabled because Mezz product found ('. $product->reference .').');
                        }
                    }
                }
            } else {
                $product = new Product($v['id_product']);
                $stock = json_decode(file_get_contents('http://stock.liquidationfurniture.ie/apii.php?get_stock_warehouse='. $product->reference));
                if ($stock && $stock -> outside_supplier_reference_no) {
                    $products_id = Db::getInstance()->executeS("
                        SELECT id_product FROM ". _DB_PREFIX_ ."product WHERE reference LIKE '". pSQL($stock->outside_supplier_reference_no)."'
                    ");
                    foreach ($products_id as $w) {
                        if (!StockAvailable::getQuantityAvailableByProduct($w['id_product'], 0)) {
                            continue;
                        }
                        $productNotMezz = new Product($w['id_product']);
                        if (!$productNotMezz -> active) {
                            $productNotMezz -> active = 1;
                            $productNotMezz -> save();
                            Migrate::insertLog($productNotMezz, 'enabled because Mezz product quantity ('. $product->reference .') equals 0 in pos.');
                            break;
                        }
                    }
                }
            }
        }
    }
}
