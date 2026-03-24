{**
 * Creative Elements - Elementor based PageBuilder
 *
 * @author    WebshopWorks
 * @copyright 2019-2020 WebshopWorks.com
 * @license   One domain support license
 *}

{capture Frontend_getBuilderContentForDisplay}
	<div class="elementor-alert elementor-alert-danger">%s</div>
{/capture}

{function Frontend_getBuilderContent css_file=null post_id=0}
	{if $css_file}
		<style>{$css_file->getCss()|cleanHtml}</style>
	{/if}
	<div class="elementor elementor-{$post_id|escape:'html':'UTF-8'}">
		<div class="elementor-inner">
			<div class="elementor-section-wrap">
{/function}

{function Frontend_printCss css_code=''}
	<style id="elementor-frontend-stylesheet">{$css_code|cleanHtml}</style>
{/function}

{function Frontend_printGoogleFonts fonts_url='' early_access_fonts=[]}
	{if $fonts_url}
		<link rel="stylesheet" href="{$fonts_url|escape:'html':'UTF-8'}">
	{/if}
	{foreach $early_access_fonts as $current_font}
		<link rel="stylesheet" href="https://fonts.googleapis.com/earlyaccess/{Tools::strtolower(str_replace(' ', '', $current_font))|escape:'html':'UTF-8'}.css">
	{/foreach}
{/function}

{function GroupControlImageSize_getAttachmentImageHtml settings=[] setting_key='image' loading='lazy'}
	{if !empty($settings[$setting_key]['url'])}
		<img src="{call_user_func('CE\\Helper::getMediaLink', $settings[$setting_key]['url'])|escape:'html':'UTF-8'}" loading="{$loading|escape:'html':'UTF-8'}"
			{if !empty($settings[$setting_key]['alt'])}alt="{$settings[$setting_key]['alt']|escape:'html':'UTF-8'}"{/if}
			{if !empty($settings[$setting_key]['title'])}title="{$settings[$setting_key]['title']|escape:'html':'UTF-8'}"{/if}
			{if !empty($settings['hover_animation'])}class="elementor-animation-{$settings['hover_animation']|escape:'html':'UTF-8'}"{/if}>
	{/if}
{/function}

{function ElementSection_beforeRender this=null settings=[]}
	<div {$this->getRenderAttributeString('wrapper')|cleanHtml}>
		{if 'video' === $settings['background_background'] && $settings['background_video_link']}
			{$video_id = call_user_func('CE\\Utils::getYoutubeIdFromUrl', $settings['background_video_link'])}
			<div class="elementor-background-video-container elementor-hidden-phone">
				{if $video_id}
					<div class="elementor-background-video" data-video-id="{$video_id|escape:'html':'UTF-8'}"></div>
				{else}
					<video class="elementor-background-video elementor-html5-video" src="{$settings['background_video_link']|escape:'html':'UTF-8'}" autoplay loop muted></video>
				{/if}
			</div>
		{/if}
		{if 'classic' === $settings['background_overlay_background']}
			<div class="elementor-background-overlay"></div>
		{/if}
		<div class="elementor-container elementor-column-gap-{$settings['gap']|escape:'html':'UTF-8'}">
			<div class="elementor-row">
{/function}

{function ElementColumn_beforeRender this=null}
	<div {$this->getRenderAttributeString('wrapper')|cleanHtml}>
		<div class="elementor-column-wrap{if $this->getChildren()} elementor-element-populated{/if}">
			<div class="elementor-widget-wrap">
{/function}

{capture ElementBase_afterRender}
			</div>
		</div>
	</div>
{/capture}

{capture WidgetBase_renderContent}
	<div class="elementor-widget-container">
{/capture}

{function WidgetBase_beforeRender this=null}
	<div {$this->getRenderAttributeString('_wrapper')|cleanHtml}>
{/function}

{capture WidgetBase_afterRender}
	</div>
{/capture}

