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

use KlarnaPayment\Module\Core\Account\Action\CreateAddressAction;
use KlarnaPayment\Module\Core\Account\DTO\CreateAddressData;
use KlarnaPayment\Module\Core\Cart\Exception\CouldNotUpdateCartShipping;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;
use Validate;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GetShippingOptionsByAddressService
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var CreateAddressAction
     */
    private $createAddressAction;

    public function __construct(
        Context $context,
        CreateAddressAction $createAddressAction
    ) {
        $this->context = $context;
        $this->createAddressAction = $createAddressAction;
    }

    /**
     * @param array $shippingAddress
     * @param int $customerId
     *
     * @return array
     *
     * @throws CouldNotUpdateCartShipping
     */
    public function run(array $shippingAddress, int $customerId): array
    {
        $cart = $this->context->getCart();

        if (!Validate::isLoadedObject($cart)) {
            throw CouldNotUpdateCartShipping::cartNotFound();
        }

        try {
            $addressData = CreateAddressData::create(
                $shippingAddress['givenName'],
                $shippingAddress['familyName'],
                $shippingAddress['streetAddress'],
                $shippingAddress['streetAddress2'] ?? '',
                $shippingAddress['postalCode'],
                $shippingAddress['phone'] ?? '',
                $shippingAddress['city'],
                $shippingAddress['country'],
                $shippingAddress['region'] ?? '',
                $customerId
            );

            $address = $this->createAddressAction->run($addressData);

            $cart->id_address_delivery = $address->id;
            $cart->id_address_invoice = $address->id;
            $cart->update();

            $carriers = $cart->getDeliveryOptionList();
            $shippingOptions = [];

            if (!empty($carriers)) {
                foreach ($carriers as $carrierGroup) {
                    foreach ($carrierGroup as $carrier) {
                        $shippingOptions[] = [
                            'id' => $carrier['id_carrier'],
                            'name' => $carrier['name'],
                            'price' => (int) \Tools::ps_round($carrier['price'] * 100),
                            'tax_amount' => (int) \Tools::ps_round($carrier['price_tax_exc'] * 100),
                            'currency' => $this->context->getCurrency()->iso_code,
                        ];
                    }
                }
            }

            $cartProducts = $cart->getProducts(true);

            $cartSummary = [
                'payment_amount' => (int) NumberUtility::multiplyByFloat($cart->getOrderTotal(true), 100),
                'line_items' => array_map(function ($product) {
                    $quantity = (int) $product['cart_quantity'];
                    $price = (float) $product['price'];
                    $priceWt = (float) $product['price_wt'];

                    return [
                        'name' => $product['name'],
                        'quantity' => $quantity,
                        'totalAmount' => NumberUtility::multiplyBy($price, $quantity),
                        'totalTaxAmount' => NumberUtility::multiplyBy(
                            NumberUtility::minus($priceWt, $price),
                            $quantity,
                            NumberUtility::FLOAT_PRECISION
                        ),
                    ];
                }, $cartProducts),
                'shipping_options' => $shippingOptions,
            ];

            return $cartSummary;
        } catch (\Throwable $exception) {
            throw CouldNotUpdateCartShipping::failedToUpdateCartShipping($exception);
        }
    }
}
