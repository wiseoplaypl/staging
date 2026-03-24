/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
 * Description
 *
 * Gamification wheel for offering discount coupons.
 */

$(document).ready(function () {
    // changes by rishabh jain
    $('#general_form').addClass('col-lg-10 col-md-9');
    $('#header_form').addClass('col-lg-10 col-md-9');
    $('#footer_form').addClass('col-lg-10 col-md-9');
    $('#randomelement_form').addClass('col-lg-10 col-md-9');
    $('#discount_form').addClass('col-lg-10 col-md-9');

    if (default_tab == 'GeneralSettings') {
        change_tab($("#link-" + default_tab), 1);
    } else if (default_tab == 'HeaderElementSetting') {
        change_tab($("#link-" + default_tab), 2);
    } else if (default_tab == 'FooterElementSetting') {
        change_tab($("#link-" + default_tab), 3);
    } else if (default_tab == 'RandomElementSetting') {
        change_tab($("#link-" + default_tab), 4);
    } else if (default_tab == 'DiscountCouponSetting') {
        change_tab($("#link-" + default_tab), 5);
    } else {
        change_tab($("#link-GeneralSettings"), 1);
    }

    // changes by rishabh jain for discount coupon
    $('#redirect_coupon_name').autocomplete(ajaxaction + '&configure=kbwebsitedecorationeffect&ajaxcouponaction=true', {
        delay: 100,
        minChars: 2,
        autoFill: true,
        max: 20,
        matchContains: true,
        mustMatch: true,
        scroll: false,
        cacheLength: 0,
        // param multipleSeparator:'||' ajoutÃ© Ã  cause de bug dans lib autocomplete
        multipleSeparator: '||',
        formatItem: function (item) {
            return item[1] + ' - ' + item[0];
        },
    }).result(function (event, item) {
        addCouponToExclude(item);
        event.stopPropagation();
    });
    headerUploadedFile();
    footerUploadedFile();
    randomUploadedFile();

    $('#uploadedHeaderfile').closest('.form-group').append($('#notification_header_error'));
    $('#uploadedFooterfile').closest('.form-group').append($('#notification_footer_error'));
    $('#uploadedRandomfile').closest('.form-group').append($('#notification_random_error'));

    $('select[name="HeaderSetting[element_type]"]').trigger('change');
    $('select[name="HeaderSetting[where_to_display]"]').trigger('change');

    $('select[name="RandomSetting[element]"]').trigger('change');
    $('select[name="HeaderSetting[element]"]').trigger('change');
    $('select[name="FooterSetting[element]"]').trigger('change');

    if ($('[id^="HeaderSetting[fix_time]_on"]').is(':checked') === true) {
        $("[name='HeaderSetting[active_date]']").parents('.form-group').show();
        $("[name='HeaderSetting[expire_date]']").parents('.form-group').show();
    }
    else {//
        $("[name='HeaderSetting[active_date]']").parents('.form-group').hide();
        $("[name='HeaderSetting[expire_date]']").parents('.form-group').hide();
    }
    $('[name="HeaderSetting[fix_time]"]').click(function () {// alert('hi');
        if ($(this).val() == '1') {//alert('hi');
            $("[name='HeaderSetting[active_date]']").parents('.form-group').show();
            $("[name='HeaderSetting[expire_date]']").parents('.form-group').show();
        } else {//
            $("[name='HeaderSetting[active_date]']").parents('.form-group').hide();
            $("[name='HeaderSetting[expire_date]']").parents('.form-group').hide();
        }
    });


    $('select[name="FooterSetting[element_type]"]').trigger('change');
    $('select[name="FooterSetting[where_to_display]"]').trigger('change');

    $('select[name="RandomSetting[element_movement]"]').trigger('change');

    if ($('[id^="FooterSetting[fix_time]_on"]').is(':checked') === true) {
        $("[name='FooterSetting[active_date]']").parents('.form-group').show();
        $("[name='FooterSetting[expire_date]']").parents('.form-group').show();
    }
    else {//
        $("[name='FooterSetting[active_date]']").parents('.form-group').hide();
        $("[name='FooterSetting[expire_date]']").parents('.form-group').hide();
    }
    $('[name="FooterSetting[fix_time]"]').click(function () {// alert('hi');
        if ($(this).val() == '1') {//alert('hi');
            $("[name='FooterSetting[active_date]']").parents('.form-group').show();
            $("[name='FooterSetting[expire_date]']").parents('.form-group').show();
        } else {//
            $("[name='FooterSetting[active_date]']").parents('.form-group').hide();
            $("[name='FooterSetting[expire_date]']").parents('.form-group').hide();
        }
    });
    $('select[name="RandomSetting[element_type]"]').trigger('change');
    $('select[name="RandomSetting[where_to_display]"]').trigger('change');
    if ($('[id^="RandomSetting[fix_time]_on"]').is(':checked') === true) {
        $("[name='RandomSetting[active_date]']").parents('.form-group').show();
        $("[name='RandomSetting[expire_date]']").parents('.form-group').show();
    }
    else {//
        $("[name='RandomSetting[active_date]']").parents('.form-group').hide();
        $("[name='RandomSetting[expire_date]']").parents('.form-group').hide();
    }
    $('[name="RandomSetting[fix_time]"]').click(function () {// alert('hi');
        if ($(this).val() == '1') {//alert('hi');
            $("[name='RandomSetting[active_date]']").parents('.form-group').show();
            $("[name='RandomSetting[expire_date]']").parents('.form-group').show();
        } else {//
            $("[name='RandomSetting[active_date]']").parents('.form-group').hide();
            $("[name='RandomSetting[expire_date]']").parents('.form-group').hide();
        }
    });
//    $('select[name="DiscountSetting[element_type]"]').trigger('change');
    $('select[name="DiscountSetting[where_to_display]"]').trigger('change');
    if ($('[id^="DiscountSetting[fix_time]_on"]').is(':checked') === true) {
        $("[name='DiscountSetting[active_date]']").parents('.form-group').show();
        $("[name='DiscountSetting[expire_date]']").parents('.form-group').show();
    }
    else {//
        $("[name='DiscountSetting[active_date]']").parents('.form-group').hide();
        $("[name='DiscountSetting[expire_date]']").parents('.form-group').hide();
    }
    $('[name="DiscountSetting[fix_time]"]').click(function () {// alert('hi');
        if ($(this).val() == '1') {//alert('hi');
            $("[name='DiscountSetting[active_date]']").parents('.form-group').show();
            $("[name='DiscountSetting[expire_date]']").parents('.form-group').show();
        } else {//
            $("[name='DiscountSetting[active_date]']").parents('.form-group').hide();
            $("[name='DiscountSetting[expire_date]']").parents('.form-group').hide();
        }
    });

        // changes over
    // changes over
    $('select[name="kbeffect[effect_type]"]').trigger('change');
    $('select[name="kbeffect[where_to_display]"]').trigger('change');
    $('[name="kbeffect[fix_time]"]').click(function () {// alert('hi');
        if ($(this).val() == '1') {//alert('hi');
            $("[name='kbeffect[active_date]']").parents('.form-group').show();
            $("[name='kbeffect[expire_date]']").parents('.form-group').show();
        } else {//
            $("[name='kbeffect[active_date]']").parents('.form-group').hide();
            $("[name='kbeffect[expire_date]']").parents('.form-group').hide();
        }
    });
    // changes by rishabh jain
    if ($('[id^="kbeffect[enable_snoweffect]_on"]').is(':checked') === true) {
        $("[name='kbeffect[effect_type]']").parents('.form-group').show();
        $("[name='kbeffect[flake_color]']").parents('.form-group').show();
        $("[name='kbeffect[letitsnow_min_size]']").parents('.form-group').show();
        $("[name='kbeffect[letitsnow_max_size]']").parents('.form-group').show();
        $("[name='kbeffect[letitsnow_max_count]']").parents('.form-group').show();
        $("[name='kbeffect[letitsnow_speed]']").parents('.form-group').show();
        $("[name='kbeffect[letitsnow_falltime]']").parents('.form-group').show();
    } else {//
        $("[name='kbeffect[effect_type]']").parents('.form-group').hide();
        $("[name='kbeffect[flake_color]']").parents('.form-group').hide();
        $("[name='kbeffect[letitsnow_min_size]']").parents('.form-group').hide();
        $("[name='kbeffect[letitsnow_max_size]']").parents('.form-group').hide();
        $("[name='kbeffect[letitsnow_max_count]']").parents('.form-group').hide();
        $("[name='kbeffect[letitsnow_speed]']").parents('.form-group').hide();
        $("[name='kbeffect[letitsnow_falltime]']").parents('.form-group').hide();

    }
    if ($('[id^="kbeffect[enable_extra_effect]_on"]').is(':checked') === true) {
        $("[name='kbeffect[extra_effect_type]']").parents('.form-group').show();
    }
    else {//
        $("[name='kbeffect[extra_effect_type]']").parents('.form-group').hide();
    }
    $('[name="kbeffect[enable_snoweffect]"]').click(function () {// alert('hi');
        if ($(this).val() == '1') {//alert('hi');
            $("[name='kbeffect[effect_type]']").parents('.form-group').show();
            $("[name='kbeffect[flake_color]']").parents('.form-group').show();
            $("[name='kbeffect[letitsnow_min_size]']").parents('.form-group').show();
            $("[name='kbeffect[letitsnow_max_size]']").parents('.form-group').show();
            $("[name='kbeffect[letitsnow_max_count]']").parents('.form-group').show();
            $("[name='kbeffect[letitsnow_speed]']").parents('.form-group').show();
            $("[name='kbeffect[letitsnow_falltime]']").parents('.form-group').show();
        } else {//
            $("[name='kbeffect[effect_type]']").parents('.form-group').hide();
            $("[name='kbeffect[flake_color]']").parents('.form-group').hide();
            $("[name='kbeffect[letitsnow_min_size]']").parents('.form-group').hide();
            $("[name='kbeffect[letitsnow_max_size]']").parents('.form-group').hide();
            $("[name='kbeffect[letitsnow_max_count]']").parents('.form-group').hide();
            $("[name='kbeffect[letitsnow_speed]']").parents('.form-group').hide();
            $("[name='kbeffect[letitsnow_falltime]']").parents('.form-group').hide();
        }
    });

    $('[name="kbeffect[enable_extra_effect]"]').click(function () {// alert('hi');
        if ($(this).val() == '1') {//alert('hi');
            $("[name='kbeffect[extra_effect_type]']").parents('.form-group').show();
        } else {//
            $("[name='kbeffect[extra_effect_type]']").parents('.form-group').hide();
        }
    });
    // changes by rishabh jain

    if ($('[id^="kbeffect[fix_time]_on"]').is(':checked') === true) {
        $("[name='kbeffect[active_date]']").parents('.form-group').show();
        $("[name='kbeffect[expire_date]']").parents('.form-group').show();
    }
    else {//
        $("[name='kbeffect[active_date]']").parents('.form-group').hide();
        $("[name='kbeffect[expire_date]']").parents('.form-group').hide();
    }
    // changes over

    $(".velovalidation_kbeffect").on('click', function () {
        if (form_validation() == false) {
            return false;
        }
        $('#header_form :input').not(':submit').clone().hide().appendTo('#general_form');
        $('#footer_form :input').not(':submit').clone().hide().appendTo('#general_form');
        $('#randomelement_form :input').not(':submit').clone().hide().appendTo('#general_form');
        $('#discount_form :input').not(':submit').clone().hide().appendTo('#general_form');
        /*Knowband button validation start*/
        $('.velovalidation_kbeffect').attr("disabled", "disabled");
        /*Knowband button validation end*/
        $('#general_form').submit();
    });
    $(".kb_header_setting_btn").on('click', function () {
        if (form_validation() == false) {
            return false;
        }
        $('#general_form :input').not(':submit').clone().hide().appendTo('#header_form');
        $('#footer_form :input').not(':submit').clone().hide().appendTo('#header_form');
        $('#randomelement_form :input').not(':submit').clone().hide().appendTo('#header_form');
        $('#discount_form :input').not(':submit').clone().hide().appendTo('#header_form');
        /*Knowband button validation start*/
        $('.kb_header_setting_btn').attr("disabled", "disabled");
        /*Knowband button validation end*/
        $('#header_form').submit();
    });
    $(".kb_footer_setting_btn").on('click', function () {
        if (form_validation() == false) {
            return false;
        }
        $('#general_form :input').not(':submit').clone().hide().appendTo('#footer_form');
        $('#header_form :input').not(':submit').clone().hide().appendTo('#footer_form');
        $('#randomelement_form :input').not(':submit').clone().hide().appendTo('#footer_form');
        $('#discount_form :input').not(':submit').clone().hide().appendTo('#footer_form');
        /*Knowband button validation start*/
        $('.kb_footer_setting_btn').attr("disabled", "disabled");
        /*Knowband button validation end*/
        $('#footer_form').submit();
    });
    $(".kb_random_setting_btn").on('click', function () {
        if (form_validation() == false) {
            return false;
        }
        $('#header_form :input').not(':submit').clone().hide().appendTo('#randomelement_form');
        $('#footer_form :input').not(':submit').clone().hide().appendTo('#randomelement_form');
        $('#general_form :input').not(':submit').clone().hide().appendTo('#randomelement_form');
        $('#discount_form :input').not(':submit').clone().hide().appendTo('#randomelement_form');
        /*Knowband button validation start*/
        $('.kb_random_setting_btn').attr("disabled", "disabled");
        /*Knowband button validation end*/
        $('#randomelement_form').submit();
    });

    $(".kb_discount_setting_btn").on('click', function () {
        if (form_validation() == false) {
            return false;
        }
        $('#header_form :input').not(':submit').clone().hide().appendTo('#discount_form');
        $('#footer_form :input').not(':submit').clone().hide().appendTo('#discount_form');
        $('#general_form :input').not(':submit').clone().hide().appendTo('#discount_form');
        $('#randomelement_form :input').not(':submit').clone().hide().appendTo('#discount_form');
        /*Knowband button validation start*/
        $('.kb_discount_setting_btn').attr("disabled", "disabled");
        /*Knowband button validation end*/
        $('#discount_form').submit();
    });

});