{function WidgetProductBase_fetchMiniature
	this=null product=null settings=[] article='' link='' cover=[] cover_url=[] cover_alt='' cover_size=[] image_size='' badges=[] show_atc=0 atc_url='' on_sale=0 regular_price=0 min_qty=0}
	<article class="elementor-product-miniature js-product-miniature"{if !$smarty.const._CE_PS16_} {$this->getRenderAttributeString($article)|cleanHtml}{/if}>
		<a class="elementor-product-link" href="{$link|escape:'html':'UTF-8'}">
			<div class="elementor-image">
				<picture class="elementor-cover-image">
					{if isset($cover_url['mobile'])}
						<source media="(max-width: 767px)" srcset="{$cover_url['mobile']|escape:'html':'UTF-8'}">
					{/if}
					{if isset($cover_url['tablet'])}
						<source media="(max-width: 991px)" srcset="{$cover_url['tablet']|escape:'html':'UTF-8'}">
					{/if}
					<img src="{$cover_url['desktop']|escape:'html':'UTF-8'}" loading="{$this->loading|escape:'html':'UTF-8'}" alt="{$cover_alt|escape:'html':'UTF-8'}"
						width="{$cover_size['width']|intval}" height="{$cover_size['height']|intval}">
				</picture>
				{if !empty($settings['show_second_image']) && !empty($product['images'])}
					{foreach $product['images'] as $image}
						{if $image['id_image'] != $cover['id_image']}
							<picture class="elementor-second-image">
								{if isset($cover_url['mobile'])}
									<source media="(max-width: 767px)" srcset="{$image['bySize'][$settings['image_size_mobile']]['url']|escape:'html':'UTF-8'}">
								{/if}
								{if isset($cover_url['tablet'])}
									<source media="(max-width: 991px)" srcset="{$image['bySize'][$settings['image_size_tablet']]['url']|escape:'html':'UTF-8'}">
								{/if}
								<img src="{$image['bySize'][$image_size]['url']|escape:'html':'UTF-8'}" loading="lazy" alt="{$image['legend']|escape:'html':'UTF-8'}"
									width="{$image['bySize'][$image_size]['width']|intval}" height="{$image['bySize'][$image_size]['height']|intval}">
							</picture>
							{break}
						{/if}
					{/foreach}
				{/if}
				{if !empty($settings['show_qv'])}
					<div class="elementor-button elementor-quick-view" data-link-action="quickview">
						<div class="elementor-button-inner">
							<span class="elementor-button-icon elementor-align-icon-{$settings['qv_icon_align']|escape:'html':'UTF-8'}">
								<i class="{$settings['qv_icon']|escape:'html':'UTF-8'}"></i>
							</span>
							<span class="elementor-button-text">{$settings['qv_text']|cleanHtml}</span>
						</div>
					</div>
				{/if}
			</div>
			{foreach ['left', 'right'] as $position}
				<div class="elementor-badges-{$position|escape:'html':'UTF-8'}">
				{foreach $badges as $badge => $label}
					{if $position == $settings["badge_{$badge}_position"]}
						<div class="elementor-badge elementor-badge-{$badge|escape:'html':'UTF-8'}">{$label|cleanHtml}</div>
					{/if}
				{/foreach}
				</div>
			{/foreach}
			<div class="elementor-content">
				{if !empty($settings['show_category'])}
					<h4 class="elementor-category">{if $smarty.const._CE_PS16_}{$product['category_default']|cleanHtml}{else}{$product['category_name']|cleanHtml}{/if}</h4>
				{/if}
				<h3 class="elementor-title">{$product['name']|cleanHtml}</h3>
				{if !empty($description)}
					<div class="elementor-description">{$description|cleanHtml}</div>
				{/if}
				{if $this->show_prices && $product['show_price']}
					<div class="elementor-price-wrapper">
						{if $on_sale && !empty($settings['show_regular_price'])}
							<span class="elementor-price-regular">{$regular_price|cleanHtml}</span>
						{/if}
						<span class="elementor-price">{$product['price']|cleanHtml}</span>
					</div>
				{/if}
			</div>
		</a>
		{if $smarty.const._CE_PS16_ && $show_atc}
			<div class="elementor-atc">
				<a class="elementor-button ajax_add_to_cart_button elementor-size-{$settings['atc_size']|escape:'html':'UTF-8'}" href="{$atc_url|escape:'html':'UTF-8'}" {$this->getRenderAttributeString($article)|cleanHtml}>
					{if !empty($settings['atc_icon'])}
						<span class="elementor-atc-icon elementor-align-icon-{$settings['atc_icon_align']|escape:'html':'UTF-8'}">
							<i class="{$settings['atc_icon']|escape:'html':'UTF-8'}"></i>
						</span>
					{/if}
					<span class="elementor-button-text">{$settings['atc_text']|cleanHtml}</span>
				</a>
			</div>
		{elseif $show_atc}
			<form class="elementor-atc" action="{$atc_url|escape:'html':'UTF-8'}">
				<input type="hidden" name="qty" value="{$min_qty|intval}">
				<button type="submit" class="elementor-button elementor-size-{$settings['atc_size']|escape:'html':'UTF-8'}" data-button-action="add-to-cart">
					{if !empty($settings['atc_icon'])}
						<span class="elementor-atc-icon elementor-align-icon-{$settings['atc_icon_align']|escape:'html':'UTF-8'}">
							<i class="{$settings['atc_icon']|escape:'html':'UTF-8'}"></i>
						</span>
					{/if}
					<span class="elementor-button-text">{$settings['atc_text']|cleanHtml}</span>
				</button>
			</form>
		{/if}
	</article>
{/function}

{function WidgetAccordion this=null settings=[]}
	<div class="elementor-accordion">
		{$counter = 1}
		{foreach $settings['tabs'] as $item}
			<div class="elementor-accordion-item">
				<div class="elementor-accordion-title" data-section="{$counter|intval}">
					<span class="elementor-accordion-icon elementor-accordion-icon-{$settings['icon_align']|escape:'html':'UTF-8'}">
						<i class="fa"></i>
					</span>
					{$item['tab_title']|cleanHtml}
				</div>
				<div class="elementor-accordion-content elementor-clearfix" data-section="{$counter|intval}">{$item['tab_content']|cleanHtml}</div>
			</div>
			{$counter = $counter + 1}
		{/foreach}
	</div>
{/function}

{function WidgetAjaxSearch this=null settings=[]}
	<form class="elementor-ajax-search" role="search" action="{Context::getContext()->link->getPageLink('search')|escape:'html':'UTF-8'}" method="get">
		<input type="hidden" name="controller" value="search">
		<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
		<div class="elementor-ajax-search-wrapper">
			{if 'minimal' === $settings['skin']}
				<div class="elementor-ajax-search-icon">
					<i class="fa fa-search" aria-hidden="true"></i>
				</div>
			{/if}
			<input {$this->getRenderAttributeString('input')|cleanHtml}>
			{if 'classic' === $settings['skin']}
				<button class="elementor-ajax-search-submit" type="submit">
					{if 'icon' === $settings['button_type']}
						<i {$this->getRenderAttributeString('icon')|cleanHtml} aria-hidden="true"></i>
					{elseif !empty($settings['button_text'])}
						{$settings['button_text']|cleanHtml}
					{/if}
				</button>
			{/if}
		</div>
	</form>
{/function}

