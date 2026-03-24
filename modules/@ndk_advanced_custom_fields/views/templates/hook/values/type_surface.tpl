{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}

	{foreach from=$field.values item=value name='mesures'}
		{if $smarty.foreach.mesures.index < 2}
		<p>
			<label class="clear clearfix">{$value.value|escape:'htmlall'} : </label>
			<input id="ndkcsfield_{$field.id_ndk_customization_field|intval}_{$value.id|intval}" data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$value.value|escape:'htmlall'}"  placeholder="{$value.value|escape:'htmlall'}" name="ndkcsfield[{$field.id_ndk_customization_field|intval}][surface][{$value.id|escape:'htmlall'}]" data-val="{$smarty.foreach.mesures.index}" data-group="{$field.id_ndk_customization_field|intval}" data-price="{$fieldPrice|escape:'htmlall'}" type="text" class="form-control surface surface_{$field.id_ndk_customization_field|intval} {if $field.required == 1} required_field{/if}" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" 
			 data-preserve-ratio="{$field.preserve_ratio}" step="{$value.step_quantity}"
			{if $value.quantity_max > 0}
			data-qtty-max="{$value.quantity_max}" max="{$value.quantity_max}" 
			{/if}
			{if $value.quantity_min > 0}
			data-qtty-min="{$value.quantity_min}"  min="{$value.quantity_min}"
			{/if}
			data-step_quantity="" size="8"
			/>
			<span class="quantity-ndk-minus btn-default btn"  data-target-class="surface"><i class="icon-minus"></i></span>
			<span class="quantity-ndk-plus btn-default btn" data-target-class="surface"><i class="icon-plus"></i></span>
		</p>
		{/if}
	{/foreach}
