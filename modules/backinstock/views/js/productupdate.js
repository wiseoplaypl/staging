/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @category  PrestaShop Module
 * @author    velsof.com <support@velsof.com>
 * @copyright 2014 Velocity Software Solutions Pvt Ltd
 * @license   see file: LICENSE.txt
 */

//$(document).ready(function() {
//	if(version==5)
//	{
//	}
//});
var times;
$(document).ready(function () {
    $('.product_update_block_back').appendTo('#add-to-cart-or-refresh');
    $('[name="back_stock_email[SendinBlue_list]"]').css('float', 'left');
    $('<img style="width:40px;height:40px;display:none" src="' + path + 'views/img/show_loader.gif" id="show_loader_list"/>').appendTo($('[name="back_stock_email[SendinBlue_list]"]').parent());

    $('[name="back_stock_email[mailchimp_list]"]').css('float', 'left');
    $('<img style="width:40px;height:40px;display:none" src="' + path + 'views/img/show_loader.gif" id="show_loader_list_mailchimp"/>').appendTo($('[name="back_stock_email[mailchimp_list]"]').parent());

    $('[name="back_stock_email[klaviyo_list]"]').css('float', 'left');
    $('<img style="width:40px;height:40px;display:none" src="' + path + 'views/img/show_loader.gif" id="show_loader_list_klaviyo"/>').appendTo($('[name="back_stock_email[klaviyo_list]"]').parent());

    if ($('[name="product_update[enable_gdpr_policy]"]:checked').val() == '1') {
        $("[name='product_update_gdpr_policy_url_1']").parents('.form-group ').show();
        $("[name='product_update_gdpr_policy_text_1']").parents('.form-group ').show();
    } else {
        $("[name='product_update_gdpr_policy_url_1']").parents('.form-group ').hide();
        $("[name='product_update_gdpr_policy_text_1']").parents('.form-group ').hide();
    }
    $('[name="product_update[enable_gdpr_policy]"]').on('change', function () {// alert('hi');
        if ($('[name="product_update[enable_gdpr_policy]"]:checked').val() == '1') {
            $("[name='product_update_gdpr_policy_url_1']").parents('.form-group ').show();
            $("[name='product_update_gdpr_policy_text_1']").parents('.form-group ').show();
        } else {
            $("[name='product_update_gdpr_policy_url_1']").parents('.form-group ').hide();
            $("[name='product_update_gdpr_policy_text_1']").parents('.form-group ').hide();
        }
    });
    if ($('[name="product_update[enable_subscription_list]"]:checked').val() == '1') {
        $("[name='product_update[subscription_per_page]']").parents('.form-group ').show();
        $("[name='product_update[enable_remove_subscription]']").parents('.form-group ').show();
    } else {
        $("[name='product_update[subscription_per_page]']").parents('.form-group ').hide();
        $("[name='product_update[enable_remove_subscription]']").parents('.form-group ').hide();
    }

    if ($('[name="product_update[enable_low_stock_alert]"]:checked').val() == '1') {
        $("[name='product_update[low_stock_alert_quantity]']").parents('.form-group ').show();
    } else {
        $("[name='product_update[low_stock_alert_quantity]']").parents('.form-group ').hide();
    }
    $('[name="product_update[enable_subscription_list]"]').on('change', function () {// alert('hi');
        if ($('[name="product_update[enable_subscription_list]"]:checked').val() == '1') {
            $("[name='product_update[subscription_per_page]']").parents('.form-group ').show();
            $("[name='product_update[enable_remove_subscription]']").parents('.form-group ').show();
        } else {
            $("[name='product_update[subscription_per_page]']").parents('.form-group ').hide();
            $("[name='product_update[enable_remove_subscription]']").parents('.form-group ').hide();
        }
    });
    $('[name="product_update[enable_low_stock_alert]"]').on('change', function () {// alert('hi');
        if ($('[name="product_update[enable_low_stock_alert]"]:checked').val() == '1') {
            $("[name='product_update[low_stock_alert_quantity]']").parents('.form-group ').show();
        } else {
            $("[name='product_update[low_stock_alert_quantity]']").parents('.form-group ').hide();
        }
    });
    if ($('[id^="back_stock_email[mailchimp_status]_on"]').is(':checked') === true) {//alert('hi');
        $("[name='back_stock_email[mailchimp_api]']").parents('.form-group').show();
        $("[name='back_stock_email[mailchimp_list]']").parents('.form-group').show();
    } else {//
        $("[name='back_stock_email[mailchimp_api]']").parents('.form-group').hide();
        $("[name='back_stock_email[mailchimp_list]']").parents('.form-group').hide();
    }

    if ($('[id^="back_stock_email[klaviyo_status]_on"]').is(':checked') === true) {//alert('hi');
        $("[name='back_stock_email[klaviyo_api]']").parents('.form-group').show();
        $("[name='back_stock_email[klaviyo_list]']").parents('.form-group').show();
    } else {//
        $("[name='back_stock_email[klaviyo_api]']").parents('.form-group').hide();
        $("[name='back_stock_email[klaviyo_list]']").parents('.form-group').hide();
    }

    if ($('[id^="back_stock_email[SendinBlue_status]_on"]').is(':checked') === true) {//alert('hi');
        $("[name='back_stock_email[SendinBlue_api]']").parents('.form-group').show();
        $("[name='back_stock_email[SendinBlue_list]']").parents('.form-group').show();
    } else {//
        $("[name='back_stock_email[SendinBlue_api]']").parents('.form-group').hide();
        $("[name='back_stock_email[SendinBlue_list]']").parents('.form-group').hide();
    }

    $('[name="back_stock_email[SendinBlue_status]"]').on('change', function () {// alert('hi');
        if ($(this).val() == '1') {
            $("[name='back_stock_email[SendinBlue_api]']").parents('.form-group').show();
            $("[name='back_stock_email[SendinBlue_list]']").parents('.form-group').show();
        } else {//
            $("[name='back_stock_email[SendinBlue_api]']").parents('.form-group').hide();
            $("[name='back_stock_email[SendinBlue_list]']").parents('.form-group').hide();
        }
    });

    $('[name="back_stock_email[klaviyo_status]"]').click(function () {// alert('hi');
        if ($(this).val() == '1') {//alert('hi');
            $("[name='back_stock_email[klaviyo_api]']").parents('.form-group').show();
            $("[name='back_stock_email[klaviyo_list]']").parents('.form-group').show();
        } else {//
            $("[name='back_stock_email[klaviyo_api]']").parents('.form-group').hide();
            $("[name='back_stock_email[klaviyo_list]']").parents('.form-group').hide();
        }
    });
    $('[name="back_stock_email[mailchimp_status]"]').click(function () {// alert('hi');
        if ($(this).val() == '1') {
            $("[name='back_stock_email[mailchimp_api]']").parents('.form-group').show();
            $("[name='back_stock_email[mailchimp_list]']").parents('.form-group').show();
        } else {//
            $("[name='back_stock_email[mailchimp_api]']").parents('.form-group').hide();
            $("[name='back_stock_email[mailchimp_list]']").parents('.form-group').hide();
        }
    });
    $('[name="back_stock_email[back_stock_email[]"]').on('change', function () {// alert('hi');
        if ($(this).val() == '1') {
            $("[name='back_stock_email[SendinBlue_api]']").parents('.form-group').show();
            $("[name='back_stock_email[SendinBlue_list]']").parents('.form-group').show();
        } else {//
            $("[name='back_stock_email[SendinBlue_api]']").parents('.form-group').hide();
            $("[name='back_stock_email[SendinBlue_list]']").parents('.form-group').hide();
        }
    });
    if (email_marketing_values['mailchimp_status'] == 1) {
        $('.spin_error').remove();
        var mailchimphtml = '';
        if (email_marketing_values['mailchimp_api'] != '') {
            $.ajax({
                url: module_path,
                type: 'post',
                data: 'ajax=true&method=mailchimpgetlist&api_key=' + email_marketing_values['mailchimp_api'],
                dataType: 'json',
                beforeSend: function () {
                    $('#show_loader_list_mailchimp').show();
                },
                success: function (json) {
                    if (json['error'] !== undefined) {
                        $("[name='back_stock_email[mailchimp_list]']").html('<option value="no_list">' + json['error'][0]['label'] + '</option>');
                        $("[name='back_stock_email[mailchimp_list]']").css('border', '1px solid #ff0000');
                    }
                    else {
                        mailchimphtml += '<select name="back_stock_email[mailchimp_list]"';
                        mailchimphtml += 'id="back_stock_email[mailchimp_list]">';
                        for (i in json['success'])
                        {
                            if (email_marketing_values['mailchimp_list'] == json['success'][i]['value']) {
                                mailchimphtml += '<option value="' + json['success'][i]['value'] + '" selected>' + json['success'][i]['label'] + '</option>';
                            }
                            else {
                                mailchimphtml += '<option value="' + json['success'][i]['value'] + '">' + json['success'][i]['label'] + '</option>';
                            }
                        }
                        mailchimphtml += '</select>';
                        $("[name='back_stock_email[mailchimp_list]']").html(mailchimphtml);
                        $("[name='back_stock_email[mailchimp_list]']").css('border', '');
                    }
                },
                complete: function () {
                    $('#show_loader_list_mailchimp').hide();
                },
            });
        }
    }

    if (email_marketing_values['SendinBlue_status'] == 1) {
        $('.spin_error').remove();
        var lists_html = '';
        if (email_marketing_values['SendinBlue_api'] != '') {
            $.ajax({
                url: module_path,
                type: 'post',
                data: 'ajax=true&method=getSendinblueList&api_key=' + email_marketing_values['SendinBlue_api'],
                dataType: 'json',
                beforeSend: function () {
                    $('#show_loader_list').show();
                },
                success: function (json) {
                    if (json == '') {
                        $("[name='back_stock_email[SendinBlue_list]']").html('<option value="no_list">' + no_list_found + '</option>');
                        $("[name='back_stock_email[SendinBlue_list]']").css('border', '1px solid #ff0000');
                    }
                    else {
                        $.each(json, function (key, value) {
                            $.each(value, function (key, list) {
                                if (email_marketing_values['SendinBlue_list'] == list.id) {
                                    lists_html += "<option selected='' value='" + (list.id) + "'>" + (list.name) + "</option>";
                                } else {
                                    lists_html += "<option value='" + (list.id) + "'>" + (list.name) + "</option>";
                                }
                            });

                        });
                        $("[name='back_stock_email[SendinBlue_list]']").html(lists_html);
                        $("[name='back_stock_email[SendinBlue_list]']").css('border', '');
                    }
                },
                complete: function () {
                    $('#show_loader_list').hide();
                },
            });
        }
    }

    if (email_marketing_values['klaviyo_status'] == 1) {
        $('.spin_error').remove();
        var api_key = email_marketing_values['klaviyo_api'];
        var listid = email_marketing_values['klaviyo_list'];
        var klaviyohtml = '';
        if (api_key != '') {
            $.ajax({
                url: module_path,
                type: 'post',
                data: 'ajax=true&method=klaviyogetlist&api_key=' + api_key,
                dataType: 'json',
                beforeSend: function () {
                    $('#show_loader_list_klaviyo').show();
                },
                success: function (json) {
                    if (json['error'] !== undefined) {
                        $("[name='back_stock_email[klaviyo_list]']").html('<option value="no_list">' + json['error'][0]['label'] + '</option>');
                        $("[name='back_stock_email[klaviyo_list]']").css('border', '1px solid #ff0000');
                    }
                    else {
                        klaviyohtml += '<select name="back_stock_email[klaviyo_list]"';

                        klaviyohtml += 'id="klaviyo_selectlist">';

                        for (i in json['success'])
                        {
                            if (listid == json['success'][i]['value'])
                                klaviyohtml += '<option value="' + json['success'][i]['value'] + '" selected>' + json['success'][i]['label'] + '</option>';
                            else
                                klaviyohtml += '<option value="' + json['success'][i]['value'] + '">' + json['success'][i]['label'] + '</option>';
                        }
                        klaviyohtml += '</select>';
                        $("[name='back_stock_email[klaviyo_list]']").html(klaviyohtml);
                        $("[name='back_stock_email[klaviyo_list]']").css('border', '');
                    }
                },
                complete: function () {
                    $('#show_loader_list_klaviyo').hide();
                },
            });
        }
    }

    $('#general_form_submit_btn').click(function () {
//        event.prevent
        if (validation_admin() == false) {
            return false;
        }
        /*Knowband button validation start*/
        $('#general_form_submit_btn').attr('disabled', 'disabled');
        $('#general_form').submit();
        //$('button[name="submitPDPConfiguration"]').submit();
        /*Knowband button validation end*/
    });
    $('.form_email_marketing').click(function () {
//        event.prevent
        if (validation_email_marketing_admin() == false) {
            return false;
        }
        /*Knowband button validation start*/
        $('.form_email_marketing').attr('disabled', 'disabled');
        $('#email_marketing_form').submit();
        //$('button[name="submitPDPConfiguration"]').submit();
        /*Knowband button validation end*/
    });
//    $('.form_initial').click(function () {
////        event.prevent
//        if (validation_email_initial_settings() == false) {
//            return false;
//        }
//        /*Knowband button validation start*/
//        $('.form_initial').attr('disabled', 'disabled');
//        $('#initial_form').submit();
//        /*Knowband button validation end*/
//    });

//    $('.form_low_stock').click(function () {
////        event.prevent
//        if (validation_email_low_stock_settings() == false) {
//            return false;
//        }
//        /*Knowband button validation start*/
//        $('.form_low_stock').attr('disabled', 'disabled');
//        $('#low_stock_alert_form').submit();
//        /*Knowband button validation end*/
//    });
//    $('.form_final').click(function () {
////        event.prevent
//        if (validation_email_final_settings() == false) {
//            return false;
//        }
//        /*Knowband button validation start*/
//        $('.form_final').attr('disabled', 'disabled');
//        $('#final_form').submit();
//        /*Knowband button validation end*/
//    });


    $('#analysis_form .panel').append($('#list_graph'));
    if (version == 1.5) {
        $('#analysis_form fieldset').append($('#list_graph'));
        $("[name='velocity_email_template[template_id]']").closest('.margin-form').hide();
        $("[name='velocity_email_template[template_id_final]']").closest('.margin-form').hide();
        $("[name='velocity_email_template[subject]']").closest('.margin-form').hide();
        $("[name='velocity_email_template[content]']").closest('.margin-form').hide();
        $("[name='velocity_email_template[subject]']").closest('.margin-form').prev('label').hide();
        $("[name='velocity_email_template[content]']").closest('.margin-form').prev('label').hide();
        $("[name='velocity_email_template[subject_final]']").closest('.margin-form').hide();
        $("[name='velocity_email_template[content_drop]']").closest('.margin-form').hide();
        $("[name='velocity_email_template[subject_final]']").closest('.margin-form').prev('label').hide();
        $("[name='velocity_email_template[content_drop]']").closest('.margin-form').prev('label').hide();
    }
    $("[name='velocity_email_template[template_id]']").closest('.form-group').hide();
    $("[name='velocity_email_template[template_id_final]']").closest('.form-group').hide();

    $("[name='velocity_low_stock_alert_setting[template_id]']").closest('.form-group').hide();

    $("[name='velocity_email_template[subject]']").closest('.form-group').hide();
    $("[name='velocity_email_template[content]']").closest('.form-group').hide();

    $("[name='velocity_low_stock_alert_setting[subject]']").closest('.form-group').hide();
    $("[name='velocity_low_stock_alert_setting[content]']").closest('.form-group').hide();

    $("[name='velocity_email_template[subject_final]']").closest('.form-group').hide();
    $("[name='velocity_email_template[content_drop]']").closest('.form-group').hide();
    $('.form_initial').hide();
    $('.form_final').hide();
    $('#general_form').addClass('col-lg-10 col-md-9');
    $('#initial_form').addClass('col-lg-10 col-md-9');
    $('#final_form').addClass('col-lg-10 col-md-9');
    $('#analysis_form').addClass('col-lg-10 col-md-9');
    $('#low_stock_alert_form').addClass('col-lg-10 col-md-9');
    $('#subscriber_list').addClass('col-lg-10 col-md-9');
    $('#email_marketing_form').addClass('col-lg-10 col-md-9');
    $('#general_form').show();

//    $('<div class="panel_spin_wheel">MailChimp</div>').insertBefore($("[name='back_stock_email[mailchimp_status]']").parents('.form-group'));
//    $('<div class="panel_spin_wheel">Klaviyo</div>').insertBefore($("[name='back_stock_email[klaviyo_status]']").parents('.form-group'));
//    $('<div class="panel_spin_wheel">SendinBlue</div>').insertBefore($("[name='back_stock_email[back_stock_email[]']").parents('.form-group'));

    $('#initial_form').hide();
    $('#final_form').hide();
    $('#email_marketing_form').hide();
    $('#low_stock_alert_form').hide();
//    $('#subscriber_list').hide();
    $('#analysis_form').hide();
    times = 0;
    $('#slide1_controls').on('click', function () {
        $('.velsof-adv-panel').removeAttr('style');
        $('#slide1_controls').hide();
        $('#slide2_controls').show();
        setTimeout("$('.buttonsidebar').css('-webkit-transform','rotate(0deg)')", 900);
        setTimeout("$('.buttonsidebar').css('ms-transform','rotate(0deg)')", 900);
        setTimeout("$('.buttonsidebar').css('-moz-transform','rotate(0deg)')", 900);
        setTimeout("$('.buttonsidebar').css('transform','rotate(0deg)')", 900);
    });

    $('#slide2_controls').on('click', function () {
        $('.velsof-adv-panel').attr('style', 'right:-10px;');
        $('#slide1_controls').show();
        $('#slide2_controls').hide();
        setTimeout("$('.buttonsidebar').css('-webkit-transform','rotate(180deg)')", 900);
        setTimeout("$('.buttonsidebar').css('ms-transform','rotate(180deg)')", 900);
        setTimeout("$('.buttonsidebar').css('-moz-transform','rotate(180deg)')", 900);
        setTimeout("$('.buttonsidebar').css('transform','rotate(180deg)')", 900);
    });
    if ($('.color-input').length) {

        $('.color-input').colpick({
            layout: 'hex',
            submit: 0,
            colorScheme: 'light',
            onBeforeShow: function (hsb, hex, rgb, el, bySetColor) {
                $(this).colpickSetColor($(this).attr('value'));
            },
            onChange: function (hsb, hex, rgb, el, bySetColor) {
                $(el).css('background-color', '#' + hex);
                $(el).attr('value', '#' + hex);
                if ($(el).attr('name') == 'kb_sfl_config[buy_color]') {
                }


                if (!bySetColor) {
                    //$(el).val(hex);
                }
            }
        }).keyup(function () {
            $(this).colpickSetColor(this.value);
        });
    }
    $('.initial_lang').bind('change', function () {
        $("[name='velocity_email_template[subject]']").closest('.form-group').hide();
        $("[name='velocity_email_template[content]']").closest('.form-group').hide();
        $('.form_initial').hide();
        var selected_lang = $(this).val();
        selected_temp = 1;
        if (selected_lang != 0)
        {
            $.ajax({
                type: "POST",
                url: action_product_update,
                data: 'ajax_rend=true&selected_lang=' + selected_lang + '&template_action=true&fetch_template=true+&selected_temp=' + selected_temp,
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (json) {
                    $("[name='velocity_email_template[subject]']").closest('.form-group').show();
                    $("[name='velocity_email_template[content]']").closest('.form-group').show();
                    $('.form_initial').show();
                    console.log(tinyMCE.get('velsof_template_content'));
                    tinyMCE.get('velsof_template_content').setContent(json['body']);
                    $('#velsof_template_subject').val(json['subject']);
                    $('#hidden_template_id').val(json['id_template']);
                },
            });
        }
    });

    $('.low_stock_lang').bind('change', function () {
        $("[name='velocity_low_stock_alert_setting[subject]']").closest('.form-group').hide();
        $("[name='velocity_low_stock_alert_setting[content]']").closest('.form-group').hide();
        $('.form_low_stock').hide();
        var selected_lang = $(this).val();
        selected_temp = 3;
        if (selected_lang != 0)
        {
            $.ajax({
                type: "POST",
                url: action_product_update,
                data: 'ajax_rend=true&selected_lang=' + selected_lang + '&template_action=true&fetch_template=true+&selected_temp=' + selected_temp,
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (json) {
                    $("[name='velocity_low_stock_alert_setting[subject]']").closest('.form-group').show();
                    $("[name='velocity_low_stock_alert_setting[content]']").closest('.form-group').show();
                    $('.form_low_stock').show();
                    console.log(tinyMCE.get('velocity_low_stock_alert_setting_template_content'));
                    tinyMCE.get('velocity_low_stock_alert_setting_template_content').setContent(json['body']);
                    $('#velocity_low_stock_alert_setting_subject').val(json['subject']);
                    $('#hidden_velocity_low_stock_alert_setting_template_id').val(json['id_template']);
                },
            });
        }
    });

    $('.final_lang').bind('change', function () {


        $("[name='velocity_email_template[subject_final]']").closest('.form-group').hide();
        $("[name='velocity_email_template[content_drop]']").closest('.form-group').hide();
        $('.form_final').hide();
        var selected_lang = $(this).val();
        selected_temp = 2;
        if (selected_lang != 0)
        {
            $.ajax({
                type: "POST",
                url: action_product_update,
                data: 'ajax_rend=true&selected_lang=' + selected_lang + '&template_action=true&fetch_template=true+&selected_temp=' + selected_temp,
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (json) {
                    $("[name='velocity_email_template[subject_final]']").closest('.form-group').show();
                    $("[name='velocity_email_template[content_drop]']").closest('.form-group').show();
                    console.log(json['body']);
                    $('.form_final').show();
                    tinyMCE.get('velsof_template_content_final').setContent(json['body']);
                    //tinyMCE.get('velsof_template_content_final1').setContent(json[1]['body']);

                    $('#velsof_template_subject_final').val(json['subject']);
                    $('#hidden_template_id_final').val(json['id_template']);


                },
//            error:function(a,b,c){
//                alert(a.status);
//            }
            });
        }
    });

    $('.form_initial').bind('click', function (e) {
        e.preventDefault();
        saveEmailProductTemplate(action_product_update);
        return false;
    });
    $('.form_final').bind('click', function (e) {
        e.preventDefault();
        saveEmailProductTemplateFinal(action_product_update);
        return false;
    });

    $('.form_low_stock').bind('click', function (e) {
        e.preventDefault();
        saveEmailProductTemplateLowStock(action_product_update);
        return false;
    });
    $('.popup_users').bind('click', function () {
        var attribute = $(this).attr('data');
        $.ajax({
            type: "POST",
            url: action_product_update,
            data: 'pop_up=true&attribute=' + attribute,
            beforeSend: function () {

            },
            success: function (x) {
                $('#popup_head_product_count').append(x);
                $('#popup_head_product_count').show();
                $('#dark_popup').show();
            }
        });
    });

    //SendinBLUE
    $("[name^='back_stock_email[SendinBlue_api]']").on('blur', function () {
        $('.error_message').remove();
        var sendinblue_api_key = $(this).val().trim();
        if (sendinblue_api_key != '') {
            $.ajax({
                url: module_path,
                type: 'post',
                data: 'ajax=true&method=getSendinblueList&api_key=' + sendinblue_api_key,
                dataType: 'json',
                beforeSend: function () {
                    $('#show_loader_list').show();
                },
                success: function (response) {
                    if (response != '') {
                        var lists_html = '';
                        $.each(response, function (key, value) {
                            $.each(value, function (key, list) {
                                lists_html += "<option value='" + (list.id) + "'>" + (list.name) + "</option>";
                            });

                        });
                        $("[name='back_stock_email[SendinBlue_list]']").html(lists_html);
                        $("[name='back_stock_email[SendinBlue_list]']").css('border', '');
                    } else {
                        $("[name='back_stock_email[SendinBlue_list]']").html('<option value="no_list">' + no_list_found + '</option>');
                        $("[name='back_stock_email[SendinBlue_list]']").css('border', '1px solid #ff0000');
                    }
                },
                complete: function () {
                    $('#show_loader_list').hide();
                }
            });
        }
    });




    $("[name^='back_stock_email[mailchimp_api]']").on('blur', function () {
        $('.error_message').remove();
        var mailchimp_api_key = $(this).val().trim();
        var clickmailchimphtml = '';
        if (mailchimp_api_key != '') {
            $.ajax({
                url: module_path,
                type: 'post',
                data: 'ajax=true&method=mailchimpgetlist&api_key=' + mailchimp_api_key,
                dataType: 'json',
                beforeSend: function () {
                    $('#show_loader_list_mailchimp').show();
                },
                success: function (json) {
                    if (json['error'] !== undefined) {
                        $("[name='back_stock_email[mailchimp_list]']").html('<option value="no_list">' + json['error'][0]['label'] + '</option>');
                        $("[name='back_stock_email[mailchimp_list]']").css('border', '1px solid #ff0000');
                    } else {
                        clickmailchimphtml += '<select name="back_stock_email[mailchimp_list]"';

                        clickmailchimphtml += 'id="back_stock_email[mailchimp_list]">';

                        for (i in json['success'])
                        {
                            clickmailchimphtml += '<option value="' + json['success'][i]['value'] + '">' + json['success'][i]['label'] + '</option>';
                        }
                        clickmailchimphtml += '</select>';
                        $("[name='back_stock_email[mailchimp_list]']").html(clickmailchimphtml);
                        $("[name='back_stock_email[mailchimp_list]']").css('border', '');
                    }
                },
                complete: function () {
                    $('#show_loader_list_mailchimp').hide();
                },
            });
        }
    });

    $("[name^='back_stock_email[klaviyo_api]']").on('blur', function () {
        $('.error_message').remove();
        var klaviyo_api_key = $(this).val().trim();
        var clickklaviyohtml = '';
        $.ajax({
            url: module_path,
            type: 'post',
            data: 'ajax=true&method=klaviyogetlist&api_key=' + klaviyo_api_key,
            dataType: 'json',
            beforeSend: function () {
                $('#show_loader_list_klaviyo').show();
            },
            success: function (json_data) {
                if (json_data['error'] !== undefined) {
                    $("[name='back_stock_email[klaviyo_list]']").html('<option value="no_list">' + json_data['error'][0]['label'] + '</option>');
                    $("[name='back_stock_email[klaviyo_list]']").css('border', '1px solid #ff0000');
                }
                else {
                    clickklaviyohtml += '<select name="back_stock_email[klaviyo_list]"';

                    clickklaviyohtml += 'id="klaviyo_selectlist">';

                    for (i in json_data['success'])
                    {
                        clickklaviyohtml += '<option value="' + json_data['success'][i]['value'] + '">' + json_data['success'][i]['label'] + '</option>';
                    }
                    clickklaviyohtml += '</select>';
                    $("[name='back_stock_email[klaviyo_list]']").html(clickklaviyohtml);
                    $("[name='back_stock_email[klaviyo_list]']").css('border', '');

                }
            },
            complete: function () {
                $('#show_loader_list_klaviyo').hide();
            },
        });
    });
});
function saveEmailProductTemplate(url)
{
    var selected_lang = $('#selected_language_template_initial').val();
    var selected_temp = 1;
    var text_email_body;
    var subject;
    var content;

    subject = $('#velsof_template_subject').val();
//    content = tinyMCE.activeEditor.getContent();
    content = tinyMCE.get('velsof_template_content').getContent('');
//    text_email_body = tinyMCE.activeEditor.getBody().textContent;

    var text_email_body = $(content).text();

    var id_lang = $('.initial_lang').val();
    var template_id = $('#hidden_template_id').val();
//    tinyMCE.triggerSave();
    if (content != '' && subject != '') {
        $.ajax({
            type: "POST",
            url: url,
            data: {ajax_rend: true, selected_temp: selected_temp, content: content, subject: subject, id_lang: id_lang, template_id: template_id, template_action: true, selected_lang: selected_lang, save_template: true, text_content: text_email_body},
            dataType: 'json',
            beforeSend: function () {
                $('#email_template_action').show();
            },
            success: function (json) {
                $('#email_template_action').hide();
                $('#scratch_coupon_container .scratchcoupon_template_msg').remove();
                if (json['error'] != undefined)
                {
                    var html = '<div class="bootstrap pricealert_template_msg"><div class="alert alert-danger">';
                    html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                    html += json['error'];
                    html += '</div></div>';
                    $('#content').children('div').eq(1).after(html);
                    setTimeout(function () {
                        $('#velsof_supercheckout_container .pricealert_template_msg').remove();
                    }, 5000);
                }
                else
                {
                    var html = '<div class="bootstrap pricealert_template_msg"><div class="alert alert-success">';
                    html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                    html += json['msg'];
                    html += '</div></div>';
                    $('#content').children('div').eq(1).after(html);
                    setTimeout(function () {
                        $('#velsof_supercheckout_container .pricealert_template_msg').remove();
                    }, 5000);
                }
                $('#initial_form').submit();
            }
        });
    }
}
function saveEmailProductTemplateFinal(url)
{
    var selected_lang = $('.final_lang').val();
    var selected_temp = 2;
    var text_email_body;
    var subject;
    var content;

    subject = $('#velsof_template_subject_final').val();
//    content = tinyMCE.activeEditor.getContent();
    content = tinyMCE.get('velsof_template_content_final').getContent('');
//    text_email_body = tinyMCE.activeEditor.getBody().textContent;

    var text_email_body = $(content).text();
    var id_lang = $('.final_lang').val();
    var template_id = $('#hidden_template_id_final').val();
//    tinyMCE.triggerSave();
    if (content != '' && subject != '') {
        $.ajax({
            type: "POST",
            url: url,
            data: {ajax_rend: true, selected_temp: selected_temp, content: content, subject: subject, id_lang: id_lang, template_id: template_id, template_action: true, selected_lang: selected_lang, save_template: true, text_content: text_email_body},
            dataType: 'json',
            beforeSend: function () {
            },
            success: function (json) {
                $('#email_template_action').hide();
                $('#scratch_coupon_container .scratchcoupon_template_msg').remove();
                if (json['error'] != undefined)
                {
                    var html = '<div class="bootstrap pricealert_template_msg"><div class="alert alert-danger">';
                    html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                    html += json['error'];
                    html += '</div></div>';
                    $('#content').children('div').eq(1).after(html);
                    setTimeout(function () {
                        $('#velsof_supercheckout_container .pricealert_template_msg').remove();
                    }, 5000);
                }
                else
                {
                    var html = '<div class="bootstrap pricealert_template_msg"><div class="alert alert-success">';
                    html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                    html += json['msg'];
                    html += '</div></div>';
                    $('#content').children('div').eq(1).after(html);
                    setTimeout(function () {
                        $('#velsof_supercheckout_container .pricealert_template_msg').remove();
                    }, 5000);
                }
                $('#final_form').submit();
            }
        });
    }
}

