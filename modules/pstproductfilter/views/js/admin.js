/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2020 Presta.Site
 * @license   LICENSE.txt
 */
var pstpf_ajax_main = null;
var pstpf_ajax_filter_list = null;
var pstpf_ajax_column = null;
var pstpf_pause_column_save = false;

$(function () {
    pstpf_copyFiltersToProductForm();
    pstpf_displayOrderByWay();
    pstpf_initTypeWatch();
    pstpf_initSortable();
    pstpf_initToolbarBtn();
    pstpf_initCustomMultiSelects();

    var $filter_wrp = $('#pst_product_filter_wrp');

    $filter_wrp.on('change', '.pstpf_select', function () {
        pstpf_reloadProductList(true);
        pstpf_resetCurrentSet();
    });

    $filter_wrp.on('change', '.pstpf_search_status', function () {
        // update hidden select
        var option_id = $(this).data('option');
        $('#' + option_id).prop('selected', $(this).is(':checked'));

        // update the filter's block, reload products
        var block_id = $(this).data('block');
        pstpf_updateStatusBlock(block_id);
        pstpf_reloadProductList(true);
        pstpf_resetCurrentSet();
    });

    $filter_wrp.on('change', '.pstpf-status-match-all', function () {
        pstpf_reloadProductList(true);
        pstpf_resetCurrentSet();
    });

    $filter_wrp.on('click', '#pstpf-edit-filters', function (e) {
        e.preventDefault();

        var $wrp = $('#pst_product_filter');
        $wrp.toggleClass('pstpf_edit');
        $('#pstpf-inactive-filters-wrp, #pstpf-list-columns-wrp').slideToggle(100);

        var $text = $(this).find('.pstpf-ef-text');
        var alt_text = $text.data('alt-text');
        var text = $text.text();
        $text.text(alt_text);
        $text.data('alt-text', text);

        if ($wrp.hasClass('pstpf_edit')) {
            $('#pstpf-active-filters, #pstpf-inactive-filters').sortable('enable');
            $('#pst-col-table').sortable('enable');
            $('#pstpf-edit-filters').addClass('btn-success btn-primary');
        } else {
            $('#pstpf-active-filters, #pstpf-inactive-filters').sortable('disable');
            $('#pst-col-table').sortable('disable');
            $('#pstpf-edit-filters').removeClass('btn-success btn-primary');
        }
    });

    $filter_wrp.on('click', '.pstpf-selected-remove', function (e) {
        $(this).fadeOut(100);
        var id_filter = $(this).data('id-filter');
        $('#' + id_filter).val('')
            .prop('checked', false)
            .prop('selected', false)
            .trigger('change');

        // clear sliders
        var $slider = $('#' + id_filter + '_slider');
        if ($slider.length) {
            var options = $slider.slider( 'option' );
            $slider.slider( 'values', [ options.min, options.max ] );
            var pstpf_range_id = $slider.data('range-id');
            var smin = options.min;
            var smax = options.max;
            $('#' + pstpf_range_id + '_from').val(smin);
            $('#' + pstpf_range_id + '_to').val(smax);
        }

        // clear checkboxes
        var $cb = $('.' + id_filter + '_cb:checked');
        if ($cb.length) {
            $cb.prop('checked', false);
            var block_id = $cb.first().data('block');
            pstpf_updateStatusBlock(block_id);
        }

        // clear hidden select
        var $select = $('#' + id_filter + '_hidden_select');
        if ($select.length) {
            $select.find('option').prop('selected', false);
        }

        pstpf_reloadProductList(true, {reset_filter: id_filter});
        pstpf_resetCurrentSet();
    });

    $filter_wrp.on('click', '.pstpf-status-helper', function (e) {
        if (!$(this).data('active')) {
            let $content_wrp = $(this).closest('.pstpf-status-block').find('.pstpf-status-content');
            let $status_wrp = $content_wrp.children('.pstpf-status-wrp');
            // if the data should be loaded by ajax:
            if ($content_wrp.hasClass('pstpf-sw-lazyload')) {
                let filter_name = $content_wrp.closest('.pstpf-filter-col').data('filter');
                pstpf_loadFilterData(filter_name, $status_wrp);
            }
            // show the dropdown
            $content_wrp.show();
            $("i", this).addClass("icon-chevron-up").removeClass('icon-chevron-down');

            $(this).data('active', 1);
            // focus the search input
            var $search_input = $(this).parent().find('.pstpf-status-search');
            if ($search_input.length) {
                $search_input.focus();
            }
        }
    });

    $(document).mouseup(function(e) {
        var $container = $(".pstpf-status-content:visible");
        $container.each(function () {
            // if the target of the click isn't the container nor a descendant of the container
            if (!$container.is(e.target) && $container.has(e.target).length === 0) {
                var $helper = $container.prev('.pstpf-status-helper');
                $container.fadeOut(50, function () {
                    $helper.data('active', 0);
                });
                $helper.find('.icon').addClass("icon-chevron-down").removeClass('icon-chevron-up');
            }
        });

        var $container2 = $(".pstpf-select-replace");
        $container2.each(function () {
            // if the target of the click isn't the container nor a descendant of the container
            if (!$container2.is(e.target) && $container2.has(e.target).length === 0) {
                $container2.find('.pstpf-select-options').hide();
                $container2.find('.icon').addClass("icon-chevron-down").removeClass('icon-chevron-up');
            }
        });
    });

    $filter_wrp.on('click', '.pstpf-dropdown-option-selected', function (e) {
        e.stopPropagation();

        var id = $(this).data('for');
        var $target = $('#' + id);
        var checked = $target.prop('checked');
        $target.prop('checked', !checked).trigger('change');
    });

    $filter_wrp.on('keyup', '.pstpf-status-search', function () {
        var val = $(this).val().toLowerCase();
        var with_optgroups = $(this).hasClass('pstpf-ss-optgroup');

        var $parent = $(this).parent();
        if (with_optgroups) {
            $parent.find('.pstpf-optgroup-label').hide();
        }

        var $items = $parent.find('.pstpf-status-name');
        if (val) {
            if (with_optgroups) {
                $parent.find('.pstpf-optgroup-toggle').hide();
            }
            $items.filter(function () {
                var text = $(this).text().toLowerCase();
                if (text.indexOf(val) === -1) {
                    $(this).parent().hide();
                } else {
                    $(this).parent().show();
                    if (with_optgroups) {
                        $(this).closest('.pstpf-optgroup-wrp').find('.pstpf-optgroup-label').show();
                    }
                }
            });
        } else {
            $items.parent().show();
            if (with_optgroups) {
                $('.pstpf-optgroup-label').show();
                $parent.find('.pstpf-optgroup-toggle').show();
                $items.parent().hide();
                $parent.find('.pstpf-optgroup-expanded').removeClass('pstpf-optgroup-expanded');
            }
        }
    });

    $filter_wrp.on('change', '.pstpf_column_cb', pstpf_saveColumnSettings);

    $filter_wrp.on('click', '#pstpf-save-set', function (e) {
        e.preventDefault();

        $(this).hide();
        $('#pstpf-save-set-form').fadeIn(100);
        $('#pstpf-set-name').focus();
    });

    $filter_wrp.on('click', '#pstpf-set-cancel', function (e) {
        e.preventDefault();

        $('#pstpf-save-set-form').hide();
        $('#pstpf-save-set').fadeIn(100);
    });

    $filter_wrp.on('click', '.pstpf-select-current-wrp', function () {
        var $parent = $(this).closest('.pstpf-select-replace');
        $parent.find('.pstpf-select-options').toggle();
    });

    $filter_wrp.on('click', '.pstpf-select-option', function (e) {
        e.preventDefault();
        
        var id_set = $(this).data('value');
        var name = $(this).text();
        var order_by = $(this).data('orderby');
        var order_way = $(this).data('orderway');
        $(this).closest('.pstpf-select-options').hide();
        $(this).closest('.pstpf-select-replace').find('.pstpf-select-current').text(name);
        $('#pst_product_filter').removeClass('pstpf_edit');

        pstpf_reloadFilterBlock({id_set: id_set, 'order[orderBy]': order_by, 'order[orderWay]': order_way}, function() {
            pstpf_reloadProductList(true, {id_set: id_set, 'order[orderBy]': order_by, 'order[orderWay]': order_way}, null, true);
        });
    });

    $filter_wrp.on('click', '.pstpf-select-del', function (e) {
        e.stopPropagation();

        if (confirm(pstpf_txt_confirm_set_delete)) {
            var data = {ajax: true, action: 'deleteSet', id_set: $(this).data('id-set')};

            $.ajax({
                url: pstpf_ajax_url,
                method: 'post',
                data: data,
                success: function (text) {
                    pstpf_showSuccessMessage();
                    pstpf_reloadFilterBlock();
                },
                error: function () {
                    pstpf_showErrorMessage();
                }
            });
        }
    });

    $filter_wrp.on('submit', '#pstpf-save-set-form', function (e) {
        e.preventDefault();

        var name = $('#pstpf-set-name').val();
        var data = {ajax: true, action: 'saveFilterSet', 'filter_set': name};

        // check if entered name already exists:
        var name_exists = false;
        $('#pstpf_set_select').find('.pstpf-select-option-name').each(function() {
            if (name === $(this).text()) {
                name_exists = true;
            }
        });
        if (name_exists && !confirm(pstpf_txt_confirm_set_override)) {
            return false;
        }

        // prepare position data, active/inactive etc
        var fdata = {};
        var i = 0;
        $('#pstpf-active-filters').find('.pstpf-filter-col').each(function () {
            var filter = $(this).data('filter');
            fdata[filter] = {active: 1, position: $(this).index(), filter: filter};
            i++;
        });
        $('#pstpf-inactive-filters').find('.pstpf-filter-col').each(function () {
            var filter = $(this).data('filter');
            fdata[filter] = {active: 0, position: i + $(this).index(), filter: filter};
        });

        // prepare filter values data
        var $filter_inputs = $('#pst_product_filter').find(':input');
        var $filled_filter_inputs = pstpf_filterInputs($filter_inputs);
        var filter_data = $filled_filter_inputs.serializeArray();
        $.each(filter_data, function (index, value) {
            // pack multi-inputs into array, else use string
            if (value.name.slice(-2) === '[]') {
                var name = value.name.slice(0, -2);
                data[name] = (data[name] ? data[name] : []);
                // skip duplicates
                if (data[name].indexOf(value.value) === -1) {
                    data[name].push(value.value);
                }
            } else {
                data[value.name] = value.value;
            }
        });

        var columns_data_string = $('#pstpf-list-columns-wrp').find(':input').serialize();

        var data_string = $.param(data) + '&' + $.param({filters: fdata}) + '&' + columns_data_string;
        $.ajax({
            url: pstpf_ajax_url,
            method: 'post',
            data: data_string,
            success: function (text) {
                if (text === '1') {
                    pstpf_showSuccessMessage();
                    pstpf_reloadProductList(true, {keep_set: 1});
                    pstpf_reloadFilterBlock();
                } else {
                    pstpf_showErrorMessage(text);
                }
            },
            error: function () {
                pstpf_showErrorMessage();
            }
        });
    });

    $(document).on('click', '.pstpf-optgroup-label', function (e) {
        e.preventDefault();

        var $parent = $(this).closest('.pstpf-optgroup-wrp');
        $parent.toggleClass('pstpf-optgroup-expanded');
        $parent.find('label').css('display', '');
    });

    $filter_wrp.on('change', '#pstpf-toggle-columns-default', function () {
        pstpf_pause_column_save = true;

        let checked = $(this).is(':checked');
        $('.pst-col-default').find('.pstpf_column_cb')
            .prop('checked', checked);

        pstpf_pause_column_save = false;
        pstpf_saveColumnSettings();
    });
    $filter_wrp.on('change', '#pstpf-toggle-columns-custom', function () {
        pstpf_pause_column_save = true;

        let checked = $(this).is(':checked');
        $('.pst-col-wrp').not('.pst-col-default').find('.pstpf_column_cb')
            .prop('checked', checked);

        pstpf_pause_column_save = false;
        pstpf_saveColumnSettings();
    });

    $filter_wrp.on('click', '#pstpf-column-reset', function () {
        pstpf_pause_column_save = true;
        // sort
        const $container = $('#pst-col-table');
        const $lis = $container.children('.pst-col-wrp');
        $lis.sort((a, b) => {
            const aNumber = parseInt($(a).data("default-position"));
            const bNumber = parseInt($(b).data("default-position"));
            return aNumber - bNumber;
        });
        $container.append($lis);

        // show default, hide custom
        $('.pst-col-default').find('.pstpf_column_cb')
            .prop('checked', true);
        $('.pst-col-wrp').not('.pst-col-default').find('.pstpf_column_cb')
            .prop('checked', false);

        pstpf_pause_column_save = false;
        pstpf_saveColumnSettings();
    });

    $(document).on('click', '#pstpf_toggle_block', function(e) {
        e.stopPropagation();
        try {
            $('#pst_product_filter_wrp').slideToggle(200, function() {
                var hidden = !$(this).is(':visible');
                localStorage.setItem('pstpf_hide_block', +hidden);
            });
        } catch {
            // do nothing
        }
    });

    $(document).on('click', '#pstpf_toggle_width', function(e) {
        e.stopPropagation();
        try {
            let $div = $('#main-div');
            $div.toggleClass('pstpf-full-width');
            var full_width = $div.hasClass('pstpf-full-width');
            localStorage.setItem('pstpf_full_width', +full_width);
        } catch {
            // do nothing
        }
    });

    // check-uncheck all statuses / list elements
    $(document).on('click', '.pstpf-status-toggle-all', function(e) {
        e.preventDefault();

        let check = !!$(this).hasClass('pstpf-status-check-all');
        let block_id = '';

        $(this).closest('.pstpf-status-wrp').find('.pstpf_search_status').each(function() {
            let option_id = $(this).data('option');
            $('#' + option_id).prop('selected', check);

            block_id = $(this).data('block');
            this.checked = check;
        });
        if (block_id) {
            pstpf_updateStatusBlock(block_id);
        }
        pstpf_reloadProductList(true);
        pstpf_resetCurrentSet();
    });
});

