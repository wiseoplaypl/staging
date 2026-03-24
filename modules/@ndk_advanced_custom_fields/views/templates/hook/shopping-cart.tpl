{if $ps_version > 1.6}
	{assign var="base_dir_ssl" value=$urls.base_url}
	{assign var="base_dir" value=$urls.base_url}
	<input type="hidden" id="idCombination"/>
{/if}

{foreach $result as $row}

<div class="clear clearfix ndk-rowcustomization" id="ndkacfproduct_{$row.id_product}_{$row.id_product_attribute}_{$row.customizationId}_{$row.id_address_delivery|intval}">
	{if $row.link_edit !=''}
		<a class="btn btn-primary btn-default longbutton" href="{$row.link_edit}"><i class="material-icons">tune</i>{l s='Edit customization' mod='ndk_advanced_custom_fields'}</a>
	{/if}
	{capture name="htmlFileNoCustomer"}{$smarty.const._PS_IMG_DIR_}scenes/ndkcf/pdf/0/{$row.id_product|intval}/{$row.customizationId|intval}/render.html{/capture}
	
	{capture name="htmlFile"}{$smarty.const._PS_IMG_DIR_}scenes/ndkcf/pdf/{Context::getContext()->customer->id|intval}/{$row.id_product|intval}/{$row.customizationId|intval}/render.html{/capture}
	
	{if Tools::file_exists_no_cache($smarty.capture.htmlFile)}
	
		{capture name="htmlLink"}{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}/img/scenes/ndkcf/pdf/{Context::getContext()->customer->id|intval}/{$row.id_product|intval}/{$row.customizationId|intval}/render.html{/capture}
		
	{elseif Tools::file_exists_no_cache($smarty.capture.htmlFileNoCustomer)}
		
		{capture name="htmlLink"}{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}/img/scenes/ndkcf/pdf/0/{$row.id_product|intval}/{$row.customizationId|intval}/render.html{/capture}
	
	{else}
		{capture name="htmlLink"}{/capture}
	{/if}
	
	{if $smarty.capture.htmlLink !=''}
		<a class="btn btn-primary btn-default fancyboxButton" target="_blank" style="text-decoration:none" href="{$smarty.capture.htmlLink}"><i class="material-icons">preview</i>&nbsp;{l s='Preview' mod='ndk_advanced_custom_fields'}</a>
	{/if}
</div>

{/foreach}

{if $ps_version < 1.7}
	<script type="text/javascript">
		$(document).ready(function(){
			
				$('.rowcustomization').each(function(){
					newDiv = $(this).clone();
					target = $(this).attr('id').replace('ndkacf', '');
					$('#'+target).find('td').last().append(newDiv);
					
					$(this).remove();
				});
		});
	</script>
{/if}