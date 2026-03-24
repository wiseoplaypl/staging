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

namespace KlarnaPayment\Module\Core\Cart\DTO;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CartInformationData
{
    /** @var int */
    private $paymentAmount;

    /** @var array */
    private $lineItems;

    private function __construct(int $paymentAmount, array $lineItems)
    {
        $this->paymentAmount = $paymentAmount;
        $this->lineItems = $lineItems;
    }

    public function getPaymentAmount(): int
    {
        return $this->paymentAmount;
    }

    public function getLineItems(): array
    {
        return $this->lineItems;
    }

    public static function create(int $paymentAmount, array $lineItems): self
    {
        return new self($paymentAmount, $lineItems);
    }
}
