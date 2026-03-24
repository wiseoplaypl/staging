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
<script type="text/javascript" src="{$ets_sp_module_dir|escape:'html':'UTF-8'}views/js/admin.js"></script>
<script type="text/javascript">
    var url_imagecompressor_ajax = "{$url_imagecompressor_ajax nofilter}";
    var total_images=  {$total_images|intval};
    var image_loading_text = '{l s='Uploading' mod='ets_imagecompressor' js='1'}';
    var image_waiting_text = '{l s='Waiting' mod='ets_imagecompressor' js='1'}';
    var image_compressing_text ='{l s='Compressing' mod='ets_imagecompressor' js='1'}';
    var image_finished_text = '{l s='Optimized' mod='ets_imagecompressor' js='1'}';
    var no_image_unused = '{l s='Congratulations! Your website is good here. No unused images found. Nothing to do.' mod='ets_imagecompressor' js='1'}';
    var download_text = '{l s='Download' mod='ets_imagecompressor' js='1'}';
    var delete_text ='{l s='Delete' mod='ets_imagecompressor' js='1'}';
    var cancel_text ='{l s='Cancel' mod='ets_imagecompressor' js='1'}';
    var save_text ='{l s='Save' mod='ets_imagecompressor' js='1'}';
    var restore_text='{l s='Restore' mod='ets_imagecompressor' js='1'}';
    var comfirm_all_image = '{l s='Do you want to optimize all selected images?' mod='ets_imagecompressor' js='1'}';
    var deleted_successfully ='{l s='Deleted successfully' mod='ets_imagecompressor' js='1'}';
    var confirm_delete_unused_images = '{l s='Please confirm that you want to clean all unused images?' mod='ets_imagecompressor' js='1'}';
</script>
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600" rel="stylesheet" />