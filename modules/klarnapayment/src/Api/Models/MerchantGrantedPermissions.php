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

namespace KlarnaPayment\Module\Api\Models;

if (!defined('_PS_VERSION_')) {
    exit;
}

class MerchantGrantedPermissions implements \JsonSerializable
{
    private $product = null;
    private $operation = null;

    /**
     * @maps product
     */
    public function setProduct(string $product): void
    {
        $this->product = $product;
    }

    /**
     * @maps operation
     */
    public function setOperation(string $operation): void
    {
        $this->operation = $operation;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function getOperation(): ?string
    {
        return $this->operation;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['product'] = $this->getProduct();
        $json['operation'] = $this->getOperation();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
