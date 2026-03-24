{*
* Call for price
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @category  FMM Modules
* @package   HIDEPRICEANDCART
* @author    FMM Modules
* @copyright Copyright 2021 © FMM Modules
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{extends file="helpers/form/form.tpl"}
{block name="input"}
    {if $input.name == 'FMM_HIDEPRICEANDCART_RULES'} 
   
    <div class="form-group">
        <div class="col-lg-12" id="rule_category_list" >
            <label class="control-label col-lg-3" for="FMM_HIDEPRICEANDCART_RULES_LOGIN">
                <span class="label-tooltip" id="HIDEPRICEANDCART-groups" data-toggle="tooltip" title=" {l s='Customer is not login' mod='hidepriceandcart'}"> {l s='Customer is not login' mod='hidepriceandcart'}</span>
            </label>        
            <div class="col-lg-8">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input {if $login == 1}checked="checked" {/if}id="FMM_HIDEPRICEANDCART_RULES_LOGIN_on" name="FMM_HIDEPRICEANDCART_RULES_LOGIN" type="radio" value="1">
                        <label for="FMM_HIDEPRICEANDCART_RULES_LOGIN_on">
                            {l s='Yes' mod='hidepriceandcart'}
                        </label>
                        <input {if $login == 0}checked="checked" {/if} id="FMM_HIDEPRICEANDCART_RULES_LOGIN_off" name="FMM_HIDEPRICEANDCART_RULES_LOGIN" type="radio" value="0">
                            <label for="FMM_HIDEPRICEANDCART_RULES_LOGIN_off">
                                {l s='No' mod='hidepriceandcart'}
                            </label>
                            <a class="slide-button btn">
                            </a>
                        </input>
                    </input>
                </span>

                <p class="help-block">
                     {l s='Hide if Customer is not login' mod='hidepriceandcart'}
                </p>
            </div>
        </div>
    </div>

    {*Display category Rule*}
    <div class="form-group">
        <div class="col-lg-12" id="rule_category_list" >
            <label class="control-label col-lg-3" for="FMM_ENABLE_CATEGORY_RULE">
                <span class="label-tooltip" id="HIDEPRICEANDCART-groups" data-toggle="tooltip" title=" {l s='Display Category Rule' mod='hidepriceandcart'}"> {l s='Display Category Rule' mod='hidepriceandcart'}</span>
            </label>        
            <div class="col-lg-8">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input {if $FMM_ENABLE_CATEGORY_RULE == 1}checked="checked" {/if}id="FMM_ENABLE_CATEGORY_RULE_on" name="FMM_ENABLE_CATEGORY_RULE" type="radio" value="1">
                        <label for="FMM_ENABLE_CATEGORY_RULE_on">
                            {l s='Yes' mod='hidepriceandcart'}
                        </label>
                        <input {if $FMM_ENABLE_CATEGORY_RULE == 0}checked="checked" {/if} id="FMM_ENABLE_CATEGORY_RULE_off" name="FMM_ENABLE_CATEGORY_RULE" type="radio" value="0">
                            <label for="FMM_ENABLE_CATEGORY_RULE_off">
                                {l s='No' mod='hidepriceandcart'}
                            </label>
                            <a class="slide-button btn">
                            </a>
                        </input>
                    </input>
                </span>
            </div>
        </div>
    </div>
    {* Categories *}
    <div class="form-group" id="category_rule">
    <div class="col-lg-12" id="rule_category_list"{if $data == 'category'} style="display: block;"{else} style="display: block;"{/if}>
        <label class="control-label col-lg-3" for="HIDEPRICEANDCART-category">
            <span class="label-tooltip" id="HIDEPRICEANDCART-groups" data-toggle="tooltip" title="{l s='Selected category will be able to ask for price only.' mod='hidepriceandcart'}">{l s='Select Category' mod='hidepriceandcart'}</span>
        </label>        
        <div class="col-lg-6">
        <table cellspacing="0" cellpadding="0" class="table std panel">
            <thead>
                <tr>
                    <th> </th>
                    <th>
                        <span class="title_box">
                            {l s='ID' mod='hidepriceandcart'}
                        </span>
                    </th>
                    <th>
                        <span class="title_box">
                            {l s='Name' mod='hidepriceandcart'}
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                {if !isset($categories) || empty($categories)}
                <tr>
                    <td>{l s='No brands found.' mod='hidepriceandcart'}</td>
                </tr>
                {else}
                {foreach from=$categories item=category}
                    <tr>
                    <td>
                        <input type="checkbox" name="categoryBox[]" value="{$category.id_category|escape:'htmlall':'UTF-8'}"{if isset($selected_cat) && in_array($category.id_category|escape:'htmlall':'UTF-8', $selected_cat)} checked="checked"{/if} />
                    </td>
                    <td>
                        {$category.id_category|escape:'htmlall':'UTF-8'}
                    </td>
                    <td>
                        {$category.name|escape:'htmlall':'UTF-8'}
                    </td>
                    </tr>
                {/foreach}
                {/if}
            </tbody>
        </table>
        <p class="help-block hint-block">{l s='Selected category will hide price and cart.' mod='hidepriceandcart'}</p>
        </div>
    </div>
    </div>
    {*Display Include Product Rule*}
    <div class="form-group">
        <div class="col-lg-12" id="rule_category_list" >
            <label class="control-label col-lg-3" for="FMM_ENABLE_INC_PRO_RULE">
                <span class="label-tooltip" id="HIDEPRICEANDCART-groups" data-toggle="tooltip" title=" {l s='Display Include Product Rule' mod='hidepriceandcart'}"> {l s='Display Include Product Rule' mod='hidepriceandcart'}</span>
            </label>        
            <div class="col-lg-8">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input {if $FMM_ENABLE_INC_PRO_RULE == 1}checked="checked" {/if}id="FMM_ENABLE_INC_PRO_RULE_on" name="FMM_ENABLE_INC_PRO_RULE" type="radio" value="1">
                        <label for="FMM_ENABLE_INC_PRO_RULE_on">
                            {l s='Yes' mod='hidepriceandcart'}
                        </label>
                        <input {if $FMM_ENABLE_INC_PRO_RULE == 0}checked="checked" {/if} id="FMM_ENABLE_INC_PRO_RULE_off" name="FMM_ENABLE_INC_PRO_RULE" type="radio" value="0">
                            <label for="FMM_ENABLE_INC_PRO_RULE_off">
                                {l s='No' mod='hidepriceandcart'}
                            </label>
                            <a class="slide-button btn">
                            </a>
                        </input>
                    </input>
                </span>
            </div>
        </div>
    </div>
    {* include Products *}
    <div class="form-group" id="inc_product_rule">
		<div class="col-lg-12 " id="rule_product_list"{if $data == 'product'} style="display: block;"{else} style="display: block"{/if}>
            <label class="control-label col-lg-3" for="HIDEPRICEANDCART-products">
                <span class="label-tooltip" id="HIDEPRICEANDCART-products" data-toggle="tooltip" title="{l s='Selected product will be able to ask for price only.' mod='hidepriceandcart'}">{l s='Select Product to Include' mod='hidepriceandcart'}</span>
            </label>
            <div class="col-lg-6{if $ps_17 <= 0} ps_16_specific{/if}">		
                <div class="col-lg-10  placeholder_holder">
                    <input type="text" placeholder="Example: Blue XL shirt" onkeyup="getRelProducts(this);" />
                    <div id="rel_holder"></div>
                    <div id="rel_holder_temp">
                        <ul>
                            {if (!empty($products))}
                            {foreach from=$products item=product}
                            <li id="row_{$product->id|escape:'htmlall':'UTF-8'}" class="media"><div class="media-left"><img src="{Context::getContext()->link->getImageLink($product->link_rewrite, $product->id_image, 'home_default')|escape:'htmlall':'UTF-8'}" class="media-object image"></div><div class="media-body media-middle"><span class="label">{$product->name|escape:'htmlall':'UTF-8'}&nbsp;(ID:{$product->id|escape:'htmlall':'UTF-8'})</span><i onclick="relDropThis(this);" class="material-icons delete">clear</i></div><input type="hidden" value="{$product->id|escape:'htmlall':'UTF-8'}" name="related_products[]"></li>
                            {/foreach}
                            {/if}
                        </ul>
                    </div>
                </div>
            </div>    
            <div class="help-block hint-block col-lg-6 col-lg-offset-3">{l s='Selected product will hide price and cart.' mod='hidepriceandcart'}</div>
		</div>
	</div>
    {*Display Exclude Product Rule*}
    <div class="form-group">
        <div class="col-lg-12" id="rule_category_list" >
            <label class="control-label col-lg-3" for="FMM_ENABLE_EXC_PRO_RULE">
                <span class="label-tooltip" id="HIDEPRICEANDCART-groups" data-toggle="tooltip" title=" {l s='Display Exclude Product Rule' mod='hidepriceandcart'}"> {l s='Display Exclude Product Rule' mod='hidepriceandcart'}</span>
            </label>        
            <div class="col-lg-8">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input {if $FMM_ENABLE_EXC_PRO_RULE == 1}checked="checked" {/if}id="FMM_ENABLE_EXC_PRO_RULE_on" name="FMM_ENABLE_EXC_PRO_RULE" type="radio" value="1">
                        <label for="FMM_ENABLE_EXC_PRO_RULE_on">
                            {l s='Yes' mod='hidepriceandcart'}
                        </label>
                        <input {if $FMM_ENABLE_EXC_PRO_RULE == 0}checked="checked" {/if} id="FMM_ENABLE_EXC_PRO_RULE_off" name="FMM_ENABLE_EXC_PRO_RULE" type="radio" value="0">
                            <label for="FMM_ENABLE_EXC_PRO_RULE_off">
                                {l s='No' mod='hidepriceandcart'}
                            </label>
                            <a class="slide-button btn">
                            </a>
                        </input>
                    </input>
                </span>
            </div>
        </div>
    </div>
    {* exclude Products *}
   <div class="form-group"  id="exc_product_rule">
        <div class="col-lg-12 " {if $data == 'product'} style="display: block;"{else} style="display: block"{/if}>
            <label class="control-label col-lg-3" for="HIDEPRICEANDCART-products">
                <span class="label-tooltip" id="HIDEPRICEANDCART-products" data-toggle="tooltip" title="{l s='Selected product will be able to ask for price only.' mod='hidepriceandcart'}">{l s='Select Product to Exclude' mod='hidepriceandcart'}</span>
            </label>
            <div class="col-lg-6{if $ps_17 <= 0} ps_16_specific{/if}">      
                <div class="col-lg-10  placeholder_holder">
                    <input type="text" placeholder="Example: Blue XL shirt" onkeyup="getExcProducts(this);" />
                    <div id="rel_holdwe_two"></div>
                    <div id="rel_holdwe_two_temp">
                        <ul>
                            {if (!empty($excluded_products))}
                            {foreach from=$excluded_products item=product}
                            <li id="row_{$product->id|escape:'htmlall':'UTF-8'}" class="media"><div class="media-left"><img src="{Context::getContext()->link->getImageLink($product->link_rewrite, $product->id_image, 'home_default')|escape:'htmlall':'UTF-8'}" class="media-object image"></div><div class="media-body media-middle"><span class="label">{$product->name|escape:'htmlall':'UTF-8'}&nbsp;(ID:{$product->id|escape:'htmlall':'UTF-8'})</span><i onclick="relDropThis(this);" class="material-icons delete">clear</i></div><input type="hidden" value="{$product->id|escape:'htmlall':'UTF-8'}" name="excluded_products[]"></li>
                            {/foreach}
                            {/if}
                        </ul>
                    </div>
                </div>
            </div>    
            <div class="help-block hint-block col-lg-6 col-lg-offset-3">{l s='The hide cart rule will not be apply on Excluded Products' mod='hidepriceandcart'}</div>
        </div>
    </div>
     {*Display Customer Rule*}
    <div class="form-group" >
        <div class="col-lg-12" >
            <label class="control-label col-lg-3" for="FMM_ENABLE_CUSTOMER_RULE">
                <span class="label-tooltip" id="HIDEPRICEANDCART-groups" data-toggle="tooltip" title=" {l s='Display  Customer Group Rule' mod='hidepriceandcart'}"> {l s='Display Customer Group Rule' mod='hidepriceandcart'}</span>
            </label>        
            <div class="col-lg-8">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input {if $FMM_ENABLE_CUSTOMER_RULE == 1}checked="checked" {/if}id="FMM_ENABLE_CUSTOMER_RULE_on" name="FMM_ENABLE_CUSTOMER_RULE" type="radio" value="1">
                        <label for="FMM_ENABLE_CUSTOMER_RULE_on">
                            {l s='Yes' mod='hidepriceandcart'}
                        </label>
                        <input {if $FMM_ENABLE_CUSTOMER_RULE == 0}checked="checked" {/if} id="FMM_ENABLE_CUSTOMER_RULE_off" name="FMM_ENABLE_CUSTOMER_RULE" type="radio" value="0">
                            <label for="FMM_ENABLE_CUSTOMER_RULE_off">
                                {l s='No' mod='hidepriceandcart'}
                            </label>
                            <a class="slide-button btn">
                            </a>
                        </input>
                    </input>
                </span>
            </div>
        </div>
    </div>

    {* Customer Groups *}
    <div class="form-group"  id="customer_rule" {if $data == 'group'} style="display: block;"{else} style="display: block"{/if}>
    <label class="control-label col-lg-3" for="HIDEPRICEANDCART-groups">
      <span class="label-tooltip" id="HIDEPRICEANDCART-groups" data-toggle="tooltip" title="{l s='Selected customer groups will have hide price and cart.' mod='hidepriceandcart'}">{l s='Group Access' mod='hidepriceandcart'}</span>
    </label>
    <div class="col-lg-5">
      <table cellspacing="0" cellpadding="0" class="table std panel">
          <thead>
              <tr>
                <th></th>
                <th>{l s='ID' mod='hidepriceandcart'}</th>
                <th>{l s='Group name' mod='hidepriceandcart'}</th>
              </tr>
          </thead>
          <tbody>
          {foreach from=$groups item=group}
              <tr>
                <td>
                  <input type="checkbox" class="cpgroups" name="cpgroups[]" id="group_{$group.id_group|escape:'htmlall':'UTF-8'}" value="{$group.id_group|escape:'htmlall':'UTF-8'}" {if isset($cpGroups) AND $cpGroups AND in_array($group.id_group, $cpGroups)}checked="checked"{/if}/>
                </td>
                <td>{$group.id_group|escape:'htmlall':'UTF-8'}</td>
                <td>
                  <label for="group_{$group.id_group|escape:'htmlall':'UTF-8'}">{$group.name|escape:'htmlall':'UTF-8'}</label>
                </td>
              </tr>
          {/foreach}
          </tbody>
      </table>
       <p class="help-block hint-block">{l s='Selected customer groups will have hide price and cart.' mod='hidepriceandcart'}</p>
    </div>
</div>
<div class="clearfix"></div>
    <div class="form-group">
<script>
{literal}
var mod_url = "{/literal}{$action_url nofilter}{literal}";
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
        if(!$("#rel_holder_temp ul #row_"+id).length) {
	        var draw_html = '<li id="row_' + id + '" class="media"><div class="media-left"><img src="'+img+'" class="media-object image"></div><div class="media-body media-middle"><span class="label">'+name+'&nbsp;(ID:'+id+')</span><i onclick="relDropThis(this);" class="material-icons delete">clear</i></div><input type="hidden" value="'+id+'" name="related_products[]"></li>'
	        $('#rel_holder_temp ul').append(draw_html);
        }
    }
}
function relClearData() {
    $('#rel_holder').html('');
}
function relDropThis(e) {
    $(e).parent().parent().remove();
}

