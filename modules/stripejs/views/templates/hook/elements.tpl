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

<div class="stripe-payment-17" style="display:none">
{if !Configuration::get('STRIPE_MODES')}
<div class="alert alert-info">Please use below test card info in test mode:<br>
<strong>4242 4242 4242 4242</strong> (for success payment)<br>
<strong>4000 0000 0000 3063</strong> (3D Secure authentication) <br>
You can use any expiry date and CVC code for card.</div>
{/if}

<div id="payment-success" class="success alert alert-success" style="display:none">{l s='Payment successful! Creating your order now...' mod='stripejs'}</div>
     <div id="stripe-ajax-loader"><div class="spinner-border"></div> {l s='Do not press BACK or REFRESH while processing...' mod='stripejs'}</div>
     <div id="stripe-api-loader"><div class="spinner-border"></div> {l s='please wait while loading...' mod='stripejs'}</div>
     <div id="error-message" class="alert alert-danger" style="display:none"></div>
      <form action="" method="post" id="payment-element-form">
        <div id="payment-element"></div>
        {if Configuration::get('STRIPE_ALLOW_USEDCARD')}
        <!--
        <div class="clearfix"></div>
        <div class="checkbox reuse_authorize" style="display: block;padding: 16px 0;">
        <input style="float: left;" id="reuse_authorize" name="reuse_authorize" type="checkbox" value="1" /> <label for="reuse_authorize">&nbsp; {l s='Yes I want to reuse my card for future purchases' mod='stripejs'} </label>
      </div>-->
        {/if}
      </form>
    </div>
