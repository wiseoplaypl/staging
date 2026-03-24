{**
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
 *}
<script type="text/javascript">
    {literal}
        var psv = {/literal}{$psv|floatval}{literal};
        var id_lang = {/literal}{$id_lang|intval}{literal};
        var hiGaSecurekey = '{/literal}{$hiGaSecureKey|escape:'htmlall':'UTF-8'}{literal}';
        var hiGaAdminController = '{/literal}{$hiGaAdminController nofilter}{literal}';
        var address_token = '{/literal}{getAdminToken tab='AdminAddresses'}{literal}';
        var ajaxErrorMessage = "{/literal}{l s='Something went wrong, please refresh the page and try again' mod='higoogleanalytics'}{literal}";
    {/literal}
</script>