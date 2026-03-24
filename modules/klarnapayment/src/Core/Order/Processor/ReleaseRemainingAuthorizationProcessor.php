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

namespace KlarnaPayment\Module\Core\Order\Processor;

use KlarnaPayment\Module\Api\Models\Order;
use KlarnaPayment\Module\Core\Order\Action\ReleaseRemainingAuthorizationAction;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotReleaseRemainingAuthorization;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotVerifyOrderAction;
use KlarnaPayment\Module\Core\Order\Verification\CanReleaseRemainingAuthorization;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ReleaseRemainingAuthorizationProcessor
{
    private $canReleaseRemainingAuthorization;
    private $releaseRemainingAuthorizationAction;

    public function __construct(
        CanReleaseRemainingAuthorization $canReleaseRemainingAuthorization,
        ReleaseRemainingAuthorizationAction $releaseRemainingAuthorizationAction
    ) {
        $this->canReleaseRemainingAuthorization = $canReleaseRemainingAuthorization;
        $this->releaseRemainingAuthorizationAction = $releaseRemainingAuthorizationAction;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function processAction(Order $externalOrder): void
    {
        try {
            $this->canReleaseRemainingAuthorization->verify($externalOrder);
        } catch (CouldNotVerifyOrderAction $exception) {
            throw CouldNotReleaseRemainingAuthorization::verificationFailed($exception);
        }

        $this->releaseRemainingAuthorizationAction->run($externalOrder->getOrderId());
    }
}
