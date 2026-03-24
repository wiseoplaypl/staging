<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 *
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_6_7(KlarnaPayment $module)
{
    try {
        \Configuration::updateValue('KLARNA_PAYMENT_SANDBOX_SHARE_SHOPPING_DATA', '1');
        \Configuration::updateValue('KLARNA_PAYMENT_PRODUCTION_SHARE_SHOPPING_DATA', '1');

        $table = _DB_PREFIX_ . 'klarna_payment_orders';

        // Add failed_to_update_klarna column only if it does not already exist
        $columnExists = Db::getInstance()->executeS(
            'SHOW COLUMNS FROM `' . bqSQL($table) . '` LIKE "failed_to_update_klarna"'
        );

        if (empty($columnExists)) {
            $sqlAdd = 'ALTER TABLE `' . bqSQL($table) . '` ADD COLUMN `failed_to_update_klarna` TINYINT(1) NOT NULL DEFAULT 0';
            Db::getInstance()->execute($sqlAdd);
        }

        // PS 9 hotfix
        if (!$module->id) {
            $query = new \DbQuery();
            $query->select('id_module');
            $query->from('module');
            $query->where('name = \'' . pSQL($module->name) . '\'');
            $module->id = (int) \Db::getInstance()->getValue($query);
        }

        $module->registerHook('actionOrderEdited');
    } catch (Exception $e) {
        \PrestaShopLogger::addLog(sprintf('KlarnaPayment 1.6.7 upgrade error: %s', $e->getMessage()));

        return false;
    }

    return true;
}
