
{assign var='field_iteration' value=$smarty.foreach.fieldsLoop.iteration}
{assign var='fieldPrice' value=Tools::convertPrice($field.price, Context::getContext()->currency->id)|round:6}
{assign var='fieldPricePerCaracter' value=Tools::convertPrice($field.price_per_caracter, Context::getContext()->currency->id)|round:6}
<div class="hidden" id="price_type_{$field.id_ndk_customization_field|intval}" data-price-type="{$field.price_type|escape:'htmlall'}"></div>
{if $field.is_visual == 1}
	<script type="text/javascript">
		var is_visual = true;
	</script>
{/if}
{if $field.recommend == 1}
	<script type="text/javascript">
		recommended.push({$field.id_ndk_customization_field});
	</script>
{/if}
{if $field.influences != ''}
	<script type="text/javascript">
		hasRestrictions.push({$field.id_ndk_customization_field});
	</script>
{/if}

{if $field.mode|intval == 1}
	<script type="text/javascript">
		scenario.push({$field.id_ndk_customization_field});
	</script>
{/if}
{if $field.open_status|intval == 1}
	<script type="text/javascript">
		opened_fields.push({$field.id_ndk_customization_field});
	</script>
{/if}
{if $field.open_status|intval == 2}
	<script type="text/javascript">
		closed_fields.push({$field.id_ndk_customization_field});
	</script>
{/if}
{if $field.open_status|intval == 3}
	<script type="text/javascript">
		hidden_fields.push({$field.id_ndk_customization_field});
	</script>
{/if}
<script type="text/javascript">
	window['fieldColors_{$field.id_ndk_customization_field}']=[];
	window['fieldSizes_{$field.id_ndk_customization_field}']=[];
	window['fieldFonts_{$field.id_ndk_customization_field}']=[];
	window['fieldEffects_{$field.id_ndk_customization_field}']=[];
	window['fieldAlignments_{$field.id_ndk_customization_field}']=[];
</script>

{if $field.type == 0}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_text.tpl'}
{elseif $field.type == 1}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_select.tpl'}
{elseif $field.type == 2}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_image.tpl'}
{elseif $field.type == 3}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_color.tpl'}
{elseif $field.type == 4}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_radio.tpl'}
{elseif $field.type == 5}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_mask.tpl'}
{elseif $field.type == 6}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_upload.tpl'}
{elseif $field.type == 7}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_date.tpl'}
{elseif $field.type == 8}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_surface.tpl'}
{elseif $field.type == 10}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_view.tpl'}
{elseif $field.type == 11}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_accessory.tpl'}
{elseif $field.type == 12}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_recipient.tpl'}
{elseif $field.type == 13}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_textarea.tpl'}
{elseif $field.type == 14}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_designer.tpl'}
{elseif $field.type == 15}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_custom_font.tpl'}
{elseif $field.type == 16}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_checkbox.tpl'}
{elseif $field.type == 17}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_accessory_product.tpl'}
{elseif $field.type == 18}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_dimension_text.tpl'}
{elseif $field.type == 19}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_dimension_select.tpl'}
{elseif $field.type == 20}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_audio.tpl'}
{elseif $field.type == 21}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_table_price.tpl'}
{elseif $field.type == 22}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_combination_tab.tpl' product_id=$product_id}
{elseif $field.type == 23}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_accessory_no_quantity.tpl'}
{elseif $field.type == 24}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_accessory_product_no_quantity.tpl'}
{elseif $field.type == 25}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_colorize.tpl'}
{elseif $field.type == 26}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_combination_tab_no_quantity.tpl' product_id=$product_id}
{elseif $field.type == 27}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_combination_list.tpl' product_id=$product_id}
{elseif $field.type == 28}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_color_picker.tpl' product_id=$product_id}
{elseif $field.type == 29}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_caracter.tpl' product_id=$product_id}

{elseif $field.type == 30}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_underwear.tpl' product_id=$product_id}

{elseif $field.type == 100}
{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_bebaboubap.tpl' product_id=$product_id}
{elseif $field.type == 101}
{include file='module:ndk_advanced_custom_fields/views/templates/hook/fields/type_step_combination_list.tpl' product_id=$product_id}
{/if}

{if $field.x_axis > 0 || $field.zone_width > 0}
	<div original-width="{$field.target_original_width|escape:'htmlall'}" zone-mode="{$field.options.zone_mode}" original-height="{$field.target_original_height|escape:'htmlall'}" data-group="{$field.id_ndk_customization_field|intval}" data-view="{$field.target_child|escape:'htmlall'}" class="zone_limit view-{$field.target_child|escape:'htmlall'} {if $field.configurator != 1 && $field.draggable != 1 && $field.resizeable != 1 && $field.rotateable != 1}unvisibleZone{/if}" data-zone-width="{$field.zone_width|escape:'htmlall'}" data-zone-height="{$field.zone_height|escape:'htmlall'}" data-zone-left="{$field.x_axis|escape:'htmlall'}" data-zone-top="{$field.y_axis|escape:'htmlall'}" style="width:{$field.zone_width|escape:'htmlall'}px; height:{$field.zone_height|escape:'htmlall'}px; left:{$field.x_axis|escape:'htmlall'}px ;top:{$field.y_axis|escape:'htmlall'}px;">
	</div>
{/if}
<input type="hidden" name="prices[{$field.id_ndk_customization_field|intval}]" id="price_{$field.id_ndk_customization_field|intval}" value="0"/>