{function WidgetAlert this=null settings=[]}
    <div {$this->getRenderAttributeString('wrapper')|cleanHtml} role="alert">
    	<span class="elementor-alert-title">{$settings['alert_title']|cleanHtml}</span>
	    {if !empty($settings['alert_description'])}
	    	<span class="elementor-alert-description">{$settings['alert_description']|cleanHtml}</span>
	    {/if}
	    {if !empty($settings['show_dismiss']) && 'show' === $settings['show_dismiss']}
	        <button type="button" class="elementor-alert-dismiss">X</button>
	    {/if}
    </div>
{/function}

{function WidgetButton this=null settings=[]}
	<div class="elementor-button-wrapper">
		<a {$this->getRenderAttributeString('button')|cleanHtml}>
			<span class="elementor-button-inner">
				{if !empty($settings['icon'])}
					<span {$this->getRenderAttributeString('icon-align')|cleanHtml}>
						<i class="{$settings['icon']|escape:'html':'UTF-8'}"></i>
					</span>
				{/if}
				<span class="elementor-button-text">{$settings['text']|cleanHtml}</span>
			</span>
		</a>
	</div>
{/function}

{function WidgetCallToAction this=null settings=[] wrapper_tag='' title_tag='' button_tag='' link_url='' content_animation='' animation_class='' print_bg=true print_content=true}
	<{$wrapper_tag|escape:'html':'UTF-8'} class="elementor-cta" {$this->getRenderAttributeString('wrapper')|cleanHtml}>
	{if $print_bg}
		<div class="elementor-cta-bg-wrapper">
			<div class="elementor-cta-bg elementor-bg" {$this->getRenderAttributeString('background_image')|cleanHtml}></div>
			<div class="elementor-cta-bg-overlay"></div>
		</div>
	{/if}
	{if $print_content}
		<div class="elementor-cta-content">
			{if 'image' === $settings['graphic_element'] && !empty($settings['graphic_image']['url'])}
				<div {$this->getRenderAttributeString('graphic_element')|cleanHtml}>
					{GroupControlImageSize_getAttachmentImageHtml settings=$settings setting_key='graphic_image'}
				</div>
			{elseif 'icon' === $settings['graphic_element'] && !empty($settings['icon'])}
				<div {$this->getRenderAttributeString('graphic_element')|cleanHtml}>
					<div class="elementor-icon">
						<i {$this->getRenderAttributeString('icon')|cleanHtml}></i>
					</div>
				</div>
			{/if}
			{if !empty($settings['title'])}
				<{$title_tag|escape:'html':'UTF-8'} {$this->getRenderAttributeString('title')|cleanHtml}>{$settings['title']|cleanHtml}</{$title_tag|escape:'html':'UTF-8'}>
			{/if}
			{if !empty($settings['description_text'])}
				<div {$this->getRenderAttributeString('description')|cleanHtml}>{$settings['description_text']|cleanHtml}</div>
			{/if}
			{if !empty($settings['button'])}
				<div class="elementor-cta-button-wrapper elementor-content-item {$animation_class|escape:'html':'UTF-8'}">
				<{$button_tag|escape:'html':'UTF-8'} {$this->getRenderAttributeString('button')|cleanHtml}>
					{if !empty($settings['button_icon'])}
						<span class="elementor-button-icon elementor-align-icon-{$settings['button_icon_align']|escape:'html':'UTF-8'}">
							<i class="{$settings['button_icon']|escape:'html':'UTF-8'}"></i>
						</span>
					{/if}
					<span class="elementor-button-text">{$settings['button']|cleanHtml}</span>
				</{$button_tag|escape:'html':'UTF-8'}>
				</div>
			{/if}
		</div>
	{/if}
	{if !empty($settings['ribbon_title'])}
		<div {$this->getRenderAttributeString('ribbon-wrapper')|cleanHtml}>
			<div class="elementor-ribbon-inner">{$settings['ribbon_title']|cleanHtml}</div>
		</div>
	{/if}
	</{$wrapper_tag|escape:'html':'UTF-8'}>
{/function}

