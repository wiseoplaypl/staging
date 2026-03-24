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

use CE\CoreXBaseXPageBase as PageBase;
use CE\ModulesXCatalogXControlsXSelectCategory as SelectCategory;
use CE\ModulesXCatalogXControlsXSelectManufacturer as SelectManufacturer;
use CE\ModulesXCatalogXControlsXSelectSupplier as SelectSupplier;
use CE\ModulesXCmsXControlsXSelectCategory as SelectCMSCategory;
use CE\ModulesXThemeXDocumentsXThemeDocument as ThemeDocument;

abstract class ModulesXThemeXDocumentsXHeaderFooterBase extends ThemeDocument
{
    public function getCssWrapperSelector()
    {
        return '#' . $this->getName();
    }

    public function getElementUniqueSelector(ElementBase $element)
    {
        return '#' . $this->getName() . ' .elementor-element' . $element->getUniqueSelector();
    }

    protected static function getEditorPanelCategories()
    {
        // Move to top as active.
        return [
            'theme-elements' => [
                'title' => __('Site'),
                'active' => true,
            ],
        ] + parent::getEditorPanelCategories();
    }

    protected function getPermalinkUrl($id_lang, $id_shop, array $args, $relative = true)
    {
        $settings = $this->getData('settings');
        $preview = isset($settings['preview']) ? $settings['preview'] : 'index';

        switch ($preview) {
            case 'index':
                return parent::getPermalinkUrl($id_lang, $id_shop, $args, $relative);
            case 'category':
                $id_category = !empty($settings['id_category'])
                    ? $settings['id_category']
                    : \Configuration::get('PS_HOME_CATEGORY', null, null, $id_shop ?: null);
                $url = Helper::$link->getCategoryLink($id_category, null, $id_lang, null, $id_shop, $relative);
                break;
            case 'manufacturer':
            case 'supplier':
                $get_link_method = 'get' . $preview . 'Link';
                $url = !empty($settings["id_$preview"])
                    ? Helper::$link->$get_link_method($settings["id_$preview"], null, $id_lang, $id_shop, $relative)
                    : Helper::$link->getPageLink($preview, true, $id_lang, null, false, $id_shop, $relative);
                break;
            case 'search':
                $url = Helper::$link->getPageLink($preview, true, $id_lang, empty($settings['search']) ? null : [
                    's' => $settings['search'],
                ], false, $id_shop, $relative);
                break;
            case 'product':
                empty($settings['id_product']) || $product = new \Product($settings['id_product'], false, $id_lang);
                empty($product->id) && $product = new \Product(Helper::getLastUpdatedProductId($id_shop) ?: null, false, $id_lang);

                if (!$product->active) {
                    isset($args['id_employee']) || $args['id_employee'] = $GLOBALS['employee']->id;
                    $args['adtoken'] = \Tools::getAdminTokenLite('AdminProducts');
                }
                $url = Helper::$link->getProductLink($product, null, null, null, $id_lang, $id_shop, $product->cache_default_attribute ?: 0, false, $relative);
                $url = explode('#', $url)[0];
                break;
            case 'cms':
                $settings['id_cms'] && $cms = new \CMS($settings['id_cms'], $id_lang);
                empty($cms->id) && $cms = new \CMS(1, $id_lang);

                if (!$cms->active) {
                    isset($args['id_employee']) || $args['id_employee'] = $GLOBALS['employee']->id;
                    $args['adtoken'] = \Tools::getAdminTokenLite('AdminCmsContent');
                }
                $url = Helper::$link->getCMSLink($cms, null, null, $id_lang, $id_shop, $relative);
                break;
            case 'cms_category':
                $id_cms_category = !empty($settings['id_cms_category']) ? $settings['id_cms_category'] : 1;
                $url = Helper::$link->getCMSCategoryLink($id_cms_category, null, $id_lang, $id_shop, $relative);
                break;
            default:
                $url = Helper::$link->getPageLink($preview, true, $id_lang, null, false, $id_shop, $relative);
                break;
        }

        return add_query_arg($args, $url);
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
            unset($pages['catalog']['options']['best-sales'], $pages['checkout']);
        } elseif (!\Configuration::get('PS_DISPLAY_BEST_SELLERS')) {
            unset($pages['catalog']['options']['best-sales']);
        }

        return $pages;
    }

    protected function registerControls()
    {
        parent::registerControls();

        $this->startControlsSection(
            'preview_settings',
            [
                'label' => __('Preview Settings'),
                'tab' => ControlsManager::TAB_SETTINGS,
            ]
        );

        $pages = _CE_ADMIN_ ? $this->getPagesGroups() : [];

        if (!$display_suppliers = \Configuration::get('PS_DISPLAY_SUPPLIERS')) {
            unset($pages['catalog']['options']['supplier']);
        }
        if (!$display_manufacturers = version_compare(_PS_VERSION_, '1.7.7', '<') ? $display_suppliers : \Configuration::get('PS_DISPLAY_MANUFACTURERS')) {
            unset($pages['catalog']['options']['manufacturer']);
        }
        $this->addControl(
            'preview',
            [
                'label' => __('Preview'),
                'type' => ControlsManager::SELECT2,
                'select2options' => [
                    'allowClear' => false,
                ],
                'options' => &$pages,
                'default' => 'index',
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
                'export' => false,
                'condition' => [
                    'preview' => 'category',
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
                    '0' => __('Brands Page'),
                ],
                'default' => 0,
                'export' => false,
                'condition' => [
                    'preview' => 'manufacturer',
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
                    '0' => __('Suppliers Page'),
                ],
                'default' => 0,
                'export' => false,
                'condition' => [
                    'preview' => 'supplier',
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
                    'preview' => 'search',
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
                'export' => false,
                'condition' => [
                    'preview' => 'product',
                ],
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
                'export' => false,
                'condition' => [
                    'preview' => 'cms',
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
                    'preview' => 'cms_category',
                ],
            ]
        );

        $this->addControl(
            'apply_preview',
            [
                'type' => ControlsManager::BUTTON,
                'text' => __('Apply & Preview'),
                'event' => 'ceThemeBuilder:ApplyPreview',
            ]
        );

        $this->endControlsSection();

        PageBase::registerStyleControls($this);

        $this->updateControl(
            'section_page_style',
            [
                'label' => __('Style'),
            ]
        );

        if ($this->getName() === 'footer') {
            $this->updateControl('padding', [
                'default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                ],
            ]);
        }

        $this->startInjection([
            'of' => 'padding',
        ]);

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'box_shadow',
                'fields_options' => $this->getName() === 'header' ? [
                    'box_shadow_type' => [
                        'default' => 'yes',
                    ],
                    'box_shadow' => [
                        'default' => [
                            'blur' => 0,
                        ],
                    ],
                ] : [],
            ]
        );

        $this->endInjection();
    }
}