function saveEmailProductTemplateLowStock(url)
{
    var selected_lang = $('.v').val();
    var selected_temp = 3;
    var text_email_body;
    var subject;
    var content;

    subject = $('#velocity_low_stock_alert_setting_subject').val();
//    content = tinyMCE.activeEditor.getContent();
    content = tinyMCE.get('velocity_low_stock_alert_setting_template_content').getContent('');
//    text_email_body = tinyMCE.activeEditor.getBody().textContent;

    var text_email_body = $(content).text();
    var id_lang = $('.low_stock_lang').val();
    var template_id = $('#hidden_velocity_low_stock_alert_setting_template_id').val();
//    tinyMCE.triggerSave();
    if (content != '' && subject != '') {
        $.ajax({
            type: "POST",
            url: url,
            data: {ajax_rend: true, selected_temp: selected_temp, content: content, subject: subject, id_lang: id_lang, template_id: template_id, template_action: true, selected_lang: selected_lang, save_template: true, text_content: text_email_body},
            dataType: 'json',
            beforeSend: function () {
            },
            success: function (json) {
                $('#email_template_action').hide();
                $('#scratch_coupon_container .scratchcoupon_template_msg').remove();
                if (json['error'] != undefined)
                {
                    var html = '<div class="bootstrap pricealert_template_msg"><div class="alert alert-danger">';
                    html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                    html += json['error'];
                    html += '</div></div>';
                    $('#content').children('div').eq(1).after(html);
                    setTimeout(function () {
                        $('#velsof_supercheckout_container .pricealert_template_msg').remove();
                    }, 5000);
                }
                else
                {
                    var html = '<div class="bootstrap pricealert_template_msg"><div class="alert alert-success">';
                    html += '<button type="button" class="close" data-dismiss="alert">×</button>';
                    html += json['msg'];
                    html += '</div></div>';
                    $('#content').children('div').eq(1).after(html);
                    setTimeout(function () {
                        $('#velsof_supercheckout_container .pricealert_template_msg').remove();
                    }, 5000);
                }
                $('#low_stock_alert_form').submit();
            }
        });
    }
}

