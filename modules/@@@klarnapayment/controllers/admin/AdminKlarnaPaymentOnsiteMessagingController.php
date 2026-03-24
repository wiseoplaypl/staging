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
use KlarnaPayment\Module\Core\Shared\Enum\OnsiteMessagingPlacementThemes;
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

class AdminKlarnaPaymentOnsiteMessagingController extends ModuleAdminController
{
    public const FILE_NAME = 'AdminKlarnaPaymentOnsiteMessagingController';

    private const KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE = 'KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE';
    private const KLARNA_PAYMENT_ONSITE_MESSAGING_DATA_CLIENT_ID = 'KLARNA_PAYMENT_ONSITE_MESSAGING_DATA_CLIENT_ID';

    private const KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_THEME = 'KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_THEME';
    private const KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY = 'KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY';

    private const KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_THEME = 'KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_THEME';
    private const KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY = 'KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY';

    private const KLARNA_PAYMENT_ONSITE_MESSAGING_LEFT_COLUMN_THEME = 'KLARNA_PAYMENT_ONSITE_MESSAGING_LEFT_COLUMN_THEME';
    private const KLARNA_PAYMENT_ONSITE_MESSAGING_LEFT_COLUMN_DATA_KEY = 'KLARNA_PAYMENT_ONSITE_MESSAGING_LEFT_COLUMN_DATA_KEY';

    private const KLARNA_PAYMENT_ONSITE_MESSAGING_RIGHT_COLUMN_THEME = 'KLARNA_PAYMENT_ONSITE_MESSAGING_RIGHT_COLUMN_THEME';
    private const KLARNA_PAYMENT_ONSITE_MESSAGING_RIGHT_COLUMN_DATA_KEY = 'KLARNA_PAYMENT_ONSITE_MESSAGING_RIGHT_COLUMN_DATA_KEY';

    private const KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_THEME = 'KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_THEME';
    private const KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY = 'KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY';

    private const KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_THEME = 'KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_THEME';
    private const KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY = 'KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY';

    /** @var KlarnaPayment */
    public $module;

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
        /** @var ApplicationContextProvider $applicationContextProvider */
        $applicationContextProvider = $this->module->getService(ApplicationContextProvider::class);

        $this->context->smarty->assign([
            'klarnapayment' => [
                'merchant_portal_url' => $applicationContextProvider->get()->getIsProduction()
                    ? Config::KLARNA_PAYMENT_MERCHANT_PORTAL_URL['production']
                    : Config::KLARNA_PAYMENT_MERCHANT_PORTAL_URL['sandbox'],
            ],
        ]);

        $this->content .= $this->displayOnsiteMessagingSettings();
        $this->content .= $this->displayOnsiteMessagingConfiguration();

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

        if ($tools->isSubmit('submit_onsite_messaging_settings')) {
            $validation = (new Validator())->make($request->all(), [
                self::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE => 'required|integer',
                self::KLARNA_PAYMENT_ONSITE_MESSAGING_DATA_CLIENT_ID => 'required_if:' . self::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE . ',1',
            ]);

            $validation->validate();

            if ($validation->fails()) {
                $notificationHandler->addNotification(self::FILE_NAME, ErrorNotification::create(
                    $this->module->l('Invalid Data Client ID', self::FILE_NAME)
                ));
            } else {
                $onsiteMessagingActive = (int) $request->get(self::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE);
                $onsiteMessagingDataClientId = $request->get(self::KLARNA_PAYMENT_ONSITE_MESSAGING_DATA_CLIENT_ID) ?? '';

                $configuration->setByEnvironment(
                    Config::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE,
                    $onsiteMessagingActive
                );

                $configuration->setByEnvironment(
                    Config::KLARNA_PAYMENT_ONSITE_MESSAGING_DATA_CLIENT_ID,
                    $onsiteMessagingDataClientId
                );

                $notificationHandler->addNotification(self::FILE_NAME, SuccessNotification::create(
                    $this->module->l('On-site messaging settings updated', self::FILE_NAME)
                ));
            }

            $isFormSubmitted = true;
        }

        if ($tools->isSubmit('submit_onsite_messaging_configuration')) {
            foreach ($this->getOnsiteMessagingSettingMaps() as $currentClassKey => $configurationKey) {
                $configuration->setByEnvironment(
                    $configurationKey,
                    $request->get($currentClassKey)
                );
            }

            $notificationHandler->addNotification(self::FILE_NAME, SuccessNotification::create(
                $this->module->l('On-site messaging configuration updated', self::FILE_NAME)
            ));

            $isFormSubmitted = true;
        }

        if ($isFormSubmitted) {
            \Tools::redirectAdmin($this->context->link->getAdminLink(ModuleTabs::ONSITE_MESSAGING_MODULE_TAB_CONTROLLER_NAME));
        }

