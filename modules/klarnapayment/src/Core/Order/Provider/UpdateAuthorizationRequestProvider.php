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

namespace KlarnaPayment\Module\Core\Order\Provider;

use KlarnaPayment\Module\Api\Requests\UpdateAuthorizationRequest;
use KlarnaPayment\Module\Core\Order\DTO\UpdateAuthorizationRequestData;

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdateAuthorizationRequestProvider
{
    public function get(UpdateAuthorizationRequestData $data): UpdateAuthorizationRequest
    {
        $request = new UpdateAuthorizationRequest();

        $request->setOrderId($data->getOrderId());
        $request->setOrderAmount($data->getOrderAmount());
        $request->setDescription($data->getDescription());
        $request->setOrderLines($data->getOrderLines());

        return $request;
    }
}
