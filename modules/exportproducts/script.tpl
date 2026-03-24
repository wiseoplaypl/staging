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
<script>
    $(document).ready(function () {

        $("#limit_limit").change(function () {
            rebuildLimitLimit();
        });

        $('input[name="export_delimiter"]' +
            ', select[name="export_activate_date"]' +
            ', select[name="export_category_format"]' +
            ', select[name="export_file_format"]' +
            ', select[name="export_format"]' +
            ', select[name="export_type"]' +
            ', input[name="export_profit_margin"]' +
            ', input[name="export_dateFrom"]' +
            ', input[name="export_dateTo"]' +
            ', input[name="limit_limit"]' +
            ', input[name="limit_page"]' +
            ', select[name="export_format"]' +
            ', radio[name="export_active"]' +
            ', input[name="export_active"]' +
            ', select[name="export_tax"]' +
            ', select[name="export_category"]' +
            ', select[name="delete_images"]' +
            ', select[name="include_url"]' +
            ', select[name="export_language"]' +
            ', select[name="export_manufacturers"]' +
            ', select[name="export_increase_price"]' +
            ', select[name="export_suppliers"]' + '').change(function () {
            updateFeed();
        });

        $('#expand-all-export_categories, #check-all-export_categories, #uncheck-all-export_categories, #collapse-all-export_categories').click(function() {
            $('#export_categories input').change(function () {
                updateFeed();
            });
            updateFeed();
        });

        $('#export_categories input').change(function () {
            updateFeed();
        });

        updateFeed();
        rebuildLimitLimit();
    });

    $('.show-links').click(function () {
        $(this).parent().find('.hide').removeClass('hide');
    });

    function updateFeed() {
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
                .fadeIn(200);
        });
        showSuccessMessage('{$feed_updated|escape:javascript}');
    }

    function infinityProducts(){
        $('.getAllProductsNbInfo').html("{l s='Defined number of items in feed is:' mod='pixelfeed'} " + "<span class='badge'>" + "&infin;" + "</span> " + "{l s='Module will export all products to one feed file' mod='pixelfeed'}");
        $('#limit_page').val(0).change();
    }

    function rebuildLimitLimit() {
        var nbOfItemsInFeed = +$('#limit_limit').val();
        var getAllProductsNb = +$('.getAllProductsNb').html()
        if (nbOfItemsInFeed < getAllProductsNb) {
            if (nbOfItemsInFeed == 0 || nbOfItemsInFeed == "") {
                infinityProducts();
            } else {
                $('.partOfFeedLine').fadeOut();
                $('.getAllProductsNbInfo').html("{l s='Defined number of items in feed is:' mod='pixelfeed'} " + "<span class='badge'>" + nbOfItemsInFeed + " </span> " + "{l s='Number of productsin your shop is: ' mod='pixelfeed'}" + "<span class='badge'>" + getAllProductsNb + " </span> " + "{l s='To export all products you need to split export process to several files' mod='pixelfeed'}" + ":<br/>");
                var nbOfParts = getAllProductsNb / nbOfItemsInFeed;
                for (i = 0; i < Math.ceil(nbOfParts); i++) {
                    var startProduct = (i * nbOfItemsInFeed == 0 ? 1 : (i * nbOfItemsInFeed) + 1);
                    var endProduct = startProduct + nbOfItemsInFeed - 1;
                    $('.getAllProductsNbInfo').append('<div class="partOfFeedLine">' + "{l s='Part: ' mod='pixelfeed'}" + " " + (i + 1) + " {l s='will contain products from' mod='pixelfeed'}: " + startProduct + " {l s='to' mod='pixelfeed'}: " + endProduct + ' <span class="btn btn-default generateThisPartOfFeed" data-part="' + (i + 1) + '">' + "{l s='generate this part of feed' mod='pixelfeed'}" + '</span></div>');
                }
            }
        }
        else {
            infinityProducts();
        }
        $('.generateThisPartOfFeed').click(function () {
            $('#limit_page').val($(this).data('part')).change();
        });
    }
</script>

<style>
    .partOfFeedLine {
        padding: 3px 0;
        border-bottom: 1px dotted blue;
    }
</style>
