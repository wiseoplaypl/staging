{**
 * Creative Elements - Elementor based PageBuilder
 *
 * @author    WebshopWorks
 * @copyright 2019-2020 WebshopWorks.com
 * @license   One domain support license
 *}

{function ElementBase_printTemplate this=null settings='' content=''}
	<script type="text/html" id="tmpl-elementor-{$this->getType()|escape:'html':'UTF-8'}-{$this->getName()|escape:'html':'UTF-8'}-content">
		{$settings|cleanHtml}
		{$content|cleanHtml}
	</script>
{/function}

{function ElementBase_renderSettings this=null}
	<div class="elementor-element-overlay">
		<div class="elementor-editor-element-settings elementor-editor-{$this->getType()|escape:'html':'UTF-8'}-settings elementor-editor-{$this->getName()|escape:'html':'UTF-8'}-settings">
			<ul class="elementor-editor-element-settings-list">
				<li class="elementor-editor-element-setting elementor-editor-element-add">
					<a href="#" title="{ce__('Add Widget')|escape:'html':'UTF-8'}">
						<span class="elementor-screen-only">{ce__('Add')}</span>
						<i class="fa fa-plus"></i>
					</a>
				</li>
				<li class="elementor-editor-element-setting elementor-editor-element-duplicate">
					<a href="#" title="{ce__('Duplicate Widget')|escape:'html':'UTF-8'}">
						<span class="elementor-screen-only">{ce__('Duplicate')}</span>
						<i class="fa fa-files-o"></i>
					</a>
				</li>
				<li class="elementor-editor-element-setting elementor-editor-element-remove">
					<a href="#" title="{ce__('Remove Widget')|escape:'html':'UTF-8'}">
						<span class="elementor-screen-only">{ce__('Remove')}</span>
						<i class="fa fa-trash-o"></i>
					</a>
				</li>
			</ul>
		</div>
	</div>
{/function}

{capture ElementSection_renderSettings}
	<div class="elementor-element-overlay"></div>
{/capture}

{capture ElementSection_contentTemplate}
	<# if ( 'video' === settings.background_background ) {
		var videoLink = settings.background_video_link;

		if ( videoLink ) {
			var videoID = elementor.helpers.getYoutubeIDFromURL( settings.background_video_link ); #>

			<div class="elementor-background-video-container elementor-hidden-phone">
				<# if ( videoID ) { #>
					<div class="elementor-background-video" data-video-id="<#- videoID #>"></div>
				<# } else { #>
					<video class="elementor-background-video" src="<#- videoLink #>" autoplay loop muted></video>
				<# } #>
			</div>
		<# }
		if ( settings.background_video_fallback ) { #>
			<div class="elementor-background-video-fallback" style="background-image: url('<#- elementor.imagesManager.getImageUrl( settings.background_video_fallback ) #>')"></div>
		<# }
	}
	if ( 'classic' === settings.background_overlay_background ) { #>
		<div class="elementor-background-overlay"></div>
	<# } #>
	<div class="elementor-container elementor-column-gap-<#- settings.gap #>" <# if ( settings.getRenderAttributeString ) { #><#= settings.getRenderAttributeString( 'wrapper' ) #><# } #> >
		<div class="elementor-row"></div>
	</div>
{/capture}

{capture ElementColumn_renderSettings}
	<div class="elementor-element-overlay">
		<div class="column-title"></div>
		<div class="elementor-editor-element-settings elementor-editor-column-settings">
			<ul class="elementor-editor-element-settings-list elementor-editor-column-settings-list">
				<li class="elementor-editor-element-setting elementor-editor-element-trigger">
					<a href="#" title="{ce__('Drag Column')|escape:'html':'UTF-8'}">{ce__('Column')}</a>
				</li>
				{foreach call_user_func('CE\\ElementColumn::getEditTools') as $edit_tool_name => $edit_tool}
					<li class="elementor-editor-element-setting elementor-editor-element-{$edit_tool_name|escape:'html':'UTF-8'}">
						<a href="#" title="{ce__($edit_tool['title'])|escape:'html':'UTF-8'}">
							<span class="elementor-screen-only">{ce__($edit_tool['title'])}</span>
							<i class="{$edit_tool['icon']|escape:'html':'UTF-8'}"></i>
						</a>
					</li>
				{/foreach}
			</ul>
			<ul class="elementor-editor-element-settings-list  elementor-editor-section-settings-list">
				<li class="elementor-editor-element-setting elementor-editor-element-trigger">
					<a href="#" title="{ce__('Drag Section')|escape:'html':'UTF-8'}">{ce__('Section')}</a>
				</li>
				{foreach call_user_func('CE\\ElementSection::getEditTools') as $edit_tool_name => $edit_tool}
					<li class="elementor-editor-element-setting elementor-editor-element-{$edit_tool_name|escape:'html':'UTF-8'}">
						<a href="#" title="{ce__($edit_tool['title'])|escape:'html':'UTF-8'}">
							<span class="elementor-screen-only">{ce__($edit_tool['title'])}</span>
							<i class="{$edit_tool['icon']|escape:'html':'UTF-8'}"></i>
						</a>
					</li>
				{/foreach}
			</ul>
		</div>
	</div>
{/capture}

{capture ElementColumn_contentTemplate}
	<div class="elementor-column-wrap">
		<div class="elementor-widget-wrap"></div>
	</div>
{/capture}
