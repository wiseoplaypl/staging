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
{block name='header_banner'}
  <div class="header-banner">
    {hook h='displayBanner'}
  </div>
{/block}

<div class="header">
    {block name='header_nav'}
      <nav class="header-nav">
        <div class="container">
            <div class="header-nav-inner">
                <div class="header-nav-left hidden-sm-down">
                    {hook h='displayNav2'}
                </div>
                <div class="header-nav-right">
                    {hook h='displayNav1'}
                </div>
            </div>
        </div>
      </nav>
    {/block}

    {block name='header_top'}
        <div class="header-top">
            <div class="container">
                <div class="header-top-inner">
                    <div class="header-left hidden-md-down" id="_desktop_logo">
                        {if $page.page_name == 'index'}
                            <a href="{$urls.base_url}">
                                <img class="logo img-responsive" src="{$shop.logo}" alt="{$shop.name}">
                            </a>
                        {else}
                            <a href="{$urls.base_url}">
                                <img class="logo img-responsive" src="{$shop.logo}" alt="{$shop.name}">
                            </a>
                        {/if}
                    </div>
                    <div class="hidden-lg-up text-sm-center mobile">
                        <div class="top-logo" id="_mobile_logo"></div>
                    </div>
                    <div class="header-center">
                        {hook h='displayNav'}
                        {widget name='pm_advancedtopmenu'}

                    </div>
                    <div class="header-right">
                        <div class="header-right-innre">
                            {hook h='displayNav3'}
                            <div class="hidden-lg-up text-sm-center mobile">
                                <div class="float-xs-left" id="menu-icon">
                                    <i class="material-icons d-inline">&#xE5D2;</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="mobile_top_menu_wrapper" class="row hidden-lg-up" style="display:none;">
            <div class="menu-close">
              {l s='menu' d='Shop.Theme.Actions'}
              <i class="material-icons float-xs-right">close</i>
            </div>
            <div class="menu-tabs">
                <div class="js-top-menu mobile" id="_mobile_top_menu"></div>
                <div id="_mobile_verticalmenu"></div>
                <div class="js-top-menu-bottom">
                </div>
            </div>
        </div>

        {hook h='displayNavFullWidth'}
    {/block}
</div>
