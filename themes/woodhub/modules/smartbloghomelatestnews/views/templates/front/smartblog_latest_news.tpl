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
<div class="section-margin-top latest-post-section">
    <div class="container">
        <h2 class='h2 products-section-title'><span>{l s='Latest News' d='ModulesSmartblogSmartblog_latest_news'}</span>
        </h2>
        <div class="row">
            <div class="blog-item blog-item-3 owl-carousel owl-theme">
            {if isset($view_data) AND !empty($view_data)}
                {foreach from=$view_data item=post}
                    {assign var='img_url' value=$smartbloglink->getImageLink($post.link_rewrite, $post.id, 'home-default')}
                    <div class="latest-post-item col-xs-12">
                        <div class="latest-post-inner clearfix">
                            <div class="post-images-wrapper">
                            {if $img_url != 'false'}
                                <a href="{$smartbloglink->getSmartBlogPostLink($post.id,$post.link_rewrite)}">
                                    <img class="replace-2x img-responsive" src="{$img_url}" alt="{$post.title|escape:'html':'UTF-8'}" title="{$post.title|escape:'html':'UTF-8'}"/>
                                </a>
                                <div class="blog_hover">
                                    <a class="hover-zoom" href="{$img_url}" data-lightbox="example-set" title="{$post.title|escape:'html':'UTF-8'}"><i class="fa fa-search"></i></a>
                                    <a class="hover-post" href="{$smartbloglink->getSmartBlogPostLink($post.id,$post.link_rewrite)}"><i class="fa fa-link"></i></a>
                                </div>
                            {/if}
                            <h5 class="post-title">
                                <a href="{$smartbloglink->getSmartBlogPostLink($post.id,$post.link_rewrite)}">{SmartBlogPost::subStr($post.title,60)}</a>
                            </h5>
                            </div>
                            <div class="latest-post-content">
                                <div class="latest-post-content-inner">
                                    <div class="blog-date">
                                        <span class="blog-day">{$post.date_added|date_format:"%d"}</span>
                                        <div class="blog-date-inner">
                                            <span class="blog-month">{$post.date_added|date_format:"%B"}</span>
                                            <span class="blog-year">{$post.date_added|date_format:"%Y"}</span>
                                        </div>
                                    </div>
                                    <div class="blog-infodesc">
                                        <div class="blog-description-content">
                                            {SmartBlogPost::subStr($post.short_description,70)}
                                        </div>
                                    </div>
                                </div>
                                <p class="link-more">
                                    <a href="{$smartbloglink->getSmartBlogPostLink($post.id,$post.link_rewrite)}" class="blog-read btn btn-primary">{l s='Read More' d='ModulesSmartblogSmartblog_latest_news'}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                {/foreach}
            {/if}
            </div>
        </div>
    </div>
</div>