function pstpf_reloadSelectedFilters(data) {
    data.action = 'renderSelectedFilters';
    if (pstpf_ajax_filter_list) {
        pstpf_ajax_filter_list.abort();
    }
    pstpf_ajax_filter_list = $.ajax({
        url: pstpf_ajax_url,
        data: data,
        method: 'post',
        success: function (html) {
            $('#pstpf-selected-list-wrp').html(html);
        }
    });
}

function pstpf_reloadProductList(force_reload, extra_data, callback, skip_default_filters) {
    if (!$('#pst_product_filter').length) {
        return false;
    }

    var $filter_inputs = $('#pst_product_filter').find(':input').not('.pstpf_skip_input, button');
    var $filled_filter_inputs = pstpf_filterInputs($filter_inputs);
    // check if any filter used
    if (!force_reload && !$filled_filter_inputs.length) {
        return false;
    }

    if (pstpf_ajax_main) {
        pstpf_ajax_main.abort();
    }

    // show/hide reset btn
    if ($filled_filter_inputs.length) {
        $('#pstpf-reset-filters').show();
    } else {
        $('#pstpf-reset-filters').hide();
    }

    // highlight active filters
    $filter_inputs.each(function () {
        var $parent = $(this).parents('.pstpf-filter-col:first');
        if ($(this).attr('type') === 'checkbox') {
            if ($parent.find('.pstpf_search_input:checked').length) {
                $parent.addClass('pstpf-filter-col-active');
            } else {
                $parent.removeClass('pstpf-filter-col-active');
            }
        } else {
            var val = $(this).val();
            if (val && typeof val === 'object' && !val.length) {
                val = null;
            }
            if (val) {
                $parent.addClass('pstpf-filter-col-active');
            } else {
                $parent.removeClass('pstpf-filter-col-active');
            }
        }
    });

    // copy correct values instead of loaded during page load
    pstpf_copyFiltersToProductForm();
    if (!skip_default_filters) {
        pstpf_copyDefaultFiltersToPstpf();
    }

    // prepare data
    var data = {ajax: true, action: 'renderProductList', pstpf_submit: 1};
    var $form = (pstpf_use_v2 ? $('#product_filter_form') : $('#product_catalog_list'));
    var $form_inputs = $form.find(':input').not('[name="token"], button');
    $form_inputs = pstpf_filterInputs($form_inputs);

    // merge all data and remove duplicates
    var form_data = $form_inputs.serializeArray();
    var filter_data = $filled_filter_inputs.serializeArray();
    var input_data = $.merge(form_data, filter_data);
    
    $.each(input_data, function (index, value) {
        // pack multi-inputs into array, else use string
        if (value.name.slice(-2) === '[]') {
            var name = value.name.slice(0, -2);
            data[name] = (data[name] ? data[name] : []);
            // skip duplicates
            if (data[name].indexOf(value.value) === -1) {
                data[name].push(value.value);
            }
        } else {
            data[value.name] = value.value;
        }
    });

    var loading_set = !!(extra_data && typeof extra_data.id_set !== 'undefined');
    // update location
    if (typeof window.history !== 'undefined') {
        var uri = URI();
        $filter_inputs.each(function() {
            var type = $(this).attr('type');
            var val = $(this).val();
            var name = $(this).attr('name');
            var filter_name = $(this).data('filter-name');
            var ps_name = 'product[filters][pstpf_' + filter_name + ']';

            if (typeof filter_name === 'undefined') {
                return true;
            }

            if ($(this).hasClass('pstpf_column_cb')) {
                // skip column checkboxes
                return true;
            }

            if (name) {
                uri.removeSearch(name);
                if (pstpf_use_symfony) {
                    uri.removeSearch(ps_name);
                }

                if (!loading_set) {
                    if (type === 'checkbox' || type === 'radio') {
                        var checked = $(this).is(':checked');
                        if (checked) {
                            uri.addSearch(name, val);
                        }
                        // set also standard filter fields
                        if (pstpf_use_symfony) {
                            data['product[filters][pstpf_' + filter_name + ']'] = (checked ? 1 : '');
                            data['product[pstpf_' + filter_name + ']'] = (checked ? 1 : '');
                        } else {
                            data['productFilter_pstpf_' + filter_name] = (checked ? 1 : '');
                        }
                    } else if (val) {
                        uri.addSearch(name, val);
                        if (pstpf_use_symfony) {
                            data['product[filters][pstpf_' + filter_name + ']'] = val;
                            data['product[pstpf_' + filter_name + ']'] = val;
                        } else {
                            data['productFilter_pstpf_' + filter_name] = val;
                        }
                    } else {
                        if (pstpf_use_symfony) {
                            data['product[filters][pstpf_' + filter_name + ']'] = '';
                            data['product[pstpf_' + filter_name + ']'] = '';
                        } else {
                            data['productFilter_pstpf_' + filter_name] = '';
                        }
                    }
                }
            }
        });

        // remove pagination from page url
        uri.removeSearch('submitFilterproduct');

        window.history.replaceState('', '', uri.toString());
    }

    let $product_list_wrp = $('#pstpf-product-list-wrp');
    if ($product_list_wrp.length) {
        $product_list_wrp.addClass('pstpf-loading');
    } else {
        $('#product_grid_panel').addClass('pstpf-loading');
    }

    if (extra_data) {
        data = $.extend(data, extra_data);
    }

    // if it's not loading set, reload selected filters. Otherwise, later will be reloaded all filters
    if (!loading_set) {
        pstpf_reloadSelectedFilters(data);
    }

    data.action = 'renderProductList';
    pstpf_ajax_main = $.ajax({
        url: pstpf_product_ajax_url,
        data: data,
        method: 'post',
        success: function (html) {
            var $helper_div = $('<div />');
            $helper_div.append(html);
            if (pstpf_use_v2) {
                let $product_list_wrp = $('#pstpf-product-list-wrp');
                if ($product_list_wrp.length) {
                    $product_list_wrp.html($helper_div.html());
                } else {
                    $helper_div.attr('id', 'pstpf-product-list-wrp');
                    $('#product_grid_panel').replaceWith($helper_div);
                }
            } else {
                if ($('#product_catalog_list').length) {
                    $('#product_catalog_list').replaceWith(html);
                } else {
                    $('#pstpf-product-list-wrp').find('.content:first').append(html);
                }
            }
            pstpf_copyFiltersToProductForm();
            pstpf_initToolbarBtn();
            pstpf_initCustomMultiSelects();
        },
        error: function(xhr, status, error) {
            if (pstpf_use_symfony) {
                $('#product_catalog_list').replaceWith(xhr.responseText);
                // $('#pstpf-product-list-wrp').html($helper_div.html());
            } else {
                $('#pstpf-product-list-wrp').replaceWith(xhr.responseText);
            }
        },
        complete: function () {
            let $pl_wrp = $('#pstpf-product-list-wrp');
            $pl_wrp.removeClass('pstpf-loading');
            if (callback && typeof callback === 'function') {
                callback();
            }
            // show the "Search" btn on default filter changes
            if (pstpf_use_symfony) {
                $pl_wrp.find('.column-filters :input:visible').on('change', function() {
                    $pl_wrp.find('[name=products_filter_submit]').prop('disabled', false);
                });
            }
        }
    });
}

