{*
	*  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
	*
	* @author    Línea Gráfica E.C.E. S.L.
	* @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
	* @license   https://www.lineagrafica.es/licenses/license_en.pdf https://www.lineagrafica.es/licenses/license_es.pdf https://www.lineagrafica.es/licenses/license_fr.pdf
*}
{if !empty($productos)}
    {foreach item=producto from=$productos}
        <tr id="{$producto.info.id_product|intval}">
            <td>
                <input type="checkbox" name="selected_products[]" value="{$producto.info.id_product|intval}">
            </td>
            <td>
                <span id="id{$producto.info.id_product|intval}">{$producto.info.id_product|intval}</span>
            </td>
            <td>{if isset($producto.imagen)}<img src="{$producto.imagen|escape:'htmlall':'UTF-8'}">{else}IMAGEN NO EXISTE:{/if}</td>
            <td>
                <span id="name{$producto.info.id_product|intval}">{$producto.nombre|escape:'htmlall':'UTF-8'}</span>
            </td>
            <td>
                <span id="reference{$producto.info.id_product|intval}">{$producto.info.reference|escape:'htmlall':'UTF-8'}</span>
            </td>
            <td>
            <span id="name'.$producto['id_product'].'">
                <input
                        type="hidden"
                        name="manufacturer{$producto.info.id_product|intval}"
                        id="manufacturer{$producto.info.id_product|intval}"
                        value="{$producto.producto.id_manufacturer|escape:'htmlall':'UTF-8'}">
                {$producto.producto.manufacturer|escape:'htmlall':'UTF-8'}
            </span>
            </td>
            <td>
            <span id="date{$producto.info.id_product|intval}">
                {$producto.producto.fecha|escape:'htmlall':'UTF-8'}
            </span>
            </td>
            <td>
            <span id="price{$producto.info.id_product|intval}">
                {$producto.producto.precio|escape:'htmlall':'UTF-8'}
            </span>
            </td>
            <td>
                <span id="stock{$producto.info.id_product|intval}">
                {$producto.quantity|escape:'htmlall':'UTF-8'}
                </span>
            </td>
            <td>
                <input
                        type="hidden"
                        name="status{$producto.info.id_product|intval}"
                        id="status{$producto.info.id_product|intval}}" '
                value="{$producto.prodActive|intval}">
                <img src="{$producto.imgProdActive|escape:'htmlall':'UTF-8'}">
            </td>
            <td>
                <input
                        type="hidden"
                        type="text"
                        name="prod{$producto.info.id_product|intval}"
                        value="{$producto.position|intval}">
                {$producto.position|intval}
            </td>
        </tr>
    {/foreach}
{/if}
