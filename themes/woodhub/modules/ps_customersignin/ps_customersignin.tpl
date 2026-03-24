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
<div id="_desktop_user_info" class="user-info dropdown js-dropdown">
  <button data-target="#" data-toggle="dropdown" class="btn-unstyle" aria-haspopup="true" aria-expanded="false" aria-label="{l s='Account' d='Shop.Theme.Customeraccount'}">
    <i class="icon-user"></i>
    <span class="expand-more  hidden-xs-up">{l s='My Account' d='Shop.Theme.Customeraccount'}</span>
    <i class="fa fa-angle-down  hidden-xs-up"></i>
  </button>
  <ul class="dropdown-menu" aria-labelledby="user-info-label">
    {if $logged}
      <li><a class="dropdown-item" href="{$my_account_url}" title="{l s='View my customer account' d='Shop.Theme.Customeraccount'}" rel="nofollow">{l s='My Account' d='Shop.Theme.Customeraccount'}</a></li>
      <li><a class="dropdown-item" href="{$urls.pages.identity}" title="{l s='Information' d='Shop.Theme.Customeraccount'}" rel="nofollow">{l s='Information' d='Shop.Theme.Customeraccount'}</a></li>
      {if $customer.addresses|count}
      <li><a class="dropdown-item" href="{$urls.pages.addresses}" title="{l s='Addresses' d='Shop.Theme.Customeraccount'}" rel="nofollow">{l s='Addresses' d='Shop.Theme.Customeraccount'}</a></li>
      {else}
      <li><a class="dropdown-item" href="{$urls.pages.address}" title="{l s='Add first address' d='Shop.Theme.Customeraccount'}" rel="nofollow">{l s='Add first address' d='Shop.Theme.Customeraccount'}</a></li>
      {/if}
      {if !$configuration.is_catalog}
      <li><a class="dropdown-item" href="{$urls.pages.history}" title="{l s='Order details' d='Shop.Theme.Customeraccount'}" rel="nofollow">{l s='Order details' d='Shop.Theme.Customeraccount'}</a></li>
      {/if}
      {hook h='displayMyAccountBlock'}
      <li><a class="dropdown-item" href="{$logout_url}" title="{l s='Sign out' d='Shop.Theme.Actions'}" rel="nofollow">{l s='Sign out' d='Shop.Theme.Actions'}</a></li>
    {else}
      <li><a class="dropdown-item" href="{$my_account_url}" title="{l s='Log in to your customer account' d='Shop.Theme.Customeraccount'}" rel="nofollow">{l s='Sign in' d='Shop.Theme.Actions'}</a></li>
      <li><a class="dropdown-item" href="{$urls.pages.register}" title="{l s='Register your new customer account' d='Shop.Theme.Customeraccount'}" rel="nofollow">{l s='Register' d='Shop.Theme.Actions'}</a></li>
    {/if}
  </ul>
</div>
