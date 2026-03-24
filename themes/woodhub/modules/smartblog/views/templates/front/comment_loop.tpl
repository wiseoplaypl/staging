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

{if $comment.id_smart_blog_comment != ''}
  <ul class="commentList">
    <div id="comment-{$comment.id_smart_blog_comment|intval}">
      <li class="even">
        <div class="comm-des">
          <div class="comment-icon">
            <img class="avatar hidden-xs-up" alt="Avatar" src="{$modules_dir|escape:'htmlall':'UTF-8'}/smartblog/images/avatar/avatar-author-default.jpg">
            <i class="fa fa-user"></i>
          </div>
          <div class="comment-info">
            <div class="comment-title">
              <div class="name">{$childcommnets.name|escape:'htmlall':'UTF-8'}</div>
              <div class="created">
               <span itemprop="commentTime">{$childcommnets.created|date_format|escape:'htmlall':'UTF-8'}</span>
              </div>
            </div>
            <p>{$childcommnets.content nofilter}</p>
            {if Configuration::get('smartenablecomment') == 1}
            {if $comment_status == 1}
              <div class="reply">
                <a onclick="return addComment.moveForm('comment-{$comment.id_smart_blog_comment|escape:'htmlall':'UTF-8'}', '{$comment.id_smart_blog_comment|escape:'htmlall':'UTF-8'}', 'respond', '{$comment.id_post|intval}')"  class="comment-reply-link">{l s='Reply' d='ModulesSmartblogComment_loop'}</a>
              </div>
            {/if}
          {/if}
        </div>
        </div>
        
        {if isset($childcommnets.child_comments)}
          {foreach from=$childcommnets.child_comments item=comment}
            {if isset($childcommnets.child_comments)}
              {include file="module:smartblog/views/templates/front/comment_loop.tpl" childcommnets=$comment}

              {$i=$i+1}

            {/if}
          {/foreach}
        {/if}
      </li>
    </div>
  </ul>
{/if}
                                        
                                        