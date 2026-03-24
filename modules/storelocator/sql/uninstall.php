<?php
/**
* DISCLAIMER
*
* Do not edit or add to this file.
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FMM Modules
*  @copyright FME Modules 2021
*  @license   Single domainn
*/

$sql = array();

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'storelocator_address`';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'storelocator_cart`';

$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'store` DROP COLUMN related_products';

$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'store` DROP COLUMN link_rewrite';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
