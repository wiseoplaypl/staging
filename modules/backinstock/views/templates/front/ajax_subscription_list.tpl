{if isset($subscribers) && count($subscribers) > 0}
    <section id="main_subscription" class="page-content card card-block">
        <h3 class="page-heading bottom-indent">{l s='Your Out of Stock Product Subscriptions - ' mod='backinstock'}</h3>
        </br>
        <div id="kb_subscription_list">
	<table class="table table-striped table-bordered table-labeled" id="order-list">
		<thead>
			<tr>
				<th class="first_item">{l s='S.No' mod='backinstock'}</th>
				<th class="item">{l s='Product' mod='backinstock'}</th>
				<th class="item">{l s='Product Name' mod='backinstock'}</th>
				<th class="item">{l s='Date' mod='backinstock'}</th>
                                <th class="item">{l s='Status' mod='backinstock'}</th>
				{if isset($remove_subscription_button) && $remove_subscription_button == 1}
                                    <th class="last_item">{l s='Action' mod='backinstock'}</th>
                                {/if}
			</tr>
		</thead>
		<tbody>
                {assign var=indexRow value=0}
                {foreach $subscribers as $subscribers_key => $subscribers_data}
                    {assign var=indexRow value=$indexRow+1}
                        <tr>
				<td>{$indexRow}</td>
				<td><a href="{$subscribers_data['product_link']}">
                                    <img src='{$subscribers_data['image_link']}' width='60'/>
                                    </a>
                                </td>
				<td><a href="{$subscribers_data['product_link']}">{$subscribers_data['product_name']}</a></td>
				<td>{$subscribers_data['date_added']}</td>
                                
                                <td>
                                {if isset($subscribers_data['quantity']) && $subscribers_data['quantity'] > 0}
                                        {l s='In Stock' mod='backinstock'}
                                {else}
                                    {l s='Out Of Stock' mod='backinstock'}
                                {/if}
                                </td>
                                
				{if isset($remove_subscription_button) && $remove_subscription_button == 1}
				<td>
                                    <a href="javascript:void(0)" title="{l s='Click to remove subscription' mod='backinstock'}" onclick="removeSubscription({$subscribers_data['id_subscription']})" >
                                    <span class="fa fa-trash"></span>
                                    {l s='Remove' mod='backinstock'}

                                    </a>
                                </td>
                                {/if}
			</tr>
                {/foreach}
				</tbody>
	</table>
	</div>
    </section>
    <section id="footer_subscription" class="page-content card card-block">
        <div class="svl_pagination">
            <div class="product-count" style="width:40%;text-align:left;">{l s='Showing' mod='backinstock'} {$start} - {$end } {l s='of' mod='backinstock'} {$total_subscription} {l s='Items' mod='backinstock'}</div>

            {if $total_pages > 1}
                <div class="bottom-pagination-content clearfix" style="margin-left: 40%;">

                    <!-- Pagination -->
                    <ul class="pagination">
                        {* for left most page *}
                        {if $total_pages > 1}

                            {if $kbpage neq 1}
                                <li>
                                    <a href='javascript:void(0)' onclick="getNextSubscriptionResultPage({1})">
                                        <span><<</span>
                                    </a>
                                </li>
                            {else}
                                <li class="active current">
                                    <span><span><<</span></span>
                                </li>
                            {/if}    
                        {/if}    
                        {* for 1 page previous to current page *}
                        {if $total_pages > 1}
                            {if $kbpage-1 > 0}

                                <li>
                                    <a href='javascript:void(0)' onclick="getNextSubscriptionResultPage({$kbpage-1})">
                                        <span><</span>
                                    </a>
                                <li>
                                {else}

                                    {if $kbpage neq 1}
                                    <li>
                                        <a href='javascript:void(0)' onclick="getNextSubscriptionResultPage({$kbpage})">
                                            <span><</span>
                                        </a>
                                    </li>
                                {else}
                                    <li class="active current">
                                        <span><span><</span></span>
                                    </li>
                                {/if}
                            {/if}    
                        {/if}    
                        {* if at last page then also show last-2 page *} 
                        {if $total_pages > 1}
                            {if $kbpage-2 >= 1 && $kbpage eq $total_pages}

                                <li>
                                    <a href='javascript:void(0)' onclick="getNextSubscriptionResultPage({$kbpage-2})">
                                        <span>{$kbpage-2}</span>
                                    </a>
                                </li>    
                            {/if}   
                        {/if}
                        {* for middle of pages *}
                        {for $count=1 to $total_pages}

                            {assign var=params value=[
                           
                            'page' => $count
                            ]}
                            {if $count eq $kbpage+1 || $count eq $kbpage-1 || $kbpage eq $count }
                                {if $kbpage ne $count}
                                    <li>
                                        <a href='javascript:void(0)' onclick="getNextSubscriptionResultPage({$count})">
                                            <span>{$count}</span>
                                        </a>
                                    </li>
                                {else}
                                    <li class="active current">
                                        <span><span>{$count}</span></span>
                                    </li>
                                {/if}
                            {/if}
                        {/for}
                        {* if  current page is 1 then also show 3rd page if exist *}
                        {if $total_pages > 1}
                            {if $kbpage+2 le $total_pages && $kbpage eq 1}

                                <li>
                                    <a href='javascript:void(0)' onclick="getNextSubscriptionResultPage({$kbpage+2})">
                                        <span>{$kbpage+2}</span>
                                    </a>
                                </li>    
                            {/if}   
                        {/if}
                        {* for next page *}
                        {if $total_pages > 1}
                            {if $kbpage+1 < $total_pages}

                                <li>
                                    <a href='javascript:void(0)' onclick="getNextSubscriptionResultPage({$kbpage+1})">
                                        <span>></span>
                                    </a>
                                </li>
                            {else}

                                {if $kbpage neq $total_pages}
                                    <li>
                                        <a href='javascript:void(0)' onclick="getNextSubscriptionResultPage({$total_pages})">
                                            <span>></span>
                                        </a>
                                    </li>
                                {else}
                                    <li class="active current">
                                        <span><span>></span></span>
                                    </li>
                                {/if}
                            {/if}

                            {* for last page i.e >> *}
                        {/if}    
                        {if $total_pages > 1}

                            {if $kbpage neq $total_pages}
                                <li>
                                    <a href='javascript:void(0)' onclick="getNextSubscriptionResultPage({$total_pages})">
                                        <span>>></span>
                                    </a>
                                </li>
                            {else}
                                <li class="active current">
                                    <span><span>>></span></span>
                                </li>
                            {/if}
                        {/if}
                    </ul>

                    <!-- /Pagination -->
                </div>
            {/if}
        </div>
    </section>
    {else}
        <section id="main_subscription" class="page-content card card-block">
            <h3 class="page-heading bottom-indent">{l s='No Subscriptions.' mod='backinstock'}</h3>
        </section>
    {/if}
    
{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    velsof.com <support@velsof.com>
* @copyright 2014 Velocity Software Solutions Pvt Ltd
* @license   see file: LICENSE.txt
*
* Description
*
* Price Alert Success Page
*}