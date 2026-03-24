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

class stripejsManageCardsModuleFrontController extends ModuleFrontControllerCore
{
    public function initContent()
    {
        parent::initContent();

        if (!$this->module->active)
            return;

		if(Tools::isSubmit('SubmitCancelCard') && Tools::getValue('stripe_source')!='' && Tools::getValue('stripe_cus')!=''){

			$stripe_source = Tools::getValue('stripe_source');
			$stripe_cus = Tools::getValue('stripe_cus');
			try {
				if(Tools::substr($stripe_source,0,3)=='pm_') {
				  $result = \Stripe\PaymentMethod::retrieve($stripe_source);
				  $result->detach();
				} else
			      $result = \Stripe\Customer::deleteSource($stripe_cus,$stripe_source);
				if((isset($result->id) && Tools::substr($result->id,0,3)=='pm_') || (isset($result->deleted) && $result->deleted)) {
				  @Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'stripejs_transaction SET card_reuse=0 where id_stripe_transaction = '.(int)Tools::getValue('SubmitCancelCard'));
				  $this->context->smarty->assign('confirmation',1);
			}
		    } catch (Exception $e) {
     		  $this->errors[] = $e->getMessage();
			}

        }

		if (Configuration::get('PS_SSL_ENABLED')) {
            $domain = Tools::getShopDomainSsl(true);
        } else {
            $domain = Tools::getShopDomain(true);
        }

		$STRIPE_MODES = (Configuration::get('STRIPE_MODES') ? 'live' : 'test');
		$stripeTokens = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'stripejs_transaction WHERE mode="'.$STRIPE_MODES.'" && `type`="payment" && stripe_cus_id != "" && `source_type` IN ("card","prbutton") && status IN ("paid","uncaptured") && cc_type!="" && card_reuse=1 && id_customer = '.(int)$this->context->customer->id.' group by `source`');


        $this->context->smarty->assign(array(
            'cards' => $stripeTokens,
			'baseDir' => $domain.__PS_BASE_URI__,
        ));

        $this->setTemplate('module:stripejs/views/templates/front/managecards.tpl');
    }
}
