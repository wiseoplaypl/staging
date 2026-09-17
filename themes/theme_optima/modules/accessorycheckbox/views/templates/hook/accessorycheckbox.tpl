{*
* 2007-2024 PrestaShop
*

* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2024 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}

{if $_PS_VERSION_<1.7}
<script type="text/javascript">
{if $_PS_VERSION_<1.6}
function onADD_TO_CART_ButtonClicked(){
	  $("input.accessories_checkbox").each(function(index, element) {
		  if(this.checked){
			// add the picture to the cart
					$element = $('#accessories_img_'+this.value);
				var $picture = $element.clone();
				var pictureOffsetOriginal = $element.offset();

				if ($picture.size()){
					$picture.css("position", "absolute");
					$picture.css("top", pictureOffsetOriginal.top);
					$picture.css("left", pictureOffsetOriginal.left);
				}
				var pictureOffset = $picture.offset();
				if ($('#cart_block').offset().top && $('#cart_block').offset().left)
					var cartBlockOffset = $('#cart_block').offset();
				else
					var cartBlockOffset = $('#shopping_cart').offset();

				// Check if the block cart is activated for the animation
				if (cartBlockOffset != undefined && $picture.size())
				{
					$picture.appendTo('body');
					$picture.css({ 'position': 'absolute', 'top': $picture.css('top'), 'left': $picture.css('left'), 'z-index': 4242 })
					.animate({ 'width': $element.attr('width')*0.66, 'height': $element.attr('height')*0.66, 'opacity': 0.2, 'top': cartBlockOffset.top + 30, 'left': cartBlockOffset.left + 15 }, 1000)
					.fadeOut(100);
				}
				$picture = null;
				$.ajax({
								type: 'POST',
								url: baseUri,
								async: false,
								cache: false,
								dataType : "json",
								data: 'controller=cart&add=1&ajax=true&qty=1&id_product=' + this.value + '&token=' + static_token,
								success: function(jsonData,textStatus,jqXHR)
								{
									//ajaxCart.updateCartInformation(jsonData, true);
								}
					});

		  }
	  });
}
{else if $_PS_VERSION_<1.7}
jQuery(document).ready(function(e) {

	{if $_PS_VERSION_<1.6}
	    $('#add_to_cart input.exclusive').attr('onClick','javascript:onADD_TO_CART_ButtonClicked();');
		$("a.quick-view").fancybox();
		$('ul#idTab4').hide();
		$("ul#more_info_tabs li a").each(function(index, element) {
				if($(this).attr('href')=='#idTab4')
				$(this).hide();
			});
	{else}

		$('div.accessories-block').parent('section').children('h3').hide();
		$('div.accessories-block, .product-accessories').hide();

		$("#add_to_cart .exclusive, button.exclusive, button.ajax_add_to_cart_product_button").click(function(){
			$("input.accessories_checkbox").each(function(index, element) {
				if(this.checked){

					var id_product_attribute = $(this).parents('table:first').find('#acc_product_'+this.value).val();
 				 if (typeof id_product_attribute === "undefined")
 				    var id_product_attribute = 0;

					//ajaxCart.add( this.value, null, false, this, 1, null);
					$.ajax({
							type: 'POST',
							headers: { "cache-control": "no-cache" },
							url: baseUri + '?rand=' + new Date().getTime(),
							async: false,
							cache: false,
							dataType : "json",
							data: 'controller=cart&add=1&ajax=true&qty=1&id_product=' + this.value + '&id_product_attribute=' + id_product_attribute + '&token=' + static_token,
							success: function(jsonData,textStatus,jqXHR)
							{
								ajaxCart.updateCartInformation(jsonData, true);
							}
				    });
				}
			});
		});
	{/if}
});
{/if}
</script>
{/if}
<div class="multi-accessories">
<div class="accessory-heading">{l s='Complete your Set' mod='accessorycheckbox'}</div>
<div class="accessorycheckbox list-inline no-print">
		{foreach from=$accessories item=accessory name=accessories_list}
								{if ($accessory.allow_oosp || $accessory.quantity > 0) && $accessory.available_for_order && !isset($restricted_country_mode)}
									{assign var='accessoryLink' value=$link->getProductLink($accessory.id_product, $accessory.link_rewrite, $accessory.category)}
							<div class="ajax_block_product{if $smarty.foreach.accessories_list.first} first_item{elseif $smarty.foreach.accessories_list.last} last_item{else} item{/if}"><article class="product-miniature js-product-miniature" data-id-product="{$accessory.id_product|intval}" data-id-product-attribute=""  >
                            <meta  content="{$accessory.name|escape:'html':'UTF-8'}">
                            <meta  content="{$accessoryLink|escape:'html':'UTF-8'}">
                            <meta  content="{$accessory.meta_description|escape:'html':'UTF-8'}">
                            <meta  content="{$accessory.manufacturer_name|escape:'html':'UTF-8'}">
                            <meta  content="{$link->getImageLink($accessory.link_rewrite, $accessory.id_image, 'small_default')|escape:'html':'UTF-8'}">
														<div   ></div>
													 <div   >
														 <meta  content="{$accessory.name|truncate:30|escape:'html':'UTF-8'}" />
													 </div>

                            <table width="100%"> <tr> <td width="20px">
                                    <input class="accessories_checkbox" type="checkbox" name="accessories" value="{$accessory.id_product|intval}">
																	</td><td align="center" width="65px">
                                    <a href="{$accessoryLink|escape:'html':'UTF-8'}"  rel="{$accessoryLink|escape:'html':'UTF-8'}" title="{$accessory.legend|escape:'html':'UTF-8'}" class="quick-view" data-link-action="quickview">
                                    <img id="accessories_img_{$accessory.id_product|intval}" src="{$link->getImageLink($accessory.link_rewrite, $accessory.id_image, 'small_default')|escape:'html':'UTF-8'}" alt="{$accessory.legend|escape:'html':'UTF-8'}" width="60px" />
                                    </a>
                                    </td><td>
									<a href="{$accessoryLink|escape:'html':'UTF-8'}" rel="{$accessoryLink|escape:'html':'UTF-8'}" title="{$accessory.legend|escape:'html':'UTF-8'}" class="quick-view" data-link-action="quickview">
													{$accessory.name|escape:'html':'UTF-8'}
												</a>

                                     {if count($accessory.combinations)>0}
																		 <br />
                                         <select class="input-group form-control" name="acc_product[{$accessory.id_product|intval}]" id="acc_product_{$accessory.id_product|intval}">
                                         {foreach $accessory.combinations as $comb}
                                           <option value="{$comb.id_product_attribute|intval}" {if $comb.default_on==1}selected="selected"{/if}>{$comb.attribute_designation|escape:'htmlall':'UTF-8'} &nbsp;+{Tools::displayPrice(Product::getPriceStatic($comb.id_product, true, $comb.id_product_attribute))|escape:'htmlall':'UTF-8'}</option>
                                         {/foreach}
                                         </select>
                                         {else}
											{if $accessory.show_price && !isset($restricted_country_mode) && !$configuration.is_catalog}
											</td><td align="right">
											<span class="price pull-right">
                                                     +{if $priceDisplay != 1}{Tools::displayPrice($accessory.price|escape:'htmlall':'UTF-8')}{else}{Tools::displayPrice($accessory.price_tax_exc|escape:'htmlall':'UTF-8')}{/if}
											</span>
											{/if}
                                            {/if}
                                         </td></tr></table>
                                         </article>
									</div>
								{/if}
							{/foreach}
</div>
</div>
