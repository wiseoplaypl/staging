{**
 * Creative Elements - Elementor based PageBuilder
 *
 * @author    WebshopWorks
 * @copyright 2019-2020 WebshopWorks.com
 * @license   One domain support license
 *}

{function SchemeColor title='' description='' schemes=[]}
    <div class="elementor-panel-scheme-content elementor-panel-box">
        <div class="elementor-panel-heading">
            <div class="elementor-panel-heading-title">{$title|cleanHtml}</div>
        </div>
        {if $description}
            <div class="elementor-panel-scheme-description elementor-descriptor">{$description|cleanHtml}</div>
        {/if}
        <div class="elementor-panel-scheme-items elementor-panel-box-content"></div>
    </div>
    <div class="elementor-panel-scheme-colors-more-palettes elementor-panel-box">
        <div class="elementor-panel-heading">
            <div class="elementor-panel-heading-title">{ce__('More Palettes')}</div>
        </div>
        <div class="elementor-panel-box-content">
            {foreach $schemes as $scheme_name => $scheme}
                <div class="elementor-panel-scheme-color-system-scheme" data-scheme-name="{$scheme_name|escape:'html':'UTF-8'}">
                    <div class="elementor-panel-scheme-color-system-items">
                        {foreach $scheme['items'] as $color_value}
                            <div class="elementor-panel-scheme-color-system-item" style="background-color: {$color_value|escape:'html':'UTF-8'};"></div>
                        {/foreach}
                    </div>
                    <div class="elementor-title">{$scheme['title']|cleanHtml}</div>
                </div>
            {/foreach}
        </div>
    </div>
{/function}

{capture SchemeTypography}
	<div class="elementor-panel-scheme-items"></div>
{/capture}

{function SchemeBase this=null type=''}
    <script type="text/template" id="tmpl-elementor-panel-schemes-{$type|escape:'html':'UTF-8'}">
        <div class="elementor-panel-scheme-buttons">
            <div class="elementor-panel-scheme-button-wrapper elementor-panel-scheme-reset">
                <button class="elementor-button">
                    <i class="fa fa-undo"></i>
                    {ce__('Reset')}
                </button>
            </div>
            <div class="elementor-panel-scheme-button-wrapper elementor-panel-scheme-discard">
                <button class="elementor-button">
                    <i class="fa fa-times"></i>
                    {ce__('Discard')}
                </button>
            </div>
            <div class="elementor-panel-scheme-button-wrapper elementor-panel-scheme-save">
                <button class="elementor-button elementor-button-success" disabled>{ce__('Apply')}</button>
            </div>
        </div>
        {$this->printTemplateContent()|cleanHtml}
    </script>
{/function}