{function WidgetContactForm this=null settings=[] id='' token='' show_labels=true input_classes=''}
	<form class="elementor-contact-form" {$this->getRenderAttributeString('form')|cleanHtml}>
		<div class="elementor-form-fields-wrapper">
			<input type="hidden" name="url">
			{if $token}
				<input type="hidden" name="{if $smarty.const._CE_PS16_}contactKey{else}token{/if}" value="{$token|escape:'html':'UTF-8'}">
			{/if}
			{if $settings['subject_id']}
				<input type="hidden" name="id_contact" value="{$settings['subject_id']|intval}">
			{else}
				<div class="elementor-field-group elementor-column elementor-col-{$settings['subject_width']|intval} elementor-field-type-select">
					{if $show_labels}
						<label class="elementor-field-label" for="id-contact-{$id|escape:'html':'UTF-8'}">
							{if $settings['subject_label']}{$settings['subject_label']|cleanHtml}{else}{$this->trans('Subject Heading')}{/if}
						</label>
					{/if}
					<div class="elementor-select-wrapper">
						<select name="id_contact" id="id-contact-{$id|escape:'html':'UTF-8'}" class="elementor-field elementor-field-textual elementor-size-{$settings['input_size']|escape:'html':'UTF-8'}">
						{foreach Contact::getContacts(Context::getContext()->language->id) as $contact}
							<option value="{$contact['id_contact']|intval}">{$contact['name']|cleanHtml}</option>
						{/foreach}
						</select>
					</div>
				</div>
			{/if}
			<div class="elementor-field-group elementor-column elementor-col-{$settings['email_width']|intval} elementor-field-type-email">
				{if $show_labels}
					<label class="elementor-field-label" for="from-{$id|escape:'html':'UTF-8'}">
						{if $settings['email_label']}{$settings['email_label']|cleanHtml}{else}{$this->trans('Email address')}{/if}
					</label>
				{/if}
				<input type="email" name="from" {$this->getRenderAttributeString('email')|cleanHtml} class="{$input_classes|escape:'html':'UTF-8'} elementor-field-textual" required>
			</div>
			{if $this->upload && $settings['show_upload']}
				<div class="elementor-field-group elementor-column elementor-col-{$settings['upload_width']|intval} elementor-field-type-file">
					{if $show_labels}
						<label class="elementor-field-label" for="file-upload-{$id|escape:'html':'UTF-8'}">
							{if $settings['upload_label']}{$settings['upload_label']|cleanHtml}{else}{$this->trans('Attach File')}{/if}
						</label>
					{/if}
					<input type="file" name="fileUpload" id="file-upload-{$id|escape:'html':'UTF-8'}" class="{$input_classes|escape:'html':'UTF-8'}">
				</div>
			{/if}
			<div class="elementor-field-group elementor-column elementor-col-{$settings['message_width']|intval} elementor-field-type-textarea">
				{if $show_labels}
					<label class="elementor-field-label" for="message-{$id|escape:'html':'UTF-8'}">
						{if $settings['message_label']}{$settings['message_label']|cleanHtml}{else}{$this->trans('Message')}{/if}
					</label>
				{/if}
				<textarea name="message" {$this->getRenderAttributeString('message')|cleanHtml} class="{$input_classes|escape:'html':'UTF-8'} elementor-field-textual" required></textarea>
			</div>
			{if $this->gdpr}
				<div class="elementor-field-group elementor-column elementor-col-100 elementor-field-type-gdpr">
					<label class="elementor-field-label">
						<input type="checkbox" name="{$this->gdpr|escape:'html':'UTF-8'}" value="1" required><span class="elementor-checkbox-label">{$this->gdpr_msg|cleanHtml}</span>
					</label>
				</div>
			{/if}
			<div class="elementor-field-group elementor-column elementor-col-{$settings['button_width']|intval} elementor-field-type-submit">
				<button type="submit" name="submitMessage" value="Send" {$this->getRenderAttributeString('button')|cleanHtml}>
					<span class="elementor-button-inner">
						<span class="elementor-button-text">{if $settings['button_text']}{$settings['button_text']|cleanHtml}{else}{$this->trans('Send')}{/if}</span>
						{if !empty($settings['icon'])}
							<span class="elementor-align-icon-{$settings['icon_align']|escape:'html':'UTF-8'}">
								<i class="{$settings['icon']|escape:'html':'UTF-8'}"></i>
							</span>
						{/if}
					</span>
				</button>
			</div>
		</div>
	</form>
{/function}

{function WidgetCountdown this=null settings=[]}
	{$actions = $this->getActions($settings)}
	<div class="elementor-countdown-wrapper" data-date="{strtotime($settings['due_date'])|escape:'html':'UTF-8'}" data-expire-actions='{$actions|json_encode}'>
	{foreach ['days', 'hours', 'minutes', 'seconds'] as $counts}
		{if $settings["show_$counts"]}
			<div class="elementor-countdown-item"><span class="elementor-countdown-digits elementor-countdown-{$counts|escape:'html':'UTF-8'}"></span>
			{if $settings['show_labels']}
				<span class="elementor-countdown-label">{if $settings['custom_labels']}{$settings[$label]|cleanHtml}{else}{ce__(Tools::ucfirst($counts))}{/if}</span>
			{/if}
			</div>
		{/if}
	{/foreach}
	</div>
	{if is_array($actions)}
		{foreach $actions as $action}
			{if 'message' === $action['type']}
				<div class="elementor-countdown-expire--message">{$settings['message_after_expire']|cleanHtml}</div>
			{/if}
		{/foreach}
	{/if}
{/function}

{function WidgetCounter this=null settings=[]}
	<div class="elementor-counter">
		<div class="elementor-counter-number-wrapper">
			<span class="elementor-counter-number-prefix">{$settings['prefix']|cleanHtml}</span>
			<span {$this->getRenderAttributeString('counter')|cleanHtml}>{$settings['starting_number']|cleanHtml}</span>
			<span class="elementor-counter-number-suffix">{$settings['suffix']|cleanHtml}</span>
		</div>
		{if $settings['title']}
			<div class="elementor-counter-title">{$settings['title']|cleanHtml}</div>
		{/if}
	</div>
{/function}

{capture WidgetDivider}
	<div class="elementor-divider">
		<span class="elementor-divider-separator"></span>
	</div>
{/capture}

