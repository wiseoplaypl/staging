<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks, Elementor
 * @copyright 2019-2025 WebshopWorks.com & Elementor.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace CE;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CE\CoreXDynamicTagsXDataTag as DataTag;
use CE\ModulesXCatalogXControlsXSelectCategory as SelectCategory;
use CE\ModulesXCatalogXControlsXSelectManufacturer as SelectManufacturer;
use CE\ModulesXCatalogXControlsXSelectSupplier as SelectSupplier;
use CE\ModulesXCmsXControlsXSelectCategory as SelectCMSCategory;
use CE\ModulesXDynamicTagsXModule as Module;

class ModulesXDynamicTagsXTagsXInternalURL extends DataTag
{
    public function getName()
    {
        return 'internal-url';
    }

    public function getGroup()
    {
        return Module::SITE_GROUP;
    }

    public function getCategories()
    {
        return [Module::URL_CATEGORY];
    }

    public function getTitle()
    {
        return __('Internal URL');
    }

    public function getPanelTemplateSettingKey()
    {
        return 'type';
    }

    protected function getPagesGroups()
    {
        $pages = [
            'index' => __('Home', 'Admin.Catalog.Feature'),
            'contact' => __('Contact us', 'Shop.Navigation'),
            'cms' => [
                'label' => __('CMS'),
                'options' => [
                    'cms' => __('CMS Page'),
                    'cms_category' => __('CMS Category'),
                ],
            ],
            'catalog' => [
                'label' => __('Catalog', 'Admin.Global'),
                'options' => [
                    'product' => __('Product', 'Admin.Global'),
                    'category' => __('Category', 'Admin.Global'),
                    'manufacturer' => __('Brand', 'Admin.Global'),
                    'supplier' => __('Supplier', 'Admin.Global'),
                    'search' => __('Search', 'Shop.Navigation'),
                    'prices-drop' => __('Prices drop', 'Shop.Navigation'),
                    'new-products' => __('New products', 'Shop.Navigation'),
                    'best-sales' => __('Best sellers', 'Shop.Navigation'),
                ],
            ],
            'checkout' => [
                'label' => __('Checkout', 'Shop.Theme.Actions'),
                'options' => [
                    'cart' => __('Cart', 'Shop.Navigation'),
                    'order' => __('Order', 'Shop.Navigation'),
                ],
            ],
            'customer' => [
                'label' => __('Customer', 'Admin.Global'),
                'options' => [
                    'authentication' => __('Login', 'Shop.Navigation'),
                    'password' => __('Forgot your password', 'Shop.Navigation'),
                    'registration' => __('Registration', 'Shop.Navigation'),
                    'guest-tracking' => __('Guest tracking', 'Shop.Navigation'),
                ],
            ],
            'account' => [
                'label' => __('Account'),
                'options' => [
                    'my-account' => __('My account', 'Shop.Navigation'),
                    'identity' => __('Personal Information', 'Shop.Theme.Checkout'),
                    'addresses' => __('Addresses', 'Shop.Navigation'),
                    'address' => __('New address', 'Shop.Theme.Customeraccount'),
                    'history' => __('Order history', 'Shop.Navigation'),
                    'order-slip' => __('Credit slip', 'Shop.Navigation'),
                    'discount' => __('Vouchers', 'Shop.Theme.Customeraccount'),
                    'order-follow' => __('Merchandise returns', 'Shop.Theme.Customeraccount'),
                    'logout' => __('Sign out', 'Shop.Theme.Actions'),
                ],
            ],
            'misc' => [
                'label' => __('Miscellaneous', 'Admin.Global'),
                'options' => [
                    'stores' => __('Stores', 'Shop.Navigation'),
                    'sitemap' => __('Sitemap', 'Shop.Navigation'),
                    'pagenotfound' => __('404 error', 'Shop.Navigation'),
                ],
            ],
        ];
        if (\Configuration::get('PS_CATALOG_MODE')) {
            unset(
                $pages['catalog']['options']['best-sales'],
                $pages['checkout'],
                $pages['account']['options']['discount'],
                $pages['account']['options']['history'],
                $pages['account']['options']['order-slip'],
                $pages['account']['options']['order-follow']
            );
        } else {
            if (!\Configuration::get('PS_DISPLAY_BEST_SELLERS')) {
                unset($pages['catalog']['options']['best-sales']);
            }
            if (!\Configuration::get('PS_CART_RULE_FEATURE_ACTIVE')) {
                unset($pages['discount']);
            }
            if (!\Configuration::get('PS_ORDER_RETURN')) {
                unset($pages['order-follow']);
            }
        }

        return $pages;
    }