function headerUploadedFile() {
    $('#uploadedHeaderfile').on('change', function (e) {
        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
        {
            var files = $(this)[0].files[0];
            var file_data = e.target.files;
            var file_mimetypes = [
                'image/gif',
                'image/jpeg',
                'image/png',
                'application/x-shockwave-flash',
                'image/psd',
                'image/bmp',
                'image/tiff',
                'application/octet-stream',
                'image/jp2',
                'image/iff',
                'image/vnd.wap.wbmp',
                'image/xbm',
                'image/vnd.microsoft.icon',
                'image/webp'
            ];

            var file_format = false;
            for (i = 0; i < file_mimetypes.length; i++) {
                if (files.type == file_mimetypes[i]) {
                    file_format = true;
                }
            }

            if (!file_format)
            {
                $('#notification_header_error').html(invalid_file_format_txt);
                file_error = true;

            } else if (files.size > 2097152) {
                $('#notification_header_error').html(file_size_error_txt);
                file_error = true;
            } else {
                file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#notificatonHeaderimage");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function (e) {

                        $('#notificatonHeaderimage').attr('src', e.target.result);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                }
                $('#notification_header_error').html('');
            }

        }
        else // Internet Explorer 9 Compatibility
        {
            $('#notification_header_error').html(invalid_file_txt);
            file_error = true;
        }
    });
}
function footerUploadedFile() {
    $('#uploadedFooterfile').on('change', function (e) {
        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
        {
            var files = $(this)[0].files[0];
            var file_data = e.target.files;
            var file_mimetypes = [
                'image/gif',
                'image/jpeg',
                'image/png',
                'application/x-shockwave-flash',
                'image/psd',
                'image/bmp',
                'image/tiff',
                'application/octet-stream',
                'image/jp2',
                'image/iff',
                'image/vnd.wap.wbmp',
                'image/xbm',
                'image/vnd.microsoft.icon',
                'image/webp'
            ];

            var file_format = false;
            for (i = 0; i < file_mimetypes.length; i++) {
                if (files.type == file_mimetypes[i]) {
                    file_format = true;
                }
            }

            if (!file_format)
            {
                $('#notification_footer_error').html(invalid_file_format_txt);
                file_error = true;

            } else if (files.size > 2097152) {
                $('#notification_footer_error').html(file_size_error_txt);
                file_error = true;
            } else {
                file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#notificatonFooterimage");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function (e) {

                        $('#notificatonFooterimage').attr('src', e.target.result);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                }
                $('#notification_footer_error').html('');
            }

        }
        else // Internet Explorer 9 Compatibility
        {
            $('#notification_footer_error').html(invalid_file_txt);
            file_error = true;
        }
    });
}

