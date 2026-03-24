{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}

<div class="form-group ndkackFieldItem field-type-{$field.type}" data-iteration="{$field_iteration}"  data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-name="{$field.name|escape:'htmlall'}" data-name="{$field.name|lower|escape:'htmlall'}" data-admin-name="{$field.admin_name|lower|escape:'htmlall'}" data-field="{$field.id_ndk_customization_field|escape:'htmlall'}" data-quantity-link="{$field.quantity_link}" {foreach from=$field.options key=k item=v} data-{$k}="{$v}"{/foreach}>
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
			<div class="clear clearfix" id="main-{$field.id_ndk_customization_field|intval}">
				{if $field.notice !=''}
					<div class="field_notice clearfix clear">{$field.notice nofilter}</div>
				{/if}
				<select data-group="{$field.id_ndk_customization_field|intval}" data-message="{l s='Select' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall'}"  name="ndkcsfield[{$field.id_ndk_customization_field|escape:'htmlall'}]"  id="ndkcsfield_{$field.id_ndk_customization_field|escape:'htmlall'}" class="form-control-ndk {if $field.is_visual == 1}visual-effect-select {/if} ndk-select {if $field.required == 1} required_field{/if}">
					{if isset($field.feature) && $field.feature > 0}
						{if isset($features) && $features}
									{foreach from=$features item=feature}
										{if $feature.id_feature == $field.feature && isset($feature.value)}
										<option selected="selected" value="{$feature.value|escape:'htmlall'}"  data-group="{$field.id_ndk_customization_field|intval}" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-quantity-available="{if $value.set_quantity >0}{$value.quantity}{else}999999999{/if}"
										>{$feature.value|escape:'htmlall'}</option>
										{/if}
										
									{/foreach}
						{/if}
					{else}
						<option value=""  data-group="{$field.id_ndk_customization_field|intval}">{l s='select an option' mod='ndk_advanced_custom_fields'}</option>
					{/if}
					{include file='module:ndk_advanced_custom_fields/views/templates/hook/values/type_select.tpl'}
				</select>
				
				<input type="hidden" id="ndkcsfieldPdf_{$field.id_ndk_customization_field|intval}" name="ndkcsfieldPdf[{$field.id_ndk_customization_field|intval}]"/>
				
				{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/specific_prices.tpl'}
			</div>
		</div>
		
	</div>