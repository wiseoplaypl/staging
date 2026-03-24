{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}

<div class="form-group ndkackFieldItem field-type-{$field.type}" data-iteration="{$field_iteration}"  data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-name="{$field.name|escape:'htmlall'}" data-field="{$field.id_ndk_customization_field|escape:'htmlall'}" {foreach from=$field.options key=k item=v} data-{$k}="{$v}"{/foreach}>
	<label class="toggler {if !$field.is_picto}toggler-default-picto{/if}"
		{if $field.is_picto} style="background-image: url('{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/pictos/{$field.id_ndk_customization_field|intval}.jpg');"{/if}
	><span id="resultValue_{$field.id_ndk_customization_field|intval}"></span>{$field.name|escape:'htmlall'}
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
		
		{if $field.price_range_min_width > 0}
			<p data-name="ndkcsfield[{$field.id_ndk_customization_field|intval}]" class=" alert-danger clear clearfix quantity_error_width_down  {if $field.price_range_min_width > 0}required_field{/if}" val="">{l s="You must add a minimum of " mod='ndk_advanced_custom_fields'}{$field.price_range_min_width} ({if $field.values.0.value != ''}{$field.values.0.value}{else}{l s='width' mod='ndk_advanced_custom_fields'}{/if})</p>
		{/if}
		{if $field.price_range_max_width > 0}
			<p data-name="ndkcsfield[{$field.id_ndk_customization_field|intval}]" class=" alert-danger clear clearfix quantity_error_width_up  {if $field.price_range_max_width > 0}required_field{/if}" val="">{l s="You must add a maximum of " mod='ndk_advanced_custom_fields'}{$field.price_range_max_width} ({if $field.values.0.value != ''}{$field.values.0.value}{else}{l s='width' mod='ndk_advanced_custom_fields'}{/if})</p>
		{/if}
		{if $field.price_range_min_height > 0}
			<p data-name="ndkcsfield[{$field.id_ndk_customization_field|intval}]" class=" alert-danger clear clearfix quantity_error_height_down  {if $field.price_range_min_height > 0}required_field{/if}" val="">{l s="You must add a minimum of " mod='ndk_advanced_custom_fields'}{$field.price_range_min_height} ({if $field.values.1.value != ''}{$field.values.1.value}{else}{l s='height' mod='ndk_advanced_custom_fields'}{/if})</p>
		{/if}
		{if $field.price_range_max_height > 0}
			<p data-name="ndkcsfield[{$field.id_ndk_customization_field|intval}]" class=" alert-danger clear clearfix quantity_error_height_up  {if $field.price_range_max_height > 0}required_field{/if}" val="">{l s="You must add a maximum of " mod='ndk_advanced_custom_fields'}{$field.price_range_max_height} ({if $field.values.0.value != ''}{$field.values.1.value}{else}{l s='height' mod='ndk_advanced_custom_fields'}{/if})</p>
		{/if}
		
	<p class="dimensions_block">
		{if $field.values.0.value != ''}
			<label class="clear clearfix">{$field.values.0.value}</label>
		{else}
			<label class="clear clearfix">{l s='width' mod='ndk_advanced_custom_fields'}</label>
		{/if}
		<input id="dimension_text_width_{$field.id_ndk_customization_field|intval}" data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall'}"  placeholder="{l s='width' mod='ndk_advanced_custom_fields'}" name="ndkcsfield[{$field.id_ndk_customization_field|intval}][width]" data-val="" data-group="{$field.id_ndk_customization_field|intval}" data-price="" type="number" step="any" class="form-control dimension_text dimension_text_width dimension_text_{$field.id_ndk_customization_field|intval} {if $field.required == 1} required_field{/if}" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" value="{$field.price_range_min_width}" min="{$field.price_range_min_width}" max="{$field.price_range_max_width}" size="8"/>
		{if $field.price_range_min_height < $field.price_range_max_height}
		{if $field.values.1.value != ''}
			<label class="clear clearfix">{$field.values.1.value}</label>
		{else}
			<label class="clear clearfix">{l s='height' mod='ndk_advanced_custom_fields'}</label>
		{/if}
		{/if}
		<input {if $field.price_range_min_height >= $field.price_range_max_height}style="display:none"{/if} id="dimension_text_height_{$field.id_ndk_customization_field|intval}" data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall'}"  placeholder="{l s='height' mod='ndk_advanced_custom_fields'}" name="ndkcsfield[{$field.id_ndk_customization_field|intval}][height]" data-val="" data-group="{$field.id_ndk_customization_field|intval}" data-price="" type="number"  step="any" class="form-control dimension_text dimension_text_height dimension_text_{$field.id_ndk_customization_field|intval} {if $field.required == 1} required_field{/if}" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" value="{$field.price_range_min_height}" min="{$field.price_range_min_height}" max="{$field.price_range_max_height}" size="8"/>
	</p>
	</div>
</div>