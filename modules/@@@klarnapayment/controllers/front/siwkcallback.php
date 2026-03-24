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
if (!defined('_PS_VERSION_')) {
    exit;
}

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\Provider\CredentialsConfigurationKeyProvider;
use KlarnaPayment\Module\Core\Merchant\Provider\Currency\LocaleProvider;
use KlarnaPayment\Module\Core\Merchant\Provider\KlarnaSdkUrlProvider;
use KlarnaPayment\Module\Core\Merchant\Provider\RegionProvider;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Controller\AbstractFrontController;
use KlarnaPayment\Module\Infrastructure\Provider\ApplicationContextProvider;

class KlarnaPaymentSiwkCallbackModuleFrontController extends AbstractFrontController
{
    const FILE_NAME = 'siwkcallback';

    public function initContent()
    {
        parent::initContent();

        /** @var LocaleProvider $localeProvider */
        $localeProvider = $this->module->getService(LocaleProvider::class);

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        /** @var CredentialsConfigurationKeyProvider $credentialsProvider */
        $credentialsProvider = $this->module->getService(CredentialsConfigurationKeyProvider::class);

        /** @var Context $context */
        $context = $this->module->getService(Context::class);

        /** @var KlarnaSdkUrlProvider $sdkUrlProvider */
        $sdkUrlProvider = $this->module->getService(KlarnaSdkUrlProvider::class);

        /** @var ApplicationContextProvider $applicationContextProvider */
        $applicationContextProvider = $this->module->getService(ApplicationContextProvider::class);

        /** @var RegionProvider $regionProvider */
        $regionProvider = $this->module->getService(RegionProvider::class);

        $environment = $applicationContextProvider->refresh()->get()->getIsProduction()
            ? Config::KLARNA_ENVIRONMENT_PRODUCTION : Config::KLARNA_ENVIRONMENT_SANDBOX;

        $region = $regionProvider->getIso();

        $this->context->smarty->assign([
            'klarnapayment' => [
                'signInWithKlarna' => [
                    'clientId' => $configuration->get($credentialsProvider->getClientId($this->context->shop->id)),
                    'locale' => $localeProvider->getLocale(),
                    'authenticationUrl' => $context->getModuleLink($this->module->name, 'authentication'),
                    'redirectUri' => $context->getBaseLink() . Config::SIGN_IN_WITH_KLARNA_CALLBACK_URL_PATH,
                    'sdkUrl' => $sdkUrlProvider->get($environment, $region),
                ],
            ],
        ]);

        $this->setTemplate('module:klarnapayment/views/templates/front/siwk/callback.tpl');
    }
}
