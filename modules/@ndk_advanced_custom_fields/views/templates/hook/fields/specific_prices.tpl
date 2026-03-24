{if $field.show_price == 1}
{assign var=quantity_discounts_all value=NdkCfSpecificPrice::getSpecificPrices($field.id_ndk_customization_field, 0, 0, 0, 0, true)}

{if (isset($quantity_discounts_all))  && $quantity_discounts_all.0.reduction > 0}
	<p class="clear clearfix toggleQuantityDiscountBlock">{l s='Volume discounts ' mod='ndk_advanced_custom_fields'}</p>
	<div class="specificPriceBlock" style="display: none;">
		{assign var='values' value=$field.values}
		{capture name='all_text_discount'}{l s='for all products from this selection' mod='ndk_advanced_custom_fields'}{/capture}
		{append var='values' value=['id'=> 0, 'id_product_value' => 0, 'price' => 0 , 'value' => $smarty.capture.all_text_discount] index=-1}
		{foreach from=$values item=value}
			{assign var=quantity_discounts value=NdkCfSpecificPrice::getSpecificPrices($field.id_ndk_customization_field, $value.id, 0, true, Tools::getValue('id_product'))}
			
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
			
			
			{assign var='valuePrice' value=$smarty.capture.itemPrice|floatval}
			{assign var='valuePrice' value=Tools::convertPrice($valuePrice, Context::getContext()->currency->id)|round:6}
{* 			{assign var='valuePrice' value=Tools::convertPrice($value.price, Context::getContext()->currency->id)|round:6} *}
			
				{if (isset($quantity_discounts) && $quantity_discounts|@count > 0) && $quantity_discounts.0.reduction > 0}
					<!-- quantity discount -->
					<section class="page-product-box-ndk table-quantity-discount-container" data-id-value="{$value.id}" data-id-group="{$field.id_ndk_customization_field}">
						<p class="clear clearfix toggleQuantityDiscount">{l s='Volume discounts for' mod='ndk_advanced_custom_fields'} {$value.value}</p>
						<div class="quantityDiscount">
							<table class="std table-product-discounts">
								<thead>
									<tr>
										<th>{l s='Quantity'}</th>
										<th>{l s='Discount' mod='ndk_advanced_custom_fields'}</th>
										{if $valuePrice > 0}
										<th>{l s='You Save' mod='ndk_advanced_custom_fields'}</th>
										{/if}
									</tr>
								</thead>
								<tbody>
								{foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
								{if $valuePrice > 0}
									{if $quantity_discount.reduction >= 0 || $quantity_discount.reduction_type == 'amount'}
										{$realDiscountPrice=$valuePrice|floatval-$quantity_discount.reduction|floatval}
									{else}
										{$realDiscountPrice=$valuePrice|floatval-($valuePrice*$quantity_discount.reduction)|floatval}
									{/if}
								{else}
									{$realDiscountPrice=0|floatval}
								{/if}
									<tr id="quantityDiscount_{$quantity_discount.id_ndk_customization_field_value}" class="quantityDiscount_{$quantity_discount.id_ndk_customization_field_value}" data-real-discount-value="{convertPrice price = $realDiscountPrice}" data-discount-type="{$quantity_discount.reduction_type}" data-discount="{$quantity_discount.reduction|floatval}" data-discount-quantity="{$quantity_discount.from_quantity|intval}">
										<td>
											{$quantity_discount.from_quantity|intval}
										</td>
										<td>
											{if $quantity_discount.reduction >= 0 && $quantity_discount.reduction_type == 'amount'}
													{convertPrice price=$quantity_discount.reduction}
												
											{else}
												{$quantity_discount.reduction|floatval}%
												
											{/if}
										</td>
										{if $valuePrice > 0}
										<td>
												{if $quantity_discount.reduction >= 0 && $quantity_discount.reduction_type == 'amount'}
													{$discountPrice=$valuePrice|floatval-$quantity_discount.reduction|floatval}
												{else}
													{$discountPrice=$valuePrice|floatval-($valuePrice*($quantity_discount.reduction/100))|floatval}
												{/if}
												{$discountPrice=$discountPrice * $quantity_discount.from_quantity}
												{$qtyProductPrice=$valuePrice|floatval * $quantity_discount.from_quantity}
												{convertPrice price=$qtyProductPrice - $discountPrice}
										</td>
										{/if}
									</tr>
								{/foreach}
								</tbody>
							</table>
						</div>
					</section>
				{/if}
		{/foreach}
	</div>
{/if}
{/if}
{assign var=quantity_discounts_named value=NdkCfSpecificPrice::getSpecificPricesNamed($field.id_ndk_customization_field, 0, 0, Tools::getValue('id_product'))}
<script type="text/javascript">
	ndkSpecificPrices[{$field.id_ndk_customization_field}] = {$quantity_discounts_named|@json_encode nofilter};
</script>
