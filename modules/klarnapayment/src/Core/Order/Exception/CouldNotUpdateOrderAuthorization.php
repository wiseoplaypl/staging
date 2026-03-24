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

namespace KlarnaPayment\Module\Core\Order\Exception;

use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotUpdateOrderAuthorization extends KlarnaPaymentException
{
    public static function failedToFindOrder(int $orderId): self
    {
        return new self(
            sprintf('Failed to find Klarna payment order with ID: %d', $orderId),
            ExceptionCode::ORDER_NOT_FOUND,
            null,
            [
                'order_id' => $orderId,
            ]
        );
    }

    public static function failedToUpdateAuthorization(int $orderId, \Throwable $exception): self
    {
        return new self(
            sprintf('Failed to update Klarna order authorization for order ID: %d', $orderId),
            ExceptionCode::ORDER_UPDATE_AUTHORIZATION_FAILED,
            $exception,
            [
                'order_id' => $orderId,
            ]
        );
    }
}
