<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks, Elementor
 * @copyright 2019-2025 WebshopWorks.com & Elementor.com
 * @license   One domain support license
 */
namespace CE;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ModulesXThemeXWidgetsXSkipLinks extends WidgetBase
{
    public function getName()
    {
        return 'skip-links';
    }

    public function getTitle()
    {
        return __('Skip Links');
    }

    public function getIcon()
    {
        return 'eicon-download-button';
    }

    public function getCategories()
    {
        return ['theme-elements'];
    }

    public function getKeywords()
    {
        return ['skip', 'link', 'accessibility', 'focus', 'menu', 'hidden'];
    }

    public function getStyleDepends()
    {
        return ['widget-skip-links'];
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_skip_links',
            [
                'label' => __('Skip Links'),
            ]
        );

        $repeater = new Repeater();

        $repeater->addControl(
            'selected_icon',
            [
                'label' => __('Icon'),
                'label_block' => false,
                'type' => ControlsManager::ICONS,
                'skin' => 'inline',
                'recommended' => [
                    'ce-icons' => [
                        'sort-down',
                    ],
                    'fa-regular' => [
                        'square-caret-down',
                        'circle-down',
                        'hand-point-down',
                    ],
                    'fa-solid' => [
                        'square-caret-down',
                        'circle-down',
                        'circle-chevron-down',
                        'circle-arrow-down',
                        'angle-down',
                        'angles-down',
                        'arrow-down',
                        'arrow-down-long',
                        'down-long',
                        'chevron-down',
                        'hand-point-down',
                    ],
                ],
            ]
        );

        $repeater->addControl(
            'text',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->addControl(
            'link',
            [
                'label' => __('Link'),
                'type' => ControlsManager::URL,
                'placeholder' => '#anchor-id',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->addControl(
            'links',
            [
                'type' => ControlsManager::REPEATER,
                'fields' => $repeater->getControls(),
                'default' => [
                    [
                        'text' => __('Skip to Content'),
                        'link' => [
                            'url' => '#notifications',
                        ],
                    ],
                    [
                        '__dynamic__' => [
                            'link' => Plugin::$instance->dynamic_tags->tagDataToTagText(null, 'toggle', ['target' => 'cart']),
                        ],
                        'text' => __('Skip to Cart'),
                    ],
                    [
                        'text' => __('Skip to Footer'),
                        'link' => [
                            'url' => '#footer',
                        ],
                    ],
                ],
                'title_field' => '{{{ elementor.helpers.renderIcon( this, selected_icon, {}, "i", "panel" ) }}} {{{ text }}}',
            ]
        );

        $this->addControl(
            'size',
            [
                'label' => __('Size'),
                'type' => ControlsManager::CHOOSE,
                'toggle' => false,
                'options' => WidgetButton::getButtonSizes(),
                'default' => 'sm',
                'style_transfer' => true,
            ]
        );

        $this->addControl(
            'tabindex',
            [
                'label' => __('Tabindex'),
                'type' => ControlsManager::NUMBER,
                'min' => 0,
                'placeholder' => 0,
                'default' => 1000,
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_link',
            [
                'label' => __('Link'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} a',
            ]
        );

        $this->addControl(
            'color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} a:not(#e), {{WRAPPER}} a:not(#e):hover, {{WRAPPER}} a:not(#e):focus' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBoxShadow::getType(),
            [
                'name' => 'box_shadow',
                'fields_options' => [
                    'box_shadow_type' => [
                        'default' => 'yes',
                    ],
                ],
                'selector' => '{{WRAPPER}} a',
            ]
        );

        $this->addControl(
            'icon_spacing',
            [
                'label' => __('Icon Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 5,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button-icon' => 'margin-inline-end: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBorder::getType(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} a',
            ]
        );

        $this->addResponsiveControl(
            'border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();
    }

    protected function getHtmlWrapperClass()
    {
        return parent::getHtmlWrapperClass() . ' elementor-widget-button';
    }

    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        foreach ($settings['links'] as $index => $item) {
            $link_key = 'link_' . $index;
            $this->addRenderAttribute($link_key, 'class', 'ce-skip-link elementor-button elementor-size-' . $settings['size']);
            empty($item['link']['url']) && $item['link']['url'] = '#';
            $this->addLinkAttributes($link_key, $item['link']);
            ?>
            <a <?php echo $this->getRenderAttributeString($link_key); ?> tabindex="<?php echo (int) $settings['tabindex']; ?>">
            <?php if (!empty($item['selected_icon']['value'])) { ?>
                <span class="elementor-button-icon" aria-hidden="true"><?php IconsManager::renderIcon($item['selected_icon']); ?></span>
            <?php } ?>
                <span class="elementor-button-text"><?php echo $item['text']; ?></span>
            </a>
            <?php
        }
    }

    protected function contentTemplate()
    {
        ?>
        <# const activeItemIndex = view.model.get('editSettings').get('activeItemIndex') || 1;
        _.each( settings.links, function( item, index ) {
            var icon, linkKey = 'link_' + index;
            view.addRenderAttribute( linkKey, 'class', 'ce-skip-link elementor-button elementor-size-' + settings.size );
            activeItemIndex === index + 1 && view.addRenderAttribute( linkKey, 'class', 'elementor-active' );
            view.addRenderAttribute( linkKey, 'href', item.link.url || '#' );
            #>
            <a {{{ view.getRenderAttributeString( linkKey ) }}} tabindex="{{ settings.tabindex }}">
            <# if ( icon = elementor.helpers.getBcIcon( view, item, 'icon' ) ) { #>
                <span class="elementor-button-icon" aria-hidden="true">{{{ icon }}}</span>
            <# } #>
                <span class="elementor-button-text">{{{ item.text }}}</span>
            </a>
        <# } ); #>
        <?php
    }
}
