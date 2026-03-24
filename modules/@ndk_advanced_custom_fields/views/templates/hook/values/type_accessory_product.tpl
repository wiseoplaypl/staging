{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}

			{foreach from=$field.values item=value}
				
				{assign var='productArray' value=NdkCf::getProductInfos($value.id_product_value|intval, O, $field.id_ndk_customization_field,$value.id )}
				{assign var='product' value=$productArray.0}
				{capture name='itemPrice'}
					{if $value.price > 0}
							{$value.price}
						{elseif $field.price > 0}
							{$field.price}
						{else}
							{if !$priceDisplay}{$product.price}{else}{$product.price_tax_exc}{/if}
						{/if}
				{/capture}
				
				{if $value.tags != ''}
					{assign var=tags value=','|explode:$value.tags}
				{else}
					{assign var=tags value=','|explode:$product.category_default}
				{/if}
				
				{assign var='valuePrice' value=$smarty.capture.itemPrice|floatval}
				{assign var='valuePrice' value=Tools::convertPrice($valuePrice, Context::getContext()->currency->id)|round:6}
				
				{if ($value.set_quantity == 0 || $value.quantity > 0) && $product.active == 1}
				<li class="clearfix col-xs-12 accessory-ndk {if $field.is_visual == 1}visual-effect {/if} filterTag tagged {foreach from=$tags item=tag}{$tag|replace:' ':'-'} {/foreach}" data-tags="{foreach from=$tags item=tag}{$tag}|{/foreach}"  data-value="{$value.value|escape:'htmlall'}" title="{$value.value|escape:'htmlall'}"  
				 data-src="{if $value.is_image}{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/{$value.id|intval}{if $value.issvg}.svg{else}.jpg{/if}{else}{$link->getImageLink($product.link_rewrite, $product.cover_image_id, Configuration::get('NDK_LARGE_IMAGE_SIZE'))|escape:'html'}{/if}"  
				 data-group="{$field.id_ndk_customization_field|intval}"  data-zindex="{$field.zindex|escape:'htmlall'}" 
				  data-dragdrop="{$field.draggable|intval}" 
				  data-resizeable="{$field.resizeable|intval}" 
				  data-rotateable="{$field.rotateable|intval}" 
				  data-price="{$valuePrice}" data-original-price="0"
				  data-id="{$field.target|escape:'htmlall'}" 
				  data-view="{$field.target_child|escape:'htmlall'}" 
				  data-blend="{$field.color_effect}" 
				  data-id-product-value="{$value.id_product_value}" 
				  {if $field.is_mask_image}data-mask-image="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/mask/{$field.id_ndk_customization_field|intval}.jpg"{/if}>
				  
				  <div class="col-md-4 accessory-img-block">
				  	<img class="img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.cover_image_id, $ndk_image_size)|escape:'html'}"/>
				  </div>
				 <div style="display:none">
					 <div id="accessory-popup-{$value.id|intval}" class="accessory-popup-ndk row">
						 	<div class="col-md-6 ndk-img-block">
						 		<img class="img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.cover_image_id, $ndk_image_size)|escape:'html'}"/>
						 	</div>
					 	<div class="col-sm-6 ndk-infos-block">
					 		<p class="ndk-subtitle">{$product.name|escape:'htmlall'}</p>
					 		<div class="ndk-accessory-desc">{$product.description_short nofilter}</div>
					 		<div class="ndk-accessory-desc">{$product.description nofilter}</div>
					 		{if $field.show_price == 1}
						 		<div class="price">
						 			{if !$priceDisplay && $product.price > $valuePrice && $value.price > 0 || $product.price_without_reduction > $product.price }
						 				<span class="old_price">{convertPrice price=$product.price_without_reduction}</span>
						 				{elseif !$priceDisplay && $product.price_tax_exc > $valuePrice && $value.price > 0}
						 					<span class="old_price">{convertPrice price=$product.price_tax_exc}</span>
						 				{/if}
						 			<span class="ndkcf-value-price final_price final_price_{$value.id|intval}">{convertPrice price=$valuePrice}</span>
						 			{if $value.price == 0 && $field.price == 0}
						 				{if !empty($product.unity) && $product.unit_price_ratio > 0.000000}
						 						{math equation="pprice / punit_price" pprice=$valuePrice  punit_price=$product.unit_price_ratio assign=unit_price}
						 						<p class="unit-price"><span class="unit_price_display">{convertPrice price=$unit_price}</span> {l s='per' mod='ndk_advanced_custom_fields'} {$product.unity|escape:'html'}</p>
						 					{/if}
						 			{/if}
						 		</div>
					 		{include file='module:ndk_advanced_custom_fields/views/templates/hook/values/quantity_discount.tpl'}
					 		{/if}
					 		
					 	</div>
					 </div>
				</div>
				 <div class="col-md-8 accessory-infos">
				 	<b>{$product.name|escape:'htmlall'}
				 	{if $value.description !=''}
				 				<div class="tooltipDescription">{$value.description nofilter}</div>
				 					<span class="tooltipDescMark"></span>
				 		{/if}
				 	</b>
				 	
				 	
				 	
				 	{assign var='ndkcf_combinations' value= NdkCf::getProductAttributeCombinations($value.id_product_value)}
				 		<div class="ndk_att_list clear clearfix" data-key-product="">
				 		{if $ndkcf_combinations && $ndkcf_combinations.values|@count > 0}
				 			<label class="ndk_attribute_label">{$ndkcf_combinations.attribute_groups|escape:'html'}&nbsp;</label>
				 			<select name="attribute_combination_{$value.id_product_value}" id="attribute_combination_{$value.id_product_value}" class=" ndk_attribute_select" ref="{$value.id_product_value}" data-link-rewrite="{$product.link_rewrite|escape:'html'}">
				 				{foreach from=$ndkcf_combinations.values key=id_product_attribute item=combination}
				 					{if StockAvailable::getQuantityAvailableByProduct($value.id_product_value, $id_product_attribute) > 0 || Product::isAvailableWhenOutOfStock($product.out_of_stock)}	
				 					<option {if $product.cache_default_attribute == $id_product_attribute}selected="selected"{/if} value="{$id_product_attribute|intval}" title="{$combination.attributes_names|escape:'html'}">{$combination.attributes_names|escape:'html'}</option>
				 					{/if}
				 				{/foreach}
				 			</select>
				 		{/if}
				 		</div>
				 		
				 	{if 1 == 1 || Product::isAvailableWhenOutOfStock($product.out_of_stock)}	
					 	<p class="ndk-accessory-quantity-block">
					 	{assign var='defaultValue' value=0}
					 	{if $value.step_quantity !=''}
					 		{assign var="steps" value=";"|explode:$value.step_quantity}
							{foreach from=$steps item=step}
								{if $step|strstr:"*"}
					 				{assign var="defaultValue" value=$step|replace:"*":""}
								{/if}
							{/foreach}
					 	{/if}
					 		
					 		<input type="number" name="ndkcsfield[{$field.id_ndk_customization_field|intval}][quantityProd][{$value.id|intval}|{$value.id_product_value|intval}|0]" {if $value.set_quantity == 1}data-qtty-available="{$value.quantity|intval}" {/if}  data-qtty-max="{$value.quantity_max|intval}"  data-qtty-min="{$value.quantity_min|intval}" {if $value.quantity_max > 0}max="{$value.quantity_max|intval}"{/if}  min="{$value.quantity_min|intval}"  type="text" class="ndk-accessory-quantity {if $value.price > 0 || $field.price > 0}price_overrided{/if}" id="ndk-accessory-quantity-{$value.id|intval}" 
					 		value="{if $defaultValue > 0 && $defaultValue > $value.quantity_min}{$defaultValue}{else}{$value.quantity_min|intval}{/if}"
					 		data-default-value="{if $defaultValue > 0 && $defaultValue > $value.quantity_min}{$defaultValue}{else}{$value.quantity_min|intval}{/if}" 
					 		data-price-ratio="{$product.unit_price_ratio}" 
							{if $value.price > 0 || $field.price > 0}data-override-price="{$valuePrice}"{/if}
					 		data-price="{$valuePrice}" data-original-price="0" data-group="{$field.id_ndk_customization_field|intval}" data-hide-field="{if $value.influences_restrictions|strpos:"all" !== false}1{else}0{/if}" data-id-value="{$value.id|intval}"  data-value="{$value.value|escape:'htmlall'}" data-value-id="{$field.id_ndk_customization_field|intval}-{$value.id|intval}" data-id-product-accessory="{$value.id_product_value|intval}" data-step_quantity="{$value.step_quantity|escape:'htmlall'|replace:'*':''}" data-weight="{$product.weight}" data-product-weight="{$product.weight}"
					 		data-attr-lang="{$product.name|escape:'htmlall'}" data-product-lang="{$product.name|escape:'htmlall'}"/>
					 		<span class="quantity-ndk-minus btn-default btn"><i class="icon-minus"></i></span>
					 		<span class="quantity-ndk-plus btn-default btn"><i class="icon-plus"></i></span>
					 		
					 	</p>
					 {else}
					 	<span class="oos_msg">{l s='Out of stock' mod='ndk_advanced_custom_fields'}</span>
					 {/if}
					 {if $ndkcf_show_quantity == 1}
					 		<span class="opt_qtty_available {if StockAvailable::getQuantityAvailableByProduct($value.id_product_value, $product.cache_default_attribute) < 1} qtty-warning{/if}">{l s='available : ' mod='ndk_advanced_custom_fields'}<b>{StockAvailable::getQuantityAvailableByProduct($value.id_product_value, $product.cache_default_attribute)}</b></span>
					 {/if}
				 	<a class="fancybox accessory-more" href="#accessory-popup-{$value.id|intval}"></a>
				 	
				 	
				 	{if $field.show_price == 1}
				 		<div class="price">
				 					{if !$priceDisplay && $product.price > $valuePrice && $value.price > 0 || $product.price_without_reduction > $product.price }
									  <span class="old_price">{convertPrice price=$product.price_without_reduction}</span>
									  {elseif !$priceDisplay && $product.price_tax_exc > $valuePrice && $value.price > 0}
										  <span class="old_price">{convertPrice price=$product.price_tax_exc}</span>
									  {/if}
				 				
				 				<span class="ndkcf-value-price final_price final_price_{$value.id|intval}">{convertPrice price=$valuePrice}</span>
				 				{if $value.price == 0 && $field.price == 0}
				 				{if !empty($product.unity) && $product.unit_price_ratio > 0.000000}
				 						{math equation="pprice / punit_price" pprice=$valuePrice  punit_price=$product.unit_price_ratio assign=unit_price}
				 						<p class="unit-price"><span class="unit_price_display">{convertPrice price=$unit_price}</span> {l s='per' mod='ndk_advanced_custom_fields'} {$product.unity|escape:'html'}</p>
				 					{/if}
				 				{/if}
				 			</div>
				 	{/if}
				 	</div>
				 	
				 	{include file='module:ndk_advanced_custom_fields/views/templates/hook/values/accessory_customization.tpl'}
				</li>
				{/if}
			{/foreach}
			