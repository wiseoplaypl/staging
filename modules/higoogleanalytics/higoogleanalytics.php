<?php
/**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @version   1.4.3
 *
 * @website   https://hipresta.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__) . '/classes/HiPrestaModule.php';
include_once dirname(__FILE__) . '/classes/adminForms.php';
include_once dirname(__FILE__) . '/classes/order.php';
include_once dirname(__FILE__) . '/classes/event.php';

class HiGoogleAnalytics extends Module
{
    public $psv;
    public $errors = [];
    public $success = [];
    public $cleanDb;
    public $debugMode;

    /* Settings */
    public $enableGa4Tracking;
    public $ga4MeasurementId;

    // Order Settings
    public $refundedOrderStates = [];
    public $partialRefundedOrderStates = [];
    public $includeTaxes;
    public $includeShipping;
    public $includeWrapping;
    public $includeProductTaxes;
    public $deductDiscount;
    public $deductWholesalePrice;

    public function __construct()
    {
        $this->name = 'higoogleanalytics';
        $this->tab = 'advertising_marketing';
        $this->version = '1.4.3';
        $this->author = 'hipresta';
        $this->need_instance = 0;
        $this->secureKey = Tools::encrypt($this->name);
        $this->bootstrap = true;
        $this->module_key = 'f96e2e6f1736a2595c1c72a25b759ad8';
        parent::__construct();
        $this->globalVars();
        $this->displayName = $this->l('Google Analytics 4 - E-commerce & Custom Events');
        $this->description = $this->l('Unlock powerful insights with the GA4 module for advanced e-commerce tracking. Track customer behavior, sales data, and add custom events for deeper analytics. Elevate your store\'s performance with seamless Google Analytics 4 integration.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->hiPrestaClass = new HiPrestaGoogleAnalytics($this);
        $this->adminForms = new HiGoogleAnalyticsAdminForms($this);

        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        if (!parent::install()
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayOrderConfirmation')
            || !$this->registerHook('displayFooterProduct')
            || !$this->registerHook('actionOrderStatusUpdate')
            || !$this->registerHook('displayAdminAfterHeader')
            || !$this->hiPrestaClass->createTabs('AdminHiGoogleAnalytics', 'Admin Google Analytics', 'CONTROLLER_TABS_HI_GA', 0)
        ) {
            return false;
        }
        $this->proceedDb();

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        if (Configuration::get('HI_GA_CLEAN_DB')) {
            $this->proceedDb(true);
        }
        $this->hiPrestaClass->deleteTabs('CONTROLLER_TABS_HI_GA');

        return true;
    }

    private function proceedDb($drop = false)
    {
        if (!$drop) {
            Configuration::updateValue('HI_GA_DEBUG_MODE', false);
            Configuration::updateValue('HI_GA_CLEAN_DB', false);
            Configuration::updateValue('HI_GA_4_TRACKING', false);
            Configuration::updateValue('HI_GA_4_MEASUREMENT_ID', '');

            // Order Settings
            Configuration::updateValue('HI_GA_REFUNDED_STATES', json_encode([]));
            Configuration::updateValue('HI_GA_PARTIAL_REFUNDED_STATES', json_encode([]));

            Configuration::updateValue('HI_GA_INCLUDE_TAXES', true);
            Configuration::updateValue('HI_GA_INCLUDE_SHIPPING', true);
            Configuration::updateValue('HI_GA_INCLUDE_WRAPPING', true);
            Configuration::updateValue('HI_GA_INCLUDE_PRODUCT_TAXES', false);
            Configuration::updateValue('HI_GA_DEDUCT_DISCOUNT', true);
            Configuration::updateValue('HI_GA_DEDUCT_WHOLESALE_PRICE', false);

            Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'higaorder` (
                    `id_track` int(10) unsigned not null AUTO_INCREMENT,
                    `id_order` int(10) not null,
                    `id_state` int(10) not null,
                    `tracked` TINYINT  not null default 0,
                    `date_add` datetime not null,
                    `date_upd` datetime not null,
                    PRIMARY KEY (`id_track`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
            ');

            Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'higacustomevent` (
                    `id_event` int(10) unsigned not null AUTO_INCREMENT,
                    `active` TINYINT  NOT NULL,
                    `action` varchar(50) not null,
                    `selector` varchar(255) not null,
                    `event_category` varchar(255) not null,
                    `event_action` varchar(255) not null,
                    `event_label` varchar(255) not null,
                    `event_value` varchar(255) not null,
                    `date_add` datetime not null,
                    `date_upd` datetime not null,
                    PRIMARY KEY (`id_event`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
            ');

            Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'higacustomevent_shop` (
                    `id_event` int(10) unsigned NOT NULL,
                    `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_event`, `id_shop`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
            ');
        } else {
            Configuration::deleteByName('HI_GA_DEBUG_MODE');
            Configuration::deleteByName('HI_GA_CLEAN_DB');
            Configuration::deleteByName('HI_GA_4_TRACKING');
            Configuration::deleteByName('HI_GA_4_MEASUREMENT_ID');

            // Order Settings
            Configuration::deleteByName('HI_GA_REFUNDED_STATES');
            Configuration::deleteByName('HI_GA_PARTIAL_REFUNDED_STATES');

            Configuration::deleteByName('HI_GA_INCLUDE_TAXES');
            Configuration::deleteByName('HI_GA_INCLUDE_SHIPPING');
            Configuration::deleteByName('HI_GA_INCLUDE_WRAPPING');
            Configuration::deleteByName('HI_GA_INCLUDE_PRODUCT_TAXES');
            Configuration::deleteByName('HI_GA_DEDUCT_DISCOUNT');
            Configuration::deleteByName('HI_GA_DEDUCT_WHOLESALE_PRICE');

            $tables = ['higaorder', 'higacustomevent', 'higacustomevent_shop'];
            foreach ($tables as $table) {
                DB::getInstance()->Execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . bqSQL($table) . '`');
            }
        }
    }

    private function globalVars()
    {
        $this->psv = (float) Tools::substr(_PS_VERSION_, 0, 3);
        $this->debugMode = (bool) Configuration::get('HI_GA_DEBUG_MODE');
        $this->cleanDb = (bool) Configuration::get('HI_GA_CLEAN_DB');
        $this->enableGa4Tracking = (bool) Configuration::get('HI_GA_4_TRACKING');
        $this->ga4MeasurementId = Configuration::get('HI_GA_4_MEASUREMENT_ID');

        // // Order Settings
        $this->refundedOrderStates = json_decode(Configuration::get('HI_GA_REFUNDED_STATES'), true);
        $this->partialRefundedOrderStates = json_decode(Configuration::get('HI_GA_PARTIAL_REFUNDED_STATES'), true);
        $this->includeTaxes = (bool) Configuration::get('HI_GA_INCLUDE_TAXES');
        $this->includeShipping = (bool) Configuration::get('HI_GA_INCLUDE_SHIPPING');
        $this->includeWrapping = (bool) Configuration::get('HI_GA_INCLUDE_WRAPPING');
        $this->includeProductTaxes = (bool) Configuration::get('HI_GA_INCLUDE_PRODUCT_TAXES');
        $this->deductDiscount = (bool) Configuration::get('HI_GA_DEDUCT_DISCOUNT');
        $this->deductWholesalePrice = (bool) Configuration::get('HI_GA_DEDUCT_WHOLESALE_PRICE');
    }

    public function renderMenuTabs()
    {
        $tabs = [
            'googleAnalytics4' => [
                'title' => $this->l('Google Analytics 4'),
                'subtitle' => $this->l('Enable GA tracking here'),
                'iconImage' => 'ga.webp',
            ],
            'orderSettings' => [
                'title' => $this->l('Order Settings'),
                'subtitle' => $this->l('Select what and when to track'),
                'iconImage' => 'order-settings.webp',
            ],
            'customEvents' => [
                'title' => $this->l('Custom Events'),
                'subtitle' => $this->l('Add custom events as needed'),
                'iconImage' => 'custom-events.webp',
            ],
            'generelSettings' => [
                'title' => $this->l('General Settings'),
                'subtitle' => $this->l('Not much, but still useful'),
                'iconImage' => 'general-settings.webp',
            ],
            'rateMe' => [
                'title' => $this->l('Leave a review'),
                'subtitle' => $this->l('Help us make the module even better'),
                'iconImage' => 'review.webp',
                'url' => $this->getRateUrl(),
            ],
            'contactUs' => [
                'title' => $this->l('Contact Us'),
                'subtitle' => $this->l('Need Help? We\'re here'),
                'iconImage' => 'support.webp',
                'url' => $this->getContactUrl(),
            ],
            'version' => [
                'title' => $this->l('Version'),
                'subtitle' => $this->l('View the changelog'),
                'iconImage' => 'changelog.webp',
            ],
        ];

        $recommendations = $this->getModuleRecommendations();
        if ($recommendations) {
            $tabs['moreModules'] = [
                'title' => $this->l('More Modules'),
                'iconImage' => 'more-modules.webp',
            ];
        }

        $displayMenuModule = true;

        if (!empty($this->context->cookie->hideMenuModule)) {
            $displayMenuModule = false;
        }

        $this->context->smarty->assign([
            'psv' => $this->psv,
            'tabs' => $tabs,
            'module_version' => $this->version,
            'module_url' => $this->hiPrestaClass->getModuleUrl(),
            'module_tab_key' => $this->name,
            'active_tab' => Tools::getValue($this->name),
            'displayMenuModule' => $displayMenuModule,
            'moduleDir' => _MODULE_DIR_ . $this->name . '/',
        ]);

        return $this->display(__FILE__, 'views/templates/admin/menu-tabs.tpl');
    }

    public function getModuleRecommendations()
    {
        $recommendations = '';
        if (file_exists(__DIR__ . '/libs/hi-modules/modules.json')) {
            $recommendations = Tools::file_get_contents(__DIR__ . '/libs/hi-modules/modules.json');
            if ($recommendations) {
                $recommendations = json_decode($recommendations, true);
            }
        }

        return $recommendations ? $recommendations : [];
    }

    public function getRateUrl()
    {
        $langIsoCode = $this->context->language->iso_code;
        $psLanguages = ['en', 'fr', 'es', 'de', 'it', 'nl', 'pl', 'pt', 'ru'];

        if (in_array($langIsoCode, $psLanguages)) {
            return 'https://addons.prestashop.com/' . $this->context->language->iso_code . '/ratings.php';
        }

        return 'https://addons.prestashop.com/en/ratings.php';
    }

    public function getContactUrl()
    {
        $langIsoCode = $this->context->language->iso_code;
        $psLanguages = ['en', 'fr', 'es', 'de', 'it', 'nl', 'pl', 'pt', 'ru'];

        if (in_array($langIsoCode, $psLanguages)) {
            return 'https://addons.prestashop.com/' . $this->context->language->iso_code . '/contact-us?id_product=88855';
        }

        return 'https://addons.prestashop.com/en/contact-us?id_product=88855';
    }

    public function renderVersionForm()
    {
        $changelog = '';
        if (file_exists(dirname(__FILE__) . '/changelog.txt')) {
            $changelog = Tools::file_get_contents(dirname(__FILE__) . '/changelog.txt');
        }
        $this->context->smarty->assign('changelog', $changelog);

        return $this->display(__FILE__, 'views/templates/admin/version.tpl');
    }

    public function renderShopGroupError()
    {
        $this->context->smarty->assign(
            [
                'psv' => $this->psv,
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/shop_group_error.tpl');
    }

    public function renderModuleAdminVariables()
    {
        $this->context->smarty->assign(
            [
                'psv' => $this->psv,
                'id_lang' => $this->context->language->id,
                'hiGaSecureKey' => $this->secureKey,
                'hiGaAdminController' => $this->context->link->getAdminLink('AdminHiGoogleAnalytics'),
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/variables.tpl');
    }

    public function renderDisplayForm($content)
    {
        $this->context->smarty->assign(
            [
                'psv' => $this->psv,
                'errors' => $this->errors,
                'success' => $this->success,
                'content' => $content,
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/display_form.tpl');
    }

    public function postProcess()
    {
        $languages = Language::getLanguages(false);
        if (Tools::isSubmit('submitSettingsForm')) {
            Configuration::updateValue('HI_GA_DEBUG_MODE', (bool) Tools::getValue('debugMode'));
            Configuration::updateValue('HI_GA_CLEAN_DB', (bool) Tools::getValue('cleanDb'));

            $this->success[] = $this->l('Successfully Saved');
        } elseif (Tools::isSubmit('submitGa4Form')) {
            Configuration::updateValue('HI_GA_4_TRACKING', (bool) Tools::getValue('enableGa4Tracking'));
            Configuration::updateValue('HI_GA_4_MEASUREMENT_ID', Tools::getValue('ga4MeasurementId'));

            $this->success[] = $this->l('Successfully Saved');
        } elseif (Tools::isSubmit('submitOrderSettingsForm')) {
            Configuration::updateValue('HI_GA_REFUNDED_STATES', json_encode(Tools::getValue('refundedOrderStates')));
            Configuration::updateValue('HI_GA_PARTIAL_REFUNDED_STATES', json_encode(Tools::getValue('partialRefundedOrderStates')));

            Configuration::updateValue('HI_GA_INCLUDE_TAXES', (bool) Tools::getValue('includeTaxes'));
            Configuration::updateValue('HI_GA_INCLUDE_SHIPPING', (bool) Tools::getValue('includeShipping'));
            Configuration::updateValue('HI_GA_INCLUDE_WRAPPING', (bool) Tools::getValue('includeWrapping'));
            Configuration::updateValue('HI_GA_INCLUDE_PRODUCT_TAXES', (bool) Tools::getValue('includeProductTaxes'));
            Configuration::updateValue('HI_GA_DEDUCT_DISCOUNT', (bool) Tools::getValue('deductDiscount'));
            Configuration::updateValue('HI_GA_DEDUCT_WHOLESALE_PRICE', (bool) Tools::getValue('deductWholesalePrice'));

            $this->success[] = $this->l('Successfully Saved');
        }
    }

    public function renderModuleAdvertisingForm()
    {
        $recommendations = $this->getModuleRecommendations();
        $this->context->smarty->assign('modules', $recommendations);

        return $this->display(__FILE__, 'views/templates/admin/hipresta-modules.tpl');
    }

    public function renderDocumentation()
    {
        $this->context->smarty->assign([
            'moduleAssetsDir' => _MODULE_DIR_ . $this->name . '/libs/hi-modules-doc/img/',
            'contactLink' => $this->getContactUrl(),
        ]);

        return $this->display(__FILE__, 'libs/hi-modules-doc/doc.tpl');
    }

    public function displayForm()
    {
        $html = '';
        $content = '';
        if (!$this->hiPrestaClass->isSelectedShopGroup()) {
            $html .= $this->renderMenuTabs();
            switch (Tools::getValue($this->name)) {
                case 'googleAnalytics4':
                    $content .= $this->adminForms->renderGa4Form();
                    break;
                case 'orderSettings':
                    $content .= $this->adminForms->renderOrderSettings();
                    break;
                case 'customEvents':
                    $content .= $this->adminForms->renderEventsList();
                    break;
                case 'generelSettings':
                    $content .= $this->adminForms->renderSettingsForm();
                    break;
                case 'version':
                    $content .= $this->renderVersionForm();
                    break;
                case 'moreModules':
                    $content .= $this->renderModuleAdvertisingForm();
                    break;
                case 'free_module':
                    $content .= $this->renderFreeModuleAdvertisingForm();
                    break;
                default:
                    $content .= $this->adminForms->renderGa4Form();
                    break;
            }

            $content .= $this->renderDocumentation();
            $html .= $this->renderDisplayForm($content);
        } else {
            $html .= $this->renderShopGroupError();
        }

        $this->context->controller->addCSS($this->_path . 'libs/magnific-popup/magnific-popup.css', 'all');
        $this->context->controller->addJS($this->_path . 'libs/magnific-popup/jquery.magnific-popup.min.js');

        $this->context->controller->addCSS($this->_path . 'libs/hi-modules-table/table.css', 'all');
        $this->context->controller->addJS($this->_path . 'libs/hi-modules-table/table.js');

        $this->context->controller->addCSS($this->_path . 'views/css/admin.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/admin.js');

        $this->context->controller->addCSS($this->_path . 'libs/hi-modules-doc/doc.css', 'all');
        $this->context->controller->addJS($this->_path . 'libs/hi-modules-doc/doc.js');

        $html .= $this->renderModuleAdminVariables();

        return $html;
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitSettingsForm')
            || Tools::isSubmit('submitGa4Form')
            || Tools::isSubmit('submitOrderSettingsForm')) {
            $this->postProcess();
        }
        $this->globalVars();

        return $this->displayForm();
    }

    public function renderModal($class = null)
    {
        $this->context->smarty->assign(
            [
                'psv' => $this->psv,
                'modal_class' => $class,
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/modal.tpl');
    }

    public function displayAjaxError($message)
    {
        exit(json_encode([
            'error' => $message,
        ]));
    }

    public function getIsoCodeById(int $id, bool $forceRefreshCache = false)
    {
        if (method_exists('Currency', 'getIsoCodeById')) {
            return Currency::getIsoCodeById($id);
        }

        $cacheId = 'Currency::getIsoCodeById' . pSQL($id);
        if ($forceRefreshCache || !Cache::isStored($cacheId)) {
            $resultIsoCode = Db::getInstance()->getValue('SELECT `iso_code` FROM `' . _DB_PREFIX_ . 'currency` WHERE `id_currency` = ' . (int) $id);
            Cache::store($cacheId, $resultIsoCode);

            return $resultIsoCode;
        }

        return Cache::retrieve($cacheId);
    }

    public function hookDisplayHeader()
    {
        if ($this->enableGa4Tracking && $this->ga4MeasurementId) {
            if ($this->debugMode) {
                $this->context->controller->addCSS($this->_path . 'libs/jquery-confirm/jquery-confirm.min.css', 'all');
                $this->context->controller->addJS($this->_path . 'libs/jquery-confirm/jquery-confirm.min.js');
            }

            $this->context->controller->addJS($this->_path . 'views/js/front.js');
        }

        $gaSmartySettings = [
            'hiEnbleGa4Tracking' => (($this->enableGa4Tracking && $this->ga4MeasurementId) ? true : false),
            'hiGa4MeasurementId' => $this->ga4MeasurementId,
        ];

        $this->context->smarty->assign($gaSmartySettings);

        $gaJsParams = [
            'enbleGa4Tracking' => (($this->enableGa4Tracking && $this->ga4MeasurementId) ? true : false),
            'ga4MeasurementId' => $this->ga4MeasurementId,
            'frontController' => $this->context->link->getModuleLink($this->name, 'track'),
            'secureKey' => $this->secureKey,
            'debugMode' => $this->debugMode,
            'customEvents' => HiGoogleAnalyticsEvent::getEvents(),
            'includeProductTaxes' => $this->includeProductTaxes,
        ];

        Media::addJsDef([
            'hiGaSettings' => $gaJsParams,
        ]);

        // view cart tracking
        if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'cart' && !$this->context->controller->ajax) {
            $products = $this->context->cart->getproducts();

            if (is_array($products) && $products) {
                $data = [
                    'currency' => $this->getIsoCodeById($this->context->currency->id),
                    'value' => (float) $this->context->cart->getOrderTotal(),
                ];

                $items = [];
                foreach ($products as $product) {
                    $category = new Category($product['id_category_default'], $this->context->language->id);
                    $item = [
                        'item_id' => $product['id_product'],
                        'item_name' => $product['name'],
                        'discount' => (float) $product['reduction'],
                        'item_brand' => Manufacturer::getNameById($product['id_manufacturer']),
                        'item_category' => $category->name,
                        'price' => ($this->includeProductTaxes ? (float) $product['price_wt'] : (float) $product['price']),
                        'quantity' => (int) $product['cart_quantity'],
                    ];

                    if (!empty($product['attributes'])) {
                        $item['item_variant'] = $product['attributes'];
                    }

                    array_push($items, $item);
                }

                $data['items'] = $items;

                Media::addJsDef([
                    'hiGaCart' => $data,
                ]);
            }
        }

        return $this->display(__FILE__, 'header.tpl');
    }

    public function isTrackingEnabled()
    {
        if ($this->enableGa4Tracking && $this->ga4MeasurementId) {
            return true;
        }

        return false;
    }

    public function hookDisplayOrderConfirmation($params)
    {
        if (!$this->isTrackingEnabled()) {
            return;
        }
        $order = $params['order'];
        $products = $order->getCartProducts();
        $cart = new Cart($order->id_cart);
        // $cartRules = $cart->getCustomerHighlightedDiscounts()

        // calcuate conversation value
        if ($this->includeTaxes) {
            $totalPrice = $order->total_products_wt;

            if ($this->includeShipping) {
                $totalPrice += $order->total_shipping_tax_incl;
            }

            if ($this->includeWrapping) {
                $totalPrice += $order->total_wrapping_tax_incl;
            }
        } else {
            $totalPrice = $order->total_products;

            if ($this->includeShipping) {
                $totalPrice += $order->total_shipping_tax_excl;
            }

            if ($this->includeWrapping) {
                $totalPrice += $order->total_wrapping_tax_excl;
            }
        }

        if ($this->deductDiscount) {
            if ($this->includeTaxes) {
                $totalPrice -= $order->total_discounts_tax_incl;
            } else {
                $totalPrice -= $order->total_discounts_tax_excl;
            }
        }

        if ($this->deductWholesalePrice) {
            // calculate wholsale price for all products
            $wholesalePrice = 0;
            foreach ($products as $product) {
                $wholesalePrice += $product['wholesale_price'] * $product['cart_quantity'];
            }

            $totalPrice -= $wholesalePrice;
        }

        $data = [
            'transaction_id' => $order->id,
            'value' => (float) $totalPrice,
            'tax' => (float) ($order->total_paid_tax_incl - $order->total_paid_tax_excl),
            'shipping' => (float) $order->total_shipping_tax_incl,
            'currency' => $this->getIsoCodeById($order->id_currency),
        ];
        $cartRules = $order->getCartRules();
        if (is_array($cartRules) && $cartRules) {
            $data['coupon'] = $cartRules[0]['name'];
        }

        $items = [];
        foreach ($products as $product) {
            $category = new Category($product['id_category_default'], $this->context->language->id);
            $item = [
                'item_id' => $product['id_product'],
                'item_name' => Product::getProductName($product['id_product'], null, $this->context->language->id),
                'discount' => (float) $product['reduction_amount'],
                'item_brand' => Manufacturer::getNameById($product['id_manufacturer']),
                'item_category' => $category->name,
                'price' => ($this->includeProductTaxes ? (float) $product['unit_price_tax_incl'] : (float) $product['unit_price_tax_excl']),
                'quantity' => (int) $product['cart_quantity'],
            ];

            if ($product['product_attribute_id']) {
                $combination = new Combination($product['product_attribute_id']);
                $attributes = $combination->getAttributesName($this->context->language->id);
                $attributeNames = '';
                if (is_array($attributes) && $attributes) {
                    foreach ($attributes as $attribute) {
                        $attributeNames .= $attribute['name'] . ' - ';
                    }
                }
                $attributeNames = rtrim($attributeNames, ' - ');

                $item['item_variant'] = $attributeNames;
            }

            array_push($items, $item);
        }

        $data['items'] = $items;

        $this->context->smarty->assign('hiGaPurchaseData', json_encode($data, JSON_HEX_APOS));

        return $this->display(__FILE__, 'order-confirmation.tpl');
    }

    public function hookDisplayFooterProduct($params)
    {
        if (!$this->isTrackingEnabled()) {
            return;
        }

        $product = $params['product'];
        $data = [
            'currency' => $this->getIsoCodeById($this->context->currency->id),
            'value' => ($this->includeProductTaxes ? (float) $product['price_without_reduction'] : (float) $product['price_tax_exc']),
        ];

        $items = [];
        $item = [
            'item_id' => $product['id_product'],
            'item_name' => $product['name'],
            'discount' => (float) $product['reduction'],
            'item_brand' => Manufacturer::getNameById($product['id_manufacturer']),
            'item_category' => $product['category_name'],
            'price' => ($this->includeProductTaxes ? (float) $product['price_without_reduction'] : (float) $product['price_tax_exc']),
            'quantity' => 1,
        ];

        array_push($items, $item);
        $data['items'] = $items;

        $this->context->smarty->assign('hiGaProductData', json_encode($data, JSON_HEX_APOS));

        return $this->display(__FILE__, 'product.tpl');
    }

    public function hookActionOrderStatusUpdate($params)
    {
        if (!$this->isTrackingEnabled()) {
            return;
        }

        $idState = $params['newOrderStatus']->id;
        $idOrder = $params['id_order'];

        $trackOrderChange = false;
        if (is_array($this->refundedOrderStates) && $this->refundedOrderStates) {
            if (in_array($idState, $this->refundedOrderStates)) {
                $trackOrderChange = true;
            }
        }

        if (is_array($this->partialRefundedOrderStates) && $this->partialRefundedOrderStates) {
            if (in_array($idState, $this->partialRefundedOrderStates)) {
                $trackOrderChange = true;
            }
        }

        if ($trackOrderChange) {
            $gaOrder = new HiGoogleAnalyticsOrder();
            $gaOrder->id_order = $idOrder;
            $gaOrder->id_state = $idState;

            $gaOrder->add();
        }
    }

    public function hookDisplayAdminAfterHeader($params)
    {
        if (!$this->isTrackingEnabled() || Tools::getValue('controller') != 'AdminOrders') {
            return;
        }

        // find orders to track
        $gaOrders = HiGoogleAnalyticsOrder::getOrdersToTrack();
        if (!is_array($gaOrders) || !$gaOrders) {
            return;
        }

        $refunds = [];
        foreach ($gaOrders as $gaOrder) {
            $order = new Order($gaOrder['id_order']);
            $idState = $gaOrder['id_state'];
            $cart = new Cart($order->id_cart);

            // calcuate conversation value
            if ($this->includeTaxes) {
                $totalPrice = $order->total_products_wt;

                if ($this->includeShipping) {
                    $totalPrice += $order->total_shipping_tax_incl;
                }

                if ($this->includeWrapping) {
                    $totalPrice += $order->total_wrapping_tax_incl;
                }
            } else {
                $totalPrice = $order->total_products;

                if ($this->includeShipping) {
                    $totalPrice += $order->total_shipping_tax_excl;
                }

                if ($this->includeWrapping) {
                    $totalPrice += $order->total_wrapping_tax_excl;
                }
            }

            if ($this->deductDiscount) {
                if ($this->includeTaxes) {
                    $totalPrice -= $order->total_discounts_tax_incl;
                } else {
                    $totalPrice -= $order->total_discounts_tax_excl;
                }
            }

            $data = [
                'transaction_id' => $order->id,
                'value' => (float) $totalPrice,
                'tax' => (float) ($order->total_paid_tax_incl - $order->total_paid_tax_excl),
                'shipping' => (float) $order->total_shipping_tax_incl,
                'currency' => $this->getIsoCodeById($order->id_currency),
            ];
            $cartRules = $order->getCartRules();
            if (is_array($cartRules) && $cartRules) {
                $data['coupon'] = $cartRules[0]['name'];
            }

            // if partial refund then send also refunded products
            if (is_array($this->partialRefundedOrderStates) && $this->partialRefundedOrderStates && in_array($idState, $this->partialRefundedOrderStates)) {
                $products = $order->getProductsDetail();
                $items = [];
                foreach ($products as $product) {
                    if (!isset($product['product_quantity_refunded']) || !$product['product_quantity_refunded']) {
                        // add only products that were refunded
                        continue;
                    }
                    $category = new Category($product['id_category_default'], $this->context->language->id);
                    $item = [
                        'item_id' => $product['id_product'],
                        'item_name' => Product::getProductName($product['id_product'], null, $this->context->language->id),
                        'discount' => (float) $product['reduction_amount'],
                        'item_brand' => Manufacturer::getNameById($product['id_manufacturer']),
                        'item_category' => $category->name,
                        'price' => ($this->includeProductTaxes ? (float) $product['unit_price_tax_incl'] : (float) $product['unit_price_tax_excl']),
                        'quantity' => (int) $product['product_quantity_refunded'],
                    ];

                    if ($product['product_attribute_id']) {
                        $combination = new Combination($product['product_attribute_id']);
                        $attributes = $combination->getAttributesName($this->context->language->id);
                        $attributeNames = '';
                        if (is_array($attributes) && $attributes) {
                            foreach ($attributes as $attribute) {
                                $attributeNames .= $attribute['name'] . ' - ';
                            }
                        }
                        $attributeNames = rtrim($attributeNames, ' - ');

                        $item['item_variant'] = $attributeNames;
                    }

                    array_push($items, $item);
                }

                $data['items'] = $items;
            }

            array_push($refunds, $data);

            // set order as tracked
            $newGaOrder = new HiGoogleAnalyticsOrder($gaOrder['id_track']);
            $newGaOrder->tracked = 1;
            $newGaOrder->update();
        }

        $gaSmartySettings = [
            'hiEnbleGa4Tracking' => (($this->enableGa4Tracking && $this->ga4MeasurementId) ? true : false),
            'hiGa4MeasurementId' => $this->ga4MeasurementId,
            'refunds' => $refunds,
        ];

        $this->context->smarty->assign($gaSmartySettings);

        return $this->display(__FILE__, 'admin-order.tpl');
    }

    public function isContentSizeValid($content, $size)
    {
        if (iconv_strlen($content) > $size) {
            return false;
        }

        return true;
    }

    public function saveCustomEvent($idEvent)
    {
        $selector = trim(Tools::getValue('selector'));
        $eventCategory = trim(Tools::getValue('event_category'));
        $eventAction = trim(Tools::getValue('event_action'));
        $eventLabel = trim(Tools::getValue('event_label'));
        $eventValue = Tools::getValue('event_value');

        if (!$selector) {
            $this->displayAjaxError($this->l('Selector is required'));
        }

        if (!$this->isContentSizeValid($selector, 255)) {
            $this->displayAjaxError($this->l('Selector is too long, max 255 characters allowed'));
        }

        if (!$eventCategory) {
            $this->displayAjaxError($this->l('Event Category is required'));
        }

        if (!$this->isContentSizeValid($eventCategory, 255)) {
            $this->displayAjaxError($this->l('Event Category is too long, max 255 characters allowed'));
        }

        if (!$eventAction) {
            $this->displayAjaxError($this->l('Event Action is required'));
        }

        if (!$this->isContentSizeValid($eventAction, 255)) {
            $this->displayAjaxError($this->l('Event Action is too long, max 255 characters allowed'));
        }

        if (!$this->isContentSizeValid($eventLabel, 255)) {
            $this->displayAjaxError($this->l('Event Label is too long, max 255 characters allowed'));
        }

        if ($eventValue && !Validate::isInt($eventValue)) {
            $this->displayAjaxError($this->l('Event Value is not valid, please enter only numeric values'));
        }

        $event = new HiGoogleAnalyticsEvent($idEvent);
        $event->active = (int) Tools::getValue('active');
        $event->action = pSQL(Tools::getValue('eventAction'));
        $event->selector = pSQL($selector);
        $event->event_category = pSQL($eventCategory);
        $event->event_action = pSQL($eventAction);
        $event->event_label = pSQL($eventLabel);
        if ($eventValue) {
            $event->event_value = (int) $eventValue;
        } else {
            $event->event_value = null;
        }

        if (!$event->save()) {
            $this->displayAjaxError($this->l('We couldn\'t save the event, please try again.'));
        }

        if (!$idEvent) {
            $idEvent = $event->id;
        }

        $event->assignEventToShops();

        return $idEvent;
    }
}