        return parent::postProcess();
    }

    /**
     * @throws SmartyException
     */
    public function displayOnsiteMessagingSettings(): string
    {
        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        $this->fields_value[self::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE] = $configuration->getByEnvironmentAsBoolean(
            Config::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE
        );

        $this->fields_value[self::KLARNA_PAYMENT_ONSITE_MESSAGING_DATA_CLIENT_ID] = $configuration->getByEnvironment(
            Config::KLARNA_PAYMENT_ONSITE_MESSAGING_DATA_CLIENT_ID
        );

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('On-site messaging settings', self::FILE_NAME),
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->module->l('Activate on-site messaging', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE,
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
                    'label' => $this->module->l('Data Client ID', self::FILE_NAME),
                    'desc' => $this->context->smarty->fetch('module:' . $this->module->name . '/views/templates/admin/settings/data-client-id-description.tpl'),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_DATA_CLIENT_ID,
                ],
            ],
            'buttons' => [
                [
                    'title' => $this->module->l('Save', self::FILE_NAME),
                    'class' => 'btn btn-lg pull-right form-btn',
                    'type' => 'submit',
                    'name' => 'submit_onsite_messaging_settings',
                ],
            ],
        ];

        return $this->renderForm();
    }

    /**
     * @throws SmartyException
     */
    public function displayOnsiteMessagingConfiguration(): string
    {
        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        foreach ($this->getOnsiteMessagingSettingMaps() as $currentClassKey => $configurationKey) {
            $this->fields_value[$currentClassKey] = $configuration->getByEnvironment(
                $configurationKey
            );
        }

        $themes = [
            [
                'value' => OnsiteMessagingPlacementThemes::DEFAULT,
                'label' => $this->module->l('Default', self::FILE_NAME),
            ],
            [
                'value' => OnsiteMessagingPlacementThemes::DARK,
                'label' => $this->module->l('Dark', self::FILE_NAME),
            ],
            [
                'value' => OnsiteMessagingPlacementThemes::CUSTOM,
                'label' => $this->module->l('Custom', self::FILE_NAME),
            ],
        ];

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Configure on-site messaging placement', self::FILE_NAME),
                'icon' => 'icon-list',
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->module->l('Footer theme', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_THEME,
                    'options' => [
                        'query' => $themes,
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'type' => 'text',
                    'col' => 4,
                    'label' => $this->module->l('Footer data key', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY,
                ],
                [
                    'type' => 'select',
                    'label' => $this->module->l('Top of page theme', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_THEME,
                    'options' => [
                        'query' => $themes,
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'type' => 'text',
                    'col' => 4,
                    'label' => $this->module->l('Top of page data key', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY,
                ],
                [
                    'type' => 'select',
                    'label' => $this->module->l('Left column theme', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_LEFT_COLUMN_THEME,
                    'options' => [
                        'query' => $themes,
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'type' => 'text',
                    'col' => 4,
                    'label' => $this->module->l('Left column data key', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_LEFT_COLUMN_DATA_KEY,
                ],
                [
                    'type' => 'select',
                    'label' => $this->module->l('Right column theme', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_RIGHT_COLUMN_THEME,
                    'options' => [
                        'query' => $themes,
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'type' => 'text',
                    'col' => 4,
                    'label' => $this->module->l('Right column data key', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_RIGHT_COLUMN_DATA_KEY,
                ],
                [
                    'type' => 'select',
                    'label' => $this->module->l('Product page theme', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_THEME,
                    'options' => [
                        'query' => $themes,
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'type' => 'text',
                    'col' => 4,
                    'label' => $this->module->l('Product page data key', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY,
                ],
                [
                    'type' => 'select',
                    'label' => $this->module->l('Cart page theme', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_THEME,
                    'options' => [
                        'query' => $themes,
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'type' => 'text',
                    'col' => 4,
                    'label' => $this->module->l('Cart page data key', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY,
                ],
            ],
            'buttons' => [
                [
                    'title' => $this->module->l('Save', self::FILE_NAME),
                    'class' => 'btn btn-lg pull-right form-btn',
                    'type' => 'submit',
                    'name' => 'submit_onsite_messaging_configuration',
                ],
            ],
        ];

        return $this->renderForm();
    }

    public function getShopName(int $shopId): string
    {
        return (new Shop($shopId))->name;
    }

    private function getOnsiteMessagingSettingMaps(): array
    {
        return [
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_THEME => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_THEME,
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY,
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_THEME => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_THEME,
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY,
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_LEFT_COLUMN_THEME => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_LEFT_COLUMN_THEME,
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_LEFT_COLUMN_DATA_KEY => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_LEFT_COLUMN_DATA_KEY,
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_RIGHT_COLUMN_THEME => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_RIGHT_COLUMN_THEME,
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_RIGHT_COLUMN_DATA_KEY => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_RIGHT_COLUMN_DATA_KEY,
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_THEME => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_THEME,
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY,
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_THEME => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_THEME,
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY,
        ];
    }
}
