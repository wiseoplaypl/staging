/*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA Miłosz Myszczuk VATEU: PL9730945634
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */


$(document).ready(function () {
    if ($("#idpa").length === 0) {
        $('button[name=submitCustomizedData]').closest('form').append('<input id="idpa" name="idpa" value="' + getIdpa() + '" type="hidden">');
        $('button[name=submitCustomizedData]').closest('form').append('<input id="id_product_attribute_customization" name="id_product_attribute" value="' + getIdpa() + '" type="hidden">');
        $('.remove-image').attr('href', function () {
            return addParams($(this).attr('href'));
        });
    }
});

prestashop.on("updateProduct", function () {
    $(document).ajaxComplete(function () {
        if ($('button[name=submitCustomizedData]').length > 0) {
            if ($("#idpa").length === 0) {
                $('button[name=submitCustomizedData]').closest('form').append('<input id="idpa" name="idpa" value="' + getIdpa() + '" type="hidden">');
                $('button[name=submitCustomizedData]').closest('form').append('<input id="id_product_attribute_customization" name="id_product_attribute" value="' + getIdpa() + '" type="hidden">');
                $('.remove-image').attr('href', function () {
                    return addParams($(this).attr('href'));
                });
            }
        }
    });
});

function getIdpa() {
    if ($('#product-details').length != 0) {
        attr = $('#product-details').attr('data-product');
        if (typeof attr !== typeof undefined && attr !== false) {
            var product_object = jQuery.parseJSON(attr);
            return product_object.id_product_attribute;
        }
    }
}

function addParams(myUrl) {
    name = 'idpa';
    value = getIdpa();

    var re = new RegExp("([?&]" + name + "=)[^&]+", "");

    function add(sep) {
        myUrl += sep + name + "=" + encodeURIComponent(value);
    }

    function change() {
        myUrl = myUrl.replace(re, "$1" + encodeURIComponent(value));
    }

    if (myUrl.indexOf("?") === -1) {
        add("?");
    } else {
        if (re.test(myUrl)) {
            change();
        } else {
            add("&");
        }
    }
    return myUrl;
}


$(document).ready(function () {
    purls_hash = $(location).attr('hash');
    purls_hash = purls_hash.replace('#/', '');
    //chars = purls_hash.split('/');
    $.each(purls_attributes, function (key, i) {
        if (key == purls_hash) {
            chars = i.split('/');
            $.each(chars, function (kkey, ii) {
                character_attribute = chars[kkey].split('-');
                splitted_character_attribute = character_attribute[0].split('|');
                var name = 'select[name="group[' + splitted_character_attribute[0] + ']"]';
                $('select[name="group[' + splitted_character_attribute[0] + ']"]').val(splitted_character_attribute[1]);
                $('input[name="group[' + splitted_character_attribute[0] + ']"]').val(splitted_character_attribute[1]);
                $('#quantity_wanted').trigger('change');
            });
        }
    });
});