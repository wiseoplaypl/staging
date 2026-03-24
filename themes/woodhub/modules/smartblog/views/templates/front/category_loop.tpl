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
<div class="blog-block col-md-6 col-xs-12">
    <div itemtype="#" itemscope="" class="sdsarticleCat clearfix">
        <div id="smartblogpost-{$post.id_post|escape:'htmlall':'UTF-8'}">
            <div class="articleContent post-thumbnail">
                {if isset($ispost) && !empty($ispost)}
                <a itemprop="url" href="{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.cat_link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$post.meta_title|escape:'htmlall':'UTF-8'}" class="imageFeaturedLink"> 
                {/if}
                {if $smartbloglink->getImageLink($post.link_rewrite, $post.id_post, 'single-default') != 'false'}
                    <img itemprop="image" alt="{$post.meta_title|escape:'htmlall':'UTF-8'}" src="{$smartbloglink->getImageLink($post.link_rewrite, $post.id_post, 'single-default')}" class="imageFeatured">
                {/if} 
                {if isset($ispost) && !empty($ispost)}
                </a>
                {/if} 
                <p class='title_block'>
                    <a title="{$post.meta_title|escape:'htmlall':'UTF-8'}" href="{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}">
                    {$post.meta_title|escape:'htmlall':'UTF-8'}
                    </a>
                </p>
            </div>
            <div class="article-inner-wrapper">
                <div class="article-inner">
                    <div class="sdsarticleHeader">
                        <div class="post_meta clearfix">
                             {if $smartshowauthor ==1}
                                    <div class="meta_author">
                                        <i class="fa fa-user"></i>
                                        <span>
                                            {if $smartshowauthorstyle != 0}
                                                {$post.firstname|escape:'htmlall':'UTF-8'}
                                                {$post.lastname|escape:'htmlall':'UTF-8'}{else}{$post.lastname|escape:'htmlall':'UTF-8'} 
                                                {$post.firstname|escape:'htmlall':'UTF-8'}
                                            {/if}
                                        </span> 
                                    </div>
                                {/if}
                                {$assocCats = BlogCategory::getPostCategoriesFull($post.id_post)}
                                {$catCounts = 0}
                                {if !empty($assocCats)}
                                    <div class="meta_tag">
                                        <i class="fa fa-tag"></i>
                                        <span>
                                            {foreach $assocCats as $catid=>$assoCat}
                                                {if $catCounts > 0}, {/if}
                                                {$catlink=[]}
                                                {$catlink.id_category = $assoCat.id_category}
                                                {$catlink.slug = $assoCat.link_rewrite}
                                                <a href="{$smartbloglink->getSmartBlogCategoryLink($assoCat.id_category,$assoCat.link_rewrite)|escape:'htmlall':'UTF-8'}">
                                                    {$assoCat.name|escape:'htmlall':'UTF-8'}
                                                </a>
                                                {$catCounts = $catCounts + 1}
                                            {/foreach}
                                        </span>
                                    </div>
                                {/if}
                                <div class="meta_comment">
                                    <i class="fa fa-comments-o"></i>
                                    <a href="{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}#articleComments" title="{$post.totalcomment|escape:'htmlall':'UTF-8'} Comments"> 
                                        {$post.totalcomment} {l s=' Comments' d='ModulesSmartblogCategory_loop'}
                                    </a>
                                </div>
                                <div class="meta_view">
                                    {if $smartshowviewed ==1}<i class="fa fa-eye"></i> {$post.viewed|intval}{/if}
                                </div>
                        </div>
                        
                    </div> 
                    <div class="sdsarticle-des">
                        {$post.short_description}
                    </div>
                    <div class="sdsreadMore">
                        <a title="{$post.meta_title|escape:'htmlall':'UTF-8'}" href="{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}" class="blog-read btn btn-primary">
                            <span>{l s='Read more' d='ModulesSmartblogCategory_loop'}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>