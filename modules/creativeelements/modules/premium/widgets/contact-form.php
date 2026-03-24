<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 */
namespace CE;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CE\ModulesXPremiumXModule as Premium;

class ModulesXPremiumXWidgetsXContactForm extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/87-widgets/premium-widgets/334-contact-form-widget';

    protected $upload;

    protected $gdpr;

    protected $gdpr_msg;

    protected $gdpr_cfg;

    public function getName()
    {
        return 'contact-form';
    }

    public function getTitle()
    {
        return __('Contact Form');
    }

    public function getIcon()
    {
        return 'eicon-form-horizontal';
    }

    public function getCategories()
    {
        return ['premium', 'contact-elements'];
    }

    public function getKeywords()
    {
        return ['submit', 'send', 'message'];
    }

    public function getStyleDepends()
    {
        return ['widget-form'];
    }

    protected function getContactOptions()
    {
        $opts = [
            '0' => __('Select', 'Shop.Theme.Actions'),
        ];
        foreach (\Contact::getContacts($GLOBALS['language']->id) as $contact) {
            $opts[$contact['id_contact']] = $contact['name'];
        }

        return $opts;
    }

    protected function getToken()
    {
        if (empty($GLOBALS['cookie']->contactFormTokenTTL) || $GLOBALS['cookie']->contactFormTokenTTL < time()) {
            $GLOBALS['cookie']->contactFormToken = call_user_func('md5', uniqid());
            $GLOBALS['cookie']->contactFormTokenTTL = time() + 600;
        }

        return $GLOBALS['cookie']->contactFormToken;
    }

    protected function registerControls()
    {
        $trans = [Helper::$translator, 'trans'];

        $this->startControlsSection(
            'section_form_content',
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
            'mark_required',
            [
                'label' => __('Required Mark'),
                'type' => ControlsManager::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'condition' => $show_labels = [
                    'show_labels!' => '',
                ],
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

        $this->addControl(
            'subject_id',
            [
                'label' => __('Subject', 'Shop.Forms.Labels'),
                'type' => ControlsManager::SELECT,
                'options' => _CE_ADMIN_ ? $this->getContactOptions() : [],
                'default' => '0',
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'show_upload',
            [
                'label' => __('Attachment', 'Shop.Forms.Labels'),
                'type' => $this->upload ? ControlsManager::SWITCHER : ControlsManager::HIDDEN,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
            ]
        );

        $this->addControl(
            'show_reference',
            [
                'label' => __('Order reference', 'Shop.Forms.Labels'),
                'type' => ControlsManager::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
            ]
        );

        Premium::addCaptchaPromoControls($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_subject_content',
            [
                'label' => __('Subject', 'Shop.Forms.Labels'),
                'condition' => [
                    'subject_id' => '0',
                ],
            ]
        );

        $this->addControl(
            'subject_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Subject', [], 'Shop.Forms.Labels'),
                'condition' => [
                    'show_labels' => 'yes',
                ],
            ]
        );

        $this->addControl(
            'subject_width',
            [
                'label' => __('Column Width'),
                'type' => ControlsManager::SELECT,
                'options' => $col_widths = [
                    '100' => '100%',
                    '80' => '80%',
                    '75' => '75%',
                    '66' => '66%',
                    '60' => '60%',
                    '50' => '50%',
                    '40' => '40%',
                    '33' => '33%',
                    '25' => '25%',
                    '20' => '20%',
                ],
                'default' => '100',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_email_content',
            [
                'label' => __('Email address', 'Shop.Forms.Labels'),
            ]
        );

        $this->addControl(
            'email_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Email address', [], 'Shop.Forms.Labels'),
                'condition' => $show_labels,
            ]
        );

        $this->addControl(
            'email_placeholder',
            [
                'label' => __('Placeholder'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('your@email.com', [], 'Shop.Forms.Help'),
            ]
        );

        $this->addControl(
            'email_width',
            [
                'label' => __('Column Width'),
                'type' => ControlsManager::SELECT,
                'options' => $col_widths,
                'default' => '100',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_reference_content',
            [
                'label' => __('Order reference', 'Shop.Forms.Labels'),
                'condition' => [
                    'show_reference!' => '',
                ],
            ]
        );

        $this->addControl(
            'reference_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Order reference', [], 'Shop.Forms.Labels'),
                'condition' => [
                    'show_labels' => 'yes',
                ],
            ]
        );

        $this->addControl(
            'reference_width',
            [
                'label' => __('Column Width'),
                'type' => ControlsManager::SELECT,
                'options' => $col_widths,
                'default' => '100',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_upload_content',
            [
                'label' => __('Attachment', 'Shop.Forms.Labels'),
                'condition' => [
                    'show_upload' => $this->upload ? 'yes' : 'hide',
                ],
            ]
        );

        $this->addControl(
            'upload_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Attachment', [], 'Shop.Forms.Labels'),
                'condition' => [
                    'show_labels' => 'yes',
                ],
            ]
        );

        $this->addControl(
            'upload_width',
            [
                'label' => __('Column Width'),
                'type' => ControlsManager::SELECT,
                'options' => $col_widths,
                'default' => '100',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_message_content',
            [
                'label' => __('Message', 'Shop.Forms.Labels'),
            ]
        );

        $this->addControl(
            'message_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Message', [], 'Shop.Forms.Labels'),
                'condition' => $show_labels,
            ]
        );

        $this->addControl(
            'message_placeholder',
            [
                'label' => __('Placeholder'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('How can we help?', [], 'Shop.Forms.Help'),
            ]
        );

        $this->addControl(
            'message_width',
            [
                'label' => __('Column Width'),
                'type' => ControlsManager::SELECT,
                'options' => $col_widths,
                'default' => '100',
            ]
        );

        $this->addControl(
            'message_rows',
            [
                'label' => __('Rows'),
                'type' => ControlsManager::NUMBER,
                'default' => '4',
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
            'button_text',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Send', [], 'Shop.Theme.Actions'),
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

        $this->addControl(
            'button_width',
            [
                'label' => __('Column Width'),
                'type' => ControlsManager::SELECT,
                'options' => $col_widths,
                'default' => '100',
            ]
        );

        $this->addResponsiveControl(
            'button_align',
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
                'default' => 'stretch',
                'prefix_class' => 'elementor%s-button-align-',
            ]
        );

        $this->addControl(
            'selected_icon',
            [
                'label' => __('Icon'),
                'label_block' => false,
                'type' => ControlsManager::ICONS,
                'skin' => 'inline',
                'fa4compatibility' => 'icon',
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
                        'paper-plane',
                    ],
                    'fa-regular' => [
                        'square-caret-right',
                        'circle-right',
                        'paper-plane',
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
                    'selected_icon[value]!' => '',
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
                    'selected_icon[value]!' => '',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_additional_options',
            [
                'label' => __('Additional Options'),
            ]
        );

        $this->addControl(
            'custom_messages',
            [
                'label' => __('Messages'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Default'),
                    'yes' => __('Custom'),
                ],
            ]
        );

        $this->addControl(
            'success_message',
            [
                'label' => __('Success'),
                'label_block' => true,
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Your message has been successfully sent to our team.', [], 'Modules.Contactform.Shop'),
                'condition' => [
                    'custom_messages!' => '',
                ],
            ]
        );

        $this->addControl(
            'error_message',
            [
                'label' => __('Error'),
                'label_block' => true,
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('An error occurred while sending the message.', [], 'Modules.Contactform.Shop'),
                'condition' => [
                    'custom_messages!' => '',
                ],
            ]
        );

        _CE_ADMIN_ && $this->addControl(
            'configure_module',
            [
                'label' => __('Contact Form'),
                'type' => ControlsManager::BUTTON,
                'text' => '<i class="eicon-external-link-square"></i>' . __('Configure', 'Admin.Actions'),
                'link' => [
                    'url' => Helper::$link->getAdminLink('AdminModules', true, [], ['configure' => 'contactform']),
                    'is_external' => true,
                ],
                'separator' => 'before',
            ]
        ) && $this->gdpr && $this->addControl(
            'configure_gdpr',
            [
                'label' => __('GDPR'),
                'type' => ControlsManager::BUTTON,
                'text' => '<i class="eicon-external-link-square"></i>' . __('Configure', 'Admin.Actions'),
                'link' => [
                    'url' => $this->gdpr_cfg,
                    'is_external' => true,
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_form',
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
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-form-fields-wrapper' => 'row-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'column_gap',
            [
                'label' => __('Columns Gap'),
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
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-group' => 'padding: 0 calc({{SIZE}}{{UNIT}} / 2);',
                    '{{WRAPPER}} .elementor-form-fields-wrapper' => 'margin: 0 calc(-{{SIZE}}{{UNIT}} / 2);',
                ],
            ]
        );

        $this->addControl(
            'heading_style_label',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Label'),
                'separator' => 'before',
                'condition' => $show_labels,
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'label_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} .elementor-form label',
                'condition' => $show_labels,
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
                'condition' => $show_labels,
            ]
        );

        $this->addControl(
            'mark_color',
            [
                'label' => __('Mark Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-mark-required .elementor-field-label:after' => 'color: {{VALUE}};',
                ],
                'condition' => $show_labels + [
                    'mark_required!' => '',
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
                'default' => [
                    'size' => 5,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-group > .elementor-field-label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementor-field-option .elementor-field-label' => 'padding-inline-start: {{SIZE}}{{UNIT}};',
                ],
                'condition' => $show_labels,
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

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'messages_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_2,
                'selector' => '{{WRAPPER}} .elementor-message',
            ]
        );

        $this->addControl(
            'heading_style_success',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Success'),
            ]
        );

        $this->addControl(
            'success_message_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-message.elementor-message-success' => 'color: {{COLOR}};',
                ],
            ]
        );

        $this->addControl(
            'heading_style_error',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Error'),
            ]
        );

        $this->addControl(
            'error_message_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-message.elementor-message-danger' => 'color: {{COLOR}};',
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
        $settings = $this->getSettingsForDisplay();
        $id = substr($this->getIdInt(), 0, 3);
        $id_contact = 0;
        $id_order = 0;
        $customer = $GLOBALS['customer'];
        $orders = $settings['show_reference'] && $customer->id ? \Order::getCustomerOrders($customer->id) : [];
        $show_labels = (bool) $settings['show_labels'];
        $mark_class = !empty($settings['mark_required']) ? ' elementor-mark-required' : '';
        $input_classes = 'elementor-field elementor-field-textual elementor-size-' . $settings['input_size'];

        $this->addRenderAttribute('form', [
            'action' => Helper::$link->getModuleLink('creativeelements', 'ajax', [], null, null, null, true),
            'data-success' => $settings['custom_messages'] ? $settings['success_message'] : '',
            'data-error' => $settings['custom_messages'] ? $settings['error_message'] : '',
        ]);
        $this->addRenderAttribute('email', [
            'value' => $customer->email ?: '',
            'placeholder' => $settings['email_placeholder'] ?: __('your@email.com', 'Shop.Forms.Help'),
        ]);
        $this->addRenderAttribute('message', [
            'placeholder' => $settings['message_placeholder'] ?: __('How can we help?', 'Shop.Forms.Help'),
            'rows' => (int) $settings['message_rows'],
        ]);
        $this->addRenderAttribute('button', 'class', 'elementor-button elementor-size-' . $settings['button_size']);

        if ($settings['button_hover_animation']) {
            $this->addRenderAttribute('button', 'class', 'elementor-animation-' . $settings['button_hover_animation']);
        }

        if (\Tools::getIsset('token') && $id_customer_thread = (int) \Tools::getValue('id_customer_thread')) {
            $ct = new \CustomerThread($id_customer_thread);

            if (\Tools::getValue('token') === $ct->token) {
                $id_contact = $ct->id_contact;

                if ($settings['show_reference'] && $id_order = $ct->id_order) {
                    $order = new \Order($id_order);
                    $orders = [
                        [
                            'id_order' => $order->id,
                            'reference' => $order->reference,
                        ],
                    ];
                }
                $this->setRenderAttribute('email', 'value', $ct->email);
            }
        } ?>
        <form class="ce-contact-form elementor-form" method="post" <?php $this->printRenderAttributeString('form'); ?> enctype="multipart/form-data">
            <div class="elementor-form-fields-wrapper">
                <input type="hidden" name="url">
            <?php if ($token = $this->getToken()) { ?>
                <input type="hidden" name="token" value="<?php escape($token); ?>">
            <?php } ?>
            <?php if ($settings['subject_id']) { ?>
                <input type="hidden" name="id_contact" value="<?php echo (int) ($id_contact ?: $settings['subject_id']); ?>">
            <?php } else { ?>
                <div class="elementor-field-group elementor-column elementor-sm-100 elementor-col-<?php echo (int) $settings['subject_width']; ?> elementor-field-type-select">
                <?php if ($show_labels) { ?>
                    <label class="elementor-field-label" for="id-contact-<?php echo (int) $id; ?>">
                        <?php echo $settings['subject_label'] ?: __('Subject', 'Shop.Forms.Labels'); ?>
                    </label>
                <?php } ?>
                    <div class="elementor-select-wrapper">
                        <select name="id_contact" id="id-contact-<?php echo (int) $id; ?>" class="<?php escape($input_classes); ?>">
                    <?php foreach (\Contact::getContacts($GLOBALS['language']->id) as $contact) { ?>
                        <?php if (!$id_contact || $id_contact == $contact['id_contact']) { ?>
                            <option value="<?php echo (int) $contact['id_contact']; ?>"><?php echo $contact['name']; ?></option>
                        <?php } ?>
                    <?php } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
                <div class="elementor-field-group elementor-column elementor-sm-100 elementor-col-<?php echo (int) $settings['email_width'], esc_attr($mark_class); ?> elementor-field-type-email">
                <?php if ($show_labels) { ?>
                    <label class="elementor-field-label" for="from-<?php echo (int) $id; ?>">
                        <?php echo $settings['email_label'] ?: __('Email address', 'Shop.Forms.Labels'); ?>
                    </label>
                <?php } ?>
                    <input type="email" name="from" id="from-<?php echo (int) $id; ?>" <?php $this->printRenderAttributeString('email'); ?> class="<?php escape($input_classes); ?>" inputmode="email" required>
                </div>
            <?php if ($settings['show_reference'] && (Plugin::$instance->editor->isEditMode() || $orders)) { ?>
                <div class="elementor-field-group elementor-column elementor-sm-100 elementor-col-<?php echo (int) $settings['reference_width']; ?> elementor-field-type-select">
                <?php if ($show_labels) { ?>
                    <label class="elementor-field-label" for="id-order-<?php echo (int) $id; ?>">
                        <?php echo $settings['reference_label'] ?: __('Order reference', 'Shop.Forms.Labels'); ?>
                    </label>
                <?php } ?>
                    <div class="elementor-select-wrapper">
                        <select name="id_order" id="id-order-<?php echo (int) $id; ?>" class="<?php escape($input_classes); ?>">
                        <?php if (!$id_order) { ?>
                            <option value=""><?php echo __('Select reference', 'Shop.Forms.Labels'); ?></option>
                        <?php } ?>
                        <?php foreach ($orders as &$order) { ?>
                            <option value="<?php echo (int) $order['id_order']; ?>"><?php echo $order['reference']; ?></option>
                        <?php } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <?php if ($this->upload && $settings['show_upload']) { ?>
                <div class="elementor-field-group elementor-column elementor-sm-100 elementor-col-<?php echo (int) $settings['upload_width']; ?> elementor-field-type-file">
                <?php if ($show_labels) { ?>
                    <label class="elementor-field-label" for="file-upload-<?php echo (int) $id; ?>">
                        <?php echo $settings['upload_label'] ?: __('Attachment', 'Shop.Forms.Labels'); ?>
                    </label>
                <?php } ?>
                    <input type="file" name="fileUpload" id="file-upload-<?php echo (int) $id; ?>" class="<?php escape($input_classes); ?>" accept=".txt, .rtf, .doc, .docx, .pdf, .zip, .png, .jpeg, .gif, .jpg">
                </div>
            <?php } ?>
                <div class="elementor-field-group elementor-column elementor-sm-100 elementor-col-<?php echo (int) $settings['message_width'], esc_attr($mark_class); ?> elementor-field-type-textarea">
                <?php if ($show_labels) { ?>
                    <label class="elementor-field-label" for="message-<?php echo (int) $id; ?>">
                        <?php echo $settings['message_label'] ?: __('Message', 'Shop.Forms.Labels'); ?>
                    </label>
                <?php } ?>
                    <textarea name="message" id="message-<?php echo (int) $id; ?>" <?php $this->printRenderAttributeString('message'); ?> class="<?php escape($input_classes); ?>" required></textarea>
                </div>
            <?php if ($this->gdpr) { ?>
                <div class="elementor-field-group elementor-column elementor-col-100<?php escape($mark_class); ?> elementor-field-type-checkbox">
                    <label class="elementor-field-option">
                        <input type="checkbox" name="<?php escape($this->gdpr); ?>" value="1" required><span class="elementor-field-label"><?php echo $this->gdpr_msg; ?></span>
                    </label>
                </div>
            <?php } ?>
                <div class="elementor-field-group elementor-column elementor-sm-100 elementor-col-<?php echo (int) $settings['button_width']; ?> elementor-field-type-submit">
                    <button type="submit" name="submitMessage" value="Send" <?php $this->printRenderAttributeString('button'); ?>>
                        <span class="elementor-button-content-wrapper">
                        <?php if ($icon = IconsManager::getBcIcon($settings, 'icon')) { ?>
                            <span class="elementor-button-icon elementor-align-icon-<?php escape($settings['icon_align']); ?>" aria-hidden="true"><?php echo $icon; ?></span>
                        <?php } ?>
                            <span class="elementor-button-text"><?php echo $settings['button_text'] ?: __('Send', 'Shop.Theme.Actions'); ?></span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
        <?php
    }

    protected function contentTemplate()
    {
        $trans = [Helper::$translator, 'trans']; ?>
        <#
        var contacts = <?php echo json_encode(\Contact::getContacts($GLOBALS['language']->id)); ?>,
            email_placeholder = settings.email_placeholder || <?php echo json_encode($trans('your@email.com', [], 'Shop.Forms.Help')); ?>,
            message_placeholder = settings.message_placeholder || <?php echo json_encode($trans('How can we help?', [], 'Shop.Forms.Help')); ?>,
            upload = <?php echo (int) $this->upload; ?>;
            mark_class = settings.mark_required ? ' elementor-mark-required' : '',
            icon = elementor.helpers.getBcIcon( view, settings, 'icon' );
        #>
        <form class="ce-contact-form elementor-form">
            <div class="elementor-form-fields-wrapper">
            <# if (!+settings.subject_id) { #>
                <div class="elementor-field-group elementor-column elementor-sm-100 elementor-col-{{ settings.subject_width }} elementor-field-type-select">
                <# if (settings.show_labels) { #>
                    <label class="elementor-field-label">{{ settings.subject_label || <?php echo json_encode($trans('Subject', [], 'Shop.Forms.Labels')); ?> }}</label>
                <# } #>
                    <div class="elementor-select-wrapper">
                        <select name="id_contact" class="elementor-field elementor-field-textual elementor-size-{{ settings.input_size }}">
                        <# _.each(contacts, function(contact) { #>
                            <option>{{ contact.name }}</option>
                        <# }) #>
                        </select>
                    </div>
                </div>
            <# } #>
                <div class="elementor-field-group elementor-column elementor-sm-100 elementor-col-{{ settings.email_width }}{{ mark_class}} elementor-field-type-email">
                <# if (settings.show_labels) { #>
                    <label class="elementor-field-label">{{ settings.email_label || <?php echo json_encode($trans('Email address', [], 'Shop.Forms.Labels')); ?> }}</label>
                <# } #>
                    <input type="email" name="from" placeholder="{{ email_placeholder }}" class="elementor-field elementor-field-textual elementor-size-{{ settings.input_size }}">
                </div>
            <# if (settings.show_reference) { #>
                <div class="elementor-field-group elementor-column elementor-sm-100 elementor-col-{{ settings.reference_width }} elementor-field-type-select">
                <# if (settings.show_labels) { #>
                    <label class="elementor-field-label">{{ settings.reference_label || <?php echo json_encode($trans('Order reference', [], 'Shop.Forms.Labels')); ?> }}</label>
                <# } #>
                    <div class="elementor-select-wrapper">
                        <select name="id_order" class="elementor-field elementor-field-textual elementor-size-{{ settings.input_size }}">
                            <option>{{ <?php echo json_encode($trans('Select reference', [], 'Shop.Forms.Labels')); ?> }}</option>
                        </select>
                    </div>
                </div>
            <# } #>
            <# if (upload && settings.show_upload) { #>
                <div class="elementor-field-group elementor-column elementor-sm-100 elementor-col-{{ settings.upload_width }} elementor-field-type-file">
                <# if (settings.show_labels) { #>
                    <label class="elementor-field-label">{{ settings.upload_label || <?php echo json_encode($trans('Attachment', [], 'Shop.Forms.Labels')); ?> }}</label>
                <# } #>
                    <input type="file" name="fileUpload" class="elementor-field elementor-field-textual elementor-size-{{ settings.input_size }}">
                </div>
            <# } #>
                <div class="elementor-field-group elementor-column elementor-sm-100 elementor-col-{{ settings.message_width }}{{ mark_class }} elementor-field-type-textarea">
                <# if (settings.show_labels) { #>
                    <label class="elementor-field-label">{{ settings.message_label || <?php echo json_encode($trans('Message', [], 'Shop.Forms.Labels')); ?> }}</label>
                <# } #>
                    <textarea name="message" placeholder="{{ message_placeholder }}" class="elementor-field elementor-field-textual elementor-size-{{ settings.input_size }}" rows="{{ settings.message_rows }}"></textarea>
                </div>
            <?php if ($this->gdpr) { ?>
                <div class="elementor-field-group elementor-column elementor-col-100{{ mark_class }} elementor-field-type-checkbox">
                    <label class="elementor-field-option">
                        <input type="checkbox"><span class="elementor-field-label"><?php echo $this->gdpr_msg; ?></span>
                    </label>
                </div>
            <?php } ?>
                <div class="elementor-field-group elementor-column elementor-sm-100 elementor-col-{{ settings.button_width }} elementor-field-type-submit">
                    <button type="submit" name="submitMessage" value="Send" class="elementor-button elementor-size-{{ settings.button_size }} elementor-animation-{{ settings.button_hover_animation }}">
                        <span class="elementor-button-content-wrapper">
                        <# if ( icon ) { #>
                            <span class="elementor-button-icon elementor-align-icon-{{ settings.icon_align }}" aria-hidden="true">{{{ icon }}}</span>
                        <# } #>
                            <span class="elementor-button-text">{{ settings.button_text || <?php echo json_encode($trans('Send', [], 'Shop.Theme.Actions')); ?> }}</span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="elementor-message elementor-message-success" role="alert" hidden>
                {{{ settings.custom_messages && settings.success_message || <?php echo json_encode($trans('Your message has been successfully sent to our team.', [], 'Modules.Contactform.Shop')); ?> }}}
            </div>
            <div class="elementor-message elementor-message-danger" role="alert" hidden>
                {{{ settings.custom_messages && settings.error_message || <?php echo json_encode($trans('An error occurred while sending the message.', [], 'Modules.Contactform.Shop')); ?> }}}
            </div>
        </form>
        <?php
    }

    public function __construct($data = [], $args = [])
    {
        $this->upload = \Configuration::get('PS_CUSTOMER_SERVICE_FILE_UPLOAD');
        $this->initGDPR();

        parent::__construct($data, $args);
    }

    protected function initGDPR()
    {
        if (\Module::isEnabled('psgdpr') && \Module::getInstanceByName('psgdpr')
            && \GDPRConsent::getConsentActive($id_module = \Module::getModuleIdByName('contactform'))
        ) {
            $this->gdpr = 'psgdpr_consent_checkbox';
            $this->gdpr_msg = \GDPRConsent::getConsentMessage($id_module, $GLOBALS['language']->id);
            _CE_ADMIN_ && $this->gdpr_cfg = Helper::$link->getAdminLink('AdminModules', true, [], [
                'configure' => 'psgdpr',
                'page' => 'dataConsent',
            ]);
        } elseif (\Module::isEnabled('gdprpro') && \Configuration::get('gdpr-pro_consent_contact_enable')) {
            $this->gdpr = 'gdpr_consent_chkbox';
            $this->gdpr_msg = \Configuration::get('gdpr-pro_consent_contact_text', $GLOBALS['language']->id);
            _CE_ADMIN_ && $this->gdpr_cfg = Helper::$link->getAdminLink('AdminGdprConfig');
        }
        // Strip <p> tags from GDPR message
        $this->gdpr_msg && $this->gdpr_msg = preg_replace('`</?p\b.*?>`i', '', $this->gdpr_msg);
    }
}