    protected function registerControls()
    {
        $pages = _CE_ADMIN_ ? $this->getPagesGroups() : [];

        if (!$display_suppliers = \Configuration::get('PS_DISPLAY_SUPPLIERS')) {
            unset($pages['catalog']['options']['supplier']);
        }
        if (!$display_manufacturers = version_compare(_PS_VERSION_, '1.7.7', '<') ? $display_suppliers : \Configuration::get('PS_DISPLAY_MANUFACTURERS')) {
            unset($pages['catalog']['options']['manufacturer']);
        }

        $this->addControl(
            'type',
            [
                'label' => __('Page'),
                'label_block' => true,
                'type' => ControlsManager::SELECT2,
                'select2options' => [
                    'placeholder' => __('Select...'),
                    'allowClear' => false,
                ],
                'options' => &$pages,
            ]
        );

        $this->addControl(
            'id_cms',
            [
                'label' => __('Search & Select'),
                'label_block' => true,
                'type' => ControlsManager::SELECT2,
                'select2options' => [
                    'placeholder' => __('Type Here'),
                    'ajax' => [
                        'get' => 'Cms',
                    ],
                ],
                'condition' => [
                    'type' => 'cms',
                ],
            ]
        );

        $this->addControl(
            'id_cms_category',
            [
                'label' => __('Search & Select'),
                'label_block' => true,
                'type' => SelectCMSCategory::CONTROL_TYPE,
                'select2options' => [
                    'allowClear' => false,
                ],
                'default' => 1,
                'export' => false,
                'condition' => [
                    'type' => 'cms_category',
                ],
            ]
        );

        $this->addControl(
            'id_product',
            [
                'label' => __('Search & Select'),
                'label_block' => true,
                'type' => ControlsManager::SELECT2,
                'select2options' => [
                    'placeholder' => __('Type Product Name / Ref'),
                    'ajax' => [
                        'get' => 'Products',
                        'url' => Helper::getAjaxProductsListLink(),
                    ],
                ],
                'condition' => [
                    'type' => 'product',
                ],
            ]
        );

        $this->addControl(
            'id_category',
            [
                'label' => __('Search & Select'),
                'label_block' => true,
                'type' => SelectCategory::CONTROL_TYPE,
                'select2options' => [
                    'allowClear' => false,
                ],
                'default' => $GLOBALS['context']->shop->id_category,
                'condition' => [
                    'type' => 'category',
                ],
            ]
        );

        $display_manufacturers && $this->addControl(
            'id_manufacturer',
            [
                'label' => __('Search & Select'),
                'label_block' => true,
                'type' => SelectManufacturer::CONTROL_TYPE,
                'select2options' => [
                    'allowClear' => false,
                ],
                'options' => [
                    '0' => __('Brands list', 'Shop.Navigation'),
                ],
                'default' => 0,
                'condition' => [
                    'type' => 'manufacturer',
                ],
            ]
        );

        $display_suppliers && $this->addControl(
            'id_supplier',
            [
                'label' => __('Search & Select'),
                'label_block' => true,
                'type' => SelectSupplier::CONTROL_TYPE,
                'select2options' => [
                    'allowClear' => false,
                ],
                'options' => [
                    '0' => __('Suppliers list', 'Shop.Navigation'),
                ],
                'default' => 0,
                'condition' => [
                    'type' => 'supplier',
                ],
            ]
        );

        $this->addControl(
            'search',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
                'placeholder' => __('Search') . '...',
                'condition' => [
                    'type' => 'search',
                ],
            ]
        );
    }

    public function getValue(array $options = [])
    {
        $settings = $this->getSettings();
        $type = $settings['type'];
        $method = 'cms_category' === $type ? 'getCmsCategoryLink' : 'get' . $type . 'Link';

        if (method_exists('Link', $method) && !empty($settings["id_$type"])) {
            return Helper::$link->$method($settings["id_$type"]);
        }
        if ('search' === $type && $settings['search']) {
            return Helper::$link->getPageLink($type, true, null, ['s' => $settings['search']]);
        }
        if ('registration' === $type && (int) _PS_VERSION_ < 8) {
            return Helper::$link->getPageLink('authentication', true, null, ['create_account' => '1']);
        }
        if ('logout' === $type) {
            return Helper::$link->getPageLink('index', true, null, 'mylogout');
        }

        return Helper::$link->getPageLink($type, true);
    }

    protected function getSmartyValue(array $options = [])
    {
        $settings = $this->getSettings();
        $type = $settings['type'];
        $method = 'cms_category' === $type ? 'getCmsCategoryLink' : 'get' . $type . 'Link';

        if (method_exists('Link', $method) && !empty($settings["id_$type"])) {
            return "{call_user_func([\$link, $method], " . (int) $settings["id_$type"] . ')}';
        }
        if ('search' === $type && $settings['search']) {
            return "{capture assign=ce_search}$settings[search]{/capture}" .
                '{call_user_func([$link, getPageLink], search, true, $language.id, array_combine([s], [$ce_search]))}';
        }
        if ('registration' === $type) {
            return '{$urls.pages.register}';
        }
        if ('logout' === $type) {
            return '{$urls.actions.logout}';
        }
        $page = str_replace('-', '_', $type);

        return "{\$urls.pages.$page}";
    }
}
