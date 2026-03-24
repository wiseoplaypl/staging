{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2018 Hendrik Masson
 *  @license   Tous droits réservés
*}
<script type="text/javascript">
	var is_visual = false;
	var recommended = [];
	var scenario = [];
	var opened_fields = [];
	var closed_fields = [];
	var hidden_fields = [];
	var hasRestrictions =[];
	var jsonDetails = [] ;
	var stroke_color = [];
	var ndkSpecificPrices = [];
	var ndkPriceDisplay = {$priceDisplay}
</script>
{if $priceDisplay == 0}
{assign var='priceDisplay' value=false}
{/if}
{if $ps_version > 1.6}
	{assign var="base_dir_ssl" value=$urls.base_url}
	{assign var="base_dir" value=$urls.base_url}
	<input type="hidden" id="idCombination"/>
{/if}
{if (isset($ndkcsfields) && $ndkcsfields|@count > 0) || ($fieldsItems)}
	
	
	{assign var='ndk_image_size' value=Configuration::get('NDK_IMAGE_SIZE')}
	{assign var='ndk_image_large_size' value=Configuration::get('NDK_IMAGE_LARGE_SIZE')}
	{assign var='fromPriceField' value=NdkCf::getFieldFromPrice($product_id, 0)}
	{if $fromPriceField|@count > 0}
	<input type="hidden" data-id-product="{$product_id}" class="ndkcfFromPriceProduct" value="{l s='From' mod='ndk_advanced_custom_fields'} {convertPrice price=$fromPriceField.0.price}"/>
	{/if}

<button type="button" class="btn-primary btn-ndkacf-scroll" data-target="#ndkcsfields-block">
		{if $edit_config > 0}
		  {l s='Edit your customization' mod='ndk_advanced_custom_fields'}
		  {else}
		  {l s='Customize' mod='ndk_advanced_custom_fields'}
		  {/if}
</button>

