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
				{assign var='valuePrice' value=Tools::convertPrice($smarty.capture.itemPrice, Context::getContext()->currency->id)|round:6}
				{if ($value.set_quantity == 0 || $value.quantity > 0) && $product.active == 1}
				<li class="clearfix accessory-ndk stepped-accessory-ndk filterTag tagged {foreach from=$tags item=tag}{$tag|replace:' ':'-'} {/foreach}" data-tags="{foreach from=$tags item=tag}{$tag}|{/foreach}"  data-group="{$field.id_ndk_customization_field|intval}"  data-zindex="{$field.zindex|escape:'htmlall'}" 
				  data-dragdrop="{$field.draggable|intval}" 
				  data-resizeable="{$field.resizeable|intval}" 
				  data-rotateable="{$field.rotateable|intval}" 
				  data-price="{$valuePrice}" data-original-price="0"
				  data-id="{$field.target|escape:'htmlall'}" 
				  data-id-value="{$value.id}"
				  data-view="{$field.target_child|escape:'htmlall'}" 
				  data-id-product-value="{$value.id_product_value}">
				  <!--<div class="col-md-4 accessory-img-block">
				  	<img class="img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.cover_image_id, $ndk_image_size)|escape:'html'}"/>
				  </div>-->
				  
				 <div style="display:none">
					 <div id="accessory-popup-{$value.id|intval}" class="accessory-popup-ndk row">
						 	<div class="col-md-6 ndk-img-block">
						 		<img class="img-responsive" style="display:none" src="{$link->getImageLink($product.link_rewrite, $product.cover_image_id, Configuration::get('NDK_LARGE_IMAGE_SIZE'))|escape:'html'}"/>
						 	</div>
					 	<div class="col-sm-6 ndk-infos-block">
					 		<p class="ndk-subtitle">{$product.name|escape:'htmlall'}</p>
					 		<div class="ndk-accessory-desc">{$product.description_short nofilter}</div>
					 		<div class="ndk-accessory-desc">{$product.description nofilter}</div>
					 		{if $field.show_price == 1}
						 		<div class="price">
						 			{if !$priceDisplay && $product.price > $valuePrice}
						 				<span class="old_price">{convertPrice price=$product.price}</span>
						 				{elseif !$priceDisplay && $product.price_tax_exc > $valuePrice}
						 					<span class="old_price">{convertPrice price=$product.price_tax_exc}</span>
						 				{/if}
										 
						 			<span class="ndkcf-value-price final_price final_price_{$value.id|intval}">{convertPrice price=$valuePrice}</span>
						 			{if $value.price == 0 && $field.price == 0}
						 				{if !empty($product.unity) && $product.unit_price_ratio > 0.000000}
						 						{math equation="pprice / punit_price" pprice=$valuePrice  punit_price=$product.unit_price_ratio assign=unit_price}
						 						<p class="unit-price"><span class="unit_price_display ndkcf-value-price">{convertPrice price=$unit_price}</span> {l s='per' mod='ndk_advanced_custom_fields'} {$product.unity|escape:'html'}</p>
						 					{/if}
						 			{/if}
						 		</div>
						 		{include file='module:ndk_advanced_custom_fields/views/templates/hook/values/quantity_discount.tpl'}
					 		{/if}
					 	</div>
					 </div>
				</div>
				 <div class="clearfix accessory-infos autoHeight">
				 	<b>{$product.name|escape:'htmlall'}</b>
				 	<a class="fancybox accessory-more" href="#accessory-popup-{$value.id|intval}"></a>
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
				 	<span>{l s='Total quantity : ' mod='ndk_advanced_custom_fields'}</span>
				 		<input class="ndkcf_totalprod_quantity" id="ndkcf_totalprod_quantity_{$value.id|intval}" type="text" readonly="readonly" value="0" name="totalprodquantity-{$value.id|intval}-{$value.id_product_value|intval}" size="8"/>
				 		
				 	</p>
				 	</div>
				 	<div class="clear clearfix"></div>
				 	{assign var='ndkcf_combinations' value=NdkCf::getProductAttributeCombinationsStep($value.id_product_value)}
				 		<div class="row clear clearfix ndkcf_combination_tab ndkcf_step_comb_list">
				 		{foreach $ndkcf_combinations.uniques as $group }
							 <div class="ndkcf_step_comb_list_group">
								 {$firstGroup = $group|reset}
								 <div class="group-name">{$firstGroup.public_group_name}</div>
									 <div class="ndkcf_step_comb_list_items">
									 {foreach $group as $col name="group"}
											<div class="ndkcf_unique_comb ndkcf_step_comb_list_item" data-id-value="{$value.id}" {foreach $col as $k=>$v}data-{$k} = {$v} {/foreach}>
												{assign var=id_image value=Ndkcf::getAttributeImageAssociations($col.id_product_attribute, $value.id_product_value)}
												 {assign var='img_color_exists' value=file_exists($img_col_dir|cat:$col.id_attribute|cat:'.jpg')}
												<span class="comblist_selector" title="{$col.attribute_name}">
													  {if $col.is_color_group == 1}
													  <span class="color_square" style="background: {$col.attribute_color};"></span>
													  {/if}
													  {$col.attribute_name}
												</span>
											</div>
							 		{/foreach}
									 </div>
							 </div>
				 		{/foreach}
						 
						
						{foreach $ndkcf_combinations.multiples as $group }
						 <div class="ndkcf_step_comb_list_group">
							 {$firstGroup = $group|reset}
							 <div class="group-name">{$firstGroup.public_group_name}</div>
							 
								 <div class="ndkcf_step_comb_list_items">
								 {foreach $group as $col name="group"}
										<div class="ndkcf_multiple_comb ndkcf_step_comb_list_item" 
										{foreach $col as $key=>$val}
											data-{$key}="{$val}" 
										{/foreach}>
											{assign var=id_image value=Ndkcf::getAttributeImageAssociations($col.id_product_attribute, $value.id_product_value)}
											{assign var='img_color_exists' value=file_exists($img_col_dir|cat:$col.id_attribute|cat:'.jpg')}
											<span class="comblist_selector" title="{$col.attribute_name}">{$col.attribute_name}</span>
											<span class="ndk-accessory-quantity-block">
												 <input type="number" name="ndkcsfield[{$field.id_ndk_customization_field|intval}][quantityProd][{$value.id|intval}|{$value.id_product_value|intval}|{$col.id_product_attribute}]" {if $value.set_quantity == 1}data-qtty-available="{$value.quantity|intval}" {/if}  data-qtty-max="{$value.quantity_max|intval}"  data-qtty-min="{$value.quantity_min|intval}" {if $value.quantity_max > 0}max="{$value.quantity_max|intval}"{/if}  min="{$value.quantity_min|intval}"  type="text" class="ndk-accessory-comb-tab ndk-accessory-quantity {if $value.price > 0 || $field.price > 0}price_overrided{/if}" id="ndk-accessory-quantity-{$value.id|intval}-{$value.id_product_value|intval}-{$col.id_product_attribute}" 
													 value="{if $defaultValue > 0 && $defaultValue > $value.quantity_min}{$defaultValue}{else}{$value.quantity_min|intval}{/if}"
													 data-default-value="{if $defaultValue > 0 && $defaultValue > $value.quantity_min}{$defaultValue}{else}{$value.quantity_min|intval}{/if}" 
													 data-price-ratio="{$product.unit_price_ratio}" 
													 data-price="{$valuePrice}" data-original-price="0" data-group="{$field.id_ndk_customization_field|intval}" data-hide-field="{if $value.influences_restrictions|strpos:"all" !== false}1{else}0{/if}" data-id-value="{$value.id|intval}"  data-value="{$value.value|escape:'htmlall'}" data-value-id="{$field.id_ndk_customization_field|intval}-{$value.id|intval}-{$col.id_product_attribute|intval}" data-id-product-accessory="{$value.id_product_value|intval}" data-step_quantity="{$value.step_quantity|escape:'htmlall'|replace:'*':''}"  data-weight="{$product.weight}" data-product-weight="{$product.weight}"
													 data-attr-lang="{NdkCf::getAttributesLang($value.id_product_value, $col.id_product_attribute, Context::getContext()->language->id)}" 
													  data-id_combination="{$col.id_product_attribute}"
													 />
													 <span class="quantity-ndk-minus btn-default "><i class="icon-minus"></i></span>
													 <span class="quantity-ndk-plus btn-default "><i class="icon-plus"></i></span>
											 </span>
												 {if $ndkcf_show_quantity == 1}
													 <span class="opt_qtty_available {if StockAvailable::getQuantityAvailableByProduct($value.id_product_value, $col.id_product_attribute) < 1} qtty-warning{/if}">{l s='available : ' mod='ndk_advanced_custom_fields'}<b>{StockAvailable::getQuantityAvailableByProduct($value.id_product_value, $col.id_product_attribute)}</b></span>
												 {/if}
												 {if $field.show_price == 1}
													 <div class="price">
																	 <span class="ndkcf-value-price final_price final_price_{$field.id_ndk_customization_field|intval}-{$value.id|intval}-{$col.id_product_attribute|intval}">{convertPrice price=NdkCf::getAttributePrice($value.id_product_value, $col.id_product_attribute)}</span>
													 </div>
												 {/if}
										</div>
								 {/foreach}
								 </div>
						 </div>
					 {/foreach}
					 
				 		</div>
				 	
				 	{include file='module:ndk_advanced_custom_fields/views/templates/hook/values/accessory_customization.tpl'}
				</li>
			{/if}
			{/foreach}