/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

var func_pa = {
    init: function () {
        func_pa.captchaType();
    },
    captchaType: function (val) {
        var _sl = val || $('input[name=PA_CAPTCHA_TYPE]:checked').val();
        var showV2 = _sl === 'google';
        var showV3 = _sl === 'google_v3';
        var showEntBox = _sl === 'enterprise_checkbox';
        var showEntScore = _sl === 'enterprise_score';

        var selectorsShow = [];
        var selectorsHide = [
            '.row_pa_google_captcha_site_key',
            '.row_pa_google_captcha_secret_key',
            '.row_pa_google_captcha_theme',
            '.row_pa_google_captcha_label',
            '.row_pa_google_v3_captcha_site_key',
            '.row_pa_google_v3_captcha_secret_key',
            '.row_pa_google_v3_captcha_score',
            '.row_pa_google_v3_position',
            '.row_pa_google_enterprise_key_id',
            '.row_pa_google_enterprise_project_id',
            '.row_pa_google_enterprise_api_key',
            '.row_pa_google_enterprise_score',
            '.row_pa_google_sb_enterprise_key_id',
            '.row_pa_google_sb_enterprise_project_id',
            '.row_pa_google_sb_enterprise_api_key',
            '.row_pa_google_sb_enterprise_score'
        ];

        if (showV2) {
            selectorsShow = [
                '.row_pa_google_captcha_site_key',
                '.row_pa_google_captcha_secret_key',
                '.row_pa_google_captcha_theme',
                '.row_pa_google_captcha_label'
            ];
        } else if (showV3) {
            selectorsShow = [
                '.row_pa_google_v3_captcha_site_key',
                '.row_pa_google_v3_captcha_secret_key',
                '.row_pa_google_v3_captcha_score',
                '.row_pa_google_v3_position',
                '.row_pa_google_captcha_theme'
            ];
        } else if (showEntBox) {
            selectorsShow = [
                '.row_pa_google_enterprise_key_id',
                '.row_pa_google_enterprise_project_id',
                '.row_pa_google_enterprise_api_key',
                '.row_pa_google_enterprise_score',
                '.row_pa_google_captcha_theme'
            ];
        } else if (showEntScore) {
            selectorsShow = [
                '.row_pa_google_sb_enterprise_key_id',
                '.row_pa_google_sb_enterprise_project_id',
                '.row_pa_google_sb_enterprise_api_key',
                '.row_pa_google_sb_enterprise_score'
            ];
        }

        $(selectorsHide.join(',')).hide();
        if (selectorsShow.length) {
            $(selectorsShow.join(',')).show();
        }
        // Score-based Enterprise: do not show theme/position fields
        if (showEntScore) {
            $('.row_pa_google_captcha_theme, .row_pa_google_v3_position').hide();
        }
    }
};
$(document).ready(function () {
    func_pa.init();
    $('input[name="PA_CAPTCHA_TYPE"]').change(function () {
        func_pa.captchaType($(this).val());
    });
});
