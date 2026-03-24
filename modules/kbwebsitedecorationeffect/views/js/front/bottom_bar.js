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
    if ($('.offer-countdown-close').length) {
        $('.offer-countdown-close').on('click', function () {
            $('.offer-countdown').hide();
            if ($('#footer_decoration').length) {
                $('#footer_decoration').removeClass('otherBottomPresent');
                $('#footer_decoration').css('bottom', '0px');
            }
        });
    }
});