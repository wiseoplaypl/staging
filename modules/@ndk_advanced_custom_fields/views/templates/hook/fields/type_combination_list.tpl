{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}


<div class="form-group ndkackFieldItem field-type-{$field.type}" data-iteration="{$field_iteration}" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}"  data-name="{$field.name|escape:'htmlall'}" data-field="{$field.id_ndk_customization_field|escape:'htmlall'}" data-qtty-min="{$field.quantity_min}"  data-qtty-max="{$field.quantity_max}" {foreach from=$field.options key=k item=v} data-{$k}="{$v}"{/foreach}>
		<label class="toggler {if !$field.is_picto}toggler-default-picto{/if}"
		{if $field.is_picto} style="background-image: url('{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/pictos/{$field.id_ndk_customization_field|intval}.jpg');"{/if}
	>{$field.name|escape:'htmlall'}
		{if $field.is_visual == 1}
			<span class="layer_view visible_layer" data-group="{$field.id_ndk_customization_field|intval}" data-zindex="{$field.zindex|escape:'htmlall'}" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}"/>&nbsp;</span>
		{/if}
		{if $field.tooltip !=''}
			<div class="tooltipDescription">{$field.tooltip nofilter}</div>
				<span class="tooltipDescMark"></span>
		{/if}
		</label>
		<div class="fieldPane clearfix">
			{if $field.notice !=''}
				<div class="field_notice clearfix clear">{$field.notice nofilter}</div>
			{/if}
			<!--<input data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall'}" id="ndkcsfield_{$field.id_ndk_customization_field|intval}" type="text" name="ndkcsfield[{$field.id_ndk_customization_field|intval}]" value="" class="{if $field.required == 1} required_field{/if}"/>-->
			
			<div class="minmaxBlock">
				{if $field.weight_max > 0 || $field.weight_min > 0}
				<div class="total_weight_container total_row">
					{l s='Total weight' mod='ndk_advanced_custom_fields'} <span class="total_weight">0</span> {Configuration::get('PS_WEIGHT_UNIT')}
				</div>
				{/if}
				{if $field.quantity_max > 0 || $field.quantity_min > 0}
				<div class="total_weight_container total_row">
					{l s='Total quantity' mod='ndk_advanced_custom_fields'} <span class="total_quantity">0</span>
				</div>
				{/if}
			{if $field.quantity_max > 0}
				<p class="quantity_error_up alert-danger clear clearfix">{l s="You can't add more than " mod='ndk_advanced_custom_fields'}<span>{$field.quantity_max}</span> {l s='quantities' mod='ndk_advanced_custom_fields'}</p>
			{/if}
			{if $field.weight_max > 0}
				<p class="weight_error_up alert-danger clear clearfix">{l s="You can't add more than " mod='ndk_advanced_custom_fields'}<span>{$field.weight_max}</span> {Configuration::get('PS_WEIGHT_UNIT')}</p>
			{/if}
			{if $field.quantity_min > 0}
				<p data-name="ndkcsfield[{$field.id_ndk_customization_field|intval}]" class=" alert-danger clear clearfix quantity_error_down  {if $field.quantity_min > 0}required_field{/if}" val="">{l s="You must add a minimum of " mod='ndk_advanced_custom_fields'}<span>{$field.quantity_min}</span> {l s='quantities' mod='ndk_advanced_custom_fields'}</p>
			{/if}
			{if $field.weight_min > 0}
				<p data-name="ndkcsfield[{$field.id_ndk_customization_field|intval}]" class=" alert-danger clear clearfix weight_error_down  {if $field.weight_min > 0}required_field{/if}" val="">{l s="You must add a minimum of " mod='ndk_advanced_custom_fields'}<span>{$field.weight_min}</span> {Configuration::get('PS_WEIGHT_UNIT')}</p>
			{/if}
			</div>
			<!-- bloc tags -->
			<div class="clearfix clear row" id="main-{$field.id_ndk_customization_field|intval}">
				<div class="clear col-xs-12 clearfix visu-tools"></div>
			</div>
			<!-- bloc tags -->
			<ul class="ndk_accessory_list">
						{include file='module:ndk_advanced_custom_fields/views/templates/hook/values/type_combination_list.tpl'}
			</ul>
			{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/specific_prices.tpl'}
		</div>
	</div>
	
	
