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
<div class="block block-blog blogModule boxPlain clearfix" id="smartblogsearch">
  <h4 class="h6"><a href='{smartblog::GetSmartBlogLink('smartblog_list')}'>{l s='Blog Search' d='ModulesSmartblogsearchSmartblogsearch'}</a></h4>
  <div id="sdssearch_block_top" class="block_content">
    <form action="{smartblog::GetSmartBlogLink('smartblog_search')}" method="post" id="searchbox">
      <input type="hidden" value="0" name="smartblogaction">
      <input type="text" placeholder="Search" name="smartsearch" id="search_query_top" class="search_query form-control ac_input" autocomplete="off" value="{$smartsearch}">
      <button class="btn-blog-search" name="smartblogsubmit" type="submit">
      <i class="material-icons search"></i>
      <span>{l s='' d='ModulesSmartblogsearchSmartblogsearch'}</span>
      </button>
    </form>
  </div>
</div>