function pstpf_showSuccessMessage(text) {
    if (!text) {
        text = $('#pstpf-saved-msg').text();
    }
    if (typeof showSuccessMessage === 'function') {
        showSuccessMessage(text);
    } else {
        $('#pstpf-saved-msg').fadeIn(300);
        setTimeout(function () {
            $('#pstpf-saved-msg').fadeOut(800);
        }, 3000);
    }
}

function pstpf_showErrorMessage(text) {
    if (!text) {
        text = $('#pstpf-error-msg').text();
    }
    if (typeof showErrorMessage === 'function') {
        showErrorMessage(text);
    } else {
        $('#pstpf-error-msg').fadeIn(300);
        setTimeout(function () {
            $('#pstpf-error-msg').fadeOut(800);
        }, 3000);
    }
}

function pstpf_copyFiltersToProductForm() {
    // prepare input container
    $('#pstpf_hidden_data').remove();
    let $hidden_data = $('<div id="pstpf_hidden_data" />');
    let $form = (pstpf_use_v2 ? $('#product_filter_form') : $('#form-product'));
    $form.append($hidden_data);

    // prepare selected filters
    var $filter_inputs = $('#pst_product_filter').find(':input');
    var $filled_filter_inputs = pstpf_filterInputs($filter_inputs);

    $filled_filter_inputs.each(function () {
        var name = $(this).attr('name');
        if (name) {
            var value = $(this).val();

            if (typeof value === 'object') {
                $.each(value, function (index, value) {
                    $hidden_data.append('<input type="hidden" name="' + name + '" value="' + value + '" />');
                });
            } else {
                $hidden_data.append('<input type="hidden" name="' + name + '" value="' + value + '" />');
            }
        }
    });

    // sync custom and standard filters if they are the same:
    if (pstpf_use_symfony) {
        $('.pstpf-filter-col').filter(function () {
            return !!$(this).data('ps-alias');
        }).each(function () {
            let alias = $(this).data('ps-alias');
            let filter_name = $(this).data('filter');

            if (filter_name && alias) {
                let $pstpf_text_input = $('#pstpf_' + filter_name);
                let $ps_text_input = $('#product_' + alias);
                if ($ps_text_input.length && $pstpf_text_input.length) {
                    $ps_text_input.val($pstpf_text_input.val());
                }

                // if date range filter
                if ($(this).find('.pstpf_search_date')) {
                    let from = $('#pstpf_' + filter_name + '_from').val();
                    let to = $('#pstpf_' + filter_name + '_to').val();
                    $('#product_' + alias + '_from').val(from);
                    $('#product_' + alias + '_to').val(to);
                }
            }
        });
    }
}

