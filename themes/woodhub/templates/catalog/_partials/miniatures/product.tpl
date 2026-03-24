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
{block name='product_miniature_item'}
  <article class="product-miniature js-product-miniature product-grid {if $page.page_name == 'index' || $page.page_name == 'cart'} col-xl-3 col-lg-3 col-md-4 {else} col-xl-4 col-lg-4 col-md-4 {/if}col-sm-6 col-xs-12" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
    <div class="product-item">
      <div class="product-image-wraper">
        {block name='product_thumbnail'}
          {if $product.cover}
            <a href="{$product.url}" class="thumbnail product-thumbnail">
              <span class="cover_image">
                <img
                  src = "{$product.cover.bySize.home_default.url}"
                  alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
                  data-full-size-image-url = "{$product.cover.large.url}"
                  width="360"
                  height="480"
                  loading="lazy"
                >
              </span>
              {if isset($product.images[1])}
                <span class="hover_image">
                    <img
                        src = "{$product.images[1].bySize.home_default.url}"
                        data-full-size-image-url = "{$product.images[1].bySize.home_default.url}"
                        alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
                      {*
                        {if isset($size_home_default.width)}width="{$size_home_default.width}"{/if}
                        {if isset($size_home_default.height)}height="{$size_home_default.height}"{/if}
                       *}
                        width="360"
                        height="480"
                        loading="lazy"
                        >
                </span>
              {/if}
            </a>
            {else}
              <a href="{$product.url}" class="thumbnail product-thumbnail">
                <img
                  src = "{$urls.no_picture_image.bySize.home_default.url}" width="360" height="480"
                >
              </a>
          {/if}
        {/block}

         {block name='product_flags'}
          <ul class="product-flags">
            {foreach from=$product.flags item=flag}
              <li class="product-flag {$flag.type}">{$flag.label}</li>
            {/foreach}
          </ul>
        {/block}

        <div class="prdouct-btn-wrapper grid">
          <div class="product-add-to-cart product-btn-icon grid-add">
            <form class="btn-add-to-cart" action="{$urls.pages.cart}" method="post">
              <input type="hidden" name="token" value="{$static_token}">
              <input type="hidden" name="id_product" value="{$product.id}">
              <button class="add-to-cart btn" data-button-action="add-to-cart" type="submit">
                <span>{l s='Add to cart' d='Shop.Theme.Actions'}</span>
              </button>
            </form>
          </div>
          {block name='quick_view'}
              <div class="product-btn-icon-quickview product-btn-icon">
                <a class="quick-view" href="#" data-link-action="quickview">
                  <i class="icon-eye" aria-hidden="true"></i>
                </a>
              </div>
          {/block}
          {hook h='displayCompareButton' product=$product}
        </div>
        {if $page.page_name != 'index' || !Context::getContext()->isMobile()}
        <div class="cart-price-block-grid">
          {block name='product_price_and_shipping'}
            {if $product.show_price}
                        {if !$product.has_discount}
                {hook h='displayProductPriceBlockNoDiscountBefore' product=$product}
            {/if}
              <div class="product-price-and-shipping">
                <span class="sr-only">{l s='Price' d='Shop.Theme.Catalog'}</span>
                <span itemprop="price" class="price">{$product.price}</span>
                {if $product.has_discount}
                  {hook h='displayProductPriceBlock' product=$product type="old_price"}

                  <span class="sr-only">{l s='Regular price' d='Shop.Theme.Catalog'}</span>
                  <span class="regular-price">{$product.regular_price}</span>
                  {if $product.discount_type === 'percentage'}
                    <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
                  {elseif $product.discount_type === 'amount'}
                    <span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
                  {/if}
                {/if}

                {hook h='displayProductPriceBlock' product=$product type="before_price"}

                {hook h='displayProductPriceBlock' product=$product type='unit_price'}

                {hook h='displayProductPriceBlock' product=$product type='weight'}
              </div>
            {/if}
          {/block}
        </div>
        {/if}

    </div>

      <div class="product-description">
        <div class="products-decs">
        {block name='product_name'}
          {if $page.page_name == 'index'}
            <h3 class="product-title" itemprop="name"><a href="{$product.url}">{$product.name|truncate:50:'...'}</a></h3>
          {else}
            <h3 class="product-title" itemprop="name"><a href="{$product.url}">{$product.name|truncate:50:'...'}</a></h3>
          {/if}
        {/block}
{if $page.page_name != 'index' || !Context::getContext()->isMobile()}

        {hook h='displayProductListFunctionalButtons' product=$product}
{/if}
        <div class="cart-price-block-list">
          {block name='product_price_and_shipping'}
            {if $product.show_price}
              <div class="product-price-and-shipping">
                <span class="sr-only">{l s='Price' d='Shop.Theme.Catalog'}</span>
                <span itemprop="price" class="price">{$product.price}</span>
                {if $product.has_discount}
                  {hook h='displayProductPriceBlock' product=$product type="old_price"}

                  <span class="sr-only">{l s='Regular price' d='Shop.Theme.Catalog'}</span>
                  <span class="regular-price">{$product.regular_price}</span>
                  {if $product.discount_type === 'percentage'}
                    <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
                  {elseif $product.discount_type === 'amount'}
                    <span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
                  {/if}
                {/if}

                {hook h='displayProductPriceBlock' product=$product type="before_price"}

                {hook h='displayProductPriceBlock' product=$product type='unit_price'}

                {hook h='displayProductPriceBlock' product=$product type='weight'}
              </div>
            {/if}
          {/block}
        </div>
        {if $page.page_name != 'index' || !Context::getContext()->isMobile()}
            {block name='product_reviews'}
                <div class="grid-reviews">
                    {hook h='displayProductListReviews' product=$product}
                  </div>
                {/block}

                {block name='product_reviews'}
                  <div class="list-reviews">
                    {hook h='displayProductListReviews' product=$product}
                  </div>
                {/block}

                {block name='product_description_short'}
                  <div class="product-desc" itemprop="description">{* $product.description_short nofilter *}</div>
                {/block}

                {block name='product_variants'}
                  <div class="highlighted-informations{if !$product.main_variants} no-variants{/if} hidden-xs-up">
                     {if $product.main_variants}
                        {include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
                     {/if}
                  </div>
                {/block}
        {/if}
      </div>
{if $page.page_name != 'index' || !Context::getContext()->isMobile()}
      <div class="prdouct-btn-wrapper list">
        <div class="product-add-to-cart product-btn-icon">
          <form class="btn-add-to-cart" action="{$urls.pages.cart}" method="post">
            <input type="hidden" name="token" value="{$static_token}">
            <input type="hidden" name="id_product" value="{$product.id}">
            <button class="add-to-cart btn" data-button-action="add-to-cart" type="submit">
              <span>{l s='Add to cart' d='Shop.Theme.Actions'}</span>
            </button>
          </form>
        </div>
        <div class="prdouct-btn-wrapper-inner list">
          {hook h='displayProductListFunctionalButtons' product=$product}
          {block name='quick_view'}
              <div class="product-btn-icon-quickview product-btn-icon">
                <a class="quick-view" href="#" data-link-action="quickview">
                  <i class="icon-eye" aria-hidden="true"></i>
                </a>
              </div>
          {/block}
          {hook h='displayCompareButton' product=$product}
        </div>
      </div>
      {/if}
      </div>
    </div>
  </article>
{/block}
