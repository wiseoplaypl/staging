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

class ModulesXCatalogXWidgetsXProductXQuantity extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/89-widgets/product-widgets/377-product-quantity-widget';

    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'product-quantity';
    }

    public function getTitle()
    {
        return __('Product Quantity');
    }

    public function getIcon()
    {
        return 'eicon-text-field';
    }

    public function getCategories()
    {
        return ['product-elements'];
    }

    public function getKeywords()
    {
        return ['shop', 'store', 'cart', 'product', 'number', 'quantity', 'add to cart'];
    }

    public function getStyleDepends()
    {
        return ['widget-form', 'widget-product-quantity'];
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_product_quantity',
            [
                'label' => __('Product Quantity'),
            ]
        );

        $this->addControl(
            'view',
            [
                'label' => __('View'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'default' => __('Default'),
                    'inline' => __('Inline'),
                    'stacked' => __('Stacked'),
                ],
                'default' => 'default',
                'prefix_class' => 'ce-product-quantity--view-',
            ]
        );

        $this->addControl(
            'size',
            [
                'label' => __('Size'),
                'type' => ControlsManager::SELECT,
                'default' => 'sm',
                'options' => [
                    'xs' => __('Extra Small'),
                    'sm' => __('Small'),
                    'md' => __('Medium'),
                    'lg' => __('Large'),
                    'xl' => __('Extra Large'),
                ],
            ]
        );

        $this->addResponsiveControl(
            'align',
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
                    'justify' => [
                        'title' => __('Justified'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'prefix_class' => 'elementor%s-align-',
            ]
        );

        $this->addControl(
            'heading_buttons',
            [
                'label' => __('Buttons'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'view!' => 'default',
                ],
            ]
        );

        $this->addControl(
            'plus_icon',
            [
                'label' => __('Up'),
                'label_block' => false,
                'type' => ControlsManager::ICONS,
                'skin' => 'inline',
                'exclude_inline_options' => ['svg'],
                'fa4compatibility' => 'plus',
                'default' => [
                    'value' => 'ceicon-sort-up',
                    'library' => 'ce-icons',
                ],
                'recommended' => [
                    'ce-icons' => [
                        'plus',
                        'sort-up',
                    ],
                    'fa-solid' => [
                        'plus',
                        'circle-plus',
                        'square-plus',
                        'square-caret-up',
                        'angle-up',
                        'angles-up',
                        'chevron-up',
                        'circle-chevron-up',
                        'arrow-up',
                        'circle-arrow-up',
                    ],
                    'fa-regular' => [
                        'circle-plus',
                        'square-plus',
                        'square-caret-up',
                    ],
                ],
            ]
        );

        $this->addControl(
            'minus_icon',
            [
                'label' => __('Down'),
                'label_block' => false,
                'type' => ControlsManager::ICONS,
                'skin' => 'inline',
                'exclude_inline_options' => ['svg'],
                'fa4compatibility' => 'minus',
                'default' => [
                    'value' => 'ceicon-sort-down',
                    'library' => 'ce-icons',
                ],
                'recommended' => [
                    'ce-icons' => [
                        'minus',
                        'sort-down',
                    ],
                    'fa-solid' => [
                        'minus',
                        'circle-minus',
                        'square-minus',
                        'square-caret-down',
                        'angle-down',
                        'angles-down',
                        'chevron-down',
                        'circle-chevron-down',
                        'arrow-down',
                        'circle-arrow-down',
                    ],
                    'fa-regular' => [
                        'circle-minus',
                        'square-minus',
                        'square-caret-down',
                    ],
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_input_style',
            [
                'label' => __('Input'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addResponsiveControl(
            'width',
            [
                'label' => __('Width'),
                'type' => ControlsManager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} input[type=number]' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'align!' => 'justify',
                ],
                'device_args' => [
                    ControlsStack::RESPONSIVE_TABLET => [
                        'condition' => [
                            'align_tablet!' => 'justify',
                        ],
                    ],
                    ControlsStack::RESPONSIVE_MOBILE => [
                        'condition' => [
                            'align_mobile!' => 'justify',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'input_align',
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
                ],
                'selectors' => [
                    '{{WRAPPER}} input[type=number]' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} input[type=number]',
            ]
        );

        $this->startControlsTabs('tabs_input_style');

        $this->startControlsTab(
            'tab_input_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} input[type=number]' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} input[type=number]' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} input[type=number]' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_input_focus',
            [
                'label' => __('Focus'),
            ]
        );

        $this->addControl(
            'text_color_focus',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} input[type=number]:focus' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'background_color_focus',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} input[type=number]:focus' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'border_color_focus',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} input[type=number]:focus' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'border_width',
            [
                'label' => __('Border Width'),
                'type' => ControlsManager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} input[type=number]' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                    '{{WRAPPER}}.ce-product-quantity--view-stacked .ce-product-quantity__plus' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}}',
                    '{{WRAPPER}}.ce-product-quantity--view-stacked .ce-product-quantity__minus' => 'margin: 0 {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} input[type=number]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} input[type=number]',
            ]
        );

        $this->addResponsiveControl(
            'text_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} input[type=number]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_buttons_style',
            [
                'label' => __('Buttons'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'view!' => 'default',
                ],
            ]
        );

        $this->addResponsiveControl(
            'buttons_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}}.ce-product-quantity--view-inline input[type=number]' => 'margin: 0 {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}}.ce-product-quantity--view-stacked .ce-product-quantity__btn' => 'inset-inline-end: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'buttons_margin',
            [
                'label' => __('Margin'),
                'type' => ControlsManager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-quantity__plus' => 'top: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .ce-product-quantity__minus' => 'bottom: {{SIZE}}{{UNIT}}',
                ],
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'condition' => [
                    'view' => 'stacked',
                ],
            ]
        );

        $this->addControl(
            'buttons_width',
            [
                'label' => __('Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                    'em' => [
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-quantity__btn' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'view' => 'stacked',
                ],
            ]
        );

        $this->addResponsiveControl(
            'buttons_size',
            [
                'label' => __('Icon Size'),
                'type' => ControlsManager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} i.ce-product-quantity__btn' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            'buttons_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} i.ce-product-quantity__btn' => 'padding: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'view' => 'inline',
                ],
            ]
        );

        $this->startControlsTabs('tabs_colors_style');

        $this->startControlsTab(
            'tab_colors_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'buttons_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} i.ce-product-quantity__btn' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'buttons_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-quantity__btn' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'buttons_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-quantity__btn' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_colors_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'buttons_color_hover',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} i.ce-product-quantity__btn:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'buttons_background_color_hover',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-quantity__btn:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'buttons_border_color_hover',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-quantity__btn:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->startControlsTabs(
            'tabs_border_style',
            [
                'condition' => [
                    'view' => 'stacked',
                ],
            ]
        );

        $this->startControlsTab(
            'tab_border_plus',
            [
                'label' => __('Up'),
            ]
        );

        $this->addControl(
            'plus_border_width',
            [
                'label' => __('Border Width'),
                'type' => ControlsManager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-quantity__plus' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'plus_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-quantity__plus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_border_minus',
            [
                'label' => __('Down'),
            ]
        );

        $this->addControl(
            'minus_border_width',
            [
                'label' => __('Border Width'),
                'type' => ControlsManager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-quantity__minus' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'minus_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-quantity__minus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'buttons_border_width',
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
                    '{{WRAPPER}} .ce-product-quantity__btn' => 'border-width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'view' => 'inline',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'buttons_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-quantity__btn' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
                'default' => [
                    'size' => 2,
                ],
                'condition' => [
                    'view' => 'inline',
                ],
            ]
        );

        $this->endControlsSection();
    }

    protected function render()
    {
        $settings = $this->getSettingsForDisplay();
        $product = $GLOBALS['smarty']->tpl_vars['product']->value;
        $min_qty = !empty($product['product_attribute_minimal_quantity'])
            ? 'product_attribute_minimal_quantity'
            : 'minimal_quantity';
        $minus = isset($settings['minus']) && !isset($settings['__fa4_migrated']['minus_icon'])
            ? $settings['minus']
            : $settings['minus_icon']['value'];
        $plus = isset($settings['plus']) && !isset($settings['__fa4_migrated']['plus_icon'])
            ? $settings['plus']
            : $settings['plus_icon']['value'];

        $this->addRenderAttribute('input', [
            'value' => $product['quantity_wanted'] ?: 1,
            'min' => $product[$min_qty] ?: 1,
            'class' => 'elementor-field elementor-field-textual elementor-size-' . $settings['size'],
            'aria-label' => __('Quantity', 'Shop.Theme.Actions'),
        ]);
        \Module::isEnabled('ec_minorder') && $this->addRenderAttribute('input', [
            'step' => max(1, $product[$min_qty]),
            'style' => 'pointer-events: none',
        ]); ?>
        <div class="ce-product-quantity elementor-field-group">
            <i class="ce-product-quantity__btn ce-product-quantity__minus <?php escape($minus); ?>" onclick="this.nextElementSibling.stepDown(), $(this.nextElementSibling).trigger('input')" aria-hidden="true"></i>
            <input type="number" form="add-to-cart-or-refresh" name="qty" <?php $this->printRenderAttributeString('input'); ?> inputmode="decimal" oninput="$(this.form.qty).val(this.value)">
            <i class="ce-product-quantity__btn ce-product-quantity__plus <?php escape($plus); ?>" onclick="this.previousElementSibling.stepUp(), $(this.previousElementSibling).trigger('input')" aria-hidden="true"></i>
        </div>
        <?php
    }

    protected function renderSmarty()
    {
        $settings = $this->getSettingsForDisplay();
        $this->addRenderAttribute('input', [
            'class' => 'elementor-field elementor-field-textual elementor-size-' . $settings['size'],
            'aria-label' => __('Quantity', 'Shop.Theme.Actions'),
        ]); ?>
        <div class="ce-product-quantity elementor-field-group">
            <i class="ce-product-quantity__btn ce-product-quantity__minus <?php escape($settings['minus_icon']['value']); ?>" onclick="this.nextElementSibling.stepDown()" aria-hidden="true"></i>
            <input type="number" name="qty" value="{$product.minimal_quantity}" min="{$product.minimal_quantity}" <?php $this->printRenderAttributeString('input'); ?> inputmode="decimal">
            <i class="ce-product-quantity__btn ce-product-quantity__plus <?php escape($settings['plus_icon']['value']); ?>" onclick="this.previousElementSibling.stepUp()" aria-hidden="true"></i>
        </div>
        <?php
    }

    public function renderPlainContent()
    {
    }
}
