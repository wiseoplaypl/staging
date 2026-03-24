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

namespace KlarnaPayment\Module\Core\Order\DTO;

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdateAuthorizationRequestData
{
    /** @var string */
    private $orderId;
    /** @var int */
    private $orderAmount;
    /** @var string|null */
    private $description;
    /** @var array|null */
    private $orderLines;

    private function __construct(
        string $orderId,
        int $orderAmount,
        ?string $description = null,
        ?array $orderLines = null
    ) {
        $this->orderId = $orderId;
        $this->orderAmount = $orderAmount;
        $this->description = $description;
        $this->orderLines = $orderLines;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getOrderAmount(): int
    {
        return $this->orderAmount;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getOrderLines(): ?array
    {
        return $this->orderLines;
    }

    public static function create(
        string $orderId,
        int $orderAmount,
        ?string $description = null,
        ?array $orderLines = null
    ): self {
        return new self(
            $orderId,
            $orderAmount,
            $description,
            $orderLines
        );
    }
}
