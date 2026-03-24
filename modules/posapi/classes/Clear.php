<?php

/**
 * @author Łukasz Szpak ( info@dev-bot.pl )
 * @Copyright 2019 SzpaQ <dev-bot.pl>
 * @license ALL RIGHTS RESERVED
 * */

class Clear
{
    public static function zeroPrices()
    {
        $products = Db::getInstance()->executeS("
            SELECT id_product
            FROM ". _DB_PREFIX_ ."product
            WHERE price = 0
            AND id_product NOT IN (
                SELECT id_product
                FROM ". _DB_PREFIX_ ."product_attribute
            )
        ");
        foreach ($products as $v) {
            $product = new Product((int) $v['id_product']);
            if ($product -> id) {
                $product -> delete();
            }
        }
    }
}
