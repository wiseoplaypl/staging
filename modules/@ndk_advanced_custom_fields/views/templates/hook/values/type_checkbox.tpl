{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}


			{foreach from=$field.values item=value}
				{if $field.price_type == 'percent'}
					{assign var='valuePrice' value=$value.price}
				{else}
					{assign var='valuePrice' value=Tools::convertPrice($value.price, Context::getContext()->currency->id)|round:6}
				{/if}
				{if $value.set_quantity == 0 || $value.quantity > 0}
				<span class="checkbox">
					{if $value.description !=''}
							<div class="tooltipDescription">{$value.description nofilter}</div>
								<span class="tooltipDescMark"></span>
					{/if}
					<input data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall'}"  id="checkbox_{$field.id_ndk_customization_field|escape:'htmlall'}_{$value.id|escape:'htmlall'}" type="checkbox" name="ndkcsfield[{$field.id_ndk_customization_field|intval}][checkbox][{$value.value}]" class=" {if $field.required == 1} required_field{/if} ndk-checkbox not_uniform{if $field.is_visual == 1}visual-effect {/if} {if $field.required == 1} required_field{/if}" data-group="{$field.id_ndk_customization_field|intval}" data-src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/thumbs/{$value.id|intval}-thickbox_default.jpg" data-zindex="{$field.zindex|escape:'htmlall'}" 
					data-dragdrop="{$field.draggable|intval}" 
					data-resizeable="{$field.resizeable|intval}" {foreach from=$value.options key=k item=v} data-{$k}="{$v}"{/foreach}
					data-rotateable="{$field.rotateable|intval}" data-default-value="{$value.default_value|intval}" 
					data-quantity-available="{if $value.set_quantity >0}{$value.quantity}{else}999999999{/if}"  
					{if $field.is_mask_image}data-mask-image="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/mask/{$field.id_ndk_customization_field|intval}.jpg"{/if}
					
					 value="{$value.value|escape:'htmlall'}" data-price="{if $valuePrice > 0}{$valuePrice|escape:'htmlall'}{else}{$fieldPrice|escape:'htmlall'}{/if}" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-value-id="{$value.id|intval}" data-hide-field="{if $value.influences_restrictions|strpos:"all" !== false}1{else}0{/if}" data-id-value="{$field.id_ndk_customization_field|intval}-{$value.id|intval}"/>
					<label for="checkbox_{$field.id_ndk_customization_field|escape:'htmlall'}_{$value.id|escape:'htmlall'}">{$value.value|escape:'htmlall'}
					{if $field.show_price == 1}
					
						{if $valuePrice > 0} : {l s="+" mod='ndk_advanced_custom_fields'}
							{if $field.price_type == 'percent'}
								<span class="ndkcf-value-price percent">{$valuePrice}%</span>
							{else}
								{$priceClass = ''}
								{if isset($value.tax_ratio) && $priceDisplay == 0}
								{$priceClass = 'dontConvertRatio'}
								{math equation='x * y' x=$valuePrice y=$value.tax_ratio assign='valuePrice'}
								{/if}
								<span class="ndkcf-value-price {$priceClass}">{convertPrice price=$valuePrice}</span>
							{/if}
						{else}
							{if $fieldPrice > 0} : {l s="+" mod='ndk_advanced_custom_fields'}
								{if $field.price_type == 'percent'}
									<span class="ndkcf-value-price percent">{$fieldPrice}%</span>
								{else}
									{if isset($value.tax_ratio) && $priceDisplay == 0}
									{math equation='x * y' x=$valuePrice y=$value.tax_ratio assign='valuePrice'}
									{/if}
									<span class="ndkcf-value-price">{convertPrice price=$fieldPrice}</span>
								{/if}
							{/if}
						{/if}
					{/if}
					</label>
				</span>
				{/if}
			{/foreach}
			