{*
* 2007-2024 PrestaShop
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
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2024 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
var mod_ajax_url = "{$mod_ajax_url|escape:'htmlall':'UTF-8'}";
var invalid_category = "{l s='Please select a default category first.' mod='accessorycheckbox'}";
var conf_dlt_cat = "{l s='Are you sure you want to delete this default category and accessories products?' mod='accessorycheckbox'}";
var conf_dlt_pro = "{l s='Are you sure you want to delete this accessories product?' mod='accessorycheckbox'}";
$(document).ready(function() {

$("#addCatRow").click(function(e) {
	$("#accessories_module div.form-wrapper").append($("#category_row").html());
});
$('body').on('focusout','input[name=search_products]', function(e){
	setTimeout(function(){ $('ul.search_p').hide(); }, 300);
});
$('body').on('click', 'ul.search_p li',function() {
	    var id_product = $(this).attr('id');
		var id_cat = $(this).parents('div.form-group:first').find('select.accessory_categories').children("option").filter(":selected").val();
		var name = $(this).text();
		var product = '<li class="media"><div class="media-body"><b>'+name+'</b><input type="hidden" name="accessories['+id_cat+'][]" value="'+id_product+'"></div></li>';
		$(this).parents('div.autocomplete-search:first').find('ul.product-list').append(product);
		$(this).remove();
		$('ul.search_p').hide();
	});

$('body').on('focusin','input[name=search_products]', function(){
	   var search_exists = $(this).parents('div:first').find('ul.search_p');
	   if(search_exists.length==0)
	   $('ul.search_p').remove();
	   else
	   $('ul.search_p').show();
});


$('body').on('keyup','input[name=search_products]', function(){
	var id_cat = $(this).parents('div.form-group:first').find('select.accessory_categories').children("option").filter(":selected").val();
	if(id_cat ==0) {
		alert(invalid_category);
	  return;
	}
	$('ul.search_p').remove();
	var this_search_pro = $(this);
	var select_li = $(this).parents('div.autocomplete-search:first').find('ul.product-list li');
	var products = '';
	if($(select_li).length) {
	  $(select_li).each(function(index, element) {
		  var id_pro = $(element).children('div.media-body:first').find('input').filter(":first").val();
		  products = products.concat(id_pro+'-');
	  });
	}
	if($(this).val().length>=3){
		$('#processing').show();
	         $.ajax({
								type: 'POST',
								url: mod_ajax_url,
								async: false,
								cache: false,
								dataType : "json",
								data: 'id_lang={$id_lang|intval}&hide_current=true&id_shop={$id_shop|intval}&products='+products+'&action=search&q=' + this.value + '&AccessoriesToken={$AccessoriesToken|escape:"html":"UTF-8"}',
								success: function(jsonData,textStatus,jqXHR)
								{
									if(jsonData.status=='ok'){
									  this_search_pro.after("<ul class='search_p'>"+jsonData.result+"</ul>");
									}else
									alert(jsonData.result);
									$('#processing').hide();
								}
					});
	}
});

$('body').on('change','select.accessory_categories', function(e) {
   var sel_value = $(this).children("option").filter(':selected').val();
   $(this).attr('name','accessory_categories['+sel_value+']');
   if(sel_value>0)
   $(this).css('border-color','#C7D6DB');
});

$("select.accessory_categories").each(function(index, element) {
     var sel_value = $(this).children("option").filter(':selected').val();
     $(this).attr('name','accessory_categories['+sel_value+']');
});

$('body').on('submit','#FormAccessories',function(event) {
	var ret_val = true;
	    $(this).find('select.accessory_categories').each(function(index, element) {
        if($(element).is(":visible") && $(element).children("option").filter(':selected').val()==0) {
			alert(invalid_category);
		  $(this).css('border-color','red');
		  $(this).focus();
		  ret_val = false;
		}
    });
	if(!ret_val) {
	  event.preventDefault();
      event.stopPropagation();
	  return false;
	}
});

$('body').on('click','button.deleteCatRowTemp',function(event) {

	if(confirm(conf_dlt_cat)) {
       $(this).parents('div.form-group:first').remove();
    }
});

$('body').on('click','div.media-body span.delete',function(event) {

	if(confirm(conf_dlt_pro)) {
       $(this).parents('li.media:first').remove();
    }
});


});
</script>
<link href="{$stripeBOCssUrl|escape:'htmlall':'UTF-8'}" rel="stylesheet" type="text/css">
{if $success}<div class="conf confirmation alert alert-success">{l s='Settings successfully saved' mod='accessorycheckbox'}</div>{/if}

<form action="" method="post" class="form-horizontal" id="FormAccessories">

<div class="row">
   <div class="col-lg-12">
     <div class="panel">

			 <div class="form-group">
			 <label class="control-label col-lg-6" for="simple_product">{l s='Enable Related Products as accessory' mod='accessorycheckbox'}:</label>
			 <div class="col-lg-6">
				 <span class="switch prestashop-switch fixed-width-lg">
						 <input type="radio" name="related_accessories" id="related_accessories_0" value="0" {if !Configuration::get('related_accessories')}checked="checked"{/if}>
						 <label for="related_accessories_0">{l s='Yes' mod='accessorycheckbox'}</label>
						 <input type="radio" name="related_accessories" id="related_accessories_1" value="1" {if Configuration::get('related_accessories')}checked="checked"{/if}>
						 <label for="related_accessories_1">{l s='No' mod='accessorycheckbox'}</label>
						 <a class="slide-button btn"></a>
				 </span>
			 </div></div>

			 <div class="form-group">
			 <label class="control-label col-lg-6" for="simple_product">{l s='Show accessory products for' mod='accessorycheckbox'}:</label>
			 <div class="col-lg-6">
				 <span class="switch prestashop-switch">
						 <input type="radio" name="category_products_acc" id="category_products_acc_0" value="0" {if !Configuration::get('category_products_acc')}checked="checked"{/if}>
						 <label for="category_products_acc_0">{l s='Default Category only' mod='accessorycheckbox'}</label>
						 <input type="radio" name="category_products_acc" id="category_products_acc_1" value="1" {if Configuration::get('category_products_acc')}checked="checked"{/if}>
						 <label for="category_products_acc_1">{l s='All Sub Categories' mod='accessorycheckbox'}</label>
						 <a class="slide-button btn"></a>
				 </span>
			 </div></div>

			</div>
			</div>
			</div>

<div class="row" id="accessories_module">
   <div class="col-lg-12">
     <div class="panel"><div class="panel-heading">{l s='+Add accessories for category products' mod='accessorycheckbox'}
         <img id="processing" style="display:none;position: absolute;right:5px; top:2px;" src="../img/loader.gif"></div>
		<div class="form-wrapper">

     {if count($accessories)==0}
          <div class="form-group">
           <div class="col-lg-1"> &nbsp;</div>
          <label class="control-label col-lg-2" for="simple_product">{l s='Choose Category' mod='accessorycheckbox'}:</label>
          <div class="col-lg-2">
          <select name="accessory_categories[0]" class="accessory_categories" style="width:200px;">
          <option value="0">{l s='Please select...' mod='accessorycheckbox'}</option>
          {foreach $categories as $cat}
               <option value="{$cat.id_category|escape:'html':'UTF-8'}" {if isset($accessory.category) && $cat.id_category==$accessory.category}selected="selected"{/if}>{$cat.name|escape:'html':'UTF-8'}</option>
          {/foreach}
          </select>
          </div>

          <label class="control-label col-lg-2" for="simple_product">{l s='Accessory products' mod='accessorycheckbox'}:</label>
          <div class="col-lg-5 autocomplete-search">
                  <input type="text" name="search_products" placeholder="search and add accessory product..." value=""  autocomplete="off" />
                   <ul id="accessories-data" class="product-list">
                   </ul>
          </div>
          </div>
     {/if}

      <div id="category_row" style="display:none;">
          <div class="form-group">
          <div class="col-lg-1">
        <button type="button" class="deleteCatRowTemp btn btn-primary" title="Remove this row.">{l s='X Delete' mod='accessorycheckbox'}</button>
        </div>
          <label class="control-label col-lg-2" for="simple_product">{l s='Choose Category' mod='accessorycheckbox'}:</label>
          <div class="col-lg-2">
          <select name="accessory_categories[0]" style="width:200px;" class="accessory_categories">
          <option value="0">{l s='Please select...' mod='accessorycheckbox'}</option>
          {foreach $categories as $cat}
               <option value="{$cat.id_category|escape:'html':'UTF-8'}" {if isset($accessory.category) && $cat.id_category==$accessory.category}selected="selected"{/if}>{$cat.name|escape:'html':'UTF-8'}</option>
          {/foreach}
          </select>
          </div>

          <label class="control-label col-lg-2" for="simple_product">{l s='Accessory products' mod='accessorycheckbox'}:</label>
          <div class="col-lg-5 autocomplete-search">
                  <input type="text" name="search_products" placeholder="search and add accessory product..." value=""  autocomplete="off" />
                  <ul id="accessories-data" class="product-list">
                   </ul>
          </div>
          </div>
        </div>

     {foreach $accessories as $id_cat=>$acc_cat}
        <div class="form-group">
        <div class="col-lg-1">
        {if $acc_cat@iteration != 1}
        <button type="button" class="deleteCatRowTemp btn btn-primary" title="Remove this row.">{l s='X Delete' mod='accessorycheckbox'}</button>
        {/if}
        </div>
        <label class="control-label col-lg-2" for="simple_product">{l s='Choose Category' mod='accessorycheckbox'}:</label>
        <div class="col-lg-2">
        <select name="accessory_categories[{$cat.id_category|escape:'html':'UTF-8'}]" class="accessory_categories" style="width:200px;" title="Please remove all accessory products to change this default product category" readonly>
        <option value="0">{l s='Please select...' mod='accessorycheckbox'}</option>
        {foreach $categories as $cat}
             <option value="{$cat.id_category|escape:'html':'UTF-8'}" {if $cat.id_category==$id_cat}selected="selected"{/if}>{$cat.name|escape:'html':'UTF-8'}</option>
        {/foreach}
        </select>
        </div>

        <label class="control-label col-lg-2" for="simple_product">{l s='Accessory products' mod='accessorycheckbox'}:</label>
        <div class="col-lg-5 autocomplete-search">
                <input type="text" name="search_products" placeholder="search and add accessory product..." value=""  autocomplete="off" />
                <ul id="accessories{$id_cat|escape:'html':'UTF-8'}-data" class="product-list">
                  {foreach $acc_cat as $acc}
                     <li class="media">
                        <div class="media-body">
                        <b>({$acc.id_product|escape:'html':'UTF-8'}) - {$acc.name|escape:'html':'UTF-8'}</b><span class="delete" title="Remove this item.">X</span>
                      <input type="hidden" name="accessories[{$id_cat|escape:'html':'UTF-8'}][]" value="{$acc.id_product|escape:'html':'UTF-8'}">
                      </div>
                    </li>
                    {/foreach}
                 </ul>
        </div>
        </div>
     {/foreach}


        </div>

        <div class="row">
       <div class="col-sm-12 col-md-12 col-lg-12" style="text-align:center">
          <button type="button" name="addCatRow" id="addCatRow" class="btn btn-primary">{l s='+ Add category row' mod='accessorycheckbox'}</button>
       </div>
       </div>

      <div class="panel-footer">
          <button type="submit" name="SubmitAccessories" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='accessorycheckbox'}</button>
          </div>
      </div>

    </div>
</div>
  </form>
