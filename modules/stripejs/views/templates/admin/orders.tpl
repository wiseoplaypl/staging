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


<div id="stripe_order_details" style="display:none;">

{if $is_ps_177}
<div class="card-header">{l s='Stripe Payment Details' mod='stripejs'}</div>
<div class="card-body" style="padding: 5px 10px; background:#ffffff; font-size:13px;"><fieldset>
{else}
  <div class="panel panel-highlighted" style="padding: 5px 10px;"><fieldset><legend>&nbsp;{l s='Stripe Payment Details' mod='stripejs'}</legend>
{/if}

<div style="display:flex;">
<div style="padding-right:25px;">
{if !empty($trans['source_type'])}
  {if $trans['status'] == 'pending' && $trans['source_type']=='multibanco'}
    {l s='Stripe Source ID:' mod='stripejs'} <b>{$trans['source']|escape:'htmlall':'UTF-8'}</b><br />
  {elseif $trans['id_transaction']!=''}
    {l s='Stripe Transaction ID:' mod='stripejs'} <b><a target="_blank" title="{l s='Open Stripe Transaction' mod='stripejs'}" href="https://dashboard.stripe.com/payments/{$trans['id_payment_intent']|escape:'htmlall':'UTF-8'}">{$trans['id_transaction']|escape:'htmlall':'UTF-8'}</a></b><br />
  {elseif $trans['id_payment_intent']!=''}
    {l s='Stripe Payment Intent:' mod='stripejs'} <b><a target="_blank" href="https://dashboard.stripe.com/payments/{$trans['id_payment_intent']|escape:'htmlall':'UTF-8'}">{$trans['id_payment_intent']|escape:'htmlall':'UTF-8'}</a></b><br />
  {/if}

  {l s='Payment Source:' mod='stripepro'} <b> {if $trans['source_type'] == 'alipay'}Alipay{elseif $trans['source_type'] == 'prbutton'}Apple Pay/ Google Pay/ Microsoft Pay{elseif $trans['source_type'] == 'three_d_secure'}3D-Secure authentication{elseif $trans['source_type'] == 'card'}Credit Card{elseif $trans['source_type'] == 'customer'} {l s='Quick Pay with saved Card' mod='stripepro'}{else}{Tools::strtoupper($trans['source_type'])|escape:'htmlall':'UTF-8'}{/if}</b>
</div>
{if !empty($trans['risk_score'])}
<div style="padding-left:25px;border-left:1px solid #ccc;">
{l s='Risk evaluation' mod='stripejs'}:
<b><span style="border-radius: 10px;padding: 5px;color: #fff;background-color:{if $trans['risk_score']<=5}#54b754{elseif $trans['risk_score']<=65}orange{elseif $trans['risk_score']<=75}red{else}brown{/if}"> {$trans['risk_score']|escape:'htmlall':'UTF-8'}</span> {$trans['risk_level']|escape:'htmlall':'UTF-8'}</b>

