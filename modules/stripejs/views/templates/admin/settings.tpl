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

 <script type="text/javascript">
    $(document).ready(function() {

    $("#get_api_link").on('click', function (e) {
			var s_mode = $("input[type='radio'][name='STRIPE_MODES']:checked").val();
			//const get_api_link = s_mode == 0 ? 'https://dashboard.stripe.com/test/settings/apps/com.ntsconnect.ps?showKeys=true' : 'https://dashboard.stripe.com/settings/apps/com.ntsconnect.ps?showKeys=true';
      const get_api_link = "https://marketplace.stripe.com/apps/install/link/com.ntsconnect.ps?showKeys=true";
      window.open(get_api_link);
      return false;
		});


    $("#get_webhook_link").on('click', function (e) {
			var s_mode = $("input[type='radio'][name='STRIPE_MODES']:checked").val();
			const get_api_link = s_mode == 0 ? 'https://dashboard.stripe.com/test/workbench/webhooks/create' : 'https://dashboard.stripe.com/workbench/webhooks/create';
      var events = "?events=checkout.session.completed%2Cpayment_intent.amount_capturable_updated%2Cpayment_intent.succeeded%2Cpayment_intent.processing%2Cpayment_intent.canceled%2Cpayment_intent.payment_failed%2Ccharge.dispute.created";
      window.open(get_api_link + events);
      return false;
		});

		$(".toggle_api_key").on('click', function (e) {
			var pass_field = $(this).parents('.form-group:first').find("input:first");
			const type = pass_field.attr('type') === 'password' ? 'text' : 'password';
			pass_field.attr('type', type);
			$(this).toggleClass('icon-eye');
			$(this).toggleClass('icon-eye-slash');
		});
		$('li.faq-item .faq-trigger').click(function(e) {
			$(this).parents('li.faq-item').find('.faq-content').toggle();
			if($(this).parents('li.faq-item').find('span.expand').text()=='+')
			$(this).parents('li.faq-item').find('span.expand').html('-');
			else
			$(this).parents('li.faq-item').find('span.expand').html('+');
		});
		$("input[name='STRIPE_MODES']").change(function(e) {
		   $('#stripe_test_keys,#stripe_live_keys').toggle();
		});

		 $(".stripe-module-wrapper .list-group .list-group-item").click(function(){
			 $(".list-group .list-group-item").removeClass("active");
			 $(this).addClass("active");
			 var ID = $(this).attr("id");
			 $(".stripe-module-wrapper fieldset").removeClass("show");
			 $(".stripe-module-wrapper fieldset."+ID).addClass("show");
		});
});
</script>
<link href="{$stripeBOCssUrl|escape:'htmlall':'UTF-8'}" rel="stylesheet" type="text/css">
{if isset($errors)}
    {foreach from=$errors item=errors_message}
        <div class="alert alert-danger clearfix">
            {$errors_message|escape:'htmlall':'UTF-8'}
        </div>
    {/foreach}
{/if}
{if $success}<div class="conf confirmation alert alert-success">{l s='Settings successfully saved' mod='stripejs'}</div>{/if}

            <div class="tabs stripe-module-wrapper">
            <div class="panel form-horizontal col-lg-12" style="margin-bottom: 7px;text-align: center;padding: 10px 0px;">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/stripe-cc.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/cc-sepa.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/google.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/apple.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/mspay.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/wechat.png">

            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/bancontact.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/ideal.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/giropay.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/sofort.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/p24.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/eps.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/multibanco.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/cc-klarna.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/cc-fpx.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/alipay.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/grabpay.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/oxxo.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/afterpay.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/clearpay.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/affirm.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/paynow.png">
            <img class="tabs-logo" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/paypal.png">
            </div>

            <div class="sidebar navigation col-md-3 col-lg-3">
              <div style="text-align:center;"><img src="{$this_path|escape:'htmlall':'UTF-8'}views/img/stripe.png"></div>
              <nav class="list-group categorieList">
  <a class="list-group-item active" id="technical_checkes" href="javascript:void(0);"><i class="icon-check-circle-o tabcbpfw-icon"></i>{l s='Technical Checks' mod='stripejs'}
  <span class="badge-module-tabs pull-right {if $requirements['result']}tab-success{else}tab-warning{/if}"></span></a>
  <a class="list-group-item" id="stripe_settings" href="javascript:void(0);"><i class="icon-key tabcbpfw-icon"></i>{l s='Stripe Configuration' mod='stripejs'}
  <span class="badge-module-tabs pull-right {if $checkSettings}tab-success{else}tab-warning{/if}"></span></a>
  <a class="list-group-item" id="order_statuses" href="javascript:void(0);"><i class="icon-filter tabcbpfw-icon"></i>{l s='Order Statuses' mod='stripejs'}</a>
  <a class="list-group-item" id="stripe_webhooks" href="javascript:void(0);"><i class="icon-question tabcbpfw-icon"></i>{l s='FAQ & Test Cards' mod='stripejs'}</a>
  <br /> <br />
  <a class="list-group-item" id="technical_checkes" href="https://addons.prestashop.com/ratings.php" target="_blank"><i class="icon-star tabcbpfw-icon" style="color:gold;"></i>{l s='Rate me' mod='stripejs'}</a>
  <br />
  <a class="list-group-item" id="technical_checkes" target="_blank"><i class="icon-info tabcbpfw-icon"></i>{l s='Version' mod='stripejs'}: {$ps_version|escape:'htmlall':'UTF-8'}</a>
          </nav>
        </div>

        <div class="panel content-wrap form-horizontal col-md-9 col-lg-9">
        <fieldset class="technical_checkes show">
          <h3 class="tab"> <i class="icon-check-circle-o"></i>&nbsp;{l s='Technical Checks' mod='stripejs'}</h3>

          <div class="{if $requirements['result']}conf confirmation alert alert-success">{l s='Good news! All the checks were successfully performed. You can now configure your module and start using Stripe.' mod='stripejs'}{else}
          error alert alert-danger">{l s='Unfortunately, at least one issue is preventing you from using Stripe. Please fix the issue and reload this page.' mod='stripejs'}{/if}</div><table cellspacing="0" cellpadding="0" class="stripe-technical">
          {foreach $requirements as $k => $requirement}
            {if $k != 'result'}
                <tr>
                    <td valign="top"><img src="../img/admin/{if $requirement['result']}enabled{else}disabled{/if}.gif" alt="" />&nbsp;</td>
                    <td>{$requirement['name']|escape:'htmlall':'UTF-8'}.
                    {if !$requirement['result'] && isset($requirement['resolution'])} <br />{$requirement['resolution']|escape:'htmlall':'UTF-8'}{/if}

                    {if $k == 'webhook' && !$requirement['result']}
                  <hr />
                    <ol>
                      <li>{l s='To add webhook in your stripe dashboard' mod='stripejs'}
                    <a id="get_webhook_link" href="javascript:void(0);"><b> {l s='Click here' mod='stripejs'} <i class="icon-external-link"></i></b></a>
                  </li>
                    <li>{l s='Webhook events will be selected automatically. Click on the Continue button.' mod='stripejs'}</li>
                    <li>{l s='Add this Webhook Url' mod='stripejs'}: <b>{$webhook_url|escape:'htmlall':'UTF-8'}</b></li>
                    <li>{l s='Refresh this page when its done.' mod='stripejs'}</li>
                  </ol>

                    {/if}
                  </td>
                </tr>
                {/if}
            {/foreach}
          </table>
				<h2>{l s='Please read information in FAQ tab for any query. For any technical support' mod='stripejs'} <a href="https://addons.prestashop.com/contact-community.php?id_product=17856" target="_blank">{l s='click here' mod='stripejs'} <i class="icon-external-link"></i></a></h2>
        </fieldset>

        <form action="" method="post" autocomplete="off">
        <fieldset class="stripe_settings">
        <h3 class="tab"> <i class="icon-key"></i>&nbsp;{l s='Stripe Connection' mod='stripejs'}</h3>

        <div class="form-group">

          <div class="alert alert-info clearfix">
            <ul style="margin:0px;padding:0px;">
              <li>{l s='Stripe is requiring all plugins to adopt new secure authentication methods (Restricted API Key, OAuth 2.0, or Stripe Connect) to protect users against fraud.' mod='stripejs'} <a href="https://support.stripe.com/questions/plugin-user-migration-guide" target="_blank">{l s='Read more' mod='stripejs'}</i></a></li>
              <li>
            {l s='To generate your api keys with a permissioned Restricted API key (RAK), use below GENERATE API KEYS option. It will ask you to install our "NTS Connect" app in your Stripe dashboard.' mod='stripejs'}</li></ul>
          </div>

        <label class="control-label col-lg-4" for="simple_product">{l s='Mode' mod='stripejs'}:</label>
        <div class="col-lg-8">
          <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_MODES" id="STRIPE_MODES_1" value="1" {if Configuration::get('STRIPE_MODES')}checked="checked"{/if}>
              <label for="STRIPE_MODES_1">{l s='LIVE' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_MODES" id="STRIPE_MODES_0" value="0" {if !Configuration::get('STRIPE_MODES')}checked="checked"{/if}>
              <label for="STRIPE_MODES_0">{l s='TEST' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
          </span>
        </div></div>
        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product"></label>
        <div class="col-lg-8">
            <!--<button type="submit" name="SubmitOAuthInstall" class="btn btn-primary"><i class="icon-lock"></i> &nbsp;{l s='Install OAuth 2.0' mod='stripejs'}</button>-->
            <a id="get_api_link" href="javascript:void(0);" class="btn btn-primary"><i class="icon icon-key"></i> &nbsp;{l s='GENERATE API KEYS' mod='stripejs'}</a><br />
        </div></div>
        <div {if Configuration::get('STRIPE_MODES')}style="display:none;"{/if} id="stripe_test_keys">
        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product">{l s='Test Publishable Key' mod='stripejs'}:</label>
        <div class="col-lg-6">
                <input type="text" name="stripe_public_key_test" value="{Configuration::get('STRIPE_PUBLIC_KEY_TEST')|escape:'htmlall':'UTF-8'}" />
        </div></div>
        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product">{l s='Test Restricted API Key (RAK)' mod='stripejs'}:</label>
        <div class="col-lg-6">
            <input type="password" name="stripe_private_key_test" value="{Configuration::get('STRIPE_PRIVATE_KEY_TEST')|escape:'htmlall':'UTF-8'}" autocomplete="new-password" />
            <i class="icon icon-eye toggle_api_key pass_eye"></i>
        </div></div>
        </div>
        <div {if !Configuration::get('STRIPE_MODES')}style="display:none;"{/if} id="stripe_live_keys">
        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product">{l s='Live Publishable Key' mod='stripejs'}:</label>
        <div class="col-lg-6">
                <input type="text" name="stripe_public_key_live" value="{Configuration::get('STRIPE_PUBLIC_KEY_LIVE')|escape:'htmlall':'UTF-8'}" />
        </div></div>
        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product">{l s='Live Restricted API Key (RAK)' mod='stripejs'}:</label>
        <div class="col-lg-6">
            <input type="password" name="stripe_private_key_live" value="{Configuration::get('STRIPE_PRIVATE_KEY_LIVE')|escape:'htmlall':'UTF-8'}" autocomplete="new-password" />
            <i class="icon icon-eye toggle_api_key pass_eye"></i>
        </div></div>
        </div>

        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product">{l s='Accept payments via:' mod='stripejs'}</label>
        <div class="col-lg-8">
          <input type="radio" name="STRIPE_ALLOW_CARDS" id="payment_c" value="3" {if Configuration::get('STRIPE_ALLOW_CARDS')==3} checked="checked"{/if} /> &nbsp;
          <label class="control-label" for="payment_c"><b>{l s='Stripe Payment element form' mod='stripejs'}</b>&nbsp;<i style="color:#00aff0;">({l s='new' mod='stripejs'})</i></label>
          <ul style="font-style:italic;">
          <li>
          <i>{l s='Shows all the enabled payment options in your Stripe dashboard' mod='stripejs'}</i> <a href="https://dashboard.stripe.com/settings/payment_methods" class="btc_link" target="_blank"><i>{l s='here' mod='stripejs'}</i></a></li>
          <li>
            {l s='Payment Element Theme' mod='stripejs'}:
            <select name="STRIPE_PETheme" style="width:auto">
                    <option value="0" {if !Configuration::get('STRIPE_PETheme')}selected="selected"{/if}>{l s='Tabs' mod='stripejs'}</option>
                    <option value="1" {if Configuration::get('STRIPE_PETheme')==1}selected="selected"{/if}>{l s='Accordion' mod='stripejs'}</option>
                    <option value="2" {if Configuration::get('STRIPE_PETheme')==2}selected="selected"{/if}>{l s='Accordion with radio button' mod='stripejs'}</option>
            </select>
          </li>
          </ul>
            <!--<input type="radio" name="STRIPE_ALLOW_CARDS" id="cc_c" value="1" {if Configuration::get('STRIPE_ALLOW_CARDS')==1} checked="checked"{/if} /> &nbsp;
            <label class="control-label" for="cc_c"><b>{l s='Stripe Card element form' mod='stripejs'}</b></label>
            <ul style="margin:0px;">
            <li><i>{l s='Onsite card payment option with card element apis.' mod='stripejs'}</i></li>
            <li><i>{l s='Offer Wallets, Bank debits, local payment options on checkout separately.' mod='stripejs'}</i></li>
          </ul>-->
            <input type="radio" name="STRIPE_ALLOW_CARDS" id="stripe_c" value="2" {if Configuration::get('STRIPE_ALLOW_CARDS')==2} checked="checked"{/if} /> &nbsp;
            <label class="control-label" for="stripe_c"><b>{l s='Stripe Checkout' mod='stripejs'}</b></label>
            <ul style="font-style:italic;">
              <li>
              <i>{l s='Shows all the enabled payment options in your Stripe dashboard' mod='stripejs'}</i> <a href="https://dashboard.stripe.com/settings/payment_methods" class="btc_link" target="_blank"><i>{l s='here' mod='stripejs'}</i></a></li>
            <li><i>{l s='Go to Branding settings to upload your icon or logo, and set colors' mod='stripejs'}</i> <a href="https://dashboard.stripe.com/account/branding" class="btc_link" target="_blank"><i>{l s='here' mod='stripejs'}</i></a></li>
            <li>
              {l s='Open Checkout payment page as' mod='stripejs'}:
              <select name="STRIPE_Checkout_Open" style="width:auto">
                      <option value="0" {if !Configuration::get('STRIPE_Checkout_Open')}selected="selected"{/if}>{l s='Hosted' mod='stripejs'}</option>
                      <option value="1" {if Configuration::get('STRIPE_Checkout_Open')==1}selected="selected"{/if}>{l s='Embedded' mod='stripejs'}</option>
              </select>
            </li>
          </ul>
            <input type="radio" name="STRIPE_ALLOW_CARDS" id="none_c" value="0" {if Configuration::get('STRIPE_ALLOW_CARDS')==0} checked="checked"{/if} /> &nbsp;
            <label class="control-label" for="none_c"><b>{l s='none' mod='stripejs'}</b></label>
        </div></div>

        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product">{l s='Send payment receipt from Stripe' mod='stripejs'}:</label>
        <div class="col-lg-8">
        <span class="switch prestashop-switch fixed-width-lg">
          <input type="radio" name="STRIPE_PAYMENT_RECEIPT" id="STRIPE_RECEIPT_1" value="1" {if Configuration::get('STRIPE_PAYMENT_RECEIPT')}checked="checked"{/if}>
          <label for="STRIPE_RECEIPT_1">{l s='Yes' mod='stripejs'}</label>
          <input type="radio" name="STRIPE_PAYMENT_RECEIPT" id="STRIPE_RECEIPT_0" value="0" {if !Configuration::get('STRIPE_PAYMENT_RECEIPT')}checked="checked"{/if}>
          <label for="STRIPE_RECEIPT_0">{l s='No' mod='stripejs'}</label>
          <a class="slide-button btn"></a>
        </span>
        </div></div>

        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product">{l s='Add #Order ID in Stripe payment description' mod='stripejs'}:</label>
        <div class="col-lg-8">
        <span class="switch prestashop-switch fixed-width-lg">
          <input type="radio" name="STRIPE_CHARGE_ORDERID" id="STRIPE_ORDERID_1" value="1" {if Configuration::get('STRIPE_CHARGE_ORDERID')}checked="checked"{/if}>
          <label for="STRIPE_ORDERID_1">{l s='Yes' mod='stripejs'}</label>
          <input type="radio" name="STRIPE_CHARGE_ORDERID" id="STRIPE_ORDERID_0" value="0" {if !Configuration::get('STRIPE_CHARGE_ORDERID')}checked="checked"{/if}>
          <label for="STRIPE_ORDERID_0">{l s='No' mod='stripejs'}</label>
          <a class="slide-button btn"></a>
        </span>
        </div></div>

        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product"><span title="{l s='It will create customer object in Stripe with info like name, email and address. It helps RADAR monitoring system of Stripe for payment frauds.' mod='stripejs'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='Send customer info to Stripe' mod='stripejs'}:</span></label>
        <div class="col-lg-8">
        <span class="switch prestashop-switch fixed-width-lg">
          <input type="radio" name="STRIPE_CUSTOMER_INFO" id="STRIPE_INFO_1" value="1" {if Configuration::get('STRIPE_CUSTOMER_INFO')}checked="checked"{/if}>
          <label for="STRIPE_INFO_1">{l s='Yes' mod='stripejs'}</label>
          <input type="radio" name="STRIPE_CUSTOMER_INFO" id="STRIPE_INFO_0" value="0" {if !Configuration::get('STRIPE_CUSTOMER_INFO')}checked="checked"{/if}>
          <label for="STRIPE_INFO_0">{l s='No' mod='stripejs'}</label>
          <a class="slide-button btn"></a>
        </span>
        </div></div>

        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product"><span title="{l s='This option provides multiple payment view options for all 3rd party OPC (OnePageCheckout) modules available in market.' mod='stripejs'}" class="label-tooltip" data-toggle="tooltip" title="">
                {l s='Show Payment options on' mod='stripejs'}:
            </span></label>
        <div class="col-lg-8">
          <select name="STRIPE_PAYMENT_METHODS_OPC" style="width:auto">
                  <option value="0" {if !Configuration::get('STRIPE_PAYMENT_METHODS_OPC')}selected="selected"{/if}>{l s='Next Page' mod='stripejs'}</option>
                  <option value="1" {if Configuration::get('STRIPE_PAYMENT_METHODS_OPC')==1}selected="selected"{/if}>{l s='Embedded' mod='stripejs'}</option>
          </select>
          <b>{l s='NOTE: Applicable for 3rd party OPC modules only.' mod='stripejs'}</b>
        </div></div>

       <div class="stripe-legend-div">
        <div class="stripe-legend">{l s='Card payment settings' mod='stripejs'}</div>
        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product"><span title="{l s='Faster checkout experience for your returning customers.' mod='stripejs'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='Ask to save cards for one-click payments' mod='stripejs'}:</span></label>
        <div class="col-lg-8">
          <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_USEDCARD" id="STRIPE_USEDCARD_1" value="1" {if Configuration::get('STRIPE_ALLOW_USEDCARD')}checked="checked"{/if}>
              <label for="STRIPE_USEDCARD_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_USEDCARD" id="STRIPE_USEDCARD_0" value="0" {if !Configuration::get('STRIPE_ALLOW_USEDCARD')}checked="checked"{/if}>
              <label for="STRIPE_USEDCARD_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
            <ul style="padding: 0px;font-style:italic;">
            <li>{l s='Only Card payment option will be visible at Stripe Checkout if buyer select this option during checkout.' mod='stripejs'}</li></ul>
        </div></div>
        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product">{l s='Allow customer to delete saved Cards' mod='stripejs'}:</label>
        <div class="col-lg-8">
        <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_DELETE_CARDS" id="STRIPE_DELETECARD_1" value="1" {if Configuration::get('STRIPE_ALLOW_DELETE_CARDS')}checked="checked"{/if}>
              <label for="STRIPE_DELETECARD_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_DELETE_CARDS" id="STRIPE_DELETECARD_0" value="0" {if !Configuration::get('STRIPE_ALLOW_DELETE_CARDS')}checked="checked"{/if}>
              <label for="STRIPE_DELETECARD_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
        </div></div>
      <!--  <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product"><span title="{l s='This information improves the acceptance rates for cards issued in the United States, the United Kingdom and Canada.' mod='stripejs'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='Collect postal code (recommended*)' mod='stripejs'}:</span></label>
        <div class="col-lg-8">
        <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_ZIP" id="STRIPE_ALLOW_ZIP_1" value="1" {if Configuration::get('STRIPE_ALLOW_ZIP')}checked="checked"{/if}>
              <label for="STRIPE_ALLOW_ZIP_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_ZIP" id="STRIPE_ALLOW_ZIP_0" value="0" {if !Configuration::get('STRIPE_ALLOW_ZIP')}checked="checked"{/if}>
              <label for="STRIPE_ALLOW_ZIP_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
        </div></div>-->
        <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product"><span title="{l s='Stripe does hold on the card payment for the amount which is authorized during checkout. That authorization will be captured and the money settled to your account when you capture the payment from the order page.' mod='stripejs'}" class="label-tooltip" data-toggle="tooltip" title="">
                {l s='Enable separate authorization and capture' mod='stripejs'}:
            </span></label>
        <div class="col-lg-8">
         <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_CAPTURE_TYPE" id="STRIPE_CAPTURE_TYPE_0" value="0" {if !Configuration::get('STRIPE_CAPTURE_TYPE')}checked="checked"{/if}>
              <label for="STRIPE_CAPTURE_TYPE_0">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_CAPTURE_TYPE" id="STRIPE_CAPTURE_TYPE_1" value="1" {if Configuration::get('STRIPE_CAPTURE_TYPE')}checked="checked"{/if}>
              <label for="STRIPE_CAPTURE_TYPE_1">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
          </span>
          <ul style="padding: 0px;font-style:italic;">
          <li> {l s='Capture authorized payment within 7 days before it gets automatically refunded.' mod='stripejs'}</li>
          <li> {l s='This option is supported by card payments mainly.' mod='stripejs'}</li></ul>

        </div></div>
         <div class="form-group">
        <label class="control-label col-lg-4" for="simple_product"><span title="{l s='These payment options are offered by Stripe Checkout and Payment Element options already. Only use it if you want to offer wallet payments as a separate payment method in your checkout area.' mod='stripejs'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='Wallet Payments' mod='stripejs'}:</span></label>
        <div class="col-lg-8">
        <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_PRBUTTON" id="STRIPE_PRBUTTON_1" value="1" {if Configuration::get('STRIPE_ALLOW_PRBUTTON')}checked="checked"{/if}>
              <label for="STRIPE_PRBUTTON_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_PRBUTTON" id="STRIPE_PRBUTTON_0" value="0" {if !Configuration::get('STRIPE_ALLOW_PRBUTTON')}checked="checked"{/if}>
              <label for="STRIPE_PRBUTTON_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
            <ul style="padding: 0px;font-style:italic;">
            <li> {l s='Link, Google Pay, Apple Pay, PayPal, Amazon Pay options available.' mod='stripejs'}</li>
            <li> {l s='These are offered by Stripe Checkout and Payment Element form already.' mod='stripejs'}</li>
            <li> {l s='Use only if you want to offer wallet payments as a separate payment method in the checkout area.' mod='stripejs'}</li>
            <li> {l s='By using Apple Pay, you agree to' mod='stripejs'} <a href="https://stripe.com/us/legal" class="btc_link" target="_blank">Stripe</a> and <a href="https://www.apple.com/legal/internet-services/terms/site.html" class="btc_link" target="_blank">Apple's</a> {l s='terms of service.' mod='stripejs'}</li></ul>
        </div></div>

        </div>

        {if Configuration::get('STRIPE_ALLOW_CARDS')==3}
        <div class="alert alert-info">{l s='Use Stripe Checkout option to show these local payment methods separately in the PrestaShop checkout.' mod='stripejs'}</div>
        {/if}

        <div class="stripe-legend-div" {if Configuration::get('STRIPE_ALLOW_CARDS')==3} style="opacity: 0.5;pointer-events: none;"{/if}>
        <div class="stripe-legend">
        <div class="col-lg-3">{l s='Local payment methods' mod='stripejs'}</div><div class="col-lg-3">{l s='Enable' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Currencies' mod='stripejs'}</div><div class="col-lg-3">{l s='Customer locations' mod='stripejs'}</div>
        </div>

        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='iDEAL' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_IDEAL" id="STRIPE_IDEAL_1" value="1" {if Configuration::get('STRIPE_ALLOW_IDEAL')}checked="checked"{/if}>
              <label for="STRIPE_IDEAL_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_IDEAL" id="STRIPE_IDEAL_0" value="0" {if !Configuration::get('STRIPE_ALLOW_IDEAL')}checked="checked"{/if}>
              <label for="STRIPE_IDEAL_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
        </div>
        <div class="col-lg-3">{l s='EUR' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Netherlands' mod='stripejs'}</div>
        </div>
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='GIROPAY' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_GIROPAY" id="STRIPE_GIROPAY_1" value="1" {if Configuration::get('STRIPE_ALLOW_GIROPAY')}checked="checked"{/if}>
              <label for="STRIPE_GIROPAY_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_GIROPAY" id="STRIPE_GIROPAY_0" value="0" {if !Configuration::get('STRIPE_ALLOW_GIROPAY')}checked="checked"{/if}>
              <label for="STRIPE_GIROPAY_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
        </div>
        <div class="col-lg-3">{l s='EUR' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Germany' mod='stripejs'}</div>
        </div>
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='BANCONTACT' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
          <input type="radio" name="STRIPE_ALLOW_BANCONTACT" id="STRIPE_BANCONTACT_1" value="1" {if Configuration::get('STRIPE_ALLOW_BANCONTACT')}checked="checked"{/if}>
          <label for="STRIPE_BANCONTACT_1">{l s='Yes' mod='stripejs'}</label>
          <input type="radio" name="STRIPE_ALLOW_BANCONTACT" id="STRIPE_BANCONTACT_0" value="0" {if !Configuration::get('STRIPE_ALLOW_BANCONTACT')}checked="checked"{/if}>
          <label for="STRIPE_BANCONTACT_0">{l s='No' mod='stripejs'}</label>
          <a class="slide-button btn"></a>
        </span>
        </div>
        <div class="col-lg-3">{l s='EUR' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Belgium' mod='stripejs'}</div>
        </div>
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='EPS' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
          <input type="radio" name="STRIPE_ALLOW_EPS" id="STRIPE_EPS_1" value="1" {if Configuration::get('STRIPE_ALLOW_EPS')}checked="checked"{/if}>
          <label for="STRIPE_EPS_1">{l s='Yes' mod='stripejs'}</label>
          <input type="radio" name="STRIPE_ALLOW_EPS" id="STRIPE_EPS_0" value="0" {if !Configuration::get('STRIPE_ALLOW_EPS')}checked="checked"{/if}>
          <label for="STRIPE_EPS_0">{l s='No' mod='stripejs'}</label>
          <a class="slide-button btn"></a>
        </span>
        </div>
        <div class="col-lg-3">{l s='EUR' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Austria' mod='stripejs'}</div>
        </div>
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='FPX' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
          <input type="radio" name="STRIPE_ALLOW_FPX" id="STRIPE_FPX_1" value="1" {if Configuration::get('STRIPE_ALLOW_FPX')}checked="checked"{/if}>
          <label for="STRIPE_FPX_1">{l s='Yes' mod='stripejs'}</label>
          <input type="radio" name="STRIPE_ALLOW_FPX" id="STRIPE_FPX_0" value="0" {if !Configuration::get('STRIPE_ALLOW_FPX')}checked="checked"{/if}>
          <label for="STRIPE_FPX_0">{l s='No' mod='stripejs'}</label>
          <a class="slide-button btn"></a>
        </span>
        </div>
        <div class="col-lg-3">{l s='MYR' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Malaysia' mod='stripejs'}</div>
        </div>
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='MULTIBANCO' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
          <input type="radio" name="STRIPE_ALLOW_MULTIBANCO" id="STRIPE_MULTIBANCO_1" value="1" {if Configuration::get('STRIPE_ALLOW_MULTIBANCO')}checked="checked"{/if}>
          <label for="STRIPE_MULTIBANCO_1">{l s='Yes' mod='stripejs'}</label>
          <input type="radio" name="STRIPE_ALLOW_MULTIBANCO" id="STRIPE_MULTIBANCO_0" value="0" {if !Configuration::get('STRIPE_ALLOW_MULTIBANCO')}checked="checked"{/if}>
          <label for="STRIPE_MULTIBANCO_0">{l s='No' mod='stripejs'}</label>
          <a class="slide-button btn"></a>
        </span>
        </div>
        <div class="col-lg-3">{l s='EUR' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Portugal' mod='stripejs'}</div>
        </div>
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='Przelewy24 (P24)' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
          <input type="radio" name="STRIPE_ALLOW_P24" id="STRIPE_P24_1" value="1" {if Configuration::get('STRIPE_ALLOW_P24')}checked="checked"{/if}>
          <label for="STRIPE_P24_1">{l s='Yes' mod='stripejs'}</label>
          <input type="radio" name="STRIPE_ALLOW_P24" id="STRIPE_P24_0" value="0" {if !Configuration::get('STRIPE_ALLOW_P24')}checked="checked"{/if}>
          <label for="STRIPE_P24_0">{l s='No' mod='stripejs'}</label>
          <a class="slide-button btn"></a>
        </span>
        </div>
        <div class="col-lg-3">{l s='EUR or PLN' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Poland' mod='stripejs'}</div>
        </div>

        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='Alipay' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_ALIPAY" id="STRIPE_ALIPAY_1" value="1" {if Configuration::get('STRIPE_ALLOW_ALIPAY')}checked="checked"{/if}>
              <label for="STRIPE_ALIPAY_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_ALIPAY" id="STRIPE_ALIPAY_0" value="0" {if !Configuration::get('STRIPE_ALLOW_ALIPAY')}checked="checked"{/if}>
              <label for="STRIPE_ALIPAY_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
        </div>
        <div class="col-lg-3">{l s='CNY, AUD, CAD, EUR, GBP, HKD, JPY, SGD, MYR, NZD, USD' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Any country (depends on currency)' mod='stripejs'}</div>
        </div>
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='PAYNOW' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_PAYNOW" id="STRIPE_PAYNOW_1" value="1" {if Configuration::get('STRIPE_ALLOW_PAYNOW')}checked="checked"{/if}>
              <label for="STRIPE_PAYNOW_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_PAYNOW" id="STRIPE_PAYNOW_0" value="0" {if !Configuration::get('STRIPE_ALLOW_PAYNOW')}checked="checked"{/if}>
              <label for="STRIPE_PAYNOW_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
        </div>
        <div class="col-lg-3">{l s='SGD' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Singapore' mod='stripejs'}</div>
        </div><div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='GrabPay' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_GRABPAY" id="STRIPE_GRABPAY_1" value="1" {if Configuration::get('STRIPE_ALLOW_GRABPAY')}checked="checked"{/if}>
              <label for="STRIPE_GRABPAY_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_GRABPAY" id="STRIPE_GRABPAY_0" value="0" {if !Configuration::get('STRIPE_ALLOW_GRABPAY')}checked="checked"{/if}>
              <label for="STRIPE_GRABPAY_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
        </div>
        <div class="col-lg-3">{l s='SGD, MYR' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Singapore, Malaysia' mod='stripejs'}</div>
        </div>
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='OXXO' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_OXXO" id="STRIPE_OXXO_1" value="1" {if Configuration::get('STRIPE_ALLOW_OXXO')}checked="checked"{/if}>
              <label for="STRIPE_OXXO_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_OXXO" id="STRIPE_OXXO_0" value="0" {if !Configuration::get('STRIPE_ALLOW_OXXO')}checked="checked"{/if}>
              <label for="STRIPE_OXXO_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
        </div>
        <div class="col-lg-3">{l s='MXN' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Mexico' mod='stripejs'}</div>
        </div>
        <!--<div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='Konbini' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_KONBINI" id="STRIPE_KONBINI_1" value="1" {if Configuration::get('STRIPE_ALLOW_KONBINI')}checked="checked"{/if}>
              <label for="STRIPE_KONBINI_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_KONBINI" id="STRIPE_KONBINI_0" value="0" {if !Configuration::get('STRIPE_ALLOW_KONBINI')}checked="checked"{/if}>
              <label for="STRIPE_KONBINI_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
        </div>
        <div class="col-lg-3">{l s='JPY' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Japan' mod='stripejs'}</div>
        </div>
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='Boleto' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_BOLETO" id="STRIPE_BOLETO_1" value="1" {if Configuration::get('STRIPE_ALLOW_BOLETO')}checked="checked"{/if}>
              <label for="STRIPE_BOLETO_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_BOLETO" id="STRIPE_BOLETO_0" value="0" {if !Configuration::get('STRIPE_ALLOW_BOLETO')}checked="checked"{/if}>
              <label for="STRIPE_BOLETO_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
        </div>
        <div class="col-lg-3">{l s='BRL' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Brazil' mod='stripejs'}</div>
      </div>-->
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='WeChat Pay' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
          <input type="radio" name="STRIPE_ALLOW_WECHAT" id="STRIPE_WECHAT_1" value="1" {if Configuration::get('STRIPE_ALLOW_WECHAT')}checked="checked"{/if}>
          <label for="STRIPE_WECHAT_1">{l s='Yes' mod='stripejs'}</label>
          <input type="radio" name="STRIPE_ALLOW_WECHAT" id="STRIPE_WECHAT_0" value="0" {if !Configuration::get('STRIPE_ALLOW_WECHAT')}checked="checked"{/if}>
          <label for="STRIPE_WECHAT_0">{l s='No' mod='stripejs'}</label>
          <a class="slide-button btn"></a>
        </span>
        </div>
        <div class="col-lg-3">{l s='AUD, CAD, EUR, GBP, HKD, JPY, SGD, USD' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Asia continent' mod='stripejs'}</div>
        </div>
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='SOFORT' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
          <input type="radio" name="STRIPE_ALLOW_SOFORT" id="STRIPE_SOFORT_1" value="1" {if Configuration::get('STRIPE_ALLOW_SOFORT')}checked="checked"{/if}>
          <label for="STRIPE_SOFORT_1">{l s='Yes' mod='stripejs'}</label>
          <input type="radio" name="STRIPE_ALLOW_SOFORT" id="STRIPE_SOFORT_0" value="0" {if !Configuration::get('STRIPE_ALLOW_SOFORT')}checked="checked"{/if}>
          <label for="STRIPE_SOFORT_0">{l s='No' mod='stripejs'}</label>
          <a class="slide-button btn"></a>
        </span>
        </div>
        <div class="col-lg-3">{l s='EUR' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Europe continent' mod='stripejs'}</div>
        </div>
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='Klarna' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_KLARNA" id="STRIPE_KLARNA_1" value="1" {if Configuration::get('STRIPE_ALLOW_KLARNA')}checked="checked"{/if}>
              <label for="STRIPE_KLARNA_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_KLARNA" id="STRIPE_KLARNA_0" value="0" {if !Configuration::get('STRIPE_ALLOW_KLARNA')}checked="checked"{/if}>
              <label for="STRIPE_KLARNA_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
        </div>
        <div class="col-lg-3">{l s='AUD, CAD, CHF, CZK, DKK, NOK, EUR, GBP, NZD, PLN, SEK, USD' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Australia, Austria, Belgium, Canada, Czechia, Denmark, Finland, France, Greece, Germany, Ireland, Italy, Netherlands, New Zealand, Norway, Poland, Portugal, Spain, Sweden, Switzerland, UK,USA' mod='stripejs'}</div>
        </div>
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='Affirm' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_AFFIRM" id="STRIPE_AFFIRM_1" value="1" {if Configuration::get('STRIPE_ALLOW_AFFIRM')}checked="checked"{/if}>
              <label for="STRIPE_AFFIRM_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_AFFIRM" id="STRIPE_AFFIRM_0" value="0" {if !Configuration::get('STRIPE_ALLOW_AFFIRM')}checked="checked"{/if}>
              <label for="STRIPE_AFFIRM_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
        </div>
        <div class="col-lg-3">{l s='USD' mod='stripejs'} <b>{l s='(Min. $50)' mod='stripejs'}</b></div>
        <div class="col-lg-3">{l s='United States' mod='stripejs'}</div>
        </div>
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='Afterpay/ Clearpay' mod='stripejs'}</b></label>
        <div class="col-lg-3">
        <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_AFTERPAY_CLEARPAY" id="STRIPE_AFTERPAY_1" value="1" {if Configuration::get('STRIPE_ALLOW_AFTERPAY_CLEARPAY')}checked="checked"{/if}>
              <label for="STRIPE_AFTERPAY_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_AFTERPAY_CLEARPAY" id="STRIPE_AFTERPAY_0" value="0" {if !Configuration::get('STRIPE_ALLOW_AFTERPAY_CLEARPAY')}checked="checked"{/if}>
              <label for="STRIPE_AFTERPAY_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
        </div>
        <div class="col-lg-3">{l s='CAD, AUD, NZD, EUR, GBP, USD' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='United States, Canada, United Kingdom, Australia, New Zealand, France, Spain' mod='stripejs'}</div>
        </div>
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><b>{l s='SEPA Direct Debit' mod='stripejs'}</b></label>
        <div class="col-lg-3">
         <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="STRIPE_ALLOW_SEPA" id="STRIPE_SEPA_1" value="1" {if Configuration::get('STRIPE_ALLOW_SEPA')}checked="checked"{/if}>
              <label for="STRIPE_SEPA_1">{l s='Yes' mod='stripejs'}</label>
              <input type="radio" name="STRIPE_ALLOW_SEPA" id="STRIPE_SEPA_0" value="0" {if !Configuration::get('STRIPE_ALLOW_SEPA')}checked="checked"{/if}>
              <label for="STRIPE_SEPA_0">{l s='No' mod='stripejs'}</label>
              <a class="slide-button btn"></a>
            </span>
        </div>
        <div class="col-lg-3">{l s='EUR' mod='stripejs'}</div>
        <div class="col-lg-3">{l s='Europe continent' mod='stripejs'}</div>
        </div>
        <div class="form-group">
        <label class="control-label col-lg-3" for="simple_product"><span title="{l s='Need to display your statement descriptor to show the agreement for SEPA Direct Debit payments mandate' mod='stripejs'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='Statement Descriptor for SEPA' mod='stripejs'}</span></label>
        <div class="col-lg-7">
		<input type="text" name="STRIPE_STMNT_DESC" value="{Configuration::get('STRIPE_STMNT_DESC')|escape:'htmlall':'UTF-8'}" />{l s='It must be same as your statement descriptor in your ' mod='stripejs'}<a href="https://dashboard.stripe.com/account" class="btc_link" target="_blank">{l s='Stripe account' mod='stripejs'}</a>
        </div></div>

        <div class="alert alert-info">{l s='*To use any payment options listed above, first please activate them in your Stripe dhasboard' mod='stripejs'} <a href="https://dashboard.stripe.com/account" class="btc_link" target="_blank">{l s='here' mod='stripejs'}</a></div>
        </div>

        <div class="panel-footer">
                <button type="submit" name="SubmitStripe" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='stripejs'}</button>
            </div>
        </fieldset>
        </form>

           <form method="post" action="">
            <fieldset class="order_statuses">
            <h3 class="tab"><i class="icon-filter"></i>&nbsp;{l s='Order Statuses' mod='stripejs'}</h3>

                    {foreach $statuses_options as $status_options}

                        <div class="form-group">
                         <label class="control-label col-lg-6" for="simple_product">{$status_options['label']|escape:'htmlall':'UTF-8'}</label>
                            <div class="col-lg-5">
                                <select name="{$status_options['name']|escape:'htmlall':'UTF-8'}" style="width:auto">
                                    {foreach $statuses as $status}
                                        <option value="{$status['id_order_state']|escape:'htmlall':'UTF-8'}"{if $status['id_order_state'] == $status_options['current_value']} selected="selected"{/if}>{$status['name']|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                </select>
                            </div></div>
                    {/foreach}

          <div class="panel-footer">
                <button type="submit" name="SubmitOrderStatuses" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='stripejs'}</button>
            </div>
            </fieldset>
            </form>


        <form method="post" action="">
            <div class="clear"></div>
            <fieldset class="stripe_webhooks">
               <h3><i class="icon-money"></i>&nbsp;{l s='Card numbers for testing' mod='stripejs'}</h3>
                <table cellspacing="0" cellpadding="0" class="stripe-cc-numbers" width="100%">
                  <thead>
                    <tr>
                      <th>{l s='Number' mod='stripejs'}</th>
                      <th>{l s='Card type' mod='stripejs'}</th>
                    </tr>
                  </thead>
                  <tbody>
                  <tr><td class="number">Cards Payment</td><td></td></tr>
                    <tr><td class="number"><code>4242424242424242</code></td><td>Visa</td></tr>
                    <tr><td class="number"><code>4000000000003063</code></td><td>Visa (3D-Secure authentication required)</td></tr>
                    <tr><td class="number"><code>5555555555554444</code></td><td>MasterCard</td></tr>
                    <tr><td class="number"><code>378282246310005</code></td><td>American Express</td></tr>
                    <tr><td class="number"><code>4000002760003184</code></td><td>VISA (3D-Secure authentication required)</td></tr>
                    <tr><td class="number">SEPA Direct Debit</td><td>IBAN for testing</td></tr>
                    <tr><td class="number"><code>DE89370400440532013000</code></td><td>IBAN: The charge status transitions from pending to succeeded</td></tr>
                    <tr><td class="number"><code>DE62370400440532013001</code></td><td>IBAN: The charge status transitions from pending to failed</td></tr>
                  </tbody>
                </table>
                <br /> <br />
            <h3><i class="icon-info-sign"></i>&nbsp;{l s='Frequently Asked Questions' mod='stripejs'}</h3>

            <div class="faq items">
            <ul id="basics" class="faq-items">
              <li class="faq-item">
              <span class="faq-trigger">{l s='From when changes are happening for using Restricted API Keys (RAK)?' mod='stripejs'}</span>
              <span class="expand pull-right">+</span>
              <div class="faq-content">
                  <p>
                      {l s='On October 29, 2024, Stripe will begin requiring that all merchants using plugins upgrade the security of their connections by authenticating with either Restricted API keys or OAuth 2.0. This is a planned upgrade to enhance the overall security of Stripe third-party integrations and comply with Stripe\'s latest standards. Stripe will charge a fee starting June 2025 for businesses that do not comply with this security requirement.' mod='stripejs'} <a href="https://support.stripe.com/questions/plugin-user-migration-guide" target="_blank">{l s='Read more' mod='stripejs'}</i></a>
                  </p>
              </div>
              </li>
              <li class="faq-item">
              <span class="faq-trigger">{l s='Why use Restricted API keys (RAK)?' mod='stripejs'}</span>
              <span class="expand pull-right">+</span>
              <div class="faq-content">
                  <p>
                      {l s='For better security and control, it is essential to use Restricted API keys (RAK) or OAuth 2.0 instead of secret keys when authenticating with third-party services. Restricted API keys improve security by offering businesses precise control over the permissions they are granting to third-parties, ensuring third-party services access only the necessary data and actions.' mod='stripejs'} <a href="https://support.stripe.com/questions/plugin-user-migration-guide" target="_blank">{l s='Read more' mod='stripejs'}</i></a>
                  </p>
              </div>
              </li>
              <li class="faq-item">
              <span class="faq-trigger">{l s='Why we need to install your "NTS Connect" app in our Stripe dashboard?' mod='stripejs'}</span>
              <span class="expand pull-right">+</span>
              <div class="faq-content">
                <ul>
                <li>
                      {l s='All new and existing plugin developers must build OAuth 2.0 or Restricted API key authorization via a Stripe App. All Stripe Apps are reviewed for accurate permissions before being published in the Stripe App Marketplace. Following the install steps and installing the app created by the third party ensures access only to necessary parts of your Stripe data.' mod='stripejs'}</li>
                  <li>    {l s='Restricted API key will be generated automatically when you complete then installation of our NTS Connect app.' mod='stripejs'}
                       <a href="https://support.stripe.com/questions/plugin-user-migration-guide" target="_blank">{l s='Read more' mod='stripejs'}</i></a>
                      </li>
                  <li>    <b>{l s='Authentication with Restricted API key (RAK)' mod='stripejs'}</b></li>
                  </ul>
                  <img class="add_domain" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/RAK_auth.png" style="width:100%;">
              </div>
              </li>
              <li class="faq-item">
              <span class="faq-trigger">{l s='Does card payments support the new SCA requirement and 3DS v2?' mod='stripejs'}</span>
              <span class="expand pull-right">+</span>
              <div class="faq-content">
                  <p>
                      {l s='Yes, from the version 4.1.5 of this module, all card payments are compatible with 3DS v2 and SCA ready.' mod='stripejs'}
                  </p>
              </div>
              </li>
              <li class="faq-item">
              <span class="faq-trigger">{l s='What is the difference between Stripe Payment Element and Stripe checkout form?' mod='stripejs'}</span>
              <span class="expand pull-right">+</span>
              <div class="faq-content">
                 <b>{l s='Stripe Payment Element' mod='stripejs'}</b>
                  <ul>
                  <li>{l s='This is an embedded payment form directly generated by Stripe APIs that offers Cards, Wallets, Bank Transfers, Local payment options with a single integration.' mod='stripejs'}</li>
                  <li>{l s='Accept payments via all the activated payment methods in your Stripe Dashboard under one checkout option.' mod='stripejs'}</li>
                  <li>{l s='Customers will be redirectred outside only for local Bank redirect payment methods to make payment.' mod='stripejs'}</li>
                  <li>{l s='Click for more details' mod='stripejs'} <a href="https://stripe.com/docs/payments/payment-element" target="_blank">{l s='here' mod='stripejs'}</a>.</li>
                  </ul>
                  <b>{l s='Stripe hosted Checkout' mod='stripejs'}</b>
                   <ul>
                   <li>{l s='Offer all the activated payment methods in your Stripe Dashboard under one checkout option.' mod='stripejs'}</li>
                   <li>{l s='Apple pay and Google Pay are offered by default.' mod='stripejs'}</li>
                   <li>{l s='Customers will be redirected on the Stripe hosted Checkout page to make payment.' mod='stripejs'}</li>
                   <li>{l s='Click to see the demo' mod='stripejs'} <a href="https://checkout.stripe.dev/preview" target="_blank">{l s='here' mod='stripejs'}</a>.</li>
                   </ul>
              </div>
              </li>
              <li class="faq-item">
              <span class="faq-trigger">{l s='Which payment methods in this module do support new Payment Intent API?' mod='stripejs'}</span>
              <span class="expand pull-right">+</span>
              <div class="faq-content">
                  <ul><li>{l s='All the payment options use the new Payment Intent API.' mod='stripejs'}</li>
                  </ul>
              </div>
              </li>
              <li class="faq-item">
                  <span class="faq-trigger">{l s='Is Stripe Radar supported?' mod='stripejs'}</span>
                  <span class="expand pull-right">+</span>
                  <div class="faq-content">
                      <p>
                          {l s='Yes, if available for your Stripe account, you can use Stripe Radar with this module.' mod='stripejs'}
                      </p>
                  </div>
              </li>
              <li class="faq-item">
                  <span class="faq-trigger">{l s='How can I manually add my domain name in the Stripe dashboard to use ApplePay' mod='stripejs'}</span>
                  <span class="expand pull-right">+</span>
                  <div class="faq-content">
                      <p>
                          {l s='You can manually add your domain(s) throught your Dashboard. You can easily do this by following steps:' mod='stripejs'} <br>
                          <ul>
                          <li> {l s='Go to the Settings > Payment Methods and Click on Configure button in Apple Pay tab.' mod='stripejs'}</li>
                          <li> {l s='Click on the button "Add new domain" and follow the procedure given in the image below.' mod='stripejs'}</li>
                          <li> {l s='Domain name should be the same. Please check for the WWW in your domain.' mod='stripejs'}</li>
                          </ul>
                          <img class="add_domain" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/applepayconfig.png" style="width:100%;">
                      </p>
                  </div>
              </li>
              <li class="faq-item">
                <span class="faq-trigger">{l s='Why are webhooks required?' mod='stripejs'}</span>
                <span class="expand pull-right">+</span>
                <div class="faq-content">
                  <ul>
                  <li>{l s='Webhook is required to process orders for redirect based payment options like Local bank redirects, Stripe Checkout etc..' mod='stripejs'}</li>
                 <li>{l s='Some payment methods are asynchronous and webhooks are the only way to keep your orders up to date automatically.' mod='stripejs'}</li>
                 <li>{l s='Domain name should be the same. Please check for the WWW in your domain.' mod='stripejs'}</li>
              </ul>
                </div>
              </li>
              <li class="faq-item">
                <span class="faq-trigger">{l s='How can I add Stripe webhooks?' mod='stripejs'}</span>
                <span class="expand pull-right">+</span>
                <div class="faq-content">
                  <ul>
                    <li>{l s='From 4.7.1 or newer, you need to add a webhook manually by using the link given on the "Technical Checks" tab.' mod='stripejs'}</li>
                  <li>{l s='From 4.2.5 to 4.6.9 versions of this module, Webhook endpoint will be added automatically when you save the api keys in the "Stripe Configuration" tab.' mod='stripejs'}</li>
                 <li>{l s='We strongly recommend you to confirm below webhook endpoint details in your Stripe dashboard to avoid any issue.
                 All webhook endpoint information is given below in case you want to confirm it on' mod='stripejs'} <a href="https://dashboard.stripe.com/webhooks" target="_blank" class="link">{l s='Stripe Dashboard' mod='stripejs'} <i class="icon-external-link"></i></a></li>

                 <li><b>{l s='Webhook endpoint:' mod='stripejs'}</b>&nbsp;<u>{$webhook_url|escape:'htmlall':'UTF-8'}</u></li>
                 <li><b>{l s='Webhook events:' mod='stripejs'}</b>
                   {$webhook_events nofilter}</li>
                   </ul>
                  <img src="{$this_path|escape:'htmlall':'UTF-8'}views/img/webhook.png" style="width: 100%;">
                  </div>
              </li>
              <li class="faq-item">
                <span class="faq-trigger">{l s='Which API version should I use in my Stripe?' mod='stripejs'}</span>
                <span class="expand pull-right">+</span>
                <div class="faq-content">
                {l s='Stripe API VERSION should be' mod='stripejs'}&nbsp;<strong>2019-08-14</strong>&nbsp;{l s='or newer' mod='stripejs'}. &nbsp;
            <a href="https://dashboard.stripe.com/developers" target="_blank" class="link">{l s='Check Stripe Dashboard' mod='stripejs'} <i class="icon-external-link"></i></a>
                </div>
              </li>
              <li class="faq-item">
                  <span class="faq-trigger">{l s='I enabled Apple Pay / Google Pay, But still not working?' mod='stripejs'}</span>
                  <span class="expand pull-right">+</span>
                  <div class="faq-content">
                      <ul>
                          <li>{l s='Make sure that your host supports TLS 1.2.' mod='stripejs'}</li>
                          <li>{l s='For Apple Pay, you also need to get your domain verified by Apple.' mod='stripejs'} <a href="https://stripe.com/docs/apple-pay/web/v2#going-live" target="blank">{l s='(see Stripe Dashboard)' mod='stripejs'}</a></li>
                          <li>{l s='Please check if you have a payment card saved in your supported device/browser. And make sure that to enable the service for supported browser as well.' mod='stripejs'}</li>
                          <li>{l s='For Apple Pay test, please set module in LIVE mode because in TEST mode you need to setup a SANDBOX TESTER ACCOUNT for your device which is a long process.' mod='stripejs'}</li>
                          <li>{l s='If still not working, please contact us:' mod='stripejs'} <a href="https://addons.prestashop.com/en/contact-us?id_product=17856" target="blank">{l s='here' mod='stripejs'}</a></li>
                      </ul>
                  </div>
              </li>
              <li class="faq-item">
                  <span class="faq-trigger">{l s='How can we do FULL or Partial REFUND?' mod='stripejs'}</span>
                  <span class="expand pull-right">+</span>
                  <div class="faq-content">
                      <p>
                          {l s='You can do the refund from the order page in this Prestashop backoffice. You need to enter the full or partial payment amount and press the refund button. In case there is any issue then you can do it from your Stripe dashboard as well. Order status will be automatically changed during this process.' mod='stripejs'}
                      </p>
                  </div>
              </li>
              <li class="faq-item">
                  <span class="faq-trigger">{l s='How can I CAPTURE payment when using separate authorization and capture?' mod='stripejs'}</span>
                  <span class="expand pull-right">+</span>
                  <div class="faq-content">
                      <p>
                          {l s='From Prestashop backoffice, You can capture the payment from the order page. You need to choose either a full or partial payment amount and press the capture button. In case there is any issue then you can do it from your Stripe dashboard as well.' mod='stripejs'}
                          <br><span style="color:#f0a213;">{l s='Warning: within 7 calendar days you need to capture the authorized payment. Otherwise payment will be automatically refunded to the same customer account.' mod='stripejs'}</span>
                      </p>
                  </div>
              </li>
               <li class="faq-item">
                  <span class="faq-trigger">{l s='Is there any minimum charge amount in Stripe?' mod='stripejs'}</span>
                  <span class="expand pull-right">+</span>
                  <div class="faq-content">
                      <p>
                          {l s='Yes there is a minimum charge amount for each currency. For most of the currencies, the minimum charge amount is 50 cents. To check minimum charge amounts for your currency please' mod='stripejs'} <a href="https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts" target="blank">{l s='click here' mod='stripejs'}</a>
                      </p>
                  </div>
              </li>
              <li class="faq-item">
                  <span class="faq-trigger">{l s='Can you add a new feature in this module?' mod='stripejs'}</span>
                  <span class="expand pull-right">+</span>
                  <div class="faq-content">
                      <p>
                          {l s='Yes we offer all kinds of customization for the module. For any new feature or a suggestion, feel free to reach out to us:' mod='stripejs'} <a href="https://addons.prestashop.com/en/contact-us?id_product=17856" target="blank">{l s='here' mod='stripejs'}</a>
                      </p>
                  </div>
              </li>
             </ul>
            </div>
            </fieldset>
        </form>
        </div></div>
