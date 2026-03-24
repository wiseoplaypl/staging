{**
 * 2007-2024 Tiralineas
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * @author    Ander <ander@tiralineas.digital>
 * @copyright 2007-2024 Tiralineas
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Tiralineas
 *}
<div class="cart-overview">
    <table style="width:100%">
         {foreach from=$products item=product name=cart_lines}
             {if $smarty.foreach.cart_lines.index >= 10}
                <tr>
                    <td colspan=6>{l s='And more....' mod='pshubspot'}</td>
                </tr>
                 {break}
             {/if}
            <tr>
                <td>
                <span class="product-image media-middle">
                    <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html':'UTF-8'}"
                         alt="{$product.name|escape:'html':'UTF-8'}" {if isset($smallSize)} width="{$smallSize.width|escape:'htmlall':'UTF-8'}" height="{$smallSize.height|escape:'htmlall':'UTF-8'}" {/if} />
                </span>
                </td>
                <td>
                     {capture name=sep} : {/capture}
                    <p class="product-name"><a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a>
                    </p>
                     {if $product.reference}<small class="cart_ref">SKU{$smarty.capture.default|escape:'htmlall':'UTF-8'}{$product.reference|escape:'html':'UTF-8'}</small> {/if}
                     {if isset($product.attributes) && $product.attributes}
                        <small><a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">{$product.attributes|@replace:$smarty.capture.sep:$smarty.capture.default|escape:'html':'UTF-8'}</a>
                        </small>
                     {/if}
                </td>
                <td>
                    <div class="current-price">
                        <ul class="price text-right" id="product_price_{$product.id_product}_{$product.id_product_attribute}{if $quantityDisplayed > 0}_nocustom{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
                             {if !empty($product.gift)}
                                <li class="gift-icon">Gift!</li>
                             {else}
                                 {if !$priceDisplay}
                                    <li class="price{if isset($product.is_discounted) && $product.is_discounted && isset($product.reduction_applies) && $product.reduction_applies} special-price{/if}">{convertPrice price=$product.price_wt}</li>
                                 {else}
                                    <li class="price{if isset($product.is_discounted) && $product.is_discounted && isset($product.reduction_applies) && $product.reduction_applies} special-price{/if}">{convertPrice price=$product.price}</li>
                                 {/if}
                                 {if isset($product.is_discounted) && $product.is_discounted && isset($product.reduction_applies) && $product.reduction_applies}
                                    <li class="price-percent-reduction small">
                                         {if !$priceDisplay}
                                             {if isset($product.reduction_type) && $product.reduction_type == 'amount'}
                                                 {assign var='priceReduction' value=($product.price_wt - $product.price_without_specific_price)}
                                                 {assign var='symbol' value=$currency->sign}
                                             {else}
                                                 {assign var='priceReduction' value=(($product.price_without_specific_price - $product.price_wt)/$product.price_without_specific_price) * 100 * -1}
                                                 {assign var='symbol' value='%'}
                                             {/if}
                                         {else}
                                             {if isset($product.reduction_type) && $product.reduction_type == 'amount'}
                                                 {assign var='priceReduction' value=($product.price - $product.price_without_specific_price)}
                                                 {assign var='symbol' value=$currency->sign}
                                             {else}
                                                 {assign var='priceReduction' value=(($product.price_without_specific_price - $product.price)/$product.price_without_specific_price) * -100}
                                                 {assign var='symbol' value='%'}
                                             {/if}
                                         {/if}
                                         {if $symbol == '%'}
                                            &nbsp;{$priceReduction|escape:'htmlall':'UTF-8'}{$symbol|escape:'htmlall':'UTF-8'}
                                         {else}
                                            &nbsp;{convertPrice price=$priceReduction}&nbsp;
                                         {/if}
                                    </li>
                                    <li class="old-price">{convertPrice price=$product.price_without_specific_price}</li>
                                 {/if}
                             {/if}
                        </ul>
                    </div>
                </td>
                <td></td>
                <td>
                    <span class="quantity">
                     {if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
                         {$product.customizationQuantityTotal}
                     {else}
                         {$product.cart_quantity-$quantityDisplayed}
                     {/if}
                    </span>
                </td>
                <td>
                    <span class="product-price">
                        <strong>
                            {if !empty($product.gift)}
                               <span class="gift-icon">{l s='Gift!'}</span>
                            {else}
                                {if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
                                    {if !$priceDisplay} {displayPrice price=$product.total_customization_wt} {else} {displayPrice price=$product.total_customization} {/if}
                                {else}
                                    {if !$priceDisplay} {displayPrice price=$product.total_wt} {else} {displayPrice price=$product.total} {/if}
                                {/if}
                            {/if}
                        </strong>
                    </span>
                </td>
            </tr>
             {if is_array($product.customizations) && $product.customizations|count >1}
                <hr>
             {/if}
         {/foreach}
    </table>
</div>