{function WidgetEmailSubscription this=null settings=[]}
	<form class="elementor-email-subscription" {$this->getRenderAttributeString('form')|cleanHtml}>
		<input type="hidden" name="action" value="0">
		<div class="elementor-field-type-subscribe">
			<input type="email" name="email" class="elementor-field elementor-field-textual" {$this->getRenderAttributeString('email')|cleanHtml} required>
			<button type="submit" name="submitNewsletter" value="1" {$this->getRenderAttributeString('button')|cleanHtml}>
				<span class="elementor-button-inner">
				{if $settings['icon'] && 'left' === $settings['icon_align']}
					<span class="elementor-button-icon"><i class="{$settings['icon']|escape:'html':'UTF-8'}"></i></span>
				{/if}
				{if trim($settings['button']) || !$settings['button']}
					<span class="elementor-button-text">{if $settings['button']}{$settings['button']|cleanHtml}{else}{$this->button_placeholder|cleanHtml}{/if}</span>
				{/if}
				{if $settings['icon'] && 'right' == $settings['icon_align']}
					<span class="elementor-button-icon"><i class="{$settings['icon']|escape:'html':'UTF-8'}"></i></span>
				{/if}
				</span>
			</button>
		</div>
		{if $this->gdpr}
			<div class="elementor-field-type-gdpr">
				<label class="elementor-field-label">
					<input type="checkbox" name="{$this->gdpr|escape:'html':'UTF-8'}" value="1" required><span class="elementor-checkbox-label">{$this->gdpr_msg|cleanHtml}</span>
				</label>
			</div>
		{/if}
	</form>
{/function}

{function WidgetFacebook this=null}
	{literal}<if{/literal}rame {$this->getRenderAttributeString('frame')|cleanHtml}></if{literal}rame>{/literal}
{/function}

{function WidgetFlipBox this=null settings=[] flipbox_b_html_tag='' button_tag=''}
	<div class="elementor-flip-box">
		<div class="elementor-flip-box-front elementor-flip-box-side">
			<div class="elementor-flip-box-overlay">
				<div class="elementor-flip-box-content">
					{if 'icon' === $settings['graphic_element']}
						<div {$this->getRenderAttributeString('icon-wrapper-front')|cleanHtml}>
							<div class="elementor-icon">
								<i {$this->getRenderAttributeString('icon_front')|cleanHtml}></i>
							</div>
						</div>
					{elseif 'image' === $settings['graphic_element']}
						<div class="elementor-flip-box-image">
							{GroupControlImageSize_getAttachmentImageHtml settings=$settings}
						</div>
					{/if}
					{if !empty($settings['title_text_a'])}
						<{$settings['title_size_a']|escape:'html':'UTF-8'} class="elementor-flip-box-title">{$settings['title_text_a']|cleanHtml}</{$settings['title_size_a']|escape:'html':'UTF-8'}>
					{/if}
					{if !empty($settings['description_text_a'])}
						<div class="elementor-flip-box-description">{$settings['description_text_a']|cleanHtml}</div>
					{/if}
				</div>
			</div>
		</div>
		<{$flipbox_b_html_tag|escape:'html':'UTF-8'} {$this->getRenderAttributeString('flipbox-back')|cleanHtml}>
			<div class="elementor-flip-box-overlay">
				<div class="elementor-flip-box-content">
					{if 'none' != $settings['graphic_element_b']}
						{if 'image' === $settings['graphic_element_b']}
						<div class="elementor-flip-box-image">
							{GroupControlImageSize_getAttachmentImageHtml settings=$settings setting_key='image_b'}
						</div>
						{elseif 'icon' == $settings['graphic_element_b']}
							<div {$this->getRenderAttributeString('icon-wrapper-back')|cleanHtml}>
								<div class="elementor-icon">
									<i {$this->getRenderAttributeString('icon_b')|cleanHtml}></i>
								</div>
							</div>
						{/if}
					{/if}
					{if !empty($settings['title_text_b'])}
						<{$settings['title_size_b']|escape:'html':'UTF-8'} class="elementor-flip-box-title">{$settings['title_text_b']|cleanHtml}</{$settings['title_size_b']|escape:'html':'UTF-8'}>
					{/if}
					{if !empty($settings['description_text_b'])}
						<div class="elementor-flip-box-description">{$settings['description_text_b']|cleanHtml}</div>
					{/if}
					{if !empty($settings['button'])}
						<{$button_tag|escape:'html':'UTF-8'} {$this->getRenderAttributeString('button')|cleanHtml}>
							{if !empty($settings['button_icon'])}
								<span class="elementor-button-icon elementor-align-icon-{$settings['button_icon_align']|escape:'html':'UTF-8'}">
									<i class="{$settings['button_icon']|escape:'html':'UTF-8'}"></i>
								</span>
							{/if}
							<span class="elementor-button-text">{$settings['button']|cleanHtml}</span>
						</{$button_tag|escape:'html':'UTF-8'}>
					{/if}
				</div>
			</div>
		</{$flipbox_b_html_tag|escape:'html':'UTF-8'}>
	</div>
{/function}

{function WidgetGoogleMaps this=null}
	{literal}<div class="elementor-custom-embed"><if{/literal}rame {$this->getRenderAttributeString('frame')|cleanHtml}></if{literal}rame></div>{/literal}
{/function}

{function WidgetHeading this=null settings=[]}
	<{$settings['header_size']|escape:'html':'UTF-8'} {$this->getRenderAttributeString('heading')|cleanHtml}>
    {if !empty($settings['link']['url'])}
    	<a href="{$settings['link']['url']|escape:'html':'UTF-8'}">{$settings['title']|cleanHtml}</a>
    {else}
    	<span>{$settings['title']|cleanHtml}</span>
    {/if}
    </{$settings['header_size']|escape:'html':'UTF-8'}>
{/function}

