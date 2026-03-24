<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 *
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 */

namespace KlarnaPayment\Module\Presentation\Presenter;

use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Presentation\Presenter\Verification\CanPresentKlarnaExpressCheckout;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaExpressCheckoutPresenter
{
    /** @var Context */
    private $context;
    /** @var \KlarnaPayment */
    private $module;
    /** @var CanPresentKlarnaExpressCheckout */
    private $canPresentKlarnaExpressCheckout;

    public function __construct(
        Context $context,
        ModuleFactory $moduleFactory,
        CanPresentKlarnaExpressCheckout $canPresentKlarnaExpressCheckout
    ) {
        $this->context = $context;
        $this->module = $moduleFactory->getModule();
        $this->canPresentKlarnaExpressCheckout = $canPresentKlarnaExpressCheckout;
    }

    public function present(): string
    {
        if (!$this->canPresentKlarnaExpressCheckout->verify(get_class($this->context->getController()))) {
            return '';
        }

        return $this->module->display($this->module->getLocalPath(), 'views/templates/front/hook/klarna_express_checkout.tpl');
    }
}
