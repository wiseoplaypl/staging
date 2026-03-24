{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}


<div class="form-group ndkackFieldItem field-type-{$field.type}" data-iteration="{$field_iteration}"  data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}"  data-name="{$field.name|escape:'htmlall'}" data-field="{$field.id_ndk_customization_field|escape:'htmlall'}" {foreach from=$field.options key=k item=v} data-{$k}="{$v}"{/foreach}>
		<label class="toggler {if !$field.is_picto}toggler-default-picto{/if}"
		{if $field.is_picto} style="background-image: url('{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/pictos/{$field.id_ndk_customization_field|intval}.jpg');"{/if}
	>{$field.name|escape:'htmlall'}
		{if $field.show_price == 1}
			{if $fieldPrice > 0}{l s='cost : ' mod='ndk_advanced_custom_fields'}{convertPrice price=$fieldPrice}{/if}
		{/if}
		{if $field.is_visual == 1}
			<span class="layer_view visible_layer" data-group="{$field.id_ndk_customization_field|intval}" data-zindex="{$field.zindex|escape:'htmlall'}" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}"/>&nbsp;</span>
		{/if}
		{if $field.tooltip !=''}
			<div class="tooltipDescription">{$field.tooltip nofilter}</div>
				<span class="tooltipDescMark"></span>
		{/if}
		</label>
		<div class="fieldPane clearfix">
			<div class="clearfix" id="main-{$field.id_ndk_customization_field|intval}">
				{if $field.notice !=''}
					<div class="field_notice clearfix clear">{$field.notice nofilter}</div>
				{/if}
				<input data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall'}" id="ndkcsfield_{$field.id_ndk_customization_field|intval}" type="text" name="ndkcsfield[{$field.id_ndk_customization_field|intval}]" value="" class="{if $field.required == 1} required_field{/if} ndk-colorpicker" data-hex="true"/>
				
				<input type="hidden" id="ndkcsfieldPdf_{$field.id_ndk_customization_field|intval}" name="ndkcsfieldPdf[{$field.id_ndk_customization_field|intval}]"/>
				
				
				
				<li class="color-ndk {if $field.is_visual == 1}visual-effect {/if} hidden" data-value=" " title=" "  
					data-src="0" data-group="{$field.id_ndk_customization_field|intval}"  data-zindex="{$field.zindex|escape:'htmlall'}" 
					data-dragdrop="{$field.draggable|intval}" 
					data-resizeable="{$field.resizeable|intval}" 
					data-rotateable="{$field.rotateable|intval}" 
					data-id-value="0" data-default-value="0"
					data-color="#FFFFFF" 
					data-id="" data-view="{$field.target_child|escape:'htmlall'}" data-blend="{$field.color_effect}" 
					{if $field.is_mask_image}data-mask-image="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/mask/{$field.id_ndk_customization_field|intval}.jpg"{/if}>
					<span style="background:">&nbsp;</span>
					</li>
				{if $field.orienteable == 1}
					{include file='module:ndk_advanced_custom_fields/views/templates/hook/orienteable.tpl'}
				{/if}
			</div>
			{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/specific_prices.tpl'}
		</div>
	</div>