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

namespace KlarnaPayment\Module\Core\Cart\Exception;

use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotUpdateCartShipping extends KlarnaPaymentException
{
    public static function cartNotFound(): self
    {
        return new self(
            'Cart not found.',
            ExceptionCode::CART_NOT_FOUND
        );
    }

    public static function failedToUpdateCartShipping(\Throwable $exception): self
    {
        return new self(
            'Failed to update cart shipping.',
            ExceptionCode::CART_UNKNOWN_ERROR,
            $exception
        );
    }
}
