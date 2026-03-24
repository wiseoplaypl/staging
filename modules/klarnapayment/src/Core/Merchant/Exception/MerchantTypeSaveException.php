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

declare(strict_types=1);

namespace KlarnaPayment\Module\Core\Merchant\Exception;

use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class MerchantTypeSaveException extends KlarnaPaymentException
{
    public static function noPermissions(): self
    {
        return new static('No permissions found', ExceptionCode::MERCHANT_TYPE_SAVE_EXCEPTION);
    }
}
