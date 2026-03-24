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

namespace KlarnaPayment\Module\Core\Order\Action;

use KlarnaPayment\Module\Api\Exception\ApiException;
use KlarnaPayment\Module\Core\Order\Api\Repository\OrderApiRepositoryInterface;
use KlarnaPayment\Module\Core\Order\DTO\UpdateAuthorizationRequestData;
use KlarnaPayment\Module\Core\Order\Provider\UpdateAuthorizationRequestProvider;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdateAuthorizationAction
{
    private $logger;
    private $orderApiRepository;
    private $updateAuthorizationRequestProvider;

    public function __construct(
        LoggerInterface $logger,
        OrderApiRepositoryInterface $orderApiRepository,
        UpdateAuthorizationRequestProvider $updateAuthorizationRequestProvider
    ) {
        $this->logger = $logger;
        $this->orderApiRepository = $orderApiRepository;
        $this->updateAuthorizationRequestProvider = $updateAuthorizationRequestProvider;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function run(UpdateAuthorizationRequestData $data): void
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $request = $this->updateAuthorizationRequestProvider->get($data);

            $this->orderApiRepository->updateAuthorization($request);
        } catch (ApiException $exception) {
            throw KlarnaPaymentException::failedToGetSuccessfulApiResponse($exception);
        } catch (\Exception $exception) {
            throw KlarnaPaymentException::unknownError($exception);
        } catch (\Throwable $exception) {
            throw KlarnaPaymentException::failedToCreateApiRequest($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));
    }
}
