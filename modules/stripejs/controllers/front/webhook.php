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

class stripejsWebhookModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
      if(!$this->module->active)
      return;
        // Retrieve the request's body and parse it as JSON
        $input = Tools::file_get_contents("php://input");
        $event_json = json_decode($input);
        try {
            \Stripe\Event::retrieve($event_json->id);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
        http_response_code(200);

    if ($event_json) {

			$data = $event_json->data->object;
			$this->context = Context::getContext();

			if (in_array($event_json->type,array("checkout.session.completed","payment_intent.amount_capturable_updated","payment_intent.succeeded","payment_intent.processing"))) {

        if($event_json->type!="payment_intent.processing")
          @Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'stripejs_transaction SET status="'.($data->status=='succeeded' ? 'paid' : 'uncaptured').'" where id_cart = "'.pSQL($data->metadata->id_cart).'"');

        $conveyor = array(); $result_json = NULL;
        if($event_json->type=="checkout.session.completed" && empty($data->payment_intent))
          return;
        if($event_json->type=="checkout.session.completed"){
          $payment = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripejs_transaction WHERE `source`="'.pSQL($data->id).'"');
          $data = \Stripe\PaymentIntent::retrieve($data->payment_intent);
          $conveyor['id_payment_intent'] = $data->id;
          $conveyor['client_secret'] = $data->client_secret;
          $conveyor['source_type'] = $payment['source_type'];
        } elseif(isset($data->metadata->id_cart)) {
          $payment = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripejs_transaction WHERE `id_cart` = "'.pSQL($data->metadata->id_cart).'"');
        }
        if(empty($payment['id_stripe_transaction']))
          die('Nothing to do!!');

			  if($event_json->type!="checkout.session.completed"){

           if(isset($data->latest_charge) && !empty($data->latest_charge))
             $result_json = \Stripe\Charge::retrieve($data->latest_charge);
           else
             $result_json = $data->charges->data[0];
           $conveyor['source_type'] = $result_json->payment_method_details->type;
           $conveyor['id_transaction'] = (isset($result_json->id)?$result_json->id:'');

           if ($payment['source_type']=='multibanco' && $event_json->type=='payment_intent.processing')
              die('Nothing to do!!');

           if((in_array($result_json->payment_method_details->type,array('card','link','prbutton')) && in_array(Configuration::get('STRIPE_ALLOW_CARDS'),array(1,3,0))) || in_array($result_json->payment_method_details->type,array('sepa_debit','wechat_pay','prbutton','paynow','promptpay'))){
             sleep(15);
           }
           /////Delay webhook To avoid duplicate order from onsite wallet payments///////////
           if($result_json->payment_method_details->type=='card' && Configuration::get('STRIPE_ALLOW_CARDS')==2 && isset($result_json->payment_method_details->card->wallet) && !empty($result_json->payment_method_details->card->wallet)){
             sleep(7);
           }
         }

         $conveyor['token'] = $data->id;
         $conveyor['cart_id'] = $payment['id_cart'];

			   if ($payment['id_order']>0) {

				  $order = new Order($payment['id_order']);
				  if ($order->getCurrentState() != Configuration::get('STRIPE_PAYMENT_ORDER_STATUS')) {

					  if((int)$payment['card_reuse']==1 && $payment['source_type']=='checkout') {
					    $conveyor['customer_id'] = $payment['id_customer'];
					    $new_cus = $this->module->processStripeCustomer($conveyor,$data);
				        $conveyor['customer_stripe_id'] = (!is_array($new_cus) && Tools::substr($new_cus,0,4)=='cus_'?$new_cus:NULL);
					  }
					  $this->module->handleUpdateTransaction($conveyor, $result_json);
					  $order->setCurrentState((int)Configuration::get('STRIPE_PAYMENT_ORDER_STATUS'));
					  Db::getInstance()->update('order_payment',array('transaction_id'=>$conveyor['id_transaction']),'order_reference="'.$order->reference.'"');
				  }
			  } else {

            $load_cart = new Cart($payment['id_cart']);
            $id_order = (int)Order::getIdByCartId($conveyor['cart_id']);
            if($load_cart->orderExists() || $id_order>0)
            die('Nothing to do!!');

  				  $this->context->cart = $load_cart;
            $addressDelivery = new Address($this->context->cart->id_address_delivery);
            $this->context->language = new Language((int) $this->context->cart->id_lang);
            $this->context->customer = new Customer((int) $this->context->cart->id_customer);
            $this->context->currency = new Currency((int) $this->context->cart->id_currency);
            $this->context->shop = new Shop((int) $this->context->cart->id_shop);
            Shop::setContext(Shop::CONTEXT_SHOP,$this->context->cart->id_shop);
            $this->context->country = new Country($addressDelivery->id_country);
  				  $this->module->processPayment($conveyor, $data);
        }
      }

			if (in_array($event_json->type,array("payment_intent.canceled","payment_intent.payment_failed","charge.dispute.created"))) {
        $wherer = ($event_json->type=="charge.dispute.created"?'id_transaction':'id_payment_intent');
				@Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'stripejs_transaction SET status="failed" where '.$wherer.' = "'.pSQL($data->id).'"');
				$payment = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripejs_transaction WHERE '.$wherer.' = "'.pSQL($data->id).'"');

				if ($payment['id_order']>0) {
					$order = new Order($payment['id_order']);
					if ($order->getCurrentState() != Configuration::get('STRIPE_CHARGEBACKS_ORDER_STATUS'))
						$order->setCurrentState((int)Configuration::get('STRIPE_CHARGEBACKS_ORDER_STATUS'));
					}
			}

      die('ok');
    }
  }
}
