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

function upgrade_module_1_9_0($module)
{
    // Process Module upgrade to 1.9
    Logger::addLog('upgrade aftermail_module_1_9_0');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'aftermail_queue` ADD `id_product` INT( 10 ) NOT NULL');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'aftermail_queue` ADD `reminder_delay`  INT( 10 ) NOT NULL');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'aftermail_queue` ADD `unsubscribe`  VARCHAR( 30 ) NOT NULL');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'aftermail_queue` ADD `unsubscribe_all`  VARCHAR( 30 ) NOT NULL');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'aftermail_conf` ADD `reminder_frequency` VARCHAR( 255 )');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'aftermail_conf` ADD `subscribe_ids` VARCHAR( 255 )');

    $module->registerHook('productbuttons');
    $module->registerHook('displayRightColumnProduct');
    $module->registerHook('displayHeader');


    /*Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'aftermail_prod_remind` (
				`id_aftermail_prod_remind` INT NOT NULL AUTO_INCREMENT,
				`id_aftermail_conf` INT NOT NULL,
				`id_product` INT NOT NULL,
				`duration` INT NOT NULL
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');*/

    Configuration::updateValue('AFTERMAIL_VERSION', '1.9.0');
    return true; // Return true if success.
}
