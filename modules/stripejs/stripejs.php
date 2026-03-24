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

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_'))
    exit;

class StripeJs extends PaymentModule
{

    public static $webhook_events = array(
        "checkout.session.completed",
        "payment_intent.amount_capturable_updated",
        "payment_intent.succeeded",
        "payment_intent.processing",
        "payment_intent.canceled",
        "payment_intent.payment_failed",
        'charge.dispute.created',
    );

    public function __construct()
    {
        $this->name = 'stripejs';
        $this->tab = 'payments_gateways';
        $this->version = '4.8.1';
        $this->author = 'NTS';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->module_key = 'df64f54a4d1bec8b516d34ab302fa648';
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        parent::__construct();

        $this->displayName = $this->l('Stripe Payment Pro');
        $this->description = $this->l('Accept payments with all the payment options available in your Stripe account.');
        $this->confirmUninstall = $this->l('Warning: all the Stripe customers cards token and transaction details saved in your database will be deleted. Are you sure you want uninstall this module?');

        $controller = Tools::getValue('controller');
        if(!isset($this->context->customer) || (isset($this->context->customer) && in_array($controller,array("order", 'paymentcard','supercheckout','default','payment','webhook','managecards','validation')))) {
          if(!class_exists('Stripe\Stripe'))
             include_once(dirname(__FILE__).'/vendor/init.php');
          if($this->checkSettings()) {
            $version = $this->version . '_' . _PS_VERSION_ . '_' . phpversion();
            \Stripe\Stripe::setApiKey(Configuration::get('STRIPE_MODES') ? Configuration::get('STRIPE_PRIVATE_KEY_LIVE') : Configuration::get('STRIPE_PRIVATE_KEY_TEST'));
            \Stripe\Stripe::setAppInfo("Stripe Payment Prestashop",$version,"https://addons.prestashop.com/en/payment-card-wallet/17856-stripe-payment-pro-sca-ready.html","pp_partner_FddKQmLagFkk1X");
          }
        }
    }

    /**
     * Stripe's module installation
     *
     * @return boolean Install result
     */
    public function install()
    {
         if (Shop::isFeatureActive())
           Shop::setContext(Shop::CONTEXT_ALL);

        include_once(dirname(__FILE__).'/classes/StripejsInstall.php');
        $Stripejsinstaller =  new StripejsInstall();

        $ret = parent::install()
        && $this->registerHook('displayHeader')
        && $this->registerHook('displayBackOfficeHeader')
        && $this->registerHook('displaycustomerAccount')
        && $this->registerHook('paymentOptions')
        //&& $this->registerHook('actionOrderStatusPostUpdate')
        && $this->registerHook('actionEmailSendBefore')
		    && $this->registerHook('displayOrderConfirmation')
        && $Stripejsinstaller->installDb()
        && $Stripejsinstaller->createOS();

        Configuration::updateValue('STRIPE_ALLOW_CARDS', 3);
        Configuration::updateValue('STRIPE_ALLOW_USEDCARD', 1);
        Configuration::updateValue('STRIPE_CAPTURE_TYPE', 1);
        Configuration::updateValue('STRIPE_MODES', 0);
        Configuration::updateValue('STRIPE_PAYMENT_ORDER_STATUS', (int)Configuration::get('PS_OS_PAYMENT'));
		    Configuration::updateValue('STRIPE_REFUND_ORDER_STATUS', (int)Configuration::get('PS_OS_REFUND'));
        Configuration::updateValue('STRIPE_CHARGEBACKS_ORDER_STATUS', (int)Configuration::get('PS_OS_ERROR'));

        return $ret;
    }

    public function uninstall()
    {
        @Db::getInstance()->Execute("DROP TABLE IF EXISTS `"._DB_PREFIX_."stripejs_transaction`");

        @Configuration::deleteByName('STRIPE_MODES');
        @Configuration::deleteByName('STRIPE_PUBLIC_KEY_TEST');
        @Configuration::deleteByName('STRIPE_PUBLIC_KEY_LIVE');
        @Configuration::deleteByName('STRIPE_PRIVATE_KEY_TEST');
        @Configuration::deleteByName('STRIPE_PRIVATE_KEY_LIVE');
        @Configuration::deleteByName('STRIPE_CAPTURE_TYPE');
        @Configuration::deleteByName('STRIPE_ALLOW_CARDS');
        @Configuration::deleteByName('STRIPE_ALLOW_USEDCARD');
        @Configuration::deleteByName('STRIPE_CHARGE_ORDERID');
        @Configuration::deleteByName('STRIPE_PAYMENT_METHODS_OPC');
        @Configuration::deleteByName('STRIPE_PETheme');
        @Configuration::deleteByName('STRIPE_Checkout_Open');
        @Configuration::deleteByName('STRIPE_PAYMENT_RECEIPT');

        @Configuration::deleteByName('STRIPEJS_WEBHOOK_SIG_1');
        @Configuration::deleteByName('STRIPEJS_WEBHOOK_SIG_0');

        @Configuration::deleteByName('STRIPE_PAYMENT_ORDER_STATUS');
        @Configuration::deleteByName('STRIPE_CHARGEBACKS_ORDER_STATUS');
        @Configuration::deleteByName('STRIPE_REFUND_ORDER_STATUS');
        @Configuration::deleteByName('STRIPE_PARTIALLY_REFUNDED_OS');
        @Configuration::deleteByName('STRIPE_AWAITING_OS');

        @Configuration::deleteByName('STRIPE_ALLOW_DELETE_CARDS');
        @Configuration::deleteByName('STRIPE_ALLOW_ZIP');
        @Configuration::deleteByName('STRIPE_ALLOW_PRBUTTON');
        @Configuration::deleteByName('STRIPE_ALLOW_ALIPAY');
        @Configuration::deleteByName('STRIPE_ALLOW_SEPA');
        @Configuration::deleteByName('STRIPE_ALLOW_IDEAL');
        @Configuration::deleteByName('STRIPE_ALLOW_KLARNA');
        @Configuration::deleteByName('STRIPE_ALLOW_GIROPAY');
        @Configuration::deleteByName('STRIPE_ALLOW_BANCONTACT');
        @Configuration::deleteByName('STRIPE_ALLOW_SOFORT');
        @Configuration::deleteByName('STRIPE_ALLOW_P24');
        @Configuration::deleteByName('STRIPE_ALLOW_EPS');
        @Configuration::deleteByName('STRIPE_ALLOW_WECHAT');
        @Configuration::deleteByName('STRIPE_ALLOW_MULTIBANCO');
        @Configuration::deleteByName('STRIPE_ALLOW_GRABPAY');
        @Configuration::deleteByName('STRIPE_ALLOW_OXXO');
        @Configuration::deleteByName('STRIPE_STMNT_DESC');
        @Configuration::deleteByName('STRIPE_ALLOW_AFFIRM');
        @Configuration::deleteByName('STRIPE_ALLOW_AFTERPAY_CLEARPAY');
        @Configuration::deleteByName('STRIPE_ALLOW_FPX');
        @Configuration::deleteByName('STRIPE_ALLOW_PAYNOW');

        return parent::uninstall();
    }

    /**
     * Load Javascripts and CSS related to the Stripe's module
     * Only loaded during the checkout process
     *
     * @return string HTML/JS Content
     */
    public function hookDisplayHeader()
    {
        $controller = Tools::getValue('controller');
        if (in_array($controller,array("order", 'paymentcard','supercheckout','default'))) {
            $is_opc_module  = $this->checkOPCModules();
            $nbProducts = $this->context->cart->nbProducts();
            if($nbProducts<=0 || (!$is_opc_module && !$this->context->cookie->logged))
              return '';

            $this->context->controller->registerStylesheet($this->name.'-frontcss', '/modules/'.$this->name.'/views/css/stripe-prestashop.css');
            $this->context->controller->registerJavascript($this->name.'-stipeV3', 'https://js.stripe.com/v3/', array('server'=>'remote'));
            $this->context->controller->registerJavascript($this->name.'-paymentjs', '/modules/'.$this->name.'/views/js/stripe-prestashop.js');

            $this->context->smarty->assign($this->getTemplateVars());
            return $this->display(__FILE__, './views/templates/hook/payment_vars.tpl');
        }
        return '';
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active || !$this->checkSettings()) {
            return;
        }
        $payment_options = array();
        $o_ttl = $this->context->cart->getOrderTotal();
        if($o_ttl<0.5) {

        $amt_warning = $this->l('Cart total amount should be greater or equal to 50 cents to pay with cards.');
        $embeddedOption = new PaymentOption();
        $embeddedOption->setModuleName($this->name)
                       ->setCallToActionText($this->l('Pay with Credit / Debit Card'))
                       ->setAdditionalInformation($amt_warning)
                       ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/powered_by_stripe.png'));
        $payment_options[] = $embeddedOption;
        return $payment_options;
        }

        $controller     = Tools::getValue('controller');
        $is_opc_module  = $this->checkOPCModules();

        $pm_text =  $this->l('Pay by Card / Wallets / Bank Transfer / Local payment methods');

        if (in_array($controller,array('order','supercheckout','ets_onepagecheckout','onepagecheckout','onepagecheckoutps','thecheckout')) && $is_opc_module && !Configuration::get('STRIPE_PAYMENT_METHODS_OPC')) {

                $embeddedOption = new PaymentOption();
                $embeddedOption->setModuleName($this->name)
                               ->setCallToActionText($pm_text)
                               ->setAction($this->context->link->getModuleLink('stripejs', 'paymentcard'))
                               ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/powered_by_stripe.png'));
                $payment_options[]=$embeddedOption;
                return $payment_options;
          }
       if(Configuration::get('STRIPE_ALLOW_CARDS')>0){

          $form = (Configuration::get('STRIPE_ALLOW_CARDS')==3?'elements.tpl':'card-pay.tpl');
          $action = ($is_opc_module && !$this->context->cookie->logged && Configuration::get('STRIPE_ALLOW_CARDS')!=2 ?"javascript:submitStripePayment(event,'OPC');":"javascript:submitStripePayment(event,'card');");

          $embeddedOption = new PaymentOption();
          $embeddedOption->setModuleName($this->name)
                         ->setCallToActionText($pm_text)
                         ->setAction($action)
                         ->setAdditionalInformation($this->display(__FILE__,'views/templates/hook/'.$form))
                         ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/powered_by_stripe.png'));
          $payment_options[] = $embeddedOption;
        }

