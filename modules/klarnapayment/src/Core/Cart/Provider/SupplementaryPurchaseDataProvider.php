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
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;
use Validate;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SupplementaryPurchaseDataProvider
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
     * @return array
     *
     * @throws CouldNotGetCartInformation
     */
    public function get(?\Cart $cart = null): array
    {
        try {
            if (null === $cart) {
                $cart = $this->context->getCart();
            }

            if (!Validate::isLoadedObject($cart)) {
                throw CouldNotGetCartInformation::cartNotFound();
            }

            $lineItems = $this->getLineItems($cart);

            return [
                'lineItems' => $lineItems,
                'purchaseReference' => (string) $cart->id,
            ];
        } catch (\Throwable $exception) {
            throw CouldNotGetCartInformation::failedToGetCartInformation($exception);
        }
    }

    /**
     * @param \Cart $cart
     *
     * @return array
     */
    private function getLineItems(\Cart $cart): array
    {
        return array_map(function ($product) {
            $quantity = (int) $product['cart_quantity'];
            $price = (float) $product['price'];
            $priceWt = (float) $product['price_wt'];
            $totalAmount = NumberUtility::multiplyBy($price, $quantity);
            $totalTaxAmount = NumberUtility::multiplyBy(
                NumberUtility::minus($priceWt, $price),
                $quantity,
                NumberUtility::FLOAT_PRECISION
            );

            return [
                'currency' => $this->context->getCurrencyIso(),
                'imageUrl' => $this->getProductImageUrl($product),
                'lineItemReference' => $product['reference'] ?? '',
                'name' => $product['name'],
                'productIdentifier' => (string) $product['id_product'],
                'productUrl' => $this->getProductUrl($product),
                'quantity' => $quantity,
                'totalAmount' => (int) NumberUtility::multiplyByFloat($totalAmount, 100),
                'totalTaxAmount' => (int) NumberUtility::multiplyByFloat($totalTaxAmount, 100),
                'unitPrice' => (int) NumberUtility::multiplyByFloat($price, 100),
            ];
        }, $cart->getProducts(true));
    }

    /**
     * @param array $product
     *
     * @return string|null
     */
    private function getProductImageUrl(array $product): ?string
    {
        if (empty($product['id_image'])) {
            return null;
        }

        return $this->context->getBaseLink() . 'img/p/' . $product['id_image'] . '.jpg';
    }

    /**
     * @param array $product
     *
     * @return string|null
     */
    private function getProductUrl(array $product): ?string
    {
        return $this->context->getPageLink('product', null, null, [
            'id_product' => $product['id_product'],
            'id_product_attribute' => $product['id_product_attribute'],
        ]);
    }
}
