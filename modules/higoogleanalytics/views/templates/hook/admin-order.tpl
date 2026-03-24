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

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$hiGa4MeasurementId}"></script>
<script>
    {literal}
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
    {/literal}
    {if $hiEnbleGa4Tracking}
        {literal}gtag('config', '{/literal}{$hiGa4MeasurementId}{literal}');{/literal}
    {/if}
</script>

<script>
    {foreach $refunds as $refund}
        {literal}
            let data = '{/literal}{$refund|@json_encode nofilter}{literal}';
            try {
                data = JSON.parse(data);
                gtag("event", "refund", data);
            } catch (e) {}
        {/literal}
    {/foreach}
</script>