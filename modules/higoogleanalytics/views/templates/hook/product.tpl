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

<script>
    {literal}
        let data = '{/literal}{$hiGaProductData nofilter}{literal}';
        try {
            data = JSON.parse(data);
            data['event_callback'] = function (containerId) {
                var hiGaInterval = setInterval(function(){
                    if (typeof hiGoogleAnalytics != 'undefined') {
                        clearInterval(hiGaInterval);

                        hiGoogleAnalytics.displayDebugModal(data, 'view_item', containerId);
                    }
                }, 200);
            }
            gtag('event', 'view_item', data);
        } catch (e) {}
    {/literal}
</script>