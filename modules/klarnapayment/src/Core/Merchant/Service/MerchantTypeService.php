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

namespace KlarnaPayment\Module\Core\Merchant\Service;

use KlarnaPayment\Module\Api\Models\MerchantGrantedPermissions;
use KlarnaPayment\Module\Api\Responses\CreateMerchantCallResponse;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\Enum\MerchantType;
use KlarnaPayment\Module\Core\Merchant\Exception\MerchantTypeSaveException;
use KlarnaPayment\Module\Core\Merchant\Provider\RegionProvider;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Provider\ApplicationContextProvider;

if (!defined('_PS_VERSION_')) {
    exit;
}

class MerchantTypeService
{
    /**
     * @var Configuration
     */
    private $configuration;
    /**
     * @var ApplicationContextProvider
     */
    private $applicationContextProvider;
    /**
     * @var RegionProvider
     */
    private $regionProvider;

    public function __construct(Configuration $configuration, ApplicationContextProvider $applicationContextProvider, RegionProvider $regionProvider)
    {
        $this->configuration = $configuration;
        $this->applicationContextProvider = $applicationContextProvider;
        $this->regionProvider = $regionProvider;
    }

    /**
     * @param CreateMerchantCallResponse $result
     * @param string $environment
     * @param string $region
     *
     * @return bool
     *
     * @throws MerchantTypeSaveException
     */
    public function saveMerchantType(CreateMerchantCallResponse $result, string $environment, string $region): bool
    {
        $permissions = $result->getGrantedPermissions()[0];

        if (empty($permissions)) {
            throw MerchantTypeSaveException::noPermissions();
        }

        $isMerchantDirect = $this->isDirectByPermissions($permissions);
        $merchantType = $isMerchantDirect ? MerchantType::DIRECT : MerchantType::PARTNER;

        $this->configuration->set(Config::KLARNA_PAYMENT_MERCHANT_TYPE[$environment][$region], $merchantType);

        return true;
    }

    /**
     * Gets the merchant type for a given environment and region.
     *
     * @param ?string $environment
     * @param ?string $region
     *
     * @return string|null
     */
    public function getMerchantType(?string $environment = null, ?string $region = null): ?string
    {
        if (null === $environment) {
            $environment = $this->applicationContextProvider->refresh()->get()->getIsProduction() ? Config::KLARNA_ENVIRONMENT_PRODUCTION : Config::KLARNA_ENVIRONMENT_SANDBOX;
        }

        if (null === $region) {
            $region = $this->regionProvider->getIso();
        }

        return $this->configuration->get(Config::KLARNA_PAYMENT_MERCHANT_TYPE[$environment][$region]);
    }

    public function fallbackMerchantType(bool $isDirect = true): void
    {
        $this->configuration->set(Config::KLARNA_PAYMENT_MERCHANT_DIRECT_KEY, $isDirect ? 1 : 0);
        $this->configuration->set(Config::KLARNA_PAYMENT_MERCHANT_PARTNER_KEY, $isDirect ? 0 : 1);
    }

    /**
     * Gets whether the merchant type is direct for the current environment and region.
     *
     * @return bool
     */
    public function isDirect(): bool
    {
        $environment = $this->applicationContextProvider->refresh()->get()->getIsProduction() ? 'production' : 'sandbox';
        $region = $this->regionProvider->getIso();
        $merchantType = $this->getMerchantType($environment, $region);
        if ($merchantType === null) {
            return true;
        }

        return $merchantType === MerchantType::DIRECT;
    }

    private function isDirectByPermissions(MerchantGrantedPermissions $permissions): bool
    {
        return (bool) ($permissions->getProduct() && $permissions->getOperation() === '*');
    }
}
