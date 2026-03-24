<?php

/**
 * @author Łukasz Szpak ( info@dev-bot.pl )
 * @Copyright 2019 SzpaQ <dev-bot.pl>
 * @license ALL RIGHTS RESERVED
 * */

class Brw extends Importer
{
    public $file;
    public $multiplier = 2.5;
    public $sofaMultiplier = 3;
    public $products = [];
    public function loadProducts()
    {
        $this->multiplier = Configuration::get('BRWMultiplier') ?: 2.5;
        $this->sofaMultiplier = Configuration::get('BRWSofasMultiplier') ?: 3;
        $xml = simplexml_load_string(file_get_contents($this->file));
        $i = 0;
        foreach ($xml->list->children() as $v) {
            $product = [
                'name' => '',
                'reference' => '',
                'categories' => [],
                'short_description' => '',
                'link_rewrite' => '',
                'meta_title' => '',
                'meta_keywords' => '',
                'meta_description' => '',
                'description' => '',
                'quantity' => 0,
                'weight' => 0,
                'price' => 0,
                'currency' => 'EUR',
                'features' => [],
                'images' => [],
                'attributes' => [],
                'suppliers' => [],
            ];
            if (
                !$v->prices ||
                !$v->prices->{"base-price"} ||
                !$v->prices->{"base-price"}->value ||
                !trim($v->prices->{"base-price"}->value->__toString())
            ) {
                continue;
            }
            $product['name'] =  $product['meta_title'] = preg_replace("/[^a-zA-Z0-9 ]+/", "", $v->title->__toString());
            $product['reference'] = $v->attributes->{"supplier-code"}->__toString();
            $product['link_rewrite'] = Tools::str2url($product['reference'] . ' '. $v->title->__toString());
            $product['categories'][] = "BRW//" . self::getCategory($v->{"category-name"}->__toString());
            $product['description_short'] = $v->{"short-description"}->__toString();
            $product['description'] = $v->{"long-description"}->__toString();
            $product['meta_description'] = '';
            $product['meta_title'] = '';
            $product['price'] = self::formatNumber(Importer::formatNumber($v->prices->{"promotion-price"}->value->__toString()) * $this->getMultiplier($v));
            $product['price_before'] = number_format($v->prices->{"promotion-price"}->value->__toString(), 6, '.','');
            $product['currency'] = $v->prices->{"base-price"}->currency->__toString();
            $product['weight'] = Importer::formatNumber($v->weight->__toString());
            $product['quantity'] = (int) Importer::formatNumber($v->quantity->__toString());
            $product['suppliers'][] = $this->getSupplierName($v);
            foreach ($v->properties->children() as $property) {
                $product['features'][$property->id->__toString()] = $property->values->value->__toString();
            }
            foreach ($v->images->children() as $image) {
                $product['images'][] = $image->url->__toString();
            }
            $this->products[] = $product;
        }
        return $this;
    }
    public function getMultiplier($v)
    {
        return self::isSofa($v)
            ? $this->sofaMultiplier
            : $this->multiplier;
    }
    public function getSupplierName($v)
    {
        return self::isSofa($v)
            ? 'BRW Sofas'
            : 'BRW';
    }
    public static function isSofa($v)
    {
        return (bool) (
            preg_match('/Sofa/i', $v->title->__toString()) ||
            preg_match('/Sofa/i', $v->{'category-name'}->__toString()) ||
            preg_match('/Sofa/i', $v->{'short-description'}->__toString())
        );
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
    public static function getCategory($name)
    {
        $x = explode('-', $name, 2);
        return isset($x[1]) ? trim($x[1]) : trim($x[0]);
    }
}
