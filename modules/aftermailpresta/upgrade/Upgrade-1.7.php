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

function upgrade_module_1_7($module)
{
    // Process Module upgrade to 1.7
    Logger::addLog('upgrade upgrade_module_1_7');
    $module->deleteDir(_PS_ADMIN_DIR_ . '/themes/default/template/controllers/after_mail/');
    $module->smartCopy(_PS_MODULE_DIR_ . '/aftermailpresta/2copy/themes/', _PS_ADMIN_DIR_ . '/themes/');
    return true; // Return true if success.
}