        $address_delivery = new Address($this->context->cart->id_address_invoice);
        $country = Country::getIsoById($address_delivery->id_country);
        $pr_countries = array('AE', 'AT', 'AU', 'BE', 'BG', 'BR', 'CA', 'CH', 'CI', 'CR', 'CY', 'CZ', 'DE', 'DK', 'DO', 'EE', 'ES', 'FI', 'FR', 'GB', 'GI', 'GR', 'GT', 'HK', 'HU', 'ID', 'IE', 'IN', 'IT', 'JP', 'LI', 'LT', 'LU', 'LV', 'MT', 'MX', 'MY', 'NL', 'NO', 'NZ', 'PE', 'PH', 'PL', 'PT', 'RO', 'SE', 'SG', 'SI', 'SK', 'SN', 'TH', 'TT', 'US', 'UY');

        if(Configuration::get('STRIPE_ALLOW_CARDS')!=3){

           if(Configuration::get('STRIPE_ALLOW_SEPA') && $this->context->currency->iso_code == "EUR"){
              $payment_option = new PaymentOption();
              $payment_option->setModuleName('stripeSepa')->setCallToActionText($this->l('Pay by SEPA Direct Debit'))
                             ->setAction("javascript:submitStripePayment(event,'sepa');")
                             ->setAdditionalInformation($this->display(__FILE__,'views/templates/hook/sepa.tpl'))
                             ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/cc-sepa.png'));
              $payment_options[] = $payment_option;
           }

            $address_invoice = new Address($this->context->cart->id_address_invoice);
            $id_zone = Address::getZoneById($this->context->cart->id_address_invoice);
            $iso_country = Country::getIsoById($address_invoice->id_country);
            $this->l('Pay by ALIPAY');$this->l('Pay by P24 (Przelewy24)');$this->l('Pay by EPS');$this->l('Pay by GIROPAY');$this->l('Pay by FPX');
            $this->l('Pay by GrabPay');$this->l('Pay by IDEAL');$this->l('Pay by MULTIBANCO');$this->l('Pay by BANCONTACT');$this->l('Pay by KLARNA');$this->l('Pay by Konbini');$this->l('Pay by Boleto');
            $this->l('Pay by SOFORT');$this->l('Pay by OXXO');$this->l('Pay by Afterpay/ Clearpay');
            $this->l('Pay by Clearpay');$this->l('Pay by Afterpay');$this->l('Pay by Affirm');
            $pay_later = $this->l('(Buy now, Pay later)');
            $methods = array('alipay','bancontact','giropay','ideal','sofort','p24','eps','fpx','grabpay','oxxo','multibanco','klarna','afterpay_clearpay','checkout','affirm');
            foreach ($methods as $method) {

              if(!$this->isPaymentMethodAllowed($method,$iso_country,$this->context->currency->iso_code,$id_zone))
                  continue;

                $method_img = ($method=="afterpay_clearpay"?($id_zone!=1?'afterpay':'clearpay'):$method).'.png';

                $pm_text = $this->l('Pay by '.Tools::strtoupper(str_replace('_','/ ',$method)).($method=='p24'?' (Przelewy24)':''));
                $pm_text = (in_array($method,array('klarna','affirm','afterpay_clearpay'))?$pm_text.' '.$pay_later:$pm_text);

                $payment_option = new PaymentOption();
                $payment_option->setModuleName('Stripe'.$method)->setCallToActionText($pm_text)
                ->setAction("javascript:submitStripePayment(event,'".$method."');")
                ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/cc-'.$method_img));
                if(in_array($method,array('ideal', 'fpx')))
                 $payment_option->setAdditionalInformation($this->display(__FILE__,'views/templates/hook/'.$method.'.tpl'));
                if($method=='affirm' && $o_ttl<50)
                 $payment_option->setAdditionalInformation($this->l('Minimum order amount is 50USD to use Affirm.'));

                $payment_options[] = $payment_option;
            }
            if(Configuration::get('STRIPE_ALLOW_WECHAT') && in_array($this->context->currency->iso_code, array('CNY', 'AUD','CAD','EUR','GBP','HKD','JPY','SGD','USD', 'DKK', 'NOK', 'SEK', 'CHF'))){
              $embeddedOption = new PaymentOption();
              $embeddedOption->setModuleName('stripeWechat')->setCallToActionText($this->l('WeChat Pay'))
                             ->setAction("javascript:submitStripePayment(event,'wechat_pay');")
                             ->setAdditionalInformation($this->display(__FILE__,'views/templates/hook/wechat-pay.tpl'))
                             ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/wechat.png'));
              $payment_options[] = $embeddedOption;
           }

           if(Configuration::get('STRIPE_ALLOW_PAYNOW') && $this->isPaymentMethodAllowed('paynow',$iso_country,$this->context->currency->iso_code,$id_zone)){
             $embeddedOption = new PaymentOption();
             $embeddedOption->setModuleName('stripePaynow')->setCallToActionText($this->l('PayNow'))
                            ->setAdditionalInformation($this->display(__FILE__,'views/templates/hook/paynow.tpl'))
                            ->setAction("javascript:submitStripePayment(event,'paynow');")
                            ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/paynow.png'));
             $payment_options[] = $embeddedOption;
          }
         }

