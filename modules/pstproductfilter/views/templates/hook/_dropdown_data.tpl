{*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2023 Presta.Site
* @license   LICENSE.txt
*}
{if empty($pstpf_lazyload)}
    <select name="pstpf[{$filter_name|escape:'html':'UTF-8'}][]"
            style="display: none;"
            id="pstpf_{$filter_name|escape:'html':'UTF-8'}_hidden_select"
            multiple
            data-filter-name="{$filter_name|escape:'html':'UTF-8'}">
        {foreach from=$input.data key="key" item='option'}
            {if isset($input.optgroup) && $input.optgroup}
                <optgroup label="{$key|escape:'html':'UTF-8'}">
                    {foreach from=$option item="item"}
                        <option value="{$item[$input.id_key]|intval}"
                                id="pstpf_{$filter_name|escape:'html':'UTF-8'}_{$item[$input.id_key]|intval}_option"
                                {if isset($input.value[$item[$input.id_key]]) && $input.value[$item[$input.id_key]]}selected{/if}
                        >
                            {$item.name|escape:'html':'UTF-8'}
                        </option>
                    {/foreach}
                </optgroup>
            {else}
                <option value="{$option[$input.id_key]|intval}"
                        id="pstpf_{$filter_name|escape:'html':'UTF-8'}_{$option[$input.id_key]|intval}_option"
                        {if isset($input.value[$option[$input.id_key]]) && $input.value[$option[$input.id_key]]}selected{/if}
                >
                    {$option.name|escape:'html':'UTF-8'}
                </option>
            {/if}
        {/foreach}
    </select>
    {if !empty($input.show_toggle_all) || !empty($input.show_strict_match)}
        <div class="pstpf-status-toggle-wrp">
            {if !empty($input.show_toggle_all)}
                <a class="pstpf-status-check-all pstpf-status-toggle-all" href="#">{l s='Check all' mod='pstproductfilter'}</a>
                {if !empty($input.show_strict_match)}/{/if}
                <a class="pstpf-status-uncheck-all pstpf-status-toggle-all" href="#">{l s='Uncheck all' mod='pstproductfilter'}</a>
            {/if}
            {if !empty($input.show_toggle_all) && !empty($input.show_strict_match)}/{/if}
            {if !empty($input.show_strict_match)}
                <label class="pstpf-status-match-all-wrp">
                    <input type="checkbox" class="pstpf-status-match-all" name="pstpf[strict][{$filter_name|escape:'html':'UTF-8'}]" value="1" {if $input.strict}checked{/if}> {l s='Strict match' mod='pstproductfilter'}
                    <span class="tooltip-link" data-confirm-message="" data-toggle="pstooltip" data-placement="top"
                          data-original-title="{l s='Enable this option to show only products with all selected values. By default, products with any selected values are shown.' mod='pstproductfilter'}"
                          title="{l s='Enable this option to show only products with all selected values. By default, products with any selected values are shown.' mod='pstproductfilter'}"
                    >
                        <i class="material-icons">help_outline</i>
                    </span>
                </label>
            {/if}
        </div>
    {/if}
    {foreach from=$input.data key="key" item='option'}
        {if isset($input.optgroup) && $input.optgroup}
            {assign var='pstpf_expanded' value=false}
            {capture name='cbs'}
                {foreach from=$option item="item"}
                    {include file="../hook/_cb.tpl" option=$item}
                    {if !empty($item[$input.id_key]) && !empty($input.value_dropdown[$item[$input.id_key]])}
                        {assign var='pstpf_expanded' value=true}
                    {/if}
                {/foreach}
            {/capture}
            <div class="pstpf-optgroup-wrp {if $pstpf_expanded}pstpf-optgroup-expanded{/if}">
                <div class="pstpf-optgroup-label">
                    <span class="font-weight-bold">{$key|escape:'html':'UTF-8'}</span>
                    <a href="#" class="pstpf-optgroup-toggle"><span class="pstpf-expand">&#x25BC;</span><span class="pstpf-collapse">&#x25B2;</span></a>
                </div>
                {$smarty.capture.cbs}
            </div>
        {else}
            {include file="../hook/_cb.tpl"}
        {/if}
    {/foreach}
{else}
    {* if it lazyload, we just make a <select> with all selected values, so they won't break the filter even though not all data is loaded *}
    <select name="pstpf[{$filter_name|escape:'html':'UTF-8'}][]"
            style="display: none;"
            id="pstpf_{$filter_name|escape:'html':'UTF-8'}_hidden_select"
            multiple
            data-filter-name="{$filter_name|escape:'html':'UTF-8'}">
        {if $input.value}
            {foreach from=$input.value item='value'}
                <option value="{$value|intval}"
                        id="pstpf_{$filter_name|escape:'html':'UTF-8'}_{$value|intval}_option"
                        selected
                >
                    {$value|intval}
                </option>
            {/foreach}
        {/if}
    </select>
    <div class="hidden">
        {if $input.value}
            {foreach from=$input.value item='value'}
                <input class="{$input.class|escape:'html':'UTF-8'} pstpf_{$filter_name|escape:'html':'UTF-8'}_cb pstpf_skip_input"
                       type="checkbox"
                       value="{$value|intval}"
                       id="pstpf_{$filter_name|escape:'html':'UTF-8'}_{$value|intval}"
                       data-option="pstpf_{$filter_name|escape:'html':'UTF-8'}_{$value|intval}_option"
                       data-block="pstpf_{$filter_name|escape:'html':'UTF-8'}_block"
                       checked
                >
            {/foreach}
        {/if}
    </div>
{/if}