</div>
{/if}
</div>
<br />

  {if $trans['status'] == 'pending' && $trans['source_type']=='multibanco'}
  {l s='Multibanco Entity:' mod='stripejs'}<b>{$trans['cc_exp']|escape:'htmlall':'UTF-8'}</b><br />{l s='Multibanco Reference:' mod='stripejs'}<b>{$trans['cc_type']|escape:'htmlall':'UTF-8'}</b><br /><br />
  {/if}

  {l s='Status:' mod='stripejs'}<span style="font-weight: bold; color: {if $trans['status'] == 'paid'}green{else}#CC0000{/if};">{Tools::strtoupper($trans['status'])|escape:'htmlall':'UTF-8'}</span><br />
  {l s='Amount:' mod='stripejs'} <b>{$currency|escape:'htmlall':'UTF-8'}{$trans['amount']|escape:'htmlall':'UTF-8'}</b><br />
  {l s='Processed on:' mod='stripejs'} <b>{$trans['date_add']|escape:'htmlall':'UTF-8'}</b><br />

  {if $trans['source_type']=='card' || $trans['source_type']=='prbutton'}

 {l s='Credit card:' mod='stripejs'} <b>{Tools::strtoupper($trans['cc_type'])|escape:'htmlall':'UTF-8'} <br /></b>{l s='Expired On:' mod='stripejs'}<b>{$trans['cc_exp']|escape:'htmlall':'UTF-8'}</b><br />{l s='Last 4 digits:' mod='stripejs'} <b>xxxx xxxx xxxx {sprintf('%04d', $trans['cc_last_digits'])|escape:'htmlall':'UTF-8'} </b><br />
{l s='CVC Check:' mod='stripejs'} <b>{if $trans['cvc_check']}<span style="color: #35c135;">{l s='OK' mod='stripejs'}</span>{else} <span style="color: orange;">{l s='Unchecked / Failed' mod='stripejs'}</span>{/if}</b><br />
{l s='Street Check:' mod='stripejs'} <b>{if $trans['line1_check']} <span style="color: #35c135;">{l s='OK' mod='stripejs'}</span>{else}<span style="color: orange;">{l s='Unchecked / Failed' mod='stripejs'}</span>{/if}</b><br />
{l s='Zipcode Check:' mod='stripejs'} <b>{if $trans['zip_check']} <span style="color: #35c135;">{l s='OK' mod='stripejs'}</span>{else}<span style="color: orange;">{l s='Unchecked / Failed' mod='stripejs'}</span>{/if}</b><br />
{l s='3D Secure:' mod='stripejs'} <b>{if $trans['three_d_secure']} <span style="color: #35c135;">{l s='Verified' mod='stripejs'}</span>{else}<span style="color: orange;">{l s='Not challenged' mod='stripejs'}</span>{/if}</b><br />

  {elseif $trans['source_type']=='sepa_debit'}
	{l s='Fingerprint:' mod='stripejs'} <b>{$trans['cc_type']|escape:'htmlall':'UTF-8'}</b><br />{l s='IBAN Last 4 digits:' mod='stripejs'} <b>xxxxxxxxxxxx{$trans['cc_last_digits']|escape:'htmlall':'UTF-8'}</b><br />

  {elseif $trans['source_type']=='customer'}
	{l s='Stripe customer ID:' mod='stripejs'} <b>{$trans['source']|escape:'htmlall':'UTF-8'}</b><br />{l s='Credit card:' mod='stripejs'} <b>{$trans['cc_type']|escape:'htmlall':'UTF-8'}{l s='Exp.:' mod='stripejs'} {$trans['cc_exp']|escape:'htmlall':'UTF-8'}</b><br />{l s='Last 4 digits:' mod='stripejs'} <b>xxxx xxxx xxxx {sprintf('%04d', $trans['cc_last_digits'])|escape:'htmlall':'UTF-8'} </b><br />
   {/if}

{if $trans['fee']>0}
  {l s='Processing Fee:' mod='stripejs'} <b>{if $isZeroDecimalCurrency} {round($trans['fee'])|escape:'htmlall':'UTF-8'}{else} {$currency|escape:'htmlall':'UTF-8'}{$trans['fee']/100|escape:'htmlall':'UTF-8'}{/if}</b>
  {/if}

<br />
{l s='Payment mode:' mod='stripejs'} <span style="font-weight: bold; color: {if $trans['mode'] == 'live'}green{else}#CC0000{/if}">{$trans['mode']|upper|escape:'htmlall':'UTF-8'}</span>
{/if}

  {if empty($trans['source_type'])}
  <b style="color: #CC0000;">{l s='Warning:' mod='stripejs'}</b> {l s='The customer paid using Stripe and an error occured while saving the transaction.' mod='stripejs'}
  {/if}

   </fieldset><br />

   {if Tools::getIsset('stripeCaptured')}
   <div  class="bootstrap">{if Tools::getValue('stripeCaptured')==1} <div class="conf confirmation alert alert-success">{l s='Your capture was successfully processed' mod='stripejs'}</div>
{else if Tools::getValue('stripeCaptured')!=1}<div style="color: #CC0000; font-weight: bold;" class="alert alert-danger">{l s='Error:' mod='stripejs'}{Tools::getValue('stripeCaptured')|escape:'htmlall':'UTF-8'}</div>{/if}</div>
   {/if}
   {if Tools::getIsset('stripeCancelAuthorization') && Tools::getValue('stripeCancelAuthorization')==1}
   <div class="bootstrap"> <div class="conf confirmation alert alert-success">{l s='Your authorized payment has been cancel successfully.' mod='stripejs'}</div></div>
   {/if}

