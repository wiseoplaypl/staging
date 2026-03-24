/**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 */
hiGoogleAnalytics = {
    productsBlockSelector: '.featured-products',

    getCardDetails: function() {
        let data = {};
        data.currency = prestashop.currency.iso_code;
        data.value = prestashop.cart.totals.total.amount;
        if (Object.keys(prestashop.cart.vouchers.added).length) {
            var coupon = Object.values(prestashop.cart.vouchers.added)[0];
            if (typeof coupon.code != 'undefined') {
                var voucherName = coupon.code;
            } else {
                var voucherName = coupon.name;
            }
            data.coupon = voucherName;
        }
        let items = [];
        for (const product of prestashop.cart.products) {
            let item = {};
            item.item_id = product.id_product;
            item.item_name = product.name;
            item.discount = product.price_without_reduction - product.price_with_reduction;
            item.item_brand = product.manufacturer_name;
            item.item_category = product.category;
            if (typeof product.attributes_small !== 'undefined') {
                item.item_variant = product.attributes_small;
            }
            item.price = (hiGaSettings.includeProductTaxes ? product.price_wt : product.price_with_reduction_without_tax);
            item.quantity = product.cart_quantity

            items.push(item);
        }

        data['items'] = items;

        return data;
    },

    searchProductDetails: function(idProduct, idProductAttribute) {
        let data = {};
        data.currency = prestashop.currency.iso_code;
        let items = [];
        let item = {};
        for (const product of prestashop.cart.products) {
            if (product.id_product == idProduct && product.id_product_attribute == idProductAttribute) {
                data.value = product.price_with_reduction;

                item.item_id = product.id_product;
                item.item_name = product.name;
                item.discount = product.price_without_reduction - product.price_with_reduction;
                item.item_brand = product.manufacturer_name;
                item.item_category = product.category;
                if (typeof product.attributes_small !== 'undefined') {
                    item.item_variant = product.attributes_small;
                }
                item.price = (hiGaSettings.includeProductTaxes ? product.price_wt : product.price_with_reduction_without_tax);
                item.quantity = product.cart_quantity

                items.push(item);
                break;
            }
        }

        if (!items.length) {
            return false;
        }

        data['items'] = items;

        return data;
    },

    trackRemoveFromCart: function (idProduct, idProductAttribute) {
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: hiGaSettings.frontController,
            data: {
                ajax: true,
                secureKey: hiGaSettings.secureKey,
                action: 'getProductDetails',
                idProduct: idProduct,
                idProductAttribute: idProductAttribute,
                currency: prestashop.currency.iso_code
            },
            success: function(response) {
                if (!response.hasError && response.data) {
                    var data = response.data;
                    data['event_callback'] = function (containerId) {
                        hiGoogleAnalytics.displayDebugModal(data, 'remove_from_cart', containerId);
                    }
                    gtag("event", 'remove_from_cart', data);
                }
            }
        });
    },

    getProductListPageData()
    {
        let data = {};
        data.item_list_name = prestashop.page.meta.title;
        let items = [];

        $('.js-product-miniature').each(function() {
            let item = {};
            item.item_id = $(this).attr('data-id-product');
            item.item_name = $(this).find('.product-title').text();

            items.push(item);
        });

        data['items'] = items;

        return data;
    },
    
    getProductsBlockData($block)
    {
        let data = {};
        if ($block.find('> h2').length > 0) {
            data.item_list_name = $block.find('> h2').text().trim();
        } else {
            data.item_list_name = prestashop.page.meta.title;
        }

        let items = [];

        $block.find('.js-product-miniature').each(function() {
            let item = {};
            item.item_id = $(this).attr('data-id-product');
            item.item_name = $(this).find('.product-title').text();

            items.push(item);
        });

        data['items'] = items;

        return data;
    },

    buildCustomEventData: function(customEvent) {
        let data = {
            'event_category': customEvent.event_category
        };

        if (customEvent.event_label) {
            data['event_label'] = customEvent.event_label;
        }

        if (customEvent.event_value) {
            data['value'] = parseInt(customEvent.event_value);
        }

        data['event_callback'] = function (containerId) {
            hiGoogleAnalytics.displayDebugModal(data, customEvent.event_action, containerId);
        }

        return data;
    },

    formatObject: function(json) {
        if (typeof json != 'string') {
             json = JSON.stringify(json, undefined, 2);
        }
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        json = json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
            var cls = 'hi-ga-number';
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = 'hi-ga-key';
                } else {
                    cls = 'hi-ga-string';
                }
            } else if (/true|false/.test(match)) {
                cls = 'hi-ga-boolean';
            } else if (/null/.test(match)) {
                cls = 'hi-ga-null';
            }
            return '<span class="' + cls + '">' + match + '</span>';
        });

        return '<pre>' + json + '</pre>';
    },

    displayDebugModal: function(data, eventName, containerId) {
        if (hiGaSettings.debugMode && containerId == hiGaSettings.ga4MeasurementId) {
            $.alert({
                title: eventName + ' event fired',
                type: 'blue',
                typeAnimated: true,
                useBootstrap: false,
                backgroundDismiss: true,
                content: hiGoogleAnalytics.formatObject(data)
            });
        }
    }
}

