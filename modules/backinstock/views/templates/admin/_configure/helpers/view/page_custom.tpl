<div class="panel col-lg1" id='list_graph'>
    <div class='panel col-lg1'>
    <h3 class="heading" >{l s='Graph' mod='backinstock'}</h3>
    <div id="graph_loader">
        <div id="flot-placeholder" style="width:100%;height:100%;"></div>
    </div>
    </div>
    <div class='panel col-lg1'>
    <h3 class="heading" >{l s='Subscribed Customer' mod='backinstock'}</h3>
    <div class="table-responsive clearfix">
        <table id="combinations-list" class="table  configuration">
			<thead>
				<tr class="nodrag nodrop">
					<th class=" left">
						<span class="title_box ">{l s='S.No.' mod='backinstock'}</span>
					</th>
					<th class=" left">
						<span class="title_box ">{l s='Product' mod='backinstock'}</span>
					</th>
                    <th class=" left">
						<span class="title_box ">{l s='SKU' mod='backinstock'}</span>
					</th>
                    <th class=" left">
						<span class="title_box ">{l s='Current Price' mod='backinstock'}</span>
					</th>
                    <th class=" left">
						<span class="title_box ">{l s='No. of Customers' mod='backinstock'}</span>
					</th>
									</tr>
						</thead>
<tbody>
    {if empty($present)}
        <tr><td class="left">{l s='No results to display' mod='backinstock'}</td><td></td></tr>	
	{/if}
     {$i=1}
    {foreach $present as $product}
        <tr {if $i is even}class="price-alert-tab-odd"{/if}>
            <td>{$i|escape:'htmlall':'UTF-8'}</td>
            <td>{$product['name']|escape:'htmlall':'UTF-8'}<br><label style="font-size: 11px; font-weight: normal;">{$product['attributes']|escape:'htmlall':'UTF-8'}</label></td>
            <td>{$product['model']|escape:'htmlall':'UTF-8'}</td>
            <td>{$product['current_price']|escape:'htmlall':'UTF-8'}</td>
            <td>{*<a  class='popup_users'  data='{$product['product_attribute_id']|escape:'htmlall':'UTF-8'}'>*}{$product['count']|escape:'htmlall':'UTF-8'}{*</a>*}</td>
        </tr>
        {$i=$i+1}
    {/foreach}
    
</tbody>

	</table>
    </div>
</div>
</div>
{*
    * DISCLAIMER
    *
    * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
    * versions in the future. If you wish to customize PrestaShop for your
    * needs please refer tohttp://www.prestashop.com for more information.
    * We offer the best and most useful modules PrestaShop and modifications for your online store.
    *
    * @category  PrestaShop Module
    * @author    knowband.com <support@knowband.com>
    * @copyright 2015 Knowband
    * @license   see file: LICENSE.txt
    *
    * Description
    *
    * Admin tpl file
    *}

