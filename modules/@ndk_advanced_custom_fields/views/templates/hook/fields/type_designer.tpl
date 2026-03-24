{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}



<div class="form-group ndkackFieldItem field-type-{$field.type}" data-min-item="{$field.quantity_min}" data-max-item="{$field.quantity_max}" data-type="designer" data-iteration="{$field_iteration}"  data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-name="{$field.name|escape:'htmlall'}" data-field="{$field.id_ndk_customization_field|escape:'htmlall'}" {foreach from=$field.options key=k item=v} data-{$k}="{$v}"{/foreach} data-item-index="1">
	<label class="toggler {if !$field.is_picto}toggler-default-picto{/if}"
		{if $field.is_picto} style="background-image: url('{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/pictos/{$field.id_ndk_customization_field|intval}.jpg');"{/if}
	>{$field.name|escape:'htmlall'} 
	{if $field.show_price == 1}
		{if $fieldPrice > 0}{l s='cost : ' mod='ndk_advanced_custom_fields'} 
		{if $fieldPrice > 0} : {l s="+" mod='ndk_advanced_custom_fields'}
			{if $field.price_type == 'percent'}
				<span class="ndkcf-value-price percent">{$fieldPrice}%</span>
			{else}
				<span class="ndkcf-value-price">{convertPrice price=$fieldPrice}</span>
			{/if}
		{/if}
		{/if}
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
		
			<input id="ndkcsfield_{$field.id_ndk_customization_field|intval}" {if $field.maxlength > 0}maxlength="{$field.maxlength}" {/if} data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall'}" type="hidden" name="ndkcsfield[{$field.id_ndk_customization_field|intval}]" class="{if $field.required == 1} required_field{/if} form-control simpleText " data-price="{$fieldPrice|escape:'htmlall'}" data-ppcprice="{$field.price_per_caracter|escape:'htmlall'}" data-group="{$field.id_ndk_customization_field|intval}" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}"/>
			
			
			
			{if $field.notice !=''}
				<div class="field_notice clearfix clear">{$field.notice nofilter}</div>
			{/if}
			<div class="clearfix clear designer-main-container " id="main-{$field.id_ndk_customization_field|intval}">
				{if $field.options.purpose_upload != "off"}
				<button 
				data-zindex="{$field.zindex|escape:'htmlall'}" 
				data-dragdrop="{$field.draggable|intval}" 
				data-group="{$field.id_ndk_customization_field|intval}" 
				data-resizeable="{$field.resizeable|intval}" 
				data-rotateable="{$field.rotateable|intval}" 
				data-id="{$field.target|escape:'htmlall'}" 
				data-max-item="{$field.quantity_max}" 
				data-view="{$field.target_child|escape:'htmlall'}"
				data-max="{$field.maxlength}" 
				data-price="{$fieldPrice|escape:'htmlall'}" 
				data-blend="{$field.color_effect}" {if $field.is_mask_image}data-mask-image="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/mask/{$field.id_ndk_customization_field|intval}.jpg"{/if}
				class="addUpload btn btn-default designer-btn col-md-4">
					<i class="material-icons">add_a_photo</i>{l s='Upload Image' mod='ndk_advanced_custom_fields'}
				</button>
				{/if}
				
				{if $field.options.purpose_images != "off" && $field.values|@count > 0}
				<button 
				data-zindex="{$field.zindex|escape:'htmlall'}" 
				data-dragdrop="{$field.draggable|intval}" 
				data-group="{$field.id_ndk_customization_field|intval}" 
				data-resizeable="{$field.resizeable|intval}" 
				data-rotateable="{$field.rotateable|intval}" 
				data-id="{$field.target|escape:'htmlall'}" 
				data-max-item="{$field.quantity_max}" 
				data-view="{$field.target_child|escape:'htmlall'}"
				data-max="{$field.maxlength}" 
				data-price="{$fieldPrice|escape:'htmlall'}" 
				data-blend="{$field.color_effect}" {if $field.is_mask_image}data-mask-image="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/mask/{$field.id_ndk_customization_field|intval}.jpg"{/if}
				class="addImg btn btn-default designer-btn col-md-4">
					<i class="material-icons">insert_emoticon</i>{l s='Image' mod='ndk_advanced_custom_fields'}
				</button>
				{/if}
				
				{if $field.options.purpose_textsimple == "on"}
				<button 
				data-zindex="{$field.zindex|escape:'htmlall'}" 
				data-dragdrop="{$field.draggable|intval}" 
				data-group="{$field.id_ndk_customization_field|intval}" 
				data-resizeable="{$field.resizeable|intval}" 
				data-rotateable="{$field.rotateable|intval}" 
				data-id="{$field.target|escape:'htmlall'}" 
				data-view="{$field.target_child|escape:'htmlall'}"
				data-max="{$field.maxlength}" 
				data-ppcprice="0" 
				data-pattern="&&{l s='Type your text here' mod='ndk_advanced_custom_fields'}"
				data-max-item="{$field.quantity_max}" 
				data-price="{$fieldPrice|escape:'htmlall'}" 
				data-blend="{$field.color_effect}" {if $field.is_mask_image}data-mask-image="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/mask/{$field.id_ndk_customization_field|intval}.jpg"{/if}
				class="addText btn btn-default designer-btn col-md-4">
					<i class="material-icons">format_size</i>{l s='Text' mod='ndk_advanced_custom_fields'}
				</button>
				{/if}
				{if $field.options.purpose_text != "off"}
				<button 
				data-zindex="{$field.zindex|escape:'htmlall'}" 
				data-dragdrop="{$field.draggable|intval}" 
				data-group="{$field.id_ndk_customization_field|intval}" 
				data-resizeable="{$field.resizeable|intval}" 
				data-rotateable="{$field.rotateable|intval}" 
				data-id="{$field.target|escape:'htmlall'}" 
				data-view="{$field.target_child|escape:'htmlall'}"
				data-max="{$field.maxlength}" 
				data-max-item="{$field.quantity_max}" 
				data-ppcprice="0" 
				data-pattern="&&{l s='Type your text here' mod='ndk_advanced_custom_fields'}"
				data-price="{$fieldPrice|escape:'htmlall'}" 
				data-blend="{$field.color_effect}" {if $field.is_mask_image}data-mask-image="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/mask/{$field.id_ndk_customization_field|intval}.jpg"{/if}
				class="addTextArea btn btn-default designer-btn col-md-4">
					<i class="material-icons">format_size</i>{l s='Textarea' mod='ndk_advanced_custom_fields'}
				</button>
				{/if}
				
				
				{if $field.quantity_max > 0}
					<p class="max-limit">{l s='You have reached the maximum of  %d items' sprintf=[$field.quantity_max]  mod='ndk_advanced_custom_fields'}</p>
				{/if}
				
				<div class="clear clearfix itemsBlock"></div>
				
				{*IMAGES*}
					{*upload*}
					{if $field.orienteable == 1}
					<div style="display:none">
						{include file='module:ndk_advanced_custom_fields/views/templates/hook/orienteable.tpl'}
					</div>
					{/if}
					<div class="ndkhiddenuploadfile">
						{if $field.options.purpose_upload != "off"}
						<div class="clear clearfix">
						<input type="file" accept="image/*" data-group="{$field.id_ndk_customization_field|intval}" id="" class=" upload_ndk_visu form-control" data-action="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}modules/ndk_advanced_custom_fields/uploadNdkcsfields.php" name="ndk_tmp_name" data-blend="{$field.color_effect}"/>
						
							<div class="img-block col-xs-12 col-md-4">
							<img class="{if $field.is_visual == 1}visual-effect {/if}img-value-{$field.id_ndk_customization_field|intval} img-responsive img-value hidden" data-value="" title="{$field.name|escape:'htmlall'}" src=""  data-group="{$field.id_ndk_customization_field|intval}" data-src="" data-zindex="{$field.zindex|escape:'htmlall'}" 
							data-dragdrop="{$field.draggable|intval}" 
							data-resizeable="{$field.resizeable|intval}" 
							data-rotateable="{$field.rotateable|intval}" 
							 data-blend="{$field.color_effect}" 
							 
							 data-price="0" data-id="{$field.target|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}"/>
							<a href="#" style="display:none" class="remove-upload button btn pull-right btn-default button-small"><span>{l s='remove' mod='ndk_advanced_custom_fields'}</span></a>
						</div>
						</div>
					{/if}
					</div>
					<div class="clear clearfix">	</div>
					{*library*}
					<div class="ndkhiddenimglibrary">
						{if $field.options.purpose_image != "off"}
						<div class="clear col-xs-12 clearfix visu-tools">
							{if $field.colorizesvg}
							<div class="pull-right">
								<p class="clear clearfix"><label>{l s='Images colors '  mod='ndk_advanced_custom_fields'}</label></p>
								<div class="ndk_selector">
									<ul class="colorize_svg" data-group="{$field.id_ndk_customization_field|intval}"></ul>
								</div>
							</div>	
							{/if}
						</div>
						<div class="image-library clear ">	
							{if $field.values|@count > 4}
								{assign var="colxsx" value="col-md-4 col-xs-4"}
							{else}
								{assign var="colxsx" value="col-md-4 col-xs-4"}
							{/if}
							{include file='module:ndk_advanced_custom_fields/views/templates/hook/values/type_designer.tpl'}
						
					</div>
						{/if}
					</div>
				{*END IMAGES*}
				
				</div>
		</div>
	</div>

