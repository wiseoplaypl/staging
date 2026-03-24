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
        <ol class="">
            <li>
              <a href="{$breadcrumb.links[0].url}">
                <span itemprop="name">{$breadcrumb.links[0].title}</span>
              </a>
            </li>
            <li>
              <a href="{smartblog::GetSmartBlogLink('smartblog')}">
              <span itemprop="name">{l s='All Post' d='ModulesSmartblogPostcategory'}</span>
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
    {capture name=path}
      <a href="{smartblog::GetSmartBlogLink('smartblog')|escape:'htmlall':'UTF-8'}">{l s='All Blog News' d='ModulesSmartblogPostcategory'}</a>
      {if $title_category != ''}<span class="navigation-pipe"></span>{$title_category|escape:'htmlall':'UTF-8'}{/if}
    {/capture}
    {if $postcategory == ''}
        {if $title_category != ''}
              <div class="alert alert-danger"><p>There is 1 error</p><ol><li>{l s='No Post in Category' d='ModulesSmartblogPostcategory'}</li></ol></div>
        {else}
          <div class="alert alert-danger"><p>There is 1 error</p><ol><li>{l s='No Post in Blog' d='ModulesSmartblogPostcategory'}</li></ol></div>
        {/if}
    {else}
      {if $smartdisablecatimg == '1'}
        {assign var="activeimgincat" value='0'}
          {$activeimgincat = $smartshownoimg} 
          {if $title_category != ''}        
            {foreach from=$categoryinfo item=category}
              <div id="sdsblogCategory">  
                {if $cat_image == "no" } 
                {else} 
                  {if ($cat_image != "no" && $activeimgincat == 0) || $activeimgincat == 1}
                    <img alt="{$category.meta_title|escape:'htmlall':'UTF-8'}" src="{$cat_image}" class="imageFeatured">
                  {/if}
                {/if}
                {$category.description}
              </div>
            {/foreach}  
          {/if}
        {/if}
        <div id="smartblogcat" class="block">
          <div class="row">
            {foreach from=$postcategory item=post}
              {include file="module:smartblog/views/templates/front/category_loop.tpl" postcategory=$postcategory}
            {/foreach}
          </div>
        </div>
        {if !empty($pagenums)}
          <div class="row bottom-pagination-content">
            <div class="post-page col-md-12">
              <div id="pagination_bottom" class="col-md-6">
                <ul class="pagination">
                  {for $k=0 to $pagenums} 
                    {if ($k+1) == $c}
                      <li><span class="page-link page-active"><span>{$k+1|escape:'htmlall':'UTF-8'}</span></span></li>
                    {else}
                      {if $title_category != ''}
                        <li><a class="page-link" href="{$smartbloglink->getSmartBlogCategoryPagination($id_category,$cat_link_rewrite,$k+1)|escape:'htmlall':'UTF-8'}"><span>{$k+1|escape:'htmlall':'UTF-8'}</span></a></li> 
                      {else}
                          <li><a class="page-link" href="{$smartbloglink->getSmartBlogListPagination($k+1)|escape:'htmlall':'UTF-8'}"><span>{$k+1|escape:'htmlall':'UTF-8'}</span></a></li>
                      {/if}
                    {/if}
                  {/for}
                </ul>
              </div>
            </div>
            <div class="col-md-6">
              <div class="results">
                {l s='Showing' d='ModulesSmartblogPostcategory'} {if $limit_start!=0}{$limit_start|escape:'htmlall':'UTF-8'}{else}1{/if} {l s='to' d='ModulesSmartblogPostcategory'} {if $limit_start+$limit >= $total}{$total|escape:'htmlall':'UTF-8'}{else}{$limit_start+$limit|escape:'htmlall':'UTF-8'}{/if} {l s='of' d='ModulesSmartblogPostcategory'} {$total|escape:'htmlall':'UTF-8'} ({$c|escape:'htmlall':'UTF-8'} {l s='Pages' d='ModulesSmartblogPostcategory'})
              </div>
            </div>
          </div>
        {/if}
      {/if}
      {if isset($smartcustomcss)}
        <style>
          {$smartcustomcss|escape:'htmlall':'UTF-8'}
        </style>
      {/if}
{/block}