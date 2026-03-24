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

namespace KlarnaPayment\Module\Infrastructure\Hook\Action;

use KlarnaPayment\Module\Core\Order\Action\UpdateOrderAuthorizationAction;
use KlarnaPayment\Module\Core\Order\Repository\KlarnaPaymentOrderRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;
use KlarnaPaymentOrder;
use Order;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ActionOrderEditedHook
{
    /** @var LoggerInterface */
    private $logger;

    /** @var UpdateOrderAuthorizationAction */
    private $updateOrderAuthorizationAction;

    /** @var KlarnaPaymentOrderRepositoryInterface */
    private $klarnaPaymentOrderRepository;

    /** @var GlobalShopContextInterface */
    private $globalShopContext;

    public function __construct(
        LoggerInterface $logger,
        UpdateOrderAuthorizationAction $updateOrderAuthorizationAction,
        KlarnaPaymentOrderRepositoryInterface $klarnaPaymentOrderRepository,
        GlobalShopContextInterface $globalShopContext
    ) {
        $this->logger = $logger;
        $this->updateOrderAuthorizationAction = $updateOrderAuthorizationAction;
        $this->klarnaPaymentOrderRepository = $klarnaPaymentOrderRepository;
        $this->globalShopContext = $globalShopContext;
    }

    public function run(Order $order): void
    {
        if ($order->module !== 'klarnapayment') {
            return;
        }

        try {
            $orderAmount = (int) round($order->total_paid_tax_incl * 100);
            $description = 'Order updated from PrestaShop admin';
            $orderLines = $this->getOrderLines($order);

            $this->updateOrderAuthorizationAction->run($order->id, $orderAmount, $description, $orderLines);

            $this->setFailedToUpdateFlag($order->id, false);
        } catch (\Throwable $exception) {
            $this->setFailedToUpdateFlag($order->id, true, $exception);
        }
    }

    private function setFailedToUpdateFlag(int $orderId, bool $flagValue = true, \Throwable $exception = null): void
    {
        /** @var KlarnaPaymentOrder|null $klarnaPaymentOrder */
        $klarnaPaymentOrder = $this->klarnaPaymentOrderRepository->findOneBy([
            'id_internal' => $orderId,
            'id_shop' => $this->globalShopContext->getShopId(),
        ]);

        if (!$klarnaPaymentOrder) {
            $this->logger->error(sprintf('%s - Failed to find order mapping.', __METHOD__), [
                'context' => [
                    'id_internal' => $orderId,
                    'id_shop' => $this->globalShopContext->getShopId(),
                ],
                'exceptions' => [],
            ]);

            return;
        }

        $klarnaPaymentOrder->failed_to_update_klarna = $flagValue;
        $klarnaPaymentOrder->save();

        $this->logger->warning(sprintf('%s - Failed to send order changes to Klarna. Try doing it manually in Klarna Merchant Portal', __METHOD__), [
            'id_order_internal' => $orderId,
            'exceptions' => $exception ? ExceptionUtility::getExceptions($exception) : null,
        ]);
    }

    private function getOrderLines(Order $order): array
    {
        $orderLines = [];
        $orderDetails = $order->getOrderDetailList();

        foreach ($orderDetails as $orderDetail) {
            $unitPrice = (int) round($orderDetail['unit_price_tax_incl'] * 100);
            $totalAmount = (int) round($orderDetail['total_price_tax_incl'] * 100);
            $totalTaxAmount = (int) round(($orderDetail['total_price_tax_incl'] - $orderDetail['total_price_tax_excl']) * 100);
            $taxRate = $this->calculateTaxRate($orderDetail['total_price_tax_incl'], $orderDetail['total_price_tax_excl']);

            $orderLines[] = [
                'name' => $orderDetail['product_name'],
                'quantity' => (int) $orderDetail['product_quantity'],
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'tax_rate' => $taxRate,
                'total_tax_amount' => $totalTaxAmount,
            ];
        }

        $shippingCost = (int) round($order->total_shipping_tax_incl * 100);
        if ($shippingCost > 0) {
            $shippingTaxAmount = (int) round(($order->total_shipping_tax_incl - $order->total_shipping_tax_excl) * 100);
            $shippingTaxRate = $this->calculateTaxRate($order->total_shipping_tax_incl, $order->total_shipping_tax_excl);

            $orderLines[] = [
                'name' => 'Shipping',
                'quantity' => 1,
                'unit_price' => $shippingCost,
                'total_amount' => $shippingCost,
                'tax_rate' => $shippingTaxRate,
                'total_tax_amount' => $shippingTaxAmount,
            ];
        }

        $discountAmount = (int) round($order->total_discounts_tax_incl * 100);
        if ($discountAmount > 0) {
            $discountTaxAmount = (int) round(($order->total_discounts_tax_incl - $order->total_discounts_tax_excl) * 100);
            $discountTaxRate = $this->calculateTaxRate($order->total_discounts_tax_incl, $order->total_discounts_tax_excl);

            $orderLines[] = [
                'name' => 'Discount',
                'quantity' => 1,
                'unit_price' => -$discountAmount,
                'total_amount' => -$discountAmount,
                'tax_rate' => $discountTaxRate,
                'total_tax_amount' => -$discountTaxAmount,
            ];
        }

        return $orderLines;
    }

    private function calculateTaxRate(float $amountInclTax, float $amountExclTax): int
    {
        if ($amountExclTax > 0) {
            return (int) round((($amountInclTax - $amountExclTax) / $amountExclTax) * 10000);
        }

        return 0;
    }
}
