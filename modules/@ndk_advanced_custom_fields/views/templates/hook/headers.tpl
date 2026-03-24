{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*}
{if Context::getContext()->controller->php_self == 'product'}
{if $nofollow}
	<meta name="robots" content="index, nofollow">
{/if}
{if $ps_version > 1.6}
	{assign var="base_dir_ssl" value=$urls.base_url}
	{assign var="base_dir" value=$urls.base_url}
{/if}
{function jsadd}
    {foreach $data as $item}
        {if not $item|@is_array}
            combinations.push('{$item}')
        {else}
            {jsadd data = $item}
        {/if}
    {/foreach}
{/function}
{if $ps_version < 1.7}
	{if isset($conf)}
	{if isset($conf->id)}
	<meta property="og:url" content="{Context::getContext()->link->getProductLink(Tools::getValue('id_product'))} {if $conf->id > 0}?id_ndk_customization_field_configuration={$conf->id}{/if}" />
	{/if}
	{if isset($conf->cover) && $conf->cover !=''}
		<meta property="og:image" content="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/{$conf->cover}">
	{/if}
	{/if}
	<meta property="og:title" content="{$meta_title|escape:'html'}" />
	<meta property="og:site_name" content="{$shop_name}" />
	<meta property="og:description" content="{$meta_description|escape:'html'}" />
{else}
{block name="head_seo" prepend}
	{if isset($conf)}
	{if isset($conf->id)}
	
	<meta property="og:url" content="{Context::getContext()->link->getProductLink(Tools::getValue('id_product'))} {if $conf->id > 0}?id_ndk_customization_field_configuration={$conf->id}{/if}" />
	{/if}
	{if isset($conf->cover) && $conf->cover !=''}
		<meta property="og:image" content="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/{$conf->cover}">
	{/if}
	{/if}
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	{/block}
{/if}

<script src="https://use.fontawesome.com/8bde1cf092.js"></script>
<!--<script src="/modules/ndk_advanced_custom_fields/views/js/html2canvas.ndk.js"></script>
<script src="/modules/ndk_advanced_custom_fields/views/js/html2canvas.svg.min.js"></script>-->
<link rel="stylesheet" href="{Configuration::get('NDK_ACF_FONTS')}" />
<link href="https://fonts.googleapis.com/css2?family=Material+Icons&family=Material+Icons+Outlined&family=Material+Icons+Round&family=Material+Icons+Sharp&family=Material+Icons+Two+Tone" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Icons"
  rel="stylesheet">
{addJsDefL name=lazyImgDefault}{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}modules/ndk_advanced_custom_fields/views/img/lazy.jpg{/addJsDefL}
{addJsDefL name=addProductPrice}{Configuration::get('NDK_ADD_PRODUCT_PRICE')}{/addJsDefL}
{addJsDefL name=showImgTooltips}{Configuration::get('NDK_SHOW_IMG_TOOLTIP', 0)}{/addJsDefL}
{addJsDefL name=quantityAvailable}{StockAvailable::getQuantityAvailableByProduct({Tools::getValue('id_product')})}{/addJsDefL}
{addJsDef stock_management=Configuration::get('PS_STOCK_MANAGEMENT')|intval}
{addJsDef allowBuyWhenOutOfStock=$allow_oosp|boolval}
{addJsDefL name=currencySign}{Context::getContext()->currency->sign}{/addJsDefL}
{addJsDefL name=currencyFormat17}{$currencyFormat17}{/addJsDefL}
{addJsDefL name=full_cldr_language_code}EN{/addJsDefL}
{addJsDefL name=ps_version}{$ps_version}{/addJsDefL}
{addJsDefL name=savedtext}{l s='Saved' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=loadingText}{l s='Generating your custom product...' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=additionnalText}{l s='Options : ' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=fillText}{l s='Please fill all values' mod='ndk_advanced_custom_fields' js='1'}{/addJsDefL}


