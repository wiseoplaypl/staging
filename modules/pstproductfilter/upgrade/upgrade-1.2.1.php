<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2023 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_1($module)
{
    try {
        Db::getInstance()->execute(
            'ALTER TABLE `' . _DB_PREFIX_ . 'pstproductfilter`
             ADD `strict` TINYINT(1) DEFAULT 0'
        );
        Db::getInstance()->execute(
            'ALTER TABLE `' . _DB_PREFIX_ . 'pstproductfilter_set_filter`
             ADD `strict` TINYINT(1) DEFAULT 0'
        );

        // unregister deprecated hooks
        $module->unregisterHook('displayAdminListBefore');
        $module->unregisterHook('displayAdminListAfter');
    } catch (Exception $e) {
        // ignore
    }

    return true; // Return true if success.
}
