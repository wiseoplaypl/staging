/**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 */

/**
 * NOTICE:
 *
 * This file is a copy of the Klarna Express Checkout script, but with differenct Klarna SDK.
 * It is used to display the Klarna express checkout button to Klarna partners (Mollie, etc.)
*/

// Support case. KEC button is displaying twice on the product page.
document.onload = function () {
    localStorage.setItem('kecLoaded', 'false');
}
window.addEventListener('unload', function() {
    localStorage.setItem('kecLoaded', 'false');
});

window.KlarnaSDKCallback = async function () {
    const klarna = await Klarna.init({
        clientId: klarnapayment.client_identifier,
        shape: klarnapayment.shape,
        theme: klarnapayment.theme
    });

    const klarnaExpressCheckout = klarna.Payment.button({
        theme: klarnapayment.theme,
        shape: klarnapayment.shape,
        locale: klarnapayment.locale,
        intents: ["PAY"],
        label: "pay",
        initiationMode: "DEVICE_BEST",
        initiate: async () => {
            return await onInitiateHandler();
        }
    }).mount(klarnapayment.container);

    klarna.Payment.on("shippingaddresschange", async (paymentRequest, shippingAddress) => {
        try {
            const response = await updateShippingOptionsByAddress(shippingAddress).then(response => JSON.parse(response));

            if (!response.success) {
                return {
                    rejectionReason: "POSTAL_CODE_NOT_SUPPORTED"
                }
            }

            sessionStorage.setItem('express_checkout_selected_shipping_option_reference', response.data.shipping_options[0].shippingOptionReference);

            return {
                amount: paymentRequest.amount,
                selectedShippingOptionReference: response.data.shipping_options[0].shippingOptionReference,
                shippingOptions: response.data.shipping_options,
            }
        } catch (error) {
            console.error('Error updating shipping address: ', error);

            return {
                rejectionReason: "POSTAL_CODE_NOT_SUPPORTED"
            }
        }
    })

    klarna.Payment.on("shippingoptionselect", async (paymentRequest, shippingOption) => {
        try {
            sessionStorage.setItem('express_checkout_selected_shipping_option_reference', shippingOption.shippingOptionReference);

            return {
                amount: paymentRequest.amount,
            }
        } catch (error) {
            console.error('Error saving shipping option to cart: ', error);

            return {
                rejectionReason: "INVALID_OPTION"
            }
        }
    })

    klarna.Payment.on("complete", async (paymentRequest) => {
        try {
            if (paymentRequest?.stateContext?.interoperabilityToken
                && paymentRequest?.state
            ) {
                sessionStorage.setItem('klarna_interoperability_token', paymentRequest.stateContext.interoperabilityToken);
                sessionStorage.setItem('klarna_order_state', paymentRequest.state);

                var supplementaryData = JSON.parse(await getSupplementaryPurchaseData()).data.supplementary_purchase_data;

                sessionStorage.setItem('interoperability_data', klarnapayment.isShareShippingDataEnabled ? JSON.stringify(supplementaryData) : null);

                prestashop.emit('updateKlarnaInteroperabilityToken', {
                    klarna_interoperability_token: paymentRequest.stateContext.interoperabilityToken,
                    klarna_order_state: paymentRequest.state,
                    interoperability_data: klarnapayment.isShareShippingDataEnabled ? JSON.stringify(supplementaryData) : null,
                });

                await updateKlarnaInteroperabilityToken(paymentRequest.stateContext.interoperabilityToken, paymentRequest.state);
            }

            // match original KEC flow
            const collectedShippingAddress = {
                'collected_shipping_address': {
                    "email": paymentRequest.stateContext.shipping.recipient.email,
                    "phone": paymentRequest.stateContext.shipping.recipient.phone,
                    "given_name": paymentRequest.stateContext.shipping.recipient.givenName,
                    "family_name": paymentRequest.stateContext.shipping.recipient.familyName,
                    "street_address": paymentRequest.stateContext.shipping.address.streetAddress,
                    "street_address2": paymentRequest.stateContext.shipping.address.streetAddress2,
                    "postal_code": paymentRequest.stateContext.shipping.address.postalCode,
                    "city": paymentRequest.stateContext.shipping.address.city,
                    "country": paymentRequest.stateContext.shipping.address.country,
                    "region": paymentRequest.stateContext.shipping.address.region,
                },
                'selected_shipping_option_reference': sessionStorage.getItem('express_checkout_selected_shipping_option_reference'),
            }

            sessionStorage.setItem('klarnapayment_express_checkout_data', JSON.stringify(collectedShippingAddress));

            var isKecFlowEnabled = await enableKecFlow(collectedShippingAddress);

            if (!isKecFlowEnabled.success) {
                console.error('Failed to enable KEC flow');
            }

            var isShippingOptionSelected = await saveShippingOptionToCart(sessionStorage.getItem('express_checkout_selected_shipping_option_reference'));

            if (!isShippingOptionSelected.success) {
                console.error('Failed to save shipping option to cart');
            }
        } catch (error) {
            console.error(`Error storing Klarna interoperability token: ${error}`);
        }
    });
}

