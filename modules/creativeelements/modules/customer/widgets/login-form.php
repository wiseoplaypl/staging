<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks, Elementor
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 */
namespace CE;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CE\ModulesXPremiumXModule as Premium;

class ModulesXCustomerXWidgetsXLoginForm extends WidgetBase
{
    public function getName()
    {
        return 'login-form';
    }

    public function getTitle()
    {
        return __('Login Form');
    }

    public function getIcon()
    {
        return 'eicon-lock';
    }

    public function getCategories()
    {
        return ['customer-elements'];
    }

    public function getKeywords()
    {
        return ['login', 'sign in', 'customer', 'user', 'form'];
    }

    public function getStyleDepends()
    {
        return ['widget-form'];
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_fields_content',
            [
                'label' => __('Form Fields'),
            ]
        );

        $this->addControl(
            'show_labels',
            [
                'label' => __('Label'),
                'type' => ControlsManager::SWITCHER,
                'default' => 'yes',
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
            ]
        );

        $this->addControl(
            'input_size',
            [
                'label' => __('Input Size'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'xs' => __('Extra Small'),
                    'sm' => __('Small'),
                    'md' => __('Medium'),
                    'lg' => __('Large'),
                    'xl' => __('Extra Large'),
                ],
                'default' => 'sm',
            ]
        );

        Premium::addCaptchaPromoControls($this);

        $this->addControl(
            'heading_email',
            [
                'label' => __('Email', 'Shop.Forms.Labels'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'email_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'placeholder' => Helper::$translator->trans('Email', [], 'Shop.Forms.Labels'),
                'condition' => [
                    'show_labels!' => '',
                ],
            ]
        );

        $this->addControl(
            'email_placeholder',
            [
                'label' => __('Placeholder'),
                'type' => ControlsManager::TEXT,
            ]
        );

        $this->addControl(
            'heading_password',
            [
                'label' => __('Password', 'Shop.Forms.Labels'),
                'type' => ControlsManager::HEADING,
            ]
        );

        $this->addControl(
            'password_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'placeholder' => Helper::$translator->trans('Password', [], 'Shop.Forms.Labels'),
                'condition' => [
                    'show_labels!' => '',
                ],
            ]
        );

        $this->addControl(
            'password_placeholder',
            [
                'label' => __('Placeholder'),
                'type' => ControlsManager::TEXT,
            ]
        );

        $this->startControlsTabs('tabs_password', [
            'condition' => $show_password_icon = [
                'show_password_icon[value]!' => '',
            ],
        ]);

        $this->startControlsTab(
            'tab_password_show',
            [
                'label' => __('Show'),
            ]
        );

