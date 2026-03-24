{**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 *}
<section id="ce-overview" class="panel widget">
	<div class="panel-heading">
		<i class="icon-bell"></i> Creative Elements
		<span class="panel-heading-action">
			<a class="list-toolbar-btn" href="http://docs.webshopworks.com/creative-elements" target="_blank" title="{l s='Help' d='Admin.Global'}">
				<i class="process-icon-help"></i>
			</a>
		</span>
	</div>

	<div class="form-group clearfix">
		<div class="pull-left">
			<img src="{$smarty.const._CE_URL_}logo.png" width="32" height="32" alt="CE" style="display: inline">
			<span style="display: inline-block; vertical-align: bottom;">
				v{$current_version}<br>
			{if $update_url}
				<a href="{$update_url}" class="label label-{$version_status}" title="v{$latest_version}" style="font-size: 10px; vertical-align: 1px">{$version_status_label}</a>
			{else}
				<span class="label label-{$version_status}" style="font-size: 10px; vertical-align: 1px">{$version_status_label}</span>
			{/if}
			</span>
		</div>
	{if $addcms_url}
		<div class="pull-right">
			<a class="btn btn-default" href="{$addcms_url}"><i class="icon-plus"></i> {l s='Add new page' d='Admin.Design.Help'}</a>
		</div>
	{/if}
	</div>

{if $recent_items}
	<section id="ce-overview__recent">
		<header class="ce-overview__heading"><i class="icon-pencil"></i> {CE\__('Recently Edited')}</header>
		<ul class="data_list_large">
		{foreach $recent_items as $item}
			<li{if $smarty.const._PS_VERSION_|intval < 9} class="clearfix"{/if}>
				<a class="data_label size_s" href="{$editor_url}&amp;uid={$item.id}">
					{$item.type} / {$item.title}
					{if $multilang && $item.lang}<span class="label label-default" style="display: inline-block; font-size: 9px; vertical-align: 1px">{$item.lang}</span>{/if}
				</a>
				<span class="pull-right">{$item.date}</span>
			</li>
		{/foreach}
		</ul>
	</section>
{/if}

{if $feed_data}
	<section id="ce-overview__feed">
		<header class="ce-overview__heading"><i class="icon-bullhorn"></i> {CE\__('News & Updates')}</header>
	{foreach $feed_data as $i => $feed_item}
		{if $i}<hr>{/if}
		<article class="ce-overview__post">
			<h4 class="ce-overview__post-title">
				<a class="ce-overview__post-link" href="{$feed_item.url}" target="_blank">
					{if $feed_item.badge}<span class="label label-success" style="display:inline-block; margin-inline-end:5px; vertical-align: 1px">{$feed_item.badge}</span>{/if}{$feed_item.title}
				</a>
			</h4>
			{if $feed_item.excerpt}<p class="ce-overview__post-description">{$feed_item.excerpt}</p>{/if}
		</article>
	{/foreach}
	</section>
{/if}
</section>