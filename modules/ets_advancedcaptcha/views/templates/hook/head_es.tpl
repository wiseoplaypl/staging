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

<script src="https://www.google.com/recaptcha/enterprise.js?render={$PA_GOOGLE_SB_ENTERPRISE_KEY_ID|escape:'quotes':'UTF-8'}{if isset($hl) && $hl}&hl={$hl|escape:'quotes':'UTF-8'}{/if}" async defer></script>
<script type="text/javascript">
    var PA_GOOGLE_SB_ENTERPRISE_KEY_ID = '{$PA_GOOGLE_SB_ENTERPRISE_KEY_ID|escape:'html':'UTF-8'}';
    {literal}
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.grecaptcha || !window.grecaptcha.enterprise || !PA_GOOGLE_SB_ENTERPRISE_KEY_ID) {
            return;
        }
        var action = (document.body && document.body.id)
            ? document.body.id.replace(/(?=[^A-Za-z_])([^A-Za-z_])/g, '_')
            : 'submit';

        var forms = document.getElementsByTagName('form');
        for (var i = 0; i < forms.length; i++) {
            (function (form) {
                var holders = form.querySelectorAll('[id^="g-recaptcha-response-"]');
                if (!holders.length) {
                    return;
                }
                grecaptcha.enterprise.ready(function () {
                    grecaptcha.enterprise.execute(PA_GOOGLE_SB_ENTERPRISE_KEY_ID, {action: action}).then(function (token) {
                        if (!token) {
                            return;
                        }
                        for (var j = 0; j < holders.length; j++) {
                            var g = holders[j];
                            var existing = g.querySelector('input[name="g-recaptcha-response"]');
                            if (existing) {
                                existing.value = token;
                            } else {
                                var input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'g-recaptcha-response';
                                input.value = token;
                                g.appendChild(input);
                            }
                            var legacyTextarea = g.querySelector('textarea#g-recaptcha-response');
                            if (legacyTextarea) {
                                legacyTextarea.parentNode.removeChild(legacyTextarea);
                            }
                        }
                    });
                });
            })(forms[i]);
        }
    });
    {/literal}
</script>