function randomUploadedFile() {
    $('#uploadedRandomfile').on('change', function (e) {
        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
        {
            var files = $(this)[0].files[0];
            var file_data = e.target.files;
            var file_mimetypes = [
                'image/gif',
                'image/jpeg',
                'image/png',
                'application/x-shockwave-flash',
                'image/psd',
                'image/bmp',
                'image/tiff',
                'application/octet-stream',
                'image/jp2',
                'image/iff',
                'image/vnd.wap.wbmp',
                'image/xbm',
                'image/vnd.microsoft.icon',
                'image/webp'
            ];

            var file_format = false;
            for (i = 0; i < file_mimetypes.length; i++) {
                if (files.type == file_mimetypes[i]) {
                    file_format = true;
                }
            }

            if (!file_format)
            {
                $('#notification_random_error').html(invalid_file_format_txt);
                file_error = true;

            } else if (files.size > 2097152) {
                $('#notification_random_error').html(file_size_error_txt);
                file_error = true;
            } else {
                file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#notificatonRandomimage");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function (e) {

                        $('#notificatonRandomimage').attr('src', e.target.result);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                }
                $('#notification_random_error').html('');
            }

        }
        else // Internet Explorer 9 Compatibility
        {
            $('#notification_random_error').html(invalid_file_txt);
            file_error = true;
        }
    });
}
function addCouponToExclude(data) {
    if (data == null)
        return false;

    var couponId = data[1];
    var couponName = data[0];
    var $divAccessories = $('#kb_excluded_coupon_holder');
    var delButtonClass = 'delExcludedCoupon';

    var current_excluded_coupon = $('input[name="redirect_coupon_id"]').val();
    if (current_excluded_coupon != '') {
        var coupon_arr_exclude = current_excluded_coupon.split(",");
        if ($.inArray(couponId, coupon_arr_exclude) != '-1') {
            return false;
        }
    }

    $divAccessories.html('<div class="form-control-static"><button type="button" onclick="deleteSelectedCoupon(' + couponId + ',this);" class="' + delButtonClass + ' btn btn-default" name="' + couponId + '"><i class="icon-remove text-danger"></i></button>&nbsp;' + couponName + '</div>');

    $('input[name="redirect_product_name"]').val('');

    if (current_excluded_coupon != '') {
        $('input[name="redirect_coupon_id"]').val(couponId);
    } else {
        $('input[name="redirect_coupon_id"]').val(couponId);
    }
}

