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
 {if isset($posts) AND !empty($posts)}
<div id="recent_article_smart_blog_block_left"  class="block block-blog blogModule boxPlain">
   <h4 class="h6"><a href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='Recent Articles' d='ModulesSmartblogrecentpostsSmartblogrecentposts'}</a></h4>
  <div class="block_content sdsbox-content">
    <ul class="recentArticles">
    {foreach from=$posts item="post"}
      <li>
        <a class="image" title="{$post.meta_title}" href="{$smartbloglink->getSmartBlogPostLink($post.id_smart_blog_post,$post.link_rewrite)}">
          <img alt="{$post.meta_title}" src="{if $smartbloglink->getImageLink($post.link_rewrite, $post.id_smart_blog_post, 'home-small') != 'false'}{$smartbloglink->getImageLink($post.link_rewrite, $post.id_smart_blog_post, 'home-small')}{/if}" style="overflow: hidden;">
        </a>
        <a class="title"  title="{$post.meta_title}" href="{$smartbloglink->getSmartBlogPostLink($post.id_smart_blog_post,$post.link_rewrite)}">{$post.meta_title}</a>
        <span class="info">{$post.created}</span>
      </li>
    {/foreach}
    </ul>
  </div>
</div>
{/if}