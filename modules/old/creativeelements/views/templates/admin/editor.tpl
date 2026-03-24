{**
 * Creative Elements - Elementor based PageBuilder
 *
 * @author    WebshopWorks
 * @copyright 2019-2020 WebshopWorks.com
 * @license   One domain support license
 *}

<!DOCTYPE html>
<html class="no-js" lang="{Context::getContext()->language->iso_code|escape:'html':'UTF-8'}">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
{if Tools::usingSecureMode()}
	<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
{/if}
	<title>{ce__('Creative Elements - Elementor based PageBuilder')}</title>
	<link rel="icon" type="image/x-icon" href="{$smarty.const._PS_IMG_|escape:'html':'UTF-8'}favicon.ico" />
	{call_user_func('CE\\do_action', 'wp_head')|cleanHtml}
</head>
<body class="elementor-editor-active">
	<div id="elementor-editor-wrapper">
		<div id="elementor-preview">
			<div id="elementor-loading">
				<div class="elementor-loader-wrapper">
					<div class="elementor-loader">
						<div class="elementor-loader-box"></div>
						<div class="elementor-loader-box"></div>
						<div class="elementor-loader-box"></div>
						<div class="elementor-loader-box"></div>
					</div>
					<div class="elementor-loading-title">{ce__('Loading')}</div>
				</div>
			</div>
			<div id="elementor-preview-responsive-wrapper" class="elementor-device-desktop elementor-device-rotate-portrait">
				<div id="elementor-preview-loading">
					<i class="fa fa-spin fa-circle-o-notch"></i>
				</div>
			</div>
		</div>
		<div id="elementor-panel" class="elementor-panel"></div>
	</div>
	{call_user_func('CE\\do_action', 'wp_footer')|cleanHtml}
	{*global*}
	<script type="text/template" id="tmpl-elementor-empty-preview">
		<div class="elementor-first-add">
			<div class="elementor-icon eicon-plus"></div>
		</div>
	</script>

	<script type="text/template" id="tmpl-elementor-preview">
		<div class="elementor-section-wrap"></div>
		<div id="elementor-add-section" class="elementor-visible-desktop">
			<div id="elementor-add-section-inner">
				<div id="elementor-add-new-section">
					<button id="elementor-add-section-button" class="elementor-button">{ce__('Add New Section')}</button>
					<button id="elementor-add-template-button" class="elementor-button">{ce__('Add Template')}</button>
					<div id="elementor-add-section-drag-title">{ce__('Or drag widget here')}</div>
				</div>
				<div id="elementor-select-preset">
					<div id="elementor-select-preset-close">
						<i class="fa fa-times"></i>
					</div>
					<div id="elementor-select-preset-title">{ce__('Select your Structure')}</div>
					<ul id="elementor-select-preset-list">
					<#
					var structures = [ 10, 20, 30, 40, 21, 22, 31, 32, 33, 50, 60, 34 ];

					_.each( structures, function( structure ) {
						var preset = elementor.presetsFactory.getPresetByStructure( structure ); #>
						<li class="elementor-preset elementor-column elementor-col-16" data-structure="<#- structure #>">
							<#= elementor.presetsFactory.getPresetSVG( preset.preset ).outerHTML #>
						</li>
					<# } ); #>
					</ul>
				</div>
			</div>
		</div>
	</script>
	{*/global*}
	{*panel*}
	<script type="text/template" id="tmpl-elementor-panel">
		<div id="elementor-mode-switcher"></div>
		<header id="elementor-panel-header-wrapper"></header>
		<main id="elementor-panel-content-wrapper"></main>
		<footer id="elementor-panel-footer">
			<div class="elementor-panel-container">
			</div>
		</footer>
	</script>

	<script type="text/template" id="tmpl-elementor-panel-menu-item">
		<div class="elementor-panel-menu-item-icon">
			<i class="fa fa-<#- icon #>"></i>
		</div>
		<div class="elementor-panel-menu-item-title"><#= title #></div>
	</script>

	<script type="text/template" id="tmpl-elementor-panel-header">
		<div id="elementor-panel-header-menu-button" class="elementor-header-button">
			<i class="elementor-icon eicon-menu tooltip-target" data-tooltip="{ce__('Menu')}"></i>
		</div>
		<div id="elementor-panel-header-title"></div>
		<div id="elementor-panel-header-add-button" class="elementor-header-button">
			<i class="elementor-icon eicon-apps tooltip-target" data-tooltip="{ce__('Widgets Panel')}"></i>
		</div>
	</script>

	<script type="text/template" id="tmpl-elementor-panel-footer-content">
		<div id="elementor-panel-footer-exit" class="elementor-panel-footer-tool">
			<i class="fa fa-times" title="{ce__('Exit')}"></i>
			<div class="elementor-panel-footer-sub-menu-wrapper">
				<div class="elementor-panel-footer-sub-menu">
					<a id="elementor-panel-footer-view-page" class="elementor-panel-footer-sub-menu-item" href="javascript:void open(elementor.config.post_permalink)">
						<i class="elementor-icon fa fa-external-link"></i>
						<span class="elementor-title">{ce__('View Page')}</span>
					</a>
					<a id="elementor-panel-footer-view-edit-page" class="elementor-panel-footer-sub-menu-item" href="javascript:void(location.href=elementor.config.edit_post_link)">
						<i class="elementor-icon fa fa-arrow-left"></i>
						<span class="elementor-title">{ce__('Go to Back-office')}</span>
					</a>
				</div>
			</div>
		</div>
	{$langs = Language::getLanguages(true, false)}
	{$uid = call_user_func('CE\\get_the_ID')}
	{if (count($langs) > 1 || Shop::isFeatureActive()) && $uid && $uid->id_type > 1}
		<div id="elementor-panel-footer-lang" class="elementor-panel-footer-tool">
			<span class="elementor-screen-only">{ce__('Language')}</span>
			<i class="fa fa-flag" title="{ce__('Language')}"></i>
			<div class="elementor-panel-footer-sub-menu-wrapper">
			{$shops = Shop::getShops()}
			{if Shop::isFeatureActive() && count($shops) > 1}
				<form class="elementor-panel-footer-sub-menu" id="ce-context-wrapper" name="context" method="post">
					{$active_shop = Shop::getContextShopID()}
					{$active_group = Shop::getContextShopGroupID()}
					{$shop_ids = $uid->getShopIds()}
					{$groups = []}
					{$group_ids = []}
					{foreach ShopGroup::getShopGroups() as $group}
						{$groups[$group->id] = $group->name}
					{/foreach}
					{foreach $shop_ids as $id_shop}
						{$id_group = $shops[$id_shop]['id_shop_group']}
						{$group_ids[$id_group] = $id_group}
					{/foreach}
					<select name="setShopContext" id="ce-context">
						<option value="">{ce__('All Shops')}{if !$active_group} ★ {/if}</option>
						{foreach $group_ids as $id_group}
							{$active = !$active_shop && $id_group == $active_group}
							<option value="g-{$id_group|intval}"{if $active} selected{/if}> ▸ {$groups[$id_group]|cleanHtml}{if $active} ★ {/if}</option>
							{foreach $shop_ids as $id_shop}
								{if $shops[$id_shop]['id_shop_group'] == $id_group}
									{$active = $id_shop == $active_shop}
									<option value="s-{$id_shop|intval}"{if $active} selected{/if}>&nbsp; &nbsp; ▸ {$shops[$id_shop]['name']|cleanHtml}{if $active} ★ {/if}</option>
								{/if}
							{/foreach}
						{/foreach}
					</select>
				</form>
			{/if}
				<div class="elementor-panel-footer-sub-menu" id="ce-langs" data-lang="{$uid->id_lang|intval}" data-built='{call_user_func('CE\\UId::getBuiltUIds', $uid->id, $uid->id_type)|json_encode}'>
					{foreach $langs as $lang}
						<div class="elementor-panel-footer-sub-menu-item ce-lang" data-lang="{$lang['id_lang']|intval}" data-shops='{array_keys($lang['shops'])|json_encode}'>
							<i class="elementor-icon">{$lang['iso_code']|cleanHtml}</i>
							<span class="elementor-title">{$lang['name']|cleanHtml}</span>
							<span class="elementor-description">
								<button class="elementor-button elementor-button-success">
									<i class="eicon-file-download"></i>
									{ce__('Insert')}
								</button>
							</span>
						</div>
					{/foreach}
				</div>
			</div>
		</div>
	{/if}
		<div id="elementor-panel-footer-responsive" class="elementor-panel-footer-tool">
			<i class="eicon-device-desktop" title="{ce__('Responsive Mode')}"></i>
			<div class="elementor-panel-footer-sub-menu-wrapper">
				<div class="elementor-panel-footer-sub-menu">
					<div class="elementor-panel-footer-sub-menu-item" data-device-mode="desktop">
						<i class="elementor-icon eicon-device-desktop"></i>
						<span class="elementor-title">{ce__('Desktop')}</span>
						<span class="elementor-description">{ce__('Default Preview')}</span>
					</div>
					<div class="elementor-panel-footer-sub-menu-item" data-device-mode="tablet">
						<i class="elementor-icon eicon-device-tablet"></i>
						<span class="elementor-title">{ce__('Tablet')}</span>
						<span class="elementor-description">{ce__('Preview for 768px')}</span>
					</div>
					<div class="elementor-panel-footer-sub-menu-item" data-device-mode="mobile">
						<i class="elementor-icon eicon-device-mobile"></i>
						<span class="elementor-title">{ce__('Mobile')}</span>
						<span class="elementor-description">{ce__('Preview for 360px')}</span>
					</div>
				</div>
			</div>
		</div>
		<div id="elementor-panel-footer-help" class="elementor-panel-footer-tool">
			<span class="elementor-screen-only">{ce__('Help')}</span>
			<i class="fa fa-question-circle" title="{ce__('Help')}"></i>
			<div class="elementor-panel-footer-sub-menu-wrapper">
				<div id="elementor-panel-footer-help-title">{ce__('Need help?')}</div>
				<div class="elementor-panel-footer-sub-menu">
					<div id="elementor-panel-footer-watch-tutorial" class="elementor-panel-footer-sub-menu-item">
						<i class="elementor-icon fa fa-video-camera"></i>
						<span class="elementor-title">{ce__('Take a tour')}</span>
					</div>
					<div class="elementor-panel-footer-sub-menu-item">
						<i class="elementor-icon fa fa-external-link"></i>
						<a class="elementor-title" href="{ce__('http://docs.webshopworks.com/creative-elements')}" target="_blank">{ce__('Go to the Documentation')}</a>
					</div>
				</div>
			</div>
		</div>
		<div id="elementor-panel-footer-templates" class="elementor-panel-footer-tool">
			<span class="elementor-screen-only">{ce__('Templates')}</span>
			<i class="fa fa-folder" title="{ce__('Templates')}"></i>
			<div class="elementor-panel-footer-sub-menu-wrapper">
				<div class="elementor-panel-footer-sub-menu">
					<div id="elementor-panel-footer-templates-modal" class="elementor-panel-footer-sub-menu-item">
						<i class="elementor-icon fa fa-folder"></i>
						<span class="elementor-title">{ce__('Templates Library')}</span>
					</div>
					<div id="elementor-panel-footer-save-template" class="elementor-panel-footer-sub-menu-item">
						<i class="elementor-icon fa fa-save"></i>
						<span class="elementor-title">{ce__('Save Template')}</span>
					</div>
				</div>
			</div>
		</div>
		<div id="elementor-panel-footer-save" class="elementor-panel-footer-tool" title="{ce__('Save')}">
			<button class="elementor-button">
				<span class="elementor-state-icon">
					<i class="fa fa-spin fa-circle-o-notch "></i>
				</span>
				{ce__('Save')}
			</button>
		</div>
	</script>

	<script type="text/template" id="tmpl-elementor-mode-switcher-content">
		<input id="elementor-mode-switcher-preview-input" type="checkbox">
		<label for="elementor-mode-switcher-preview-input" id="elementor-mode-switcher-preview" title="{ce__('Preview')}">
			<span class="elementor-screen-only">{ce__('Preview')}</span>
			<i class="fa"></i>
		</label>
	</script>

	<script type="text/template" id="tmpl-editor-content">
		<div class="elementor-panel-navigation">
			<# _.each( elementData.tabs_controls, function( tabTitle, tabSlug ) { #>
			<div class="elementor-panel-navigation-tab elementor-tab-control-<#- tabSlug #>">
				<a href="#" data-tab="<#- tabSlug #>">
					<#= tabTitle #>
				</a>
			</div>
			<# } ); #>
		</div>
		<# if ( elementData.reload_preview ) { #>
			<div id="elementor-update-preview">
				<div id="elementor-update-preview-title">{ce__('Update changes to page')}</div>
				<div id="elementor-update-preview-button-wrapper">
					<button id="elementor-update-preview-button" class="elementor-button elementor-button-success">{ce__('Apply')}</button>
				</div>
			</div>
		<# } #>
		<div id="elementor-controls"></div>
	</script>

	<script type="text/template" id="tmpl-elementor-panel-schemes-disabled">
		<i class="elementor-panel-nerd-box-icon eicon-nerd"></i>
		<div class="elementor-panel-nerd-box-title"><#= '{ce__('{0} are disabled')}'.replace( '{0}', disabledTitle ) #></div>
		<div class="elementor-panel-nerd-box-message">{sprintf(ce__('You can enable it from the <a href="%s" target="_blank">Elementor settings page</a>.'), call_user_func('CE\\Settings::getUrl'))}</div>
	</script>

	<script type="text/template" id="tmpl-elementor-panel-scheme-color-item">
		<div class="elementor-panel-scheme-color-input-wrapper">
			<input type="text" class="elementor-panel-scheme-color-value" value="<#- value #>" data-alpha="true" />
		</div>
		<div class="elementor-panel-scheme-color-title"><#= title #></div>
	</script>

	<script type="text/template" id="tmpl-elementor-panel-scheme-typography-item">
		<div class="elementor-panel-heading">
			<div class="elementor-panel-heading-toggle">
				<i class="fa"></i>
			</div>
			<div class="elementor-panel-heading-title"><#= title #></div>
		</div>
		<div class="elementor-panel-scheme-typography-items elementor-panel-box-content">
		{$scheme_fields_keys = call_user_func('CE\\GroupControlTypography::getSchemeFieldsKeys')}
		{$typography_fields = call_user_func('CE\\GroupControlTypography::getFields')}
		{$scheme_fields = array_intersect_key($typography_fields, array_flip($scheme_fields_keys))}
		{foreach $scheme_fields as $option_name => $option}
			<div class="elementor-panel-scheme-typography-item">
				<div class="elementor-panel-scheme-item-title elementor-control-title">{$option['label']|cleanHtml}</div>
				<div class="elementor-panel-scheme-typography-item-value">
				{if 'select' === $option['type']}
					<select name="{$option_name|escape:'html':'UTF-8'}" class="elementor-panel-scheme-typography-item-field">
					{foreach $option['options'] as $field_key => $field_value}
						<option value="{$field_key|escape:'html':'UTF-8'}">{$field_value|cleanHtml}</option>
					{/foreach}
					</select>
				{elseif 'font' === $option['type']}
					<select name="{$option_name|escape:'html':'UTF-8'}" class="elementor-panel-scheme-typography-item-field">
						<option value="">{ce__('Default')}</option>

						<optgroup label="{ce__('System')}">
						{foreach call_user_func('CE\\Fonts::getFontsByGroups', ['system']) as $font_title => $font_type}
							<option value="{$font_title|escape:'html':'UTF-8'}">{$font_title|cleanHtml}</option>
						{/foreach}
						</optgroup>

						<optgroup label="{ce__('Google')}">
						{foreach call_user_func('CE\\Fonts::getFontsByGroups', ['googlefonts', 'earlyaccess']) as $font_title => $font_type}
							<option value="{$font_title|escape:'html':'UTF-8'}">{$font_title|cleanHtml}</option>
						{/foreach}
						</optgroup>
					</select>
				{elseif 'text' === $option['type']}
					<input name="{$option_name|escape:'html':'UTF-8'}" class="elementor-panel-scheme-typography-item-field" />
				{/if}
				</div>
			</div>
		{/foreach}
		</div>
	</script>

	<script type="text/template" id="tmpl-elementor-control-responsive-switchers">
		<div class="elementor-control-responsive-switchers">
			<a class="elementor-responsive-switcher elementor-responsive-switcher-desktop" data-device="desktop">
				<i class="eicon-device-desktop"></i>
			</a>
			<a class="elementor-responsive-switcher elementor-responsive-switcher-tablet" data-device="tablet">
				<i class="eicon-device-tablet"></i>
			</a>
			<a class="elementor-responsive-switcher elementor-responsive-switcher-mobile" data-device="mobile">
				<i class="eicon-device-mobile"></i>
			</a>
		</div>
	</script>
	{*/panel*}
	{*panel-elements*}
	<script type="text/template" id="tmpl-elementor-panel-elements">
		<div id="elementor-panel-elements-navigation" class="elementor-panel-navigation">
			<div id="elementor-panel-elements-navigation-all" class="elementor-panel-navigation-tab active" data-view="categories">{ce__('Elements')}</div>
			<div id="elementor-panel-elements-navigation-global" class="elementor-panel-navigation-tab" data-view="global">{ce__('Global')}</div>
		</div>
		<div id="elementor-panel-elements-languageselector-area"></div>
		<div id="elementor-panel-elements-search-area"></div>
		<div id="elementor-panel-elements-wrapper"></div>
	</script>

	<script type="text/template" id="tmpl-elementor-panel-categories">
		<div id="elementor-panel-categories"></div>

		<div id="elementor-panel-get-pro-elements" class="elementor-panel-nerd-box">
			<i class="elementor-panel-nerd-box-icon eicon-hypster"></i>
			<div class="elementor-panel-nerd-box-message panel-elements-category-title panel-elements-category-title-<#- name #>">{ce__('Get more with Elementor Pro')}</div>
			<a class="elementor-button elementor-button-default elementor-panel-nerd-box-link" target="_blank" href="https://go.elementor.com/pro-widgets/">{ce__('Go Pro')}</a>
		</div>
	</script>

	<script type="text/template" id="tmpl-elementor-panel-elements-category">
		<div class="panel-elements-category-title panel-elements-category-title-<#- name #>"><#= title #></div>
		<div class="panel-elements-category-items"></div>
	</script>

	<script type="text/template" id="tmpl-elementor-panel-element-search">
		<input id="elementor-panel-elements-search-input" placeholder="{ce__('Search Widget...')}" />
		<i class="fa fa-search"></i>
	</script>

	<script type="text/template" id="tmpl-elementor-element-library-element">
		<div class="elementor-element">
			<div class="icon">
				<i class="<#- icon #>"></i>
			</div>
			<div class="elementor-element-title-wrapper">
				<div class="title"><#= title #></div>
			</div>
		</div>
	</script>

	<script type="text/template" id="tmpl-elementor-panel-global">
		<div class="elementor-panel-nerd-box">
			<i class="elementor-panel-nerd-box-icon eicon-hypster"></i>
			<div class="elementor-panel-nerd-box-title">{ce__('Meet Our Global Widget')}</div>
			<div class="elementor-panel-nerd-box-message">{ce__('With this feature, you can save a widget as global, then add it to multiple areas. All areas will be editable from one single place.')}</div>
			<div class="elementor-panel-nerd-box-message">{ce__('This feature is only available on Elementor Pro.')}</div>
			<a class="elementor-button elementor-button-default elementor-panel-nerd-box-link" href="https://go.elementor.com/pro/" target="_blank">{ce__('Go Pro')}</a>
		</div>
	</script>
	{*/panel-elements*}
	{*repeater*}
	<script type="text/template" id="tmpl-elementor-repeater-row">
		<div class="elementor-repeater-row-tools">
			<div class="elementor-repeater-row-handle-sortable">
				<i class="fa fa-ellipsis-v"></i>
			</div>
			<div class="elementor-repeater-row-item-title"></div>
			<div class="elementor-repeater-row-tool elementor-repeater-tool-duplicate">
				<i class="fa fa-copy"></i>
			</div>
			<div class="elementor-repeater-row-tool elementor-repeater-tool-remove">
				<i class="fa fa-remove"></i>
			</div>
		</div>
		<div class="elementor-repeater-row-controls"></div>
	</script>
	{*/repeater*}
	{*templates*}
	<script type="text/template" id="tmpl-elementor-template-library-header">
		<div id="elementor-template-library-header-logo-area"></div>
		<div id="elementor-template-library-header-menu-area"></div>
		<div id="elementor-template-library-header-items-area">
			<div id="elementor-template-library-header-close-modal" class="elementor-template-library-header-item" title="{ce__('Close')}">
				<i class="eicon-close" title="{ce__('Close')}"></i>
			</div>
			<div id="elementor-template-library-header-tools2"></div>
			<div id="elementor-template-library-header-tools"></div>
		</div>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-header-logo">
		<img src="{$smarty.const._MODULE_DIR_|escape:'html':'UTF-8'}creativeelements/logo.png" width="20">
		<span>{ce__('Library')}</span>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-header-save">
		<i class="eicon-save" title="{ce__('Save Template')}"></i>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-header-import">
		<i class="material-icons">cloud_upload</i>
		<input type="file" accept=".json" id="elementor-template-library-header-import-file" title="{ce__('Import Template')}">
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-header-load">
		<i class="eicon-file-download" title="{ce__('Import Template')}" style="transform: translate(-50%, -50%) scale(-1);"></i>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-header-menu">
		<div id="elementor-template-library-menu-pre-made-templates" class="elementor-template-library-menu-item" data-template-source="remote">{ce__('Predesigned Templates')}</div>
		<div id="elementor-template-library-menu-my-templates" class="elementor-template-library-menu-item" data-template-source="local">{ce__('Templates list')}</div>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-header-preview">
		<div id="elementor-template-library-header-preview-insert-wrapper" class="elementor-template-library-header-item">
			<#= elementor.templates.getLayout().getTemplateActionButton( isPro ) #>
		</div>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-header-back">
		<i class="eicon-"></i><span>{ce__('Back To library')}</span>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-loading">
		<div class="elementor-loader-wrapper">
			<div class="elementor-loader">
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
			</div>
			<div class="elementor-loading-title">{ce__('Loading')}</div>
		</div>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-templates">
		<div id="elementor-template-library-templates-container"></div>
		<div id="elementor-template-library-footer-banner">
			<i class="eicon-nerd"></i>
			<div class="elementor-excerpt">{ce__('Stay tuned! More awesome templates coming real soon.')}</div>
		</div>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-template-remote">
		<div class="elementor-template-library-template-body">
			<div class="elementor-template-library-template-screenshot" style="background-image: url('<#- thumbnail #>');"></div>
			<div class="elementor-template-library-template-controls">
				<div class="elementor-template-library-template-preview">
					<i class="fa fa-search-plus"></i>
				</div>
				<#= elementor.templates.getLayout().getTemplateActionButton( isPro ) #>
			</div>
		</div>
		<div class="elementor-template-library-template-name"><#= title #></div>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-insert-button">
		<button class="elementor-template-library-template-insert elementor-button elementor-button-success" data-action="insert">
			<i class="eicon-file-download"></i><span class="elementor-button-title">{ce__('Insert')}</span>
		</button>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-get-pro-button">
		<button class="elementor-template-library-template-insert elementor-button elementor-button-go-pro" data-action="get-pro">
			<i class="fa fa-external-link-square"></i><span class="elementor-button-title">{ce__('Go Pro')}</span>
		</button>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-template-local">
		<div class="elementor-template-library-template-icon">
			<i class="fa fa-<#- 'section' === type ? 'columns' : 'file-text-o' #>"></i>
		</div>
		<div class="elementor-template-library-template-name"><#= title #></div>
		<div class="elementor-template-library-template-type"><#= elementor.translate( type ) #></div>
		<div class="elementor-template-library-template-controls">
			<button class="elementor-template-library-template-insert elementor-button elementor-button-success" data-action="insert">
				<i class="eicon-file-download"></i><span class="elementor-button-title">{ce__('Insert')}</span>
			</button>
			<div class="elementor-template-library-template-export">
				<a href="<#- export_link #>">
					<i class="fa fa-sign-out"></i><span class="elementor-template-library-template-control-title">{ce__('Export')}</span>
				</a>
			</div>
			<div class="elementor-template-library-template-delete">
				<i class="fa fa-trash-o"></i><span class="elementor-template-library-template-control-title">{ce__('Delete')}</span>
			</div>
			<div class="elementor-template-library-template-preview">
				<i class="eicon-zoom-in"></i><span class="elementor-template-library-template-control-title">{ce__('Preview')}</span>
			</div>
		</div>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-save-template">
		<div class="elementor-template-library-blank-title"><#= title #></div>
		<div class="elementor-template-library-blank-excerpt"><#= description #></div>
		<form id="elementor-template-library-save-template-form">
			<input id="elementor-template-library-save-template-name" name="title" placeholder="{ce__('Enter Template Name')}" required>
			<button id="elementor-template-library-save-template-submit" class="elementor-button elementor-button-success">
				<span class="elementor-state-icon">
					<i class="fa fa-spin fa-circle-o-notch "></i>
				</span>
				{ce__('Save')}
			</button>
		</form>
		<div class="elementor-template-library-blank-footer">
			{ce__('What is Library?')}
			<a class="elementor-template-library-blank-footer-link" href="https://go.elementor.com/docs-library/" target="_blank">{ce__('Read our tutorial on using Library templates.')}</a>
		</div>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-load-template">
		<div class="elementor-template-library-blank-title"><#= elementor.translate( 'load_your_template'  ) #></div>
		<div class="elementor-template-library-blank-excerpt">{ce__('Import your .json design file from your local pc')}</div>
		<form id="elementor-template-library-load-template-form">
			<div id="elementor-template-library-load-wrapper">
			<button id="elementor-template-library-load-btn-file">{ce__('Select template .json file')}</button>
			<input id="elementor-template-library-load-template-file" type="file" name="file" required>
			</div>
			<button id="elementor-template-library-load-template-submit" class="elementor-button elementor-button-success">
				<span class="elementor-state-icon">
					<i class="fa fa-spin fa-circle-o-notch "></i>
				</span>
				{ce__('load')}
			</button>
		</form>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-templates-empty">
		<div id="elementor-template-library-templates-empty-icon">
			<i class="eicon-nerd"></i>
		</div>
		<div class="elementor-template-library-blank-title">{ce__('Haven’t Saved Templates Yet?')}</div>
		<div class="elementor-template-library-blank-excerpt">{ce__('This is where your templates should be. Design it. Save it. Reuse it.')}</div>
		<div class="elementor-template-library-blank-footer">
			{ce__('What is Library?')}
			<a class="elementor-template-library-blank-footer-link" href="https://go.elementor.com/docs-library/" target="_blank">{ce__('Read our tutorial on using Library templates.')}</a>
		</div>
	</script>

	<script type="text/template" id="tmpl-elementor-template-library-preview">
		<if{literal}rame></if{/literal}rame>
	</script>
	{*/templates*}
</body>
</html>