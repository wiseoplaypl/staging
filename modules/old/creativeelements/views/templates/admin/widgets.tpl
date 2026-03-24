{**
 * Creative Elements - Elementor based PageBuilder
 *
 * @author    WebshopWorks
 * @copyright 2019-2020 WebshopWorks.com
 * @license   One domain support license
 *}

{function WidgetBase_printTemplate this=null type='' settings='' content=''}
	<script type="text/html" id="tmpl-elementor-{$type|escape:'html':'UTF-8'}-{$this->getName()|escape:'html':'UTF-8'}-content">
		{$settings|cleanHtml}
		<div class="elementor-widget-container">
			{$content|cleanHtml}
		</div>
	</script>
{/function}

{function WidgetBase_renderSettings this=null type=''}
	<div class="elementor-editor-element-settings elementor-editor-{$type|escape:'html':'UTF-8'}-settings elementor-editor-{$this->getName()|escape:'html':'UTF-8'}-settings">
		<ul class="elementor-editor-element-settings-list">
		{foreach call_user_func('CE\\WidgetBase::getEditTools') as $edit_tool_name => $edit_tool}
			<li class="elementor-editor-element-setting elementor-editor-element-{$edit_tool_name|escape:'html':'UTF-8'}">
				<a href="#" title="{ce__($edit_tool['title'])|escape:'html':'UTF-8'}">
					<span class="elementor-screen-only">{ce__($edit_tool['title'])}</span>
					<i class="{$edit_tool['icon']|escape:'html':'UTF-8'}"></i>
				</a>
			</li>
		{/foreach}
		</ul>
	</div>
{/function}

{capture WidgetAccordion}
	<div class="elementor-accordion" data-active-section="<#- editSettings.activeItemIndex ? editSettings.activeItemIndex : 0 #>">
	<#
	if ( settings.tabs ) {
		var counter = 1;
		_.each( settings.tabs, function( item ) { #>
			<div class="elementor-accordion-item">
				<div class="elementor-accordion-title" data-section="<#- counter #>">
					<span class="elementor-accordion-icon elementor-accordion-icon-<#- settings.icon_align #>">
						<i class="fa"></i>
					</span>
					<#= item.tab_title #>
				</div>
				<div class="elementor-accordion-content elementor-clearfix" data-section="<#- counter #>"><#= item.tab_content #></div>
			</div>
			<#
			counter++;
		} );
	} #>
	</div>
{/capture}

{capture WidgetAjaxSearch}
	<#
	var iconClass = 'fa fa-search';

	if ( 'arrow' === settings.icon ) {
		if ( elementor.config.is_rtl ) {
			iconClass = 'fa fa-arrow-left';
		} else {
			iconClass = 'fa fa-arrow-right';
		}
	}
	#>
	<form class="elementor-ajax-search" action="" role="search">
		<input type="hidden" name="controller" value="search">
		<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
		<div class="elementor-ajax-search-wrapper">
			<# if ( 'minimal' === settings.skin ) { #>
				<div class="elementor-ajax-search-icon">
					<i class="fa fa-search" aria-hidden="true"></i>
				</div>
			<# } #>
			<input type="search"
				   name="s"
				   title="{ce__('Search')|escape:'html':'UTF-8'}"
				   class="elementor-ajax-search-field"
				   placeholder="<#- settings.placeholder #>">

			<# if ( 'classic' === settings.skin ) { #>
				<button class="elementor-ajax-search-submit" type="submit">
					<# if ( 'icon' === settings.button_type ) { #>
						<i class="<#- iconClass #>" aria-hidden="true"></i>
					<# } else if ( settings.button_text ) { #>
						<#= settings.button_text #>
					<# } #>
				</button>
			<# } #>
		</div>
	</form>
{/capture}

