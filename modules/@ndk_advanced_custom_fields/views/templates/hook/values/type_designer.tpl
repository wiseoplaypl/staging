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
								{assign var=tags value=','|explode:$value.tags}
								<div class="filterTag {if $value.tags && $value.tags !=''} tagged {foreach from=$tags item=tag}{cleanCssClass class=$tag} {/foreach}{/if} img-item-row" data-tags="{foreach from=$tags item=tag}{$tag}|{/foreach}" data-root="{$field.id_ndk_customization_field|intval}">
									<img loading="lazy" class="ndk-lazy {if $field.is_visual == 1}visual-effect {/if}{if $value.issvg}svg {else} jpg{/if} img-value-{$field.id_ndk_customization_field|intval} img-responsive img-value" src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}modules/ndk_advanced_custom_fields/views/img/lazy.jpg" data-value="{$value.value|escape:'htmlall'}" title="{$value.value|escape:'htmlall'}"  data-group="{$field.id_ndk_customization_field|intval}" data-src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/{$value.id|intval}{if $value.issvg}.svg{else}.jpg{/if}" data-zindex="{$field.zindex|escape:'htmlall'}" data-dragdrop="{$field.draggable|intval}" data-hide-field="{if $value.influences_restrictions|strpos:"all" !== false}1{else}0{/if}" data-id-value="{$value.id|intval}"
									data-resizeable="{$field.resizeable|intval}" 
									data-rotateable="{$field.rotateable|intval}" data-group="{$field.id_ndk_customization_field|intval}"
									data-quantity-available="{if $value.set_quantity >0}{$value.quantity}{else}null{/if}"  
									 data-price="0" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-default-value="{$value.default_value|intval}"
									 {if $ndkccpwebp && !$value.issvg && !$value.is3D}
										  data-thumb="{include file="module:ndk_custom_product_page/views/templates/hook/image-display-by-path.tpl" path='img/scenes/ndkcf/thumbs/'|cat:$value.id|cat:'-'|cat:Configuration::get('NDK_IMAGE_SIZE')|cat:'.jpg' get_link='true'}"
									  {else}
										  data-thumb="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/{if !$value.issvg}thumbs/{/if}{$value.id|intval}{if !$value.issvg}{if $value.is_texture}-texture{else}-{Configuration::get('NDK_IMAGE_SIZE')}{/if}{/if}{if $value.issvg}.svg{else}.jpg{/if}" 
									  {/if}
									  data-blend="{$field.color_effect}"/>
									{if $value.issvg}
									<div class="svg-container">{$value.svgcode nofilter}</div>
									{/if}
									
								<center><i>{$value.value|escape:'htmlall'}</i>
								{if $value.description !=''}
										<div class="tooltipDescription">{$value.description nofilter}</div>
											<span class="tooltipDescMark"></span>
									{/if}
								</center>
								</div>
								{/if}
							{/foreach}