function pstpf_filterInputs($filter_inputs) {
    return $filter_inputs.filter(function () {
        var type = $(this).attr('type');
        // skip column checkboxes
        if ($(this).hasClass('pstpf_column_cb') || !$(this).attr('name')) {
            return false;
        }
        // skip unchecked checkboxes
        if (type === 'checkbox' || type === 'radio') {
            return $(this).is(':checked');
        } else {
            return !!this.value;
        }
    });
}

function pstpf_updateStatusBlock(block_id) {
    var $block = $('#' + block_id);
    var $wrp = $block.find('.pstpf-status-wrp');

    var $items = $wrp.find('.pstpf_search_status:checked');
    var html = '';
    $items.each(function () {
        var name = $(this).siblings('.pstpf-status-name').text();
        var color = $(this).data('color');
        html +=
            '<span class="pstpf-dropdown-option-selected badge badge-light" data-for="' + $(this).attr('id') + '">' +
                (color ? '<span class="pstpf-status-marker" style="background: ' + color + ';"></span> ' : '') +
                name +
                ' &times;' +
            '</span> ';
    });
    $block.find('.pstpf-status-helper-text').html(html);
}

function pstpf_displayOrderByWay() {
    var $form = $('#form-product');
    if (!$form.find('.title_box.active').length) {
        $form.find('.title_box a').each(function () {
            var href = $(this).attr('href');
            if (href.indexOf(pstpf_order_by) !== -1 && href.indexOf(pstpf_order_way) !== -1) {
                $(this).addClass('active');
                $(this).parent().addClass('active');
            }
        });
    }
}

