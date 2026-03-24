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

namespace KlarnaPayment\Module\Presentation\Loader;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\Provider\CredentialsConfigurationKeyProvider;
use KlarnaPayment\Module\Core\Merchant\Provider\KlarnaSdkUrlProvider;
use KlarnaPayment\Module\Core\Merchant\Provider\RegionProvider;
use KlarnaPayment\Module\Core\Merchant\Service\MerchantTypeService;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Provider\ApplicationContextProvider;
use KlarnaPayment\Module\Infrastructure\Utility\ArrayUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaSdkClientAssetLoader
{
    /** @var Configuration */
    private $configuration;
    /** @var Context */
    private $context;

    /** @var \KlarnaPayment */
    private $module;

    /** @var CredentialsConfigurationKeyProvider */
    private $credentialsConfigurationKeyProvider;

    /** @var MerchantTypeService */
    private $merchantTypeService;

    /** @var KlarnaSdkUrlProvider */
    private $sdkUrlProvider;

    /** @var ApplicationContextProvider */
    private $applicationContextProvider;

    /** @var RegionProvider */
    private $regionProvider;

    public function __construct(
        Configuration $configuration,
        Context $context,
        ModuleFactory $moduleFactory,
        CredentialsConfigurationKeyProvider $credentialsConfigurationKeyProvider,
        MerchantTypeService $merchantTypeService,
        KlarnaSdkUrlProvider $sdkUrlProvider,
        ApplicationContextProvider $applicationContextProvider,
        RegionProvider $regionProvider
    ) {
        $this->configuration = $configuration;
        $this->context = $context;
        $this->module = $moduleFactory->getModule();
        $this->credentialsConfigurationKeyProvider = $credentialsConfigurationKeyProvider;
        $this->merchantTypeService = $merchantTypeService;
        $this->sdkUrlProvider = $sdkUrlProvider;
        $this->applicationContextProvider = $applicationContextProvider;
        $this->regionProvider = $regionProvider;
    }

    public function register(\FrontController $controller): string
    {
        $isOnsiteMessagingEnabled = $this->configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE);
        $isSignInWithKlarnaEnabled = $this->configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ACTIVE);

        $signInWithKlarnaPlacements = explode(
            ',',
            $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS) ?? ''
        );

        $previousKlarnaPaymentSmartyVars = $this->context->getSmarty()->getTemplateVars('klarnapayment') ?? [];

        $environment = $this->applicationContextProvider->refresh()->get()->getIsProduction()
            ? Config::KLARNA_ENVIRONMENT_PRODUCTION : Config::KLARNA_ENVIRONMENT_SANDBOX;
        $region = $this->regionProvider->getIso();

        $this->context->getSmarty()->assign([
            'klarnapayment' => ArrayUtility::recursiveArrayMerge($previousKlarnaPaymentSmartyVars, [
                'sdk_url' => $this->sdkUrlProvider->get($environment, $region),
                'client_id' => $this->configuration->get(
                    $this->credentialsConfigurationKeyProvider->getClientId($this->context->getShopId()),
                    $this->context->getShopId()
                ),
                'client_instance_name' => Config::KLARNA_PAYMENT_CLIENT_INSTANCE_NAME,
            ]),
        ]);

        return $this->module->display($this->module->getLocalPath(), 'views/templates/front/hook/klarna_sdk_script.tpl');
    }
}
