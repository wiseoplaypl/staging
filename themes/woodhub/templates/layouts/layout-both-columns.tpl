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
<!doctype html>
<html lang="{$language.iso_code}">

  <head>
    {block name='head'}
      {include file='_partials/head.tpl'}
    {/block}
  </head>

  <body id="{$page.page_name}" class="{$page.body_classes|classnames}">

    {block name='hook_after_body_opening_tag'}
      {hook h='displayAfterBodyOpeningTag'}
    {/block}
    <div class="page-loader" style="display:none"></div>
    <main id="page">
      {block name='product_activation'}
        {include file='catalog/_partials/product-activation.tpl'}
      {/block}

      <header id="header">
        {block name='header'}
          {include file='_partials/header.tpl'}
        {/block}
      </header>

      {block name='notifications'}
        {include file='_partials/notifications.tpl'}
      {/block}

      {if $page.page_name == 'index'}
        <div class="section-top">
            {if Context::getContext()->isMobile()}
                {hook h='displayTopColumnMobile'}
            {else}
                {hook h='displayTopColumn'}
            {/if}
        </div>
      {/if}

      <section id="wrapper">
        {hook h="displayWrapperTop"}
          {block name='breadcrumb'}
            {include file='_partials/breadcrumb.tpl'}
          {/block}
           {if $page.page_name != 'index'}
          <div class="page-content-wrapper">
            <div class="container">
          {/if}

          <div class="row">

          {block name="left_column"}
            <div id="left-column" class="col-xs-12 col-sm-4 col-md-3">
              {if $page.page_name == 'module-smartblog-details' || $page.page_name == 'module-smartblog-category' || $page.page_name == 'module-smartblog-archive' }
                {hook h='displaySmartBlogLeft'}
              {else}
                {hook h="displayLeftColumn"}
              {/if}
            </div>
          {/block}

          {block name="content_wrapper"}
            <div id="content-wrapper" class="left-column right-column col-lg-6 col-md-12 col-sm-12 col-xs-12">
              {hook h="displayContentWrapperTop"}
              {block name="content"}
                <p>Hello world! This is HTML5 Boilerplate.</p>
              {/block}
              {hook h="displayContentWrapperBottom"}
            </div>
          {/block}

          {block name="right_column"}
            <div id="right-column" class="col-md-3 col-sm-3 col-xs-12">
              {if $page.page_name == 'module-smartblog-details' || $page.page_name == 'module-smartblog-category' || $page.page_name == 'module-smartblog-archive' }
                {hook h='displaySmartBlogLeft'}
              {else}
                {hook h="displayRightColumn"}
              {/if}
            </div>
          {/block}

          </div>

         {if $page.page_name != 'index'}
          </div>
          </div>
        {/if}
        {hook h="displayWrapperBottom"}
      </section>

      <footer id="footer">
        {block name="footer"}
          {include file="_partials/footer.tpl"}
        {/block}
      </footer>

    </main>
    <a href="#" class="scrollToTop back-to-top">
    <i class="icon-chair1"></i>
    </a>
    {block name='javascript_bottom'}
      {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
    {/block}

    {block name='hook_before_body_closing_tag'}
      {hook h='displayBeforeBodyClosingTag'}
    {/block}
  </body>

</html>
