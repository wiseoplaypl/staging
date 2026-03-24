{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2017 Hendrik Masson
 *  @license   Tous droits réservés
*}

{assign var=quantity_discounts value=NdkCf::getQuantityDiscount($value.id_product_value)}
{assign var=display_discount_price value=true}

{if (isset($quantity_discounts) && count($quantity_discounts) > 0)}
			<!-- quantity discount -->
			<section class="page-product-box-ndk table-quantity-discount-container" data-id-value="{$value.id}" data-id-group="{$value.id}">
				<p class="ndk-subtitle">{l s='Volume discounts' mod='ndk_advanced_custom_fields'}</p>
				<div id="quantityDiscount">
					<table class="std table-product-discounts">
						<thead>
							<tr>
								<th>{l s='Quantity'}</th>
								<th>{if $display_discount_price}{l s='Price' mod='ndk_advanced_custom_fields'}{else}{l s='Discount' mod='ndk_advanced_custom_fields'}{/if}</th>
								<th>{l s='You Save' mod='ndk_advanced_custom_fields'}</th>
							</tr>
						</thead>
						<tbody>
						{foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
							{if $quantity_discount.price >= 0 || $quantity_discount.reduction_type == 'amount'}
								{$realDiscountPrice=$valuePrice|floatval-$quantity_discount.real_value|floatval}
							{else}
								{$realDiscountPrice=$valuePrice|floatval-($valuePrice*$quantity_discount.reduction)|floatval}
							{/if}
							<tr id="quantityDiscount_{$quantity_discount.id_product_attribute}" class="quantityDiscount_{$quantity_discount.id_product_attribute}" data-real-discount-value="{convertPrice price = $realDiscountPrice}" data-discount-type="{$quantity_discount.reduction_type}" data-discount="{$quantity_discount.real_value|floatval}" data-discount-quantity="{$quantity_discount.quantity|intval}">
								<td>
									{$quantity_discount.quantity|intval}
								</td>
								<td>
									{if $quantity_discount.price >= 0 || $quantity_discount.reduction_type == 'amount'}
										{if $display_discount_price}
											{if $quantity_discount.reduction_tax == 0 && !$quantity_discount.price}
												{convertPrice price = $valuePrice|floatval-($valuePrice*$quantity_discount.reduction_with_tax)|floatval}
											{else}
												{convertPrice price=$valuePrice|floatval-$quantity_discount.real_value|floatval}
											{/if}
										{else}
											{convertPrice price=$quantity_discount.real_value|floatval}
										{/if}
									{else}
										{if $display_discount_price}
											{if $quantity_discount.reduction_tax == 0}
												{convertPrice price = $valuePrice|floatval-($valuePrice*$quantity_discount.reduction_with_tax)|floatval}
											{else}
												{convertPrice price = $valuePrice|floatval-($valuePrice*$quantity_discount.reduction)|floatval}
											{/if}
										{else}
											{$quantity_discount.real_value|floatval}%
										{/if}
									{/if}
								</td>
								<td>
									<span>{l s='Up to' mod='ndk_advanced_custom_fields'}</span>
									{if $quantity_discount.price >= 0 || $quantity_discount.reduction_type == 'amount'}
										{$discountPrice=$valuePrice|floatval-$quantity_discount.real_value|floatval}
									{else}
										{$discountPrice=$valuePrice|floatval-($valuePrice*$quantity_discount.reduction)|floatval}
									{/if}
									{$discountPrice=$discountPrice * $quantity_discount.quantity}
									{$qtyProductPrice=$valuePrice|floatval * $quantity_discount.quantity}
									{convertPrice price=$qtyProductPrice - $discountPrice}
								</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				</div>
			</section>
		{/if}