function getExcProducts(e) {
    var search_q_val = $(e).val();
    //controller_url = controller_url+'&q='+search_q_val;
    if (typeof search_q_val !== 'undefined' && search_q_val) {
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: mod_url + '&q=' + search_q_val,
            success: function(data)
            {
                var quicklink_list ='<li class="rel_breaker" onclick="relClearDatatwo();"><i class="material-icons">&#xE14C;</i></li>';
                $.each(data, function(index,value){
                    if (typeof data[index]['id'] !== 'undefined')
                        quicklink_list += '<li onclick="relSelectThisTwo('+data[index]['id']+','+data[index]['id_product_attribute']+',\''+data[index]['name']+'\',\''+data[index]['image']+'\');"><img src="' + data[index]['image'] + '" width="60"> ' + data[index]['name'] + '</li>';
                });
                if (data.length == 0) {
                    quicklink_list = '';
                }
                $('#rel_holdwe_two').html('<ul>'+quicklink_list+'</ul>');
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    }
    else {
        $('#rel_holdwe_two').html('');
    }
}
function relSelectThisTwo(id, ipa, name, img) {
    if ($('#row_' + id + '_' + ipa).length > 0) {
        showErrorMessage(error_msg);
    } else {
        if(!$("#rel_holdwe_two_temp ul #row_"+id).length) {
            var draw_html = '<li id="row_' + id + '" class="media"><div class="media-left"><img src="'+img+'" class="media-object image"></div><div class="media-body media-middle"><span class="label">'+name+'&nbsp;(ID:'+id+')</span><i onclick="relDropThisTwo(this);" class="material-icons delete">clear</i></div><input type="hidden" value="'+id+'" name="excluded_products[]"></li>'
            $('#rel_holdwe_two_temp ul').append(draw_html);
        }
    }
}
function relClearDatatwo() {
    $('#rel_holdwe_two').html('');
}
function relDropThisTwo(e) {
    $(e).parent().parent().remove();
}
{/literal}
</script>
{else}
    {$smarty.block.parent}
{/if}
{literal}
<style type="text/css">
#rule_category_list { max-height: 600px; overflow-y: scroll}
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
.ps_16_specific .material-icons {font-size: 1px;color: #fff;display:block}
.ps_16_specific .material-icons::before {content: "\f00d"; font-family: "FontAwesome"; font-size: 25px;text-align: center;
color: red;font-style: normal; text-indent: -9999px; font-weight: normal; line-height: 20px;}

#rel_holdwe_two ul { position: absolute; left: 12px; border-radius: 4px; top: 40px; margin: 0px 0 20%; padding: 0; background: #fff;
border: 1px solid #BBCDD2; z-index: 999}
#rel_holdwe_two ul li { list-style: none; padding: 5px 10px; display: block; margin: 0px}
#rel_holdwe_two ul li:hover { cursor: pointer; background: #25B9D7}
#rel_holdwe_two ul li.rel_breaker { padding: 0px; margin: -1px -22px 0 0; background: #fff; float: right;border: 1px solid #BBCDD2;
 border-left: 0px; height: 24px;}
#rel_holdwe_two ul li.rel_breaker:hover { background: #fff;}
.rel_breaker i {font-size: 22px; color: #E50B70; cursor: pointer}
#rel_holdwe_two_temp { clear: both; padding: 10px 0}
#rel_holdwe_two_temp ul { padding: 0; margin: 0}
#rel_holdwe_two_temp ul li { list-style: none; padding: 3px 5px; border-radius: 5px; margin: 6px 0; border: 1px solid #E5E5E5;
display: block}
#rel_holdwe_two_temp ul li div { display: inline-block; vertical-align: middle}
#rel_holdwe_two_temp ul li .media-left { width: 8%}
#rel_holdwe_two_temp ul li .media-left img { max-width: 100%}
#rel_holdwe_two_temp ul li .media-body { width: 86%; margin-left: 5%}
#rel_holdwe_two_temp ul li .media-body span { float: left; font-size: 13px; color: #6c868e; font-weight: normal; white-space: normal !important;
text-align: left; width: 92%}
#rel_holdwe_two_temp ul li .media-body i { float: right; cursor: pointer}
.placeholder_holder { position: relative}
.ps_16_specific .material-icons {font-size: 1px;color: #fff;display:block}
.ps_16_specific .material-icons::before {content: "\f00d"; font-family: "FontAwesome"; font-size: 25px;text-align: center;
color: red;font-style: normal; text-indent: -9999px; font-weight: normal; line-height: 20px;}
</style>{/literal}
{/block}