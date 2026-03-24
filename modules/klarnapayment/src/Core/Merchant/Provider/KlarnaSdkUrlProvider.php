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

namespace KlarnaPayment\Module\Core\Merchant\Provider;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\Enum\MerchantType;
use KlarnaPayment\Module\Core\Merchant\Service\MerchantTypeService;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaSdkUrlProvider
{
    /** @var MerchantTypeService */
    private $merchantTypeService;

    public function __construct(MerchantTypeService $merchantTypeService)
    {
        $this->merchantTypeService = $merchantTypeService;
    }

    /**
     * Get the appropriate Klarna SDK URL based on merchant type, environment, and region
     *
     * @param string $environment
     * @param string $region
     *
     * @return string The SDK URL for the merchant type
     */
    public function get(string $environment, string $region): string
    {
        $merchantType = $this->merchantTypeService->getMerchantType($environment, $region);

        return $merchantType === MerchantType::DIRECT ? Config::KLARNA_WEB_SDK_URL_V1 : Config::KLARNA_WEB_SDK_URL_V2;
    }
}
