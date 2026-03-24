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
 * Elementor image box widget.
 *
 * Elementor widget that displays an image, a headline and a text.
 *
 * @since 1.0.0
 */
class WidgetImageBox extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/86-widgets/general-widgets/304-image-box-widget';

    /**
     * Get widget name.
     *
     * Retrieve image box widget name.
     *
     * @since 1.0.0
     *
     * @return string Widget name
     */
    public function getName()
    {
        return 'image-box';
    }

    /**
     * Get widget title.
     *
     * Retrieve image box widget title.
     *
     * @since 1.0.0
     *
     * @return string Widget title
     */
    public function getTitle()
    {
        return __('Image Box');
    }

    /**
     * Get widget icon.
     *
     * Retrieve image box widget icon.
     *
     * @since 1.0.0
     *
     * @return string Widget icon
     */
    public function getIcon()
    {
        return 'eicon-image-box';
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
        return ['image', 'photo', 'visual', 'box'];
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
        return ['widget-image-box'];
    }

    /**
     * Register image box widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_image',
            [
                'label' => __('Image Box'),
            ]
        );

        $this->addControl(
            'image',
            [
                'label' => __('Choose Image'),
                'type' => ControlsManager::MEDIA,
                'seo' => true,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::getPlaceholderImageSrc(),
                ],
            ]
        );

        $this->addControl(
            'title_text',
            [
                'label' => __('Title & Description'),
                'type' => ControlsManager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('This is the heading'),
                'placeholder' => __('Enter your title'),
                'label_block' => true,
            ]
        );

        $this->addControl(
            'description_text',
            [
                'show_label' => false,
                'type' => ControlsManager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                'placeholder' => __('Enter your description'),
                'rows' => 10,
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
            ]
        );

        $this->addResponsiveControl(
            'position',
            [
                'label' => __('Image Position'),
                'type' => ControlsManager::CHOOSE,
                'default' => 'top',
                'mobile_default' => 'top',
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'top' => [
                        'title' => __('Top'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'prefix_class' => 'elementor%s-position-',
                'device_args' => [
                    ControlsStack::RESPONSIVE_DESKTOP => [
                        'toggle' => false,
                    ],
                ],
                'condition' => [
                    'image[url]!' => '',
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
            ]
        );

        $this->addControl(
            'title_size',
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
                'default' => 'h3',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_image',
            [
                'label' => __('Image'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'image[url]!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            'image_space',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image-box-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'image_size',
            [
                'label' => __('Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'default' => [
                    'size' => 30,
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'min' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image-box-wrapper .elementor-image-box-img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'image_height',
            [
                'label' => __('Height'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'vh'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image-box-img img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'image_object_fit',
            [
                'label' => __('Object Fit'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Default'),
                    'fill' => __('Fill'),
                    'cover' => __('Cover'),
                    'contain' => __('Contain'),
                    'scale-down' => __('Scale Down'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image-box-img img' => 'object-fit: {{VALUE}};',
                ],
                'condition' => [
                    'image_height[size]!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            'image_object_position',
            [
                'label' => __('Object Position'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Default'),
                    'center center' => __('Center Center'),
                    'center left' => __('Center Left'),
                    'center right' => __('Center Right'),
                    'top center' => __('Top Center'),
                    'top left' => __('Top Left'),
                    'top right' => __('Top Right'),
                    'bottom center' => __('Bottom Center'),
                    'bottom left' => __('Bottom Left'),
                    'bottom right' => __('Bottom Right'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image-box-img img' => 'object-position: {{VALUE}};',
                ],
                'condition' => [
                    'image_height[size]!' => '',
                ],
            ]
        );

        $this->startControlsTabs('image_effects');

        $this->startControlsTab(
            'normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'image_opacity',
            [
                'label' => __('Opacity'),
                'type' => ControlsManager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0.1,
                        'max' => 1,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image-box-img img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlCssFilter::getType(),
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} .elementor-image-box-img img',
            ]
        );

        $this->addControl(
            'image_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} img' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'image_border_border!' => '',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'image_opacity_hover',
            [
                'label' => __('Opacity'),
                'type' => ControlsManager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0.1,
                        'max' => 1,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}:has(:hover, :focus) .elementor-image-box-img img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlCssFilter::getType(),
            [
                'name' => 'css_filters_hover',
                'selector' => '{{WRAPPER}}:has(:hover, :focus) .elementor-image-box-img img',
            ]
        );

        $this->addControl(
            'image_border_color_hover',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}:has(:hover, :focus) img' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'image_border_border!' => '',
                ],
            ]
        );

        $this->addControl(
            'background_hover_transition',
            [
                'label' => __('Transition Duration') . ' (s)',
                'type' => ControlsManager::SLIDER,
                'default' => [
                    'size' => 0.3,
                ],
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image-box-img img' => 'transition: {{SIZE}}s',
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
                'name' => 'image_border',
                'exclude' => ['color'],
                'selector' => '{{WRAPPER}} .elementor-image-box-img img',
                'separator' => 'before',
            ]
        );

        $this->addResponsiveControl(
            'image_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image-box-img img' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .elementor-image-box-img img',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_content',
            [
                'label' => __('Content'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addResponsiveControl(
            'text_align',
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
                    '{{WRAPPER}} .elementor-image-box-wrapper' => 'text-align: {{VALUE}}; justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'content_vertical_alignment',
            [
                'label' => __('Vertical Alignment'),
                'type' => ControlsManager::CHOOSE,
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
                'default' => 'top',
                'toggle' => false,
                'prefix_class' => 'elementor-vertical-align-',
                'condition' => [
                    'position!' => 'top',
                ],
            ]
        );

        $this->addControl(
            'heading_title',
            [
                'label' => __('Title'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
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
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-image-box-title' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_1,
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
            'hover_title_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                     '{{WRAPPER}}:has(:hover, :focus) .elementor-image-box-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'hover_title_color_transition_duration',
            [
                'label' => __('Transition Duration'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['s', 'ms'],
                'default' => [
                    'unit' => 's',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image-box-title' => 'transition-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .elementor-image-box-title',
                'scheme' => SchemeTypography::TYPOGRAPHY_1,
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
                'selector' => '{{WRAPPER}} .elementor-image-box-title',
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'title_shadow',
                'selector' => '{{WRAPPER}} .elementor-image-box-title',
            ]
        );

        $this->addResponsiveControl(
            'title_bottom_space',
            [
                'label' => __('Spacing'),
                'size_units' => ['px', 'em'],
                'type' => ControlsManager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-image-box-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'heading_description',
            [
                'label' => __('Description'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'description_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-image-box-description' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_3,
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .elementor-image-box-description',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'description_shadow',
                'selector' => '{{WRAPPER}} .elementor-image-box-description',
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render image box widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        $has_content = '' !== $settings['title_text'] || '' !== $settings['description_text'];

        $html = '<div class="elementor-image-box-wrapper">';

        if (!empty($settings['image']['url'])) {
            $image_html = GroupControlImageSize::getAttachmentImageHtml($settings);

            if (!empty($settings['link']['url'])) {
                $this->addLinkAttributes('image_link', $settings['link']);
                '' !== $settings['title_text'] && $this->addRenderAttribute('image_link', 'tabindex', '-1');
                $image_html = '<a ' . $this->getRenderAttributeString('image_link') . '>' . $image_html . '</a>';
            }

            $html .= '<figure class="elementor-image-box-img">' . $image_html . '</figure>';
        }

        if ($has_content) {
            $html .= '<div class="elementor-image-box-content">';

            if ('' !== $settings['title_text']) {
                $this->addRenderAttribute('title_text', 'class', 'elementor-image-box-title');

                $settings['title_display'] && $this->addRenderAttribute('title_text', 'class', 'ce-display-' . $settings['title_display']);

                $title_tag = Utils::validateHtmlTag($settings['title_size']);
                $title_html = $settings['title_text'];

                if (!empty($settings['link']['url'])) {
                    $this->addInlineEditingAttributes('title_link', 'none');
                    $this->addLinkAttributes('title_link', $settings['link']);
                    $title_html = "<a {$this->getRenderAttributeString('title_link')}>$title_html</a>";
                } else {
                    $this->addInlineEditingAttributes('title_text', 'none');
                }

                $html .= "<$title_tag {$this->getRenderAttributeString('title_text')}>$title_html</$title_tag>";
            }

            if ('' !== $settings['description_text']) {
                $this->addRenderAttribute('description_text', 'class', 'elementor-image-box-description');
                $this->addInlineEditingAttributes('description_text');

                $html .= "<p {$this->getRenderAttributeString('description_text')}>$settings[description_text]</p>";
            }

            $html .= '</div>';
        }

        $html .= '</div>';

        echo $html;
    }

    /**
     * Render image box widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 2.9.0
     */
    protected function contentTemplate()
    {
        ?>
        <#
        var html = '<div class="elementor-image-box-wrapper">';

        if ( settings.image.url ) {
            var imageUrl = elementor.helpers.getMediaLink( settings.image.url ),
                imageHtml = '<img src="' + _.escape(imageUrl) + '" class="elementor-animation-' + _.escape(settings.hover_animation) + '">';

            if ( settings.link.url ) {
                view.addRenderAttribute( 'image_link', 'href', settings.link.url );
                settings.title_text && view.addRenderAttribute( 'image_link', 'tabindex', '-1' );
                imageHtml = '<a ' + view.getRenderAttributeString( 'image_link' ) + '>' + imageHtml + '</a>';
            }

            html += '<figure class="elementor-image-box-img">' + imageHtml + '</figure>';
        }

        if ( settings.title_text || settings.description_text ) {
            html += '<div class="elementor-image-box-content">';

            if ( settings.title_text ) {
                var title_html = settings.title_text,
                    titleSizeTag = elementor.helpers.validateHTMLTag( settings.title_size );

                if ( settings.link.url ) {
                    view.addInlineEditingAttributes( 'title_link', 'none' );
                    view.addRenderAttribute( 'title_link', 'href', settings.link.url );
                    title_html = '<a ' + view.getRenderAttributeString( 'title_link' ) + '>' + title_html + '</a>';
                } else {
                    view.addInlineEditingAttributes( 'title_text', 'none' );
                }

                view.addRenderAttribute( 'title_text', 'class', [
                    'elementor-image-box-title',
                    'ce-display-' + settings.title_display
                ] );

                html += '<' + titleSizeTag  + ' ' + view.getRenderAttributeString( 'title_text' ) + '>' + title_html + '</' + titleSizeTag  + '>';
            }

            if ( settings.description_text ) {
                view.addRenderAttribute( 'description_text', 'class', 'elementor-image-box-description' );

                view.addInlineEditingAttributes( 'description_text' );

                html += '<p ' + view.getRenderAttributeString( 'description_text' ) + '>' + settings.description_text + '</p>';
            }

            html += '</div>';
        }

        html += '</div>';

        print( html );
        #>
        <?php
    }
}
