{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}
{if $field.values|@count > 4}
{assign var="colxsx" value="col-md-4 col-xs-4"}
{else}
{assign var="colxsx" value="col-md-4 col-xs-4"}
{/if}
		{foreach from=$field.values item=value}
			{if $field.price_type == 'percent'}
				{assign var='valuePrice' value=$value.price}
			{else}
				{assign var='valuePrice' value=Tools::convertPrice($value.price, Context::getContext()->currency->id)|round:6}
			{/if}
			{if $value.type== 'body'}
				{assign var="colxsx" value="col-md-4 col-xs-4"}
			{/if}			
			{if $value.set_quantity == 0 || $value.quantity > 0}
				{assign var=tags value=','|explode:$value.tags}
					
					<div class="{$colxsx} filterTag {if $value.tags && $value.tags !=''} tagged {foreach from=$tags item=tag}{$tag|replace:' ':'-'} {/foreach}{/if} img-item-row {if $value.type !=""}{$value.type} typed{/if} {if !$value.is_image && !$value.issvg} text-selector {/if}" 
						data-tags="{foreach from=$tags item=tag}{$tag}|{/foreach}" data-root="{$field.id_ndk_customization_field|intval}" data-type="{$value.type}" >
						
							<img class="{if !$value.is_image && !$value.issvg} hidden {else}{if $field.is_visual == 1 && $value.type != 'gender'}visual-effect {/if}{if $value.issvg}svg {else} jpg{/if}{/if}  img-value-{$field.id_ndk_customization_field|intval} img-responsive img-value img-value-{$value.type} img-caracter" 
								data-value="{$value.value|escape:'htmlall'}" title="{$value.value|escape:'htmlall'}"  
								data-group="{$field.id_ndk_customization_field|intval}" 
								data-src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/{$value.id|intval}{if $value.issvg}.svg{else}.jpg{/if}" 
								data-zindex="{$field.zindex|escape:'htmlall'}" data-dragdrop="{$field.draggable|intval}" 
								data-hide-field="{if $value.influences_restrictions|strpos:"all" !== false}1{else}0{/if}" 
								data-id-value="{$value.id|intval}"
								data-resizeable="{$field.resizeable|intval}" 
								data-rotateable="{$field.rotateable|intval}" 
								data-quantity-available="{if $value.set_quantity >0}{$value.quantity}{else}null{/if}"  
								data-price="0" data-id="{$field.target|escape:'htmlall'}" 
								data-view="{$field.target_child|escape:'htmlall'}" 
								data-type="{$value.type}"
								data-default-value="{$value.default_value|intval}"
								data-thumb="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/{if !$value.issvg}thumbs/{/if}{$value.id|intval}{if !$value.issvg}{if $value.is_texture}-texture{else}-{Configuration::get('NDK_IMAGE_SIZE')}{/if}{/if}{if $value.issvg}.svg{else}.jpg{/if}"  
								data-blend="{$field.color_effect}"/>
								{if $value.issvg}
									<div class="svg-container" data-type="{$value.type}">{$value.svgcode nofilter}</div>
								{/if}
								<span class="{if !$value.is_image && !$value.issvg} text-select-node {/if}">{$value.value|escape:'htmlall'}
								{if $value.description !=''}
									<div class="tooltipDescription">{$value.description nofilter}</div>
										<span class="tooltipDescMark"></span>
								{/if}
								</span>
								{/if}
					</div>
					
		{/foreach}