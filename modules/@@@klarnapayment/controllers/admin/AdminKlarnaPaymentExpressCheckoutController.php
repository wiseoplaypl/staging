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
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaExpressCheckoutButtonShape;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaExpressCheckoutButtonTheme;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaExpressCheckoutPlacement;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Tools;
use KlarnaPayment\Module\Infrastructure\Bootstrap\ModuleTabs;
use KlarnaPayment\Module\Infrastructure\Controller\AbstractAdminController as ModuleAdminController;
use KlarnaPayment\Module\Infrastructure\Enum\PermissionType;
use KlarnaPayment\Module\Infrastructure\Notification\Handler\NotificationHandlerInterface;
use KlarnaPayment\Module\Infrastructure\Notification\Notifications\ErrorNotification;
use KlarnaPayment\Module\Infrastructure\Notification\Notifications\SuccessNotification;
use KlarnaPayment\Module\Infrastructure\Provider\ApplicationContextProvider;
use KlarnaPayment\Module\Infrastructure\Request\Request;
use Rakit\Validation\Validator;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminKlarnaPaymentExpressCheckoutController extends ModuleAdminController
{
    public const FILE_NAME = 'AdminKlarnaPaymentExpressCheckoutController';

    private const KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE = 'KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE';

    private const KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS_INPUT = 'KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS[]';
    private const KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS_VALUE = 'KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS';

    private const KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME = 'KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME';
    private const KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE = 'KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE';
    private const KLARNA_PAYMENT_EXPRESS_CHECKOUT_CLIENT_IDENTIFIER = 'KLARNA_PAYMENT_EXPRESS_CHECKOUT_CLIENT_IDENTIFIER';

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
        $this->context->controller->addJqueryPlugin('select2');

        /** @var ApplicationContextProvider $applicationContextProvider */
        $applicationContextProvider = $this->module->getService(ApplicationContextProvider::class);

        $this->context->smarty->assign([
            'klarnapayment' => [
                'merchant_portal_url' => $applicationContextProvider->get()->getIsProduction()
                    ? Config::KLARNA_PAYMENT_MERCHANT_PORTAL_URL['production']
                    : Config::KLARNA_PAYMENT_MERCHANT_PORTAL_URL['sandbox'],
            ],
        ]);

        $this->addJS($this->module->getLocalPath() . 'views/js/admin/settings/general.js');

        $this->content .= $this->displayExpressCheckoutSettings();
        $this->content .= $this->displayExpressCheckoutPlacementConfiguration();

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

        $request = Request::createFromGlobals();

        if ($tools->isSubmit('submit_express_checkout_settings')) {
            $validation = (new Validator())->make($request->all(), [
                self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE => 'required|integer',
                self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_CLIENT_IDENTIFIER => 'required_if:' . self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE . ',1',
            ]);

            $validation->validate();

            if ($validation->fails()) {
                $notificationHandler->addNotification(self::FILE_NAME, ErrorNotification::create(
                    $this->module->l('Invalid Klarna Client Identifier', self::FILE_NAME)
                ));
            } else {
                $expressCheckoutActive = (int) $request->get(self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE);
                $expressCheckoutClientIdentifier = $request->get(self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_CLIENT_IDENTIFIER) ?? '';

                $configuration->setByEnvironment(
                    Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE,
                    $expressCheckoutActive
                );

                $configuration->setByEnvironment(
                    Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_CLIENT_IDENTIFIER,
                    $expressCheckoutClientIdentifier
                );

                $notificationHandler->addNotification(self::FILE_NAME, SuccessNotification::create(
                    $this->module->l('Express checkout settings updated', self::FILE_NAME)
                ));
            }
        }

        if ($tools->isSubmit('submit_express_checkout_placement_configuration')) {
            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS,
                implode(
                    ',',
                    $tools->getValue(self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS_VALUE) ?: []
                )
            );

            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME,
                $tools->getValue(self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME)
            );

            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE,
                $tools->getValue(self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE)
            );

            $notificationHandler->addNotification(self::FILE_NAME, SuccessNotification::create(
                $this->module->l('Settings updated', self::FILE_NAME)
            ));

            $isFormSubmitted = true;
        }

        if ($isFormSubmitted) {
            \Tools::redirectAdmin($this->context->link->getAdminLink(ModuleTabs::EXPRESS_CHECKOUT_MODULE_TAB_CONTROLLER_NAME));
        }

        return parent::postProcess();
    }

    private function displayExpressCheckoutSettings(): string
    {
        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        $this->fields_value[self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE] = $configuration->getByEnvironmentAsBoolean(
            Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE
        );

        $this->fields_value[self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_CLIENT_IDENTIFIER] = $configuration->getByEnvironment(
            Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_CLIENT_IDENTIFIER
        );

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Klarna Express Checkout settings', self::FILE_NAME),
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->module->l('Activate Klarna Express Checkout', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', self::FILE_NAME),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', self::FILE_NAME),
                        ],
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('Klarna Client Identifier', self::FILE_NAME),
                    'desc' => $this->context->smarty->fetch('module:' . $this->module->name . '/views/templates/admin/settings/client-identifier-description.tpl'),
                    'name' => self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_CLIENT_IDENTIFIER,
                ],
            ],
            'buttons' => [
                [
                    'title' => $this->module->l('Save', self::FILE_NAME),
                    'class' => 'btn btn-lg pull-right form-btn',
                    'type' => 'submit',
                    'name' => 'submit_express_checkout_settings',
                ],
            ],
        ];

        return parent::renderForm();
    }

    private function displayExpressCheckoutPlacementConfiguration(): string
    {
        $placements = [
            [
                'value' => KlarnaExpressCheckoutPlacement::PRODUCT_PAGE,
                'label' => $this->module->l('Product page', self::FILE_NAME),
            ],
            [
                'value' => KlarnaExpressCheckoutPlacement::CART_PAGE,
                'label' => $this->module->l('Cart page', self::FILE_NAME),
            ],
        ];

        $buttonThemes = [
            [
                'value' => KlarnaExpressCheckoutButtonTheme::DEFAULT,
                'label' => $this->module->l('Default', self::FILE_NAME),
            ],
            [
                'value' => KlarnaExpressCheckoutButtonTheme::DARK,
                'label' => $this->module->l('Dark', self::FILE_NAME),
            ],
            [
                'value' => KlarnaExpressCheckoutButtonTheme::LIGHT,
                'label' => $this->module->l('Light', self::FILE_NAME),
            ],
        ];

        $buttonShapes = [
            [
                'value' => KlarnaExpressCheckoutButtonShape::DEFAULT,
                'label' => $this->module->l('Default', self::FILE_NAME),
            ],
            [
                'value' => KlarnaExpressCheckoutButtonShape::PILL,
                'label' => $this->module->l('Pill', self::FILE_NAME),
            ],
            [
                'value' => KlarnaExpressCheckoutButtonShape::RECT,
                'label' => $this->module->l('Rect', self::FILE_NAME),
            ],
        ];

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        $this->fields_value[self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS_INPUT] = explode(
            ',',
            $configuration->getByEnvironment(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS)
        );

        $this->fields_value[self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME] =
            $configuration->getByEnvironment(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME);

        $this->fields_value[self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE] =
            $configuration->getByEnvironment(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE);

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Configure Klarna Express Checkout placement', self::FILE_NAME),
                'icon' => 'icon-list',
            ],
            'input' => [
                [
                    'class' => 'multiselect-options',
                    'type' => 'select',
                    'name' => self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS_INPUT,
                    'label' => $this->module->l('Placement', self::FILE_NAME),
                    'desc' => $this->module->l('Choose where Klarna Express Checkout button will be displayed', self::FILE_NAME),
                    'options' => [
                        'query' => $placements,
                        'id' => 'value',
                        'name' => 'label',
                    ],
                    'multiple' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->module->l('Theme', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME,
                    'options' => [
                        'query' => $buttonThemes,
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->module->l('Shape', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE,
                    'options' => [
                        'query' => $buttonShapes,
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
            ],
            'buttons' => [
                [
                    'title' => $this->module->l('Save', self::FILE_NAME),
                    'class' => 'btn btn-lg pull-right form-btn',
                    'type' => 'submit',
                    'name' => 'submit_express_checkout_placement_configuration',
                ],
            ],
        ];

        return $this->renderForm();
    }
}
