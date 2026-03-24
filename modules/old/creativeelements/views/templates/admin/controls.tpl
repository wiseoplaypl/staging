{**
 * Creative Elements - Elementor based PageBuilder
 *
 * @author    WebshopWorks
 * @copyright 2019-2020 WebshopWorks.com
 * @license   One domain support license
 *}

{capture ControlBaseUnits}
	<# if ( data.size_units.length > 1 ) { #>
	<div class="elementor-units-choices">
		<# _.each( data.size_units, function( unit ) { #>
		<input id="elementor-choose-<#- data._cid + data.name + unit #>" type="radio" name="elementor-choose-<#- data.name #>" data-setting="unit" value="<#- unit #>">
		<label class="elementor-units-choices-label" for="elementor-choose-<#- data._cid + data.name + unit #>"><#= unit #></label>
		<# } ); #>
	</div>
	<# } #>
{/capture}

{capture ControlText}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-control-input-wrapper">
			<input type="<#- data.input_type #>" class="tooltip-target" data-tooltip="<#- data.title #>" title="<#- data.title #>" data-setting="<#- data.name #>" placeholder="<#- data.placeholder #>" />
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlNumber}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-control-input-wrapper">
			<input type="number" min="<#- data.min #>" max="<#- data.max #>" step="<#- data.step #>" class="tooltip-target" data-tooltip="<#- data.title #>" title="<#- data.title #>" data-setting="<#- data.name #>" placeholder="<#- data.placeholder #>" />
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlTextarea}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-control-input-wrapper">
			<textarea rows="<#- data.rows || 5 #>" data-setting="<#- data.name #>" placeholder="<#- data.placeholder #>"></textarea>
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlSelect}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-control-input-wrapper">
			<select data-setting="<#- data.name #>">
			<# _.each( data.options, function( option_title, option_value ) { #>
				<option value="<#- option_value #>"><#= option_title #></option>
			<# } ); #>
			</select>
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlCheckbox}
	<label class="elementor-control-title">
		<input type="checkbox" data-setting="<#- data.name #>" />
		<span><#= data.label #></span>
	</label>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlSwitcher}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-control-input-wrapper">
			<label class="elementor-switch">
				<input type="checkbox" data-setting="<#- data.name #>" class="elementor-switch-input" value="<#- data.return_value #>">
				<span class="elementor-switch-label" data-on="<#- data.label_on #>" data-off="<#- data.label_off #>"></span>
				<span class="elementor-switch-handle"></span>
			</label>
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlHidden}
	<input type="hidden" data-setting="<#= data.name #>" />
{/capture}

{capture ControlHeading}
	<h3 class="elementor-control-title"><#- data.label #></h3>
{/capture}

{capture ControlRawHtml}
	<# if ( data.label ) { #>
	<span class="elementor-control-title"><#= data.label #></span>
	<# } #>
	<div class="elementor-control-raw-html <#- data.classes #>"><#= data.raw #></div>
{/capture}

{capture ControlSection}
	<div class="elementor-panel-heading">
		<div class="elementor-panel-heading-toggle elementor-section-toggle" data-collapse_id="<#- data.name #>">
			<i class="fa"></i>
		</div>
		<div class="elementor-panel-heading-title elementor-section-title"><#= data.label #></div>
	</div>
{/capture}

{capture ControlTab}
	<div class="elementor-panel-tab-heading">
		<#= data.label #>
	</div>
{/capture}

{capture ControlDivider}
	<hr />
{/capture}

{capture ControlColor}
	<#
	var defaultValue = '', dataAlpha = '';
	if ( data.default ) {
		if ( '#' !== data.default.substring( 0, 1 ) ) {
			defaultValue = '#' + data.default;
		} else {
			defaultValue = data.default;
		}
		defaultValue = ' data-default-color=' + defaultValue; // Quotes added automatically.
	}
	if ( data.alpha ) {
		dataAlpha = ' data-alpha=true';
	}
	#>
	<div class="elementor-control-field">
		<label class="elementor-control-title">
		<# if ( data.label ) { #>
			<#= data.label #>
		<# } #>
		<# if ( data.description ) { #>
			<span class="elementor-control-description"><#= data.description #></span>
		<# } #>
		</label>
		<div class="elementor-control-input-wrapper">
			<input data-setting="<#- name #>" class="color-picker-hex" type="text" maxlength="7" placeholder="{ce__('Hex Value')|escape:'html':'UTF-8'}" <#- defaultValue #><#- dataAlpha #> />
		</div>
	</div>
{/capture}

