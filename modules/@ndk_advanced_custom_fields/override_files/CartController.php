<?php
/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*/

	class CartController extends CartControllerCore {
	
 	/**
      * This process add or update a product in the cart
      */
     protected function processChangeProductInCart()
     {
         $mode = (Tools::getIsset('update') && $this->id_product) ? 'update' : 'add';
 
         if ($this->qty == 0) {
             $this->errors[] = Tools::displayError('Null quantity.', !Tools::getValue('ajax'));
         } elseif (!$this->id_product) {
             $this->errors[] = Tools::displayError('Product not found', !Tools::getValue('ajax'));
         }
 
         $product = new Product($this->id_product, true, $this->context->language->id);
         if (!$product->id || !$product->active || !$product->checkAccess($this->context->cart->id_customer)) {
             $this->errors[] = Tools::displayError('This product is no longer available.', !Tools::getValue('ajax'));
             return;
         }
 
         $qty_to_check = $this->qty;
         $cart_products = $this->context->cart->getProducts();
         $adding_products = array();
         
         $adding_products[] = array('idProduct' => $this->id_product, 'idProductAttribute' => $this->id_product_attribute, 'quantity' => $this->qty);
         
         if(Pack::isPack((int)$this->id_product))
         {
         	$add_packItems = Db::getInstance()->executeS('SELECT id_product_item as idProduct, id_product_attribute_item as idProductAttribute, quantity FROM `'._DB_PREFIX_.'pack` where id_product_pack = '.(int)$this->id_product);
         	
         	foreach($add_packItems as $item)
         	{
         		$adding_products[] = array('idProduct' => $item['idProduct'], 'idProductAttribute' => $item['idProductAttribute'], 'quantity' => $item['quantity']*$this->qty);
         	}
         }
        
 
		 foreach($adding_products as $adding_product)
		 {
		         //var_dump($adding_product);
		         if(!$adding_product['idProductAttribute'] == '' )
		         	$adding_product['idProductAttribute'] = 0;
		         if (is_array($cart_products)) {

		             	//add by ndkdesign to check pack items
		             	foreach ($cart_products as $cart_product) {
		             		if( Pack::isPack((int)$cart_product['id_product']) ){
		             			$packItems = Db::getInstance()->executeS('SELECT id_product_item, id_product_attribute_item, quantity FROM `'._DB_PREFIX_.'pack` where id_product_pack = '.(int)$cart_product['id_product']);
		             			foreach ($packItems as $item) {
		             				if ((!isset($adding_product['idProductAttribute']) || $item['id_product_attribute_item'] == $adding_product['idProductAttribute']) &&
		             				    (isset($adding_product['idProduct']) && $item['id_product_item'] == $adding_product['idProduct'])) {
		             				            $qty_to_check += $item['quantity']*$cart_product['cart_quantity'];
		             				}
		             			}
		             			
		             		 }
		             		
			                 if ((!isset($adding_product['idProductAttribute']) || $cart_product['id_product_attribute'] == $adding_product['idProductAttribute']) &&
			                     (isset($adding_product['idProduct']) && $cart_product['id_product'] == $adding_product['idProduct'])) {
			                     $qty_to_check += $cart_product['cart_quantity'];
			 
			                     if (Tools::getValue('op', 'up') == 'down') {
			                         $qty_to_check -= $this->qty*$cart_product['cart_quantity'];
			                     }
			 
			                     break;
			                 }
		             }
		          }
		          
		          
		          // Check product quantity availability
		          if ($adding_product['idProductAttribute']) {
		              if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty($adding_product['idProductAttribute'], $qty_to_check)) {
		                  $this->errors[] = Tools::displayError('There isn\'t enough product in stock.', !Tools::getValue('ajax'));
		              }
		          } elseif ($product->hasAttributes()) {
		              $minimumQuantity = ($product->out_of_stock == 2) ? !Configuration::get('PS_ORDER_OUT_OF_STOCK') : !$product->out_of_stock;
		              $adding_product['idProductAttribute'] = Product::getDefaultAttribute($adding_product['idProduct'], $minimumQuantity);
		              // @todo do something better than a redirect admin !!
		              if (!$adding_product['idProductAttribute']) {
		                  Tools::redirectAdmin($this->context->link->getProductLink($product));
		              } elseif (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty($adding_product['idProductAttribute'], $qty_to_check)) {
		                  $this->errors[] = Tools::displayError('There isn\'t enough product in stock.', !Tools::getValue('ajax'));
		              }
		          } elseif (!$product->checkQty($qty_to_check)) {
		              $this->errors[] = Tools::displayError('There isn\'t enough product in stock.', !Tools::getValue('ajax'));
		          }
		          
		          
		    }
		 
		         
		 
		         // If no errors, process product addition
		         if (!$this->errors && $mode == 'add') {
		             // Add cart if no cart found
		             if (!$this->context->cart->id) {
		                 if (Context::getContext()->cookie->id_guest) {
		                     $guest = new Guest(Context::getContext()->cookie->id_guest);
		                     $this->context->cart->mobile_theme = $guest->mobile_theme;
		                 }
		                 $this->context->cart->add();
		                 if ($this->context->cart->id) {
		                     $this->context->cookie->id_cart = (int)$this->context->cart->id;
		                 }
		             }
		 
		             // Check customizable fields
		             if (!$product->hasAllRequiredCustomizableFields() && !$this->customization_id) {
		                 $this->errors[] = Tools::displayError('Please fill in all of the required fields, and then save your customizations.', !Tools::getValue('ajax'));
		             }
		 
		             if (!$this->errors) {
		                 $cart_rules = $this->context->cart->getCartRules();
		                 $available_cart_rules = CartRule::getCustomerCartRules($this->context->language->id, (isset($this->context->customer->id) ? $this->context->customer->id : 0), true, true, true, $this->context->cart, false, true);
		                 $update_quantity = $this->context->cart->updateQty($this->qty, $this->id_product, $this->id_product_attribute, $this->customization_id, Tools::getValue('op', 'up'), $this->id_address_delivery);
		                 if ($update_quantity < 0) {
		                     // If product has attribute, minimal quantity is set with minimal quantity of attribute
		                     $minimal_quantity = ($this->id_product_attribute) ? Attribute::getAttributeMinimalQty($this->id_product_attribute) : $product->minimal_quantity;
		                     $this->errors[] = sprintf(Tools::displayError('You must add %d minimum quantity', !Tools::getValue('ajax')), $minimal_quantity);
		                 } elseif (!$update_quantity) {
		                     $this->errors[] = Tools::displayError('You already have the maximum quantity available for this product.', !Tools::getValue('ajax'));
		                 } elseif ((int)Tools::getValue('allow_refresh')) {
		                     // If the cart rules has changed, we need to refresh the whole cart
		                     $cart_rules2 = $this->context->cart->getCartRules();
		                     if (count($cart_rules2) != count($cart_rules)) {
		                         $this->ajax_refresh = true;
		                     } elseif (count($cart_rules2)) {
		                         $rule_list = array();
		                         foreach ($cart_rules2 as $rule) {
		                             $rule_list[] = $rule['id_cart_rule'];
		                         }
		                         foreach ($cart_rules as $rule) {
		                             if (!in_array($rule['id_cart_rule'], $rule_list)) {
		                                 $this->ajax_refresh = true;
		                                 break;
		                             }
		                         }
		                     } else {
		                         $available_cart_rules2 = CartRule::getCustomerCartRules($this->context->language->id, (isset($this->context->customer->id) ? $this->context->customer->id : 0), true, true, true, $this->context->cart, false, true);
		                         if (count($available_cart_rules2) != count($available_cart_rules)) {
		                             $this->ajax_refresh = true;
		                         } elseif (count($available_cart_rules2)) {
		                             $rule_list = array();
		                             foreach ($available_cart_rules2 as $rule) {
		                                 $rule_list[] = $rule['id_cart_rule'];
		                             }
		                             foreach ($cart_rules2 as $rule) {
		                                 if (!in_array($rule['id_cart_rule'], $rule_list)) {
		                                     $this->ajax_refresh = true;
		                                     break;
		                                 }
		                             }
		                         }
		                     }
		                 }
		             }
		         }
		 
		         $removed = CartRule::autoRemoveFromCart();
		         CartRule::autoAddToCart();
		         if (count($removed) && (int)Tools::getValue('allow_refresh')) {
		             $this->ajax_refresh = true;
		         }
		     }
 
 }
 ?>