$('#filter_data').live('click', function (e) {

    var date_from = document.getElementById("date_from_cus").value;
    var date_to = document.getElementById("date_to_cus").value;

    var from = new Date(date_from);
    var to = new Date(date_to);
    var status = "true";

    var pro_value = $("#c_products").multipleSelect("getSelects");

    if (pro_value == 0 || pro_value == '')
    {

        document.getElementById("date-error-cust").innerHTML = pal_product_select_require;
        status = "false";
        return false;
    }
    else
    {
        document.getElementById("date-error-cust").innerHTML = "";


    }

    if (document.getElementById("date_from_cus").value == "")
    {
        document.getElementById("date_from_cus").placeholder = pal_date_required;
        document.getElementById("date_from_cus").style.background = "rgb(255,209,209)";
        status = "false";
    }
    else
    {
        document.getElementById("date_from_cus").setAttribute = ('placeholder', "");
        document.getElementById("date_from_cus").style.background = "";

    }

    if (document.getElementById("date_to_cus").value == "")
    {
        document.getElementById("date_to_cus").placeholder = pal_date_required;
        document.getElementById("date_to_cus").style.background = "rgb(255,209,209)";
        status = "false";
    }
    else if (document.getElementById("date_from_cus").value != "")
    {
        document.getElementById("date_to_cus").setAttribute = ('placeholder', "");
        document.getElementById("date_to_cus").style.background = "";

    }

    if (from > to)
    {
        document.getElementById("date-error-cust").innerHTML = pal_date_range_error;
        status = "false";
    }
    else if (from <= to && from != "")
    {
        document.getElementById("date-error-cust").innerHTML = "";

    }

    if (status == "true") {
        $.ajax({
            type: "POST",
            url: mod_dir + 'get_customer.php',
            data: "from=" + date_from + "&to=" + date_to + "&product=" + pro_value,
            beforeSend: function () {
                $('#c_loader').show();
            },
            success: function (response) {
                $('#c_loader').hide();
                $('#customer_data').html(response);
            }
        });
    }
});