function pstpf_reloadFilterBlock(extra_data, callback) {
    $('#pst_product_filter').addClass('pstpf-loading');

    let data = {ajax: true, action: 'renderFilterBlock'};
    if (extra_data) {
        data = $.extend(data, extra_data);
    }

    $.ajax({
        url: pstpf_ajax_url,
        method: 'post',
        data: data,
        success: function (html) {
            var $tmp = $('<div />').html(html);
            $('#pst_product_filter').html($tmp.find('#pst_product_filter').html());
            pstpf_initTypeWatch();
            pstpf_initSortable();
        },
        complete: function () {
            $('#pst_product_filter').removeClass('pstpf-loading');
            if (callback && typeof callback === 'function') {
                callback();
            }
        }
    });
}

function pstpf_initTypeWatch() {
    $('.pstpf_search_text').pstpfTypeWatch({
        captureLength: 0,
        highlight: false,
        wait: 500,
        callback: function(text){
            pstpf_reloadProductList(true);
            pstpf_resetCurrentSet();
        }
    });
}

function pstpf_initSortable() {
    var $sortable_filters = $('#pstpf-active-filters, #pstpf-inactive-filters');
    $sortable_filters.sortable({
        placeholder: "pstpf-drop-placeholder col-lg-2",
        connectWith: ".pstpf-filters-row",
        tolerance: 'pointer',
        stop: function (event, ui) {
            var fdata = {};
            var i = 0;
            $('#pstpf-active-filters').find('.pstpf-filter-col').each(function () {
                var filter = $(this).data('filter');
                fdata[filter] = {active: 1, position: $(this).index(), filter: filter};
                i++;
            });
            $('#pstpf-inactive-filters').find('.pstpf-filter-col').each(function () {
                var filter = $(this).data('filter');
                fdata[filter] = {active: 0, position: i + $(this).index(), filter: filter};
            });

            var data = {ajax: true, action: 'saveFilterSettings'};
            var data_string = $.param(data) + '&' + $.param({filters: fdata});

            $.ajax({
                url: pstpf_ajax_url,
                method: 'post',
                data: data_string,
                success: function (text) {
                    pstpf_showSuccessMessage();
                },
                error: function () {
                    pstpf_showErrorMessage();
                }
            });
        }
    }).disableSelection();
    // disable by default, enable only when in edit mode
    $sortable_filters.sortable('disable');

    let $sortable_columns = $('#pst-col-table');
    $sortable_columns.sortable({
        placeholder: "pstpf-drop-placeholder pstpf-col-placeholder",
        tolerance: 'pointer',
        stop: function (event, ui) {
            pstpf_saveColumnSettings();
        }
    }).disableSelection();
    // disable by default, enable only when in edit mode
    $sortable_columns.sortable('disable');
}

