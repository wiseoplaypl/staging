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
{if isset($latesComments) AND !empty($latesComments)}
<div class="block block-blog blogModule boxPlain">
  <h4 class="h6">{l s='Latest Comments' d='ModulesSmartbloglatestcommentsSmartbloglatestcomments'}</h4>
  <div class="block_content sdsbox-content">
    <ul class="recentComments">
    {foreach from=$latesComments item="comment"}
      <li>
        <a class="image" href="{$smartbloglink->getSmartBlogPostLink($comment.id_post,$comment.link_rewrite)}">
          <img alt="Avatar" src="{$modules_dir}/smartblog/images/avatar/avatar-author-default.jpg">
        </a>
        <div class="blog-side-content">
          {$comment.name} <i>{l s='on' d='ModulesSmartbloglatestcommentsSmartbloglatestcomments'}</i>
          <a class="title" href="{$smartbloglink->getSmartBlogPostLink($comment.id_post,$comment.link_rewrite)}">
          {SmartBlogPost::subStr($comment.content,30) nofilter}
        </a>
        </div>
      </li>
    {/foreach}
    </ul>
  </div>
</div>
{/if}