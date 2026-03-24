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

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Tools;
use KlarnaPayment\Module\Infrastructure\Bootstrap\ModuleTabs;
use KlarnaPayment\Module\Infrastructure\Controller\AbstractAdminController as ModuleAdminController;
use KlarnaPayment\Module\Infrastructure\Enum\PermissionType;
use KlarnaPayment\Module\Infrastructure\Notification\Handler\NotificationHandlerInterface;
use KlarnaPayment\Module\Infrastructure\Notification\Notifications\SuccessNotification;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminKlarnaPaymentStylingController extends ModuleAdminController
{
    const FILE_NAME = 'AdminKlarnaPaymentStylingController';

    private const KLARNA_PAYMENT_COLOR_DETAILS = 'KLARNA_PAYMENT_COLOR_DETAILS';
    private const KLARNA_PAYMENT_COLOR_BORDER = 'KLARNA_PAYMENT_COLOR_BORDER';
    private const KLARNA_PAYMENT_COLOR_BORDER_SELECTED = 'KLARNA_PAYMENT_COLOR_BORDER_SELECTED';
    private const KLARNA_PAYMENT_COLOR_TEXT = 'KLARNA_PAYMENT_COLOR_TEXT';
    private const KLARNA_PAYMENT_RADIUS_BORDER = 'KLARNA_PAYMENT_RADIUS_BORDER';

    public function __construct()
    {
        $this->bootstrap = true;

        parent::__construct();
    }

    /**
     * @throws SmartyException
     */
    public function initContent(): void
    {
        $this->content .= $this->displayStylingSettings();

        parent::initContent();
    }

    public function postProcess(): bool
    {
        if (!$this->ensureHasPermissions([PermissionType::EDIT, PermissionType::VIEW])) {
            return false;
        }

        /**
         * NOTE: this boolean is used to check
         * if we need to refresh page after form submit.
         *
         * This must be changed to true on every isSubmit method.
         */
        $isFormSubmitted = false;

        /** @var NotificationHandlerInterface $notificationHandler */
        $notificationHandler = $this->module->getService(NotificationHandlerInterface::class);

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        /** @var Tools $tools */
        $tools = $this->module->getService(Tools::class);

        if ($tools->isSubmit('submit_styling_settings')) {
            $configuration->setByEnvironment(Config::KLARNA_PAYMENT_COLOR_DETAILS, $tools->getValue(self::KLARNA_PAYMENT_COLOR_DETAILS));
            $configuration->setByEnvironment(Config::KLARNA_PAYMENT_COLOR_BORDER, $tools->getValue(self::KLARNA_PAYMENT_COLOR_BORDER));
            $configuration->setByEnvironment(Config::KLARNA_PAYMENT_COLOR_BORDER_SELECTED, $tools->getValue(self::KLARNA_PAYMENT_COLOR_BORDER_SELECTED));
            $configuration->setByEnvironment(Config::KLARNA_PAYMENT_COLOR_TEXT, $tools->getValue(self::KLARNA_PAYMENT_COLOR_TEXT));
            $configuration->setByEnvironment(Config::KLARNA_PAYMENT_RADIUS_BORDER, $tools->getValue(self::KLARNA_PAYMENT_RADIUS_BORDER));

            $notificationHandler->addNotification(self::FILE_NAME, SuccessNotification::create(
                $this->module->l('Settings updated.', self::FILE_NAME)
            ));

            $isFormSubmitted = true;
        }

        if ($isFormSubmitted) {
            \Tools::redirectAdmin($this->context->link->getAdminLink(ModuleTabs::STYLING_MODULE_TAB_CONTROLLER_NAME));
        }

        return parent::postProcess();
    }

    /**
     * @throws SmartyException
     */
    public function displayStylingSettings(): string
    {
        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        $this->fields_value[self::KLARNA_PAYMENT_COLOR_DETAILS] = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_COLOR_DETAILS);
        $this->fields_value[self::KLARNA_PAYMENT_COLOR_BORDER] = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_COLOR_BORDER);
        $this->fields_value[self::KLARNA_PAYMENT_COLOR_BORDER_SELECTED] = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_COLOR_BORDER_SELECTED);
        $this->fields_value[self::KLARNA_PAYMENT_COLOR_TEXT] = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_COLOR_TEXT);
        $this->fields_value[self::KLARNA_PAYMENT_RADIUS_BORDER] = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_RADIUS_BORDER);

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Styling settings', self::FILE_NAME),
            ],
            'input' => [
                [
                    'type' => 'color',
                    'label' => $this->module->l('Color details', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_COLOR_DETAILS,
                    'hint' => $this->module->l('Choose a color with the color picker, or enter an HTML color (e.g "#CC6600")', self::FILE_NAME),
                ],
                [
                    'type' => 'color',
                    'label' => $this->module->l('Color border', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_COLOR_BORDER,
                    'hint' => $this->module->l('Choose a color with the color picker, or enter an HTML color (e.g. "#CC6600")', self::FILE_NAME),
                ],
                [
                    'type' => 'color',
                    'label' => $this->module->l('Color border selected', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_COLOR_BORDER_SELECTED,
                    'hint' => $this->module->l('Choose a color with the color picker, or enter an HTML color (e.g. "#CC6600")', self::FILE_NAME),
                ],
                [
                    'type' => 'color',
                    'label' => $this->module->l('Color text', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_COLOR_TEXT,
                    'hint' => $this->module->l('Choose a color with the color picker, or enter an HTML color (e.g. "#CC6600")', self::FILE_NAME),
                ],
                [
                    'type' => 'text',
                    'class' => 'fixed-width-sm',
                    'label' => $this->module->l('Radius border', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_RADIUS_BORDER,
                    'suffix' => 'px',
                ],
            ],
            'buttons' => [
                [
                    'title' => $this->module->l('Save', self::FILE_NAME),
                    'class' => 'btn btn-lg pull-right form-btn',
                    'type' => 'submit',
                    'name' => 'submit_styling_settings',
                ],
            ],
        ];

        return parent::renderForm();
    }
}