{function WidgetIcon this=null icon_tag=''}
	<div {$this->getRenderAttributeString('wrapper')|cleanHtml}>
		<{$icon_tag|escape:'html':'UTF-8'} {$this->getRenderAttributeString('icon-wrapper')|cleanHtml}>
			<i {$this->getRenderAttributeString('icon')|cleanHtml}></i>
		</{$icon_tag|escape:'html':'UTF-8'}>
	</div>
{/function}

{function WidgetIconBox this=null settings=[] icon_tag=''}
	<div class="elementor-icon-box-wrapper">
		<div class="elementor-icon-box-icon">
			<{$icon_tag|escape:'html':'UTF-8'} {$this->getRenderAttributeString('icon')|cleanHtml} {$this->getRenderAttributeString('link')|cleanHtml}>
				<i class="{$settings['icon']|escape:'html':'UTF-8'}"></i>
			</{$icon_tag|escape:'html':'UTF-8'}>
		</div>
		<div class="elementor-icon-box-content">
			<{$settings['title_size']|escape:'html':'UTF-8'} class="elementor-icon-box-title">
				<{$icon_tag|escape:'html':'UTF-8'} {$this->getRenderAttributeString('link')|cleanHtml}>{$settings['title_text']|cleanHtml}</{$icon_tag|escape:'html':'UTF-8'}>
			</{$settings['title_size']|escape:'html':'UTF-8'}>
			<div class="elementor-icon-box-description">{$settings['description_text']|cleanHtml}</div>
		</div>
	</div>
{/function}

{function WidgetIconList settings=[]}
	<ul class="elementor-icon-list-items">
	{foreach $settings['icon_list'] as $item}
		<li class="elementor-icon-list-item">
			{if !empty($item['link']['url'])}
				<a href="{$item['link']['url']|escape:'html':'UTF-8'}"{if $item['link']['is_external']} target="_blank"{/if}>
			{/if}
			{if $item['icon']}
				<span class="elementor-icon-list-icon">
					<i class="{$item['icon']|escape:'html':'UTF-8'}"></i>
				</span>
			{/if}
			<span class="elementor-icon-list-text">{$item['text']|cleanHtml}</span>
			{if !empty($item['link']['url'])}
				</a>
			{/if}
		</li>
	{/foreach}
	</ul>
{/function}

{function WidgetImage this=null settings=[] link=''}
	<div {$this->getRenderAttributeString('wrapper')|cleanHtml}>
		{if $settings['caption']}
			<figure class="wp-caption">
		{/if}
		{if $link}
			<a {$this->getRenderAttributeString('link')|cleanHtml}>
		{/if}
		{GroupControlImageSize_getAttachmentImageHtml settings=$settings}
		{if $link}
			</a>
		{/if}
		{if $settings['caption']}
			<figcaption class="widget-image-caption wp-caption-text">{$settings['caption']|cleanHtml}</figcaption>
			</figure>
		{/if}
	</div>
{/function}

{function WidgetImageBox settings=[]}
	<div class="elementor-image-box-wrapper">
	{if !empty($settings['image']['url'])}
		<figure class="elementor-image-box-img">
		{if !empty($settings['link']['url'])}
			<a href="{$settings['link']['url']|escape:'html':'UTF-8'}"{if $settings['link']['is_external']} target="_blank"{/if}>
		{/if}
			{GroupControlImageSize_getAttachmentImageHtml settings=$settings}
		{if !empty($settings['link']['url'])}
			</a>
		{/if}
		</figure>
	{/if}
	{if !empty($settings['title_text']) || !empty($settings['description_text'])}
		<div class="elementor-image-box-content">
		{if !empty($settings['title_text'])}
			<{$settings['title_size']|escape:'html':'UTF-8'} class="elementor-image-box-title">
			{if !empty($settings['link']['url'])}
				<a href="{$settings['link']['url']|escape:'html':'UTF-8'}"{if $settings['link']['is_external']} target="_blank"{/if}>{$settings['title_text']|cleanHtml}</a>
			{else}
				{$settings['title_text']|cleanHtml}
			{/if}
			</{$settings['title_size']|escape:'html':'UTF-8'}>
		{/if}
		{if !empty($settings['description_text'])}
			<div class="elementor-image-box-description">{$settings['description_text']|cleanHtml}</div>
		{/if}
		</div>
	{/if}
	</div>
{/function}

{function WidgetImageCarousel this=null settings=[] slick_options=[]}
	<div class="elementor-image-carousel-wrapper elementor-slick-slider" dir="{$settings['direction']|escape:'html':'UTF-8'}">
		<div {$this->getRenderAttributeString('carousel')|cleanHtml} data-slider_options='{$slick_options|json_encode}'>
		{foreach $settings['carousel'] as $item}
			<div><div class="slick-slide-inner">
				{if !empty($item['link']['url'])}
					<a href="{$item['link']['url']|escape:'html':'UTF-8'}"{if $item['link']['is_external']} target="_blank"{/if}>
				{/if}
				{GroupControlImageSize_getAttachmentImageHtml settings=$item loading='auto'}
				{if !empty($item['link']['url'])}
					</a>
				{/if}
			</div></div>
		{/foreach}
		</div>
	</div>
{/function}

