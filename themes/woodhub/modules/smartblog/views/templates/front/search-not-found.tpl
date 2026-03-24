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
<div id="pagenotfound" class="row">
	<div class="center_column col-xs-12 col-sm-12" id="center_column">
		<div class="pagenotfound">
			<h3>{l s='Sorry, but nothing matched your search terms.' d='ModulesSmartblogSearch-not-found'}</h3>
			<p>
				{l s='Please try again with some different keywords.' d='ModulesSmartblogSearch-not-found'}
			</p>
			<form class="std" method="post" action="{smartblog::GetSmartBlogLink('smartblog_search')|escape:'htmlall':'UTF-8'}">
				<fieldset>
					<div>
						<input type="text" class="form-control grey" value="{$smartsearch|escape:'htmlall':'UTF-8'}" name="smartsearch" id="search_query">
						<button class="btn btn-primary button button-small" value="{l s='Ok' d='ModulesSmartblogSearch-not-found'}" name="smartblogsubmit" type="submit"><span>{l s='Ok' d='ModulesSmartblogSearch-not-found'}</span></button>
					</div>
				</fieldset>
			</form>
			<div class="buttons">
				<a title="Home" href="{smartblog::GetSmartBlogLink('smartblog')|escape:'htmlall':'UTF-8'}" class="button button-medium">
					<span><i class="icon-chevron-left left"></i>{l s='Home page' d='ModulesSmartblogSearch-not-found'}</span>
				</a>
			</div>
		</div>
	</div>
</div>