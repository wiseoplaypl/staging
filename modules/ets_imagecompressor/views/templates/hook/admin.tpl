{*
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
*}
<script type="text/javascript">
var unoptimized_text = '{l s='unoptimized' mod='ets_imagecompressor' js='1'}';
var optimized_text = '{l s='optimized' mod='ets_imagecompressor' js='1'}';
var optimized_succ_text = '{l s='100% optimized' mod='ets_imagecompressor' js='1'}';
var restored_succ_text = '{l s='100% restored' mod='ets_imagecompressor' js='1'}';
var page_loading_time_text ='{l s='Page loading time' mod='ets_imagecompressor' js='1'}';
var speed_loading_text= '{l s='Speed loading' mod='ets_imagecompressor' js='1'}';
var time_text = '{l s='Time' mod='ets_imagecompressor' js='1'}';
var image_text = '{l s='Images' mod='ets_imagecompressor' js='1'}';
var Optimized_text ='{l s='Optimized' mod='ets_imagecompressor' js='1'}';
var Unoptimized_text='{l s='Unoptimized' mod='ets_imagecompressor' js='1'}';
var Forever_text= '{l s='Forever' mod='ets_imagecompressor'}';
var please_wait ='{l s='images, please wait' mod='ets_imagecompressor' js='1'}';
var Optimizing_text ='{l s='Optimizing' mod='ets_imagecompressor' js='1'}';
var Restoring_text= '{l s='Restoring' mod='ets_imagecompressor' js='1'}';
var Restore_original_images_text='{l s='Restore original images' mod='ets_imagecompressor' js='1'}';
var Optimize_existing_images_text='{l s='Optimize existing images' mod='ets_imagecompressor' js='1'}';
var restored_text = '{l s='restored' mod='ets_imagecompressor' js='1'}';
var restorable_text ='{l s='restorable' mod='ets_imagecompressor' js='1'}';
var unoptimized_image_text = '{l s='unoptimized images' mod='ets_imagecompressor' js='1'}';
var popup_optimize_image = '{l s='You are going to optimize total_images image(s). This process can take a few minutes depending on your server speed. Do you want to proceed?' mod='ets_imagecompressor' js='1'}';
var popup_restore_image = '{l s='You are going to restore total_images image(s). This process can take a few minutes depending on your server speed. Do you want to proceed?' mod='ets_imagecompressor' js='1'}';
var resmush_text = '{l s='Resmush server is not responding. Switched to PHP script' mod='ets_imagecompressor' js='1'}';
var resmush_run = '{l s='Resmush is working' mod='ets_imagecompressor' js='1'}';
var tyny_run = '{l s='Tinypng is working' mod='ets_imagecompressor' js='1'}';
var tyny_text = '{l s='Tinypng server is not responding. Switched to PHP script' mod='ets_imagecompressor' js='1'}';
var tiny_label = '{l s='TinyPNG API key' mod='ets_imagecompressor' js='1'}' ;
var optimize_type = 'products';
var limit_optimized =0;
var stop_optimized = false;
var continue_optimize = false;
var continue_optimize_webp= false;
var pause_text = '{l s='Pause' mod='ets_imagecompressor' js='1'}';
var stop_text = '{l s='Stop' mod='ets_imagecompressor' js='1'}';
var resume_text = '{l s='Resume' mod='ets_imagecompressor' js='1'}';
var popup_error = '{l s='Oops! There are problems while optimizing images:' mod='ets_imagecompressor' js='1'}';
var continue_text = '{l s='Yes, continue' mod='ets_imagecompressor' js='1'}';
var no_continue_text = '{l s='No, stop optimization' mod='ets_imagecompressor' js='1'}';
var continue_question = '{l s='Do you want to continue by switching image optimization method to "PHP image optimization script"?' mod='ets_imagecompressor' js='1'}';
var continue_question_webp = '{l s='Do you want to continue by switching image optimization method to "Google Webp image optimizer"?' mod='ets_imagecompressor' js='1'}';
var optimize_pause = '{l s='Image optimization stopped' mod='ets_imagecompressor' js='1'}';
var optimize_title_text ='{l s='Image optimization' mod='ets_imagecompressor' js='1'}';
</script>
{if isset($ets_imagecompressor_disabled) && $ets_imagecompressor_disabled}
    <div class="alert alert-warning">{l s='You have to enable Total Image Optimization Pro module to configure its features' mod='ets_imagecompressor'  js='1'}</div>
{/if}
<div class="sp_block_left">
    {hook h='displayAdminLeft'}
</div>
<div class="sp_block_space"></div>
<div class="sp_block_right{if isset($page)} {$page|escape:'html':'UTF-8'}{/if}">
    
    {$html_form nofilter}
</div>
<div class="ets-copyright"><p>{l s='This module is made by'  mod='ets_imagecompressor'} <a href="https://addons.prestashop.com/en/207_ets-soft" target="_blank">PrestaHero</a> ({l s='Prestashop Modules Partner Creator'  mod='ets_imagecompressor'}). <a href="https://addons.prestashop.com/en/207_ets-soft" target="_blank">{l s='View more modules' mod='ets_imagecompressor'}</a></p></div>
<script type="text/javascript">
    $(document).ready(function(){
        $('.sp_block_space').css('height',$('.sp_block_left').height()+'px');
        $(window).resize(function(){
            setTimeout(function(){ $('.sp_block_space').css('height',$('.sp_block_left').height()+'px'); }, 1000);
            
        });
        $(document).on('click','.menu-collapse',function(){
            $('.sp_block_space').css('height',$('.sp_block_left').height()+'px');
        });
        $(window).load(function(){
            $('.sp_block_space').css('height',$('.sp_block_left').height()+'px');
        });
    });
    
</script>