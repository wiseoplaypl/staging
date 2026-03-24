{*
* 2007-2025 PrestaShop
*

* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2025 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}

<div id="stripe-translations">
        <span id="stripe-missing">{l s='There is no card on a customer that is being charged.' mod='stripejs'}</span>
        <span id="stripe-processing_error">{l s='An error occurred while processing the card.' mod='stripejs'}</span>
        <span id="stripe-payment_method_not_available">{l s='The payment method is currently not available. Please use another payment method to proceed.' mod='stripejs'}</span>
        <span id="stripe-rate_limit">{l s='An error occurred due to requests hitting the API too quickly. Please let us know if you\'re consistently running into this error.' mod='stripejs'}</span>
        <span id="stripe-3d_declined">{l s='The card doesn\'t support 3DS.' mod='stripejs'}</span>
        <span id="stripe-3d_required">{l s='3D Secure is required to process the payment.' mod='stripejs'}</span>
        <span id="stripe-no_api_key">{l s='There\'s an error with your API keys. If you\'re the administrator of this website, please go on the "Connection" tab of your plugin.' mod='stripejs'}</span>
        <span id="stripe-timeout">{l s='Request timed out, please try again..' mod='stripejs'}</span>
        <span id="stripe-wechat_declined">{l s='Wechat payment was declined.' mod='stripejs'}</span>
        <span id="stripe-please-fix">{l s='Please fix it and submit your payment again.' mod='stripejs'}</span>
      </div>
<div id="stripe-ajax-loader-redirect"><div class="spinner-border"></div> {l s='Do not press BACK or REFRESH while processing...' mod='stripejs'}</div>

<div id="modal-stripe-error" class="modal" style="display: none">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <p class="stripe-payment-europe-errors"></p>
</div>
<div id="sofort_available_countries" class="modal" style="display: none">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="title">{l s='Choose your bank country :' mod='stripejs'}</div>
    <select id="sofort_country">
        {foreach from=$sofort_countries item=country key=iso}
            <option value="{$iso|escape:'htmlall':'UTF-8'}" {if isset($country_iso_code) && $iso == $country_iso_code} selected="selected"{/if}>{$country|escape:'htmlall':'UTF-8'}</option>
        {/foreach}
    </select><br>
    <button class="btn btn-primary" onClick="$('#payment-confirmation button[type=submit]').addClass('sofort_country_selected');$('#sofort_available_countries').modalStripe().close();">{l s='Select' mod='stripejs'}</button>
</div>
<script type="text/javascript">
 //////////////////Variables/////////////////////
  var ps_cart_id;
  var cu_email;
  var cu_fname;
  var cu_lname;
  var billing_address;
  var ship_address;
  var country_iso_code;
  var amount_ttl;
  ////////////////Variables/////////////////////
  var mode = {$STRIPE_MODES|escape:'htmlall':'UTF-8'};
  var logo_url = "{$logo_url|escape:'htmlall':'UTF-8'}";
  var shop_name = "{Configuration::get('PS_SHOP_NAME')|escape:'htmlall':'UTF-8'}";
  var lang_iso_code = "{$lang_iso_code|escape:'htmlall':'UTF-8'}";
  var currency = "{$currency_iso|escape:'htmlall':'UTF-8'}";
  var currency_lower = "{$currency_iso|lower|escape:'htmlall':'UTF-8'}";
  var baseDir = "{$baseDir|escape:'htmlall':'UTF-8'}";
  var module_dir = "{$module_dir|escape:'htmlall':'UTF-8'}";
  var StripePubKey = "{$publishableKey|escape:'htmlall':'UTF-8'}";
  var StripePETheme = {if !Configuration::get('STRIPE_PETheme')}'tabs'{else}'accordion'{/if};
  var StripePEThemeRadio = {if Configuration::get('STRIPE_PETheme')==2}true{else}false{/if};
  var stripe_allow_zip = {if $stripe_allow_zip}false{else}true{/if};
  var stripe_allow_cards = {$stripe_allow_cards|escape:'htmlall':'UTF-8'};
  var stripe_allow_sepa = {if $stripe_allow_sepa}true{else}false{/if};
  var stripe_allow_fpx = {if Configuration::get('STRIPE_ALLOW_FPX')}true{else}false{/if};
  var stripe_allow_ideal = {if Configuration::get('STRIPE_ALLOW_IDEAL')}true{else}false{/if};
  var stripe_allow_prbutton = {if isset($stripe_allow_prbutton) && $stripe_allow_prbutton}true{else}false{/if};
  var validation_url = "{$order_validation_url|escape:'htmlall':'UTF-8'}";
  var stripe_error = "{$stripe_error|escape:'htmlall':'UTF-8'}";
  var stripe_error_msg = "{l s='An error occured during transaction. Please try again or contact us' mod='stripejs'}";
  var ajax_payment = "{$ajax_payment nofilter}";
  var prbutton_alert = "{l s='Click on Pay Now button under Pay with Google/Apple Pay/ Microsoft Pay option.' mod='stripejs'}";
  var bank_empty_error = "{l s='Please select a bank first.' mod='stripejs'}";
  var confirm_unload_msg = "{l s='If you leave now, the transaction may not finalize. Are you sure?' mod='stripejs'}";
</script>
