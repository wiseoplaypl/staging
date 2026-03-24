<?php

/**
 * @author Łukasz Szpak ( info@dev-bot.pl )
 * @Copyright 2019 SzpaQ <dev-bot.pl>
 * @license ALL RIGHTS RESERVED
 * */
class Bru extends Importer
{
    public $file;
    public $multiplier = 1;
    public $products = [];
    public static $categoryNames = [];
    public function loadProducts()
    {
        $this->multiplier = 1;
        $csv = new CsvObject(dirname(__FILE__).'/../../bru.csv');
        while ($row = $csv -> getRow())
        {
            if (preg_match('/ignore/', $row->Name) || preg_match('/IGNORE/', $row->Name)) {
                continue;
            }
            if (strlen($row->Categories) < 3) {
                $row->Categories = 'BRW';
            }
            $categories = explode(',', $row->Categories);
            $categories = array_map(
                function($cat) {
                    return Bru::findCategoryFullPath(trim($cat));
                },
                $categories
            );
            $categories = array_filter($categories);
            $categories[] = 'MF-Collections//'.$row->Collection;

            /**
            header('Content-Type: application/json');
            echo json_encode($categories);
            exit;
            /**/
            $product = [
                'name' => $row->Name,
                'reference' => $row->reference,
                'categories' => $categories,
                'short_description' => $row -> desc_short,
                'link_rewrite' => $row -> seo,
                'meta_title' => '',
                'meta_keywords' => '',
                'meta_description' => '',
                'description' => $row->Description,
                'quantity' => $row->Quantity,
                'weight' => $row->Weight,
                'price' => $row->price,
                'currency' => 'EUR',
                'features' => [],
                'images' => [],
                'attributes' => [],
                'suppliers' => [],
            ];
            $this->products[] = $product;
        }

        return $this;
    }
    public static function findCategoryFullPath($name)
    {
        if (!isset(self::$categoryNames[$name])) {
            $id_category = Db::getInstance()->getValue("
                SELECT id_category
                FROM " . _DB_PREFIX_ ."category_lang
                WHERE name LIKE '". pSQL($name) ."'
                ORDER BY id_category ASC
            ");
            if ($id_category) {
                self::$categoryNames[$name] = Migrate::getCategoryPath($id_category);
            } else {
                self::$categoryNames[$name] = null;
            }
        }
        return self::$categoryNames[$name];
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