$('#search_data').live('click', function (e) {
    var date_from = document.getElementById("count-from-date").value;
    var date_to = document.getElementById("count-to-date").value;

    var from = new Date(date_from);
    var to = new Date(date_to);
    var status = "true";


    var pro_value = $("#p_categories").multipleSelect("getSelects");

    if (document.getElementById("count-from-date").value == "")
    {
        document.getElementById("count-from-date").placeholder = pal_date_required;
        document.getElementById("count-from-date").style.background = "rgb(255,209,209)";
        status = "false";
    }
    else
    {
        document.getElementById("count-from-date").setAttribute = ('placeholder', "");
        document.getElementById("count-from-date").style.background = "";

    }

    if (document.getElementById("count-to-date").value == "")
    {
        document.getElementById("count-to-date").placeholder = pal_date_required;
        document.getElementById("count-to-date").style.background = "rgb(255,209,209)";
        status = "false";
    }
    else if (document.getElementById("count-from-date").value != "")
    {
        document.getElementById("count-to-date").setAttribute = ('placeholder', "");
        document.getElementById("count-to-date").style.background = "";

    }

    if (from > to)
    {
        document.getElementById("date-error-pro").innerHTML = pal_date_range_error;
        status = "false";
    }
    else if (from <= to && from != "")
    {
        document.getElementById("date-error-pro").innerHTML = "";

    }

    if (status == "true") {
        var skv = $('#Search_skv').val();
        var categories;
        if ($('#p_categories').val() == null)
        {
            categories = "nothing";
        }
        else
        {
            categories = $('#p_categories').val();
        }

        $.ajax({
            type: "POST",
            url: mod_dir + 'get_alert.php',
            data: "from=" + $("#count-from-date").val() + "&to=" + $("#count-to-date").val() + "&category=" + categories + "&skv=" + $('#Search_skv').val(),
            beforeSend: function () {
                $('#loader').show();
            },
            success: function (response) {
                $('#loader').hide();
//              $('#graph_loader').hide();
                $('#product_data').html(response);
                $('#graph_loader').show();

            }
        });
    }
    else
    {
        return false;
    }

});
function getDefaultGraph() {
    if (times == 0) {
        getDate(1);
    }
}
function getDate(type)
{
    switch (type) {
        case 1 :
            var todate = moment().format("MM/DD/YYYY");
            var fromdate = moment(todate).startOf('week').format("MM/DD/YYYY");
            $("#calender-txt").html(this_week_txt);
            break;
        case 2 :
            var todate = moment().format("MM/DD/YYYY");
            var fromdate = moment(todate).startOf('month').format("MM/DD/YYYY");
            $("#calender-txt").html(this_month_txt);
            break;
        case 3 :
            var todate = moment().format("MM/DD/YYYY");
            var fromdate = moment(todate).startOf('year').format("MM/DD/YYYY");
            $("#calender-txt").html(this_year_txt);
            break;
        case 4 :
            var todate = moment().subtract(1, 'weeks').endOf('Week').format("MM/DD/YYYY");
            var fromdate = moment().subtract(1, 'weeks').startOf('Week').format("MM/DD/YYYY");
            $("#calender-txt").html(last_week_txt);
            break;
        case 5 :
            var todate = moment().subtract(1, 'months').endOf('month').format("MM/DD/YYYY");
            var fromdate = moment().subtract(1, 'months').startOf('month').format("MM/DD/YYYY");
            $("#calender-txt").html(last_month_txt);
            break;
        case 6 :
            var todate = moment().subtract(1, 'years').endOf('year').format("MM/DD/YYYY");
            var fromdate = moment().subtract(1, 'years').startOf('year').format("MM/DD/YYYY");
            $("#calender-txt").html(last_year_txt);
            break;
    }
//    $('.bgcolor').removeClass('tab-active');
//    $('.bgcolor-total').addClass('tab-active');
    $("#count-to-date").val(todate);
    $("#count-from-date").val(fromdate);
    var fromdate = $("#count-from-date").val();
    var todate = $("#count-to-date").val();
    getGraph();
}
$('#filter_graph').live('click', function (e) {
    var date_from = document.getElementById("count-from-date").value;
    var date_to = document.getElementById("count-to-date").value;
    var from = new Date(date_from);
    var to = new Date(date_to);
    var status = "true";
    var currentDate = new Date()
    var day = currentDate.getDate()
    var month = currentDate.getMonth() + 1
    var year = currentDate.getFullYear()
    var date_curent = (month + "/" + day + "/" + year);
    if (document.getElementById("count-from-date").value == "")
    {
        document.getElementById("count-from-date").placeholder = pal_date_required;
        document.getElementById("count-from-date").style.background = "rgb(255,209,209)";
        status = "false";
    }
    else
    {
        document.getElementById("count-from-date").setAttribute = ('placeholder', "");
        document.getElementById("count-from-date").style.background = "";

    }

    if (document.getElementById("count-to-date").value == "")
    {
        document.getElementById("count-to-date").placeholder = pal_date_required;
        document.getElementById("count-to-date").style.background = "rgb(255,209,209)";
        status = "false";
    }
    else if (document.getElementById("count-from-date").value != "")
    {
        document.getElementById("count-to-date").setAttribute = ('placeholder', "");
        document.getElementById("count-to-date").style.background = "";

    }

    if (from > to)
    {
        document.getElementById("date-error-pro").innerHTML = pal_date_range_error;
        status = "false";
    }
    else if (from <= to && from != "")
    {
        document.getElementById("date-error-pro").innerHTML = "";

    }
    if (document.getElementById("count-from-date").value > date_curent)
    {
        $('#date-error-pro').html(pal_date_future);
        status = "false";
    }
    if (status == "true") {
        $.ajax({
            type: "POST",
            url: mod_dir + 'get_alert.php',
            data: "from=" + $("#count-from-date").val() + "&to=" + $("#count-to-date").val(),
            beforeSend: function () {
                $('#loader').show();
            },
            success: function (response) {
                $('#loader').hide();
//                $('#graph_loader').hide();
//                $('#product_data').html(response);
                $('#graph_loader').show();
                getGraph();
            }
        });
    }
    else
    {
        return false;
    }
});

