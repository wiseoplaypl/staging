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

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_0_0($module)
{
    $return = true;
    $return &= $module->registerHook(array(
        'displayAdminOrder',
        'displayOrderDetail',
        'actionValidateOrder',
        'displayPDFDeliverySlip',
        'displayOverrideTemplate',
        'actionOrderStatusPostUpdate',
        'actionAdminStoresFormModifier',
        'actionAdminStoresControllerSaveAfter',
        'actionAdminStoresListingFieldsModifier',
    ));

    $return &= $module->createTables();
    $return &= $module->installTab('AdminStoreLocatorParent', 'Store Locator', 'location_on');
    $return &= $module->installTab('AdminStoreLocator', 'Stores', 'store_mall_directory', 'AdminStoreLocatorParent');
    $return &= $module->installTab('AdminStoreSlip', 'Store Slip', 'receipt', 'AdminStoreLocatorParent');
    $return &= $module->installTab('AdminStoreSettings', 'Settings', 'settings', 'AdminStoreLocatorParent');
    $return &= Configuration::updateValue(
        'FMESL_PICKUP_TIME',
        1,
        false,
        Context::getContext()->shop->id_shop_group,
        Context::getContext()->shop->id
    );
    $return &= Configuration::updateValue(
        'FMESL_PICKUP_DATE',
        1,
        false,
        Context::getContext()->shop->id_shop_group,
        Context::getContext()->shop->id
    );

    // remove override files - not using overrides anymore
    $return &= $module->delFiles();
    $return &= $module->renameIndex();
    return $return;
}
