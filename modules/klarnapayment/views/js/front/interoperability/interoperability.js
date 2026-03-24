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
async function getSupplementaryPurchaseData() {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: klarnapayment.interoperabilityUrl,
            data: {
                ajax: true,
                action: 'getSupplementaryPurchaseData',
            },
            success: function(response) {
                resolve(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                const errorMessage = jqXHR.responseText || errorThrown || textStatus || 'Unknown error';
                reject(new Error(`Failed to get supplementary purchase data: ${errorMessage}`));
            }
        });
    });
}

window.addEventListener("load", async function () {
    const KlarnaSDK = await Klarna.init({
        clientId: klarnapayment.interoperability.clientId,
        environment: klarnapayment.interoperability.environment,
        locale: klarnapayment.interoperability.locale,
    });

    KlarnaSDK.Interoperability.on('tokenupdate', async (response) => {
        sessionStorage.setItem('klarna_interoperability_token', response.interoperabilityToken);

        prestashop.emit('updateKlarnaInteroperabilityToken', {
            token: response.interoperabilityToken
        });

        if (klarnapayment.isShareShippingDataEnabled) {
            try {
                var supplementaryData = JSON.parse(await getSupplementaryPurchaseData()).data.supplementary_purchase_data;
                sessionStorage.setItem('interoperability_data', JSON.stringify(supplementaryData));
            } catch (error) {
                console.error('Failed to fetch supplementary purchase data:', error);
            }
        }

        $.ajax({
            type: 'POST',
            url: klarnapayment.interoperabilityUrl,
            data: {
                ajax: true,
                action: 'updateKlarnaInteroperabilityToken',
                klarna_interoperability_token: response.interoperabilityToken
            },
            success: function() {
                console.log('Klarna interoperability token updated');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Failed to update Klarna interoperability token', {
                    status: jqXHR.status,
                    statusText: jqXHR.statusText,
                    error: errorThrown,
                    response: jqXHR.responseText
                });
            }
        });
    });
})
