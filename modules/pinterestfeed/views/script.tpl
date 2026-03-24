{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA MILOSZ MYSZCZUK VATEU: PL9730945634
* @copyright 2010-2022 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

<script>
    (function( factory ) {
        if ( typeof module === "object" && module.exports ) {
            // Node/CommonJS
            module.exports = factory( require( "jquery" ) );

        } else {
            // Browser globals
            factory( window.jQuery );
        }
    }(function( $ ) {

        function updateKeyValueArray( prop, value, obj ) {
            var current = obj[ prop ];

            if ( current === undefined ) {
                obj[ prop ] = [ value ];

            } else {
                current.push( value );
            }

            return obj;
        }

        function getFieldsByName( $elements, filter ) {
            var elementsByName = {};

            // Extract fields from elements
            var fields = $elements
                .map(function convertFormToElements() {
                    return this.elements ? $.makeArray( this.elements ) : this;
                })
                .filter( filter || ":input:not(:disabled)" )
                .get();

            $.each( fields, function( index, field ) {
                updateKeyValueArray( field.name, field, elementsByName );
            });

            return elementsByName;
        }

        function getElementType( element ) {
            return ( element.type || element.nodeName ).toLowerCase();
        }

        function normalizeData( data ) {
            var normalized = {};
            var rPlus = /\+/g;

            // Convert data from .serializeObject() notation
            if ( $.isPlainObject( data ) ) {
                $.extend( normalized, data );

                // Convert non-array values into an array
                $.each( normalized, function( name, value ) {
                    if ( !$.isArray( value ) ) {
                        normalized[ name ] = [ value ];
                    }
                });

                // Convert data from .serializeArray() notation
            } else if ( $.isArray( data ) ) {
                $.each( data, function( index, field ) {
                    updateKeyValueArray( field.name, field.value, normalized );
                });

                // Convert data from .serialize() notation
            } else if ( typeof data === "string" ) {
                $.each( data.split( "&" ), function( index, field ) {
                    var current = field.split( "=" );
                    var name = decodeURIComponent( current[ 0 ].replace( rPlus, "%20" ) );
                    var value = decodeURIComponent( current[ 1 ].replace( rPlus, "%20" ) );
                    updateKeyValueArray( name, value, normalized );
                });
            }

            return normalized;
        }

        var updateTypes = {
            checked: [
                "radio",
                "checkbox"
            ],
            selected: [
                "option",
                "select-one",
                "select-multiple"
            ],
            value: [
                "button",
                "color",
                "date",
                "datetime",
                "datetime-local",
                "email",
                "hidden",
                "month",
                "number",
                "password",
                "range",
                "reset",
                "search",
                "submit",
                "tel",
                "text",
                "textarea",
                "time",
                "url",
                "week"
            ]
        };

        function getPropertyToUpdate( element ) {
            var type = getElementType( element );
            var elementProperty = undefined;

            $.each( updateTypes, function( property, types ) {
                if ( $.inArray( type, types ) > -1 ) {
                    elementProperty = property;
                    return false;
                }
            });

            return elementProperty;
        }

        function update( element, elementIndex, value, valueIndex, callback ) {
            var property = getPropertyToUpdate( element );

            // Handle value inputs
            // If there are multiple value inputs with the same name, they will be populated by matching indexes.
            if ( property == "value" && elementIndex == valueIndex ) {
                element.value = value;
                callback.call( element, value );

                // Handle select menus, checkboxes and radio buttons
            } else if ( property == "checked" || property == "selected" ) {
                var fields = [];

                // Extract option fields from select menus
                if ( element.options ) {
                    $.each( element.options, function( index, option ) {
                        fields.push( option );
                    });

                } else {
                    fields.push( element );
                }

                // #37: Remove selection from multiple select menus before deserialization
                if ( element.multiple && valueIndex == 0 ) {
                    element.selectedIndex = -1;
                }

                $.each( fields, function( index, field ) {
                    if ( field.value == value ) {
                        field[ property ] = true;
                        callback.call( field, value );
                    }
                });
            }
        }

        var defaultOptions = {
            change: $.noop,
            complete: $.noop
        };
        $.fn.deserialize = function( data, options ) {

            // Backwards compatible with old arguments: data, callback
            if ( $.isFunction( options ) ) {
                options = { complete: options };
            }

            options = $.extend( defaultOptions, options || {} );
            data = normalizeData( data );

            var elementsByName = getFieldsByName( this, options.filter );

            $.each( data, function( name, values ) {
                $.each( elementsByName[ name ], function( elementIndex, element ) {
                    $.each( values, function( valueIndex, value ) {
                        update( element, elementIndex, value, valueIndex, options.change );
                    });
                });
            });

            options.complete.call( this );

            return this;
        };
    }));

    function rebuildLimitLimit() {
        var nbOfItemsInFeed = +$('#limit_limit').val();
        var getAllProductsNb = +$('.getAllProductsNb').html()
        if (nbOfItemsInFeed < getAllProductsNb) {
            if (nbOfItemsInFeed == 0 || nbOfItemsInFeed == "") {
                $('.getAllProductsNbInfo').html("{l s='Defined number of items in feed is:' mod='pinterestfeed'} " + "<span class='badge'>" + "&infin;" + "</span> " + "{l s='Module will export all products to one feed file' mod='pinterestfeed'}");
                $('#limit_page').val(0).change();
            } else {
                $('.partOfFeedLine').fadeOut();
                $('.getAllProductsNbInfo').html("{l s='Defined number of items in feed is:' mod='pinterestfeed'} " + "<span class='badge'>" + nbOfItemsInFeed + " </span> " + "{l s='Number of productsin your shop is: ' mod='pinterestfeed'}" + "<span class='badge'>" + getAllProductsNb + " </span> " + "{l s='To export all products you need to split export process to several files' mod='pinterestfeed'}" + ":<br/>");
                var nbOfParts = getAllProductsNb / nbOfItemsInFeed;
                for (i = 0; i < Math.ceil(nbOfParts); i++) {
                    var startProduct = (i * nbOfItemsInFeed == 0 ? 1 : (i * nbOfItemsInFeed) + 1);
                    var endProduct = startProduct + nbOfItemsInFeed - 1;
                    $('.getAllProductsNbInfo').append('<div class="partOfFeedLine">' + "{l s='Part: ' mod='pinterestfeed'}" + " " + (i + 1) + " {l s='will contain products from' mod='pinterestfeed'}: " + startProduct + " {l s='to' mod='pinterestfeed'}: " + endProduct + ' <span class="btn btn-default generateThisPartOfFeed" data-part="' + (i + 1) + '">' + "{l s='generate this part of feed' mod='pinterestfeed'}" + '</span></div>');
                }
            }
        } else {
            $('.getAllProductsNbInfo').html("{l s='Defined number of items in feed is:' mod='pinterestfeed'} " + "<span class='badge'>" + "&infin;" + "</span> " + "{l s='Module will export all products to one feed file' mod='pinterestfeed'}");
            $('#limit_page').val(0).change();
        }
        $('.generateThisPartOfFeed').click(function () {
            $('#limit_page').val($(this).data('part')).change();
        });
    }

    $(document).ready(function () {
        $('select[name="export_size"], select[name="export_size"], select[name="export_gender"], select[name="export_color"], select[name="export_age_group"]').change(function() {
            showHideFields();
        });


        showHideFields();
        $("#limit_limit").keyup(function () {
            rebuildLimitLimit();
        });

        $('.deserializeUrl').change(function () {
            $('#configuration_form').deserialize($(this).val());
        });
        {if Tools::getValue('updatepms','false') !='false'}
        {if Tools::getValue('id_pms','false') !='false'}
        $('.deserializeUrl').trigger('change');
        {/if}
        {/if}
        var AjaxToken = "{Tools::getAdminTokenLite('AdminExportProductsFeedPinterest')}";
        updateFeed(); rebuildLimitLimit();
        $('input[name="export_nophoto_exclude"], select[name="export_ie"], input[name="export_size_default"], select[name="export_save_file"], select[name="export_size_attribute"], select[name="export_size"], select[name="export_age_group"], select[name="export_age_group_default"], select[name="export_age_group_feature"], select[name="export_age_group_type"], select[name="export_age_group_attribute"],input[name="export_color_default"], select[name="export_color_feature"], select[name="export_color_attribute"], select[name="export_color_type"], select[name="export_color"], select[name="export_gender"], select[name="export_gender_default"], select[name="export_gender_feature"], input[name="export_product_name_format"], input[name="export_inventory"], input[name="export_sc"], select[name="export_mainpicture"], select[name="export_storecode"], select[name="export_exreference"], input[name="export_exreference_value"], input[name="export_unit_price"], input[name="limit_limit"], input[name="limit_page"], select[name="export_additional_sc"], select[name="export_shipping_id_zone"], select[name="export_product_type_id"], select[name="export_shipping_info"], input[name="export_shipping_info_price"], input[name="export_instock_info"], input[name="export_what_pictures"], input[name="export_gtin"], select[name="export_product_type"], select[name="export_file_format"], select[name="export_currency"], input[name="export_instock"], input[name="export_removehtml"], input[name="export_description_what"], input[name="export_short_description_what"], input[name="export_delimiter"], input[name="export_active"], select[name="export_identification"],  select[name="export_currency"], select[name="export_category"], select[name="export_type"], select[name="export_language"], select[name="export_manufacturers"], select[name="export_suppliers"], select[name="export_tax"], select[name="export_img"], select[name="export_specific"], select[name="export_igid"], select[name="export_gender"], select[name="export_gender_values"], select[name="export_agegroup"], select[name="export_agegroup_values"], select[name="export_size"], input[name="export_size_values"], select[name="export_country_prices"]').change(function () {
            updateFeed();
        });

        $(".fancybox").fancybox({
            type: 'ajax',
            fitToView: true,
            width: '80%',
            height: '80%',
            autoSize: false,
            closeClick: false,
        });

        $(".syncGooleCategories").click(function (e) {
            var button = $(this);
            e.preventDefault();
            $.ajax({
                url: "ajax-tab.php?language_code=" + $(this).attr('data-language-code') + "&controller=AdminExportProductsFeedPinterest&action=downloadCategories&token=" + AjaxToken,
                beforeSend: function () {
                    button.find('i').addClass('icon-spin').addClass('icon-refresh').removeClass('icon-check').removeClass('icon-bug').parent().removeClass('btn-danger').removeClass('btn-success');
                },
                success: function (result) {
                    if (result == 1) {
                        button.find('i').removeClass('icon-refresh').removeClass('icon-spin').addClass('icon-check').parent().addClass('btn-success').removeClass('btn-danger').parent().parent().find('.taxonomy_info').hide();
                        button.parent().parent().find('.taxonomy_info_success').removeClass('hide').show();
                    } else {
                        button.find('i').removeClass('icon-refresh').removeClass('icon-spin').addClass('icon-bug').parent().addClass('btn-danger').removeClass('btn-success');
                    }
                }
            });
        });

        $(".syncGooleCategoriesCustom").click(function (e) {
            var button = $(this);
            e.preventDefault();
            $.ajax({
                url: "ajax-tab.php?language_code=" + $(this).attr('data-language-code') + "&selected_option=" + $(this).parent().find('select option:selected').val() + "&controller=AdminExportProductsFeedPinterest&action=downloadCategoriesCustom&token=" + AjaxToken,
                beforeSend: function () {
                    button.find('i').addClass('icon-spin').addClass('icon-refresh').removeClass('icon-check').removeClass('icon-bug').parent().removeClass('btn-danger').removeClass('btn-success');
                },
                success: function (result) {
                    if (result == 1) {
                        button.find('i').removeClass('icon-refresh').removeClass('icon-spin').addClass('icon-check').parent().addClass('btn-success').removeClass('btn-danger').parent().parent().find('.taxonomy_info').hide();
                        button.parent().parent().find('.taxonomy_info_success').removeClass('hide').show();
                    } else {
                        button.find('i').removeClass('icon-refresh').removeClass('icon-spin').addClass('icon-bug').parent().addClass('btn-danger').removeClass('btn-success');
                    }
                }
            });
        });
    });

    $('.show-links').click(function () {
        $(this).parent().find('.hide').removeClass('hide');
    });

    function updateFeed() {
        $('#shipping_currency').html($('#export_currency option:selected').html());
        $('.feedurl').html($('#configuration_form').serialize());
        $('.feedurl').each(function () {
            var elem = $(this);
            elem.fadeOut(200)
                .fadeIn(200)
                .fadeOut(200)
                .fadeIn(200)
                .fadeOut(200)
                .fadeIn(200)
                .fadeOut(200)
                .fadeIn(200)
                .fadeOut(200)
                .fadeIn(200);
        });

        $('.SavedFeedUrlFile').html($('#export_type').val()+'.'+$('#export_file_format').val());
        showSuccessMessage('{$feed_updated|escape:javascript}');
        if ($('input[name="export_gtin"]:checked').val() == 'upc') {
            $("#gtin_details").html('UPC, EAN13');
        } else if ($('input[name="export_gtin"]:checked').val() == 'ean13') {
            $("#gtin_details").html('EAN13, UPC');

        } else if ($('input[name="export_gtin"]:checked').val() == 'reference') {
            $("#gtin_details").html('Reference, UPC, EAN13');
        }
    }


    function showHideFields() {
        if ($('#export_gender option:selected').val() != 1) {
            $('#export_gender_feature').parent().parent().hide();
            $('#export_gender_default').parent().parent().hide();
            $('#export_gender_type').parent().parent().hide();
            $('#export_gender_attribute').parent().parent().hide();
        } else {
            $('#export_gender_feature').parent().parent().show();
            $('#export_gender_default').parent().parent().show();
            $('#export_gender_type').parent().parent().show();
            $('#export_gender_attribute').parent().parent().show();
        }

        if ($('#export_age_group option:selected').val() != 1) {
            $('#export_age_group_attribute').parent().parent().hide();
            $('#export_age_group_feature').parent().parent().hide();
            $('#export_age_group_default').parent().parent().hide();
            $('#export_age_group_type').parent().parent().hide();
        } else {
            $('#export_age_group_type').parent().parent().show();
            $('#export_age_group_attribute').parent().parent().show();
            $('#export_age_group_feature').parent().parent().show();
            $('#export_age_group_default').parent().parent().show();
        }

        if ($('#export_color option:selected').val() != 1) {
            $('#export_color_feature').parent().parent().hide();
            $('#export_color_type').parent().parent().hide();
            $('#export_color_attribute').parent().parent().hide();
            $('#export_color_default').parent().parent().hide();
        } else {
            $('#export_color_feature').parent().parent().show();
            $('#export_color_type').parent().parent().show();
            $('#export_color_attribute').parent().parent().show();
            $('#export_color_default').parent().parent().show();
        }

        if ($('#export_size option:selected').val() != 1) {
            $('#export_size_attribute').parent().parent().hide();
            $('#export_size_default').parent().parent().hide();
        } else {
            $('#export_size_attribute').parent().parent().show();
            $('#export_size_default').parent().parent().show();
        }
    }

</script>

