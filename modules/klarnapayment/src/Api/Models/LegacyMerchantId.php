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

class LegacyMerchantId implements \JsonSerializable
{
    private $legacyMerchantId = null;

    public function getMerchantId(): ?string
    {
        return $this->legacyMerchantId;
    }

    /**
     * @maps legacy_merchant_id
     */
    public function setMerchantId(string $legacyMerchantId): void
    {
        $this->legacyMerchantId = $legacyMerchantId;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['merchant_type'] = $this->getMerchantId();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
