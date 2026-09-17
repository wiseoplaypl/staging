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
{extends file='page.tpl'}
{block name='page_title'}
	{l s='Shopping Cart' d='Shop.Theme.Checkout'}
{/block}

{block name='content'}

  <section id="main">
    <div class="cart-grid row">

      <!-- Left Block: cart product informations & shpping -->
      <div class="cart-grid-body col-xs-12 col-lg-8">

        <!-- cart products detailed -->
        <div class="card cart-container">
          {block name='cart_overview'}
            {include file='checkout/_partials/cart-detailed.tpl' cart=$cart}
          {/block}
        </div>

        {block name='continue_shopping'}
          <a class="btn-primary btn-continue" href="{$urls.pages.index}">
           {l s='Continue shopping' d='Shop.Theme.Actions'}
          </a>
        {/block}

        <!-- shipping informations -->
        {block name='hook_shopping_cart_footer'}
          {hook h='displayShoppingCartFooter'}
        {/block}
      </div>

      <!-- Right Block: cart subtotal & cart total -->
      <div class="cart-grid-right col-xs-12 col-lg-4">

        {block name='cart_summary'}
          <div class="card cart-summary">

          <div class="humm-widget-box" id="humm-cart-box" style="display:none;border-style:solid;border-width:1px;border-color:#F0EEEB;padding:14px 14px;margin-bottom:20px;">
  <div id="flexifi-widget"></div>
</div>
<script>
(function () {
  var ID = '0011n00002eAdc1AAC', MIN = 1, MAX = 15000, last = null;
  var box = document.getElementById('humm-cart-box');
  if (!box) return;

  function total() {
    try { return parseFloat(prestashop.cart.totals.total.amount); }
    catch (e) { return NaN; }
  }

  function render() {
    var p = total();
    if (isNaN(p) || p < MIN || p > MAX) { box.style.display = 'none'; return; }
    if (p === last) return;
    last = p;
    box.innerHTML = '<div id="flexifi-widget"></div>';
    var s = document.createElement('script');
    s.src = 'https://d3v2ir16k1una.cloudfront.net/content/scripts/flexifi-widget.js'
          + '?id=' + ID
          + '&productPrice=' + p.toFixed(2)
          + '&element=%23flexifi-widget';
    s.setAttribute('data-min', MIN);
    s.setAttribute('data-max', MAX);
    document.body.appendChild(s);
    box.style.display = '';
  }

  function bind() {
    if (window.prestashop && prestashop.on) {
      prestashop.on('updateCart', function () { setTimeout(render, 150); });
      render();
    } else {
      setTimeout(bind, 200);
    }
  }

  render();
  bind();
})();
</script>


            {block name='hook_shopping_cart'}
              {hook h='displayShoppingCart'}
            {/block}

            {block name='cart_totals'}
              {include file='checkout/_partials/cart-detailed-totals.tpl' cart=$cart}
            {/block}

            {block name='cart_actions'}
              {include file='checkout/_partials/cart-detailed-actions.tpl' cart=$cart}
            {/block}

          </div>
        {/block}

        {block name='hook_reassurance'}
          {hook h='displayReassurance'}
        {/block}

      </div>

    </div>
  </section>
{/block}
