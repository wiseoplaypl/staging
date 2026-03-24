/**
/**
 * 2007-2016 PrestaShop
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
 * @author    SeoSA <885588@bk.ru>
 * @copyright 2012-2020 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

$(function () {
    window.tree = new TreeCustom('.tree_custom .block_category_tree', '.tree_custom .tree_categories_header');
    window.tree.init();

    $('#beginSearch').live('click', function (e, orderby, orderway) {
        test = location.href;
        test = test.replace('&conf=2', '');
        test = test.replace('#product','');
        test = test.replace('&submitBulkenableSelectionproduct', '');
        test = test.replace('&submitBulkdisableSelectionproduct','');
        var url = addSearchParam(test);
        if (typeof orderby != 'undefined' && typeof orderway != 'undefined') {
            url = url + '&order_by=' + orderby + '&order_way=' + orderway;
        }
        location.href = url;
    });

    window.addSearchParam = function(url) {
        var categories = '';
        $('[name="categories[]"]:checked').each(function (i) {
            categories += ($(this).val());
            if($('[name="categories[]"]:checked').length > i+1)
                categories += ',';
        });
        var attributes = '';
        $('[name="attributes[]"]:checked').each(function (i) {
            attributes += ($(this).val());
            if($('[name="attributes[]"]:checked').length > i+1)
                attributes += ',';
        });
        if($('[name="categoryBox[]"]:checked').length)
        {
            $('[name="categoryBox[]"]:checked').each(function (i) {
                categories += ($(this).val());
                if($('[name="categoryBox[]"]:checked').length > i+1)
                    categories += ',';
            });
        }
        var manufacturers = '';
        $('[name="manufacturer[]"] option:selected').each(function (i) {
            manufacturers += ($(this).val());
            if($('[name="manufacturer[]"] option:selected').length > i+1)
                manufacturers += ',';
        });
        var suppliers = '';
        $('[name="supplier[]"] option:selected').each(function (i) {
            suppliers += ($(this).val());
            if($('[name="supplier[]"] option:selected').length > i+1)
                suppliers += ',';
        });
        var carriers = '';
        $('[name="carrier[]"] option:selected').each(function (i) {
            carriers += ($(this).val());
            if($('[name="carrier[]"] option:selected').length > i+1)
                carriers += ',';
        });
        if(typeof orderby !== 'undefined' && typeof orderway !== 'undefined')
            var sort = '&order_by='+orderby+'&order_way='+orderway;
        else
            var sort = '';

        var url = url.match(/^.+token[^&]+/i);
        return url
            + '&categories=' + categories
            + '&search_default_categories=' + ($('#search_default_categories').prop('checked') ? 1 : 0)
            + '&search_query=' + $('[name="search_query"]').val()
            + '&type_search=' + $('[name="type_search"]').val()
            + '&manufacturers=' + manufacturers
            + '&suppliers=' + suppliers
            + '&carriers=' + carriers
            + '&how_many_show=' + $('[name="how_many_show"]').val()
            + '&active=' + parseInt($('[name="active"]:checked').val())
            + '&low=' + parseInt($('[name="low"]:checked').val())
            + '&disable=' + parseInt($('[name="disable"]:checked').val())
            + '&product_name_type_search=' + $('[name="product_name_type_search"]:checked').val()
            + '&qty_from=' + ($('[name="qty_from"]').val() != '' ? parseInt($('[name="qty_from"]').val()) : '')
            + '&qty_to=' + ($('[name="qty_to"]').val() != '' ? parseInt($('[name="qty_to"]').val()) : '')
            + '&price_from=' + ($('[name="price_from"]').val() != '' ? parseFloat($('[name="price_from"]').val()) : '')
            + '&price_to=' + ($('[name="price_to"]').val() != '' ? parseFloat($('[name="price_to"]').val()) : '')
            + '&date_from=' + ($('[name="date_from"]').val() != '' ? $('[name="date_from"]').val() : '')
            + '&date_to=' + ($('[name="date_to"]').val() != '' ? $('[name="date_to"]').val() : '')
            + '&custom_feature=' + ($('[name="custom_feature"]').val() != '' ? $('[name="custom_feature"]').val() : '')
            + '&refilters=' + '1'
            + '&attributes=' + attributes
            + sort;
    }

    $('#resetSearch').live('click', function () {
        location.href = location.href.match(/^[^&]+&[^&]+/i);
    });

    $('.pagination-link').off().click(function () {
        var url = location.href.replace(/&submitFilterproduct=\d+/i, '');
        url = location.href.replace(/#product/i, '');
        location.href = url + '&submitFilterproduct=' + $(this).data('page');
    });

    $(".tabs a[data-toggle='tab']").click(function(e){
        e.preventDefault();
        $(this).tab('show');
    });

    $(".panel-heading-top, .hidden_filter").click(function () {
        $(".mode_search > .row").slideToggle();
        $(".hidden_filter i").toggleClass('icon-chevron-up').toggleClass('icon-chevron-down');
    });

    $('input.search_category:first').focus({el:$('.block_category_tree.tree_root:first input.tree_input')}, function(eventObj){
        eventObj.data.el.each(function(){
            $(this).attr('data-search', $(this).data('name').toLowerCase());
        });
    });

    $('table.product .title_box a').on('click', function(){
        var $_GET = {};
        var __GET = $(this).attr('href').split("&");
        for(var i=0; i<__GET.length; i++) {
            var getVar = __GET[i].split("=");
            $_GET[getVar[0]] = typeof(getVar[1])=='undefined' ? '' : getVar[1];
        }
        var default_filter = false;
        $('[name^="productFilter_"]').each(function () {
            if ($(this).val()) {
                default_filter = true;
            }
        });

        if(!default_filter) {
            $('#beginSearch').trigger('click', [$_GET.productOrderby, $_GET.productOrderway]);
            return false;
        }
    });

    $('[name="attribute_group"]').live('change', function () {
        $('[data-attribute-values]').hide();
        $("#btn_list").removeClass("open btn-danger").addClass("btn-success");
    }).trigger('change');

    $("#btn_list").click(function () {
        $(this).toggleClass("open btn-success btn-danger");
        var temp = $("#select_attribute").val();
        $('[data-attribute-values="' + temp + '"]').toggle();
    });

    $("body").on('click', '.datetimepicker-val', function () {
        $(".datetimepicker").removeClass("active");
        $(this).next('.datetimepicker').addClass("active");
        $(this).next('.datetimepicker').trigger('focus');
    });

    $("body").on('click', '.datetimepicker', function () {
        $(".datetimepicker").removeClass("active");
        $(this).addClass("active");
    });

    $("body").on('click', '.ui-datepicker-close', function () {
        var datetimepicker_val = $(".datetimepicker.active").val();
        $(".datetimepicker.active").prev(".datetimepicker-val").val(datetimepicker_val);
        $(".datetimepicker").removeClass("active");
    });

});