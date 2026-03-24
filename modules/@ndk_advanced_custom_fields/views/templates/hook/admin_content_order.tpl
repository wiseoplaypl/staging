<div id="ndkTabContent" class="adminContentOrderNdk">
	<table class="table" id="orderProductsTableNdk">
		<thead>
			<tr>
				<th>
					<p>{l s='Product' mod="ndk_advanced_custom_fields"}</p>
				</th>
				<th>
					<p>{l s='Personalization' mod="ndk_advanced_custom_fields"}</p>
				</th>
				<th>
					<p>{l s='Export' mod="ndk_advanced_custom_fields"}</p>
				</th>
			</tr>
		</thead>
		<tbody>
			{foreach $products as $product}
			{if $product['customizedDatas']}
			{foreach $product["customizedDatas"] as $a}
			{foreach $a as $customizationId => $b}
			<tr>
				<td>{$product["product_name"]}</td>
				<td>
					{foreach $b as $c}
					{foreach $c as $d}
					{foreach $d as $customization}
					{if $customization["value"]}
					<span><strong>{$customization["name"]}</strong>
						<p>{$customization["value"]}</p>
					</span>
					{/if}
					{/foreach}
					{/foreach}
					{/foreach}
				</td>

				<td class="action">


					{capture name="htmlFileNoCustomer"}{$smarty.const._PS_IMG_DIR_}scenes/ndkcf/pdf/0/{$product['product_id']|intval}/{$customizationId|intval}/render.html{/capture}

					{capture name="htmlFile"}{$smarty.const._PS_IMG_DIR_}scenes/ndkcf/pdf/{$order->id_customer|intval}/{$product['product_id']|intval}/{$customizationId|intval}/render.html{/capture}

					{capture name="recipientPdf"}{$smarty.const._PS_IMG_DIR_}scenes/ndkcf/pdf/{$order->id_customer|intval}/{$product['product_id']|intval}_WEB{$order->reference}.pdf{/capture}
					{capture name="recipientPdf_new"}{$smarty.const._PS_IMG_DIR_}scenes/ndkcf/pdf/{$order->id_customer|intval}/{$product['product_id']|intval}_{$customizationId}_WEB{$order->reference}.pdf{/capture}

					{if Tools::file_exists_no_cache($smarty.capture.htmlFile)}

					{capture name="htmlLink"}{$urls.base_url}/img/scenes/ndkcf/pdf/{$order->id_customer|intval}/{$product['product_id']|intval}/{$customizationId|intval}/render.html{/capture}

					{elseif Tools::file_exists_no_cache($smarty.capture.htmlFileNoCustomer)}

					{capture name="htmlLink"}{$urls.base_url}/img/scenes/ndkcf/pdf/0/{$product['product_id']|intval}/{$customizationId|intval}/render.html{/capture}

					{else}
					{capture name="htmlLink"}{/capture}
					{/if}

					{if Tools::file_exists_no_cache($smarty.capture.recipientPdf)}
					{capture name="recipientLink"}{$urls.base_url}/img/scenes/ndkcf/pdf/{$order->id_customer|intval}/{$product['product_id']|intval}_WEB{$order->reference}.pdf{/capture}
					{elseif Tools::file_exists_no_cache($smarty.capture.recipientPdf_new)}
					{capture name="recipientLink"}{$urls.base_url}/img/scenes/ndkcf/pdf/{$order->id_customer|intval}/{$product['product_id']|intval}_{$customizationId}_WEB{$order->reference}.pdf{/capture}

					{else}
					{capture name="recipientLink"}{/capture}
					{/if}

					{if $smarty.capture.htmlLink !=''}
					<a class="btn btn-primary pull-left" target='_blank' href="{$smarty.capture.htmlLink}"><i class="icon-print"></i>&nbsp;{l s='Print Customization'}</a>
					{/if}
					{if $smarty.capture.recipientLink !=''}
					<a class="btn btn-primary pull-left" target='_blank' href="{$smarty.capture.recipientLink}"><i class="icon-print"></i>&nbsp;{l s='Print recipient pdf'}</a>
					{/if}
				</td>
			</tr>
			{/foreach}
			{/foreach}
			{/if}
			{/foreach}
		</tbody>
	</table>
</div>