{capture WidgetAlert}
	<#
	var html = '<div class="elementor-alert elementor-alert-' + settings.alert_type + '" role="alert">';
	if ( '' !== settings.title ) {
		html += '<span class="elementor-alert-title">' + settings.alert_title + '</span>';

		if ( '' !== settings.description ) {
			html += '<span class="elementor-alert-description">' + settings.alert_description + '</span>';
		}

		if ( 'show' === settings.show_dismiss ) {
			html += '<button type="button" class="elementor-alert-dismiss">X</button></div>';
		}

		print( html );
	}
	#>
{/capture}

{capture WidgetButton}
	<div class="elementor-button-wrapper">
		<a class="elementor-button elementor-button-<#- settings.button_type #> elementor-size-<#- settings.size #> elementor-animation-<#- settings.hover_animation #>" href="<#- settings.link.url #>">
			<span class="elementor-button-inner">
				<# if ( settings.icon ) { #>
				<span class="elementor-button-icon elementor-align-icon-<#- settings.icon_align #>">
					<i class="<#- settings.icon #>"></i>
				</span>
				<# } #>
				<span class="elementor-button-text"><#= settings.text #></span>
			</span>
		</a>
	</div>
{/capture}

{capture WidgetCallToAction}
	<#
	var view = new CeView(),
		wrapperTag = 'div',
		buttonTag = 'a',
		animationClass,
		btnSizeClass = 'elementor-size-' + settings.button_size,
		printBg = true,
		printContent = true;

	if ( 'box' === settings.link_click || !settings.button ) {
		wrapperTag = 'a';
		buttonTag = 'button';
		view.addRenderAttribute( 'wrapper', 'href', '#' );
	}

	if ( settings.bg_image.url ) {
		var bgImageUrl = elementor.imagesManager.getImageUrl( settings.bg_image );

		view.addRenderAttribute( 'background_image', 'style', "background-image: url('" + bgImageUrl + "');" );
	} else if ( 'classic' == settings.skin ) {
		printBg = false;
	}

	if ( ! settings.title && ! settings.description_text && ! settings.button && 'none' == settings.graphic_element ) {
		printContent = false;
	}

	if ( 'icon' === settings.graphic_element ) {
		var iconWrapperClasses = 'elementor-icon-wrapper';
			iconWrapperClasses += ' elementor-cta-image';
			iconWrapperClasses += ' elementor-view-' + settings.icon_view;
		if ( 'default' !== settings.icon_view ) {
			iconWrapperClasses += ' elementor-shape-' + settings.icon_shape;
		}
		view.addRenderAttribute( 'graphic_element', 'class', iconWrapperClasses );

	} else if ( 'image' === settings.graphic_element && settings.graphic_image.url ) {
		var imageUrl = elementor.imagesManager.getImageUrl( settings.graphic_image );

		view.addRenderAttribute( 'graphic_element', 'class', 'elementor-cta-image' );
	}

	if ( settings.content_animation && 'cover' === settings.skin ) {
		var animationClass = 'elementor-animated-item--' + settings.content_animation;

		view.addRenderAttribute( 'title', 'class', animationClass );
		view.addRenderAttribute( 'description', 'class', animationClass );
		view.addRenderAttribute( 'graphic_element', 'class', animationClass );
	}

	view.addRenderAttribute( 'title', 'class', [ 'elementor-cta-title', 'elementor-content-item' ] );
	view.addRenderAttribute( 'description', 'class', [ 'elementor-cta-description', 'elementor-content-item' ] );
	view.addRenderAttribute( 'button', 'class', [ 'elementor-button', btnSizeClass ] );
	view.addRenderAttribute( 'graphic_element', 'class', 'elementor-content-item' );
	#>

	<<#- wrapperTag #> class="elementor-cta" <#= view.getRenderAttributeString( 'wrapper' ) #>>

	<# if ( printBg ) { #>
		<div class="elementor-cta-bg-wrapper">
			<div class="elementor-cta-bg elementor-bg" <#= view.getRenderAttributeString( 'background_image' ) #>></div>
			<div class="elementor-cta-bg-overlay"></div>
		</div>
	<# } #>
	<# if ( printContent ) { #>
		<div class="elementor-cta-content">
			<# if ( imageUrl ) { #>
				<div <#= view.getRenderAttributeString( 'graphic_element' ) #>>
					<img src="<#- imageUrl #>">
				</div>
			<#  } else if ( 'icon' === settings.graphic_element && settings.icon ) { #>
				<div <#= view.getRenderAttributeString( 'graphic_element' ) #>>
					<div class="elementor-icon">
						<i class="<#- settings.icon #>"></i>
					</div>
				</div>
			<# } #>
			<# if ( settings.title ) { #>
				<<#- settings.title_tag #> <#= view.getRenderAttributeString( 'title' ) #>><#= settings.title #></<#- settings.title_tag #>>
			<# } #>

			<# if ( settings.description_text ) { #>
				<div <#= view.getRenderAttributeString( 'description' ) #>><#= settings.description_text #></div>
			<# } #>

			<# if ( settings.button ) { #>
				<div class="elementor-cta-button-wrapper elementor-content-item <#- animationClass #>">
					<<#- buttonTag #> href="#" <#= view.getRenderAttributeString( 'button' ) #>>
						<# if ( settings.button_icon ) { #>
						<span class="elementor-button-icon elementor-align-icon-<#- settings.button_icon_align #>">
							<i class="<#- settings.button_icon #>"></i>
						</span>
						<# } #>
						<span class="elementor-button-text"><#= settings.button #></span>
					</<#- buttonTag #>>
				</div>
			<# } #>
		</div>
	<# } #>
	<# if ( settings.ribbon_title ) {
		var ribbonClasses = 'elementor-ribbon';

		if ( settings.ribbon_horizontal_position ) {
			ribbonClasses += ' elementor-ribbon-' + settings.ribbon_horizontal_position;
		} #>
		<div class="<#- ribbonClasses #>">
			<div class="elementor-ribbon-inner"><#= settings.ribbon_title #></div>
		</div>
	<# } #>
	</<#- wrapperTag #>>
{/capture}

