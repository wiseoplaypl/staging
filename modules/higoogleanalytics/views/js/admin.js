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
    hideMenuModule: function() {
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: hiGaAdminController,
            data: {
                ajax: true,
                action: 'hideMenuModule',
                secure_key: hiGaSecurekey
            },
            beforeSend: function() {
                $('.hipresta-modules-menu-ad').remove();
            },
            success: function(response) {},
            error: function(jqXHR, error, errorThrown) {}
        });
    }
}
$(function() {
    $('#form-higacustomevent').hiPrestaTable({
        friendlyName: 'CustomEvent',
        secureKey: hiGaSecurekey,
        ajaxUrl: hiGaAdminController,
        identifier: 'id_event'
    });

    $(document).on('click', '.hide-hm-menu-module', function(e) {
        e.preventDefault();

        hiGoogleAnalytics.hideMenuModule();
    })
});