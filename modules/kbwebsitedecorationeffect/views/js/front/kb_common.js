/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2018 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
 */
$(document).ready(function () {
    if ($("#footer_decoration").length) {
        $("#footer_decoration").hide();
    }
    if ($("#header_decoration").length) {
        $("#header_decoration").hide();
    }
    $(window).scroll(function () {
        if ($("#footer_decoration").length) {
            if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
                $("#footer_decoration").fadeOut('2000');
            } else {
                $("#footer_decoration").fadeIn('2000');
            }
        }
        if ($("#header_decoration").length) {
            if ($(window).scrollTop() < 100) {
                $("#header_decoration").fadeOut('2000');
            } else {
                $("#header_decoration").fadeIn('2000');
            }
        }
    });

    if ($('.offer-countdown').length > 0) {
        var countHeight = $('.offer-countdown').outerHeight();
        if ($('#footer_decoration').length) {
            $('#footer_decoration').addClass('otherBottomPresent').css({'bottom': +countHeight});
        }
    } else {
        if ($('#footer_decoration').length) {
            $('#footer_decoration').removeClass('otherBottomPresent');
        }
    }
});