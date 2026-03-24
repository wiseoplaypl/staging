/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    velsof.com <support@velsof.com>
 * @copyright 2014 Velocity Software Solutions Pvt Ltd
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

function save_subscribe_data_back()
{
    if ($('#kb_proupdate_back_privacy_text').length) {
        if (!document.getElementById("kb_proupdate_back_privacy_text").checked) {
            alert(backinstock_privacy_accept_error);
            return false;
        }
    }
//    var validate_email
    var email = document.getElementById("user_email_subscribe_back").value;
    var combi_id = $('#idCombination').val();
    $('#pal_attribute_id_back').val(combi_id);

    /*Knowband validation start*/
    validate_email = velovalidation.checkEmail($('#user_email_subscribe_back'));
    validate_email_mand = velovalidation.checkMandatory($('#user_email_subscribe_back'));
    if (validate_email_mand != true) {
        $('#email_error_back').show();
        $('#email_error_back').html(validate_email_mand);
        $('#user_email_subscribe_back').css('border', '2px solid #f3515c');
        error_occurred = true;
    }
    else if ((validate_email != true)) {
        $('#email_error_back').show();
        $('#email_error_back').html(validate_email);
        $('#user_email_subscribe_back').css('border', '2px solid #f3515c');
        error_occurred = true;
    } else {
        $('#email_error_back').hide();
        $('#email_error_back').html('');
        $('#user_email_subscribe_back').css('border', 'none');
        /*Knowband validation end*/

        /*Knowband button validation start*/
        var btn = $('#save_subscribe_back');
        btn.attr('disabled', true);
        /*Knowband button validation end*/
        $.ajax({
            type: "POST",
            url: action_product_front_back,
            data: $("#add-to-cart-or-refresh").serialize(),
            beforeSend: function () {
                $('#loading_image_back').show();
                document.getElementById("email_error_back").innerHTML = '';
                document.getElementById("email_error_back").style.display = 'none';
            },
            success: function (res) {
                $(block_id).addClass('pal_success_row_back');
                if (res == 1)
                {
                    document.getElementById("email_error_back").innerHTML = pal_alert_create_success_msg;
                    document.getElementById("email_error_back").style.display = 'block';
                    document.getElementById("email_error_back").style.color = 'green';
                    //$(block_id).html(pal_alert_create_success_msg);
                    //$('#product_update_block_back .product_update_block_back').css('background','green');
                    $('#loading_image_back').css("display", "none");
                }
                else if (res == 0)
                {
                    document.getElementById("email_error_back").innerHTML = pal_alert_update_success_msg;
                    //document.getElementById("user_email_subscribe").style.border='2px solid #f3515c';
                    document.getElementById("email_error_back").style.display = 'block';
                    document.getElementById("email_error_back").style.color = 'red';
                    //$(block_id).html(pal_alert_update_success_msg);
                    //$('#product_update_block_back .product_update_block_back').css('background','red');
                    $('#loading_image_back').css("display", "none");
                }
                $('#product_update_popup_back').slideUp("slow");
                $('#arrow_update_back').hide();
            },
            complete: function () {
                $('input[name="kb_proupdate_back_privacy_text"]').trigger('click');
                btn.removeAttr('disabled');
            }
        });
    }
    return false;
}
var quantityAvailable;
$(document).ready(function () {
    velovalidation.setErrorLanguage({
        empty_fname: empty_fname,
        minchar_fname: minchar_fname,
        empty_mname: empty_mname,
        maxchar_mname: maxchar_mname,
        maxchar_fname: maxchar_fname,
        minchar_mname: minchar_mname,
        only_alphabet: only_alphabet,
        empty_lname: empty_lname,
        maxchar_lname: maxchar_lname,
        minchar_lname: minchar_lname,
        alphanumeric: alphanumeric,
        empty_pass: empty_pass,
        maxchar_pass: maxchar_pass,
        minchar_pass: minchar_pass,
        specialchar_pass: specialchar_pass,
        alphabets_pass: alphabets_pass,
        capital_alphabets_pass: capital_alphabets_pass,
        small_alphabets_pass: small_alphabets_pass,
        digit_pass: digit_pass,
        empty_field: empty_field,
        number_field: number_field,
        positive_number: positive_number,
        maxchar_field: maxchar_field,
        minchar_field: minchar_field,
        empty_email: empty_email,
        validate_email: validate_email,
        empty_country: empty_country,
        maxchar_country: maxchar_country,
        minchar_country: minchar_country,
        empty_city: empty_city,
        maxchar_city: maxchar_city,
        minchar_city: minchar_city,
        empty_state: empty_state,
        maxchar_state: maxchar_state,
        minchar_state: minchar_state,
        empty_proname: empty_proname,
        maxchar_proname: maxchar_proname,
        minchar_proname: minchar_proname,
        empty_catname: empty_catname,
        maxchar_catname: maxchar_catname,
        minchar_catname: minchar_catname,
        empty_zip: empty_zip,
        maxchar_zip: maxchar_zip,
        minchar_zip: minchar_zip,
        empty_username: empty_username,
        maxchar_username: maxchar_username,
        minchar_username: minchar_username,
        invalid_date: invalid_date,
        maxchar_sku: maxchar_sku,
        minchar_sku: minchar_sku,
        invalid_sku: invalid_sku,
        empty_sku: empty_sku,
        validate_range: validate_range,
        empty_address: empty_address,
        minchar_address: minchar_address,
        maxchar_address: maxchar_address,
        empty_company: empty_company,
        minchar_company: minchar_company,
        maxchar_company: maxchar_company,
        invalid_phone: invalid_phone,
        empty_phone: empty_phone,
        minchar_phone: minchar_phone,
        maxchar_phone: maxchar_phone,
        empty_brand: empty_brand,
        maxchar_brand: maxchar_brand,
        minchar_brand: minchar_brand,
        empty_shipment: empty_shipment,
        maxchar_shipment: maxchar_shipment,
        minchar_shipment: minchar_shipment,
        invalid_ip: invalid_ip,
        invalid_url: invalid_url,
        empty_url: empty_url,
        valid_amount: valid_amount,
        valid_decimal: valid_decimal,
        max_email: max_email,
        specialchar_zip: specialchar_zip,
        specialchar_sku: specialchar_sku,
        max_url: max_url,
        valid_percentage: valid_percentage,
        between_percentage: between_percentage,
        maxchar_size: maxchar_size,
        specialchar_size: specialchar_size,
        specialchar_upc: specialchar_upc,
        maxchar_upc: maxchar_upc,
        specialchar_ean: specialchar_ean,
        maxchar_ean: maxchar_ean,
        specialchar_bar: specialchar_bar,
        maxchar_bar: maxchar_bar,
        positive_amount: positive_amount,
        maxchar_color: maxchar_color,
        invalid_color: invalid_color,
        specialchar: specialchar,
        script: script,
        style: style,
        iframe: iframe,
        not_image: not_image,
        image_size: image_size,
        html_tags: html_tags,
        number_pos: number_pos,
        invalid_separator: invalid_separator

    });
    $('#kb_proupdate_back_privacy_text').click(function ()
    {
        $(this).parents('.checker').toggleClass('checked');
    });
    disableBuyNowButton();
    $('.product-add-to-cart').on("DOMSubtreeModified", function () {
        if ($('.add-to-cart').is(":disabled")) {
            quantityAvailable = 0;
            showSection(quantityAvailable);
        } else {
            quantityAvailable = 1;
            showSection(quantityAvailable);
        }
    });
    $('.ui-btn-text').remove();
});

