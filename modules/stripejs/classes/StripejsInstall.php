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

include_once(dirname(__FILE__).'/StripejsWebhook.php');

class StripejsInstall extends StripeJs
{

   public function installDb()
    {
        return Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'stripejs_transaction` (`id_stripe_transaction` int(11) NOT NULL AUTO_INCREMENT,
            `type` enum("payment","refund") NOT NULL DEFAULT "payment",
            `source` varchar(120),
            `source_type` varchar(32) NOT NULL DEFAULT "card",
            `id_payment_intent` VARCHAR(100),
            `client_secret` varchar(120),
            `stripe_cus_id` VARCHAR(120),
            `id_customer` int(10),
            `id_cart` int(10),
            `id_order` int(10),
            `id_transaction` varchar(32),
            `amount` int(11),
            `status` enum("paid","pending","processing","uncaptured","failed","canceled") NOT NULL DEFAULT "pending",
            `currency` varchar(3),
            `cc_type` varchar(16), `cc_exp` varchar(10), `cc_last_digits` varchar(4),
            `line1_check` tinyint(1) NOT NULL DEFAULT "0", `zip_check` tinyint(1) NOT NULL DEFAULT "0", `cvc_check` tinyint(1) NOT NULL DEFAULT "0",
            `three_d_secure` tinyint(1) NOT NULL DEFAULT "0",`risk_score` tinyint(3) NOT NULL DEFAULT "0",
            `risk_level` varchar(16),`card_reuse` tinyint(1) NOT NULL DEFAULT "0",
            `fee` int(11),
            `mode` enum("live","test"),
            `date_add` datetime,
            PRIMARY KEY (`id_stripe_transaction`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');
    }

   public function createOS()
    {
	    if (!Configuration::get('STRIPE_AWAITING_OS')
            || !Validate::isLoadedObject(new OrderState((int)Configuration::get('STRIPE_AWAITING_OS')))) {
            $order_state = new OrderState();
            $order_state->name = array();
            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $order_state->name[$language['id_lang']] = 'En attente de paiement Stripe';
                } else {
                    $order_state->name[$language['id_lang']] = 'Awaiting Stripe payment';
                }
            }
            $order_state->send_email = false;
            $order_state->color = '#5469d4';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            $order_state->paid = false;
            if ($order_state->add()) {
                $source = _PS_MODULE_DIR_.'stripejs/views/img/stripe-icon.gif';
                $destination = _PS_ROOT_DIR_.'/img/os/'.(int) $order_state->id.'.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('STRIPE_AWAITING_OS', (int) $order_state->id);
		}

		if (!Configuration::get('STRIPE_PARTIALLY_REFUNDED_OS')
            || !Validate::isLoadedObject(new OrderState((int)Configuration::get('STRIPE_PARTIALLY_REFUNDED_OS')))) {
            $order_state = new OrderState();
            $order_state->name = array();
            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $order_state->name[$language['id_lang']] = 'Stripe partiellement remboursé';
                } else {
                    $order_state->name[$language['id_lang']] = 'Stripe partially refunded';
                }
            }
            $order_state->send_email = false;
            $order_state->color = '#ff7590';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            if ($order_state->add()) {
                $source = _PS_MODULE_DIR_.'stripejs/views/img/stripe-icon.gif';
                $destination = _PS_ROOT_DIR_.'/img/os/'.(int) $order_state->id.'.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('STRIPE_PARTIALLY_REFUNDED_OS', (int) $order_state->id);
		}
        return true;
    }

    public function addWebhook()
    {
        if (!Configuration::get('STRIPEJS_WEBHOOK_SIG_'.(int)Configuration::get('STRIPE_MODES'))
            || Configuration::get('STRIPEJS_WEBHOOK_SIG_'.(int)Configuration::get('STRIPE_MODES')) == ''
            && StripejsWebhook::countWebhooksList() < 16) {
            $webhooksList = StripejsWebhook::getWebhookList();

            foreach ($webhooksList as $webhookEndpoint) {
                if ($webhookEndpoint->url == $this->context->link->getModuleLink('stripejs', 'webhook', array('ajax'=>true), true)) {
                    $webhookEndpoint->delete();
                }
            }
            StripejsWebhook::create();
        }
        return true;
    }

    public function deleteWebhook()
    {
        if (!empty(Configuration::get('STRIPEJS_WEBHOOK_SIG_'.(int)Configuration::get('STRIPE_MODES'))) && StripejsWebhook::countWebhooksList() > 0) {
            $webhooksList = StripejsWebhook::getWebhookList();

            foreach ($webhooksList as $webhookEndpoint) {
                if ($webhookEndpoint->url == $this->context->link->getModuleLink('stripejs', 'webhook', array('ajax'=>true), true)) {
                    $webhookEndpoint->delete();
                }
            }
        }
        return true;
    }

    public function addAppleDomainAssociation()
    {
      if(Tools::getShopDomainSsl()=='localhost')
      return false;

      if (!is_dir(_PS_ROOT_DIR_.'/.well-known')) {
          if (!mkdir(_PS_ROOT_DIR_.'/.well-known')) {
              return $this->l('Unable to create directory for adding certificate.');
          }
      }

      $domain_file = _PS_ROOT_DIR_.'/.well-known/apple-developer-merchantid-domain-association';
      if (!file_exists($domain_file)) {
  		  if (!$this->copyAppleDomainFile()) {
  			  return $this->l('Your host does not authorize us to add your domain to use ApplePay. To add your domain manually please follow the subject "Add my domain ApplePay manually from my dashboard" which is located in the tab F.A.Q of the module.');
  		  } else {

  			  \Stripe\Stripe::setApiKey(Configuration::get('STRIPE_PRIVATE_KEY_LIVE'));
  			  \Stripe\ApplePayDomain::create(array('domain_name' => $this->context->shop->domain));

  			  $curl = curl_init(Tools::getShopDomainSsl(true, true).'/.well-known/apple-developer-merchantid-domain-association');
  			  curl_setopt($curl, CURLOPT_FAILONERROR, true);
  			  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
  			  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  			  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  			  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  			  $result = curl_exec($curl);
  			  $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  			  curl_close($curl);

  			  if ($httpcode != 200 || !$result) {
  				  return $this->l('The configurations has been saved, however your host does not authorize us to add your domain to use ApplePay. To add your domain manually please follow the subject "Add my domain ApplePay manually from my dashboard in order to use ApplePay" which is located in the tab F.A.Q of the module.');
  			  }
  		  }
      }

		return true;
    }

	public function copyAppleDomainFile()
    {
        if (!Tools::copy(_PS_MODULE_DIR_.'stripejs/apple-developer-merchantid-domain-association', _PS_ROOT_DIR_.'/.well-known/apple-developer-merchantid-domain-association')) {
            return false;
        } else {
            return true;
        }
    }

	public function setCorrectFilePers($dir, $dirPermissions, $filePermissions) {
      $dp = opendir($dir);
       while($file = readdir($dp)) {
         if (($file == ".") || ($file == ".."))
            continue;

        $fullPath = $dir."/".$file;

         if(is_dir($fullPath)) {
            chmod($fullPath, $dirPermissions);
            $this->setCorrectFilePers($fullPath, $dirPermissions, $filePermissions);
         } else {
            chmod($fullPath, $filePermissions);
         }
       }
     closedir($dp);
   }
}
