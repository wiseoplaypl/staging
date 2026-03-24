{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2017 Hendrik Masson
 *  @license   Tous droits réservés
*}

<div data-group-target="{$field.id_ndk_customization_field|intval}" data-name="{$field.name|escape:'htmlall'}" class="clear clearfix orientation_selection">
	<p class="orientation-title">{l s='Choose orientation' mod='ndk_advanced_custom_fields'}</p>
	<input type="hidden" class="orientation_input" name="ndkcsfield[orientation][{$field.id_ndk_customization_field|intval}]" value=""/>
	<span class="orientation-btn pull-left btn btn-default active_orientation " data-orientation="standard-orientation">{l s='Standard' mod='ndk_advanced_custom_fields'}</span>
	<span class="orientation-btn pull-right btn btn-default " data-orientation="reverse-orientation">{l s='Reverse' mod='ndk_advanced_custom_fields'}</span>
</div>