function showSection(quantityAvailable) {
    if (quantityAvailable == 0)
    {
        $('.product_update_block_back').show();
        $('#pal_title_row_price').hide();
        $('#subscribe_type_back').val("quantity");
        block_id = ".product_update_block_back";

    }
    else
    {
        $('.product_update_block_back').hide();
        $('#pal_title_row_price').show();
        $('#subscribe_type_back').val("price");
        block_id = "#pal_title_row_price";
    }
    $(document).on('change', '.attribute_select', function (e) {
        if (quantityAvailable == 0)
        {
            $('.product_update_block_back').show();
            $('#pal_title_row_price').hide();
            $('#subscribe_type_back').val("quantity");
            block_id = ".product_update_block_back";
        }
        else
        {
            $('.product_update_block_back').hide();
            $('#pal_title_row_price').show();
            $('#subscribe_type_back').val("price");
            block_id = "#pal_title_row_price";
        }

    });
    $(document).on('click', '.color_pick', function (e) {
        findCombination();
        if (quantityAvailable == 0)
        {
            $('.product_update_block_back').show();
            $('#pal_title_row_price').hide();
            $('#subscribe_type_back').val("quantity");
            block_id = ".product_update_block_back";
        }
        else
        {
            $('.product_update_block_back').hide();
            $('#pal_title_row_price').show();
            $('#subscribe_type_back').val("price");
            block_id = "#pal_title_row_price";
        }

    });
    $(document).on('click', '.attribute_radio', function (e) {
        if (quantityAvailable == 0)
        {
            $('.product_update_block_back').show();
            $('#pal_title_row_price').hide();
            $('#subscribe_type_back').val("quantity");
            block_id = ".product_update_block_back";
        }
        else
        {
            $('.product_update_block_back').hide();
            $('#pal_title_row_price').show();
            $('#subscribe_type_back').val("price");
            block_id = "#pal_title_row_price";
        }

    });
    $('#user_email_subscribe_back').keypress(function (e) {
        if (e.which == 13) {
            e.preventDefault();
            //do something
        }
    });
}
$("#show_Update_popup").on('click', function (e) {
    var combi_id = $('#idCombination').val();
    $('#pal_attribute_id_back').val(combi_id);
    if ($('#product_update_popup_back').is(':visible'))
    {
        $('#product_update_popup_back').slideUp("fast");
        $('#arrow_update_back').hide();
        e.stopPropagation();
    }
    else
    {
        $('#arrow_update_back').show();
        $('#product_update_popup_back').slideDown("slow");
        e.stopPropagation();
    }
});

$("#product_update_popup_back").click(function (e) {
    e.stopPropagation();
    return false;
});

$(document).click(function () {
    $('#product_update_popup_back').slideUp('fast');
    $('#arrow_update_back').hide();
});

function disableBuyNowButton()
{
    if ($('.add-to-cart').is(":disabled")) {
        var quantityAvailable = 0;
        showSection(quantityAvailable);
    } else {
        var quantityAvailable = 1;
        showSection(quantityAvailable);
    }
}