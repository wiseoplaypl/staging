<?php

/**
 * @author Łukasz Szpak ( info@dev-bot.pl )
 * @Copyright 2019 SzpaQ <dev-bot.pl>
 * @license ALL RIGHTS RESERVED
 * */

class Garden extends Importer
{
    public $file;
    public $multiplier = 2.5;
    public $products = [];
    public static $stocks = false;
    public function loadProducts()
    {
        $file = json_decode(file_get_contents('https://liquidationfurniture.ie/expP.php'), true);
        foreach ($file as $v) {
            $v['ean'] = $v['ean13'];
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
}