{function WidgetContactForm this=null}
	<#
	var contacts = {Contact::getContacts(Context::getContext()->language->id)|json_encode},
		email_placeholder = settings.email_placeholder || {if $smarty.const._CE_PS16_}''{else}{$this->trans('your@email.com', [], 'Shop.Forms.Help')|json_encode}{/if},
		message_placeholder = settings.message_placeholder || {if $smarty.const._CE_PS16_}''{else}{$this->trans('How can we help?', [], 'Shop.Forms.Help')|json_encode}{/if},
		upload = {$this->upload|intval};
	#>
	<form class="elementor-contact-form">
		<div class="elementor-form-fields-wrapper">
			<# if (settings.subject_id <= 0) { #>
				<div class="elementor-field-group elementor-column elementor-col-<#- settings.subject_width #> elementor-field-type-select">
					<# if (settings.show_labels) { #>
						<label class="elementor-field-label"><#- settings.subject_label || {$this->trans('Subject Heading')|json_encode} #></label>
					<# } #>
					<div class="elementor-select-wrapper">
						<select name="id_contact" class="elementor-field elementor-field-textual elementor-size-<#- settings.input_size #>">
						<# _.each(contacts, function(contact) { #>
							<option><#- contact.name #></option>
						<# }) #>
						</select>
					</div>
				</div>
			<# } #>
			<div class="elementor-field-group elementor-column elementor-col-<#- settings.email_width #> elementor-field-type-email">
				<# if (settings.show_labels) { #>
					<label class="elementor-field-label"><#- settings.email_label || {$this->trans('Email address')|json_encode} #></label>
				<# } #>
				<input type="email" name="from" placeholder="<#- email_placeholder #>" class="elementor-field elementor-field-textual elementor-size-<#- settings.input_size #>">
			</div>
			<# if (upload && settings.show_upload) { #>
				<div class="elementor-field-group elementor-column elementor-col-<#- settings.upload_width #> elementor-field-type-file">
					<# if (settings.show_labels) { #>
						<label class="elementor-field-label"><#- settings.upload_label || {$this->trans('Attach File')|json_encode} #></label>
					<# } #>
					<input type="file" name="fileUpload" class="elementor-field elementor-size-<#- settings.input_size #>">
				</div>
			<# } #>
			<div class="elementor-field-group elementor-column elementor-col-<#- settings.message_width #> elementor-field-type-textarea">
				<# if (settings.show_labels) { #>
					<label class="elementor-field-label"><#- settings.message_label || {$this->trans('Message')|json_encode} #></label>
				<# } #>
				<textarea name="message" placeholder="<#- message_placeholder #>" class="elementor-field elementor-field-textual elementor-size-<#- settings.input_size #>" rows="<#- settings.message_rows #>"></textarea>
			</div>
			{if $this->gdpr}
				<div class="elementor-field-group elementor-column elementor-col-100 elementor-field-type-gdpr">
					<label class="elementor-field-label">
						<input type="checkbox"><span class="elementor-checkbox-label">{$this->gdpr_msg|cleanHtml}</span>
					</label>
				</div>
			{/if}
			<div class="elementor-field-group elementor-column elementor-col-<#- settings.button_width #> elementor-field-type-submit">
				<button type="submit" name="submitMessage" value="Send" class="elementor-button elementor-size-<#- settings.button_size #> elementor-animation-<#- settings.button_hover_animation #>">
					<span class="elementor-button-inner">
						<span class="elementor-button-text"><#- settings.button_text || {$this->trans('Send')|json_encode} #></span>
						<# if (settings.icon) { #>
							<span class="elementor-button-icon elementor-align-icon-<#- settings.icon_align #>">
								<i class="<#- settings.icon #>"></i>
							</span>
						<# } #>
					</span>
				</button>
			</div>
		</div>
	</form>
{/function}

