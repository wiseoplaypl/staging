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

namespace KlarnaPayment\Module\Api\Apis;

use KlarnaPayment\Module\Api\ApiClient;
use KlarnaPayment\Module\Api\Exception\ApiException;
use KlarnaPayment\Module\Api\Helper\ApiHelper;
use KlarnaPayment\Module\Api\Http\ApiResponse;
use KlarnaPayment\Module\Api\Http\HttpContext;
use KlarnaPayment\Module\Api\Http\HttpMethod;
use KlarnaPayment\Module\Api\Http\HttpRequest;
use KlarnaPayment\Module\Api\Http\HttpResponse;
use KlarnaPayment\Module\Api\Responses\CreateMerchantCallResponse;
use KlarnaPayment\Vendor\Unirest\Request;

if (!defined('_PS_VERSION_')) {
    exit;
}

class MerchantManagementApi extends BaseApi
{
    /** @var ApiClient */
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @throws \ReflectionException
     * @throws ApiException
     *
     * @return ApiResponse
     */
    public function requestMerchantType(): ApiResponse
    {
        $queryParameters = '/v1/management/credentials/info';

        $queryUrl = ApiHelper::cleanUrl($this->apiClient->getBaseUrl() . $queryParameters);

        $headers = [
            'User-Agent' => $this->apiClient->getUserAgent(),
            'Authorization' => 'Basic ' . $this->apiClient->getApiKey(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $body = '';

        $request = new Request();
        $request->timeout($this->apiClient->getTimeout());

        $httpRequest = new HttpRequest(HttpMethod::GET, $headers, $queryUrl, $body);

        try {
            $response = $request->get($queryUrl, $headers, $body);
        } catch (\KlarnaPayment\Vendor\Unirest\Exception $exception) {
            throw new ApiException(sprintf('Api exception. Exception message: (%s). Exception code: (%s)', $exception->getMessage(), (int) $exception->getCode()));
        }

        $httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $httpContext = new HttpContext($httpRequest, $httpResponse);

        $mapper = $this->getJsonMapper();

        if (!$this->apiClient->isValidResponse($httpResponse)) {
            return ApiResponse::createFromContext(null, $httpContext);
        }

        $deserializedResponse = $mapper->mapClass($response->body, CreateMerchantCallResponse::class);

        return ApiResponse::createFromContext($deserializedResponse, $httpContext);
    }
}