{capture ControlMedia}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-units-choices">
			<input type="radio" checked id="elementor-control-media-url-<#- data._cid #>" value="<#- data.controlValue.url #>"
			><label class="elementor-units-choices-label elementor-control-media-url">{ce__('Modify URL')}</label>
		</div>
		<div class="elementor-control-input-wrapper">
			<div class="elementor-control-media">
				<div class="elementor-control-media-upload-button">
					<i class="fa fa-plus-circle"></i>
				</div>
				<div class="elementor-control-media-image-area<# print( data.seo ? ' elementor-control-media-seo' : '' ) #>">
					<div class="elementor-control-media-image" style="background-image: url('<#- elementor.imagesManager.getImageUrl( data.controlValue ) #>');"></div>
					<div class="elementor-control-media-btn elementor-control-media-alt">{ce__('Alt')}</div>
					<div class="elementor-control-media-btn elementor-control-media-title">{ce__('Title')}</div>
					<div class="elementor-control-media-btn elementor-control-media-delete">{ce__('Delete')}</div>
				</div>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-description"><#= data.description #></div>
		<# } #>
		<input type="hidden" data-setting="<#- data.name #>" />
	</div>
{/capture}

{capture ControlSlider}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		{$smarty.capture.ControlBaseUnits|cleanHtml}
		<div class="elementor-control-input-wrapper elementor-clearfix">
			<div class="elementor-slider"></div>
			<div class="elementor-slider-input">
				<input type="number" min="<#- data.min #>" max="<#- data.max #>" step="<#- data.step #>" data-setting="size" />
			</div>
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlDimensions}
	{$dimensions = [
		'top' => ce__('Top'),
		'right' => ce__('Right'),
		'bottom' => ce__('Bottom'),
		'left' => ce__('Left')
	]}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		{$smarty.capture.ControlBaseUnits|cleanHtml}
		<div class="elementor-control-input-wrapper">
			<ul class="elementor-control-dimensions">
				{foreach $dimensions as $dimension_key => $dimension_title}
					<li class="elementor-control-dimension">
						<input type="number" data-setting="{$dimension_key|escape:'html':'UTF-8'}" placeholder="<#
							if ( _.isObject( data.placeholder ) ) {
								if ( ! _.isUndefined( data.placeholder.{$dimension_key|escape:'html':'UTF-8'} ) ) {
									print( data.placeholder.{$dimension_key|escape:'html':'UTF-8'} );
								}
							} else {
								print( data.placeholder );
							} #>"
							<# if ( -1 === _.indexOf( allowed_dimensions, '{$dimension_key|escape:'html':'UTF-8'}' ) ) { #>disabled<# } #>/>
						<span>{$dimension_title|cleanHtml}</span>
					</li>
				{/foreach}
				<li>
					<button class="elementor-link-dimensions tooltip-target" data-tooltip="{ce__('Link values together')|escape:'html':'UTF-8'}">
						<span class="elementor-linked"><i class="fa fa-link"></i></span>
						<span class="elementor-unlinked"><i class="fa fa-chain-broken"></i></span>
					</button>
				</li>
			</ul>
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlChoose}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-control-input-wrapper">
			<div class="elementor-choices">
				<# _.each( data.options, function( options, value ) { #>
				<input id="elementor-choose-<#- data._cid + data.name + value #>" type="radio" name="elementor-choose-<#- data.name #>" value="<#- value #>">
				<label class="elementor-choices-label tooltip-target" for="elementor-choose-<#- data._cid + data.name + value #>" data-tooltip="<#- options.title #>" title="<#- options.title #>">
					<i class="<#- options.icon #>"></i>
				</label>
				<# } ); #>
			</div>
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlWysiwyg}
	<label>
		<span class="elementor-control-title"><#= data.label #></span>
		<textarea data-setting="<#- data.name #>"></textarea>
	</label>
{/capture}

