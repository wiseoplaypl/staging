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
						
						<option {if $value.set_quantity == 1 && $value.quantity < 1}class="disabled_value_by_qtty"{/if} value="{$value.value|escape:'htmlall'}" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-group="{$field.id_ndk_customization_field|intval}" data-src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/{$value.id|intval}.jpg" data-zindex="{$field.zindex|escape:'htmlall'}"
						 data-dragdrop="{$field.draggable|intval}" 
						 data-resizeable="{$field.resizeable|intval}" 
						 data-rotateable="{$field.rotateable|intval}" 
						 {foreach from=$value.options key=k item=v} data-{$k}="{$v}"{/foreach}
						  data-hide-field="{if $value.influences_restrictions|strpos:"all" !== false}1{else}0{/if}" data-id-value="{$value.id|intval}" data-default-value="{$value.default_value|intval}" 
						 data-quantity-available="{if $value.set_quantity >0}{$value.quantity}{else}999999999{/if}"  
						 data-price="{if $valuePrice != 0}{$valuePrice|escape:'htmlall'}{else}{$fieldPrice|escape:'htmlall'}{/if}" {if $field.is_mask_image}data-mask-image="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/mask/{$field.id_ndk_customization_field|intval}.jpg"{/if}>{$value.value|escape:'htmlall'} 
						 {if $field.show_price == 1}
							 {if $valuePrice != 0} : {l s="+" mod='ndk_advanced_custom_fields'}
							 	{if $field.price_type == 'percent'}
							 		{$valuePrice}%
							 	{else}
							 		{if isset($value.tax_ratio) && $priceDisplay == 0}
									 {math equation='x * y' x=$valuePrice y=$value.tax_ratio assign='valuePrice'}
									 {/if}
									 {convertPrice price=$valuePrice}
							 	{/if}
							 {else}
							 	{if $fieldPrice != 0} : {l s="+" mod='ndk_advanced_custom_fields'}
							 		{if $field.price_type == 'percent'}
							 			{$fieldPrice}%
							 		{else}
							 			{if isset($value.tax_ratio) && $priceDisplay == 0}
										 {math equation='x * y' x=$fieldPrice y=$value.tax_ratio assign='fieldPrice'}
										 {/if}
										 {convertPrice price=$fieldPrice}
							 		{/if}
							 	{/if}
							 {/if}
						{/if}
						{if $value.set_quantity == 1 && $value.quantity < 1} {l s='(Out of stock)' mod='ndk_advanced_custom_fields'}{/if}
						 </option>
						
					{/foreach}
				