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

use KlarnaPayment\Module\Core\Account\DTO\AddProductToCartProcessorData;
use KlarnaPayment\Module\Core\Account\Processor\AddProductToCartProcessor;
use KlarnaPayment\Module\Core\Account\Provider\AddressChecksumProvider;
use KlarnaPayment\Module\Core\Account\Provider\ExpressCheckoutAddressDataProvider;
use KlarnaPayment\Module\Core\Cart\Provider\CartInformationProvider;
use KlarnaPayment\Module\Core\Cart\Service\GetShippingOptionsByAddressService;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Payment\Provider\PaymentSessionProvider;
use KlarnaPayment\Module\Core\Shared\Repository\KlarnaExpressCheckoutRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Controller\AbstractFrontController;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Request\Request;
use KlarnaPayment\Module\Infrastructure\Response\JsonResponse;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;
use Rakit\Validation\Validator;
use Validate;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaPaymentExpressCheckoutModuleFrontController extends AbstractFrontController
{
    public const FILE_NAME = 'expressCheckout';

    public function postProcess()
    {
        if (!$this->isTokenValid()) {
            $this->ajaxResponse(JsonResponse::error(
                $this->module->l('Unauthorized.', self::FILE_NAME),
                JsonResponse::HTTP_UNAUTHORIZED
            ));
        }

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        if (!$configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE)) {
            $this->ajaxResponse(JsonResponse::error(
                $this->module->l('Inactive service', self::FILE_NAME),
                JsonResponse::HTTP_UNAUTHORIZED
            ));
        }

        parent::postProcess();
    }

    public function displayAjaxAddProductToCart(): void
    {
        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        $request = Request::createFromGlobalVars();

        $validation = (new Validator())->make($request->all(), [
            'product_data.product_id' => 'required',
            'product_data.product_attribute_id' => 'required',
            'product_data.customization_id' => 'required',
            'product_data.quantity' => 'required',
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $this->ajaxResponse(JsonResponse::error(
                $validation->errors()->toArray(),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            ));
        }

        /** @var AddProductToCartProcessor $addProductToCartProcessor */
        $addProductToCartProcessor = $this->module->getService(AddProductToCartProcessor::class);

        $productData = $request->get('product_data');

        try {
            $addProductToCartProcessor->run(AddProductToCartProcessorData::create(
                (int) $productData['quantity'],
                (int) $productData['product_id'],
                (int) $productData['product_attribute_id'],
                (int) $productData['customization_id']
            ));
        } catch (\Throwable $exception) {
            $logger->error('Failed to add product to cart.', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            $this->ajaxResponse(JsonResponse::error([
                $this->module->l('Failed to add product to cart.', self::FILE_NAME),
                JsonResponse::HTTP_BAD_REQUEST,
            ]));
        }

        $logger->debug(sprintf('%s - Controller action ended', self::FILE_NAME));

        $this->ajaxResponse(JsonResponse::success([]));
    }

    public function displayAjaxGetAddressData(): void
    {
        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        $request = Request::createFromGlobalVars();

        $validation = (new Validator())->make($request->all(), [
            'country_iso_code' => 'required|alpha',
            'state_iso_code' => 'alpha',
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $this->ajaxResponse(JsonResponse::error(
                $validation->errors()->toArray(),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            ));
        }

        /** @var ExpressCheckoutAddressDataProvider $expressCheckoutAddressDataProvider */
        $expressCheckoutAddressDataProvider = $this->module->getService(ExpressCheckoutAddressDataProvider::class);

        try {
            $result = $expressCheckoutAddressDataProvider->run(
                (string) $request->get('country_iso_code'),
                (string) $request->get('state_iso_code')
            );
        } catch (\Throwable $exception) {
            $logger->error('Failed to retrieve express checkout address data.', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            $this->ajaxResponse(JsonResponse::error([
                $this->module->l('Failed to retrieve express checkout address data.', self::FILE_NAME),
                JsonResponse::HTTP_BAD_REQUEST,
            ]));
        }

        $logger->debug(sprintf('%s - Controller action ended', self::FILE_NAME));

        $this->ajaxResponse(JsonResponse::success([
            'countryId' => $result->getCountryId(),
            'stateId' => $result->getStateId(),
        ]));
    }

    public function displayAjaxGetPayload(): void
    {
        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        /** @var Cart $cart */
        $cart = Context::getContext()->cart;

        /** @var PaymentSessionProvider $paymentSessionProvider */
        $paymentSessionProvider = $this->module->getService(PaymentSessionProvider::class);

        try {
            $response = $paymentSessionProvider->get($cart, true);
            $filteredResponseArray = array_filter(json_decode(json_encode($response), true));
        } catch (\Throwable $exception) {
            $logger->error('Failed to retrieve payload.', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            $this->ajaxResponse(JsonResponse::error([
                $this->module->l('Failed to retrieve express checkout data.', self::FILE_NAME),
                JsonResponse::HTTP_BAD_REQUEST,
            ]));
        }

        $logger->debug(sprintf('%s - Controller action ended', self::FILE_NAME));

        $this->ajaxResponse(JsonResponse::success([
            'payload' => $filteredResponseArray,
        ]));
    }

    public function displayAjaxEnableKecFlow(): void
    {
        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        $request = Request::createFromGlobalVars();

        $validation = (new Validator())->make($request->all(), [
            'client_token' => 'required',
            'shipping_address' => 'required',
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $this->ajaxResponse(JsonResponse::error(
                $validation->errors()->toArray(),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            ));
        }

        /** @var Cart $cart */
        $cart = Context::getContext()->cart;

        try {
            /** @var KlarnaExpressCheckoutRepositoryInterface $klarnaExpressCheckoutRepository */
            $klarnaExpressCheckoutRepository = $this->module->getService(KlarnaExpressCheckoutRepositoryInterface::class);

            /** @var KlarnaExpressCheckout $klarnaExpressCheckout */
            $klarnaExpressCheckout = $klarnaExpressCheckoutRepository->findOneBy([
                'id_cart' => $cart->id,
            ]);

            if (!$klarnaExpressCheckout) {
                $klarnaExpressCheckout = new KlarnaExpressCheckout();
                $klarnaExpressCheckout->id_cart = $cart->id;
            }

            $klarnaExpressCheckout->address_checksum = $this->getAddressChecksum($request->get('shipping_address'));
            $klarnaExpressCheckout->client_token = (string) $request->get('client_token');
            $klarnaExpressCheckout->is_kec = 1;
            $klarnaExpressCheckout->save();
        } catch (\Throwable $exception) {
            $logger->error('Failed to enable KEC flow', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            $this->ajaxResponse(JsonResponse::error([
                $this->module->l('Failed to process express checkout data', self::FILE_NAME),
                JsonResponse::HTTP_BAD_REQUEST,
            ]));
        }

        $logger->debug(sprintf('%s - Controller action ended', self::FILE_NAME));

        $this->ajaxResponse(JsonResponse::success([]));
    }

    private function getAddressChecksum(array $shippingAddress): string
    {
        /** @var ExpressCheckoutAddressDataProvider $expressCheckoutAddressDataProvider */
        $expressCheckoutAddressDataProvider = $this->module->getService(ExpressCheckoutAddressDataProvider::class);
        /** @var AddressChecksumProvider $addressChecksumProvider */
        $addressChecksumProvider = $this->module->getService(AddressChecksumProvider::class);

        $addressData = $expressCheckoutAddressDataProvider->run(
            (string) $shippingAddress['country'],
            (string) ($shippingAddress['region'] ?? '')
        );

        $shippingAddress['country'] = $addressData->getCountryId();
        $shippingAddress['region'] = $addressData->getStateId();

        return $addressChecksumProvider->get($shippingAddress);
    }

    public function displayAjaxGetCartInfo(): void
    {
        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        try {
            /** @var CartInformationProvider $cartInformationProvider */
            $cartInformationProvider = $this->module->getService(CartInformationProvider::class);

            $cartInformation = $cartInformationProvider->get();

            $this->ajaxResponse(JsonResponse::success([
                'cart_data' => [
                    'payment_amount' => $cartInformation->getPaymentAmount(),
                    'line_items' => $cartInformation->getLineItems(),
                ],
            ]));
        } catch (\Throwable $exception) {
            $logger->error('Failed to get cart information.', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            $this->ajaxResponse(JsonResponse::error([
                $this->module->l('Failed to get cart information.', self::FILE_NAME),
                JsonResponse::HTTP_BAD_REQUEST,
            ]));
        }

        $logger->debug(sprintf('%s - Controller action ended', self::FILE_NAME));
    }

    public function displayAjaxUpdateShippingOptionsByAddress(): void
    {
        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        $request = Request::createFromGlobalVars();

        $validation = (new Validator())->make($request->all(), [
            'shipping_address' => 'required|array',
            'shipping_address.streetAddress' => 'required',
            'shipping_address.city' => 'required',
            'shipping_address.postalCode' => 'required',
            'shipping_address.country' => 'required',
            'shipping_address.familyName' => 'required',
            'shipping_address.givenName' => 'required',
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $this->ajaxResponse(JsonResponse::error(
                $validation->errors()->toArray(),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            ));
        }

        try {
            $shippingAddress = $request->get('shipping_address');
            $cart = $this->context->cart;
            $customer = new \Customer((int) $cart->id_customer);

            /** @var GetShippingOptionsByAddressService $getShippingOptionsService */
            $getShippingOptionsService = $this->module->getService(GetShippingOptionsByAddressService::class);
            $cartSummary = $getShippingOptionsService->run($shippingAddress, (int) $customer->id);

            $this->ajaxResponse(JsonResponse::success($cartSummary));
        } catch (\Throwable $exception) {
            $logger->error('Failed to update shipping address.', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            $this->ajaxResponse(JsonResponse::error([
                $this->module->l('Failed to update shipping address.', self::FILE_NAME),
                JsonResponse::HTTP_BAD_REQUEST,
            ]));
        }

        $logger->debug(sprintf('%s - Controller action ended', self::FILE_NAME));
    }

    public function displayAjaxUpdatePaymentAmountByShippingOption(): void
    {
        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        $request = Request::createFromGlobalVars();

        $validation = (new Validator())->make($request->all(), [
            'shipping_option' => 'required|array',
            'shipping_option.id' => 'required',
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $this->ajaxResponse(JsonResponse::error(
                $validation->errors()->toArray(),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            ));
        }

        try {
            $shippingOption = $request->get('shipping_option');
            $cart = $this->context->cart;

            if (!Validate::isLoadedObject($cart)) {
                $this->ajaxResponse(JsonResponse::error(
                    $this->module->l('Cart not found.', self::FILE_NAME),
                    JsonResponse::HTTP_NOT_FOUND
                ));

                return;
            }

            $cart->id_carrier = (int) $shippingOption['id'];
            $cart->update();

            $this->ajaxResponse(JsonResponse::success([
                'payment_amount' => (int) NumberUtility::multiplyByFloat($cart->getOrderTotal(true), 100),
            ]));
        } catch (\Throwable $exception) {
            $logger->error('Failed to update shipping option.', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            $this->ajaxResponse(JsonResponse::error([
                $this->module->l('Failed to update shipping option.', self::FILE_NAME),
                JsonResponse::HTTP_BAD_REQUEST,
            ]));
        }

        $logger->debug(sprintf('%s - Controller action ended', self::FILE_NAME));
    }

    public function displayAjaxUpdateKlarnaInteroperabilityToken(): void
    {
        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        $request = Request::createFromGlobalVars();

        $validation = (new Validator())->make($request->all(), [
            'klarna_interoperability_token' => 'required',
            'klarna_order_state' => 'required',
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $this->ajaxResponse(JsonResponse::error(
                $validation->errors()->toArray(),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            ));
        }

        $klarnaInteroperabilityToken = $request->get('klarna_interoperability_token');
        $klarnaOrderState = $request->get('klarna_order_state');

        try {
            Hook::exec('updateKlarnaInteroperabilityToken', [
                'klarna_interoperability_token' => $klarnaInteroperabilityToken,
                'klarna_order_state' => $klarnaOrderState,
            ]);
        } catch (\Throwable $exception) {
            $logger->error('Failed to update Klarna interoperability token.', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            $this->ajaxResponse(JsonResponse::error([
                $this->module->l('Failed to update Klarna interoperability token.', self::FILE_NAME),
                JsonResponse::HTTP_BAD_REQUEST,
            ]));
        }

        $this->ajaxResponse(JsonResponse::success([]));
    }
}
