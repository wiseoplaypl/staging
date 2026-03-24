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

namespace KlarnaPayment\Module\Api\Responses;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://developers.klarna.com/api/#payments-api-create-a-new-order
 */
class CreateMerchantCallResponse implements \JsonSerializable, ResponseInterface
{
    private $legacy_merchant_id = '';
    private $granted_permissions = [];

    /**
     * @maps legacy_merchant_id
     */
    public function setMerchantId(string $merchantId): void
    {
        $this->legacy_merchant_id = $merchantId;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\MerchantGrantedPermissions[] $granted_permissions
     *
     * @maps granted_permissions
     */
    public function setGrantedPermissions(array $granted_permissions): void
    {
        $this->granted_permissions = $granted_permissions;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['legacy_merchant_id'] = $this->getLegacyMerchantId();
        $json['granted_permissions'] = $this->getGrantedPermissions();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }

    public function getLegacyMerchantId(): ?string
    {
        return $this->legacy_merchant_id;
    }

    public function getGrantedPermissions(): ?array
    {
        return $this->granted_permissions;
    }
}
