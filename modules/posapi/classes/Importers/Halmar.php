<?php

/**
 * @author Łukasz Szpak ( info@dev-bot.pl )
 * @Copyright 2019 SzpaQ <dev-bot.pl>
 * @license ALL RIGHTS RESERVED
 * */

class Halmar extends Importer
{
    public $file;
    public $multiplier = 2.5;
    public $products = [];
    public static $stocks = false;
    public function loadProducts()
    {
        $xml = simplexml_load_string(file_get_contents($this->file));
        foreach ($xml->children() as $v) {

            if (!preg_match('/85\-KR\-POPIEL/', $v->code->__toString())) {
           //     continue;
            }
            if ($v->price_currency == 'EUR') {
                $this->multiplier = 2.5;
            } else {
                $this->multiplier = 0.544662309;
            }
            $HalmarMultiplier = Configuration::get('HalmarMultiplier') ?: 2.325;
            $HalmarVCHMultiplier = Configuration::get('HalmarVCHMultiplier') ?: 2.125;
            $this->multiplier = preg_match('/V\-CH/', $v->code->__toString()) ? $HalmarVCHMultiplier : $HalmarMultiplier;
            $this->discount = preg_match('/V\-CH/', $v->code->__toString()) ? .85 : .93;
            $price = self::formatNumber($v->price2->__toString() * $this->multiplier);
          //  $price = self::formatNumber($price * $this->discount) / $this->discount;

            $product = [
                'name' => $result = preg_replace("/[^a-zA-Z0-9\-\/ ]+/", '', $v->name->__toString()),
                'reference' => $v->code->__toString(),
                'ean' => $v->ean->__toString(),
                'categories' => ['Halmar//'.$v->category->__toString()],
                'short_description' => '',
                'link_rewrite' => '',
                'meta_title' => '',
                'meta_keywords' => '',
                'meta_description' => '',
                'description' => $v->description->__toString(),
                'quantity' => self::getStock($v->ean->__toString()),
                'weight' => $v->gros_weight->__toString(),
                'price' => number_format($price, 6, '.', ''),
                'price_before' => number_format($v->price2->__toString(), 6, '.', ''),
                'currency' => $v->price_currency->__toString(),
                'features' => [],
                'images' => [],
                'attributes' => [],
                'suppliers' => [],
            ];
            $product['link_rewrite'] = Tools::str2url($product['reference'] . ' '. $product['name']);
            $product['meta_description'] = $product['description'] ? substr(strip_tags($product['description']),0,100) : '';
            $product['suppliers'][] = 'Halmar';
            if ($v->pictures) {
                foreach ($v->pictures->children() as $image) {
                    $product['images'][] = $image->__toString();
                }
            }
            $this->products[] = $product;

        }
        return $this;
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
        return file_put_contents($destination.'/helmar', $file);

    }
    public static function getCategory($name)
    {
        $x = explode('-', $name, 2);
        return isset($x[1]) ? trim($x[1]) : trim($x[0]);
    }
}
