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

class ModulesXCustomerXWidgetsXPasswordForm extends WidgetBase
{
    public function getName()
    {
        return 'password-form';
    }

    public function getTitle()
    {
        return __('Password Recovery');
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
        return ['reset', 'forgot', 'new', 'customer', 'user', 'form'];
    }

    public function getStyleDepends()
    {
        return ['widget-form'];
    }

    protected function registerControls()
    {
        $trans = [Helper::$translator, 'trans'];

        $this->startControlsSection(
            'section_fields_content',
            [
                'label' => __('Form'),
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

        $this->endControlsSection();

        $this->startControlsSection(
            'section_reset_content',
            [
                'label' => __('Send reset link', 'Shop.Theme.Actions'),
            ]
        );

        $this->addControl(
            'reset_description',
            [
                'label' => __('Description'),
                'type' => ControlsManager::TEXTAREA,
                'rows' => 3,
                'placeholder' => $trans('Please enter the email address you used to register. You will receive a temporary link to reset your password.', [], 'Shop.Theme.Customeraccount'),
                'dynamic' => [
                    'active' => true,
                ],
                'classes' => 'elementor-control-type-heading',
            ]
        );

        $this->addControl(
            'heading_email',
            [
                'label' => __('Email', 'Shop.Forms.Labels'),
                'type' => ControlsManager::HEADING,
            ]
        );

        $this->addControl(
            'email_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Email address', [], 'Shop.Forms.Labels'),
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
            'heading_button_reset',
            [
                'label' => __('Button'),
                'type' => ControlsManager::HEADING,
            ]
        );

        $this->addControl(
            'button_reset',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Send reset link', [], 'Shop.Theme.Actions'),
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_change_content',
            [
                'label' => __('Change Password', 'Shop.Theme.Actions'),
            ]
        );

        $this->addControl(
            'change_description',
            [
                'label' => __('Description'),
                'type' => ControlsManager::TEXTAREA,
                'rows' => 3,
                'placeholder' => $trans('Email address: %email%', ['%email%' => $trans('your@email.com', [], 'Shop.Forms.Help')], 'Shop.Theme.Customeraccount'),
                'dynamic' => [
                    'active' => true,
                ],
                'classes' => 'elementor-control-type-heading',
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
                'placeholder' => $trans('New password', [], 'Shop.Forms.Labels'),
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

        $this->addControl(
            'heading_confirmation',
            [
                'label' => __('Confirmation', 'Shop.Forms.Labels'),
                'type' => ControlsManager::HEADING,
            ]
        );

        $this->addControl(
            'confirmation_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Confirmation', [], 'Shop.Forms.Labels'),
                'condition' => [
                    'show_labels!' => '',
                ],
            ]
        );

        $this->addControl(
            'confirmation_placeholder',
            [
                'label' => __('Placeholder'),
                'type' => ControlsManager::TEXT,
            ]
        );

        $this->addControl(
            'heading_button_change',
            [
                'label' => __('Button'),
                'type' => ControlsManager::HEADING,
            ]
        );

        $this->addControl(
            'button_change',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Change Password', [], 'Shop.Theme.Actions'),
            ]
        );

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

        _CE_ADMIN_ && $this->addControl(
            'reset_delay',
            [
                'label' => __('Password reset delay', 'Admin.Shopparameters.Feature'),
                'type' => ControlsManager::BUTTON,
                'text' => '<i class="eicon-external-link-square"></i>' . __('Configure', 'Admin.Actions'),
                'link' => [
                    'url' => Helper::$link->getAdminLink('AdminCustomerPreferences'),
                    'is_external' => true,
                ],
                'separator' => 'before',
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
                'label' => __('Alert'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'alert_typography',
                'selector' => '{{WRAPPER}} .elementor-alert-description',
            ]
        );

        $this->startControlsTabs('tabs_alert_style');

        $this->startControlsTab(
            'tab_alert_error',
            [
                'label' => __('Error'),
            ]
        );

        $this->addControl(
            'alert_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-alert-danger .elementor-alert-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'alert_background',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-alert-danger' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'alert_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-alert-danger' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_alert_success',
            [
                'label' => __('Success'),
            ]
        );

        $this->addControl(
            'success_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-alert-success .elementor-alert-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'success_background',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-alert-success' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'success_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-alert-success' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'alert_border_width',
            [
                'label' => __('Left Border Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-alert' => 'border-left-width: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'alert_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-alert' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();
    }

    protected function getHtmlWrapperClass()
    {
        return parent::getHtmlWrapperClass() . ' elementor-widget-alert';
    }

    protected function render()
    {
        if (_CE_ADMIN_) {
            return; // Use contentTemplate
        }
        $controller = $GLOBALS['context']->controller;
        $vars = &$GLOBALS['smarty']->tpl_vars;
        $customer_email = isset($vars['customer_email']) ? $vars['customer_email']->value : '';
        $password_policy = !$customer_email || (int) _PS_VERSION_ < 8 ? [] : $vars['configuration']->value['password_policy'];
        $settings = $this->getSettingsForDisplay();
        $id = substr($this->getIdInt(), 0, 3);

        $this->addRenderAttribute('button', 'class', 'elementor-button elementor-size-' . $settings['button_size']);
        $settings['button_hover_animation'] && $this->addRenderAttribute('button', 'class', 'elementor-animation-' . $settings['button_hover_animation']);
        ?>
        <form class="ce-password-recovery elementor-form" method="post" action="<?php escape($vars['urls']->value['pages']['password']); ?>">
        <?php foreach ($controller->success ?: $controller->errors as $alert) { ?>
            <div class="elementor-alert elementor-alert-<?php echo $controller->success ? 'success' : 'danger'; ?>" role="alert">
                <span class="elementor-alert-description"><?php echo $alert; ?></span>
            </div>
        <?php } ?>
            <div class="elementor-form-fields-wrapper<?php $password_policy && print ' field-password-policy'; ?>">
            <?php if (!$customer_email) {
                $this->addRenderAttribute('input', [
                    'id' => 'email-' . (int) $id,
                    'placeholder' => $settings['email_placeholder'],
                    'value' => $controller->success ? '' : \Tools::getValue('email'),
                    'class' => 'elementor-field elementor-field-textual elementor-size-' . $settings['input_size'],
                ]); ?>
                <div class="elementor-field-group"><?php echo $settings['reset_description']
                    ?: __('Please enter the email address you used to register. You will receive a temporary link to reset your password.', 'Shop.Theme.Customeraccount'); ?>
                </div>
                <div class="elementor-field-type-email elementor-field-group elementor-column elementor-col-100">
                <?php if ($settings['show_labels']) { ?>
                    <label for="email-<?php echo (int) $id; ?>" class="elementor-field-label"><?php echo $settings['email_label'] ?: __('Email address', 'Shop.Forms.Labels'); ?></label>
                <?php } ?>
                    <input type="email" name="email" <?php $this->printRenderAttributeString('input'); ?> required>
                </div>
            <?php } else {
                $this->addRenderAttribute('input', [
                    'type' => 'password',
                    'class' => 'elementor-field elementor-field-textual elementor-size-' . $settings['input_size'],
                    'data-validate' => 'isPasswd',
                ]); ?>
                <div class="elementor-field-group">
                    <?php echo $settings['change_description'] ?: __('Email address: %email%', 'Shop.Theme.Customeraccount', ['%email%' => $customer_email]); ?>
                </div>
                <div class="elementor-field-type-password elementor-field-group elementor-column elementor-col-100 js-input-column">
                <?php if ($settings['show_labels']) { ?>
                    <label for="passwd-<?php echo (int) $id; ?>" class="elementor-field-label"><?php echo $settings['password_label'] ?: __('New password', 'Shop.Forms.Labels'); ?></label>
                <?php } ?>
                    <div class="elementor-password-wrapper">
                        <input name="passwd" id="passwd-<?php echo (int) $id; ?>" placeholder="<?php escape($settings['password_placeholder']); ?>" <?php $this->printRenderAttributeString('input')
                            && $password_policy && print ' ' . Utils::renderHtmlAttributes([
                                'data-minlength' => $password_policy['minimum_length'],
                                'data-maxlength' => $password_policy['maximum_length'],
                                'data-minscore' => $password_policy['minimum_score'],
                            ]); ?> required>
                    <?php if ($settings['show_password_icon']['value']) { ?>
                        <a href="#ce-action=toggle&target=password" class="elementor-icon" title="<?php esc_attr_e('Show', 'Shop.Theme.Actions'); ?>" role="button" aria-controls="passwd-<?php echo (int) $id; ?>" aria-label="<?php esc_attr_e('Show', 'Shop.Theme.Actions'); ?>">
                            <?php IconsManager::renderIcon($settings['show_password_icon'], ['aria-hidden' => 'true']); ?>
                        </a>
                        <a href="#ce-action=toggle&target=password" class="elementor-icon" title="<?php esc_attr_e('Hide', 'Shop.Theme.Actions'); ?>" role="button" aria-controls="passwd-<?php echo (int) $id; ?>" aria-label="<?php esc_attr_e('Hide', 'Shop.Theme.Actions'); ?>">
                            <?php IconsManager::renderIcon($settings['hide_password_icon'], ['aria-hidden' => 'true']); ?>
                        </a>
                    <?php } ?>
                    </div>
                </div>
                <div class="elementor-field-type-password elementor-field-group elementor-column elementor-col-100">
                <?php if ($settings['show_labels']) { ?>
                    <label for="conf-<?php echo (int) $id; ?>" class="elementor-field-label"><?php echo $settings['confirmation_label'] ?: __('Confirmation', 'Shop.Forms.Labels'); ?></label>
                <?php } ?>
                    <div class="elementor-password-wrapper">
                        <input name="confirmation" id="conf-<?php echo (int) $id; ?>" placeholder="<?php escape($settings['confirmation_placeholder']); ?>" <?php $this->printRenderAttributeString('input'); ?> required>
                    <?php if ($settings['show_password_icon']['value']) { ?>
                        <a href="#ce-action=toggle&target=password" class="elementor-icon" title="<?php esc_attr_e('Show', 'Shop.Theme.Actions'); ?>" role="button" aria-controls="conf-<?php echo (int) $id; ?>" aria-label="<?php esc_attr_e('Show', 'Shop.Theme.Actions'); ?>">
                            <?php IconsManager::renderIcon($settings['show_password_icon'], ['aria-hidden' => 'true']); ?>
                        </a>
                        <a href="#ce-action=toggle&target=password" class="elementor-icon" title="<?php esc_attr_e('Hide', 'Shop.Theme.Actions'); ?>" role="button" aria-controls="conf-<?php echo (int) $id; ?>" aria-label="<?php esc_attr_e('Hide', 'Shop.Theme.Actions'); ?>">
                            <?php IconsManager::renderIcon($settings['hide_password_icon'], ['aria-hidden' => 'true']); ?>
                        </a>
                    <?php } ?>
                    </div>
                </div>
                <input type="hidden" name="id_customer" value="<?php echo (int) $vars['id_customer']->value; ?>">
                <input type="hidden" name="token" value="<?php escape($vars['customer_token']->value); ?>">
                <input type="hidden" name="reset_token" value="<?php escape($vars['reset_token']->value); ?>">
            <?php } ?>
                <div class="elementor-field-type-submit elementor-field-group elementor-column elementor-col-100">
                    <button type="submit" name="submit" <?php $this->printRenderAttributeString('button'); ?>>
                        <span class="elementor-button-content-wrapper">
                        <?php if (!empty($settings['button_icon']['value'])) { ?>
                            <span class="elementor-button-icon elementor-align-icon-<?php escape($settings['icon_align']); ?>" aria-hidden="true"><?php IconsManager::renderIcon($settings['button_icon']); ?></span>
                        <?php } ?>
                            <span class="elementor-button-text"><?php echo empty($vars['customer_email'])
                                ? ($settings['button_reset'] ?: __('Send reset link', 'Shop.Theme.Actions'))
                                : ($settings['button_change'] ?: __('Change Password', 'Shop.Theme.Actions')); ?>
                            </span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
        <?php
    }

    protected function contentTemplate()
    {
        $trans = [Helper::$translator, 'trans'];
        $email = ['%email%' => $trans('your@email.com', [], 'Shop.Forms.Help')];
        $reset_description = $trans('Please enter the email address you used to register. You will receive a temporary link to reset your password.', [], 'Shop.Theme.Customeraccount');
        ?>
        <#
        var inputClasses = 'elementor-field elementor-field-textual elementor-size-' + settings.input_size,
            show = <?php echo json_encode($trans('Show', [], 'Shop.Theme.Actions')); ?>,
            hide = <?php echo json_encode($trans('Hide', [], 'Shop.Theme.Actions')); ?>;
        #>
        <form class="ce-password-recovery elementor-form">
            <div class="elementor-alert elementor-alert-success" role="alert" hidden>
                <span class="elementor-alert-description">
                    <?php echo $trans('If this email address has been registered in our shop, you will receive a link to reset your password at %email%.', $email, 'Shop.Notifications.Success'); ?>
                </span>
            </div>
            <div class="elementor-alert elementor-alert-danger" role="alert" hidden>
                <span class="elementor-alert-description"><?php echo $trans('An error occurred while sending the email.', [], 'Shop.Notifications.Error'); ?></span>
            </div>
            <div class="elementor-form-fields-wrapper">
                <div class="elementor-field-group">
                    {{{ settings.reset_description || <?php echo json_encode($reset_description); ?> }}}
                </div>
                <div class="elementor-field-type-email elementor-field-group elementor-column elementor-col-100">
                <# if ( settings.show_labels ) { #>
                    <label class="elementor-field-label" for="email-{{ id }}">{{{ settings.email_label || <?php echo json_encode($trans('Email address', [], 'Shop.Forms.Labels')); ?> }}}</label>
                <# } #>
                    <input type="email" id="email-{{ id }}" placeholder="{{ settings.email_placeholder }}" class="{{ inputClasses }}" required>
                </div>
                <div class="elementor-field-group elementor-column elementor-field-type-submit elementor-col-100">
                    <button type="submit" class="elementor-button elementor-size-{{ settings.button_size }} elementor-animation-{{ settings.button_hover_animation }}">
                        <span class="elementor-button-content-wrapper">
                        <# if ( settings.button_icon.value ) { #>
                            <span class="elementor-button-icon elementor-align-icon-{{ settings.icon_align }}" aria-hidden="true">{{{ elementor.helpers.renderIcon(view, settings.button_icon) }}}</span>
                        <# } #>
                            <span class="elementor-button-text">{{{ settings.button_reset || <?php echo json_encode($trans('Send reset link', [], 'Shop.Theme.Actions')); ?> }}}</span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
        <form class="ce-password-recovery elementor-form">
            <div class="elementor-alert elementor-alert-success" role="alert" hidden>
                <span class="elementor-alert-description">
                    <?php echo $trans('Your password has been successfully reset and a confirmation has been sent to your email address: %s', $email, 'Shop.Notifications.Success'); ?>
                </span>
            </div>
            <div class="elementor-alert elementor-alert-danger" role="alert" hidden>
                <span class="elementor-alert-description"><?php echo $trans('The password and its confirmation do not match.', [], 'Shop.Notifications.Error'); ?></span>
            </div>
            <div class="elementor-form-fields-wrapper">
                <div class="elementor-field-group">
                    {{{ settings.change_description || <?php echo json_encode($trans('Email address: %email%', $email, 'Shop.Theme.Customeraccount')); ?> }}}
                </div>
                <div class="elementor-field-type-password elementor-field-group elementor-column elementor-col-100">
                <# if ( settings.show_labels ) { #>
                    <label class="elementor-field-label" for="passwd-{{ id }}">{{{ settings.password_label || <?php echo json_encode($trans('New password', [], 'Shop.Forms.Labels')); ?> }}}</label>
                <# } #>
                    <div class="elementor-password-wrapper">
                        <input type="password" id="passwd-{{ id }}" placeholder="{{ settings.password_placeholder }}" class="{{ inputClasses }}" data-validate="isPasswd" required>
                    <# if ( settings.show_password_icon.value ) { #>
                        <a href="#ce-action=toggle&target=password" class="elementor-icon" title="{{ show }}" role="button" aria-controls="passwd-{{ id }}" aria-label="{{ show }}">
                            {{{ elementor.helpers.renderIcon(view, settings.show_password_icon) }}}
                        </a>
                        <a href="#ce-action=toggle&target=password" class="elementor-icon" title="{{ hide }}" role="button" aria-controls="passwd-{{ id }}" aria-label="{{ hide }}">
                            {{{ elementor.helpers.renderIcon(view, settings.hide_password_icon) }}}
                        </a>
                    <# } #>
                    </div>
                </div>
                <div class="elementor-field-type-password elementor-field-group elementor-column elementor-col-100">
                <# if ( settings.show_labels ) { #>
                    <label class="elementor-field-label" for="conf-{{ id }}">{{{ settings.confirmation_label || <?php echo json_encode($trans('Confirmation', [], 'Shop.Forms.Labels')); ?> }}}</label>
                <# } #>
                    <div class="elementor-password-wrapper">
                        <input type="password" id="conf-{{ id }}" placeholder="{{ settings.confirmation_placeholder }}" class="{{ inputClasses }}" data-validate="isPasswd" required>
                    <# if ( settings.show_password_icon.value ) { #>
                        <a href="#ce-action=toggle&target=password" class="elementor-icon" title="{{ show }}" role="button" aria-controls="conf-{{ id }}" aria-label="{{ show }}">
                            {{{ elementor.helpers.renderIcon( view, settings.show_password_icon, { 'aria-hidden': true } ) }}}
                        </a>
                        <a href="#ce-action=toggle&target=password" class="elementor-icon" title="{{ hide }}" role="button" aria-controls="conf-{{ id }}" aria-label="{{ hide }}">
                            {{{ elementor.helpers.renderIcon( view, settings.hide_password_icon, { 'aria-hidden': true } ) }}}
                        </a>
                    <# } #>
                    </div>
                </div>
                <div class="elementor-field-group elementor-column elementor-field-type-submit elementor-col-100">
                    <button type="submit" class="elementor-button elementor-size-{{ settings.button_size }} elementor-animation-{{ settings.button_hover_animation }}">
                        <span class="elementor-button-content-wrapper">
                        <# if ( settings.button_icon.value ) { #>
                            <span class="elementor-button-icon elementor-align-icon-{{ settings.icon_align }}" aria-hidden="true">{{{ elementor.helpers.renderIcon(view, settings.button_icon) }}}</span>
                        <# } #>
                            <span class="elementor-button-text">{{{ settings.button_change || <?php echo json_encode($trans('Change Password', [], 'Shop.Theme.Actions')); ?> }}}</span>
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
