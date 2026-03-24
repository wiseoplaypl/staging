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

<div class="payment_module stripe-payment-17">
{if !Configuration::get('STRIPE_MODES')}
<div class="alert alert-info">Please use below test card info in test mode:<br>
<strong>4242 4242 4242 4242</strong> (for success payment)<br>
<strong>4000 0000 0000 3063</strong> (3D Secure authentication) <br>
You can use any expiry date and CVC code for card.</div>
{/if}
<div id="payment-success" class="success alert alert-success" style="display:none">{l s='Payment successful! Creating your order now...' mod='stripejs'}</div>
<div id="checkout-success" class="success alert alert-success" style="display:none">{l s='Payment token created, redirecting to Stripe Checkout...' mod='stripejs'}</div>
     <div id="stripe-ajax-loader"><div class="spinner-border"></div> {l s='Do not press BACK or REFRESH while processing...' mod='stripejs'}</div>

      <form action="" method="post" id="stripe-payment-form">
      <input type="hidden" name="selected_pm" id="selected_pm" value="1" />

         {if isset($stripeTokens) && is_array($stripeTokens) && count($stripeTokens)>0 && Configuration::get('STRIPE_ALLOW_USEDCARD')}
         <div class="saved_cards">
             <label class="card_line active" for="new_card_line">
           <span>
            <input type="radio" name="stripe_pm" id="new_card_line" value="1" checked="checked" />
          </span>&nbsp;{l s='Pay with a new payment method' mod='stripejs'}
            </label>
            {foreach $stripeTokens as $key => $card}
            <label class="card_line" for="{$key|escape:'htmlall':'UTF-8'}_card_line">
             <span>
              <input type="radio" name="stripe_pm" id="{$key|escape:'htmlall':'UTF-8'}_card_line" value="{$card['source']|escape:'htmlall':'UTF-8'}" />
              </span>&nbsp;{l s='Pay with card' mod='stripejs'}&nbsp;<img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc-{if $card['cc_type']=='American Express'}amex{elseif $card['cc_type']=='Diners Club'}diners{elseif $card['cc_type']=='Mastercard (prepaid)'}mastercard{elseif $card['cc_type']=='Mastercard (debit)'}mastercard{elseif $card['cc_type']=='Visa (debit)'}visa{else}{$card['cc_type']|lower|escape:'htmlall':'UTF-8'}{/if}.png" alt="" />&nbsp;<b>•••• •••• •••• {$card['cc_last_digits']|escape:'htmlall':'UTF-8'}</b> &nbsp; &nbsp;{$card['cc_exp']|escape:'htmlall':'UTF-8'}
              </label>
              {/foreach}
            </div>
        {/if}

        <div id="card-errors" role="alert"></div>
        {if !is_array($stripeTokens) || count($stripeTokens)==0 || !Configuration::get('STRIPE_ALLOW_USEDCARD')}
        <p><img alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/output.png"/> {l s='After submission, you will be redirected to securely complete next steps.' mod='stripejs'}</p>
        {/if}
        <div class="form-row">
{if Configuration::get('STRIPE_ALLOW_CARDS')==1}

        {if Configuration::get('STRIPE_ALLOW_NAME_CARDS')}
        <label>{l s='Name' mod='stripejs'}</label>
          <input id="cardholder-name" type="text" value="{$cu_fname|escape:'htmlall':'UTF-8'} {$cu_lname|escape:'htmlall':'UTF-8'}" />
        {else}
        <input type="hidden" name="cardholder-name" id="cardholder-name" value="{$cu_fname|escape:'htmlall':'UTF-8'} {$cu_lname|escape:'htmlall':'UTF-8'}" />
        {/if}

        <label> {l s='Card details' mod='stripejs'}</label>
         <div id="card-element"></div>

          {if Configuration::get('STRIPE_ALLOW_USEDCARD')}
          <p class="checkbox">
          <input id="reuse_authorize" name="reuse_authorize" type="checkbox" value="1" /> <label for="reuse_authorize">&nbsp;{l s='Save card for later use' mod='stripejs'} </label>
          </p>
          {/if}
          <div class="pull-right">
        <img class="verified_stripe" alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/verified_visa.png"/>
        <img class="verified_stripe" alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/mastercard_secure.png"/>
        <img class="verified_stripe" alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/powered_by_stripe.png"/>
        </div>
 {else}

          {if Configuration::get('STRIPE_ALLOW_USEDCARD')}
          <p class="checkbox">
          <input id="reuse_authorize" name="reuse_authorize" type="checkbox" value="1" /> <label for="reuse_authorize">&nbsp; {l s='Yes I want to reuse my card for future purchases' mod='stripejs'} </label>
          </p>
          {/if}
         <img class="verified_stripe" alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/stripe-cc.png"/>
         <img class="verified_stripe" alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/apple.png"/>
         <img class="verified_stripe" alt="" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/google.png"/>
 {/if}
 </div>
      </form>
    </div>
