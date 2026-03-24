{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}


<div class="form-group ndkackFieldItem field-type-{$field.type}" data-iteration="{$field_iteration}"  data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-name="{$field.name|escape:'htmlall'}" data-field="{$field.id_ndk_customization_field|escape:'htmlall'}" data-quantity-link="{$field.quantity_link}" {foreach from=$field.options key=k item=v} data-{$k}="{$v}"{/foreach}>
		{capture name='placeholder'}{foreach from=$field.values item=value}{$value.value}{/foreach}{/capture}
		<label class="toggler {if !$field.is_picto}toggler-default-picto{/if}"
		{if $field.is_picto} style="background-image: url('{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/pictos/{$field.id_ndk_customization_field|intval}.jpg');"{/if}
	>{$field.name|escape:'htmlall'} 
	{if $field.show_price == 1}
		{if $fieldPricePerCaracter > 0}{l s='cost : ' mod='ndk_advanced_custom_fields'}<span class="ndkcf-value-price">{convertPrice price=$fieldPricePerCaracter}</span> {l s='per caracter' mod='ndk_advanced_custom_fields'}
		{elseif $fieldPrice > 0}{l s='cost : ' mod='ndk_advanced_custom_fields'}<span class="ndkcf-value-price">{convertPrice price=$fieldPrice}</span>{/if}
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
		{if $field.notice !=''}
			<div class="field_notice clearfix clear">{$field.notice nofilter}</div>
		{/if}
		
		{if $field.configurator == 1}
			<textarea id="ndkcsfield_{$field.id_ndk_customization_field|intval}" data-lines="{$field.nb_lines|escape:'htmlall'}" data-max="{$field.maxlength}"  data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall'}"  name="ndkcsfield[{$field.id_ndk_customization_field|intval}]" class="{if $field.is_visual == 1}visual-effect {/if} form-control {if $field.configurator == 1} textzone{/if} ndktextarea type_textarea {if $field.required == 1} required_field{/if}"  data-group="{$field.id_ndk_customization_field|intval}" data-zindex="{$field.zindex|escape:'htmlall'}" data-price="{$fieldPrice|escape:'htmlall'}" data-ppcprice="{$fieldPricePerCaracter|escape:'htmlall'}"  data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-dragdrop="{$field.draggable|intval}" 
			data-resizeable="{$field.resizeable|intval}" 
			data-rotateable="{$field.rotateable|intval}"  data-blend="{$field.color_effect}" {if $field.is_mask_image}data-mask-image="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/mask/{$field.id_ndk_customization_field|intval}.jpg"{/if}></textarea><br>
		{else}
		<div class="clearfix clear bordered">
			<textarea id="ndkcsfield_{$field.id_ndk_customization_field|intval}" {if $field.maxlength > 0}maxlength="{$field.maxlength}"{/if} data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall'}" type="text" name="ndkcsfield[{$field.id_ndk_customization_field|intval}]" class="{if $field.required == 1} required_field{/if} form-control simpleText {if $field.is_visual == 1}visual-text{/if}"  data-group="{$field.id_ndk_customization_field|intval}" data-zindex="{$field.zindex|escape:'htmlall'}" data-price="{$fieldPrice|escape:'htmlall'}" data-ppcprice="{$fieldPricePerCaracter|escape:'htmlall'}" placeholder="{$smarty.capture.placeholder}" data-view="{$field.target_child|escape:'htmlall'}" data-dragdrop="{$field.draggable|intval}" 
			data-resizeable="{$field.resizeable|intval}" 
			data-rotateable="{$field.rotateable|intval}"  data-blend="{$field.color_effect}" {if $field.is_mask_image}data-mask-image="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/mask/{$field.id_ndk_customization_field|intval}.jpg"{/if}></textarea>
			{if $field.is_visual == 1}<span class="button button-small submitSimpleText">{l s='Apply' mod='ndk_advanced_custom_fields'}</span>{/if}
		</div>
		{/if}
		{if $field.orienteable == 1}
			{include file='module:ndk_advanced_custom_fields/views/templates/hook/orienteable.tpl'}
		{/if}
	</div>
	
	
</div>

<input type="hidden" id="ndkcsfieldPdf_{$field.id_ndk_customization_field|intval}" name="ndkcsfieldPdf[{$field.id_ndk_customization_field|intval}]"/>


<script type="text/javascript">
	fieldColors_{$field.id_ndk_customization_field}= [];
	fieldSizes_{$field.id_ndk_customization_field} = [];
	fieldFonts_{$field.id_ndk_customization_field} = [];
	fieldEffects_{$field.id_ndk_customization_field} = [];
	fieldAlignments_{$field.id_ndk_customization_field} = [];
	{if $field.colors !=''}
		{foreach from=$field.colors  item=color}
			window['fieldColors_{$field.id_ndk_customization_field}'].push('{$color}');
		{/foreach}
	{/if}
	{if $field.sizes !=''}
		{foreach from=$field.sizes  item=size}
			window['fieldSizes_{$field.id_ndk_customization_field}'].push('{$size}');
		{/foreach}
	{/if}
	{if $field.fonts !=''}
		{foreach from=$field.fonts  item=font}
			window['fieldFonts_{$field.id_ndk_customization_field}'].push('{$font}');
		{/foreach}
	{/if}
	{if $field.effects !=''}
		{foreach from=$field.effects  item=effect}
			window['fieldEffects_{$field.id_ndk_customization_field}'].push('{$effect}');
		{/foreach}
	{/if}
	{if $field.alignments !=''}
		{foreach from=$field.alignments  item=alignment}
			window['fieldAlignments_{$field.id_ndk_customization_field}'].push('{$alignment}');
		{/foreach}
	{/if}
</script>