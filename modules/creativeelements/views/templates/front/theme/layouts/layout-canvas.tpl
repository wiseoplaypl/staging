{**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 *}
<!doctype html>
<html lang="{$language.iso_code}">
<head>
	{block name='head'}{include file='_partials/head.tpl'}{/block}
</head>
<body id="{$page.page_name}" class="{$page.body_classes|classnames}">
	<main>
		{block name='notifications'}{include file='_partials/notifications.tpl'}{/block}
		{block name='content_wrapper'}{$ce_desc.description|cefilter}{/block}
	</main>
	{block name='javascript_bottom'}
	{if $smarty.const._PS_VERSION_|intval >= 8 && ('registration' === $page.page_name || 'password' === $page.page_name)}
		{if file_exists("{$smarty.const._PS_THEME_DIR_}templates/_partials/password-policy-template.tpl")}
			{include '[1]_partials/password-policy-template.tpl'}
		{elseif $smarty.const._PARENT_THEME_NAME_ && file_exists("{$smarty.const._PS_PARENT_THEME_DIR_}templates/_partials/password-policy-template.tpl")}
			{include 'parent:_partials/password-policy-template.tpl'}
		{else}
			{include file='components/password-policy-template.tpl'}
		{/if}
	{/if}
		{include file="_partials/javascript.tpl" javascript=$javascript.bottom}
	{/block}
</body>
</html>