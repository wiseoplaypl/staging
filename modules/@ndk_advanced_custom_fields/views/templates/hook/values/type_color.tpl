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
					{assign var=tags value=','|explode:$value.tags}
					{if $value.set_quantity == 0 || $value.quantity > 0}
					<li class="color-ndk {if $field.is_visual == 1}visual-effect {/if} filterTag {if $value.tags && $value.tags !=''} tagged {foreach from=$tags item=tag}{$tag|replace:' ':'-'} {/foreach}{/if}" data-value="{$value.value|escape:'htmlall'}" title="{$value.value|escape:'htmlall'}"  
					data-tags="{foreach from=$tags item=tag}{$tag}|{/foreach}"
					data-src="{if $value.is_image}{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/{$value.id|intval}.jpg{else}0{/if}" data-group="{$field.id_ndk_customization_field|intval}"  data-zindex="{$field.zindex|escape:'htmlall'}" 
					data-dragdrop="{$field.draggable|intval}" 
					data-resizeable="{$field.resizeable|intval}" 
					data-rotateable="{$field.rotateable|intval}" 
					 data-hide-field="{if $value.influences_restrictions|strpos:"all" !== false}1{else}0{/if}" data-id-value="{$value.id|intval}" data-default-value="{$value.default_value|intval}"
					data-quantity-available="{if $value.set_quantity >0}{$value.quantity}{else}999999999{/if}"  
					data-color="{if $value.is_image}url('{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/{$value.id|intval}.jpg'){else}{$value.color|escape:'htmlall'}{/if} " 
					 data-price="{if $valuePrice > 0}{$valuePrice|escape:'htmlall'}{else}{$fieldPrice|escape:'htmlall'}{/if}" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-blend="{$field.color_effect}" {if $field.is_mask_image}data-mask-image="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/mask/{$field.id_ndk_customization_field|intval}.jpg"{/if}>
						<div class="color-price-block">{$value.value|escape:'htmlall'}
						{if $field.show_price == 1}
							{if $valuePrice > 0} : {l s="+" mod='ndk_advanced_custom_fields'}
								{if $field.price_type == 'percent'}
									<span class="ndkcf-value-price percent">{$valuePrice}%</span>
								{else}
									<span class="ndkcf-value-price">{convertPrice price=$valuePrice}</span>
								{/if}
							{else}
								{if $fieldPrice > 0} : {l s="+" mod='ndk_advanced_custom_fields'}
									{if $field.price_type == 'percent'}
										<span class="ndkcf-value-price percent">{$fieldPrice}%</span>
									{else}
										<span class="ndkcf-value-price">{convertPrice price=$fieldPrice}</span>
									{/if}
								{/if}
							{/if}
						{/if}
						
						{if $value.description !=''}
								<div class="tooltipDescription">{$value.description nofilter}</div>
									<span class="tooltipDescMark"></span>
							{/if}
						</div>
						<span class="color-span" style="background:{if $value.is_texture}url('{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/thumbs/{$value.id|intval}-texture.jpg'){/if} {$value.color|escape:'htmlall'}">&nbsp;</span>
					</li>
					{/if}
				{/foreach}
				