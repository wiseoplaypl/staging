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

class ModulesXCustomerXWidgetsXRegisterForm extends WidgetBase
{
    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'register-form';
    }

    public function getTitle()
    {
        return __('Register Form');
    }

    public function getIcon()
    {
        return 'eicon-site-identity';
    }

    public function getCategories()
    {
        return ['customer-elements'];
    }

    public function getKeywords()
    {
        return ['register', 'sign up', 'customer', 'user', 'form'];
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
                'label' => __('Form Fields'),
            ]
        );

        $this->addControl(
            'mark_required',
            [
                'label' => __('Required Mark'),
                'type' => ControlsManager::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
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
            'show_comment',
            [
                'label' => __('Comment'),
                'type' => ControlsManager::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
            ]
        );

        $this->addControl(
            'show_gender',
            [
                'label' => __('Social title', 'Shop.Forms.Labels'),
                'type' => ControlsManager::SWITCHER,
                'label_off' => __('Hide'),
                'label_on' => __('Show'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'show_birthdate',
            [
                'type' => ControlsManager::HIDDEN,
                'default' => \Configuration::get('PS_CUSTOMER_BIRTHDATE'),
            ]
        );

        _CE_ADMIN_ && $this->addControl(
            'birthdate',
            [
                'label' => __('Ask for birth date', 'Admin.Shopparameters.Feature'),
                'type' => ControlsManager::BUTTON,
                'text' => '<i class="eicon-external-link-square"></i>' . __('Configure', 'Admin.Actions'),
                'link' => [
                    'url' => $url = Helper::$link->getAdminLink('AdminCustomerPreferences'),
                    'is_external' => true,
                ],
            ]
        ) && $this->addControl(
            'partner_offers',
            [
                'label' => __('Partner offers', 'Admin.Orderscustomers.Feature'),
                'type' => ControlsManager::BUTTON,
                'text' => '<i class="eicon-external-link-square"></i>' . __('Configure', 'Admin.Actions'),
                'link' => [
                    'url' => $url,
                    'is_external' => true,
                ],
            ]
        );

        Premium::addCaptchaPromoControls($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_gender',
            [
                'label' => __('Social title', 'Shop.Forms.Labels'),
                'condition' => [
                    'show_gender!' => '',
                ],
            ]
        );

        $this->addControl(
            'id_gender_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Social title', [], 'Shop.Forms.Labels'),
            ]
        );

        $this->addControl(
            'id_gender_width',
            [
                'label' => __('Column Width'),
                'type' => ControlsManager::SELECT,
                'options' => $col_widths = [
                    // '' => __('Default'),
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

        _CE_ADMIN_ && $this->addControl(
            'genders',
            [
                'label' => __('Social titles', 'Admin.Shopparameters.Feature'),
                'type' => ControlsManager::BUTTON,
                'text' => '<i class="eicon-external-link-square"></i>' . __('Configure', 'Admin.Actions'),
                'link' => [
                    'url' => $url = Helper::$link->getAdminLink('AdminGenders'),
                    'is_external' => true,
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_name',
            [
                'label' => __('Name'),
            ]
        );

        $this->addControl(
            'heading_firstname',
            [
                'label' => __('First name', 'Shop.Forms.Labels'),
                'type' => ControlsManager::HEADING,
            ]
        );

        $this->addControl(
            'firstname_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('First name', [], 'Shop.Forms.Labels'),
            ]
        );

        $this->addControl(
            'firstname_placeholder',
            [
                'label' => __('Placeholder'),
                'type' => ControlsManager::TEXT,
            ]
        );

        $this->addControl(
            'firstname_width',
            [
                'label' => __('Column Width'),
                'type' => ControlsManager::SELECT,
                'options' => $col_widths,
                'default' => '100',
            ]
        );

        $this->addControl(
            'heading_lastname',
            [
                'label' => __('Last name', 'Shop.Forms.Labels'),
                'type' => ControlsManager::HEADING,
            ]
        );

        $this->addControl(
            'lastname_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Last name', [], 'Shop.Forms.Labels'),
            ]
        );

        $this->addControl(
            'lastname_placeholder',
            [
                'label' => __('Placeholder'),
                'type' => ControlsManager::TEXT,
            ]
        );

        $this->addControl(
            'lastname_width',
            [
                'label' => __('Column Width'),
                'type' => ControlsManager::SELECT,
                'options' => $col_widths,
                'default' => '100',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_email',
            [
                'label' => __('Email', 'Shop.Forms.Labels'),
            ]
        );

        $this->addControl(
            'email_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Email', [], 'Shop.Forms.Labels'),
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
            'section_password',
            [
                'label' => __('Password', 'Shop.Forms.Labels'),
            ]
        );

        $this->addControl(
            'password_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Password', [], 'Shop.Forms.Labels'),
            ]
        );

        $this->addControl(
            'password_placeholder',
            [
                'label' => __('Placeholder'),
                'type' => ControlsManager::TEXT,
            ]
        );

        $this->addControl(
            'password_width',
            [
                'label' => __('Column Width'),
                'type' => ControlsManager::SELECT,
                'options' => $col_widths,
                'default' => '100',
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
            'section_birthdate',
            [
                'label' => __('Birthdate', 'Shop.Forms.Labels'),
                'condition' => [
                    'show_birthdate!' => '',
                ],
            ]
        );

        $this->addControl(
            'birthday_label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'placeholder' => $trans('Birthdate', [], 'Shop.Forms.Labels'),
            ]
        );

        $this->addControl(
            'birthday_width',
            [
                'label' => __('Column Width'),
                'type' => ControlsManager::SELECT,
                'options' => $col_widths,
                'default' => '100',
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
                'placeholder' => Helper::$translator->trans((int) _PS_VERSION_ < 8 ? 'Save' : 'Create account', [], 'Shop.Theme.Actions'),
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

        _CE_ADMIN_ && \Module::isEnabled('psgdpr') && $this->addControl(
            'gdpr',
            [
                'label' => __('GDPR'),
                'type' => ControlsManager::BUTTON,
                'text' => '<i class="eicon-external-link-square"></i>' . __('Configure', 'Admin.Actions'),
                'link' => [
                    'url' => Helper::$link->getAdminLink('AdminModules', true, [], [
                        'configure' => 'psgdpr',
                        'page' => 'dataConsent',
                    ]),
                    'is_external' => true,
                ],
            ]
        );

        _CE_ADMIN_ && \Module::isEnabled('ps_emailsubscription') && $this->addControl(
            'emailsubscription',
            [
                'label' => __('Subscribe', 'Show.Theme.Actions'),
                'type' => ControlsManager::BUTTON,
                'text' => '<i class="eicon-external-link-square"></i>' . __('Configure', 'Admin.Actions'),
                'link' => [
                    'url' => Helper::$link->getAdminLink('AdminModules', true, [], ['configure' => 'ps_emailsubscription']),
                    'is_external' => true,
                ],
            ]
        );

        _CE_ADMIN_ && \Module::isEnabled('ps_dataprivacy') && $this->addControl(
            'dataprivacy',
            [
                'label' => explode('[1]', __('Customer data privacy[1][2]%message%[/2]', 'Modules.Dataprivacy.Shop'))[0],
                'type' => ControlsManager::BUTTON,
                'text' => '<i class="eicon-external-link-square"></i>' . __('Configure', 'Admin.Actions'),
                'link' => [
                    'url' => Helper::$link->getAdminLink('AdminModules', true, [], ['configure' => 'ps_dataprivacy']),
                    'is_external' => true,
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
                    '{{WRAPPER}} .elementor-field-type-link' => 'margin-top: {{SIZE}}{{UNIT}};',
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
                    'size' => '10',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-group' => 'padding: 0 calc({{SIZE}}{{UNIT}} / 2);',
                    '{{WRAPPER}} .elementor-form-fields-wrapper' => 'margin: 0 calc(-{{SIZE}}{{UNIT}} / 2);',
                ],
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
            'mark_color',
            [
                'label' => __('Mark Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-mark-required .elementor-field-label:after' => 'color: {{VALUE}};',
                ],
                'condition' => [
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
            ]
        );

        $this->addControl(
            'heading_radio_style',
            [
                'label' => __('Radio Buttons'),
                'type' => ControlsManager::HEADING,
            ]
        );

        $this->addControl(
            'option_space_between',
            [
                'label' => __('Space Between'),
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
                    '{{WRAPPER}} .elementor-field-type-radio-buttons .elementor-row' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'heading_comment',
            [
                'label' => __('Comment'),
                'type' => ControlsManager::HEADING,
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'comment_typography',
                'selector' => '{{WRAPPER}} em',
            ]
        );

        $this->addControl(
            'comment_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} em' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'comment_spacing',
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
                    '{{WRAPPER}} em, {{WRAPPER}} .elementor-field-label > br' => 'margin-top: {{SIZE}}{{UNIT}};',
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
        $tpl_vars = &$GLOBALS['smarty']->tpl_vars;
        $form = $tpl_vars['register_form']->value;
        $vars = \Closure::bind(function () {
            return $this->renderable->getTemplateVariables();
        }, $form, $form)->__invoke();
        $id = substr($this->getIdInt(), 0, 3);
        $settings = $this->getSettingsForDisplay();
        $mark_required = (bool) $settings['mark_required'];
        $show_comment = (bool) $settings['show_comment'];
        $input_classes = 'elementor-field elementor-field-textual elementor-size-' . $settings['input_size'];

        if ($edit_mode = Plugin::$instance->editor->isEditMode()) {
            $vars['errors'][''][] = __('An unexpected error occurred while creating your account.', 'Shop.Notifications.Error');
            $vars['errors']['email'][] = __('The email is already used, please choose another one or sign in', 'Shop.Notifications.Error');
        }
        $vars['errors'][''] && $this->addRenderAttribute('_wrapper', 'class', 'elementor-widget-alert');

        if (!$settings['show_gender']) {
            unset($vars['formFields']['id_gender']);
        }

        if ($password_policy = (int) _PS_VERSION_ < 8 ? [] : $tpl_vars['configuration']->value['password_policy']) {
            $password_width = $settings['password_width'];
            unset($settings['password_width']);
        }
        $this->addRenderAttribute('button', 'class', 'elementor-button elementor-size-' . $settings['button_size']);
        $settings['button_hover_animation'] && $this->addRenderAttribute('button', 'class', 'elementor-animation-' . $settings['button_hover_animation']);

        echo $tpl_vars['hook_create_account_top']->value;
        ?>
        <form class="ce-register-form elementor-form js-customer-form" method="post" action="<?php escape($vars['action']); ?>">
        <?php foreach ($vars['errors'][''] as $error) { ?>
            <div class="elementor-alert elementor-alert-danger" role="alert"<?php $edit_mode && print ' hidden'; ?>>
                <span class="elementor-alert-description"><?php echo $error; ?></span>
            </div>
        <?php } ?>
            <div class="elementor-form-fields-wrapper">
        <?php foreach ($vars['formFields'] as &$field) { ?>
            <?php if ('hidden' === $type = $field['type']) { ?>
                <input type="hidden" name="<?php escape($field['name']); ?>" value="<?php escape($field['value'] ?: ''); ?>">
            <?php } elseif ($name = $field['name']) { ?>
                <?php 'password' === $name && $password_policy && print '<div class="field-password-policy elementor-column elementor-sm-100 elementor-col-' . (int) $password_width . '">'; ?>
                <div class="elementor-field-group elementor-field-type-<?php escape($type); ?> js-input-column elementor-column elementor-sm-100 elementor-col-<?php
                    echo !empty($settings[$name . '_width']) ? (int) $settings[$name . '_width'] : 100, $mark_required && $field['required'] ? ' elementor-mark-required' : '', $vars['errors'][$name] && !$edit_mode ? ' elementor-error' : ''; ?>">
                <?php if ('checkbox' !== $type) { ?>
                    <label for="<?php escape("$name-$id"); ?>" class="elementor-field-label">
                        <?php echo !empty($settings[$name . '_label']) ? $settings[$name . '_label'] : $field['label']; ?>
                    </label>
                <?php } ?>
                <?php if ('select' === $type || 'countrySelect' === $type) { ?>
                    <div class="elementor-select-wrapper">
                        <select name="<?php escape($name); ?>" id="<?php escape("$name-$id"); ?>" class="<?php escape($input_classes); ?>"<?php $field['required'] && print ' required'; ?>>
                        <?php if ('countrySelect' === $type) { ?>
                            <option disabled selected><?php _e('Please choose', 'Shop.Forms.Labels'); ?></option>
                        <?php } ?>
                        <?php foreach ($field['availableValues'] as $value => $label) { ?>
                            <option value="<?php escape($value); ?>"<?php $value == $field['value'] && print ' selected'; ?>><?php echo $label; ?></label>
                        <?php } ?>
                        </select>
                    </div>
                <?php } elseif ('radio-buttons' === $type) { ?>
                    <div class="elementor-row">
                    <?php foreach ($field['availableValues'] as $value => $label) { ?>
                        <label class="elementor-field-option">
                            <input type="radio" name="<?php escape($name); ?>" value="<?php escape($value); ?>"<?php echo $field['value'] == $value ? ' checked' : '', $field['required'] ? ' required' : ''; ?>><span class="elementor-field-label"><?php echo $label; ?></span>
                        </label>
                    <?php } ?>
                    </div>
                <?php } elseif ('checkbox' === $type) { ?>
                    <label class="elementor-field-option">
                        <input type="checkbox" name="<?php escape($name); ?>" value="1"<?php echo $field['value'] ? ' checked' : '', $field['required'] ? ' required' : ''; ?>><span class="elementor-field-label"><?php echo ($label = explode('<br>', $field['label'], 2))[0]; ?></span>
                    </label><?php empty($label[1]) || print $label[1]; ?>
                <?php } elseif ('password' === $type) { ?>
                    <div class="elementor-password-wrapper">
                        <input type="password" name="<?php escape($name); ?>" id="<?php escape("$name-$id"); ?>" class="<?php escape($input_classes); ?>" <?php echo Utils::renderHtmlAttributes(
                            ('password' === $name && $password_policy ? [
                                'data-minlength' => $password_policy['minimum_length'],
                                'data-maxlength' => $password_policy['maximum_length'],
                                'data-minscore' => $password_policy['minimum_score'],
                                'pattern' => '.{' . $password_policy['minimum_length'] . ',' . $password_policy['maximum_length'] . '}',
                            ] : [
                                'title' => __('At least 5 characters long', 'Shop.Forms.Help'),
                                'pattern' => '.{5,}',
                            ]) + [
                                'placeholder' => $settings['password_placeholder'],
                                'autocomplete' => $edit_mode ? 'new-password' : (isset($field['autocomplete']) ? $field['autocomplete'] : ''),
                                'required' => $field['required'] ? [] : '',
                            ]
                        ); ?>>
                    <?php if ($settings['show_password_icon']['value']) { ?>
                        <a href="#ce-action=toggle&target=password" class="elementor-icon" title="<?php esc_attr_e('Show', 'Shop.Theme.Actions'); ?>" role="button" aria-controls="<?php escape("$name-$id"); ?>" aria-label="<?php esc_attr_e('Show', 'Shop.Theme.Actions'); ?>">
                            <?php IconsManager::renderIcon($settings['show_password_icon'], ['aria-hidden' => 'true']); ?>
                        </a>
                        <a href="#ce-action=toggle&target=password" class="elementor-icon" title="<?php esc_attr_e('Hide', 'Shop.Theme.Actions'); ?>" role="button" aria-controls="<?php escape("$name-$id"); ?>" aria-label="<?php esc_attr_e('Hide', 'Shop.Theme.Actions'); ?>">
                            <?php IconsManager::renderIcon($settings['hide_password_icon'], ['aria-hidden' => 'true']); ?>
                        </a>
                    <?php } ?>
                    </div>
                <?php } else { ?>
                    <input type="<?php escape($type); ?>" name="<?php escape($name); ?>" id="<?php escape("$name-$id"); ?>" class="<?php escape($input_classes); ?>" <?php echo Utils::renderHtmlAttributes([
                        'value' => $field['value'],
                        'placeholder' => !empty($settings[$name . '_placeholder']) ? $settings[$name . '_placeholder'] : (
                            isset($field['availableValues']['placeholder']) ? $field['availableValues']['placeholder'] : ''
                        ),
                        'title' => !$show_comment && isset($field['availableValues']['comment']) ? $field['availableValues']['comment'] : '',
                        'maxlength' => $field['maxLength'],
                        'autocomplete' => isset($field['autocomplete']) ? $field['autocomplete'] : '',
                        'required' => $field['required'] ? [] : '',
                    ]); ?>>
                    <?php !$show_comment || empty($field['availableValues']['comment']) || print '<em>' . $field['availableValues']['comment'] . '</em>'; ?>
                <?php } ?>
                <?php foreach ($vars['errors'][$name] as $error) { ?>
                    <div class="elementor-message elementor-message-danger" role="alert"<?php $edit_mode && print ' hidden'; ?>><?php echo $error; ?></div>
                <?php } ?>
                </div>
                <?php 'password' === $name && $password_policy && print '</div>'; ?>
            <?php } ?>
        <?php } ?>
                <?php echo $vars['hook_create_account_form']; ?>
                <div class="elementor-field-type-submit elementor-field-group elementor-column elementor-col-100">
                    <button type="submit" name="submitCreate" value="1" <?php $this->printRenderAttributeString('button'); ?> data-link-action="save-customer">
                        <span class="elementor-button-content-wrapper">
                        <?php if (!empty($settings['button_icon']['value'])) { ?>
                            <span class="elementor-button-icon elementor-align-icon-<?php escape($settings['icon_align']); ?>" aria-hidden="true"><?php IconsManager::renderIcon($settings['button_icon']); ?></span>
                        <?php } ?>
                            <span class="elementor-button-text"><?php echo $settings['button_text'] ?: __((int) _PS_VERSION_ < 8 ? 'Save' : 'Create account', 'Shop.Theme.Actions'); ?></span>
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
