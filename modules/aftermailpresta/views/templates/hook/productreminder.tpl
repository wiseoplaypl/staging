{*
* AfterMail version 1.9.0
*
* @author    Shopmonauten <prestashop@shopmonauten.com>
* @copyright Shopmonauten <www.shopmonauten.com>
* @license   go to addons.prestashop.com (buy one module for one shop).
* @for PrestaShop version 1.6X
* @site www.shopmonauten.de
* @email prestashop@shopmonauten.com
*}

<div>
	{if $subscribed}
		<input type="hidden" value="{$unsub_token|escape:'htmlall':'UTF-8'}" id="unsub_token">
		<p>{l s='You\'ve already subscribed to receive reminders about this product: ' mod='aftermailpresta'}<b>{l s='every %s' sprintf=$formattedOption mod='aftermailpresta'}</b>.</p>
		<button id="unsubscribebutton" class="btn btn-default">{l s='Unsubscribe' mod='aftermailpresta'}</button>	
	{else}
		<p class="clearfix list-inline" style="display: flex; flex-flow:row;">
			<select id="subscribe_product" name="subscribe_product" class="form-control attribute_select">
				{html_options values=$frequencies output=$frequenciesFormatted}
			</select>
			<button id="subscribebutton" class="btn btn-default" style="padding:0 12px;margin-left:4px;">{l s='Subscribe' mod='aftermailpresta'}</button>
		</p>
		<p>{l s='Subscribe to receive reminders about this product in a given interval.' mod='aftermailpresta'}</p>	
		<style>{literal}#uniform-subscribe_product { flex:1; } #uniform-subscribe_product span { width: unset !important; }{/literal}</style>
	{/if}
	<input type="hidden" value="{$id_conf|escape:'htmlall':'UTF-8'}" id="id_conf">
</div>
<br>