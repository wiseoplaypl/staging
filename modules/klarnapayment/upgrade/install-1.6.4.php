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

function upgrade_module_1_6_4(KlarnaPayment $module)
{
    try {
        \Configuration::deleteByName('KLARNA_PAYMENT_SANDBOX_ORDER_MAX');
        \Configuration::deleteByName('KLARNA_PAYMENT_PRODUCTION_ORDER_MAX');
        \Configuration::deleteByName('KLARNA_PAYMENT_SANDBOX_ORDER_MIN');
        \Configuration::deleteByName('KLARNA_PAYMENT_PRODUCTION_ORDER_MIN');
    } catch (Exception $e) {
        \PrestaShopLogger::addLog("KlarnaPayment upgrade error: {$e->getMessage()}");

        return false;
    }

    return true;
}
