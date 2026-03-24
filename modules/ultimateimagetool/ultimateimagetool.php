<?php
/**
*    2007-2017 PrestaShop
*
*    NOTICE OF LICENSE
*
*    This source file is subject to the Academic Free License (AFL 3.0)
*    that is bundled with this package in the file LICENSE.txt.
*    It is also available through the world-wide-web at this URL:
*    http://opensource.org/licenses/afl-3.0.php
*    If you did not receive a copy of the license and are unable to
*    obtain it through the world-wide-web, please send an email
*    to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*    @author    PrestaShop SA <contact@prestashop.com>
*    @copyright 2007-2015 PrestaShop SA
*    @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

require_once(_PS_MODULE_DIR_ .'/ultimateimagetool/simple_html_dom.php');
require_once(_PS_MODULE_DIR_ .'/ultimateimagetool/vendor/autoload.php');

class ultimateimagetool extends Module
{	
	public $languages;
	public $html;
	public $module_p;

	public function __construct()
	{
		$this->name = 'ultimateimagetool';
		$this->tab = 'administration';
		$this->version = '1.5.21';
		$this->author = 'advancedplugins';
		$this->need_instance = 1;
		$this->bootstrap  = true;
		$this->module_key = 'c5e7e38aa69cc969804a213ca75bde94';
		$this->author_address = '0x6b0EA5e7A2019Ca82d288436edf45e1B9a3540b5';
		parent::__construct();
		$this->displayName = $this->l('Image: WebP, Compress, Zoom, Regenerate & More');
		$this->description = $this->l('Compress, WebP, Zoom, Regenerate, Delete, Lazy Load, SEO Alt Tags, Swap on  hover and much more.');
		$this->_buildData();
	}

	public function installDb()
	{
		Configuration::updateValue('uit_token', md5(Configuration::get('PS_SHOP_EMAIL')) );	
		Configuration::updateValue('uit_products', 1 );
		Configuration::updateValue('uit_cron_quality', 80);
		Configuration::updateValue('uit_quality', 80);
		Configuration::updateValue('uit_quality_manuf', 80);
		Configuration::updateValue('uit_cron_quality_manuf', 80);
		Configuration::updateValue('uit_quality_sup', 80);
		Configuration::updateValue('uit_cron_quality_sup', 80);
		Configuration::updateValue('uit_quality_cat', 80);
		Configuration::updateValue('uit_quality_cms', 80);
		Configuration::updateValue('uit_cron_quality_cat', 80);
		Configuration::updateValue('uit_lazy_load', 'disabled');
		Configuration::updateValue('uit_mouse_hover', 'disabled');
		Configuration::updateValue('uit_mouse_hover_thumb', 'disabled');
		Configuration::updateValue('uit_hover_image_type', 'home_'.'default');
		Configuration::updateValue('uit_mouse_hover_position', 'last_image');
		Configuration::updateValue('uit_hover_image_ps', 'home_'.'default');
		Configuration::updateValue('uit_hover_image_ts', 'cart_'.'default');
		Configuration::updateValue('uit_alt_format', '{PRODUCT_NAME} {MANUFACTURER_NAME} - {IMAGE_POSITION}');
		Configuration::updateValue('alt_tags_auto', 'no');
		Configuration::updateValue('uit_use_webp', '2');
		Configuration::updateValue('uit_auto_webp', '1');
		Configuration::updateValue('uit_zoom', '0');
		Configuration::updateValue('uit_zoom_type', '0');
		Configuration::updateValue('uit_enable_gzip', '0');
		Configuration::updateValue('ait_force_regenerate', '0');

		$sql = 'SELECT name FROM `'._DB_PREFIX_.'image_type` WHERE products = 1 ORDER BY `width` DESC';
        $result = Db::getInstance()->getValue($sql);
        
        if($result)
        {
			Configuration::updateValue('uit_zoom_full_image_size',   $result);
			Configuration::updateValue('uit_zoom_normal_image_size',   $result);
			Configuration::updateValue('uit_zoom_thumb_image_size',   $result);
        }
        else
        {
			Configuration::updateValue('uit_zoom_full_image_size', '');
			Configuration::updateValue('uit_zoom_normal_image_size', '');
			Configuration::updateValue('uit_zoom_thumb_image_size', '');
        }

	    return true;
  	}


	public function hookactionHtaccessCreate()
	{

  	 	require_once $this->getLocalPath() . 'src/htaccess.php';
  	 	
        $htaccess_builder = new Htaccess();
        $htaccess_builder->generate_htaccess_content();
        $htaccess_builder->add_to_htaccess();

		if(file_exists(_PS_IMG_DIR_.'/.htaccess'))
		{
			$str = Tools::file_get_contents(_PS_IMG_DIR_.'/.htaccess');
			file_put_contents(_PS_IMG_DIR_.'/.htaccess', str_replace("|ico)", "|ico|webp)",$str));
		}

		return true;
	}

	public function uninstall()
	{
		Configuration::deleteByName('uit_token');
		
	  	parent::uninstall();
	  	return true;

	}
  
	public function install()
	{
		if (
			!parent::install() OR 
			!$this->registerHook('updateproduct') OR
			!$this->registerHook('addproduct') OR
			!$this->registerHook('displayHeader') OR
			!$this->registerHook('Header') OR
			!$this->registerHook('actionOnImageResizeAfter')
		 )
			return false;

		$this->installDb();
		$this->registerHook('actionHtaccessCreate');
		Tools::generateHtaccess();
		
		 $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'uit_smush` (
					`id` int(99) NOT NULL AUTO_INCREMENT,
					`object_id` int(11) NOT NULL,
					`object_type` varchar(255) NOT NULL,
					`image_size` varchar(255) NOT NULL,
					`original_size` int(11) NOT NULL,
					`new_size`  int(11) NOT NULL,
					`date_add` datetime NOT NULL,
					`processed` int(1) NOT NULL,
					PRIMARY KEY (`id`),
  					KEY `id` (`id`),
  					KEY `object_id` (`object_id`),
  					KEY `object_type` (`object_type`),
  					KEY `image_size` (`image_size`),
  					KEY `processed` (`processed`)
					) ENGINE= '._MYSQL_ENGINE_.' CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ';

		 Db::getInstance()->execute($sql);

		return true;
	}

 	public function hookActionOnImageResizeAfter($params)
    {
		if( (int)Configuration::get('uit_use_webp') == 0 || (int)Configuration::get('uit_auto_webp') == 0)
    		return true;

		$imagewebp = function_exists('imagewebp');
		$imagick = extension_loaded('imagick');
		$is_imagick = false;

		if($imagick)
		{
			if(class_exists('Imagick'))
			{
				$formats = Imagick::queryFormats();
				foreach($formats as $format)
				{
					if(strtoupper($format) == 'WEBP')
						$is_imagick = true;
				}
			}
		}

        try {

            $image_path = $params['dst_file'];
            $image_quality = 85;

			if (file_exists( $image_path )) 
			{
				if($is_imagick || $imagewebp )
				{
					$true = \WebPConvert\WebPConvert::convert($image_path, str_replace('.jpg', '.webp', $image_path), array( 'converters' => ['gd', 'imagick', 'imagemagick'], 'lossless' => false,  'max-quality' => $image_quality , 'quality' => $image_quality , 'default-quality' => $image_quality ,  'fail' => 'original',  'serve-image' => ['headers' => [  'cache-control' => true,   'vary-accept' => true  ],   'cache-control-header' => 'max-age=2' ],   'convert' => [  'quality' => $image_quality   ]));	

					$image_path2x = str_replace('.jpg', '2x.jpg', $image_path);

					if(file_exists($image_path2x))
					{
						$true = \WebPConvert\WebPConvert::convert($image_path2x, str_replace('.jpg', '.webp', $image_path2x), array( 'converters' => ['gd', 'imagick', 'imagemagick'], 'lossless' => false,  'max-quality' => $image_quality , 'quality' => $image_quality , 'default-quality' => $image_quality ,  'fail' => 'original',  'serve-image' => ['headers' => [  'cache-control' => true,   'vary-accept' => true  ],   'cache-control-header' => 'max-age=2' ],   'convert' => [  'quality' => $image_quality   ]));	
					}
				}
				else
				{
					if(!file_exists(str_replace('.jpg', '.webp', $image_path)))
					{
						$image = UltimateImageTool::get_webp_from_advancedplugins(base64_encode(file_get_contents($image_path)), 'image_file_b64');
							
						if($image)
							file_put_contents(str_replace('.jpg', '.webp', $image_path), $image);
					}
				}
			}

        } catch (Exception $exception) {
            PrestaShopLogger::addLog(
                empty($exception->getMessage()) ? 'Unknown error' : $exception->getMessage(),
                1,
                $exception->getCode(),
                $this->name,
                null,
                true
            );
        }
    }

    public function register_filters($params)
    {
    	global $smarty;

    	if(Tools::getValue('controller') != 'product')
    		return true;

        $smarty->registerFilter('output', array(Module::getInstanceByName($this->name), 'parseTemplateStandard'));
      
    }


    private function get_zoom_html()
    {
        $product = new Product((int)Tools::getValue('id_product'), true, (int)$this->context->cookie->id_lang);
        $lrw = $product->link_rewrite;
        $pid = (int)$product->id;
        $link = new Link();

        $productImages = $product->getImages((int)$this->context->cookie->id_lang);

        if (!is_array($productImages) || empty($productImages)) 
        {
            return '';
        }
		
        if( strlen(Configuration::get('uit_zoom_normal_image_size')) > 3){
			$thumb_info  = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'image_type` WHERE products = 1 AND name = "'.pSQL(Configuration::get('uit_zoom_normal_image_size')).'"');
        }
		else
			$thumb_info  = array('width' => 502, 'height' => 334);
		
       	$prd_images = array();
       	$thumbnail_size = Configuration::get('uit_zoom_thumb_image_size'); 
       	$normal_size = Configuration::get('uit_zoom_normal_image_size');
       	$full_size = Configuration::get('uit_zoom_full_image_size'); 

	    foreach($productImages as $i)
	    {
	    	$a = array();
	    	$a['full'] = '//'.$link->getImageLink($product->link_rewrite, $pid .'-'.$i['id_image'], $full_size);
	    	$a['normal'] = '//'.$link->getImageLink($product->link_rewrite, $pid .'-'.$i['id_image'], $normal_size);
	    	
	    	if(!empty($thumbnail_size))
	    		$a['thumb'] = '//'.$link->getImageLink($product->link_rewrite, $pid .'-'.$i['id_image'], $thumbnail_size);
	    	else
	    		$a['thumb'] = '';

	    	$a['legend'] = $i['legend'];
	    	$prd_images[] = $a;
	    }

    	$type = (int)Configuration::get('uit_zoom_type');
    	$zoom_html = '<div class="uit-gallery uit-bx-slider" itemscope itemtype="http://schema.org/ImageGallery" id="thumbnails">';
    	foreach($prd_images as $i)
    	{
    		 $zoom_html .='<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
	                        <a href="'.$i['full'].'" data-thumb="'.$i['normal'].'" itemprop="contentUrl">
	                            <img width="'.$thumb_info['width'].'" height="'.$thumb_info['height'].'" src="'.$i['normal'].'" alt="'.$i['legend'].'">
	                        </a>
	                    </figure>';
    	}

        $zoom_html .='</div>';
        if(!empty($thumbnail_size))
        {
        	if( strlen(Configuration::get('uit_zoom_thumb_image_size')) > 2)
				$thumb_info  = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'image_type` WHERE products = 1 AND name = "'.pSQL(Configuration::get('uit_zoom_thumb_image_size')).'"');
			else
				$thumb_info  = array('width' => 80, 'height' => 80);

        	$zoom_html .= '<div class="uit-gallery-thumbs-container"><ul id="uit-gallery-thumbs" class="uit-gallery-thumbs-list" style="display:none">';
	    	foreach($prd_images as $i)
	    	{
	    		 $zoom_html .='<li class="thumb-item">
		                        <div class="thumb">
		                        	<a href="">
		                        	 	<img width="80" src="'.$i['thumb'].'" alt="'.$i['legend'].'">
		                        	</a>
		                           
		                        </div>
		                    </li>';
	    	}
        	$zoom_html .= '</ul></div>';

        }

		return $zoom_html;
    }

    public function parseTemplateStandard($output, $smarty = NULL)
    {	
    	if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') === true) 
    		if(substr_count($output, 'images-container' ) == 0)
    			 return $output;

    	if (version_compare(_PS_VERSION_, '1.7.0.0', '<') === true) 
    		if(substr_count($output, 'image-block' ) == 0)
    			 return $output;

    	global $smarty;
        $zoom_html  =  $this->get_zoom_html();

    	if(empty($zoom_html))
    		 return $output;

		$html = str_get_html($output);
    	if(is_object($html))
    	{
	       
	        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') === true) 
	        {
	        	foreach($html->find('.images-container') as $imgc)
	        		$imgc->innertext = $zoom_html;
	        }
	        else
	        {
	        	foreach($html->find('#image-block') as $imgc)
	        		$imgc->innertext = $zoom_html;
	        	
	        	foreach($html->find('#views_block') as $imgc)
	        		$imgc->outertext = '';
	        }
    	}

    	$output = $html;
    	unset($html);
    	unset($zoom_html);
        return $output;
    }

	public function hookdisplayHeader($params)
	{	

        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') === true) 
        {

        	if( Configuration::get('uit_lazy_load') == 'enabled')
				    $this->context->controller->registerJavascript(
				        'qazy-lib',
				         'modules/'.$this->name.'/views/js/qazy.js',
				       array(
				          'position' => 'bottom',
				           'attribute' => 'async',
				           'inline' => false,
				           'priority' => 10,
				        )
				    );

			if( Configuration::get('uit_mouse_hover') == 'enabled')
				    $this->context->controller->registerJavascript(
				        'mousehover-lib',
				         'modules/'.$this->name.'/views/js/mousehover_17.js',
				        array(
				          'position' => 'bottom',
				           'attribute' => 'async',
				           'inline' => false,
				           'priority' => 11,
				        )
				    );	

	 		if( Configuration::get('uit_zoom') == '1')
	 		{
	 			$this->context->controller->addJqueryPlugin('bxslider');
  				$this->context->controller->registerJavascript(
				        'modulobox-lib',
				         'modules/'.$this->name.'/views/js/modulobox.min.js',
				       array(
				          'position' => 'bottom',
				           'attribute' => 'async',
				           'inline' => false,
				           'priority' => 10,
				        )
				    );

				$this->context->controller->registerStylesheet(
				   'modulobox-lib-css',
				     'modules/'.$this->name.'/views/css/modulobox.min.css',
				   [
				      'media' => 'all',
				      'priority' => 200,
				   ]
				);

				$this->context->smarty->assign(
				array(
						'jqZoomEnabled' => false
				));

				$this->register_filters($params);

	 		}
        }
        else
        {
	 		if( Configuration::get('uit_lazy_load') == 'enabled')
				$this->context->controller->addJS(($this->_path).'views/js/qazy.js', 'all');

	 		if( Configuration::get('uit_zoom') == '1')
	 		{
	 			$this->context->controller->addJqueryPlugin('bxslider');

				$this->context->controller->addJS(($this->_path).'views/js/modulobox.min.js', 'all');
				$this->context->controller->addCSS(($this->_path).'views/css/modulobox.min.css', 'all');

				$this->context->smarty->assign(
				array(
						'jqZoomEnabled' => false
				));

				$this->register_filters($params);
	 		}

			if( Configuration::get('uit_mouse_hover') == 'enabled')
			{
				$this->context->controller->addJS(($this->_path).'views/js/mousehover.js', 'all');
			}
        }
	}

	public static function get_domain_prefix()
	{
		$protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
		return $protocol_link;
	}

	public function hookaddproduct($params)
	{
		$this->hookupdateproduct($params);
		return true;
	}

	public static function imageExists($url)
	{

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,$url);
	    curl_setopt($ch, CURLOPT_NOBODY, 1);
	    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	    if(curl_exec($ch)!==FALSE)
	    {
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	}

	public static function isJson($string) 
	{
	 	json_decode($string);
	 	return (json_last_error() == JSON_ERROR_NONE);
	}


	public static function get_webp_from_advancedplugins($path = NULL, $image_type = 'image_file_b64')
	{

		if($path == NULL)
			return false;

		$ch = curl_init();
		$data = array(
						'action' => 'convert2webp',
						'token'  => '63etrbf3yhrtbsgwsd',
						$image_type => $path,
		);

		curl_setopt($ch, CURLOPT_URL,"https://api.advancedplugins.com");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		// Receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: image/jpeg"));

		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);

		$server_output = curl_exec($ch);

		curl_close ($ch);

		if(!$server_output)
		{
			return false;
		}
		if (substr_count($server_output, 'WEBPVP8') == 0) {
	       return false;
	    }

		$im = getimagesizefromstring($server_output);

		return $server_output;
	}

	public static function get_url_content2($url)
	{

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_COOKIEFILE, "cookie.txt");
		curl_setopt($curl, CURLOPT_COOKIEJAR, "cookie.txt");
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_ENCODING, "");
		curl_setopt($curl, CURLOPT_TIMEOUT, 1);
		curl_setopt($curl, CURLOPT_MAXREDIRS, 8);
		$contents = curl_exec($curl);
		curl_close($curl);

		return $contents;
	}

	public static function get_optimized_image($img_src, $quality = 70)
	{
		$service = 'http://api.resmush.it/ws.php?qlty='.$quality.'&img=';
		$o = Tools::jsonDecode(self::get_url_content_static2($service . $img_src), true);

		if(isset($o->error))
		{
			return array('is_end' => 1);
		}
		else
			return $o;
	}

	public function hookupdateproduct($params)
	{

		$id_product = (int)$params['id_product'];
		$image_types = ImageType::getImagesTypes('products');
		$alt_apply =  Configuration::get('alt_tags_auto');
		$alt_format =  Configuration::get('uit_alt_format');
		$alt_tags_enbled = 0;

		foreach($image_types as $it)
		{
			$sql2 = 'SELECT * FROM  `'._DB_PREFIX_.'uit_smush` WHERE object_id = '.(int)$id_product.' AND image_size = "'.pSQL($it['name']).'"  AND object_type ="product"';
			$row2 = Db::getInstance()->getRow($sql2);

			if(!$row2)
			{
				$sql = 'INSERT INTO  `'._DB_PREFIX_.'uit_smush` ( id, object_id, object_type, original_size, new_size, date_add, processed, image_size  )  VALUES (NULL,'.(int)$id_product.',"product", 0,0, "'.date('Y-m-d H:i:s').'", 0, "'.pSQL($it['name']).'" )';
				Db::getInstance()->execute($sql);
			}
		}

		$sql2 = 'SELECT * FROM  `'._DB_PREFIX_.'uit_smush` WHERE object_id = '.(int)$id_product.' AND image_size = "" AND object_type ="product"';
		$row2 = Db::getInstance()->getRow($sql2);

		if(!$row2)
		{
			$sql = 'INSERT INTO  `'._DB_PREFIX_.'uit_smush` ( id, object_id, object_type, original_size, new_size, date_add, processed, image_size  )  VALUES (NULL,'.(int)$id_product.', "product", 0,0, "'.date('Y-m-d H:i:s').'", 0, "" )';
			Db::getInstance()->execute($sql);
		}

		if($alt_apply == 'yes-all')
		{
	   		$sql = 'SELECT  * FROM `'._DB_PREFIX_.'image` i INNER JOIN `'._DB_PREFIX_.'image_lang` il ON i.id_image = il.id_image WHERE i.id_product ='.(int)$id_product;	
	   		$product_images = Db::getInstance()->executeS($sql);

	   		if($product_images )
	   		{
		 	   	foreach($product_images as $pi)
		 	   	{
					$q = 'UPDATE `'._DB_PREFIX_.'image_lang` SET legend = "" WHERE id_image = '.(int)$pi['id_image'];
					Db::getInstance()->execute($q);  
		 	   	}			
	   		}

			$alt_tags_enbled = 1;
		}
		elseif($alt_apply == 'yes-only-without-tags')
		{
			$sql = 'SELECT  * FROM `'._DB_PREFIX_.'image` i INNER JOIN `'._DB_PREFIX_.'image_lang` il ON i.id_image = il.id_image AND il.legend = "" WHERE i.id_product ='.(int)$id_product;
			$alt_tags_enbled = 1;
		}
	
		if($alt_tags_enbled == 1)
		{
			$results = Db::getInstance()->executeS($sql);

			if(!empty($results))
			{
				foreach($results as $res)
				{
					$alt_tag = $alt_format;
					$product = new Product($res['id_product'], true, $res['id_lang']);
					$category = new Category($product->id_category_default, $res['id_lang']);
					$price = Product::getPriceStatic($product->id,true);
					$alt_tag  = str_replace(array('{PARENT_CATEGORY_NAME}', '{SUPPLIER_NAME}', '{MANUFACTURER_NAME}', '{PRODUCT_NAME}', '{PRODUCT_PRICE}', '{PRODUCT_SHORT_DESCRIPTION}', '{IMAGE_POSITION}'), array($category->name, $product->supplier_name, $product->manufacturer_name, $product->name, $price, $product->description_short, $res['position']), $alt_tag);
					
					$q = 'UPDATE `'._DB_PREFIX_.'image_lang` SET legend = "'.pSQL($alt_tag).'" WHERE id_image ='.(int)$res['id_image'] .' AND id_lang ='.(int)$res['id_lang'];
					Db::getInstance()->execute($q);
				}

			}
		}

		return true;
	}

	public function _buildData()
	{
		$this->languages = Language::getLanguages(true); 
	}

	public function process_post()
	{
		Configuration::updateValue('uit_products',  Tools::getValue('product_per_excution') );
		Configuration::updateValue('uit_cron_quality',  Tools::getValue('cron_image_quality') );
		Configuration::updateValue('uit_quality',  Tools::getValue('cron_image_quality') );
		return true;
	}

	public function getContent()
	{
		if(Tools::getIsset('product_per_excution'))
			$this->process_post();

		$this->_html = '<h2>'.$this->displayName.'</h2>';
		$this->_buildData();
		$this->_buildHtml();

		return $this->_html;
	}

	private function get_url_content($url)
	{
		return Tools::file_get_contents($url);
	}


	public static function get_url_content_static2($url)
	{
		return Tools::file_get_contents($url);
	}

	public function set_sitemap_variables()
	{
		$arr = array();	
		$arr['sitemap_image_size'] = Configuration::get('sitemap_image_size');

		$this->smarty->assign($arr);
	}


	public function set_alt_tags()
	{
		$arr = array();	
		$arr['uit_alt_format'] = Configuration::get('uit_alt_format');
		$arr['alt_tags_auto'] = Configuration::get('alt_tags_auto');

		$this->smarty->assign($arr);
	}


	public function set_product_variables()
	{
		$arr = array();	

		$sql = 'SELECT  count(id_product) FROM `'._DB_PREFIX_.'product`  WHERE active = 1';	
		$results = Db::getInstance()->getRow($sql);

		if($results)
			$arr['products_count'] = $results['count(id_product)'];
	    else
	   		$arr['products_count'] = 0;

	   	$is_writable = false;

	   	if(  Tools::substr(decoct(fileperms(_PS_PROD_IMG_DIR_)), -3) >= 755)
	   		$is_writable = true;

		$image_types = ImageType::getImagesTypes('products');
		$image_types_convert = $image_types;

		$k = 0;
		$arr['total_saved_space_products'] = (int)Configuration::get('uit_saved_');
		foreach($image_types as $it)
		{
			$image_types[$k]['saved_space']  = (int)Configuration::get('uit_saved_'.$it['name']);
			$arr['total_saved_space_products'] += $image_types[$k]['saved_space'];

			$off =  Configuration::get('uit_i_o_'.$it['name']);
			$off_convert =  Configuration::get('uit_i_o_c_'.$it['name']);

			if($off < $arr['products_count'])
				$image_types[$k]['offset']  = (int)$off;
			else
				$image_types[$k]['offset']  = (int) $arr['products_count'];	

			if($off_convert < $arr['products_count'])
				$image_types_convert[$k]['offset']  = (int)$off_convert;
			else
				$image_types_convert[$k]['offset']  = (int) $arr['products_count'];	


			$image_types_convert[$k]['percent']  = $image_types_convert[$k]['offset']* 100 / $arr['products_count'];
			$image_types[$k]['percent']  = $image_types[$k]['offset']* 100 / $arr['products_count'];

			$k++;
		}

		$arr['product_writable'] = $is_writable;
		$arr['product_sizes'] = $image_types;
		$arr['product_sizes_convert'] = $image_types_convert;
		$arr['original_saved_space'] = (int)Configuration::get('uit_saved_');
		$offset_original = (int)Configuration::get('uit_i_o_');
		$offset_original_convert = (int)Configuration::get('uit_i_o_c_');
		$arr['uit_product'] = (int)Configuration::get('uit_products');
		$arr['uit_cron_quality'] = (int)Configuration::get('uit_cron_quality');	
		$arr['uit_quality'] = (int)Configuration::get('uit_quality');	
		$arr['uit_quality_cms'] = (int)Configuration::get('uit_quality_cms');	
		$arr['uit_use_webp'] = (int)Configuration::get('uit_use_webp');	
		$arr['uit_auto_webp'] = (int)Configuration::get('uit_auto_webp');	
		$arr['logs']  = false;
		$arr['log_pages']  = false;
		$arr['uit_cron_last_execution'] = Configuration::get('uit_cron_last_execution');	

		if($offset_original >= $arr['products_count'])
		{
			$arr['original_offset'] = (int)$arr['products_count'];
		}
		else
		{
			$arr['original_offset'] = (int)$offset_original;		
		}


		if($offset_original_convert >= $arr['products_count'])
		{
			$arr['original_offset_convert'] = (int)$arr['products_count'];
		}
		else
		{
			$arr['original_offset_convert'] = (int)$offset_original_convert;		
		}

		if( $arr['products_count'] > 0)
		{
			$arr['original_percent']  = $arr['original_offset']* 100 / $arr['products_count'];
			$arr['original_percent_convert']  = $arr['original_offset_convert']* 100 / $arr['products_count'];	
		}
		else
		{
			$arr['original_percent']  = 0;
			$arr['original_percent_convert']  = 0;		
		}

		$this->smarty->assign($arr);
	}

	public function set_category_variables()
	{
		$arr = array();	

		$sql = 'SELECT  count(id_category) FROM `'._DB_PREFIX_.'category`  WHERE active = 1';	
		$results = Db::getInstance()->getRow($sql);

		if($results)
			$arr['category_count'] = $results['count(id_category)'];
	    else
	   		$arr['category_count'] = 0;


   		$is_writable = false;

	   	if(  Tools::substr(decoct(fileperms(_PS_CAT_IMG_DIR_)), -3) >= 755)
	   		$is_writable = true;

		$image_types = ImageType::getImagesTypes('categories');
		$image_types_convert = $image_types;

		$k = 0;
		$arr['total_saved_space_categories'] = (int)Configuration::get('uit_saved_cat_');

		foreach($image_types as $it)
		{
			$image_types[$k]['saved_space']  = (int)Configuration::get('uit_saved_cat_'.$it['name']);
			$arr['total_saved_space_categories'] += $image_types[$k]['saved_space'];

			$off =  Configuration::get('uit_i_o_cat_'.$it['name']);
			$off_convert =  Configuration::get('uit_i_o_cat_c_'.$it['name']);

			if($off < $arr['category_count'])
				$image_types[$k]['offset']  = (int)$off;
			else
				$image_types[$k]['offset']  = (int) $arr['category_count'];	

			if($off_convert < $arr['category_count'])
				$image_types_convert[$k]['offset']  = (int)$off_convert;
			else
				$image_types_convert[$k]['offset']  = (int) $arr['category_count'];	

			$image_types_convert[$k]['percent']  = $image_types_convert[$k]['offset']* 100 / $arr['category_count'];
			$image_types[$k]['percent']  = $image_types[$k]['offset']* 100 / $arr['category_count'];

			$k++;
		}

		$arr['category_sizes_convert'] = $image_types_convert;
		$arr['category_sizes'] = $image_types;
		$arr['category_writable'] = $is_writable;
		$arr['original_saved_space_cat'] = (int)Configuration::get('uit_saved_cat_');
		$offset_original = (int)Configuration::get('uit_i_o_cat_');
		$offset_original_convert = (int)Configuration::get('uit_i_o_cat_c_');
		$arr['uit_category'] = (int)Configuration::get('uit_categories');
		$arr['uit_cron_quality_cat'] = (int)Configuration::get('uit_cron_quality_cat');	
		$arr['uit_quality_cat'] = (int)Configuration::get('uit_quality_cat');	

		if($offset_original >= $arr['category_count'])
			$arr['original_offset_cat'] = (int)$arr['category_count'];
		else
			$arr['original_offset_cat'] = (int)$offset_original;		

		if($offset_original_convert >= $arr['category_count'])
		{
			$arr['original_offset_cat_convert'] = (int)$arr['category_count'];
		}
		else
		{
			$arr['original_offset_cat_convert'] = (int)$offset_original_convert;		
		}

		if($arr['category_count'] > 0 )
		{
			$arr['original_percent_cat']  = $arr['original_offset_cat']* 100 / $arr['category_count'];
			$arr['original_percent_cat_convert']  = $arr['original_offset_cat_convert']* 100 / $arr['category_count'];	
		}
		else
		{
			$arr['original_percent_cat']  = 0;
			$arr['original_percent_cat_convert']  = 0;	
		}

		$this->smarty->assign($arr);
	}

	public function set_manufacturers_variables()
	{
		$arr = array();	

		$sql = 'SELECT  count(id_manufacturer) FROM `'._DB_PREFIX_.'manufacturer`  WHERE active = 1';	
		$results = Db::getInstance()->getRow($sql);

		if($results)
			$arr['manufacturer_count'] = $results['count(id_manufacturer)'];
	    else
	   		$arr['manufacturer_count'] = 0;

   		$is_writable = false;

	   	if(  Tools::substr(decoct(fileperms(_PS_MANU_IMG_DIR_)), -3) >= 755)
	   		$is_writable = true;

		$image_types = ImageType::getImagesTypes('manufacturers');
		$image_types_convert = $image_types;

		$k = 0;
		$arr['total_saved_space_manufacturers'] = (int)Configuration::get('uit_saved_manuf_');
		foreach($image_types as $it)
		{
			$image_types[$k]['saved_space']  = (int)Configuration::get('uit_saved_manuf_'.$it['name']);
			$arr['total_saved_space_manufacturers'] += $image_types[$k]['saved_space'];


			$off =  Configuration::get('uit_i_o_manuf_'.$it['name']);
			$off_convert =  Configuration::get('uit_i_o_manuf_'.$it['name']);

			if($off < $arr['manufacturer_count'])
				$image_types[$k]['offset']  = (int)$off;
			else
				$image_types[$k]['offset']  = (int) $arr['manufacturer_count'];	


			if($off_convert < $arr['manufacturer_count'])
				$image_types_convert[$k]['offset']  = (int)$off_convert;
			else
				$image_types_convert[$k]['offset']  = (int) $arr['manufacturer_count'];	


			$image_types_convert[$k]['percent']  = $image_types_convert[$k]['offset']* 100 / $arr['manufacturer_count'];
			$image_types[$k]['percent']  = $image_types[$k]['offset']* 100 / $arr['manufacturer_count'];

			$k++;

		}

		$arr['manufacturer_sizes_convert'] = $image_types_convert;
		$arr['manufacturer_sizes'] = $image_types;
		$arr['manufacturer_writable'] = $is_writable;
		$arr['original_saved_space_manuf'] = (int)Configuration::get('uit_saved_manuf_');
		$offset_original = (int)Configuration::get('uit_i_o_manuf_');
		$offset_original_convert = (int)Configuration::get('uit_i_o_manuf_c_');
		$arr['uit_manufacturer'] = (int)Configuration::get('uit_manufacturer');
		$arr['uit_cron_quality_manuf'] = (int)Configuration::get('uit_cron_quality_manuf');	
		$arr['uit_quality_manuf'] = (int)Configuration::get('uit_quality_manuf');	

		if($offset_original >= $arr['manufacturer_count'])
			$arr['original_offset_manuf'] = (int)$arr['manufacturer_count'];
		else
			$arr['original_offset_manuf'] = (int)$offset_original;		

		if($offset_original_convert >= $arr['manufacturer_count'])
		{
			$arr['original_offset_manuf_convert'] = (int)$arr['manufacturer_count'];
		}
		else
		{
			$arr['original_offset_manuf_convert'] = (int)$offset_original_convert;		
		}

		if($arr['manufacturer_count']>0)
		{
			$arr['original_percent_manuf']  = $arr['original_offset_manuf'] * 100 / $arr['manufacturer_count'];
			$arr['original_percent_manuf_convert']  = $arr['original_offset_manuf_convert'] * 100 / $arr['manufacturer_count'];	
		}
		else
		{
			$arr['original_percent_manuf']  = 0;
			$arr['original_percent_manuf_convert']  = 0;	
		}

		$this->smarty->assign($arr);
	}

	public function set_suppliers_variables()
	{
		$arr = array();	

		$sql = 'SELECT  count(id_supplier) FROM `'._DB_PREFIX_.'supplier`  WHERE active = 1';	
		$results = Db::getInstance()->getRow($sql);

		if($results)
			$arr['supplier_count'] = $results['count(id_supplier)'];
	    else
	   		$arr['supplier_count'] = 0;


   		$is_writable = false;

	   	if(  Tools::substr(decoct(fileperms(_PS_SUPP_IMG_DIR_)), -3) >= 755)
	   		$is_writable = true;

		$image_types = ImageType::getImagesTypes('suppliers');
		$image_types_convert = $image_types;

		$k = 0;
		$arr['total_saved_space_suppliers'] = (int)Configuration::get('uit_saved_sup_');
		foreach($image_types as $it)
		{
			$image_types[$k]['saved_space']  = (int)Configuration::get('uit_saved_sup_'.$it['name']);
			$arr['total_saved_space_suppliers'] += $image_types[$k]['saved_space'];

			$off =  Configuration::get('uit_i_o_sup_'.$it['name']);
			$off_convert =  Configuration::get('uit_i_o_sup_c_'.$it['name']);

			if($off < $arr['supplier_count'])
				$image_types[$k]['offset']  = (int)$off;
			else
				$image_types[$k]['offset']  = (int) $arr['supplier_count'];	

			if($off_convert < $arr['supplier_count'])
				$image_types_convert[$k]['offset']  = (int)$off_convert;
			else
				$image_types_convert[$k]['offset']  = (int) $arr['supplier_count'];	


			if(!empty($arr['supplier_count']))
				$image_types[$k]['percent']  = $image_types[$k]['offset']* 100 / $arr['supplier_count'];
			else
				$image_types[$k]['percent']  = 0;

			if(!empty($arr['supplier_count']))
				$image_types_convert[$k]['percent']  = $image_types_convert[$k]['offset']* 100 / $arr['supplier_count'];
			else
				$image_types_convert[$k]['percent']  = 0;

			$k++;
		}

		$arr['supplier_sizes'] = $image_types;
		$arr['supplier_writable'] = $is_writable;
		$arr['original_saved_space_sup'] = (int)Configuration::get('uit_saved_sup_');
		$offset_original = (int)Configuration::get('uit_i_o_sup_');
		$offset_original_convert = (int)Configuration::get('uit_i_o_cat_c_');
		$arr['uit_supplier'] = (int)Configuration::get('uit_supplier');
		$arr['uit_cron_quality_sup'] = (int)Configuration::get('uit_cron_quality_sup');	
		$arr['uit_quality_sup'] = (int)Configuration::get('uit_quality_sup');	

		if($offset_original >= $arr['supplier_count'])
			$arr['original_offset_sup'] = (int)$arr['supplier_count'];
		else
			$arr['original_offset_sup'] = (int)$offset_original;		

		if($offset_original_convert >= $arr['supplier_count'])
		{
			$arr['original_offset_sup_convert'] = (int)$arr['supplier_count'];
		}
		else
		{
			$arr['original_offset_sup_convert'] = (int)$offset_original_convert;		
		}

		if(!empty($arr['supplier_count']))
		{
			$arr['original_percent_sup']  = $arr['original_offset_sup'] * 100 / $arr['supplier_count'];
			$arr['original_percent_sup_convert']  = $arr['original_offset_sup_convert'] * 100 / $arr['supplier_count'];
		}
		else
		{
			$arr['original_percent_sup'] = 0;
			$arr['original_percent_sup_convert']  =  0;
		}

		$arr['supplier_sizes_convert'] = $image_types_convert;
		$this->smarty->assign($arr);
	}

	public static function getAllDirs($directory, $directory_seperator)
	{
		$dirs = array_map(function ($item) use ($directory_seperator) 
		{
		    return $item . $directory_seperator;
		}, array_filter(glob($directory . '*'), 'is_dir'));

		foreach ($dirs AS $dir) {
		    $dirs = array_merge($dirs, self::getAllDirs($dir, $directory_seperator));
		}

		return $dirs;
	}

	public static function getAllImgs($directory, $type = '')
	{
		  $resizedFilePath = array();
		  
		  foreach ($directory AS $dir) 
		  {
		    foreach (glob($dir . '/*'.$type.'.jpg') as $filename) 
		        array_push($resizedFilePath, $filename);

		    foreach (glob($dir . '/*'.$type.'.png') as $filename) 
		        array_push($resizedFilePath, $filename);
		    
		    foreach (glob($dir . '/*'.$type.'.jpeg') as $filename) 
		        array_push($resizedFilePath, $filename);
		    
		    foreach (glob($dir . '/*'.$type.'.JPEG') as $filename) 
		        array_push($resizedFilePath, $filename);

		    foreach (glob($dir . '/*'.$type.'.JPG') as $filename) 
		        array_push($resizedFilePath, $filename);
		    		
		    foreach (glob($dir . '/*'.$type.'.jpg') as $filename) 
		        array_push($resizedFilePath, $filename);

		    foreach (glob($dir . '/*'.$type.'.PNG') as $filename) 
		        array_push($resizedFilePath, $filename);
		}

		return $resizedFilePath;
	}

	public static function getDirContents($root) 
	{
		$iter = new RecursiveIteratorIterator(
		    new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
		    RecursiveIteratorIterator::SELF_FIRST,
		    RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
		);

		$paths = array($root);

		foreach ($iter as $path => $dir) 
		{
		    if ($dir->isDir()) 
		    {
		        $paths[] = $path;
		    }
		}

		return $paths;
	}


	public static function search_for_images_in_folder($directory, $type = '')
	{
		$directories = self::getDirContents($directory);
		$directories[] = $directory;

		if(substr_count($directory, '/modules') > 0)
		{
			$directories[] = str_replace('/modules', '/img/co', $directory);
			$directories[] = str_replace('/modules', '/img/l', $directory);
			$directories[] = str_replace('/modules', '/img/genders', $directory);
			$directories[] = str_replace('/modules', '/img/os', $directory);
			$directories[] = str_replace('/modules', '/img/su', $directory);
			$directories[] = str_replace('/modules', '/img/jquery-ui', $directory);
			$directories[] = str_replace('/modules', '/img/st', $directory);
			$directories[] = str_replace('/modules', '/img/scenes', $directory);
			$directories[] = str_replace('/modules', '/upload', $directory);
		}

		$directories = array_unique($directories);
		array_filter($directories);
		$allimages = self::getAllImgs($directories, $type);

		return  $allimages;
	}

	public function verify_if_htaccess_is_written()
	{

		if(file_exists(_PS_ROOT_DIR_.'/.htaccess'))
		{
			$str = Tools::file_get_contents(_PS_ROOT_DIR_.'/.htaccess');
			if($str)
			{
				if(substr_count($str, '# Ultimate Image Tools - Do not edit') > 0)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}
	}

	public function get_subdirectories($directory)
	{
	    $glob = glob($directory . '/*');

	    if($glob === false)
	    {
	        return array();
	    }

	    return array_filter($glob, function($dir) {
	        return is_dir($dir);
	    });
	}
	public function _buildHtml()
	{
	
		$ps_version = Tools::substr(_PS_VERSION_,0,3);
		$arr = array();

		$htaccess_modified = $this->verify_if_htaccess_is_written();

		if((int)Configuration::get('ait_force_regenerate') == 0)
		{
			Configuration::updateValue('ait_force_regenerate', 1);
			Tools::generateHtaccess();
			$htaccess_modified = $this->verify_if_htaccess_is_written();
		}

		$arr['uit_module_path'] = Tools::getHttpHost(true).__PS_BASE_URI__.'modules/'.$this->name;		
		$arr['uit_module_path_short'] = Tools::getHttpHost(true).__PS_BASE_URI__.'module/'.$this->name;		
		$arr['uit_domain_url'] = Tools::getHttpHost(true).__PS_BASE_URI__;
		$arr['uit_token'] = Configuration::get('uit_token');
		$arr['uit_tpl_dir'] = _PS_THEME_DIR_;
		$arr['uit_module_dir'] = _PS_MODULE_DIR_;
		$arr['uit_root_dir'] = _PS_ROOT_DIR_;
		$arr['uit_htaccess'] = $htaccess_modified;
		$arr['uit_quality_theme'] = (int)Configuration::get('uit_quality_theme');
		$arr['uit_quality_module'] = (int)Configuration::get('uit_quality_module');
		$arr['total_saved_space_theme'] = (int)Configuration::get('uit_saved_theme');
		$arr['total_saved_space_module'] = (int)Configuration::get('uit_saved_module');
		$arr['uit_lazy_load'] = Configuration::get('uit_lazy_load');
		$arr['uit_mouse_hover'] = Configuration::get('uit_mouse_hover');
		$arr['uit_mouse_hover_thumb'] = Configuration::get('uit_mouse_hover_thumb');
		$arr['uit_mouse_hover_ts'] = Configuration::get('uit_mouse_hover_ts');		
		$arr['uit_mouse_hover_ps'] = Configuration::get('uit_mouse_hover_ps');			
		$arr['uit_mouse_hover_position'] = Configuration::get('uit_mouse_hover_position');
		$arr['uit_hover_image_type'] = Configuration::get('uit_hover_image_type');
		$arr['uit_shop_enable'] = Configuration::get('PS_SHOP_ENABLE');
		$arr['uit_zoom'] = Configuration::get('uit_zoom');
		$arr['uit_zoom_full_image_size'] = Configuration::get('uit_zoom_full_image_size');
		$arr['uit_zoom_normal_image_size'] = Configuration::get('uit_zoom_normal_image_size');
		$arr['uit_zoom_thumb_image_size'] = Configuration::get('uit_zoom_thumb_image_size');
		$arr['uit_zoom_type'] = Configuration::get('uit_zoom_type');
		$arr['uit_enable_gzip'] = Configuration::get('uit_enable_gzip');
		$arr['root_folders'] = self::get_subdirectories(_PS_ROOT_DIR_);
        $arr['uit_image_type']  = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'image_type` WHERE products = 1 ORDER BY `id_image_type` ASC');
		$arr['uit_ajax_url'] = Context::getContext()->link->getModuleLink($this->name, 'ajax');
		$arr['webp_exists'] = function_exists('imagewebp');
		$arr['imagick_exists'] = extension_loaded('imagick');

		if($arr['imagick_exists'])
		{
			if(class_exists('Imagick'))
			{
				$is_imagick = false;
				$formats = Imagick::queryFormats();

				foreach($formats as $format)
				{
					if(strtoupper($format) == 'WEBP')
						$is_imagick = true;
				}

				$arr['imagick_exists'] = $is_imagick;	
			}
		}
		
		$sql = 'SELECT  count(id_image) FROM `'._DB_PREFIX_.'image_lang`';	
		$results = Db::getInstance()->getRow($sql);
		$arr['uit_total_images'] = (int)$results['count(id_image)'];
		$sql = 'SELECT  count(id_image) FROM `'._DB_PREFIX_.'image_lang` WHERE legend = ""';	
		$results = Db::getInstance()->getRow($sql);	
		$arr['uit_empty_images'] = (int)$results['count(id_image)'];

		if ($ps_version == '1.5')
			$arr['css_file'] = 'global_15.css';
		else
			$arr['css_file'] = 'global_16.css';

		$this->set_product_variables();
		$this->set_category_variables();
		$this->set_manufacturers_variables();
		$this->set_suppliers_variables();
		$this->set_sitemap_variables();
		$this->set_alt_tags();

		$this->smarty->assign($arr);
		$this->_html .=  $this->display(__FILE__, 'views/templates/admin/content.tpl');
	}
}