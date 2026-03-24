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
 * Elementor star rating widget.
 *
 * Elementor widget that displays star rating.
 *
 * @since 2.3.0
 */
class WidgetStarRating extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/86-widgets/general-widgets/353-star-rating-widget';

    /**
     * Get widget name.
     *
     * Retrieve star rating widget name.
     *
     * @since 2.3.0
     *
     * @return string Widget name
     */
    public function getName()
    {
        return 'star-rating';
    }

    /**
     * Get widget title.
     *
     * Retrieve star rating widget title.
     *
     * @since 2.3.0
     *
     * @return string Widget title
     */
    public function getTitle()
    {
        return __('Star Rating');
    }

    /**
     * Get widget icon.
     *
     * Retrieve star rating widget icon.
     *
     * @since 2.3.0
     *
     * @return string Widget icon
     */
    public function getIcon()
    {
        return 'eicon-rating';
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     * @since 2.3.0
     *
     * @return array Widget keywords
     */
    public function getKeywords()
    {
        return ['star', 'rating', 'rate', 'review'];
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
        return ['widget-star-rating'];
    }

    /**
     * Register star rating widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 2.3.0
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_rating',
            [
                'label' => __('Rating'),
            ]
        );

        $this->addControl(
            'rating_scale',
            [
                'label' => __('Rating Scale'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '5' => '0-5',
                    '10' => '0-10',
                ],
                'default' => '5',
            ]
        );

        $this->addControl(
            'rating',
            [
                'label' => __('Rating'),
                'type' => ControlsManager::NUMBER,
                'min' => 0,
                'max' => 10,
                'step' => 0.1,
                'default' => 5,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->addControl(
            'star_style',
            [
                'label' => __('Icon Set'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'star_fontawesome' => __('Default'),
                    'star_unicode' => 'Unicode',
                ],
                'default' => 'star_fontawesome',
                'render_type' => 'template',
                'prefix_class' => 'elementor--star-style-',
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'unmarked_star_style',
            [
                'label' => __('Unmarked Style'),
                'type' => ControlsManager::CHOOSE,
                'options' => [
                    'solid' => [
                        'title' => __('Solid'),
                        'icon' => 'ceicon-star',
                    ],
                    'outline' => [
                        'title' => __('Outline'),
                        'icon' => 'ceicon-star-o',
                    ],
                ],
                'default' => 'solid',
            ]
        );

        $this->addControl(
            'title',
            [
                'label' => __('Title'),
                'type' => ControlsManager::TEXT,
                'separator' => 'before',
                'dynamic' => [
                    'active' => true,
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
                'prefix_class' => 'elementor-star-rating%s--align-',
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_title_style',
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
                    'value' => SchemeColor::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-star-rating__title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .elementor-star-rating__title',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'title_shadow',
                'selector' => '{{WRAPPER}} .elementor-star-rating__title',
            ]
        );

        $this->addResponsiveControl(
            'title_gap',
            [
                'label' => __('Gap'),
                'type' => ControlsManager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}:not(.elementor-star-rating--align-justify) .elementor-star-rating__title' => 'margin-inline-end: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_stars_style',
            [
                'label' => __('Stars'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addResponsiveControl(
            'icon_size',
            [
                'label' => __('Size'),
                'type' => ControlsManager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .elementor-star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            'icon_space',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-star-rating i:not(:last-of-type)' => 'margin-inline-end: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'stars_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-star-rating i:before' => 'color: {{VALUE}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'stars_unmarked_color',
            [
                'label' => __('Unmarked Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-star-rating i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsSection();
    }

    /**
     * @since 2.3.0
     */
    protected function getRating()
    {
        $settings = $this->getSettingsForDisplay();
        $rating_scale = (int) $settings['rating_scale'];
        $rating = (float) $settings['rating'] > $rating_scale ? $rating_scale : $settings['rating'];

        return [$rating, $rating_scale];
    }

    /**
     * Print the actual stars and calculate their filling.
     *
     * Rating type is float to allow stars-count to be a fraction.
     * Floored-rating type is int, to represent the rounded-down stars count.
     * In the `for` loop, the index type is float to allow comparing with the rating value.
     *
     * @since 2.3.0
     */
    protected function renderStars($icon)
    {
        $rating_data = $this->getRating();
        $rating = (float) $rating_data[0];
        $floored_rating = floor($rating);
        $stars_html = '';

        for ($stars = 1.0; $stars <= $rating_data[1]; ++$stars) {
            if ($stars <= $floored_rating) {
                $stars_html .= '<i class="elementor-star-full" aria-hidden="true">' . $icon . '</i>';
            } elseif ($floored_rating + 1 === $stars && $rating !== $floored_rating) {
                $stars_html .= '<i class="elementor-star-' . ($rating - $floored_rating) * 10 . '" aria-hidden="true">' . $icon . '</i>';
            } else {
                $stars_html .= '<i class="elementor-star-empty" aria-hidden="true">' . $icon . '</i>';
            }
        }

        return $stars_html;
    }

    /**
     * @since 2.3.0
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();
        $rating_data = $this->getRating();
        $textual_rating = "$rating_data[0]/$rating_data[1] " . __('Stars');

        if ('star_fontawesome' === $settings['star_style']) {
            $icon = 'outline' === $settings['unmarked_star_style'] ? '&#xF006;' : '&#xF005;';
        } elseif ('star_unicode' === $settings['star_style']) {
            $icon = 'outline' === $settings['unmarked_star_style'] ? '&#9734;' : '&#9733;';
        }

        $this->addRenderAttribute('icon_wrapper', [
            'class' => 'elementor-star-rating',
            'title' => $textual_rating,
            'role' => 'img',
            'aria-label' => $textual_rating,
        ]); ?>
        <div class="elementor-star-rating__wrapper">
        <?php if ('' !== $settings['title']) { ?>
            <div class="elementor-star-rating__title"><?php echo $settings['title']; ?></div>
        <?php } ?>
            <div <?php $this->printRenderAttributeString('icon_wrapper'); ?>><?php echo $this->renderStars($icon); ?></div>
        </div>
        <?php
    }

    /**
     * @since 2.9.0
     */
    protected function contentTemplate()
    {
        ?>
        <#
        var getRating = function() {
            var ratingScale = parseInt( settings.rating_scale, 10 ),
                rating = settings.rating > ratingScale ? ratingScale : settings.rating;

            return [ rating, ratingScale ];
        },
        ratingData = getRating(),
        rating = ratingData[0],
        textualRating = ratingData[0] + '/' + ratingData[1],
        renderStars = function( icon ) {
            var starsHtml = '',
                flooredRating = Math.floor( rating );

            for ( var stars = 1; stars <= ratingData[1]; ++stars ) {
                if ( stars <= flooredRating  ) {
                    starsHtml += '<i class="elementor-star-full" aria-hidden="true">' + icon + '</i>';
                } else if ( flooredRating + 1 === stars && rating !== flooredRating ) {
                    starsHtml += '<i class="elementor-star-' + ( rating - flooredRating ).toFixed( 1 ) * 10 + '" aria-hidden="true">' + icon + '</i>';
                } else {
                    starsHtml += '<i class="elementor-star-empty" aria-hidden="true">' + icon + '</i>';
                }
            }

            return starsHtml;
        };

        if ( 'star_fontawesome' === settings.star_style ) {
            var icon = 'outline' === settings.unmarked_star_style ? '&#xF006;' : '&#xF005;';
        } else if ( 'star_unicode' === settings.star_style ) {
            var icon = 'outline' === settings.unmarked_star_style ? '&#9734;' : '&#9733;';
        } #>
        <div class="elementor-star-rating__wrapper">
        <# if ( settings.title ) { #>
            <div class="elementor-star-rating__title">{{ settings.title }}</div>
        <# } #>
            <div class="elementor-star-rating" title="{{ textualRating }}" role="img" aria-label="{{ textualRating }}">{{{ renderStars( icon ) }}}</div>
        </div>
        <?php
    }
}
