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

class stripejsPaymentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
      parent::initContent();
      $stripe = Module::getInstanceByName('stripejs');

      if ($stripe && $stripe->active && Tools::getIsset('getPaymentVars') && Tools::getValue('getPaymentVars')==1) {
 			     die(json_encode($stripe->getTemplateVars()));

 			 }elseif ($stripe && $stripe->active && Tools::getIsset('stripeElement') && Tools::getValue('stripeElement')=='getPICS') {
          $stripe->getTemplateVars(true,'paymentElement');

      }elseif ($stripe && $stripe->active && Tools::getIsset('stripeToken') && Tools::getValue('stripeToken')!='') {

    			if(Tools::getIsset('cart_id')){
    			  $order_exists = (int)Order::getIdByCartId(Tools::getValue('cart_id'));
    			  if($order_exists>0)
    			  die(json_encode(array('code' => 1)));
    			}
          $params = array('token' => Tools::getValue('stripeToken'),'source_type' => Tools::getValue('sourceType'),);
          $stripe->processPayment($params);

      }elseif (Tools::getIsset('handlePIntent') && !empty(Tools::getValue('sourceType'))) {
  			return $stripe->getTemplateVars(true,Tools::getValue('sourceType'));
  		}else {
          die(json_encode(array(
                      'code' => '0',
                      'msg' => 'Empty token. Unknown error, please use another card or contact us.',
                  )));
        }
    }
}
