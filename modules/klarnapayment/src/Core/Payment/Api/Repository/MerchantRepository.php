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

namespace KlarnaPayment\Module\Core\Payment\Api\Repository;

use KlarnaPayment\Module\Api\ApiClient;
use KlarnaPayment\Module\Api\Responses\CreateMerchantCallResponse;
use KlarnaPayment\Module\Infrastructure\Api\ApiCaller;
use KlarnaPayment\Module\Infrastructure\Factory\ApiClientFactoryInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class MerchantRepository implements MerchantRepositoryInterface
{
    /**
     * @var \KlarnaPayment\Module\Api\ApiClient
     */
    private $apiClient;
    /**
     * @var ApiCaller
     */
    private $apiCaller;
    /**
     * @var ApiClientFactoryInterface
     */
    private $apiClientFactory;
    private $baseUrl = '';

    public function __construct(ApiClientFactoryInterface $apiClientFactory, ApiCaller $apiCaller)
    {
        $this->apiClientFactory = $apiClientFactory;
        $this->apiCaller = $apiCaller;
    }

    public function setApiClient(ApiClient $apiClient): void
    {
        $this->apiClient = $apiClient;
    }

    public function createMerchantRequest(): ?CreateMerchantCallResponse
    {
        /** @var ?CreateMerchantCallResponse $result */
        $result = $this->apiCaller->getResult(function () {
            return $this->apiClient()->getMerchantManagementApi()->requestMerchantType();
        });

        return $result;
    }

    private function apiClient(): ApiClient
    {
        if ($this->apiClient === null) {
            $this->apiClient = $this->apiClientFactory->create();
        }

        return $this->apiClient;
    }
}
