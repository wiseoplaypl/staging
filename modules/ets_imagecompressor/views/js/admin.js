/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
var ajaxPercentImageOptimize=false;
var ajaxPercentAllImageOptimize=false;
var ajaxPercentAllImageOptimizeDashboard=false;
var editor_script=false;
var total_optimize_images=0;
$(document).ready(function(){
    $(document).on('click','.sp_cleaner_image',function(e){
        e.preventDefault();
        if(!$(this).hasClass('loading'))
        {
            if(!confirm(confirm_delete_unused_images)){
                return false;
            }
            $(this).addClass('loading');
            var $this = $(this);
            $.ajax({
                url: url_imagecompressor_ajax,
                data: {
                    btnSubmitCleaneImageUnUsed:1,
                    unused_category_images : $('input[name="unused_category_images"]').length ? 1 :0,
                    unused_supplier_images : $('input[name="unused_supplier_images"]').length ? 1 :0,
                    unused_manufacturer_images : $('input[name="unused_manufacturer_images"]').length ? 1 :0,
                    unused_product_images : $('input[name="unused_product_images"]').length ? 1 :0,
                },
                type: 'post',
                dataType: 'json',
                success: function(json){
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                        $('.form_cache_page.image_cleaner').html('<div class="alert alert-info">'+no_image_unused+'</div>');
                    }
                    if(json.errors)
                        $.growl.error({message:json.errors});
                    $(this).removeClass('loading');
                },
                error: function(xhr, status, error)
                {
                    $(this).removeClass('loading');
                }
            });
        }

    });
    $(document).on('click','.image_upload_otpimize_quality',function(){
        $('.popup-optimize_image_upload').addClass('show');
        ets_sp_change_range($('#ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_UPLOAD'));
        ets_sp_change_range($('#ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_BROWSE'));
    });
    $(document).on('click','button[name="btnSubmitImageCompressorException"]',function(e){
        e.preventDefault(); 
        if(!$(this).hasClass('loading'))
        {
            $(this).addClass('loading');
            var $this = $(this);
            $.ajax({
                url: '',
                data: {
                    btnSubmitImageCompressorException:1,
                    ETS_IMGCOMPRESSOR_PAGES_EXCEPTION: $('#ETS_IMGCOMPRESSOR_PAGES_EXCEPTION').val(),
                },
                type: 'post',
                dataType: 'json',
                success: function(json){
                    $this.removeClass('loading');
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                    }
                    if(json.errors)
                        $.growl.error({message:json.errors});
                },
                error: function(xhr, status, error)
                {     
                    $this.removeClass('loading');
                }
            });
        }
    });
    $('.sp_button-group').parent().removeClass('col-lg-9').removeClass('col-lg-offset-3');
    if($('.config_tab_image_old').length)
    {
        $('.confi_tab.config_tab_image_old').addClass('active');
        $('.form_cache_page').hide();
        $('.form_cache_page.'+$('.confi_tab.active').attr('data-tab-id')).show();
        $('button[name="btnSubmitNewImageOptimize"]').hide();
        $('button[name="btnSubmitImageOptimize"]').show();
        $('button[name="btnSubmitLazyLoadImage"]').hide();
        if(total_images>0)
        {
            $('button[name="btnSubmitImageOptimize"]').removeAttr('disabled');
        }
        else
            $('button[name="btnSubmitImageOptimize"]').attr('disabled','disabled');
    }
    $(document).on('click','.add_api_key',function(e){
       e.preventDefault(); 
       $(this).prev().append('<div class="input-inline"><input type="text" name="ETS_IMGCOMPRESSOR_API_TYNY_KEY[]" value="" placeholder="'+tiny_label+'"/><button class="delete_api_key"><i class="icon icon-trash"></i></button></div>');
       $('.delete_api_key').show();
    });
    $(document).on('click','.delete_api_key',function(e){
       e.preventDefault();
       $(this).parent().remove(); 
       if($('.delete_api_key').length==1)
            $('.delete_api_key').hide();
    });
    $(document).keyup(function(e) { 
        if(e.keyCode == 27) {
            $('.confirm-popup').removeClass('show');
            $('.popup-optimize_image_upload').removeClass('show');
        }
    });
    $(document).on('click','.btn-cancel',function(e){
       e.preventDefault();
       $('.popup-optimize_image_upload').removeClass('show'); 
    });
    $(document).mouseup(function (e)
    {
        var confirm_popup=$('.confirm-popup .popup-content');
        if (!confirm_popup.is(e.target)&& confirm_popup.has(e.target).length === 0)
        {
            $('.confirm-popup').removeClass('show');
        }
        if (!$('.popup-optimize_image_upload .popup-content').is(e.target)&& $('.popup-optimize_image_upload .popup-content').has(e.target).length === 0)
        {
            $('.popup-optimize_image_upload').removeClass('show');
        }
        if (!$('.popup-optimize_image_upload .popup-content').is(e.target)&& $('.popup-optimize_image_upload .popup-content').has(e.target).length === 0)
        {
            $('.popup-optimize_image_upload').removeClass('show');
        }
    });
    sp_displayTynyPNG();
    $('#ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT,#ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_BROWSE,#ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_UPLOAD').change(function(){
        sp_displayTynyPNG();
    });
    $('.confi_tab').click(function(){
        $('.popup_run').show();
        if(!$(this).hasClass('active'))
        {
            $('.form_cache_page').hide();
            $('.confi_tab').removeClass('active');
            $('.form_cache_page.'+$(this).attr('data-tab-id')).show();
            $(this).addClass('active');
			if($('input[type="range"]').length>0)
			{
				$('input[type="range"]').each(function(){
					ets_sp_change_range($(this));
				});
			}
            if($('.config_tab_image_old').length)
            {
                if($(this).attr('data-tab-id')=='image_new')
                {
                    $('button[name="btnSubmitNewImageOptimize"]').show();
                    $('button[name="btnSubmitImageOptimize"]').hide();
                    $('button[name="btnSubmitLazyLoadImage"]').hide();
                    $('button[name="btnSubmitSaveOptimizeImage"]').hide();
                }
                else if($(this).attr('data-tab-id')=='image_old')
                {
                    $('button[name="btnSubmitNewImageOptimize"]').hide();
                    $('button[name="btnSubmitImageOptimize"]').show();
                    $('button[name="btnSubmitSaveOptimizeImage"]').show();
                    $('button[name="btnSubmitLazyLoadImage"]').hide();
                }
                else if($(this).attr('data-tab-id')=='image_lazy_load')
                {
                    $('button[name="btnSubmitNewImageOptimize"]').hide();
                    $('button[name="btnSubmitImageOptimize"]').hide();
                    $('button[name="btnSubmitLazyLoadImage"]').show();
                    $('button[name="btnSubmitSaveOptimizeImage"]').hide();
                }
                else
                {
                    $('button[name="btnSubmitNewImageOptimize"]').hide();
                    $('button[name="btnSubmitImageOptimize"]').hide();
                    $('button[name="btnSubmitLazyLoadImage"]').hide();
                    $('button[name="btnSubmitSaveOptimizeImage"]').hide();
                } 
            }
            ets_sp_change_range($('input[name="ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE"]'));
        } 
        sp_displayTynyPNG();        
    });
    $(document).on('click','.sp_close',function(){
        if($('.popup-optimize_image_upload').length)
            $('.popup-optimize_image_upload').removeClass('show');
    });
    if(parseInt($('input[name="ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE"]').val())==100)
    {
        $('.form_cache_page.update_quality .col-lg-9').hide();
    }
    else
    {
        $('.form_cache_page.update_quality .col-lg-9').show();
    } 
    $(document).on('change','.checkbox_all input,.unoptimized_image input,input[name="ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE"],select[name="ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT"],#ETS_IMGCOMPRESSOR_UPDATE_QUALITY_1',function(){
        var formData = new FormData($('button[name="btnSubmitImageOptimize"]').parents('form').get(0));
        formData.append('changeSubmitImageOptimize', '1');
        stop_optimized = false;
        continue_optimize = false;
        var url_ajax= url_imagecompressor_ajax;
        if(parseInt($('input[name="ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE"]').val())==100)
        {
            $('button[name="btnSubmitImageOptimize"]').html('<i class="process-icon-cogs"></i>'+Restore_original_images_text);
            $('.form_cache_page.update_quality .col-lg-9').hide();
        }
        else
        {
            $('button[name="btnSubmitImageOptimize"]').html('<i class="process-icon-cogs"></i>'+Optimize_existing_images_text);
            $('.form_cache_page.update_quality .col-lg-9').show();
        }    
        $('button[name="btnSubmitImageOptimize"]').attr('disabled','disabled');
         $.ajax({
                url: url_ajax,
                data: formData,
                type: 'post',
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(json){
                    sp_displayInfoImageOptimize(json);
                },
                error: function(xhr, status, error)
                {            
                }
            });
        
    });
}); 
$(document).on('click','button[name="btnSubmitSaveOptimizeImage"],button[name="btnSaveOptimizeImageUpload"],button[name="btnSaveOptimizeImageBrowse"],button[name="btnSubmitLazyLoadImage"]',function(e){
    e.preventDefault();
    var name=$(this).attr('name');
    sp_submitFormAjax(name,$(this));
});
$(document).on('click','button[name="btnSubmitImageOptimize"]',function(e){
    e.preventDefault();
    if(confirm( parseInt($('input[name="ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE"]').val()) ==100 ? popup_restore_image.replace('total_images',total_images) :  popup_optimize_image.replace('total_images',total_images)))
    {
        if(stop_optimized)
        {
            stop_optimized=false;
        }
        else
        {
            optimize_type = 'products';
            limit_optimized =0;
            total_optimize_images = total_images;
        }
        continue_optimize=false;
        sp_ajaxOptimizeImage(false);
        setTimeout(function(){  ajaxPercentImageOptimize = setInterval(function(){ sp_ajaxPercentageImageOptimize() }, 1000); }, 2000);
    }
});
$(document).on('click','.optimize_all_images',function(e){
    e.preventDefault();
    $('.confirm-popup-optimize_all_images').addClass('show');
});
$(document).on('click','.btnSubmitImageOptimize,.confirm-popup-optimize_all_images-yes',function(e){
    $('.confirm-popup-optimize_all_images').removeClass('show');
    e.preventDefault();
    if(stop_optimized)
    {
        stop_optimized=false;
    }
    else
    {
        optimize_type = 'products';
        limit_optimized =0;
        total_optimize_images = total_need_optimized_images;
    }
    continue_optimize=false;
    sp_ajaxOptimizeAllImage(false);
    setTimeout(function(){  ajaxPercentAllImageOptimize = setInterval(function(){ sp_ajaxPercentageAllImageOptimize() }, 1000); }, 2000);
      
});
$(document).on('click','.optimize_pause,.optimize_stop',function(e){
    e.preventDefault();
    stop_optimized = true;
    if(ajaxPercentAllImageOptimize)
        clearInterval(ajaxPercentAllImageOptimize);
    if(ajaxPercentImageOptimize)
    {
        clearInterval(ajaxPercentImageOptimize);
    }
    if(ajaxPercentAllImageOptimizeDashboard)
        clearInterval(ajaxPercentAllImageOptimizeDashboard);
    if($(this).hasClass('optimize_pause'))
    {
        $(this).removeClass('optimize_pause').addClass('optimize_resume').html(resume_text);
        $(this).parents('.popup_optimizeing_wapper').addClass('popup_pause');
        if($('.list_optimized_images').length)
            $('.list_optimized_images').html('<li class="stop">'+optimize_pause+'</li>');
    }
    else
    {
        $('.popup_optimizeing_wapper').remove();
        $('.popup-configuration-cache').removeClass('show');
    }
});
$(document).on('click','.optimize_resume',function(e){
   e.preventDefault(); 
   stop_optimized = false;
   $(this).removeClass('optimize_resume').addClass('optimize_pause').html(pause_text);
   $(this).parents('.popup_optimizeing_wapper').removeClass('popup_pause');
   $('.list_optimized_images').html('');
   if($('.confirm-popup-optimize_all_images').length>0)
   {
        if($('.popup-configuration-cache').hasClass('show'))
        {
            sp_submitPageCacheDashboard(true);
			if($('input[name="optimize_existing_images"]').is(':checked'))
				ajaxPercentAllImageOptimizeDashboard = setInterval(function(){ sp_ajaxPercentageAllImageOptimizeDashboard(); }, 1000);
        }
        else
        {
            sp_ajaxOptimizeAllImage(true);
            ajaxPercentAllImageOptimize = setInterval(function(){ sp_ajaxPercentageAllImageOptimize() }, 1000);
        }
        
   }
   else
   {
        sp_ajaxOptimizeImage(true);
        ajaxPercentImageOptimize = setInterval(function(){ sp_ajaxPercentageImageOptimize() }, 1000);
   }
});
$(document).on('click','.optimize_continue',function(e){
    e.preventDefault();
    stop_optimized = false;
    continue_optimize=true;
    $('.optimize_resume').removeClass('optimize_resume').addClass('optimize_pause').html(pause_text);
    if($('.confirm-popup-optimize_all_images').length>0)
    {
        if($('.popup-configuration-cache').hasClass('show'))
        {
            sp_submitPageCacheDashboard(true);
			if($('input[name="optimize_existing_images"]').is(':checked'))
				ajaxPercentAllImageOptimizeDashboard = setInterval(function(){ sp_ajaxPercentageAllImageOptimizeDashboard(); }, 1000);
        }
        else
        {
            sp_ajaxOptimizeAllImage(true);
            ajaxPercentAllImageOptimize = setInterval(function(){ sp_ajaxPercentageAllImageOptimize() }, 1000);
        }
        
    }
    else
    {
        sp_ajaxOptimizeImage(true);
        ajaxPercentImageOptimize = setInterval(function(){ sp_ajaxPercentageImageOptimize() }, 1000);
    }
});
$(document).on('click','.checkbox_all input',function(){
    if($(this).is(':checked'))
    {
        $(this).closest('.form-group').find('input').prop('checked',true);
    }
    else
    {
        $(this).closest('.form-group').find('input').removeAttr('checked');
    }
});
$(document).on('click','.checkbox input',function(){
    if($(this).is(':checked'))
    {
        if($(this).closest('.form-group').find('input:checked').length==$(this).closest('.form-group').find('input[type="checkbox"]').length-1)
             $(this).closest('.form-group').find('.checkbox_all input').prop('checked',true);
    }
    else
    {
        $(this).closest('.form-group').find('.checkbox_all input').removeAttr('checked');
    } 
});
$(document).ready(function(){
    if($('.image_old.blog_gallery').find('input:checked').length==$('.image_old.blog_gallery').find('input').length-1)
    {
        $('.image_old.blog_gallery').find('.checkbox_all input').prop('checked',true);
    }
    if($('.image_old.others').find('input:checked').length==$('.image_old.others').find('input').length-1)
    {
        $('.image_old.others').find('.checkbox_all input').prop('checked',true);
    }
    if($('.image_new.blog_gallery').find('input:checked').length==$('.image_new.blog_gallery').find('input').length-1)
    {
        $('.image_new.blog_gallery').find('.checkbox_all input').prop('checked',true);
    }
    $('input[type="range"]').each(function(){
        ets_sp_change_range($(this));
    });
    $('input[type="range"]').mousemove(function(){
        ets_sp_change_range($(this));
    });
});
function sp_displaySuccessMessage(msg)
{
    if($('form .bootstrap_sussec').length)
    {
        $('form .bootstrap_sussec').replaceWith(msg);
    }
    else
        $('form .form-wrapper').append(msg);
    setTimeout(function(){ $('form .bootstrap_sussec').remove(); }, 3000);
}
function sp_displayErrorMessage(msg)
{
    if($('form .form-wrapper').next('.module_error').length)
        $('form .form-wrapper').next('.module_error').replaceWith(msg);
    else
        $('form .form-wrapper').after(msg);
}
function sp_ajaxOptimizeImage(resume)
{
    if(stop_optimized)
        return true;
    var formData = new FormData($('button[name="btnSubmitImageOptimize"]').parents('form').get(0));
    formData.append('btnSubmitImageOptimize', '1');
    formData.append('optimize_type', optimize_type);
    formData.append('limit_optimized', limit_optimized);
    if(resume)
    {
        formData.append('resume', resume);
    } 
    else
    {
       continue_optimize_webp =false;
       $('.list_optimized_images').html('');
    }
    if(continue_optimize_webp)
        formData.append('continue_webp',1);
    if(continue_optimize)
    {
        formData.append('continue', 1);
    }
    if($('.popup_error').length>0)
    {
        $('.popup_error').remove();
        $('.popup_run').show();
    }
    else
        $('.bootstrap .module_error').remove();
    var url_ajax= url_imagecompressor_ajax;
    var html = '<div class="popup_optimizeing_wapper"><div class="popup_optimizeing">';
        html += '<div class="popup-title"><h3>'+optimize_title_text+'</h3><span class="optimize_stop" title="Close">Close</span></div> <div class="popup_run"><div class="optimize-wapper-percent"><div class="percentage_optimize">0%</div></div>';
        html += '<div class="popup_optimizeing_pls">'+(parseInt($('input[name="ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE"]').val())==100 ? Restoring_text : Optimizing_text )+' '+total_images+' '+please_wait+'<span class="bacham">...</span></div>';
        html += '<div class="button-group"><button class="btn btn-default pull-left optimize_pause">'+pause_text+'</button><button class="btn btn-default pull-right optimize_stop">'+stop_text+'</button></div></div></div></div>';
    if(!$('#configuration_form .popup_optimizeing_wapper').length)
        $('#configuration_form .panel-footer').before(html);
    $.ajax({
        url: url_ajax,
        data: formData,
        type: 'post',
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(json){
            if(!json)
            {
                sp_ajaxOptimizeImage(true);
            }
            else
            {
                if(json.resume)
                {
                    optimize_type = json.optimize_type;
                    limit_optimized = json.limit_optimized;
                    sp_ajaxOptimizeImage(true);
                }
                if(json.errors)
                {
                    sp_displayErrorMessage(json.errors);
                    setTimeout(function(){ $('.popup_optimizeing_wapper').remove(); }, 500);
                    if(ajaxPercentImageOptimize)
                        clearInterval(ajaxPercentImageOptimize);
                }                
                if(json.success)
                {
                    sp_displaySuccessMessage(json.success);
                    sp_displayInfoImageOptimize(json);
                    $('#configuration_form .popup_optimizeing .optimize-wapper-percent').css('transition','all 1s ease 0s');
                    $('#configuration_form .popup_optimizeing .optimize-wapper-percent').css('width','100%');
                    $('#configuration_form .popup_optimizeing .percentage_optimize').html('100%');
                    setTimeout(function(){ $('.popup_optimizeing_wapper').remove(); }, 1500);
                    $('.list_optimized_images').html('<li class="stop"></li>');
                    if(ajaxPercentImageOptimize)
                    {
                        clearInterval(ajaxPercentImageOptimize);
                    }
                }
                if(json.error)
                {
                    if(ajaxPercentImageOptimize)
                        clearInterval(ajaxPercentImageOptimize);
                    if(!$('.popup_optimizeing_wapper .popup_error').length)
                        $('.popup_optimizeing_wapper .popup_run').before('<div class="popup_error"><p>'+popup_error+'</p>'+json.error+'<div class="popup_continue"><p>'+(json.script_continue=='php' ? continue_question : continue_question_webp)+'</p> <button class="btn btn-default optimize_continue">'+continue_text+'</button>  <button class="btn btn-default optimize_stop">'+no_continue_text+'</button></div></div>');
                    if(json.script_continue=='webp')
                        continue_optimize_webp=true;
                    else
                        continue_optimize_webp=false;
                    $('.popup_run').hide();
                }    
            }
        },
        error: function(xhr, status, error)
        {
            sp_ajaxOptimizeImage(true);              
        }
    });
}
function sp_ajaxOptimizeAllImage(resume)
{
    if(stop_optimized)
        return true;
    var url_ajax= url_imagecompressor_ajax;
    var html = '<div class="popup_optimizeing_wapper"><div class="popup_optimizeing"><div class="popup-title"><h3>'+optimize_title_text+'</h3><span class="optimize_stop" title="Close">Close</span></div><div class="popup_run">';
        html += '<div class="optimize-wapper-percent"><div class="percentage_optimize">0%</div></div>';
        html +='<div class="popup_optimizeing_pls">'+Optimizing_text+' '+total_need_optimized_images+' '+please_wait+'<span class="bacham">...</span></div>';
        html +='<div class="button-group"><button class="btn btn-default pull-left optimize_pause">'+pause_text+'</button><button class="btn btn-default pull-right optimize_stop">'+stop_text+'</button></div></div></div></div>';
    if($('.popup_error').length>0)
    {
        $('.popup_error').remove();
        $('.popup_run').show();
    }
    else
        $('.bootstrap .module_error').remove();
    if(!$('#configuration_form .popup_optimizeing_wapper').length)
        $('#configuration_form .panel-footer').before(html);
    if(!resume)
    {
        continue_optimize_webp=false;
        $('.list_optimized_images').html('');
    }
    $.ajax({
        url: url_ajax,
        data: 'btnSubmitImageAllOptimize=1&ajax=1&optimize_type='+optimize_type+'&limit_optimized='+limit_optimized+(resume ? '&resume=1':'')+(continue_optimize ? '&continue=1':'')+(continue_optimize_webp ? '&continue_webp=1':''),
        type: 'post',
        dataType: 'json',
        success: function(json){
            if(!json)
            {
                sp_ajaxOptimizeAllImage(true);
            }
            else
            {
                if(json.resume)
                {
                    optimize_type = json.optimize_type;
                    limit_optimized = json.limit_optimized;
                    sp_ajaxOptimizeAllImage(true);
                }
                if(json.errors)
                {
                    sp_displayErrorMessage(json.errors);
                    setTimeout(function(){ $('.popup_optimizeing_wapper').remove(); }, 500);
                    if(ajaxPercentAllImageOptimize)
                        clearInterval(ajaxPercentAllImageOptimize);
                }                
                if(json.success)
                {
                    sp_displaySuccessMessage(json.success);
                    sp_displayInfoImageOptimize(json);
                    $('#configuration_form .popup_optimizeing .optimize-wapper-percent').css('transition','all 1s ease 0s');
                    $('#configuration_form .popup_optimizeing .optimize-wapper-percent').css('width','100%');
                    $('#configuration_form .popup_optimizeing .percentage_optimize').html('100%');
                    $('.total_image_optimized').html(total_images +' '+images_optimized_text);
                    $('.total_image_optimized_size').html(json.total_size_save);
                    $('.list-chonse-configuration-auto input[name="optimize_existing_images"]').parents('li').remove();
                    $('.list_optimized_images').html('<li class="stop"></li>');
                    setTimeout(function(){ $('.popup_optimizeing_wapper').remove(); }, 1500);
                    if($('.optimize_all_images').length)
                        sp_updateDashboardChart(chart_image_optimize,false,[100,0]);
                    if(ajaxPercentAllImageOptimize)
                        clearInterval(ajaxPercentAllImageOptimize);
                }
                if(json.error)
                {
                    if(ajaxPercentAllImageOptimize)
                        clearInterval(ajaxPercentAllImageOptimize);
                    if(!$('.popup_optimizeing_wapper .popup_error').length)
                        $('.popup_optimizeing_wapper .popup_run').before('<div class="popup_error"><p>'+popup_error+'</p>'+json.error+'<div class="popup_continue"><p>'+(json.script_continue=='php' ? continue_question : continue_question_webp)+'</p> <button class="btn btn-default optimize_continue">'+continue_text+'</button>  <button class="btn btn-default optimize_stop">'+no_continue_text+'</button></div></div>');
                    if(json.script_continue=='webp')
                        continue_optimize_webp=true;
                    else
                        continue_optimize_webp=false;
                    $('.popup_run').hide();
                }
                    
            }
            
        },
        error: function(xhr, status, error)
        {
            sp_ajaxOptimizeAllImage(true);              
        }
    });
}
function sp_displayInfoImageOptimize(image)
{
    if(image.total_images>0)
    {
        $('.info_total_images').removeClass('hide');
        $('.info_total_images .total_images').html(image.total_images);
        $('button[name="btnSubmitImageOptimize"]').removeAttr('disabled');
    }
    else
    {
        $('button[name="btnSubmitImageOptimize"]').attr('disabled','disabled');
        $('.info_total_images').addClass('hide');
    }  
    if($('.unoptimized_image').length>0)  
        total_images = image.total_images;
    if($('label.unoptimized_image').length)
    {
       $('label.unoptimized_image').each(function(){
            var total_image_unoptimized = parseInt(image[$(this).attr('data-image')]);
            var total_image_optimized = parseInt(image[$(this).attr('data-image')+'_optimized']);
            if(total_image_unoptimized > 0)
            {
                if(total_image_optimized)
                    $(this).find('.total_unoptimized_image').html('<span class="alert-blue">'+total_image_optimized+' '+(image.quality_optimize == 100 ? restored_text :optimized_text)+'</span>, '+total_image_unoptimized+' '+(image.quality_optimize ==100 ? restorable_text : unoptimized_text));
                else
                    $(this).find('.total_unoptimized_image').html(total_image_unoptimized +' '+(image.quality_optimize ==100 ? restorable_text : unoptimized_text));
            }
            else
                $(this).find('.total_unoptimized_image').html('<span class="alert-blue">'+(image.quality_optimize==100 ? restored_succ_text: optimized_succ_text)+', </span>'+(image.quality_optimize==100 ? '<span>'+total_image_optimized+' '+unoptimized_text+'</span>' :'' ));
                
       });  
       if(image.check_optimize)
       {
            $('.congratulations_image_success').removeClass('hide');
            $('.congratulations_image_success .total_all_image_optimized').html(image.check_optimize);
            $('.congratulations_image_success .total_all_size_image_optimize').html(image.total_size_save);
            if(image.total_size_save)
                $('.total_all_size_image').removeClass('hide');
            else
                $('.total_all_size_image').addClass('hide');
       }
       else
            $('.congratulations_image_success').addClass('hide');
    }
}
function sp_ajaxPercentageImageOptimize()
{
    var formData = new FormData($('button[name="btnSubmitImageOptimize"]').parents('form').get(0));
    formData.append('getPercentageImageOptimize', '1');
    formData.append('total_optimize_images',total_optimize_images);
    formData.delete('btnSubmitImageOptimize');
    $.ajax({
        url: url_imagecompressor_ajax,
        data: formData,
        type: 'post',
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(json){
            if(json.percent>0 && json.percent<100)
            {
                $('#configuration_form .popup_optimizeing .optimize-wapper-percent').css('transition','all 3s ease 0s');
                $('#configuration_form .popup_optimizeing .optimize-wapper-percent').css('width',json.percent+'%');
                $('#configuration_form .popup_optimizeing .percentage_optimize').html(json.percent+'%');
                if(json.optimized_images)
                {
                    if($('.popup_optimizeing_wapper .popup_optimizeing .list_optimized_images').length==0)
                    {
                       $('.popup_optimizeing_wapper .popup_optimizeing .button-group').before('<ul class="list_optimized_images"></ul>');
                    }
                    for (image in json.optimized_images)
                    {
                        if($('.popup_optimizeing_wapper .popup_optimizeing .list_optimized_images li[data-image="'+json.optimized_images[image]['image']+'"]').length==0 && $('.list_optimized_images li.stop').length==0)
                        {
                            if($('.popup_optimizeing_wapper .popup_optimizeing .list_optimized_images li').length >=5)
                               $('.popup_optimizeing_wapper .popup_optimizeing .list_optimized_images li:first-child').remove(); 
                            $('.popup_optimizeing_wapper .popup_optimizeing .list_optimized_images').append('<li data-image="'+json.optimized_images[image]['image']+'">'+json.optimized_images[image]['image_cat']+'</li>');
                        }
                    }
                }
                if(json.image)
                    sp_displayInfoImageOptimize(json.image);
            }
            if(json.percent>=100 && ajaxPercentImageOptimize)
                clearInterval(ajaxPercentImageOptimize);
        },
    });
}
function sp_ajaxPercentageAllImageOptimize()
{
    $.ajax({
        url: url_imagecompressor_ajax,
        data: 'getPercentageAllImageOptimize=1&total_optimize_images='+total_optimize_images,
        type: 'post',
        dataType: 'json',
        success: function(json){
            if(json.percent>0 && json.percent<100)
            {
                $('#configuration_form .popup_optimizeing .optimize-wapper-percent').css('transition','all 3s ease 0s');
                $('#configuration_form .popup_optimizeing .optimize-wapper-percent').css('width',json.percent2+'%');
                $('#configuration_form .popup_optimizeing .percentage_optimize').html(json.percent2+'%');
                total_need_optimized_images = json.total_unoptimized;    
                $('.percent-image-in-chart').html(json.percent+'%');
                $('.total_image_optimized').html(json.total_optimizeed +' '+images_optimized_text);
                $('.total_image_optimized_size').html(json.total_size_save);
                $('.total_unoptimized_images').html(json.total_unoptimized +' '+ unoptimized_image_text);
                $('.dashboad-image-optimized .percent-image').html(json.percent+'% <span class="number-image">('+json.total_optimizeed+' images)</span>');
                $('.dashboad-image-unoptimized .percent-image').html((json.percent_unoptimized)+'% <span class="number-image">('+json.total_unoptimized+' images)</span>');
                if(json.optimized_images)
                {
                    if($('.popup_optimizeing_wapper .popup_optimizeing .list_optimized_images').length==0)
                    {
                       $('.popup_optimizeing_wapper .popup_optimizeing .button-group').before('<ul class="list_optimized_images"></ul>');
                    }
                    for (image in json.optimized_images)
                    {
                        if($('.popup_optimizeing_wapper .popup_optimizeing .list_optimized_images li[data-image="'+json.optimized_images[image]['image']+'"]').length==0 && $('.list_optimized_images li.stop').length==0)
                        {
                            if($('.popup_optimizeing_wapper .popup_optimizeing .list_optimized_images li').length >=5)
                               $('.popup_optimizeing_wapper .popup_optimizeing .list_optimized_images li:first-child').remove(); 
                            $('.popup_optimizeing_wapper .popup_optimizeing .list_optimized_images').append('<li data-image="'+json.optimized_images[image]['image']+'">'+json.optimized_images[image]['image_cat']+'</li>');
                        }
                    }
                }
                sp_updateDashboardChart(chart_image_optimize,false,[json.percent,json.percent_unoptimized]);
            }
            if(json.percent>=100 && ajaxPercentAllImageOptimize)
                clearInterval(ajaxPercentAllImageOptimize);
        },
    });
}
function sp_ajaxPercentageAllImageOptimizeDashboard()
{
    $.ajax({
        url: url_imagecompressor_ajax,
        data: 'getPercentageAllImageOptimize=1&total_optimize_images='+total_optimize_images,
        type: 'post',
        dataType: 'json',
        success: function(json){
            if(json.percent>0 && json.percent<100)
            {
                $('.popup-content-body .percent-image-optimized').html(json.percent2+'%');
                $('.popup-content-body .number-image').html(json.total_optimizeed2);
                total_need_optimized_images= json.total_unoptimized;
                $('.percent-image-in-chart').html(json.percent+'%');
                $('.total_image_optimized').html(json.total_optimizeed +' '+images_optimized_text);
                $('.total_image_optimized_size').html(json.total_size_save);
                $('.total_unoptimized_images').html(json.total_unoptimized +' '+ unoptimized_image_text);
                $('.dashboad-image-optimized .percent-image').html(json.percent+'% <span class="number-image">('+json.total_optimizeed+' images)</span>');
                $('.dashboad-image-unoptimized .percent-image').html((json.percent_unoptimized)+'% <span class="number-image">('+json.total_unoptimized+' images)</span>');
                sp_updateDashboardChart(chart_image_optimize,false,[json.percent,json.percent_unoptimized]);
                if(json.optimized_images)
                {
                    if($('.popup-configuration-cache .popup_run .list_optimized_images').length==0)
                    {
                       $('.popup-configuration-cache .popup_run .button-group').before('<ul class="list_optimized_images"></ul>');
                    }
                    for (image in json.optimized_images)
                    {
                        if($('.popup-configuration-cache .popup_run .list_optimized_images li.'+json.optimized_images[image]['image']).length==0 && $('.list_optimized_images li.stop').length==0)
                        {
                            if($('.popup-configuration-cache .popup_run .list_optimized_images li').length >=5)
                               $('.popup-configuration-cache .popup_run .list_optimized_images li:first-child').remove(); 
                            $('.popup-configuration-cache .popup_run .list_optimized_images').append('<li class="'+json.optimized_images[image]['image']+'" data-image="'+json.optimized_images[image]['image']+'">'+json.optimized_images[image]['image_cat']+'</li>');
                        }
                    }
                }
            }
            if(json.percent>=100 && ajaxPercentAllImageOptimizeDashboard)
                clearInterval(ajaxPercentAllImageOptimizeDashboard);
        },
    });
}
function sp_submitFormAjax(name,$this)
{
    if($this.hasClass('loading'))
        return false;
    if(editor_script)
        $('#live_script').val(editor_script.getValue());
    var formData = new FormData($this.parents('form').get(0));
    formData.append(name, 1);
    formData.append('ajax', 1);
    var url_ajax= url_imagecompressor_ajax;
    $('.bootstrap .module_error').remove();
    $this.addClass('loading');
    $.ajax({
        url: url_ajax,
        data: formData,
        type: 'post',
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(json){
            $this.removeClass('loading');
            if(json.success)
            {
                sp_displaySuccessMessage(json.success);
                if(name=='btnSaveOptimizeImageUpload')
                    $('.image_upload_otpimize_quality.image_upload').html('<i class="fa fa-cogs"></i> '+$('#ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_UPLOAD option[value="'+$('#ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_UPLOAD').val()+'"]').html()+' ('+$('#ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_UPLOAD').val()+'%)');
                if(name=='btnSaveOptimizeImageBrowse')
                    $('.image_upload_otpimize_quality.image_browse').html('<i class="fa fa-cogs"></i> '+$('#ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_BROWSE option[value="'+$('#ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_BROWSE').val()+'"]').html()+' ('+$('#ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_BROWSE').val()+'%)');
                $('.popup-optimize_image_upload').removeClass('show');
            }
            else if(json.errors)
            {
                
                if($('.popup-optimize_image_upload').length && $('.popup-optimize_image_upload').hasClass('show'))
                {
                    $('.popup-optimize_image_upload .popup-content-body').append(json.errors);
                }
                else
                {
                    sp_displayErrorMessage(json.errors);
                }
                    
            }
        },
        error: function(xhr, status, error)
        {     
            $this.removeClass('loading');
        }
    });
}
function sp_displayTynyPNG()
{
    if($('.config_tab_image_old').hasClass('active'))
    {
        if(!$('.form_cache_page.image_old.script').next('.tinypng').length)
        {
            var html_tiny = $('.form-group.tinypng').clone();
            $('.form-group.tinypng').remove();
            $('.form_cache_page.image_old.script').after(html_tiny);
        }
    }
    else if($('.config_tab_image_upload').hasClass('active') ||$('.config_tab_image_browse').hasClass('active'))
    {
        if($('.form_cache_page.image_browse.script').next('.tinypng').length==0)
        {
            var html_tiny = $('.form-group.tinypng').clone();
            $('.form-group.tinypng').remove();
            $('.form_cache_page.image_browse.script').after(html_tiny);
        }
    }
    if($('.config_tab_image_old').hasClass('active'))
    {
        var script_optimize = $('#ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT');
        var type ='';
    }
    else if($('.config_tab_image_upload').hasClass('active'))
    {
        var script_optimize = $('#ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_UPLOAD');
        var type = 'upload_';
    }
    else if($('.config_tab_image_browse').hasClass('active'))
    {
        var script_optimize = $('#ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_BROWSE');
        var type = 'browse_';
    }
    else
        var script_optimize= false;
    
    if(script_optimize && script_optimize.length && !$('.config_tab_image_cleaner').hasClass('active') && !$('.config_tab_image_lazy_load').hasClass('active'))
    {
        script_optimize.next('.help-block').find('span').hide();
        $('.help-block #optimize_script_'+type+script_optimize.val()).show();
        if(script_optimize.val()=='tynypng')
        {
            $('.form-group.tinypng').show();
            
        }     
        else
        {
            $('.form-group.tinypng').hide();
        }
        if(type=='new_' || type=='')
        {
            if(script_optimize.val()=='google')
            {
                $('.form-group.webp.'+(type=='new_' ? 'image_new' :'image_old')).show();
                
            }     
            else
            {
                $('.form-group.webp.'+(type=='new_' ? 'image_new' :'image_old')).hide();
            }
        }
    }
    if($('.config_tab_image_cleaner').hasClass('active') || $('.config_tab_image_lazy_load').hasClass('active'))
        $('.form-group.tinypng').hide();
}
function ets_sp_change_range($range)
{
    if($range.val()<=1)
        $range.next('.range_new').next('.input-group-unit').html($range.val()+ ($range.attr('data-unit')!='%' ? ' ':'')+$range.attr('data-unit'));
    else
    {
        if($range.attr('forever')=='1' && $range.val()=='31')
            $range.next('.range_new').next('.input-group-unit').html(Forever_text);
        else
            $range.next('.range_new').next('.input-group-unit').html($range.val()+ ($range.attr('data-units')!='%' ? ' ':'')+$range.attr('data-units'));
    }
    var newPoint = ($range.val() - $range.attr("min")) / ($range.attr("max") - $range.attr("min"));
    var offset = -1;
    var  width = $range.width();
    var newPlace;
    if (newPoint < 0) { newPlace = 0; }
    else if (newPoint > 1) { newPlace = width; }
    else { newPlace = width * newPoint + offset; offset -= newPoint; }
    $range.next('.range_new').find('.range_new_run').css({
         width: newPlace+'px'
    });
}