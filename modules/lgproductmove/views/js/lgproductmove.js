/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf https://www.lineagrafica.es/licenses/license_es.pdf https://www.lineagrafica.es/licenses/license_fr.pdf
 */

function LGGetProducts(target) {
    var filters = {};

    if ($('#filterid').val().trim() != '') {
        filters['id'] = $('#filterid').val().trim();
    }

    if ($('#filtername').val().trim() != '') {
        filters['name'] = $('#filtername').val().trim();
    }

    if ($('#filterreference').val().trim() != '') {
        filters['reference'] = $('#filterreference').val().trim();
    }

    if ($('#filterprice').val().trim() != '') {
        filters['price'] = $('#filterprice').val().trim();
    }

    if ($('#filterstock').val().trim() != '') {
        filters['stock'] = $('#filterstock').val().trim();
    }

    if ($('#filtermanufacturer').val().trim() != '') {
        filters['manufacturer'] = $('#filtermanufacturer').val().trim();
    }

    if ($('#filterstatus').val().trim() != '') {
        filters['status'] = $('#filterstatus').val().trim();
    }

    if ($('#filterdate').val().trim() != '') {
        filters['date'] = $('#filterdate').val().trim();
    }
    $(target).LoadingOverlay('show');
    $.ajax({
        method: 'get',
        url: 'index.php',
        data: {
            ajax: true,
            controller: 'AdminLGProductMove',
            categoria_origen: $('select[name="categoria_origen"]').val(),
            categoria_destino: $('select[name="categoria_destino"]').val(),
            p: $('input[name="lgproductmove_page"]').val(),
            lgproductmove_pagination: $('input[name="lgproductmove_pagination"]').val(),
            accion: $('select[name="accion"]').val(),
            action: 'getProducts',
            filters: filters,
            token: lgproductmove_token,
            satoken: lgproductmove_satoken
        },
        dataType: 'json'
    }).done(function(response) {
        $(target).LoadingOverlay('hide');
        if (response.status == 'ok') {
            $('select[name="categoria_destino"]').prop('disabled',false);
            $('select[name="accion"]').prop('disabled',false);
            $('input[name="copycatlikedefault"]').prop('disabled',false);
            $('input[name="moveproducts"]').prop('disabled',false);
            $('#checkall').prop('checked',false);
            //$('#tableproduct tbody').('');
            $('#tableproduct tbody').html(response.rows);
            $('#lgproductmove_pagination').html(response.pagination);
            $('#category_total_products').html(response.total_products);

            if (window.lgproductmove_select_all) {
                checkAll();
            } else {
                if (window.lgproductmove_selected_items.length > 0) {
                    $('input[name^="selected_products"]').each(function(){
                        if (window.lgproductmove_selected_items.indexOf(parseInt($(this).val())) >= 0) {
                            $(this).attr('checked', true);
                        }
                    });
                }
                var all_selected = true && ($('input[name^="selected_products"]').length > 0);
                $('input[name^="selected_products"]').each(function(){
                    if (!$(this).is(':checked')) {
                        all_selected = false;
                    }
                });
                if (all_selected) {
                    $('#checkall').attr('checked', true);
                }
            }
        }
    });
}

function uncheckAll()
{
    $('input[name^="selected_products"]').each(function(){
        $(this).attr('checked', false);
    });
    $('#checkall').attr('checked', false);
    window.lgproductmove_select_all = 0;
}

function checkAll()
{
    $('input[name^="selected_products"]').each(function(){
        $(this).attr('checked', true);
    });
    $('#checkall').attr('checked', true);
    window.lgproductmove_select_all = 1;
}

