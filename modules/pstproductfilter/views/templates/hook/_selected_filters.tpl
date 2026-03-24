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
<div id="pstpf-selected-list" {if !$pstpf_any_filter_active}style="display: none;" {/if}>
    {l s='Selected filters:' mod='pstproductfilter'}
    {foreach from=$pstpf_filters key='filter_name' item='input'}
        {if $input.value && !(isset($input.hidden) && $input.hidden)}
            <span class="badge badge-info pstpf-selected-remove" data-id-filter="pstpf_{$filter_name|escape:'html':'UTF-8'}">
                {$input.label|escape:'html':'UTF-8'} {$pstpf_module->renderFilterValue($input)|escape:'html':'UTF-8'}
                &times;
            </span>
        {/if}
    {/foreach}
</div>