{capture WidgetCounter}
	<div class="elementor-counter">
		<div class="elementor-counter-number-wrapper">
			<span class="elementor-counter-number-prefix"><#= settings.prefix #></span>
			<span class="elementor-counter-number" data-duration="<#- settings.duration #>" data-to-value="<#- settings.ending_number #>" data-delimiter="<#- settings.thousand_separator ? ',' : '' #>"><#= settings.starting_number #></span>
			<span class="elementor-counter-number-suffix"><#= settings.suffix #></span>
		</div>
		<# if ( settings.title ) { #>
			<div class="elementor-counter-title"><#= settings.title #></div>
		<# } #>
	</div>
{/capture}

{capture WidgetDivider}
	<div class="elementor-divider">
		<span class="elementor-divider-separator"></span>
	</div>
{/capture}

{function WidgetEmailSubscription this=null}
	<# var placeholder = settings.email_placeholder || {$this->email_placeholder|json_encode} #>
	<form class="elementor-email-subscription">
		<div class="elementor-field-type-subscribe">
			<input type="email" placeholder="<#- placeholder #>" class="elementor-field elementor-field-textual" required>
			<button type="submit" class="elementor-button elementor-animation-<#- settings.hover_animation #>">
				<span class="elementor-button-inner">
				<# if (settings.icon && 'left' == settings.icon_align) { #>
					<span class="elementor-button-icon"><i class="<#- settings.icon #>"></i></span>
				<# } #>
				<# if (settings.button.trim() || !settings.button) { #>
					<span class="elementor-button-text"><#- settings.button || {$this->button_placeholder|json_encode} #></span>
				<# } #>
				<# if (settings.icon && 'right' == settings.icon_align) { #>
					<span class="elementor-button-icon"><i class="<#- settings.icon #>"></i></span>
				<# } #>
				</span>
			</button>
		</div>
		{if $this->gdpr}
			<div class="elementor-field-type-gdpr">
				<label class="elementor-field-label">
					<input type="checkbox"><span class="elementor-checkbox-label">{$this->gdpr_msg|cleanHtml}</span>
				</label>
			</div>
		{/if}
	</form>
{/function}

