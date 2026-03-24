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
class Ets_imagecompressor_defines
{
    protected static $instance;
    protected $isblog;
    protected $isSlide;
    protected $isBanner;
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Ets_imagecompressor_defines();
        }
        return self::$instance;
    }
    public function __construct()
	{
        if(Module::isInstalled('ybc_blog') && Module::isEnabled('ybc_blog'))
            $this->isblog = true;
        else
            $this->isblog = false;
        if((Module::isInstalled('ps_imageslider') && Module::isEnabled('ps_imageslider')) ||  (Module::isInstalled('homeslider') && Module::isEnabled('homeslider')))
            $this->isSlide = true;
        else
            $this->isSlide = false;
        if((Module::isInstalled('blockbanner') && Module::isEnabled('blockbanner')) ||  (Module::isInstalled('ps_banner') && Module::isEnabled('ps_banner')))
            $this->isBanner = true;
        else
            $this->isBanner = false;
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_imagecompressor', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public function getFieldConfig($field_type)
    {
        switch ($field_type) {
            case '_hooks':
                return array(
                    'actionWatermark',
                    'displayAdminLeft',
                    'displayBackOfficeHeader',
                    'displayHeader',
                    'displayImagesBrowse',
                    'displayImagesUploaded',
                    'displayImagesCleaner',
                    'actionHtaccessCreate',
                    'actionOnImageResizeAfter'
                );
            case '_admin_tabs':
                return array(
                    array(
                        'class_name' => 'AdminImageCompressorImage',
                        'tab_name' => $this->l('Optimize images'),
                        'icon'=>'icon icon-speedimage'
                    )
                );
            case '_config_images':
                return $this->getFieldImageconfig();  
            case '_cache_image_tabs':
                return  array(
                        'image_old' => $this->l('Optimize images'),
                        'image_upload'=> $this->l('Upload to optimize'),
                        'image_browse' => $this->l('Browse images'),
                        'image_cleaner' => $this->l('Image cleaner'),
                        'image_lazy_load' => $this->l('Lazy load'),
                    );
        } 
    }
    public function getFieldImageconfig()
    {
        $lazys =array(
            array(
                'value' => 'product_list',
                'label' => $this->l('Product listing')
            )
        );
        if($this->isSlide)
        {
            $lazys[] = array(
                'value' => 'home_slide',
                'label' => $this->l('Home slider')
            );
        }
        if($this->isBanner)
        {
            $lazys[] = array(
                'value' => 'home_banner',
                'label' => $this->l('Home banner')
            );
        }
        if(Module::isInstalled('themeconfigurator') && Module::isEnabled('themeconfigurator'))
        {
            $lazys[] = array(
                'value' => 'home_themeconfig',
                'label' => $this->l('Home themeconfigurator')
            );
        }
        $config_images=array(
            array(
				'type' => 'switch',
				'label' => $this->l('Enable lazy load'),
				'name' => 'ETS_IMGCOMPRESSOR_ENABLE_LAYZY_LOAD',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
					)
				),
                'form_group_class'=>'form_cache_page image_lazy_load',
			),
            array(
                'type' => 'radio',
				'label' => $this->l('Preloading image'),
				'name' => 'ETS_IMGCOMPRESSOR_LOADING_IMAGE_TYPE',
                'default' => 'type_1',
				'values' => array(
					array(
						'id' => 'type_1',
						'value' => 'type_1',
						'label' => $this->l('Type 1'),
                        'html' => Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','spinner_1'),
					),
					array(
						'id' => 'type_2',
						'value' => 'type_2',
						'label' => $this->l('Type 2'),
                        'html' => Module::getInstanceByName('ets_imagecompressor')->displayHtml(
                            Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div',''),
                            'div','lds-ring'),
					),
                    array(
                        'id' => 'type_3',
                        'value' => 'type_3',
                        'label' => $this->l('Type 3'),
                        'html' => Module::getInstanceByName('ets_imagecompressor')->displayHtml(
                            Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div',''),
                            'div','lds-roller'),
                    ),
                    array(
                        'id' => 'type_4',
                        'value' => 'type_4',
                        'label' => $this->l('Type 4'),
                        'html' => Module::getInstanceByName('ets_imagecompressor')->displayHtml(
                            Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div',''),
                            'div','lds-ellipsis'),
                    ),
                    array(
                        'id' => 'type_5',
                        'value' => 'type_5',
                        'label' => $this->l('Type 5'),
                        'html' => Module::getInstanceByName('ets_imagecompressor')->displayHtml(
                            Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div','').Module::getInstanceByName('ets_imagecompressor')->displayHtml('','div',''),
                            'div','lds-spinner'),
                    ),
				),
                'form_group_class'=>'form_cache_page image_lazy_load type',
            ),
            array(
                'type' => 'checkbox',
                'label' => $this ->l('Enable Lazy Load for'),
                'name' => 'ETS_IMGCOMPRESSOR_LAZY_FOR',
                'values' => array(
                    'query'=> $lazys,
                    'id' => 'value',
                    'name' => 'label',
                ),
                'form_group_class'=>'form_cache_page image_lazy_load',
            )
        );
        if($types = $this->getImageTypes('products'))
            $config_images[]=array(
                'type'=>'checkbox',
                'label' => $this->l('Product images'),
                'name'=>'ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_PRODCUT_TYPE',
                'values' => array(
                     'query' => $types,
                     'id' => 'value',
                     'name' => 'label'                                                               
                ),
                'default'=> $this->getImageTypes('products',true),
                'isGlobal'=> 1,
                'image_old'=>'product',
                'form_group_class'=>'form_cache_page image_old',
            );
        if($types = $this->getImageTypes('categories'))
            $config_images[]= array(
                'type'=>'checkbox',
                'label' => $this->l('Product category images'),
                'name'=>'ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_CATEGORY_TYPE',
                'values' => array(
                     'query' => $types,
                     'id' => 'value',
                     'name' => 'label'                                                               
                ),
                'isGlobal'=> 1,
                'default'=> $this->getImageTypes('categories',true),
                'image_old'=>'category',
                'form_group_class'=>'form_cache_page image_old',
            );
        if($types = $this->getImageTypes('suppliers'))
            $config_images[]= array(
                'type'=>'checkbox',
                'label' => $this->l('Supplier images'),
                'name'=>'ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_SUPPLIER_TYPE',
                'values' => array(
                     'query' => $types,
                     'id' => 'value',
                     'name' => 'label'                                                               
                ),
                'isGlobal'=> 1,
                'default'=> $this->getImageTypes('suppliers',true),
                'image_old'=>'supplier',
                'form_group_class'=>'form_cache_page image_old',
        );
        if($types = $this->getImageTypes('manufacturers'))
            $config_images[]=array(
                'type'=>'checkbox',
                'label' => $this->l('Manufacturer images'),
                'name'=>'ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_MANUFACTURER_TYPE',
                'values' => array(
                     'query' => $types,
                     'id' => 'value',
                     'name' => 'label'                                                               
                ),
                'isGlobal'=> 1,
                'default'=> $this->getImageTypes('manufacturers',true),
                'image_old'=>'manufacturer',
                'form_group_class'=>'form_cache_page image_old manufacturer',
            );
        
        if($this->isblog)
        {
            if($types = $this->getImageTypes('blog_post'))
                $config_images[]=array(
                    'type'=>'checkbox',
                    'label' => $this->l('Blog post images'),
                    'name'=>'ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_POST_TYPE',
                    'values' => array(
                         'query' => $types,
                         'id' => 'value',
                         'name' => 'label'                                                               
                    ),
                    'isGlobal'=> 1,
                    'default'=> $this->getImageTypes('blog_post',true),
                    'image_old'=>'blog_post',
                    'form_group_class'=>'form_cache_page image_old blog_post',
                );
            if($types = $this->getImageTypes('blog_category'))
                $config_images[]=array(
                    'type'=>'checkbox',
                    'label' => $this->l('Blog category images'),
                    'name'=>'ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_CATEGORY_TYPE',
                    'values' => array(
                         'query' => $types,
                         'id' => 'value',
                         'name' => 'label'                                                               
                    ),
                    'isGlobal'=> 1,
                    'default'=> $this->getImageTypes('blog_category',true),
                    'image_old'=>'blog_category',
                    'form_group_class'=>'form_cache_page image_old blog_category',
                );
            if($types = $this->getImageTypes('blog_gallery'))
                $config_images[]=array(
                    'type'=>'checkbox',
                    'label' => $this->l('Blog gallery & slider images'),
                    'name'=>'ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_GALLERY_TYPE',
                    'values' => array(
                         'query' => $types,
                         'id' => 'value',
                         'name' => 'label'                                                               
                    ),
                    'isGlobal'=> 1,
                    'default'=> $this->getImageTypes('blog_gallery',true),
                    'image_old'=>'blog_gallery',
                    'form_group_class'=>'form_cache_page image_old blog_gallery',
                );
            if($types = $this->getImageTypes('blog_slide'))
                $config_images[]=array(
                    'type'=>'checkbox',
                    'label' => $this->l('Slider images'),
                    'name'=>'ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_SLIDE_TYPE',
                    'values' => array(
                         'query' => $types,
                         'id' => 'value',
                         'name' => 'label'                                                               
                    ),
                    'isGlobal'=> 1,
                    'default'=> $this->getImageTypes('blog_slide',true),
                    'image_old'=>'blog_slide',
                    'form_group_class'=>'form_cache_page image_old blog_slide',
                );
        }
        if($this->isSlide)
        {
            if($types = $this->getImageTypes('home_slide'))
                $config_images[]=array(
                    'type'=>'checkbox',
                    'label' => $this->l('Home slider images'),
                    'name'=>'ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_HOME_SLIDE_TYPE',
                    'values' => array(
                         'query' => $types,
                         'id' => 'value',
                         'name' => 'label'                                                               
                    ),
                    'isGlobal'=> 1,
                    'default'=> $this->getImageTypes('home_slide',true),
                    'image_old'=>'home_slide',
                    'form_group_class'=>'form_cache_page image_old home_slide',
            );
        }
        $config_images[] = array(
            'type'=>'checkbox',
            'label' => $this->l('Others images'),
            'name'=>'ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_OTHERS_TYPE',
            'values' => array(
                 'query' => $this->getImageTypes('others'), 
                 'id' => 'value',
                 'name' => 'label'                                                               
            ),
            'isGlobal'=> 1,
            'default'=> $this->getImageTypes('others',true),
            'image_old'=>'others',
            'form_group_class'=>'form_cache_page image_old others',
        );
        $whitelist = array(
            '127.0.0.1',
            '::1'
        );
        $optimize_type = array(
            'type'=>'select',
            'label'=>$this->l('Image optimization method'),
            'name'=>'ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT',
            'options' => array(
                    'query' => array(
                        array(
                            'id_option' =>'php',
                            'name' => $this->l('PHP image optimization script')
                        ),
                        array(
                            'id_option' =>'resmush',
                            'name' => $this->l('Resmush - Free image optimization web service API')
                        ),
                        array(
                            'id_option' =>'tynypng',
                            'name' => $this->l('TinyPNG - Premium image optimization web service API (500 images for free per month)')
                        ),
                        array(
                            'id_option' =>'google',
                            'name' => $this->l('Google Webp image optimizer')
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name'
            ),
            'isGlobal'=> 1,
            'form_group_class'=>'form_cache_page image_old script',
            'default'=>'php',
		);
        if(in_array(Tools::getRemoteAddr(), $whitelist))
            unset($optimize_type['options']['query'][1]);
        $config_images[]= $optimize_type;
        $config_images[]= array(
                'type'=>'range',
                'label'=>$this->l('Image quality'),
                'name'=>'ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE',
                'min'=>'1',
                'max'=>'100',
                'unit' => '%',
                'units'=>'%',
                'isGlobal'=> 1,
                'form_group_class'=>'form_cache_page use_default form_group_range_small quality image_old',
                'desc' => $this->l('The higher image quality, the longer page loading time, 50% is recommended value. Setup image quality up to 100% will restore original images.'),
                'default'=>50,
		);
        $config_images[] = array(
			'type' => 'switch',
			'label' => $this->l('Change file extension to .webp for product images when converting?'),
			'name' => 'ETS_IMGCOMPRESSOR_ENABLE_WEBP_FORMAT',
			'values' => array(
				array(
					'id' => 'active_on',
					'value' => 1,
					'label' => $this->l('Yes')
				),
				array(
					'id' => 'active_off',
					'value' => 0,
					'label' => $this->l('No')
				)
			),
            'isGlobal'=> 1,
            'form_group_class'=>'form_cache_page image_old webp',
            'default' => 0,
		);
        $config_images[] = array(
				'type' => 'checkbox',
				'label' => '',
				'name' => 'ETS_IMGCOMPRESSOR_UPDATE_QUALITY',
                'isGlobal'=> 1,
                'values' => array(
                     'query' => array(
                        array(
                            'value' => 1,
                            'label' => $this->l('Do not reoptimized images that have been optimized with different image quality or image optimization method'),
                        )
                     ), 
                     'id' => 'value',
                     'name' => 'label'                                                               
                ),
                'form_group_class'=>'form_cache_page image_old update_quality',
                'default' => 1,
		);
        return $config_images;
    }
    public function getImageTypes($type='',$string=false,$get_total=false)
    {
        $is_install = Module::isInstalled('ets_imagecompressor');
        if($is_install || version_compare(_PS_VERSION_, '1.7', '<'))
        {
            if(in_array($type,array('products','manufacturers','categories','suppliers')))
            {
                $sql = 'SELECT name as value,name as label FROM `'._DB_PREFIX_.'image_type` '.($type ? ' WHERE '.pSQL($type).'=1' :'' );
                $image_types = Db::getInstance()->executeS($sql);
                
            }
            elseif($type=='home_slide' && $this->isSlide)
            {
                $image_types = array(
                    array(
                        'value'=> 'image',
                        'label' =>''
                    )
                );
            }
            elseif($type=='others')
            {
                $image_types = array(
                    array(
                        'value'=>'logo',
                        'label' => $this->l('Logo image')
                    ),
                    array(
                        'value'=>'banner',
                        'label' => $this->l('Banner image')
                    ),
                );
                if(version_compare(_PS_VERSION_, '1.7', '<'))
                    $image_types[]=array(
                        'value'=>'themeconfig',
                        'label' => $this->l('Theme configurator image')
                    );
                if($this->isSlide)
                {
                    $image_types[] = array(
                        'value'=>'home_slide',
                        'label' => $this->l('Home slider images')
                    );
                }
            }
            elseif(in_array($type,array('blog_post','blog_category','blog_gallery','blog_slide')) &&  $this->isblog)
            {
                $image_types = array(
                    array(
                        'value'=> 'image',
                        'label' => $this->l('Main image')
                    ),
                    array(
                        'value'=> 'thumb',
                        'label' => $this->l('Thumb image')
                    )
                );
                if($type=='blog_slide')
                {
                    $image_types= array(
                        array(
                            'value'=> 'image',
                            'label' =>''
                        ),
                    );
                }
                if($type=='blog_gallery')
                {
                    $image_types=array(
                        array(
                            'value'=> 'image',
                            'label' => $this->l('Main gallery image')
                        ),
                        array(
                            'value'=> 'thumb',
                            'label' => $this->l('Thumb gallery image')
                        ),
                        array(
                            'value'=> 'blog_slide',
                            'label' => $this->l('Slider images')
                        )
                    );
                }
            }
            else
                $image_types=array();
            
            $total=0;
            if($string)
            {
                $images='';
                foreach($image_types as $image_type)
                {
                    $images .=','.$image_type['value'];
                }
                return trim($images,',');
            }
            else
            {
                if($image_types)
                {
                    foreach($image_types as &$image)
                    {
                        $total_image=0;
                        $total_image_optimized = 0;
                        switch($type){
                            case 'products':
                                if($is_install)
                                    $total_image_optimized = Ets_imagecompressor_defines::getTotalImage('product',false,true,true,false,$image['value']);
                                else
                                    $total_image_optimized =0;
                                $image_product = Ets_imagecompressor_defines::getTotalImage('product',false,false,false,false,$image['value']);
                                $total_image =  $image_product- $total_image_optimized;
                                $total +=$image_product;
                                break;
                            case 'manufacturers':
                                if($is_install)
                                    $total_image_optimized = Ets_imagecompressor_defines::getTotalImage('manufacturer',false,true,true,false,$image['value']);
                                else
                                    $total_image_optimized =0;
                                $image_manu = Ets_imagecompressor_defines::getTotalImage('manufacturer',false,false,false,false,$image['value']) ;
                                $total_image = $image_manu - $total_image_optimized;
                                $total +=$image_manu;
                                break;
                            case 'categories':
                                if($is_install)
                                    $total_image_optimized = Ets_imagecompressor_defines::getTotalImage('category',false,true,true,false,$image['value']);
                                else
                                    $total_image_optimized = 0;
                                $image_cate = Ets_imagecompressor_defines::getTotalImage('category',false,false,false,false,$image['value']);
                                $total_image =  $image_cate - $total_image_optimized;
                                $total +=$image_cate;
                                break;
                            case 'suppliers':
                                if($is_install)
                                    $total_image_optimized = Ets_imagecompressor_defines::getTotalImage('supplier',false,true,true,false,$image['value']);
                                else
                                    $total_image_optimized =0;
                                $image_supplier = Ets_imagecompressor_defines::getTotalImage('supplier',false,false,false,false,$image['value']);
                                $total_image = $image_supplier - $total_image_optimized;
                                $total += $image_supplier;
                                break;
                            case 'blog_post' :
                                if($is_install)
                                    $total_image_optimized = Ets_imagecompressor_defines::getTotalImage('blog_post',false,true,true,false,$image['value']);
                                else
                                    $total_image_optimized =0;
                                $image_post = Ets_imagecompressor_defines::getTotalImage('blog_post',false,false,false,false,$image['value']);
                                $total_image = $image_post - $total_image_optimized;
                                $total += $image_post;
                                break;
                            case 'blog_category' :
                                if($is_install)
                                    $total_image_optimized = Ets_imagecompressor_defines::getTotalImage('blog_category',false,true,true,false,$image['value']);
                                else
                                    $total_image_optimized =0;
                                $image_blog_category = Ets_imagecompressor_defines::getTotalImage('blog_category',false,false,false,false,$image['value']);
                                $total_image = $image_blog_category - $total_image_optimized;
                                $total += $image_blog_category;
                                break;
                            case 'blog_gallery' :
                                if($image['value']=='blog_slide')
                                {
                                    if($is_install)
                                        $total_image_optimized = Ets_imagecompressor_defines::getTotalImage('blog_slide',false,true,true,false,'image');
                                    else
                                        $total_image_optimized =0;
                                    $image_blog_slide = Ets_imagecompressor_defines::getTotalImage('blog_slide',false,false,false,false,'image');
                                    $total_image = $image_blog_slide - $total_image_optimized;
                                    $total += $image_blog_slide;
                                }
                                else
                                {
                                    if($is_install)
                                        $total_image_optimized = Ets_imagecompressor_defines::getTotalImage('blog_gallery',false,true,true,false,$image['value']);
                                    else
                                        $total_image_optimized =0;
                                    $image_blog_gallery = Ets_imagecompressor_defines::getTotalImage('blog_gallery',false,false,false,false,$image['value']);
                                    $total_image = $image_blog_gallery - $total_image_optimized;
                                    $total += $image_blog_gallery;
                                }
                                break;
                            case 'blog_slide' :
                                if($is_install)
                                    $total_image_optimized = Ets_imagecompressor_defines::getTotalImage('blog_slide',false,true,true,false,$image['value']);
                                else
                                    $total_image_optimized =0;
                                $image_blog_slide = Ets_imagecompressor_defines::getTotalImage('blog_slide',false,false,false,false,$image['value']);
                                $total_image = $image_blog_slide - $total_image_optimized;
                                $total += $image_blog_slide;
                                break;
                            case 'home_slide' :
                                if($is_install)
                                    $total_image_optimized = Ets_imagecompressor_defines::getTotalImage('home_slide',false,true,true,false,$image['value']);
                                else
                                    $total_image_optimized = 0;
                                $image_home_slide = Ets_imagecompressor_defines::getTotalImage('home_slide',false,false,false,false,$image['value']);
                                $total_image = $image_home_slide - $total_image_optimized;
                                $total += $image_home_slide;
                                break;
                            case 'others' :
                                if($image['value']=='home_slide')
                                {
                                    if($is_install)
                                        $total_image_optimized = Ets_imagecompressor_defines::getTotalImage('home_slide',false,true,true,false,$image['value']);
                                    else
                                         $total_image_optimized =0;
                                    $image_home_slide = Ets_imagecompressor_defines::getTotalImage('home_slide',false,false,false,false,$image['value']);
                                    $total_image = $image_home_slide - $total_image_optimized;
                                    $total += $image_home_slide;
                                }
                                else
                                {
                                    if($is_install)
                                        $total_image_optimized = Ets_imagecompressor_defines::getTotalImage('others',false,true,true,false,$image['value']);
                                    else
                                        $total_image_optimized =0;
                                    $image_others = Ets_imagecompressor_defines::getTotalImage('others',false,false,false,false,$image['value']);
                                    $total_image = $image_others - $total_image_optimized;
                                    $total += $image_others;
                                }
                                
                                break; 
                        }
                        $image['total_image'] = $total_image;
                        $image['total_image_optimized'] = $total_image_optimized;
                    }
                }
            }
            return $get_total ? $total : $image_types;
        }
        else    
            return false;
        
    }
    public static function getTotalImage_3_2_0($type = 'product', $all_type = false, $optimizaed = false, $check_quality = false, $noconfig = false, $type_image = '')
    {
        $total = 0;
        $quality = (int)Tools::getValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE', Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE'));
        $optimize_script = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT', Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT'));
        if(!in_array($optimize_script,array('php','tynypng','resmush','google')))
            $optimize_script ='php';
        $update_quantity = Tools::isSubmit('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE') ? (int)Tools::getValue('ETS_IMGCOMPRESSOR_UPDATE_QUALITY') : (int)Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_UPDATE_QUALITY');
        if ($update_quantity && $quality != 100) {
            $check_quality = false;
            $check_optimize_script = false;
        } else {
            $check_quality = true;
            if ($quality == 100)
                $check_optimize_script = false;
            else
                $check_optimize_script = true;
        }
        switch ($type) {
            case 'blog_post':
                if (Module::isEnabled('ybc_blog')) {
                    if (Tools::isSubmit('changeSubmitImageOptimize'))
                        $blog_post_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_POST_TYPE') ? : array();
                    else
                        $blog_post_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_POST_TYPE', Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_POST_TYPE') ? explode(',', Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_POST_TYPE')) : array());
                    if(!Ets_imagecompressor::validateArray($blog_post_type))
                        $blog_post_type = array();
                    $total = 0;
                    if ($all_type) {
                        if (in_array('image', $blog_post_type) || $noconfig)
                            $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT image) FROM `' . _DB_PREFIX_ . 'ybc_blog_post_lang` WHERE image!=""');
                        if (in_array('thumb', $blog_post_type) || $noconfig)
                            $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT thumb) FROM `' . _DB_PREFIX_ . 'ybc_blog_post_lang` WHERE thumb!=""');
                    } elseif ($type_image && in_array($type_image, array('image', 'thumb')))
                        $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT ' . pSQL($type_image) . ') FROM `' . _DB_PREFIX_ . 'ybc_blog_post_lang` WHERE ' . pSQL($type_image) . '!=""');
                    if ($optimizaed) {
                        $total_optimized = Db::getInstance()->getValue('SELECT COUNT(sbpi.id_post) FROM `' . _DB_PREFIX_ . 'ets_imagecompressor_blog_post_image` sbpi
                        INNER JOIN `' . _DB_PREFIX_ . 'ybc_blog_post` bp ON (sbpi.id_post = bp.id_post)
                        WHERE sbpi.size_old!=0' . ($all_type && $blog_post_type && !$noconfig ? ' AND  type_image IN ("' . implode('","', array_map('pSQL', $blog_post_type)) . '")' : '') . ($type_image ? ' AND type_image="' . pSQL($type_image) . '"' : '') . ($check_quality ? ' AND quality = "' . (int)$quality . '"' : ' AND quality!=100') . ($check_optimize_script ? ' AND optimize_type="' . pSQL($optimize_script) . '"' : ''));
                        return $total_optimized > $total ? $total : $total_optimized;
                    }
                    return $total;
                } else
                    return 0;
            case 'blog_category':
                if (Module::isEnabled('ybc_blog')) {
                    if (Tools::isSubmit('changeSubmitImageOptimize'))
                        $blog_category_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_CATEGORY_TYPE') ? : array();
                    else
                        $blog_category_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_CATEGORY_TYPE', Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_CATEGORY_TYPE') ? explode(',', Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_CATEGORY_TYPE')) : array());
                    if(!Ets_imagecompressor::validateArray($blog_category_type))
                        $blog_category_type = array();
                    $total = 0;
                    if ($all_type) {
                        if (in_array('image', $blog_category_type) || $noconfig)
                            $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT image) FROM `' . _DB_PREFIX_ . 'ybc_blog_category_lang` WHERE image!=""');
                        if (in_array('thumb', $blog_category_type) || $noconfig)
                            $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT thumb) FROM `' . _DB_PREFIX_ . 'ybc_blog_category_lang` WHERE thumb!=""');
                    } elseif ($type_image && in_array($type_image, array('image', 'thumb')))
                        $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT ' . pSQL($type_image) . ') FROM `' . _DB_PREFIX_ . 'ybc_blog_category_lang` WHERE ' . pSQL($type_image) . '!=""');
                    if ($optimizaed) {
                        $total_optimized = Db::getInstance()->getValue('SELECT COUNT(sbci.id_category) FROM `' . _DB_PREFIX_ . 'ets_imagecompressor_blog_category_image` sbci
                        INNER JOIN `' . _DB_PREFIX_ . 'ybc_blog_category` bc ON (bc.id_category = sbci.id_category)
                        WHERE sbci.size_old!=0 ' . ($all_type && $blog_category_type && !$noconfig ? ' AND  type_image IN ("' . implode('","', array_map('pSQL', $blog_category_type)) . '")' : '') . ($type_image ? ' AND type_image="' . pSQL($type_image) . '"' : '') . ($check_quality ? ' AND quality = "' . (int)$quality . '"' : ' AND quality!=100') . ($check_optimize_script ? ' AND optimize_type="' . pSQL($optimize_script) . '"' : ''));
                        return $total_optimized > $total ? $total : $total_optimized;
                    }
                    return $total;
                } else
                    return 0;
            case 'blog_gallery':
                if (Module::isEnabled('ybc_blog')) {
                    if (Tools::isSubmit('changeSubmitImageOptimize'))
                        $blog_gallery_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_GALLERY_TYPE') ? : array();
                    else
                        $blog_gallery_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_GALLERY_TYPE', Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_GALLERY_TYPE') ? explode(',', Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_GALLERY_TYPE')) : array());
                    if(!Ets_imagecompressor::validateArray($blog_gallery_type))
                        $blog_gallery_type = array();
                    $total = 0;
                    if ($all_type) {
                        if (in_array('image', $blog_gallery_type) || $noconfig)
                            $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT image) FROM `' . _DB_PREFIX_ . 'ybc_blog_gallery_lang` WHERE image!=""');
                        if (in_array('thumb', $blog_gallery_type) || $noconfig)
                            $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT thumb) FROM `' . _DB_PREFIX_ . 'ybc_blog_gallery_lang` WHERE thumb!=""');
                    } elseif ($type_image && in_array($type_image, array('image', 'thumb')))
                        $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT ' . pSQL($type_image) . ') FROM `' . _DB_PREFIX_ . 'ybc_blog_gallery_lang` WHERE ' . pSQL($type_image) . '!=""');
                    if ($optimizaed) {
                        $total_optimized = Db::getInstance()->getValue('SELECT COUNT(sbgi.id_gallery) FROM `' . _DB_PREFIX_ . 'ets_imagecompressor_blog_gallery_image` sbgi
                        INNER JOIN `' . _DB_PREFIX_ . 'ybc_blog_gallery` bg ON (bg.id_gallery = sbgi.id_gallery)
                        WHERE sbgi.size_old!=0' . ($all_type && $blog_gallery_type && !$noconfig ? ' AND  type_image IN ("' . implode('","', array_map('pSQL', $blog_gallery_type)) . '")' : '') . ($type_image ? ' AND type_image="' . pSQL($type_image) . '"' : '') . ($check_quality ? ' AND quality = "' . (int)$quality . '"' : ' AND quality!=100') . ($check_optimize_script ? ' AND optimize_type="' . pSQL($optimize_script) . '"' : ''));
                        return $total_optimized > $total ? $total : $total_optimized;
                    }
                    return $total;
                } else
                    return 0;
            case 'blog_slide':
                if (Module::isEnabled('ybc_blog')) {
                    if (Tools::isSubmit('changeSubmitImageOptimize'))
                        $blog_slide_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_SLIDE_TYPE') ? : array();
                    else
                        $blog_slide_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_SLIDE_TYPE', Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_SLIDE_TYPE') ? explode(',', Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_SLIDE_TYPE')) : array());
                    if(!Ets_imagecompressor::validateArray($blog_slide_type))
                        $blog_slide_type = array();
                    $total = 0;
                    if ($all_type) {
                        if (in_array('image', $blog_slide_type) || $noconfig)
                            $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT image) FROM `' . _DB_PREFIX_ . 'ybc_blog_slide_lang` WHERE image!=""');
                    } elseif ($type_image && in_array($type_image, array('image')))
                        $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT image) FROM `' . _DB_PREFIX_ . 'ybc_blog_slide_lang` WHERE ' . pSQL($type_image) . '!=""');
                    if ($optimizaed) {
                        $total_optimized = Db::getInstance()->getValue('SELECT COUNT(sbsi.id_slide) FROM `' . _DB_PREFIX_ . 'ets_imagecompressor_blog_slide_image` sbsi
                        INNER JOIN `' . _DB_PREFIX_ . 'ybc_blog_slide` bs ON (bs.id_slide = sbsi.id_slide)
                        WHERE sbsi.size_old!=0' . ($all_type && $blog_slide_type && !$noconfig ? ' AND  type_image IN ("' . implode('","', array_map('pSQL', $blog_slide_type)) . '")' : '') . ($type_image ? ' AND type_image="' . pSQL($type_image) . '"' : '') . ($check_quality ? ' AND quality = "' . (int)$quality . '"' : ' AND quality!=100') . ($check_optimize_script ? ' AND optimize_type="' . pSQL($optimize_script) . '"' : ''));
                        return $total_optimized > $total ? $total : $total_optimized;
                    }
                    return $total;
                } else
                    return 0;
            case 'home_slide':
                if (Module::isEnabled('blockbanner') ||  Module::isEnabled('ps_banner')) {
                    if (Tools::isSubmit('changeSubmitImageOptimize'))
                        $home_slide_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_HOME_SLIDE_TYPE') ? : array();
                    else
                        $home_slide_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_HOME_SLIDE_TYPE', Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_HOME_SLIDE_TYPE') ? explode(',', Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_HOME_SLIDE_TYPE')) : array());
                    if(!Ets_imagecompressor::validateArray($home_slide_type))
                        $home_slide_type = array();
                    $total = 0;
                    if ($home_slide_type || ($all_type && $noconfig) || $type_image) {
                        $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT image) FROM `' . _DB_PREFIX_ . 'homeslider_slides_lang` WHERE image!=""');
                    }
                    if ($optimizaed) {
                        $total_optimized = Db::getInstance()->getValue('SELECT COUNT(shsi.image) FROM `' . _DB_PREFIX_ . 'ets_imagecompressor_home_slide_image` shsi
                        INNER JOIN `' . _DB_PREFIX_ . 'homeslider_slides` hs ON (hs.id_homeslider_slides=shsi.id_homeslider_slides)
                        WHERE shsi.size_old!=0' . ($check_quality ? ' AND quality = "' . (int)$quality . '"' : ' AND quality!=100') . ($check_optimize_script ? ' AND optimize_type="' . pSQL($optimize_script) . '"' : ''));
                        return $total_optimized > $total ? $total : $total_optimized;
                    }
                    return $total;
                } else
                    return 0;
        }
        return $total;
    }
    public static function getTotalImage($type='product',$all_type=false,$optimizaed=false,$check_quality=false,$noconfig=false,$type_image='')
    {
        if(!Module::isInstalled('ets_imagecompressor'))
            return 1;
        
        if (in_array($type, array('blog_post','blog_category','blog_gallery','blog_slide'))) {
            $ybc_blog = Module::getInstanceByName('ybc_blog');
            if (version_compare($ybc_blog->version, '3.2.0', '>='))
                return self::getTotalImage_3_2_0($type, $all_type, $optimizaed, $check_quality, $noconfig, $type_image);
        }
        $total=0;
        $count_type=1;
        $quality = (int)Tools::getValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE'));
        $optimize_script = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT'));
        if(!in_array($optimize_script,array('php','tynypng','resmush','google')))
            $optimize_script ='php';
        $update_quantity = Tools::isSubmit('changeSubmitImageOptimize')|| Tools::isSubmit('btnSubmitImageOptimize') ? (int)Tools::getValue('ETS_IMGCOMPRESSOR_UPDATE_QUALITY') : (int)Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_UPDATE_QUALITY');
        if($update_quantity && $quality!=100)
        {
            $check_quality = false;
            $check_optimize_script = false;
        }
        else
        {
            $check_quality = true;
            if($quality==100)
                $check_optimize_script=false;
            else
                $check_optimize_script = true;
        }
        switch($type){
            case 'category':
                if(Tools::isSubmit('changeSubmitImageOptimize'))
                    $category_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_CATEGORY_TYPE') ?:array();
                else
                    $category_type= Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_CATEGORY_TYPE', Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_CATEGORY_TYPE') ? explode(',',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_CATEGORY_TYPE')):array());
                if(!Ets_imagecompressor::validateArray($category_type))
                    $category_type = array();
                if($all_type)
                {
                    if(( $category_type && in_array('0',$category_type))|| $noconfig)
                        $count_type = count(Ets_imagecompressor_defines::getInstance()->getImageTypes('categories'));
                    else
                        $count_type = $category_type ? count($category_type):0;
                }
                $categoies= Db::getInstance()->executeS('SELECT id_category FROM '._DB_PREFIX_.'category');
                if($categoies)
                {
                    foreach($categoies as $category)
                    {
                        if(file_exists(_PS_CAT_IMG_DIR_.$category['id_category'].'.jpg'))
                            $total++;
                    }
                }
                $total = $total*$count_type;
                if($optimizaed)
                {
                    $total_optimized = Db::getInstance()->getValue('SELECT COUNT(sci.id_category) FROM `'._DB_PREFIX_.'ets_imagecompressor_category_image` sci 
                    INNER JOIN `'._DB_PREFIX_.'category` c ON (c.id_category = sci.id_category)
                    WHERE sci.size_old!=0 '.($all_type && $category_type && !$noconfig ? ' AND  type_image IN ("'.implode('","', array_map('pSQL',$category_type)).'")':'').($type_image ? ' AND type_image="'.pSQL($type_image).'"':'').($check_quality ? ' AND quality = "'.(int)$quality.'"':' AND quality!=100').($check_optimize_script ? ' AND optimize_type="'.pSQL($optimize_script).'"':''));
                    return $count_type ? ($total_optimized > $total ? $total : $total_optimized) :0;
                }
                return $total;
            case 'manufacturer':
                if(Tools::isSubmit('changeSubmitImageOptimize'))
                    $manufacturer_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_MANUFACTURER_TYPE') ?:array();
                else
                    $manufacturer_type= Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_MANUFACTURER_TYPE',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_MANUFACTURER_TYPE') ? explode(',',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_MANUFACTURER_TYPE')):array());
                if(!Ets_imagecompressor::validateArray($manufacturer_type))
                    $manufacturer_type = array();
                if($all_type)
                {
                    if(($manufacturer_type && in_array('0',$manufacturer_type))||$noconfig)
                        $count_type= count(Ets_imagecompressor_defines::getInstance()->getImageTypes('manufacturers'));
                    else
                        $count_type = $manufacturer_type ? count($manufacturer_type):0;
                }
                $manufacturers = Db::getInstance()->executeS('SELECT id_manufacturer FROM '._DB_PREFIX_.'manufacturer');
                if($manufacturers)
                {
                    foreach($manufacturers as $manufacturer)
                    {
                        if(file_exists(_PS_MANU_IMG_DIR_.$manufacturer['id_manufacturer'].'.jpg'))
                            $total++;
                    }  
                        
                }
                $total = $count_type*$total;
                if($optimizaed)
                {
                    $total_optimized = Db::getInstance()->getValue('SELECT COUNT(smi.id_manufacturer) FROM `'._DB_PREFIX_.'ets_imagecompressor_manufacturer_image` smi
                    INNER JOIN `'._DB_PREFIX_.'manufacturer` m ON (smi.id_manufacturer = m.id_manufacturer)
                    WHERE smi.size_old!=0 '.($all_type && $manufacturer_type && !$noconfig ? ' AND  type_image IN ("'.implode('","', array_map('pSQL',$manufacturer_type)).'")':'').($type_image ? ' AND type_image="'.pSQL($type_image).'"':'').($check_quality ? ' AND quality = "'.(int)$quality.'"':' AND quality!=100').($check_optimize_script ? ' AND optimize_type="'.pSQL($optimize_script).'"':''));
                    return $count_type ? ($total_optimized >$total ? $total : $total_optimized) :0;
                }
                return $total;
            case 'supplier':
                if(Tools::isSubmit('changeSubmitImageOptimize'))
                    $supplier_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_SUPPLIER_TYPE') ?:array();
                else
                    $supplier_type= Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_SUPPLIER_TYPE',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_SUPPLIER_TYPE') ? explode(',',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_SUPPLIER_TYPE')):array());
                if(!Ets_imagecompressor::validateArray($supplier_type))
                    $supplier_type = array();
                if($all_type)
                {
                    if(($supplier_type && in_array('0',$supplier_type))||$noconfig)
                        $count_type = count(Ets_imagecompressor_defines::getInstance()->getImageTypes('suppliers'));
                    else
                        $count_type = $supplier_type ? count($supplier_type):0;
                }
                $suppliers = Db::getInstance()->executeS('SELECT id_supplier FROM '._DB_PREFIX_.'supplier');
                if($suppliers)
                {
                    foreach($suppliers as $supplier)
                    {
                        if(file_exists(_PS_SUPP_IMG_DIR_.$supplier['id_supplier'].'.jpg'))
                            $total++;
                    }
                }
                $total = $total*$count_type;
                if($optimizaed)
                {
                    $total_optimized = Db::getInstance()->getValue('SELECT COUNT(ssi.id_supplier) FROM `'._DB_PREFIX_.'ets_imagecompressor_supplier_image` ssi
                    INNER JOIN `'._DB_PREFIX_.'supplier` s ON (ssi.id_supplier = s.id_supplier)
                    WHERE ssi.size_old!=0'.($all_type && $supplier_type && !$noconfig ? ' AND  type_image IN ("'.implode('","', array_map('pSQL',$supplier_type)).'")':'').($type_image ? ' AND type_image="'.pSQL($type_image).'"':'').($check_quality ? ' AND quality = "'.(int)$quality.'"':' AND quality!=100').($check_optimize_script ? ' AND optimize_type="'.pSQL($optimize_script).'"':''));
                    return $count_type ? ($total_optimized > $total ? $total: $total_optimized): 0;
                }
                return $total;
            case 'product':
                if(Tools::isSubmit('changeSubmitImageOptimize'))
                    $product_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_PRODCUT_TYPE') ?:array();
                else
                    $product_type= Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_PRODCUT_TYPE', Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_PRODCUT_TYPE') ? explode(',',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_PRODCUT_TYPE')) : array());
                if(!Ets_imagecompressor::validateArray($product_type))
                    $product_type = array();
                if($all_type)
                {
                    if(($product_type && in_array('0',$product_type))||$noconfig)
                        $count_type = count(Ets_imagecompressor_defines::getInstance()->getImageTypes('products'));
                    else
                        $count_type = $product_type ? count($product_type):0;
                    
                }
                if($optimizaed)
                {
                    $total = Db::getInstance()->getValue('
                        SELECT COUNT(pm.id_image) FROM `'._DB_PREFIX_.'ets_imagecompressor_product_image` pm
                        INNER JOIN `'._DB_PREFIX_.'image` m ON (pm.id_image= m.id_image)
                        WHERE 1'.($all_type && $product_type && !$noconfig ? ' AND pm.type_image IN ("'.implode('","', array_map('pSQL',$product_type)).'")':'').($type_image ? ' AND pm.type_image="'.pSQL($type_image).'"':'').( $check_quality ? ' AND pm.quality = "'.(int)$quality.'"':' AND pm.quality!=100').($check_optimize_script ? 'AND optimize_type="'.pSQL($optimize_script).'"':''));
                    if (Module::isInstalled('ets_multilangimages') && Module::isEnabled('ets_multilangimages')) {
                        $total += Db::getInstance()->getValue('
                        SELECT COUNT(pm.id_image_lang) FROM `' . _DB_PREFIX_ . 'ets_imagecompressor_product_image_lang` pm
                        INNER JOIN `' . _DB_PREFIX_ . 'ets_image_lang` m ON (pm.id_image_lang = m.id_image_lang)
                        WHERE 1' . ($all_type && $product_type && !$noconfig ? ' AND pm.type_image IN ("' . implode('","', array_map('pSQL', $product_type)) . '")' : '') . ($type_image ? ' AND pm.type_image="' . pSQL($type_image) . '"' : '') . ($check_quality ? ' AND pm.quality = "' . (int)$quality . '"' : ' AND pm.quality!=100') . ($check_optimize_script ? 'AND optimize_type="' . pSQL($optimize_script) . '"' : ''));
                    }
                    return $count_type ? $total:0;
                }
                $total = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'image');
                if (Module::isInstalled('ets_multilangimages') && Module::isEnabled('ets_multilangimages')) {
                    $total += Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_image_lang`');
                }
                return $total*$count_type;
            case 'blog_post':
                if(Module::isInstalled('ybc_blog') && Module::isEnabled('ybc_blog'))
                {
                    if(Tools::isSubmit('changeSubmitImageOptimize'))
                        $blog_post_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_POST_TYPE') ?:array();
                    else
                        $blog_post_type= Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_POST_TYPE',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_POST_TYPE') ? explode(',',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_POST_TYPE')):array());
                    $total = 0;
                    if($all_type)
                    {
                        if((Ets_imagecompressor::validateArray($blog_post_type) && in_array('image',$blog_post_type)) || $noconfig)
                            $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT id_post) FROM `'._DB_PREFIX_.'ybc_blog_post` WHERE image!=""');
                        if((Ets_imagecompressor::validateArray($blog_post_type) && in_array('thumb',$blog_post_type)) || $noconfig)
                            $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT id_post) FROM `'._DB_PREFIX_.'ybc_blog_post` WHERE thumb!=""');
                    }
                    elseif($type_image && in_array($type_image,array('image','thumb')))
                        $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT id_post) FROM `'._DB_PREFIX_.'ybc_blog_post` WHERE '.pSQL($type_image).'!=""');
                    if($optimizaed)
                    {
                        $total_optimized = Db::getInstance()->getValue('SELECT COUNT(sbpi.id_post) FROM `'._DB_PREFIX_.'ets_imagecompressor_blog_post_image` sbpi
                        INNER JOIN `'._DB_PREFIX_.'ybc_blog_post` bp ON (sbpi.id_post = bp.id_post)
                        WHERE sbpi.size_old!=0'.($all_type && $blog_post_type && Ets_imagecompressor::validateArray($blog_post_type) && !$noconfig ? ' AND  type_image IN ("'.implode('","', array_map('pSQL',$blog_post_type)).'")':'').($type_image ? ' AND type_image="'.pSQL($type_image).'"':'').($check_quality ? ' AND quality = "'.(int)$quality.'"':' AND quality!=100').($check_optimize_script ? ' AND optimize_type="'.pSQL($optimize_script).'"':''));
                        return $total_optimized > $total ? $total: $total_optimized;
                    }
                    return $total;
                }
                else
                    return 0;
            case 'blog_category':
                if(Module::isInstalled('ybc_blog') && Module::isEnabled('ybc_blog'))
                {
                    if(Tools::isSubmit('changeSubmitImageOptimize'))
                        $blog_category_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_CATEGORY_TYPE') ?:array();
                    else
                        $blog_category_type= Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_CATEGORY_TYPE',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_CATEGORY_TYPE') ? explode(',',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_CATEGORY_TYPE')):array());
                    $total = 0;
                    if($all_type)
                    {
                        if((Ets_imagecompressor::validateArray($blog_category_type) && in_array('image',$blog_category_type)) || $noconfig)
                            $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT id_category) FROM `'._DB_PREFIX_.'ybc_blog_category` WHERE image!=""');
                        if((Ets_imagecompressor::validateArray($blog_category_type) && in_array('thumb',$blog_category_type)) || $noconfig)
                            $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT id_category) FROM `'._DB_PREFIX_.'ybc_blog_category` WHERE thumb!=""');
                    }
                    elseif($type_image && in_array($type_image,array('image','thumb')))
                        $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT id_category) FROM `'._DB_PREFIX_.'ybc_blog_category` WHERE '.pSQL($type_image).'!=""');
                    if($optimizaed)
                    {
                        $total_optimized = Db::getInstance()->getValue('SELECT COUNT(sbci.id_category) FROM `'._DB_PREFIX_.'ets_imagecompressor_blog_category_image` sbci
                        INNER JOIN `'._DB_PREFIX_.'ybc_blog_category` bc ON (bc.id_category = sbci.id_category)
                        WHERE sbci.size_old!=0 '.($all_type && $blog_category_type && Ets_imagecompressor::validateArray($blog_category_type) && !$noconfig ? ' AND  type_image IN ("'.implode('","', array_map('pSQL',$blog_category_type)).'")':'').($type_image ? ' AND type_image="'.pSQL($type_image).'"':'').($check_quality ? ' AND quality = "'.(int)$quality.'"':' AND quality!=100').($check_optimize_script ? ' AND optimize_type="'.pSQL($optimize_script).'"':''));
                        return $total_optimized > $total ? $total: $total_optimized;
                    }
                    return $total;
                }
                else
                    return 0;
            case 'blog_gallery':
                if(Module::isInstalled('ybc_blog') && Module::isEnabled('ybc_blog'))
                {
                    if(Tools::isSubmit('changeSubmitImageOptimize'))
                        $blog_gallery_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_GALLERY_TYPE') ?:array();
                    else
                        $blog_gallery_type= Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_GALLERY_TYPE',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_GALLERY_TYPE') ? explode(',',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_GALLERY_TYPE')):array());
                    $total = 0;
                    if($all_type)
                    {
                        if((Ets_imagecompressor::validateArray($blog_gallery_type) && in_array('image',$blog_gallery_type)) || $noconfig)
                            $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT id_gallery) FROM `'._DB_PREFIX_.'ybc_blog_gallery` WHERE image!=""');
                        if((Ets_imagecompressor::validateArray($blog_gallery_type) && in_array('thumb',$blog_gallery_type)) || $noconfig)
                            $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT id_gallery) FROM `'._DB_PREFIX_.'ybc_blog_gallery` WHERE thumb!=""');
                    }
                    elseif($type_image && in_array($type_image,array('image','thumb')))
                        $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT id_gallery) FROM `'._DB_PREFIX_.'ybc_blog_gallery` WHERE '.pSQL($type_image).'!=""');
                    if($optimizaed)
                    {
                        $total_optimized = Db::getInstance()->getValue('SELECT COUNT(sbgi.id_gallery) FROM `'._DB_PREFIX_.'ets_imagecompressor_blog_gallery_image` sbgi
                        INNER JOIN `'._DB_PREFIX_.'ybc_blog_gallery` bg ON (bg.id_gallery = sbgi.id_gallery)
                        WHERE sbgi.size_old!=0'.($all_type && $blog_gallery_type && !$noconfig ? ' AND  type_image IN ("'.implode('","', array_map('pSQL',$blog_gallery_type)).'")':'').($type_image ? ' AND type_image="'.pSQL($type_image).'"':'').($check_quality ? ' AND quality = "'.(int)$quality.'"':' AND quality!=100').($check_optimize_script ? ' AND optimize_type="'.pSQL($optimize_script).'"':''));
                        return $total_optimized > $total ? $total: $total_optimized;
                    }
                    return $total;
                }
                else
                    return 0;
            case 'blog_slide':
                if(Module::isInstalled('ybc_blog') && Module::isEnabled('ybc_blog'))
                {
                    if(Tools::isSubmit('changeSubmitImageOptimize'))
                        $blog_slide_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_SLIDE_TYPE') ?:array();
                    else
                        $blog_slide_type= Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_SLIDE_TYPE',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_SLIDE_TYPE') ? explode(',',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_SLIDE_TYPE')):array());
                    $total = 0;
                    if($all_type)
                    {
                        if((Ets_imagecompressor::validateArray($blog_slide_type) && in_array('image',$blog_slide_type)) || $noconfig)
                            $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT id_slide) FROM `'._DB_PREFIX_.'ybc_blog_slide` WHERE image!=""');
                    }
                    elseif($type_image && in_array($type_image,array('image')))
                        $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT id_slide) FROM `'._DB_PREFIX_.'ybc_blog_slide` WHERE '.pSQL($type_image).'!=""');
                    if($optimizaed)
                    {
                        $total_optimized = Db::getInstance()->getValue('SELECT COUNT(sbsi.id_slide) FROM `'._DB_PREFIX_.'ets_imagecompressor_blog_slide_image` sbsi
                        INNER JOIN `'._DB_PREFIX_.'ybc_blog_slide` bs ON (bs.id_slide = sbsi.id_slide)
                        WHERE sbsi.size_old!=0'.($all_type && $blog_slide_type && Ets_imagecompressor::validateArray($blog_slide_type) && !$noconfig ? ' AND  type_image IN ("'.implode('","', array_map('pSQL',$blog_slide_type)).'")':'').($type_image ? ' AND type_image="'.pSQL($type_image).'"':'').($check_quality ? ' AND quality = "'.(int)$quality.'"':' AND quality!=100').($check_optimize_script ? ' AND optimize_type="'.pSQL($optimize_script).'"':''));
                        return $total_optimized > $total ? $total: $total_optimized;
                    }
                    return $total;
                }
                else
                    return 0;
            case 'home_slide':
                if((Module::isInstalled('ps_imageslider') && Module::isEnabled('ps_imageslider')) ||  (Module::isInstalled('homeslider') && Module::isEnabled('homeslider')))
                {
                    if(Tools::isSubmit('changeSubmitImageOptimize'))
                        $home_slide_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_HOME_SLIDE_TYPE') ?:array();
                    else
                        $home_slide_type= Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_HOME_SLIDE_TYPE',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_HOME_SLIDE_TYPE') ? explode(',',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_HOME_SLIDE_TYPE')):array());
                    $total = 0;
                    if(($home_slide_type && Ets_imagecompressor::validateArray($home_slide_type)) || ($all_type && $noconfig) || $type_image)
                    {
                        $total += Db::getInstance()->getValue('SELECT COUNT(DISTINCT image) FROM `'._DB_PREFIX_.'homeslider_slides_lang` WHERE image!=""');
                    }
                    if($optimizaed)
                    {
                        $total_optimized = Db::getInstance()->getValue('SELECT COUNT(shsi.image) FROM `'._DB_PREFIX_.'ets_imagecompressor_home_slide_image` shsi
                        INNER JOIN `'._DB_PREFIX_.'homeslider_slides` hs ON (hs.id_homeslider_slides=shsi.id_homeslider_slides)
                        WHERE shsi.size_old!=0'.($check_quality ? ' AND quality = "'.(int)$quality.'"':' AND quality!=100').($check_optimize_script ? ' AND optimize_type="'.pSQL($optimize_script).'"':''));
                        return $total_optimized > $total ? $total: $total_optimized;
                    }
                    return $total;
                }
                else
                    return 0;
            case 'others' :
            {
                if(Tools::isSubmit('changeSubmitImageOptimize'))
                    $orther_type = Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_OTHERS_TYPE')?:array();
                else
                    $orther_type= Tools::getValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_OTHERS_TYPE',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_OTHERS_TYPE') ? explode(',',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_OTHERS_TYPE')):array());
                $total = 0;
                if($all_type)
                {
                    if((Ets_imagecompressor::validateArray($orther_type) &&  in_array('logo',$orther_type)) || $noconfig)
                        $total += (Configuration::get('PS_LOGO') ? 1 :0);
                    if(in_array('banner',$orther_type) || $noconfig)
                    {
                        if(version_compare(_PS_VERSION_, '1.7', '>='))
                        {
                            if(Module::isInstalled('ps_banner') && Module::isEnabled('ps_banner'))
                            {
                                $languages = Language::getLanguages(false);
                                $banners = array();
                                foreach($languages as $language)
                                {
                                    if(($image = Configuration::get('BANNER_IMG',$language['id_lang'])) && !in_array($image,$banners))
                                    {
                                        $banners[] = $image;
                                        $total ++;
                                    }
                                }
                            }
                        }
                        else
                        {
                            if(Module::isInstalled('blockbanner') && Module::isEnabled('blockbanner'))
                            {
                                $languages = Language::getLanguages(false);
                                $banners = array();
                                foreach($languages as $language)
                                {
                                    if(($image = Configuration::get('BLOCKBANNER_IMG',$language['id_lang'])) && !in_array($image,$banners))
                                    {
                                        $banners[] = $image;
                                        $total ++;
                                    }
                                }
                            }
                        }
                    } 
                    if(in_array('themeconfig',$orther_type) || $noconfig)
                    {
                      
                        if(Module::isInstalled('themeconfigurator') && Module::isEnabled('themeconfigurator'))
                        {
                            $themeconfigurators = Db::getInstance()->executeS('SELECT image FROM `'._DB_PREFIX_.'themeconfigurator` WHERE image!="" GROUP BY image');
                            $themes = array();
                            if($themeconfigurators)
                            {
                                foreach($themeconfigurators as $themeconfigurator)
                                {
                                    $themes[]= $themeconfigurator['image'];
                                    $total ++;
                                }
                            }
                        }
                    }    
                }
                elseif($type_image && in_array($type_image,array('logo','banner','themeconfig')))
                {
                    if($type_image=='logo' && Configuration::get('PS_LOGO'))
                        $total ++;
                    elseif($type_image=='banner')
                    {
                        if(version_compare(_PS_VERSION_, '1.7', '>='))
                        {
                            if(Module::isInstalled('ps_banner') && Module::isEnabled('ps_banner'))
                            {
                                $languages = Language::getLanguages(false);
                                $banners = array();
                                foreach($languages as $language)
                                {
                                    if(($image = Configuration::get('BANNER_IMG',$language['id_lang'])) && !in_array($image,$banners))
                                    {
                                        $banners[] = $image;
                                        $total ++;
                                    }
                                }
                            }
                        }
                        else
                        {
                            if(Module::isInstalled('blockbanner') && Module::isEnabled('blockbanner'))
                            {
                                $languages = Language::getLanguages(false);
                                $banners = array();
                                foreach($languages as $language)
                                {
                                    if(($image = Configuration::get('BLOCKBANNER_IMG',$language['id_lang'])) && !in_array($image,$banners))
                                    {
                                        $banners[] = $image;
                                        $total ++;
                                    }
                                }
                            }
                        }
                    }
                    elseif($type_image=='themeconfig')
                    {
                        if(Module::isInstalled('themeconfigurator') && Module::isEnabled('themeconfigurator'))
                        {
                            $themeconfigurators = Db::getInstance()->executeS('SELECT image FROM `'._DB_PREFIX_.'themeconfigurator` WHERE image!="" GROUP BY image');
                            $themes = array();
                            if($themeconfigurators)
                            {
                                foreach($themeconfigurators as $themeconfigurator)
                                {
                                    $themes[]= $themeconfigurator['image'];
                                    $total ++;
                                }
                            }
                        }
                    }
                }
                if($optimizaed)
                {
                    if(isset($banners))
                        $images = $banners;
                    else    
                        $images = array();
                    if(Configuration::get('PS_LOGO'))
                        $images[] = Configuration::get('PS_LOGO');
                    if(isset($themes))
                        $images = array_merge($images,$themes);
                    $total_optimized = Db::getInstance()->getValue('SELECT COUNT(image) FROM `'._DB_PREFIX_.'ets_imagecompressor_others_image` WHERE 1'.($all_type && $orther_type && !$noconfig ? ' AND  type_image IN ("'.implode('","', array_map('pSQL',$orther_type)).'")':'').($type_image ? ' AND type_image="'.pSQL($type_image).'"':'').($images ? ' AND image IN ("'.implode('","',array_map('pSQL',$images)).'")':'').($check_quality ? ' AND quality = "'.(int)$quality.'"':' AND quality!=100').($check_optimize_script ? ' AND optimize_type="'.pSQL($optimize_script).'"':''));
                    return $total_optimized > $total ? $total: $total_optimized;
                }
                return $total;
            }
        }
        return $total;
    }
    public static function installDb()
    {
        $rs =Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_imagecompressor_category_image` (
          `id_category` int(11) NOT NULL,
          `type_image` varchar(64) NOT NULL,
          `quality` int(11) NOT NULL,
          `size_old` float(10,2),
          `size_new` float(10,2),
          `optimize_type` VARCHAR(8),
          PRIMARY KEY( `id_category`, `type_image`)
          ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8');
        $rs &=Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_imagecompressor_manufacturer_image` (
          `id_manufacturer` int(11) NOT NULL,
          `type_image` varchar(64) NOT NULL,
          `quality` int(11) NOT NULL,
          `size_old` float(10,2),
          `size_new` float(10,2),
          `optimize_type` VARCHAR(8),
          PRIMARY KEY( `id_manufacturer`, `type_image`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8');
        $rs &=Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_imagecompressor_product_image` (
          `id_image` int(11) NOT NULL,
          `type_image` varchar(64) NOT NULL,
          `quality` int(11) NOT NULL,
          `size_old` float(10,2),
          `size_new` float(10,2),
          `optimize_type` VARCHAR(8),
          PRIMARY KEY( `id_image`, `type_image`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8');
        $rs &=Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_imagecompressor_supplier_image` (
          `id_supplier` int(11) NOT NULL,
          `type_image` varchar(64) NOT NULL,
          `quality` int(11) NOT NULL,
          `size_old` float(10,2),
          `size_new` float(10,2),
          `optimize_type` VARCHAR(8),
          PRIMARY KEY( `id_supplier`, `type_image`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8');
        $rs &=Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_imagecompressor_blog_post_image` (
          `id_post` int(11) NOT NULL,
          `type_image` varchar(64) NOT NULL,
          `image` varchar(222) NOT NULL,
          `thumb` varchar(222) NOT NULL,
          `quality` int(11) NOT NULL,
          `size_old` float(10,2),
          `size_new` float(10,2),
          `optimize_type` VARCHAR(8),
          PRIMARY KEY( `id_post`, `type_image`,`image`,`thumb`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8');
        $rs &=Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_imagecompressor_blog_category_image` (
          `id_category` int(11) NOT NULL,
          `type_image` varchar(64) NOT NULL,
          `image` varchar(222) NOT NULL,
          `thumb` varchar(222) NOT NULL,
          `quality` int(11) NOT NULL,
          `size_old` float(10,2),
          `size_new` float(10,2),
          `optimize_type` VARCHAR(8),
          PRIMARY KEY( `id_category`, `type_image`,`image`,`thumb`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8');
        $rs &=Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_imagecompressor_blog_gallery_image` (
          `id_gallery` int(11) NOT NULL,
          `type_image` varchar(64) NOT NULL,
          `image` varchar(222) NOT NULL,
          `thumb` varchar(222) NOT NULL,
          `quality` int(11) NOT NULL,
          `size_old` float(10,2),
          `size_new` float(10,2),
          `optimize_type` VARCHAR(8),
          PRIMARY KEY( `id_gallery`, `type_image`,`image`,`thumb`)
        )  ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8');
        $rs &=Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_imagecompressor_blog_slide_image` (
          `id_slide` int(11) NOT NULL,
          `type_image` varchar(64) NOT NULL,
          `image` varchar(222) NOT NULL,
          `quality` int(11) NOT NULL,
          `size_old` float(10,2),
          `size_new` float(10,2),
          `optimize_type` VARCHAR(8),
          PRIMARY KEY( `id_slide`, `type_image`,`image`)
        )  ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8');
        $rs &=Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_imagecompressor_home_slide_image` (
          `id_homeslider_slides` int(11) NOT NULL,
          `image` varchar(165) NOT NULL,
          `type_image` varchar(64) NOT NULL,
          `quality` int(11) NOT NULL,
          `size_old` float(10,2),
          `size_new` float(10,2),
          `optimize_type` VARCHAR(8),
          PRIMARY KEY( `id_homeslider_slides`, `type_image`,`image`)
        )  ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8');
        $rs &=Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_imagecompressor_others_image`(
          `image` varchar(165) NOT NULL,
          `type_image` varchar(64) NOT NULL,
          `quality` int(11) NOT NULL,
          `size_old` float(10,2),
          `size_new` float(10,2),
          `optimize_type` VARCHAR(8),
          PRIMARY KEY(`type_image`,`image`)
        )  ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8');
        $rs &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_imagecompressor_upload_image` (
          `id_ets_imagecompressor_upload_image` int(11) NOT NULL AUTO_INCREMENT,
          `image_name` varchar(222) NOT NULL,
          `old_size` float(10,2) NOT NULL,
          `new_size` float(10,2) NOT NULL,
          `image_name_new` varchar(222) NOT NULL,
          `date_add` datetime NOT NULL,
           PRIMARY KEY (`id_ets_imagecompressor_upload_image`))  ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8');
        $rs &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_imagecompressor_browse_image` (
          `id_ets_imagecompressor_browse_image` int(11) NOT NULL AUTO_INCREMENT,
          `image_name` varchar(222) NOT NULL,
          `image_dir` text,
          `image_id` text,
          `old_size` float(10,2) NOT NULL,
          `new_size` float(10,2) NOT NULL,
          `date_add` datetime NOT NULL,
           PRIMARY KEY (`id_ets_imagecompressor_browse_image`))  ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8');
        $rs &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_imagecompressor_product_image_lang` (
          `id_image_lang` int(11) NOT NULL,
          `id_lang` int(11) NULL,
          `type_image` varchar(64) NOT NULL,
          `quality` int(11) NOT NULL,
          `size_old` float(10,2),
          `size_new` float(10,2),
          `optimize_type` VARCHAR(8),
          PRIMARY KEY( `id_image_lang`, `type_image`)
        )  ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8');
        return $rs;
    }
    public static function uninstallDb()
    {
        $res  =Db::getInstance()->execute("DROP TABLE IF EXISTS "._DB_PREFIX_."ets_imagecompressor_category_image");
        $res &=Db::getInstance()->execute("DROP TABLE IF EXISTS "._DB_PREFIX_."ets_imagecompressor_manufacturer_image");
        $res &=Db::getInstance()->execute("DROP TABLE IF EXISTS "._DB_PREFIX_."ets_imagecompressor_product_image");
        $res &=Db::getInstance()->execute("DROP TABLE IF EXISTS "._DB_PREFIX_."ets_imagecompressor_supplier_image");
        $res &=Db::getInstance()->execute("DROP TABLE IF EXISTS "._DB_PREFIX_."ets_imagecompressor_blog_post_image");
        $res &=Db::getInstance()->execute("DROP TABLE IF EXISTS "._DB_PREFIX_."ets_imagecompressor_blog_category_image");
        $res &=Db::getInstance()->execute("DROP TABLE IF EXISTS "._DB_PREFIX_."ets_imagecompressor_blog_gallery_image");
        $res &=Db::getInstance()->execute("DROP TABLE IF EXISTS "._DB_PREFIX_."ets_imagecompressor_blog_slide_image");
        $res &=Db::getInstance()->execute("DROP TABLE IF EXISTS "._DB_PREFIX_."ets_imagecompressor_home_slide_image");
        $res &=Db::getInstance()->execute("DROP TABLE IF EXISTS "._DB_PREFIX_."ets_imagecompressor_others_image");
        $res &=Db::getInstance()->execute("DROP TABLE IF EXISTS "._DB_PREFIX_."ets_imagecompressor_browse_image");
        $res &=Db::getInstance()->execute("DROP TABLE IF EXISTS "._DB_PREFIX_."ets_imagecompressor_upload_image");
        $res &=Db::getInstance()->execute("DROP TABLE IF EXISTS "._DB_PREFIX_."ets_imagecompressor_product_image_lang");
        $res &= Configuration::deleteByName('ETS_IMGCOMPRESSOR_UPDATE_QUALITY');
        $res &= Configuration::deleteByName('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE');
        $res &= Configuration::deleteByName('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT');
        $res &= Configuration::deleteByName('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_OTHERS_TYPE');
        $res &= Configuration::deleteByName('ETS_IMGCOMPRESSOR_LAZY_FOR');
        $res &= Configuration::deleteByName('ETS_IMGCOMPRESSOR_LOADING_IMAGE_TYPE');
        $res &= Configuration::deleteByName('ETS_IMGCOMPRESSOR_ENABLE_LAYZY_LOAD');
        $res &=Configuration::updateValue('ETS_IMGCOMPRESSOR_ENABLE_LAYZY_LOAD',0);
        return $res;
    }
    public static function getFieldsTable($table)
    {
        try {
            return Db::getInstance()->ExecuteS('DESCRIBE '._DB_PREFIX_.pSQL($table));
        }
        catch(Exception $ex){
            if($ex){
                return false;
            }
        }
        return false;
    }
}