async function onInitiateHandler() {
    try {
        if (klarnapayment.isProductPage) {
            await addProductToCart();
        }

        var cartInfo = await getCartInfo();
        var supplementaryData = await getSupplementaryPurchaseData();

        cartInfo = JSON.parse(cartInfo).data.cart_data;
        supplementaryData = JSON.parse(supplementaryData).data.supplementary_purchase_data;

        if (!cartInfo) {
            console.error('Failed to get cart information');
        }

        sessionStorage.setItem('klarnapayment_express_checkout_data', JSON.stringify(cartInfo));

        return {
            amount: cartInfo.payment_amount,
            currency: prestashop.currency.iso_code,
            customerInteractionConfig: {
                returnUrl: klarnapayment.checkoutUrl
            },
            supplementaryPurchaseData: supplementaryData,
            shippingConfig: {
                mode: "EDITABLE",
                supportedCountries: ["DE", "FR"]
            },
        };
    } catch (error) {
        console.error(`Error processing express checkout: ${error}`);
    }
}

async function getCartInfo() {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'GET',
            url: klarnapayment.expressCheckoutUrl,
            data: {
                ajax: true,
                token: klarnapayment.staticToken,
                action: 'getCartInfo',
            },
            success: function(response) {
                resolve(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                const errorMessage = jqXHR.responseText || errorThrown || textStatus || 'Unknown error';
                reject(new Error(`Failed to get cart information: ${errorMessage}`));
            }
        });
    });
}

function getProductData() {
    const productDetails = $('#product-details');

    if (
        !productDetails.length ||
        !productDetails.data('product')
    ) {
        throw new Error('Failed to retrieve product details from DOM');
    }

    const productData = productDetails.data('product');

    if (
        typeof productData !== 'object' ||
        !('id_product' in productData) ||
        !('id_product_attribute' in productData) ||
        !('quantity_wanted' in productData) ||
        !('id_customization' in productData)
    ) {
        throw new Error('Failed to find required data properties in product detail dataset');
    }

    let quantity = productData.quantity_wanted;

    if (productData.quantity_wanted < 1) {
        quantity = 1;
    }

    return {
        'product_id': productData.id_product,
        'product_attribute_id': productData.id_product_attribute,
        'quantity': quantity,
        'customization_id': productData.id_customization
    };
}

async function addProductToCart() {
    const productData = getProductData();

    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: klarnapayment.expressCheckoutUrl,
            data: {
                ajax: true,
                product_data: productData,
                token: klarnapayment.staticToken,
                action: 'addProductToCart',
            },
            success: function () {
                resolve();
            },
            error: function (xhr) {
                const result = xhr.responseText ? JSON.parse(xhr.responseText) : {};
                const errorMessage = result.errors || 'Unknown error occurred';

                reject(new Error(`Failed to add product to cart: ${errorMessage}`));
            }
        });
    });
}

function getPayload() {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: klarnapayment.expressCheckoutUrl,
            data: {
                ajax: true,
                token: klarnapayment.staticToken,
                action: 'getPayload',
            },
            success: function (response) {
                resolve(response);
            },
            error: function () {
                reject(new Error('Failed to get session payload for KEC'));
            }

        });
    });
}

async function updateShippingOptionsByAddress(shippingAddress) {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: klarnapayment.expressCheckoutUrl,
            data: {
                ajax: true,
                token: klarnapayment.staticToken,
                action: 'updateShippingOptionsByAddress',
                shipping_address: shippingAddress
            },
            success: function(response) {
                resolve(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                const errorMessage = jqXHR.responseText || errorThrown || textStatus || 'Unknown error';
                reject(new Error(`Failed to update shipping address: ${errorMessage}`));
            }
        });
    });
}

async function saveShippingOptionToCart(shippingOption) {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: klarnapayment.expressCheckoutUrl,
            data: {
                ajax: true,
                token: klarnapayment.staticToken,
                action: 'saveShippingOptionToCart',
                shipping_option: shippingOption
            },
            success: function(response) {
                resolve(response);
            },
            error: function(error) {
                reject(new Error(`Failed to save shipping option to cart: ${error}`));
            }
        });
    });
}

async function getSupplementaryPurchaseData() {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: klarnapayment.expressCheckoutUrl,
            data: {
                ajax: true,
                token: klarnapayment.staticToken,
                action: 'getSupplementaryPurchaseData',
            },
            success: function(response) {
                resolve(response);
            },
            error: function(error) {
                reject(new Error(`Failed to get supplementary purchase data: ${error}`));
            }
        });
    });
}

async function enableKecFlow(shippingAddress) {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: klarnapayment.expressCheckoutUrl,
            data: {
                ajax: true,
                token: klarnapayment.staticToken,
                action: 'enableKecFlow',
                shipping_address: shippingAddress
            },
            success: function () {
                console.info('Enabled KEC flow');
                resolve({
                    success: true
                });
            },
            error: function () {
                reject({
                    success: false
                });
            }
        });
    });
}

async function updateKlarnaInteroperabilityToken(interoperabilityToken, orderState) {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: klarnapayment.expressCheckoutUrl,
            data: {
                ajax: true,
                token: klarnapayment.staticToken,
                action: 'updateKlarnaInteroperabilityToken',
                klarna_interoperability_token: interoperabilityToken,
                klarna_order_state: orderState
            },
            success: function() {
                console.log('Klarna interoperability token updated');
                resolve();
            },
            error: function(error) {
                reject(new Error(`Failed to update Klarna interoperability token: ${error}`));
            }
        });
    });
}