{capture ControlCode}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-control-input-wrapper">
			<textarea rows="10" class="elementor-input-style elementor-code-editor" data-setting="<#- data.name #>"></textarea>
		</div>
	</div>
	<# if ( data.description ) { #>
		<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlFont}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-control-input-wrapper">
			<select class="elementor-control-font-family" data-setting="<#- data.name #>">
				<option value="">{ce__('Default')}</option>
				<optgroup label="{ce__('System')|escape:'html':'UTF-8'}">
					<# _.each( getFontsByGroups( 'system' ), function( fontType, fontName ) { #>
					<option value="<#- fontName #>"><#= fontName #></option>
					<# } ); #>
				</optgroup>
				<optgroup label="{ce__('Google')|escape:'html':'UTF-8'}">
					<# _.each( getFontsByGroups( [ 'googlefonts', 'earlyaccess' ] ), function( fontType, fontName ) { #>
					<option value="<#- fontName #>"><#= fontName #></option>
					<# } ); #>
				</optgroup>
			</select>
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlURL}
	<div class="elementor-control-field elementor-control-url-external-<#= data.show_external ? 'show' : 'hide' #>">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-control-input-wrapper">
			<input type="url" data-setting="url" placeholder="<#- data.placeholder #>" />
			<button class="elementor-control-url-target tooltip-target" data-tooltip="{ce__('Open Link in new Tab')|escape:'html':'UTF-8'}" title="{ce__('Open Link in new Tab')|escape:'html':'UTF-8'}">
				<span class="elementor-control-url-external" title="{ce__('New Window')|escape:'html':'UTF-8'}"><i class="fa fa-external-link"></i></span>
			</button>
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlRepeater}
	<label>
		<span class="elementor-control-title"><#= data.label #></span>
	</label>
	<div class="elementor-repeater-fields"></div>
	<div class="elementor-button-wrapper">
		<button class="elementor-button elementor-button-default elementor-repeater-add"><span class="eicon-plus"></span>{ce__('Add Item')}</button>
	</div>
{/capture}

{capture ControlIcon}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-control-input-wrapper">
			<select class="elementor-control-icon" data-setting="<#- data.name #>" data-placeholder="{ce__('Select Icon')}">
				<option value="">{ce__('Select Icon')}</option>
				<# _.each( data.icons, function( option_title, option_value ) { #>
				<option value="<#- option_value #>"><#= option_title #></option>
				<# } ); #>
			</select>
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#- data.description #></div>
	<# } #>
{/capture}

