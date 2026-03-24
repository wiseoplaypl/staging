{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    FMM Modules
*  @copyright 2018 FME Modules
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<form class="form-horizontal" enctype="multipart/form-data" method="post" id="configuration_form" action="{$action|escape:'htmlall':'UTF-8'}">
	{if $id > 0}<input type="hidden" value="{$id|escape:'htmlall':'UTF-8'}" name="id_seoaltimages" />{/if}
<div class="alert alert-info">
	<p>{l s='Please add this hook in <img> tag you already have in file' mod='seoaltimages'} {if $ps_version > 0}<strong>themes/YOUR_THEME/templates/catalog/_partials/product-cover-thumbnails.tpl and also in product-images-modal.tpl</strong>{l s=' by editing alt like alt="{hook h=\'displaySeoAltImages\'}"' mod='seoaltimages'}{else}<strong>themes/YOUR_THEME/product.tpl</strong>{l s=' by editing alt like alt="{hook h=\'displaySeoAltImages\'}"' mod='seoaltimages'}{/if}</p>
	<p><strong>{l s='{hook h=\'displaySeoAltImages\'}' mod='seoaltimages'}</strong></p>
</div>
<div class="panel">
	<div class="panel-heading"><i class="icon-cogs"></i> {l s='Settings' mod='seoaltimages'}</div>
	<div class="form-wrapper">
		<div class="form-group">
			<label class="control-label col-lg-4 required">{l s='Status' mod='seoaltimages'}</label>
			<div class="col-lg-8">
				<span class="switch prestashop-switch fixed-width-lg">
					<input id="control_seo_alt_0" name="control_seo_alt" value="0" type="radio"{if (!is_array($collection) && $collection->status <= 0) || is_array($collection)} checked="checked"{/if}><label for="control_seo_alt_0">{l s='Yes' mod='seoaltimages'}</label>
					<input id="control_seo_alt_1" name="control_seo_alt" value="1" type="radio"{if !is_array($collection) && $collection->status > 0} checked="checked"{/if}><label for="control_seo_alt_1">{l s='No' mod='seoaltimages'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-lg-4">{l s='Title' mod='seoaltimages'}</label>
			<div class="col-lg-8">
				<input type="text" name="seo_alt_title" placeholder="Alternate text title" value="{if !is_array($collection)}{$collection->title|escape:'htmlall':'UTF-8'}{/if}" />
				<small class="form-text fmm_exp_tags">{l s='Please enter title for Back office use only.' mod='seoaltimages'}</small>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-lg-4 required">{l s='Select Shop(s)' mod='seoaltimages'}</label>
			<div class="col-lg-8">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="fixed-width-xs"> </th>
							<th class="fixed-width-xs"><span class="title_box">ID</span></th>
							<th>
								<span class="title_box">
									{l s='Store name' mod='seoaltimages'}
								</span>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<input type="checkbox" value="0" id="groupBox_1" onclick="selectAllShops(this);" class="groupBox" name="shops[]">
							</td>
							<td>--</td>
							<td>
								<label for="groupBox_1">{l s='All' mod='seoaltimages'}</label>
							</td>
						</tr>
						{foreach from=$shops item=_item}
						<tr>
							<td>
								<input type="checkbox" value="{$_item.id_shop|escape:'htmlall':'UTF-8'}"{if !is_array($collection) && in_array($_item.id_shop, $collection->shops)} checked="checked"{/if} id="groupBox_2" class="groupBox sub_sp" name="shops[]">
							</td>
							<td>{$_item.id_shop|escape:'htmlall':'UTF-8'}</td>
							<td>
								<label for="groupBox_2">{$_item.name|escape:'htmlall':'UTF-8'}</label>
							</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-lg-4 required">{l s='Select Language(s)' mod='seoaltimages'}</label>
			<div class="col-lg-8">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="fixed-width-xs"> </th>
							<th class="fixed-width-xs"><span class="title_box">ID</span></th>
							<th>
								<span class="title_box">
									{l s='Language' mod='seoaltimages'}
								</span>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<input type="checkbox" value="0" id="groupBox_1" onclick="selectAllLangs(this);" class="groupBox" name="langs[]">
							</td>
							<td>--</td>
							<td>
								<label for="groupBox_1">{l s='All' mod='seoaltimages'}</label>
							</td>
						</tr>
						{foreach from=$languages item=_item}
						<tr>
							<td>
								<input type="checkbox" value="{$_item.id_lang|escape:'htmlall':'UTF-8'}"{if !is_array($collection) && in_array($_item.id_lang, $collection->languages)} checked="checked"{/if} id="groupBox_2" class="groupBox sub_sp_lng" name="langs[]">
							</td>
							<td>{$_item.id_lang|escape:'htmlall':'UTF-8'}</td>
							<td>
								<label for="groupBox_2">{$_item.name|escape:'htmlall':'UTF-8'}</label>
							</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-lg-4 required">{l s='Alt Rule' mod='seoaltimages'}</label>
			<div class="col-lg-8">
				<input type="text" name="seo_alt_value" placeholder="productname-productdesc" value="{if !is_array($collection) && $collection->rule}{$collection->rule|escape:'htmlall':'UTF-8'}{/if}" />
				<p style="clear:both"><a class="fmm_dropme" onclick="appendThisTag(this);" title="productname">{l s='Product Name' mod='seoaltimages'}</a><br/>
					<a class="fmm_dropme" onclick="appendThisTag(this);" title="productcategory">{l s='Product Category' mod='seoaltimages'}</a><br/>
					<a class="fmm_dropme" onclick="appendThisTag(this);" title="productshortdesc">{l s='Product Short Description' mod='seoaltimages'}</a><br/>
					<a class="fmm_dropme" onclick="appendThisTag(this);" title="productreference">{l s='Product reference' mod='seoaltimages'}</a><br/>
					<a class="fmm_dropme" onclick="appendThisTag(this);" title="productmanufacturer">{l s='Product Manufacturer' mod='seoaltimages'}</a><br/>
					<a class="fmm_dropme" onclick="appendThisTag(this);" title="productretailpricewithtax">{l s='Product retail price with tax' mod='seoaltimages'}</a><br/>
					<a class="fmm_dropme" onclick="appendThisTag(this);" title="productretailpricewithouttax">{l s='Product retail price withOut tax' mod='seoaltimages'}</a><br/>
					<a class="fmm_dropme" onclick="appendThisTag(this);" title="productspecificpricewithtax">{l s='Product specific price with tax' mod='seoaltimages'}</a><br/>
					<a class="fmm_dropme" onclick="appendThisTag(this);" title="productspecificpricewithouttax">{l s='Product specific price withOut tax' mod='seoaltimages'}</a><br/>
					<a class="fmm_dropme" onclick="appendThisTag(this);" title="productreduction">{l s='Product reduction' mod='seoaltimages'}</a><br/>
					<a class="fmm_dropme" onclick="appendThisTag(this);" title="shoptitle">{l s='Shop Title' mod='seoaltimages'}</a>
				</p>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-lg-4 required">{l s='Apply On' mod='seoaltimages'}</label>
			<div class="col-lg-8">
				<p class="radio">
					<label for="seo_alt_type_0"><input type="radio" name="seo_alt_type" id="seo_alt_type_0" value="0" onclick="seoAltChoice(0);"{if !is_array($collection) && $collection->type <= 0} checked="checked"{/if}>{l s='All Products' mod='seoaltimages'}</label>
				</p>
				<p class="radio">
					<label for="seo_alt_type_1"><input type="radio" name="seo_alt_type" id="seo_alt_type_1" value="1" onclick="seoAltChoice(1);"{if !is_array($collection) && $collection->type && $collection->type == 1} checked="checked"{/if}>{l s='Selected Categories' mod='seoaltimages'}</label>
				</p>
				<p class="radio">
					<label for="seo_alt_type_2"><input type="radio" name="seo_alt_type" id="seo_alt_type_2" value="2" onclick="seoAltChoice(2);"{if !is_array($collection) && $collection->type && $collection->type == 2} checked="checked"{/if}>{l s='Selected Products' mod='seoaltimages'}</label>
				</p>
				<small class="form-text fmm_exp_tags">{l s='The rule will be applied on above choice.' mod='seoaltimages'}</small>
			</div>
		</div>
		
		<div class="form-group" id="seo_alt_category"{if !is_array($collection) && $collection->type && $collection->type == 1} style="display: block"{else} style="display: none"{/if}>
			<label class="control-label col-lg-4">{l s='Select Categories' mod='seoaltimages'}</label>
			<div class="col-lg-8">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th> </th>
							<th>
								<span class="title_box">
									{l s='ID' mod='seoaltimages'}
								</span>
							</th>
							<th>
								<span class="title_box">
									{l s='Name' mod='seoaltimages'}
								</span>
							</th>
						</tr>
					</thead>
					<tbody>
						{if !isset($categories) || empty($categories)}
						<tr>
							<td>{l s='No categories found.' mod='seoaltimages'}</td>
						</tr>
						{else}
						{foreach from=$categories item=category}
							<tr>
							<td>
								<input type="checkbox" name="category[]" value="{$category.id_category}"{if !is_array($collection) && in_array($category.id_category, $collection->categories)} checked="checked"{/if} />
							</td>
							<td>
								{$category.id_category}
							</td>
							<td>
								{$category.name}
							</td>
							</tr>
						{/foreach}
						{/if}
					</tbody>
				</table>
			</div>
		</div>
		
		<div class="form-group" id="seo_alt_product"{if !is_array($collection) && $collection->type && $collection->type == 2} style="display: block"{else} style="display: none"{/if}>
			<label class="control-label col-lg-4">{l s='Select Product' mod='seoaltimages'}</label>
			<div class="col-lg-8 placeholder_holder">
				<input type="text" placeholder="Example: Blue XL shirt" onkeyup="getRelProducts(this);" />
				<div id="rel_holder"></div>
				<div id="rel_holder_temp">
					<ul>
						{if !is_array($collection) && !empty($collection->products) && $collection->type == 2}
						{foreach from=$collection->products item=product}
							<li id="row_{$product->id|escape:'htmlall':'UTF-8'}" class="media"><div class="media-left"><img src="{$link->getImageLink($product->link_rewrite, $product->id_image, 'home_default')|escape:'htmlall':'UTF-8'}" class="media-object image"></div><div class="media-body media-middle"><span class="label">{$product->name|escape:'htmlall':'UTF-8'}&nbsp;(ID:{$product->id|escape:'htmlall':'UTF-8'})</span><i onclick="relDropThis(this);" class="material-icons delete">clear</i></div><input type="hidden" value="{$product->id|escape:'htmlall':'UTF-8'}" name="related_products[]"></li>
						{/foreach}
						{/if}
					</ul>
				</div>
				<small class="form-text fmm_exp_tags">{l s='Please type product name and select it from drop down.' mod='seoaltimages'}</small>
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<button name="submitSeoAlt" class="btn btn-default pull-right" type="submit"><i class="process-icon-save"></i> {l s='Save' mod='seoaltimages'}</button>
	</div>
</div>
</form>
<script type="text/javascript">{literal}
var mod_url = "{/literal}{$action_url nofilter}{literal}";
	function appendThisTag(el) {
		var tag_product = $(el).attr("title");
		var input_current_value = $(el).parent().parent().find("input").val();
		$(el).parent().parent().find("input").val(input_current_value+tag_product);
	}
	function selectAllShops(g) {
		if (jQuery(g).is(":checked")) {
			jQuery('.sub_sp').attr('disabled','disabled');
		}
		else
		{
			jQuery('.sub_sp').removeAttr('disabled');
		}
	}
	function selectAllLangs(g) {
		if (jQuery(g).is(":checked")) {
			jQuery('.sub_sp_lng').attr('disabled','disabled');
		}
		else
		{
			jQuery('.sub_sp_lng').removeAttr('disabled');
		}
	}
	function seoAltChoice(v) {
		v = parseInt(v);
		console.log(v);
		if (v <= 0) {
			$('#seo_alt_category').hide();
			$('#seo_alt_product').hide();
		}
		else if (v > 1) {
			$('#seo_alt_product').show();
			$('#seo_alt_category').hide();
		}
		else {
			$('#seo_alt_category').show();
			$('#seo_alt_product').hide();
		}
	}
function getRelProducts(e) {
	var search_q_val = $(e).val();
	//controller_url = controller_url+'&q='+search_q_val;
	if (typeof search_q_val !== 'undefined' && search_q_val) {
		$.ajax({
			type: 'GET',
			dataType: 'json',
			url: mod_url + '&q=' + search_q_val,
			success: function(data)
			{
				var quicklink_list ='<li class="rel_breaker" onclick="relClearData();"><i class="material-icons">&#xE14C;</i></li>';
				$.each(data, function(index,value){
					if (typeof data[index]['id'] !== 'undefined')
						quicklink_list += '<li onclick="relSelectThis('+data[index]['id']+','+data[index]['id_product_attribute']+',\''+data[index]['name']+'\',\''+data[index]['image']+'\');"><img src="' + data[index]['image'] + '" width="60"> ' + data[index]['name'] + '</li>';
				});
				if (data.length == 0) {
					quicklink_list = '';
				}
				$('#rel_holder').html('<ul>'+quicklink_list+'</ul>');
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				console.log(textStatus);
			}
		});
	}
	else {
		$('#rel_holder').html('');
	}
}
function relSelectThis(id, ipa, name, img) {
	if ($('#row_' + id + '_' + ipa).length > 0) {
		showErrorMessage(error_msg);
	} else {
	  var draw_html = '<li id="row_' + id + '" class="media"><div class="media-left"><img src="'+img+'" class="media-object image"></div><div class="media-body media-middle"><span class="label">'+name+'&nbsp;(ID:'+id+')</span><i onclick="relDropThis(this);" class="material-icons delete">clear</i></div><input type="hidden" value="'+id+'" name="related_products[]"></li>'
	  $('#rel_holder_temp ul').append(draw_html);
	}
}
function relClearData() {
    $('#rel_holder').html('');
}
function relDropThis(e) {
    $(e).parent().parent().remove();
}
</script>
<style type="text/css">
.fmm_dropme { cursor: pointer;}
.fmm_exp_tags { clear: both;}
#rel_holder ul { position: absolute; left: 12px; border-radius: 4px; top: 40px; margin: 0px 0 20%; padding: 0; background: #fff;
border: 1px solid #BBCDD2; z-index: 999}
#rel_holder ul li { list-style: none; padding: 5px 10px; display: block; margin: 0px}
#rel_holder ul li:hover { cursor: pointer; background: #25B9D7}
#rel_holder ul li.rel_breaker { padding: 0px; margin: -1px -22px 0 0; background: #fff; float: right;border: 1px solid #BBCDD2;
 border-left: 0px; height: 24px;}
#rel_holder ul li.rel_breaker:hover { background: #fff;}
.rel_breaker i {font-size: 22px; color: #E50B70; cursor: pointer}
#rel_holder_temp { clear: both; padding: 10px 0}
#rel_holder_temp ul { padding: 0; margin: 0}
#rel_holder_temp ul li { list-style: none; padding: 3px 5px; border-radius: 5px; margin: 6px 0; border: 1px solid #E5E5E5;
display: block}
#rel_holder_temp ul li div { display: inline-block; vertical-align: middle}
#rel_holder_temp ul li .media-left { width: 8%}
#rel_holder_temp ul li .media-left img { max-width: 100%}
#rel_holder_temp ul li .media-body { width: 86%; margin-left: 5%}
#rel_holder_temp ul li .media-body span { float: left; font-size: 13px; color: #6c868e; font-weight: normal; white-space: normal !important;
text-align: left; width: 92%}
#rel_holder_temp ul li .media-body i { float: right; cursor: pointer}
.placeholder_holder { position: relative}
.ps_16_specific .material-icons {font-size: 1px;color: #fff;}
.ps_16_specific .material-icons::before {content: "\f00d"; font-family: "FontAwesome"; font-size: 25px;text-align: center;
color: red;font-style: normal; text-indent: -9999px; font-weight: normal; line-height: 20px;}
</style>
{/literal}