$(document).ready(function(){
    window.inactiveCategories           = [];
    window.lgproductmove_selected_items = [];
    window.lgproductmove_select_all     = 0;
    $('#filterdate').datepicker({
        onClose: function(){
            $('input[name="lgproductmove_page"]').val(1);
            LGGetProducts();
        },
        prevText: '',
        nextText: '',
        dateFormat: 'yy-mm-dd'
    });
    $("#checkall").click(function(){
        if ($(this).is(":checked")) {
            $("#tableproduct input[type=checkbox]:visible").each(function() {
                if ($(this).attr("id") != "checkall") {
                    $(this).attr("checked", "checked");
                    if (window.lgproductmove_selected_items.indexOf(parseInt($(this).val())) < 0) {
                        window.lgproductmove_selected_items.push(parseInt($(this).val()));
                    }
                }
            });
        } else {
            $('input[name^="selected_products"]').each(function(){
                $(this).removeAttr("checked");
                if (window.lgproductmove_selected_items.indexOf(parseInt($(this).val())) >= 0) {
                    var pos = window.lgproductmove_selected_items.indexOf(parseInt($(this).val()));
                    window.lgproductmove_selected_items.splice(parseInt(pos), 1);
                }
            });
        }
    });

    $('#lg_product_move_selected_category').text($('select[name="categoria_origen"]').find('option:selected').text().trim());

    if (typeof lgproductmove_recharge != 'undefined') {
        LGGetProducts();
    }

    $(document).on('click','.pagination-link', function() {
        var selected_products = [];
        $('input[name="lgproductmove_page"]').val($(this).attr('data-page'));
        LGGetProducts('#tableproduct tbody');
    });

    $(document).on('keyup', '#filterid', function() {
        $('input[name="lgproductmove_page"]').val(1);
        LGGetProducts('#tableproduct tbody');
    });

    $(document).on('keyup', '#filtername', function() {
        $('input[name="lgproductmove_page"]').val(1);
        LGGetProducts('#tableproduct tbody');
    });

    $(document).on('keyup', '#filterreference', function() {
        $('input[name="lgproductmove_page"]').val(1);
        LGGetProducts('#tableproduct tbody');
    });

    $(document).on('keyup', '#filterprice', function() {
        $('input[name="lgproductmove_page"]').val(1);
        LGGetProducts('#tableproduct tbody');
    });

    $(document).on('keyup', '#filterstock', function() {
        $('input[name="lgproductmove_page"]').val(1);
        LGGetProducts('#tableproduct tbody');
    });

    $(document).on('change', '#filtermanufacturer', function() {
        $('input[name="lgproductmove_page"]').val(1);
        LGGetProducts();
    });

    $(document).on('change', '#filterstatus', function() {
        $('input[name="lgproductmove_page"]').val(1);
        LGGetProducts();
    });

    $(document).on('click', '.pagination-items-page', function(e){
        e.preventDefault();
        $('#lgproductmove-pagination-items-page').val($(this).data("items"));
        LGGetProducts('#tableproduct');
    });

    $(document).on('change', 'select[name="pagination15"]', function(e){
        e.preventDefault();
        $('#lgproductmove-pagination-items-page').val($(this).val());
        LGGetProducts('#tableproduct');
    });

    $(document).on('change', 'select[name="categoria_origen"]', function() {
        window.lgproductmove_selected_items = [];
        uncheckAll();
        $('#lg_product_move_selected_category').text($('select[name="categoria_origen"]').find('option:selected').text().trim());
        LGGetProducts('#tableproduct');
    });

    $(document).on('click', 'input[name^="selected_products"]', function () {
        if (window.lgproductmove_selected_items.indexOf(parseInt($(this).val())) < 0) {
            window.lgproductmove_selected_items.push(parseInt($(this).val()));
        } else {
            var pos = window.lgproductmove_selected_items.indexOf(parseInt($(this).val()));
            window.lgproductmove_selected_items.splice(parseInt(pos), 1);
        }
    });

    $(document).on('click', '#lgmoveproducts_cancel', function() {
        window.lgproductmove_selected_items = [];
        uncheckAll();
    });

    $(document).on('click', '#lgmoveproducts_clear_selection', function() {
        window.lgproductmove_selected_items = [];
        uncheckAll();
    });

    $(document).on('click', '#lgmoveproducts_selection_all_products', function() {
        window.lgproductmove_select_all = 1;
        checkAll();
    });

    $(document).on('click','#lgmoveproductssubmit', function (e) {
        e.preventDefault();
        var final_selected_products = [];

        //if (window.lgproductmove_selected_items.length > 0) {
        //    for(var i = 0; i < window.lgproductmove_selected_items.length; i++) {
        //        final_selected_products = window.lgproductmove_selected_items[i];
        //    }
        //}

        //$('input[name^="selected_products"]:checked').each(function(){
        //    if (final_selected_products.indexOf(parseInt($(this).val())) < 0) {
        //        final_selected_products.push(parseInt($(this).val()));
        //    }
        //});

        if ($('select[name="categoria_origen"]').val() == $('select[name="categoria_destino"]').val()) {
            showErrorMessage(lgproductmove_msg_samecat);
            return false;
        }

        if (window.lgproductmove_selected_items.length <= 0 && window.lgproductmove_select_all == 0) {
            showErrorMessage(lgproductmove_msg_emptyproducts);
            return false;
        }

        if ($('select[name="categoria_origen"]').val() == 0) {
            showErrorMessage(lgproductmove_msg_origincatnotselected);
            return false;
        }

        if ($('select[name="categoria_destino"]').val() == 0) {
            showErrorMessage(lgproductmove_msg_targetcatnotselected);
            return false;
        }

        if ($('select[name="accion"]').val() == 0) {
            showErrorMessage(lgproductmove_msg_actionnotselected);
            return false;
        }
        lgproductmove_confirm_aux = lgproductmove_confirm;
        if ($('select[name="accion"]').val() == 1) {
            lgproductmove_confirm_aux = lgproductmove_confirm_aux.replace("#action#", lgproductmove_confirm_move);
        } else {
            lgproductmove_confirm_aux = lgproductmove_confirm_aux.replace("#action#", lgproductmove_confirm_copy);
        }
        lgproductmove_confirm_aux = lgproductmove_confirm_aux.replace("#origin_category#", $('select[name="categoria_origen"]').find('option:selected').text().trim());
        lgproductmove_confirm_aux = lgproductmove_confirm_aux.replace("#target_category#", $('select[name="categoria_destino"]').find('option:selected').text().trim());
        if (!confirm(lgproductmove_confirm_aux)) {
            return false;
        }

        $.ajax({
            method: 'post',
            url: 'index.php',
            data: {
                ajax: true,
                controller: 'AdminLGProductMove',
                categoria_origen: $('select[name="categoria_origen"]').val(),
                categoria_destino: $('select[name="categoria_destino"]').val(),
                accion: $('select[name="accion"]').val(),
                copycatlikedefault: ($('#copycatlikedefault').prop('checked')?1:0),
                action: 'copyMoveProducts',
                token: lgproductmove_token,
                satoken: lgproductmove_satoken,
                selected_products: (window.lgproductmove_select_all == 1)?[]:window.lgproductmove_selected_items,
                select_all: window.lgproductmove_select_all
            },
            dataType: 'json'
        }).success(function(response) {
            if (response.status == 'ok') {
                window.lgproductmove_selected_items = [];
                showSuccessMessage(response.message);
                LGGetProducts();
            }
            if (response.status == 'ko') {
                $.each(response.errors, function(index, value) {
                    showErrorMessage(value);
                });
            }
        }).error(function(response) {
            if (response.status == 'ko') {
                $.each(response.errors, function(index, value) {
                    showErrorMessage(value);
                });
            } else {
                showErrorMessage(lgproductmove_msg_unkknownerror);
            }
        });
    });
});