{if $trans['status'] == 'uncaptured'}

    <fieldset><h3><u>&nbsp;{l s='Proceed to a full or partial capture via Stripe' mod='stripejs'}</u></h3>
    {if $diff>0}
    <form action="" method="post">{l s='Capture:' mod='stripejs'} {$currency|escape:'htmlall':'UTF-8'} <input type="text" value="{$trans['amount']|escape:'htmlall':'UTF-8'}" name="stripe_amount_to_capture" style="display: inline-block; width: 60px;" /> <input type="hidden" name="id_payment_intent" value="{$trans['id_payment_intent']|escape:'htmlall':'UTF-8'}" /><input type="submit" class="button btn btn-primary" onclick="return confirm('{l  s='Do you want to proceed to this capture?' mod='stripejs'}');" name="SubmitStripeCapture" value="{l s='Process Capture' mod='stripejs'}" />&nbsp;&nbsp;<input type="submit" class="button btn" onclick="return confirm('{l  s='Do you want to cancel this authorization?' mod='stripejs'}');" name="SubmitStripeCancelAuth" value="{l s='Cancel Authorization' mod='stripejs'}" /></form><font style="color:red;font-size:13px;"> <br>{l s='NOTE: Time left to Capture payment:' mod='stripejs'} <b>{$timeleft|escape:'htmlall':'UTF-8'}</b> {l s='otherwise payment will be automatically refunded.' mod='stripejs'}</font>
    {else}
    <font style="color:red;"> <b>{l s='7 days has been passed so the payment has been refunded.' mod='stripejs'}</b></font>
    {/if}
    </fieldset>

{else if $trans['status'] == 'paid'}

   <fieldset class="bootstrap {if empty($trans['id_transaction'])||$trans['status'] == 'unpaid'} hidden{/if}"><legend>&nbsp;{l s='Proceed to a full or partial refund via Stripe' mod='stripejs'}</legend>
    {if Tools::getIsset('stripeRefunded')}
     {if Tools::getValue('stripeRefunded')==1} <div class="conf confirmation alert alert-success">{l s='Your refund was successfully processed' mod='stripejs'}</div>
     {else if Tools::getValue('stripeRefunded')!=1} <div style="color: #CC0000; font-weight: bold;" class="alert alert-danger">{l s='Error:' mod='stripejs'} {Tools::getValue('stripeRefunded')|escape:'htmlall':'UTF-8'}</div>{/if}
    {/if}

    {l s='Already refunded:' mod='stripejs'} <b>{$currency|escape:'htmlall':'UTF-8'}{$stripe_refunded|escape:'htmlall':'UTF-8'}</b><br /><br />{if $stripe_refunded}<table class="table" cellpadding="0" cellspacing="0" style="font-size: 12px;"><tr><th>{l s='Date' mod='stripejs'}</th><th>{l s='Amount refunded' mod='stripejs'}</th><th>{l s='Status' mod='stripejs'}</th></tr>
    {foreach $stripe_refund_details as $stripe_refund_detail}
    <tr><td>{$stripe_refund_detail['date_add']|escape:'htmlall':'UTF-8'}</td><td>{$currency|escape:'htmlall':'UTF-8'}{$stripe_refund_detail['amount']|escape:'htmlall':'UTF-8'}</td><td>{if $stripe_refund_detail['status'] == 'paid'}{l s='Processed' mod='stripejs'}{else}{l s='Error' mod='stripejs'}{/if}</td></tr>
    {/foreach}
    </table><br /></fieldset>
{/if}
{if $trans['amount'] > $stripe_refunded &&  $trans['status'] == 'paid'}
<form action="" method="post">
<button type="submit" class="button btn btn-default" onclick="return confirm('{l s='Do you want to proceed to this refund?' mod='stripejs'}');" name="SubmitStripeRefund" {if $stripe_refunded>0} disabled="disabled" {/if}><i class="icon icon-undo"></i> {l s='Refund Order' mod='stripejs'}</button> &nbsp;<b>{l s='OR' mod='stripejs'}</b>&nbsp;
{$c_char|escape:'htmlall':'UTF-8'}<input type="text" value="{if $isZeroDecimalCurrency} {round($trans['amount']-$stripe_refunded)|escape:'htmlall':'UTF-8'}{else}{($trans['amount']-$stripe_refunded)|escape:'htmlall':'UTF-8'}{/if}" name="stripe_amount_to_refund" style="display: inline-block; width: 60px;" /> <input type="hidden" name="id_transaction" value="{$trans['id_transaction']|escape:'htmlall':'UTF-8'}" /><button type="submit" class="button btn btn-default" onclick="return confirm('{l s='Do you want to proceed to this refund?' mod='stripejs'}');" name="SubmitStripePartialRefund"><i class="icon icon-undo"></i> {l s='Partial Refund' mod='stripejs'}</button></form><br />
{/if}
{/if}
</div>
</div>
<script type="text/javascript">
var appendEl;
$(document).ready(function() {
	  if ($("select[name=id_order_state]").is(":visible")) {
		  appendEl = $("select[name=id_order_state]").parents("form").after($("<div/>"));
	  } else if ($("#orderProductsOriginalPosition").length){
		  appendEl = $("#orderProductsOriginalPosition");
	  } else {
		  appendEl = $("#status");
		  }
	  $('#stripe_order_details').show();
	  $('#stripe_order_details').appendTo(appendEl);
});
</script>