{capture ControlStructure}
	<div class="elementor-control-field">
		<div class="elementor-control-input-wrapper">
			<div class="elementor-control-structure-title">{ce__('Structure')}</div>
			<# var currentPreset = elementor.presetsFactory.getPresetByStructure( data.controlValue ); #>
			<div class="elementor-control-structure-preset elementor-control-structure-current-preset">
				<#= elementor.presetsFactory.getPresetSVG( currentPreset.preset, 233, 72, 5 ).outerHTML #>
			</div>
			<div class="elementor-control-structure-reset"><i class="fa fa-undo"></i>{ce__('Reset Structure')}</div>
			<#
			var morePresets = getMorePresets();

			if ( morePresets.length > 1 ) { #>
				<div class="elementor-control-structure-more-presets-title">{ce__('More Structures')}</div>
				<div class="elementor-control-structure-more-presets">
					<# _.each( morePresets, function( preset ) { #>
						<div class="elementor-control-structure-preset-wrapper">
							<input id="elementor-control-structure-preset-<#- data._cid #>-<#- preset.key #>" type="radio" name="elementor-control-structure-preset-<#- data._cid #>" data-setting="structure" value="<#- preset.key #>">
							<label class="elementor-control-structure-preset" for="elementor-control-structure-preset-<#- data._cid #>-<#- preset.key #>">
								<#= elementor.presetsFactory.getPresetSVG( preset.preset, 102, 42 ).outerHTML #>
							</label>
							<div class="elementor-control-structure-preset-title"><#= preset.preset.join( ', ' ) #></div>
						</div>
					<# } ); #>
				</div>
			<# } #>
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlSelect2}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-control-input-wrapper">
			<# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
			<select class="elementor-select2" type="select2" <#- multiple #> data-setting="<#- data.name #>">
				<# _.each( data.options, function( option_title, option_value ) {
					var value = data.controlValue;
					if ( typeof value == 'string' ) {
						var selected = ( option_value === value ) ? 'selected' : '';
					} else if ( null !== value ) {
						var value = _.values( value );
						var selected = ( -1 !== value.indexOf( option_value ) ) ? 'selected' : '';
					}
					#>
				<option <#- selected #> value="<#- option_value #>"><#= option_title #></option>
				<# } ); #>
			</select>
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlDateTime}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-control-input-wrapper">
			<input class="elementor-date-time-picker" type="text" data-setting="<#- data.name #>">
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlBoxShadow}
	<#
	var defaultColorValue = '';

	if ( data.default.color ) {
		if ( '#' !== data.default.color.substring( 0, 1 ) ) {
			defaultColorValue = '#' + data.default.color;
		} else {
			defaultColorValue = data.default.color;
		}

		defaultColorValue = ' data-default-color=' + defaultColorValue; // Quotes added automatically.
	}
	#>
	<div class="elementor-control-field">
		<label class="elementor-control-title">{ce__('Color')}</label>
		<div class="elementor-control-input-wrapper">
			<input data-setting="color" class="elementor-box-shadow-color-picker" type="text" maxlength="7" placeholder="{ce__('Hex Value')|escape:'html':'UTF-8'}" data-alpha="true"<#= defaultColorValue #>>
		</div>
	</div>
	{foreach call_user_func('CE\\ControlBoxShadow::getSliders') as $slider}
		<div class="elementor-box-shadow-slider">
			<label class="elementor-control-title">{$slider['label']|cleanHtml}</label>
			<div class="elementor-control-input-wrapper">
				<div class="elementor-slider" data-input="{$slider['type']|escape:'html':'UTF-8'}"></div>
				<div class="elementor-slider-input">
					<input type="number" min="{$slider['min']|intval}" max="{$slider['max']|intval}" data-setting="{$slider['type']|escape:'html':'UTF-8'}">
				</div>
			</div>
		</div>
	{/foreach}
{/capture}

{capture ControlAnimation}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-control-input-wrapper">
			<select data-setting="<#- data.name #>">
				<option value="">{ce__('None')}</option>
				{foreach call_user_func('CE\\ControlAnimation::getAnimations') as $animations_group_name => $animations_group}
					<optgroup label="{$animations_group_name|escape:'html':'UTF-8'}">
					{foreach $animations_group as $animation_name => $animation_title}
						<option value="{$animation_name|escape:'html':'UTF-8'}">{$animation_title|cleanHtml}</option>
					{/foreach}
					</optgroup>
				{/foreach}
			</select>
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlHoverAnimation}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-control-input-wrapper">
			<select data-setting="<#- data.name #>">
				<option value="">{ce__('None')}</option>
				{foreach call_user_func('CE\\ControlHoverAnimation::getAnimations') as $animation_name => $animation_title}
					<option value="{$animation_name|escape:'html':'UTF-8'}">{$animation_title|cleanHtml}</option>
				{/foreach}
			</select>
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{capture ControlOrder}
	<div class="elementor-control-field">
		<label class="elementor-control-title"><#= data.label #></label>
		<div class="elementor-control-input-wrapper">
			<div class="elementor-control-oreder-wrapper">
				<select data-setting="order_by">
				<# _.each( data.options, function( option_title, option_value ) { #>
					<option value="<#- option_value #>"><#= option_title #></option>
				<# } ); #>
				</select>
				<input id="elementor-control-order-input-<#- data._cid #>" type="checkbox" data-setting="reverse_order">
				<label for="elementor-control-order-input-<#- data._cid #>" class="elementor-control-order-label">
					<i class="fa fa-sort-amount-desc"></i>
				</label>
			</div>
		</div>
	</div>
	<# if ( data.description ) { #>
	<div class="elementor-control-description"><#= data.description #></div>
	<# } #>
{/capture}

{function ControlBase this=null}
	<script type="text/html" id="tmpl-elementor-control-{$this->getType()|escape:'html':'UTF-8'}-content">
		<div class="elementor-control-content">
			{$this->contentTemplate()|cleanHtml}
		</div>
	</script>
{/function}

