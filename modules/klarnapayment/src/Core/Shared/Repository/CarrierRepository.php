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

namespace KlarnaPayment\Module\Core\Shared\Repository;

use Context;
use KlarnaPayment\Module\Infrastructure\Repository\CollectionRepository;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CarrierRepository extends CollectionRepository implements CarrierRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(\Carrier::class);
    }

    /**
     * Get all active carriers formatted for Klarna shipping options
     *
     * @return array
     */
    public function getShippingOptions(): array
    {
        return \Carrier::getCarriers(
            (int) Context::getContext()->language->id,
            true,
            false,
            false,
            null,
            \Carrier::ALL_CARRIERS
        );
    }
}