{capture WidgetFlipBox}
	<#
	if ( 'icon' === settings.graphic_element ) {
		var iconWrapperClasses = 'elementor-icon-wrapper';
		iconWrapperClasses += ' elementor-view-' + settings.icon_view;

		if ( 'default' !== settings.icon_view ) {
			iconWrapperClasses += ' elementor-shape-' + settings.icon_shape;
		}
	}
	if ( 'icon' === settings.graphic_element_b ) {
		var iconWrapperClassesBack = 'elementor-icon-wrapper';
		iconWrapperClassesBack += ' elementor-view-' + settings.icon_view_b;

		if ( 'default' !== settings.icon_view_b ) {
			iconWrapperClassesBack += ' elementor-shape-' + settings.icon_shape_b;
		}
	}
	var view = new CeView(),
		titleTagFront = settings.title_size_a,
		titleTagBack = settings.title_size_b,
		btnSizeClass = 'elementor-size-' + settings.button_size,
		wrapperTag = 'div',
		buttonTag = 'div';

	view.addRenderAttribute('button', 'class', ['elementor-button', btnSizeClass]);
	view.addRenderAttribute('flipbox-back', 'class', 'elementor-flip-box-back elementor-flip-box-side');

	if (settings.link) {
		if ( 'box' === settings.link_click || !settings.button ) {
			wrapperTag = 'a';
			buttonTag = 'button';
			view.addRenderAttribute( 'flipbox-back', 'href', settings.link.url );

			if (settings.link.is_external) {
				view.addRenderAttribute('flipbox-back', 'target', '_blank');
			}
		} else {
			buttonTag = 'button';
			view.addRenderAttribute('button', 'href', settings.link.url);

			if (settings.link.is_external) {
				view.addRenderAttribute('button', 'target', '_blank');
			}
		}
	}
	#>
	<div class="elementor-flip-box">
		<div class="elementor-flip-box-front elementor-flip-box-side">
			<div class="elementor-flip-box-overlay">
				<div class="elementor-flip-box-content">
				<# if ( 'icon' === settings.graphic_element ) { #>
					<div class="<#- iconWrapperClasses #>">
						 <div class="elementor-icon">
							<i class="<#- settings.icon #>"></i>
						</div>
					</div>
				<# } else if ( 'image' === settings.graphic_element && settings.image.url ) { #>
					<div class="elementor-flip-box-image">
						<img src="<#- elementor.imagesManager.getImageUrl( settings.image ) #>">
					</div>
				<# } #>
				<# if ( settings.title_text_a ) { #>
					<<#- titleTagFront #> class="elementor-flip-box-title"><#= settings.title_text_a #></<#- titleTagFront #>>
				<# } #>
				<# if ( settings.description_text_a ) { #>
					<div class="elementor-flip-box-description"><#= settings.description_text_a #></div>
				<# } #>
				</div>
			</div>
		</div>
		<<#- wrapperTag #> <#= view.getRenderAttributeString('flipbox-back') #>>
			<div class="elementor-flip-box-overlay">
				<div class="elementor-flip-box-content">
				<# if ( 'icon' === settings.graphic_element_b ) { #>
					<div class="<#- iconWrapperClassesBack #>">
						 <div class="elementor-icon">
							<i class="<#- settings.icon_b #>"></i>
						</div>
					</div>
				<# } else if ( 'image' === settings.graphic_element_b && settings.image_b.url ) { #>
					<div class="elementor-flip-box-image">
						<img src="<#- elementor.imagesManager.getImageUrl( settings.image_b ) #>">
					</div>
				<# } #>
				<# if ( settings.title_text_b ) { #>
					<<#- titleTagBack #> class="elementor-flip-box-title"><#= settings.title_text_b #></<#- titleTagBack #>>
				<# } #>
				<# if ( settings.description_text_b ) { #>
					<div class="elementor-flip-box-description"><#= settings.description_text_b #></div>
				<# } #>
				<# if (settings.button) { #>
					<<#- buttonTag #> <#= view.getRenderAttributeString('button') #>>
						<# if (settings.button_icon) { #>
							<span class="elementor-button-icon elementor-align-icon-<#- settings.button_icon_align #>">
								<i class="<#- settings.button_icon #>"></i>
							</span>
						<# } #>
						<span class="elementor-button-text"><#= settings.button #></span>
					</<#- buttonTag #>>
				<# } #>
				</div>
			</div>
		</<#- wrapperTag #>>
	</div>
{/capture}

