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

function upgrade_module_1_8_9()
{
    // Process Module upgrade to 1.7
    Logger::addLog('upgrade aftermail_module_1_8_9');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'aftermail_conf` ADD `min_cart_sum`  FLOAT NOT NULL ');
    Configuration::updateValue('AFTERMAIL_VERSION', '1.8.9');
    return true; // Return true if success.
}
