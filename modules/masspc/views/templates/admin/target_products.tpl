{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-2020 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

<script type="text/javascript" src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/masspc/views/js/jquery.autocomplete.js"></script>
<script type="text/javascript">
    var masspc_pl_token = '{Tools::getAdminTokenLite('AdminProducts')}';
    var img = '';
    var msgTitle = '{$msgTitle|escape:javascript}';
    var msgContents = '{$msgContents|escape:javascript}';
    {if $version < 1.6}
    img = '<img src="../img/admin/delete.gif" />';
    {/if}
    {literal}

    (function ($) {
        $.fn.goTo = function () {
            $('html, body').animate({
                scrollTop: $(this).offset().top + 'px'
            }, 'fast');
            return this; // for chaining...
        }
    })(jQuery);

    var version = "{/literal}{$version|escape:'htmlall':'UTF-8'}{literal}";
    $(document).ready(function () {
        $(document).ready(function () {
            $('.tpbtn').click(function () {
                $(this).find('input').attr('checked', true);
                set_target();
            });
        });

        var link = "{/literal}ajax_products_list.php{literal}";
        var lang = jQuery('#lang_spy').val();
        $("#product_autocomplete_input")
            .autocomplete(
                link, {
                    minChars: 3,
                    max: 10,
                    width: 500,
                    selectFirst: false,
                    scroll: false,
                    dataType: "json",
                    formatItem: function (data, i, max, value, term) {
                        return value;
                    },
                    parse: function (data) {
                        var mytab = new Array();
                        for (var i = 0; i < data.length; i++)
                            mytab[mytab.length] = {data: data[i], value: data[i].id + ' - ' + data[i].name};
                        return mytab;
                    },
                    extraParams: {
                        token: masspc_pl_token,
                        forceJson: 1,
                        disableCombination: 1,
                        exclude_packs: 0,
                        exclude_virtuals: 0,
                        limit: 20,
                        id_lang: lang
                    }
                }
            )
            .result(function (event, data, formatted) {
                var $divAccessories = $('#addProducts');
                if (data.id.length > 0 && data.name.length > 0) {
                    var exclude = [];
                    var selected = $('.masspc_products');
                    for (var i = 0; i < selected.length; i++)
                        exclude.push(selected[i].value);
                    var ps_div = '';

                    if ($.inArray(data.id_product, exclude) == -1) {
                        ps_div = '<div id="selected_product_' + data.id + '" class="form-control-static margin-form"><input type="hidden" name="masspc_products[]" value="' + data.id + '" class="masspc_products"/><button type="button" class="btn btn-default remove-product" name="' + data.id + '" onclick="deleteProduct(' + data.id + ')">' + img + '<i class="icon-remove text-danger"></i></button>&nbsp;' + data.name + '</div>';
                        $divAccessories.show().html($divAccessories.html() + ps_div);
                        showNoticeMessage(msgTitle, msgContents);

                    }

                }
            });
    });

    function categoryBoxBulk() {
        var ids = [];
        $('#associated-categories-tree-bulk input:checked').each(function (i, e) {
            ids.push($(this).val());
        });

        $.ajax({
            url: window.location.href,
            type: "post",
            dataType: "json",
            data: {
                'ajax': 1,
                'AddProductsCategoryBoxBulk': ids.join()
            },
            success: function (data) {
                var selected = $('.masspc_products');
                var exclude = [];
                for (var i = 0; i < selected.length; i++) {
                    exclude.push(selected[i].value);
                }
                $.each(data, function (i, val) {
                    if ($.inArray(i, exclude) == -1) {
                        $("#addProducts").append('<div id="selected_product_' + i + '" class="form-control-static margin-form"><input type="hidden" name="masspc_products[]" value="' + i + '" class="masspc_products"><button type="button" class="btn btn-default remove-product" name="' + i + '" onclick="deleteProduct(' + i + ')"><i class="icon-remove text-danger"></i></button> ' + val['pname'] + '</div>');
                        $("#addProducts").show();
                        showNoticeMessage(msgTitle, msgContents);
                    }
                });
            }
        });
    }

    function deleteProduct(id) {
        $("#selected_product_" + id).remove();
    }

    function set_target() {
        {/literal}
        if ($('input[name=MASSPC_TARGETPRODUCTS]:checked').val() == "categories") {
            $('#masspc_products').hide();
            $('#masspc_categories').show();
            $('#masspc_manufacturers').hide();
            $('#masspc_fv').hide();
            $('#masspc_attr').hide();
        } else if ($('input[name=MASSPC_TARGETPRODUCTS]:checked').val() == "products") {
            $('#masspc_categories').hide();
            $('#masspc_products').show();
            $('#masspc_manufacturers').hide();
            $('#masspc_fv').hide();
            $('#masspc_attr').hide();
        } else if ($('input[name=MASSPC_TARGETPRODUCTS]:checked').val() == "manufacturers") {
            $('#masspc_categories').hide();
            $('#masspc_products').hide();
            $('#masspc_manufacturers').show();
            $('#masspc_fv').hide();
            $('#masspc_attr').hide();
        } else if ($('input[name=MASSPC_TARGETPRODUCTS]:checked').val() == "fv") {
            $('#masspc_categories').hide();
            $('#masspc_products').hide();
            $('#masspc_manufacturers').hide();
            $('#masspc_fv').show();
            $('#masspc_attr').hide();
        } else if ($('input[name=MASSPC_TARGETPRODUCTS]:checked').val() == "attr") {
            $('#masspc_categories').hide();
            $('#masspc_products').hide();
            $('#masspc_manufacturers').hide();
            $('#masspc_fv').hide();
            $('#masspc_attr').show();
        }
        {literal}
    }

    $(document).ready(function () {
        var link = "{/literal}ajax_products_list.php{literal}";
        var lang = jQuery('#lang_spy').val();
        $('.add_all_search_results').click(function () {
            var search_query = $('#product_autocomplete_input').val();
            $.ajax({
                url: link,
                type: "post",
                dataType: "json",
                data: {
                    'token': masspc_pl_token,
                    'limit': 999999999,
                    'disableCombination': 1,
                    'exclude_packs': 0,
                    'exclude_virtuals': 0,
                    'id_lang': lang,
                    'forceJson': 1,
                    'q': search_query
                },
                success: function (data) {
                    var selected = $('.masspc_products');
                    var exclude = [];
                    for (var i = 0; i < selected.length; i++) {
                        exclude.push(selected[i].value);
                    }
                    $.each(data, function (i, val) {
                        if ($.inArray(i, exclude) == -1) {
                            $("#addProducts").append('<div id="selected_product_' + val['id'] + '" class="form-control-static margin-form"><input type="hidden" name="masspc_products[]" value="' + val['id'] + '" class="masspc_products"><button type="button" class="btn btn-default remove-product" name="' + val['id'] + '" onclick="deleteProduct(' + val['id'] + ')"><i class="icon-remove text-danger"></i></button> ' + val['name'] + '</div>');
                            $("#addProducts").show();
                            showNoticeMessage(msgTitle, msgContents);
                        }
                    });
                }
            });

        });
    });

</script>
{/literal}
<div class="col-lg-12 form-group margin-form">
    <label class="control-label col-lg-3">
        <span data-html="true" data-original-title="{l s='Module will assign / unassign these products to / from selected categories. Select the way of how you want to select these products.' mod='masspc'}" class="label-tooltip" data-toggle="tooltip" title="">{l s='Target products' mod='masspc'}</span>
    </label>
    <div class="col-lg-9">
        <div class="col-lg-12 row">
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 margin-form  btn btn-default tpbtn">
                <input type="radio" name="MASSPC_TARGETPRODUCTS" id="MASSPC_TARGETPRODUCTS_PRODUCTS" value="products" onclick="set_target()" checked/><br/>
                <label class="t" for="MASSPC_TARGETPRODUCTS_PRODUCTS">{l s='Defined products' mod='masspc'}</label>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 margin-form btn btn-default tpbtn">
                <input type="radio" name="MASSPC_TARGETPRODUCTS" id="MASSPC_TARGETPRODUCTS_CATEGORIES" value="categories" onclick="set_target()"/><br/>
                <label class="t" for="MASSPC_TARGETPRODUCTS_CATEGORIES">{l s='Products from categories' mod='masspc'}</label>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 margin-form btn btn-default tpbtn">
                <input type="radio" name="MASSPC_TARGETPRODUCTS" id="MASSPC_TARGETPRODUCTS_MANUFACTURERS" value="manufacturers" onclick="set_target()"/><br/>
                <label class="t" for="MASSPC_TARGETPRODUCTS_MANUFACTURERS">{l s='Products by brand' mod='masspc'}</label>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 margin-form  btn btn-default tpbtn">
                <input type="radio" name="MASSPC_TARGETPRODUCTS" id="MASSPC_TARGETPRODUCTS_FV" value="fv" onclick="set_target()"/><br/>
                <label class="t" for="MASSPC_TARGETPRODUCTS_FV">{l s='By feature' mod='masspc'}</label>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 margin-form  btn btn-default tpbtn">
                <input type="radio" name="MASSPC_TARGETPRODUCTS" id="MASSPC_TARGETPRODUCTS_ATTR" value="attr" onclick="set_target()"/><br/>
                <label class="t" for="MASSPC_TARGETPRODUCTS_ATTR">{l s='By attribute' mod='masspc'}</label>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div id="masspc_products" class="form-group margin-form">
    <div class="row">
        <label class="control-label col-lg-3" for="product_autocomplete_input">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Add all products from categories' mod='masspc'}">{l s='Bulk add products by category' mod='masspc'}</span>
        </label>
        <div class="form-group margin-form ">
            <div class="col-lg-9">
                <div class="alert alert-info">
                    {l s='You can bulk define products from categories. Select category and press "add" button. Module will add products associated from selected categories to the list of products to move (so you will be able to manage them and for example remove some products from list)' mod='masspc'}
                </div>
                {$categories_bulk}
                <span onclick="checkAllSubcats('associated-categories-tree-bulk'); return false;" id="check-all-associated-subcategories-tree-bulk" class="btn btn-default">
                    <i class="icon-check-sign"></i>	{l s='Check all subcategories of checked categories' mod='masspc'}
                </span>
                <span onclick="uncheckAllsubcats('associated-categories-tree-bulk'); return false;" id="uncheck-all-associated-subcategories-tree-bulk" class="btn btn-default">
                    <i class="icon-check-empty"></i> {l s='Uncheck all subcategories of checked categories' mod='masspc'}
                </span>
            </div>
        </div>
        <div class="clearfix"></div>
        <a href="javascript:categoryBoxBulk();" class="btn btn-default pull-right">
            <i class="process-icon-plus"></i>
            {l s='Add' mod='masspc'}
        </a>
    </div>

    <div class="clearfix"></div>
    <br/>

    <div class="row">
        <label class="control-label col-lg-3" for="product_autocomplete_input">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Define products that you want to assign / unasign to other categories' mod='masspc'}">{l s='Select products' mod='masspc'}</span>
        </label>

        <div class="col-lg-9">
            <div class="alert alert-info">
                {l s='You can also search for specific product and just add it to the list of products to move.' mod='masspc'}
            </div>
            <div id="ajax_choose_product">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="input-group">
                            <input id="product_autocomplete_input" name="" type="text" class="text ac_input" value=""/>
                            <input id="lang_spy" type="hidden" value="{Context::getContext()->language->id}"/>
                            <span class="input-group-addon"><i class="icon-search"></i></span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="btn btn-primary add_all_search_results"><i class="icon-plus"></i> {l s='Add all search results' mod='masspc'}</div>
                    </div>
                </div>
                <p class="preference_description help-block margin-form">({l s='Start by typing the first letters of the product\'s name, then select the product from the drop-down list.' mod='masspc'})</p>
            </div>
            <div id="addProducts" style="display:none;">
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div id="masspc_categories" class="col-lg-12 form-group margin-form" style="display:none;">
    <label class="form-group control-label col-lg-3">
        <span class="label-tooltip" data-toggle="tooltip" title="{l s='Select categories. All products from these categories will be a target of the mass assign / unassign process' mod='masspc'}">{l s='Categories' mod='masspc'}</span>
    </label>
    <div class="form-group margin-form ">
        <div class="col-lg-9">
            {$categories}
            <span onclick="checkAllSubcats('associated-categories-tree'); return false;" id="check-all-associated-subcategories-tree-bulk" class="btn btn-default">
                <i class="icon-check-sign"></i>	{l s='Check all subcategories of checked categories' mod='masspc'}
            </span>
            <span onclick="uncheckAllsubcats('associated-categories-tree'); return false;" id="uncheck-all-associated-subcategories-tree-bulk" class="btn btn-default">
                <i class="icon-check-empty"></i> {l s='Uncheck all subcategories of checked categories' mod='masspc'}
            </span>
        </div>
    </div>
</div><br><br/>
<div class="clearfix"></div>
<div id="masspc_manufacturers" class="col-lg-12 form-group margin-form" style="display:none;">
    <div class="alert alert-info">
        {l s='Select manufacturer (brand). Module will operate on products associated with selected brand.' mod='masspc'}
    </div>
    <label class="form-group control-label col-lg-3">
        <span class="label-tooltip" data-toggle="tooltip" title="{l s='Select manufacturer (brand). Module will operate on products associated with selected brand.'}">{l s='Manufacturers' mod='masspc'}</span>
    </label>
    <div class="form-group margin-form ">
        <div class="col-lg-9">
            <select name="id_manufacturer">
                {foreach $manufacturers AS $manufacturer}
                    <option value="{$manufacturer.id_manufacturer}">
                        {$manufacturer.name}<br/>
                    </option>
                {/foreach}
            </select>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div id="masspc_fv" class="col-lg-12 form-group margin-form" style="display:none;">
    <div class="panel" id="fieldset_0">
        <div class="panel-heading">
            <i class="icon-cogs"></i> {l s='Features' mod='masspc'}
        </div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Search for feature' mod='masspc'}
                </label>
                <div class="col-lg-9">
                    <div class="input-group assign_fv">
                        <span class="input-group-addon">
						    <input style="width: 80px; font-size: 10px; margin: 0px; height: 17px;" type="text" placeholder="Search" id="searchTool_feature_value" data-showcounter="true" data-replacementtype="" data-resultinput="assign_fv" data-type="feature_value" data-toggle="tooltip" data-original-title="{l s='Search for name of: feature_value' mod='masspc'}" class="label-tooltip searchToolInput searchTool_feature_value"/>
                        </span>
                        <input type="text" name="assign_fv" id="assign_fv" value="" class="assign_fv"/>
                    </div>
                    <p class="help-block">
                    <div class="assign_fv_feature_valuesBox"></div>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div id="masspc_attr" class="col-lg-12 form-group margin-form" style="display:none;">
    <div class="panel" id="fieldset_1_1">
        <div class="panel-heading">
            <i class="icon-cogs"></i> {l s='Search for attribute' mod='masspc'}
        </div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='By attribute' mod='masspc'}
                </label>
                <div class="col-lg-9">
                    <div class="input-group assign_attr">
                        <span class="input-group-addon">
					        <input style="width: 80px; font-size: 10px; margin: 0px; height: 17px;" type="text" placeholder="{l s='Search' mod='masspc'}" id="searchTool_attribute_value" data-replacementtype="" data-resultinput="assign_attr" data-type="attribute_value" data-showcounter="true" data-toggle="tooltip" data-original-title="{l s='Search for name of: attribute_value' mod='masspc'}" class="label-tooltip searchToolInput searchTool_attribute_value ac_input" autocomplete="off">
                        </span>
                        <input type="text" name="assign_attr" id="assign_attr" value="" class="assign_attr">
                    </div>
                    <p class="help-block">{l s='Search for attribute' mod='masspc'}</p><div class="assign_attr_attribute_valuesBox"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>

<div class="panel-footer">
    <a href="javascript:displayCartRuleTab('wtd');" class="btn btn-default pull-right">
        <i class="process-icon-next"></i>
        {l s='next' mod='masspc'}
    </a>
</div>

