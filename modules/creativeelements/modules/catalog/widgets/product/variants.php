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

class ModulesXCatalogXWidgetsXProductXVariants extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/89-widgets/product-widgets/375-product-variations-widget';

    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'product-variants';
    }

    public function getTitle()
    {
        return __('Product Variations');
    }

    public function getIcon()
    {
        return 'eicon-select';
    }

    public function getCategories()
    {
        return ['product-elements'];
    }

    public function getKeywords()
    {
        return ['shop', 'store', 'product', 'variations', 'variants'];
    }

    public function getStyleDepends()
    {
        return ['widget-form', 'widget-product-variants'];
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_variations',
            [
                'label' => __('Product Variations'),
            ]
        );

        $this->addControl(
            'layout',
            [
                'label' => __('Layout'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'inline' => __('Inline'),
                    'stacked' => __('Stacked'),
                    'table' => __('Table'),
                ],
                'default' => 'stacked',
                'prefix_class' => 'ce-product-variants--layout-',
                'render_type' => 'template',
            ]
        );

        $this->addControl(
            'hide_signle_options',
            [
                'label' => __('Hide Single Option'),
                'type' => ControlsManager::SWITCHER,
            ]
        );

        $this->addControl(
            'show_value',
            [
                'label' => __('Value'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__label::after' => 'content: ":"',
                ],
                'condition' => [
                    'layout' => 'stacked',
                ],
                'render_type' => 'template',
            ]
        );

        $this->addControl(
            'pattern_heading',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Color/Texture Buttons'),
            ]
        );

        $this->addControl(
            'show_image',
            [
                'label' => __('Product Image'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
            ]
        );

        $image_size_options = GroupControlImageSize::getAllImageSizes('products');
        $image_size_keys = array_keys($image_size_options);

        $this->addControl(
            'image_size',
            [
                'label' => __('Image Size'),
                'type' => ControlsManager::SELECT,
                'options' => &$image_size_options,
                'default' => array_pop($image_size_keys),
                'condition' => [
                    'show_image!' => '',
                ],
            ]
        );

        _CE_ADMIN_ && $this->addControl(
            'configure',
            [
                'label' => __('Product Attributes'),
                'type' => ControlsManager::BUTTON,
                'text' => '<i class="eicon-external-link-square"></i>' . __('Configure', 'Admin.Actions'),
                'link' => [
                    'url' => Helper::$link->getAdminLink('AdminAttributesGroups'),
                    'is_external' => true,
                ],
                'separator' => 'before',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_variations_style',
            [
                'label' => __('Product Variations'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'space_between',
            [
                'label' => __('Space Between'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'heading_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::HEADING,
            ]
        );

        $this->addControl(
            'label_inline',
            [
                'label' => __('Inline'),
                'type' => ControlsManager::SWITCHER,
                'return_value' => 'inline',
                'prefix_class' => 'ce-product-variants--label-',
                'condition' => [
                    'layout' => 'inline',
                ],
            ]
        );

        $this->addControl(
            'label_vertical_align',
            [
                'label' => __('Vertical Align'),
                'type' => ControlsManager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'flex-start' => [
                        'title' => __('Start'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => __('End'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__item' => 'align-items: {{VALUE}}',
                ],
                'condition' => [
                    'layout!' => 'stacked',
                    'label_inline!' => '',
                ],
            ]
        );

        $this->addControl(
            'label_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 5,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__item' => 'gap: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'layout!' => 'table',
                ],
            ]
        );

        $this->addResponsiveControl(
            'label_width',
            [
                'label' => __('Min Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'default' => [
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__label' => 'min-width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'layout' => 'table',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .ce-product-variants__label',
            ]
        );

        $this->addControl(
            'label_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__label' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'heading_value',
            [
                'label' => __('Value'),
                'type' => ControlsManager::HEADING,
                'condition' => [
                    'layout' => 'stacked',
                    'show_value!' => '',
                ],
            ]
        );

        $this->addControl(
            'value_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__item' => 'column-gap: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'layout' => 'stacked',
                    'show_value!' => '',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'value_typography',
                'selector' => '{{WRAPPER}} .ce-product-variants__value',
                'condition' => [
                    'layout' => 'stacked',
                    'show_value!' => '',
                ],
            ]
        );

        $this->addControl(
            'value_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__value' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'layout' => 'stacked',
                    'show_value!' => '',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_select_style',
            [
                'label' => __('Drop-down List'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'select_size',
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

        $this->addControl(
            'select_width',
            [
                'label' => __('Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}.ce-product-variants--layout-inline .ce-product-variants__select' => 'width: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}}:not(.ce-product-variants--layout-inline) .ce-product-variants__select' => 'margin-inline-end: calc(100% - {{SIZE}}{{UNIT}})',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'select_typography',
                'selector' => '{{WRAPPER}} select.elementor-field',
            ]
        );

        $this->startControlsTabs('tabs_style_select');

        $this->startControlsTab(
            'tab_select_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'select_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} select.elementor-field' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'select_bg_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} select.elementor-field' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'select_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} select.elementor-field' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_select_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'select_color_hover',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} select.elementor-field:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'select_bg_color_hover',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} select.elementor-field:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'select_border_color_hover',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} select.elementor-field:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'select_border_width',
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
                    '{{WRAPPER}} select.elementor-field' => 'border-width: {{SIZE}}{{UNIT}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'select_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} select.elementor-field' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            'select_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} select.elementor-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'select_box_shadow',
                'selector' => '{{WRAPPER}} select.elementor-field',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_radio_style',
            [
                'label' => __('Radio Buttons'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'radio_space_between',
            [
                'label' => __('Space Between'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__options' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'radio_min_width',
            [
                'label' => __('Min Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__option' => 'min-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'radio_typography',
                'selector' => '{{WRAPPER}} .ce-product-variants__option',
            ]
        );

        $this->startControlsTabs('tabs_style_radio');

        $this->startControlsTab(
            'tab_radio_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'radio_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__option' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'radio_bg_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__option' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'radio_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'default' => '#818a91',
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__option' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_radio_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'radio_color_hover',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a:hover .ce-product-variants__option, {{WRAPPER}} a:focus .ce-product-variants__option' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'radio_bg_color_hover',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a:hover .ce-product-variants__option, {{WRAPPER}} a:focus .ce-product-variants__option' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'radio_border_color_hover',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a:hover .ce-product-variants__option, {{WRAPPER}} a:focus .ce-product-variants__option' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_radio_active',
            [
                'label' => __('Active'),
            ]
        );

        $this->addControl(
            'radio_color_active',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} input:checked ~ .ce-product-variants__option' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'radio_bg_color_active',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} input:checked ~ .ce-product-variants__option' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'radio_border_color_active',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'default' => '#5bc0de',
                'selectors' => [
                    '{{WRAPPER}} input:checked ~ .ce-product-variants__option' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'radio_border_width',
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
                'default' => [
                    'size' => 2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__option' => 'border-style: solid; border-width: {{SIZE}}{{UNIT}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'radio_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__option' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            'radio_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'default' => [
                    'top' => 5,
                    'right' => 10,
                    'bottom' => 5,
                    'left' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__option' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'radio_box_shadow',
                'selector' => '{{WRAPPER}} .ce-product-variants__option',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_pattern_style',
            [
                'label' => __('Color/Texture Buttons'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'pattern_space_between',
            [
                'label' => __('Space Between'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__patterns' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->startControlsTabs('tabs_style_pattern');

        $this->startControlsTab(
            'tab_pattern_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'pattern_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'default' => '#818a91',
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__pattern' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'pattern_border_width',
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
                'default' => [
                    'size' => 2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__pattern' => 'border-style: solid; border-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'pattern_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__pattern' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'pattern_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__pattern' => 'padding: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_pattern_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'pattern_border_color_hover',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a:hover .ce-product-variants__pattern, {{WRAPPER}} a:focus .ce-product-variants__pattern' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'pattern_border_width_hover',
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
                'default' => [
                    'size' => 2,
                ],
                'selectors' => [
                    '{{WRAPPER}} a:hover .ce-product-variants__pattern, {{WRAPPER}} a:focus .ce-product-variants__pattern' => 'border-style: solid; border-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'pattern_border_radius_hover',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} a:hover .ce-product-variants__pattern, {{WRAPPER}} a:focus .ce-product-variants__pattern' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'pattern_padding_hover',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 2,
                ],
                'selectors' => [
                    '{{WRAPPER}} a:hover .ce-product-variants__pattern, {{WRAPPER}} a:focus .ce-product-variants__pattern' => 'padding: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_pattern_active',
            [
                'label' => __('Active'),
            ]
        );

        $this->addControl(
            'pattern_border_color_active',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'default' => '#5bc0de',
                'selectors' => [
                    '{{WRAPPER}} input:checked ~ .ce-product-variants__pattern' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'pattern_border_width_active',
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
                'default' => [
                    'size' => 2,
                ],
                'selectors' => [
                    '{{WRAPPER}} input:checked ~ .ce-product-variants__pattern' => 'border-style: solid; border-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'pattern_border_radius_active',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} input:checked ~ .ce-product-variants__pattern' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'pattern_padding_active',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 2,
                ],
                'selectors' => [
                    '{{WRAPPER}} input:checked ~ .ce-product-variants__pattern' => 'padding: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addResponsiveControl(
            'pattern_size',
            [
                'label' => __('Size'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-variants__pattern' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'pattern_box_shadow',
                'selector' => '{{WRAPPER}} .ce-product-variants__pattern',
            ]
        );

        $this->endControlsSection();
    }

    protected function getImageByColor($id_attr_group, $color, $size)
    {
        $combinations = &$GLOBALS['smarty']->tpl_vars['combinations']->value;

        foreach ($combinations as &$combination) {
            if ($combination['attributes_values'][$id_attr_group] === $color && $combination['id_image'] > 0) {
                return Helper::getProductImageLink($combination, $size);
            }
        }
    }

    protected function getHtmlWrapperClass()
    {
        return parent::getHtmlWrapperClass() . ' elementor-overflow-hidden';
    }

    protected function render()
    {
        if (empty($GLOBALS['smarty']->tpl_vars['groups']) || !$groups = &$GLOBALS['smarty']->tpl_vars['groups']->value) {
            return;
        }
        $id = $this->getId();
        $settings = $this->getSettingsForDisplay();
        $image_size = isset($settings['image_size']) ? $settings['image_size'] : '';
        $hide_single_options = (bool) $settings['hide_signle_options'];
        ?>
        <div class="ce-product-variants" role="form" aria-label="<?php esc_attr_e('Product Variations'); ?>">
        <?php foreach ($groups as $id_attr_group => $group) {
            if (empty($group['attributes']) || $hide_single_options && count($group['attributes']) === 1) {
                continue;
            } ?>
            <div class="ce-product-variants__item elementor-field-group">
                <span class="ce-product-variants__label" id="<?php escape($label_id = "ce-product-variants__label-$id_attr_group-$id"); ?>"><?php echo esc_html($group['name']); ?></span>
            <?php if ('stacked' === $settings['layout'] && $settings['show_value']) { ?>
                <span class="ce-product-variants__value">
                <?php foreach ($group['attributes'] as $value) { ?>
                    <?php $value['selected'] && print $value['name']; ?>
                <?php } ?>
                </span>
            <?php } ?>
            <?php if ('select' === $group['group_type']) { ?>
                <div class="ce-product-variants__select elementor-select-wrapper">
                    <select class="elementor-field elementor-field-textual elementor-size-<?php escape($settings['select_size']); ?>" oninput="$(this.form[this.name]).val(this.value)"
                        form="add-to-cart-or-refresh" name="group[<?php echo (int) $id_attr_group; ?>]" data-product-attribute="<?php echo (int) $id_attr_group; ?>" aria-labelledby="<?php escape($label_id); ?>">
                    <?php foreach ($group['attributes'] as $id_attr => $group_attr) { ?>
                        <option <?php $group_attr['selected'] && print 'selected'; ?> value="<?php echo (int) $id_attr; ?>"><?php echo esc_html($group_attr['name']); ?>&emsp;</option>
                    <?php } ?>
                    </select>
                </div>
            <?php } elseif ('radio' === $group['group_type']) { ?>
                <div class="ce-product-variants__options" role="radiogroup" aria-labelledby="<?php escape($label_id); ?>">
                <?php foreach ($group['attributes'] as $id_attr => $group_attr) { ?>
                    <a href="#ce-action=select" class="ce-product-variants__radio" role="radio" aria-checked="<?php echo $group_attr['selected'] ? 'true' : 'false'; ?>">
                        <input form="add-to-cart-or-refresh" type="radio" hidden<?php $group_attr['selected'] && print ' checked'; ?>
                            name="group[<?php echo (int) $id_attr_group; ?>]" value="<?php echo (int) $id_attr; ?>" data-product-attribute="<?php echo (int) $id_attr_group; ?>">
                        <span class="ce-product-variants__option"><?php echo esc_html($group_attr['name']); ?></span>
                    </a>
                <?php } ?>
                </div>
            <?php } elseif ('color' === $group['group_type']) { ?>
                <div class="ce-product-variants__patterns" role="radiogroup" aria-labelledby="<?php escape($label_id); ?>">
                <?php foreach ($group['attributes'] as $id_attr => $group_attr) { ?>
                    <a href="#ce-action=select" class="ce-product-variants__radio" title="<?php escape($group_attr['name']); ?>" role="radio" aria-checked="<?php echo $group_attr['selected'] ? 'true' : 'false'; ?>" aria-label="<?php escape($group_attr['name']); ?>">
                        <input form="add-to-cart-or-refresh" type="radio" hidden<?php $group_attr['selected'] && print ' checked'; ?>
                            name="group[<?php echo (int) $id_attr_group; ?>]" value="<?php echo (int) $id_attr; ?>" data-product-attribute="<?php echo (int) $id_attr_group; ?>">
                    <?php if ($image_size && $image = $this->getImageByColor($id_attr_group, $group_attr['name'], $image_size)) { ?>
                        <span class="ce-product-variants__pattern ce-product-variants__texture" <?php echo Utils::renderHtmlAttributes(['style' => 'background-image:url' . "($image)"]); ?>></span>
                    <?php } elseif ($group_attr['html_color_code']) { ?>
                        <span class="ce-product-variants__pattern ce-product-variants__color" <?php echo Utils::renderHtmlAttributes(['style' => 'background-color:' . $group_attr['html_color_code']]); ?>></span>
                    <?php } elseif ($group_attr['texture']) { ?>
                        <span class="ce-product-variants__pattern ce-product-variants__texture" <?php echo Utils::renderHtmlAttributes(['style' => 'background-image:url' . "($group_attr[texture])"]); ?>></span>
                    <?php } ?>
                    </a>
                <?php } ?>
                </div>
            <?php } ?>
            </div>
        <?php } ?>
        </div>
        <?php
    }

    public function renderPlainContent()
    {
    }
}
