/*
* AfterMail version 1.8.6
*
* @author    Shopmonauten <prestashop@shopmonauten.com>
* @copyright Shopmonauten <www.shopmonauten.com>
* @license   go to addons.prestashop.com (buy one module for one shop).
* @for PrestaShop version 1.6X
* @site www.shopmonauten.de
* @email prestashop@shopmonauten.com
*/
function setList2All(elementName) {
    var productsElement = getE(elementName);
    productsElement.options.length = 0;
    var allOpt = document.createElement("option");
    allOpt.value = -1;
    allOpt.text = "All";

    if (!productsElement)
        return;
    productsElement.options.add(allOpt);
    allOpt.selected = true;
}

function setCurrentFilter(form) {
    form.filterid.value = currentFilterID;
}

function showOrHideProductAttrs()
{
    var categoriesElement = getE('categories');

    if (categoriesElement.selectedIndex == -1)
    {
        $('#product_config').hide();
    }
    else {
        $('#product_config').show();
    }
}

function log(msg) {
    setTimeout(function() {
        throw new Error(msg);
    }, 0);
}

function isMultiSelected(elementName) {
    var multiSelectBox = getE(elementName);
    var counter = 0;
    for (x=0; x < multiSelectBox.length; x++) {
          if (multiSelectBox[x].selected) {
              counter++;
          }
          if(counter>1)
              return true;
    }
    return false;
}


function selectEntry(elementName, id) {
    var multiSelectBox = getE(elementName);
    for (x=0; x < multiSelectBox.length; x++) {
      if (multiSelectBox[x].value == id) {
          multiSelectBox[x].selected = true;
      }else {
          multiSelectBox[x].selected = false;
      }
    }
    $('#ajax_running').hide();
}

function selectEntries(elementName, ids) {

    var multiSelectBox = getE(elementName);
    var jqueryEname = "#" + elementName;
    for (x=0; x < multiSelectBox.length; x++) {
        var test = $.inArray(parseInt(multiSelectBox[x].value), ids);
        if (test!=-1) {
            multiSelectBox[x].selected = true;
        }else {
            multiSelectBox[x].selected = false;
        }
    }
    $('#ajax_running').hide();
}


function fillFilter(filterid,categories,prodids,variantids,ajaxfile) {

    $('#category_config').show();
    fillCategoryBox(filterid,categories,prodids,variantids,ajaxfile);
}

function fillCategoryBox(filterid,categories,prodids,variantids,ajaxfile) {

    var categoryFilled = parseInt($.inArray(-1, categories));

    if (categoryFilled == -1 && filterid!=-1 ) {
        $('#product_config').show();
        selectEntries('categories',categories);
        if(categories.length==1) {
            fillProductBox(filterid,categories[0],prodids,variantids,ajaxfile);
        }
    }else {
        selectEntry('categories',-1);
    }
}

function fillProductBox(filterid,categoryID,prodids,variantids,ajaxfile) {

    var productFilled = parseInt($.inArray(-1, prodids));

    if (productFilled == -1 ) {
        $('#variant_config').show();

        var myAction="loadProducts";

        $("#products").load(ajaxfile,{action:myAction,category:categoryID,id_filter:filterid},new function() {
            if(prodids.length==1) {
                fillVariantBox(filterid,prodids[0],variantids,ajaxfile);
            }else {
                setList2All('attributes');
            }
        });
    }else {
        selectEntries('products',-1);
    }
}

function showTriggerStates() {
    var triggerElement = getE('trigger_type');
    var strSelected = triggerElement.options[triggerElement.selectedIndex].value;
    if(strSelected==0) {
        $('#id_orderstate').hide();
        $('.subscription').hide();
        $('.subscription').parents('.form-group').hide();
    } else if(strSelected==1) {
        $('#id_orderstate').show();
        $('.subscription').hide();
        $('.subscription').parents('.form-group').hide();
    } else if (strSelected == 2) {
        $('#id_orderstate').hide();
        $('.subscription').show();
        $('.subscription').parents('.form-group').show();
    } else if (strSelected == 4) {
        $('#id_orderstate').hide();
        $('.subscription').hide();
        $('.subscription').parents('.form-group').hide();
    }

}

function showVoucherState() {

    var voucherElement = getE('voucher');
    var strSelected = voucherElement.options[voucherElement.selectedIndex].value;

    if(strSelected==0) {
        $('#vouchertype').hide();
        $('#voucheramount').hide();
        $('#voucherdays').hide();
        $('#vouchername').hide();
        $('#restrict_on').hide();
        $('#restrict_off').hide();
        $('#vouchertype').parent().prev().hide();
        $('#voucheramount').parent().prev().hide();
        $('#voucherdays').parent().prev().hide();
        $('#vouchername').parent().prev().hide();
        $('#restrict_off').parent().parent().parent().hide();
        $('#restrict_off').parent().parent().parent().prev().hide();
    } else {
        $('#vouchertype').show();
        $('#voucheramount').show();
        $('#voucherdays').show();
        $('#vouchername').show();
        $('#restrict_on').show();
        $('#restrict_off').show();
        $('#active_on').show();
        $('#active_off').show();
        $('#vouchertype').parent().prev().show();
        $('#voucheramount').parent().prev().show();
        $('#voucherdays').parent().prev().show();
        $('#vouchername').parent().prev().show();
        $('#restrict_off').parent().parent().parent().show();
        $('#restrict_off').parent().parent().parent().prev().show();
    }
}


function fillVariantBox(filterid,productid,variantids,ajaxfile) {
    var myAction="loadAttributes";
    $("#attributes").load(ajaxfile,{action:myAction,product:productid,id_filter:filterid});
}

function showFilterSaveButton() {
    $('#saveFilterButton').show();
}

$(document).ready(function(){
    showTriggerStates();
});
