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

namespace KlarnaPayment\Module\Core\Order\Verification;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OrderStatusMappingValidator
{
    public function hasDuplicateMappings(array $statusIds): bool
    {
        return count($statusIds) !== count(array_unique($statusIds));
    }
}