{capture WidgetHeading}
	<#
	if ( '' !== settings.title ) {
		var title_html = '<' + settings.header_size  + ' class="elementor-heading-title elementor-size-' + settings.size + '"><span>' + settings.title + '</span></' + settings.header_size + '>';
	}
	if ( '' !== settings.link.url ) {
		var title_html = '<' + settings.header_size  + ' class="elementor-heading-title elementor-size-' + settings.size + '"><a href="' + settings.link.url + '"><span>' + title_html + '</span></a></' + settings.header_size + '>';
	}
	print( title_html );
	#>
{/capture}

{capture WidgetHtml}
	<#= settings.html #>
{/capture}

{capture WidgetIcon}
	<# var link = settings.link.url ? 'href="' + settings.link.url + '"' : '', iconTag = link ? 'a' : 'div'; #>
	<div class="elementor-icon-wrapper">
		<<#= iconTag #> class="elementor-icon elementor-animation-<#- settings.hover_animation #>" <#= link #>>
			<i class="<#- settings.icon #>"></i>
		</<#= iconTag #>>
	</div>
{/capture}

{capture WidgetIconBox}
	<#
	var link = settings.link.url ? 'href="' + settings.link.url + '"' : '',
		iconTag = link ? 'a' : 'span';
	#>
	<div class="elementor-icon-box-wrapper">
		<div class="elementor-icon-box-icon">
			<<#= iconTag + ' ' + link #> class="elementor-icon elementor-animation-<#- settings.hover_animation #>">
				<i class="<#- settings.icon #>"></i>
			</<#= iconTag #>>
		</div>
		<div class="elementor-icon-box-content">
			<<#= settings.title_size #> class="elementor-icon-box-title">
				<<#= iconTag + ' ' + link #>><#= settings.title_text #></<#= iconTag #>>
			</<#= settings.title_size #>>
			<div class="elementor-icon-box-description"><#= settings.description_text #></div>
		</div>
	</div>
{/capture}

{capture WidgetIconList}
	<ul class="elementor-icon-list-items">
	<# if ( settings.icon_list ) {
		_.each( settings.icon_list, function( item ) { #>
			<li class="elementor-icon-list-item">
				<# if ( item.link && item.link.url ) { #>
					<a href="<#- item.link.url #>">
				<# } #>
				<span class="elementor-icon-list-icon">
					<i class="<#- item.icon #>"></i>
				</span>
				<span class="elementor-icon-list-text"><#= item.text #></span>
				<# if ( item.link && item.link.url ) { #>
					</a>
				<# } #>
			</li>
			<#
		} );
	} #>
	</ul>
{/capture}

{capture WidgetImage}
	<# if ( settings.image.url ) {
		var image_url = elementor.imagesManager.getImageUrl( settings.image ),
			link_url;

		if ( 'custom' === settings.link_to ) {
			link_url = settings.link.url;
		}
		if ( 'file' === settings.link_to ) {
			link_url = image_url;
		}
		#><div class="elementor-image<#- settings.shape ? ' elementor-image-shape-' + settings.shape : '' #>"><#
		var imgClass = '',
			hasCaption = '' !== settings.caption;

		if ( '' !== settings.hover_animation ) {
			imgClass = 'elementor-animation-' + settings.hover_animation;
		}
		if ( hasCaption ) {
			#><figure class="wp-caption"><#
		}
		if ( link_url ) {
			#><a href="<#- link_url #>"><#
		}
		#><img src="<#- image_url #>" class="<#- imgClass #>" /><#

		if ( link_url ) {
			#></a><#
		}
		if ( hasCaption ) {
			#><figcaption class="widget-image-caption wp-caption-text"><#= settings.caption #></figcaption><#
		}
		if ( hasCaption ) {
			#></figure><#
		}
		#></div><#
	} #>
{/capture}

