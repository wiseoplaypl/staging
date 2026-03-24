<?php
/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/models/ndkCf.php';

$full_products = array();	
	$sql_ordered = "SELECT od.id_order_detail, od.id_order, od.product_id, od.product_reference , o.current_state
	FROM "._DB_PREFIX_."order_detail od 
	inner join "._DB_PREFIX_."orders o on o.id_order = od.id_order 
	WHERE od.product_reference REGEXP '^custom-[0-9*]+-[0-9*]+-[0-9*]+-[0-9*]' 
	ORDER BY od.id_order_detail DESC";
	$products_ordered = Db::getInstance()->executeS($sql_ordered);
	


	foreach($products_ordered as $product)
	{
		$id_product_to_search =  explode('-', $product['product_reference']);
		$my_id_product = $id_product_to_search[1];
		$my_id_product_attribute = $id_product_to_search[2];		
		if($my_id_product > 0)
		{	
			var_dump($my_id_product);
			//on supprime si dejà splitté
			Db::getInstance()->execute('delete from  ' . _DB_PREFIX_ . 'order_detail WHERE `id_order` = '.$product['id_order'].' AND `product_id` = '.(int)$my_id_product);
			
			//var_dump($product['id_order_detail']);
			Db::getInstance()->execute('update ' . _DB_PREFIX_ . 'order_detail set product_id = '.(int)$my_id_product.' , product_attribute_id = '.(int)$my_id_product_attribute.' WHERE `id_order_detail` = '.$product['id_order_detail'].' AND `product_id` = '.(int)$product['product_id']);
			Db::getInstance()->execute('update ' . _DB_PREFIX_ . 'customization set id_product = '.(int)$my_id_product.' , id_product_attribute = '.(int)$my_id_product_attribute.' WHERE `id_product` = '.(int)$product['product_id']);
		}
	}

 ?>