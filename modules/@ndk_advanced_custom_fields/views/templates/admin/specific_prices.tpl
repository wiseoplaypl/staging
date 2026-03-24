{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2019 Hendrik Masson
 *  @license   Tous droits réservés
*}
		{if (Tools::getValue('id_ndk_customization_field_value')) || Tools::getIsset('updatendk_customization_field')}
		<h4>{l s='specific prices'}</h4>
			<span class="addSpecificPrice"><i class="icon icon-plus"></i></span>
			<div class="clear clearfix specificPriceBlock specificPriceBlock_matrix" style="display:none"><form class="form-specific-price">
			<input type="hidden" name="specificprice[id_ndk_customization_field]" value="{Tools::getValue('id_ndk_customization_field')}"/><input class="id_specific_price" type="hidden" name="specificprice[id_ndk_customization_field_specific_price]" value="0"/><input type="hidden" name="specificprice[id_ndk_customization_field_value]" value="{Tools::getValue('id_ndk_customization_field_value')}"/><label>{l s='Reduction' mod='ndk_advanced_custom_fields'}</label><input class="price-input" type="text" size="6" name="specificprice[reduction]" value=""/><label>{l s='Reduction type' mod='ndk_advanced_custom_fields'}</label><select name="specificprice[reduction_type]"><option value="amount">{l s='amount' mod='ndk_advanced_custom_fields'}</option><option value="percent">{l s='percent' mod='ndk_advanced_custom_fields'}</option></select><label>{l s='From quantity' mod='ndk_advanced_custom_fields'}</label><input type="text" size="6" name="specificprice[from_quantity]" value=""/>
		
			<button class="submitSpecificPrice btn btn-default">{l s='save' mod='ndk_advanced_custom_fields'}</button></form><span class="removeSpecificPrice pull-right"><i class="icon icon-trash"></i></span></div>
		
			{$specificPrices = NdkCfSpecificPrice::getSpecificPrices(Tools::getValue('id_ndk_customization_field'), Tools::getValue('id_ndk_customization_field_value'))}
			
			{if ($specificPrices && sizeof($specificPrices) > 0)}
				{foreach $specificPrices as $row}	
				<div class="clear clearfix specificPriceBlock ">
					<form class="form-specific-price"><input type="hidden" name="specificprice[id_ndk_customization_field]" value="{$row['id_ndk_customization_field']}"/>
					<input class="id_specific_price" type="hidden" name="specificprice[id_ndk_customization_field_specific_price]" value="{$row['id_ndk_customization_field_specific_price']}"/>
					<input type="hidden" name="specificprice[id_ndk_customization_field_value]" value="{$row['id_ndk_customization_field_value']}"/>
					<label>{l s='Reduction' mod='ndk_advanced_custom_fields'}</label><input type="text" size="6" name="specificprice[reduction]" value="{$row['reduction']}"/>
					<label>{l s='Reduction type' mod='ndk_advanced_custom_fields'}</label>
					<select name="specificprice[reduction_type]">
						<option {if 'amount' == $row['reduction_type']}selected="selected"{/if} value="amount">{l s='amount' mod='ndk_advanced_custom_fields'}</option>
							<option {if 'percent' == $row['reduction_type']} selected="selected" {/if} value="percent">{l s='percent' mod='ndk_advanced_custom_fields'}</option>
					</select>
					<label>{l s='From quantity' mod='ndk_advanced_custom_fields'}</label>
					<input type="text" size="6" name="specificprice[from_quantity]" value="{$row['from_quantity']}"/>
					<button class="submitSpecificPrice btn btn-default">{l s='save' mod='ndk_advanced_custom_fields'}</button></form><span class="removeSpecificPrice pull-right"><i class="icon icon-trash"></i></span></div>
				{/foreach}
			{/if}
		{else}
			<span class="addSpecificPrice"><i class="icon icon-plus"></i></span><div class="clear clearfix specificPriceBlock specificPriceBlock_matrix" style="display:none">
			{l s='You have to save value before add specific prices.' mod='ndk_advanced_custom_fields'}</div>
		{/if}
