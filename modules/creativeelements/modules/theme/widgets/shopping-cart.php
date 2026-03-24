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

class ModulesXThemeXWidgetsXShoppingCart extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/88-widgets/site-widgets/360-shopping-cart-widget';

    const REMOTE_RENDER = true;

    protected $imageSize;

    public function getName()
    {
        return 'shopping-cart';
    }

    public function getTitle()
    {
        return __('Shopping Cart');
    }

    public function getIcon()
    {
        return 'eicon-cart';
    }

    public function getCategories()
    {
        return ['theme-elements'];
    }

    public function getKeywords()
    {
        return ['shopping', 'cart', 'basket', 'bag'];
    }

    public function getStyleDepends()
    {
        return ['widget-shopping-cart', 'widget-alert'];
    }

    public function getFrontendSettings()
    {
        return [
            'modal_url' => Helper::$link->getModuleLink('creativeelements', 'ajax', [], true),
            'labels' => [
                'gift' => __('Gift', 'Shop.Theme.Checkout'),
                'facetLabelFacetValue' => __('%facet_label%: %facet_value%', 'Shop.Theme.Catalog'),
                'productDetails' => __('Product Details', 'Shop.Theme.Catalog'),
                'regularPrice' => __('Regular price', 'Shop.Theme.Catalog'),
                'removeFromCart' => __('remove from cart', 'Shop.Theme.Actions'),
            ],
        ] + parent::getFrontendSettings();
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_shopping_cart',
            [
                'label' => __('Shopping Cart'),
            ]
        );

        $this->addControl(
            'skin',
            [
                'label' => __('Skin'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'classic' => __('Classic'),
                    'sidebar' => __('Sidebar'),
                ],
                'default' => 'sidebar',
            ]
        );

        $this->addControl(
            'heading_toggle',
            [
                'label' => __('Toggle Button'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'show_subtotal',
            [
                'label' => __('Subtotal', 'Shop.Theme.Checkout'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
                'prefix_class' => 'elementor-cart--show-subtotal-',
            ]
        );

        $this->addControl(
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
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle' => 'text-align: {{VALUE}}',
                ],
                'condition' => [
                    '_element_width!' => 'auto',
                ],
            ]
        );

        $this->addControl(
            'toggle_size',
            [
                'label' => __('Size'),
                'type' => ControlsManager::CHOOSE,
                'toggle' => false,
                'options' => WidgetButton::getButtonSizes(),
                'default' => 'sm',
                'style_transfer' => true,
            ]
        );

        $this->addControl(
            'selected_icon',
            [
                'label' => __('Icon'),
                'label_block' => false,
                'type' => ControlsManager::ICONS,
                'skin' => 'inline',
                'fa4compatibility' => 'icon',
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
                    ],
                ],
                'default' => [
                    'value' => 'ceicon-basket-solid',
                    'library' => 'ce-icons',
                ],
            ]
        );

        $this->addControl(
            'icon_align',
            [
                'label' => __('Icon Position'),
                'type' => ControlsManager::SELECT,
                'default' => 'left',
                'options' => [
                    'left' => __('Before'),
                    'right' => __('After'),
                ],
                'prefix_class' => 'elementor-cart--align-icon-',
                'condition' => [
                    'show_subtotal!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            'toggle_icon_size',
            [
                'label' => __('Icon Size'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                    'em' => [
                        'max' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle .elementor-button-icon' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'selected_icon[value]!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            'toggle_icon_spacing',
            [
                'label' => __('Icon Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                    'em' => [
                        'max' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle a' => 'gap: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'show_subtotal!' => '',
                ],
            ]
        );

        $this->addControl(
            'items_indicator',
            [
                'label' => __('Items Indicator'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'none' => __('None'),
                    'bubble' => __('Bubble'),
                    // 'plain' => __('Plain'),
                ],
                'prefix_class' => 'elementor-cart--items-indicator-',
                'default' => 'bubble',
            ]
        );

        $this->addControl(
            'hide_empty_indicator',
            [
                'label' => __('Hide Empty'),
                'type' => ControlsManager::SWITCHER,
                'return_value' => 'hide',
                'prefix_class' => 'elementor-cart--empty-indicator-',
                'condition' => [
                    'items_indicator!' => 'none',
                ],
            ]
        );

        $this->addControl(
            'heading_atc_action',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Add to Cart Action'),
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'action_show_modal',
            [
                'label' => __('Show Modal'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('On'),
                'label_off' => __('Off'),
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'action_open_cart',
            [
                'label' => __('Open Shopping Cart'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('On'),
                'label_off' => __('Off'),
                'frontend_available' => true,
                'condition' => [
                    'skin' => 'sidebar',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_sidebar',
            [
                'label' => __('Sidebar'),
                'condition' => [
                    'skin' => 'sidebar',
                ],
            ]
        );

        $this->addControl(
            'title',
            [
                'label' => __('Title'),
                'type' => ControlsManager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
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
                    'title!' => '',
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
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'div',
                'condition' => [
                    'title!' => '',
                ],
            ]
        );

        $this->addControl(
            'empty_message',
            [
                'label' => __('Empty Message'),
                'type' => ControlsManager::TEXT,
                'label_block' => true,
                'default' => Helper::$translator->trans('Cart is empty', [], 'Shop.Notifications.Error'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->addControl(
            'remove_item_icon',
            [
                'label' => __('Remove Item Icon'),
                'label_block' => false,
                'type' => ControlsManager::ICONS,
                'skin' => 'inline',
                'exclude_inline_options' => ['svg'],
                'default' => [
                    'value' => 'far fa-circle-xmark',
                    'library' => 'fa-regular',
                ],
                'recommended' => [
                    'ce-icons' => [
                        'delete-left',
                        'close',
                        'times',
                    ],
                    'fa-solid' => [
                        'trash-can',
                        'trash',
                        'circle-xmark',
                        'xmark',
                        'eraser',
                    ],
                    'fa-regular' => [
                        'circle-xmark',
                        'trash-can',
                        'eraser',
                    ],
                ],
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'show_shipping',
            [
                'label' => __('Shipping cost', 'Shop.Theme.Checkout'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
                'prefix_class' => 'elementor-cart--show-shipping-',
            ]
        );

        $this->addControl(
            'heading_buttons',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Buttons'),
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'show_view_cart',
            [
                'label' => __('View Cart'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'prefix_class' => 'elementor-cart--show-view-cart-',
                'default' => 'yes',
            ]
        );

        $this->addControl(
            'view_cart_type',
            [
                'label' => __('Type'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Default'),
                    'primary' => __('Primary'),
                    'secondary' => __('Secondary'),
                ],
                'style_transfer' => true,
                'condition' => [
                    'show_view_cart!' => '',
                ],
            ]
        );

        $this->addControl(
            'view_cart',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
                'placeholder' => \Translate::getModuleTranslation('creativeelements', 'View Cart', ''),
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'show_view_cart!' => '',
                ],
            ]
        );

        $this->addControl(
            'view_cart_size',
            [
                'label' => __('Size'),
                'type' => ControlsManager::CHOOSE,
                'toggle' => false,
                'options' => WidgetButton::getButtonSizes(),
                'default' => 'md',
                'condition' => [
                    'show_view_cart!' => '',
                ],
                'style_transfer' => true,
                'separator' => 'after',
            ]
        );

        $this->addControl(
            'show_checkout',
            [
                'label' => __('Checkout', 'Shop.Theme.Actions'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'prefix_class' => 'elementor-cart--show-checkout-',
                'default' => 'yes',
            ]
        );

        $this->addControl(
            'checkout_type',
            [
                'label' => __('Type'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Default'),
                    'primary' => __('Primary'),
                    'secondary' => __('Secondary'),
                ],
                'condition' => [
                    'show_checkout!' => '',
                ],
                'style_transfer' => true,
            ]
        );

        $this->addControl(
            'checkout',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
                'placeholder' => Helper::$translator->trans('Checkout', [], 'Shop.Theme.Actions'),
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'show_checkout!' => '',
                ],
            ]
        );

        $this->addControl(
            'checkout_size',
            [
                'label' => __('Size'),
                'type' => ControlsManager::CHOOSE,
                'toggle' => false,
                'options' => WidgetButton::getButtonSizes(),
                'default' => 'md',
                'condition' => [
                    'show_checkout!' => '',
                ],
                'style_transfer' => true,
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_toggle_style',
            [
                'label' => __('Toggle Button'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'toggle_button_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .elementor-cart__toggle a',
                'condition' => [
                    'show_subtotal!' => '',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'toggle_button_text_shadow',
                'selector' => '{{WRAPPER}} .elementor-cart__toggle a',
                'condition' => [
                    'show_subtotal!' => '',
                ],
            ]
        );

        $this->startControlsTabs('toggle_button_colors');

        $this->startControlsTab('toggle_button_normal_colors', ['label' => __('Normal')]);

        $this->addControl(
            'toggle_button_text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle a:not(#e)' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'show_subtotal!' => '',
                ],
            ]
        );

        $this->addControl(
            'toggle_button_icon_color',
            [
                'label' => __('Icon Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle .elementor-button-icon' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'toggle_button_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle a' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'toggle_button_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle a' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'toggle_button_box_shadow',
                'selector' => '{{WRAPPER}} .elementor-cart__toggle a',
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab('toggle_button_hover_colors', ['label' => __('Hover')]);

        $this->addControl(
            'toggle_button_hover_text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle a:not(#e):hover, {{WRAPPER}} .elementor-cart__toggle a:not(#e):focus' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'show_subtotal!' => '',
                ],
            ]
        );

        $this->addControl(
            'toggle_button_hover_icon_color',
            [
                'label' => __('Icon Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle a:hover .elementor-button-icon, {{WRAPPER}} .elementor-cart__toggle a:focus .elementor-button-icon' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'toggle_button_hover_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle a:hover, {{WRAPPER}} .elementor-cart__toggle a:focus' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'toggle_button_hover_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle a:hover, {{WRAPPER}} .elementor-cart__toggle a:focus' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'toggle_button_hover_box_shadow',
                'selector' => '{{WRAPPER}} .elementor-cart__toggle a:hover, {{WRAPPER}} .elementor-cart__toggle a:focus',
            ]
        );

        $this->addControl(
            'toggle_button_hover_transition_duration',
            [
                'label' => __('Transition Duration'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['s', 'ms'],
                'default' => [
                    'unit' => 's',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle a' => 'transition: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'toggle_button_border_width',
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
                    '{{WRAPPER}} .elementor-cart__toggle a' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'toggle_button_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle a' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            'toggle_button_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'items_indicator_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Items Indicator'),
                'separator' => 'before',
                'condition' => [
                    'items_indicator!' => 'none',
                ],
            ]
        );
        $this->addControl(
            'items_indicator_text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle .elementor-button-icon[data-counter]:before' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'items_indicator!' => 'none',
                ],
            ]
        );

        $this->addControl(
            'items_indicator_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle .elementor-button-icon[data-counter]:before' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'items_indicator' => 'bubble',
                ],
            ]
        );

        $this->addControl(
            'items_indicator_distance_top',
            [
                'label' => __('Vertical Position'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => -50,
                        'max' => 50,
                    ],
                    'em' => [
                        'min' => -5,
                        'max' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle .elementor-button-icon[data-counter]:before' => 'top: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'items_indicator' => 'bubble',
                ],
            ]
        );

        $this->addControl(
            'items_indicator_distance_right',
            [
                'label' => __('Horizontal Position'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => -50,
                        'max' => 50,
                    ],
                    'em' => [
                        'min' => -5,
                        'max' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__toggle .elementor-button-icon[data-counter]:before' => 'inset-inline-end: calc(0em - {{SIZE}}{{UNIT}})',
                ],
                'condition' => [
                    'items_indicator' => 'bubble',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_cart_style',
            [
                'label' => __('Sidebar'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'skin' => 'sidebar',
                ],
            ]
        );

        $this->addResponsiveControl(
            'cart_width',
            [
                'label' => __('Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 600,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__main' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBackground::getType(),
            [
                'name' => 'cart_background',
                'selector' => '{{WRAPPER}} .elementor-cart__main',
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'cart',
                'selector' => '{{WRAPPER}} .elementor-cart__main',
                'fields_options' => [
                    'box_shadow_type' => [
                        'default' => 'yes',
                    ],
                    'box_shadow' => [
                        'default' => [
                            'horizontal' => 0,
                            'vertical' => 0,
                            'blur' => 20,
                            'spread' => 0,
                            'color' => 'rgba(0,0,0,0.2)',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'lightbox_color',
            [
                'label' => __('Overlay Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--e-cart-overlay-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'heading_close',
            [
                'label' => __('Close Button'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addResponsiveControl(
            'close_icon_size',
            [
                'label' => __('Icon Size'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} a.elementor-cart__close-button' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->startControlsTabs('close_icon_colors');

        $this->startControlsTab('close_icon_normal_colors', ['label' => __('Normal')]);

        $this->addControl(
            'lightbox_ui_color',
            [
                'label' => __('Icon Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-cart__close-button:not(#e)' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab('close_icon_hover_colors', ['label' => __('Hover')]);

        $this->addControl(
            'lightbox_ui_color_hover',
            [
                'label' => __('Icon Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-cart__close-button:not(#e):hover, {{WRAPPER}} a.elementor-cart__close-button:not(#e):focus' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'heading_title_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Title'),
                'condition' => [
                    'title!' => '',
                ],
                'separator' => 'before',
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .elementor-cart__title',
                'condition' => [
                    'title!' => '',
                ],
            ]
        );

        $this->addControl(
            'title_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__title' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'title!' => '',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTextStroke::getType(),
            [
                'name' => 'title_stroke',
                'selector' => '{{WRAPPER}} .elementor-cart__title',
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'title_shadow',
                'selector' => '{{WRAPPER}} .elementor-cart__title',
            ]
        );

        $this->addResponsiveControl(
            'title_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                    'em' => [
                        'max' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'heading_empty_message_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Empty Message'),
                'separator' => 'before',
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'empty_message_typography',
                'selector' => '{{WRAPPER}} .elementor-cart__empty-message',
            ]
        );

        $this->addControl(
            'empty_message_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__empty-message' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'heading_product_divider_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Divider'),
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'divider_style',
            [
                'label' => __('Style'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Default'),
                    'none' => __('None'),
                    'solid' => __('Solid'),
                    'double' => __('Double'),
                    'dotted' => __('Dotted'),
                    'dashed' => __('Dashed'),
                    'groove' => __('Groove'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__product:not(:last-of-type), {{WRAPPER}} .elementor-cart__products, {{WRAPPER}} .elementor-cart__summary' => 'border-bottom-style: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'divider_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__product:not(:last-of-type), {{WRAPPER}} .elementor-cart__products, {{WRAPPER}} .elementor-cart__summary' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'divider_style!' => 'none',
                ],
            ]
        );

        $this->addControl(
            'divider_width',
            [
                'label' => __('Weight'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 10,
                    ],
                    'em' => [
                        'max' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__product:not(:last-of-type), {{WRAPPER}} .elementor-cart__products, {{WRAPPER}} .elementor-cart__summary' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'divider_style!' => 'none',
                ],
            ]
        );

        $this->addResponsiveControl(
            'divider_gap',
            [
                'label' => __('Space Between'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                    'em' => [
                        'max' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__product, {{WRAPPER}} .elementor-cart__footer-buttons, {{WRAPPER}} .elementor-cart__summary' => 'padding-bottom: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .elementor-cart__product:not(:first-of-type), {{WRAPPER}} .elementor-cart__footer-buttons, {{WRAPPER}} .elementor-cart__summary' => 'padding-top: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_product_tabs_style',
            [
                'label' => __('Products'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'skin' => 'sidebar',
                ],
            ]
        );

        $this->addControl(
            'heading_product_image_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Product Image'),
            ]
        );

        $this->addGroupControl(
            GroupControlBorder::getType(),
            [
                'name' => 'product_image_border',
                'selector' => '{{WRAPPER}} .elementor-cart__product-image img',
            ]
        );

        $this->addResponsiveControl(
            'product_image_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__product-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'product_image_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .elementor-cart__product-image img',
            ]
        );

        $this->addControl(
            'heading_product_title_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Product Name'),
                'separator' => 'before',
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'product_title_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .elementor-cart__product-name a',
            ]
        );

        $this->startControlsTabs('product_title_colors');

        $this->startControlsTab('product_title_normal_colors', ['label' => __('Normal')]);

        $this->addControl(
            'product_title_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__product-name a:not(#e)' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab('product_title_hover_colors', ['label' => __('Hover')]);

        $this->addControl(
            'product_title_hover_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__product-name a:not(#e):hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'heading_product_attr_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Product Attributes'),
                'separator' => 'before',
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'product_attr_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} .elementor-cart__product-attr',
            ]
        );

        $this->addControl(
            'product_attr_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__product-attr' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'heading_product_price_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Product Price'),
                'separator' => 'before',
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'product_price_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .elementor-cart__product-price',
            ]
        );

        $this->addControl(
            'product_price_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__product-price' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'remove_icon_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Remove Item Icon'),
                'separator' => 'before',
                'condition' => [
                    'remove_item_icon[value]!' => '',
                ],
            ]
        );

        $this->addControl(
            'remove_icon_size',
            [
                'label' => __('Size'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                    'em' => [
                        'max' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__product-remove' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'remove_item_icon[value]!' => '',
                ],
            ]
        );

        $this->startControlsTabs(
            'remove_icon_colors',
            [
                'condition' => [
                    'remove_item_icon[value]!' => '',
                ],
            ]
        );

        $this->startControlsTab(
            'remove_icon_colors_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'remove_icon_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-cart__product-remove:not(#e)' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'remove_icon_colors_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'remove_icon_color_hover',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-cart__product-remove:not(#e):hover, {{WRAPPER}} a.elementor-cart__product-remove:not(#e):focus' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_summary',
            [
                'label' => !_CE_ADMIN_ ?: __('Summary', 'Admin.Global'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'skin' => 'sidebar',
                ],
            ]
        );

        $this->addControl(
            'subtotal_position',
            [
                'label' => __('Vertical Position'),
                'label_block' => false,
                'type' => ControlsManager::CHOOSE,
                'toggle' => false,
                'options' => [
                    '' => [
                        'title' => __('Top'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'bottom' => [
                        'title' => __('Bottom'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'selectors_dictionary' => [
                    'bottom' => 1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__products' => 'flex-grow: {{VALUE}}',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'subtotal_typography',
                'selector' => '{{WRAPPER}} .elementor-cart__summary',
            ]
        );

        $this->addControl(
            'subtotal_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__summary' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'heading_total_style',
            [
                'label' => __('Total'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'show_shipping!' => '',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'total_typography',
                'selector' => '{{WRAPPER}} .elementor-cart__summary strong',
                'condition' => [
                    'show_shipping!' => '',
                ],
            ]
        );

        $this->addControl(
            'total_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__summary strong' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'show_shipping!' => '',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_buttons',
            [
                'label' => __('Buttons'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'skin' => 'sidebar',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'show_view_cart',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'show_checkout',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'buttons_layout',
            [
                'label' => __('Layout'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'inline' => __('Inline'),
                    'stacked' => __('Stacked'),
                ],
                'default' => 'inline',
                'prefix_class' => 'elementor-cart--buttons-',
                'condition' => [
                    'show_view_cart!' => '',
                    'show_checkout!' => '',
                ],
            ]
        );

        $this->addControl(
            'space_between_buttons',
            [
                'label' => __('Space Between'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                    'em' => [
                        'max' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__footer-buttons' => 'gap: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'show_view_cart!' => '',
                    'show_checkout!' => '',
                ],
            ]
        );

        $this->addControl(
            'button_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-cart__footer-buttons .elementor-button' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'product_buttons_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .elementor-cart__footer-buttons .elementor-button',
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'heading_view_cart_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('View Cart'),
                'separator' => 'before',
                'condition' => [
                    'show_view_cart!' => '',
                ],
            ]
        );

        $this->startControlsTabs(
            'tabs_view_cart_style',
            [
                'condition' => [
                    'show_view_cart!' => '',
                ],
            ]
        );

        $this->startControlsTab(
            'tabs_view_cart_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'view_cart_text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button--cart:not(#e)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'view_cart_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button--cart' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'view_cart_box_shadow',
                'selector' => '{{WRAPPER}} .elementor-button--cart',
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tabs_view_cart_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'view_cart_text_color_hover',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button--cart:not(#e):hover, {{WRAPPER}} a.elementor-button--cart:not(#e):focus' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'view_cart_background_color_hover',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button--cart:hover, {{WRAPPER}} .elementor-button--cart:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'view_cart_border_color_hover',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button--cart:hover, {{WRAPPER}} .elementor-button--cart:focus' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'view_cart_border_border!' => '',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addGroupControl(
            GroupControlBorder::getType(),
            [
                'name' => 'view_cart_border',
                'selector' => '{{WRAPPER}} .elementor-button--cart',
                'condition' => [
                    'show_view_cart!' => '',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'view_cart_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button--cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'heading_checkout_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Checkout', 'Shop.Theme.Actions'),
                'separator' => 'before',
                'condition' => [
                    'show_checkout!' => '',
                ],
            ]
        );

        $this->startControlsTabs(
            'tabs_checkout_style',
            [
                'condition' => [
                    'show_checkout!' => '',
                ],
            ]
        );

        $this->startControlsTab(
            'tabs_checkout_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'checkout_text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button--checkout:not(#e)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'checkout_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button--checkout' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'checkout_box_shadow',
                'selector' => '{{WRAPPER}} .elementor-button--checkout',
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tabs_checkout_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'checkout_text_color_hover',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button--checkout:not(#e):hover, {{WRAPPER}} a.elementor-button--checkout:not(#e):focus' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'checkout_background_color_hover',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button--checkout:hover, {{WRAPPER}} .elementor-button--checkout:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'checkout_border_color_hover',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button--checkout:hover, {{WRAPPER}} .elementor-button--checkout:focus' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'checkout_border_border!' => '',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addGroupControl(
            GroupControlBorder::getType(),
            [
                'name' => 'checkout_border',
                'selector' => '{{WRAPPER}} .elementor-button--checkout',
                'condition' => [
                    'show_checkout!' => '',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'checkout_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button--checkout' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->endControlsSection();
    }

    protected function render()
    {
        $settings = $this->getSettingsForDisplay();
        $controller = $GLOBALS['context']->controller;
        $cart = &$GLOBALS['smarty']->tpl_vars['cart']->value;

        $this->addRenderAttribute('toggle', [
            'href' => add_query_arg(['action' => 'show'], $GLOBALS['smarty']->tpl_vars['urls']->value['pages']['cart']),
            'class' => 'elementor-button elementor-size-' . $settings['toggle_size'],
        ]);
        if ('sidebar' === $settings['skin'] && !($controller instanceof \CartController || $controller instanceof \OrderController || $controller instanceof \OrderConfirmationController)) {
            $this->addRenderAttribute('toggle', [
                'role' => 'button',
                'aria-label' => __('Shopping Cart', 'Shop.Theme.Checkout'),
                'aria-controls' => $dialog_id = 'elementor-cart__dialog-' . $this->getId(),
                'aria-expanded' => 'false',
            ]);
            $aria_desc = [];
            $settings['items_indicator'] !== 'none' && $aria_desc[] = $cart['summary_string'];
            $settings['show_subtotal'] && $aria_desc[] = $cart['subtotals']['products']['value'];
            $aria_desc && $this->addRenderAttribute('toggle', 'aria-description', implode(', ', $aria_desc));

            $this->addRenderAttribute('title', [
                'class' => 'elementor-cart__title',
                'id' => $title_id = 'elementor-cart__title-' . $this->getId(),
            ]);
            $settings['title_display'] && $this->addRenderAttribute('title', 'class', 'ce-display-' . $settings['title_display']);
            $this->addInlineEditingAttributes('title', 'none');
            ?>
            <dialog class="elementor-cart__container elementor-lightbox" id="<?php escape($dialog_id); ?>" aria-modal="true" aria-labelledby="<?php escape($title_id); ?>">
                <div class="elementor-cart__main">
                    <a class="elementor-cart__close-button ceicon-close" href="javascript://close" role="button" aria-label="<?php esc_attr_e('Close'); ?>"></a>
                    <<?php Utils::printValidatedHtmlTag($settings['title_tag']); ?> <?php $this->printRenderAttributeString('title'); ?>>
                        <?php echo $settings['title'] ?: '<span class="screen-reader-text">' . __('Shopping Cart', 'Shop.Theme.Checkout') . '</span>'; ?>
                    </<?php Utils::printValidatedHtmlTag($settings['title_tag']); ?>>
                    <?php $this->renderCartContent($cart, $settings); ?>
                </div>
            </dialog><?php
        } ?>
        <div class="elementor-cart__toggle">
            <a <?php $this->printRenderAttributeString('toggle'); ?>>
                <span class="elementor-button-icon" data-counter="<?php echo (int) $cart['products_count']; ?>" aria-hidden="true">
                    <?php echo IconsManager::getBcIcon($settings, 'icon'); ?>
                </span>
                <span class="elementor-button-text"><?php echo $cart['subtotals']['products']['value']; ?></span>
            </a>
        </div>
        <?php
    }

    protected function renderCartContent(&$cart, array &$settings)
    {
        $pages = &$GLOBALS['smarty']->tpl_vars['urls']->value['pages'];
        $minimalPurchaseRequired = $cart['minimalPurchaseRequired'];
        $checkout_disabled = $minimalPurchaseRequired || !$cart['products'];
        $this->imageSize = \ImageType::getFormattedName('cart');
        ?>
        <div class="elementor-cart__empty-message" role="alert" aria-live="polite"<?php $cart['products'] && print ' hidden'; ?>><?php echo $settings['empty_message']; ?></div>
        <div class="elementor-cart__products ce-scrollbar-y--auto" role="list">
            <?php
            foreach ($cart['products'] as $product) {
                $this->renderCartItem($product, $settings);
            } ?>
        </div>
        <div class="elementor-cart__summary" role="list" aria-label="<?php esc_attr_e('Your order', 'Shop.Theme.Customeraccount'); ?>">
            <div class="elementor-cart__summary-label" role="term"><?php echo $cart['summary_string']; ?></div>
            <div class="elementor-cart__summary-value" role="definition"><?php echo $cart['subtotals']['products']['value']; ?></div>
        <?php if ($cart['subtotals']['discounts']) { ?>
            <div class="elementor-cart__summary-label" role="term"><?php echo $cart['subtotals']['discounts']['label']; ?></div>
            <div class="elementor-cart__summary-value" role="definition">-<?php echo $cart['subtotals']['discounts']['value']; ?></div>
        <?php } ?>
            <span class="elementor-cart__summary-label" role="term"><?php echo $cart['subtotals']['shipping']['label']; ?></span>
            <span class="elementor-cart__summary-value" role="definition"><?php echo $cart['subtotals']['shipping']['value']; ?></span>
            <strong class="elementor-cart__summary-label" role="term"><?php echo $cart['totals']['total']['label']; ?></strong>
            <strong class="elementor-cart__summary-value" role="definition"><?php echo $cart['totals']['total']['value']; ?></strong>
        </div>
        <div class="elementor-alert elementor-alert-warning"<?php $minimalPurchaseRequired || print ' hidden'; ?> role="alert" aria-live="polite">
            <span class="elementor-alert-description"><?php echo $minimalPurchaseRequired; ?></span>
        </div>
        <footer class="elementor-cart__footer-buttons">
            <div class="elementor-align-justify<?php empty($settings['view_cart_type']) || escape(' elementor-button-' . $settings['view_cart_type']); ?>">
                <a href="<?php escape(add_query_arg(['action' => 'show'], $pages['cart'])); ?>" class="elementor-button elementor-button--cart elementor-size-<?php escape($settings['view_cart_size']); ?>">
                    <span class="elementor-button-text"><?php echo !empty($settings['view_cart']) ? $settings['view_cart'] : __('View Cart'); ?></span>
                </a>
            </div>
            <div class="elementor-align-justify<?php $settings['checkout_type'] && escape(' elementor-button-' . $settings['checkout_type']); ?>">
                <a href="<?php escape($pages['order']); ?>" class="elementor-button elementor-button--checkout elementor-size-<?php escape($settings['view_cart_size']); ?>"<?php
                    $checkout_disabled && print 'tabindex="-1" aria-disabled="true"'; ?>>
                    <span class="elementor-button-text"><?php echo !empty($settings['checkout']) ? $settings['checkout'] : __('Checkout', 'Shop.Theme.Actions'); ?></span>
                </a>
            </div>
        </footer>
        <?php
        echo \Hook::exec('displayCEShoppingCartFooter');
    }

    protected function renderCartItem($product, array &$settings)
    {
        $cover = !empty($product['default_image']) ? $product['default_image'] : (
            $product['cover'] ?: $GLOBALS['smarty']->tpl_vars['urls']->value['no_picture_image']
        );
        $cover_image = isset($cover['bySize'][$this->imageSize]) ? $cover['bySize'][$this->imageSize] : $cover['small']; ?>
        <div class="elementor-cart__product" role="listitem">
            <a class="elementor-cart__product-image" href="<?php escape($product['url']); ?>" tabindex="-1">
                <img src="<?php escape($cover_image['url']); ?>" alt="<?php escape($cover['legend']); ?>" fetchpriority="low">
            </a>
            <div class="elementor-cart__product-name">
                <a href="<?php escape($product['url']); ?>"><?php echo $product['name']; ?></a>
                <div class="elementor-cart__product-attrs" role="list" aria-label="<?php esc_attr_e('Product Details', 'Shop.Theme.Catalog'); ?>">
                <?php foreach ($product['attributes'] as $attribute => $value) { ?>
                    <div class="elementor-cart__product-attr" role="listitem">
                        <?php _e('%facet_label%: %facet_value%', 'Shop.Theme.Catalog', ['%facet_label%' => $attribute, '%facet_value%' => $value]); ?>
                    </div>
                <?php } ?>
            <?php foreach ($product['customizations'] as $customization) { ?>
                <?php foreach ($customization['fields'] as &$field) { ?>
                    <div class="elementor-cart__product-attr" role="listitem">
                        <?php
                        _e('%facet_label%: %facet_value%', 'Shop.Theme.Catalog', [
                            '%facet_label%' => $field['label'],
                            '%facet_value%' => 'image' === $field['type'] ? '<img src="' . esc_attr($field['image']['small']['url']) . '" alt="" fetchpriority="low">' : esc_html($field['text']),
                        ]); ?>
                    </div>
                <?php } ?>
            <?php } ?>
                </div>
            </div>
            <div class="elementor-cart__product-price">
                <span class="elementor-cart__product-quantity"><?php echo $product['quantity']; ?></span> &times; <?php echo $product['is_gift'] ? __('Gift', 'Shop.Theme.Checkout') : $product['price']; ?>
            <?php if ($product['has_discount']) { ?>
                <del aria-label="<?php esc_attr_e('Regular price', 'Shop.Theme.Catalog'); ?>"><?php echo $product['regular_price']; ?></del>
            <?php } ?>
            </div>
        <?php if (!empty($settings['remove_item_icon']['value'])) { ?>
            <a class="elementor-cart__product-remove <?php escape($settings['remove_item_icon']['value']); ?>" href="<?php escape($product['remove_from_cart_url']); ?>" rel="nofollow"
                data-id-product="<?php echo (int) $product['id_product']; ?>" data-id-product-attribute="<?php echo (int) $product['id_product_attribute']; ?>"
                data-id-customization="<?php echo (int) $product['id_customization']; ?>" title="<?php esc_attr_e('remove from cart', 'Shop.Theme.Actions'); ?>"
                role="button" aria-label="<?php esc_attr_e('remove from cart', 'Shop.Theme.Actions'); ?>"></a>
        <?php } ?>
        </div>
        <?php
    }

    public function renderPlainContent()
    {
    }
}