{addJsDefL name=applyText}{l s='Apply' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=filterText}{l s='Filter' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=allText}{l s='All' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
	{addJsDefL name=timelineText}{l s='Price/quantity' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
	{addJsDefL name=priceMessage}{l s='Not enouth quantity in stock' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
	{addJsDefL name=priceMessageSpecific}{l s='A discount of' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
	{addJsDefL name=labelTotal}{l s='Total : ' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
	
{addJsDefL name=cusText}{l s='Customization' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=detailText}{l s='Details' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=base_productText}{l s='Base product' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=resetText}{l s='done' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=selectLayer}{l s='select layer' mod='ndk_advanced_custom_fields'}{/addJsDefL}

{addJsDefL name=caracterText}{l s='Caracter' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=underwearText}{l s='Underwear' mod='ndk_advanced_custom_fields'}{/addJsDefL}

{addJsDefL name=designerRemoveText}{l s='remove' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=designerImgText}{l s='> Item (image)' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=designerUploadText}{l s='> Item (upload)' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=designerTextText}{l s='> Item (text)' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=designerValue}{l s='see picture' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=charsLeftText}{l s='character left' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}

{addJsDefL name=priceMessage}{l s='Not enouth quantity in tock' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=priceMessageSpecific}{l s='A discount of' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=labelTotal}{l s='Total : ' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
		
{addJsDefL name=disableLoader}{Configuration::get('NDK_DISABLE_LOADER')|intval}{/addJsDefL}
{addJsDefL name=ndk_disableAutoScroll}{Configuration::get('NDK_DISABLE_AUTOSCROLL')|intval}{/addJsDefL}
{addJsDefL name=showSocialTools}{Configuration::get('NDK_SHOW_SOCIAL_TOOLS')|intval}{/addJsDefL}
{addJsDefL name=showHdPreview}{Configuration::get('NDK_SHOW_HD_PREVIEW')|intval}{/addJsDefL}
{addJsDefL name=showImgPreview}{Configuration::get('NDK_SHOW_IMG_PREVIEW')|intval}{/addJsDefL}
{addJsDefL name=editText}{l s='Save configuration' mod='ndk_advanced_custom_fields'}{/addJsDefL}

	
{addJsDefL name=isFields}1{/addJsDefL}
{addJsDefL name=templateType}{$template_type|escape:'htmlall'}{/addJsDefL}
{if isset($make_float) && $make_float !=''}
	{addJsDefL name=makeItFloat}{$make_float|escape:'htmlall'}{/addJsDefL}
{else}
	{addJsDefL name=makeItFloat}0{/addJsDefL}
{/if}
{if isset($let_open) && $let_open !=''}
	{addJsDefL name=letOpen}{$let_open|escape:'htmlall'}{/addJsDefL}
{else}
	{addJsDefL name=letOpen}0{/addJsDefL}
{/if}
{if isset($make_slide) && $make_slide !=''}
	{addJsDefL name=makeSlide}{$make_slide|escape:'htmlall'}{/addJsDefL}
{else}
	{addJsDefL name=makeSlide}0{/addJsDefL}
{/if}	
{if isset($displayPriceHT) && $displayPriceHT !=''}
	{addJsDefL name=displayPriceHT}{$displayPriceHT|escape:'htmlall'}{/addJsDefL}
	{addJsDefL name=ndk_taxe_rate}{NdkCf::getNdkTaxeRate(Tools::getValue('id_product'))}{/addJsDefL}
{else}
	{addJsDefL name=displayPriceHT}0{/addJsDefL}
	{addJsDefL name=ndk_taxe_rate}0{/addJsDefL}
{/if}

{if isset($showRecap) && $showRecap !=''}
	{addJsDefL name=showRecap}{$showRecap|escape:'htmlall'}{/addJsDefL}
{else}
	{addJsDefL name=showRecap}0{/addJsDefL}
{/if}
{if isset($showQuicknav) && $showQuicknav !=''}
	{addJsDefL name=showQuicknav}{$showQuicknav|escape:'htmlall'}{/addJsDefL}
{else}
	{addJsDefL name=showRecap}0{/addJsDefL}
{/if}
{if isset($allowEdit) && $allowEdit !=''}
	{addJsDefL name=allowEdit}{$allowEdit|escape:'htmlall'}{/addJsDefL}
{else}
	{addJsDefL name=allowEdit}1{/addJsDefL}
{/if}
{if  Tools::getValue('showRecap')}
	{addJsDefL name=showRecap}1{/addJsDefL}
{/if}
{addJsDefL name=labelTotalHT}{l s='Total tax excl.: ' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}

{addJsDefL name=templateType}{$template_type|escape:'htmlall'}{/addJsDefL}

{addJsDefL name=toggleOpenText}{l s='View options' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=toggleCloseText}{l s='Hide options' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}

{addJsDefL name=cusText}{l s='Customization' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=baseUrl}{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}{/addJsDefL}
{addJsDefL name=baseDir}{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}{/addJsDefL}
{addJsDefL name=textMaxQuantity}{l s='with theses options you can order a max quantity of' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=tagslabel}{l s='add tags here : ' js=1 mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=contentOnly}{if Tools::getIsset('content_only')}true{else}false{/if}{/addJsDefL}
{addJsDefL name=out_of_stock_text}{l s='Out of stock' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=in_stock_text}{l s='In stock' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{addJsDefL name=editConfig}{$edit_config}{/addJsDefL}
{addJsDefL name=refProd}{$ref_prod}{/addJsDefL}
{addJsDefL name=oldRef}{$old_ref}{/addJsDefL}
{addJsDef stockManagement=Configuration::get('PS_STOCK_MANAGEMENT')}
{if $edit_config > 0}
		{addJsDefL name=submitBtnText}{l s='Edit your customization' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{else}
		{addJsDefL name=submitBtnText}{l s='Add to cart' mod='ndk_advanced_custom_fields'}{/addJsDefL}
{/if}
{function jsadd}
    {foreach $data as $item}
        {if not $item|@is_array}
            combinations.push('{$item}')
        {else}
            {jsadd data = $item}
        {/if}
    {/foreach}
{/function}



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
{* {addJsDefL name=ndkMovableTools}
<div class="ndk-movable-tools" id="ndk-movable-tools">
	<span class="tool-item material-icons-outlined ndk-movable-setting" title="{l s="edit" mod='ndk_advanced_custom_fields'}">settings</span>
	<span class="tool-item material-icons-outlined ndk-movable-to-front" title="{l s="move to front" mod="ndk_advanced_custom_fields"}">flip_to_front</span>
	<span class="tool-item material-icons-outlined ndk-movable-to-back" title="{l s="move to back" mod="ndk_advanced_custom_fields"}">flip_to_back</span>
</div>
{/addJsDefL} *}
<script>
	var typeText = [];
    var fonts = [];
    {jsaddFonts data=$fonts}
    var colors = [];
    {jsaddColors data=$colors_ndk}
</script>

<script type="text/javascript">
	var is_visual = false;
</script>

{/if}