{*
	*  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
	*
	* @author    Línea Gráfica E.C.E. S.L.
	* @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
	* @license   https://www.lineagrafica.es/licenses/license_en.pdf https://www.lineagrafica.es/licenses/license_es.pdf https://www.lineagrafica.es/licenses/license_fr.pdf
*}
{if !$simple_header}
<div class="row">
    <div class="col-lg-6">
        {* Choose number of results per page *}
        <div class="pagination">
            {l s='Display' mod='lgproductmove'}
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                {$selected_pagination|intval}
                <i class="icon-caret-down"></i>
            </button>
            <ul class="dropdown-menu">
                {foreach $pagination AS $value}
                    <li>
                        <a href="javascript:void(0);" class="pagination-items-page" data-items="{$value|intval}" data-list-id="{$list_id|escape:'htmlall':'UTF-8'}">{$value|intval}</a>
                    </li>
                {/foreach}
            </ul>
            / {$list_total|intval} {l s='result(s)' mod='lgproductmove'}
            <input type="hidden" id="{$list_id|escape:'htmlall':'UTF-8'}-pagination-items-page" name="{$list_id|escape:'htmlall':'UTF-8'}_pagination" value="{$selected_pagination|intval}" />
            <input type="hidden" id="{$list_id|escape:'htmlall':'UTF-8'}-pagination-page" name="{$list_id|escape:'htmlall':'UTF-8'}_page" value="{$page|intval}" />
        </div>
        {if !$simple_header && $list_total > $pagination[0]}
        <ul class="pagination pull-right">
            <li {if $page <= 1}class="disabled"{/if}>
                <a href="javascript:void(0);" class="pagination-link" data-page="1" data-list-id="{$list_id|escape:'htmlall':'UTF-8'}">
                    <i class="icon-double-angle-left"></i>
                </a>
            </li>
            <li {if $page <= 1}class="disabled"{/if}>
                <a href="javascript:void(0);" class="pagination-link" data-page="{$page|intval - 1}" data-list-id="{$list_id|escape:'htmlall':'UTF-8'}">
                    <i class="icon-angle-left"></i>
                </a>
            </li>
            {assign p 0}
            {while $p++ < $total_pages}
                {if $p < $page-2}
                    <li class="disabled">
                        <a href="javascript:void(0);" style="font-size: 9.5px;">&hellip;</a>
                    </li>
                    {assign p $page-3}
                {elseif $p > $page+2}
                    <li class="disabled">
                        <a href="javascript:void(0);" style="font-size: 9.5px;">&hellip;</a>
                    </li>
                    {assign p $total_pages}
                {else}
                    <li {if $p == $page}class="active"{/if}>
                        <a href="javascript:void(0);" class="pagination-link" data-page="{$p|intval}" data-list-id="{$list_id|escape:'htmlall':'UTF-8'}" style="font-size: 9.5px;">{$p|intval}</a>
                    </li>
                {/if}
            {/while}
            <li {if $page >= $total_pages}class="disabled"{/if}>
                <a href="javascript:void(0);" class="pagination-link" data-page="{$page|intval + 1}" data-list-id="{$list_id|escape:'htmlall':'UTF-8'}">
                    <i class="icon-angle-right"></i>
                </a>
            </li>
            <li {if $page >= $total_pages}class="disabled"{/if}>
                <a href="javascript:void(0);" class="pagination-link" data-page="{$total_pages|intval}" data-list-id="{$list_id|escape:'htmlall':'UTF-8'}">
                    <i class="icon-double-angle-right"></i>
                </a>
            </li>
        </ul>
        {/if}
    </div>
</div>
{/if}
