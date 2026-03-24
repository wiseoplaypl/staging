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

class ModulesXPremiumXWidgetsXCountdown extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/87-widgets/premium-widgets/356-countdown-widget';

    const DEFAULT_LABELS = [
        'label_months' => 'Months',
        'label_weeks' => 'Weeks',
        'label_days' => 'Days',
        'label_hours' => 'Hours',
        'label_minutes' => 'Minutes',
        'label_seconds' => 'Seconds',
    ];

    public function getName()
    {
        return 'countdown';
    }

    public function getTitle()
    {
        return __('Countdown');
    }

    public function getIcon()
    {
        return 'eicon-countdown';
    }

    public function getCategories()
    {
        return ['premium'];
    }

    public function getKeywords()
    {
        return ['countdown', 'number', 'timer', 'time', 'date'];
    }

    public function getStyleDepends()
    {
        return ['widget-countdown'];
    }

    protected function isDynamicContent()
    {
        return false;
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_countdown',
            [
                'label' => __('Countdown'),
            ]
        );

        $this->addControl(
            'due_date',
            [
                'label' => __('Due Date'),
                'type' => ControlsManager::DATE_TIME,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => date('Y-m-d H:i', strtotime('+1 month')),
                'save_default' => true,
                'description' => sprintf(__('Date set according to your timezone: %s.'), Utils::getTimezoneString()),
            ]
        );

        $this->addControl(
            'label_display',
            [
                'label' => __('View'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'block' => __('Block'),
                    'inline' => __('Inline'),
                ],
                'default' => 'block',
                'prefix_class' => 'elementor-countdown--label-',
            ]
        );

        $this->addResponsiveControl(
            'inline_align',
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
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-countdown-wrapper' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    'label_display' => 'inline',
                ],
            ]
        );

        $this->addControl(
            'show_days',
            [
                'label' => __('Days'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
            ]
        );

        $this->addControl(
            'show_hours',
            [
                'label' => __('Hours'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
            ]
        );

        $this->addControl(
            'show_minutes',
            [
                'label' => __('Minutes'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
            ]
        );

        $this->addControl(
            'show_seconds',
            [
                'label' => __('Seconds'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
            ]
        );

        $this->addControl(
            'show_labels',
            [
                'label' => __('Label'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'custom_labels',
            [
                'label' => __('Custom Label'),
                'type' => ControlsManager::SWITCHER,
                'condition' => [
                    'show_labels!' => '',
                ],
            ]
        );

        $this->addControl(
            'label_days',
            [
                'label' => __('Days'),
                'type' => ControlsManager::TEXT,
                'default' => \Translate::getModuleTranslation('creativeelements', 'Days', ''),
                'condition' => [
                    'show_labels!' => '',
                    'custom_labels!' => '',
                    'show_days' => 'yes',
                ],
            ]
        );

        $this->addControl(
            'label_hours',
            [
                'label' => __('Hours'),
                'type' => ControlsManager::TEXT,
                'default' => \Translate::getModuleTranslation('creativeelements', 'Hours', ''),
                'condition' => [
                    'show_labels!' => '',
                    'custom_labels!' => '',
                    'show_hours' => 'yes',
                ],
            ]
        );

        $this->addControl(
            'label_minutes',
            [
                'label' => __('Minutes'),
                'type' => ControlsManager::TEXT,
                'default' => \Translate::getModuleTranslation('creativeelements', 'Minutes', ''),
                'condition' => [
                    'show_labels!' => '',
                    'custom_labels!' => '',
                    'show_minutes' => 'yes',
                ],
            ]
        );

        $this->addControl(
            'label_seconds',
            [
                'label' => __('Seconds'),
                'type' => ControlsManager::TEXT,
                'default' => \Translate::getModuleTranslation('creativeelements', 'Seconds', ''),
                'condition' => [
                    'show_labels!' => '',
                    'custom_labels!' => '',
                    'show_seconds' => 'yes',
                ],
            ]
        );

        $this->addControl(
            'expire_actions',
            [
                'label' => __('Actions After Expire'),
                'label_block' => true,
                'type' => ControlsManager::SELECT2,
                'multiple' => true,
                'options' => [
                    'redirect' => __('Redirect'),
                    'hide' => __('Hide'),
                    'hide_element' => __('Hide Element'),
                    'message' => __('Show Message'),
                ],
                'separator' => 'before',
                'render_type' => 'none',
            ]
        );

        $this->addControl(
            'message_after_expire',
            [
                'label' => __('Message', 'Shop.Forms.Labels'),
                'type' => ControlsManager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'expire_actions',
                            'operator' => 'contains',
                            'value' => 'message',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'expire_redirect_url',
            [
                'label' => __('Redirect URL'),
                'type' => ControlsManager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'render_type' => 'none',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'expire_actions',
                            'operator' => 'contains',
                            'value' => 'redirect',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'hide_element',
            [
                'label' => __('Hide Element'),
                'type' => ControlsManager::TEXT,
                'placeholder' => __('CSS Selector'),
                'render_type' => 'none',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'expire_actions',
                            'operator' => 'contains',
                            'value' => 'hide_element',
                        ],
                    ],
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_box_style',
            [
                'label' => __('Countdown'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addResponsiveControl(
            'container_width',
            [
                'label' => __('Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'max' => 2000,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-countdown-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'headign_box',
            [
                'label' => __('Box'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addResponsiveControl(
            'box_spacing',
            [
                'label' => __('Space Between'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-countdown-item:not(:first-of-type)' => 'margin-inline-start: calc({{SIZE}}{{UNIT}}/2);',
                    '{{WRAPPER}} .elementor-countdown-item:not(:last-of-type)' => 'margin-inline-end: calc({{SIZE}}{{UNIT}}/2);',
                ],
            ]
        );

        $this->addControl(
            'box_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-countdown-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlBorder::getType(),
            [
                'name' => 'box_border',
                'label' => __('Border'),
                'selector' => '{{WRAPPER}} .elementor-countdown-item',
            ]
        );

        $this->addControl(
            'box_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-countdown-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'box_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-countdown-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_content_style',
            [
                'label' => __('Content'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'heading_digits',
            [
                'label' => __('Digits'),
                'type' => ControlsManager::HEADING,
            ]
        );

        $this->addControl(
            'digits_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-countdown-digits' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'digits_typography',
                'selector' => '{{WRAPPER}} .elementor-countdown-digits',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
            ]
        );

        $this->addGroupControl(
            GroupControlTextStroke::getType(),
            [
                'name' => 'text_stroke',
                'selector' => '{{WRAPPER}} .elementor-countdown-digits',
            ]
        );

        $this->addControl(
            'heading_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'label_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-countdown-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .elementor-countdown-label',
                'scheme' => SchemeTypography::TYPOGRAPHY_2,
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_expire_message_style',
            [
                'label' => __('Message', 'Shop.Forms.Labels'),
                'tab' => ControlsManager::TAB_STYLE,
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'expire_actions',
                            'operator' => 'contains',
                            'value' => 'message',
                        ],
                    ],
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
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-countdown-expire--message' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-countdown-expire--message' => 'color: {{VALUE}};',
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
                'name' => 'typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} .elementor-countdown-expire--message',
            ]
        );

        $this->addResponsiveControl(
            'message_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-countdown-expire--message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();
    }

    protected function getStrftime(&$settings)
    {
        $string = '';
        if ($settings['show_days']) {
            $string .= $this->renderCountdownItem($settings, 'label_days', 'elementor-countdown-days');
        }
        if ($settings['show_hours']) {
            $string .= $this->renderCountdownItem($settings, 'label_hours', 'elementor-countdown-hours');
        }
        if ($settings['show_minutes']) {
            $string .= $this->renderCountdownItem($settings, 'label_minutes', 'elementor-countdown-minutes');
        }
        if ($settings['show_seconds']) {
            $string .= $this->renderCountdownItem($settings, 'label_seconds', 'elementor-countdown-seconds');
        }

        return $string;
    }

    protected function renderCountdownItem(&$settings, $label, $part_class)
    {
        $string = '<div class="elementor-countdown-item">' .
            '<span class="elementor-countdown-digits ' . $part_class . '"></span>';
        if ($settings['show_labels']) {
            $label = $settings['custom_labels'] ? $settings[$label] : __(self::DEFAULT_LABELS[$label]);
            $string .= ' <span class="elementor-countdown-label">' . $label . '</span>';
        } else {
            $string .= ' <span class="screen-reader-text">' . __(self::DEFAULT_LABELS[$label]) . '</span>';
        }
        $string .= '</div>';

        return $string;
    }

    protected function getActions(&$settings)
    {
        if (!$settings['expire_actions']) {
            return false;
        }
        $actions = [];

        foreach ($settings['expire_actions'] as &$expire_action) {
            $action = ['type' => $expire_action];

            if ('redirect' === $expire_action) {
                if (empty($settings['expire_redirect_url']['url'])) {
                    continue;
                }
                $action['redirect_url'] = $settings['expire_redirect_url']['url'];
                $action['redirect_is_external'] = $settings['expire_redirect_url']['is_external'];
            } elseif ('hide_element' === $expire_action) {
                $action['hide_element'] = $settings['hide_element'];
            }
            $actions[] = $action;
        }

        return $actions;
    }

    protected function render()
    {
        $settings = $this->getSettingsForDisplay();
        ?>
        <div class="elementor-countdown-wrapper" role="timer" aria-live="off" data-date="<?php echo strtotime($settings['due_date']); ?>" data-expire-actions='<?php echo json_encode($this->getActions($settings)); ?>'>
            <?php echo $this->getStrftime($settings); ?>
        </div>
        <?php
        if (in_array('message', (array) $settings['expire_actions'])) {
            echo '<div class="elementor-countdown-expire--message">' . $settings['message_after_expire'] . '</div>';
        }
    }

    public function renderPlainContent()
    {
    }
}
