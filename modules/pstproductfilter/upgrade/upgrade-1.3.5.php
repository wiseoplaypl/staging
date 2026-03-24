<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2017 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_5($module)
{
    try {
        Db::getInstance()->execute(
            'ALTER TABLE `' . _DB_PREFIX_ . 'pstproductfilter_set`
             ADD `admin_filter` LONGTEXT,
             ADD `order_by` VARCHAR(65),
             ADD `order_way` VARCHAR(65)'
        );
    } catch (Exception $e) {
        // ignore
    }

    return true; // Return true if success.
}
