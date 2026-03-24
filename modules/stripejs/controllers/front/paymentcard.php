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

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class stripejsPaymentCardModuleFrontController extends ModuleFrontControllerCore
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

        if (!$this->module->active || !$this->context->cart->nbProducts() || !$this->context->cart->id_address_invoice) {
            return;
        }

       $paymentOptionsFinder    = new PaymentOptionsFinder();
       $payment_options         = $paymentOptionsFinder->present();
        if (!$payment_options) {
            return false;
        }

        $this->context->smarty->assign(array(
            'name_module' => 'stripejs',
            'payment_options' => $payment_options['stripejs'],
            'language' => (array)$this->context->language
        ));

        $this->setTemplate('module:stripejs/views/templates/front/paymentcard.tpl');
    }
}
