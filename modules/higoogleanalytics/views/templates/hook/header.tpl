{**
* 2012 - 2022 HiPresta
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License 3.0 (AFL-3.0).
* It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
*
* @author    HiPresta <support@hipresta.com>
* @copyright HiPresta 2022
* @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
* @link      https://hipresta.com
*}

{if $hiEnbleGa4Tracking}
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={$hiGa4MeasurementId}"></script>
    <script>
        {literal}
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            if (hiGaSettings.enbleGa4Tracking) {
                gtag('config', hiGaSettings.ga4MeasurementId);
            }

            // Set default values
            gtag('set', {
                'page_title': prestashop.page.meta.title,
                'page_name': prestashop.page.page_name,
                'language': prestashop.language.iso_code,
                'currency': prestashop.currency.iso_code
            });
        {/literal}
    </script>
{/if}