function removeIdFromCommaString(list, value, separator) {
    separator = separator || ",";
    var values = list.split(separator);
    for (var i = 0; i < values.length; i++) {
        if (values[i] == value) {
            values.splice(i, 1);
            return values.join(separator);
        }
    }
    return list;
}
function deleteSelectedCoupon(couponId, current) {
    $('input[name="redirect_coupon_id"]').val(0);
    $('input[name="redirect_product_name"]').val('');
    $(current).parent().remove();
}

function change_tab(a, b) {
    $('.gamification-tip').hide();
    $('.list-group-item').removeClass('active');
    $(a).addClass(' active');
    if (b == 1) {
        $('#general_form').show();
        $('#header_form').hide();
        $('#footer_form').hide();
        $('#randomelement_form').hide();
        $('#discount_form').hide();

    } else if (b == 2) {
        $('#general_form').hide();
        $('#header_form').show();
        $('#footer_form').hide();
        $('#randomelement_form').hide();
        $('#discount_form').hide();
    } else if (b == 3) {
        $('#general_form').hide();
        $('#header_form').hide();
        $('#footer_form').show();
        $('#randomelement_form').hide();
        $('#discount_form').hide();
    } else if (b == 4) {
        $('#general_form').hide();
        $('#header_form').hide();
        $('#footer_form').hide();
        $('#randomelement_form').show();
        $('#discount_form').hide();
    } else if (b == 5) {
        $('#general_form').hide();
        $('#header_form').hide();
        $('#footer_form').hide();
        $('#randomelement_form').hide();
        $('#discount_form').show();
    }
    $('.list-group-item').attr('class', 'list-group-item');
    $(a).attr('class', 'list-group-item active');
}

function showHideEffectSetting(selectObject) {
    var value = selectObject.value;
    $('*[class^="show_effect_option_"]').closest('.form-group').hide();
    $('.show_effect_option_' + value).closest('.form-group').show();
}

