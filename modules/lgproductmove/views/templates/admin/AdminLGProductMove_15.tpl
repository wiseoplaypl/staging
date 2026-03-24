{*
	*  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
	*
	* @author    Línea Gráfica E.C.E. S.L.
	* @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
	* @license   https://www.lineagrafica.es/licenses/license_en.pdf https://www.lineagrafica.es/licenses/license_es.pdf https://www.lineagrafica.es/licenses/license_fr.pdf
*}
{include './variables.tpl'}
<form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}" class="defaultForm" style="padding-top: 15px;">
    <fieldset id="fieldset_0">
        <legend>
            Mover/Asociar Productos
            <a href="../modules/lgproductmove/readme/readme_es.pdf#page=4" target="_blank">
                <img src="../modules/lgproductmove/views/img/info.png" style="width: 15px; height: 15px;">
            </a>
        </legend>
        <label for="categoria" style="text-align: left;">{l s='1. Select the source category (From)' mod='lgproductmove'}:</label>
        <div class="margin-form">
            <select name="categoria_origen">
                {foreach item=categoria from=$categorias}
                    <option value="{$categoria['id_category']|intval}" {if $categoria['active'] == 0} style="color:grey;"{/if}{if $categoria['id_category'] == $categoria_origen} selected{/if}>{for $i=0 to $categoria['level_depth']}&nbsp;&nbsp;{/for}{$categoria['name']|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
            <span id="category_total_products">{count($productos)|intval}</span> {l s='products' mod='lgproductmove'}
        </div>
        <label style="width: 100%; text-align: left; padding-bottom: 1em;">
            {l s='2. Tick the products you want to move' mod='lgproductmove'} {l s='(column SELECTION in the table below)' mod='lgproductmove'}
        </label>
        <br>
        <label style="text-align: left;">{l s='3. Select the destination category (To)' mod='lgproductmove'}</label>
        <div class="margin-form">
            <select name="categoria_destino"{if (!Tools::getValue('categoria_origen'))} disabled{/if}>
                {foreach  item=categoria from=$categorias_to}
                    <option value="{$categoria['id_category']|intval}"{if $categoria['active'] == 0} style="color:grey;"{/if}{if $categoria['id_category'] == $categoria_destino} selected{/if}>{for $i=0 to $categoria['level_depth']}&nbsp;&nbsp;{/for}{$categoria['name']|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
        <label for="categoria2option" style="text-align: left;">{l s='4. Choose to' mod='lgproductmove'}:</label>
        <div class="margin-form">
            <select name="accion" class="lginput"{if (!Tools::getValue('categoria_origen'))} disabled{/if}>
                <option value="0">{l s='Select an action' mod='lgproductmove'}</option>
                <option value="1">{l s='Move the selected products to the destination category' mod='lgproductmove'}</option>
                <option value="2">{l s='Display the selected products in both categories' mod='lgproductmove'}</option>
            </select>
        </div>
        <div id="divchekcopycat" class="margin-form">
            <input type="checkbox" name="copycatlikedefault"  id="copycatlikedefault"{if (!Tools::getValue('categoria_origen'))} disabled{/if}>
            <span>
                {l s='Mark this option if you want the destination category' mod='lgproductmove'}
                &nbsp;
                {l s='to be the default category of the selected product(s)' mod='lgproductmove'}
            </span>
        </div>
        <div class="margin-form">
            <input type="submit" id="lgmoveproductssubmit" name="moveproducts" value="{l s='Confirm' mod='lgproductmove'}" class="button btn btn-default"{if (!Tools::getValue('categoria_origen'))} disabled{/if}>
        </div>
    </fieldset>
    {include './product_list_15.tpl'}
</form>
