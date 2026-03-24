{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}

<div class="form-group ndkackFieldItem field-type-{$field.type}" data-iteration="{$field_iteration}"  data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-name="{$field.name|escape:'htmlall'}" data-field="{$field.id_ndk_customization_field|escape:'htmlall'}" data-qtty-min="{$field.quantity_min}"  data-qtty-max="{$field.quantity_max}" data-quantity-link="{$field.quantity_link}" {foreach from=$field.options key=k item=v} data-{$k}="{$v}"{/foreach}>
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
		<div class="minmaxBlock">
			<p class="quantity_error_up  alert-danger clear clearfix">{l s="You can't add more than " mod='ndk_advanced_custom_fields'}<span>{$field.quantity_max}</span> {l s='quantities' mod='ndk_advanced_custom_fields'}</p>
			<p data-name="ndkcsfield[{$field.id_ndk_customization_field|intval}]" class=" alert-danger clear clearfix quantity_error_down  {if $field.quantity_min > 0}required_field{/if}" val="">{l s="You must add a minimum of " mod='ndk_advanced_custom_fields'}<span>{$field.quantity_min}</span> {l s='quantities' mod='ndk_advanced_custom_fields'}</p>
		</div>
		{if isset($field.feature) && $field.feature > 0}
			{if isset($features) && $features}
						{foreach from=$features item=feature}
							{if $feature.id_feature == $field.feature && isset($feature.value)}
							<span class="checkbox">
								<input data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall'}" id="checkbox_{$field.id_ndk_customization_field|escape:'htmlall'}_0" type="checkbox" name="ndkcsfield[{$field.id_ndk_customization_field|intval}][checkbox][{$value.value}]" class="{if $field.required == 1} required_field{/if} ndk-checkbox not_uniform{if $field.is_visual == 1}visual-effect {/if}" data-group="{$field.id_ndk_customization_field|intval}" data-src="" data-zindex="{$field.zindex|escape:'htmlall'}" value="{$feature.value|escape:'htmlall'}" data-price="0" checked="checked" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-quantity-available="{if $value.set_quantity >0}{$value.quantity}{else}999999999{/if}" data-value-id="{$value.id|intval}" data-hide-field="{if $value.influences_restrictions|strpos:"all" !== false}1{else}0{/if}" data-id-value="{$field.id_ndk_customization_field|intval}-{$value.id|intval}"
								/>
								<label for="checkbox_{$field.id_ndk_customization_field|escape:'htmlall'}_0">{$feature.value|escape:'htmlall'}({l s='by default'  mod='ndk_advanced_custom_fields'})</label>
								</span>
							{/if}
							
						{/foreach}
			{/if}
		
		{/if}
			{include file='module:ndk_advanced_custom_fields/views/templates/hook/values/type_checkbox.tpl'}

			{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/specific_prices.tpl'}
			
		</div>	
		<input type="hidden" id="ndkcsfieldPdf_{$field.id_ndk_customization_field|intval}" name="ndkcsfieldPdf[{$field.id_ndk_customization_field|intval}]"/>
		
	</div>