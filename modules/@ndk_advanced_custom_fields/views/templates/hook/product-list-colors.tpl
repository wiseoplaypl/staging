{if $ps_version > 1.6}
	{assign var="base_dir_ssl" value=$urls.base_url}
	{assign var="base_dir" value=$urls.base_url}
{/if}
{if $color_fields.fields|@count > 0}
<div class="variant-links ndkcf-colors-variant">	
	{foreach from=$color_fields.fields item=$field}
		<ul class="ndk_color_list">
		{foreach from=$field.values item=value name='myLoop'}
			<li data-index="{$smarty.foreach.myLoop.index}" class="color-ndk-list colorize-ndk-list {if $field.is_visual == 1}visual-effect-list {/if}" data-default-value="{$value.default_value|intval}" data-target-product="{$target_product}" data-value="{$value.value|escape:'htmlall'}" title="{$value.value|escape:'htmlall'}"  data-src=""  
			data-color="{if $value.is_texture}{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/{$value.id|intval}-texture.jpg{else}{$value.color|escape:'htmlall'}{/if}"  
			  data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-blend="{$field.color_effect}">
				<span style="background:{if $value.is_texture}url('{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/thumbs/{$value.id|intval}-texture.jpg'){/if} {$value.color|escape:'htmlall'}">&nbsp;</span>
			</li>
		{/foreach}
		</ul>
	{/foreach}
	</div>
{/if}