{**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 *}
{if isset($CE_PAGE_MANUFACTURERS)}
    {$ce_layout=$layout}
{elseif file_exists("{$smarty.const._PS_THEME_DIR_}templates/catalog/manufacturers.tpl")}
    {$ce_layout='[1]catalog/manufacturers.tpl'}
{elseif $smarty.const._PARENT_THEME_NAME_}
    {$ce_layout='parent:catalog/manufacturers.tpl'}
{/if}

{extends $ce_layout}

{if isset($CE_PAGE_MANUFACTURERS)}
    {block name='content'}<section id="content">{$CE_PAGE_MANUFACTURERS|cefilter}</section>{/block}
{/if}