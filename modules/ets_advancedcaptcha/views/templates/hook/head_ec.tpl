{*
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
*}

<script src="https://www.google.com/recaptcha/enterprise.js?onload=onloadCallback&render=explicit{if isset($hl) && $hl}&hl={$hl|escape:'quotes':'UTF-8'}{/if}" async defer></script>
<script type="text/javascript">
    var PA_GOOGLE_CAPTCHA_THEME = '{$PA_GOOGLE_CAPTCHA_THEME|escape:'html':'UTF-8'}';
    var PA_GOOGLE_ENTERPRISE_KEY_ID = '{$PA_GOOGLE_ENTERPRISE_KEY_ID|escape:'html':'UTF-8'}';
    {literal}
        var recaptchaWidgets = [];
        var onloadCallback = function () {
            if (!window.grecaptcha || !window.grecaptcha.enterprise || !PA_GOOGLE_ENTERPRISE_KEY_ID) {
                return;
            }
            var api = window.grecaptcha.enterprise;
            var forms = document.getElementsByTagName('form');
            var pattern = /(^|\s)g-recaptcha(\s|$)/;
            for (var i = 0; i < forms.length; i++) {
                var items = forms[i].getElementsByTagName('div');
                for (var k = 0; k < items.length; k++) {
                    if (items[k].className && items[k].className.match(pattern)) {
                        if (items[k].innerHTML && items[k].innerHTML.replace(/\s/g, '') !== '') {
                            continue;
                        }
                        try {
                            var widget_id = api.render(items[k], {
                                sitekey: PA_GOOGLE_ENTERPRISE_KEY_ID,
                                theme: PA_GOOGLE_CAPTCHA_THEME ? PA_GOOGLE_CAPTCHA_THEME : 'light'
                            });
                            recaptchaWidgets.push(widget_id);
                        } catch (e) {
                        }
                        break;
                    }
                }
            }
        };
    {/literal}
</script>

