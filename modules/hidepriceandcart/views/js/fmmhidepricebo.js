/*
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    FMM Modules
 * @copyright FMM Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @package   hidepriceandcart
 */
$(document).ready(function() {
    $('#category_rule').hide();
    if ($('#FMM_ENABLE_CATEGORY_RULE_on').is(':checked') == true) {
        $('#category_rule').show();
    } else {
        $('#category_rule').hide();
    }
    $('#FMM_ENABLE_CATEGORY_RULE_on').on('click', function() {
        $('#category_rule').show();
    });
    $('#FMM_ENABLE_CATEGORY_RULE_off').on('click', function() {
        $('#category_rule').hide();
    });
    //inc product
    $('#inc_product_rule').hide();
    if ($('#FMM_ENABLE_INC_PRO_RULE_on').is(':checked') == true) {
        $('#inc_product_rule').show();
    } else {
        $('#inc_product_rule').hide();
    }
    $('#FMM_ENABLE_INC_PRO_RULE_on').on('click', function() {
        $('#inc_product_rule').show();
    });
    $('#FMM_ENABLE_INC_PRO_RULE_off').on('click', function() {
        $('#inc_product_rule').hide();
    });
    //exc product
    $('#exc_product_rule').hide();
    if ($('#FMM_ENABLE_EXC_PRO_RULE_on').is(':checked') == true) {
        $('#exc_product_rule').show();
    } else {
        $('#exc_product_rule').hide();
    }
    $('#FMM_ENABLE_EXC_PRO_RULE_on').on('click', function() {
        $('#exc_product_rule').show();
    });
    $('#FMM_ENABLE_EXC_PRO_RULE_off').on('click', function() {
        $('#exc_product_rule').hide();
    });
    //customer
    $('#customer_rule').hide();
    if ($('#FMM_ENABLE_CUSTOMER_RULE_on').is(':checked') == true) {
        $('#customer_rule').show();
    } else {
        $('#customer_rule').hide();
    }
    $('#FMM_ENABLE_CUSTOMER_RULE_on').on('click', function() {
        $('#customer_rule').show();
    });
    $('#FMM_ENABLE_CUSTOMER_RULE_off').on('click', function() {
        $('#customer_rule').hide();
    });
});