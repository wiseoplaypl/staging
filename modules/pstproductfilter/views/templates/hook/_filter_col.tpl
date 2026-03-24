{*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2020 Presta.Site
* @license   LICENSE.txt
*}
<div class="pstpf-filter-col-content">
    <label class="control-label pstpf-label-filter" for="pstpf_{$filter_name|escape:'html':'UTF-8'}">
        {$input.label|escape:'html':'UTF-8'}
        {if $input.type == 'range'}
            <span class="pstpf_label_range">
                <input type="text" class="pstpf-min pstpf-range-input form-control" id="pstpf_{$filter_name|escape:'html':'UTF-8'}_from" value="{$input.min|intval}">
                -
                <input type="text" class="pstpf-max pstpf-range-input form-control" id="pstpf_{$filter_name|escape:'html':'UTF-8'}_to" value="{$input.max|intval}">
            </span>
        {/if}
        {if isset($input.hint2)}
            {if $pstpf_use_symfony}
                <span class="tooltip-link" data-confirm-message="" data-toggle="pstooltip" data-placement="top" data-original-title="{$input.hint2|escape:'html':'UTF-8'}">
                    <i class="material-icons">help_outline</i>
                </span>
            {else}
                <span class="pstpf-hint2 label-tooltip" data-toggle="tooltip" title="{$input.hint2|escape:'html':'UTF-8'}">?</span>
            {/if}
        {/if}
    </label>
    {if $input.type == 'text'}
        <input type="text"
               name="pstpf[{$filter_name|escape:'html':'UTF-8'}]"
               value="{$input.value|escape:'html':'UTF-8'}"
               id="pstpf_{$filter_name|escape:'html':'UTF-8'}"
               class="{$input.class|escape:'html':'UTF-8'} form-control"
               data-filter-name="{$filter_name|escape:'html':'UTF-8'}"
               {if isset($input.hint)}placeholder="{$input.hint|escape:'html':'UTF-8'}"{/if}
        >
    {elseif $input.type == 'select'}
        <select name="pstpf[{$filter_name|escape:'html':'UTF-8'}]{if isset($input.multiple) && $input.multiple}[]{/if}"
                data-filter-name="{$filter_name|escape:'html':'UTF-8'}"
                id="pstpf_{$filter_name|escape:'html':'UTF-8'}"
                class="{$input.class|escape:'html':'UTF-8'}"
                {if isset($input.multiple) && $input.multiple}multiple{/if}
        >
            {foreach from=$input.options key='id_option' item='name'}
                {if isset($input.multiple) && $input.multiple && is_array($input.value)}
                    <option value="{$id_option|intval}" {if in_array($id_option, $input.value)}selected{/if}>{$name|escape:'html':'UTF-8'}</option>
                {else}
                    <option value="{$id_option|intval}" {if $input.value == $id_option}selected{/if}>{$name|escape:'html':'UTF-8'}</option>
                {/if}
            {/foreach}
        </select>
    {elseif $input.type == 'select_yn'}
        <select name="pstpf[{$filter_name|escape:'html':'UTF-8'}]"
                data-filter-name="{$filter_name|escape:'html':'UTF-8'}"
                id="pstpf_{$filter_name|escape:'html':'UTF-8'}"
                class="{$input.class|escape:'html':'UTF-8'}"
        >
            <option value=""></option>
            <option value="yes" {if $input.value == 'yes'}selected{/if}>{l s='Yes' mod='pstproductfilter'}</option>
            <option value="no" {if $input.value == 'no'}selected{/if}>{l s='No' mod='pstproductfilter'}</option>
        </select>
    {elseif $input.type == 'range'}
        <input type="hidden"
               name="pstpf[{$filter_name|escape:'html':'UTF-8'}]"
               data-filter-name="{$filter_name|escape:'html':'UTF-8'}"
               value="{$input.value|escape:'html':'UTF-8'}"
               data-default="{$input.value|escape:'html':'UTF-8'}"
               id="pstpf_{$filter_name|escape:'html':'UTF-8'}"
        >
        <div class="pstpf_range_slider_wrp">
            <div id="pstpf_{$filter_name|escape:'html':'UTF-8'}_slider"
                 data-range-id="pstpf_{$filter_name|escape:'html':'UTF-8'}"
                 data-min="{$input.min|intval}"
                 data-max="{$input.max|intval}"
                 data-step="{if isset($input.step) && $input.step}{$input.step|escape:'html':'UTF-8'}{else}1{/if}"
            ></div>
        </div>
        <script>
            $(function () {
                var pstpf_range_id = "pstpf_{$filter_name|escape:'html':'UTF-8'}";
                var pstpf_slider_id = pstpf_range_id + '_slider';
                var $slider = $('#' + pstpf_slider_id);
                var pstpf_range_values = $('#' + pstpf_range_id).val();
                var $pstpf_from = $('#' + pstpf_range_id + '_from');
                var $pstpf_to = $('#' + pstpf_range_id + '_to');
                var min = $slider.data('min');
                var max = $slider.data('max');
                var step = ($slider.data('step') ? $slider.data('step') : 1);

                var r_from = min;
                var r_to = max;
                if (pstpf_range_values) {
                    pstpf_range_values = pstpf_range_values.split('-');
                    r_from = pstpf_range_values[0];
                    r_to = pstpf_range_values[1];
                    var r_from_text = r_from;
                    var r_to_text = r_to;
                    $pstpf_from.val(r_from_text);
                    $pstpf_to.val(r_to_text);
                } else {
                    var from_text = $pstpf_from.val();
                    var to_text = $pstpf_to.val();
                    $pstpf_from.val(from_text);
                    $pstpf_to.val(to_text);
                }

                pstpf_initSlider($slider, min, max, r_from, r_to, step, $pstpf_from, $pstpf_to, pstpf_range_id);

                $pstpf_from.add($pstpf_to).pstpfTypeWatch({
                    captureLength: 0,
                    highlight: false,
                    wait: 500,
                    callback: function(text){
                        $slider.slider('values', 0, $pstpf_from.val());
                        $slider.slider('values', 1, $pstpf_to.val());
                        $('#' + pstpf_range_id).val($pstpf_from.val() + '-' + $pstpf_to.val());
                        pstpf_reloadProductList(true);
                        pstpf_resetCurrentSet();
                    }
                });
            });
        </script>
    {elseif $input.type == 'dropdown'}
        <div class="pstpf-status-block" id="pstpf_{$filter_name|escape:'html':'UTF-8'}_block">
            <span class="form-control pstpf-status-helper" data-target="pstpf_status_wrp_{$filter_name|escape:'html':'UTF-8'}">
                <span class="pstpf-status-helper-text">
                    {if isset($input.value_dropdown) && is_array($input.value_dropdown)}
                        {foreach from=$input.value_dropdown item='option'}
                            <span class="pstpf-dropdown-option-selected badge badge-light" data-for="pstpf_{$filter_name|escape:'html':'UTF-8'}_{$option[$input.id_key]|intval}">
                                    {if isset($option.color)}<span class="pstpf-status-marker" style="background: {$option.color|escape:'html':'UTF-8'};"></span>{/if}
                            {$option.name|escape:'html':'UTF-8'}
                                    &times;
                            </span>
                        {/foreach}
                    {/if}
                </span>
                <i class="icon icon-chevron-down pull-right pstpf-dropdown-toggle"></i>
            </span>
            <div class="pstpf-status-content {if !empty($input.lazyload)}pstpf-sw-lazyload{/if}">
                {if isset($input.search) && $input.search}
                    <input type="text" class="form-control pstpf-status-search {if isset($input.optgroup) && $input.optgroup}pstpf-ss-optgroup{/if}" placeholder="{l s='Search' mod='pstproductfilter'}">
                {/if}
                <div class="pstpf-status-wrp" id="pstpf_status_wrp_{$filter_name|escape:'html':'UTF-8'}">
                    {include file="./_dropdown_data.tpl" pstpf_lazyload=(!empty($input.lazyload))}
                </div>
            </div>
        </div>
    {/if}
    <span class="pstpf-move">{if $pstpf_use_symfony}<i class="material-icons">open_with</i> {else}<i class="icon-move"></i>{/if}</span>
</div>