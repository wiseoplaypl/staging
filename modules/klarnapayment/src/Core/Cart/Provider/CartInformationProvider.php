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

namespace KlarnaPayment\Module\Core\Cart\Provider;

use KlarnaPayment\Module\Core\Cart\Exception\CouldNotGetCartInformation;
use KlarnaPayment\Module\Core\Cart\Provider\DTO\KlarnaExpressCheckoutCartData;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;
use Validate;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CartInformationProvider
{
    /** @var Context */
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @param \Cart|null $cart
     *
     * @return KlarnaExpressCheckoutCartData
     *
     * @throws CouldNotGetCartInformation
     */
    public function get(?\Cart $cart = null): KlarnaExpressCheckoutCartData
    {
        try {
            if ($cart === null) {
                $cart = $this->context->getCart();
            }

            if (!Validate::isLoadedObject($cart)) {
                throw CouldNotGetCartInformation::cartNotFound();
            }

            $lineItems = array_map(function ($product) {
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
            }, $cart->getProducts(true));

            return KlarnaExpressCheckoutCartData::create(
                (int) NumberUtility::multiplyByFloat($cart->getOrderTotal(true), 100),
                $lineItems
            );
        } catch (\Throwable $exception) {
            throw CouldNotGetCartInformation::failedToGetCartInformation($exception);
        }
    }
}
