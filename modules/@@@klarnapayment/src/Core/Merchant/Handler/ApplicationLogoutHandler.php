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

namespace KlarnaPayment\Module\Core\Merchant\Handler;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ApplicationLogoutHandler
{
    private $configuration;

    public function __construct(
        Configuration $configuration
    ) {
        $this->configuration = $configuration;
    }

    public function handle(): void
    {
        $this->configuration->removeByEnvironment(Config::KLARNA_PAYMENT_API_KEY);
        $this->configuration->removeByEnvironment(Config::KLARNA_PAYMENT_API_USERNAME);
        $this->configuration->removeByEnvironment(Config::KLARNA_PAYMENT_API_PASSWORD);
        $this->configuration->removeByEnvironment(Config::KLARNA_PAYMENT_MERCHANT_ID);
        $this->configuration->removeByEnvironment(Config::KLARNA_PAYMENT_API_ENDPOINT);
    }
}
