<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */


header("Access-Control-Allow-Origin: *");
class ultimateimagetoolajaxswapModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	private $function_pre = "ajax_";
	private $prefix = 'http://' ;

	/**
	 * @see FrontController::initContent()
	 */


	public function initContent()
	{
		 parent::initContent();

		// die('xxxx');

		if (Tools::getIsset('action'))
		{

			$function_name = $this->function_pre.Tools::getValue('action');
			
			$this->set_prefix();

			if (method_exists('ultimateimagetoolajaxswapModuleFrontController', $function_name))
				$this->$function_name();
		}

	
	    if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') === true) 
			$this->setTemplate('module:ultimateimagetool/views/templates/front/sitemap.tpl');

		die();
		
	}



	private function set_prefix()
	{
		$PS_SSL_ENABLED = (int)Configuration::get('PS_SSL_ENABLED');
		
		if(	$PS_SSL_ENABLED === 1)
			$this->prefix = 'https://';

	}

	private function ajax_get_all_swap_images()
	{
		$position = Configuration::get('uit_mouse_hover_position');
		$products = Tools::jsonDecode(Tools::getValue('products'), true);

		$uit_mouse_hover_thumb = Configuration::get('uit_mouse_hover_thumb');

		$answer = array();
		$link = new Link();
		$context = Context::getContext();
		$id_lang = $context->cookie->id_lang;

		$image_type = 'home_'.'default';
		$image_type_ts =  'cart_'.'default';
		$image_type_ps =  'home_'.'default';

		if($uit_mouse_hover_thumb == 'enabled')
		{
			$set_type = Configuration::get('uit_hover_image_type');
			$set_type_ts = Configuration::get('uit_hover_image_ts');
			$set_type_ps = Configuration::get('uit_hover_image_ps');			
		}


		if($set_type)
			$image_type = $set_type;

		if($set_type_ps)
			$image_type_ps = $set_type_ps;

		if($set_type_ps)
			$image_type_ts = $set_type_ps;


		foreach($products as $prd)
		{
			$image_id = NULL;


		     $i = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS( 'SELECT id_image FROM  '._DB_PREFIX_.'image WHERE id_product = '.(int)$prd.' and cover is NOT NULL ORDER BY position DESC ' ) ;
		     $answer[$prd]['all'] = array();
	 
		    if($i)
		    {	
		    	$product = new Product($prd, false, $id_lang );
		    
		    	foreach($i as $ii)
		    	{
			    	$image_id = $ii['id_image'];
					if($uit_mouse_hover_thumb == 'enabled')
					{
						$arr = array();
						$arr['small'] =  $this->prefix .$link->getImageLink($product->link_rewrite, $image_id, $image_type_ts);
						$arr['big'] =  $this->prefix .$link->getImageLink($product->link_rewrite, $image_id, $image_type_ps);
						$answer[$prd]['all'][] = $arr;			
					}	    		
		    	}

		    }



			if($position == 'second_image')
		        $i = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS( 'SELECT id_image FROM  '._DB_PREFIX_.'image WHERE id_product = '.(int)$prd.' and cover is NULL ORDER BY position ASC LIMIT 1' ) ;
			else
		        $i = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS( 'SELECT id_image FROM  '._DB_PREFIX_.'image WHERE id_product = '.(int)$prd.' and cover is NULL ORDER BY position DESC LIMIT 3' ) ;
	 
		    if($i)
		    {	
		    	$product = new Product($prd, false, $id_lang );
		 
		    	foreach($i as $ii)
		    	{
			    	$image_id = $ii['id_image'];
			    	$imageLink = $this->prefix .$link->getImageLink($product->link_rewrite, $image_id, $image_type);
			    	$answer[$prd]['second'] = $imageLink;

					if($uit_mouse_hover_thumb == 'enabled')
					{
						$arr = array();
						$arr['small'] =  $this->prefix .$link->getImageLink($product->link_rewrite, $image_id, $image_type_ts);
						$arr['big'] =  $this->prefix .$link->getImageLink($product->link_rewrite, $image_id, $image_type_ps);
						$answer[$prd]['all'][] = $arr;			
					}	    		
		    	}

		    }






		}

	    die(
	    			Tools::jsonEncode(
	    					array(
	    							'error' => 0,
	    							'images' => $answer
	    						)
	    				)
	    		);


	}

	private function ajax_get_swap_image()
	{

		$position = Configuration::get('uit_mouse_hover_position');
		$id_product = Tools::getValue('id_product');

		$image_id = NULL;

		if($position == 'second_image')
	        $i = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow( 'SELECT id_image FROM  '._DB_PREFIX_.'image WHERE id_product = '.(int)$id_product.' and cover is NULL ORDER BY position ASC ' ) ;
		else
	        $i = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow( 'SELECT id_image FROM  '._DB_PREFIX_.'image WHERE id_product = '.(int)$id_product.' and cover is NULL ORDER BY position DESC ' ) ;
	 

	    if(!$i)
	    	die(
	    			Tools::jsonEncode(
	    					array(
	    							'error' => 1
	    						)
	    				)
	    		);

	   	$image_id = $i['id_image'];

		$link = new Link();
		$context = Context::getContext();
		$id_lang = $context->cookie->id_lang;
		$product = new Product($id_product, false, $id_lang );
		$image_type = 'home_'.'default';
		$set_type = Configuration::get('uit_hover_image_type');

		if($set_type == $image_type)
			$image_type = $set_type;

		$imageLink = $this->prefix .$link->getImageLink($product->link_rewrite, $image_id, $image_type);

	    die(
	    			Tools::jsonEncode(
	    					array(
	    							'error' => 0,
	    							'img_src' => $imageLink
	    						)
	    				)
	    		);
	    


	}


}