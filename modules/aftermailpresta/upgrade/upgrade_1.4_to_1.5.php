<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Shoprunners
 * Use, copy, modification or distribution of this source file without written
 * license agreement from Shoprunners is strictly forbidden.
 * In order to obtain a license, please contact us: info@shoprunners.de
 *
 * @author    Peter Schaeffer - Shoprunners
 * @copyright Copyright(c) 2012-2022 Shoprunners
 * @license   Commercial license
 * @package   aftermail
 */

if (! defined('_PS_VERSION_')) {
    exit();
}

function upgrade_module_1_1()
{
    // Process Module upgrade to 1.1
    // ....
    return true; // Return true if success.
}

function upgrade_module_1_2()
{
    // Process Module upgrade to 1.2
    // ....
    return true; // Return true if success.
}

function upgrade_module_1_3()
{
    // Process Module upgrade to 1.3
    // ....
    return true; // Return true if success.
}

function upgrade_module_1_4()
{
    // Process Module upgrade to 1.4
    // ....
    return true; // Return true if success.
}

function upgrade_module_1_5($module)
{
    // Process Module upgrade to 1.5
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'aftermail_conf` ADD `voucher` TINYINT NOT NULL ');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'aftermail_conf` ADD `vouchertype` TINYINT NOT NULL ');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'aftermail_conf` ADD `voucheramount` FLOAT NOT NULL ');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'aftermail_conf` ADD `voucherdays` INT NOT NULL ');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'aftermail_conf` ADD `vouchername` VARCHAR( 60 ) NOT NULL ');
    Configuration::updateValue('AFTERMAIL_VERSION', '1.5');
    // $this->module(_PS_MODULE_DIR_.'/aftermailpresta/2copy/',_PS_ADMIN_DIR_);
    $module->smartCopy(_PS_MODULE_DIR_ . '/aftermailpresta/2copy/themes/', _PS_ADMIN_DIR_ . '/themes/');
    return true; // Return true if success.
}

function upgrade_module_1_7($module)
{
    // Process Module upgrade to 1.7
    // $this->module(_PS_MODULE_DIR_.'/aftermailpresta/2copy/',_PS_ADMIN_DIR_);
    $module->smartCopy(_PS_MODULE_DIR_ . '/aftermailpresta/2copy/themes/', _PS_ADMIN_DIR_ . '/themes/');
    return true; // Return true if success.
}
