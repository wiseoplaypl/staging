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

/**
 * Elementor button widget.
 *
 * Elementor widget that displays a button with the ability to control every
 * aspect of the button design.
 *
 * @since 1.0.0
 */
class WidgetButton extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/85-widgets/basic-widgets/297-button-widget';

    /**
     * Get widget name.
     *
     * Retrieve button widget name.
     *
     * @since 1.0.0
     *
     * @return string Widget name
     */
    public function getName()
    {
        return 'button';
    }

    /**
     * Get widget title.
     *
     * Retrieve button widget title.
     *
     * @since 1.0.0
     *
     * @return string Widget title
     */
    public function getTitle()
    {
        return __('Button');
    }

    /**
     * Get widget icon.
     *
     * Retrieve button widget icon.
     *
     * @since 1.0.0
     *
     * @return string Widget icon
     */
    public function getIcon()
    {
        return 'eicon-button';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the button widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * @since 2.0.0
     *
     * @return array Widget categories
     */
    public function getCategories()
    {
        return ['basic'];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     * @since 2.1.0
     *
     * @return array Widget keywords
     */
    public function getKeywords()
    {
        return ['button'];
    }

    protected function isDynamicContent()
    {
        return false;
    }

    /**
     * Get button sizes.
     *
     * Retrieve an array of button sizes for the button widget.
     *
     * @since 1.0.0
     *
     * @return array An array containing button sizes
     */
    public static function getButtonSizes()
    {
        return [
            'xs' => [
                'title' => __('Extra Small'),
                'icon' => 'eicon-xs',
            ],
            'sm' => [
                'title' => __('Small'),
                'icon' => 'eicon-sm',
            ],
            'md' => [
                'title' => __('Medium'),
                'icon' => 'eicon-md',
            ],
            'lg' => [
                'title' => __('Large'),
                'icon' => 'eicon-lg',
            ],
            'xl' => [
                'title' => __('Extra Large'),
                'icon' => 'eicon-xl',
            ],
        ];
    }

    /**
     * Register button widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_button',
            [
                'label' => __('Button'),
            ]
        );

        $this->addControl(
            'button_type',
            [
                'label' => __('Type'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Default'),
                    'primary' => __('Primary'),
                    'secondary' => __('Secondary'),
                    'info' => __('Info'),
                    'success' => __('Success'),
                    'warning' => __('Warning'),
                    'danger' => __('Danger'),
                ],
                'style_transfer' => true,
                'prefix_class' => 'elementor-button-',
            ]
        );

        $this->addControl(
            'text',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Click here'),
                'placeholder' => __('Click here'),
            ]
        );

        $this->addControl(
            'link',
            [
                'label' => __('Link'),
                'type' => ControlsManager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => '#',
                ],
            ]
        );

        $this->addResponsiveControl(
            'align',
            [
                'label' => __('Position'),
                'type' => ControlsManager::CHOOSE,
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
                    'justify' => [
                        'title' => __('Stretch'),
                        'icon' => 'eicon-h-align-stretch',
                    ],
                ],
                'prefix_class' => 'elementor%s-align-',
            ]
        );

        $this->addControl(
            'size',
            [
                'label' => __('Size'),
                'type' => ControlsManager::CHOOSE,
                'toggle' => false,
                'options' => self::getButtonSizes(),
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
                'condition' => [
                    'selected_icon[value]!' => '',
                ],
            ]
        );

        $this->addControl(
            'icon_indent',
            [
                'label' => __('Icon Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button-content-wrapper' => 'gap: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .elementor-button-text' => 'flex-grow: min(0, {{SIZE}})',
                ],
                'condition' => [
                    'selected_icon[value]!' => '',
                ],
            ]
        );

        $this->addControl(
            'button_css_id',
            [
                'label' => __('Button ID'),
                'type' => ControlsManager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'title' => __('Add your custom id WITHOUT the Pound key. e.g: my-id'),
                'description' => __('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.'),
                'separator' => 'before',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style',
            [
                'label' => __('Button'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $start = is_rtl() ? 'right' : 'left';
        $end = is_rtl() ? 'left' : 'right';

        $this->addResponsiveControl(
            'content_align',
            [
                'label' => __('Alignment'),
                'type' => ControlsManager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => __('Start'),
                        'icon' => "eicon-text-align-$start",
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'end' => [
                        'title' => __('End'),
                        'icon' => "eicon-text-align-$end",
                    ],
                    // 'space-between' => [
                    //     'title' => __('Space between'),
                    //     'icon' => 'eicon-text-align-justify',
                    // ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button-content-wrapper' => 'justify-content: {{VALUE}};',
                    '{{WRAPPER}} .elementor-button-text' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    'align' => 'justify',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} a.elementor-button',
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} a.elementor-button',
            ]
        );

        $this->startControlsTabs('tabs_button_style');

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
                    '{{WRAPPER}} a.elementor-button:not(#e)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBackground::getType(),
            [
                'name' => 'background',
                'types' => ['classic', 'gradient'],
                'exclude' => ['image', 'loading', 'position', 'xpos', 'ypos', 'attachment', 'attachment_alert', 'repeat', 'size', 'bg_width'],
                'fields_options' => [
                    'background' => [
                        'toggle' => false,
                        'default' => 'classic',
                    ],
                    'color' => [
                        'label' => __('Background Color'),
                        'scheme' => [
                            'type' => SchemeColor::getType(),
                            'value' => SchemeColor::COLOR_4,
                        ],
                        'selectors' => [
                            '{{SELECTOR}}' => '--ce-btn-bg-color: {{VALUE}}; background-color: var(--ce-btn-bg-color)',
                        ],
                    ],
                    'color_b' => [
                        'selectors' => [
                            '{{SELECTOR}}' => '--ce-btn-bg-color-b: {{VALUE}}; background-color: transparent',
                        ],
                    ],
                    'gradient_angle' => [
                        'selectors' => [
                            '{{SELECTOR}}' => 'background-image: linear-gradient({{SIZE}}{{UNIT}}, var(--ce-btn-bg-color) {{color_stop.SIZE}}{{color_stop.UNIT}}, var(--ce-btn-bg-color-b) {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
                        ],
                    ],
                    'gradient_position' => [
                        'selectors' => [
                            '{{SELECTOR}}' => 'background-image: radial-gradient(at {{VALUE}}, var(--ce-btn-bg-color) {{color_stop.SIZE}}{{color_stop.UNIT}}, var(--ce-btn-bg-color-b) {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
                        ],
                    ],
                ],
                'selector' => '{{WRAPPER}} a.elementor-button',
            ]
        );

        $this->addControl(
            'border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'border_border!' => '',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .elementor-button',
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
            'hover_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button:not(#e):hover, {{WRAPPER}} a.elementor-button:not(#e):focus' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'button_background_hover_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} a.elementor-button:focus' => '--ce-btn-bg-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'button_background_hover_color_b',
            [
                'label' => _x('Second Color', 'Background Control'),
                'type' => ControlsManager::COLOR,
                'default' => '#f2295b',
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} a.elementor-button:focus' => '--ce-btn-bg-color-b: {{VALUE}};',
                ],
                'condition' => [
                    'background_background' => 'gradient',
                ],
            ]
        );

        $this->addControl(
            'button_hover_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} a.elementor-button:focus' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'border_border!' => '',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'button_hover_box_shadow',
                'selector' => '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus',
            ]
        );

        $this->addControl(
            'button_hover_transition_duration',
            [
                'label' => __('Transition Duration'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['s', 'ms'],
                'default' => [
                    'unit' => 's',
                ],
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button' => 'transition-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'hover_animation',
            [
                'label' => __('Hover Animation'),
                'type' => ControlsManager::HOVER_ANIMATION,
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addGroupControl(
            GroupControlBorder::getType(),
            [
                'name' => 'border',
                'exclude' => ['color'],
                'selector' => '{{WRAPPER}} a.elementor-button',
                'separator' => 'before',
            ]
        );

        $this->addResponsiveControl(
            'border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'text_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render button widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        if (!empty($settings['link']['url'])) {
            $this->addLinkAttributes('button', $settings['link']);
            $this->addRenderAttribute('button', 'class', 'elementor-button-link');
        } else {
            $this->addRenderAttribute('button', 'role', 'button');
        }

        $this->addRenderAttribute('button', 'class', 'elementor-button elementor-size-' . $settings['size']);
        $this->addRenderAttribute('text', 'class', 'elementor-button-text');
        $this->addInlineEditingAttributes('text', 'none');

        if (!empty($settings['button_css_id'])) {
            $this->addRenderAttribute('button', 'id', $settings['button_css_id']);
        }

        if ($settings['hover_animation']) {
            $this->addRenderAttribute('button', 'class', 'elementor-animation-' . $settings['hover_animation']);
        } ?>
        <a <?php $this->printRenderAttributeString('button'); ?>>
            <span class="elementor-button-content-wrapper">
                <?php $this->renderIcon($settings); ?>
            <?php if ('' !== $settings['text']) { ?>
                <span <?php $this->printRenderAttributeString('text'); ?>><?php echo $settings['text']; ?></span>
            <?php } ?>
            </span>
        </a>
        <?php
    }

    /**
     * Render button icon.
     *
     * Render button widget icon.
     *
     * @since 2.9.0
     */
    protected function renderIcon(array &$settings)
    {
        if (!$icon = IconsManager::getBcIcon($settings, 'icon')) {
            return;
        }
        // BC fix
        $icon_align = $this->getSettings('icon_align'); ?>
        <span class="elementor-button-icon elementor-align-icon-<?php escape($icon_align); ?>" aria-hidden="true"><?php echo $icon; ?></span>
        <?php
    }

    /**
     * Render button widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 2.9.0
     */
    protected function contentTemplate()
    {
        ?>
        <#
        var icon;
        view.addRenderAttribute( 'button', 'class', [
            'elementor-button',
            'elementor-size-' + settings.size,
            'elementor-animation-' + settings.hover_animation
        ] );
        view.addRenderAttribute( 'button', settings.link.url ? { href: settings.link.url, 'class': 'elementor-button-link' } : { role: 'button' } );
        view.addRenderAttribute( 'text', 'class', 'elementor-button-text' );
        view.addInlineEditingAttributes( 'text', 'none' );
        #>
        <a id="{{ settings.button_css_id }}" {{{ view.getRenderAttributeString( 'button' ) }}}>
            <span class="elementor-button-content-wrapper">
            <# if ( icon = elementor.helpers.getBcIcon( view, settings, 'icon' ) ) { #>
                <span class="elementor-button-icon elementor-align-icon-{{ settings.icon_align }}" aria-hidden="true">{{{ icon }}}</span>
            <# } #>
            <# if ( settings.text ) { #>
                <span {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ settings.text }}}</span>
            <# } #>
            </span>
        </a>
        <?php
    }

    public function onImport($element)
    {
        return IconsManager::onImportMigration($element, 'icon', 'selected_icon');
    }
}
