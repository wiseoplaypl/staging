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

namespace KlarnaPayment\Module\Core\Order\Action;

use KlarnaPayment\Module\Core\Order\DTO\UpdateAuthorizationRequestData;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotUpdateOrderAuthorization;
use KlarnaPayment\Module\Core\Order\Repository\KlarnaPaymentOrderRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdateOrderAuthorizationAction
{
    private $logger;
    private $klarnaPaymentOrderRepository;
    private $globalShopContext;
    private $updateAuthorizationAction;

    public function __construct(
        LoggerInterface $logger,
        KlarnaPaymentOrderRepositoryInterface $klarnaPaymentOrderRepository,
        GlobalShopContextInterface $globalShopContext,
        UpdateAuthorizationAction $updateAuthorizationAction
    ) {
        $this->logger = $logger;
        $this->klarnaPaymentOrderRepository = $klarnaPaymentOrderRepository;
        $this->globalShopContext = $globalShopContext;
        $this->updateAuthorizationAction = $updateAuthorizationAction;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function run(int $orderId, int $orderAmount, ?string $description = null, ?array $orderLines = null): void
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        /** @var \KlarnaPaymentOrder|null $klarnaPaymentOrder */
        $klarnaPaymentOrder = $this->klarnaPaymentOrderRepository->findOneBy([
            'id_internal' => $orderId,
            'id_shop' => $this->globalShopContext->getShopId(),
        ]);

        if (!$klarnaPaymentOrder) {
            throw CouldNotUpdateOrderAuthorization::failedToFindOrder($orderId);
        }

        try {
            $updateAuthorizationData = UpdateAuthorizationRequestData::create(
                $klarnaPaymentOrder->id_external,
                $orderAmount,
                $description,
                $orderLines
            );

            $this->updateAuthorizationAction->run($updateAuthorizationData);
        } catch (\Throwable $exception) {
            throw CouldNotUpdateOrderAuthorization::failedToUpdateAuthorization($orderId, $exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));
    }
}
