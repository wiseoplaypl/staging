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
				{if $value.set_quantity == 0 || $value.quantity > 0}
				{assign var=tags value=','|explode:$value.tags}
				<div data-hide-field="{if $value.influences_restrictions|strpos:"all" !== false}1{else}0{/if}" data-id-value="{$value.id|intval}" class=" filterTag {if $value.tags && $value.tags !=''} tagged {foreach from=$tags item=tag}{$tag|replace:' ':'-'} {/foreach}{/if} img-item-row" data-tags="{foreach from=$tags item=tag}{$tag}|{/foreach}" data-root="{$field.id_ndk_customization_field|intval}" data-group="{$field.id_ndk_customization_field|intval}">
					<img loading="lazy" class="ndk-lazy {if $value.reference == '[:product_image]'}load_product_image{/if} {if $field.is_visual == 1}visual-effect {/if}{if $value.issvg && $value.svgcode}svg {else} jpg{/if} img-value-{$field.id_ndk_customization_field|intval} img-responsive img-value" data-value="{$value.value|escape:'htmlall'}" title="{$value.value|escape:'htmlall'}"   data-group="{$field.id_ndk_customization_field|intval}" 
					
					data-src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/{$value.id|intval}{if $value.issvg}.svg{else}.jpg{/if}" data-zindex="{$field.zindex|escape:'htmlall'}" data-dragdrop="{$field.draggable|intval}" data-id-value="{$value.id|intval}"
					data-resizeable="{$field.resizeable|intval}" 
					data-rotateable="{$field.rotateable|intval}" 
					data-quantity-available="{if $value.set_quantity >0}{$value.quantity}{else}null{/if}" data-default-value="{$value.default_value|intval}"  
					 data-price="{if $valuePrice > 0}{$valuePrice|escape:'htmlall'}{else}{$fieldPrice|escape:'htmlall'}{/if}" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-blend="{$field.color_effect}"
					 
					 {if $ndkccpwebp && !$value.issvg && !$value.is3D}
						 data-thumb="{include file="module:ndk_custom_product_page/views/templates/hook/image-display-by-path.tpl" path='img/scenes/ndkcf/thumbs/'|cat:$value.id|cat:'-'|cat:Configuration::get('NDK_IMAGE_SIZE')|cat:'.jpg' get_link='true'}"
					 {else}
						 data-thumb="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/{if !$value.issvg}thumbs/{/if}{$value.id|intval}{if !$value.issvg}{if $value.is_texture}-texture{else}-{Configuration::get('NDK_IMAGE_SIZE')}{/if}{/if}{if $value.issvg}.svg{else}.jpg{/if}" 
					 {/if}
					 
					 
					 {if $field.is_mask_image}data-mask-image="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/mask/{$field.id_ndk_customization_field|intval}.jpg"{/if}
					 {foreach from=$value.options key=k item=v} data-{$k}="{$v}"{/foreach}
					 />
					{if $value.issvg && $value.svgcode }
					<div class="svg-container">{$value.svgcode nofilter}</div>
					{/if}
					
				<center><i>{$value.value|escape:'htmlall'}
				{if $field.show_price == 1}
				<span class="ndkcf-img-price">
					{if $valuePrice > 0} {l s="+" mod='ndk_advanced_custom_fields'}
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
						{if $fieldPrice > 0} {l s="+" mod='ndk_advanced_custom_fields'}
							{if $field.price_type == 'percent'}
								<span class="ndkcf-value-price percent">{$fieldPrice}%</span>
							{else}
								{if isset($value.tax_ratio) && $priceDisplay == 0}
								{math equation='x * y' x=$fieldPrice y=$value.tax_ratio assign='fieldPrice'}
								{/if}
								<span class="ndkcf-value-price">{convertPrice price=$fieldPrice}</span>
							{/if}
						{/if}
					{/if}
				</span>
				{/if}
				</i>
				{if $value.description !=''}
						<div class="tooltipDescription">{$value.description nofilter}</div>
							<span class="tooltipDescMark"></span>
					{/if}
				</center>
				<span class="value-json-details" id="value-json-details-{$value.id|intval}" ></span>
				</div>
				{/if}
			{/foreach}
			