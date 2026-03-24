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

namespace KlarnaPayment\Module\Core\Cart\Service;

use Cart;
use KlarnaPayment\Module\Core\Cart\Exception\CouldNotUpdateCartShipping;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;
use Validate;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GetShippingOptionsService
{
    /** @var Context */
    private $context;

    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * @return array
     *
     * @throws CouldNotUpdateCartShipping
     */
    public function run(): array
    {
        try {
            /** @var Cart $cart */
            $cart = $this->context->getCart();

            if (!Validate::isLoadedObject($cart)) {
                throw CouldNotUpdateCartShipping::cartNotFound();
            }

            $availableCarriers = $cart->getDeliveryOptionList();

            if (empty($availableCarriers)) {
                return [];
            }

            $shippingOptions = $this->parseShippingOptions($availableCarriers);

            return [
                'shipping_options' => $shippingOptions,
            ];
        } catch (\Throwable $exception) {
            throw CouldNotUpdateCartShipping::failedToUpdateCartShipping($exception);
        }
    }

    private function parseShippingOptions(array $availableCarriers): array
    {
        $shippingOptions = [];

        foreach ($availableCarriers as $id_address => $options) {
            foreach ($options as $key => $option) {
                foreach ($option['carrier_list'] as $carrierId => $carrierData) {
                    $carrier = $carrierData['instance'];
                    $shippingOptions[] = [
                        'amount' => NumberUtility::multiplyBy($option['total_price_with_tax'], 100),
                        'description' => $carrier->delay[$this->context->getLanguageId()],
                        'displayName' => $carrier->name,
                        'shippingOptionReference' => $carrier->id_reference,
                        'shippingType' => 'TO_DOOR',
                    ];
                }
            }
        }

        return $shippingOptions;
    }
}
