{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}

<ul class="replace-img-block">
	{foreach from=$field.values item=value name=value}
		<li class="view_tab {if $value.issvg}svg {elseif $value.is3D} glb{else} jpg{/if}" data-id="{$field.id_ndk_customization_field|escape:'htmlall'}" data-view="{$value.id|intval}" 
		{if $ndkccpwebp && !$value.issvg && !$value.is3D}
			data-img="{include file="module:ndk_custom_product_page/views/templates/hook/image-display-by-path.tpl" path='img/scenes/ndkcf/'|cat:$value.id|cat:'.jpg' get_link='true'}"
		{else}
			data-img="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/{$value.id|intval}{if !$value.issvg}{/if}{if $value.issvg}.svg{elseif $value.is3D}.glb?v={$smarty.now|date_format:"%m-%d-%y-%H-%M"}{else}.jpg{/if}" 
		{/if}
		data-blend="{$field.color_effect}">{$value.value|escape:'htmlall'}
</li>
		<!--<div class="layers" data-view="{$value.id|intval}"></div>-->
		<input type="hidden" id="ndkcsfieldPdf_{$field.id_ndk_customization_field|intval}" name="ndkcsfieldPdf[{$field.id_ndk_customization_field|intval}]"/>
		{if $value.issvg}
		<div class="svg-container hidden" id="ndkcsfieldSVGView_{$value.id|intval}">{$value.svgcode nofilter}</div>
		{/if}
		
	{/foreach}
</ul>
{foreach from=$field.values item=value name=value}
	<input type="hidden" name="image-url[]" value="" id="image-url-{$value.id|intval}" class="image-url"/>
{/foreach}

