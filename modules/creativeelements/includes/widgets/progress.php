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
 * Elementor progress widget.
 *
 * Elementor widget that displays an escalating progress bar.
 *
 * @since 1.0.0
 */
class WidgetProgress extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/86-widgets/general-widgets/308-progress-bar-widget';

    /**
     * Get widget name.
     *
     * Retrieve progress widget name.
     *
     * @since 1.0.0
     *
     * @return string Widget name
     */
    public function getName()
    {
        return 'progress';
    }

    /**
     * Get widget title.
     *
     * Retrieve progress widget title.
     *
     * @since 1.0.0
     *
     * @return string Widget title
     */
    public function getTitle()
    {
        return __('Progress Bar');
    }

    /**
     * Get widget icon.
     *
     * Retrieve progress widget icon.
     *
     * @since 1.0.0
     *
     * @return string Widget icon
     */
    public function getIcon()
    {
        return 'eicon-skill-bar';
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
        return ['progress', 'bar'];
    }

    protected function isDynamicContent()
    {
        return false;
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
        return ['widget-progress'];
    }

    /**
     * Register progress widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_progress',
            [
                'label' => __('Progress Bar'),
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
                'placeholder' => __('Enter your title'),
                'default' => __('My Skill'),
                'label_block' => true,
            ]
        );

        $this->addControl(
            'title_display',
            [
                'label' => __('Title Display'),
                'type' => ControlsManager::CHOOSE,
                'options' => [
                    'none' => [
                        'title' => __('Hide'),
                        'icon' => 'eicon-ban',
                    ],
                ] + WidgetHeading::getDisplaySizes(),
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
                'default' => 'span',
                'condition' => [
                    'title!' => '',
                    'title_display!' => 'none',
                ],
            ]
        );

        $this->addControl(
            'progress_type',
            [
                'label' => __('Type'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Default'),
                    'info' => __('Info'),
                    'success' => __('Success'),
                    'warning' => __('Warning'),
                    'danger' => __('Danger'),
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'percent',
            [
                'label' => __('Percentage'),
                'type' => ControlsManager::SLIDER,
                'default' => [
                    'size' => 50,
                    'unit' => '%',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->addControl(
            'display_percentage',
            [
                'label' => __('Display Percentage'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'return_value' => 'show',
                'default' => 'show',
                'condition' => [
                    'percent!' => '',
                ],
            ]
        );

        $this->addControl(
            'inner_text',
            [
                'label' => __('Inner Text'),
                'type' => ControlsManager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('e.g. Web Designer'),
                'default' => __('Web Designer'),
                'label_block' => true,
                'separator' => 'before',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_progress_style',
            [
                'label' => __('Progress Bar'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'title_heading',
            [
                'label' => __('Title'),
                'type' => ControlsManager::HEADING,
                'condition' => [
                    'title!' => '',
                    'title_display!' => 'none',
                ],
            ]
        );

        $this->addControl(
            'title_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-title' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_1,
                ],
                'condition' => [
                    'title!' => '',
                    'title_display!' => 'none',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .elementor-title',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
                'condition' => [
                    'title!' => '',
                    'title_display!' => 'none',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'title_shadow',
                'selector' => '{{WRAPPER}} .elementor-title',
                'condition' => [
                    'title!' => '',
                    'title_display!' => 'none',
                ],
            ]
        );

        $this->addControl(
            'percentage_divider',
            [
                'type' => ControlsManager::DIVIDER,
                'condition' => [
                    'title!' => '',
                    'title_display!' => 'none',
                ],
            ]
        );

        $this->addControl(
            'percentage_heading',
            [
                'label' => __('Percentage'),
                'type' => ControlsManager::HEADING,
            ]
        );

        $this->addControl(
            'bar_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-progress-wrapper .elementor-progress-bar' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'bar_bg_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-progress-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'bar_height',
            [
                'label' => __('Height'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-progress-bar' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'bar_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-progress-wrapper' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->addControl(
            'inner_text_heading',
            [
                'label' => __('Inner Text'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'bar_inline_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-progress-bar' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'bar_inner_typography',
                'selector' => '{{WRAPPER}} .elementor-progress-bar',
                'exclude' => [
                    'line_height',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'bar_innner_shadow',
                'selector' => '{{WRAPPER}} .elementor-progress-bar',
                'condition' => [
                    'title!' => '',
                    'title_display!' => 'none',
                ],
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render progress widget output on the frontend.
     * Make sure value does no exceed 100%.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        if (100 < $progress_percentage = is_numeric($settings['percent']['size']) ? $settings['percent']['size'] : 0) {
            $progress_percentage = 100;
        }

        $this->addRenderAttribute('wrapper', [
            'class' => 'elementor-progress-wrapper',
            'role' => 'progressbar',
            'aria-valuemin' => '0',
            'aria-valuemax' => '100',
            'aria-valuenow' => $progress_percentage,
        ]);
        $settings['inner_text'] && $this->addRenderAttribute('wrapper', 'aria-valuetext', "$progress_percentage% ($settings[inner_text])");
        $settings['progress_type'] && $this->addRenderAttribute('wrapper', 'class', 'progress-' . $settings['progress_type']);

        $this->addRenderAttribute('progress-bar', [
            'class' => 'elementor-progress-bar',
            'data-max' => $progress_percentage,
        ]);

        $this->addRenderAttribute('inner_text', 'class', 'elementor-progress-text');
        $this->addInlineEditingAttributes('inner_text');

        if ('' !== $settings['title']) {
            if ('none' !== $settings['title_display']) {
                $title_tag = Utils::validateHtmlTag($settings['title_tag']);
                $title_id = 'elementor-progress-bar__title-' . $this->getId();
                $this->addRenderAttribute('wrapper', 'aria-labelledby', $title_id);

                $this->addRenderAttribute('title', [
                    'class' => 'elementor-title',
                    'id' => $title_id,
                ]);
                $this->addInlineEditingAttributes('title');
                $settings['title_display'] && $this->addRenderAttribute('title', 'class', 'ce-display-' . $settings['title_display']);

                echo "<$title_tag {$this->getRenderAttributeString('title')}>$settings[title]</$title_tag>";
            } else {
                $this->addRenderAttribute('wrapper', 'aria-label', $settings['title']);
            }
        } ?>
        <div <?php $this->printRenderAttributeString('wrapper'); ?>>
            <div <?php $this->printRenderAttributeString('progress-bar'); ?>>
                <span <?php $this->printRenderAttributeString('inner_text'); ?>><?php echo $settings['inner_text']; ?></span>
            <?php if ('show' === $settings['display_percentage']) { ?>
                <span class="elementor-progress-percentage"><?php echo $progress_percentage; ?>%</span>
            <?php } ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render progress widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 2.9.0
     */
    protected function contentTemplate()
    {
        ?>
        <#
        var progress_percentage = 0;
        if ( ! isNaN( settings.percent.size ) ) {
            progress_percentage = 100 < settings.percent.size ? 100 : settings.percent.size;
        }

        view.addRenderAttribute( 'progressWrapper', {
            'class': [ 'elementor-progress-wrapper', 'progress-' + settings.progress_type ],
            'role': 'progressbar',
            'aria-valuemin': '0',
            'aria-valuemax': '100',
            'aria-valuenow': progress_percentage,
        } );
        settings.inner_text && view.addRenderAttribute( 'progressWrapper', 'aria-valuetext', progress_percentage + '% (' + settings.inner_text + ')' );

        view.addRenderAttribute( 'inner_text', 'class', 'elementor-progress-text' );
        view.addInlineEditingAttributes( 'inner_text' );

        if ( settings.title ) {
            if ('none' !== settings.title_display) {
                const title_tag = elementor.helpers.validateHTMLTag( settings.title_tag );
                const title_id = 'elementor-progress-bar__title-' + view.getID();
                view.addRenderAttribute( 'progressWrapper', 'aria-labelledby', title_id );

                view.addRenderAttribute( 'title', {
                    'class': 'elementor-title',
                    'id': title_id,
                } );
                view.addInlineEditingAttributes( 'title' );
                settings.title_display && view.addRenderAttribute( 'title', 'class', 'ce-display-' + settings.title_display );

                #><{{ title_tag }} {{{ view.getRenderAttributeString( 'title' ) }}}>{{ settings.title }}</{{ title_tag }}><#
            } else {
                view.addRenderAttribute( 'progressWrapper', 'aria-label', settings.title );
            }
        } #>
        <div {{{ view.getRenderAttributeString( 'progressWrapper' ) }}}>
            <div class="elementor-progress-bar" data-max="{{ progress_percentage }}">
                <span {{{ view.getRenderAttributeString( 'inner_text' ) }}}>{{{ settings.inner_text }}}</span>
            <# if ( 'show' === settings.display_percentage ) { #>
                <span class="elementor-progress-percentage">{{{ progress_percentage }}}%</span>
            <# } #>
            </div>
        </div>
        <?php
    }
}