function pstpf_resetCurrentSet() {
    var $set_select = $('#pstpf_set_select');
    var $set_select_current = $set_select.find('.pstpf-select-current');
    $set_select_current.text($set_select_current.data('default-text'));
    $set_select.find('.pstpf-selected').removeClass('pstpf-selected');
}

function pstpf_initSlider($slider, min, max, r_from, r_to, step, $pstpf_from, $pstpf_to, pstpf_range_id) {
    $slider.slider({
        range: true,
        min: min,
        max: max,
        values: [ r_from, r_to ],
        step: step,
        slide: function(event, ui) {
            var v_from = ui.values[0];
            var v_to = ui.values[1];
            $pstpf_from.val(v_from);
            $pstpf_to.val(v_to);
        },
        stop: function( event, ui ) {
            $('#' + pstpf_range_id).val(ui.values[0] + '-' + ui.values[1]);
            pstpf_reloadProductList(true);
            pstpf_resetCurrentSet();
        }
    });
}

function pstpf_saveColumnSettings() {
    if (pstpf_pause_column_save) {
        return false;
    }

    if (pstpf_ajax_column) {
        pstpf_ajax_column.abort();
    }

    let cdata = {};
    $('#pst-col-table').find('.pst-col-wrp').each(function () {
        let column = $(this).data('column');
        let active = +$(this).find('.pstpf_column_cb').is(':checked');
        cdata[column] = {value: 1, position: $(this).index(), name: column, active: active};
    });

    let data = {ajax: true, action: 'saveColumnSettings'};
    var data_string = $.param(data) + '&' + $.param({columns: cdata});

    pstpf_ajax_column = $.ajax({
        url: pstpf_ajax_url,
        method: 'post',
        data: data_string,
        success: function (text) {
            pstpf_showSuccessMessage();
            pstpf_reloadProductList(true);
            pstpf_resetCurrentSet();
        },
        error: function (jqXHR, textStatus) {
            if (textStatus !== 'abort') {
                pstpf_showErrorMessage();
            }
        }
    });
}

