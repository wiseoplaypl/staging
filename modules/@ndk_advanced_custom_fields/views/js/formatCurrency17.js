function formatCurrency17(price, currencyFormat17, currencySign, currencyBlank) {
    if (parseFloat(price) in formatedPrices) {
        return formatedPrices[parseFloat(price)];
    }
    else {
        var response = '';
        $.ajax({
            type: "GET",
            async: false,
            url: baseUrl + 'modules/ndk_advanced_custom_fields/front_ajax.php?action=formatPrice',
            data: { price: parseFloat(price) },
            success: function(data) {
                response = data;
                formatedPrices[parseFloat(price)] = data;
            }
        });
        return response;
    }
}