{function WidgetImageHotspot this=null settings=[]}
	<div class="elementor-image-hotspot">
		{GroupControlImageSize_getAttachmentImageHtml settings=$settings}
		{foreach $settings['hotspots'] as $item}
			<div class="elementor-image-hotspot-wrapper elementor-repeater-item-{$item['_id']|escape:'html':'UTF-8'}">
				{if empty($item['link']['url'])}{$icon_tag = 'div'}{else}{$icon_tag = 'a'}{/if}
				<{$icon_tag|escape:'html':'UTF-8'} class="elementor-icon{if $settings['icon_animation']} elementor-animation-{$settings['icon_animation']|escape:'html':'UTF-8'}{/if}"
					{if 'a' === $icon_tag}href="{$item['link']['url']|escape:'html':'UTF-8'}"{if !empty($item['link']['is_external'])} target="_blank"{/if}{/if}>
					<i {$this->getRenderAttributeString('icon')|cleanHtml}></i>
				</{$icon_tag|escape:'html':'UTF-8'}>
				<div class="elementor-image-hotspot-content">
				{if !empty($item['title'])}
					<{$settings['title_size']|escape:'html':'UTF-8'} class="elementor-image-hotspot-title">{$item['title']|cleanHtml}</{$settings['title_size']|escape:'html':'UTF-8'}>
				{/if}
				{if !empty($item['description'])}
					<div class="elementor-image-hotspot-description">{$item['description']|cleanHtml}</div>
				{/if}
				</div>
			</div>
		{/foreach}
	</div>
{/function}

{function WidgetMenuAnchor this=null}
	<div {$this->getRenderAttributeString('inner')|cleanHtml}></div>
{/function}

{function WidgetProductBox box=''}
	<div class="elementor-product-box">{$box|cleanHtml}</div>
{/function}

{function WidgetProductCarousel this=null settings=[] slick_options=[] boxes=[] inner=true}
	<div class="elementor-image-carousel-wrapper elementor-slick-slider" dir="{$settings['direction']|escape:'html':'UTF-8'}">
		<div {$this->getRenderAttributeString('carousel')|cleanHtml} data-slider_options='{$slick_options|json_encode}'>
		{foreach $boxes as $box}
			<div>{if $inner}<div class="slick-slide-inner">{/if}
				{$box|cleanHtml}
			</div>{if $inner}</div>{/if}
		{/foreach}
		</div>
	</div>
{/function}

{function WidgetProductGrid boxes=[]}
	<div class="elementor-product-grid">
	{foreach $boxes as $box}
		{$box|cleanHtml}
	{/foreach}
	</div>
{/function}

{function WidgetProgress this=null settings=[]}
	{if !empty($settings['title'])}
		<span class="elementor-title">{$settings['title']|cleanHtml}</span>
	{/if}
	<div {$this->getRenderAttributeString('wrapper')|cleanHtml} role="timer">
		<div {$this->getRenderAttributeString('progress-bar')|cleanHtml}>
			<span class="elementor-progress-text">{$settings['inner_text']|cleanHtml}</span>
			{if 'hide' != $settings['display_percentage']}
				<span class="elementor-progress-percentage">{$settings['percent']['size']|cleanHtml}%</span>
			{/if}
		</div>
	</div>
{/function}

{function WidgetSocialIcons this=null}
	<div class="elementor-social-icons-wrapper">
	{foreach $this->getSettings('social_icon_list') as $item}
		<a class="elementor-icon elementor-social-icon elementor-social-icon-{str_replace('fa fa-', '', $item['social'])|escape:'html':'UTF-8'}"
			href="{$item['link']['url']|escape:'html':'UTF-8'}"{if $item['link']['is_external']} target="_blank"{/if}>
			<i class="{$item['social']|escape:'html':'UTF-8'}"></i>
		</a>
	{/foreach}
	</div>
{/function}

{capture WidgetSpacer}
	<div class="elementor-spacer">
		<div class="elementor-spacer-inner"></div>
	</div>
{/capture}

{function WidgetTabs this=null tabs=[]}
	<div class="elementor-tabs">
		<div class="elementor-tabs-wrapper">
		{foreach $tabs as $counter => $item}
			<div class="elementor-tab-title" data-tab="{$counter+1|intval}"><span>{$item['tab_title']|cleanHtml}</span></div>
		{/foreach}
		</div>
		<div class="elementor-tabs-content-wrapper">
		{foreach $tabs as $counter => $item}
			<div class="elementor-tab-content elementor-clearfix" data-tab="{$counter+1|intval}">{$item['tab_content']|cleanHtml}</div>
		{/foreach}
		</div>
	</div>
{/function}

{function WidgetTestimonial settings=[] has_image=false position='testimonial_image_position'}
	<div class="elementor-testimonial-wrapper{if $settings['testimonial_alignment']} elementor-testimonial-text-align-{$settings['testimonial_alignment']|escape:'html':'UTF-8'}{/if}">
		{if !empty($settings['testimonial_content'])}
			<div class="elementor-testimonial-content">
				{$settings['testimonial_content']|cleanHtml}
			</div>
		{/if}
		<div class="elementor-testimonial-meta{if $has_image} elementor-has-image{/if}{if $settings[$position]} elementor-testimonial-image-position-{$settings[$position]|escape:'html':'UTF-8'}{/if}">
			<div class="elementor-testimonial-meta-inner">
				{if $has_image}
					<div class="elementor-testimonial-image">
						{GroupControlImageSize_getAttachmentImageHtml settings=$settings setting_key='testimonial_image'}
					</div>
				{/if}
				<div class="elementor-testimonial-details">
					{if !empty($settings['testimonial_name'])}
						<div class="elementor-testimonial-name">
							{$settings['testimonial_name']|cleanHtml}
						</div>
					{/if}
					{if !empty($settings['testimonial_job'])}
						<div class="elementor-testimonial-job">
							{$settings['testimonial_job']|cleanHtml}
						</div>
					{/if}
				</div>
			</div>
		</div>
	</div>
{/function}