function showHideDisplayPageSetting(selectObject) {
    var value = selectObject.value;
    $("[name='kbeffect[show_page][]']").parents('.form-group').hide();
    $("[name='kbeffect[not_show_page][]']").parents('.form-group').hide();
    if (value == '2') {
        $("[name='kbeffect[show_page][]']").parents('.form-group').show();
    } else if (value == '3') {
        $("[name='kbeffect[not_show_page][]']").parents('.form-group').show();
    }
}
function showHideDisplayPageHeaderSetting(selectObject) {
    var value = selectObject.value;
    $("[name='HeaderSetting[show_page][]']").parents('.form-group').hide();
    $("[name='HeaderSetting[not_show_page][]']").parents('.form-group').hide();
    if (value == '2') {
        $("[name='HeaderSetting[show_page][]']").parents('.form-group').show();
    } else if (value == '3') {
        $("[name='HeaderSetting[not_show_page][]']").parents('.form-group').show();
    }
}
function showHideDisplayPageDiscountSetting(selectObject) {
    var value = selectObject.value;
    $("[name='DiscountSetting[show_page][]']").parents('.form-group').hide();
    $("[name='DiscountSetting[not_show_page][]']").parents('.form-group').hide();
    if (value == '2') {
        $("[name='DiscountSetting[show_page][]']").parents('.form-group').show();
    } else if (value == '3') {
        $("[name='DiscountSetting[not_show_page][]']").parents('.form-group').show();
    }
}
function showHideHeaderElementType(selectObject) {
    var value = selectObject.value;
    $("[name='HeaderSetting[element]']").parents('.form-group').hide();
    $("[name='kbwd_preview_header_element']").parents('.form-group').hide();
    $("[name='uploadedHeaderfile']").parents('.form-group').hide();
    if (value == '1') {
        $("[name='kbwd_preview_header_element']").parents('.form-group').show();
        $("[name='HeaderSetting[element]']").parents('.form-group').show();
    } else if (value == '2') {
        $("[name='uploadedHeaderfile']").parents('.form-group').show();
    }
}
function showHideDisplayPageFooterSetting(selectObject) {
    var value = selectObject.value;
    $("[name='FooterSetting[show_page][]']").parents('.form-group').hide();
    $("[name='FooterSetting[not_show_page][]']").parents('.form-group').hide();
    if (value == '2') {
        $("[name='FooterSetting[show_page][]']").parents('.form-group').show();
    } else if (value == '3') {
        $("[name='FooterSetting[not_show_page][]']").parents('.form-group').show();
    }
}

function showHideHeaderElementSetting(selectObject) {
    var value = selectObject.value;
    if (typeof (header_image_path) != 'undefined') {
        for (var img in header_image_path) {
            if (header_image_path[img].id_image == value) {
                $('.header-preview-image').attr('src', header_image_path[img].path);
            }
        }
    }
}
function showHideFooterElementSetting(selectObject) {
    var value = selectObject.value;
    if (typeof (footer_image_path) != 'undefined') {
        for (var img in footer_image_path) {
            if (footer_image_path[img].id_image == value) {
                $('.footer-preview-image').attr('src', footer_image_path[img].path);
            }
        }
    }
}
function showHideRandomElementSetting(selectObject) {
    var value = selectObject.value;
    if (typeof (random_image_path) != 'undefined') {
        for (var img in random_image_path) {
            if (random_image_path[img].id_image == value) {
                $('.random-preview-image').attr('src', random_image_path[img].path);
            }
        }
    }
}
function showHideFooterElementType(selectObject) {
    var value = selectObject.value;
    $("[name='FooterSetting[element]']").parents('.form-group').hide();
    $("[name='kbwd_preview_footer_element']").parents('.form-group').hide();
    $("[name='uploadedFooterfile']").parents('.form-group').hide();
    if (value == '1') {
        $("[name='kbwd_preview_footer_element']").parents('.form-group').show();
        $("[name='FooterSetting[element]']").parents('.form-group').show();
    } else if (value == '2') {
        $("[name='uploadedFooterfile']").parents('.form-group').show();
    }
}
function showHideDisplayPageRandomSetting(selectObject) {
    var value = selectObject.value;
    $("[name='RandomSetting[show_page][]']").parents('.form-group').hide();
    $("[name='RandomSetting[not_show_page][]']").parents('.form-group').hide();
    if (value == '2') {
        $("[name='RandomSetting[show_page][]']").parents('.form-group').show();
    } else if (value == '3') {
        $("[name='RandomSetting[not_show_page][]']").parents('.form-group').show();
    }
}
function showHideRandomElementType(selectObject) {
    var value = selectObject.value;
    $("[name='RandomSetting[element]']").parents('.form-group').hide();
    $("[name='kbwd_preview_random_element']").parents('.form-group').hide();
    $("[name='uploadedRandomfile']").parents('.form-group').hide();
    if (value == '1') {
        $("[name='kbwd_preview_random_element']").parents('.form-group').show();
        $("[name='RandomSetting[element]']").parents('.form-group').show();
    } else if (value == '2') {
        $("[name='uploadedRandomfile']").parents('.form-group').show();
    }
}
function showHideRandomElementPositions(selectObject) {
    var value = selectObject.value;
    $("[name='RandomSetting[static_position]']").parents('.form-group').hide();
    $("[name='RandomSetting[moving_position]']").parents('.form-group').hide();
    if (value == '1') {
        $("[name='RandomSetting[static_position]']").parents('.form-group').show();
    } else if (value == '2') {
        $("[name='RandomSetting[moving_position]']").parents('.form-group').show();
    }
}