function getGraph()
{
    $.ajax({
        type: "POST",
        url: action_product_update,
        data: "graph=true&from=" + $("#count-from-date").val() + "&to=" + $("#count-to-date").val(),
        dataType: 'json',
        success: function (json) {
            times = 1;
            $('.no_data').remove();
            if (json['combination'].length == 0) {
                $('#graph_loader').css('height', '80px');
                $('#flot-placeholder').html("");
                $('#graph_loader').prepend('<div class="no_data">' + no_data + '</div>');
            }
            else {
                $('#graph_loader').css('height', '300px');
                $('.no_data').remove();
                drawProductChart(json);
            }
        }
    });
}

function drawProductChart(json)
{
    var dataset = [], ticks = [];
    var temp = [], comb = [];
    var i;


    var bar_property = {order: i + 1, lineWidth: 0};
    for (i = 0; i < json['combination'].length; i++)
    {
        temp.push([i, parseInt(json['count'][i])]);
        comb.push([json['combination'][i]['label']]);
        var combination = {label: comb, data: temp, bars: bar_property};
        dataset.push(combination);
        var temp = [], comb = [];

    }
    for (d = 0; d < json['name'].length; d++) {
        ticks.push([d, json['name'][d]['name']]);
    }
    var options = {
        series: {
            grow: {active: true}
        },
        bars: {
            show: true,
            barWidth: 0.2,
            fill: 1,
            align: "center"
        },
        xaxis: {
            axisLabel: products,
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 16,
//                           axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 10,
            ticks: ticks,
            autoscaleMargin: 0.01,
            tickColor: "#fff",
        },
        yaxis: {
            axisLabel: customers,
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 15,
            axisLabelFontFamily: 'sans-serif',
            axisLabelPadding: 3,
            tickFormatter: function (v, axis) {
                return Math.round(v * 100) / 100;
            }
        },
        legend: {
            noColumns: 0,
            labelBoxBorderColor: null,
            position: "ne"
        },
        grid: {
            hoverable: true,
            borderWidth: 1,
            borderColor: '#EEEEEE',
            mouseActiveRadius: 10,
            backgroundColor: "#ffffff",
            axisMargin: 20
        }
    };

    $.plot($("#flot-placeholder"), dataset, options);

    var previousPoint = null, previousLabel = null;

    $("#flot-placeholder").on("plothover", function (event, pos, item) {
        if (item) {
            if ((previousLabel != item.series.label) || (previousPoint != item.dataIndex)) {
                previousPoint = item.dataIndex;
                previousLabel = item.series.label;
                $("#tooltip").remove();

                var x = Math.floor(item.datapoint[0]);
                if (x < 0) {
                    x = 0;
                }
                var y = item.datapoint[1];

                var color = item.series.color;

                showTooltip(item.pageX,
                    item.pageY,
                    color,
                    "<strong>" + item.series.label + "</strong><br>" + item.series.xaxis.ticks[x].label + " : <strong>" + y + "</strong>");
            }
        } else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });

    function showTooltip(x, y, color, contents) {
        $('<div id="tooltip">' + contents + '</div>').css({
            position: 'absolute',
            display: 'none',
            top: y - 40,
            left: x - 70,
            border: '1px solid ' + color,
            padding: '3px',
            'font-size': '11px',
            'border-radius': '5px',
            'background-color': '#fff',
            'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
            opacity: 0.9
        }).appendTo("body").fadeIn(200);
    }
}
function add_mcefile() {



    $(document).ready(function () {


        _tinyMCE = tinySetup({
            editor_selector: "velsof_email_content",
            theme_advanced_buttons1: "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
            theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",
            theme_advanced_buttons4: "styleprops,|,cite,abbr,acronym,del,ins,attribs,pagebreak",
            setup: function (ed) {


                ed.onKeyUp.add(function (ed, e) {
                    tinyMCE.triggerSave();
                    textarea = $('#' + ed.id);
                    max = textarea.parent('div').find('span.counter').attr('max');
                    if (max != 'none')
                    {
                        textarea_value = textarea.val();
                        count = stripHTML(textarea_value).length;
                        rest = max - count;
                        if (rest < 0)
                            textarea.parent('div').find('span.counter').html('<span style="color:red;">Maximum ' + max + ' characters : ' + rest + '</span>');
                        else
                            textarea.parent('div').find('span.counter').html(' ');
                    }
                });
            }
        });


    });
}
function change_tab(a, b) {
    if (version == 1.5) {
        $('.tab-page').removeClass('active');
        $('.tab-page').removeClass('selected');
    }
    $('.list-group-item').removeClass('active');
    $(a).addClass(' active');
    if (b == 1) {
        $('#general_form').show();
        $('#initial_form').hide();
        $('#final_form').hide();
        $('#analysis_form').hide();
        $('#email_marketing_form').hide();
        $('#low_stock_alert_form').hide();
    } else if (b == 2) {
        $('#general_form').hide();
        $('#initial_form').show();
        $('#final_form').hide();
        $('#analysis_form').hide();
        $('#email_marketing_form').hide();
        $('#low_stock_alert_form').hide();
//        $('#subscriber_list').hide();

    } else if (b == 3) {
        $('#general_form').hide();
        $('#initial_form').hide();
        $('#final_form').show();
        $('#analysis_form').hide();
        $('#list_viewers').show();
        $('#email_marketing_form').hide();
        $('#low_stock_alert_form').hide();
//        $('#subscriber_list').hide();

    } else if (b == 4) {
        $('#general_form').hide();
        $('#initial_form').hide();
        $('#final_form').hide();
        $('#analysis_form').hide();
        $('#list_viewers').hide();
        $('#email_marketing_form').hide();
        $('#low_stock_alert_form').show();
//        $('#subscriber_list').hide();

    } else if (b == 5) {
        $('#general_form').hide();
        $('#initial_form').hide();
        $('#final_form').hide();
        $('#analysis_form').hide();
        $('#list_viewers').hide();
        $('#email_marketing_form').show();
        $('#subscriber_list').hide();
        $('#low_stock_alert_form').hide();

    } else if (b == 7) {
        $('#general_form').hide();
        $('#initial_form').hide();
        $('#final_form').hide();
        $('#analysis_form').hide();
        $('#list_viewers').hide();
        $('#low_stock_alert_form').hide();
//        $('#subscriber_list').show();
    } else {
        $('#subscriber_list').hide();
        $('#general_form').hide();
        $('#initial_form').hide();
        $('#final_form').hide();
        $('#analysis_form').show();
        $('#list_viewers').show();
        $('#email_marketing_form').hide();
        $('#low_stock_alert_form').hide();
        if (times == 0) {
            getGraph();
        }


    }
}