// GA Events Tracking
$(function() {
    // update Cart
    prestashop.on('updateCart', function (event) {
        // Add to cart
        if (typeof event.reason.linkAction !== "undefined" && event.reason.linkAction == "add-to-cart") {
            let data = hiGoogleAnalytics.searchProductDetails(event.reason.idProduct, event.reason.idProductAttribute);
            if (data) {
                data['event_callback'] = function (containerId) {
                    hiGoogleAnalytics.displayDebugModal(data, 'add_to_cart', containerId);
                }
                gtag('event', 'add_to_cart', data);
            }
        // remove from cart
        } else if (typeof event.reason.linkAction !== "undefined" && event.reason.linkAction == "delete-from-cart") {
            hiGoogleAnalytics.trackRemoveFromCart(event.reason.idProduct, event.reason.idProductAttribute);
        }
    });

    // delivery method selected
    $('#js-delivery input[type="radio"]').on('change', function() {
        let carrierId = $(this).attr('id');
        let carrierName = $(this).closest('.js-delivery-option').find('label[for="' + carrierId + '"] .carrier-name').text();

        data = hiGoogleAnalytics.getCardDetails();
        data.shipping_tier = carrierName.trim();

        data['event_callback'] = function (containerId) {
            hiGoogleAnalytics.displayDebugModal(data, 'add_shipping_info', containerId);
        }
        gtag('event', 'add_shipping_info', data);
    });

    // Payment method selected
    $('input[name="payment-option"]').on('change', function() {
        let paymentOptionId = $(this).attr('id');
        let paymentOptionName = $(this).closest('.payment-option').find('label[for="' + paymentOptionId + '"]').text();
        let data = hiGoogleAnalytics.getCardDetails();
        data.payment_type = paymentOptionName.trim();

        data['event_callback'] = function (containerId) {
            hiGoogleAnalytics.displayDebugModal(data, 'add_payment_info', containerId);
        }
        gtag('event', 'add_payment_info', data);
    });

    // checkout step 1
    if ($('#checkout-personal-information-step.js-current-step').length) {
        let data = hiGoogleAnalytics.getCardDetails();
        data['event_callback'] = function (containerId) {
            hiGoogleAnalytics.displayDebugModal(data, 'begin_checkout', containerId);
        }
        gtag("event", "begin_checkout", data);
    }

    // Login
    $('#login-form').on('submit', function() {
        gtag('event', 'login', {
            method: 'email',
            'event_callback': function(containerId) {
                hiGoogleAnalytics.displayDebugModal({
                    method: 'email'
                }, 'login', containerId);
            }
        });
    });

    // registration
    $('#customer-form').on('submit', function() {
        gtag('event', 'sign_up', {
            method: 'email',
            'event_callback': function(containerId) {
                hiGoogleAnalytics.displayDebugModal({
                    method: 'email'
                }, 'sign_up', containerId);
            }
        });
    });

    // To-Do: Search
    
    // select_item
    $(document).on('click', '.js-product-miniature', function() {
        let idProduct = $(this).attr('data-id-product');
        let productName = $(this).find('.product-title').text();
        let categoryName = prestashop.page.meta.title;

        let data = {}
        data.item_list_name = categoryName;
        let items = [];
        let item = {};
        item.item_id = idProduct;
        item.item_name = productName;
        items.push(item);

        data['items'] = items;

        data['event_callback'] = function (containerId) {
            hiGoogleAnalytics.displayDebugModal(data, 'select_item', containerId);
        }

        gtag('event', 'select_item', data);
    });

    // Share
    $(document).on('click', '.social-sharing a', function() {
        if (!$('#product_page_product_id').length) {
            return;
        }

        let url = $(this).attr('href');
        var matches = url.match(/^https?\:\/\/([^\/?#]+)(?:[\/?#]|$)/i);
        var domain = matches && matches[1];

        let idProduct = $('#product_page_product_id').val();
        let $this = $(this);
        gtag('event', 'share', {
            method: domain,
            content_type: 'product',
            item_id: idProduct,
            'event_callback': function(containerId) {
                hiGoogleAnalytics.displayDebugModal({
                    method: domain,
                    item_id: idProduct,
                    content_type: 'product'
                }, 'share', containerId);
            }
        });
    });

    // view_cart
    if (typeof hiGaCart !== 'undefined') {
        hiGaCart['event_callback'] = function (containerId) {
            hiGoogleAnalytics.displayDebugModal(hiGaCart, 'view_cart', containerId);
        }
        gtag('event', 'view_cart', hiGaCart);
    }

    // view_item_list
    if ($('#js-product-list').length > 0 && $('.js-product-miniature').length > 0) {
        let data = hiGoogleAnalytics.getProductListPageData();
        data['event_callback'] = function (containerId) {
            hiGoogleAnalytics.displayDebugModal(data, 'view_item_list', containerId);
        }
        gtag('event', 'view_item_list', data);
    }
    
    if ($(hiGoogleAnalytics.productsBlockSelector).length > 0) {
        $(hiGoogleAnalytics.productsBlockSelector).each(function(){
            let data = hiGoogleAnalytics.getProductsBlockData($(this));
            data['event_callback'] = function (containerId) {
                hiGoogleAnalytics.displayDebugModal(data, 'view_item_list', containerId);
            }
            gtag('event', 'view_item_list', data);
        });
    }

    // CUSTOM EVENTS:
    // click quick view event
    prestashop.on('clickQuickView', function (event) {
        let idProduct = event.dataset.idProduct;

        let data = {}
        let items = [];
        let item = {};
        item.item_id = idProduct;
        items.push(item);

        data['items'] = items;

        data['event_callback'] = function (containerId) {
            hiGoogleAnalytics.displayDebugModal(data, 'click_quick_view', containerId);
        }
        gtag('event', 'click_quick_view', data);
    });

    if(hiGaSettings.customEvents.length > 0) {
        for (const customEventKey in hiGaSettings.customEvents) {
            let customEvent = hiGaSettings.customEvents[customEventKey];
            let data = hiGoogleAnalytics.buildCustomEventData(customEvent);
            if (customEvent.action == 'click') {
                $(document).on('click', customEvent.selector, function(e) {
                    gtag('event', customEvent.event_action, data);
                });
            } else if (customEvent.action == 'scroll') {
                $(window).on('scroll', function() {
                    if (typeof hiGaSettings.customEvents[customEventKey].tracked != 'undefined' && hiGaSettings.customEvents[customEventKey].tracked) {
                        return;
                    }
                    let elementTop = $(customEvent.selector).offset().top,
                        elementHight = $(customEvent.selector).outerHeight(),
                        windowHeight = $(window).height(),
                        windowScroll = $(this).scrollTop();
                    if (windowScroll > (elementTop + elementHight - windowHeight)) {
                        gtag('event', customEvent.event_action, data);

                        hiGaSettings.customEvents[customEventKey].tracked = true;
                    }
                });
            }
        }
    }
});