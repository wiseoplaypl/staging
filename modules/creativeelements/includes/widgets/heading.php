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
 * Elementor heading widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class WidgetHeading extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/85-widgets/basic-widgets/294-heading-widget';

    /**
     * Get widget name.
     *
     * Retrieve heading widget name.
     *
     * @since 1.0.0
     *
     * @return string Widget name
     */
    public function getName()
    {
        return 'heading';
    }

    /**
     * Get widget title.
     *
     * Retrieve heading widget title.
     *
     * @since 1.0.0
     *
     * @return string Widget title
     */
    public function getTitle()
    {
        return __('Heading');
    }

    /**
     * Get widget icon.
     *
     * Retrieve heading widget icon.
     *
     * @since 1.0.0
     *
     * @return string Widget icon
     */
    public function getIcon()
    {
        return 'eicon-t-letter';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the heading widget belongs to.
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
        return ['heading', 'title', 'text'];
    }

    protected function isDynamicContent()
    {
        return false;
    }

    /**
     * Get display sizes.
     *
     * Retrieve an array of display sizes for the heading widget.
     *
     * @since 2.9.0
     *
     * @return array An array containing display sizes
     */
    public static function getDisplaySizes()
    {
        return [
            'small' => [
                'title' => __('Small'),
                'icon' => 'eicon-sm',
            ],
            'medium' => [
                'title' => __('Medium'),
                'icon' => 'eicon-md',
            ],
            'large' => [
                'title' => __('Large'),
                'icon' => 'eicon-lg',
            ],
            'xl' => [
                'title' => __('Extra Large'),
                'icon' => 'eicon-xl',
            ],
            'xxl' => [
                'title' => __('XXL'),
                'icon' => 'eicon-xxl',
            ],
        ];
    }

    /**
     * Register heading widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_title',
            [
                'label' => __('Title'),
            ]
        );

        $this->addControl(
            'title',
            [
                'label' => __('Title'),
                'type' => ControlsManager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('Enter your title'),
                'default' => __('Add Your Heading Text Here'),
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
                    'url' => '',
                ],
            ]
        );

        $this->addControl(
            'size',
            [
                'label' => __('Display'),
                'type' => ControlsManager::CHOOSE,
                'options' => self::getDisplaySizes(),
                'style_transfer' => true,
            ]
        );

        $this->addControl(
            'header_size',
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
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_title_style',
            [
                'label' => __('Title'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->startControlsTabs('title_colors');

        $this->startControlsTab(
            'title_colors_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'title_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-heading-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'title_colors_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'title_hover_color',
            [
                'label' => __('Link Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-heading-title a:not(#e):hover, {{WRAPPER}} .elementor-heading-title a:not(#e):focus' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'title_hover_color_transition_duration',
            [
                'label' => __('Transition Duration'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['s', 'ms'],
                'default' => [
                    'unit' => 's',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-heading-title a' => 'transition: color {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .elementor-heading-title',
                'fields_options' => [
                    'typography' => [
                        'separator' => 'before',
                    ],
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTextStroke::getType(),
            [
                'name' => 'text_stroke',
                'selector' => '{{WRAPPER}} .elementor-heading-title',
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .elementor-heading-title',
            ]
        );

        $this->addControl(
            'blend_mode',
            [
                'label' => __('Blend Mode'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Normal'),
                    'multiply' => 'Multiply',
                    'screen' => 'Screen',
                    'overlay' => 'Overlay',
                    'darken' => 'Darken',
                    'lighten' => 'Lighten',
                    'color-dodge' => 'Color Dodge',
                    'saturation' => 'Saturation',
                    'color' => 'Color',
                    'difference' => 'Difference',
                    'exclusion' => 'Exclusion',
                    'hue' => 'Hue',
                    'luminosity' => 'Luminosity',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-heading-title' => 'mix-blend-mode: {{VALUE}}',
                ],
                'separator' => 'none',
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render heading widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        if ('' === $settings['title']) {
            return;
        }

        $this->addRenderAttribute('title', 'class', 'elementor-heading-title');

        $settings['size'] && $this->addRenderAttribute('title', 'class', 'ce-display-' . $settings['size']);

        $this->addInlineEditingAttributes('title');

        $title_tag = Utils::validateHtmlTag($settings['header_size']);
        $title = $settings['title'];

        if (!empty($settings['link']['url'])) {
            $this->addLinkAttributes('url', $settings['link']);

            $title = "<a {$this->getRenderAttributeString('url')}>$title</a>";
        }

        echo "<$title_tag {$this->getRenderAttributeString('title')}>$title</$title_tag>";
    }

    /**
     * Render heading widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 2.9.0
     */
    protected function contentTemplate()
    {
        ?>
        <#
        var title = settings.title,
            headerSize = elementor.helpers.validateHTMLTag(settings.header_size);

        if ( '' !== settings.link.url ) {
            title = '<a href="' + _.escape(settings.link.url) + '">' + title + '</a>';
        }

        view.addRenderAttribute( 'title', 'class', ['elementor-heading-title', 'ce-display-' + settings.size] );

        view.addInlineEditingAttributes( 'title' );
        #>
        <{{{ headerSize }}} {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ title }}}</{{{ headerSize }}}>
        <?php
    }
}
