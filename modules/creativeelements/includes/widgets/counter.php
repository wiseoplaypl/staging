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
 * Elementor counter widget.
 *
 * Elementor widget that displays stats and numbers in an escalating manner.
 *
 * @since 1.0.0
 */
class WidgetCounter extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/86-widgets/general-widgets/307-counter-widget';

    /**
     * Get widget name.
     *
     * Retrieve counter widget name.
     *
     * @since 1.0.0
     *
     * @return string Widget name
     */
    public function getName()
    {
        return 'counter';
    }

    /**
     * Get widget title.
     *
     * Retrieve counter widget title.
     *
     * @since 1.0.0
     *
     * @return string Widget title
     */
    public function getTitle()
    {
        return __('Counter');
    }

    /**
     * Get widget icon.
     *
     * Retrieve counter widget icon.
     *
     * @since 1.0.0
     *
     * @return string Widget icon
     */
    public function getIcon()
    {
        return 'eicon-counter';
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
        return ['counter'];
    }

    protected function isDynamicContent()
    {
        return false;
    }

    /**
     * Get script dependencies
     *
     * Retrieve the list of scripts dependencies the widget requires.
     *
     * @since 1.3.0
     *
     * @return array Widget scripts dependencies
     */
    public function getScriptDepends()
    {
        return ['jquery-numerator'];
    }

    /**
     * Get style dependencies.
     *
     * Retrieve the list of style dependencies the widget requires.
     *
     * @since 2.14.0
     *
     * @return array Widget style dependencies
     */
    public function getStyleDepends()
    {
        return ['widget-counter'];
    }

    /**
     * Register counter widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     */
    protected function registerControls()
    {
        $start = is_rtl() ? 'right' : 'left';
        $end = !is_rtl() ? 'right' : 'left';

        $this->startControlsSection(
            'section_counter',
            [
                'label' => __('Counter'),
            ]
        );

        $this->addControl(
            'starting_number',
            [
                'label' => __('Starting Number'),
                'type' => ControlsManager::NUMBER,
                'default' => 0,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->addControl(
            'ending_number',
            [
                'label' => __('Ending Number'),
                'type' => ControlsManager::NUMBER,
                'default' => 100,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->addControl(
            'prefix',
            [
                'label' => __('Number Prefix'),
                'type' => ControlsManager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => 1,
            ]
        );

        $this->addControl(
            'suffix',
            [
                'label' => __('Number Suffix'),
                'type' => ControlsManager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('Plus'),
            ]
        );

        $this->addControl(
            'duration',
            [
                'label' => __('Animation Duration') . ' (ms)',
                'type' => ControlsManager::NUMBER,
                'default' => 2000,
                'min' => 100,
                'step' => 100,
            ]
        );

        $this->addControl(
            'thousand_separator',
            [
                'label' => __('Thousand Separator'),
                'type' => ControlsManager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
            ]
        );

        $this->addControl(
            'thousand_separator_char',
            [
                'label' => __('Separator'),
                'type' => ControlsManager::SELECT,
                'condition' => [
                    'thousand_separator' => 'yes',
                ],
                'options' => [
                    '' => __('Default'),
                    '.' => 'Dot',
                    ',' => 'Comma',
                    ' ' => 'Space',
                    '_' => 'Underline',
                    "'" => 'Apostrophe',
                ],
            ]
        );

        $this->addControl(
            'title',
            [
                'label' => __('Title'),
                'type' => ControlsManager::TEXT,
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Cool Number'),
                'placeholder' => __('Cool Number'),
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

        $this->endControlsSection();

        $this->startControlsSection(
            'section_counter_style',
            [
                'label' => __('Counter'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'heading_title',
            [
                'label' => __('Title'),
                'type' => ControlsManager::HEADING,
                'condition' => [
                    'title!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            'title_position',
            [
                'label' => __('Position'),
                'type' => ControlsManager::CHOOSE,
                'options' => [
                    'before' => [
                        'title' => __('Before'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'after' => [
                        'title' => __('After'),
                        'icon' => 'eicon-v-align-bottom',
                    ],

                    'start' => [
                        'title' => __('Start'),
                        'icon' => "eicon-h-align-$start",
                    ],
                    'end' => [
                        'title' => __('End'),
                        'icon' => "eicon-h-align-$end",
                    ],
                ],
                'selectors_dictionary' => [
                    'before' => 'flex-direction: column;',
                    'after' => 'flex-direction: column-reverse;',
                    'start' => 'flex-direction: row;',
                    'end' => 'flex-direction: row-reverse;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-counter' => '{{VALUE}}',
                ],
                'condition' => [
                    'title!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            'title_horizontal_alignment',
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
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-counter-title' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'title!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            'title_vertical_alignment',
            [
                'label' => __('Vertical Alignment'),
                'type' => ControlsManager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => __('Top'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => __('Middle'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'end' => [
                        'title' => __('Bottom'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-counter-title' => 'align-items: {{VALUE}};',
                ],
                'condition' => [
                    'title!' => '',
                    'title_position' => ['start', 'end'],
                ],
            ]
        );

        $this->addResponsiveControl(
            'title_gap',
            [
                'label' => __('Gap'),
                'type' => ControlsManager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-counter' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'title!' => '',
                    'title_position' => ['', 'before', 'after'],
                ],
            ]
        );

        $this->addControl(
            'heading_number',
            [
                'label' => __('Number'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addResponsiveControl(
            'number_position',
            [
                'label' => __('Position'),
                'type' => ControlsManager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => __('Start'),
                        'icon' => "eicon-h-align-$start",
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'end' => [
                        'title' => __('End'),
                        'icon' => "eicon-h-align-$end",
                    ],
                    'stretch' => [
                        'title' => __('Stretch'),
                        'icon' => 'eicon-grow',
                    ],
                ],
                'selectors_dictionary' => [
                    'start' => 'text-align: {{VALUE}}; --counter-prefix-grow: 0; --counter-suffix-grow: 1; --counter-number-grow: 0;',
                    'center' => 'text-align: {{VALUE}}; --counter-prefix-grow: 1; --counter-suffix-grow: 1; --counter-number-grow: 0;',
                    'end' => 'text-align: {{VALUE}}; --counter-prefix-grow: 1; --counter-suffix-grow: 0; --counter-number-grow: 0;',
                    'stretch' => '--counter-prefix-grow: 0; --counter-suffix-grow: 0; --counter-number-grow: 1;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-counter-number-wrapper' => '{{VALUE}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            'number_alignment',
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
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-counter-number' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    'number_position' => 'stretch',
                ],
            ]
        );

        $this->addResponsiveControl(
            'number_gap',
            [
                'label' => __('Gap'),
                'type' => ControlsManager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-counter-number-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'number_position',
                            'operator' => '!==',
                            'value' => 'stretch',
                        ],
                        [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => 'prefix',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                                [
                                    'name' => 'suffix',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_number',
            [
                'label' => __('Number'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'number_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-counter-number-wrapper' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'typography_number',
                'scheme' => SchemeTypography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .elementor-counter-number-wrapper',
            ]
        );

        $this->addGroupControl(
            GroupControlTextStroke::getType(),
            [
                'name' => 'number_stroke',
                'selector' => '{{WRAPPER}} .elementor-counter-number-wrapper',
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'number_shadow',
                'selector' => '{{WRAPPER}} .elementor-counter-number-wrapper',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_title',
            [
                'label' => __('Title'),
                'tab' => ControlsManager::TAB_STYLE,
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
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-counter-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'typography_title',
                'scheme' => SchemeTypography::TYPOGRAPHY_2,
                'selector' => '{{WRAPPER}} .elementor-counter-title',
            ]
        );

        $this->addGroupControl(
            GroupControlTextStroke::getType(),
            [
                'name' => 'title_stroke',
                'selector' => '{{WRAPPER}} .elementor-counter-title',
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'title_shadow',
                'selector' => '{{WRAPPER}} .elementor-counter-title',
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render counter widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 2.9.0
     */
    protected function contentTemplate()
    {
        ?>
        <#
        const titleTag = elementor.helpers.validateHTMLTag( settings.title_tag );
        view.addRenderAttribute( 'counter_title', 'class', 'elementor-counter-title' );
        view.addInlineEditingAttributes( 'counter_title' );
        #>
        <div class="elementor-counter">
        <# if ( settings.title ) { #>
            <{{ titleTag }} {{{ view.getRenderAttributeString( 'counter_title' ) }}}>{{{ settings.title }}}</{{ titleTag }}>
        <# } #>
            <div class="elementor-counter-number-wrapper">
                <span class="elementor-counter-number-prefix">{{{ settings.prefix }}}</span>
                <span class="elementor-counter-number" data-duration="{{ settings.duration }}" data-to-value="{{ settings.ending_number }}"
                    data-delimiter="{{ settings.thousand_separator ? settings.thousand_separator_char || ',' : '' }}">{{{ settings.starting_number }}}</span>
                <span class="elementor-counter-number-suffix">{{{ settings.suffix }}}</span>
            </div>
        </div>
        <?php
    }

    /**
     * Render counter widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        $this->addRenderAttribute('counter', [
            'class' => 'elementor-counter-number',
            'data-duration' => $settings['duration'],
            'data-to-value' => $settings['ending_number'],
            'data-from-value' => $settings['starting_number'],
        ]);

        if ($settings['thousand_separator']) {
            $this->addRenderAttribute('counter', 'data-delimiter', $settings['thousand_separator_char'] ?: ',');
        }
        $this->addRenderAttribute('counter_title', 'class', 'elementor-counter-title');
        $this->addInlineEditingAttributes('counter_title');
        ?>
        <div class="elementor-counter">
        <?php if ($settings['title']) { ?>
            <<?php Utils::printValidatedHtmlTag($settings['title_tag']); ?> <?php $this->printRenderAttributeString('counter_title'); ?>>
                <?php echo $settings['title']; ?>
            </<?php Utils::printValidatedHtmlTag($settings['title_tag']); ?>>
        <?php } ?>
            <div class="elementor-counter-number-wrapper">
                <span class="elementor-counter-number-prefix"><?php echo $settings['prefix']; ?></span>
                <span <?php $this->printRenderAttributeString('counter'); ?>><?php echo $settings['starting_number']; ?></span>
                <span class="elementor-counter-number-suffix"><?php echo $settings['suffix']; ?></span>
            </div>
        </div>
        <?php
    }
}
