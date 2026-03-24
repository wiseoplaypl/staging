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

namespace KlarnaPayment\Module\Core\Merchant\Handler;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\Service\MerchantTypeService;
use KlarnaPayment\Module\Core\Payment\Api\Repository\MerchantRepository;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Factory\ApiClientFactory;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class MerchantTypeRequestHandler
{
    /** @var ApiClientFactory */
    private $apiClientFactory;

    /** @var MerchantRepository */
    private $merchantRepository;

    /** @var Configuration */
    private $configuration;

    /** @var LoggerInterface */
    private $logger;

    /** @var MerchantTypeService */
    private $merchantTypeService;

    public function __construct(
        ApiClientFactory $apiClientFactory,
        MerchantRepository $merchantRepository,
        Configuration $configuration,
        LoggerInterface $logger,
        MerchantTypeService $merchantTypeService
    ) {
        $this->apiClientFactory = $apiClientFactory;
        $this->merchantRepository = $merchantRepository;
        $this->configuration = $configuration;
        $this->logger = $logger;
        $this->merchantTypeService = $merchantTypeService;
    }

    public function handle(string $apiKey, string $env, string $region): void
    {
        $apiClient = $this->apiClientFactory->create([
            'apiKey' => $apiKey,
            'customUrl' => Config::KLARNA_PAYMENT_MERCHANT_MANAGEMENT_API_URL[$env],
        ]);

        $this->merchantRepository->setApiClient($apiClient);

        $result = $this->merchantRepository->createMerchantRequest();

        try {
            $this->merchantTypeService->saveMerchantType($result, $env, $region);
        } catch (\Throwable $th) {
            $this->logger->debug(sprintf('%s - Could not save merchant type', __METHOD__), [
                'result' => $result,
            ]);
        }
    }
}
