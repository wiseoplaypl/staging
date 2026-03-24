<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 */
namespace CE;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CE\ModulesXCatalogXControlsXSelectCategory as SelectCategory;
use CE\ModulesXCatalogXControlsXSelectManufacturer as SelectManufacturer;
use CE\ModulesXCatalogXControlsXSelectSupplier as SelectSupplier;

abstract class ModulesXCatalogXWidgetsXProductXBase extends WidgetBase
{
    const REMOTE_RENDER = true;

    const FLAGS = [
        'sale' => 'discount',
        'new' => 'new',
        'pack' => 'pack',
        'out' => 'out_of_stock',
        'online' => 'online-only',
    ];

    private static $assembler;

    private static $presenter;

    private static $presentationSettings;

    private static $itemList;

    protected $catalog;

    protected $imageSize;

    public function __construct($data = [], $args = [])
    {
        $this->catalog = \Configuration::get('PS_CATALOG_MODE');
        $this->imageSize = \ImageType::getFormattedName('home');

        if (_CE_ADMIN_ || \CreativeElements::isMaintenance()) {
            isset($GLOBALS['customer']->id) || $GLOBALS['context']->customer = $GLOBALS['customer'] = new \Customer();
        } elseif (null === self::$assembler) {
            $presenter_class = version_compare(_PS_VERSION_, '1.7.5', '<')
                ? '\PrestaShop\PrestaShop\Core\Product\ProductPresenter'
                : '\PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductPresenter';
            self::$presentationSettings = (new \ProductPresenterFactory($GLOBALS['context']))->getPresentationSettings();
            self::$presenter = new $presenter_class(
                new \PrestaShop\PrestaShop\Adapter\Image\ImageRetriever(Helper::$link),
                Helper::$link,
                new \PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
                new \PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
                Helper::$translator
            );
            self::$assembler = new \ProductAssembler($GLOBALS['context']);
        }
        parent::__construct($data, $args);
    }

    public function getCategories()
    {
        return ['catalog'];
    }

    public static function getSkinTemplate($skin)
    {
        if (preg_match('/^product-(\d+)\d{6}$/', $skin, $m)) {
            $uid = new UId($m[1], UId::THEME, $GLOBALS['language']->id, $GLOBALS['context']->shop->id);
            $path = "catalog/_partials/miniatures/product-$uid.tpl";

            if (file_exists(_CE_TEMPLATES_ . "front/theme/$path")) {
                return $path;
            }
            if ($document = Plugin::$instance->documents->get($uid)) {
                method_exists($document, 'saveTpl') && $document->saveTpl();
            }
        }
        $path = "catalog/_partials/miniatures/$skin.tpl";

        return (file_exists(_CE_TEMPLATES_ . "front/theme/$path")
            || file_exists(_PS_THEME_DIR_ . "templates/$path")
            || _PARENT_THEME_NAME_
            && file_exists(_PS_PARENT_THEME_DIR_ . "templates/$path")
        ) ? $path : '';
    }

    public static function getSkinOptions()
    {
        static $opts;

        if (_CE_ADMIN_ && null === $opts) {
            $_uid = sprintf('%02d%02d%02d', UId::THEME, $GLOBALS['language']->id, $GLOBALS['context']->shop->id);
            $themes = \CETheme::getOptions('product-miniature', $GLOBALS['language']->id, $GLOBALS['context']->shop->id);
            $skins = [
                'product' => __('Default'),
            ];
            foreach ($themes as $theme) {
                $skins["product-$theme[value]$_uid"] = $theme['name'];
            }
            $pattern = 'templates/catalog/_partials/miniatures/*product*.tpl';
            $tpls = array_merge(
                _PARENT_THEME_NAME_ ? glob(_PS_PARENT_THEME_DIR_ . $pattern) : [],
                glob(_PS_THEME_DIR_ . $pattern),
                glob(_CE_TEMPLATES_ . "front/theme/catalog/_partials/miniatures/product-*$_uid.tpl")
            );
            $opts = [];

            foreach ($tpls as $tpl) {
                $opt = basename($tpl, '.tpl');
                $opts[$opt] = isset($skins[$opt]) ? $skins[$opt] : \Tools::ucFirst($opt);
            }
            unset($opts['pack-product']);
        }

        return $opts ?: [];
    }

    protected function getListingOptions()
    {
        $opts = [
            'category' => __('Products by Category'),
            'manufacturer' => __('Products by Brand'),
            'supplier' => __('Products by Supplier'),
            'prices-drop' => __('Prices drop', 'Shop.Navigation'),
            'new-products' => __('New products', 'Shop.Navigation'),
            'best-sales' => __('Best sellers', 'Shop.Navigation'),
            'related' => __('Related Products'),
            'viewed' => __('Recently Viewed'),
            'products' => __('Custom Products'),
        ];
        if ($this->catalog) {
            unset($opts['best-sales']);
        }

        return $opts;
    }