            if(Configuration::get('STRIPE_ALLOW_PRBUTTON') && in_array($country, $pr_countries)){
              $embeddedOption = new PaymentOption();
              $embeddedOption->setModuleName('stripePRButton')->setCallToActionText($this->l('Pay with Google/ Apple Pay/ Link'))
                             ->setAdditionalInformation($this->display(__FILE__,'views/templates/hook/pr-button.tpl'))
                             ->setAction("javascript:submitStripePayment(event,'prbutton');")
                             ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/gpay_apay.png'));
              $payment_options[] = $embeddedOption;
           }
        return $payment_options;
    }

    public function isPaymentMethodAllowed($method, $iso_country, $currency, $zone)
    {
        if (!Configuration::get('STRIPE_ALLOW_'.Tools::strtoupper($method)))
           return false;
        if ($method == 'sofort' && $currency != "EUR")
           return false;
        if ($method == 'alipay' && (($currency=='AUD' && $iso_country!='AU') || ($currency=='CAD' && $iso_country!='CA') || ($currency=='GBP' && $iso_country!='GB') || ($currency=='HKD' && $iso_country!='HK') || ($currency=='JPY' && $iso_country!='JP') || ($currency=='NZD' && $iso_country!='NZ') || ($currency=='SGD' && $iso_country!='SG') || ($currency=='MYR' && $iso_country!='MY') || ($currency=='USD' && $iso_country!='US') || ($currency=='EUR' && $zone!=1)))
           return false;
        if ($method == 'klarna' && (!in_array($iso_country, array('AU','AT','BE','CA','CH','CZ','DK','EE','ES','FI','FR','DE','IE','IT','LT','LV','NL','NZ','NO','PL','PT','SE','GR','GB','SK','SI','US')) || !in_array($currency, array('AUD','CAD','CHF','CZK','DKK', 'NOK', 'EUR', 'GBP','NZD','PLN','SEK','USD'))))
           return false;
        if ($method == 'p24' && ($iso_country!='PL' || !in_array($currency, array('EUR','PLN'))))
           return false;
        if ($method == 'giropay' && ($iso_country!='DE' || $currency != "EUR"))
           return false;
        if ($method == 'ideal' && ($iso_country!='NL' || $currency != "EUR"))
           return false;
        if ($method == 'bancontact' && ($iso_country!='BE' || $currency != "EUR"))
           return false;
        if ($method == 'eps' && ($iso_country!='AT' || $currency != "EUR"))
           return false;
        if ($method == 'boleto' && ($iso_country!='BR' || $currency != "BRL"))
           return false;
        if ($method == 'konbini' && ($iso_country!='JP' || $currency != "JPY"))
             return false;
        if ($method == 'fpx' && ($iso_country!='MY' || $currency != "MYR"))
           return false;
        if ($method == 'grabpay' && (!in_array($iso_country, array('MY','SG')) || !in_array($currency, array('SGD','MYR'))))
           return false;
        if ($method == 'oxxo' && ($iso_country!='MX' || $currency != "MXN"))
           return false;
        if ($method == 'multibanco' && ($iso_country!='PT' || $currency != "EUR"))
              return false;
        if ($method == 'affirm' && ($iso_country!='US' || $currency != "USD"))
              return false;
        if ($method == 'paynow' && ($iso_country!='SG' || $currency != "SGD"))
              return false;
        if ($method == 'afterpay_clearpay' && (!in_array($iso_country, array('AU','CA','ES','FR','NZ','GB','US')) || !in_array($currency, array('CAD', 'AUD', 'EUR', 'GBP', 'NZD','USD'))))
                 return false;

        return true;
    }

    public function getLineItemArray($p_name,$qty,$amount,$currency,$img_url)
    {
        return array('quantity'=>$qty,'price_data'=>array(
          'currency'=>$currency,
          'unit_amount' => $amount,
          'product_data'=>array('name'=>$p_name,'images'=>array($img_url)),
        ));
    }

    public function handleCheckoutSession($amount,$currency,$cus_id,$logo_url,$base_uri)
    {
       $conveyor = array('source_type' => 'checkout',
        'cart_id' => (int)$this->context->cart->id,
        'customer_id' => (int)$this->context->cart->id_customer,
        'customer_stripe_id' => $cus_id,
        'amount' => $amount, 'currency'=>$currency,
        'card_reuse' => Tools::getValue('card_reuse'),
        'STRIPE_MODES' => (Configuration::get('STRIPE_MODES') ? 'live' : 'test'));
        $session_items = array();
        $discounts = $this->context->cart->getOrderTotal(true,Cart::ONLY_DISCOUNTS);
        $products = $this->context->cart->getProducts();
        $nbProducts = $this->context->cart->nbProducts();
        $image_type = ($nbProducts>1?'home':'large');
        $lines_ttl = 0;
        $ship_img = $base_uri.$this->_path.'views/img/shipping.png';
        $card_reuse = ((int)Tools::getValue('card_reuse')==1?'on_session':NULL);
        foreach($products as $pr){
          $line_total =0;
          $p = new Product((int)$pr['id_product'], false, $this->context->language->id);
          if(!empty($pr['id_product_attribute']))
          $img = Product::getCombinationImageById((int)$pr['id_product_attribute'], $this->context->language->id);
          else
          $img = $p->getCover($pr['id_product']);
          $p_name = (is_array($p->name)?$p->name[$this->context->language->id]:$p->name);
          $img_url = $this->context->link->getImageLink(str_replace('%2F', '/', urlencode($p_name)), (int)$img['id_image'], ImageType::getFormattedName($image_type));

          if($nbProducts==1) {
            $line_total = $amount;
            $session_items[] = $this->getLineItemArray($p_name,1,$line_total,$currency,$img_url);

          }elseif($discounts<=0) {
            $line_total = ($this->isZeroDecimalCurrency($currency) ? round($pr['total_wt']) : round($pr['total_wt'],2)*100);
            $session_items[] = $this->getLineItemArray($p_name,1,$line_total,$currency,$img_url);
          }
          $lines_ttl+=$line_total;
          $lines_ttl = (string)$lines_ttl;
        }
        if($nbProducts>1 && $discounts<=0){
            $ship_cost = $this->context->cart->getTotalShippingCost();
            $ship_cost = ($this->isZeroDecimalCurrency($currency) ? round($ship_cost) : round($ship_cost*100,2));
            $lines_ttl+=$ship_cost;
            if($amount>$lines_ttl){
              $ship_cost+= $amount-$lines_ttl;
            }
            if($ship_cost>0)
            $session_items[] = $this->getLineItemArray($this->l('Shipping'),1,$ship_cost,$currency,$ship_img);

         }elseif($discounts>0 && $nbProducts>1) {
           $final_arr= $this->getLineItemArray($this->l('Shopping cart ID:').$this->context->cart->id,1,$amount,$currency,$logo_url);
           $session_items = array($final_arr);
        }
        $checkout_locales = array('bg','cs','da','de','el','en','es','et','fi','fr','he','hr','hu','id','it','ja','ko','lt','lv','ms','mt','nb','nl','pl','pt','ro','ru','sk','sl','sv','th','tr','vi','zh');
        $address_delivery = new Address($this->context->cart->id_address_delivery);
        $country = Country::getIsoById($address_delivery->id_country);
        $state = (!empty($address_delivery->id_state)?State::getNameById($address_delivery->id_state):0);
        $shipping_address = array('name'=>$address_delivery->firstname.' '.$address_delivery->lastname,
                            'address'=>array('line1' => $address_delivery->address1,
                                             'line2' => $address_delivery->address2,
                                             'city' => $address_delivery->city,
                                             'state' => $state,
                                             'country' => $country,
                                             'postal_code' => $address_delivery->postcode));
        $session_arr = array('mode' => 'payment',
            'ui_mode' => (Configuration::get('STRIPE_Checkout_Open')?'embedded':'hosted'),
            'payment_intent_data' => array('setup_future_usage' => $card_reuse, 'capture_method' => (!Configuration::get('STRIPE_CAPTURE_TYPE')?'manual':'automatic'),
				'description'=>$this->context->shop->name.' - '.$this->l('Customer #').$this->context->customer->id.' - '.$this->l('Cart #').$this->context->cart->id, 'shipping' => $shipping_address,
				'receipt_email' => (Configuration::get('STRIPE_PAYMENT_RECEIPT')?$this->context->customer->email:NULL)),
            'expand' => array('payment_intent'),
            'locale' => (in_array($this->context->language->iso_code,$checkout_locales)?$this->context->language->iso_code:'auto'),
            //'payment_method_types' => array('card'),
            'line_items' => $session_items,
          );
        if((int)Tools::getValue('card_reuse')==1){
          $session_arr['payment_method_types'] = array('card');
        }
        if(Configuration::get('STRIPE_Checkout_Open')){
          $session_arr['return_url'] = $this->context->link->getModuleLink($this->name, 'validation', array('content_only'=>1, 'stripe_checkout'=>'success','cid' => (int)$this->context->cart->id), true);
        } else{
          $session_arr['success_url'] = $this->context->link->getModuleLink($this->name, 'validation', array('content_only'=>1, 'stripe_checkout'=>'success','cid' => (int)$this->context->cart->id), true);
          $session_arr['cancel_url'] = $this->context->link->getModuleLink($this->name, 'validation', array('content_only'=>1, 'stripe_checkout'=>'failed'), true);
        }

        if(Tools::substr($cus_id,0,4)=='cus_') {
            $session_arr['customer'] = $cus_id;
        } else {

           if(Configuration::get('STRIPE_CUSTOMER_INFO')) {
             $cus_id = $this->createStripeCustomer($conveyor);
            if(Tools::substr($cus_id,0,4)=='cus_')
              $session_arr['customer'] = $cus_id;
            }
            if(Tools::substr($cus_id,0,4)!='cus_')
              $session_arr['customer_email'] = $this->context->customer->email;
        }
        try {
          $session = \Stripe\Checkout\Session::create($session_arr);
        } catch (Exception $e) { die(json_encode(array('code' => '0','msg' => $e->getMessage()))); }

        $conveyor['pm'] = pSQL($session->id);
        $conveyor['id_payment_intent'] = pSQL($session->payment_intent->id);
        $conveyor['client_secret'] = pSQL($session->payment_intent->client_secret);
        $conveyor['customer_stripe_id'] = pSQL($cus_id);
        $this->updateTransaction($conveyor);
        die(json_encode(array('code' => 1,'sess_id'=>$session->id)));
   }

    public function handlePaymentIntent($intent,$amount,$currency,$cus_id,$pm='card')
    {
      $new_intent = NULL;
      $pi_data = array('amount' => $amount,'currency' => $currency);
      $address_delivery = new Address($this->context->cart->id_address_delivery);
      $country = Country::getIsoById($address_delivery->id_country);
      $state = (!empty($address_delivery->id_state)?State::getNameById($address_delivery->id_state):0);
      $conveyor = array('source_type' => $pm, 'amount'  => $amount, 'currency' => pSQL($currency),
                    'cart_id' => (int)$this->context->cart->id,
                    'customer_id' => (int)$this->context->customer->id,
                    'mode' => pSQL((Configuration::get('STRIPE_MODES') ? 'live' : 'test')),
                    );

      if(Configuration::get('STRIPE_CUSTOMER_INFO') && Tools::substr($cus_id,0,4)!='cus_') {
        $cus_id = $this->createStripeCustomer($conveyor);
      }
      $conveyor['customer_stripe_id'] = pSQL($cus_id);
      $pi_new_data = array(
      'description'=>$this->context->shop->name.' - '.$this->l('Customer #').$this->context->customer->id.' - '.$this->l('Cart #').$this->context->cart->id,
      'automatic_payment_methods' => array('enabled' => true),
      'metadata' => array('id_cart' => (int)$this->context->cart->id),
      //'payment_method_types' => array('card','paynow'),
      'customer' => (Tools::substr($cus_id,0,4)=='cus_'?$cus_id:NULL),
      'shipping' => array('name'=>$address_delivery->firstname.' '.$address_delivery->lastname,
                          'address'=>array('line1' => $address_delivery->address1,
                                           'line2' => $address_delivery->address2,
                                           'city' => $address_delivery->city,
                                           'state' => $state,
                                           'country' => $country,
                                           'postal_code' => $address_delivery->postcode)),
      'receipt_email' => (Configuration::get('STRIPE_PAYMENT_RECEIPT')?$this->context->customer->email:NULL),
      'capture_method' => (!Configuration::get('STRIPE_CAPTURE_TYPE')?'manual':'automatic'));
      if($country=="US"){
        $pi_new_data['shipping']['address']['state'] = State::getNameById($address_delivery->id_state);
      }

      if(isset($intent['id_stripe_transaction']))
      {
           if($intent['amount']!=$amount || $intent['currency']!=$currency || $intent['source_type']!=$pm || empty($intent['id_payment_intent']))
           {
              $new_new_pi = false;
              if($intent['source_type']=='checkout')
                $new_new_pi = true;

              try {
                 if($new_new_pi || empty($intent['id_payment_intent']))
                    $new_intent = \Stripe\PaymentIntent::create(array_merge($pi_data,$pi_new_data));
                 else
                    $new_intent = \Stripe\PaymentIntent::update($intent['id_payment_intent'], $pi_data);
               } catch (Exception $e) { die(json_encode(array('code' => '0','msg' => $e->getMessage()))); }
           }
        } else {
          $pi_data = array_merge($pi_data,$pi_new_data);
          try {
              $new_intent = \Stripe\PaymentIntent::create($pi_data);
            } catch (Exception $e) { die(json_encode(array('code' => '0','msg' => $e->getMessage()))); }
        }

        if($new_intent==NULL)
        return $intent;

        if($pm=='paymentElement')
        $conveyor['source_type'] = 'paymentElement';
        $conveyor['id_payment_intent'] = pSQL($new_intent->id);
        $conveyor['client_secret'] = pSQL($new_intent->client_secret);
        $conveyor['STRIPE_MODES'] = (Configuration::get('STRIPE_MODES') ? 'live' : 'test');
        $this->updateTransaction($conveyor);
        return $new_intent;
    }

    public function getTemplateVars($checkCartInfo = false, $pm = false, $token = NULL)
    {
        $currency = $this->context->currency->iso_code;
        $amount = $this->context->cart->getOrderTotal();
        $amount = (string)($this->isZeroDecimalCurrency($currency) ? round($amount) : round($amount,2)*100);
        if($amount<50)
        return;
        $STRIPE_MODES = (Configuration::get('STRIPE_MODES') ? 'live' : 'test');
        $domain = Tools::getShopDomainSsl(true, true);
        $base_uri= $domain.__PS_BASE_URI__;
        $logo_url = $base_uri.'img/'.Configuration::get('PS_LOGO');
        $lang_iso = ($this->context->language->iso_code=='br'?'pt-br':$this->context->language->iso_code);
        $iso_countries = array('AT', 'BE', 'DE', 'NL', 'ES', 'IT');
        $sofort_countries = array();
        foreach ($iso_countries as $iso) {
            $id_country = Country::getByIso($iso);
            $sofort_countries[$iso] = Country::getNameById($this->context->language->id, $id_country);
        }

        $conveyor = array(
            'publishableKey' => (Configuration::get('STRIPE_MODES') ? Configuration::get('STRIPE_PUBLIC_KEY_LIVE') : Configuration::get('STRIPE_PUBLIC_KEY_TEST')),
            'currency_iso' => $currency,
            'amount_ttl' => $amount,
            'ps_cart_id' => $this->context->cart->id,
            'baseDir' => $domain.__PS_BASE_URI__,
            'STRIPE_MODES' => (int)Configuration::get('STRIPE_MODES'),
            'module_dir' => $this->_path,
            'stripe_cc' => $this->_path."views/img/stripe-cc.png",
            'stripe_alipay' => $this->_path."views/img/alipay.png",
            'stripe_ps_version' => _PS_VERSION_,
            'stripe_allow_zip'  => Configuration::get('STRIPE_ALLOW_ZIP'),
            'stripe_allow_cards'  => Configuration::get('STRIPE_ALLOW_CARDS'),
            'stripe_allow_sepa'  => Configuration::get('STRIPE_ALLOW_SEPA'),
            'stripe_allow_alipay'  => Configuration::get('STRIPE_ALLOW_ALIPAY'),
            'stripe_error' => (string)Tools::getValue('stripe_error'),
            'order_validation_url' => $this->context->link->getModuleLink($this->name, 'validation', array('content_only'=>1), true),
            'sofort_countries' => $sofort_countries,
            'lang_iso_code' => $lang_iso,
            'logo_url' => $logo_url,
            'ajax_payment' => $this->context->link->getModuleLink($this->name, 'payment', array(), true),
        );

        if(!$this->context->cookie->logged)
          return $conveyor;

        $intent = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripejs_transaction WHERE mode="'.$STRIPE_MODES.'" && status="pending" && id_cart = '.(int)$this->context->cart->id);
        $stripeTokens = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'stripejs_transaction WHERE mode="'.$STRIPE_MODES.'" && `type`="payment" && stripe_cus_id != "" && `source_type` IN ("card","prbutton") && status IN ("paid","uncaptured") && cc_type!="" && card_reuse=1 && id_customer = '.(int)$this->context->customer->id.' group by `source`');
        $cus_id = Db::getInstance()->getValue('SELECT stripe_cus_id FROM '._DB_PREFIX_.'stripejs_transaction WHERE mode="'.$STRIPE_MODES.'" && `type`="payment" && stripe_cus_id LIKE "cus_%" && id_customer = '.(int)$this->context->customer->id);

        ///////////////Handle Payment Intent///////////////////
        if ($checkCartInfo && !empty($pm)) {
            if (Configuration::get('STRIPE_ALLOW_CARDS')==2 && $pm=='checkout'){
                if(!empty($intent['source']) && $intent['id_order']>0)
                  die(json_encode(array('code'=>1,'sess_id'=>$intent['source'])));
                $this->handleCheckoutSession($amount,$currency,$cus_id,$logo_url,$base_uri);
            } else {
                $pi = $this->handlePaymentIntent($intent,$amount,$currency,$cus_id,$pm);
                die(json_encode(array('code'=>1,'pi_id'=>(isset($pi->id)?$pi->id:$intent['id_payment_intent']),'pi_cs'=>(isset($pi->client_secret)?$pi->client_secret:$intent['client_secret']))));
            }
        }

        $address_billing = new Address($this->context->cart->id_address_invoice);
        $address_delivery = new Address($this->context->cart->id_address_delivery);
        $state = (!empty($address_billing->id_state)?State::getNameById($address_billing->id_state):0);

        $billing_address = array(
            'fname' => $address_billing->firstname,
            'lname' => $address_billing->lastname,
            'line1' => $address_billing->address1,
            'line2' => $address_billing->address2,
            'city' => $address_billing->city,
            'state' => $state,
            'zip_code' => $address_billing->postcode,
            'country' => $address_billing->country,
            'phone' => $address_billing->phone ? $address_billing->phone : $address_billing->phone_mobile,
        );
        $address_delivery = array(
            'fname' => $address_delivery->firstname,
            'lname' => $address_delivery->lastname,
            'line1' => $address_delivery->address1,
            'line2' => $address_delivery->address2,
            'city' => $address_delivery->city,
            'zip_code' => $address_delivery->postcode,
            'country' => $address_delivery->country,
            'phone' => $address_delivery->phone ? $address_delivery->phone : $address_delivery->phone_mobile,
        );
        $country = Country::getIsoById($address_billing->id_country);
        $pr_countries = array('AE', 'AT', 'AU', 'BE', 'BG', 'BR', 'CA', 'CH', 'CI', 'CR', 'CY', 'CZ', 'DE', 'DK', 'DO', 'EE', 'ES', 'FI', 'FR', 'GB', 'GI', 'GR', 'GT', 'HK', 'HU', 'ID', 'IE', 'IN', 'IT', 'JP', 'LI', 'LT', 'LU', 'LV', 'MT', 'MX', 'MY', 'NL', 'NO', 'NZ', 'PE', 'PH', 'PL', 'PT', 'RO', 'SE', 'SG', 'SI', 'SK', 'SN', 'TH', 'TT', 'US', 'UY');
        $STRIPE_ALLOW_PRBUTTON = (Configuration::get('STRIPE_ALLOW_PRBUTTON') && in_array($country,$pr_countries)?1:0);

        $conveyor['cu_fname'] = $this->context->customer->firstname;
        $conveyor['cu_lname'] = $this->context->customer->lastname;
        $conveyor['cu_email'] = $this->context->customer->email;
        $conveyor['stripeTokens'] = $stripeTokens;
        $conveyor['billing_address'] = $billing_address;
        $conveyor['ship_address'] = $address_delivery;
        $conveyor['country_iso_code'] = $country;
        $conveyor['stripe_allow_prbutton'] = $STRIPE_ALLOW_PRBUTTON;

        return $conveyor;
    }

    /**
     * Process a payment
     */
    public function processPayment(array $params, $result_pi = NULL)
    {
        @ini_set('display_errors', 'off');
        $amount = $this->context->cart->getOrderTotal();
        $currency = $this->context->currency->iso_code;
        $conveyor = array('token' => $params['token'],
        'currency' => $currency,
        'cart_id' => (int)$this->context->cart->id,
        'customer_id' => (int)$this->context->cart->id_customer,
        'secure_key' => $this->context->cart->secure_key,
        'amount' => ($this->isZeroDecimalCurrency($currency) ? round($amount) : round($amount,2)*100),
        'amt_paid' => ($this->isZeroDecimalCurrency($currency) ? round($amount) : round($amount,2)),
        'cu_email' => $this->context->customer->email,
        'STRIPE_MODES' => (Configuration::get('STRIPE_MODES') ? 'live' : 'test'));
        $conveyor = array_merge($conveyor,$params);
        $this->updateTransaction($conveyor);
        $intent = NULL;

        if(Tools::substr($conveyor['token'],0,3)=='pi_') {
          if($result_pi != NULL)
            $intent = $result_pi;
          else
            $intent = \Stripe\PaymentIntent::retrieve($conveyor['token']);

          if(in_array($conveyor['source_type'],array('checkout','paymentElement')) && $intent->status=='requires_action'){
            $src_type = explode('_',$intent->next_action->type);
            $conveyor['source_type'] = $src_type[0];
          }
        }

        $pm_array = array('paymentElement'=>'Payment','prbutton'=>'Wallet','card'=>'Card','sepa_debit'=>'SEPA','alipay'=>'Alipay','grabpay'=>'GrabPay','giropay'=>'Giropay','ideal'=>'iDEAL','bancontact'=>'Bancontact','sofort'=>'Sofort','p24'=>'Przelewy24','eps'=>'EPS','fpx'=>'FPX','oxxo'=>'OXXO','multibanco'=>'MULTIBANCO','klarna'=>'Klarna','affirm'=>'Affirm','wechat_pay' => 'WeChat Pay','paypal' => 'Paypal','amazon_pay' => 'Amazon Pay','paynow' => 'PayNow','afterpay_clearpay'=> 'Afterpay/ Clearpay','boleto'=>'Boleto','konbini'=>'Konbini','us_bank_account'=>'US Bank Transfer','jp_bank_account'=>'JP Bank Transfer','blik'=>'Blik','pix'=>'Pix');
        $conveyor['display_pm'] = ($conveyor['source_type']=='checkout'?'Stripe Checkout':(array_key_exists($conveyor['source_type'],$pm_array)?$pm_array[$conveyor['source_type']]:ucfirst($conveyor['source_type'])).' - Stripe');

        ////////////Handle for Multibanco/////////////
        if (in_array($conveyor['source_type'],array('multibanco','boleto','pix'))) {

            $conveyor['id_order'] = $this->createOrder($conveyor,(int)Configuration::get('STRIPE_AWAITING_OS'));
            if ($conveyor['id_order']>0) {
                $conveyor['pm'] = $conveyor['token'];
                if($conveyor['source_type']=='multibanco'){
                  $conveyor['cc_exp'] = $intent->next_action->multibanco_display_details->entity;
                  $conveyor['cc_type'] = $intent->next_action->multibanco_display_details->reference;
                  $vars = array('{total_amt}' => $currency.$conveyor['amt_paid'],'{name}' => $this->context->customer->firstname.' '.$this->context->customer->lastname,'{mb_reference}'=>$conveyor['cc_type'],'{mb_entity}'=>$conveyor['cc_exp'],);

                  Mail::Send(
                      (int)$this->context->cookie->id_lang,
                      'mb_info',
                      Mail::l('MULTIBANCO payment info'),
                      $vars,
                      $this->context->customer->email,
                      $this->context->customer->firstname.' '.$this->context->customer->lastname,
                      null,
                      null,
                      null,
                      null,
                      dirname(__FILE__).'/mails/');
                }
                $this->updateTransaction($conveyor);
                die(json_encode(array('code' => 1,'url' => Context::getContext()->link->getPageLink('order-confirmation',true,null,array('id_cart' => $conveyor['cart_id'],'id_module' => (int)$this->id,'id_order' => $conveyor['id_order'],'key' =>$conveyor['secure_key'])))));
            } else
            die(json_encode(array('code' => '0','msg' => $this->l('unable to register order, please use any other payment option or contact us for support.'))));
        }
        ////////////Handle for Multibanco/////////////

        if(Tools::substr($conveyor['token'],0,3)=='pi_') {

          $pi_error = (!empty($intent->last_payment_error)?$intent->last_payment_error->message:(isset($intent->error)?$intent->error->message:''));
          if(isset($intent->latest_charge) && !empty($intent->latest_charge))
            $result_json = \Stripe\Charge::retrieve($intent->latest_charge);
          else
            $result_json = $intent->charges->data[0];
          $conveyor['amount'] = round($intent->amount);
          $conveyor['amt_paid'] = ($this->isZeroDecimalCurrency($currency) ? $intent->amount : $intent->amount/100);
          if($conveyor['source_type']=='paymentElement' || empty($conveyor['source_type'])){
            $conveyor['source_type'] = $result_json->payment_method_details->type;
            $conveyor['display_pm'] = (array_key_exists($conveyor['source_type'],$pm_array)?$pm_array[$conveyor['source_type']]:$conveyor['source_type']).' - Stripe';
          }

          if (in_array($intent->status,array('succeeded','requires_capture','processing'))) {

              @Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'stripejs_transaction SET id_customer='.$conveyor['customer_id'].', status="'.($intent->status=='succeeded' ? 'paid' : ($intent->status=='requires_capture' ? 'uncaptured' : 'processing')).'" where id_cart = '.$conveyor['cart_id']);
              $conveyor['customer_stripe_id'] = (!empty($intent->customer)?$intent->customer:NULL);

             ################Saving Customer card#################
             if(Configuration::get('STRIPE_ALLOW_USEDCARD') && ((Tools::getIsset('card_reuse') && (int)Tools::getValue('card_reuse')==1) || (isset($conveyor['card_reuse']) && (int)$conveyor['card_reuse']==1)) && !Tools::getValue('quick_pay'))
              {
                $new_cus = $this->processStripeCustomer($conveyor,$intent);
                $conveyor['customer_stripe_id'] = (!is_array($new_cus) && Tools::substr($new_cus,0,4)=='cus_'?$new_cus:$conveyor['customer_stripe_id']);
              }

          } else {
            if(Tools::getValue('controller')=='validation')
              Tools::redirect($this->context->link->getPageLink('order', true,null,array('stripe_error'=>$pi_error)));
            else
              die(json_encode(array('code' => '0','msg' => $pi_error)));
            }
        } else {
          die(json_encode(array('code' => '0', 'msg' => $this->l('Invalid token!!'),)));
        }

        if (in_array($result_json->status,array('succeeded')) && $result_json->captured == true)
          $order_status = (int)Configuration::get('STRIPE_PAYMENT_ORDER_STATUS');
        elseif(in_array($result_json->status,array('pending')) || $result_json->captured == false)
          $order_status = (int)Configuration::get('STRIPE_AWAITING_OS');
        else
          die(json_encode(array('code' => '0', 'msg' => $this->l('Payment declined. Unknown error, please use another card or contact us.'),)));

        $conveyor['id_transaction'] = (isset($result_json->id)?$result_json->id:'');
        $conveyor['id_order'] = $this->createOrder($conveyor,$order_status);

        $this->handleUpdateTransaction($conveyor, $result_json);
        if($conveyor['id_order']<=0)
        die(json_encode(array('code' => '0', 'msg' => $this->l('Payment was successful but unable to register the order. Please contact us and do not submit payment again.'),)));

        $order_conf_url = Context::getContext()->link->getPageLink('order-confirmation',true,null,array('id_cart' => $conveyor['cart_id'],'id_module' => (int)$this->id,'id_order' => $conveyor['id_order'],'key' => $conveyor['secure_key']));
        if(Tools::getValue('controller')=='validation')
          Tools::redirect($order_conf_url);
        else
          die(json_encode(array('code' => '1','url' => $order_conf_url)));
    }

    public function handleUpdateTransaction($conveyor, $result_json)
    {
       $payment_method = NULL;
       if(Tools::substr($conveyor['token'],0,3)=='pi_' && !empty($result_json->balance_transaction)) {
            $charge = \Stripe\BalanceTransaction::retrieve($result_json->balance_transaction);
            $conveyor['fee'] = $charge->fee;
        } else
            $conveyor['fee'] = $result_json->balance_transaction->fee;
        if($result_json->payment_method_details->type=='card')
            $payment_method = $result_json->payment_method_details->card;
        elseif($result_json->payment_method_details->type=='sepa_debit')
            $payment_method = $result_json->payment_method_details->sepa_debit;

        $conveyor['pm'] = pSQL($result_json->payment_method);
        $conveyor['pm_type'] = pSQL($result_json->payment_method_details->type);
        $conveyor['status'] = pSQL($result_json->paid=='true'?($result_json->captured?'paid':'uncaptured'):($result_json->status=='processing'?'processing':'pending'));
        $conveyor['risk_score'] = pSQL($result_json->outcome->risk_score);
        $conveyor['risk_level'] = pSQL($result_json->outcome->risk_level);
        if(in_array($conveyor['source_type'],array('card','checkout','sepa_debit'))){
          $conveyor['cc_type'] = pSQL($conveyor['source_type']=='sepa_debit'?pSQL($payment_method->fingerprint):pSQL($payment_method->brand));
          $conveyor['cc_last_digits'] = pSQL($payment_method->last4);
          $conveyor['cc_exp'] = pSQL(sprintf("%02d", $payment_method->exp_month).' / '.$payment_method->exp_year);
          $conveyor['line1_check'] = (int)($payment_method->checks->address_line1_check == 'pass' ? 1 : 0);
          $conveyor['zip_check'] = (int)($payment_method->checks->address_postal_code_check == 'pass' ? 1 : 0);
          $conveyor['cvc_check'] = (int)($payment_method->checks->cvc_check == 'pass' ? 1 : 0);
          $conveyor['three_d_secure'] = (!empty($payment_method->three_d_secure) && $payment_method->three_d_secure->result == 'authenticated' ? 1 : 0);
        }
        $this->updateTransaction($conveyor);
    }

    public function updateTransaction($conveyor)
    {
        $data = array('type' => 'payment');
        if(isset($conveyor['pm']))
          $data['source'] = $conveyor['pm'];
        if(isset($conveyor['pm_type']) || isset($conveyor['source_type']))
          $data['source_type'] = (isset($conveyor['pm_type'])?$conveyor['pm_type']:$conveyor['source_type']);
        if(isset($conveyor['id_payment_intent']))
          $data['id_payment_intent'] = $conveyor['id_payment_intent'];
        if(isset($conveyor['client_secret']))
          $data['client_secret'] = $conveyor['client_secret'];
        if(isset($conveyor['customer_stripe_id']))
          $data['stripe_cus_id'] = $conveyor['customer_stripe_id'];
        if(isset($conveyor['customer_id']))
          $data['id_customer'] = $conveyor['customer_id'];
        if(isset($conveyor['cart_id']))
          $data['id_cart'] = $conveyor['cart_id'];
        if(isset($conveyor['id_order']))
          $data['id_order'] = $conveyor['id_order'];
        if(isset($conveyor['id_transaction']))
          $data['id_transaction'] = $conveyor['id_transaction'];
        if(isset($conveyor['amount']))
          $data['amount'] = $conveyor['amount'];
        if(isset($conveyor['status']))
          $data['status'] = $conveyor['status'];
        if(isset($conveyor['currency']))
          $data['currency'] = $conveyor['currency'];
        if(isset($conveyor['cc_type']))
          $data['cc_type'] = $conveyor['cc_type'];
        if(isset($conveyor['cc_exp']))
          $data['cc_exp'] = $conveyor['cc_exp'];
        if(isset($conveyor['cc_last_digits']))
          $data['cc_last_digits'] = $conveyor['cc_last_digits'];
        if(isset($conveyor['line1_check']))
          $data['line1_check'] = $conveyor['line1_check'];
        if(isset($conveyor['zip_check']))
          $data['zip_check'] = $conveyor['zip_check'];
        if(isset($conveyor['cvc_check']))
          $data['cvc_check'] = $conveyor['cvc_check'];
        if(isset($conveyor['three_d_secure']))
          $data['three_d_secure'] = $conveyor['three_d_secure'];
        if(isset($conveyor['risk_score']))
          $data['risk_score'] = $conveyor['risk_score'];
        if(isset($conveyor['risk_level']))
          $data['risk_level'] = $conveyor['risk_level'];
        if(Tools::getIsset('card_reuse') || isset($conveyor['card_reuse']))
          $data['card_reuse'] = (Tools::getIsset('card_reuse')?(int)Tools::getValue('card_reuse'):(int)$conveyor['card_reuse']);
        if(isset($conveyor['fee']))
          $data['fee'] = $conveyor['fee'];
        if(isset($conveyor['STRIPE_MODES']))
          $data['mode'] = $conveyor['STRIPE_MODES'];
        $data['date_add'] = array('type' => 'sql', 'value' => 'NOW()');

        $trans_exists = Db::getInstance()->getValue('SELECT id_stripe_transaction FROM '._DB_PREFIX_.'stripejs_transaction WHERE type = "payment" && id_cart = '.(int)$conveyor['cart_id']);
        if(empty($trans_exists))
          Db::getInstance()->insert('stripejs_transaction',$data);
        else
          Db::getInstance()->update('stripejs_transaction',$data,'id_stripe_transaction='.$trans_exists);
    }

    public function createStripeCustomer($conveyor)
     {
       $customer_stripe_id = ''; $cus_address = array();
       if(!empty($this->context->cart->id_address_invoice)) {
         $address = new Address($this->context->cart->id_address_invoice);
         $state = (!empty($address->id_state)?State::getNameById($address->id_state):0);
         $cus_address = array(
             'line1' => $address->address1,
             'line2' => $address->address2,
             'city' => $address->city,
             'state' => $state,
             'postal_code' => $address->postcode,
             'country' => $address->country,
         );
       }
       try {
             $customer_stripe = \Stripe\Customer::create(array(
               'description' => $this->context->shop->name.' - '.$this->l('Customer #').$conveyor['customer_id'],
               'email' => $this->context->customer->email,
               'name' => $this->context->customer->firstname.' '.$this->context->customer->lastname,
               'address' => $cus_address));
             if (isset($customer_stripe->id))
                $customer_stripe_id = $customer_stripe->id;
          } catch (Exception $e) { Logger::addLog((string)$e->getMessage(), 4, null, 'Cart', $conveyor['cart_id'], true); }

         return $customer_stripe_id;
     }

   public function processStripeCustomer($conveyor,$intent)
    {
      $customer_stripe_id='';
      try {
              if (empty($intent->customer)) {
                  $customer_stripe = \Stripe\Customer::create(array('description' => $this->context->shop->name.' - '.$this->l('Customer #').$conveyor['customer_id'],
                           'email' => $conveyor['cu_email'], 'payment_method' => $intent->payment_method));
                  if (isset($customer_stripe->id)) {
                     $customer_stripe_id = $customer_stripe->id;
                  }
               } else {
                   $customer_stripe_id = $intent->customer;
                   $payment_method = \Stripe\PaymentMethod::retrieve($intent->payment_method);
                   $payment_method->attach(array('customer' => $intent->customer));
                   }
             } catch (Exception $e) {
                 return array('status'=>false,'msg'=>$e->getMessage());
          }
        return $customer_stripe_id;
    }

    public function createOrder($conveyor, $order_status)
    {
        $id_order = (int)Order::getIdByCartId($conveyor['cart_id']);
        if($id_order>0)
          return $id_order;
        try {
          ob_start();
          parent::validateOrder(
                  $conveyor['cart_id'],
                  $order_status,
                  $conveyor['amt_paid'],
                  $conveyor['display_pm'],
                  null,
                  (!empty($conveyor['id_transaction'])?array('transaction_id' => $conveyor['id_transaction']):array()),
                  null,
                  false,
                  $conveyor['secure_key']
              );
            ob_flush();
         } catch (PrestaShopException $e) { Logger::addLog((string)$e->getMessage(), 4, null, 'Cart', $conveyor['cart_id'], true); }

         $id_order = (int)Order::getIdByCartId($conveyor['cart_id']);

         if (Configuration::get('STRIPE_CHARGE_ORDERID') && $id_order>0) {
         try {
            if(Tools::substr($conveyor['token'],0,3)=='pi_')
              \Stripe\PaymentIntent::update($conveyor['token'],array('description'=>$this->context->shop->name.' - '.$this->l("ORDER #").$id_order));
             elseif(!empty($conveyor['id_transaction'])){
              $ch = \Stripe\Charge::retrieve($conveyor['id_transaction']);
              $ch->description = $this->context->shop->name.' - '.$this->l("ORDER #").$id_order;
              $ch->save();
            }
         } catch (PrestaShopException $e) {}
        }
        return $id_order;
    }

	public function hookActionEmailSendBefore($params)
	{
		if (!isset($params['cart']->id)) {
			return true;
		}
		$orderId = Order::getIdByCartId($params['cart']->id);
		$order = new Order($orderId);
		if ($order->module !== $this->name) {
			return true;
		}
		$template = $params['template'];

		if (in_array($template,array('new_order','payment')) && $order->current_state==Configuration::get('STRIPE_AWAITING_OS')) {

      return false;
		}

		return true;
	}

  public function hookActionOrderStatusPostUpdate($params)
	{

		$order = new Order($params['id_order']);
		if ($order->module !== $this->name) {
			return true;
		}

    $trans = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripejs_transaction WHERE id_order = '.(int)$order->id.' AND type = "payment" AND status = "uncaptured"');
    $ps_amt = $this->isZeroDecimalCurrency($trans['currency']) ? $trans['amount'] : $trans['amount'] / 100;
    if (isset($trans['id_payment_intent']))
    {
      if($order->getCurrentState() == 4)
        $this->processCapture($trans['id_payment_intent'], $trans['currency'], $ps_amt);
    }

		return true;
	}

    public function hookDisplayOrderConfirmation($params)
    {
        if (!isset($params['order']) || ($params['order']->module != $this->name))
            return false;

        $currentOrderStatus = (int)$params['order']->getCurrentState();
        $valid = ($currentOrderStatus==Configuration::get('STRIPE_CHARGEBACKS_ORDER_STATUS')?0:1);

        $pending = 0;
        $pending_arr = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripejs_transaction WHERE id_order = '.(int)$params['order']->id.' AND type = "payment" AND source_type="multibanco" AND status = "pending"');
        if(!empty($pending_arr['amount'])){
            $pending = 1;
            $pending_arr['amount'] = $this->isZeroDecimalCurrency($params['currency']) ? $pending_arr['amount'] : $pending_arr['amount'] / 100;
            $this->context->smarty->assign('module_dir', $this->_path);
            }

        if ($params['order'] && Validate::isLoadedObject($params['order']) && isset($params['order']->valid))
            $this->context->smarty->assign('stripe_order', array('reference' => isset($params['order']->reference) ? $params['order']->reference : '#'.sprintf('%06d', $params['objOrder']->id), 'valid' => $valid, 'pending' => $pending, 'pending_arr' => $pending_arr));

            $this->context->smarty->assign('order_pending', false);

        return $this->fetch('module:stripejs/views/templates/front/order-confirmation.tpl');
    }

    public function hookDisplayCustomerAccount()
    {
        if(!Configuration::get('STRIPE_ALLOW_DELETE_CARDS') || !Configuration::get('STRIPE_ALLOW_USEDCARD'))
          return;
        else
          return $this->display(__FILE__, 'my-account.tpl');
    }


    public function isZeroDecimalCurrency($currency)
    {
        $zeroDecimalCurrencies = array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','VND','VUV','XAF','XOF','XPF');
        return in_array($currency, $zeroDecimalCurrencies);
    }

    /**
     * Check settings requirements to make sure the Stripe's module will work properly
     *
     * @return boolean Check result
     */
    public function checkSettings()
    {
        if (Configuration::get('STRIPE_MODES'))
            return (bool)(Tools::substr(Configuration::get('STRIPE_PUBLIC_KEY_LIVE'),0,8) == 'pk_live_' && Tools::substr(Configuration::get('STRIPE_PRIVATE_KEY_LIVE'),0,8) == 'rk_live_');
        else
            return (bool)(Tools::substr(Configuration::get('STRIPE_PUBLIC_KEY_TEST'),0,8) == 'pk_test_' && Tools::substr(Configuration::get('STRIPE_PRIVATE_KEY_TEST'),0,8) == 'rk_test_');
    }

    /**
     * Check technical requirements to make sure the Stripe's module will work properly
     *
     * @return array Requirements tests results
     */
    public function checkRequirements()
    {
        $tests = array('result' => true);
        $tests['curl'] = array('name' => $this->l('PHP cURL extension must be enabled on your server'), 'result' => extension_loaded('curl'));
        $tests['mbstring'] = array('name' => $this->l('PHP Multibyte String extension must be enabled on your server'), 'result' => extension_loaded('mbstring'));
        if (Configuration::get('STRIPE_MODES'))
            $tests['ssl'] = array('name' => $this->l('SSL must be enabled on your store (before entering Live mode)'), 'result' => Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS']) && Tools::strtolower($_SERVER['HTTPS']) != 'off'));
        $tests['php52'] = array('name' => $this->l('Your server must run PHP 5.6 or greater'), 'result' => version_compare(PHP_VERSION, '5.6', '>='));
        $tests['configuration'] = array('name' => $this->l('You must sign-up for Stripe and configure your account settings in the module (publishable key, Restricted API key (RAK)...etc.)'), 'result' => $this->checkSettings());
        if($this->checkSettings() && Tools::getShopDomainSsl()!='localhost') {
          include_once(dirname(__FILE__).'/classes/StripejsInstall.php');
          $Stripejsinstaller =  new StripejsInstall();
          if(Configuration::get('STRIPE_MODES')){
            $apple_pay = $Stripejsinstaller->addAppleDomainAssociation();
            $tests['applepay'] = array('name' => $this->l('Your domain must be added in your Stripe dashboard to use Apple Pay'), 'result' => ($apple_pay?1:0));
          }
          if(!Configuration::get('STRIPEJS_WEBHOOK_SIG_'.(int)Configuration::get('STRIPE_MODES'))){
             StripejsWebhook::webhookExists();
          }
          $tests['webhook'] = array('name' => $this->l('Webhook endpoint must be added in your Stripe dashboard. Read more about webhook in FAQs'), 'result' => !empty(Configuration::get('STRIPEJS_WEBHOOK_SIG_'.(int)Configuration::get('STRIPE_MODES'))));
        }

        foreach ($tests as $k => $test)
            if ($k != 'result' && !$test['result'])
                $tests['result'] = false;

        return $tests;
    }

   /*
     ** @Method: getContent
     ** @description: render main content
     **
     ** @arg:
     ** @return: (none)
     */
    public function getContent()
    {
        include_once(dirname(__FILE__).'/classes/StripejsInstall.php');
        $Stripejsinstaller =  new StripejsInstall();
        if (Tools::substr(decoct(fileperms(_PS_MODULE_DIR_.'stripejs/')), -3)!='755' || Tools::substr(decoct(fileperms(_PS_MODULE_DIR_.'stripejs/stripejs.php')), -3)!='644') {
        $Stripejsinstaller->setCorrectFilePers(dirname(_PS_MODULE_DIR_.'stripejs/'), 0755, 0644);
        }

        $errors = array();

        /* Update Configuration Values when settings are updated */
        if (Tools::isSubmit('SubmitStripe'))
        {
            if (!empty(Tools::getValue('stripe_public_key_test')) && Tools::substr(Tools::getValue('stripe_public_key_test'),0,8) != "pk_test_") {
                $errors[] = $this->l("Invalid TEST Publishable key!");
            }
            if (!empty(Tools::getValue('stripe_private_key_test')) && Tools::substr(Tools::getValue('stripe_private_key_test'),0,8) != "rk_test_") {
                $errors[] = $this->l("Invalid TEST Restricted api key!");
            }
            if (!empty(Tools::getValue('stripe_public_key_live')) && Tools::substr(Tools::getValue('stripe_public_key_live'),0,8) != "pk_live_") {
                $errors[] = $this->l("Invalid LIVE Publishable key!");
            }
            if (!empty(Tools::getValue('stripe_private_key_live')) && Tools::substr(Tools::getValue('stripe_private_key_live'),0,8) != "rk_live_") {
                $errors[] = $this->l("Invalid LIVE Restricted api key!");
            }

            if (empty($errors)) {

                $configuration_values = array(
                    'STRIPE_MODES' => Tools::getValue('STRIPE_MODES'),
                    'STRIPE_ALLOW_CARDS' => Tools::getValue('STRIPE_ALLOW_CARDS'),
                    'STRIPE_PETheme' => Tools::getValue('STRIPE_PETheme'),
                    'STRIPE_Checkout_Open' => Tools::getValue('STRIPE_Checkout_Open'),
                    'STRIPE_CHECKOUT_ORDER' => Tools::getValue('STRIPE_CHECKOUT_ORDER'),
                    'STRIPE_PAYMENT_RECEIPT' => Tools::getValue('STRIPE_PAYMENT_RECEIPT'),
                    'STRIPE_CUSTOMER_INFO' => Tools::getValue('STRIPE_CUSTOMER_INFO'),
                    'STRIPE_ALLOW_USEDCARD' => Tools::getValue('STRIPE_ALLOW_USEDCARD'),
                    'STRIPE_ALLOW_DELETE_CARDS' => Tools::getValue('STRIPE_ALLOW_DELETE_CARDS'),
                    'STRIPE_CAPTURE_TYPE' => Tools::getValue('STRIPE_CAPTURE_TYPE'),
                    'STRIPE_ALLOW_ZIP' => Tools::getValue('STRIPE_ALLOW_ZIP'),
                    'STRIPE_ALLOW_ALIPAY' => Tools::getValue('STRIPE_ALLOW_ALIPAY'),
                    'STRIPE_ALLOW_GRABPAY' => Tools::getValue('STRIPE_ALLOW_GRABPAY'),
                    'STRIPE_ALLOW_OXXO' => Tools::getValue('STRIPE_ALLOW_OXXO'),
                    'STRIPE_STMNT_DESC' => Tools::getValue('STRIPE_STMNT_DESC'),
                    'STRIPE_ALLOW_SEPA' => Tools::getValue('STRIPE_ALLOW_SEPA'),
                    'STRIPE_ALLOW_PRBUTTON' => Tools::getValue('STRIPE_ALLOW_PRBUTTON'),
                    'STRIPE_ALLOW_IDEAL' => Tools::getValue('STRIPE_ALLOW_IDEAL'),
                    'STRIPE_ALLOW_KLARNA' => Tools::getValue('STRIPE_ALLOW_KLARNA'),
                    'STRIPE_ALLOW_AFFIRM' => Tools::getValue('STRIPE_ALLOW_AFFIRM'),
                    'STRIPE_ALLOW_AFTERPAY_CLEARPAY' => Tools::getValue('STRIPE_ALLOW_AFTERPAY_CLEARPAY'),
                    'STRIPE_ALLOW_GIROPAY' => Tools::getValue('STRIPE_ALLOW_GIROPAY'),
                    'STRIPE_ALLOW_SOFORT' => Tools::getValue('STRIPE_ALLOW_SOFORT'),
                    'STRIPE_ALLOW_BANCONTACT' => Tools::getValue('STRIPE_ALLOW_BANCONTACT'),
                    'STRIPE_ALLOW_P24' => Tools::getValue('STRIPE_ALLOW_P24'),
                    'STRIPE_ALLOW_EPS' => Tools::getValue('STRIPE_ALLOW_EPS'),
                    'STRIPE_ALLOW_FPX' => Tools::getValue('STRIPE_ALLOW_FPX'),
                    //'STRIPE_ALLOW_KONBINI' => Tools::getValue('STRIPE_ALLOW_KONBINI'),
                    //'STRIPE_ALLOW_BOLETO' => Tools::getValue('STRIPE_ALLOW_BOLETO'),
                    'STRIPE_ALLOW_PAYNOW' => Tools::getValue('STRIPE_ALLOW_PAYNOW'),
                    'STRIPE_ALLOW_MULTIBANCO' => Tools::getValue('STRIPE_ALLOW_MULTIBANCO'),
                    'STRIPE_ALLOW_WECHAT' => Tools::getValue('STRIPE_ALLOW_WECHAT'),
                    'STRIPE_CHARGE_ORDERID' => Tools::getValue('STRIPE_CHARGE_ORDERID'),
                    'STRIPE_PAYMENT_METHODS_OPC' => Tools::getValue('STRIPE_PAYMENT_METHODS_OPC'),
                    'STRIPE_PUBLIC_KEY_TEST' => trim(Tools::getValue('stripe_public_key_test')),
                    'STRIPE_PUBLIC_KEY_LIVE' => trim(Tools::getValue('stripe_public_key_live')),
                    'STRIPE_PRIVATE_KEY_TEST' => trim(Tools::getValue('stripe_private_key_test')),
                    'STRIPE_PRIVATE_KEY_LIVE' => trim(Tools::getValue('stripe_private_key_live')),
                );

                foreach ($configuration_values as $configuration_key => $configuration_value)
                    Configuration::updateValue($configuration_key, $configuration_value);

            //////////////////////////Domain for Apple Pay///////////////////////
                if($this->checkSettings() && Tools::getShopDomainSsl()!='localhost') {
                    if(Configuration::get('STRIPE_MODES')) {
                      $apple_pay = $Stripejsinstaller->addAppleDomainAssociation();
                      if($apple_pay!==true)
                        $errors[] = $apple_pay;
                    }
                }
             //////////////////////////Domain for Apple Pay///////////////////////
            }
        }
        if (Tools::isSubmit('SubmitOrderStatuses'))
        {
            $configuration_values = array(
                'STRIPE_AWAITING_OS' => (int)Tools::getValue('STRIPE_AWAITING_OS'),
                'STRIPE_PAYMENT_ORDER_STATUS' => (int)Tools::getValue('stripe_payment_status'),
		          	'STRIPE_REFUND_ORDER_STATUS' => (int)Tools::getValue('stripe_refund_status'),
			          'STRIPE_PARTIALLY_REFUNDED_OS' => (int)Tools::getValue('stripe_partial_refund_status'),
                'STRIPE_CHARGEBACKS_ORDER_STATUS' => (int)Tools::getValue('stripe_chargebacks_status'),
            );

            foreach ($configuration_values as $configuration_key => $configuration_value)
                Configuration::updateValue($configuration_key, $configuration_value);
        }

        $requirements = $this->checkRequirements();
        $shopDomainSsl = Tools::getShopDomainSsl(true, true);
        $stripeBOCssUrl = $shopDomainSsl.__PS_BASE_URI__.'modules/'.$this->name.'/views/css/stripe-prestashop-admin.css';
        $statuses = OrderState::getOrderStates((int)$this->context->cookie->id_lang);
        $statuses_options = array(array('name' => 'STRIPE_AWAITING_OS', 'label' => $this->l('Order status for pending/uncaptured payments:'), 'current_value' => Configuration::get('STRIPE_AWAITING_OS')),array('name' => 'stripe_payment_status', 'label' => $this->l('Order status in case of sucessfull payment:'), 'current_value' => Configuration::get('STRIPE_PAYMENT_ORDER_STATUS')),array('name' => 'stripe_refund_status', 'label' => $this->l('Order status in case of full refund:'), 'current_value' => Configuration::get('STRIPE_REFUND_ORDER_STATUS')),array('name' => 'stripe_partial_refund_status', 'label' => $this->l('Order status in case of partial refund:'), 'current_value' => Configuration::get('STRIPE_PARTIALLY_REFUNDED_OS')),array('name' => 'stripe_chargebacks_status', 'label' => $this->l('Order status in case of a Failed/ Canceled payment:'), 'current_value' => Configuration::get('STRIPE_CHARGEBACKS_ORDER_STATUS')));

         $tplVars = array(
            'errors' => $errors,
            'statuses' => $statuses,
            'statuses_options' => $statuses_options,
            'this_path' => $this->_path,
            'requirements' => $requirements,
            'checkSettings' => $this->checkSettings(),
            'stripeBOCssUrl' => $stripeBOCssUrl,
            'ps_version' => $this->version,
            'webhook_events' => implode("<br>- ",StripeJs::$webhook_events),
            'webhook_url' => $this->context->link->getModuleLink('stripejs', 'webhook', array('ajax'=>true), true),
        );

        if (Tools::isSubmit('SubmitStripe') && empty($errors) || Tools::isSubmit('SubmitOrderStatuses'))
            $tplVars['success'] = true;
        else
            $tplVars['success'] = false;

        $this->context->smarty->assign($tplVars);
        return $this->display(__FILE__, 'views/templates/admin/settings.tpl');
    }

     /**
     * Display Stripe's transactions details
     * Visible on the Order's detail page in the Back-office only
     *
     * @return string HTML/JS Content
     */
    public function hookDisplayBackOfficeHeader()
    {
        /* Continue if we are on the order's details page (Back-office) */

        if(Tools::getValue('id_order')>0 && Tools::getValue('controller')=='AdminOrders')
        {
            $order = new Order((int)Tools::getValue('id_order'));

        /* If the "Refund" button has been clicked, check if we can perform a partial or full refund on this order */
        if ((Tools::isSubmit('SubmitStripeRefund') || Tools::isSubmit('SubmitStripePartialRefund')) && Tools::getIsset('id_transaction'))
        {
            /* Get transaction details and make sure the token is valid */
            $trans = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripejs_transaction WHERE id_transaction = "'.pSQL(Tools::getValue('id_transaction')).'" AND type = "payment" AND status = "paid"');
            if (isset($trans['id_transaction']) && $trans['id_transaction'] === Tools::getValue('id_transaction'))
            {
                /* Check how much has been refunded already on this order */
                $stripe_refunded = Db::getInstance()->getValue('SELECT SUM(amount) FROM '._DB_PREFIX_.'stripejs_transaction WHERE id_order = '.(int)Tools::getValue('id_order').' AND type = "refund" AND status = "paid"');
                $stripe_refunded = ($this->isZeroDecimalCurrency($trans['currency']) ? (int)$stripe_refunded : ($stripe_refunded / 100));
                $ps_amt = ($this->isZeroDecimalCurrency($trans['currency']) ? (int)$trans['amount'] : ($trans['amount'] / 100));
        				$remain = (string)($ps_amt - $stripe_refunded);
        				if(Tools::isSubmit('SubmitStripeRefund')) {
        					$processRefund = $this->processRefund($trans['id_transaction'], $trans['currency'], $ps_amt, $trans);
                  if ($processRefund && $order->getCurrentState() != Configuration::get('STRIPE_REFUND_ORDER_STATUS'))
                    $order->setCurrentState((int)Configuration::get('STRIPE_REFUND_ORDER_STATUS'));
    				    } elseif (Tools::isSubmit('SubmitStripePartialRefund') && Tools::getValue('stripe_amount_to_refund') <= $remain) {
                    $processRefund = $this->processRefund($trans['id_transaction'], $trans['currency'], (float)Tools::getValue('stripe_amount_to_refund'), $trans);
    					      $refund_os = ((float)$remain > (float)Tools::getValue('stripe_amount_to_refund') ? Configuration::get('STRIPE_PARTIALLY_REFUNDED_OS'):Configuration::get('STRIPE_REFUND_ORDER_STATUS'));
                    if ($processRefund && $order->getCurrentState() != $refund_os)
                      $order->setCurrentState((int)$refund_os);
                } else
                  $this->_errors['stripe_refund_error'] = $this->l('You cannot refund more than').' '.$trans['currency'].($ps_amt - $stripe_refunded).' '.$this->l('on this order');

                  $stripeRefunded = (isset($this->_errors['stripe_refund_error'])?$this->_errors['stripe_refund_error']:1);
                  if(version_compare(_PS_VERSION_, '1.7.5', '>')) {
                    Tools::redirect($this->context->link->getLegacyAdminLink('AdminOrders',true, array('id_order'=>$order->id,'vieworder'=>1,'stripeRefunded'=>$stripeRefunded)));
                  } else {
                    Tools::redirect($this->context->link->getAdminLink('AdminOrders',true, array(), array('id_order'=>$order->id,'vieworder'=>1,'stripeRefunded'=>$stripeRefunded)));
                  }
            }
        }

        if (Tools::isSubmit('SubmitStripeCancelAuth') && Tools::getIsset('id_payment_intent'))
        {
            $trans = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripejs_transaction WHERE id_order = '.(int)Tools::getValue('id_order').' AND type = \'payment\' AND status = \'uncaptured\'');
            if (isset($trans['id_payment_intent']) && $trans['id_payment_intent'] === Tools::getValue('id_payment_intent'))
            {
                  $processCancelAuth =  $this->cancelPaymentIntent($trans['id_payment_intent']);
                  if ($processCancelAuth && $order->getCurrentState() != 6)
                      $order->setCurrentState(6);
            }

            $stripeCancelAuthorization = (isset($this->_errors['stripe_capture_error'])?$this->_errors['stripe_capture_error']:1);
            if(version_compare(_PS_VERSION_, '1.7.5', '>')) {
            Tools::redirect($this->context->link->getLegacyAdminLink('AdminOrders',true, array('id_order'=>$order->id,'vieworder'=>1,'stripeCancelAuthorization'=>$stripeCancelAuthorization)));
           } else {
              Tools::redirect($this->context->link->getAdminLink('AdminOrders',true, array(), array('id_order'=>$order->id,'vieworder'=>1,'stripeCancelAuthorization'=>$stripeCancelAuthorization)));
            }
        }

        /* If the "Capture" button has been clicked, check if we can perform a partial or full capture on this order */
        if (Tools::isSubmit('SubmitStripeCapture') && Tools::getIsset('stripe_amount_to_capture') && Tools::getIsset('id_payment_intent'))
        {
            /* Get transaction details and make sure the token is valid */
            $trans = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripejs_transaction WHERE id_order = '.(int)Tools::getValue('id_order').' AND type = \'payment\' AND status = \'uncaptured\'');
            $ps_amt = $this->isZeroDecimalCurrency($trans['currency']) ? $trans['amount'] : $trans['amount'] / 100;
            if (isset($trans['id_payment_intent']) && $trans['id_payment_intent'] === Tools::getValue('id_payment_intent'))
            {
                if (Tools::getValue('stripe_amount_to_capture') <= number_format($trans['amount'], 2, '.', '')){
                    $processCapture = $this->processCapture($trans['id_payment_intent'], $trans['currency'], (float)Tools::getValue('stripe_amount_to_capture'));
                    if ($processCapture && $order->getCurrentState() != Configuration::get('STRIPE_PAYMENT_ORDER_STATUS'))
                        $order->setCurrentState((int)Configuration::get('STRIPE_PAYMENT_ORDER_STATUS'));
                } else
                    $this->_errors['stripe_capture_error'] = $this->l('You cannot capture more than').' '.$trans['currency'].$ps_amt.' '.$this->l('on this order');

                $stripeCaptured = (isset($this->_errors['stripe_capture_error'])?$this->_errors['stripe_capture_error']:1);
                if(version_compare(_PS_VERSION_, '1.7.5', '>')) {
                Tools::redirect($this->context->link->getLegacyAdminLink('AdminOrders',true, array('id_order'=>$order->id,'vieworder'=>1,'stripeCaptured'=>$stripeCaptured)));
               } else {
                  Tools::redirect($this->context->link->getAdminLink('AdminOrders',true, array(), array('id_order'=>$order->id,'vieworder'=>1,'stripeCaptured'=>$stripeCaptured)));
                }
            }
        }

        /* Check if the order was paid with Stripe and display the transaction details */
        if (Db::getInstance()->getValue('SELECT module FROM '._DB_PREFIX_.'orders WHERE id_order = '.(int)Tools::getValue('id_order')) == $this->name)
        {
            /* Get the transaction details */
            $trans = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'stripejs_transaction WHERE id_order = '.(int)Tools::getValue('id_order').' AND type = "payment"');
            $trans['amount'] = $this->isZeroDecimalCurrency($trans['currency']) ? $trans['amount'] : $trans['amount'] / 100;

            /* Get all the refunds previously made (to build a list and determine if another refund is still possible) */
            $stripe_refunded = 0;
            $stripe_refund_details = Db::getInstance()->ExecuteS('SELECT amount, status, date_add FROM '._DB_PREFIX_.'stripejs_transaction
            WHERE id_order = '.(int)Tools::getValue('id_order').' AND type = "refund" ORDER BY date_add DESC');
            foreach ($stripe_refund_details as $key=>$stripe_refund_detail)
            {
                $refund_amt = $this->isZeroDecimalCurrency($trans['currency']) ? $stripe_refund_detail['amount'] : $stripe_refund_detail['amount'] / 100;
                $stripe_refunded += ($stripe_refund_detail['status'] == 'paid' ?  $refund_amt: 0);
                $stripe_refund_details[$key]['amount']=$refund_amt;
            }
            $currency = new Currency((int)$order->id_currency);
            $c_char = $currency->sign;
            $timeleft = '';$diff='';
            if($trans['status'] == 'uncaptured') {

               $date2 = $trans['date_add'];
               $diff = strtotime($date2 ."+7 days") - strtotime('now');

               $secondsInAMinute = 60;
               $secondsInAnHour  = 60 * $secondsInAMinute;
               $secondsInADay    = 24 * $secondsInAnHour;

              // extract days
              $days = floor($diff / $secondsInADay);
              // extract hours
              $hourSeconds = $diff % $secondsInADay;
              $hours = floor($hourSeconds / $secondsInAnHour);

              $timeleft = $days ." days & ". $hours." hrs";
            }

            $tplVars = array(
            'trans' => $trans,
            'timeleft' => $timeleft,
            'currency' => $c_char,
            'diff' => $diff,
            'c_char' => $c_char,
            'is_ps_177' => ((version_compare(_PS_VERSION_, '1.7.7') >= 0) ?1:0),
            'isZeroDecimalCurrency' => $this->isZeroDecimalCurrency($trans['currency']),
            'stripe_refunded' => $stripe_refunded,
            'stripe_refund_details' => $stripe_refund_details,
            );

            $this->context->smarty->assign($tplVars);
            return $this->display(__FILE__, 'views/templates/admin/orders.tpl');
        }
      }
      return '';
   }

   public function cancelPaymentIntent($id_payment_intent)
    {
        try
        {
            $intent = \Stripe\PaymentIntent::retrieve($id_payment_intent);
            $intent->cancel();

        } catch (Exception $e) {
            $this->_errors['stripe_capture_error'] = $e->getMessage();
        }

        if(isset($intent) && $intent->status=='canceled'){

           Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'stripejs_transaction SET `status` = "canceled" WHERE `id_payment_intent` = "'. pSQL($id_payment_intent).'"');
            return true;
           }
        return false;
    }

    public function processCapture($id_payment_intent, $currency, $amount)
    {
        try
        {
            $amount = $this->isZeroDecimalCurrency($currency) ? $amount : $amount*100;
            $intent = \Stripe\PaymentIntent::retrieve($id_payment_intent);
            $intent->capture(array('amount_to_capture'=>$amount));
            $result_json = $intent->charges->data[0];

        } catch (Exception $e) {
            $this->_errors['stripe_capture_error'] = $e->getMessage();
        }

        if(isset($intent) && !isset($this->_errors['stripe_capture_error']) && $intent->status=='succeeded'){
           $fee = 0;
           if(!empty($result_json->balance_transaction)) {
            $charge = \Stripe\BalanceTransaction::retrieve($result_json->balance_transaction);
            $fee = $charge->fee;
           }

           Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'stripejs_transaction SET `status` = "paid", `amount` = '.(int)$amount.', `fee` = '.(int)$fee.' WHERE `id_payment_intent` = "'. pSQL($id_payment_intent).'"');
            return true;
           }
        return false;
    }

    public function processRefund($id_transaction_stripe,$currency, $amount, $original_transaction)
    {
        try
        {
            $amount = (int)$this->isZeroDecimalCurrency($currency) ? $amount : $amount*100;
            \Stripe\Refund::create(array('charge' => $id_transaction_stripe,'amount' => $amount));

        } catch (Exception $e) {
            $this->_errors['stripe_refund_error'] = $e->getMessage();
        }

        if(!isset($this->_errors['stripe_refund_error'])) {
          Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'stripejs_transaction (type, source, source_type, id_customer, id_order,
          id_transaction, amount, status, currency, cc_type, cc_exp, cc_last_digits, fee, mode, date_add)
          VALUES ("refund","'.pSQL($original_transaction['source']).'","'.pSQL($original_transaction['source_type']).'", '.(int)$original_transaction['id_customer'].', '.(int)$original_transaction['id_order'].', "'.pSQL($id_transaction_stripe).'","'.(int)$amount.'", "'.(!isset($this->_errors['stripe_refund_error']) ? 'paid' : 'pending').'", "'.pSQL($currency).'","", "", 0, 0, "'.(Configuration::get('STRIPE_MODES') ? 'live' : 'test').'", NOW())');
             return true;
           }
        return false;
    }

    public function checkOPCModules()
    {
        $is_opc_module  = false;
        $opc_modules = array('onepagecheckout','ets_onepagecheckout','onepagecheckoutps','thecheckout','supercheckout','xtocheckout','sequrapayment','esp_1stepcheckout','advancedcheckout','steasycheckout');
        foreach($opc_modules as $opc) {
          if (Module::isInstalled($opc)) {
              $module = Module::getInstanceByName($opc);
              if (Validate::isLoadedObject($module) && $module->active) {
                      $is_opc_module = true;
                      break;
              }
          }
        }
        return $is_opc_module;
    }
}
