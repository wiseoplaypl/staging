<?php
/**
 * 2007-2024 Tiralineas
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Ander <ander@tiralineas.digital>
 * @copyright 2007-2024 Tiralineas
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Tiralineas
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

$sql[] = 'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'tiralineas_available_groups`
    (`object_type`,`name`,`label`) VALUES
    ("deal","billing_address","' . $this->l('Billing Address') . '"),
    ("deal","shipping_address","' . $this->l('Shipping Address') . '"),
    ("deal","order_fields","' . $this->l('Order Information') . '"),
    ("deal","shopping_cart_fields","' . $this->l('Shopping Cart Information') . '"),
    ("deal","billing_address","' . $this->l('Billing Address') . '"),
    ("deal","cart_details","' . $this->l('Cart Details') . '"),
    ("contact","customer_group","' . $this->l('Customer Group') . '"),
    ("contact","customer_fields","' . $this->l('Customer Information') . '"),
    ("contact","newsletter","' . $this->l('Newsletter') . '");
';