    protected function registerListingControls($limit = 'limit')
    {
        $this->addControl(
            'heading',
            [
                'label' => __('Title'),
                'classes' => 'elementor-control-type-heading',
                'type' => ControlsManager::TEXT,
                'label_block' => true,
                'placeholder' => __('Enter your title'),
                'dynamic' => [
                    'active' => true,
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'heading_display',
            [
                'label' => __('Display'),
                'type' => ControlsManager::CHOOSE,
                'options' => WidgetHeading::getDisplaySizes(),
                'style_transfer' => true,
            ]
        );

        $this->addControl(
            'heading_size',
            [
                'label' => __('HTML Tag'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'h2',
            ]
        );

        $this->addControl(
            'listing',
            [
                'label' => __('Listing'),
                'classes' => 'elementor-control-type-heading',
                'type' => ControlsManager::SELECT,
                'default' => 'category',
                'options' => $this->getListingOptions(),
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'products',
            [
                'type' => ControlsManager::REPEATER,
                'item_actions' => [
                    'add' => [
                        'product' => Helper::getAjaxProductsListLink(),
                        'placeholder' => __('Add Product'),
                    ],
                    'duplicate' => false,
                ],
                'prevent_empty' => false,
                'fields' => [
                    [
                        'name' => 'id',
                        'type' => ControlsManager::HIDDEN,
                        'default' => '',
                    ],
                ],
                'title_field' => '<# var prodImg = elementor.getProductImage( id ), prodName = elementor.getProductName( id ); #>' .
                    '<# if ( prodImg ) { #><img src="{{ prodImg }}" class="ce-repeater-thumb"><# } #>' .
                    '<# if ( prodName ) { #><span title="{{ prodName }}">{{{ prodName }}}</span><# } #>',
                'condition' => [
                    'listing' => 'products',
                ],
            ]
        );

        $this->addControl(
            'related_product_id',
            [
                'label' => __('Product', 'Shop.Theme.Catalog'),
                'type' => ControlsManager::SELECT2,
                'label_block' => true,
                'select2options' => [
                    'placeholder' => __('Current Product'),
                    'ajax' => [
                        'get' => 'Products',
                        'url' => Helper::getAjaxProductsListLink(),
                    ],
                ],
                'condition' => [
                    'listing' => 'related',
                ],
            ]
        );

        $this->addControl(
            'category_id',
            [
                'label' => __('Category'),
                'label_block' => true,
                'type' => SelectCategory::CONTROL_TYPE,
                'select2options' => [
                    'allowClear' => false,
                ],
                'options' => [
                    '0' => __('Current Category') . ' / ' . __('Default'),
                ],
                'default' => 0,
                'condition' => [
                    'listing' => 'category',
                ],
            ]
        );

        $this->addControl(
            'manufacturer_id',
            [
                'label' => __('Brand'),
                'label_block' => true,
                'type' => SelectManufacturer::CONTROL_TYPE,
                'select2options' => [
                    'allowClear' => false,
                ],
                'options' => [
                    '0' => __('Products with the same brand'),
                ],
                'default' => 0,
                'condition' => [
                    'listing' => 'manufacturer',
                ],
            ]
        );

        $this->addControl(
            'supplier_id',
            [
                'label' => __('Supplier'),
                'label_block' => true,
                'type' => SelectSupplier::CONTROL_TYPE,
                'select2options' => [
                    'allowClear' => false,
                ],
                'options' => [
                    '0' => __('Products with the same supplier'),
                ],
                'default' => 0,
                'condition' => [
                    'listing' => 'supplier',
                ],
            ]
        );

        $this->addControl(
            'order_by',
            [
                'label' => __('Order By'),
                'type' => ControlsManager::SELECT,
                'default' => 'position',
                'options' => [
                    'name' => __('Name'),
                    'price' => __('Price', 'Shop.Theme.Catalog'),
                    'position' => __('Popularity'),
                    'quantity' => __('Sales Volume'),
                    'date_add' => __('Arrival'),
                    'date_upd' => __('Update'),
                ],
                'condition' => [
                    'listing!' => ['related', 'viewed', 'products'],
                ],
            ]
        );

        $this->addControl(
            'order_dir',
            [
                'label' => __('Order Direction'),
                'type' => ControlsManager::SELECT,
                'default' => 'asc',
                'options' => [
                    'asc' => __('Ascending'),
                    'desc' => __('Descending'),
                ],
                'condition' => [
                    'listing!' => ['related', 'viewed', 'products'],
                ],
            ]
        );

        $this->addControl(
            'randomize',
            [
                'label' => __('Randomize'),
                'type' => ControlsManager::SWITCHER,
                'condition' => [
                    'listing' => ['category', 'viewed', 'products', 'related'],
                ],
            ]
        );

        $this->addControl(
            'show_out',
            [
                'label' => __('Out-of-Stock'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
                'condition' => [
                    'listing' => 'products',
                ],
            ]
        );

        $this->addControl(
            $limit,
            [
                'label' => __('Product Limit'),
                'type' => ControlsManager::NUMBER,
                'min' => 1,
                'default' => 8,
                'condition' => [
                    'listing!' => 'products',
                ],
            ]
        );
    }

    protected function registerMiniatureSections()
    {
        $this->startControlsSection(
            'section_content',
            [
                'label' => __('Content'),
                'condition' => [
                    'skin' => 'custom',
                ],
            ]
        );

        $image_size_options = GroupControlImageSize::getAllImageSizes('products');

        if (empty($image_size_options[$this->imageSize])) {
            // Set first image size as default when home doesn't exists
            $this->imageSize = key($image_size_options);
        }

        $this->addResponsiveControl(
            'image_size',
            [
                'label' => __('Image Size'),
                'type' => ControlsManager::SELECT,
                'options' => &$image_size_options,
                'default' => $this->imageSize,
            ]
        );

        $this->addControl(
            'show_second_image',
            [
                'label' => __('Second Image'),
                'description' => __('Show second image on hover if exists'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
            ]
        );

        $this->addControl(
            'show_category',
            [
                'label' => __('Category'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
            ]
        );

        $this->addControl(
            'show_description',
            [
                'label' => __('Description'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
            ]
        );

        $this->addControl(
            'description_length',
            [
                'label' => __('Max. Length'),
                'type' => ControlsManager::NUMBER,
                'min' => 1,
                'condition' => [
                    'show_description!' => '',
                ],
            ]
        );

        $this->addControl(
            'show_regular_price',
            [
                'label' => __('Regular price', 'Shop.Theme.Catalog'),
                'type' => $this->catalog ? ControlsManager::HIDDEN : ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
            ]
        );

        $this->addControl(
            'show_atc',
            [
                'label' => __('Add to Cart'),
                'type' => $this->catalog ? ControlsManager::HIDDEN : ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
            ]
        );

        $this->addControl(
            'show_qv',
            [
                'label' => __('Quick View'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
            ]
        );

        $this->addControl(
            'heading_product_name',
            [
                'label' => __('Product Name'),
                'type' => ControlsManager::HEADING,
                'condition' => [
                    'show_badges!' => [],
                ],
            ]
        );

        $this->addControl(
            'title_display',
            [
                'label' => __('Display'),
                'type' => ControlsManager::CHOOSE,
                'options' => WidgetHeading::getDisplaySizes(),
                'style_transfer' => true,
            ]
        );

        $this->addControl(
            'title_tag',
            [
                'label' => __('HTML Tag'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'h3',
            ]
        );

        $this->addControl(
            'show_badges',
            [
                'label' => __('Badges'),
                'classes' => 'elementor-control-type-heading',
                'type' => ControlsManager::SELECT2,
                'multiple' => true,
                'label_block' => true,
                'options' => [
                    'sale' => __('Sale'),
                    'new' => __('New', 'Shop.Theme.Catalog'),
                    'pack' => __('Pack', 'Shop.Theme.Catalog'),
                    'out' => __('Out-of-Stock'),
                    'online' => __('Online only', 'Shop.Theme.Catalog'),
                ],
                'default' => ['sale', 'new', 'pack', 'out', 'online'],
            ]
        );

        $this->addControl(
            'badge_sale_text',
            [
                'label' => __('Sale'),
                'type' => ControlsManager::TEXT,
                'placeholder' => __('Default'),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'show_badges',
                            'operator' => 'contains',
                            'value' => 'sale',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'badge_new_text',
            [
                'label' => __('New', 'Shop.Theme.Catalog'),
                'type' => ControlsManager::TEXT,
                'placeholder' => Helper::$translator->trans('New', [], 'Shop.Theme.Catalog'),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'show_badges',
                            'operator' => 'contains',
                            'value' => 'new',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'badge_pack_text',
            [
                'label' => __('Pack', 'Shop.Theme.Catalog'),
                'type' => ControlsManager::TEXT,
                'placeholder' => Helper::$translator->trans('Pack', [], 'Shop.Theme.Catalog'),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'show_badges',
                            'operator' => 'contains',
                            'value' => 'pack',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'badge_out_text',
            [
                'label' => __('Out-of-Stock'),
                'type' => ControlsManager::TEXT,
                'placeholder' => \Translate::getModuleTranslation('creativeelements', 'Out-of-Stock', ''),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'show_badges',
                            'operator' => 'contains',
                            'value' => 'out',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'badge_online_text',
            [
                'label' => __('Online only', 'Shop.Theme.Catalog'),
                'type' => ControlsManager::TEXT,
                'placeholder' => Helper::$translator->trans('Online only', [], 'Shop.Theme.Catalog'),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'show_badges',
                            'operator' => 'contains',
                            'value' => 'online',
                        ],
                    ],
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_atc',
            [
                'label' => __('Add to Cart'),
                'condition' => [
                    'skin' => 'custom',
                    'show_atc' => $this->catalog ? 'hide' : 'yes',
                ],
            ]
        );

        $this->addControl(
            'atc_type',
            [
                'label' => __('Type'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Default'),
                    'primary' => __('Primary'),
                    'secondary' => __('Secondary'),
                ],
                'default' => 'primary',
                'style_transfer' => true,
            ]
        );

        $this->addControl(
            'atc_text',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
                'default' => Helper::$translator->trans('Add to cart', [], 'Shop.Theme.Actions'),
            ]
        );

        $this->addControl(
            'atc_align',
            [
                'label' => __('Alignment'),
                'label_block' => false,
                'type' => ControlsManager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'prefix_class' => 'elementor-atc--align-',
                'default' => 'justify',
            ]
        );

        $this->addControl(
            'atc_size',
            [
                'label' => __('Size'),
                'type' => ControlsManager::CHOOSE,
                'toggle' => false,
                'options' => WidgetButton::getButtonSizes(),
                'default' => 'xs',
                'style_transfer' => true,
            ]
        );

        $this->addControl(
            'selected_atc_icon',
            [
                'label' => __('Icon'),
                'label_block' => false,
                'type' => ControlsManager::ICONS,
                'skin' => 'inline',
                'fa4compatibility' => 'atc_icon',
                'recommended' => [
                    'ce-icons' => [
                        'cart-light',
                        'cart-medium',
                        'cart-solid',
                        'trolley-light',
                        'trolley-medium',
                        'trolley-solid',
                        'trolley-bold',
                        'basket-light',
                        'basket-medium',
                        'basket-solid',
                        'bag-light',
                        'bag-medium',
                        'bag-solid',
                        'bag-rounded-o',
                        'bag-rounded',
                        'bag-trapeze-o',
                        'bag-trapeze',
                    ],
                    'fa-solid' => [
                        'bag-shopping',
                        'basket-shopping',
                        'cart-shopping',
                        'cart-plus',
                    ],
                ],
            ]
        );

        $this->addControl(
            'atc_icon_align',
            [
                'label' => __('Icon Position'),
                'type' => ControlsManager::SELECT,
                'default' => 'left',
                'options' => [
                    'left' => __('Before'),
                    'right' => __('After'),
                ],
                'condition' => [
                    'selected_atc_icon[value]!' => '',
                ],
            ]
        );

        $this->addControl(
            'atc_icon_indent',
            [
                'label' => __('Icon Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'condition' => [
                    'selected_atc_icon[value]!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-atc .elementor-button-content-wrapper' => 'gap: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .elementor-atc .elementor-button-text' => 'flex-grow: min(0, {{SIZE}})',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_qv',
            [
                'label' => __('Quick View'),
                'condition' => [
                    'skin' => 'custom',
                    'show_qv!' => '',
                ],
            ]
        );

        $this->addControl(
            'qv_text',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
                'default' => Helper::$translator->trans('Quick view', [], 'Shop.Theme.Actions'),
            ]
        );

        $this->addControl(
            'selected_qv_icon',
            [
                'label' => __('Icon'),
                'label_block' => false,
                'type' => ControlsManager::ICONS,
                'skin' => 'inline',
                'fa4compatibility' => 'qv_icon',
                'recommended' => [
                    'ce-icons' => [
                        'search-light',
                        'search-medium',
                        'search-glint',
                        'search-minimal',
                        'loupe',
                        'magnifier',
                    ],
                    'fa-solid' => [
                        'magnifying-glass',
                        'magnifying-glass-plus',
                        'expand',
                        'up-right-and-down-left-from-center',
                        'maximize',
                        'eye',
                    ],
                    'fa-regular' => [
                        'eye',
                    ],
                ],
            ]
        );

        $this->addControl(
            'qv_icon_align',
            [
                'label' => __('Icon Position'),
                'type' => ControlsManager::SELECT,
                'default' => 'left',
                'options' => [
                    'left' => __('Before'),
                    'right' => __('After'),
                ],
                'condition' => [
                    'selected_qv_icon[value]!' => '',
                ],
            ]
        );

        $this->addControl(
            'qv_icon_indent',
            [
                'label' => __('Icon Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'condition' => [
                    'selected_qv_icon[value]!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-quick-view .elementor-button-content-wrapper' => 'gap: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->endControlsSection();
    }

    protected function registerHeadingStyleSection()
    {
        $this->startControlsSection(
            'section_heading_style',
            [
                'label' => __('Title'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'heading!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            'heading_align',
            [
                'label' => __('Alignment'),
                'type' => ControlsManager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} > .elementor-widget-container > .elementor-heading-title' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'heading_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} > .elementor-widget-container > .elementor-heading-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'heading_typography',
                'selector' => '{{WRAPPER}} > .elementor-widget-container > .elementor-heading-title',
            ]
        );

        $this->addGroupControl(
            GroupControlTextStroke::getType(),
            [
                'name' => 'text_stroke',
                'selector' => '{{WRAPPER}} > .elementor-widget-container > .elementor-heading-title',
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'heading_shadow',
                'selector' => '{{WRAPPER}} > .elementor-widget-container > .elementor-heading-title',
            ]
        );

        $this->addResponsiveControl(
            'heading_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} > .elementor-widget-container > .elementor-heading-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();
    }

    protected function registerMiniatureStyleSections()
    {
        $scheme = $this->getName() === 'product-box';

        $this->startControlsSection(
            'section_style_image',
            [
                'label' => __('Image'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'skin' => 'custom',
                ],
            ]
        );

        $this->addControl(
            'image_width',
            [
                'label' => __('Width'),
                'type' => ControlsManager::SLIDER,
                'placeholder' => 'Auto',
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'max' => 800,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                ],
                'placeholder' => __('Auto'),
                'selectors' => [
                    '{{WRAPPER}} .elementor-image img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBorder::getType(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .elementor-image img',
            ]
        );

        $this->addControl(
            'image_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'hover_animation',
            [
                'label' => __('Hover Animation'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('None'),
                    'grow' => __('Grow'),
                    'shrink' => __('Shrink'),
                    'rotate' => __('Rotate'),
                    'grow-rotate' => __('Grow Rotate'),
                    'float' => __('Float'),
                    'sink' => __('Sink'),
                    'bob' => __('Bob'),
                    'hang' => __('Hang'),
                    'buzz-out' => __('Buzz Out'),
                ],
                'prefix_class' => 'elementor-img-hover-',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_content',
            [
                'label' => __('Content'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'skin' => 'custom',
                ],
            ]
        );

        $this->addControl(
            'content_align',
            [
                'label' => __('Alignment'),
                'type' => ControlsManager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'content_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'content_min_height',
            [
                'label' => __('Min Height'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-content' => 'min-height: {{SIZE}}{{UNIT}};',
                ],
                'range' => [
                    'px' => [
                        'max' => 300,
                    ],
                ],
            ]
        );

        $this->startControlsTabs('content_style_tabs');

        $this->startControlsTab(
            'content_style_title',
            [
                'label' => __('Name'),
            ]
        );

        $this->addControl(
            'title_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-title' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'title_multiline',
            [
                'label' => __('Allow Multiline'),
                'type' => ControlsManager::SWITCHER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-title' => 'white-space: normal;',
                ],
            ]
        );

        $this->addControl(
            'title_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => !$scheme ? '' : [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'title_typography',
                'scheme' => !$scheme ? '' : SchemeTypography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .elementor-title',
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'content_style_category',
            [
                'label' => __('Category'),
                'condition' => [
                    'show_category!' => '',
                ],
            ]
        );

        $this->addControl(
            'category_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-category' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'category_multiline',
            [
                'label' => __('Allow Multiline'),
                'type' => ControlsManager::SWITCHER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-category' => 'white-space: normal;',
                ],
            ]
        );

        $this->addControl(
            'category_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => !$scheme ? '' : [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-category' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'category_typography',
                'scheme' => !$scheme ? '' : SchemeTypography::TYPOGRAPHY_2,
                'selector' => '{{WRAPPER}} .elementor-category',
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'content_style_description',
            [
                'label' => __('Description'),
                'condition' => [
                    'show_description!' => '',
                ],
            ]
        );

        $this->addControl(
            'description_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-description' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'description_line_clamp',
            [
                'label' => __('Max Lines'),
                'type' => ControlsManager::NUMBER,
                'min' => 1,
                'selectors' => [
                    '{{WRAPPER}} .elementor-description' => '-webkit-line-clamp: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'description_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => !$scheme ? '' : [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'description_typography',
                'scheme' => !$scheme ? '' : SchemeTypography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} .elementor-description',
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'content_style_price',
            [
                'label' => __('Price', 'Shop.Theme.Catalog'),
            ]
        );

        $this->addControl(
            'price_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-price-wrapper' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'price_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => !$scheme ? '' : [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'price_typography',
                'scheme' => !$scheme ? '' : SchemeTypography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .elementor-price-wrapper',
            ]
        );

        $this->addControl(
            'heading_style_regular_price',
            [
                'label' => __('Regular Price', 'Shop.Theme.Catalog'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'show_regular_price' => $this->catalog ? 'hide' : 'yes',
                ],
            ]
        );

        $this->addControl(
            'regular_price_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => !$scheme ? '' : [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-price-regular' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_regular_price' => $this->catalog ? 'hide' : 'yes',
                ],
            ]
        );

        $this->addResponsiveControl(
            'regular_price_size',
            [
                'label' => _x('Size', 'Typography Control'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-price-regular' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_regular_price' => $this->catalog ? 'hide' : 'yes',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_atc',
            [
                'label' => __('Add to Cart'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'skin' => 'custom',
                    'show_atc' => $this->catalog ? 'hide' : 'yes',
                ],
            ]
        );

        $this->addControl(
            'atc_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-atc' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'atc_typography',
                'scheme' => !$scheme ? '' : SchemeTypography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .elementor-atc .elementor-button',
            ]
        );

        $this->startControlsTabs('atc_style_tabs');

        $this->startControlsTab(
            'atc_style_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'atc_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-atc .elementor-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'atc_bg_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-atc .elementor-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'atc_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-atc .elementor-button' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'atc_style_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'atc_color_hover',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-atc .elementor-button:hover, {{WRAPPER}} .elementor-atc .elementor-button:focus' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'atc_bg_color_hover',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-atc .elementor-button:hover, {{WRAPPER}} .elementor-atc .elementor-button:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'atc_border_color_hover',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-atc .elementor-button:hover, {{WRAPPER}} .elementor-atc .elementor-button:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'atc_style_disabled',
            [
                'label' => __('Disabled'),
            ]
        );

        $this->addControl(
            'atc_color_disabled',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-atc .elementor-button:disabled' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'atc_bg_color_disabled',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-atc .elementor-button:disabled' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'atc_border_color_disabled',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-atc .elementor-button:disabled' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'atc_cursor_disabled',
            [
                'label' => __('Cursor'),
                'label_block' => false,
                'type' => ControlsManager::CHOOSE,
                'options' => [
                    'default' => [
                        'icon' => 'fas fa-arrow-pointer',
                    ],
                    'not-allowed' => [
                        'icon' => 'eicon-ban',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} button.elementor-button:disabled' => 'cursor: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'atc_border_width',
            [
                'label' => __('Border Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 20,
                    ],
                    'em' => [
                        'max' => 2,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-atc .elementor-button' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'atc_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-atc .elementor-button' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_qv',
            [
                'label' => __('Quick View'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'show_qv' => 'yes',
                    'skin' => 'custom',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'qv_typography',
                'scheme' => !$scheme ? '' : SchemeTypography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .elementor-quick-view',
            ]
        );

        $this->startControlsTabs('qv_style_tabs');

        $this->startControlsTab(
            'qv_style_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'qv_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-quick-view:not(#e)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'qv_bg_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-quick-view' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'qv_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-quick-view' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'qv_style_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'qv_color_hover',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-quick-view:not(#e):hover, {{WRAPPER}} a.elementor-quick-view:not(#e):focus' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'qv_bg_color_hover',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-quick-view:hover, {{WRAPPER}} .elementor-quick-view:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'qv_border_color_hover',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-quick-view:hover, {{WRAPPER}} .elementor-quick-view:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'qv_border_width',
            [
                'label' => __('Border Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 20,
                    ],
                    'em' => [
                        'max' => 2,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-quick-view' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'qv_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-quick-view' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_badges',
            [
                'label' => __('Badges'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'skin' => 'custom',
                    'show_badges!' => [],
                ],
            ]
        );

        $this->addControl(
            'badges_top',
            [
                'label' => __('Top Distance'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => -20,
                        'max' => 20,
                    ],
                    'em' => [
                        'min' => -2,
                        'max' => 2,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-badges-left' => 'margin-top: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementor-badges-right' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'badges_left',
            [
                'label' => __('Left Distance'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => -20,
                        'max' => 20,
                    ],
                    'em' => [
                        'min' => -2,
                        'max' => 2,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-badges-left' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'badge_sale_position',
                            'value' => 'left',
                        ],
                        [
                            'name' => 'badge_new_position',
                            'value' => 'left',
                        ],
                        [
                            'name' => 'badge_pack_position',
                            'value' => 'left',
                        ],
                        [
                            'name' => 'badge_out_position',
                            'value' => 'left',
                        ],
                        [
                            'name' => 'badge_online_position',
                            'value' => 'left',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'badges_right',
            [
                'label' => __('Right Distance'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => -20,
                        'max' => 20,
                    ],
                    'em' => [
                        'min' => -2,
                        'max' => 2,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-badges-right' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'badge_sale_position',
                            'value' => 'right',
                        ],
                        [
                            'name' => 'badge_new_position',
                            'value' => 'right',
                        ],
                        [
                            'name' => 'badge_pack_position',
                            'value' => 'right',
                        ],
                        [
                            'name' => 'badge_out_position',
                            'value' => 'right',
                        ],
                        [
                            'name' => 'badge_online_position',
                            'value' => 'right',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'bagdes_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-badge' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'badges_min_width',
            [
                'label' => __('Min Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-badge' => 'min-width: {{SIZE}}{{UNIT}};',
                ],
                'default' => [
                    'size' => 50,
                ],
            ]
        );

        $this->addControl(
            'badges_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'badges_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-badge' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'badges_typography',
                'selector' => '{{WRAPPER}} .elementor-badge',
            ]
        );

        $this->startControlsTabs('badge_style_tabs');

        $this->startControlsTab(
            'badge_style_sale',
            [
                'label' => __('Sale'),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'show_badges',
                            'operator' => 'contains',
                            'value' => 'sale',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'badge_sale_position',
            [
                'label' => __('Position'),
                'type' => ControlsManager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'icon' => 'eicon-h-align-left',
                        'title' => __('Left'),
                    ],
                    'right' => [
                        'icon' => 'eicon-h-align-right',
                        'title' => __('Right'),
                    ],
                ],
                'default' => 'right',
            ]
        );

        $this->addControl(
            'badge_sale_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-badge-sale' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'badge_sale_bg_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-badge-sale' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'badge_style_new',
            [
                'label' => __('New', 'Shop.Theme.Catalog'),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'show_badges',
                            'operator' => 'contains',
                            'value' => 'new',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'badge_new_position',
            [
                'label' => __('Position'),
                'type' => ControlsManager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'icon' => 'eicon-h-align-left',
                        'title' => __('Left'),
                    ],
                    'right' => [
                        'icon' => 'eicon-h-align-right',
                        'title' => __('Right'),
                    ],
                ],
                'default' => 'right',
            ]
        );

        $this->addControl(
            'badge_new_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-badge-new' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'badge_new_bg_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-badge-new' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'badge_style_pack',
            [
                'label' => __('Pack', 'Shop.Theme.Catalog'),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'show_badges',
                            'operator' => 'contains',
                            'value' => 'pack',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'badge_pack_position',
            [
                'label' => __('Position'),
                'type' => ControlsManager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'icon' => 'eicon-h-align-left',
                        'title' => __('Left'),
                    ],
                    'right' => [
                        'icon' => 'eicon-h-align-right',
                        'title' => __('Right'),
                    ],
                ],
                'default' => 'right',
            ]
        );

        $this->addControl(
            'badge_pack_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-badge-pack' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'badge_pack_bg_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-badge-pack' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'badge_style_out',
            [
                'label' => __('Out-of-Stock'),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'show_badges',
                            'operator' => 'contains',
                            'value' => 'out',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'badge_out_position',
            [
                'label' => __('Position'),
                'type' => ControlsManager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'icon' => 'eicon-h-align-left',
                        'title' => __('Left'),
                    ],
                    'right' => [
                        'icon' => 'eicon-h-align-right',
                        'title' => __('Right'),
                    ],
                ],
                'default' => 'right',
            ]
        );

        $this->addControl(
            'badge_out_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-badge-out' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'badge_out_bg_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-badge-out' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'badge_style_online',
            [
                'label' => __('Online only', 'Shop.Theme.Catalog'),
            ]
        );

        $this->addControl(
            'badge_online_position',
            [
                'label' => __('Position'),
                'type' => ControlsManager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'icon' => 'eicon-h-align-left',
                        'title' => __('Left'),
                    ],
                    'right' => [
                        'icon' => 'eicon-h-align-right',
                        'title' => __('Right'),
                    ],
                ],
                'default' => 'right',
            ]
        );

        $this->addControl(
            'badge_online_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-badge-online' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'badge_online_bg_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-badge-online' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->endControlsSection();
    }

    public function onImport($widget)
    {
        static $id_product;

        null === $id_product && $id_product = Helper::getLastUpdatedProductId($GLOBALS['context']->shop->id) ?: '';

        // Check Skin
        if (empty($widget['settings']['skin']) || !in_array($widget['settings']['skin'], ['product', 'custom']) && !static::getSkinTemplate($widget['settings']['skin'])) {
            $widget['settings']['skin'] = 'product';
        }

        // Check Product ID
        if (!empty($widget['settings']['product_id'])) {
            $product = new \Product($widget['settings']['product_id']);

            if (!$product->id) {
                $widget['settings']['product_id'] = $id_product;
            }
        }

        // Check Related Product ID
        if (!empty($widget['settings']['related_product_id'])) {
            $product = new \Product($widget['settings']['related_product_id']);

            if (!$product->id) {
                $widget['settings']['related_product_id'] = $id_product;
            }
        }

        // Check Product IDs
        if (!empty($widget['settings']['products'])) {
            $ids = array_column($widget['settings']['products'], 'id');
            $pids = array_column(
                \Db::getInstance()->executeS(
                    'SELECT `id_product` FROM ' . _DB_PREFIX_ . 'product WHERE `id_product` IN (' . implode(',', array_map('intval', $ids)) . ')'
                ) ?: [],
                'id_product'
            );
            foreach ($widget['settings']['products'] as &$product) {
                in_array($product['id'], $pids) || $product['id'] = $id_product;
            }
        }

        // Check Product Image Sizes
        $sizes = array_column(\ImageType::getImagesTypes('products'), 'name');

        foreach (['image_size', 'image_size_tablet', 'image_size_mobile'] as $image_size) {
            if (isset($widget['settings'][$image_size]) && !in_array($widget['settings'][$image_size], $sizes)) {
                $home = \ImageType::getFormattedName('home');

                $widget['settings'][$image_size] = in_array($home, $sizes) ? $home : reset($sizes);
            }
        }

        IconsManager::onImportMigration($widget, 'atc_icon', 'selected_atc_icon');
        IconsManager::onImportMigration($widget, 'qv_icon', 'selected_qv_icon');

        return $widget;
    }

    public static function getAccessoriesLight($id_product)
    {
        return \Db::getInstance()->executeS('
            SELECT p.`id_product` FROM ' . _DB_PREFIX_ . 'accessory
            LEFT JOIN ' . _DB_PREFIX_ . 'product p ON p.`id_product` = `id_product_2` ' .
            \Shop::addSqlAssociation('product', 'p') . '
            WHERE `id_product_1` = ' . (int) $id_product . ' AND p.`active` = 1 AND p.`visibility` IN ("both", "catalog")
        ');
    }

    protected function getProduct($id)
    {
        try {
            // Fix: Missing id_product not handled before PS 8.1
            $assembledProduct = @self::$assembler->assembleProduct(['id_product' => $id]);

            return !empty($assembledProduct['active'])
                ? self::$presenter->present(self::$presentationSettings, $assembledProduct, $GLOBALS['language'])
                : false;
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }

    protected function getProducts($listing, $order_by, $order_dir, $limit, $id, $products = [])
    {
        $tpls = [];
        $controller = $GLOBALS['context']->controller;
        $isProductController = $controller instanceof \ProductController;

        if ('products' === $listing) {
            // Custom Products
            if ('rand' === $order_by) {
                shuffle($products);
            }
            $show_out = $this->getSettings('show_out');

            foreach ($products as &$product) {
                if ($product['id'] && $product = $this->getProduct($product['id'])) {
                    if ($show_out || empty($product['flags']['out_of_stock'])) {
                        $tpls[] = $product;
                    }
                }
            }

            return $tpls;
        }
        if ('related' === $listing && !$id && $isProductController) {
            // Related Products on product page
            if (empty($GLOBALS['smarty']->tpl_vars['accessories']) || !$products = &$GLOBALS['smarty']->tpl_vars['accessories']->value) {
                return [];
            }
            if ('rand' === $order_by) {
                shuffle($products);
            }
            if (count($products) > $limit) {
                $products = array_slice($products, 0, $limit);
            }

            return $products;
        }
        if ('viewed' === $listing) {
            // Recently Viewed
            $products = isset($GLOBALS['cookie']->ceViewedProducts) ? explode(',', $GLOBALS['cookie']->ceViewedProducts) : [];

            if ($isProductController) {
                $id_product = $controller->getProduct()->id;

                if ($id_product && in_array($id_product, $products)) {
                    $products = array_diff($products, [$id_product]);
                }
            }
            $products = array_slice($products, 0, $limit);

            if ('rand' === $order_by) {
                shuffle($products);
            }
            foreach ($products as $id_product) {
                if ($product = $this->getProduct($id_product)) {
                    $tpls[] = $product;
                }
            }

            return $tpls;
        }

        $products = [];
        $query = new \PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery();
        $query->setResultsPerPage($isProductController ? $limit + 1 : $limit);
        $query->setQueryType($listing);

        switch ($listing) {
            case 'category':
                if ($id) {
                    $category = new \Category((int) $id);
                } elseif ($isProductController) {
                    $category = new \Category((int) $controller->getProduct()->id_category_default);
                } elseif ($controller instanceof \CategoryController) {
                    $category = $controller->getCategory();
                } else {
                    $category = new \Category($GLOBALS['context']->shop->id_category);
                }
                $searchProvider = new \PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider(Helper::$translator, $category);

                $query->setSortOrder(
                    'rand' === $order_by
                    ? \PrestaShop\PrestaShop\Core\Product\Search\SortOrder::random()
                    : new \PrestaShop\PrestaShop\Core\Product\Search\SortOrder('product', $order_by, $order_dir)
                );
                break;
            case 'prices-drop':
                $searchProvider = new \PrestaShop\PrestaShop\Adapter\PricesDrop\PricesDropProductSearchProvider(Helper::$translator);
                $query->setSortOrder(new \PrestaShop\PrestaShop\Core\Product\Search\SortOrder('product', $order_by, $order_dir));
                break;
            case 'new-products':
                $searchProvider = new \PrestaShop\PrestaShop\Adapter\NewProducts\NewProductsProductSearchProvider(Helper::$translator);
                $query->setSortOrder(new \PrestaShop\PrestaShop\Core\Product\Search\SortOrder('product', $order_by, $order_dir));
                break;
            case 'best-sales':
                $searchProvider = new \PrestaShop\PrestaShop\Adapter\BestSales\BestSalesProductSearchProvider(Helper::$translator);
                'position' === $order_by && $order_by = 'sales'; // fallback
                $query->setSortOrder(new \PrestaShop\PrestaShop\Core\Product\Search\SortOrder('product', $order_by, $order_dir));
                break;
            case 'related':
                if ($id) {
                    $products = self::getAccessoriesLight($id);
                } elseif ($controller instanceof \CartController) {
                    $cart = &$GLOBALS['smarty']->tpl_vars['cart']->value;
                    $i = count($cart['products']);

                    $exclude_ids = array_unique(array_map(function ($product) {
                        return $product->id;
                    }, $cart['products']));

                    while ($i--) {
                        $related_products = self::getAccessoriesLight($cart['products'][$i]->id);

                        foreach ($related_products as &$related_product) {
                            if (!in_array($related_product['id_product'], $exclude_ids)) {
                                $products[] = $related_product;
                                $exclude_ids[] = $related_product['id_product'];
                            }
                        }
                        if (count($products) > $limit) {
                            break;
                        }
                    }
                }
                if ('rand' === $order_by) {
                    shuffle($products);
                }
                if (count($products) > $limit) {
                    $products = array_slice($products, 0, $limit);
                }
                break;
            case 'manufacturer':
                if (!$id && $isProductController) {
                    $id = $controller->getProduct()->id_manufacturer;
                }
                $manufacturer = new \Manufacturer((int) $id);
                $searchProvider = new \PrestaShop\PrestaShop\Adapter\Manufacturer\ManufacturerProductSearchProvider(Helper::$translator, $manufacturer);
                $query->setSortOrder(new \PrestaShop\PrestaShop\Core\Product\Search\SortOrder('product', $order_by, $order_dir));
                break;
            case 'supplier':
                if (!$id && $isProductController) {
                    $id = $controller->getProduct()->id_supplier;
                }
                $supplier = new \Supplier((int) $id);
                $searchProvider = new \PrestaShop\PrestaShop\Adapter\Supplier\SupplierProductSearchProvider(Helper::$translator, $supplier);
                $query->setSortOrder(new \PrestaShop\PrestaShop\Core\Product\Search\SortOrder('product', $order_by, $order_dir));
                break;
        }

        if ('category' === $listing && !$id && $controller instanceof \CartController) {
            // Current Category on Cart Page
            $cart = &$GLOBALS['smarty']->tpl_vars['cart']->value;

            $category_ids = array_unique(array_map(function ($product) {
                return $product->id_category_default;
            }, $cart['products']));

            $exclude_ids = array_unique(array_map(function ($product) {
                return $product->id;
            }, $cart['products']));

            $productSearchContext = new \PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext($GLOBALS['context']);

            foreach ($category_ids as $id_category) {
                $category = new \Category($id_category);
                $searchProvider = new \PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider(Helper::$translator, $category);
                $result = $searchProvider->runQuery($productSearchContext, $query);

                foreach ($result->getProducts() as $product) {
                    if (!in_array($product['id_product'], $exclude_ids)) {
                        $products[] = $product;
                        $exclude_ids[] = $product['id_product'];

                        if (count($products) > $limit) {
                            break 2;
                        }
                    }
                }
            }
        } elseif ('related' !== $listing && isset($searchProvider)) {
            $result = $searchProvider->runQuery(new \PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext($GLOBALS['context']), $query);
            $products = $result->getProducts();
        }

        if ($isProductController) {
            $current_product_id = $controller->getProduct()->id;
            $products = array_filter($products, function ($product) use ($current_product_id) {
                return $product['id_product'] != $current_product_id;
            });
            if (count($products) > $limit) {
                array_pop($products);
            }
        }

        foreach ($products as &$product) {
            $tpls[] = self::$presenter->present(
                self::$presentationSettings,
                self::$assembler->assembleProduct($product),
                $GLOBALS['language']
            );
        }

        return $tpls;
    }

    protected function fetchMiniature(array &$settings, $product)
    {
        $id = $product['id'] . '-' . $this->getId();
        $title_class = 'elementor-title' . ($settings['title_display'] ? ' ce-display-' . $settings['title_display'] : '');
        $image_size = $settings['image_size'] ?: $this->imageSize;
        $cover = $product['cover'] ?: $GLOBALS['smarty']->tpl_vars['urls']->value['no_picture_image'];
        $webp = isset($cover['bySize'][$image_size]['sources']['webp']);
        $cover_url = [
            'desktop' => $webp ? $cover['bySize'][$image_size]['sources']['webp'] : $cover['bySize'][$image_size]['url'],
        ];
        if ($settings['image_size_tablet'] && $settings['image_size_tablet'] !== $image_size) {
            $cover_url['tablet'] = $webp ? $cover['bySize'][$settings['image_size_tablet']]['sources']['webp'] : $cover['bySize'][$settings['image_size_tablet']]['url'];
        }
        if ($settings['image_size_mobile'] && $settings['image_size_mobile'] !== $settings['image_size_tablet']) {
            $cover_url['mobile'] = $webp ? $cover['bySize'][$settings['image_size_mobile']]['sources']['webp'] : $cover['bySize'][$settings['image_size_mobile']]['url'];
        }
        $cover_alt = !empty($product['cover']['legend']) ? $product['cover']['legend'] : $product['name'];
        $aria_label = $product['name'];
        $product['show_price'] && $aria_label .= ', ' . $product['price'];

        if ($settings['show_description']) {
            $description = strip_tags($product['description_short']);

            if (!empty($settings['description_length']) && \Tools::strlen($description) > $settings['description_length']) {
                $description = rtrim(\Tools::substr($description, 0, \Tools::strpos($description, ' ', $settings['description_length'])), '-,.') . '…';
            }
        }
        $this->setRenderAttribute('article', [
            'data-id-product' => $product['id_product'],
            'data-id-product-attribute' => $product['id_product_attribute'],
        ]);
        ob_start();
        ?>
        <article class="elementor-product-miniature" <?php $this->printRenderAttributeString('article'); ?>>
            <a class="elementor-product-link" href="<?php escape($product['url']); ?>" aria-label="<?php escape($aria_label); ?>" aria-describedby="elementor-description-<?php escape($id); ?> elementor-category-<?php escape($id); ?>"></a>
            <div class="elementor-image">
                <picture class="elementor-cover-image">
                <?php if (isset($cover_url['mobile'])) { ?>
                    <source media="(max-width: 767px)" srcset="<?php escape($cover_url['mobile']); ?>">
                <?php } ?>
                <?php if (isset($cover_url['tablet'])) { ?>
                    <source media="(max-width: 991px)" srcset="<?php escape($cover_url['tablet']); ?>">
                <?php } ?>
                    <img src="<?php escape($cover_url['desktop']); ?>" loading="lazy" alt="<?php escape($cover_alt); ?>"
                        width="<?php echo (int) $cover['bySize'][$image_size]['width']; ?>" height="<?php echo (int) $cover['bySize'][$image_size]['height']; ?>">
                </picture>
            <?php if ($settings['show_second_image'] && count($product['images']) > 1) {
                $image = $product['images'][$cover['id_image'] == $product['images'][0]['id_image'] ? 1 : 0];
                $bySize = $image['bySize']; ?>
                <picture class="elementor-second-image">
                <?php if (isset($cover_url['mobile'])) { ?>
                    <source media="(max-width: 767px)" srcset="<?php escape($webp ? $bySize[$settings['image_size_mobile']]['sources']['webp'] : $bySize[$settings['image_size_mobile']]['url']); ?>">
                <?php } ?>
                <?php if (isset($cover_url['tablet'])) { ?>
                    <source media="(max-width: 991px)" srcset="<?php escape($webp ? $bySize[$settings['image_size_tablet']]['sources']['webp'] : $bySize[$settings['image_size_tablet']]['url']); ?>">
                <?php } ?>
                    <img src="<?php escape($webp ? $bySize[$image_size]['sources']['webp'] : $bySize[$image_size]['url']); ?>" loading="lazy" alt="<?php escape($image['legend']); ?>"
                        width="<?php echo (int) $bySize[$image_size]['width']; ?>" height="<?php echo (int) $bySize[$image_size]['height']; ?>">
                </picture>
            <?php } ?>
            <?php if ($settings['show_qv']) { ?>
                <a href="#ce-action=quickview" class="elementor-button elementor-quick-view" role="button">
                    <span class="elementor-button-content-wrapper">
                    <?php if ($qv_icon = IconsManager::getBcIcon($settings, 'qv_icon')) { ?>
                        <span class="elementor-button-icon elementor-align-icon-<?php escape($settings['qv_icon_align']); ?>" aria-hidden="true"><?php echo $qv_icon; ?></span>
                    <?php } ?>
                        <span class="elementor-button-text"><?php echo $settings['qv_text']; ?></span>
                    </span>
                </a>
            <?php } ?>
            </div>
        <?php foreach (['left', 'right'] as $position) { ?>
            <div class="elementor-badges-<?php escape($position); ?>">
            <?php foreach ($settings['show_badges'] as $badge) { ?>
                <?php if ($position === $settings['badge_' . $badge . '_position'] && !empty($product['flags'][self::FLAGS[$badge]])) { ?>
                    <div class="elementor-badge elementor-badge-<?php escape($badge); ?>">
                        <?php echo $settings['badge_' . $badge . '_text'] ?: $product['flags'][self::FLAGS[$badge]]['label']; ?>
                    </div>
                <?php } ?>
            <?php } ?>
            </div>
        <?php } ?>
            <div class="elementor-content">
            <?php if ($settings['show_category']) { ?>
                <div class="elementor-category" id="elementor-category-<?php escape($id); ?>">
                    <span class="screen-reader-text"><?php _e('Category: %category_name%', 'Shop.Theme.Catalog', ['%category_name%' => '']); ?></span><?php echo $product['category_name']; ?>
                </div>
            <?php } ?>
                <<?php Utils::printValidatedHtmlTag($settings['title_tag']); ?> class="<?php escape($title_class); ?>"><?php echo $product['name']; ?></<?php Utils::printValidatedHtmlTag($settings['title_tag']); ?>>
            <?php if (!empty($description)) { ?>
                <div class="elementor-description" id="elementor-description-<?php escape($id); ?>"><?php echo $description; ?></div>
            <?php } ?>
            <?php if ($product['show_price']) { ?>
                <div class="elementor-price-wrapper">
                <?php if ($product['has_discount'] && $settings['show_regular_price']) { ?>
                    <del class="elementor-price-regular"><?php echo $product['regular_price']; ?></del>
                <?php } ?>
                    <span class="elementor-price"><?php echo $product['price']; ?></span>
                </div>
            <?php } ?>
            </div>
        <?php if ($settings['show_atc'] && !$this->catalog && $product['available_for_order']) { ?>
            <form class="elementor-atc<?php $settings['atc_type'] && print ' elementor-button-' . esc_attr($settings['atc_type']); ?>" action="<?php escape($product['add_to_cart_url']); ?>">
                <input type="hidden" name="qty" value="<?php echo (int) max(1, $product[
                    !empty($product['product_attribute_minimal_quantity']) ? 'product_attribute_minimal_quantity' : 'minimal_quantity'
                ]); ?>">
                <button type="submit" class="elementor-button elementor-size-<?php escape($settings['atc_size']); ?>"
                    data-button-action="add-to-cart"<?php $product['add_to_cart_url'] || print ' disabled'; ?>>
                    <span class="elementor-button-content-wrapper">
                    <?php if ($atc_icon = IconsManager::getBcIcon($settings, 'atc_icon')) { ?>
                        <span class="elementor-button-icon elementor-align-icon-<?php escape($settings['atc_icon_align']); ?>" aria-hidden="true"><?php echo $atc_icon; ?></span>
                    <?php } ?>
                        <span class="elementor-button-text"><?php echo $settings['atc_text']; ?></span>
                    </span>
                </button>
            </form>
        <?php } ?>
        </article>
        <?php
        return ob_get_clean();
    }

    protected function addRenderAttributes()
    {
        parent::addRenderAttributes();

        if ($this->getSettings('skin') === 'product' && !\Configuration::get('CE_PRODUCT_MINIATURE') && $wrapfix = Helper::getWrapfix()) {
            $this->addRenderAttribute('_wrapper', 'class', $wrapfix);
        }
    }

    protected function renderItemList(&$products)
    {
        if (self::$itemList || $GLOBALS['context']->controller instanceof \ProductListingFrontController) {
            return;
        }
        self::$itemList = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => [],
        ];
        foreach ($products as $i => &$product) {
            self::$itemList['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $i,
                'name' => $product['name'],
                'url' => $product['url'],
            ];
        }
        echo '<script type="application/ld+json">' . json_encode(self::$itemList) . '</script>';
    }

    public function renderPlainContent()
    {
    }
}
