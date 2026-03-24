{if (isset($ndkcsfields) && $ndkcsfields|@count > 0) || ($fieldsItems)}
<script type="text/javascript">
	var is_popup_mode = true;
</script>
<button type="button" class="btn-primary btn-ndkacf-popup" data-toggle="modal" data-target="#ndkacf-modal">
	{if $edit_config > 0}
	  {l s='Edit your customization' mod='ndk_advanced_custom_fields'}
	  {else}
	  {l s='Customize' mod='ndk_advanced_custom_fields'}
	  {/if}
</button>
<div class="alert alert-warning popup_required hidden" role="alert">
	  {l s='Please fill option form by clicking here' mod='ndk_advanced_custom_fields'}
</div>

	
	<!-- Modal -->
	<div class="modal fade" id="ndkacf-modal" tabindex="-1" role="dialog" aria-labelledby="ndkacf-modal" aria-hidden="true">
		{* <div class="title_popup">
			<h5 class="modal-title">
				{if $edit_config > 0}
					{l s='Edit your customization' mod='ndk_advanced_custom_fields'}
					{else}
					{l s='Customize' mod='ndk_advanced_custom_fields'}
				{/if}
			</h5>
			
		</div> *} 
	
	  <div class="modal-dialog full-width" role="document">
				
		  <div id="ndkacf_modal_body" class="modal-body clearfix">
				<div id="custom-block-popup" class="ndkacf-options animated opened">	
				<div id="ndkcf_mobile_options_toggler" title="{l s='Toggle menu' mod='ndk_advanced_custom_fields'}">
					<span class="material-icons">tune</span>
				</div>
					<h4 class="ndkacf-popup-selection">{l s='Your selection : '  mod='ndk_advanced_custom_fields'} <span id="popup_product_name">{$product.name}</span></h4>
					{include file='module:ndk_advanced_custom_fields/views/templates/hook/ndkcf_uncached.tpl'}
				</div>	
				<div id="imgs-bloc-popup" class="ndk-imgs-popup">
					<div class="ndk-imgs-autoHeight">
						<div id="image-block" class="ndk-product-cover ndkacf-imgs">
							{if $ps_version > 1.6}
								{if $product.cover}
								{if $ndkccpwebp}
								{capture name='img_datas'}{/capture}
									{include file="module:ndk_custom_product_page/views/templates/hook/image-display-product.tpl" product=$product image_class='ndk-js-qv-product-cover' image_size='large_default' image_datas='style="width:100%;"' image_id='bigpic'}
								{else}
									<img id="bigpic" class="ndk-js-qv-product-cover" src="{$product.cover.bySize.large_default.url}" alt="{$product.cover.legend}" title="{$product.cover.legend}" style="width:100%;" itemprop="image">
								{/if}
								  
								  {* <div class="layer hidden-sm-down" data-toggle="modal" data-target="#product-modal">
									<i class="material-icons zoom-in">&#xE8FF;</i>
								  </div> *}
								{else}
								  <img src="{$urls.no_picture_image.bySize.large_default.url}" style="width:100%;">
								{/if}
							{else}
								<img id="bigpic" itemprop="image" src="{$link->getImageLink($product.link_rewrite, $cover.id_image, 'thickbox_default')|escape:'html'}" title="{if !empty($cover.legend)}{$cover.legend|escape:'html'}{else}{$product.name|escape:'html'}{/if}" alt="{if !empty($cover.legend)}{$cover.legend|escape:'html'}{else}{$product.name|escape:'html'}{/if}" width="{$largeSize.width}" height="{$largeSize.height}"/>
							{/if}
						</div>
					</div>	
				</div>
						
		  </div>
	  </div>
	  <div class="close-popup">
		  <button type="button" class="close  ndkacf-close-modal" data-dismiss="modal" aria-label="Close">
			  <span class="btn btn-primary" aria-hidden="true">&times;</span>
		  </button>
	  </div>
	  <div class="sticky-responsive header-pc clear clearfix">  
		  <div class="col-md-4 col-xs-8 pull-left product-actions">
		  <button type="button" id="popup-add-to-cart" class="btn btn-primary full-width add-to-cart falseButton"><span class="material-icons">shopping_cart</span>{l s='Add to cart' mod='ndk_advanced_custom_fields'}</button>
		  </div>
	  </div>
	</div>
{/if}