{capture User_getCurrentIntroduction}
	<div id="elementor-introduction-title">{ce__('Two Minute Tour Of Elementor')}</div>
	<div id="elementor-introduction-subtitle">{ce__('Watch this quick tour that gives you a basic understanding of how to use Elementor.')}</div>
{/capture}

{capture User_getCurrentIntroductionVideo}
	<div class="elementor-video-wrapper">
		<if{literal}rame src="https://www.youtube.com/embed/6u45V2q1s4k?autoplay=1&rel=0&showinfo=0" frameborder="0" allowfullscreen></if{/literal}rame>
	</div>
{/capture}

{capture ce_custom_css}
	{ce__('Use "selector" to target wrapper element. Examples:')}
	<pre style="font-style: normal">{ce__(
		"/* For main element */\nselector { color: red; }\n"|cat:
		"/* For child element */\nselector .child-element { margin: 10px; }\n"|cat:
		"/* Or use any custom selector */\n.my-class { text-align: center; }"
	)}</pre>
{/capture}

{capture ce_icon_list}<i class="<#- icon #>"></i> <#= text #>{/capture}

{capture ce_social_icon_list}
	<i class="<#- social #>"></i>
	<#= social.replace( 'fa fa-', '' ).replace( '-', ' ' ).replace( /\b\w/g, function( letter ) { return letter.toUpperCase() } ) #>
{/capture}

{capture ce_configure_module}
	<br><br>
    <a class="elementor-button elementor-button-default" href="%s" target="_blank"><i class="fa fa-external-link"></i> {ce__('Configure Module')}</a>
{/capture}

{capture ce_ls_new}
    <button class="elementor-button elementor-button-default" style="width: 100%;">
        <i class="fa fa-plus"></i> {ce__('Create New Slider')}
    </button>
    <div class="elementor-control-description"
    	style="position: absolute; z-index: 1; left: 50%; background: #fff; padding: 0 5px; transform: translate(-50%, -1px);">
    	{ce__('or')}
    </div>
{/capture}

{capture ce_ls_edit}
    <button class="elementor-button elementor-button-default" style="margin-left: 45%; width: 55%;">
        <i class="fa fa-edit"></i> {ce__('Edit Slider')}
    </button>
{/capture}

{capture ce_ls_alert}
    <div style="background: #d1eff8; border: 1px solid #bcdff1; border-radius: 4px; padding: 20px; font-size: 12px; text-align: center; color: #43a2bf;">
        <svg width="40" viewBox="0 0 259.559 259.559" fill="currentColor">
            <polygon points="186.811,106.547 129.803,218.647 73.273,106.547"/><polygon points="78.548,94.614 129.779,43.382 181.011,94.614"/>
            <polygon points="144.183,40.912 213.507,40.912 193.941,90.67"/><polygon points="66.375,89.912 50.044,40.912 115.375,40.912"/>
            <polygon points="59.913,106.547 109.546,204.977 3.288,106.547"/><polygon points="200.2,106.547 256.271,106.547 150.258,204.75"/>
            <polygon points="205.213,94.614 223.907,47.082 259.559,94.614"/><polygon points="38.331,43.507 55.373,94.614 0,94.614"/>
        </svg>
        <h3 style="margin: 5px 0 13px; font-size: 13px; font-weight: bold;">{ce__('Do you need an awesome slider?')}</h3>
        <p style="line-height: 1.3em">
        	{ce__('Creative Slider is the perfect choice for you. With this widget you can easily place Creative Slider anywhere.')}
        </p>
    </div>
{/capture}

{capture ce_ls_promo}
	<style>
	#ls-btn-demo, #ls-btn-more {
		display: inline-block;
		width: 48%;
		text-align: center;
	}
	#ls-btn-demo { background: #38b54a; }
	#ls-btn-demo:hover { opacity: 0.85; }
	#ls-btn-more { margin-left: 4%; }
	</style>
	<a href="https://addons.prestashop.com/demo/FO11013.html" target="_blank"
		id="ls-btn-demo" class="elementor-button elementor-button-default">{ce__('Live Demo')}</a
	><a href="https://addons.prestashop.com/sliders-galleries/19062-creative-slider-responsive-slideshow.html" target="_blank"
		id="ls-btn-more" class="elementor-button elementor-button-default">{ce__('Read More')}</a>
{/capture}
