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

function upgrade_module_1_6_6()
{
    try {
        \Configuration::updateValue('KLARNA_PAYMENT_SANDBOX_B2B_ENABLE', 0);
        \Configuration::updateValue('KLARNA_PAYMENT_PRODUCTION_B2B_ENABLE', 0);

        $table = _DB_PREFIX_ . 'klarna_expresscheckout';
        $sqlModify = 'ALTER TABLE ' . $table . ' MODIFY COLUMN `client_token` VARCHAR(4096) NULL';
        $sqlAdd = 'ALTER TABLE ' . $table . ' ADD COLUMN `carrier_reference_id` INT(10) NULL';

        if (!Db::getInstance()->execute($sqlModify)
            || !Db::getInstance()->execute($sqlAdd)) {
            return false;
        }
    } catch (Exception $e) {
        \PrestaShopLogger::addLog(sprintf('KlarnaPayment 1.6.6 upgrade error: %s', $e->getMessage()));

        return false;
    }

    return true;
}
