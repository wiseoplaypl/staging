<?php
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

if (!defined('_PS_VERSION_'))
    	exit;
class AdminImageCompressorAjaxController extends ModuleAdminController
{
    public function init()
    {
       parent::init();
       if(Tools::isSubmit('btnSubmitImageOptimize') || Tools::isSubmit('btnSubmitImageAllOptimize') || Tools::isSubmit('submitUploadImageSave')||Tools::isSubmit('submitUploadImageCompress') || Tools::isSubmit('submitBrowseImageOptimize') || Tools::isSubmit('btnSubmitCleaneImageUnUsed'))
            $this->module->_postImage();
       $optimized_images = array();
       $list_image_optimized = Configuration::get('ETS_IMGCOMPRESSOR_LIST_IMAGE_OPTIMIZED');
       if($list_image_optimized)
       {
            $list_image_optimized = explode(',',$list_image_optimized);
            foreach($list_image_optimized as $image)
            {
                $optimized_images[]= array(
                    'image'=>str_replace(array('/','\\','.'),'',Tools::substr($image,5)),
                    'image_cat' => Tools::strlen($image) > 40 ? Tools::substr($image,0,20).' . . . '.Tools::substr($image,Tools::strlen($image)-20) : $image
                );
            }
       }
       if(Tools::isSubmit('getPercentageImageOptimize'))
       {
            $this->getPercentageImageOptimize($optimized_images);
       }
       if(Tools::isSubmit('getPercentageAllImageOptimize'))
       {
           $this->getPercentageAllImageOptimize($optimized_images);

        }
    }
    protected function getPercentageImageOptimize($optimized_images)
    {
        $total_optimizeed = (int)$total_optimizeed = (int)Configuration::get('ETS_IMGCOMPRESSOR_TOTAL_IMAGE_OPTIMIZED');
        $total = (int)Tools::getValue('total_optimize_images');
        if($total && $total_optimizeed)
        {
            die(
                json_encode(
                    array(
                        'percent' => Tools::ps_round($total_optimizeed*100/$total,2),
                        'total_optimizeed' => $total_optimizeed,
                        'optimized_images' => $optimized_images,
                        'image' => $this->module->getImageOptimize(true),
                        'ETS_SPEEP_RESUMSH' => Configuration::get('ETS_SPEEP_RESUMSH'),
                    )
                )
            );
        }
        die(
            json_encode(
                array(
                    'percent' => 100,
                )
            )
        );
    }
    protected function getPercentageAllImageOptimize($optimized_images){
        $total=0;
        $total_optimizeed =0;
        $total += Ets_imagecompressor_defines::getTotalImage('product',true,false,false,true);
        $total_optimizeed += Ets_imagecompressor_defines::getTotalImage('product',true,true,true,true);
        $total += Ets_imagecompressor_defines::getTotalImage('category',true,false,false,true);
        $total_optimizeed += Ets_imagecompressor_defines::getTotalImage('category',true,true,true,true);
        $total += Ets_imagecompressor_defines::getTotalImage('supplier',true,false,false,true);
        $total_optimizeed += Ets_imagecompressor_defines::getTotalImage('supplier',true,true,true,true);
        $total += Ets_imagecompressor_defines::getTotalImage('manufacturer',true,false,false,true);
        $total_optimizeed += Ets_imagecompressor_defines::getTotalImage('manufacturer',true,true,true,true);
        if($this->module->isblog)
        {
            $total += Ets_imagecompressor_defines::getTotalImage('blog_post',true,false,false,true);
            $total_optimizeed += Ets_imagecompressor_defines::getTotalImage('blog_post',true,true,true,true);
            $total += Ets_imagecompressor_defines::getTotalImage('blog_category',true,false,false,true);
            $total_optimizeed += Ets_imagecompressor_defines::getTotalImage('blog_category',true,true,true,true);
            $total += Ets_imagecompressor_defines::getTotalImage('blog_gallery',true,false,false,true);
            $total_optimizeed += Ets_imagecompressor_defines::getTotalImage('blog_gallery',true,true,true,true);
            $total += Ets_imagecompressor_defines::getTotalImage('blog_slide',true,false,false,true);
            $total_optimizeed += Ets_imagecompressor_defines::getTotalImage('blog_slide',true,true,true,true);
        }
        if($this->module->isSlide)
        {
            $total += Ets_imagecompressor_defines::getTotalImage('home_slide',true,false,false,true);
            $total_optimizeed += Ets_imagecompressor_defines::getTotalImage('home_slide',true,true,true,true);
        }
        $total += Ets_imagecompressor_defines::getTotalImage('others',true,false,false,true);
        $total_optimizeed += Ets_imagecompressor_defines::getTotalImage('others',true,true,true,true);
        $total_optimizeed2 = (int)Configuration::get('ETS_IMGCOMPRESSOR_TOTAL_IMAGE_OPTIMIZED');
        $total2 = (int)Tools::getValue('total_optimize_images');
        if($total && $total_optimizeed)
        {
            die(
                json_encode(
                    array(
                        'percent' => Tools::ps_round($total_optimizeed*100/$total,2),
                        'percent2' => Tools::ps_round($total_optimizeed2*100/$total2,2),
                        'total_optimizeed2' => $total_optimizeed2,
                        'total_optimizeed' => $total_optimizeed,
                        'total_unoptimized' => $total- $total_optimizeed,
                        'optimized_images' => $optimized_images,
                        'percent_unoptimized' => Tools::ps_round(100 - Tools::ps_round($total_optimizeed*100/$total,2),2),
                        'total_size_save' => $this->module->getTotalSizeSave(),
                        'ETS_SPEEP_RESUMSH' => Configuration::get('ETS_SPEEP_RESUMSH'),
                    )
                )
            );
        }
        die(
            json_encode(
                array(
                    'percent' => 100,
                )
            )
        );
    }
}