var error_display = 0;
var error_display1 = 0;
var error_display2 = 0;
var error_display3 = 0;
var error_display4 = 0;
var error_display9 = 0;
var error_display10 = 0;
function validation_admin() {
    var error = false;
    $('.error_message').remove();
    var product_update_privacy_text_err = false;
    var product_update_privacy_URL_err = false;
    var product_update_privacy_check_URL_err = false;
    if ($('[id^="product_update[enable_gdpr_policy]_on"]').is(':checked') === true) {
        $("input[name^=product_update_gdpr_policy_text_]").each(function () {
            var product_update_privacy_text_mand = velovalidation.checkMandatory($(this));
            if (product_update_privacy_text_mand != true) {
                error = true;
                product_update_privacy_text_err = true;
            }
        });
        if (product_update_privacy_text_err) {
            $("input[name^=product_update_gdpr_policy_text_]").addClass('error_field');
            $("input[name^=product_update_gdpr_policy_text_]").after('<span class="error_message">' + check_for_all_lang + '</span>');
        }
        $("input[name^=product_update_gdpr_policy_url]").each(function () {
            var product_update_gdpr_policy_url_mand = velovalidation.checkMandatory($(this));
            if (product_update_gdpr_policy_url_mand != true) {
                error = true;
                product_update_privacy_URL_err = true;
            }
        });
        if (product_update_privacy_URL_err) {
            $("input[name^=product_update_gdpr_policy_url_]").addClass('error_field');
            $("input[name^=product_update_gdpr_policy_url_]").after('<span class="error_message">' + check_for_all_lang + '</span>');
        }
        if (!product_update_privacy_URL_err) {
            $("input[name^=product_update_gdpr_policy_url]").each(function () {
                var product_update_gdpr_policy_check_url = velovalidation.checkUrl($(this));
                if (product_update_gdpr_policy_check_url != true) {
                    error = true;
                    product_update_privacy_check_URL_err = true;
                }
            });
            if (product_update_privacy_check_URL_err) {
                $("input[name^=product_update_gdpr_policy_url_]").addClass('error_field');
                $("input[name^=product_update_gdpr_policy_url_]").after('<span class="error_message">' + check_url_for_all_lang + '</span>');
            }
        }
    }



    // validation for subscription page
    if ($('[id^="product_update[enable_subscription_list]_on"]').is(':checked') === true) {
        var subscription_per_page = velovalidation.checkMandatory($('input[name="product_update[subscription_per_page]"]'));
        if (subscription_per_page != true)
        {
            error = true;
            $('input[name="product_update[subscription_per_page]"]').addClass('error_field');
            $('input[name="product_update[subscription_per_page]"]').parent().after('<span class="error_message">' + empty_field + '</span>');
        } else {
            var subscription_per_page = velovalidation.isNumeric($('input[name="product_update[subscription_per_page]"]'), true);
            if (subscription_per_page != true)
            {
                error = true;
                $('input[name="product_update[subscription_per_page]"]').addClass('error_field');
                $('input[name="product_update[subscription_per_page]"]').parent().after('<span class="error_message">' + kb_numeric + '</span>');
            } else if ($('input[name="product_update[subscription_per_page]"]').val() == 0) {
                error = true;
                $('input[name="product_update[subscription_per_page]"]').parent().addClass('error_field');
                $('input[name="product_update[subscription_per_page]"]').after('<span class="error_message">' + kb_numeric + '</span>');
            }
        }
    }
    if ($('[id^="product_update[enable_low_stock_alert]_on"]').is(':checked') === true) {
        var low_stock_alert_quantity = velovalidation.checkMandatory($('input[name="product_update[low_stock_alert_quantity]"]'));
        if (low_stock_alert_quantity != true)
        {
            error = true;
            $('input[name="product_update[low_stock_alert_quantity]"]').addClass('error_field');
            $('input[name="product_update[low_stock_alert_quantity]"]').parent().after('<span class="error_message">' + empty_field + '</span>');
        } else {
            var low_stock_alert_quantity = velovalidation.isNumeric($('input[name="product_update[low_stock_alert_quantity]"]'), true);
            if (low_stock_alert_quantity != true)
            {
                error = true;
                $('input[name="product_update[low_stock_alert_quantity]"]').addClass('error_field');
                $('input[name="product_update[low_stock_alert_quantity]"]').parent().after('<span class="error_message">' + kb_numeric_low_stock + '</span>');
            } else if ($('input[name="product_update[low_stock_alert_quantity]"]').val() == 0) {
                error = true;
                $('input[name="product_update[low_stock_alert_quantity]"]').parent().addClass('error_field');
                $('input[name="product_update[low_stock_alert_quantity]"]').after('<span class="error_message">' + kb_numeric_low_stock + '</span>');
            }
        }
    }
    // changes over
    /*Knowband validation start*/
    var validate_background_color_mandatory = velovalidation.checkMandatory($('input[name="product_update[background]"]'));
    if (validate_background_color_mandatory != true) {
        error = true;
        $("input[name='product_update[background]']").closest('.form-group').find('.error_message').show();
        if (error_display < 1 && $("input[name='product_update[background]']").closest('.form-group').find('.error_message').length <= 0) {
            $('<p class="error_message"></p>').appendTo($("input[name='product_update[background]']").closest('.form-group'));
            error_display++;
        }
        $('input[name="product_update[background]"]').closest('.input-group').addClass('error_field');
        $('input[name="product_update[background]"]').closest('.form-group').find('.error_message').html(validate_background_color_mandatory);

    } else {
        var validate_background_color = velovalidation.isColor($('input[name="product_update[background]"]'));
        var validate_background_color_tags = velovalidation.checkHtmlTags($('input[name="product_update[background]"]'));
        if (validate_background_color_tags != true) {
            error = true;
            $("input[name='product_update[background]']").closest('.form-group').find('.error_message').show();
            if (error_display1 < 1 && $("input[name='product_update[background]']").closest('.form-group').find('.error_message').length <= 0) {
                $('<p class="error_message"></p>').appendTo($("input[name='product_update[background]']").closest('.form-group'));
                error_display1++;
            }
            $('input[name="product_update[background]"]').closest('.input-group').addClass('error_field');
            $('input[name="product_update[background]"]').closest('.form-group').find('.error_message').html(validate_background_color_tags);
        }
        else if (validate_background_color != true) {
            error = true;
            $("input[name='product_update[background]']").closest('.form-group').find('.error_message').show();
            if (error_display1 < 1 && $("input[name='product_update[background]']").closest('.form-group').find('.error_message').length <= 0) {
                $('<p class="error_message"></p>').appendTo($("input[name='product_update[background]']").closest('.form-group'));
                error_display1++;
            }
            $('input[name="product_update[background]"]').closest('.input-group').addClass('error_field');
            $('input[name="product_update[background]"]').closest('.form-group').find('.error_message').html(validate_background_color);
        } else {
            $('input[name="product_update[background]"]').closest('.input-group').removeClass('error_field');
            $('input[name="product_update[background]"]').closest('.form-group').find('.error_message').hide();
        }
    }
    /*Knowband validation end*/

    /*Knowband validation start*/
    var validate_css = velovalidation.checkTags($('textarea[name="kb_backinstock_css'));
    if (validate_css != true) {
        error = true;
        $("textarea[name='kb_backinstock_css']").closest('div').find('.error_message').show();
        if (error_display9 < 1 && $("textarea[name='kb_backinstock_css']").closest('div').find('.error_message').length <= 0) {
            $('textarea[name="kb_backinstock_css"]').after($('<p class="error_message"></p>'));
            error_display9++;
        }
        $('textarea[name="kb_backinstock_css"]').addClass('error_field');
        $('textarea[name="kb_backinstock_css"]').closest('div').find('.error_message').html(validate_css);

    } else if ($('textarea[name="kb_backinstock_css').val().trim.length > 10000) {
        error = true;
        $("textarea[name='kb_backinstock_css']").closest('div').find('.error_message').show();
        if (error_display9 < 1 && $("textarea[name='kb_backinstock_css']").closest('div').find('.error_message').length <= 0) {
            $('textarea[name="kb_backinstock_css"]').after($('<p class="error_message"></p>'));
            error_display9++;
        }
        $('textarea[name="kb_backinstock_css"]').addClass('error_field');
        $('textarea[name="kb_backinstock_css"]').closest('div').find('.error_message').html(error_length_message);
    } else {
        $('textarea[name="kb_backinstock_css"]').removeClass('error_field');
        $('textarea[name="kb_backinstock_css"]').closest('div').find('.error_message').hide();
    }
    /*Knowband validation end*/
    /*Knowband validation start*/
    var validate_js = velovalidation.checkTags($('textarea[name="kb_backinstock_js'));
    if (validate_js != true) {
        error = true;
        $("textarea[name='kb_backinstock_js']").closest('div').find('.error_message').show();
        if (error_display10 < 1 && $("textarea[name='kb_backinstock_js']").closest('div').find('.error_message').length <= 0) {
            $('textarea[name="kb_backinstock_js"]').after($('<p class="error_message"></p>'));
            error_display10++;
        }
        $('textarea[name="kb_backinstock_js"]').addClass('error_field');
        $('textarea[name="kb_backinstock_js"]').closest('div').find('.error_message').html(validate_css);

    } else if ($('textarea[name="kb_backinstock_js').val().trim.length > 10000) {
        error = true;
        $("textarea[name='kb_backinstock_js']").closest('div').find('.error_message').show();
        if (error_display10 < 1 && $("textarea[name='kb_backinstock_js']").closest('div').find('.error_message').length <= 0) {
            $('textarea[name="kb_backinstock_js"]').after($('<p class="error_message"></p>'));
            error_display10++;
        }
        $('textarea[name="kb_backinstock_js"]').addClass('error_field');
        $('textarea[name="kb_backinstock_js"]').closest('div').find('.error_message').html(error_length_message);
    } else {
        $('textarea[name="kb_backinstock_js"]').removeClass('error_field');
        $('textarea[name="kb_backinstock_js"]').closest('div').find('.error_message').hide();
    }
    /*Knowband validation end*/

    /*Knowband validation start*/
    var validate_border_color_mandatory = velovalidation.checkMandatory($('input[name="product_update[border]"]'));
    if (validate_border_color_mandatory != true) {
        error = true;
        $("input[name='product_update[border]']").closest('.form-group').find('.error_message').show();
        if (error_display2 < 1 && $("input[name='product_update[border]']").closest('.form-group').find('.error_message').length <= 0) {
            $('<p class="error_message"></p>').appendTo($("input[name='product_update[border]']").closest('.form-group'));
            error_display2++;
        }
        $('input[name="product_update[border]"]').closest('.input-group').addClass('error_field');
        $('input[name="product_update[border]"]').closest('.form-group').find('.error_message').html(validate_border_color_mandatory);

    } else {
        var validate_border_color = velovalidation.isColor($('input[name="product_update[border]"]'));
        var validate_border_color_tags = velovalidation.checkHtmlTags($('input[name="product_update[border]"]'));
        if (validate_border_color_tags != true) {
            error = true;
            $("input[name='product_update[border]']").closest('.form-group').find('.error_message').show();
            if (error_display3 < 1 && $("input[name='product_update[border]']").closest('.form-group').find('.error_message').length <= 0) {
                $('<p class="error_message"></p>').appendTo($("input[name='product_update[border]']").closest('.form-group'));
                error_display3++;
            }
            $('input[name="product_update[border]"]').closest('.input-group').addClass('error_field');
            $('input[name="product_update[border]"]').closest('.form-group').find('.error_message').html(validate_border_color_tags);
        }
        else if (validate_border_color != true) {
            error = true;
            $("input[name='product_update[border]']").closest('.form-group').find('.error_message').show();
            if (error_display3 < 1 && $("input[name='product_update[border]']").closest('.form-group').find('.error_message').length <= 0) {
                $('<p class="error_message"></p>').appendTo($("input[name='product_update[border]']").closest('.form-group'));
                error_display3++;
            }
            $('input[name="product_update[border]"]').closest('.input-group').addClass('error_field');
            $('input[name="product_update[border]"]').closest('.form-group').find('.error_message').html(validate_border_color);
        } else {
            $('input[name="product_update[border]"]').closest('.input-group').removeClass('error_field');
            $('input[name="product_update[border]"]').closest('.form-group').find('.error_message').hide();
        }
    }
    /*Knowband validation end*/

    /*Knowband validation start*/
    var validate_text_color_mandatory = velovalidation.checkMandatory($('input[name="product_update[text]"]'));
    if (validate_text_color_mandatory != true) {
        error = true;
        $("input[name='product_update[text]']").closest('.form-group').find('.error_message').show();
        if (error_display4 < 1 && $("input[name='product_update[text]']").closest('.form-group').find('.error_message').length <= 0) {
            $('<p class="error_message"></p>').appendTo($("input[name='product_update[text]']").closest('.form-group'));
            error_display4++;
        }
        $('input[name="product_update[text]"]').closest('.input-group').addClass('error_field');
        $('input[name="product_update[text]"]').closest('.form-group').find('.error_message').html(validate_text_color_mandatory);

    } else {
        var validate_text_color = velovalidation.isColor($('input[name="product_update[text]"]'));
        var validate_text_color_tags = velovalidation.checkHtmlTags($('input[name="product_update[text]"]'));
        if (validate_text_color_tags != true) {
            error = true;
            $("input[name='product_update[text]']").closest('.form-group').find('.error_message').show();
            if (error_display4 < 1 && $("input[name='product_update[text]']").closest('.form-group').find('.error_message').length <= 0) {
                $('<p class="error_message"></p>').appendTo($("input[name='product_update[text]']").closest('.form-group'));
                error_display4++;
            }
            $('input[name="product_update[text]"]').closest('.input-group').addClass('error_field');
            $('input[name="product_update[text]"]').closest('.form-group').find('.error_message').html(validate_text_color_tags);
        }
        else if (validate_text_color != true) {
            error = true;
            $("input[name='product_update[text]']").closest('.form-group').find('.error_message').show();
            if (error_display4 < 1 && $("input[name='product_update[text]']").closest('.form-group').find('.error_message').length <= 0) {
                $('<p class="error_message"></p>').appendTo($("input[name='product_update[text]']").closest('.form-group'));
                error_display4++;
            }
            $('input[name="product_update[text]"]').closest('.input-group').addClass('error_field');
            $('input[name="product_update[text]"]').closest('.form-group').find('.error_message').html(validate_text_color);
        } else {
            $('input[name="product_update[text]"]').closest('.input-group').removeClass('error_field');
            $('input[name="product_update[text]"]').closest('.form-group').find('.error_message').hide();
        }
    }

    if (error == true) {
        return false;
    } else {
        return true;
    }
}

