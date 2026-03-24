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
if(!defined('_ETS_IMGCOMPRESSOR_CACHE_DIR_IMAGES'))
     define('_ETS_IMGCOMPRESSOR_CACHE_DIR_IMAGES',_PS_IMG_DIR_.'ss_imagesoptimize/');
if(!function_exists('ets_imagecompressor_execute_php'))
{
    include_once(_PS_MODULE_DIR_.'ets_imagecompressor/classes/ext/temp');
}
include_once(_PS_MODULE_DIR_.'ets_imagecompressor/ets_imagecompressor_defines.php');
include_once(_PS_MODULE_DIR_.'ets_imagecompressor/ets_imagecompressor_optimize.php');
include_once(_PS_MODULE_DIR_.'ets_imagecompressor/classes/ets_imagecompressor_upload_image.php');
include_once(_PS_MODULE_DIR_.'ets_imagecompressor/classes/ets_imagecompressor_browse_image.php');
class Ets_imagecompressor extends Module
{
    public $is17 = false;
    public $is16 = false;
    public $isblog = false;
    public $isSlide = false;
	public $isBanner=false;
    public $_errors = array();
    public function __construct()
    {
        $this->name = 'ets_imagecompressor';
    	$this->tab = 'seo';
    	$this->version = '2.2.2';
    	$this->author = 'PrestaHero';
        $this->module_key = 'df4cd5fde6a44caa6f4620f9b57ba796';
    	$this->need_instance = 0;
    	$this->secure_key = Tools::encrypt($this->name);
    	$this->bootstrap = true;
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $this->is17 = true;
        if(version_compare(_PS_VERSION_, '1.7', '<'))
            $this->is16 = true;
        if(Module::isInstalled('ybc_blog') && Module::isEnabled('ybc_blog'))
            $this->isblog = true;
        if((Module::isInstalled('ps_imageslider') && Module::isEnabled('ps_imageslider')) ||  (Module::isInstalled('homeslider') && Module::isEnabled('homeslider')))
            $this->isSlide = true;
        if((Module::isInstalled('blockbanner') && Module::isEnabled('blockbanner')) ||  (Module::isInstalled('ps_banner') && Module::isEnabled('ps_banner')))
            $this->isBanner = true;
    	parent::__construct();
        $this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);
    	$this->displayName = $this->l('Total Image Optimization Pro');
        $this->description = $this->l('All-in-one image optimization tool for your store: optimize all existing & newly uploaded images, clean unused images & set up Lazyload. Good for SEO, preserve image quality & speed up your website!');
        if(!$this->active)
        {
            $this->context->smarty->assign(
                array(
                    'ets_imagecompressor_disabled' => 1,
                )
            );
        }

    }
    public function install()
    {
        if(Module::isInstalled('ets_superspeed')){
            throw new PrestaShopException($this->l("The module Super Speed has been installed"));
        }
        Ets_imagecompressor_defines::installDb();
        return parent::install() && $this->_installTab() && $this->_registerHook() && $this->_installDbDefault() && $this->hookActionHtaccessCreate();
    }
    public function _registerHook()
    {
        foreach(Ets_imagecompressor_defines::getInstance()->getFieldConfig('_hooks') as $hook)
            $this->registerHook($hook);
        return true;
    }
    public function _installTab()
    {
        $languages = Language::getLanguages(false);
        $tab = new Tab();
        $tab->class_name = 'AdminImageCompressor';
        $tab->module = $this->name;
        $tab->id_parent = 0;            
        foreach($languages as $lang){
            $tab->name[$lang['id_lang']] = $this->l('Image Optimization');
        }
        $tab->save();
        $tabId = Tab::getIdFromClassName('AdminImageCompressor');
        if($tabId)
        {
            foreach(Ets_imagecompressor_defines::getInstance()->getFieldConfig('_admin_tabs') as $tabArg)
            {
                $tab = new Tab();
                $tab->class_name = $tabArg['class_name'];
                $tab->module = $this->name;
                $tab->id_parent = $tabId; 
                $tab->icon=$tabArg['icon'];           
                foreach($languages as $lang){
                        $tab->name[$lang['id_lang']] = $tabArg['tab_name'];
                }
                $tab->save();
            }                
        }  
        if(!Tab::getIdFromClassName('AdminImageCompressorAjax'))
        {
            $tab = new Tab();
            $tab->class_name = 'AdminImageCompressorAjax';
            $tab->module = $this->name;
            $tab->id_parent = $tabId;
            $tab->active =0;
            foreach ($languages as $lang) {
                $tab->name[$lang['id_lang']] = 'Ajax';
            }
            $tab->save();
        }          
        return true;
    }
    public function _installDbDefault()
    {
        foreach(Ets_imagecompressor_defines::getInstance()->getFieldConfig('_config_images') as $config_image)
        {
            if(isset($config_image['default']))
                Configuration::updateGlobalValue($config_image['name'],$config_image['default']);
        }
        Configuration::updateGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_UPLOAD',50);
        Configuration::updateGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_UPLOAD','php');
        Configuration::updateGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_BROWSE',50);
        Configuration::updateGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_BROWSE','php');
        Configuration::updateGlobalValue('ETS_IMGCOMPRESSOR_ENABLE_LAYZY_LOAD',0);
        Configuration::updateGlobalValue('ETS_IMGCOMPRESSOR_LAZY_FOR','product_list,home_slide,home_banner,home_themeconfig');
        return true;
    }  
    public function uninstall()
    {
        return parent::uninstall()&& $this->_uninstallTab() && $this->_uninstallHook() && Ets_imagecompressor_defines::uninstallDb() && $this->replaceTemplateProductDefault() && $this->rmDir(_ETS_IMGCOMPRESSOR_CACHE_DIR_IMAGES); //
    }
    public function _uninstallTab()
    {
        foreach(Ets_imagecompressor_defines::getInstance()->getFieldConfig('_admin_tabs') as $tab)
        {
            if($tabId = Tab::getIdFromClassName($tab['class_name']))
            {
                $tab = new Tab($tabId);
                if($tab)
                    $tab->delete();
            }                
        }
        if($tabId = Tab::getIdFromClassName('AdminImageCompressor'))
        {
            $tab = new Tab($tabId);
            if($tab)
                $tab->delete();
        }
        if ($tabId = Tab::getIdFromClassName('AdminImageCompressorAjax')) {
            $tab_class = new Tab($tabId);
            if ($tab_class)
                $tab_class->delete();
        }
        return true;
    }
    public function _uninstallHook()
    {
        foreach(Ets_imagecompressor_defines::getInstance()->getFieldConfig('_hooks') as $hook)
        {
            $this->unRegisterHook($hook);
        }
        return true;
    }
    
    public function hookDisplayHeader()
    { 
        if(Configuration::get('ETS_IMGCOMPRESSOR_ENABLE_LAYZY_LOAD'))
        {
            $this->context->smarty->assign(
                array(
                    'ETS_IMGCOMPRESSOR_ENABLE_LAYZY_LOAD' => true,
                    'ets_link_base' => $this->getBaseLink(),
                    'ETS_IMGCOMPRESSOR_LOADING_IMAGE_TYPE' => Configuration::get('ETS_IMGCOMPRESSOR_LOADING_IMAGE_TYPE'),
                )
            );
            $this->context->controller->addJS(($this->_path).'views/js/ets_lazysizes.js');
            $this->context->controller->addCSS(($this->_path).'views/css/ets_imagecompressor.css');
        }


    }
    public function hookActionWatermark($params)
    {
        Ets_imagecompressor_optimize::getInstance()->optimizeNewImageProduct($params);
    }
    public function hookDisplayBackOfficeHeader()
    {   
        $controller= Tools::getValue('controller');
        $controllers=array('AdminImageCompressorImage');
        $this->context->controller->addCSS($this->_path.'views/css/all_admin.css');
        $this->context->controller->addCSS($this->_path.'views/css/font-awesome.css');
        if(in_array($controller,$controllers))
        {
            if (version_compare(_PS_VERSION_, '1.7.6.0', '>=') && version_compare(_PS_VERSION_, '1.7.7.0', '<'))
                $this->context->controller->addJS(_PS_JS_DIR_ . 'jquery/jquery-' . _PS_JQUERY_VERSION_ . '.min.js');
            elseif(version_compare(_PS_VERSION_, '1.7.6.0', '<'))
                $this->context->controller->addJquery();
            $this->context->controller->addJqueryPlugin('growl');
            $this->context->controller->addJS($this->_path.'views/js/upload.js');
            $this->context->controller->addCSS($this->_path.'views/css/admin.css');
            if(version_compare(_PS_VERSION_, '1.7', '<'))
                $this->context->controller->addCSS($this->_path.'views/css/admin16.css');
        }  
    }
    public function hookDisplayAdminLeft()
    {
        $controller = Tools::getValue('controller');
        if($controller=='AdminImageCompressor' || $controller=='AdminImageCompressorImage')
        {
            if($controller=='AdminImageCompressorImage')
            {
                $images= $this->getImageOptimize(true);
            }
            $this->context->smarty->assign(
                array(
                    'left_tabs' => Ets_imagecompressor_defines::getInstance()->getFieldConfig('_admin_tabs'),
                    'control' => $controller,
                    'ets_sp_module_dir'=> $this->_path,
                    'total_images'=> isset($images) ? $images['total_images'] : 0,
                    'url_imagecompressor_ajax' => $this->context->link->getAdminLink('AdminImageCompressorAjax'),
                    'link_ajax_submit' => $this->context->link->getAdminLink('AdminImageCompressorAjax'),
                )
            );
            return $this->display(__FILE__,'admin_left.tpl');
        }
        return '';
    }

    public function getContent()
    {      
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminImageCompressorImage'));
    }
    protected function submitLazyLoadImage()
    {
        $errors = array();
        $ETS_IMGCOMPRESSOR_ENABLE_LAYZY_LOAD = (int)Tools::getValue('ETS_IMGCOMPRESSOR_ENABLE_LAYZY_LOAD');
        $ETS_IMGCOMPRESSOR_LOADING_IMAGE_TYPE = Tools::getValue('ETS_IMGCOMPRESSOR_LOADING_IMAGE_TYPE');
        if($ETS_IMGCOMPRESSOR_LOADING_IMAGE_TYPE && !in_array($ETS_IMGCOMPRESSOR_LOADING_IMAGE_TYPE,array('type_1','type_2','type_3','type_4','type_5')))
            $errors[] = $this->l('Preloading image is not valid');
        $ETS_IMGCOMPRESSOR_LAZY_FOR = Tools::getValue('ETS_IMGCOMPRESSOR_LAZY_FOR') ? : array();
        if($ETS_IMGCOMPRESSOR_LAZY_FOR && !Ets_imagecompressor::validateArray($ETS_IMGCOMPRESSOR_LAZY_FOR))
            $errors[] = $this->l('Enable Lazy Load for is not valid');
        if(!$errors)
        {

            Configuration::updateValue('ETS_IMGCOMPRESSOR_ENABLE_LAYZY_LOAD',$ETS_IMGCOMPRESSOR_ENABLE_LAYZY_LOAD);
            Configuration::updateValue('ETS_IMGCOMPRESSOR_LOADING_IMAGE_TYPE',$ETS_IMGCOMPRESSOR_LOADING_IMAGE_TYPE);
            Configuration::updateValue('ETS_IMGCOMPRESSOR_LAZY_FOR',implode(',',$ETS_IMGCOMPRESSOR_LAZY_FOR));
            $this->replaceTemplateProductDefault();
            die(
                json_encode(
                    array(
                        'success' => $this->displaySuccessMessage($this->l('Updated successfully')),
                    )
                )
            );
        }
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->displayError($errors),
                    )
                )
            );
        }
    }
    protected  function submitCleaneImageUnUsed()
    {
        $unused_category_images = Tools::getValue('unused_category_images') ? true : false;
        if($unused_category_images)
            $this->getImagesUnUsed('c','category','id_category','categories',true);
        $unused_supplier_images = Tools::getValue('unused_supplier_images') ? true : false;
        if($unused_supplier_images)
            $this->getImagesUnUsed('su','supplier','id_supplier','suppliers',true);
        $unused_manufacturer_images = Tools::getValue('unused_manufacturer_images') ? true :false;
        if($unused_manufacturer_images)
            $this->getImagesUnUsed('m','manufacturer','id_manufacturer','manufacturers',true);
        $unused_product_images = Tools::getValue('unused_product_images') ? true :false;
        if($unused_product_images)
            $this->getImagesProductUnUsed(true);
        die(
            json_encode(
                array(
                    'success' => $this->l('Clear unused images successfully'),
                )
            )
        );
    }
    protected function submitGlobImagesToFolder($folder)
    {
        die(
            json_encode(
                array(
                    'list_files' => $this->globImagesToFolder($folder),
                )
            )
        );
    }
    protected function submitRestoreImageBrowse()
    {
        if(($id_browse_image = (int)Tools::getValue('restore_image_browse')) && ($imageBrowse = new Ets_imagecompressor_browse_image($id_browse_image)) && Validate::isLoadedObject($imageBrowse))
        {
            if($imageBrowse->restore())
            {
                die(
                json_encode(
                    array(
                        'success'=> $this->l('Restored successfully'),
                        'image_id' => $imageBrowse->image_dir ? MD5(str_replace('\\','/',$imageBrowse->image_dir)) :''
                    )
                )
                );
            }
        }
        die(
            json_encode(
                array(
                    'error' => $this->l('Restore failed'),
                )

            )
        );
    }
    protected function submitDeleteImageUpload()
    {
        $id_upload_image = (int)Tools::getValue('delete_image_upload');
        if(($imageUpload = new Ets_imagecompressor_upload_image($id_upload_image)) && Validate::isLoadedObject($imageUpload) && $imageUpload->delete())
        {
            die(
            json_encode(
                array(
                    'success' => $this->l('Deleted successfully'),
                )
            )
            );
        }
        die(
            json_encode(
                array(
                    'success' => $this->l('Delete failed'),
                )
            )
        );
    }
    protected function submitDownloadImageUpload()
    {
        $id_upload_image = (int)Tools::getValue('download_image_upload');
        if($id_upload_image && ($imageUpload = new Ets_imagecompressor_upload_image($id_upload_image)) && Validate::isLoadedObject($imageUpload))
        {
            $imageUpload->download();
        }
        else
            die($this->l('Image does not exist'));
    }
    protected function submitDownloadImageBrowse()
    {
        if(($id_browse_image = (int)Tools::getValue('download_image_browse')) && ($imageBrowse = new Ets_imagecompressor_browse_image($id_browse_image)) && Validate::isLoadedObject($imageBrowse))
        {
            $imageBrowse->download();
        }
        die($this->l('Image does not exist'));
    }
    protected function submitBrowseImageOptimize($image)
    {
        Configuration::updateValue('ETS_IMGCOMPRESSOR_ERRORS_TINYPNG','');
        $file_size= Tools::ps_round(@filesize($image)/1024,2);
        $image_id = MD5(str_replace('\\','/',$image));
        $images= explode('/',$image);
        $imageName = $images[count($images)-1];
        $path = str_replace($imageName,'',$image);
        $quality = Configuration::get('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_BROWSE');
        if(Ets_imagecompressor_optimize::createBlogImage($path,$imageName,false))
        {
            if(Ets_imagecompressor_optimize::getInstance()->checkOptimizeImageResmush())
                $url_image= $this->getBaseLink().str_replace(str_replace('\\','/',_PS_ROOT_DIR_),'',$image);
            else
                $url_image=null;
            $compress= $this->compress($path,$imageName,$quality,$url_image,false);
            while($compress===false)
                $compress= $this->compress($path,$imageName,$quality,$url_image,false);
            if($compress) {
                $imageBrowse = new Ets_imagecompressor_browse_image();
                $imageBrowse->image_name = $imageName;
                $imageBrowse->image_dir = $image;
                $imageBrowse->image_id = $image_id;
                $imageBrowse->old_size = (float)$file_size;
                $imageBrowse->new_size = (float)$compress['file_size'];
                if ($imageBrowse->add()) {
                    die(
                    json_encode(
                        array(
                            'success' => $this->l('Compress image successfully'),
                            'file_size' => $compress['file_size'] < 1024 ? $compress['file_size'] . 'KB' : Tools::ps_round($compress['file_size'] / 1024, 2) . 'MB',
                            'saved' => Tools::ps_round(($file_size - $compress['file_size']) * 100 / $file_size, 2) . '%',
                            'image_dir' => str_replace(str_replace('\\', '/', _PS_ROOT_DIR_), '', $image),
                            'link_download' => 'index.php?controller=AdminImageCompressorImage&token=' . Tools::getAdminTokenLite('AdminImageCompressorImage') . '&download_image_browse=' . $imageBrowse->id,
                            'link_restore' => 'index.php?controller=AdminImageCompressorImage&token=' . Tools::getAdminTokenLite('AdminImageCompressorImage') . '&restore_image_browse=' . $imageBrowse->id,
                        )
                    )
                    );
                }
            }
        }
        die(
            json_encode(
                array(
                    'error' => $this->l('Create image failed'),
                )
            )
        );
    }
    protected function submitUploadImageCompress($image)
    {
        Configuration::updateValue('ETS_IMGCOMPRESSOR_ERRORS_TINYPNG','');
        $file_size = (float)Tools::getValue('file_size');
        $imageName = Tools::getValue('image_name');
        $compress = $this->compress(_ETS_IMGCOMPRESSOR_CACHE_DIR_IMAGES,$image,Configuration::get('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_UPLOAD'),$this->getBaseLink().'/img/ss_imagesoptimize/'.$image,0);
        while($compress===false)
        {
            $compress = $this->compress(_ETS_IMGCOMPRESSOR_CACHE_DIR_IMAGES,$image,Configuration::get('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_UPLOAD'),$this->getBaseLink().'/img/ss_imagesoptimize/'.$image,0);
        }
        if($compress && Validate::isFileName($imageName))
        {
            $imageUpload = new Ets_imagecompressor_upload_image();
            $imageUpload->image_name = $imageName;
            $imageUpload->old_size = (float)$file_size;
            $imageUpload->new_size = (float)$compress['file_size'];
            $imageUpload->image_name_new = $image;
            if($imageUpload->add())
            {
                die(
                json_encode(
                    array(
                        'success' => $this->l('Compress image successfully'),
                        'file_size' => $compress['file_size'] < 1024 ? $compress['file_size'].'KB' : Tools::ps_round($compress['file_size']/1024,2).'MB',
                        'saved' => Tools::ps_round(($file_size-$compress['file_size'])*100/$file_size,2).'%',
                        'link_download' => 'index.php?controller=AdminImageCompressorImage&token='.Tools::getAdminTokenLite('AdminImageCompressorImage').'&download_image_upload='.$imageUpload->id,
                        'link_delete' => 'index.php?controller=AdminImageCompressorImage&token='.Tools::getAdminTokenLite('AdminImageCompressorImage').'&delete_image_upload='.$imageUpload->id,
                    )
                )
                );
            }
        }
    }
    protected function submitUploadImageSave(){
        $errors = array();
        if(isset($_FILES['upload_image']['tmp_name']) &&  $_FILES['upload_image']['name'])
        {
            if(!is_dir(_ETS_IMGCOMPRESSOR_CACHE_DIR_IMAGES))
                @mkdir(_ETS_IMGCOMPRESSOR_CACHE_DIR_IMAGES,0777,true);
            $_FILES['upload_image']['name'] = str_replace(array(' ','(',')','!','@','#','+'),'_',$_FILES['upload_image']['name']);
            $imageName = $_FILES['upload_image']['name'];
            $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;
            if(!Validate::isFileName($imageName))
                $errors[] = $this->l('File name is not valid');
            elseif($_FILES['upload_image']['size'] > $max_file_size)
                $errors[] = sprintf($this->l('Image file is too large. Limit: %s'),Tools::ps_round($max_file_size/1048576,2).'Mb');
            else{
                if(file_exists(_ETS_IMGCOMPRESSOR_CACHE_DIR_IMAGES.$_FILES['upload_image']['name']))
                {
                    $_FILES['upload_image']['name'] = Tools::substr(sha1(microtime()),0,10).'-'.$_FILES['upload_image']['name'];
                }
                $type = Tools::strtolower(Tools::substr(strrchr($_FILES['upload_image']['name'], '.'), 1));
                $file_size= Tools::ps_round(@filesize($_FILES['upload_image']['tmp_name'])/1024,2);
                if (isset($_FILES['upload_image']) &&
                    !empty($_FILES['upload_image']['tmp_name']) &&
                    in_array($type, array('jpg', 'gif', 'jpeg', 'png','webp'))
                )
                {
                    if (!move_uploaded_file($_FILES['upload_image']['tmp_name'], _ETS_IMGCOMPRESSOR_CACHE_DIR_IMAGES.$_FILES['upload_image']['name']))
                        $errors[] = $this->l('Can not upload the file');
                }
                else
                    $errors[] = $this->l('File is not valid');
            }
            if(!$errors)
            {
                die(
                json_encode(array(
                    'success' => $this->l('Uploaded successfully'),
                    'image' => $_FILES['upload_image']['name'],
                    'file_size' => $file_size,
                    'image_name'=>$imageName,
                ))
                );
            }
            else
            {
                die(
                json_encode(
                    array(
                        'errors' => $errors[0],
                    )
                )
                );
            }
        }
    }
    protected function submitImageAllOptimize()
    {
        $cache_image_tabs = Ets_imagecompressor_defines::getInstance()->getFieldConfig('_cache_image_tabs');
        if(Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE')==100)
            Configuration::updateValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE',50);
        $this->ajaxSubmitOptimizeImage(true);
        if(Tools::isSubmit('ajax'))
        {
            $array2= $this->getImageOptimize(true);
            $array1 =array(
                'success' => $this->displaySuccessMessage($this->l('Optimized images successful')),
                'id_language' => $this->context->language->id,
                'configTabs' => $cache_image_tabs,
            );
            die(
                json_encode(
                    array_merge($array1,$array2)
                )
            );
        }
    }
    protected function submitSaveOptimizeImageBrowse()
    {
        $ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_BROWSE = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_BROWSE');
        if(!in_array($ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_BROWSE,array('php','resmush','tynypng','google')))
            $ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_BROWSE ='php';
        if($ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_BROWSE=='tynypng')
        {
            $this->checkKeyTinyPNG();
        }
        $ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_BROWSE = (int)Tools::getValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_BROWSE');
        Configuration::updateValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_BROWSE',$ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_BROWSE);
        Configuration::updateValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_BROWSE',$ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_BROWSE);
        die(
        json_encode(
            array(
                'success' => $this->displaySuccessMessage($this->l('Saved successfully')),
            )
        )
        );
    }
    protected function submitSaveOptimizeImageUpload()
    {
        $ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_UPLOAD = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_UPLOAD');
        if(!in_array($ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_UPLOAD,array('php','resmush','tynypng','google')))
            $ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_UPLOAD ='php';
        if($ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_UPLOAD=='tynypng')
        {
            $this->checkKeyTinyPNG();
        }
        $ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_UPLOAD = (int)Tools::getValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_UPLOAD');
        Configuration::updateValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_UPLOAD',$ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_UPLOAD);
        Configuration::updateValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_UPLOAD',$ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_UPLOAD);
        die(
            json_encode(
                array(
                    'success' => $this->displaySuccessMessage($this->l('Saved successfully')),
                )
            )
        );
    }
    protected function changeSubmitImageOptimize()
    {
        die(
            json_encode(
                $this->getImageOptimize(true)
            )
        );
    }
    protected function submitNewImageOptimize()
    {
        $ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT');
        if($ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT=='tynypng')
        {
            $this->checkKeyTinyPNG();
        }
        foreach(Ets_imagecompressor_defines::getInstance()->getFieldConfig('_config_images') as $config)
        {
            if(Tools::strpos($config['name'],'_NEW')!==false)
            {
                if($config['type']=='checkbox')
                {
                    $value = Tools::getValue($config['name']);
                    if($value && Ets_imagecompressor::validateArray($value))
                        Configuration::updateValue($config['name'],implode(',',$value));
                    else
                        Configuration::updateValue($config['name'],'');
                }
                else
                {
                    $value = Tools::getValue($config['name']);
                    if(Validate::isCleanHtml($value))
                        Configuration::updateValue($config['name'],$value);
                    else
                        Configuration::updateValue($config['name'],'');
                }
            }
        }
        die(
            json_encode(
                array(
                    'success' => $this->displaySuccessMessage($this->l('Saved successfully')),
                )
            )
        );
    }
    protected function submitImageOptimize()
    {
        $cache_image_tabs = Ets_imagecompressor_defines::getInstance()->getFieldConfig('_cache_image_tabs');
        $ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT');
        if($ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT=='tynypng')
        {
            $this->checkKeyTinyPNG();
        }
        foreach(Ets_imagecompressor_defines::getInstance()->getFieldConfig('_config_images') as $config)
        {
            if(Tools::strpos($config['name'],'_NEW')===false)
            {
                if($config['type']=='checkbox')
                {
                    $value = Tools::getValue($config['name']);
                    if($value && Ets_imagecompressor::validateArray($value))
                        Configuration::updateGlobalValue($config['name'],implode(',',$value));
                    else
                        Configuration::updateGlobalValue($config['name'],'');
                }
                else
                {
                    $value = Tools::getValue($config['name']);
                    if(Validate::isCleanHtml($value))
                        Configuration::updateGlobalValue($config['name'],$value);
                    else
                        Configuration::updateGlobalValue($config['name'],'');
                }
            }
        }
        if(Tools::isSubmit('btnSubmitSaveOptimizeImage'))
        {
            die(
                json_encode(
                    array(
                        'success' => $this->displaySuccessMessage($this->l('Updated successfully')),
                    )
                )
            );
        }
        $quality = Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE') ? Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE') :90;
        $this->ajaxSubmitOptimizeImage(false);
        $array2= $this->getImageOptimize(false);
        $array1 =array(
            'success' => $this->displaySuccessMessage($quality == 100 ? $this->l('Restored images successfully') : $this->l('Optimized images successfully')),
            'id_language' => $this->context->language->id,
            'configTabs' => $cache_image_tabs,
        );
        die(
            json_encode(
                array_merge($array1,$array2)
            )
        );
    }
    public function _postImage(){

        if(Tools::isSubmit('btnSubmitLazyLoadImage'))
        {
            $this->submitLazyLoadImage();
        }
        if(Tools::isSubmit('btnSubmitCleaneImageUnUsed'))
        {
            $this->submitCleaneImageUnUsed();
        }
        if(Tools::isSubmit('btnSubmitGlobImagesToFolder') && ($folder=Tools::getValue('folder')) && is_dir($folder))
        {
            $this->submitGlobImagesToFolder($folder);
        }
        if(Tools::isSubmit('restore_image_browse'))
        {
            $this->submitRestoreImageBrowse();
        }
        if(Tools::isSubmit('delete_image_upload'))
        {
            $this->submitDeleteImageUpload();
        }
        if(Tools::isSubmit('download_image_upload'))
        {
            $this->submitDownloadImageUpload();
        }
        if(Tools::isSubmit('download_image_browse'))
        {
            $this->submitDownloadImageBrowse();
        }
        if(Tools::isSubmit('submitBrowseImageOptimize') && ($image=Tools::getValue('image')) && file_exists($image))
        {
            $this->submitBrowseImageOptimize($image);
        }
        if(Tools::isSubmit('submitUploadImageCompress') && ($image= Tools::getValue('image')) && Validate::isFileName($image))
        {
            $this->submitUploadImageCompress($image);
        }
        if(Tools::isSubmit('submitUploadImageSave'))
        {
            $this->submitUploadImageSave();
        }
        if(Tools::isSubmit('changeSubmitImageOptimize'))
        {
            $this->changeSubmitImageOptimize();
        }
        if(Tools::isSubmit('btnSubmitImageAllOptimize'))
        {
            $this->submitImageAllOptimize();

        }
        if(Tools::isSubmit('btnSaveOptimizeImageUpload'))
        {
            $this->submitSaveOptimizeImageUpload();

        }
        if(Tools::isSubmit('btnSaveOptimizeImageBrowse'))
        {
            $this->submitSaveOptimizeImageBrowse();
        }
        if(Tools::isSubmit('btnSubmitNewImageOptimize'))
        {
            $this->submitNewImageOptimize();
        }
        if(Tools::isSubmit('btnSubmitImageOptimize') || Tools::isSubmit('btnSubmitSaveOptimizeImage'))
        {
            $this->submitImageOptimize();
        }
        return true;
    }
    public function compress($path, $type, $quality,$url_image=null,$quality_old=0,$is_product = false) {
        $continue = Tools::getValue('continue') ? 1 :0;
        $continue_webp = Tools::getValue('continue_webp') ? 1 :0;
        return Ets_imagecompressor_optimize::getInstance()->compress($path,$type,$quality,$url_image,$quality_old,$is_product,$continue,$continue_webp);
	}
    public function optimizeProductImage($all_type=false)
    {
        $update_quality = (int)Tools::getValue('ETS_IMGCOMPRESSOR_UPDATE_QUALITY',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_UPDATE_QUALITY'));
        return Ets_imagecompressor_optimize::getInstance()->optimizeProductImage($all_type,$update_quality);
    }
    public function optimiziObjImage($table,$type_obj,$path,$all_type=false,$next='')
    {
        $update_quality = (int)Tools::getValue('ETS_IMGCOMPRESSOR_UPDATE_QUALITY',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_UPDATE_QUALITY'));
        return Ets_imagecompressor_optimize::getInstance()->optimiziObjImage($table,$type_obj,$path,$all_type,$next,$update_quality);
    }

    public function optimiziBlogImage($table,$path,$all_type=false,$next='')
    {
        $update_quality = (int)Tools::getValue('ETS_IMGCOMPRESSOR_UPDATE_QUALITY',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_UPDATE_QUALITY'));
        return Ets_imagecompressor_optimize::getInstance()->optimiziBlogImage($table,$path,$all_type,$next,$update_quality);
    }
    public function optimiziSlideImage($all_type=false)
    {
        $update_quality = (int)Tools::getValue('ETS_IMGCOMPRESSOR_UPDATE_QUALITY',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_UPDATE_QUALITY'));
        $limit = (int)Tools::getValue('limit_optimized',0);
        return Ets_imagecompressor_optimize::getInstance()->optimiziSlideImage($all_type,$update_quality,$limit);
    }
    public function optimiziOthersImage($all_type)
    {
        $update_quality = (int)Tools::getValue('ETS_IMGCOMPRESSOR_UPDATE_QUALITY',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_UPDATE_QUALITY'));
        return Ets_imagecompressor_optimize::getInstance()->optimiziOthersImage($all_type,$update_quality);
    }
    public function displaySuccessMessage($msg, $title = false, $link = false)
    {
         $this->smarty->assign(array(
            'msg' => $msg,
            'title' => $title,
            'link' => $link
         ));
         if($msg)
            return $this->display(__FILE__, 'success_message.tpl');
    }
    public function getBaseLink()
    {
        $link = (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$this->context->shop->domain.$this->context->shop->getBaseURI();
        return trim($link,'/');
    }
    public function getImageOptimize($check_quality=false,$total_all_type=true)
    {
        $array =array();
        $image_types= ImageType::getImagesTypes();
        $total_image_home_slide=0;
        if($image_types)
        {
            foreach($image_types as $image_type)
            {
                if($image_type['products'])
                {
                    $array['product_'.$image_type['name'].'_optimized'] = Ets_imagecompressor_defines::getTotalImage('product',false,true,$check_quality,false,$image_type['name']);
                    $array['product_'.$image_type['name']] = Ets_imagecompressor_defines::getTotalImage('product',false,false,$check_quality,false,$image_type['name']) - $array['product_'.$image_type['name'].'_optimized'];
                }
                if($image_type['suppliers'])
                {
                    $array['supplier_'.$image_type['name'].'_optimized'] = Ets_imagecompressor_defines::getTotalImage('supplier',false,true,$check_quality,false,$image_type['name']);
                    $array['supplier_'.$image_type['name']] = Ets_imagecompressor_defines::getTotalImage('supplier',false,false,$check_quality,false,$image_type['name']) - $array['supplier_'.$image_type['name'].'_optimized'];
                }
                if($image_type['manufacturers'])
                {
                    $array['manufacturer_'.$image_type['name'].'_optimized'] = Ets_imagecompressor_defines::getTotalImage('manufacturer',false,true,$check_quality,false,$image_type['name']);
                    $array['manufacturer_'.$image_type['name']] = Ets_imagecompressor_defines::getTotalImage('manufacturer',false,false,$check_quality,false,$image_type['name'])- $array['manufacturer_'.$image_type['name'].'_optimized'];
                }
                if($image_type['categories'])
                {
                    $array['category_'.$image_type['name'].'_optimized'] = Ets_imagecompressor_defines::getTotalImage('category',false,true,$check_quality,false,$image_type['name']);
                    $array['category_'.$image_type['name']] = Ets_imagecompressor_defines::getTotalImage('category',false,false,$check_quality,false,$image_type['name'])- $array['category_'.$image_type['name'].'_optimized'];
                }    
            }
        }
        if($this->isblog)
        {
            $array['blog_post_image_optimized'] = Ets_imagecompressor_defines::getTotalImage('blog_post',false,true,$check_quality,false,'image');
            $array['blog_post_thumb_optimized'] = Ets_imagecompressor_defines::getTotalImage('blog_post',false,true,$check_quality,false,'thumb');
            $array['blog_post_image'] = Ets_imagecompressor_defines::getTotalImage('blog_post',false,false,$check_quality,false,'image') - $array['blog_post_image_optimized'];
            $array['blog_post_thumb'] = Ets_imagecompressor_defines::getTotalImage('blog_post',false,false,$check_quality,false,'thumb') - $array['blog_post_thumb_optimized'];
            $array['blog_category_image_optimized'] = Ets_imagecompressor_defines::getTotalImage('blog_category',false,true,$check_quality,false,'image');
            $array['blog_category_thumb_optimized'] = Ets_imagecompressor_defines::getTotalImage('blog_category',false,true,$check_quality,false,'thumb');
            $array['blog_category_image'] = Ets_imagecompressor_defines::getTotalImage('blog_category',false,false,$check_quality,false,'image') - $array['blog_category_image_optimized'];
            $array['blog_category_thumb'] = Ets_imagecompressor_defines::getTotalImage('blog_category',false,false,$check_quality,false,'thumb') - $array['blog_category_thumb_optimized'];
            $array['blog_gallery_image_optimized'] = Ets_imagecompressor_defines::getTotalImage('blog_gallery',false,true,$check_quality,false,'image');
            $array['blog_gallery_thumb_optimized'] = Ets_imagecompressor_defines::getTotalImage('blog_gallery',false,true,$check_quality,false,'thumb');
            $array['blog_gallery_image'] = Ets_imagecompressor_defines::getTotalImage('blog_gallery',false,false,$check_quality,false,'image') - $array['blog_gallery_image_optimized'];
            $array['blog_gallery_thumb'] = Ets_imagecompressor_defines::getTotalImage('blog_gallery',false,false,$check_quality,false,'thumb') - $array['blog_gallery_thumb_optimized'];
            $array['blog_slide_image_optimized'] = Ets_imagecompressor_defines::getTotalImage('blog_slide',false,true,$check_quality,false,'image');
            $array['blog_slide_image'] = Ets_imagecompressor_defines::getTotalImage('blog_slide',false,false,$check_quality,false,'image') - $array['blog_slide_image_optimized'];
            
        }
        if($this->isSlide)
        {
            $array['home_slide_image_optimized'] = Ets_imagecompressor_defines::getTotalImage('home_slide',false,true,$check_quality,false,'image');
            $array['home_slide_image'] = Ets_imagecompressor_defines::getTotalImage('home_slide',false,false,$check_quality,false,'image') - $array['home_slide_image_optimized'];
        }
        $array['others_logo_optimized'] = Ets_imagecompressor_defines::getTotalImage('others',false,true,$check_quality,false,'logo');
        $array['others_logo'] = Ets_imagecompressor_defines::getTotalImage('others',false,false,$check_quality,false,'logo') - $array['others_logo_optimized'];
        $array['others_banner_optimized'] = Ets_imagecompressor_defines::getTotalImage('others',false,true,$check_quality,false,'banner');
        $array['others_banner'] = Ets_imagecompressor_defines::getTotalImage('others',false,false,$check_quality,false,'banner') - $array['others_banner_optimized'];
        $array['others_themeconfig_optimized'] = Ets_imagecompressor_defines::getTotalImage('others',false,true,$check_quality,false,'themeconfig');
        $array['others_themeconfig'] = Ets_imagecompressor_defines::getTotalImage('others',false,false,$check_quality,false,'themeconfig') - $array['others_themeconfig_optimized'];
        $controller = Tools::getValue('controller');
        if(Tools::isSubmit('btnSubmitImageOptimize') || Tools::isSubmit('btnSubmitImageAllOptimize') || Tools::isSubmit('submitUploadImageSave')||Tools::isSubmit('submitUploadImageCompress') || Tools::isSubmit('submitBrowseImageOptimize') || Tools::isSubmit('btnSubmitCleaneImageUnUsed') || $controller=='AdminImageCompressorImage' || Tools::isSubmit('getPercentageImageOptimize'))
            $noconfig=false;
        else
            $noconfig=true;
        $total_image_product= Ets_imagecompressor_defines::getTotalImage('product',$total_all_type,false,$check_quality,$noconfig);
        $total_image_category = Ets_imagecompressor_defines::getTotalImage('category',$total_all_type,false,$check_quality,$noconfig);
        $total_image_manufacturer = Ets_imagecompressor_defines::getTotalImage('manufacturer',$total_all_type,false,$check_quality,$noconfig);
        $total_image_supplier = Ets_imagecompressor_defines::getTotalImage('supplier',$total_all_type,false,$check_quality,$noconfig);
        if($this->isblog)
        {
            $total_image_blog_post = Ets_imagecompressor_defines::getTotalImage('blog_post',$total_all_type,false,$check_quality,$noconfig);
            $total_image_blog_category = Ets_imagecompressor_defines::getTotalImage('blog_category',$total_all_type,false,$check_quality,$noconfig);
            $total_image_blog_gallery = Ets_imagecompressor_defines::getTotalImage('blog_gallery',$total_all_type,false,$check_quality,$noconfig);
            $total_image_blog_slide = Ets_imagecompressor_defines::getTotalImage('blog_slide',$total_all_type,false,$check_quality,$noconfig);
        }
        if($this->isSlide)
            $total_image_home_slide = Ets_imagecompressor_defines::getTotalImage('home_slide',$total_all_type,false,$check_quality,$noconfig);
        $total_image_product_optimizaed = Ets_imagecompressor_defines::getTotalImage('product',$total_all_type,true,$check_quality,$noconfig);
        $total_image_category_optimizaed = Ets_imagecompressor_defines::getTotalImage('category',$total_all_type,true,$check_quality,$noconfig);
        $total_image_manufacturer_optimizaed = Ets_imagecompressor_defines::getTotalImage('manufacturer',$total_all_type,true,$check_quality,$noconfig);
        $total_image_supplier_optimizaed = Ets_imagecompressor_defines::getTotalImage('supplier',$total_all_type,true,$check_quality,$noconfig);
        $total = ($total_image_product-$total_image_product_optimizaed)+($total_image_category-$total_image_category_optimizaed)+($total_image_manufacturer-$total_image_manufacturer_optimizaed)+($total_image_supplier-$total_image_supplier_optimizaed);  
        if($this->isblog)
        {
            $total_image_blog_post_optimizaed = Ets_imagecompressor_defines::getTotalImage('blog_post',$total_all_type,true,$check_quality,$noconfig);
            $total_image_blog_category_optimizaed = Ets_imagecompressor_defines::getTotalImage('blog_category',$total_all_type,true,$check_quality,$noconfig);
            $total_image_blog_gallery_optimizaed = Ets_imagecompressor_defines::getTotalImage('blog_gallery',$total_all_type,true,$check_quality,$noconfig);
            $total_image_blog_slide_optimizaed = Ets_imagecompressor_defines::getTotalImage('blog_slide',$total_all_type,true,$check_quality,$noconfig);
            $total += ($total_image_blog_slide - $total_image_blog_slide_optimizaed)+($total_image_blog_post-$total_image_blog_post_optimizaed) + ($total_image_blog_category-$total_image_blog_category_optimizaed) + ($total_image_blog_gallery-$total_image_blog_gallery_optimizaed);
        }
        if($this->isSlide)
        {
            $total_image_home_slide_optimizaed = Ets_imagecompressor_defines::getTotalImage('home_slide',$total_all_type,true,$check_quality,$noconfig);
            $total += ($total_image_home_slide-$total_image_home_slide_optimizaed);
        }
        $total_image_others = Ets_imagecompressor_defines::getTotalImage('others',$total_all_type,false,$check_quality,$noconfig);
        $total_image_others_optimizaed = Ets_imagecompressor_defines::getTotalImage('others',$total_all_type,true,$check_quality,$noconfig);
        $total += ($total_image_others-$total_image_others_optimizaed);
        $array['total_images'] = $total >0 ? $total :0;
        $array['quality_optimize'] = (int)Tools::getValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE'));
        $array['total_images_optimized']= $total_image_product_optimizaed+$total_image_category_optimizaed+$total_image_manufacturer_optimizaed+$total_image_supplier_optimizaed + ($this->isblog ? $total_image_blog_post_optimizaed+$total_image_blog_category_optimizaed+$total_image_blog_gallery_optimizaed+$total_image_blog_slide_optimizaed :0) +($this->isSlide ? $total_image_home_slide_optimizaed:0);
        $array['total_size_save'] = $this->getTotalSizeSave();
        $array['check_optimize'] = $this->checkOptimizeAllImage(true);
        return $array;
    }

    public function getOverrides()
    {
        if (!$this->is17)
        {
            if (!is_dir($this->getLocalPath().'override')) {
                return null;
            }
            $result = array();
            foreach (Tools::scandir($this->getLocalPath().'override', 'php', '', true) as $file) {
                $class = basename($file, '.php');
                if (PrestaShopAutoload::getInstance()->getClassPath($class.'Core') || Module::getModuleIdByName($class)) {
                    $result[] = $class;
                }
            }
            return $result;
        }
        else
            return parent::getOverrides();
    }

    public function addOverride($classname)
    {
        $_errors = array();
        $orig_path = $path = PrestaShopAutoload::getInstance()->getClassPath($classname.'Core');
        if (!$path) {
            $path = 'modules'.DIRECTORY_SEPARATOR.$classname.DIRECTORY_SEPARATOR.$classname.'.php';
        }
        $path_override = $this->getLocalPath().'override'.DIRECTORY_SEPARATOR.$path;
        if (!@file_exists($path_override)) {
            return true;
        } else {
            @file_put_contents($path_override, preg_replace('#(\r\n|\r)#ism', "\n", Tools::file_get_contents($path_override)));
        }
        $pattern_escape_com = '#(^\s*?\/\/.*?\n|\/\*(?!\n\s+\* module:.*?\* date:.*?\* version:.*?\*\/).*?\*\/)#ism';
        if ($file = PrestaShopAutoload::getInstance()->getClassPath($classname))
        {
            $override_path = _PS_ROOT_DIR_.'/'.$file;

            if ((!@file_exists($override_path) && !is_writable(dirname($override_path))) || (@file_exists($override_path) && !is_writable($override_path))) {
                $_errors[] = sprintf($this->l('file (%s) not writable'), $override_path);
            }
            do {
                $uniq = uniqid();
            } while (@class_exists($classname.'OverrideOriginal_remove', false));

            $override_file = file($override_path);
            $override_file = array_diff($override_file, array("\n"));
            $this->execCode(preg_replace(array('#^\s*<\?(?:php)?#', '#class\s+'.$classname.'\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?#i'), array(' ', 'class '.$classname.'OverrideOriginal'.$uniq.' extends \stdClass'), implode('', $override_file)));
            $override_class = new ReflectionClass($classname.'OverrideOriginal'.$uniq);

            $module_file = file($path_override);
            $module_file = array_diff($module_file, array("\n"));
            $this->execCode(preg_replace(array('#^\s*<\?(?:php)?#', '#class\s+'.$classname.'(\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?)?#i'), array(' ', 'class '.$classname.'Override'.$uniq.' extends \stdClass'), implode('', $module_file)));
            $module_class = new ReflectionClass($classname.'Override'.$uniq);

            foreach ($module_class->getMethods() as $method) {
                if ($override_class->hasMethod($method->getName())) {
                    $method_override = $override_class->getMethod($method->getName());
                    if (preg_match('/module: (.*)/ism', $override_file[$method_override->getStartLine() - 5], $name) && preg_match('/date: (.*)/ism', $override_file[$method_override->getStartLine() - 4], $date) && preg_match('/version: ([0-9.]+)/ism', $override_file[$method_override->getStartLine() - 3], $version)) {
                        $_errors[] = sprintf($this->l('The method %1$s in the class %2$s is already overridden by the module %3$s version %4$s at %5$s.'), $method->getName(), $classname, $name[1], $version[1], $date[1]);
                    } else {
                        $_errors[] = sprintf($this->l('The method %1$s in the class %2$s is already overridden.'), $method->getName(), $classname);
                    }
                }
                $module_file = preg_replace('/((:?public|private|protected)\s+(static\s+)?function\s+(?:\b'.$method->getName().'\b))/ism', "/*\n    * module: ".$this->name."\n    * date: ".date('Y-m-d H:i:s')."\n    * version: ".$this->version."\n    */\n    $1", $module_file);
                if ($module_file === null) {
                    $_errors[] = sprintf($this->l('Failed to override method %1$s in class %2$s.'), $method->getName(), $classname);
                }
            }
            if (!$_errors)
            {
                $copy_from = array_slice($module_file, $module_class->getStartLine() + 1, $module_class->getEndLine() - $module_class->getStartLine() - 2);
                array_splice($override_file, $override_class->getEndLine() - 1, 0, $copy_from);
                $code = implode('', $override_file);

                @file_put_contents($override_path, preg_replace($pattern_escape_com, '', $code));
            }
        }
        else
        {
            $override_src = $path_override;
            $override_dest = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'override'.DIRECTORY_SEPARATOR.$path;
            $dir_name = dirname($override_dest);
            if (!$orig_path && !is_dir($dir_name)) {
                $oldumask = umask(0000);
                @mkdir($dir_name, 0777);
                umask($oldumask);
            }
            if (!is_writable($dir_name)) {
                $_errors[] = sprintf($this->l('directory (%s) not writable'), $dir_name);
            }
            $module_file = file($override_src);
            $module_file = array_diff($module_file, array("\n"));
            if ($orig_path) {
                do {
                    $uniq = uniqid();
                } while (@class_exists($classname.'OverrideOriginal_remove', false));
                $this->execCode(preg_replace(array('#^\s*<\?(?:php)?#', '#class\s+'.$classname.'(\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?)?#i'), array(' ', 'class '.$classname.'Override'.$uniq.' extends \stdClass'), implode('', $module_file)));
                $module_class = new ReflectionClass($classname.'Override'.$uniq);

                foreach ($module_class->getMethods() as $method) {
                    $module_file = preg_replace('/((:?public|private|protected)\s+(static\s+)?function\s+(?:\b'.$method->getName().'\b))/ism', "/*\n    * module: ".$this->name."\n    * date: ".date('Y-m-d H:i:s')."\n    * version: ".$this->version."\n    */\n    $1", $module_file);
                    if ($module_file === null) {
                        $_errors[] = sprintf($this->l('Failed to override method %1$s in class %2$s.'), $method->getName(), $classname);
                    }
                }
            }
            if (!$_errors)
            {
                @file_put_contents($override_dest, preg_replace($pattern_escape_com, '', $module_file));
                Tools::generateIndex();
            }
        }
        if ($_errors)
            $this->logInstall($classname, $_errors);
        return true;
    }

    public function execCode($php_code)
    {
        if(function_exists('ets_imagecompressor_execute_php'))
            call_user_func('ets_imagecompressor_execute_php',$php_code);
        else
        {
            $temp = @tempnam($this->getLocalPath().'cache', 'execCode');
            $handle = fopen($temp, "w+");
            fwrite($handle, "<?php\n" . $php_code);
            fclose($handle);
            if(file_exists($temp))
            {
                include $temp;
                @unlink($temp);
            }

        }
    }

    public function removeOverride($classname)
    {
        if ($this->isLogInstall($classname))
            return true;
        $orig_path = $path = PrestaShopAutoload::getInstance()->getClassPath($classname.'Core');
        if ($orig_path && !$file = PrestaShopAutoload::getInstance()->getClassPath($classname))
            return true;
        elseif (!$orig_path && Module::getModuleIdByName($classname))
            $path = 'modules'.DIRECTORY_SEPARATOR.$classname.DIRECTORY_SEPARATOR.$classname.'.php';
        $override_path = $orig_path? _PS_ROOT_DIR_.'/'.$file : _PS_OVERRIDE_DIR_.$path;
        if (!@is_file($override_path) || !is_writable($override_path))
            return true;
        return parent::removeOverride($classname);
    }

    public $log_file = 'cache/install.log';

    public function logInstall($classname, $_errors)
    {
        $log_file = $this->getLocalPath().$this->log_file;
        $data = array();
        if (@file_exists($log_file))
            $data = (array)json_decode(Tools::file_get_contents($log_file),true);
        $data[$classname] = $_errors;
        @file_put_contents($log_file, json_encode($data));
    }

    public function isLogInstall($classname)
    {
        $log_file = $this->getLocalPath().$this->log_file;
        if (!@file_exists($log_file))
            return false;
        $cached = (array)json_decode(Tools::file_get_contents($log_file),true);
        if ($cached && !empty($cached[$classname]))
            return true;
        return false;
    }

    public function clearLogInstall()
    {
        $log_file = $this->getLocalPath().$this->log_file;
        if (@file_exists($log_file))
            @unlink($log_file);
        return true;
    }
    public function getTotalSizeSave()
    {
        $quality = (int)Tools::getValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE'));
        $controller = Tools::getValue('controller');
        if(Validate::isControllerName($controller) && ($controller!='AdminImageCompressorImage' || Tools::isSubmit('ajax')) && !Tools::isSubmit('getPercentageAllImageOptimize') && $quality==100 )
            $check_quality = false;
        else
            $check_quality = true;
        return Ets_imagecompressor_optimize::getInstance()->getTotalSizeSave($quality,$check_quality);
    }
    public function checkOptimizeAllImage($check_quality=false)
    {
        $total_image_product= Ets_imagecompressor_defines::getTotalImage('product',true,false,$check_quality,true);
        $total_image_category = Ets_imagecompressor_defines::getTotalImage('category',true,false,$check_quality,true);
        $total_image_manufacturer = Ets_imagecompressor_defines::getTotalImage('manufacturer',true,false,$check_quality,true);
        $total_image_supplier = Ets_imagecompressor_defines::getTotalImage('supplier',true,false,$check_quality,true);
        $total_image_product_optimizaed = Ets_imagecompressor_defines::getTotalImage('product',true,true,$check_quality,true);
        $total_image_category_optimizaed = Ets_imagecompressor_defines::getTotalImage('category',true,true,$check_quality,true);
        $total_image_manufacturer_optimizaed = Ets_imagecompressor_defines::getTotalImage('manufacturer',true,true,$check_quality,true);
        $total_image_supplier_optimizaed = Ets_imagecompressor_defines::getTotalImage('supplier',true,true,$check_quality,true);
        $total_images = $total_image_product + $total_image_category + $total_image_manufacturer + $total_image_supplier;
        $total_optimized_images = $total_image_category_optimizaed + $total_image_product_optimizaed + $total_image_supplier_optimizaed + $total_image_manufacturer_optimizaed;
        if($this->isblog)
        {
            $total_image_blog_post= Ets_imagecompressor_defines::getTotalImage('blog_post',true,false,$check_quality,true);
            $total_image_blog_category = Ets_imagecompressor_defines::getTotalImage('blog_category',true,false,$check_quality,true);
            $total_image_blog_gallery = Ets_imagecompressor_defines::getTotalImage('blog_gallery',true,false,$check_quality,true);
            $total_image_blog_slide = Ets_imagecompressor_defines::getTotalImage('blog_slide',true,false,$check_quality,true);
            $total_image_blog_post_optimizaed = Ets_imagecompressor_defines::getTotalImage('blog_post',true,true,$check_quality,true);
            $total_image_blog_category_optimizaed = Ets_imagecompressor_defines::getTotalImage('blog_category',true,true,$check_quality,true);
            $total_image_blog_gallery_optimizaed = Ets_imagecompressor_defines::getTotalImage('blog_gallery',true,true,$check_quality,true);
            $total_image_blog_slide_optimizaed = Ets_imagecompressor_defines::getTotalImage('blog_slide',true,true,$check_quality,true);
            $total_images += $total_image_blog_post + $total_image_blog_category + $total_image_blog_gallery + $total_image_blog_slide;
            $total_optimized_images += $total_image_blog_post_optimizaed + $total_image_blog_category_optimizaed + $total_image_blog_gallery_optimizaed + $total_image_blog_slide_optimizaed;
        }
        if($this->isSlide)
        {
            $total_image_home_slide= Ets_imagecompressor_defines::getTotalImage('home_slide',true,false,$check_quality,true);
            $total_image_home_slide_optimizaed = Ets_imagecompressor_defines::getTotalImage('home_slide',true,true,$check_quality,true);
            $total_images += $total_image_home_slide;
            $total_optimized_images += $total_image_home_slide_optimizaed; 
        }
        $total_image_others = Ets_imagecompressor_defines::getTotalImage('others',true,false,$check_quality,true);
        $total_image_others_optimizaed = Ets_imagecompressor_defines::getTotalImage('others',true,true,$check_quality,true);
        $total_images += $total_image_others;
        $total_optimized_images += $total_image_others_optimizaed; 
        $total_unoptimized_images = $total_images - $total_optimized_images;
        if($total_unoptimized_images==0)
            return $total_images;
        else
            return false;
    }
    public function checkCreatedColumn($table,$column)
    {
        $fieldsCustomers = Ets_imagecompressor_defines::getFieldsTable($table);
        $check_add=false;
        foreach($fieldsCustomers as $field)
        {
            if($field['Field']==$column)
            {
                $check_add=true;
                break;
            }    
        }
        return $check_add;
    }
    public function hookActionUpdateBlogImage($params)
    {
        Ets_imagecompressor_optimize::getInstance()->actionUpdateBlogImage($params);
    }
    public function ajaxSubmitOptimizeImage($all_type)
    {
        if(!Tools::isSubmit('resume'))
        {
            Configuration::updateValue('ETS_SPEEP_RESUMSH',2);
            Configuration::updateValue('ETS_IMGCOMPRESSOR_ERRORS_TINYPNG','');
            Configuration::updateValue('ETS_IMGCOMPRESSOR_TOTAL_IMAGE_OPTIMIZED',0);
            Configuration::updateValue('ETS_IMGCOMPRESSOR_LIST_IMAGE_OPTIMIZED','');
        }
        $optimize_type  = Tools::getValue('optimize_type','products');
        if($this->isblog)
            $ybc_blog = Module::getInstanceByName('ybc_blog');
        switch ($optimize_type) {
            case 'products':
                $this->optimizeProductImage($all_type);
            case 'categories':
                $this->optimiziObjImage('category','categories',_PS_CAT_IMG_DIR_,$all_type,'manufacturers');
            case 'manufacturers':
                $this->optimiziObjImage('manufacturer','manufacturers',_PS_MANU_IMG_DIR_,$all_type,'suppliers');
            case 'suppliers':
                $next= $this->isblog ? 'post' : ($this->isSlide ? 'home_slide' :'other_image');
                $this->optimiziObjImage('supplier','suppliers',_PS_SUPP_IMG_DIR_,$all_type,$next);
            case 'post':
                if($this->isblog)
                    $this->optimiziBlogImage('post',version_compare($ybc_blog->version, '3.2.0', '>') ? _PS_YBC_BLOG_IMG_DIR_.'post/': _PS_MODULE_DIR_.'ybc_blog/views/img/post/',$all_type,'category');
            case 'category':
                if($this->isblog)
                    $this->optimiziBlogImage('category',version_compare($ybc_blog->version, '3.2.0', '>') ? _PS_YBC_BLOG_IMG_DIR_.'category/':_PS_MODULE_DIR_.'ybc_blog/views/img/category/',$all_type,'gallery');
            case 'gallery':
                if($this->isblog)
                    $this->optimiziBlogImage('gallery',version_compare($ybc_blog->version, '3.2.0', '>') ? _PS_YBC_BLOG_IMG_DIR_.'gallery/':_PS_MODULE_DIR_.'ybc_blog/views/img/gallery/',$all_type,'slide');
            case 'slide':
                if($this->isblog)
                {
                    $next = $this->isSlide ? 'home_slide' :'other_image';
                    $this->optimiziBlogImage('slide',version_compare($ybc_blog->version, '3.2.0', '>') ? _PS_YBC_BLOG_IMG_DIR_.'slide/':_PS_MODULE_DIR_.'ybc_blog/views/img/slide/',$all_type,$next); 
                }
            case 'home_slide':
                if($this->isSlide)
                    $this->optimiziSlideImage($all_type);
            case 'other_image':
                $this->optimiziOthersImage($all_type);
        }
    }
    public function displayError($errors,$popup=false)
    {
        $this->context->smarty->assign(
            array(
                'errors'=>$errors,
                'popup' => $popup
            )
        );
        return $this->display(__FILE__,'error.tpl');
    }
    public function displayGoogleError()
    {
        return $this->display(__FILE__,'google.tpl');
    }
    public function hookDisplayImagesBrowse()
    {
        $dir_files = $this->globImagesToFolder(_PS_ROOT_DIR_);
        $images = Ets_imagecompressor_browse_image::getBrowseImages();
        if($images)
        {
            foreach($images as &$image)
            {
                $image['saved'] = Tools::ps_round(($image['old_size']-$image['new_size'])*100/$image['old_size'],2).'%';
                $image['old_size'] = $image['old_size'] < 1024 ? $image['old_size'].'KB' : Tools::ps_round($image['old_size']/2014,2).'MB';
                $image['new_size'] = $image['new_size'] < 1024 ? $image['new_size'].'KB' : Tools::ps_round($image['new_size']/2014,2).'MB';
                $image['image_dir'] = str_replace(str_replace('\\','/',_PS_ROOT_DIR_),'',$image['image_dir']);
                $image['image_name_hide'] = Tools::strlen($image['image_name']) > 23 ? Tools::substr($image['image_name'],0,11).' . . . '.Tools::substr($image['image_name'],Tools::strlen($image['image_name'])-12):$image['image_name'];
            }
        }
        $this->context->smarty->assign(
            array(
                'dir_files' => $dir_files,
                'images' => $images,
            )
        ); 
        return $this->display(__FILE__,'browse_images.tpl');
    }
    public function globImagesToFolder($folder)
    {
        $files = glob($folder.'/*'); 
        $list_files= array();
        $list_folders= array();
        foreach($files as $file){ 
            $name= explode('/',$file);
            if(is_file($file))
            {
                $type = Tools::strtolower(Tools::substr(strrchr($file, '.'), 1));
                if(in_array($type, array('jpg', 'gif', 'jpeg', 'png')) && Tools::strpos($file,'_bk.'.$type)===false)
                {
                    $file_size = Tools::ps_round(@filesize($file)/1024,2);
                    $file_id = MD5(str_replace('\\','/',$file));
                    if(Ets_imagecompressor_browse_image::getBrowseImageByImageId($file_id))
                        $uploaed = true;
                    else
                        $uploaed = false;
                    $list_files[] = array(
                        'dir' => str_replace('\\','/',$file),
                        'id' => $file_id,
                        'name' =>$name[count($name)-1], 
                        'type'=>'file',
                        'uploaed' =>$uploaed,
                        'file_size' => $file_size <1024 ? $file_size.'KB' : Tools::ps_round($file_size/1024,2).'MB',
                    );
                }
            }
            elseif(Tools::strpos($file,'ss_imagesoptimize')===false)
            {
                $list_folders[] = array(
                    'dir' => str_replace('\\','/',$file),
                    'name' =>$name[count($name)-1],
                    'type' => 'folder', 
                    'id' => MD5(str_replace('\\','/',$file)),
                    'has_file' => $this->checkHasFileInFolder($file),
                );
            }
        }
        $this->context->smarty->assign(
            array(
                'list_files' => array_merge($list_folders,$list_files),
            )
        );
        return $this->display(__FILE__,'dir_list_files.tpl');
    }
    public function checkHasFileInFolder($folder)
    {
        $files = glob($folder.'/*'); 
        foreach($files as $file){ 
            if(is_file($file))
            {
                $type = Tools::strtolower(Tools::substr(strrchr($file, '.'), 1));
                if(in_array($type, array('jpg', 'gif', 'jpeg', 'png')) && Tools::strpos($file,'_bk.'.$type)===false)
                {
                    return true;
                }
            }
        }
        return false;
    }
    public function checkKeyTinyPNG(){
        if(($api_keys = Tools::getValue('ETS_IMGCOMPRESSOR_API_TYNY_KEY')) && Ets_imagecompressor::validateArray($api_keys))
        {
              $keys = array();  
              foreach($api_keys as $key)
              {
                    if(trim($key))
                        $keys[]=$key;
              }
              if($keys)
              {
                    Configuration::updateValue('ETS_IMGCOMPRESSOR_API_TYNY_KEY',implode(';',$keys)); 
                    return true;
              }
                     
        }
        die(
            json_encode(
                array(
                    'errors' => $this->displayError($this->l('Tinypng API key is required'))
                )
            )
        ); 
    }
    public function hookDisplayImagesUploaded()
    {
        $images = Ets_imagecompressor_upload_image::getImagesUploaed();
        if($images)
        {
            foreach($images as &$image)
            {
                $image['saved'] = Tools::ps_round(($image['old_size'] - $image['new_size'])*100/$image['old_size'],2).'%';
                $image['old_size'] = $image['old_size']<1024 ? $image['old_size'].'KB' : Tools::ps_round($image['old_size']/1024,2).'MB';
                $image['new_size'] = $image['new_size']<1024 ? $image['new_size'].'KB' : Tools::ps_round($image['new_size']/1024,2).'MB';
                $image['image_name_hide'] = Tools::strlen($image['image_name'])> 23 ? Tools::substr($image['image_name'],0,11).' . . . '.Tools::substr($image['image_name'],Tools::strlen($image['image_name'])-12):$image['image_name'];
            }
        }
        $this->context->smarty->assign(
            array(
                'images' => $images,
            )
        );
        return $this->display(__FILE__,'images.tpl');
    }
    public function getImagesUnUsed($folder='c',$table='category',$primakey='id_category',$image_type='categories',$delete=false)
    {
        return Ets_imagecompressor_optimize::getImagesUnUsed($folder,$table,$primakey,$image_type,$delete);
    }
    public function getImagesProductUnUsed($delete=false)
    {
        return Ets_imagecompressor_optimize::getImagesProductUnUsed($delete);
    }
    public function hookDisplayImagesCleaner()
    {
        $image_category =$this->getImagesUnUsed();
        $image_supplier = $this->getImagesUnUsed('su','supplier','id_supplier','suppliers');
        $image_manufacturer = $this->getImagesUnUsed('m','manufacturer','id_manufacturer','manufacturers');
        $image_product = $this->getImagesProductUnUsed();
        $this->context->smarty->assign(
            array(
                'image_category' => $image_category,
                'image_supplier'=> $image_supplier,
                'image_manufacturer'=> $image_manufacturer,
                'image_product' => $image_product,
            )
        );
        return $this->display(__FILE__,'image_cleaner.tpl');
    }
    public function replaceTemplateProductDefault($delete_cache = true)
    {
        if($this->is17)
        {
            $product_tpl = _PS_THEME_DIR_.'templates/catalog/_partials/miniatures/product.tpl';
            $product_tpl_bk = _PS_THEME_DIR_.'templates/catalog/_partials/miniatures/product.ssbackup.tpl';
            if(file_exists(_PS_THEME_DIR_.'modules/ps_imageslider/views/templates/hook/slider.tpl'))
                $slide_tpl= _PS_THEME_DIR_.'modules/ps_imageslider/views/templates/hook/slider.tpl';
            else    
                $slide_tpl= _PS_MODULE_DIR_.'ps_imageslider/views/templates/hook/slider.tpl';
            $slide_tpl_bk= _PS_MODULE_DIR_.'ps_imageslider/views/templates/hook/slider.ssbackup.tpl';
            
            if(file_exists(_PS_THEME_DIR_.'modules/ps_banner/ps_banner.tpl'))
                $banner_tpl= _PS_THEME_DIR_.'modules/ps_banner/ps_banner.tpl';
            else    
                $banner_tpl= _PS_MODULE_DIR_.'ps_banner/ps_banner.tpl';
            $banner_tpl_bk = _PS_MODULE_DIR_.'ps_banner/ps_banner.ssbackup.tpl';
        }
        elseif($this->is16)
        {
            $product_tpl = _PS_THEME_DIR_.'product-list.tpl';
            $product_tpl_bk = _PS_THEME_DIR_.'product-list.ssbackup.tpl';
            if(file_exists(_PS_THEME_DIR_.'modules/homeslider/homeslider.tpl'))
                $slide_tpl= _PS_THEME_DIR_.'modules/homeslider/homeslider.tpl';
            else    
                $slide_tpl= _PS_MODULE_DIR_.'homeslider/views/templates/hook/homeslider.tpl';
            $slide_tpl_bk= _PS_MODULE_DIR_.'homeslider/views/templates/hook/homeslider.ssbackup.tpl';
            
            if(file_exists(_PS_THEME_DIR_.'modules/blockbanner/blockbanner.tpl'))
                $banner_tpl= _PS_THEME_DIR_.'modules/blockbanner/blockbanner.tpl';
            else    
                $banner_tpl= _PS_MODULE_DIR_.'blockbanner/blockbanner.tpl';
            $banner_tpl_bk = _PS_MODULE_DIR_.'blockbanner/blockbanner.ssbackup.tpl';
            if(file_exists(_PS_THEME_DIR_.'modules/themeconfigurator/views/templates/hook/hook.tpl'))
                $themeconfigurator_tpl= _PS_THEME_DIR_.'modules/themeconfigurator/views/templates/hook/hook.tpl';
            else    
                $themeconfigurator_tpl= _PS_MODULE_DIR_.'themeconfigurator/views/templates/hook/hook.tpl';
            $themeconfigurator_tpl_bk = _PS_MODULE_DIR_.'themeconfigurator/views/templates/hook/hook.ssbackup.tpl';
        }
        if((int)Configuration::get('ETS_IMGCOMPRESSOR_ENABLE_LAYZY_LOAD'))
        {
            if(Configuration::get('ETS_IMGCOMPRESSOR_LAZY_FOR'))
                $image_for = explode(',',Configuration::get('ETS_IMGCOMPRESSOR_LAZY_FOR'));
            else
                $image_for = array();
            $bloklazyload = Tools::file_get_contents(dirname(__FILE__).'/views/templates/hook/blocklazyload.txt');
            $preg_replace_text = '/<' . 'img(.*?)\ssrc(.*?)=(.*?)(")(.*?)(")(.*?>)/is';
            if(in_array('product_list',$image_for))
            {
                if(file_exists($product_tpl) && !file_exists($product_tpl_bk))
                {
                    Tools::copy($product_tpl,$product_tpl_bk);
                    $content = Tools::file_get_contents($product_tpl);
                    if($this->is17)
                        $content = preg_replace($preg_replace_text,'<' . 'img' . ' src="{if isset($ets_link_base)}{$ets_link_base}/modules/'.$this->name.'/views/img/preloading.png{else}$5{/if}" class="lazyload" data-src="$5"$7'.$bloklazyload,$content);
                    else
                        $content = preg_replace($preg_replace_text,'<' .'img' . ' src="{if isset($ets_link_base)}{$ets_link_base}/modules/'.$this->name.'/views/img/preloading.png{else}$5{/if}" class="replace-2x img-responsive lazyload" data-src="$5"$7'.$bloklazyload,$content);
                    file_put_contents($product_tpl,$content);
                }
            }
            elseif(file_exists($product_tpl_bk))
            {
                Tools::copy($product_tpl_bk,$product_tpl);
                @unlink($product_tpl_bk);
            }
            if(in_array('home_slide',$image_for))
            {
                if(file_exists($slide_tpl) && !file_exists($slide_tpl_bk))
                {
                    Tools::copy($slide_tpl,$slide_tpl_bk);
                    $content = Tools::file_get_contents($slide_tpl);
                    $content = preg_replace($preg_replace_text,'<' . 'img' . ' src="{if isset($ets_link_base)}{$ets_link_base}/modules/'.$this->name.'/views/img/preloading.png{else}$5{/if}" class="lazyload" data-src="$5"$7'.$bloklazyload,$content);
                    file_put_contents($slide_tpl,$content);
                }
            }
            elseif(file_exists($slide_tpl_bk))
            {
                Tools::copy($slide_tpl_bk,$slide_tpl);
                    @unlink($slide_tpl_bk);
            }
            if(in_array('home_banner',$image_for))
            {
                if(file_exists($banner_tpl) && !file_exists($banner_tpl_bk))
                {
                    if(file_exists($banner_tpl))
                        Tools::copy($banner_tpl,$banner_tpl_bk);
                    $content = Tools::file_get_contents($banner_tpl);
                    $content = preg_replace($preg_replace_text,'<' . 'img' . ' src="{if isset($ets_link_base)}{$ets_link_base}/modules/'.$this->name.'/views/img/preloading.png{else}$5{/if}" class="lazyload" data-src="$5"$7'.$bloklazyload,$content);
                    file_put_contents($banner_tpl,$content);
                }
            }
            elseif(file_exists($banner_tpl_bk))
            {
                Tools::copy($banner_tpl_bk,$banner_tpl);
                @unlink($banner_tpl_bk);
            } 
            if($this->is16)
            {
                if(in_array('home_themeconfig',$image_for))
                {
                    if(file_exists($themeconfigurator_tpl) && !file_exists($themeconfigurator_tpl_bk))
                    {
                        if(file_exists($themeconfigurator_tpl))
                            Tools::copy($themeconfigurator_tpl,$themeconfigurator_tpl_bk);
                        $content = Tools::file_get_contents($themeconfigurator_tpl);
                        $content = preg_replace($preg_replace_text,'<' . 'img' . ' src="{if isset($ets_link_base)}{$ets_link_base}/modules/'.$this->name.'/views/img/preloading.png{else}$5{/if}" class="lazyload" data-src="$5"$7'.$bloklazyload,$content);
                        file_put_contents($themeconfigurator_tpl,$content);
                    }
                }
                elseif(file_exists($themeconfigurator_tpl_bk))
                {
                    Tools::copy($themeconfigurator_tpl_bk,$themeconfigurator_tpl);
                    @unlink($themeconfigurator_tpl_bk);
                }
            }
            
        }
        else
        {
            if(file_exists($product_tpl_bk))
            {
                Tools::copy($product_tpl_bk,$product_tpl);
                @unlink($product_tpl_bk);
            }
            if(file_exists($banner_tpl_bk))
            {
                Tools::copy($banner_tpl_bk,$banner_tpl);
                @unlink($banner_tpl_bk);
            }
            if(file_exists($slide_tpl_bk))
            {
                Tools::copy($slide_tpl_bk,$slide_tpl);
                @unlink($slide_tpl_bk);
            }
            if($this->is16 &&  file_exists($themeconfigurator_tpl_bk))
            {
                Tools::copy($themeconfigurator_tpl_bk,$themeconfigurator_tpl);
                @unlink($themeconfigurator_tpl_bk);
            }
        }
        if($delete_cache)
        {
            Tools::clearSmartyCache();
            Tools::clearXMLCache();
            Media::clearCache();
            if(Module::isEnabled('ets_homecategories'))
            {
                $ets_homecategories = Module::getInstanceByName('ets_homecategories');
                if(method_exists($ets_homecategories,'clearCache'))
                    $ets_homecategories->clearCache();
            }
        }
        
        return true;
    }
    public function rmDir($directory)
    {
        if(is_dir($directory))
        {
            $dir = @opendir(trim($directory,'/'));
            while (false !== ($file = @readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($directory . '/' . $file)) {
                        $this->rmDir($directory . '/' . $file);
                    } else {
                        if (file_exists($directory . '/' . $file)) {
                            @unlink($directory . '/' . $file);
                        }
                    }
                }
            }
            @closedir($dir);
        }
        return true;
    }
    public static function validateArray($array,$validate='isCleanHtml')
    {
        if(!is_array($array))
            return false;
        if(method_exists('Validate',$validate))
        {
            if($array && is_array($array))
            {
                $ok= true;
                foreach($array as $val)
                {
                    if(!is_array($val))
                    {
                        if($val && !Validate::$validate($val))
                        {
                            $ok= false;
                            break;
                        }
                    }
                    else
                        $ok = self::validateArray($val,$validate);
                }
                return $ok;
            }
        }
        return true;
    }
    public function displayHtml($content,$tag,$class=null,$id=null,$href=null,$blank=false)
    {
        $this->smarty->assign(array(
            'content' => $content,
            'tag' => $tag,
            'class' => $class,
            'id' => $id,
            'href' => $href,
            'blank' => $blank,
        ));
        return $this->display(__FILE__, 'html.tpl');
    }
    public function hookActionHtaccessCreate()
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            call_user_func('Ets_imagecompressor_generateHtaccess17');
        } else
            call_user_func('Ets_imagecompressor_generateHtaccess16');
        call_user_func('Ets_imagecompressor_generateHtaccessIMG');
        return true;
    }
    public function hookActionOnImageResizeAfter($params)
    {
        if($this->is17 && isset($params['dst_file']) && ($destinationFile = $params['dst_file']))
        {
            if(strpos($destinationFile,_PS_CAT_IMG_DIR_)===0 && ($id_category = (int)str_replace(_PS_CAT_IMG_DIR_,'',$destinationFile)))
            {
                return Ets_imagecompressor_optimize::optimizeCategoryImage($id_category);
            }
            if(strpos($destinationFile,_PS_MANU_IMG_DIR_)===0 && ($id_manu = (int)str_replace(_PS_MANU_IMG_DIR_,'',$destinationFile)))
            {
                return Ets_imagecompressor_optimize::optimizeManufacturerImage($id_manu);
            }
            if(strpos($destinationFile,_PS_SUPP_IMG_DIR_)===0 && ($id_sup = (int)str_replace(_PS_SUPP_IMG_DIR_,'',$destinationFile)))
            {
                return Ets_imagecompressor_optimize::optimizeImageSupplier($id_sup);
            }
        }
    }
}