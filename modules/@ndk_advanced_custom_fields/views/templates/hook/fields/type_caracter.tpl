{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}
{capture name='dataPattern'}
data-pattern="{foreach from=$field.values  item=value}{$value.textmask nofilter}&&{$value.value nofilter}|{/foreach}"
{/capture}
<div class="form-group ndkackFieldItem field-type-{$field.type}" data-min-item="{$field.quantity_min}" data-max-item="{$field.quantity_max}" data-iteration="{$field_iteration}"  data-type="caracter" data-id="{$field.target|escape:'htmlall'}" data-name="{$field.name|escape:'htmlall'}" data-view="{$field.target_child|escape:'htmlall'}" data-field="{$field.id_ndk_customization_field|escape:'htmlall'}" {foreach from=$field.options key=k item=v} data-{$k}="{$v}"{/foreach}>
	<label class="toggler {if !$field.is_picto}toggler-default-picto{/if}"
		{if $field.is_picto} style="background-image: url('{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/pictos/{$field.id_ndk_customization_field|intval}.jpg');"{/if}
	>{$field.name|escape:'htmlall'} 
	{if $field.show_price == 1}
		{if $fieldPrice > 0}{l s='cost : ' mod='ndk_advanced_custom_fields'} 
		{if $fieldPrice > 0} : {l s="+" mod='ndk_advanced_custom_fields'}
			{if $field.price_type == 'percent'}
				{$fieldPrice}%
			{else}
				{convertPrice price=$fieldPrice}
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
			<div class="clearfix clear" id="main-{$field.id_ndk_customization_field|intval}">
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
				data-price="{$fieldPrice|escape:'htmlall'}" 
				data-blend="{$field.color_effect}" {if $field.is_mask_image}data-mask-image="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/mask/{$field.id_ndk_customization_field|intval}.jpg"{/if}
				class="addCaracter btn btn-default">
					<i class="material-icons">add</i>{l s='Add' mod='ndk_advanced_custom_fields'}
				</button>
				
				{if $field.quantity_max > 0}
					<p class="max-limit">{l s='You have reached the maximum of  %d caracters' sprintf=[$field.quantity_max]  mod='ndk_advanced_custom_fields'}</p>
				{/if}
				
				<div class="clear clearfix itemsBlock"></div>
				
				{*IMAGES*}
					{*upload*}
					{if $field.orienteable == 1}
					<div style="display:none">
						{include file='module:ndk_advanced_custom_fields/views/templates/hook/orienteable.tpl'}
					</div>
					{/if}
					
					<div class="clear clearfix">	</div>
					{*library*}
					<div class="ndkhiddenimglibrary">
						
						<div class="image-library clear clearfix">	
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
						{include file='module:ndk_advanced_custom_fields/views/templates/hook/values/type_caracter.tpl'}	
						<div class="typed name" data-type="name" >
							<textarea id="ndkcsfield_{$field.id_ndk_customization_field|intval}" {$smarty.capture.dataPattern nofilter} data-lines="{$field.nb_lines|escape:'htmlall'}" data-max="{$field.maxlength}"  
									data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall'}"  
									class="{if $field.is_visual == 1}visual-effect {/if} form-control textzone ndktextarea {if $field.required == 1} required_field{/if} textItem caracter-text dontInit"  
									data-group="{$field.id_ndk_customization_field|intval}" 
									data-zindex="{$field.zindex|escape:'htmlall'}" 
									data-price="{$fieldPrice|escape:'htmlall'}" 
									data-ppcprice="{$field.price_per_caracter|escape:'htmlall'}"  
									data-id="{$field.target|escape:'htmlall'}" 
									data-view="{$field.target_child|escape:'htmlall'}" 
									data-dragdrop="{$field.draggable|intval}" 
									data-resizeable="{$field.resizeable|intval}" 
									data-rotateable="{$field.rotateable|intval}" 
									data-path="{$field.svg_path|escape:'html'}"  
									data-blend="{$field.color_effect}" 
									data-number="1" 
									
									{if $field.is_mask_image}data-mask-image="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/mask/{$field.id_ndk_customization_field|intval}.jpg"{/if}>
									{$value.value|escape:'htmlall'}
							</textarea>
						</div>
						
							
					</div>
						
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
	 typeText['gender'] = "{l s='gender' mod='ndk_advanced_custom_fields' js='1'}";
	 typeText['name'] = "{l s='name' mod='ndk_advanced_custom_fields' js='1'}";
	 typeText['body'] = "{l s='body' mod='ndk_advanced_custom_fields' js='1'}";
	 typeText['clothes'] = "{l s='clothes' mod='ndk_advanced_custom_fields' js='1'}";
	 typeText['hair'] = "{l s='hair' mod='ndk_advanced_custom_fields' js='1'}";
	 typeText['age'] = "{l s='age' mod='ndk_advanced_custom_fields' js='1'}";
	 typeText['accessory'] = "{l s='accessory' mod='ndk_advanced_custom_fields' js='1'}";
	 typeText['beard'] = "{l s='beard' mod='ndk_advanced_custom_fields' js='1'}";
	 typeText['babytrage'] = "{l s='babytrage' mod='ndk_advanced_custom_fields' js='1'}";
	 typeText['with-baby'] = "{l s='with baby' mod='ndk_advanced_custom_fields' js='1'}";
	 typeText['baby-body'] = "{l s='baby body' mod='ndk_advanced_custom_fields' js='1'}";
	 typeText['baby-hair'] = "{l s='baby hair' mod='ndk_advanced_custom_fields' js='1'}";
</script>
	
{addJsDefL name=designerRemoveText}{l s='remove' mod='ndk_advanced_custom_fields'}{/addJsDefL}

{addJsDefL name=caracterText}{l s='Caracter' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=designerImgText}{l s='> Item (image)' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=designerTextText}{l s='> Item (text)' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=designerValue}{l s='see picture' mod='ndk_advanced_custom_fields'}{/addJsDefL}

