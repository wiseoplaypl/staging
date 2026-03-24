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

function upgrade_module_2_0_1($module)
{
    // Process Module upgrade to 2.0.1
    Logger::addLog('upgrade aftermail_module_2_0_1');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'aftermail_conf_filter` CHANGE `id_aftermail_conf_filter` `id_aftermail_conf_filter` INT( 11 ) NOT NULL');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'aftermail_conf_filter` DROP PRIMARY KEY');
    return true;
}
