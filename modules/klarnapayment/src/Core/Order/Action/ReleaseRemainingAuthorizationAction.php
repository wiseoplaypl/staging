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
use KlarnaPayment\Module\Api\Requests\ReleaseRemainingAuthorizationRequest;
use KlarnaPayment\Module\Core\Order\Api\Repository\OrderApiRepositoryInterface;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotReleaseRemainingAuthorization;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ReleaseRemainingAuthorizationAction
{
    private $logger;
    private $orderApiRepository;

    public function __construct(
        LoggerInterface $logger,
        OrderApiRepositoryInterface $orderApiRepository
    ) {
        $this->logger = $logger;
        $this->orderApiRepository = $orderApiRepository;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function run(string $orderId): void
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $request = new ReleaseRemainingAuthorizationRequest();
            $request->setOrderId($orderId);

            $this->orderApiRepository->releaseRemainingAuthorization($request);
        } catch (ApiException $exception) {
            throw CouldNotReleaseRemainingAuthorization::failedToGetSuccessfulApiResponse($exception);
        } catch (\Exception $exception) {
            throw CouldNotReleaseRemainingAuthorization::unknownError($exception);
        } catch (\Throwable $exception) {
            throw CouldNotReleaseRemainingAuthorization::failedToCreateApiRequest($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));
    }
}
