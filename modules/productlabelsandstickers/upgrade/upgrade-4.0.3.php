<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2025
 *  @license   Single domain
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_0_3($module)
{
    $module->unregisterHook('displayFooterProduct');
    $module->unregisterHook('displayAdminProductsExtra');
    $module->unregisterHook('actionProductUpdate');

    return true;
}
