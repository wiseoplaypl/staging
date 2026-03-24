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

$sql[] = 'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'tiralineas_available_properties`
    (`object_type`,`name`,`groupName`,`label`,`type`,`fieldType`,`formField`,`options_callback`,`value_callback`) VALUES
    ("deal","shipping_address_line_1","shipping_address","' . $this->l('Shipping Address Line 1', 'properties') . '","string","text",1,"",""),
    ("deal","shipping_address_line_2","shipping_address","' . $this->l('Shipping Address Line 2', 'properties') . '","string","text",1,"",""),
    ("deal","shipping_city","shipping_address","' . $this->l('Shipping City', 'properties') . '","string","text",1,"",""),
    ("deal","shipping_state","shipping_address","' . $this->l('Shipping State', 'properties') . '","string","text",1,"",""),
    ("deal","shipping_postal_code","shipping_address","' . $this->l('Shipping Postal Code', 'properties') . '","string","text",1,"",""),
    ("deal","shipping_country","shipping_address","' . $this->l('Shipping Country', 'properties') . '","string","text",1,"",""),
    ("deal","shipping_phone","shipping_address","' . $this->l('Shipping Phone', 'properties') . '","string","text",1,"",""),
    ("deal","shipping_mobile_phone","shipping_address","' . $this->l('Shipping Mobile Phone', 'properties') . '","string","text",1,"",""),
    ("deal","billing_address_line_1","billing_address","' . $this->l('Billing Address Line 1', 'properties') . '","string","text",1,"",""),
    ("deal","billing_address_line_2","billing_address","' . $this->l('Billing Address Line 2', 'properties') . '","string","text",1,"",""),
    ("deal","billing_city","billing_address","' . $this->l('Billing City', 'properties') . '","string","text",1,"",""),
    ("deal","billing_state","billing_address","' . $this->l('Billing State', 'properties') . '","string","text",1,"",""),
    ("deal","billing_postal_code","billing_address","' . $this->l('Billing Postal Code', 'properties') . '","string","text",1,"",""),
    ("deal","billing_country","billing_address","' . $this->l('Billing Country', 'properties') . '","string","text",1,"",""),
    ("deal","billing_phone","billing_address","' . $this->l('Billing Phone', 'properties') . '","string","text",1,"",""),
    ("deal","billing_mobile_phone","billing_address","' . $this->l('Billing Mobile Phone', 'properties') . '","string","text",1,"",""),
    ("deal","id_cart","shopping_cart_fields","' . $this->l('Cart Id', 'properties') . '","string","text",1,"",""),
    ("deal","id_order","order_fields","' . $this->l('Order Id', 'properties') . '","string","text",1,"",""),
    ("deal","order_status","order_fields","' . $this->l('Order Status', 'properties') . '","string","text",1,"",""),
    ("deal","order_payment","order_fields","' . $this->l('Order Payment Method', 'properties') . '","string","text",1,"",""),
    ("deal","total_discounts","order_fields","' . $this->l('Total Discounts', 'properties') . '","number","number",1,"",""),
    ("deal","total_discounts_tax_incl","order_fields","' . $this->l('Total Discounts Tax Included', 'properties') . '","number","number",1,"",""),
    ("deal","total_discounts_tax_excl","order_fields","' . $this->l('Total Discounts Tax Included', 'properties') . '","number","number",1,"",""),
    ("deal","total_paid_tax_incl","order_fields","' . $this->l('Total Paid Tax Included', 'properties') . '","number","number",1,"",""),
    ("deal","total_paid_tax_excl","order_fields","' . $this->l('Total Paid Tax Excluded', 'properties') . '","number","number",1,"",""),
    ("deal","total_paid_real","order_fields","' . $this->l('Total Paid Real', 'properties') . '","number","number",1,"",""),
    ("deal","total_products","order_fields","' . $this->l('Total Products', 'properties') . '","number","number",1,"",""),
    ("deal","total_products_wt","order_fields","' . $this->l('Total Products Tax Included', 'properties') . '","number","number",1,"",""),
    ("deal","total_shipping","order_fields","' . $this->l('Total Shipping', 'properties') . '","number","number",1,"",""),
    ("deal","total_shipping_tax_incl","order_fields","' . $this->l('Total Shipping Tax Included', 'properties') . '","number","number",1,"",""),
    ("deal","total_shipping_tax_excl","order_fields","' . $this->l('Total Shipping Tax Excluded', 'properties') . '","number","number",1,"",""),
    ("deal","carrier_tax_rate","order_fields","' . $this->l('Carrier Tax Rate', 'properties') . '","number","number",1,"",""),
    ("deal","total_wrapping","order_fields","' . $this->l('Total Wrapping', 'properties') . '","number","number",1,"",""),
    ("deal","total_wrapping_tax_incl","order_fields","' . $this->l('Total Wrapping Tax Included', 'properties') . '","number","number",1,"",""),
    ("deal","total_wrapping_tax_excl","order_fields","' . $this->l('Total Wrapping Tax Excluded', 'properties') . '","number","number",1,"",""),
    ("deal","shipping_number","order_fields","' . $this->l('Shipping Number', 'properties') . '","string","text",1,"",""),
    ("deal","conversion_rate","order_fields","' . $this->l('Convertsion Rate', 'properties') . '","number","number",1,"",""),
    ("deal","invoice_number","order_fields","' . $this->l('Invoice Number', 'properties') . '","string","text",1,"",""),
    ("deal","delivery_number","order_fields","' . $this->l('Delivery Number', 'properties') . '","string","text",1,"",""),
    ("deal","invoice_date","order_fields","' . $this->l('Invoice Date', 'properties') . '","datetime","date",1,"",""),
    ("deal","delivery_date","order_fields","' . $this->l('Delivery Date', 'properties') . '","datetime","date",1,"",""),
    ("deal","cart_products_html","cart_details","' . $this->l('Abandoned Cart Products HTML', 'properties') . '","string","textarea",0,"",""),
    ("deal","coupon_code","order_fields","' . $this->l('Coupon code', 'properties') . '","string","text",1,"",""),
    ("contact","customer_group","customer_group","' . $this->l('Default Customer Group/User role', 'properties') . '","string","textarea",0,"",""),
    ("contact","customer_groups","customer_group","' . $this->l('Customer\'s Groups', 'properties') . '","enumeration","checkbox",0,"getCustomerGroups",""),
    ("contact","newsletter_subscription","newsletter","' . $this->l('Sign up for our newsletter', 'properties') . '","enumeration","booleancheckbox",0,"getYesNoOption",""),
    ("contact","newsletter_date_add","newsletter","' . $this->l('Newsletter Subscription Date', 'properties') . '","datetime","date",1,"",""),
    ("contact","ip_registration_newsletter","newsletter","' . $this->l('Newsletter Suscription IP Address', 'properties') . '","string","text",1,"",""),
    ("contact","id_customer","customer_fields","' . $this->l('Customer Id', 'properties') . '","string","text",1,"",""),
    ("contact","note","customer_fields","' . $this->l('Note', 'properties') . '","string","text",1,"",""),
    ("contact","optin","customer_fields","' . $this->l('Receive offers from our partners', 'properties') . '","enumeration","booleancheckbox",0,"getYesNoOption",""),
    ("contact","language","customer_fields","' . $this->l('Language', 'properties') . '","string","text",1,"",""),
    ("contact","birthday","customer_fields","' . $this->l('Birthday', 'properties') . '","datetime","date",1,"","");
';