{capture WidgetImageBox}
	<#
	var html = '<div class="elementor-image-box-wrapper">';

	if ( settings.image.url ) {
		var imageSrc = elementor.imagesManager.getImageUrl( settings.image );
		var imageHtml = '<img src="' + imageSrc + '"  alt="' + settings.caption + '" class="elementor-animation-' + settings.hover_animation + '" />';

		if ( settings.link.url ) {
			imageHtml = '<a href="' + settings.link.url + '">' + imageHtml + '</a>';
		}
		html += '<figure class="elementor-image-box-img">' + imageHtml + '</figure>';
	}
	var hasContent = !! ( settings.title_text || settings.description_text );

	if ( hasContent ) {
		html += '<div class="elementor-image-box-content">';

		if ( settings.title_text ) {
			var title_html = settings.title_text;

			if ( settings.link.url ) {
				title_html = '<a href="' + settings.link.url + '">' + title_html + '</a>';
			}
			html += '<' + settings.title_size  + ' class="elementor-image-box-title">' + title_html + '</' + settings.title_size  + '>';
		}
		if ( settings.description_text ) {
			html += '<div class="elementor-image-box-description">' + settings.description_text + '</div>';
		}
		html += '</div>';
	}
	html += '</div>';

	print( html );
	#>
{/capture}

{capture WidgetImageHotspot}
	<#
	if (!settings.image.url) {
		return;
	}
	var animation = settings.icon_animation ? 'elementor-animation-' + settings.icon_animation : '';
	#>
	<div class="elementor-image-hotspot">
		<img class="elementor-image" src="<#- elementor.imagesManager.getImageUrl( settings.image ) #>" />
		<# _.each( settings.hotspots, function( item ) { #>
			<div class="elementor-image-hotspot-wrapper elementor-repeater-item-<#= item._id #>">
				<# var link = item.link.url ? 'href="' + item.link.url + '"' : '', iconTag = link ? 'a' : 'div'; #>
				<<#= iconTag #> class="elementor-icon elementor-animation-<#- settings.icon_animation #>" <#= link #>>
					<i class="<#- settings.icon #>"></i>
				</<#= iconTag #>>
				<div class="elementor-image-hotspot-content">
				<# if ( item.title ) { #>
					<<#= settings.title_size #> class="elementor-image-hotspot-title"><#= item.title #></<#= settings.title_size #>>
				<# } #>
				<# if ( item.description ) { #>
					<div class="elementor-image-hotspot-description"><#= item.description #></div>
				<# } #>
				</div>
			</div>
		<# } ); #>
	</div>
{/capture}

{capture WidgetLayerSlider}
	<if{literal}rame src="https://creativeslider.webshopworks.com/promo-slider.html" style="width: 100%; height: 66vh; border: none;"></if{/literal}rame>
{/capture}

{function WidgetLayerSlider_initScript id=''}
	<script data-cfasync="false">
	var js = top.elementor.$previewContents.find('#layerslider_{$id|escape:'html':'UTF-8'}').prev().html() || '';
	{literal}if (js = js.match(/{([^]*)}/)) top.elementor.$preview[0].contentWindow.eval(js[1]);{/literal}
	</script>
{/function}

{capture WidgetMenuAnchor}
	<div class="elementor-menu-anchor"<#= settings.anchor ? ' id="' + settings.anchor + '"' : '' #>></div>
{/capture}

{capture WidgetProgress}
	<# if ( settings.title ) { #>
		<span class="elementor-title"><#= settings.title #></span>
	<# } #>
	<div class="elementor-progress-wrapper progress-<#- settings.progress_type #>" role="timer">
		<div class="elementor-progress-bar" data-max="<#- settings.percent.size #>">
			<span class="elementor-progress-text"><#= settings.inner_text #></span>
			<# if ( 'hide' !== settings.display_percentage ) { #>
				<span class="elementor-progress-percentage"><#= settings.percent.size #>%</span>
			<# } #>
		</div>
	</div>
{/capture}

