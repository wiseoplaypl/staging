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
    if (fmm_ps_version >= '1.7.0.0') {
        if (fmm_controller == 'product' && fmm_rule_applicable == true && fmm_is_price_enable == 1) {
            $(".product-quantity").addClass('fmm-product-quantity');
            $("#buy_block .content_prices").addClass('fmm-content-prices');
            $(".product-prices").addClass('fmm-product-prices');
            $(".product-add-to-cart").addClass('fmm-add-to-cart');
            if (fmm_is_contact_enable == 1 ) {
                var contact_us = '';
                if (fmm_is_contact_enable) {
                    contact_us = '<div class="hidepriceandcart_msg" style="margin-top: 5px; margin-bottom: 5px;background-color:' + fmm_bg_color+'"><a   style="background-color:' + fmm_bg_color + ';" href=' + fmm_contact_us + '>' + fmm_message + '</a></div>';
                }
                var html = contact_us;
                $(".product-information").prepend(html);
            }
            
            $('.fmm-product-quantity').remove();
            $('.fmm-content-prices').remove();
            $('.fmm-product-prices').remove();
            $('.fmm-add-to-cart').remove();
        } else if (fmm_controller == 'product' && fmm_rule_applicable == true && fmm_is_price_enable == 0 && fmm_is_cart_enable == 1) {
            $(".product-add-to-cart").addClass('fmm-add-to-cart');
            $(".product-quantity").addClass('fmm-product-quantity');
            if (fmm_is_contact_enable == 1 ) {
                var contact_us = '';
                if (fmm_is_contact_enable) {
                    contact_us = '<div class="hidepriceandcart_msg" style="margin-top: 5px; margin-bottom: 5px;background-color:' + fmm_bg_color+'"><a   style="background-color:' + fmm_bg_color + ';" href=' + fmm_contact_us + '>' + fmm_message + '</a></div>';
                }
            
                var html = contact_us;
                $(".product-information").prepend(html);
            }
            $('.fmm-add-to-cart').remove();
            $('.fmm-product-quantity').remove();
        } else if (fmm_controller != 'product' && fmm_is_price_enable == 1) {
            $('.hidepriceandcart').closest('.product-description').find('.product-price-and-shipping').hide();
            prestashop.on('clickQuickView', function(event) {
                setTimeout(function() {
                    $('.fmm_rule_applicable').closest('.modal-content').find('.product-prices').remove();
                    $('.fmm_rule_applicable').closest('.modal-content').find('.product-quantity .qty').remove();
                    $('.fmm_rule_applicable').closest('.modal-content').find('.product-quantity .add').remove();
                    $('.fmm_rule_applicable').closest('.modal-content').find('.product-add-to-cart .control-label').remove();
                    var html = '<div id="fmm-hide-price-product-page" style="background-color:' + fmm_bg_color + ';"><h2>' + fmm_message + '</h2></div>';
                    $('.fmm_rule_applicable').closest('.modal-content').find(".product-add-to-cart").prepend(html);
                }, 500);
            });
        } else if (fmm_controller != 'product' && fmm_is_price_enable == 0 && fmm_is_cart_enable == 1) {
            prestashop.on('clickQuickView', function(event) {
                setTimeout(function() {
                    // $('.fmm_rule_applicable').closest('.modal-content').find('.product-prices').remove();
                    $('.fmm_rule_applicable').closest('.modal-content').find('.product-quantity .qty').remove();
                    $('.fmm_rule_applicable').closest('.modal-content').find('.product-quantity .add').remove();
                    $('.fmm_rule_applicable').closest('.modal-content').find('.product-add-to-cart .control-label').remove();
                    var html = '<div id="fmm-hide-price-product-page" style="background-color:' + fmm_bg_color + ';"><h2>' + fmm_message + '</h2></div>';
                    $('.fmm_rule_applicable').closest('.modal-content').find(".product-add-to-cart").prepend(html);
                }, 500);
            });
        }
    } else {
        if (fmm_controller == 'product' && fmm_rule_applicable == true && fmm_is_price_enable == 1) {
            $("#buy_block #add_to_cart").addClass('fmm-add-to-cart');
            $("#buy_block .content_prices").addClass('fmm-content-prices');
            $("#buy_block #quantity_wanted_p").addClass('fmm-product-quantity');
            var html = '<div id="fmm-hide-price-product-page-16" style="background-color:' + fmm_bg_color + ';"><h2>' + fmm_message + '</h2></div>';
            $("#buy_block .box-cart-bottom").prepend(html);
        } else if (fmm_controller == 'product' && fmm_rule_applicable == true && fmm_is_price_enable == 0 && fmm_is_cart_enable == 1) {
            $("#buy_block #add_to_cart").addClass('fmm-add-to-cart');
            $("#buy_block #quantity_wanted_p").addClass('fmm-product-quantity');
            var html = '<div id="fmm-hide-price-product-page-16" style="background-color:' + fmm_bg_color + ';"><h2>' + fmm_message + '</h2></div>';
            $("#buy_block .box-cart-bottom").prepend(html);
        } else if (fmm_ps_version < '1.7.0.0' && fmm_controller != 'product' && fmm_rule_applicable == true && fmm_is_price_enable == 1) {
            $('ul.product_list.grid > li .product-container .hook-reviews').next('.product-desc').next('.content_price').remove();
            $('ul.product_list.grid > li .product-container .hook-reviews').next('.product-desc').next('.button-container').remove();
            $('.hook-reviews').parent().parent().find('.content_price').remove();
        } else if (fmm_ps_version < '1.7.0.0' && fmm_controller != 'product' && fmm_rule_applicable == true && fmm_is_price_enable == 0 && fmm_is_cart_enable == 1) {
            $('ul.product_list.grid > li .product-container .hook-reviews').next('.product-desc').next('.content_price').remove();
            $('ul.product_list.grid > li .product-container .hook-reviews').next('.product-desc').next('.button-container').remove();
        }
    }
});