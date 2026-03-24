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

class ModulesXThemeXWidgetsXAjaxSearch extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/88-widgets/site-widgets/336-ajax-search-widget';

    public function getName()
    {
        return 'ajax-search';
    }

    public function getTitle()
    {
        return __('AJAX Search');
    }

    public function getIcon()
    {
        return 'eicon-search';
    }

    public function getCategories()
    {
        return ['theme-elements'];
    }

    public function getKeywords()
    {
        return ['search', 'form', 'live', 'ajax'];
    }

    public function getStyleDepends()
    {
        return ['widget-ajax-search'];
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'search_content',
            [
                'label' => __('Search'),
            ]
        );

        $this->addControl(
            'skin',
            [
                'label' => __('Skin'),
                'type' => ControlsManager::SELECT,
                'default' => 'classic',
                'options' => [
                    'classic' => __('Classic'),
                    'minimal' => __('Minimal'),
                    'topbar' => __('Topbar'),
                ],
                'prefix_class' => 'elementor-search--skin-',
                'render_type' => 'template',
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'skin_divider',
            [
                'type' => ControlsManager::DIVIDER,
            ]
        );

        $this->addControl(
            'heading_button_content',
            [
                'label' => __('Button'),
                'type' => ControlsManager::HEADING,
                'condition' => [
                    'skin' => 'classic',
                ],
            ]
        );

        $this->addControl(
            'heading_toggle_content',
            [
                'label' => __('Toggle'),
                'type' => ControlsManager::HEADING,
                'condition' => [
                    'skin' => 'topbar',
                ],
            ]
        );

        $this->addControl(
            'button_type',
            [
                'label' => __('Type'),
                'type' => ControlsManager::SELECT,
                'default' => 'icon',
                'options' => [
                    'icon' => __('Icon'),
                    'text' => __('Text'),
                ],
                'prefix_class' => 'elementor-search--button-type-',
                'render_type' => 'template',
                'condition' => [
                    'skin' => 'classic',
                ],
            ]
        );

        $this->addControl(
            'button_text',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
                'default' => $search = Helper::$translator->trans('Search', [], 'Shop.Theme.Catalog'),
                'dynamic' => [
                    'active' => 1,
                ],
                'condition' => [
                    'button_type' => 'text',
                    'skin' => 'classic',
                ],
            ]
        );

        $this->addControl(
            'selected_icon',
            [
                'label' => __('Icon'),
                'type' => ControlsManager::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-magnifying-glass',
                    'library' => 'fa-solid',
                ],
                'recommended' => [
                    'ce-icons' => [
                        'search-light',
                        'search-medium',
                        'search-glint',
                        'search-minimal',
                        'magnifier',
                        'loupe',
                    ],
                    'fa-solid' => [
                        'magnifying-glass',
                        'magnifying-glass-arrow-right',
                    ],
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'button_type',
                            'value' => 'icon',
                        ],
                        [
                            'name' => 'skin',
                            'operator' => '!==',
                            'value' => 'classic',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'toggle_align',
            [
                'label' => __('Alignment'),
                'type' => ControlsManager::CHOOSE,
                'label_block' => false,
                'default' => 'center',
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search' => 'text-align: {{VALUE}}',
                ],
                'condition' => [
                    'skin' => 'topbar',
                ],
            ]
        );

        $this->addControl(
            'toggle_size',
            [
                'label' => __('Size'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 33,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__toggle' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'skin' => 'topbar',
                ],
            ]
        );

        $this->addControl(
            'heading_topbar_content',
            [
                'label' => __('Topbar'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'skin' => 'topbar',
                ],
            ]
        );

        $this->addControl(
            'heading_input_content',
            [
                'label' => __('Input'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'skin' => 'classic',
                ],
            ]
        );

        $this->addControl(
            'label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'default' => \Translate::getModuleTranslation('creativeelements', 'What are you looking for?', ''),
                'condition' => [
                    'skin' => 'topbar',
                ],
            ]
        );

        $this->addControl(
            'size',
            [
                'label' => __('Size'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__container' => 'min-height: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .elementor-search__submit' => 'min-width: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .elementor-search__icon, ' .
                    '{{WRAPPER}} .elementor-search__input, ' .
                    '{{WRAPPER}}.elementor-search--button-type-text .elementor-search__submit' => 'padding: 0 calc({{SIZE}}{{UNIT}} / 3)',
                ],
                'condition' => [
                    'skin!' => 'topbar',
                ],
            ]
        );

        $this->addControl(
            'placeholder',
            [
                'label' => __('Placeholder'),
                'type' => ControlsManager::TEXT,
                'default' => $search . '...',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->addControl(
            'clear_icon',
            [
                'label' => __('Clear'),
                'label_block' => false,
                'type' => ControlsManager::ICONS,
                'skin' => 'inline',
                'exclude_inline_options' => ['svg'],
                'default' => [
                    'value' => 'ceicon-close',
                    'library' => 'ce-icons',
                ],
                'recommended' => [
                    'fa-regular' => [
                        'circle-xmark',
                        'eraser',
                        'trash-can',
                    ],
                    'fa-solid' => [
                        'eraser',
                        'trash-can',
                        'trash',
                        'rotate-left',
                        'circle-xmark',
                        'xmark',
                    ],
                    'ce-icons' => [
                        'times',
                        'close',
                        'delete-left',
                    ],
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_results',
            [
                'label' => __('Result List'),
            ]
        );

        $this->addControl(
            'list_limit',
            [
                'label' => __('Product Limit'),
                'type' => ControlsManager::NUMBER,
                'min' => 1,
                'default' => 10,
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'list_product_content',
            [
                'label' => __('Content'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
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
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'show_category',
            [
                'label' => __('Category'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'show_description',
            [
                'label' => __('Description'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'description_line_clamp',
            [
                'label' => __('Max Lines'),
                'type' => ControlsManager::NUMBER,
                'min' => 1,
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__product-description' => '-webkit-line-clamp: {{VALUE}}',
                ],
                'condition' => [
                    'show_description!' => '',
                ],
            ]
        );

        $this->addControl(
            'show_price',
            [
                'label' => __('Price', 'Shop.Theme.Catalog'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_toggle_style',
            [
                'label' => __('Toggle'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'skin' => 'topbar',
                ],
            ]
        );

        $this->startControlsTabs('tabs_toggle_color');

        $this->startControlsTab(
            'tab_toggle_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'toggle_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__toggle' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'toggle_color',
            [
                'label' => __('Icon Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__toggle' => 'color: {{VALUE}}; border-color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_toggle_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'toggle_background_color_hover',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__toggle:hover, {{WRAPPER}} .elementor-search__toggle:focus' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'toggle_color_hover',
            [
                'label' => __('Icon Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__toggle:hover, {{WRAPPER}} .elementor-search__toggle:focus' => 'color: {{VALUE}}; border-color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'toggle_icon_size',
            [
                'label' => __('Icon Size') . ' (%)',
                'type' => ControlsManager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}}' => '--ce-search-toggle-icon-size: calc({{SIZE}}em / 100)',
                ],
                'condition' => [
                    'skin' => 'topbar',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'toggle_border_width',
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
                    '{{WRAPPER}} .elementor-search__toggle' => 'border-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'toggle_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__toggle' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_topbar_style',
            [
                'label' => __('Topbar'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'skin' => 'topbar',
                ],
            ]
        );

        $this->addControl(
            'overlay_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.elementor-search--skin-topbar .elementor-search__container' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            'overlay_padding',
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
                    '{{WRAPPER}}.elementor-search--skin-topbar .elementor-search__container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'heading_close_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Close'),
            ]
        );

        $this->addControl(
            'close_color',
            [
                'label' => __('Icon Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dialog-lightbox-close-button' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'close_color_hover',
            [
                'label' => __('Icon Hover'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dialog-lightbox-close-button:hover, {{WRAPPER}} .dialog-lightbox-close-button:focus' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'heading_label_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Label'),
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .elementor-search__label',
            ]
        );

        $this->addControl(
            'label_text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__label' => 'color: {{VALUE}}',
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

        $this->addControl(
            'icon_size_minimal',
            [
                'label' => __('Icon Size'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__icon' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'skin' => 'minimal',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'input_typography',
                'selector' => '{{WRAPPER}} input[type=search].elementor-search__input',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
            ]
        );

        $this->startControlsTabs('tabs_input_colors');

        $this->startControlsTab(
            'tab_input_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'input_text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__input, ' .
                    '{{WRAPPER}} .elementor-search__icon, ' .
                    '{{WRAPPER}} .elementor-lightbox .dialog-lightbox-close-button, ' .
                    '{{WRAPPER}} .elementor-lightbox .dialog-lightbox-close-button:hover, ' .
                    '{{WRAPPER}}.elementor-search--skin-topbar input[type="search"].elementor-search__input' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'input_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}:not(.elementor-search--skin-topbar) .elementor-search__container' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}}.elementor-search--skin-topbar input[type="search"].elementor-search__input' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'skin!' => 'topbar',
                ],
            ]
        );

        $this->addControl(
            'input_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}:not(.elementor-search--skin-topbar) .elementor-search__container' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}}.elementor-search--skin-topbar input[type="search"].elementor-search__input' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'input_box_shadow',
                'selector' => '{{WRAPPER}} .elementor-search__container',
                'fields_options' => [
                    'box_shadow_type' => [
                        'separator' => 'default',
                    ],
                ],
                'condition' => [
                    'skin!' => 'topbar',
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
            'input_text_color_focus',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}:not(.elementor-search--skin-topbar) .elementor-search--focus .elementor-search__input, ' .
                    '{{WRAPPER}} .elementor-search--focus .elementor-search__icon, ' .
                    '{{WRAPPER}} .elementor-lightbox .dialog-lightbox-close-button:hover, ' .
                    '{{WRAPPER}}.elementor-search--skin-topbar input[type="search"].elementor-search__input:focus' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'input_background_color_focus',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}:not(.elementor-search--skin-topbar) .elementor-search--focus .elementor-search__container' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}}.elementor-search--skin-topbar input[type="search"].elementor-search__input:focus' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'skin!' => 'topbar',
                ],
            ]
        );

        $this->addControl(
            'input_border_color_focus',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}:not(.elementor-search--skin-topbar) .elementor-search--focus .elementor-search__container' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}}.elementor-search--skin-topbar input[type="search"].elementor-search__input:focus' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'input_box_shadow_focus',
                'selector' => '{{WRAPPER}} .elementor-search--focus .elementor-search__container',
                'fields_options' => [
                    'box_shadow_type' => [
                        'separator' => 'default',
                    ],
                ],
                'condition' => [
                    'skin!' => 'topbar',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'button_border_width',
            [
                'label' => __('Border Size'),
                'type' => ControlsManager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}}:not(.elementor-search--skin-topbar) .elementor-search__container' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}}.elementor-search--skin-topbar input[type="search"].elementor-search__input' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->addResponsiveControl(
            'border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'max' => 200,
                    ],
                ],
                'default' => [
                    'size' => 3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__container' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'skin!' => 'topbar',
                ],
            ]
        );

        $this->addControl(
            'heading_cancel_icon_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Cancel', 'Shop.Theme.Actions'),
            ]
        );

        $this->addControl(
            'clear_icon_size',
            [
                'label' => __('Icon Size'),
                'size_units' => ['px', 'em'],
                'type' => ControlsManager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__clear' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_button_style',
            [
                'label' => __('Button'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'skin' => 'classic',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .elementor-search__submit',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
                'condition' => [
                    'button_type' => 'text',
                ],
            ]
        );

        $this->startControlsTabs('tabs_button_colors');

        $this->startControlsTab(
            'tab_button_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'button_text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__submit' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'button_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__submit' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_button_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'button_text_color_hover',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__submit:hover, {{WRAPPER}} .elementor-search__submit:focus' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'button_background_color_hover',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__submit:hover, {{WRAPPER}} .elementor-search__submit:focus' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'icon_size',
            [
                'label' => __('Icon Size'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__submit' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'button_type' => 'icon',
                    'skin!' => 'topbar',
                ],
                'separator' => 'before',
            ]
        );

        $this->addResponsiveControl(
            'button_width',
            [
                'label' => __('Width'),
                'type' => ControlsManager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__submit' => 'min-width: calc({{SIZE}} * {{size.SIZE}}{{size.UNIT}})',
                ],
                'condition' => [
                    'skin' => 'classic',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_results_style',
            [
                'label' => __('Result List'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'list_align',
            [
                'label' => __('Align'),
                'type' => ControlsManager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'prefix_class' => 'elementor-search--align-',
                'condition' => [
                    'skin!' => 'topbar',
                ],
            ]
        );

        $this->addResponsiveControl(
            'list_width',
            [
                'label' => __('Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1400,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 400,
                    ],
                ],
                'default' => [
                    'size' => 400,
                ],
                'selectors' => [
                    '{{WRAPPER}}:not(.elementor-search--skin-topbar) .elementor-search__products' => 'width: {{SIZE}}{{UNIT}} !important',
                    '{{WRAPPER}}.elementor-search--skin-topbar .elementor-search__container' => 'border-width: 0 calc(50vw - {{SIZE}}{{UNIT}} / 2)',
                ],
            ]
        );

        $this->addResponsiveControl(
            'list_distance',
            [
                'label' => __('Distance'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__products' => 'margin-top: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            'list_padding',
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
                    '{{WRAPPER}} .elementor-search__products' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'list_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__products' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'skin!' => 'topbar',
                ],
            ]
        );

        $this->addResponsiveControl(
            'list_border_width',
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
                    '{{WRAPPER}} .elementor-search__products' => 'border-width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'skin!' => 'topbar',
                ],
            ]
        );

        $this->addResponsiveControl(
            'list_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__products' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'skin!' => 'topbar',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'list_box_shadow',
                'selector' => '{{WRAPPER}} .elementor-search__products',
                'condition' => [
                    'skin!' => 'topbar',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_products_style',
            [
                'label' => __('Products'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addResponsiveControl(
            'product_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} a.elementor-search__product-link' => 'padding: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'heading_product_image_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Image'),
                'condition' => [
                    'show_image!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            'product_image_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__product-image' => 'margin-inline-end: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'show_image!' => '',
                ],
            ]
        );

        $this->addControl(
            'heading_product_typography_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Typography'),
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'label' => __('Name'),
                'name' => 'product_name_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .elementor-search__product-name',
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'label' => __('Category'),
                'name' => 'product_category_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_2,
                'selector' => '{{WRAPPER}} .elementor-search__product-category',
                'condition' => [
                    'show_category!' => '',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'label' => __('Description'),
                'name' => 'product_description_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} .elementor-search__product-description',
                'condition' => [
                    'show_description!' => '',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'label' => __('Price', 'Shop.Theme.Catalog'),
                'name' => 'product_price_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .elementor-search__product-price',
                'condition' => [
                    'show_price!' => '',
                ],
            ]
        );

        $this->addControl(
            'heading_product_color_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Color'),
            ]
        );

        $this->startControlsTabs('tabs_product_colors');

        $this->startControlsTab(
            'tab_product_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'product_background_color',
            [
                'label' => __('Background'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-search__product-link' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'product_name_color',
            [
                'label' => __('Name'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__product-name' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'product_category_color',
            [
                'label' => __('Category'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__product-category' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'show_category!' => '',
                ],
            ]
        );

        $this->addControl(
            'product_description_color',
            [
                'label' => __('Description'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__product-description' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'show_description!' => '',
                ],
            ]
        );

        $this->addControl(
            'product_price_color',
            [
                'label' => __('Price', 'Shop.Theme.Catalog'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-search__product-price' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'show_price!' => '',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_product_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'product_background_color_hover',
            [
                'label' => __('Background'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-search__product-link.ui-state-focus' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'product_name_color_hover',
            [
                'label' => __('Name'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ui-state-focus .elementor-search__product-name' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'product_category_color_hover',
            [
                'label' => __('Category'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ui-state-focus .elementor-search__product-category' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'show_category!' => '',
                ],
            ]
        );

        $this->addControl(
            'product_description_color_hover',
            [
                'label' => __('Description'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ui-state-focus .elementor-search__product-description' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'show_description!' => '',
                ],
            ]
        );

        $this->addControl(
            'product_price_color_hover',
            [
                'label' => __('Price', 'Shop.Theme.Catalog'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ui-state-focus .elementor-search__product-price' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'show_price!' => '',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->endControlsSection();
    }

    protected function render()
    {
        $settings = $this->getSettingsForDisplay();
        $action = Helper::$link->getPageLink('search');

        $this->addRenderAttribute('container', 'class', 'elementor-search__container');
        $this->addRenderAttribute('input', [
            'placeholder' => $settings['placeholder'],
            'value' => \Tools::getValue('controller') === 'search' ? \Tools::getValue('s') : '',
            'minlength' => \Configuration::get('PS_SEARCH_MINWORDLEN'),
        ]); ?>
        <form class="elementor-search" role="search" action="<?php escape($action); ?>" method="get">
        <?php if (strpos($action, 'controller=search') !== false) { ?>
            <input type="hidden" name="controller" value="search">
        <?php } ?>
        <?php if ('topbar' === $settings['skin']) {
            $this->addRenderAttribute('container', [
                'id' => $container_id = 'elementor-search__container-' . $this->getId(),
                'role' => 'dialog',
                'aria-labelledby' => $label_id = 'elementor-search__label-' . $this->getId(),
            ]); ?>
            <div class="elementor-search__toggle" role="button" tabindex="0" aria-label="<?php esc_attr_e('Search', 'Shop.Theme.Catalog'); ?>" aria-controls="<?php escape($container_id); ?>" aria-expanded="false">
                <?php echo IconsManager::getBcIcon($settings, 'icon', ['aria-hidden' => 'true']); ?>
            </div>
        <?php } ?>
            <div <?php $this->printRenderAttributeString('container'); ?>>
            <?php if ('minimal' === $settings['skin']) { ?>
                <div class="elementor-search__icon" aria-label="<?php esc_attr_e('Search', 'Shop.Theme.Catalog'); ?>">
                    <?php echo IconsManager::getBcIcon($settings, 'icon', ['aria-hidden' => 'true']); ?>
                </div>
            <?php } elseif ('topbar' === $settings['skin']) { ?>
                <div class="elementor-search__label" id="<?php escape($label_id); ?>"><?php echo $settings['label']; ?></div>
                <div class="elementor-search__input-wrapper">
            <?php } ?>
                <input class="elementor-search__input" type="search" name="s" <?php $this->printRenderAttributeString('input'); ?> role="combobox" aria-expanded="false" aria-haspopup="listbox" aria-autocomplete="list">
            <?php if (!empty($settings['clear_icon']['value'])) { ?>
                <div class="elementor-search__icon elementor-search__clear" aria-hidden="true"><?php IconsManager::renderIcon($settings['clear_icon']); ?></div>
            <?php } ?>
            <?php if ('classic' === $settings['skin']) { ?>
                <button class="elementor-search__submit" type="submit">
                <?php if ('icon' === $settings['button_type']) { ?>
                    <?php echo IconsManager::getBcIcon($settings, 'icon', ['aria-hidden' => 'true']); ?>
                    <span class="screen-reader-text"><?php _e('Search', 'Shop.Theme.Catalog'); ?></span>
                <?php } elseif ('' !== $settings['button_text']) { ?>
                    <?php echo $settings['button_text']; ?>
                <?php } ?>
                </button>
            <?php } elseif ('topbar' === $settings['skin']) { ?>
                </div>
                <div class="dialog-lightbox-close-button" role="button" tabindex="0" aria-label="<?php esc_attr_e('Close', 'Shop.Theme.Global'); ?>">
                    <i class="ceicon-close" aria-hidden="true"></i>
                </div>
            <?php } ?>
            </div>
        </form>
        <?php
    }

    protected function contentTemplate()
    {
        ?>
        <# view.addRenderAttribute( 'container', 'class', 'elementor-search__container' ); #>
        <form class="elementor-search" action="<?php escape(Helper::$link->getPageLink('search')); ?>" role="search">
        <# if ( 'topbar' === settings.skin ) {
            view.addRenderAttribute( 'container', {
                id: 'elementor-search__container-' + id,
                role: 'dialog',
                'aria-labelledby': 'elementor-search__label-' + id
            } ); #>
            <div class="elementor-search__toggle" role="button" tabindex="0" aria-label="<?php esc_attr_e('Search', 'Shop.Theme.Catalog'); ?>" aria-controls="elementor-search__container-{{ id }}" aria-expanded="false">
                {{{ elementor.helpers.getBcIcon( view, settings, 'icon', { 'aria-hidden': true } ) }}}
            </div>
        <# } #>
            <div {{{ view.getRenderAttributeString( 'container' ) }}}>
            <# if ( 'minimal' === settings.skin ) { #>
                <div class="elementor-search__icon" aria-label="<?php esc_attr_e('Search', 'Shop.Theme.Catalog'); ?>">
                    {{{ elementor.helpers.getBcIcon( view, settings, 'icon', { 'aria-hidden': true } ) }}}
                </div>
            <# } else if ( 'topbar' === settings.skin ) { #>
                <div class="elementor-search__label" id="elementor-search__label-{{ id }}">{{{ settings.label }}}</div>
                <div class="elementor-search__input-wrapper">
            <# } #>
                <input class="elementor-search__input" type="search" name="s" placeholder="{{ settings.placeholder }}" minlength="<?php (int) \Configuration::get('PS_SEARCH_MINWORDLEN'); ?>" role="combobox" aria-expanded="false" aria-haspopup="listbox" aria-autocomplete="list">
            <# if ( settings.clear_icon.value ) { #>
                <div class="elementor-search__icon elementor-search__clear" aria-hidden="true">{{{ elementor.helpers.renderIcon(view, settings.clear_icon) }}}</div>
            <# } #>
            <# if ( 'classic' === settings.skin ) { #>
                <button class="elementor-search__submit" type="submit">
                <# if ( 'icon' === settings.button_type ) { #>
                    {{{ elementor.helpers.getBcIcon( view, settings, 'icon', { 'aria-hidden': true } ) }}}
                    <span class="screen-reader-text"><?php _e('Search', 'Shop.Theme.Catalog'); ?></span>
                <# } else if ( settings.button_text ) { #>
                    {{{ settings.button_text }}}
                <# } #>
                </button>
            <# } else if ( 'topbar' === settings.skin ) { #>
                </div>
                <div class="dialog-lightbox-close-button" role="button" tabindex="0" aria-label="<?php esc_attr_e('Close', 'Shop.Theme.Global'); ?>">
                    <i class="ceicon-close" aria-hidden="true"></i>
                </div>
            <# } #>
            </div>
        </form>
        <?php
    }
}
