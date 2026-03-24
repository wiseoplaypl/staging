{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}


<div class="form-group ndkackFieldItem recipient-group field-type-{$field.type}" data-iteration="{$field_iteration}"  data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-name="{$field.name|escape:'htmlall'}"  data-field="{$field.id_ndk_customization_field|escape:'htmlall'}" {foreach from=$field.options key=k item=v} data-{$k}="{$v}"{/foreach}>

	<label class="toggler {if !$field.is_picto}toggler-default-picto{/if}"
		{if $field.is_picto} style="background-image: url('{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/pictos/{$field.id_ndk_customization_field|intval}.jpg');"{/if}
	>{$field.name|escape:'htmlall'} 
	{if $field.show_price == 1}
		{if $fieldPricePerCaracter > 0}{l s='cost : ' mod='ndk_advanced_custom_fields'}{convertPrice price=$fieldPricePerCaracter} {l s='per caracter' mod='ndk_advanced_custom_fields'}
		{elseif $fieldPrice > 0}{l s='cost : ' mod='ndk_advanced_custom_fields'}{convertPrice price=$fieldPrice}{/if}
	{/if}
	{if $field.is_visual == 1}
		<span class="layer_view visible_layer" data-group="{$field.id_ndk_customization_field|intval}" data-zindex="{$field.zindex|escape:'htmlall'}" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}"/>&nbsp;</span>
	{/if}
	{if $field.tooltip !=''}
		<div class="tooltipDescription">{$field.tooltip nofilter}</div>
			<span class="tooltipDescMark"></span>
	{/if}
	</label>
	<div class="fieldPane clearfix">
		{if $field.notice !=''}
			<div class="field_notice clearfix clear">{$field.notice nofilter}</div>
		{/if}
			<div class="form_element">
				<label class="clearfix">{l s='Firstname'  mod='ndk_advanced_custom_fields'}</label>
				<input type="text" class="form-control recipient-field recipient-text {if $field.required == 1} required_field{/if}" prev-target="r-firstname" name="ndkcsfield[{$field.id_ndk_customization_field|intval}][recipient][firstname]"/>
			</div>
			<div class="form_element">
				<label class="clearfix">{l s='Lastname'  mod='ndk_advanced_custom_fields'}</label>
				<input type="text" class="form-control recipient-field recipient-text {if $field.required == 1} required_field{/if}" prev-target="r-lastname" name="ndkcsfield[{$field.id_ndk_customization_field|intval}][recipient][lastname]"/>
			</div>
			<div class="form_element">
				<label class="clearfix">{l s='Email'  mod='ndk_advanced_custom_fields'}</label>
				<input type="text" class="form-control recipient-field recipient-text {if $field.required == 1} required_field{/if}" name="ndkcsfield[{$field.id_ndk_customization_field|intval}][recipient][email]"/>
			</div>
			<div class="form_element">
				<label class="clearfix">{l s='Who offers'  mod='ndk_advanced_custom_fields'}</label>
				<input type="text" class="form-control recipient-field recipient-text {if $field.required == 1} required_field{/if}" prev-target="r-who_offers" name="ndkcsfield[{$field.id_ndk_customization_field|intval}][recipient][who_offers]"/>
			</div>
			<div class="form_element">
				<label class="clearfix">{l s='Your message'  mod='ndk_advanced_custom_fields'}</label>
				<textarea class="form-control recipient-field recipient-text {if $field.required == 1} required_field{/if}" prev-target="r-message" name="ndkcsfield[{$field.id_ndk_customization_field|intval}][recipient][message]"></textarea>
			</div>
			<div class="form_element">
				<label class="clearfix">{l s='Send to recipient '  mod='ndk_advanced_custom_fields'}</label>
				<p>
				<span>{l s='No' mod='ndk_advanced_custom_fields'}</span>
				<input type="radio" class="recipient-field {if $field.required == 1} required_field{/if}" name="ndkcsfield[{$field.id_ndk_customization_field|intval}][recipient][send_mail]" value="0" checked="checked"/><br>
				<i>{l s='Only you receive the gift voucher ' mod='ndk_advanced_custom_fields'}</i>
				</p>
				<p>
				<span>{l s='Yes ' mod='ndk_advanced_custom_fields' mod='ndk_advanced_custom_fields'}  </span>
				<input type="radio" class="recipient-field {if $field.required == 1} required_field{/if}" name="ndkcsfield[{$field.id_ndk_customization_field|intval}][recipient][send_mail]" value="1"/><br>
				<i>{l s='Both you and the recipient receive the gift voucher ' mod='ndk_advanced_custom_fields'}</i>
				</p>
			</div>

	</div>
</div>