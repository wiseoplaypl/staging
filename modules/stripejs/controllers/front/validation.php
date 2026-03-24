<?php
/**
* 2007-2025 PrestaShop
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
 * @author    NTS <nexustotalsolutions@gmail.com>
 * @copyright Copyright (c) NTS
 * @license   Commercial license
 */

if (!defined('_PS_VERSION_')) { exit; }

class stripejsValidationModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->ssl = true;
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
      parent::initContent();

  		$this->context = Context::getContext();
      $conveyor['source_type'] = $intent = NULL;
  		$domain = Tools::getShopDomainSsl(true,true);
      $order_page = $this->context->link->getPageLink('order', true);
  		$history_page = $this->context->link->getPageLink('history', true);
  		$conveyor = array();

      if(!$this->context->cookie->logged){
          return die('<h2>'.$this->module->l('Your transaction will be validated soon.').'</h2>');
      }

  		if(Tools::getIsset('stripe_checkout') && Tools::getValue('stripe_checkout')=='failed') {
  			  Tools::redirect($order_page);

        } elseif(Tools::getIsset('payment_intent') && Tools::getIsset('redirect_status') && Tools::getValue('redirect_status')=='failed'){

          $intent = \Stripe\PaymentIntent::retrieve(Tools::getValue('payment_intent'));
          $pi_error = (!empty($intent->last_payment_error)?$intent->last_payment_error->message:(isset($intent->error)?$intent->error->message:''));
          Tools::redirect($this->context->link->getPageLink('order', true,null,array('stripe_error'=>$pi_error)));
        } else {

          if(Tools::getIsset('payment_intent')){
            $conveyor['cart_id'] = Db::getInstance()->getValue('SELECT id_cart FROM '._DB_PREFIX_.'stripejs_transaction WHERE id_payment_intent = "'.pSQL(Tools::getValue('payment_intent')).'" AND type = "payment"');
            $conveyor['token'] = Tools::getValue('payment_intent');
          } elseif(!empty($this->context->cart->id)) {
            $conveyor['cart_id'] = $this->context->cart->id;
            $conveyor['token'] = Db::getInstance()->getValue('SELECT id_payment_intent FROM '._DB_PREFIX_.'stripejs_transaction WHERE id_cart = '.(int)$conveyor['cart_id'].' AND type = "payment"');
          } elseif(Tools::getIsset('cid')) {
            $conveyor['cart_id'] = (int)Tools::getValue('cid');
              $conveyor['token'] = Db::getInstance()->getValue('SELECT id_payment_intent FROM '._DB_PREFIX_.'stripejs_transaction WHERE id_cart = '.(int)Tools::getValue('cid').' AND type = "payment"');
          }

          $id_order = (int)Order::getIdByCartId((int)$conveyor['cart_id']);

          if($id_order>0) {
            $order = new Order($id_order);
            $url = $this->context->link->getPageLink('order-confirmation', true).'?id_cart='.(int)$order->id_cart.'&id_module='.(int)$this->module->id.'&id_order='.$id_order.'&key='.$order->secure_key;
            Tools::redirect($url);
          } else {

            $this->setTemplate('module:stripejs/views/templates/hook/payment_validation.tpl');
            if(Tools::getValue('attempt')<4){
              $arr_content = array('content_only'=>1,'attempt'=>(int)Tools::getValue('attempt')+1,'cid'=>(int)$conveyor['cart_id']);
              $this->context->smarty->assign(array(
                  'reload' => 1,
      			      'reloadURL' => $this->context->link->getModuleLink($this->module->name, 'validation', $arr_content, true),
              ));
            } else {

              if(empty($conveyor['token']) && Configuration::get('STRIPE_ALLOW_CARDS')==2){
                $payment = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripejs_transaction WHERE id_cart = '.(int)$conveyor['cart_id'].' AND type = "payment"');
                $checkout_session = \Stripe\Checkout\Session::retrieve($payment['source']);
                $conveyor['token'] = $checkout_session->payment_intent;
                $intent = \Stripe\PaymentIntent::retrieve($checkout_session->payment_intent);
                $conveyor['id_payment_intent'] = $intent->id;
                $conveyor['client_secret'] = $intent->client_secret;
                $conveyor['source_type'] = $payment['source_type'];
              }
              $this->module->processPayment($conveyor, $intent);
           }

          }
    		}
    }
    public function setMedia($isNewTheme = false)
    {
        $this->context->controller->registerStylesheet($this->module->name.'-frontcss', 'modules/'.$this->module->name.'/views/css/stripe-prestashop.css');
        return true;
    }
}