var error_display8 = 0;
var error_display9 = 0;
function validation_email_final_settings() {
    var error = false;
    /*Knowband validation start*/
    var validate_subject_mandatory = velovalidation.checkMandatory($('input[name="velocity_email_template[subject_final]"]'));
    if (validate_subject_mandatory != true) {
        error = true;
        $("input[name='velocity_email_template[subject_final]']").closest('div').find('.error_message').show();
        if (error_display8 < 1 && $("input[name='velocity_email_template[subject]']").closest('div').find('.error_message').length <= 0) {
            $('input[name="velocity_email_template[subject_final]"]').after($('<p class="error_message"></p>'));
            error_display8++;
        }
        $('input[name="velocity_email_template[subject_final]"]').addClass('error_field');
        $('input[name="velocity_email_template[subject_final]"]').closest('div').find('.error_message').html(validate_subject_mandatory);

    } else {
//        var validate_username = velovalidation.checkUsername($('input[name="velocity_email_template[subject]"]'));
        var validate_subject_tags = velovalidation.checkHtmlTags($('input[name="velocity_email_template[subject_final]"]'));
        if (validate_subject_tags != true) {
            error = true;
            $("input[name='velocity_email_template[subject_final]']").closest('div').find('.error_message').show();
            if (error_display8 < 1 && $("input[name='velocity_email_template[subject_final]']").closest('div').find('.error_message').length <= 0) {
                $('input[name="velocity_email_template[subject_final]"]').after($('<p class="error_message"></p>'));
                error_display8++;
            }
            $('input[name="velocity_email_template[subject_final]"]').addClass('error_field');
            $('input[name="velocity_email_template[subject_final]"]').closest('div').find('.error_message').html(validate_subject_tags);
        }
        else {
            $('input[name="velocity_email_template[subject_final]"]').removeClass('error_field');
            $('input[name="velocity_email_template[subject_final]"]').closest('div').find('.error_message').hide();
        }
    }
    /*Knowband validation end*/
//    console.log(tinyMCE.get('velsof_template_content_final').getContent());
    /*Knowband validation start*/
    var validate_template_mandatory = CheckMandonly(tinyMCE.get('velsof_template_content_final').getContent());
    if (validate_template_mandatory != true) {
        error = true;
        $("textarea[name='velocity_email_template[content_drop]']").closest('div').find('.error_message').show();
        if (error_display9 < 1 && $("textarea[name='velocity_email_template[content_drop]']").closest('div').find('.error_message').length <= 0) {
            $("textarea[name='velocity_email_template[content_drop]']").after($('<p class="error_message"></p>'));
            error_display9++;
        }
        $('#mce_74').addClass('error_field');
        $('textarea[name="velocity_email_template[content_drop]"]').closest('div').find('.error_message').html(validate_template_mandatory);

    }
    else {
        $('#mce_74').removeClass('error_field');
        $('textarea[name="velocity_email_template[content_drop]"]').closest('div').find('.error_message').hide();
    }
    /*Knowband validation end*/



    if (error == true) {
        return false;
    } else {
        return true;
    }
}
var error_display6 = 0;
var error_display7 = 0;
function validation_email_initial_settings() {
    var error = false;
    /*Knowband validation start*/
    var validate_subject_mandatory = velovalidation.checkMandatory($('input[name="velocity_email_template[subject]"]'));
    if (validate_subject_mandatory != true) {
        error = true;
        $("input[name='velocity_email_template[subject]']").closest('div').find('.error_message').show();
        if (error_display6 < 1 && $("input[name='velocity_email_template[subject]']").closest('div').find('.error_message').length <= 0) {
            $('input[name="velocity_email_template[subject]"]').after($('<p class="error_message"></p>'));
            error_display6++;
        }
        $('input[name="velocity_email_template[subject]"]').addClass('error_field');
        $('input[name="velocity_email_template[subject]"]').closest('div').find('.error_message').html(validate_subject_mandatory);

    } else {
//        var validate_username = velovalidation.checkUsername($('input[name="velocity_email_template[subject]"]'));
        var validate_subject_tags = velovalidation.checkHtmlTags($('input[name="velocity_email_template[subject]"]'));
        if (validate_subject_tags != true) {
            error = true;
            $("input[name='velocity_email_template[subject]']").closest('div').find('.error_message').show();
            if (error_display6 < 1 && $("input[name='velocity_email_template[subject]']").closest('div').find('.error_message').length <= 0) {
                $('input[name="velocity_email_template[subject]"]').after($('<p class="error_message"></p>'));
                error_display6++;
            }
            $('input[name="velocity_email_template[subject]"]').addClass('error_field');
            $('input[name="velocity_email_template[subject]"]').closest('div').find('.error_message').html(validate_subject_tags);
        }
        else {
            $('input[name="velocity_email_template[subject]"]').removeClass('error_field');
            $('input[name="velocity_email_template[subject]"]').closest('div').find('.error_message').hide();
        }
    }

    /*Knowband validation end*/

    /*Knowband validation start*/
    var validate_template_mandatory = CheckMandonly(tinyMCE.get('velsof_template_content').getContent());
    if (validate_template_mandatory != true) {
        error = true;
        $("textarea[name='velocity_email_template[content]']").closest('div').find('.error_message').show();
        if (error_display7 < 1 && $("textarea[name='velocity_email_template[content]']").closest('div').find('.error_message').length <= 0) {
            $("textarea[name='velocity_email_template[content]']").after($('<p class="error_message"></p>'));
            error_display7++;
        }
        $('#mce_34').addClass('error_field');
        $('textarea[name="velocity_email_template[content]"]').closest('div').find('.error_message').html(validate_template_mandatory);

    }
    else {
        $('#mce_34').removeClass('error_field');
        $('textarea[name="velocity_email_template[content]"]').closest('div').find('.error_message').hide();
    }
    /*Knowband validation end*/



    if (error == true) {
        return false;
    } else {
        return true;
    }
}