{capture WidgetSocialIcons}
	<div class="elementor-social-icons-wrapper">
	<# _.each( settings.social_icon_list, function( item ) {
		var link = item.link ? item.link.url : '',
			social = item.social.replace( 'fa fa-', '' ); #>
		<a class="elementor-icon elementor-social-icon elementor-social-icon-<#- social #>" href="<#- link #>">
			<i class="<#- item.social #>"></i>
		</a>
	<# } ); #>
	</div>
{/capture}

{capture WidgetSpacer}
	<div class="elementor-spacer">
		<div class="elementor-spacer-inner"></div>
	</div>
{/capture}

{capture WidgetTabs}
	<div class="elementor-tabs" data-active-tab="<#- editSettings.activeItemIndex ? editSettings.activeItemIndex : 0 #>">
	<# if ( settings.tabs ) {
		var counter = 1; #>
		<div class="elementor-tabs-wrapper">
		<#
		_.each( settings.tabs, function( item ) { #>
			<div class="elementor-tab-title" data-tab="<#- counter #>"><span><#= item.tab_title #></span></div>
			<#
			counter++;
		} ); #>
		</div>
		<# counter = 1; #>
		<div class="elementor-tabs-content-wrapper">
		<#
		_.each( settings.tabs, function( item ) { #>
			<div class="elementor-tab-content elementor-clearfix elementor-repeater-item-<#- item._id #>" data-tab="<#- counter #>"><#= item.tab_content #></div>
			<#
			counter++;
		} ); #>
		</div>
	<# } #>
	</div>
{/capture}

{capture WidgetTestimonial}
	<#
	var hasImage = settings.testimonial_image.url ? ' elementor-has-image' : '',
		testimonial_alignment = settings.testimonial_alignment ? ' elementor-testimonial-text-align-' + settings.testimonial_alignment : '',
		testimonial_image_position = settings.testimonial_image_position ? ' elementor-testimonial-image-position-' + settings.testimonial_image_position : '';
	#>
	<div class="elementor-testimonial-wrapper<#- testimonial_alignment #>">
		<# if ( '' !== settings.testimonial_content ) { #>
			<div class="elementor-testimonial-content">
				<#= settings.testimonial_content #>
			</div>
		<# } #>
		<div class="elementor-testimonial-meta<#- hasImage #><#- testimonial_image_position #>">
			<div class="elementor-testimonial-meta-inner">
				<# if ( hasImage ) { #>
					<div class="elementor-testimonial-image">
						<img src="<#- elementor.imagesManager.getImageUrl( settings.testimonial_image ) #>" alt="testimonial" />
					</div>
				<# } #>
				<div class="elementor-testimonial-details">
				<# if ( '' !== settings.testimonial_name ) { #>
					<div class="elementor-testimonial-name">
						<#= settings.testimonial_name #>
					</div>
				<# } #>
				<# if ( '' !== settings.testimonial_job ) { #>
					<div class="elementor-testimonial-job">
						<#= settings.testimonial_job #>
					</div>
				<# } #>
				</div>
			</div>
		</div>
	</div>
{/capture}

{capture WidgetTextEditor}
	<div class="elementor-text-editor elementor-clearfix rte-content"><#= settings.editor #></div>
{/capture}

{capture WidgetToggle}
	<div class="elementor-toggle">
	<#
	if ( settings.tabs ) {
		var counter = 1;
		_.each(settings.tabs, function( item ) { #>
			<div class="elementor-toggle-title" data-tab="<#- counter #>">
				<span class="elementor-toggle-icon">
					<i class="fa"></i>
				</span>
				<#= item.tab_title #>
			</div>
			<div class="elementor-toggle-content elementor-clearfix" data-tab="<#- counter #>"><#= item.tab_content #></div>
			<#
			counter++;
		} );
	} #>
	</div>
{/capture}
