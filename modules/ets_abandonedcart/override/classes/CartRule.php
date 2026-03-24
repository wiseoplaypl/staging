<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }

class CartRule extends CartRuleCore
{
    public function checkValidity(Context $context, $alreadyInCart = false, $display_error = true, $check_carrier = true, $useOrderPrices = false)
    {
        $error = parent::checkValidity($context, $alreadyInCart, $display_error, $check_carrier, $useOrderPrices);

        // Check etsdiscountcombinations
        if (Module::isEnabled('etsdiscountcombinations')) {
            $module = Module::getInstanceByName('etsdiscountcombinations');
            if ($module && method_exists($module, 'checkValidity')) {
                if (($display_error && !$error) || (!$display_error && $error)) {
                    $error = $module->checkValidity($this, $display_error);
                }
            }
        }

        // Check ets_reviews
        if (Module::isEnabled('ets_reviews')) {
            $module = Module::getInstanceByName('ets_reviews');
            if ($module && method_exists($module, 'checkValidityVoucher')) {
                if (($display_error && !$error) || (!$display_error && $error)) {
                    $error = $module->checkValidityVoucher($this->code, $error, $context);
                    if (is_bool($error)) return $error;
                    if (is_string($error)) return !$display_error ? false : $error;
                }
            }
        }

        // Check ets_abandonedcart
        if (Module::isEnabled('ets_abandonedcart')) {
            $module = Module::getInstanceByName('ets_abandonedcart');
            if ($module && method_exists($module, 'checkValidityVoucher')) {
                if (($display_error && !$error) || (!$display_error && $error)) {
                    $error = $module->checkValidityVoucher($this->code, $error, $context);
                    if (is_bool($error)) return $error;
                    if (is_string($error)) return !$display_error ? false : $error;
                }
            }
        }

        if (is_bool($error))
            return $error;
        if (is_string($error))
            return (!$display_error) ? false : $error;

        return $error;
    }
}
