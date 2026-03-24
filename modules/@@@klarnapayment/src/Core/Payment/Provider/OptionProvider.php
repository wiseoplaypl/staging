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

namespace KlarnaPayment\Module\Core\Payment\Provider;

use KlarnaPayment\Module\Api\Models\Option;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OptionProvider
{
    /** @var Configuration */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function get(): ?Option
    {
        if (!$this->determineIfOptionsAreValid()) {
            return null;
        }

        $option = new Option();
        $option->setColorDetails($this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_COLOR_DETAILS));
        $option->setColorBorder($this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_COLOR_BORDER));
        $option->setColorBorderSelected($this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_COLOR_BORDER_SELECTED));
        $option->setColorText($this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_COLOR_TEXT));
        $option->setRadiusBorder($this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_RADIUS_BORDER));

        return $option;
    }

    private function determineIfOptionsAreValid(): bool
    {
        return $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_COLOR_DETAILS)
            || $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_COLOR_BORDER)
            || $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_COLOR_BORDER_SELECTED)
            || $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_COLOR_TEXT)
            || $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_RADIUS_BORDER);
    }
}
