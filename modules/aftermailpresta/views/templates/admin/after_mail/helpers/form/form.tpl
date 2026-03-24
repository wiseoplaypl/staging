{*
* @author    Shopmonauten <prestashop@shopmonauten.com>
* @copyright Shopmonauten <www.shopmonauten.com>
* @license   go to addons.prestashop.com (buy one module for one shop).
* @for PrestaShop version 1.6X
* @site www.shopmonauten.de
* @email prestashop@shopmonauten.com
*}
{extends file="helpers/form/form.tpl"}
{block name="input"}
{if $input.type == "select_attachment"}
	{if isset($input.lang) AND $input.lang}
		{if $languages|count > 1}
			<div class="form-group">
		{/if}
		{foreach $languages as $language}
		{* assign var='value_text' value=$fields_value[$input.name][$language.id_lang] *}
		{if $languages|count > 1}
			<div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang|intval != $defaultFormLanguage|intval}style="display:none"{/if}>
				<div class="col-lg-9">
		{/if}	
													
		<select name="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|intval}"
				id="{$input.name|escape:'htmlall':'UTF-8'}_select_{$language.id_lang|intval}"
				{if isset($input.multiple)}multiple="multiple" {/if}
				{if isset($input.size)}size="{$input.size|intval}"{/if}
				{if isset($input.onchange)}onchange="{$input.onchange|escape:'htmlall':'UTF-8'}"{/if}>
				{foreach $input.options.query AS $option}	
							
					{if $option == "-"}
						<option value="">--</option>
					{else}
						<option value="{$option["id_attachment"]|escape:'htmlall':'UTF-8'}"
							{if isset($input.multiple)}
								{foreach $fields_value[$input.name] as $field_value}
									{if $field_value == $option["id_attachment"]}selected="selected"{/if}
								{/foreach}
							{else}
								{if isset($fields_value[$input.name][$language.id_lang|intval]) && ($fields_value[$input.name][$language.id_lang|intval] == $option["id_attachment"])}selected="selected"{/if}
							{/if}
					>{$option[$input.options.name]|escape:'htmlall':'UTF-8'}</option>
					{/if}					
				{/foreach}
		</select>	
					
		{if $languages|count > 1}
			</div>
			<div class="col-lg-2">
				<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
					{$language.iso_code|escape:'htmlall':'UTF-8'}
					<i class="icon-caret-down"></i>
				</button>
					<ul class="dropdown-menu">
						{foreach from=$languages item=language}
						<li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'htmlall':'UTF-8'}</a></li>
						{/foreach}
					</ul>
			</div>
		</div>
		{/if}
		{/foreach}									
		{if $languages|count > 1}
		</div>
		{/if}
	{/if}
{else}
	{$smarty.block.parent}
{/if}
{/block}
	
{block name="after"}    
	{if isset($filterConfiguration)}
		{$filterConfiguration}
	{/if}
{/block}
{block name="script"}
{if $fields_value['trigger_type'] == 0}
	$('#id_orderstate').hide();	
{else}
	$('#id_orderstate').show();	
{/if}
{if $fields_value['voucher'] == 0}
	$('#vouchertype').hide();				
	$('#voucheramount').hide();			
	$('#voucherdays').hide();			
	$('#vouchername').hide();
	$('#restrict_on').hide();
	$('#restrict_off').hide();	
	$('#vouchertype').parent().prev().hide();
	$('#voucheramount').parent().prev().hide();
	$('#voucherdays').parent().prev().hide();
	$('#vouchername').parent().prev().hide();	
	$('#restrict_off').parent().parent().parent().hide();
	$('#restrict_off').parent().parent().parent().prev().hide();
	
	
{else}
	$('#vouchertype').show();		
	$('#voucheramount').show();
	$('#voucherdays').show();	
	$('#vouchername').show();
	$('#restrict_on').show();
	$('#restrict_off').show();	
	$('#vouchertype').parent().prev().show();
	$('#voucheramount').parent().prev().show();
	$('#voucherdays').parent().prev().show();
	$('#vouchername').parent().prev().show();	
	$('#restrict_off').parent().parent().parent().show();
	$('#restrict_off').parent().parent().parent().prev().show();
	
{/if}
{/block}
</script>