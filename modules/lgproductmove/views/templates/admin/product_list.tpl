{*
	*  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
	*
	* @author    Línea Gráfica E.C.E. S.L.
	* @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
	* @license   https://www.lineagrafica.es/licenses/license_en.pdf https://www.lineagrafica.es/licenses/license_es.pdf https://www.lineagrafica.es/licenses/license_fr.pdf
*}
<div class="panel col-lg-12" id="lgproductmove_products_table">
    <div class="panel-heading">
        <i class="icon-user"></i>
        {l s='Products' mod='lgproductmove'} - {l s='Selected source category:' mod='lgproductmove'} <span id="lg_product_move_selected_category"></span>
    </div>
    <div class="table-responsive-row clearfix">
        <table class="table product" id="tableproduct" width="100%">
            <thead>
            <tr class="nodrag nodrop" style="font-size: 12px">
                <th><span class="title_box">{l s='SELECTION' mod='lgproductmove'}</span></th>
                <th><span class="title_box">{l s='ID' mod='lgproductmove'}</span></th>
                <th><span class="title_box">{l s='IMAGE' mod='lgproductmove'}</span></th>
                <th><span class="title_box">{l s='NAME' mod='lgproductmove'}</span></th>
                <th><span class="title_box">{l s='REFERENCE' mod='lgproductmove'}</span></th>
                <th><span class="title_box">{l s='MANUFACTURER' mod='lgproductmove'}</span></th>
                <th><span class="title_box">{l s='DATE' mod='lgproductmove'}</span></th>
                <th><span class="title_box">{l s='PRICE' mod='lgproductmove'}</span></th>
                <th><span class="title_box">{l s='QUANTITY' mod='lgproductmove'}</span></th>
                <th><span class="title_box">{l s='STATUS' mod='lgproductmove'}</span></th>
                <th><span class="title_box">{l s='ORDER' mod='lgproductmove'}</span></th>
            </tr>
            <tr class="nodrag nodrop filter row_hover" style="font-size: 12px;">
                <th>
                    <input type="checkbox" id="checkall" value="1" name="4"> {l s='All' mod='lgproductmove'}
                </th>
                <th>
                    <input type="text" name="filterid" id="filterid" style="width:50px;"{if isset($filters.id)} value="{$filters.id|escape:'htmlall':'UTF-8'}"{/if}>
                </th>
                <th>
                    --
                </th>
                <th>
                    <input type="text" name="filtername" id="filtername"{if isset($filters.name)} value="{$filters.name|escape:'htmlall':'UTF-8'}"{/if}>
                </th>
                <th>
                    <input type="text" name="filterreference" id="filterreference"{if isset($filters.reference)} value="{$filters.reference|escape:'htmlall':'UTF-8'}"{/if} style="width:100px;">
                </th>
                <th>
                    <select name="filtermanufacturer" id="filtermanufacturer">
                        <option value="0">{l s='All' mod='lgproductmove'}</option>
                        {foreach item=marca from=$marcas}
                            <option value="{$marca['id_manufacturer']|intval}"{if isset($filters.manufacturer) && $filters.manufacturer == $marca['id_manufacturer']} selected{/if}>{$marca['name']|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </th>
                <th>
                    <input type="text" name="filterdate" id="filterdate"{if isset($filters.date)} value="{$filters.date|escape:'htmlall':'UTF-8'}"{/if} style="width:100px;" class="datepicker">
                </th>
                <th><input type="text" name="filterprice" id="filterprice"{if isset($filters.price)} value="{$filters.price|escape:'htmlall':'UTF-8'}"{/if} style="width:70px;"></th>
                <th><input type="text" name="filterstock" id="filterstock"{if isset($filters.quantity)} value="{$filters.quantity|escape:'htmlall':'UTF-8'}"{/if} style="width:70px;"></th>
                <th>
                    <select name="filterstatus" id="filterstatus">
                        <option value="2"{if (isset($filters.status) && $filters.status == 2) || !isset($filters.status)} selected{/if}>{l s='All' mod='lgproductmove'}</option>
                        <option value="1"{if isset($filters.status) && $filters.status == 1} selected{/if}>{l s='Enabled' mod='lgproductmove'}</option>
                        <option value="0"{if isset($filters.status) && $filters.status == 0} selected{/if}>{l s='Disabled' mod='lgproductmove'}</option>
                    </select>
                </th>
                <th>--</th>
            </tr>
            </thead>
            <tbody>
            {include './product_rows.tpl'}
            </tbody>
        </table>
        <div id="lgproductmove_pagination">
            {* PAGINACION *}
            {include './pagination.tpl'}
        </div>
    </div>
</div>
