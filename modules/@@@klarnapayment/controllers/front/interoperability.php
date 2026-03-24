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

use KlarnaPayment\Module\Infrastructure\Controller\AbstractFrontController;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Request\Request;
use KlarnaPayment\Module\Infrastructure\Response\JsonResponse;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;
use Rakit\Validation\Validator;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaPaymentInteroperabilityModuleFrontController extends AbstractFrontController
{
    public const FILE_NAME = 'interoperability';

    public function postProcess()
    {
        $this->checkApplicationCredentials();

        parent::postProcess();
    }

    public function displayAjaxUpdateKlarnaInteroperabilityToken(): void
    {
        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        $request = Request::createFromGlobalVars();

        $validation = (new Validator())->make($request->all(), [
            'klarna_interoperability_token' => 'required',
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $this->ajaxResponse(JsonResponse::error(
                $validation->errors()->toArray(),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            ));
        }

        $klarnaInteroperabilityToken = $request->get('klarna_interoperability_token');

        try {
            /* interoperability.js event does not have a order state */
            Hook::exec('actionKlarnaInteroperabilityTokenUpdate', [
                'klarna_interoperability_token' => $klarnaInteroperabilityToken,
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

        $logger->debug(sprintf('%s - Controller action ended', self::FILE_NAME));

        $this->ajaxResponse(JsonResponse::success([]));
    }
}