function validation_email_low_stock_settings() {
    var error = false;
    /*Knowband validation start*/
    var validate_subject_mandatory = velovalidation.checkMandatory($('input[name="velocity_low_stock_alert_setting[subject]"]'));
    if (validate_subject_mandatory != true) {
        error = true;
        $("input[name='velocity_low_stock_alert_setting[subject]']").closest('div').find('.error_message').show();
        if (error_display6 < 1 && $("input[name='velocity_low_stock_alert_setting[subject]']").closest('div').find('.error_message').length <= 0) {
            $('input[name="velocity_low_stock_alert_setting[subject]"]').after($('<p class="error_message"></p>'));
            error_display6++;
        }
        $('input[name="velocity_low_stock_alert_setting[subject]"]').addClass('error_field');
        $('input[name="velocity_low_stock_alert_setting[subject]"]').closest('div').find('.error_message').html(validate_subject_mandatory);

    } else {
//        var validate_username = velovalidation.checkUsername($('input[name="velocity_low_stock_alert_setting[subject]"]'));
        var validate_subject_tags = velovalidation.checkHtmlTags($('input[name="velocity_low_stock_alert_setting[subject]"]'));
        if (validate_subject_tags != true) {
            error = true;
            $("input[name='velocity_low_stock_alert_setting[subject]']").closest('div').find('.error_message').show();
            if (error_display6 < 1 && $("input[name='velocity_low_stock_alert_setting[subject]']").closest('div').find('.error_message').length <= 0) {
                $('input[name="velocity_low_stock_alert_setting[subject]"]').after($('<p class="error_message"></p>'));
                error_display6++;
            }
            $('input[name="velocity_low_stock_alert_setting[subject]"]').addClass('error_field');
            $('input[name="velocity_low_stock_alert_setting[subject]"]').closest('div').find('.error_message').html(validate_subject_tags);
        }
        else {
            $('input[name="velocity_low_stock_alert_setting[subject]"]').removeClass('error_field');
            $('input[name="velocity_low_stock_alert_setting[subject]"]').closest('div').find('.error_message').hide();
        }
    }

    /*Knowband validation end*/

    /*Knowband validation start*/
    var validate_template_mandatory = CheckMandonly(tinyMCE.get('velocity_low_stock_alert_setting_template_content').getContent());
    if (validate_template_mandatory != true) {
        error = true;
        $("textarea[name='velocity_low_stock_alert_setting[content]']").closest('div').find('.error_message').show();
        if (error_display7 < 1 && $("textarea[name='velocity_low_stock_alert_setting[content]']").closest('div').find('.error_message').length <= 0) {
            $("textarea[name='velocity_low_stock_alert_setting[content]']").after($('<p class="error_message"></p>'));
            error_display7++;
        }
//        $('#mce_34').addClass('error_field');
        $('textarea[name="velocity_low_stock_alert_setting[content]"]').closest('div').find('.error_message').html(validate_template_mandatory);

    }
    else {
//        $('#mce_34').removeClass('error_field');
        $('textarea[name="velocity_low_stock_alert_setting[content]"]').closest('div').find('.error_message').hide();
    }
    /*Knowband validation end*/



    if (error == true) {
        return false;
    } else {
        return true;
    }
}

function validation_email_marketing_admin() {

    var error = false;
    $('.error_message').remove();
    /*Knowband validation end*/
    var email_marketing_tab = 0;
    if ($('[id^="back_stock_email[mailchimp_status]_on"]').is(':checked') === true) {
        //  alert('test');
        var mailchimp_api_mand = velovalidation.checkMandatory($("input[name='back_stock_email[mailchimp_api]']"));
        var list_val = $("[name='back_stock_email[mailchimp_list]']").val();
        if (mailchimp_api_mand !== true) {
            error = true;

            $("input[name='back_stock_email[mailchimp_api]']").addClass('error_field');
            $("input[name='back_stock_email[mailchimp_api]']").after($('<p class="mailchimp_api_mand error_message"></p>'));
            $('.mailchimp_api_mand').html(mailchimp_api_mand);
            email_marketing_tab = 1;
        } else if (list_val == 'no_list') {
            error = true;

            $("input[name='back_stock_email[mailchimp_api]']").addClass('error_field');
            $("input[name='back_stock_email[mailchimp_api]']").after($('<p class="mailchimp_api_mand error_message"></p>'));
            $('.mailchimp_api_mand').html(no_list_mailchimp);
            email_marketing_tab = 1;
        }
    }
    if ($('[id^="back_stock_email[klaviyo_status]_on"]').is(':checked') === true) {
        //  alert('test');
        var klaviyo_api_mand = velovalidation.checkMandatory($("input[name='back_stock_email[klaviyo_api]']"));
        var list_val_ka = $("[name='back_stock_email[klaviyo_list]']").val();
        if (klaviyo_api_mand !== true) {
            error = true;

            $("input[name='back_stock_email[klaviyo_api]']").addClass('error_field');
            $("input[name='back_stock_email[klaviyo_api]']").after($('<p class="klaviyo_api_mand error_message"></p>'));
            $('.klaviyo_api_mand').html(klaviyo_api_mand);
            email_marketing_tab = 1;
        } else if (list_val_ka == 'no_list') {
            error = true;

            $("input[name='back_stock_email[klaviyo_api]']").addClass('error_field');
            $("input[name='back_stock_email[klaviyo_api]']").after($('<p class="klaviyo_api_mand error_message"></p>'));
            $('.klaviyo_api_mand').html(no_list_mailchimp);
            email_marketing_tab = 1;
        }
    }
    if ($('[id^="back_stock_email[SendinBlue_status]_on"]').is(':checked') === true) {
        //  alert('test');
        var getresponse_api_mand = velovalidation.checkMandatory($("input[name='back_stock_email[SendinBlue_api]']"));
        var list_val = $("[name='back_stock_email[SendinBlue_list]']").val();
        if (getresponse_api_mand !== true) {
            error = true;

            $("input[name='back_stock_email[SendinBlue_api]']").addClass('error_field');
            $("input[name='back_stock_email[SendinBlue_api]']").after($('<p class="SendinBlue_api error_message"></p>'));
            $('.SendinBlue_api').html(getresponse_api_mand);
            email_marketing_tab = 1;
        } else if (list_val == 'no_list') {
            error = true;

            $("input[name='back_stock_email[SendinBlue_api]']").addClass('error_field');
            $("input[name='back_stock_email[SendinBlue_api]']").after($('<p class="SendinBlue_api error_message"></p>'));
            $('.SendinBlue_api').html(no_list_mailchimp);
            email_marketing_tab = 1;
        }
    }
    if (error == true) {
        return false;
    } else {
        return true;
    }

}

function CheckMandonly(val) {
    var val = val.trim();
    var return_val = true;
    if (val == '') {
        return_val = empty_field;
    }
    return return_val;

}