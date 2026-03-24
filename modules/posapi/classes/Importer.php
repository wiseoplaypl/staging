<?php

/**
 * @author Łukasz Szpak ( info@dev-bot.pl )
 * @Copyright 2019 SzpaQ <dev-bot.pl>
 * @license ALL RIGHTS RESERVED
 * */

class Importer
{
    public $file;
    public $multiplier = 2.5;
    public static $updateExisting = true;
    public static $productsAtOnce = 5;
    public static $pageFile = false;
    public static $page = false;
    public static $productsToKeepEnabled = [];

    public $schema = [
        'name' => '',
        'reference' => '',
        'categories' => [],
        'description_short' => '',
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
        'ean' => '',
    ];
    public function __construct($dir)
    {
        $this->file = $this->getFile($dir);
    }
    public function getProducts()
    {
        $this->loadProducts();
        foreach ($this->schema as $key => $v) {
            foreach ($this->products as $k => $product) {
                if (!isset($product[$key])) {
                    $this->products[$k][$key] = $v;
                }
            }
        }
        return $this->products;
    }
    public static function formatNumber($string)
    {
        return number_format((float) str_replace(',', '.', $string), 6, '.', '');
    }
    public function getFile($dir)
    {
        if (!file_exists($dir)) {
            @mkdir($dir);
        }
        $files = scandir($dir, SCANDIR_SORT_DESCENDING);
        return $dir.'/'.$files[0];
    }
    public function import($extra = [])
    {
        $productsToKeepEnabled = [];
        foreach ($this->getProducts() as $v) {
            if (Migrate::productExists($v['reference']) && self::$updateExisting === false) {
                continue;
            }
            self::$productsToKeepEnabled[$v['reference']] = $v['reference'];
            Migrate::importProduct($v, $extra);
        }

        return;
    }
    public static function getPage()
    {
        if (self::$page === false) {
            $tmpFile = self::getPageFile();
            self::$page = 0;
            if(file_exists($tmpFile)) {
                self::$page = (int) file_get_contents($tmpFile);
            }

            file_put_contents($tmpFile, self::$page + 1);
        }

        return self::$page;
    }
    public static function getPageFile()
    {
        $class = get_called_class();
        if ($class::$pageFile === false) {
            $class::$pageFile = sys_get_temp_dir().'/importer-'. $class;
        }
        return $class::$pageFile;
    }
    public static function clearPage()
    {
        return @unlink(self::getPageFile());
    }
}
