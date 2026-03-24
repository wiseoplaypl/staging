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

    {if !empty($smarty.get.resultsPerPage)}
        {assign var='results_per_page' value=$smarty.get.resultsPerPage}
    {else}
        {assign var='results_per_page' value=12}
    {/if}
{hook h='actionSortOrdersModify' listing=$listing}
<span class="col-sm-3 col-md-4 hidden-md-down sort-by">{l s='Sort by:' d='Shop.Theme.Global'}</span>
<div class="{if !empty($listing.rendered_facets)}col-sm-9 col-xs-8{else}col-sm-12 col-xs-12{/if} col-md-8 products-sort-order dropdown">
  <div class="products-sort-order-inner show">
    <button
      class="btn-unstyle select-title"
      rel="nofollow"
      data-toggle="dropdown"
      aria-haspopup="true"
      aria-expanded="false">
      {if isset($listing.sort_selected)}{$listing.sort_selected}{else}{l s='Select' d='Shop.Theme.Actions'}{/if}
      <i class="material-icons float-xs-right">&#xE5C5;</i>
    </button>
    <div class="dropdown-menu">
      {foreach from=$listing.sort_orders item=sort_order}
        {if $sort_order.urlParameter == 'product.sales.desc'}

        {/if}
        <a
          rel="nofollow"
          href="{$sort_order.url}"
          class="select-list {['current' => $sort_order.current, 'js-search-link' => true]|classnames}"
          {if $sort_order.urlParameter == 'product.sales.desc'}style="display:none"{/if}
        >
          {$sort_order.label}
        </a>
      {/foreach}
    </div>
  </div>
</div>

    <span class="col-sm-3 col-md-4 hidden-md-down per-page">{l s='Per page:' d='Shop.Theme.Global'}</span>
<div class="{if !empty($listing.rendered_facets)}col-sm-9 col-xs-8{else}col-sm-12 col-xs-12{/if} col-md-8 products-per-page products-sort-order dropdown">
          <button
      class="btn-unstyle select-title"
      rel="nofollow"
      data-toggle="dropdown"
      aria-haspopup="true"
      aria-expanded="false">
      {$results_per_page}       <i class="material-icons float-xs-right">&#xE5C5;</i>
    </button>
    {if !isset($ordering)}
        {assign var='ordering' value=''}
    {/if}
    <div class="dropdown-menu">
      <a rel="nofollow" href="?{$ordering}resultsPerPage=12" class="dropdown-item js-search-link">
            12
        </a>
      <a rel="nofollow" href="?{$ordering}resultsPerPage=24" class="dropdown-item js-search-link">
            24
        </a>
      <a rel="nofollow" href="?{$ordering}resultsPerPage=48" class="dropdown-item js-search-link">
            48
        </a>
      <a rel="nofollow" href="?{$ordering}resultsPerPage=96" class="dropdown-item js-search-link">
            96
        </a>
    </div>
  </div>