// changes by rishabh jain
function showHideExtraEffectSetting(a) {
//    if ($("[name='kbeffect[extra_effect_type]']").val() == 1) {
//        alert('testing');
//
//        $("#effects_image").attr('src', effect_url_1)
//        $('#image_container').show();
//    }
    //$("#effectimage").attr('src', b[i]['value'])
}
// changes over
function form_validation() {
    $('.vel_error_msg').remove();
    $('.error_field').removeClass('error_field');
    $('.velsof_error_label').hide();
    $('.enter_text_mand').remove();
    $('.kbeffect_error').remove();


    var error = false;
    var general_error = false;
    var header_error = false;
    var footer_error = false;
    var random_error = false;
    var discount_error = false;
    if ($('select[name="kbeffect[effect_type]"]').val() != '') {
        var selected_effect_type = $('select[name="kbeffect[effect_type]"]').val();
        $('.show_effect_option_' + selected_effect_type).each(function () {
            var current_validation_element = $(this);
            var current_error = velovalidation.checkMandatory(current_validation_element);
            if (current_error !== true) {
                error = true;
                general_error = true;
                current_validation_element.addClass('error_field');
                current_validation_element.after($('<p class="kbeffect_error">' + current_error + '</p>'));
            } else {
                if (current_validation_element.attr('id') != 'kbeffect[flurry_character]') {
                    current_error = velovalidation.isNumeric(current_validation_element, true);
                    if (current_error !== true) {
                        error = true;
                        general_error = true;
                        current_validation_element.addClass('error_field');
                        current_validation_element.after($('<p class="kbeffect_error">' + current_error + '</p>'));
                    }
                }

            }
        });
    }

    if ($('[id^="kbeffect[fix_time]_on"]').is(':checked') === true) {
        var active_date = velovalidation.checkMandatory($("input[name='kbeffect[active_date]']"));
        if (active_date !== true) {
            error = true;
            general_error = true;
            $("input[name='kbeffect[active_date]']").addClass('error_field');
            $("input[name='kbeffect[active_date]']").after($('<span class="active_date kbeffect_error">' + active_date + '</span>'));
        }
        var expire_date = velovalidation.checkMandatory($("input[name='kbeffect[expire_date]']"));
        if (expire_date !== true) {
            error = true;
            general_error = true;
            $("input[name='kbeffect[expire_date]']").addClass('error_field');
            $("input[name='kbeffect[expire_date]']").parent().parent().append('<span class="expire_date kbeffect_error">' + expire_date + '</span>');
        }
        var activation_date = Date.parse($('input[name="kbeffect[active_date]"]').val());
        var deactivation_date = Date.parse($('input[name="kbeffect[expire_date]"]').val());
        if (parseInt(deactivation_date) <= parseInt(activation_date)) {
            error = true;
            general_error = true;
            $('input[name="kbeffect[expire_date]"]').addClass('error_field');
            $('input[name="kbeffect[expire_date]"]').parent().parent().append('<span class="kbeffect_error">' + deactivation_date_error + '</span>');
        }
    }

    if ($('[name="kbeffect[where_to_display]"]').val() === '2') {
        var selected_page = $("[name='kbeffect[show_page][]']").val();
        if (selected_page === null) {
            error = true;
            general_error = true;
            $("[name='kbeffect[show_page][]']").addClass('error_field');
            $("[name='kbeffect[show_page][]']").after($('<p class="selected_page kbeffect_error"></p>'));
            $('.selected_page').text(multiple_select_message);
        }
    }
    if ($('[name="kbeffect[where_to_display]"]').val() === '3') {
        var unselected_page = $("[name='kbeffect[not_show_page][]']").val();
        if (unselected_page === null) {
            error = true;
            general_error = true;
            $("[name='kbeffect[not_show_page][]']").addClass('error_field');
            $("[name='kbeffect[not_show_page][]']").after($('<p class="unselected_page kbeffect_error"></p>'));
            $('.unselected_page').text(multiple_select_message);
        }
    }
    // header form validation

    if ($('[id^="HeaderSetting[enable]_on"]').is(':checked') === true) {
        var is_enable_header = true;
    } else {
        var is_enable_header = false;
    }
    if (is_enable_header) {
        if ($('[id^="HeaderSetting[fix_time]_on"]').is(':checked') === true) {
            var active_date = velovalidation.checkMandatory($("input[name='HeaderSetting[active_date]']"));
            if (active_date !== true) {
                error = true;
                header_error = true;
                $("input[name='HeaderSetting[active_date]']").addClass('error_field');
                $("input[name='HeaderSetting[active_date]']").parent().parent().append($('<p class="active_date kbeffect_error">' + active_date + '</p>'));
            }
            var expire_date = velovalidation.checkMandatory($("input[name='HeaderSetting[expire_date]']"));
            if (expire_date !== true) {
                error = true;
                header_error = true;
                $("input[name='HeaderSetting[expire_date]']").addClass('error_field');
                $("input[name='HeaderSetting[expire_date]']").parent().parent().append($('<p class="expire_date kbeffect_error">' + expire_date + '</p>'));
            }
            var activation_date = Date.parse($('input[name="HeaderSetting[active_date]"]').val());
            var deactivation_date = Date.parse($('input[name="HeaderSetting[expire_date]"]').val());
            if (parseInt(deactivation_date) <= parseInt(activation_date)) {
                error = true;
                header_error = true;
                $('input[name="HeaderSetting[expire_date]"]').addClass('error_field');
                $('input[name="HeaderSetting[expire_date]"]').parent().parent().append('<span class="kbeffect_error">' + deactivation_date_error + '</span>');
            }
        }

        if ($('[name="HeaderSetting[where_to_display]"]').val() === '2') {
            var selected_page = $("[name='HeaderSetting[show_page][]']").val();
            if (selected_page === null) {
                error = true;
                header_error = true;
                $("[name='HeaderSetting[show_page][]']").addClass('error_field');
                $("[name='HeaderSetting[show_page][]']").after($('<p class="selected_page kbeffect_error"></p>'));
                $('.selected_page').text(multiple_select_message);
            }
        }
        if ($('[name="HeaderSetting[where_to_display]"]').val() === '3') {
            var unselected_page = $("[name='HeaderSetting[not_show_page][]']").val();
            if (unselected_page === null) {
                error = true;
                header_error = true;
                $("[name='HeaderSetting[not_show_page][]']").addClass('error_field');
                $("[name='HeaderSetting[not_show_page][]']").after($('<p class="unselected_page kbeffect_error"></p>'));
                $('.unselected_page').text(multiple_select_message);
            }
        }
        if ($('[name="HeaderSetting[element_type]"]').val() == '2') {
            var is_file_uploaded = $('#uploadedHeaderfile').get(0).files.length;
            if (is_default_header_image == '1' && is_file_uploaded == 0) {
                error = true;
                header_error = true;
                $('#notification_header_error').html(upload_file_error);
            }
        }
    }

    // footer form validation
    if ($('[id^="FooterSetting[enable]_on"]').is(':checked') === true) {
        var is_enable_footer = true;
    } else {
        var is_enable_footer = false;
    }
    if (is_enable_footer) {
        if ($('[id^="FooterSetting[fix_time]_on"]').is(':checked') === true) {
            var active_date = velovalidation.checkMandatory($("input[name='FooterSetting[active_date]']"));
            if (active_date !== true) {
                error = true;
                footer_error = true;
                $("input[name='FooterSetting[active_date]']").addClass('error_field');
                $("input[name='FooterSetting[active_date]']").parent().parent().append($('<p class="active_date kbeffect_error">' + active_date + '</p>'));
            }
            var expire_date = velovalidation.checkMandatory($("input[name='FooterSetting[expire_date]']"));
            if (expire_date !== true) {
                error = true;
                footer_error = true;
                $("input[name='FooterSetting[expire_date]']").addClass('error_field');
                $("input[name='FooterSetting[expire_date]']").parent().parent().append($('<p class="expire_date kbeffect_error">' + expire_date + '</p>'));
            }
            var activation_date = Date.parse($('input[name="FooterSetting[active_date]"]').val());
            var deactivation_date = Date.parse($('input[name="FooterSetting[expire_date]"]').val());
            if (parseInt(deactivation_date) <= parseInt(activation_date)) {
                error = true;
                footer_error = true;
                $('input[name="FooterSetting[expire_date]"]').addClass('error_field');
                $('input[name="FooterSetting[expire_date]"]').parent().parent().append('<span class="kbeffect_error">' + deactivation_date_error + '</span>');
            }
        }

        if ($('[name="FooterSetting[where_to_display]"]').val() === '2') {
            var selected_page = $("[name='FooterSetting[show_page][]']").val();
            if (selected_page === null) {
                error = true;
                footer_error = true;
                $("[name='FooterSetting[show_page][]']").addClass('error_field');
                $("[name='FooterSetting[show_page][]']").after($('<p class="selected_page kbeffect_error"></p>'));
                $('.selected_page').text(multiple_select_message);
            }
        }
        if ($('[name="FooterSetting[where_to_display]"]').val() === '3') {
            var unselected_page = $("[name='FooterSetting[not_show_page][]']").val();
            if (unselected_page === null) {
                error = true;
                footer_error = true;
                $("[name='FooterSetting[not_show_page][]']").addClass('error_field');
                $("[name='FooterSetting[not_show_page][]']").after($('<p class="unselected_page kbeffect_error"></p>'));
                $('.unselected_page').text(multiple_select_message);
            }
        }
        if ($('[name="FooterSetting[element_type]"]').val() == '2') {
            var is_file_uploaded = $('#uploadedFooterfile').get(0).files.length;
            if (is_default_footer_image == '1' && is_file_uploaded == 0) {
                error = true;
                footer_error = true;
                $('#notification_footer_error').html(upload_file_error);
            }
        }
    }
    // random form validation
    if ($('[id^="RandomSetting[enable]_on"]').is(':checked') === true) {
        var is_enable_random = true;
    } else {
        var is_enable_random = false;
    }
    if (is_enable_random) {
        if ($('[id^="RandomSetting[fix_time]_on"]').is(':checked') === true) {
            var active_date = velovalidation.checkMandatory($("input[name='RandomSetting[active_date]']"));
            if (active_date !== true) {
                error = true;
                random_error = true;
                $("input[name='RandomSetting[active_date]']").addClass('error_field');
                $("input[name='RandomSetting[active_date]']").parent().parent().append($('<p class="active_date kbeffect_error">' + active_date + '</p>'));
            }
            var expire_date = velovalidation.checkMandatory($("input[name='RandomSetting[expire_date]']"));
            if (expire_date !== true) {
                error = true;
                random_error = true;
                $("input[name='RandomSetting[expire_date]']").addClass('error_field');
                $("input[name='RandomSetting[expire_date]']").parent().parent().append($('<p class="expire_date kbeffect_error">' + expire_date + '</p>'));
            }
            var activation_date = Date.parse($('input[name="RandomSetting[active_date]"]').val());
            var deactivation_date = Date.parse($('input[name="RandomSetting[expire_date]"]').val());
            if (parseInt(deactivation_date) <= parseInt(activation_date)) {
                error = true;
                random_error = true;
                $('input[name="RandomSetting[expire_date]"]').addClass('error_field');
                $('input[name="RandomSetting[expire_date]"]').parent().parent().append('<span class="kbeffect_error">' + deactivation_date_error + '</span>');
            }
        }

        if ($('[name="RandomSetting[where_to_display]"]').val() === '2') {
            var selected_page = $("[name='RandomSetting[show_page][]']").val();
            if (selected_page === null) {
                error = true;
                random_error = true;
                $("[name='RandomSetting[show_page][]']").addClass('error_field');
                $("[name='RandomSetting[show_page][]']").after($('<p class="selected_page kbeffect_error"></p>'));
                $('.selected_page').text(multiple_select_message);
            }
        }
        if ($('[name="RandomSetting[where_to_display]"]').val() === '3') {
            var unselected_page = $("[name='RandomSetting[not_show_page][]']").val();
            if (unselected_page === null) {
                error = true;
                random_error = true;
                $("[name='RandomSetting[not_show_page][]']").addClass('error_field');
                $("[name='RandomSetting[not_show_page][]']").after($('<p class="unselected_page kbeffect_error"></p>'));
                $('.unselected_page').text(multiple_select_message);
            }
        }
        if ($('[name="RandomSetting[element_type]"]').val() == '2') {
            var is_file_uploaded = $('#uploadedRandomfile').get(0).files.length;
            if (is_default_random_image == '1' && is_file_uploaded == 0) {
                error = true;
                random_error = true;
                $('#notification_random_error').html(upload_file_error);
            }
        }
    }
    // discount form validation
    if ($('[id^="DiscountSetting[enable]_on"]').is(':checked') === true) {
        var is_enable_discount = true;
    } else {
        var is_enable_discount = false;
    }
    if (is_enable_discount) {
        if ($('[id^="DiscountSetting[fix_time]_on"]').is(':checked') === true) {
            var active_date = velovalidation.checkMandatory($("input[name='DiscountSetting[active_date]']"));
            if (active_date !== true) {
                error = true;
                discount_error = true;
                $("input[name='DiscountSetting[active_date]']").addClass('error_field');
                $("input[name='DiscountSetting[active_date]']").parent().parent().append($('<p class="active_date kbeffect_error">' + active_date + '</p>'));
            }
            var expire_date = velovalidation.checkMandatory($("input[name='DiscountSetting[expire_date]']"));
            if (expire_date !== true) {
                error = true;
                discount_error = true;
                $("input[name='DiscountSetting[expire_date]']").addClass('error_field');
                $("input[name='DiscountSetting[expire_date]']").parent().parent().append($('<p class="expire_date kbeffect_error">' + expire_date + '</p>'));
            }
            var activation_date = Date.parse($('input[name="DiscountSetting[active_date]"]').val());
            var deactivation_date = Date.parse($('input[name="DiscountSetting[expire_date]"]').val());
            if (parseInt(deactivation_date) <= parseInt(activation_date)) {
                error = true;
                discount_error = true;
                $('input[name="DiscountSetting[expire_date]"]').addClass('error_field');
                $('input[name="DiscountSetting[expire_date]"]').parent().parent().append('<span class="kbeffect_error">' + deactivation_date_error + '</span>');
            }
        }

        if ($('[name="DiscountSetting[where_to_display]"]').val() === '2') {
            var selected_page = $("[name='DiscountSetting[show_page][]']").val();
            if (selected_page === null) {
                error = true;
                discount_error = true;
                $("[name='DiscountSetting[show_page][]']").addClass('error_field');
                $("[name='DiscountSetting[show_page][]']").after($('<p class="selected_page kbeffect_error"></p>'));
                $('.selected_page').text(multiple_select_message);
            }
        }
        if ($('[name="DiscountSetting[where_to_display]"]').val() === '3') {
            var unselected_page = $("[name='DiscountSetting[not_show_page][]']").val();
            if (unselected_page === null) {
                error = true;
                discount_error = true;
                $("[name='DiscountSetting[not_show_page][]']").addClass('error_field');
                $("[name='DiscountSetting[not_show_page][]']").after($('<p class="unselected_page kbeffect_error"></p>'));
                $('.unselected_page').text(multiple_select_message);
            }
        }
        if ($('#redirect_coupon_id').val() == '0') {
            error = true;
            discount_error = true;
            $("[name='redirect_coupon_name']").addClass('error_field');
            $("[name='redirect_coupon_name']").after($('<p class="unselected_page kbeffect_error"></p>'));
            $('.unselected_page').text(no_coupon_added);
        }

    }

    // validation over
    if (general_error == true) {
        $('#link-GeneralSettings').attr('style', 'border: 2px solid red');
    } else {
        $('#link-GeneralSettings').attr('style', '');
    }
    if (header_error == true) {
        $('#link-HeaderElementSetting').attr('style', 'border: 2px solid red');
    } else {
        $('#link-HeaderElementSetting').attr('style', '');
    }
    if (footer_error == true) {
        $('#link-FooterElementSetting').attr('style', 'border: 2px solid red');
    } else {
        $('#link-FooterElementSetting').attr('style', '');
    }
    if (random_error == true) {
        $('#link-RandomElementSetting').attr('style', 'border: 2px solid red');
    } else {
        $('#link-RandomElementSetting').attr('style', '');
    }
    if (discount_error == true) {
        $('#link-DiscountCouponSetting').attr('style', 'border: 2px solid red');
    } else {
        $('#link-DiscountCouponSetting').attr('style', '');
    }

    if (error === true) {
        return false;
    }
}