function pstpf_loadFilterData(filter_name, $wrp) {
    if ($wrp.hasClass('loaded')) {
        return true;
    }

    $wrp.addClass('pstpf-loading');
    $.ajax({
        url: pstpf_ajax_url,
        method: 'post',
        data: {ajax: true, action: 'loadFilterData', filter_name: filter_name},
        success: function (html) {
            $wrp.html(html);
            $wrp.addClass('loaded');
        },
        error: function (jqXHR, textStatus) {
            if (textStatus !== 'abort') {
                pstpf_showErrorMessage();
            }
        },
        complete: function () {
            $wrp.removeClass('pstpf-loading');
        }
    });
}

function pstpf_copyDefaultFiltersToPstpf() {
    if (pstpf_use_v2) {
        let $data_wrp = $('#pstpf_hidden_data');
        $('#product_filter_form .column-filters :input').each(function() {
            var regExp = /product\[(.*)\]/g;
            var name = regExp.exec($(this).attr('name'));
            if (name && name[1] !== '_token') {
                $data_wrp.append('<input type="hidden" name="product[filters][' + name[1] + ']" value="' + $(this).val() + '" />');
            }
        });
    }
}

function pstpf_initToolbarBtn() {
    if ($('#pstpf_toggle_block').length) {
        return false;
    }

    let btns = {
        'pstpf_toggle_block': pstpf_txt_show_hide
    };
    if (pstpf_psv >= 8.1) {
        btns['pstpf_toggle_width'] = pstpf_txt_toggle_width;
    }

    $.each(btns, function (id, text) {
        if (pstpf_psv >= 8.1) {
            let btn = '<div class="d-inline-block float-right"><button class="btn btn-text tool-button" id="' + id + '" aria-haspopup="true" aria-expanded="false" title="' + text + '"><span class="sr-only">' + text + '</span></button></div>';
            $('#product_grid_panel').find('.card-header:first').append(btn);
        } else {
            // todo check
            let btn = '<a class="list-toolbar-btn" href="javascript:void(0);" id="' + id + '"><span class="label-tooltip" data-toggle="tooltip" data-original-title="' + text + '" data-html="true" data-placement="top"><i class="process-icon-power"></i></span></a>';
            $('#catalog-tools-button').parent().prepend(btn);
        }
    });
}

function pstpf_initCustomMultiSelects() {
    if (!$('#product_grid').length || !$.fn.SumoSelect) {
        return false;
    }

    $('#product_grid .column-filters select[multiple]').SumoSelect({
        placeholder: '--'
    });
    $('.SumoUnder').off('change').on('change', function () {
        $('.grid-search-button').prop('disabled', false);
    });
}