{*
* Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

<!-- BEGIN GCR Opt-in Module Code -->
<script src="https://apis.google.com/js/platform.js?onload=renderOptIn"
    async defer>
</script>

<script>
    {literal}
        window.renderOptIn = function() {
            window.gapi.load('surveyoptin', function() {
                window.gapi.surveyoptin.render({
                    "merchant_id": {/literal}{$google_reviews_id|escape:'htmlall':'UTF-8'}{literal},
                    "order_id": {/literal}{$google_reviews_id_order|escape:'htmlall':'UTF-8'}{literal},
                    "email": "{/literal}{$google_reviews_email|escape:'htmlall':'UTF-8'}{literal}",
                    "delivery_country": "{/literal}{$google_reviews_delivery_country|escape:'htmlall':'UTF-8'}{literal}",
                    "estimated_delivery_date": "{/literal}{$google_reviews_date|escape:'htmlall':'UTF-8'}{literal}",
                    "opt_in_style": "CENTER_DIALOG"
                });
            });
        }
    {/literal}
</script>
<!-- END GCR Opt-in Module Code -->