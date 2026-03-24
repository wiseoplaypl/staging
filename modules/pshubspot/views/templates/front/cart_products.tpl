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
     {if $cart.products}
        <table style="width:100%">
             {foreach from=$cart.products item=product name=cart_lines}
                 {if $smarty.foreach.cart_lines.index >= 10}
                    <tr>
                        <td colspan=6>{l s='And more....' mod='pshubspot'}</td>
                    </tr>
                     {break}
                 {/if}
                <tr>
                    <td>
                        <span class="product-image media-middle">
                            {if $product.cover}
                               <img src="{$product.cover.bySize.cart_default.url|escape:'htmlall':'UTF-8'}" alt="{$product.name|escape:'quotes':'UTF-8'}"/>
                            {else}
                               <img src="{$urls.no_picture_image.bySize.cart_default.url|escape:'htmlall':'UTF-8'}"/>
                            {/if}
                        </span>
                    </td>
                    <td><a class="label" href="{$product.url|escape:'htmlall':'UTF-8'}" data-id_customization="{$product.id_customization|intval}">{$product.name|escape:'htmlall':'UTF-8'}</a></td>
                    <td>
                         {if $product.has_discount}
                            <div class="product-discount">
                                <span class="regular-price">{$product.regular_price|escape:'htmlall':'UTF-8'}</span>
                                 {if $product.discount_type === 'percentage'}
                                    <span class="discount discount-percentage">-{$product.discount_percentage_absolute|escape:'htmlall':'UTF-8'}</span>
                                 {else}
                                    <span class="discount discount-amount">-{$product.discount_to_display|escape:'htmlall':'UTF-8'}</span>
                                 {/if}
                            </div>
                         {/if}
                        <div class="current-price">
                            <span class="price">{$product.price|escape:'htmlall':'UTF-8'}</span>
                             {if $product.unit_price_full}
                                <div class="unit-price-cart">{$product.unit_price_full|escape:'htmlall':'UTF-8'}</div>
                             {/if}
                        </div>
                    </td>
                    <td>
                         {foreach from=$product.attributes key="attribute" item="value"}
                            <div class="product-line-info">
                                <span class="label">{$attribute|escape:'htmlall':'UTF-8'}:</span>
                                <span class="value">{$value|escape:'htmlall':'UTF-8'}</span>
                            </div>
                         {/foreach}
                    </td>
                    <td>
                        <span class="quantity">{$product.quantity|escape:'htmlall':'UTF-8'}</span>
                    </td>
                    <td>
                        <span class="product-price"><strong> {if isset($product.is_gift) && $product.is_gift} <span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span> {else} {$product.total|escape:'htmlall':'UTF-8'} {/if}</strong></span>
                    </td>
                </tr>
                 {if is_array($product.customizations) && $product.customizations|count >1}
                    <hr>
                 {/if}
             {/foreach}
        </table>
     {/if}
</div>