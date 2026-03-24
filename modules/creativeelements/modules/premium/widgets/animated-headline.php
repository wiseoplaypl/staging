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

class ModulesXPremiumXWidgetsXAnimatedHeadline extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/87-widgets/premium-widgets/355-animated-headline-widget';

    public function getName()
    {
        return 'animated-headline';
    }

    public function getTitle()
    {
        return __('Animated Headline');
    }

    public function getIcon()
    {
        return 'eicon-animated-headline';
    }

    public function getCategories()
    {
        return ['premium'];
    }

    public function getKeywords()
    {
        return ['headline', 'heading', 'animation', 'title', 'text'];
    }

    public function getStyleDepends()
    {
        return ['widget-animated-headline'];
    }

    protected function isDynamicContent()
    {
        return false;
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'text_elements',
            [
                'label' => __('Headline'),
            ]
        );

        $this->addControl(
            'headline_style',
            [
                'label' => __('Style'),
                'type' => ControlsManager::SELECT,
                'default' => 'highlight',
                'options' => [
                    'highlight' => __('Highlighted'),
                    'rotate' => __('Rotating'),
                ],
                'prefix_class' => 'elementor-headline--style-',
                'render_type' => 'template',
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'animation_type',
            [
                'label' => __('Animation'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'typing' => 'Typing',
                    'clip' => 'Clip',
                    'flip' => 'Flip',
                    'swirl' => 'Swirl',
                    'blinds' => 'Blinds',
                    'drop-in' => 'Drop-in',
                    'wave' => 'Wave',
                    'slide' => 'Slide',
                    'slide-down' => 'Slide Down',
                ],
                'default' => 'typing',
                'condition' => [
                    'headline_style' => 'rotate',
                ],
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'marker',
            [
                'label' => __('Shape'),
                'type' => ControlsManager::SELECT,
                'default' => 'circle',
                'options' => [
                    'circle' => __('Circle'),
                    'curly' => _x('Curly', 'Shapes'),
                    'underline' => __('Underline'),
                    'double' => __('Double'),
                    'double_underline' => _x('Double Underline', 'Shapes'),
                    'underline_zigzag' => _x('Underline Zigzag', 'Shapes'),
                    'diagonal' => _x('Diagonal', 'Shapes'),
                    'strikethrough' => _x('Strikethrough', 'Shapes'),
                    'x' => 'X',
                ],
                'render_type' => 'template',
                'condition' => [
                    'headline_style' => 'highlight',
                ],
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'before_text',
            [
                'label' => __('Before Text'),
                'type' => ControlsManager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('This page is'),
                'placeholder' => __('Enter your headline'),
                'label_block' => true,
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'highlighted_text',
            [
                'label' => __('Highlighted Text'),
                'type' => ControlsManager::TEXT,
                'default' => __('Amazing'),
                'label_block' => true,
                'condition' => [
                    'headline_style' => 'highlight',
                ],
                'separator' => 'none',
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'rotating_text',
            [
                'label' => __('Rotating Text'),
                'type' => ControlsManager::TEXTAREA,
                'placeholder' => __('Enter each word in a separate line'),
                'separator' => 'none',
                'default' => "Better\nBigger\nFaster",
                'condition' => [
                    'headline_style' => 'rotate',
                ],
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'after_text',
            [
                'label' => __('After Text'),
                'type' => ControlsManager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('Enter your headline'),
                'label_block' => true,
                'separator' => 'none',
            ]
        );

        $this->addControl(
            'loop',
            [
                'label' => __('Infinite Loop'),
                'type' => ControlsManager::SWITCHER,
                'default' => 'yes',
                'frontend_available' => true,
                'condition' => [
                    'headline_style' => 'rotate',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'rotate_iteration_delay',
            [
                'label' => __('Duration') . ' (ms)',
                'type' => ControlsManager::NUMBER,
                'default' => 2500,
                'render_type' => 'template',
                'frontend_available' => true,
                'condition' => [
                    'headline_style' => 'rotate',
                ],
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
                'separator' => 'before',
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
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .elementor-headline' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'tag',
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

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_marker',
            [
                'label' => __('Shape'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'headline_style' => 'highlight',
                ],
            ]
        );

        $this->addControl(
            'marker_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-headline-dynamic-wrapper path' => 'stroke: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'stroke_width',
            [
                'label' => __('Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                    'em' => [
                        'max' => 2,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-headline-dynamic-wrapper path' => 'stroke-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'above_content',
            [
                'label' => __('Bring to Front'),
                'type' => ControlsManager::SWITCHER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-headline-dynamic-wrapper svg' => 'z-index: 2',
                    '{{WRAPPER}} .elementor-headline-dynamic-text' => 'z-index: auto',
                ],
            ]
        );

        $this->addControl(
            'rounded_edges',
            [
                'label' => __('Rounded Edges'),
                'type' => ControlsManager::SWITCHER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-headline-dynamic-wrapper path' => 'stroke-linecap: round; stroke-linejoin: round',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_text',
            [
                'label' => __('Headline'),
                'tab' => ControlsManager::TAB_STYLE,
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
                    '{{WRAPPER}} .elementor-headline-plain-text' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'title_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .elementor-headline',
            ]
        );

        $this->addGroupControl(
            GroupControlTextStroke::getType(),
            [
                'name' => 'text_stroke',
                'selector' => '{{WRAPPER}} .elementor-headline',
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'title_text_shadow',
                'selector' => '{{WRAPPER}} .elementor-headline',
            ]
        );

        $this->addControl(
            'heading_words_style',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Animated Text'),
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'words_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_2,
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--dynamic-text-color: {{VALUE}}',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'words_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .elementor-headline-dynamic-text',
                'exclude' => ['font_size'],
            ]
        );

        $this->addGroupControl(
            GroupControlTextStroke::getType(),
            [
                'name' => 'animated_text_stroke',
                'selector' => '{{WRAPPER}} .elementor-headline .elementor-headline-dynamic-wrapper',
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'animated_text_shadow',
                'selector' => '{{WRAPPER}} .elementor-headline .elementor-headline-dynamic-wrapper',
            ]
        );

        $this->addControl(
            'typing_animation_highlight_colors',
            [
                'label' => __('Selected', 'Shop.Theme.Checkout'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'headline_style' => 'rotate',
                    'animation_type' => 'typing',
                ],
            ]
        );

        $this->addControl(
            'highlighted_text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--typing-selected-color: {{VALUE}}',
                ],
                'condition' => [
                    'headline_style' => 'rotate',
                    'animation_type' => 'typing',
                ],
            ]
        );

        $this->addControl(
            'highlighted_text_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--typing-selected-bg-color: {{VALUE}}',
                ],
                'condition' => [
                    'headline_style' => 'rotate',
                    'animation_type' => 'typing',
                ],
            ]
        );

        $this->endControlsSection();
    }

    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        $this->addRenderAttribute('headline', 'class', 'elementor-headline');

        if ('rotate' === $settings['headline_style']) {
            $this->addRenderAttribute('headline', 'class', [
                'elementor-headline-animation-type-' . $settings['animation_type'],
            ]);

            $is_letter_animation = in_array($settings['animation_type'], ['typing', 'swirl', 'blinds', 'wave']);

            if ($is_letter_animation) {
                $this->addRenderAttribute('headline', 'class', 'elementor-headline-letters');
            }
        }

        if ($has_link = !empty($settings['link']['url'])) {
            $this->addLinkAttributes('url', $settings['link']);

            echo '<a ' . $this->getRenderAttributeString('url') . '>';
        } ?>
        <<?php Utils::printValidatedHtmlTag($settings['tag']); ?> <?php $this->printRenderAttributeString('headline'); ?>>
        <?php if ('' !== $settings['before_text']) { ?>
            <span class="elementor-headline-plain-text elementor-headline-text-wrapper">
                <?php echo $settings['before_text']; ?>
            </span>
        <?php } ?>
            <span class="elementor-headline-dynamic-wrapper elementor-headline-text-wrapper"></span>
        <?php if ('' !== $settings['after_text']) { ?>
            <span class="elementor-headline-plain-text elementor-headline-text-wrapper">
                <?php echo $settings['after_text']; ?>
            </span>
        <?php } ?>
        </<?php Utils::printValidatedHtmlTag($settings['tag']); ?>>
        <?php
        if ($has_link) {
            echo '</a>';
        }
    }

    protected function contentTemplate()
    {
        ?>
        <#
        var headlineClasses = 'elementor-headline';

        if ( 'rotate' === settings.headline_style ) {
            headlineClasses += ' elementor-headline-animation-type-' + settings.animation_type;

            var isLetterAnimation = -1 !== [ 'typing', 'swirl', 'blinds', 'wave' ].indexOf( settings.animation_type );

            if ( isLetterAnimation ) {
                headlineClasses += ' elementor-headline-letters';
            }
        }

        if ( settings.link.url ) {
            print('<a href="#">');
        }
        #>
        <{{{ settings.tag }}} class="{{{ headlineClasses }}}">
        <# if ( settings.before_text ) { #>
            <span class="elementor-headline-plain-text elementor-headline-text-wrapper">
                {{{ settings.before_text }}}
            </span>
        <# } #>
        <# if ( settings.rotating_text ) { #>
            <span class="elementor-headline-dynamic-wrapper elementor-headline-text-wrapper"></span>
        <# } #>
        <# if ( settings.after_text ) { #>
            <span class="elementor-headline-plain-text elementor-headline-text-wrapper">
                {{{ settings.after_text }}}
            </span>
        <# } #>
        </{{{ settings.tag }}}>
        <#
        if ( settings.link.url ) {
            print('</a>');
        }
        #>
        <?php
    }
}
