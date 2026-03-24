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
<div class="payment_module stripe-payment-17 ideal_payment_module">
      <form action="" method="POST" id="stripe-ideal-form">
      <div id="ideal-errors" role="alert"></div>
        <div class="form-row">
          <label for="accountholder-name">
            {l s='Name' mod='stripejs'}
          </label>
          <input type="text" id="accountholder-name" name="accountholder-name" value="{$cu_fname|escape:'htmlall':'UTF-8'} {$cu_lname|escape:'htmlall':'UTF-8'}">
        </div>
        <div class="form-row">
          <label for="ideal-bank-element">{l s='iDeal Bank' mod='stripejs'}</label>
          <div id="ideal-bank-element"></div>
          </div>
	</form>
</div>
</div>
</div>
