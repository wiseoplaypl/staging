{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
<div id="_desktop_cart" class="desktop_cart">
  <div class="blockcart cart-preview {if $cart.products_count > 0}active{else}inactive{/if}" data-refresh-url="{$refresh_url}">
    <div class="header-cart" data-toggle="dropdown">
     {if $cart.products_count > 0}
        <a rel="nofollow" href="{$cart_url}">
      {else}
        <a rel="nofollow" href="javascript:void(0)">
      {/if}
        <i class="icon-supermarket"></i>
        <span class="cart-products-count">{$cart.products_count}</span>
        <div class="shopcart-inner hidden-xs-up">
          <span>{$cart.totals.total.value} </span>
        </div>
        </a>
    </div>

     {if $cart.products_count > 0}
      <div class="cart_block block exclusive dropdown-menu">
          <div class="cart_block_content">
              {foreach from=$cart.products item=product}
                  <div class="cart_block_item products">
                      {include 'module:ps_shoppingcart/ps_shoppingcart-product-line.tpl' product=$product}
                  </div>
              {/foreach}
          </div>
          <div class="cart-summary">
              <div class="cart-total">
                  <span class="label pull-left">{$cart.totals.total.label}</span>
                  <span class="value pull-right">{$cart.totals.total.value}</span>
              </div>
          </div>
          <div class="cart-button cart-action">
              <a class="cart-viewcart btn btn-primary" href="{$cart_url}">{l s='View Cart' d='Shop.Theme.Checkout'}</a>
              <a class="cart-checkout btn btn-primary" href="{$urls.pages.order}">{l s='Check Out' d='Shop.Theme.Actions'}</a>
          </div>
      </div> 
  {else}
      <div class="cart_block block exclusive">
          <p class="item-empty"> {l s='There are no items in your cart.' d='Shop.Theme.Checkout'}</p>
      </div>
  {/if}
  </div>
</div>



