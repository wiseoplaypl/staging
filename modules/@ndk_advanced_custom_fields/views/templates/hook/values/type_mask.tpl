{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}

{foreach from=$field.values item=value name="mask"}
	{if $smarty.foreach.mask.last}
		<span class="hidden ndkmask" data-src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/{$value.id|intval}.jpg"  data-id="{$field.target|escape:'htmlall'}" data-field="{$field.id_ndk_customization_field|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-zindex="{$field.zindex|escape:'htmlall'}"></span>
	{/if}
{/foreach}