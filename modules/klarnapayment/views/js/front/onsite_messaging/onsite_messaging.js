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
window.addEventListener("DOMContentLoaded", async function () {
    if (!klarnapayment.onsite_messaging || !klarnapayment.onsite_messaging.client_id) {
        return;
    }

    const clientId = klarnapayment.onsite_messaging.client_id;

    // Check if there are any containers to mount
    const $containers = $('.klarna-onsite-messaging-container');

    if ($containers.length === 0) {
        return;
    }

    try {
        const { KlarnaSDK } = await import("https://js.klarna.com/web-sdk/v2/klarna.mjs");

        const Klarna = await KlarnaSDK({
            clientId: clientId,
            products: ["MESSAGING"],
        });

        $containers.each(function() {
            const $container = $(this);
            const key = $container.data('key');
            const locale = $container.data('locale');
            const theme = $container.data('theme');
            const purchaseAmount = parseInt($container.data('purchase-amount'), 10);

            if (!key || !locale) {
                console.warn('Klarna onsite messaging: missing required data attributes', {
                    key: key,
                    locale: locale
                });

                return;
            }

            // Prepare placement options
            const placementOptions = {
                key: key,
                locale: locale
            };

            if (theme) {
                placementOptions.theme = theme;
            }

            if (purchaseAmount && !isNaN(purchaseAmount) && purchaseAmount > 0) {
                placementOptions.amount = purchaseAmount;
            }

            try {
                Klarna.Messaging.placement(placementOptions).mount(this);
            } catch (error) {
                console.error('Klarna onsite messaging: failed to mount placement', error);
            }
        });
    } catch (error) {
        console.error('Klarna onsite messaging: failed to initialize SDK', error);
    }
});
