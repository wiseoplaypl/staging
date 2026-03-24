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

function upgrade_module_1_6_6_1()
{
    try {
        $table = _DB_PREFIX_ . 'klarna_expresscheckout';

        // Modify client_token column to expected size/type (safe to attempt)
        $sqlModify = 'ALTER TABLE `' . bqSQL($table) . '` MODIFY COLUMN `client_token` VARCHAR(4096) NULL';
        Db::getInstance()->execute($sqlModify);

        // Add carrier_reference_id only if it does not already exist
        $columnExists = Db::getInstance()->executeS(
            'SHOW COLUMNS FROM `' . bqSQL($table) . '` LIKE "carrier_reference_id"'
        );

        if (empty($columnExists)) {
            $sqlAdd = 'ALTER TABLE `' . bqSQL($table) . '` ADD COLUMN `carrier_reference_id` INT(10) NULL';
            Db::getInstance()->execute($sqlAdd);
        }
    } catch (Exception $e) {
        \PrestaShopLogger::addLog(sprintf('KlarnaPayment 1.6.6 upgrade error: %s', $e->getMessage()));

        return false;
    }

    return true;
}
