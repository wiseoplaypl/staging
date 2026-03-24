{**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 *}

{extends file='helpers/options/options.tpl'}

{block name='input' prepend}
	{if 'button' === $field.type}
		<div class="col-lg-9">
			<a class="btn btn-default fixed-width-xxl" href="{$field.href}">
				{if isset($field.icon)}<i class="icon-{$field.icon}"></i>&ensp;{/if}{$field.label}
			</a>
		</div>
	{/if}
{/block}