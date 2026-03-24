<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks, Elementor
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 */
namespace CE;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CE\ModulesXCatalogXControlsXSelectCategory as SelectCategory;
use CE\ModulesXCatalogXModule as Catalog;
use CE\ModulesXCmsXControlsXSelectCategory as SelectCMSCategory;
use CE\ModulesXCmsXModule as Cms;

class ModulesXPremiumXWidgetsXArticles extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/87-widgets/premium-widgets/447-articles-widget';

    const REMOTE_RENDER = true;

    const WORDS_PER_MIN = 200;

    public function getName()
    {
        return 'articles';
    }

    public function getTitle()
    {
        return __('Articles');
    }

    public function getIcon()
    {
        return 'eicon-archive';
    }

    public function getCategories()
    {
        return ['premium'];
    }

    public function getKeywords()
    {
        return ['cms', 'category', 'categories', 'brands', 'manufacturers', 'suppliers', 'pages', 'articles', 'grid', 'item', 'loop', 'cards'];
    }

    public function getStyleDepends()
    {
        return ['widget-articles', 'e-transitions'];
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_content',
            [
                'label' => $this->getTitle(),
            ]
        );

        $this->addControl(
            'source',
            [
                'label' => __('Source'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'category' => __('Catalog') . ' › ' . __('Categories', 'Shop.Theme.Catalog'),
                    'manufacturer' => __('Catalog') . ' › ' . __('Brands', 'Shop.Theme.Catalog'),
                    'supplier' => __('Catalog') . ' › ' . __('Suppliers', 'Shop.Theme.Catalog'),
                    'cms_category' => __('CMS') . ' › ' . __('Categories', 'Shop.Theme.Catalog'),
                    'cms' => __('CMS') . ' › ' . __('Pages', 'Shop.Theme.Catalog'),
                ] + apply_filters('ce/articles/source/additional_options', []),
                'default' => 'cms',
            ]
        );

        $this->addControl(
            'id_category',
            [
                'label' => __('Category Root'),
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
                    'source' => 'category',
                ],
            ]
        );

        $this->addControl(
            'id_cms_category',
            [
                'label' => __('Category Root'),
                'label_block' => true,
                'type' => SelectCMSCategory::CONTROL_TYPE,
                'select2options' => [
                    'allowClear' => false,
                ],
                'options' => [
                    '0' => __('Current Category') . ' / ' . __('Default'),
                ],
                'default' => 0,
                'condition' => [
                    'source' => ['cms', 'cms_category'],
                ],
            ]
        );

        $this->addControl(
            'order_by',
            [
                'label' => __('Order By'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Default'),
                    'position' => __('Position'),
                    'title' => __('Title'),
                    'id' => __('Date'),
                    'rand' => __('Random'),
                ],
            ]
        );

        $this->addControl(
            'order_way',
            [
                'label' => __('Order Direction'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'asc' => __('Ascending'),
                    'desc' => __('Descending'),
                ],
                'default' => 'asc',
                'condition' => [
                    'order_by!' => ['', 'rand'],
                ],
            ]
        );

        $this->addControl(
            'offset',
            [
                'label' => __('Offset'),
                'type' => ControlsManager::NUMBER,
                'min' => 0,
                'max' => 100,
                'placeholder' => 0,
                'title' => __('Use this setting to skip over articles (e.g. 2 to skip over 2 articles)'),
            ]
        );

        $this->addControl(
            'limit',
            [
                'label' => __('Limit'),
                'type' => ControlsManager::NUMBER,
                'min' => 0,
                'max' => 100,
                'placeholder' => __('No'),
            ]
        );

        $this->addControl(
            'heading',
            [
                'label' => __('Heading'),
                'type' => ControlsManager::TEXT,
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('Enter your title'),
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
                'condition' => [
                    'heading!' => '',
                ],
            ]
        );

        $this->addControl(
            'heading_tag',
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
                'condition' => [
                    'heading!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            'layout',
            [
                'label' => __('Layout'),
                'type' => ControlsManager::SELECT,
                'options' => $layout_options = [
                    'grid' => __('Grid'),
                    'masonry' => __('Masonry'),
                    'inline' => __('Inline'),
                ],
                'default' => 'grid',
                'prefix_class' => 'ce-articles--layout%s-',
                'device_args' => [
                    ControlsStack::RESPONSIVE_TABLET => [
                        'options' => ['' => __('Default')] + $layout_options,
                    ],
                    ControlsStack::RESPONSIVE_MOBILE => [
                        'options' => ['' => __('Default')] + $layout_options,
                    ],
                ],
                'separator' => 'before',
            ]
        );

        $this->addResponsiveControl(
            'columns',
            [
                'label' => __('Columns'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'selectors' => [
                    '{{WRAPPER}}' => '--ce-columns: {{VALUE}};',
                ],
                'condition' => [
                    'layout!' => 'inline',
                ],
                'device_args' => [
                    ControlsStack::RESPONSIVE_TABLET => [
                        'condition' => null,
                        'conditions' => [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => 'layout_tablet',
                                    'operator' => 'in',
                                    'value' => ['grid', 'masonry'],
                                ],
                                [
                                    'terms' => [
                                        [
                                            'name' => 'layout_tablet',
                                            'value' => '',
                                        ],
                                        [
                                            'name' => 'layout',
                                            'operator' => 'in',
                                            'value' => ['grid', 'masonry'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    ControlsStack::RESPONSIVE_MOBILE => [
                        'condition' => null,
                        'conditions' => [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => 'layout_mobile',
                                    'operator' => 'in',
                                    'value' => ['grid', 'masonry'],
                                ],
                                [
                                    'terms' => [
                                        [
                                            'name' => 'layout_mobile',
                                            'value' => '',
                                        ],
                                        [
                                            'name' => 'layout_tablet',
                                            'operator' => 'in',
                                            'value' => ['grid', 'masonry'],
                                        ],
                                    ],
                                ],
                                [
                                    'terms' => [
                                        [
                                            'name' => 'layout_mobile',
                                            'value' => '',
                                        ],
                                        [
                                            'name' => 'layout_tablet',
                                            'value' => '',
                                        ],
                                        [
                                            'name' => 'layout',
                                            'operator' => 'in',
                                            'value' => ['grid', 'masonry'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->addResponsiveControl(
            'column_width',
            [
                'label' => __('Column Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 1000,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 300,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-article' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'layout' => 'inline',
                ],
                'device_args' => [
                    ControlsStack::RESPONSIVE_TABLET => [
                        'condition' => null,
                        'conditions' => [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => 'layout_tablet',
                                    'value' => 'inline',
                                ],
                                [
                                    'terms' => [
                                        [
                                            'name' => 'layout_tablet',
                                            'value' => '',
                                        ],
                                        [
                                            'name' => 'layout',
                                            'value' => 'inline',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    ControlsStack::RESPONSIVE_MOBILE => [
                        'condition' => null,
                        'conditions' => [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => 'layout_mobile',
                                    'value' => 'inline',
                                ],
                                [
                                    'terms' => [
                                        [
                                            'name' => 'layout_mobile',
                                            'value' => '',
                                        ],
                                        [
                                            'name' => 'layout_tablet',
                                            'value' => 'inline',
                                        ],
                                    ],
                                ],
                                [
                                    'terms' => [
                                        [
                                            'name' => 'layout_mobile',
                                            'value' => '',
                                        ],
                                        [
                                            'name' => 'layout_tablet',
                                            'value' => '',
                                        ],
                                        [
                                            'name' => 'layout',
                                            'value' => 'inline',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'scroll_snap',
            [
                'label' => __('Scroll Snap'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('On'),
                'label_off' => __('Off'),
                'return_value' => 'start',
                'selectors' => [
                    '{{WRAPPER}} .ce-scrollbar-x--auto > *' => 'scroll-snap-align: {{VALUE}};',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'layout',
                            'value' => 'inline',
                        ],
                        [
                            'name' => 'layout_tablet',
                            'value' => 'inline',
                        ],
                        [
                            'name' => 'layout_mobile',
                            'value' => 'inline',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'show_image',
            [
                'label' => __('Image'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'image_size_category',
            [
                'label' => __('Image Size'),
                'type' => ControlsManager::SELECT,
                'options' => GroupControlImageSize::getAllImageSizes('categories', true),
                'condition' => [
                    'source' => 'category',
                    'show_image!' => '',
                ],
            ]
        );

        $this->addControl(
            'image_size_manufacturer',
            [
                'label' => __('Image Size'),
                'type' => ControlsManager::SELECT,
                'options' => GroupControlImageSize::getAllImageSizes('manufacturers', true),
                'condition' => [
                    'source' => 'manufacturer',
                    'show_image!' => '',
                ],
            ]
        );

        $this->addControl(
            'image_size_supplier',
            [
                'label' => __('Image Size'),
                'type' => ControlsManager::SELECT,
                'options' => GroupControlImageSize::getAllImageSizes('suppliers', true),
                'condition' => [
                    'source' => 'supplier',
                    'show_image!' => '',
                ],
            ]
        );

        $rtl = is_rtl();

        $this->addResponsiveControl(
            'image_position',
            [
                'label' => __('Image Position'),
                'type' => ControlsManager::CHOOSE,
                'toggle' => false,
                'default' => 'top',
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'top' => [
                        'title' => __('Top'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'bottom' => [
                        'title' => __('Bottom'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => $rtl ? 'right' : 'left',
                'selectors_dictionary' => [
                    'top' => 'column',
                    'left' => $rtl ? 'row-reverse' : 'row',
                    'right' => $rtl ? 'row' : 'row-reverse',
                    'bottom' => 'column-reverse',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-article' => 'flex-direction: {{VALUE}}',
                ],
                'condition' => [
                    'show_image!' => '',
                ],
                'device_args' => [
                    ControlsStack::RESPONSIVE_TABLET => [
                        'toggle' => true,
                    ],
                    ControlsStack::RESPONSIVE_MOBILE => [
                        'toggle' => true,
                    ],
                ],
            ]
        );

        $this->addControl(
            'show_title',
            [
                'label' => __('Title'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'title_display',
            [
                'label' => __('Title Display'),
                'type' => ControlsManager::CHOOSE,
                'options' => WidgetHeading::getDisplaySizes(),
                'style_transfer' => true,
                'condition' => [
                    'show_title!' => '',
                ],
            ]
        );

        $this->addControl(
            'title_tag',
            [
                'label' => __('Title HTML Tag'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                ],
                'default' => 'h3',
                'condition' => [
                    'show_title!' => '',
                ],
            ]
        );

        $this->addControl(
            'subtitle',
            [
                'label' => __('Subtitle'),
                'type' => ControlsManager::SELECT,
                'options' => _CE_ADMIN_ ? [
                    '' => __('None'),
                    'meta_title' => __('Meta title', 'Admin.Global'),
                    'meta_description' => __('Meta description', 'Admin.Global'),
                    'read_time' => __('Read time'),
                    'custom' => __('Custom'),
                ] : [],
                'default' => 'meta_title',
            ]
        );

        $this->addControl(
            'subtitle_before',
            [
                'label' => __('Before'),
                'type' => ControlsManager::TEXT,
                'condition' => [
                    'subtitle' => 'read_time',
                ],
            ]
        );

        $this->addControl(
            'subtitle_after',
            [
                'label' => __('After'),
                'type' => ControlsManager::TEXT,
                'condition' => [
                    'subtitle' => 'read_time',
                ],
            ]
        );

        $this->addControl(
            'subtitle_text',
            [
                'show_label' => false,
                'type' => ControlsManager::TEXT,
                'label_block' => true,
                'placeholder' => __('Enter your title'),
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'subtitle' => 'custom',
                ],
            ]
        );

        $this->addControl(
            'show_excerpt',
            [
                'label' => __('Excerpt'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'excerpt_length',
            [
                'label' => __('Excerpt Length'),
                'type' => ControlsManager::NUMBER,
                'min' => 1,
                'default' => 100,
                'condition' => [
                    'show_excerpt!' => '',
                ],
            ]
        );

        $this->addControl(
            'show_cta',
            [
                'label' => __('Call to Action'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'read_more',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
                'placeholder' => \Translate::getModuleTranslation('creativeelements', 'Read more', '') . ' »',
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'show_cta!' => '',
                    'source!' => ['category', 'manufacturer', 'supplier'],
                ],
            ]
        );

        $this->addControl(
            'view_products',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
                'placeholder' => Helper::$translator->trans('View products', [], 'Shop.Theme.Actions'),
                'condition' => [
                    'show_cta!' => '',
                    'source' => ['category', 'manufacturer', 'supplier'],
                ],
            ]
        );

        $this->addControl(
            'link_click',
            [
                'label' => __('Apply Link on'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Default'),
                    'box' => __('Whole Box'),
                ],
                'separator' => 'before',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_empty',
            [
                'label' => __('No Results'),
            ]
        );

        $this->addControl(
            'empty_message',
            [
                'label' => __('Empty Message'),
                'type' => ControlsManager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->endControlsSection();

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

        $this->endControlsSection();

        $this->startControlsSection(
            'section_layout_style',
            [
                'label' => __('Layout'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addResponsiveControl(
            'column_gap',
            [
                'label' => __('Columns Gap'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-articles' => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'row_gap',
            [
                'label' => __('Rows Gap'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-articles' => 'row-gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.ce-articles--layout-masonry .ce-article' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.ce-articles--layout-tablet-masonry .ce-article' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.ce-articles--layout-mobile-masonry .ce-article' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'alignment',
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
                'prefix_class' => 'elementor%s-align-',
            ]
        );

        $this->addResponsiveControl(
            'vertical_alignment',
            [
                'label' => __('Vertical Alignment'),
                'type' => ControlsManager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'top' => [
                        'title' => __('Top'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'middle' => [
                        'title' => __('Middle'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => __('Bottom'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'middle',
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'middle' => 'center',
                    'bottom' => 'flex-end',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-article' => 'align-items: {{VALUE}}',
                ],
                'condition' => [
                    'image_position' => ['left', 'right'],
                ],
                'device_args' => [
                    ControlsStack::RESPONSIVE_TABLET => [
                        'condition' => null,
                        'conditions' => [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => 'image_position_tablet',
                                    'operator' => 'in',
                                    'value' => ['left', 'right'],
                                ],
                                [
                                    'terms' => [
                                        [
                                            'name' => 'image_position_tablet',
                                            'value' => '',
                                        ],
                                        [
                                            'name' => 'image_position',
                                            'operator' => 'in',
                                            'value' => ['left', 'right'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    ControlsStack::RESPONSIVE_MOBILE => [
                        'condition' => null,
                        'conditions' => [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => 'image_position_mobile',
                                    'operator' => 'in',
                                    'value' => ['left', 'right'],
                                ],
                                [
                                    'terms' => [
                                        [
                                            'name' => 'image_position_mobile',
                                            'value' => '',
                                        ],
                                        [
                                            'name' => 'image_position_tablet',
                                            'operator' => 'in',
                                            'value' => ['left', 'right'],
                                        ],
                                    ],
                                ],
                                [
                                    'terms' => [
                                        [
                                            'name' => 'image_position_mobile',
                                            'value' => '',
                                        ],
                                        [
                                            'name' => 'image_position_tablet',
                                            'value' => '',
                                        ],
                                        [
                                            'name' => 'image_position',
                                            'operator' => 'in',
                                            'value' => ['left', 'right'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_box_style',
            [
                'label' => __('Box'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->startControlsTabs('box_tabs');

        $this->startControlsTab(
            'box_tab_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'box_shadow',
                'selector' => '{{WRAPPER}} .ce-article',
            ]
        );

        $this->addControl(
            'box_bg_color',
            [
                'label' => __('Background Color'),
                'type' => controlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-article' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'box_border_color',
            [
                'label' => __('Border Color'),
                'type' => controlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-article' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'box_tab_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'box_shadow_hover',
                'selector' => '{{WRAPPER}} .ce-article:hover',
            ]
        );

        $this->addControl(
            'box_bg_color_hover',
            [
                'label' => __('Background Color'),
                'type' => controlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-article:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'box_border_color_hover',
            [
                'label' => __('Border Color'),
                'type' => controlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-article:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addResponsiveControl(
            'box_border_width',
            [
                'label' => __('Border Width'),
                'type' => ControlsManager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .ce-article' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->addResponsiveControl(
            'box_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ce-article' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            'box_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-article' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            'content_padding',
            [
                'label' => __('Content Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-article__text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_image_style',
            [
                'label' => __('Image'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'show_image!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            'image_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ce-article' => 'gap: {{SIZE}}{{UNIT}}',
                ],
                'default' => [
                    'size' => 20,
                ],
            ]
        );

        $this->addResponsiveControl(
            'image_ratio',
            [
                'label' => __('Aspect Ratio'),
                'type' => ControlsManager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0.25,
                        'max' => 4,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-article__thumbnail img' => 'aspect-ratio: {{SIZE}};',
                ],
                'condition' => [
                    'show_image!' => '',
                ],
            ]
        );

        $this->addControl(
            'image_object_fit',
            [
                'label' => __('Object Fit'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Default'),
                    'fill' => _x('Fill', 'Background Control'),
                    'cover' => _x('Cover', 'Background Control'),
                    'contain' => _x('Contain', 'Background Control'),
                    'scale-down' => _x('Scale Down', 'Background Control'),
                ],
                'default' => 'cover',
                'selectors' => [
                    '{{WRAPPER}} .ce-article__thumbnail img' => 'object-fit: {{VALUE}};',
                ],
                'condition' => [
                    'show_image!' => '',
                    'image_ratio[size]!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            'image_width',
            [
                'label' => __('Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-article__thumbnail' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_image!' => '',
                ],
            ]
        );

        $this->addControl(
            'image_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ce-article__thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->startControlsTabs('image_tabs');

        $this->startControlsTab(
            'image_tab_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addGroupControl(
            GroupControlCssFilter::getType(),
            [
                'name' => 'image_filters',
                'selector' => '{{WRAPPER}} .ce-article__thumbnail img',
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addGroupControl(
            GroupControlCssFilter::getType(),
            [
                'name' => 'image_filters_hover',
                'selector' => '{{WRAPPER}} .ce-article:hover .ce-article__thumbnail img',
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'image_hover_animation',
            [
                'label' => __('Hover Animation'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('None'),
                    'zoom-in' => 'Zoom In',
                    'zoom-out' => 'Zoom Out',
                    'move-left' => 'Move Left',
                    'move-right' => 'Move Right',
                    'move-up' => 'Move Up',
                    'move-down' => 'Move Down',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'image_effect_duration',
            [
                'label' => __('Transition Duration') . ' (ms)',
                'type' => ControlsManager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 3000,
                        'step' => 50,
                    ],
                ],
                'default' => [
                    'size' => 800,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-article__thumbnail img' => 'transition-duration: {{SIZE}}ms; transition-property: all;',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_content_style',
            [
                'label' => __('Content'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'heading_title_style',
            [
                'label' => __('Title'),
                'type' => ControlsManager::HEADING,
            ]
        );

        $this->addControl(
            'title_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_2,
                ],
                'selectors' => [
                    '.ce-article__title a:not(#e)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'title_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .ce-article__title a',
            ]
        );

        $this->addControl(
            'title_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ce-article__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'heading_subtitle_style',
            [
                'label' => __('Subtitle'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'subtitle!' => '',
                ],
            ]
        );

        $this->addControl(
            'subtitle_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-article__subtitle' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'subtitle!' => '',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'subtitle_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_2,
                'selector' => '{{WRAPPER}} .ce-article__subtitle',
                'condition' => [
                    'subtitle!' => '',
                ],
            ]
        );

        $this->addControl(
            'subtitle_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ce-article__subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'subtitle!' => '',
                ],
            ]
        );

        $this->addControl(
            'heading_excerpt_style',
            [
                'label' => __('Excerpt'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'show_excerpt!' => '',
                ],
            ]
        );

        $this->addControl(
            'excerpt_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-article__excerpt' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_excerpt!' => '',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'excerpt_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} .ce-article__excerpt',
                'condition' => [
                    'show_excerpt!' => '',
                ],
            ]
        );

        $this->addControl(
            'excerpt_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ce-article__excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_excerpt!' => '',
                ],
            ]
        );

        $this->addControl(
            'heading_cta_style',
            [
                'label' => __('Call to Action'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'show_cta!' => '',
                ],
            ]
        );

        $this->addControl(
            'cta_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_4,
                ],
                'selectors' => [
                    '{{WRAPPER}} a.ce-article__cta:not(#e)' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_cta!' => '',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'cta_typography',
                'selector' => '{{WRAPPER}} a.ce-article__cta',
                'scheme' => SchemeTypography::TYPOGRAPHY_4,
                'condition' => [
                    'show_cta!' => '',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_empty_style',
            [
                'label' => __('No Results'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'empty_message!' => '',
                ],
            ]
        );

        $this->addControl(
            'empty_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-articles__empty' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'empty_typography',
                'selector' => '{{WRAPPER}} .ce-articles__empty',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
            ]
        );

        $this->endControlsSection();
    }

    protected function getArticles($type, $offset, $limit, $order_by, $order_way, $show_image)
    {
        $articles = [];
        $context = $GLOBALS['context'];
        $id_lang = $context->language->id;
        $id_shop = $context->shop->id;
        $controller = $context->controller;
        $vars = &$GLOBALS['smarty']->tpl_vars;
        $offset = (int) $offset;
        $limit = (int) $limit;
        $order_way = strtoupper($order_way);
        $order_map = [
            '' => '',
            'id' => 'id_' . $type,
            'title' => 'name',
            'position' => 'position',
            'rand' => 'RAND()',
        ];
        switch ($type) {
            case 'cms':
                $id = $this->getSettings('id_cms_category');

                if ($id || !isset($vars['cms_pages']) && $id = !empty($vars['cms']->value) ? $vars['cms']->value['id_cms_category'] : 1) {
                    $order_map['title'] = 'meta_title';
                    $current_cms_id = !empty($vars['cms']->value) ? $vars['cms']->value['id'] : 0;
                    $rows = Cms::getSubPages($id, $id_lang, $id_shop, $order_map[$order_by], $order_way, $limit, $offset, $current_cms_id);
                } else {
                    $order_map = false;
                    $rows = &$vars['cms_pages']->value;
                }
                foreach ($rows as &$row) {
                    $articles[] = [
                        'id' => $row['id_cms'],
                        'position' => $row['position'],
                        'url' => &$row['link'],
                        'image' => $show_image ? self::getFirstImage($row['content']) : null,
                        'title' => &$row['meta_title'],
                        'excerpt' => $text = self::getExcerpt($row['content']),
                        'meta_title' => &$row['head_seo_title'],
                        'meta_description' => &$row['meta_description'],
                        'read_time' => self::getReadTime($text),
                    ];
                }
                break;
            case 'cms_category':
                $id = $this->getSettings('id_cms_category');

                if ($id || !isset($vars['sub_categories']) && $id = !empty($vars['cms']->value['id_cms_category']) ? $vars['cms']->value['id_cms_category'] : 1) {
                    $rows = Cms::getSubCategories($id, $id_lang, $id_shop, $order_map[$order_by], $order_way, $limit, $offset);
                } else {
                    $order_map = false;
                    $rows = &$vars['sub_categories']->value;
                }
                foreach ($rows as &$row) {
                    $articles[] = [
                        'id' => $row['id_cms_category'],
                        'position' => $row['position'],
                        'url' => &$row['link'],
                        'image' => $show_image ? self::getFirstImage($row['description']) : null,
                        'title' => &$row['name'],
                        'excerpt' => $text = self::getExcerpt($row['description']),
                        'meta_title' => &$row['meta_title'],
                        'meta_description' => &$row['meta_description'],
                        'read_time' => self::getReadTime($text),
                    ];
                }
                break;
            case 'category':
                $id = $this->getSettings('id_category');
                $show_image && $extension = (
                    $dimensions = GroupControlImageSize::getDimensions('categories', $image_size = $this->getSettings('image_size_category'))
                ) && strpos(\Configuration::get('PS_IMAGE_FORMAT'), 'webp') !== false ? 'webp' : 'jpg';

                if ($id || !isset($vars['subcategories']) && $id = isset($controller->product) ? $controller->product->id_category_default : $context->shop->id_category) {
                    $rows = Catalog::getSubCategories($id, $id_lang, $id_shop, $order_map[$order_by], $order_way, $limit, $offset);
                } else {
                    $order_map = false;
                    $rows = &$vars['subcategories']->value;
                }
                foreach ($rows as &$row) {
                    $articles[] = [
                        'id' => $row['id_category'],
                        'position' => $row['position'],
                        'url' => $row['url'],
                        'image' => $show_image ? [
                            'url' => Helper::$link->getCatImageLink($row['link_rewrite'], $row['id_image'], $dimensions ? $image_size : '', $extension),
                            'alt' => $row['name'],
                        ] + $dimensions : null,
                        'title' => $row['name'],
                        'excerpt' => $text = self::getExcerpt($row['description']),
                        'meta_title' => $row['meta_title'],
                        'meta_description' => $row['meta_description'],
                        'read_time' => self::getReadTime($text),
                    ];
                }
                break;
            case 'manufacturer':
            case 'supplier':
                $id_key = "id_$type";
                $var_key = (int) _PS_VERSION_ < 9 || 'manufacturer' === $type ? 'brands' : 'suppliers';
                $show_image && $extension = (
                    $dimensions = GroupControlImageSize::getDimensions($type . 's', $image_size = $this->getSettings('image_size_' . $type))
                ) && strpos(\Configuration::get('PS_IMAGE_FORMAT'), 'webp') !== false ? 'webp' : 'jpg';
                $get_image_link_method = 'get' . $type . 'ImageLink';

                if (isset($vars[$var_key]) && $type === $vars['page']->value['page_name']) {
                    $order_map = false;
                    $rows = &$vars[$var_key]->value;
                    $subtitle = $this->getSettings('subtitle');
                    $meta_key = strpos($subtitle, 'meta') === 0 ? $subtitle : '';

                    if ($meta_key && $ids = array_column($rows, $id_key)) {
                        $meta_rows = array_column(\Db::getInstance()->executeS('
                            SELECT ' . bqSQL($id_key) . ', ' . bqSQL($meta_key) . ' FROM ' . bqSQL(_DB_PREFIX_ . $type . '_lang') . '
                            WHERE `id_lang` = ' . (int) $id_lang . ' AND ' . bqSQL($id_key) . ' IN (' . implode(',', array_map('intval', $ids)) . ')
                        '), $meta_key, $id_key);
                    }
                    foreach ($rows as &$row) {
                        $row['meta_title'] = $row['meta_description'] = '';
                        $meta_key && $row[$meta_key] = $meta_rows[$row[$id_key]];
                    }
                } else {
                    $order_map['position'] = 'name';
                    $catalog_get_rows = [Catalog::class, 'get' . $type . 's'];
                    $rows = $catalog_get_rows($id_lang, $id_shop, $order_map[$order_by], $order_way, $limit, $offset);
                }
                foreach ($rows as &$row) {
                    $text = self::getExcerpt($row['description']);
                    $nb_products = isset($row['nb_products']) ? $row['nb_products'] : '';
                    $articles[] = [
                        'id' => $row[$id_key],
                        'position' => $row['name'],
                        'url' => $row['url'],
                        'image' => $show_image ? [
                            'url' => Helper::$link->$get_image_link_method($row[$id_key], $dimensions ? $image_size : '', $extension),
                            'alt' => $row['name'],
                        ] + $dimensions : null,
                        'title' => $row['name'],
                        'excerpt' => trim(!empty($row['short_description']) ? self::getExcerpt($row['short_description']) : '') ?: $text,
                        'meta_title' => $row['meta_title'],
                        'meta_description' => $row['meta_description'],
                        'nb_products' => is_numeric($nb_products)
                            ? __(1 == $nb_products ? '%number% product' : '%number% products', 'Shop.Theme.Catalog', ['%number%' => $nb_products])
                            : $nb_products,
                        'read_time' => self::getReadTime($text),
                    ];
                }
                break;
            default:
                $articles = apply_filters("ce/articles/$type", [], [
                    'offset' => $offset,
                    'limit' => $limit,
                    'order_by' => $order_by,
                    'order_way' => $order_way,
                    'show_image' => $show_image,
                ]);
                break;
        }
        if (!$order_map) {
            if ('rand' === $order_by) {
                shuffle($articles);
            } elseif ($order_by) {
                array_multisort(array_column($articles, $order_by), 'ASC' === $order_way ? SORT_ASC : SORT_DESC, $articles);
            }
        }

        return $order_map || !$limit && !$offset ? $articles : array_slice($articles, $offset, $limit ?: null);
    }

    public static function getFirstImage(&$html)
    {
        if (preg_match('/<img[^>]+>/', $html, $img) && preg_match_all('/(\w+)="([^"]+)"/', $img[0], $attrs)) {
            $image = array_combine($attrs[1], $attrs[2]);
            $src = $image['src'];
            unset($image['src']);

            return [
                'url' => strpos($src, _PS_IMG_) === 0 ? substr($src, strlen(__PS_BASE_URI__)) : $src,
            ] + $image;
        }
    }

    public static function getExcerpt($html)
    {
        $text = preg_replace('/^(?:\s*<(h[1-6]|a\b)[^>]*>.*?<\/\1>\s*)+|\s*<[^>]*>\s*/si', ' ', $html);

        return preg_replace('/\s+/', ' ', trim($text));
    }

    public static function getReadTime(&$text)
    {
        $mins = ceil(str_word_count($text) / self::WORDS_PER_MIN);

        return sprintf(_n('%s min', '%s mins', $mins), $mins);
    }

    public function render()
    {
        $settings = $this->getSettingsForDisplay();
        $articles = $this->getArticles($settings['source'], $settings['offset'], $settings['limit'], $settings['order_by'], $settings['order_way'], $settings['show_image']);

        if (!$articles && '' === $settings['empty_message']) {
            return;
        }
        $title_tag = $settings['title_tag'];
        $display_class = $settings['title_display'] ? ' ce-display-' . $settings['title_display'] : '';
        $excerpt_length = $settings['show_excerpt'] ? $settings['excerpt_length'] : 0;
        $image_hover_class = $settings['image_hover_animation'] ? ' elementor-bg-transform-' . $settings['image_hover_animation'] : '';

        if ('' !== $settings['heading']) {
            $heading_tag = Utils::validateHtmlTag($settings['heading_tag']);
            $this->addRenderAttribute('heading', 'class', 'elementor-heading-title');
            $settings['heading_display'] && $this->addRenderAttribute('heading', 'class', 'ce-display-' . $settings['heading_display']);
            $this->addInlineEditingAttributes('heading');

            echo "<$heading_tag {$this->getRenderAttributeString('heading')}>$settings[heading]</$heading_tag>";
        } ?>
        <div class="ce-articles ce-scrollbar-x--auto">
        <?php foreach ($articles as &$article) { ?>
            <article class="ce-article<?php escape($image_hover_class); ?>">
            <?php if ($settings['show_image'] && $article['image']) { ?>
                <a class="ce-article__thumbnail" href="<?php escape($article['url']); ?>" tabindex="-1">
                    <?php echo GroupControlImageSize::getAttachmentImageHtml($article, 'image', 'elementor-bg'); ?>
                </a>
            <?php } ?>
                <div class="ce-article__text">
                <?php if ($settings['show_title']) { ?>
                    <<?php Utils::printValidatedHtmlTag($title_tag); ?> class="ce-article__title<?php escape($display_class); ?>">
                        <a href="<?php escape($article['url']); ?>"><?php echo $article['title']; ?></a>
                    </<?php Utils::printValidatedHtmlTag($title_tag); ?>>
                <?php } ?>
                <?php if ($subtitle = $settings['subtitle']) { ?>
                    <div class="ce-article__subtitle">
                        <?php 'read_time' === $subtitle && print $settings['subtitle_before']; ?>
                        <?php echo 'custom' === $subtitle ? $settings['subtitle_text'] : $article[$subtitle]; ?>
                        <?php 'read_time' === $subtitle && print $settings['subtitle_after']; ?>
                    </div>
                <?php } ?>
                <?php if ($excerpt_length) { ?>
                    <p class="ce-article__excerpt">
                        <?php echo mb_strlen($article['excerpt']) > $excerpt_length ? mb_substr($article['excerpt'], 0, $excerpt_length) . '…' : $article['excerpt']; ?>
                    </p>
                <?php } ?>
                <?php if ($settings['show_cta']) { ?>
                    <a class="ce-article__cta" href="<?php escape($article['url']); ?>" tabindex="-1"><?php
                    if ('category' === $settings['source'] || 'manufacturer' === $settings['source'] || 'supplier' === $settings['source']) {
                        echo $settings['view_products'] ?: __('View products', 'Shop.Theme.Actions');
                    } else {
                        echo $settings['read_more'] ?: __('Read more') . ' »';
                    } ?>
                    </a>
                <?php } ?>
                </div>
            <?php if ('box' === $settings['link_click']) { ?>
                <a class="ce-article__link" href="<?php escape($article['url']); ?>" tabindex="-1"></a>
            <?php } ?>
            </article>
        <?php } ?>
        <?php if (!$articles) { ?>
            <div class="ce-articles__empty"><?php echo $settings['empty_message']; ?></div>
        <?php } ?>
        </div>
        <?php
    }
}
