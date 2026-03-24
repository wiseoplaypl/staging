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

<div class="row">
<div class="col-xs-12">
<div class="payment_module stripe-payment-17 sepa_payment_module">
      <form action="" method="POST" id="stripe-sepa-form">
      <input type="hidden" name="sepa_client_secret" id="sepa_client_secret" value="{$intent.client_secret|escape:'htmlall':'UTF-8'}" />
        <div class="form-row">
          <div id="sepa-errors" role="alert"></div>
          <label for="name">{l s='Name' mod='stripejs'}</label>
          <input id="sepa_name" name="sepa_name" type="text" placeholder="Jenny Rosen" value="{$cu_fname|escape:'htmlall':'UTF-8'} {$cu_lname|escape:'htmlall':'UTF-8'}" required>

          <label for="iban-element">{l s='IBAN' mod='stripejs'}</label>
          <div id="iban-element"></div>
          <div id="bank-name"></div>
          <div id="mandate-acceptance">
             {l s='By providing your IBAN and confirming this payment, you are authorizing' mod='stripejs'} <b>{Configuration::get('STRIPE_STMNT_DESC')|escape:'htmlall':'UTF-8'}</b> {l s='and Stripe, our payment service provider, to send instructions to your bank to debit your account and your bank to debit your account in accordance with those instructions. You are entitled to a refund from your bank under the terms and conditions of your agreement with your bank. A refund must be claimed within 8 weeks starting from the date on which your account was debited.' mod='stripejs'}
          </div>
          </div>
	</form>
    <div id="sepa-payment-success" class="success alert alert-success" style="display:none">{l s='Payment successful! Creating your order now...' mod='stripejs'}</div>
     <div id="stripe-ajax-loader-sepa" style="display:none"><div class="spinner-border"></div> {l s='Do not press BACK or REFRESH while processing...' mod='stripejs'}</div>
	<div class="stripe-translations" style="display:none">
		<span id="stripe-incorrect_ownername">{l s='The account holder name is empty.' mod='stripejs'}</span>
        <span id="stripe-incorrect_number_iban">{l s='IBAN number is incorrect.' mod='stripejs'}</span>
        <span id="stripe-mandate">{l s='You must accept the SEPA Direct Debit mandate.' mod='stripejs'}</span>
		<span id="stripe-currency_error">{l s='SEPA Direct Debit payments only support Euros as a currency.' mod='stripejs'}</span>
        <span id="stripe-please-fix">{l s='Please fix it and submit your payment again.' mod='stripejs'}</span>
	</div>
</div>
</div>
</div>
