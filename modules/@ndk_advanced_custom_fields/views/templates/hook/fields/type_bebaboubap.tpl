{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}

<div class="form-group ndkackFieldItem field-type-{$field.type}" data-iteration="{$field_iteration}"  data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-name="{$field.name|escape:'htmlall'}" data-field="{$field.id_ndk_customization_field|escape:'htmlall'}" data-quantity-link="{$field.quantity_link}" {foreach from=$field.options key=k item=v} data-{$k}="{$v}"{/foreach}>
		<label class="toggler {if !$field.is_picto}toggler-default-picto{/if}"
		{if $field.is_picto} style="background-image: url('{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/pictos/{$field.id_ndk_customization_field|intval}.jpg');"{/if}
	>{$field.name|escape:'htmlall'} {if $field.show_price == 1}{if $fieldPrice > 0}{l s='cost : ' mod='ndk_advanced_custom_fields'} {convertPrice price=$fieldPrice}{/if}{/if}
			{if $field.is_visual == 1}
				<span class="layer_view visible_layer" data-group="{$field.id_ndk_customization_field|intval}" data-zindex="{$field.zindex|escape:'htmlall'}" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}"/>&nbsp;</span>
			{/if}
			{if $field.tooltip !=''}
				<div class="tooltipDescription">{$field.tooltip nofilter}</div>
					<span class="tooltipDescMark"></span>
			{/if}
		</label>
		<div class="fieldPane clearfix clear clearfix">
			{if $field.notice !=''}
				<div class="field_notice clearfix clear">{$field.notice nofilter}</div>
			{/if}
				<input data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall'}"  id="ndkcsfield_{$field.id_ndk_customization_field|intval}" type="hidden" name="ndkcsfield[{$field.id_ndk_customization_field|intval}]" value="" class="{if $field.required == 1} required_field{/if}"/>
				{if $field.values|@count > 4}
					{assign var="colxsx" value="col-md-3 col-xs-6"}
				{else}
					{assign var="colxsx" value="col-md-4 col-xs-6"}
				{/if}
				<input type="file" id="" class="upload_ndk_visu form-control" data-action="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}modules/ndk_advanced_custom_fields/uploadNdkcsfields.php" name="ndk_tmp_name" data-blend="{$field.color_effect}" data-special="bebaboubap"/>
						
					<div class="{$colxsx} img-block">
						<img class="{if $field.is_visual == 1}visual-effect {/if}img-value-{$field.id_ndk_customization_field|intval} img-responsive img-value hidden" data-value="" title="{$field.name|escape:'htmlall'}" src=""  data-group="{$field.id_ndk_customization_field|intval}" data-src="" data-zindex="{$field.zindex|escape:'htmlall'}" 
						data-dragdrop="{$field.draggable|intval}" 
						data-resizeable="{$field.resizeable|intval}" 
						data-rotateable="{$field.rotateable|intval}"  data-blend="{$field.color_effect}"
						{if $field.is_mask_image}data-mask-image="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/mask/{$field.id_ndk_customization_field|intval}.jpg"{/if}
						 data-price="{$fieldPrice|escape:'htmlall'}" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}"/>
						<a href="#" style="display:none" class="remove-upload button btn pull-right btn-default button-small"><span>{l s='remove' mod='ndk_advanced_custom_fields'}</span></a>
					</div>
		</div>
		<input type="hidden" id="ndkcsfieldPdf_{$field.id_ndk_customization_field|intval}" name="ndkcsfieldPdf[{$field.id_ndk_customization_field|intval}]"/>
		
	</div>