        $this->addControl(
            'show_password_icon',
            [
                'label' => __('Icon'),
                'type' => ControlsManager::ICONS,
                'label_block' => false,
                'skin' => 'inline',
                'recommended' => [
                    'fa-solid' => [
                        'eye',
                    ],
                    'fa-regular' => [
                        'eye',
                    ],
                ],
                'default' => [
                    'value' => 'fas fa-eye',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'show_password_icon[value]!' => ' ',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_password_hide',
            [
                'label' => __('Hide'),
            ]
        );

        $this->addControl(
            'hide_password_icon',
            [
                'label' => __('Icon'),
                'type' => ControlsManager::ICONS,
                'label_block' => false,
                'skin' => 'inline',
                'recommended' => [
                    'fa-solid' => [
                        'eye-slash',
                    ],
                    'fa-regular' => [
                        'eye-slash',
                    ],
                ],
                'default' => [
                    'value' => 'fas fa-eye-slash',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->endControlsSection();

        $this->startControlsSection(
            'section_button_content',
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
                ],
                'prefix_class' => 'elementor-button-',
                'style_transfer' => true,
            ]
        );

        $this->addControl(
            'button_text',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
                'placeholder' => Helper::$translator->trans('Sign in', [], 'Shop.Theme.Actions'),
            ]
        );

        $this->addControl(
            'button_size',
            [
                'label' => __('Size'),
                'type' => ControlsManager::CHOOSE,
                'toggle' => false,
                'options' => WidgetButton::getButtonSizes(),
                'default' => 'sm',
                'style_transfer' => true,
            ]
        );

        $this->addResponsiveControl(
            'align',
            [
                'label' => __('Alignment'),
                'type' => ControlsManager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => __('Left'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'end' => [
                        'title' => __('Right'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'stretch' => [
                        'title' => __('Justified'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'prefix_class' => 'elementor%s-button-align-',
                'default' => '',
            ]
        );

        $this->addControl(
            'button_icon',
            [
                'label' => __('Icon'),
                'label_block' => false,
                'type' => ControlsManager::ICONS,
                'skin' => 'inline',
                'recommended' => [
                    'ce-icons' => [
                        'caret-right',
                        'angle-right',
                        'chevron-right',
                        'arrow-right',
                        'long-arrow-right',
                    ],
                    'fa-solid' => [
                        'right-to-bracket',
                        'arrow-right-to-bracket',
                        'right-long',
                        'arrow-right-long',
                        'arrow-right',
                        'chevron-right',
                        'caret-right',
                        'angle-right',
                        'angles-right',
                        'square-caret-right',
                        'circle-chevron-right',
                        'circle-arrow-right',
                        'circle-right',
                    ],
                    'fa-regular' => [
                        'square-caret-right',
                        'circle-right',
                    ],
                ],
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
                    'button_icon[value]!' => '',
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
                    'button_icon[value]!' => '',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_login_content',
            [
                'label' => __('Additional Options'),
            ]
        );

        $this->addControl(
            'redirect_after_login',
            [
                'label' => __('Redirect After Login'),
                'type' => ControlsManager::SWITCHER,
                'default' => '',
                'label_off' => __('Off'),
                'label_on' => __('On'),
            ]
        );

        $this->addControl(
            'redirect_url',
            [
                'show_label' => false,
                'type' => ControlsManager::URL,
                'options' => false,
                'dynamic' => [
                    'active' => true,
                ],
                'description' => __('Note: Because of security reasons, you can ONLY use your current domain here.'),
                'condition' => [
                    'redirect_after_login!' => '',
                ],
            ]
        );

        $this->addControl(
            'show_lost_password',
            [
                'label' => __('Forgot your password?', 'Shop.Theme.Customeraccount'),
                'type' => ControlsManager::SWITCHER,
                'default' => 'yes',
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
            ]
        );

        $this->addResponsiveControl(
            'link_align',
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
                    '{{WRAPPER}} .elementor-field-type-link' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'show_lost_password!' => '',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style',
            [
                'label' => __('Form'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'row_gap',
            [
                'label' => __('Rows Gap'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 60,
                    ],
                    'em' => [
                        'max' => 6,
                    ],
                ],
                'default' => [
                    'size' => '10',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-form-fields-wrapper' => 'row-gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementor-form ~ .elementor-field-type-link' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'heading_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::HEADING,
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .elementor-form label',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
            ]
        );

        $this->addControl(
            'label_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-form label' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_3,
                ],
            ]
        );

        $this->addControl(
            'label_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 60,
                    ],
                    'em' => [
                        'max' => 6,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-group > label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_field_style',
            [
                'label' => __('Field'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'field_typography',
                'selector' => '{{WRAPPER}} .elementor-field',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
            ]
        );

        $this->addControl(
            'field_text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-field' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementor-field-group' => '--ce-field-color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_3,
                ],
            ]
        );

        $this->addControl(
            'field_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-textual' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'field_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-textual' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'field_border_width',
            [
                'label' => __('Border Width'),
                'type' => ControlsManager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-textual' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'field_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-textual' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_password_style',
            [
                'label' => __('Password'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => $show_password_icon,
            ]
        );

        $this->addControl(
            'password_icon_color',
            [
                'label' => __('Icon Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-password-wrapper a:not(#e)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'password_icon_hover',
            [
                'label' => __('Icon Hover'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-password-wrapper a:not(#e):hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'password_icon_size',
            [
                'label' => __('Icon Size'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-password-wrapper a' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'password_icon_indent',
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
                    '{{WRAPPER}} .elementor-password-wrapper a' => 'inset-inline-end: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_link_style',
            [
                'label' => __('Link'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'show_lost_password!' => '',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'link_typography',
                'selector' => '{{WRAPPER}} .elementor-field-type-link a',
            ]
        );

        $this->addControl(
            'link_color',
            [
                'label' => __('Link Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-type-link a:not(#e)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'link_hover_color',
            [
                'label' => __('Link Hover Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-type-link a:not(#e):hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_button_style',
            [
                'label' => __('Button'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'button_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .elementor-button',
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'button_text_shadow',
                'selector' => '{{WRAPPER}} .elementor-button',
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
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-button' => 'color: {{VALUE}};',
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
                    'value' => SchemeColor::COLOR_4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'button_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button' => 'border-color: {{VALUE}};',
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
            'button_hover_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'button_background_hover_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'button_hover_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
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
                    '{{WRAPPER}} .elementor-button' => 'transition-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'button_hover_animation',
            [
                'label' => __('Animation'),
                'type' => ControlsManager::HOVER_ANIMATION,
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'button_border_width',
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
                    '{{WRAPPER}} .elementor-button' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'button_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'button_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_messages_style',
            [
                'label' => __('Messages'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'message_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-alert' => 'margin: 0 0 {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementor-message' => 'margin: {{SIZE}}{{UNIT}} 0 0;',
                ],
            ]
        );

        $this->addControl(
            'heading_alert',
            [
                'label' => __('Alert'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'alert_typography',
                'selector' => '{{WRAPPER}} .elementor-alert-description',
            ]
        );

        $this->addControl(
            'alert_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-alert-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'alert_background',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-alert' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'alert_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-alert' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'alert_border_width',
            [
                'label' => __('Left Border Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-alert' => 'border-left-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'heading_error',
            [
                'label' => __('Error'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'message_typography',
                'selector' => '{{WRAPPER}} .elementor-message',
            ]
        );

        $this->addControl(
            'error_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-message-danger' => 'color: {{COLOR}};',
                ],
            ]
        );

        $this->addControl(
            'error_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-error .elementor-field-textual' => 'border-color: {{COLOR}};',
                ],
            ]
        );

        $this->endControlsSection();
    }

    protected function render()
    {
        if (_CE_ADMIN_) {
            return; // Use contentTemplate
        }
        $form = $GLOBALS['smarty']->tpl_vars['login_form']->value;
        $vars = \Closure::bind(function () {
            return $this->renderable->getTemplateVariables();
        }, $form, $form)->__invoke();
        $settings = $this->getSettingsForDisplay();
        $redirect_url = $settings['redirect_after_login'] ? $settings['redirect_url']['url'] : '';
        $id = substr($this->getIdInt(), 0, 3);

        $vars['errors'][''] && $this->addRenderAttribute('_wrapper', 'class', 'elementor-widget-alert');

        $this->addRenderAttribute('email_input', [
            'id' => 'email-' . (int) $id,
            'placeholder' => $settings['email_placeholder'],
            'value' => $vars['formFields']['email']['value'],
            'class' => 'elementor-field elementor-field-textual elementor-size-' . $settings['input_size'],
        ]);
        $this->addRenderAttribute('password_input', [
            'id' => 'password-' . (int) $id,
            'placeholder' => $settings['password_placeholder'],
            'class' => 'elementor-field elementor-field-textual elementor-size-' . $settings['input_size'],
        ]);
        $this->addRenderAttribute('button', 'class', 'elementor-button elementor-size-' . $settings['button_size']);
        $settings['button_hover_animation'] && $this->addRenderAttribute('button', 'class', 'elementor-animation-' . $settings['button_hover_animation']);
        ?>
        <form class="ce-login-form elementor-form" method="post" action="<?php escape($vars['action']); ?>">
        <?php foreach ($vars['errors'][''] as $error) { ?>
            <div class="elementor-alert elementor-alert-danger" role="alert">
                <span class="elementor-alert-description"><?php echo $error; ?></span>
            </div>
        <?php } ?>
            <div class="elementor-form-fields-wrapper">
                <input type="hidden" name="back" value="<?php escape($vars['formFields']['back']['value'] ?: $redirect_url); ?>">
                <div class="elementor-field-type-email elementor-field-group elementor-column elementor-col-100<?php $vars['errors']['email'] && print ' elementor-error'; ?>">
                <?php if ($settings['show_labels']) { ?>
                    <label for="email-<?php echo (int) $id; ?>" class="elementor-field-label"><?php echo $settings['email_label'] ?: __('Email', 'Shop.Forms.Labels'); ?></label>
                <?php } ?>
                    <input type="email" name="email" <?php $this->printRenderAttributeString('email_input'); ?> autocomplete="email" required>
                <?php foreach ($vars['errors']['email'] as $error) { ?>
                    <div class="elementor-message elementor-message-danger" role="alert"><?php echo $error; ?></div>
                <?php } ?>
                </div>
                <div class="elementor-field-type-password elementor-field-group elementor-column elementor-col-100<?php $vars['errors']['password'] && print ' elementor-error'; ?>">
                <?php if ($settings['show_labels']) { ?>
                    <label for="password-<?php echo (int) $id; ?>" class="elementor-field-label"><?php echo $settings['password_label'] ?: __('Password', 'Shop.Forms.Labels'); ?></label>
                <?php } ?>
                    <div class="elementor-password-wrapper">
                        <input type="password" name="password" <?php $this->printRenderAttributeString('password_input'); ?> autocomplete="current-password" required>
                    <?php if ($settings['show_password_icon']['value']) { ?>
                        <a href="#ce-action=toggle&target=password" class="elementor-icon" title="<?php esc_attr_e('Show', 'Shop.Theme.Actions'); ?>" role="button" aria-controls="password-<?php echo (int) $id; ?>" aria-label="<?php esc_attr_e('Show', 'Shop.Theme.Actions'); ?>">
                            <?php IconsManager::renderIcon($settings['show_password_icon'], ['aria-hidden' => 'true']); ?>
                        </a>
                        <a href="#ce-action=toggle&target=password" class="elementor-icon" title="<?php esc_attr_e('Hide', 'Shop.Theme.Actions'); ?>" role="button" aria-controls="password-<?php echo (int) $id; ?>" aria-label="<?php esc_attr_e('Hide', 'Shop.Theme.Actions'); ?>">
                            <?php IconsManager::renderIcon($settings['hide_password_icon'], ['aria-hidden' => 'true']); ?>
                        </a>
                    <?php } ?>
                    </div>
                <?php foreach ($vars['errors']['password'] as $error) { ?>
                    <div class="elementor-message elementor-message-danger" role="alert"><?php echo $error; ?></div>
                <?php } ?>
                </div>
            <?php if ($settings['show_lost_password']) { ?>
                <div class="elementor-field-type-link elementor-column elementor-col-100">
                    <a class="elementor-lost-password" href="<?php escape($vars['urls']['pages']['password']); ?>" rel="nofollow">
                        <?php _e('Forgot your password?', 'Shop.Theme.Customeraccount'); ?>
                    </a>
                </div>
            <?php } ?>
                <div class="elementor-field-type-submit elementor-field-group elementor-column elementor-col-100">
                    <button type="submit" name="submitLogin" value="1" <?php $this->printRenderAttributeString('button'); ?>>
                        <span class="elementor-button-content-wrapper">
                        <?php if (!empty($settings['button_icon']['value'])) { ?>
                            <span class="elementor-button-icon elementor-align-icon-<?php escape($settings['icon_align']); ?>" aria-hidden="true"><?php IconsManager::renderIcon($settings['button_icon']); ?></span>
                        <?php } ?>
                            <span class="elementor-button-text"><?php echo $settings['button_text'] ?: __('Sign in', 'Shop.Theme.Actions'); ?></span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
        <?php
        echo \Hook::exec('displayCustomerLoginFormAfter');
    }

    protected function contentTemplate()
    {
        $trans = [Helper::$translator, 'trans'];
        $password_page = Helper::$link->getPageLink('password', true);
        $register_page = Helper::$link->getPageLink(...((int) _PS_VERSION_ < 8 ? ['authentication', true, null, 'create_account=1'] : ['registration', true]));
        ?>
        <# var inputClasses = 'elementor-field elementor-field-textual elementor-size-' + settings.input_size; #>
        <form class="ce-login-form elementor-form">
            <div class="elementor-alert elementor-alert-danger" role="alert" hidden>
                <span class="elementor-alert-description"><?php echo $trans('Authentication failed.', [], 'Shop.Notifications.Error'); ?></span>
            </div>
            <div class="elementor-form-fields-wrapper">
                <div class="elementor-field-type-email elementor-field-group elementor-column elementor-col-100">
                <# if ( settings.show_labels ) { #>
                    <label class="elementor-field-label" for="email-{{ id }}">{{{ settings.email_label || <?php echo json_encode($trans('Email', [], 'Shop.Forms.Labels')); ?> }}}</label>
                <# } #>
                    <input type="email" id="email-{{ id }}" placeholder="{{ settings.email_placeholder }}" class="{{ inputClasses }}" required>
                    <div class="elementor-message elementor-message-danger" role="alert" hidden><?php echo $trans('Invalid format.', [], 'Shop.Forms.Error'); ?></div>
                </div>
                <div class="elementor-field-type-password elementor-field-group elementor-column elementor-col-100">
                <# if ( settings.show_labels ) { #>
                    <label class="elementor-field-label" for="password-{{ id }}">{{{ settings.password_label || <?php echo json_encode($trans('Password', [], 'Shop.Forms.Labels')); ?> }}}</label>
                <# } #>
                    <div class="elementor-password-wrapper">
                        <input type="password" id="password-{{ id }}" placeholder="{{ settings.password_placeholder }}" class="{{ inputClasses }}" autocomplete="new-password" required>
                    <# if ( settings.show_password_icon.value ) { #>
                        <a href="#ce-action=toggle&target=password" class="elementor-icon" title="<?php escape($trans('Show', [], 'Shop.Theme.Actions')); ?>" role="button" aria-controls="password-{{ id }}" aria-label="<?php escape($trans('Show', [], 'Shop.Theme.Actions')); ?>">
                            {{{ elementor.helpers.renderIcon( view, settings.show_password_icon, { 'aria-hidden': 'true' } ) }}}
                        </a>
                        <a href="#ce-action=toggle&target=password" class="elementor-icon" title="<?php escape($trans('Hide', [], 'Shop.Theme.Actions')); ?>" role="button" aria-controls="password-{{ id }}" aria-label="<?php escape($trans('Hide', [], 'Shop.Theme.Actions')); ?>">
                            {{{ elementor.helpers.renderIcon( view, settings.hide_password_icon, { 'aria-hidden': 'true' } ) }}}
                        </a>
                    <# } #>
                    </div>
                </div>
            <# if ( settings.show_lost_password ) { #>
                <div class="elementor-field-type-link elementor-column elementor-col-100">
                    <a class="elementor-lost-password" href="<?php escape($password_page); ?>" rel="nofollow">
                        <?php echo $trans('Forgot your password?', [], 'Shop.Theme.Customeraccount'); ?>
                    </a>
                </div>
            <# } #>
                <div class="elementor-field-group elementor-column elementor-field-type-submit elementor-col-100">
                    <button type="submit" class="elementor-button elementor-size-{{ settings.button_size }} elementor-animation-{{ settings.button_hover_animation }}">
                        <span class="elementor-button-content-wrapper">
                        <# if ( settings.button_icon.value ) { #>
                            <span class="elementor-button-icon elementor-align-icon-{{ settings.icon_align }}" aria-hidden="true">{{{ elementor.helpers.renderIcon(view, settings.button_icon) }}}</span>
                        <# } #>
                            <span class="elementor-button-text">{{{ settings.button_text || <?php echo json_encode($trans('Sign in', [], 'Shop.Theme.Actions')); ?> }}}</span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
        <?php
    }

    public function renderPlainContent()
    {
    }
}
