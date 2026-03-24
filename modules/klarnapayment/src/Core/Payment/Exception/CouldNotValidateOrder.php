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

namespace KlarnaPayment\Module\Core\Payment\Exception;

use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotValidateOrder extends KlarnaPaymentException
{
    public static function failedToFindCustomer(int $customerId, int $shopId): self
    {
        return new static(
            sprintf('Customer %s not found in shop %s', $customerId, $shopId),
            ExceptionCode::PAYMENT_FAILED_TO_FIND_CUSTOMER,
            null,
            [
                'customer_id' => $customerId,
                'shop_id' => $shopId,
                'hint' => sprintf(
                    'Check if the customer exists and its associated with the shop_id %s',
                    $shopId
                ),
            ]
        );
    }
}
