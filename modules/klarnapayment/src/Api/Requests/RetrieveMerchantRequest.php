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

use KlarnaPayment\Module\Api\Models\LegacyMerchantId;
use KlarnaPayment\Module\Api\Models\MerchantGrantedPermissions;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://docs.klarna.com/api/payments/#operation/updateCreditSession
 */
class RetrieveMerchantRequest implements \JsonSerializable, RequestInterface
{
    private $merchantId = '';
    private $grantedPermissions = [];

    /**
     * @maps legacy_merchant_id
     */
    public function setMerchantId(?LegacyMerchantId $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    /**
     * @param MerchantGrantedPermissions|null $grantedPermissions
     *
     * @maps granted_permissions
     */
    public function setGrantedPermissions(?MerchantGrantedPermissions $grantedPermissions)
    {
        $this->grantedPermissions = $grantedPermissions;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['legacy_merchant_id'] = $this->getMerchantId();
        $json['granted_permissions'] = $this->getGrantedPermissions();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }

    private function getMerchantId(): string
    {
        return $this->merchantId;
    }

    private function getGrantedPermissions(): array
    {
        return $this->grantedPermissions;
    }
}
