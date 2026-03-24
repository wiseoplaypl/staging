{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}


<div class="form-group ndkackFieldItem field-type-{$field.type}" data-admin-name="{$field.admin_name|lower|escape:'htmlall'}" data-iteration="{$field_iteration}"  data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-name="{$field.name|escape:'htmlall'}" data-field="{$field.id_ndk_customization_field|escape:'htmlall'}" data-quantity-link="{$field.quantity_link}" {foreach from=$field.options key=k item=v} data-{$k}="{$v}"{/foreach}>
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
			<div class="clearfix" id="main-{$field.id_ndk_customization_field|intval}">
				{if $field.notice !=''}
					<div class="field_notice clearfix clear">{$field.notice nofilter}</div>
				{/if}
				<div class="clear col-xs-12 clearfix visu-tools"></div>
				<input data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall'}" id="ndkcsfield_{$field.id_ndk_customization_field|intval}" type="hidden" name="ndkcsfield[{$field.id_ndk_customization_field|intval}]" value="" class="{if $field.required == 1} required_field{/if}"/>
				
				<input type="hidden" id="ndkcsfieldPdf_{$field.id_ndk_customization_field|intval}" name="ndkcsfieldPdf[{$field.id_ndk_customization_field|intval}]"/>
				
				<ul class="ndk_color_list">
					{include file='module:ndk_advanced_custom_fields/views/templates/hook/values/type_color.tpl'}

				</ul>
				{if $field.orienteable == 1}
					{include file='module:ndk_advanced_custom_fields/views/templates/hook/orienteable.tpl'}
				{/if}
			</div>
			{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/specific_prices.tpl'}
		</div>
	</div>