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

{extends file='page.tpl'}
{block name='page_title'}
  {l s='My Cards' mod='stripejs'} ({count($cards)|escape:'htmlall':'UTF-8'})
{/block}

{block name='page_content'}
     {if isset($confirmation) && $confirmation}
		<p class="alert alert-success">{l s='Card has been deleted successfully.' mod='stripejs'}</p>
	{/if}
     {if count($cards)==0}
		<p>{l s='You did not saved any card yet.' mod='stripejs'}</p>
	{else}

		{foreach from=$cards item=card name=cards}

             <form action="" method="post" class="col-md-12" style="box-shadow: 2px 2px 11px 0 rgba(0,0,0,.1);background: #fff;padding: 10px;margin-bottom: 5px;">
              <input type="hidden" name="stripe_source" value="{$card.source|escape:'htmlall':'UTF-8'}" />
               <input type="hidden" name="stripe_cus" value="{$card.stripe_cus_id|escape:'htmlall':'UTF-8'}" />
                <table cellpadding="10" cellspacing="10" style="width: 500px;">

                  <tr style="border-top: 1px dashed #999;">
                  <td><img src="{$baseDir|escape:'htmlall':'UTF-8'}/modules/stripejs/views/img/cc-{if $card['cc_type']=='American Express'}amex{elseif $card['cc_type']=='Diners Club'}diners{elseif $card['cc_type']=='Mastercard (prepaid)'}mastercard{elseif $card['cc_type']=='Mastercard (debit)'}mastercard{elseif $card['cc_type']=='Visa (debit)'}visa{else}{$card['cc_type']|lower|escape:'htmlall':'UTF-8'}{/if}.png" alt="" />&nbsp;<b>•••• •••• •••• {$card['cc_last_digits']|escape:'htmlall':'UTF-8'}</b> &nbsp; &nbsp;{l s='Expired on:' mod='stripejs'} &nbsp;&nbsp;<b>{$card['cc_exp']|escape:'htmlall':'UTF-8'}</b></td>
                  <td><button type="submit" class="button btn btn-primary" style="padding:2px 5px;{if $item['deleted']==1}'visibility: hidden'{/if}" onclick="return confirm('{l s='Are you sure you want to remove this card?' mod='stripejs'}');" name="SubmitCancelCard" value="{$card.id_stripe_transaction|escape:'htmlall':'UTF-8'}">{l s='(X) Delete' mod='stripejs'}</button></td>
                  </tr>
                  </table>
                  </form>
          {/foreach}
          {/if}

{/block}

{block name='page_footer'}
    {block name='my_account_links'}
        {include file='customer/_partials/my-account-links.tpl'}
    {/block}
{/block}
