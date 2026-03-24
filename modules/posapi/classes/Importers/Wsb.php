<?php

/**
 * @author Łukasz Szpak ( info@dev-bot.pl )
 * @Copyright 2019 SzpaQ <dev-bot.pl>
 * @license ALL RIGHTS RESERVED
 * */

class Wsb extends Importer
{
    public $file;
    public $multiplier = 2.5;
    public $products = [];
    public static $stocks = false;
    public function loadProducts()
    {
        $this->multiplier = Configuration::get('WsbMultiplier') ?: 2.5;

        $file = __DIR__ .'/../../../brw_feed/WSB/wsb.csv';
        $images = __DIR__ .'/../../../brw_feed/WSB/wsb-images.csv';
        $quantities = _PS_ROOT_DIR_ .'/wsb_feed/stockfeed.csv';
        $imgCsv = new CsvObject($images);
        $images = [];
        while ($image = $imgCsv->getRow())
        {
            if (!isset($images[$image->PartNum])) {
                $images[$image->PartNum] = [];
            }
            $images[$image->PartNum][] = $image->WebImgPath;
        }

        /**
        header('Content-Type:application/json');
        echo json_encode($images);
        exit;
        /**/
        $quantitiesCsv = new CsvObject($quantities);
        $quantities = [];
        while ($quantity = $quantitiesCsv->getRow())
        {
            $row = (array) $quantity;
            foreach ($row as $k => $v) {
                if (preg_match('/PartNum/', $k)) {
                    $quantity->PartNum = $v;
                }
            }

            $quantities[$quantity->PartNum] = $quantity;
        }
        $csv = new CsvObject($file);
        while ($row = $csv->getRow())
        {
            $product = [
                'name' => $row->{"NEW NAME"},
                'reference' => $row->PartNum,
                'ean' => $row->EAN13BC,
                'categories' => ['WSB'],
                'short_description' => '',
                'link_rewrite' => '',
                'meta_title' => '',
                'meta_keywords' => '',
                'meta_description' => '',
                'description' => $row->Finish,
                'quantity' => 0,
                'weight' => (float) $row->CartGrossWeight01,
                'price' => 0,
                'price_before' => 0,
                'currency' => 'EUR',
                'features' => [],
                'images' => [],
                'attributes' => [],
                'suppliers' => ['WSB'],
            ];

            if (isset($images[$row->PartNum])) {
                $product['images'] = $images[$row->PartNum];
            }
            if (isset($quantities[$row->PartNum]) && isset($quantities[$row->PartNum]->StockLevel)) {
                $product['quantity'] = $quantities[$row->PartNum]->StockLevel;
            }
            if (isset($quantities[$row->PartNum]) && isset($quantities[$row->PartNum]->Price)) {
                $product['price'] = self::formatNumber(((float) $quantities[$row->PartNum]->Price) * $this->multiplier);
                $product['price_before'] = number_format(((float) $quantities[$row->PartNum]->Price), 6, '.', '');
            }
            if ($row->AssembledDepth01) {
                $product['features']['Depth'] = $row->AssembledDepth01;
            }
            if ($row->AssembledHeight01) {
                $product['features']['Height'] = $row->AssembledHeight01;
            }
            if ($row->AssembledWidth01) {
                $product['features']['Width'] = $row->AssembledWidth01;
            }
            for ($i = 1; $i < 11; $i++)
            {
                $key = "CartExInfo". str_pad($i, 2, 0, STR_PAD_LEFT);
                if ($row->$key && trim($row->$key)) {
                    $product['description'] .= '<p>'.$row->$key .'</p>';
                }
            }
            $this->products[] = $product;
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
