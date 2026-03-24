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
        let data = '{/literal}{$hiGaPurchaseData nofilter}{literal}';
        try {
            data = JSON.parse(data);
            data['event_callback'] = function (containerId) {
                var hiGaInterval = setInterval(function(){
                    if (typeof hiGoogleAnalytics != 'undefined') {
                        clearInterval(hiGaInterval);

                        hiGoogleAnalytics.displayDebugModal(data, 'purchase', containerId);
                    }
                }, 200)
            }
            gtag("event", "purchase", data);
        } catch (e) {}
    {/literal}
</script>