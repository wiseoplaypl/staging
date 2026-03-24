#!/usr/bin/php
<?php
/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/models/ndkCfConfig.php';

$full_products = array();
if ((int)Configuration::get('NDK_ORDERED_DELAY') == 0) {
    Configuration::updateValue('NDK_ORDERED_DELAY', '60');
}
    
if ((int)Configuration::get('NDK_UNORDERED_DELAY') == 0) {
    Configuration::updateValue('NDK_UNORDERED_DELAY', '4');
}
    
    $sql_unordered = "SELECT p.id_product FROM "._DB_PREFIX_."product p 
    WHERE (p.supplier_reference = 'myndkcustomprod' OR  (p.reference REGEXP '^custom-[0-9*]+-[0-9*]+-[0-9*]+-[0-9*]')) 
    AND p.id_product NOT IN( SELECT product_id FROM "._DB_PREFIX_."order_detail) 
    AND p.cache_is_pack = 1 
    AND p.date_add < DATE_SUB(DATE(NOW()), INTERVAL ".(int)Configuration::get('NDK_UNORDERED_DELAY')." DAY)";
    
    
    $sql_ordered = "SELECT p.id_product FROM "._DB_PREFIX_."product p 
    WHERE (p.supplier_reference = 'myndkcustomprod' OR  (p.reference REGEXP '^custom-[0-9*]+-[0-9*]+-[0-9*]+-[0-9*]')) 
    AND p.cache_is_pack = 1 
    AND p.id_product IN( SELECT product_id FROM "._DB_PREFIX_."order_detail) 
    AND p.date_add < DATE_SUB(DATE(NOW()), INTERVAL ".(int)Configuration::get('NDK_ORDERED_DELAY')." DAY) ORDER BY p.id_product DESC";
    
    $products_unordered = Db::getInstance()->executeS($sql_unordered);
    $products_ordered = Db::getInstance()->executeS($sql_ordered);
    //$full_products = array_merge($products_unordered, $products_ordered);
    foreach ($products_unordered as $product) {
        $full_products[] = $product['id_product'];
    }
    foreach ($products_ordered as $product) {
        $full_products[] = $product['id_product'];
    }
    
    array_unique($full_products);
    
    //var_dump((int)Configuration::get('NDK_ORDERED_DELAY'));
    //var_dump($products_ordered);
    //var_dump($full_products);
    foreach ($full_products as $product) {
        $tempProd = new Product($product);
        if (Validate::isLoadedObject($tempProd)) {
            $pack_infos = Db::getInstance()->executeS("SELECT * FROM "._DB_PREFIX_."pack WHERE id_product_pack=".(int)$product);
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'product_shop WHERE id_product='.(int)$product);
            $tempProd->delete();
            foreach ($pack_infos as $pack_info) {
                Db::getInstance()->execute(
                    "INSERT INTO "._DB_PREFIX_."pack 
                (id_product_pack, id_product_item, id_product_attribute_item, quantity) 
                VALUES (".(int)$pack_info['id_product_pack'].",". (int)$pack_info['id_product_item'].",".(int)$pack_info['id_product_attribute_item'].",".(int)$pack_info['quantity'].")"
                );
            }
            $sqlc = "SELECT c.id_customization FROM "._DB_PREFIX_."customization c 
            WHERE c.id_product = ".(int)$product;
            
            $customisation = new Customization((int)Db::getInstance()->getRow($sqlc));
            $search = Db::getInstance()->executeS(
                'SELECT fc.id_ndk_customization_field_configuration as id FROM '._DB_PREFIX_.'ndk_customization_field_configuration fc WHERE fc.id_customization = '.(int)Db::getInstance()->getRow($sqlc)
            );
            if (sizeof($search) > 0) {
                //print(Tools::jsonEncode($search[0]['name']));
                $config = new ndkCfConfig((int)$search[0]['id']);
                if (Validate::isLoadedObject($config) && (int)$config->is_admin != 1) {
                    $config->delete();
                }
            }
            $customisation->delete();
            $tempProd->delete();
        }
    }
    
    $orphelan_customizations = Db::getInstance()->executeS(
        'SELECT n.id_ndk_customization_field_configuration as id 
    FROM '._DB_PREFIX_.'ndk_customization_field_configuration n 
    WHERE n.id_customization NOT IN( SELECT id_customization FROM '._DB_PREFIX_.'customization) '
    );
    foreach ($orphelan_customizations as $customization) {
        $config = new ndkCfConfig((int)$customization['id']);
        if (Validate::isLoadedObject($config) && (int)$config->is_admin != 1) {
            $config->delete();
        }
    }
    
 ?>