<script type="text/javascript">
	fieldColors_{$field.id_ndk_customization_field}= [];
	fieldSizes_{$field.id_ndk_customization_field} = [];
	fieldFonts_{$field.id_ndk_customization_field} = [];
	fieldEffects_{$field.id_ndk_customization_field} = [];
	fieldAlignments_{$field.id_ndk_customization_field} = [];
	{if $field.colors !=''}
		{foreach from=$field.colors  item=color}
			window['fieldColors_{$field.id_ndk_customization_field}'].push('{$color}');
		{/foreach}
	{/if}
	{if $field.sizes !=''}
		{foreach from=$field.sizes  item=size}
			window['fieldSizes_{$field.id_ndk_customization_field}'].push('{$size}');
		{/foreach}
	{/if}
	{if $field.fonts !=''}
		{foreach from=$field.fonts  item=font}
			window['fieldFonts_{$field.id_ndk_customization_field}'].push('{$font}');
		{/foreach}
	{/if}
	{if $field.effects !=''}
		{foreach from=$field.effects  item=effect}
			window['fieldEffects_{$field.id_ndk_customization_field}'].push('{$effect}');
		{/foreach}
	{/if}
	{if $field.alignments !=''}
		{foreach from=$field.alignments  item=alignment}
			window['fieldAlignments_{$field.id_ndk_customization_field}'].push('{$alignment}');
		{/foreach}
	{/if}
</script>
	
{addJsDefL name=designerRemoveText}{l s='remove' mod='ndk_advanced_custom_fields'}{/addJsDefL}

{addJsDefL name=designerImgText}{l s='> Item (image)' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=designerUploadText}{l s='> Item (upload)' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=designerTextText}{l s='> Item (text)' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=designerValue}{l s='see picture' mod='ndk_advanced_custom_fields'}{/addJsDefL}