{function WidgetTestimonialCarousel this=null settings=[] slick_options=[] layout_class=''}
	<div class="elementor-image-carousel-wrapper elementor-slick-slider elementor-testimonial-carousel" dir="{$settings['direction']|escape:'html':'UTF-8'}">
		<div {$this->getRenderAttributeString('carousel')|cleanHtml} data-slider_options='{$slick_options|json_encode}'>
		{foreach $settings['slides'] as $slide}
			<div class="slick-slide-inner">
				<div class="elementor-testimonial-wrapper">
				{if 'image_above' === $settings['layout'] && !empty($slide['image']['url'])}
					<div class="elementor-testimonial-meta {$layout_class|escape:'html':'UTF-8'}">
						<div class="elementor-testimonial-meta-inner">
							<div class="elementor-testimonial-image">
								{GroupControlImageSize_getAttachmentImageHtml settings=$slide loading='auto'}
							</div>
						</div>
					</div>
				{/if}
				{if !empty($slide['content'])}
					<div class="elementor-testimonial-content">{$slide['content']|cleanHtml}</div>
				{/if}
					<div class="elementor-testimonial-meta {$layout_class|escape:'html':'UTF-8'}">
						<div class="elementor-testimonial-meta-inner">
						{if 'image_above' != $settings['layout'] && !empty($slide['image']['url'])}
							<div class="elementor-testimonial-image">
								{GroupControlImageSize_getAttachmentImageHtml settings=$slide loading='auto'}
							</div>
						{/if}
							<div class="elementor-testimonial-details">
							{if !empty($slide['name'])}
								<div class="elementor-testimonial-name">{$slide['name']|cleanHtml}</div>
							{/if}
							{if !empty($slide['title'])}
								<div class="elementor-testimonial-job">{$slide['title']|cleanHtml}</div>
							{/if}
							</div>
						</div>
					</div>
				</div>
			</div>
		{/foreach}
		</div>
	</div>
{/function}

{function WidgetTextEditor this=null}
	<div class="elementor-text-editor elementor-clearfix rte-content">{$this->getSettings('editor')|cleanHtml}</div>
{/function}

{function WidgetToggle this=null}
	<div class="elementor-toggle">
	{foreach $this->getSettings('tabs') as $counter => $item}
		<div class="elementor-toggle-title" data-tab="{$counter+1|intval}">
			<span class="elementor-toggle-icon">
				<i class="fa"></i>
			</span>
			{$item['tab_title']|cleanHtml}
		</div>
		<div class="elementor-toggle-content elementor-clearfix" data-tab="{$counter+1|intval}">{$item['tab_content']|cleanHtml}</div>
	{/foreach}
	</div>
{/function}

{function WidgetTrustedShopsReviews this=null settings=[] slick_options=[] reviews=[]}
	{$star = '<i class="fa fa-star"></i>'}
	{$unstar = sprintf('<i class="fa fa-%s elementor-unmarked-star"></i>', $settings['rating_unmarked_style']|escape:'html')}
	<div class="elementor-image-carousel-wrapper elementor-slick-slider elementor-trustedshops-reviews" dir="{$settings['direction']|escape:'html':'UTF-8'}">
		<div {$this->getRenderAttributeString('carousel')|cleanHtml} data-slider_options='{$slick_options|json_encode}'>
		{foreach $reviews as $review}
			{$rating = round($review['mark'])}
			{if $rating >= $settings['min_rating']|intval}
				<div><div class="slick-slide-inner">
					<div class="elementor-trustedshops-review">
						<div class="elementor-trustedshops-reviews-header">
							<div class="elementor-trustedshops-reviews-date">{date($date_format, strtotime($review['changeDate']))|cleanHtml}</div>
							<div class="elementor-trustedshops-reviews-stars">{str_repeat($star, $rating)|cleanHtml}{str_repeat($unstar, 5 - $rating)|cleanHtml}</div>
						</div>
						<div class="elementor-trustedshops-reviews-comment">{$review['comment']|cleanHtml}</div>
					</div>
				</div></div>
			{/if}
		{/foreach}
		</div>
	</div>
{/function}

{function WidgetVideo this=null settings=[] video_link=''}
	<div class="elementor-video-wrapper">
		{$this->videoParser($video_link, $this->getEmbedSettings())|cleanHtml}
		{if $this->hasImageOverlay()}
			<div class="elementor-custom-embed-image-overlay" style="background-image: url('{call_user_func('CE\\Helper::getMediaLink', $settings['image_overlay']['url'])|escape:'html':'UTF-8'}');">
			{if 'yes' === $settings['show_play_icon']}
				<div class="elementor-custom-embed-play">
					<i class="fa fa-play-circle"></i>
				</div>
			{/if}
			</div>
		{/if}
	</div>
{/function}

{capture WidgetVideo}
	<if{literal}rame src="%s"  width="%d" height="%d" loading="lazy" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></if{/literal}rame>
{/capture}
