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
<div id="pst_product_filter_wrp">
    <div id="pst_product_filter" class="panel {if $pstpf_use_symfony}panel-sf{else}panel panel-nosf{/if} {if $pstpf_ps81_plus}pstpf_ps81{/if} {if $pstpf_ps178_plus}pstpf_ps178{/if}">
        {capture name='pstpf_set_select'}
            {if $pstpf_sets && count($pstpf_sets)}
                <div class="pstpf-select-replace" id="pstpf_set_select">
                    <div class="pstpf-select-current-wrp">
                        <span class="pstpf-select-current" data-default-text="{l s='-- select filter set --' mod='pstproductfilter'}">
                            {if $pstpf_current_set && $pstpf_current_set->id}{$pstpf_current_set->name|escape:'html':'UTF-8'}{else}{l s='-- select filter set --' mod='pstproductfilter'}{/if}
                        </span>
                        <i class="icon icon-chevron-down pull-right pstpf-select-toggle"></i>
                    </div>
                    <div class="pstpf-select-options">
                        <div class="pstpf-select-option" data-value="">--</div>
                        {foreach from=$pstpf_sets item='set'}
                            <div class="pstpf-select-option {if $pstpf_current_set && $pstpf_current_set->id == $set->id}pstpf-selected{/if}" data-value="{$set->id|intval}">
                                <span class="pstpf-select-option-name">{$set->name|escape:'html':'UTF-8'}</span>
                                <span class="pstpf-select-del" data-id-set="{$set->id|intval}" title="{l s='Delete' mod='pstproductfilter'}">&times;</span>
                            </div>
                        {/foreach}
                    </div>
                </div>
            {/if}
        {/capture}
        {capture name='pstpf_filter_btns'}
            <div class="pstpf_btns_wrp">
                <button class="btn btn-default pull-right pstpf-btn-edit-filters" id="pstpf-edit-filters">
                    {if $pstpf_use_symfony}<i class="material-icons">edit</i>{else}<i class="icon-edit"></i> {/if}
                    <span class="pstpf-ef-text" data-alt-text="{l s='Finish edit' mod='pstproductfilter'}">{l s='Edit filters' mod='pstproductfilter'}</span>
                </button>
                <form action="{$pstpf_products_url|escape:'quotes':'UTF-8'}" method="post" class="pull-right">
                    <button name="submitResetproduct" class="btn btn-warning pstpf-btn-reset-filters" id="pstpf-reset-filters" {if !$pstpf_any_filter_active}style="display: none;" {/if}>
                        {if $pstpf_use_symfony}<i class="material-icons">delete</i>{else}<i class="icon-remove"></i>{/if}
                        {l s='Reset' mod='pstproductfilter'}
                    </button>
                </form>
                <div id="pstpf-save-set-wrp" class="pull-right">
                    <form id="pstpf-save-set-form">
                        <input type="text" class="form-control pstpf_skip_input" id="pstpf-set-name" placeholder="{l s='Name' mod='pstproductfilter'}" name="filter_set" value="{if $pstpf_current_set}{$pstpf_current_set->name|escape:'html':'UTF-8'}{/if}">
                        <button class="btn btn-primary" type="submit">{l s='Save' mod='pstproductfilter'}</button>
                        <button class="btn btn-default" id="pstpf-set-cancel">{l s='Cancel' mod='pstproductfilter'}</button>
                    </form>
                    <button class="btn btn-default" id="pstpf-save-set">{if $pstpf_use_symfony}<i class="material-icons">save</i>{else}<i class="icon icon-save"></i>{/if} {l s='Save filter set' mod='pstproductfilter'}</button>
                </div>
            </div>
        {/capture}

        {if $pstpf_use_symfony}
        <div class="card js-grid-panel" id="pstpf_grid_panel">
            <div class="card-header js-grid-header">
                <h3 class="d-inline-block card-header-title">
                    {l s='Product filter' mod='pstproductfilter'}
                    {$smarty.capture.pstpf_set_select}
                </h3>
                {$smarty.capture.pstpf_filter_btns}
            </div>
            <div class="card-body">
        {else}
            <div class="panel-heading">
                <i class="icon-cogs"></i> {l s='Product filter' mod='pstproductfilter'}
                {$smarty.capture.pstpf_set_select}
                {$smarty.capture.pstpf_filter_btns}
            </div>
        {/if}

            <p class="pstpf-fgroup-h">{l s='Active filters:' mod='pstproductfilter'}</p>
            <div class="row pstpf-filters-row" id="pstpf-active-filters">
                {foreach from=$pstpf_filters key='filter_name' item='input'}
                    {if $input.active && !(isset($input.hidden) && $input.hidden)}
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 pstpf-filter-col {if $pstpf_use_symfony}px-2{/if} {if $input.value}pstpf-filter-col-active{/if}" data-filter="{$filter_name|escape:'html':'UTF-8'}">
                        {include file="../hook/_filter_col.tpl" input=$input filter_name=$filter_name}
                    </div>
                    {/if}
                {/foreach}
            </div>

            <div id="pstpf-inactive-filters-wrp">
                <hr>
                <p class="pstpf-fgroup-h">{l s='Inactive filters:' mod='pstproductfilter'}</p>
                <div class="row pstpf-filters-row" id="pstpf-inactive-filters">
                    {foreach from=$pstpf_filters key='filter_name' item='input'}
                        {if !$input.active && !(isset($input.hidden) && $input.hidden)}
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 pstpf-filter-col {if $pstpf_use_symfony}px-2{/if} {if $input.value}pstpf-filter-col-active{/if}" data-filter="{$filter_name|escape:'html':'UTF-8'}">
                                {include file="../hook/_filter_col.tpl" input=$input filter_name=$filter_name}
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>

            <div id="pstpf-list-columns-wrp">
                <div class="pstpf-hr2"></div>
                <p class="pstpf-fgroup-h">
                    {l s='Product list columns' mod='pstproductfilter'}
                    <button class="btn btn-default" id="pstpf-column-reset">{l s='Reset' mod='pstproductfilter'}</button>
                    <label class="pstpf-toggle-columns"><input type="checkbox" id="pstpf-toggle-columns-custom">{l s='Select / unselect all custom columns' mod='pstproductfilter'}</label>
                    <label class="pstpf-toggle-columns"><input type="checkbox" id="pstpf-toggle-columns-default">{l s='Select / unselect all default columns' mod='pstproductfilter'}</label>
                </p>
                <ol class="pst-col-table" id="pst-col-table">
                    {foreach from=$pstpf_columns key='key' item='column'}
                        <li class="pst-col-wrp {if isset($column.default) && $column.default}pst-col-default{/if}" data-column="{$key|escape:'html':'UTF-8'}" data-default-position="{$column.default_position|intval}">
                            <div class="pst-col-title pst-col-td">
                                <label>
                                    <input type="checkbox" name="columns[{$key|escape:'html':'UTF-8'}]" {if $column.active}checked{/if} class="pstpf_column_cb" value="1" {if !empty($column.disabled)}disabled{/if}>
                                    {$column.name|escape:'html':'UTF-8'}
                                </label>
                                {if isset($column.hint)}
                                    <span class="tooltip-link" data-confirm-message="" data-toggle="pstooltip" data-placement="top" data-original-title="{$column.hint|escape:'html':'UTF-8'}">
                                        <i class="material-icons">help_outline</i>
                                    </span>
                                {/if}
                                {if empty($column.disabled)}<span class="pstpf-move"><i class="material-icons">open_with</i></span>{/if}
                            </div>
                        </li>
                    {/foreach}
                </ol>
            </div>

            <div id="pstpf-selected-list-wrp">
                {include file="../hook/_selected_filters.tpl"}
            </div>

        {if $pstpf_use_symfony}
            </div></div>
        {/if}
    </div>
</div>
<div id="pstpf-saved-msg" style="display: none;">{l s='Saved' mod='pstproductfilter'}</div>
<div id="pstpf-error-msg" style="display: none;">{l s='Unknown error' mod='pstproductfilter'}</div>
<script>
    try {
        if (+localStorage.getItem('pstpf_hide_block')) {
            var pstpf_wrp = document.getElementById('pst_product_filter_wrp');
            pstpf_wrp.style.display = 'none';
        }
        if (+localStorage.getItem('pstpf_full_width')) {
            var pstpf_page_wrp = document.getElementById('main-div');
            pstpf_page_wrp.classList.add("pstpf-full-width");
        }
    } catch {
        // do nothing
    }
</script>