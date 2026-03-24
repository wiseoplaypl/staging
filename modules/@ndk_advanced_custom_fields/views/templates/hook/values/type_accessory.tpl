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
				<li class="clearfix col-xs-12 accessory-ndk {if $field.is_visual == 1}visual-effect {/if} filterTag {if $value.tags && $value.tags !=''} tagged {foreach from=$tags item=tag}{$tag|replace:' ':'-'} {/foreach}{/if}" data-tags="{foreach from=$tags item=tag}{$tag}|{/foreach}" data-value="{$value.value|escape:'htmlall'}" title="{$value.value|escape:'htmlall'}"  data-src="{if $value.is_image}{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/thumbs/{$value.id|intval}-{Configuration::get('NDK_IMAGE_LARGE_SIZE')}.jpg{else}0{/if}" data-group="{$field.id_ndk_customization_field|intval}"  data-zindex="{$field.zindex|escape:'htmlall'}" 
				  data-dragdrop="{$field.draggable|intval}" 
				  data-resizeable="{$field.resizeable|intval}" 
				  data-rotateable="{$field.rotateable|intval}" 
				  
				  data-price="{if $valuePrice > 0}{$valuePrice|escape:'htmlall'}{else}{$fieldPrice|escape:'htmlall'}{/if}"
				  data-id="{$field.target|escape:'htmlall'}" 
				  data-view="{$field.target_child|escape:'htmlall'}">
				  <div class="col-md-4">
				  	<img class="img-responsive" src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/thumbs/{$value.id|intval}-{Configuration::get('NDK_IMAGE_SIZE')}.jpg"/>
				  </div>
				 <div style="display:none">
					 <div id="accessory-popup-{$value.id|intval}" class="accessory-popup-ndk row">
					 	{if $value.is_image}
						 	<div class="col-md-6 ndk-img-block">
						 		<img class="img-responsive" src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/thumbs/{$value.id|intval}-{Configuration::get('NDK_IMAGE_LARGE_SIZE')}.jpg"/>
						 	</div>
					 	{/if}
					 	<div class="col-sm-6 ndk-infos-block">
					 		<p class="title_block">{$value.value|escape:'htmlall'}</p>
					 		<div class="ndk-accessory-desc">{$value.description nofilter}</div>
					 		{if $field.show_price == 1}
					 			{if $valuePrice > 0} : 
					 					{if $field.price_type == 'percent'}
					 						<span class="ndkcf-value-price percent">+{$valuePrice}%</span>
					 					{else}
					 						<span class="ndkcf-value-price">{convertPrice price=$valuePrice}</span>
					 					{/if}
					 				{else}
					 					{if $fieldPrice > 0} : 
					 						{if $field.price_type == 'percent'}
					 							<span class="ndkcf-value-price percent">+{$fieldPrice}%</span>
					 						{else}
					 							<span class="ndkcf-value-price">{convertPrice price=$fieldPrice}</span>
					 						{/if}
					 					{/if}
					 				{/if}
					 		{/if}
					 	</div>
					 </div>
				</div>
				 <div class="col-md-8 accessory-infos">
				 	<b>{$value.value|escape:'htmlall'}</b>
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
				 		
				 		<input type="number" name="ndkcsfield[{$field.id_ndk_customization_field|intval}][quantity][{$value.value}]" {if $value.set_quantity == 1}data-qtty-available="{$value.quantity|intval}" {/if}  data-qtty-max="{$value.quantity_max|intval}"  data-qtty-min="{$value.quantity_min|intval}" {if $value.quantity_max > 0}max="{$value.quantity_max|intval}"{/if}  min="{$value.quantity_min|intval}"  type="text" class="ndk-accessory-quantity price_overrided_accessory" id="ndk-accessory-quantity-{$value.id|intval}" 
				 		data-attr-lang="{$value.value}" 
				 		value="{if $defaultValue > 0 && $defaultValue > $value.quantity_min}{$defaultValue}{else}{$value.quantity_min|intval}{/if}"
				 		data-default-value="{if $defaultValue > 0 && $defaultValue > $value.quantity_min}{$defaultValue}{else}{$value.quantity_min|intval}{/if}"  
				 		data-step_quantity="{$value.step_quantity|escape:'htmlall'|replace:'*':''}" 
				 		data-price="{if $valuePrice > 0}{$valuePrice|escape:'htmlall'}{else}{$fieldPrice|escape:'htmlall'}{/if}" data-group="{$field.id_ndk_customization_field|intval}" data-hide-field="{if $value.influences_restrictions|strpos:"all" !== false}1{else}0{/if}" data-id-value="{$value.id|intval}"  data-value="{$value.value|escape:'htmlall'}" 
				 		data-value-id="{$field.id_ndk_customization_field|intval}-{$value.id|intval}" data-step_quantity="{$value.step_quantity|intval}"/>
				 		<span class="quantity-ndk-minus btn-default btn"><i class="icon-minus"></i></span>
				 		<span class="quantity-ndk-plus btn-default btn"><i class="icon-plus"></i></span>
				 	</p>
				 	{if $field.show_price == 1}
					 	<div class="price ndkcf-value-price">
					 		{if $valuePrice > 0}  
					 			{if $field.price_type == 'percent'}
					 				+{$valuePrice}%
					 			{else}
					 				{convertPrice price=$valuePrice}
					 			{/if}
					 		{else}
					 			{if $fieldPrice > 0}  
					 				{if $field.price_type == 'percent'}
					 					+{$fieldPrice}%
					 				{else}
					 					{convertPrice price=$fieldPrice}
					 				{/if}
					 			{/if}
					 		{/if}
					 	</div>
					 {/if}
				 </div>
				 
				</li>
				{/if}
			{/foreach}
			