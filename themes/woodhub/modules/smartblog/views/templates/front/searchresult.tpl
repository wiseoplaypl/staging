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
{block name='breadcrumb'}
  {if isset($breadcrumb)}
    <div class="container">
      <nav class="breadcrumb">
          <h2 class="page_title"></h2>
        <ol>
            <li>
              <a href="{$breadcrumb.links[0].url}">
                <span itemprop="name">{$breadcrumb.links[0].title}</span>
              </a>
            </li>
            <li>
              <a href="{smartblog::GetSmartBlogLink('smartblog')}">
              <span itemprop="name">{l s='All Post' d='ModulesSmartblogSearchresult'}</span>
              </a>
            </li>
            {if $title_category != ''}
            {assign var="link_category" value=null}
            {$link_category.id_category = $id_category}
            {$link_category.slug = $cat_link_rewrite}
            <li>
              <a href="{smartblog::GetSmartBlogLink('smartblog_category',$link_category)}">
              <span itemprop="name">{$title_category}</span>
              </a>
            </li>
          {/if}
        </ol>
      </nav>
    </div>
  {/if}
{/block}
{block name='page_content'}
    {if $postcategory == ''}
        {include file="module:smartblog/views/templates/front/search-not-found.tpl" postcategory=$postcategory}
    {else}
        <div id="smartblogcat" class="block clearfix">
          <div class="row">
            {foreach from=$postcategory item=post}
                {include file="module:smartblog/views/templates/front/category_loop.tpl" postcategory=$postcategory}
            {/foreach}
          </div>
        </div>
    {/if}
    {if isset($smartcustomcss)}
        <style>
            {$smartcustomcss|escape:'htmlall':'UTF-8'}
        </style>
    {/if}
{/block}