<div class="block ndkcsfields-block" id="ndkcsfields-block" data-key="{$product_id}">
	<noscript>
		<div class="javascript-disabled">
		{l s='Please activate javascript on your browser to be able to use our configurator' mod='ndk_advanced_custom_fields'}
		</div>
	</noscript>
	
	{if $edit_config > 0}
		{addJsDefL name=oldRef}{$old_ref}{/addJsDefL}
		<h3 class="ndkcfTitle">{l s='Edit your customization' mod='ndk_advanced_custom_fields'}</h3>
	{else}
		<h3 class="ndkcfTitle">{l s='Customize' mod='ndk_advanced_custom_fields'}</h3>
	{/if}
	<form id="ndkcsfields" class="clear clearfix ajax_form" action="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}modules/ndk_advanced_custom_fields/">
	
	{if $fieldsItems}
		
		{addJsDefL name=isFieldsPack}1{/addJsDefL}
			<div class="clear clearfix">
			<h3>{l s='Wich product would you want to customize ?' mod='ndk_advanced_custom_fields'}</h3>
			<ul class="item-images-nav clear clearfix">
				{foreach $ndkpackitems as $key=>$ndkpackitem}
					{if $ndkpackitem.ndkcsfields|@count > 0 && (!$ndkpackitem.ndkcsfields.0.id_ndk_customization_field|in_array:$encountredField || $ndkpackitem.ndkcsfields|@count > 1 )}
						<li class="col-md-3 col-xs-6"><img target="#groupField_{$key}" class="toggleGroupField img-responsive groupItemImage" src="{$link->getImageLink($ndkpackitem.link_rewrite, $ndkpackitem.cover, 'home_default')|escape:'html'}"/></li>
					{/if}
				{/foreach}
			</ul>
		</div>
		
		{foreach $ndkpackitems as $key=>$ndkpackitem}
			{assign var='features' value=$ndkpackitem.features}
				{if $ndkpackitem.ndkcsfields|@count > 0 && (!$ndkpackitem.ndkcsfields.0.id_ndk_customization_field|in_array:$encountredField || $ndkpackitem.ndkcsfields|@count > 1 )}
				{addJsDefL name=isFields}1{/addJsDefL}
			
			<script type="text/javascript">
				var isFieldsPack = true;
			</script>
			
			{*if $edit_config > 0}
				{addJsDefL name=oldRef}{$old_ref}{/addJsDefL}
				<h3 target="#groupField_{$key}" class="toggleGroupField">{l s='Edit your customization for' mod='ndk_advanced_custom_fields'} : {$ndkpackitem.name}</h3>
			{else}
				<h3 target="#groupField_{$key}" class="toggleGroupField">{l s='Customize' mod='ndk_advanced_custom_fields'} : {$ndkpackitem.name}</h3>
			{/if*}
				
				
				
			<div class="groupFieldBlock" id="groupField_{$key}">
				{foreach from=$ndkpackitem.ndkcsfields item=field name=fieldsLoop}
					{assign var='field_iteration' value=$smarty.foreach.fieldsLoop.iteration}
					{if !$field.id_ndk_customization_field|in_array:$encountredField}
						{append var='encountredField' value=$field.id_ndk_customization_field|intval}
						{include file='module:ndk_advanced_custom_fields/views/templates/hook/callField.tpl'}
						{if $field.fontLink !=''}
							<link rel="stylesheet" href="{$field.fontLink}" />
						{/if}
					{/if}
				{/foreach}
			</div>
			{/if}
		{/foreach}
	{/if}
		<div class="{if $fieldsItems}alwaysVisible{/if} groupFieldBlock packlistGroup">
		
			{foreach from=$ndkcsfields item=field name=fieldsLoop}
				{include file='module:ndk_advanced_custom_fields/views/templates/hook/callField.tpl'}
				{if $field.fontLink !=''}
					<link rel="stylesheet" href="{$field.fontLink}" />
				{/if}
			{/foreach}
		</div>
		
		
		
		
		<div class="form-group clearfix box-info-product submitContainer product-actions" data-view="0" data-field="">
			
			<div class="clearfix ndk_recap_block" data-view="0">
			{if $show_prices}
				<div id="ndkcf_recap_linear" class="clear clearfix "></div>
			{/if}
			</div>
			
			<input class="dontCare image-url" type="hidden" name="image-url[]" value=" " id="image-url-0" />
			<input id="ndkcf_id_product" class="dontCare" type="hidden" name="id_product" value="{$product_id|intval}"/>
			<input class="dontCare" id="ndkcf_id_combination" type="hidden" name="ndkcf_id_combination" value=""/>
			<input class="dontCare" type="hidden" name="cusText" value="{l s='Customized' mod='ndk_advanced_custom_fields'}"/>
			<input class="dontCare" type="hidden" name="cusTextTotal" value="{l s='Customization total price' mod='ndk_advanced_custom_fields'}"/>
			<input class="dontCare" type="hidden" name="cusTextRef" value="{l s='Customization reference' mod='ndk_advanced_custom_fields'}"/>
			<input class="dontCare" type="hidden" name="cusTextComb" value="{l s='Combination' mod='ndk_advanced_custom_fields'}"/>
			<input class="dontCare" type="hidden" name="previewText" value="{l s='Preview' mod='ndk_advanced_custom_fields'}"/>
			{if $show_prices && Configuration::get('PS_CATALOG_MODE') != 1}
			<button form="ndkcsfields" id="submitNdkcsfields" name="submitNdkcsfields" class="button exclusive btn btn-primary add-to-cart-false " >
					<span>{if $ps_version > 1.6}<i class="material-icons shopping-cart">&#xE547;</i>{/if}{if $edit_config > 0}{l s='Edit' mod='ndk_advanced_custom_fields'}{else}{l s='Add to cart' mod='ndk_advanced_custom_fields'}{/if}</span>
			</button>
			{/if}
		</div>
		</form>
		{if $show_prices}
		<div id="ndkcf_recap" class="clear clearfix ndk_recap_block">
		<p  class="ndkcf_recap_title clear clearfix btn-primary"><i class="material-icons toggleRecap opened">arrow_drop_down</i><span class="ndkcf_recap_total"><span class="ndk-overview-text">{l s='Overview' mod='ndk_advanced_custom_fields'} : </span><span class="price"></span></span></p>
			<div class="clear clearfix ndkcf_recap_content">
				
			<div class="recap_items">
				{foreach from=$ndkcsfields item=field name=fieldsLoop}
						<div class="recap_group recap_group_{$field.id_ndk_customization_field|escape:'htmlall'}" data-group="{$field.id_ndk_customization_field|escape:'htmlall'}"></div>
					{/foreach}
			</div>
			<p class="ndkcf-tax-infos">
				{if $priceDisplay == 1}{l s='prices are expressed excluding taxes' mod='ndk_advanced_custom_fields'}{else}{l s='prices are expressed including taxes' mod='ndk_advanced_custom_fields'}{/if}
			</p>
			<p class="ndkcf_recap_total">{l s='Total' mod='ndk_advanced_custom_fields'} : <span class="price"></span><br/><span class="priceht"></span></p>
			</div>
		</div>
		{/if}
		{include file='module:ndk_advanced_custom_fields/views/templates/hook/recommends.tpl'}
		<div class="ndkShareCompo">
			<h4>{l s="Share your customization with your firends" mod='ndk_advanced_custom_fields'}</h4>
				<button data-type="twitter" type="button" class="btn btn-default btn-twitter ndk-social-sharing">
					<i class="icon-twitter"></i> {l s="Tweet" mod='ndk_advanced_custom_fields'}
				</button>
				<button data-type="facebook" type="button" class="btn btn-default btn-facebook ndk-social-sharing">
					<i class="icon-facebook"></i> {l s="Facebook" mod='ndk_advanced_custom_fields'}
				</button>
				{* <button data-type="google-plus" type="button" class="btn btn-default btn-google-plus ndk-social-sharing">
					<i class="icon-google-plus"></i> {l s="Google+" mod='ndk_advanced_custom_fields'}
				</button> *}
				<button data-type="pinterest" type="button" class="btn btn-default btn-pinterest ndk-social-sharing">
					<i class="icon-pinterest"></i> {l s="Pinterest" mod='ndk_advanced_custom_fields'}
				</button>
				<p class="clear clearfix"></p>
				<img class="img-responsive current_config_img" src=""/>
				<div class="clear clearfix">
				<a data-type="shareImgDl" target="_blank" type="button" class="btn btn-default shareImgDl ndk-social-sharing" download>
					<i class="icon-download"></i> {l s='Download image' mod='ndk_advanced_custom_fields'}
				</a>
				<button data-type="copyLink" type="button" class="btn btn-default copyLink ndk-social-sharing">
					<i class="icon-link"></i> {l s='Copy link' mod='ndk_advanced_custom_fields'}
				</button>
				</div>
		</div>
	</div>
	<input id="copyLinkInput" class="clear clearfix copyLinkInput" type="hidden"></input>
	<div class="ndk-movable-tools" id="ndk-movable-tools" style="display:none">
		
		<span class="tool-item material-icons-outlined ndk-movable-setting" title="{l s="edit" mod='ndk_advanced_custom_fields'}">settings</span>
		<span class="material-icons-outlined tool-item ndk-rotate tool-item" title="{l s="rotate" mod="ndk_advanced_custom_fields"}">rotate_90_degrees_ccw</span>
		<span class="material-icons-outlined tool-item ndk-crop tool-item ndk-crop-tool" data-clippath="inset" title="{l s="crop" mod="ndk_advanced_custom_fields"}">crop</span>
		<span class="material-icons-outlined tool-item ndk-crop tool-item ndk-crop-tool" data-clippath="ellipse" title="{l s="crop" mod="ndk_advanced_custom_fields"}">rounded_corner</span>
		<span class="tool-item material-icons-outlined ndk-movable-to-front ndk-designer-only" title="{l s="move to front" mod="ndk_advanced_custom_fields"}">flip_to_front</span>
		<span class="tool-item material-icons-outlined ndk-movable-to-back ndk-designer-only" title="{l s="move to back" mod="ndk_advanced_custom_fields"}">flip_to_back</span>
		<span class="flex-break ndk-designer-only"></span>
		<span class="material-icons-outlined tool-item ndk-tool-add ndk-designer-only" title="{l s="add" mod="ndk_advanced_custom_fields"}">add</span>
		<span class="material-icons-outlined tool-item ndk-tool-duplicate ndk-designer-only" title="{l s="duplicate" mod="ndk_advanced_custom_fields"}">file_copy</span>
		<span class="material-icons-outlined tool-item ndk-tool-delete tool-item ndk-designer-only" title="{l s="delete" mod="ndk_advanced_custom_fields"}">delete</span>
		
	</div>
	
	{function jsadd}
	    {foreach $data as $item}
	        {if not $item|@is_array}
	            combinations.push('{$item}')
	        {else}
	            {jsadd data = $item}
	        {/if}
	    {/foreach}
	{/function}
	
	
	
	{addJsDefL name=savedtext}{l s='Saved' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
	{addJsDefL name=applyText}{l s='Apply' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
	{addJsDefL name=filterText}{l s='Filter' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
	{addJsDefL name=allText}{l s='All' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
		{addJsDefL name=timelineText}{l s='Price/quantity' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
		{addJsDefL name=priceMessage}{l s='Not enouth quantity in stock' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
		{addJsDefL name=priceMessageSpecific}{l s='A discount of' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
		{addJsDefL name=labelTotal}{l s='Total : ' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
			
	{addJsDefL name=isFields}1{/addJsDefL}
	
	
	
	
	
	
	
	{addJsDefL name=cusText}{l s='Customization' mod='ndk_advanced_custom_fields'}{/addJsDefL}
	{addJsDefL name=baseUrl}{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}{/addJsDefL}
	{addJsDefL name=textMaxQuantity}{l s='with theses options you can order a max quantity of' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
	{addJsDefL name=tagslabel}{l s='add tags here : ' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
	{function jsaddFonts}
	    {foreach $data as $item}
	        {if not $item|@is_array}
	            fonts.push('{$item}')
	        {else}
	            {jsadd data = $item}
	        {/if}
	    {/foreach}
	{/function}
	{function jsaddColors}
	    {foreach $data as $item}
	        {if not $item|@is_array}
	            colors.push('{$item}')
	        {else}
	            {jsadd data = $item}
	        {/if}
	    {/foreach}
	{/function}
	
	<script>
	    var fonts = [];
	    {jsaddFonts data=$fonts}
	    var colors = [];
	    {jsaddColors data=$colors_ndk}
	</script>

	
		<div style="display:none" class="hiddenfortranslation">
		{l s='Preview (image)' mod='ndk_advanced_custom_fields'}
		{l s='Base product' mod='ndk_advanced_custom_fields'}
		{l s='Details' mod='ndk_advanced_custom_fields'}
		{l s='reference' mod='ndk_advanced_custom_fields'}
		{l s='No preview required' mod='ndk_advanced_custom_fields'}
	</div>
	{if isset($jsonDatas)}
		<script type="text/javascript">
			var jsonDatas = {$jsonDatas|@json_encode nofilter};
		</script>
	{/if}
	{include file='module:ndk_advanced_custom_fields/views/templates/hook/old_browser.tpl'}
{/if}
{if Configuration::get('PS_CATALOG_MODE') == 1}
<div style="display:none">
<input type="hidden" name="qty" id="quantity_wanted" value="1" >
</div>
{/if}
	


