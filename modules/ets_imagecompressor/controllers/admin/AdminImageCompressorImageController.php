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
class AdminImageCompressorImageController extends ModuleAdminController
{
    public function __construct()
    {
       parent::__construct();
       $this->context= Context::getContext();
       $this->bootstrap = true;
    }
    public function initContent()
    {
        $this->module->_postImage();
        parent::initContent();
    }
    public function renderList()
    {
        $this->context->smarty->assign(
            array(
                'page' => 'speed_image',
                'html_form' =>$this->renderFromImageCache(),
            )
        );
        return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'admin.tpl');
    }
    public function renderFromImageCache()
    {
        $config_images = Ets_imagecompressor_defines::getInstance()->getFieldConfig('_config_images');
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Total Image Optimization Pro'),
                    'icon' => 'icon-envelope'
                ),
                'input' => $config_images,
                'submit' => array(
                    'title' =>Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE')==100 ? $this->l('Restore original images'): $this->l('Optimize existing images'),
                    'icon'=>'process-icon-cogs',
                ),
                'buttons'=>array(
                    array(
                        'name'=> 'btnSubmitLazyLoadImage',
                        'icon'=>'process-icon-save',
                        'title'=> $this->l('Save'),
                        'class'=>'pull-right',
                    ),
                    array(
                        'name'=> 'btnSubmitSaveOptimizeImage',
                        'icon'=>'process-icon-save',
                        'title'=> $this->l('Save'),
                        'class'=>'pull-left',
                    ),
                ),
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int)$this->module->id;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmitImageOptimize';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminImageCompressorImage',false);
        $helper->token = Tools::getAdminTokenLite('AdminImageCompressorImage');
        $helper->module = $this->module;
        $install_logs= file_exists(dirname(__FILE__).'/../../cache/install.log') ? array_keys(json_decode(Tools::file_get_contents(dirname(__FILE__).'/../../cache/install.log'),true)):false;
        if($install_logs)
        {
            foreach($install_logs as $key=>$log)
                if(!in_array($log,array('AdminCategoriesController','AdminManufacturersController','AdminSuppliersController')))
                    unset($install_logs[$key]);
                else
                    $install_logs[$key] .='.php';
        }
        $tpl_vars=array(
            'fields_value' => array_merge($this->getFieldsValues($config_images),array('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_UPLOAD'=> Configuration::get('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_UPLOAD'),'ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_UPLOAD'=> Configuration::get('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_UPLOAD'),'ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_BROWSE'=>Configuration::get('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_BROWSE'),'ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_BROWSE'=>Configuration::get('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE_BROWSE'))),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'dir_override' => _PS_OVERRIDE_DIR_,
            'sp_dir_override' => dirname(__FILE__).'/../../override/',
            'configTabs' => Ets_imagecompressor_defines::getInstance()->getFieldConfig('_cache_image_tabs'),
            'ETS_IMGCOMPRESSOR_API_TYNY_KEY' => explode(';',Configuration::get('ETS_IMGCOMPRESSOR_API_TYNY_KEY')),
            'install_logs' => $install_logs ? implode(', ',$install_logs) : false,
        );
        $images= $this->module->getImageOptimize(true);
        $helper->tpl_vars = array_merge($tpl_vars,$images);
        return $helper->generateForm(array($fields_form));
    }
    public function getFieldsValues($formFields){
        $values=array();
        foreach($formFields as $field)
        {
            if($field['type']=='checkbox')
            {
                $values[$field['name']] = Tools::getValue($field['name'], explode(',', isset($field['isGlobal']) && $field['isGlobal'] ? Configuration::getGlobalValue($field['name']) :  Configuration::get($field['name']) ));
            }

            else
                $values[$field['name']] = Tools::getValue($field['name'],isset($field['isGlobal']) && $field['isGlobal'] ? Configuration::getGlobalValue($field['name']) :  Configuration::get($field['name']) );
        }
        return $values;
    }
}