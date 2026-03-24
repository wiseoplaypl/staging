{if Configuration::get('NDK_LOAD_ACCESSORIES_FIELDS') == 1 && $product.id_product != $product_id}
<div class="clear clearfix a_c_m_b_separator">
{assign var ='productFields' value=NdkCf::getCustomFields($product.id_product, $product.id_category_default)}
	{if $productFields.fields|@count > 0}
 		<a id="t_a_c_{$value.id}" href="#a_c_m_b_{$value.id}" class="btn btn-default toggleAccessoriesCutomization" style="display: none;" data-value-id="{$value.id}">{l s='customize your accessories'}</a>
 		<div id="a_c_m_b_{$value.id}" class="accessory_customization_main_block clearfix" style="display: none;">
 			<div class="accessory_customization hidden justforexample" id="accessory_customization_{$value.id}">
	 				<div class="ac_container" data-button-id="t_a_c_{$value.id}">
		 				<label class="toggler clearfix"><span class="toggler_title">{l s='Customize'  mod='ndk_advanced_custom_fields'}</span> <span class="toggleText">{l s='Show'  mod='ndk_advanced_custom_fields'} </span></label>
		 				<div class="fieldPane clearfix">
				 		{foreach from=$productFields.fields item=field name=fieldsLoop}
					 		{if !$field.type|in_array:[17,24,22,26,27]}
					 			{include file='module:ndk_advanced_custom_fields/views/templates/hook/callField.tpl'}
					 		{/if}
				 		{/foreach}
				 		</div>
			 		</div>
		 	</div>
		 	<p class="clear clearfix action_button">
		 		<a class="btn btn-default trigger-close-fancybox pull-right" href="#">{l s='Confirm' mod='ndk_advanced_custom_fields'}</a>
		 	</p>
		 </div>
	{/if}
</div>
{/if}