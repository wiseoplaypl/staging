{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA MILOSZ MYSZCZUK VATEU: PL9730945634
* @copyright 2010-2023 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

<link rel="stylesheet" href="../modules/gmfeed/views/admin-theme.css" type="text/css" media="all">
<script>
    {literal}
    $(document).ready(function () {
        $('.label-tooltip').tooltip();
        {/literal}
        var AjaxToken = "{Tools::getAdminTokenLite('AdminExportProductsFeedGoogle')}";
        var LanguageCode = "{Tools::getValue('language_code','en-us')}";

        {literal}
        function log(message) {
            $("<div>").text(message).prependTo("#log");
            $("#log").scrollTop(0);
        }

        $(".searchCategory").autocomplete(
            "index.php?ajax=1&controller=AdminExportProductsFeedGoogle&action=searchCategory&token=" + AjaxToken + "&language_code=" + LanguageCode,
            {
                minLength: 2,
                max: 20,
            }
        ).result(function (event, data, formatted) {
            var categoryId =  $(this).data('category-id');
            var associationId =  $(this).data('association-id');
            $.ajax({
                type: "POST",
                url: "index.php",
                data: {
                    ajax: 1,
                    language_code: LanguageCode,
                    controller: 'AdminExportProductsFeedGoogle',
                    action: 'saveCategories',
                    id_category: $(this).data('category-id'),
                    id_association: $(this).data('association-id'),
                    value: formatted,
                    token: AjaxToken,
                },
                beforeSend: function() {
                   $('.confirmation_'+categoryId).html('<span class="label label-info"><i class="icon-refresh icon-spin" aria-hidden="true"></i></span>')
                },
                success: function(result)
                {
                    $('.confirmation_'+categoryId).html('<span class="label label-success">{/literal}{l s='Saved' mod='gmfeed'}{literal}</span>')
                }
            });
        });


        $('.repeat_association').click(function(e){
            var categoryId =  $(this).data('category-id');
            var associationId =  $(this).data('association-id');
            var repeated_value = $(this).parent().parent().find('.searchCategory').val();
            var repeated_category_id_before = repeated_value.split("-");
            var repeated_category_id = $.trim(repeated_category_id_before[0]);
            $('.searchCategory').each(function(i,e){
                $(this).val(repeated_value);
                save_repeated_association($(this).data('category-id'), repeated_category_id, repeated_value)
            })
        });

        function save_repeated_association(id_category, id_google_category, formatted) {
            $.ajax({
                type: "POST",
                url: "index.php",
                data: {
                    ajax: 1,
                    language_code: LanguageCode,
                    controller: 'AdminExportProductsFeedGoogle',
                    action: 'saveCategories',
                    id_category: id_category,
                    id_association: id_google_category,
                    value: formatted,
                    token: AjaxToken,
                },
                beforeSend: function() {
                    $('.confirmation_'+id_category).html('<span class="label label-info"><i class="icon-refresh icon-spin" aria-hidden="true"></i></span>')
                },
                success: function(result)
                {
                    $('.confirmation_'+id_category).html('<span class="label label-success">{/literal}{l s='Saved' mod='gmfeed'}{literal}</span>')
                }
            });
        }


        $('.delete_association').click(function(e){
            var categoryId =  $(this).data('category-id');
            var associationId =  $(this).data('association-id');
            $.ajax({
                type: "POST",
                url: "index.php",
                data: {
                    language_code: LanguageCode,
                    controller: 'AdminExportProductsFeedGoogle',
                    action: 'deleteCategories',
                    id_category: $(this).data('category-id'),
                    id_association: $(this).data('association-id'),
                    token: AjaxToken,
                },
                beforeSend: function() {
                    $('.confirmation_'+categoryId).html('<span class="label label-info"><i class="icon-refresh icon-spin" aria-hidden="true"></i></span>')
                },
                success: function(result)
                {
                    $('#category_'+categoryId).attr('data-association-id','');
                    $('#category_delete_'+categoryId).attr('data-association-id','');
                    $('#category_'+categoryId).val('');
                    $('.confirmation_'+categoryId).html('<span class="label label-success">{/literal}{l s='Saved' mod='gmfeed'}{literal}</span>')
                }
            });
        });
    });
    {/literal}
</script>