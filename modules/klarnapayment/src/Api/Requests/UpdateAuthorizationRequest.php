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

namespace KlarnaPayment\Module\Api\Requests;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://docs.klarna.com/api/ordermanagement/#operation/updateAuthorization
 */
class UpdateAuthorizationRequest implements \JsonSerializable, RequestInterface
{
    /** @var string */
    private $orderId;

    /** @var int */
    private $orderAmount;

    /** @var ?string */
    private $description;

    /** @var ?array */
    private $orderLines;

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

    /**
     * @maps order_id
     */
    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @maps order_amount
     */
    public function setOrderAmount(int $orderAmount): void
    {
        $this->orderAmount = $orderAmount;
    }

    /**
     * @maps description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @maps order_lines
     */
    public function setOrderLines(?array $orderLines): void
    {
        $this->orderLines = $orderLines;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['order_amount'] = $this->getOrderAmount();

        if ($this->getDescription() !== null) {
            $json['description'] = $this->getDescription();
        }

        if ($this->getOrderLines() !== null) {
            $json['order_lines'] = $this->getOrderLines();
        }

        return $json;
    }
}
