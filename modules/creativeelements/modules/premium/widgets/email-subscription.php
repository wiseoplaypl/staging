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

class ModulesXPremiumXWidgetsXEmailSubscription extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/87-widgets/premium-widgets/333-email-subscription-widget';

    protected $gdpr;

    protected $gdpr_msg;

    protected $gdpr_cfg;

    public function getName()
    {
        return 'email-subscription';
    }

    public function getTitle()
    {
        return __('Email Subscription');
    }

    public function getIcon()
    {
        return 'eicon-email-field';
    }

    public function getCategories()
    {
        return ['premium'];
    }

    public function getKeywords()
    {
        return ['email', 'subscribe', 'signup', 'newsletter', 'form'];
    }

    public function getStyleDepends()
    {
        return ['widget-form', 'widget-email-subscription'];
    }

    protected function isDynamicContent()
    {
        return false;
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_email_subscription',
            [
                'label' => __('Form Fields'),
            ]
        );

        $this->addResponsiveControl(
            'layout',
            [
                'label' => __('Layout'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'inline' => __('Inline'),
                    'multiline' => __('Stacked'),
                ],
                'default' => 'inline',
                'tablet_default' => 'inline',
                'mobile_default' => 'inline',
                'prefix_class' => 'elementor%s-layout-',
                'selectors_dictionary' => [
                    'inline' => 'nowrap',
                    'multiline' => 'wrap',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-type-subscribe' => 'flex-wrap: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'input_height',
            [
                'label' => __('Size'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 1,
                    ],
                ],
                'default' => [
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} input[type=email]' => 'height: {{SIZE}}{{UNIT}}; padding: 0 calc({{SIZE}}{{UNIT}} / 3);',
                    '{{WRAPPER}} button[type=submit]' => 'height: {{SIZE}}{{UNIT}}; padding: 0 calc({{SIZE}}{{UNIT}} / 3);',
                ],
            ]
        );

        Premium::addCaptchaPromoControls($this);

        $this->addControl(
            'heading_email',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Email', 'Shop.Forms.Labels'),
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'placeholder',
            [
                'label' => __('Placeholder'),
                'type' => ControlsManager::TEXT,
                'reset' => true,
                'placeholder' => Helper::$translator->trans('Your email address', [], 'Shop.Forms.Labels'),
            ]
        );

        $this->addResponsiveControl(
            'input_align',
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
                    '{{WRAPPER}} input[type=email]' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'heading_button',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Button'),
            ]
        );

        $this->addControl(
            'button_type',
            [
                'label' => __('Type'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'classic' => __('Classic'),
                    '' => __('Default'),
                    'primary' => __('Primary'),
                    'secondary' => __('Secondary'),
                ],
                'default' => 'classic',
                'prefix_class' => 'elementor-button-',
            ]
        );

        $this->addControl(
            'button',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
                'reset' => true,
                'placeholder' => Helper::$translator->trans('Subscribe', [], 'Shop.Theme.Actions'),
            ]
        );

        $this->addResponsiveControl(
            'button_align',
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
                'prefix_class' => 'elementor%s-align-',
                'selectors_dictionary' => [
                    'justify' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-type-subscribe' => 'justify-content: {{VALUE}};',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'layout',
                            'value' => 'multiline',
                        ],
                        [
                            'name' => 'layout_tablet',
                            'value' => 'multiline',
                        ],
                        [
                            'name' => 'layout_mobile',
                            'value' => 'multiline',
                        ],
                    ],
                ],
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
                    'em' => [
                        'max' => 5,
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

        $this->addControl(
            'view',
            [
                'label' => __('View'),
                'type' => ControlsManager::HIDDEN,
                'default' => 'traditional',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_additional_options',
            [
                'label' => __('Additional Options'),
            ]
        );

        _CE_ADMIN_ && $this->addControl(
            'configure_module',
            [
                'label' => __('Email Subscription'),
                'type' => ControlsManager::BUTTON,
                'text' => '<i class="eicon-external-link-square"></i>' . __('Configure', 'Admin.Actions'),
                'link' => [
                    'url' => Helper::$link->getAdminLink('AdminModules', true, [], ['configure' => 'ps_emailsubscription']),
                    'is_external' => true,
                ],
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
            'section_form_style',
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
                'condition' => $show_gdpr = [
                    'view' => $this->gdpr ? 'traditional' : '',
                ],
            ]
        );

        $this->addResponsiveControl(
            'max_width',
            [
                'label' => __('Max Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'max' => 1600,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} form' => 'max-width: {{SIZE}}{{UNIT}}',
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
                    '{{WRAPPER}} .elementor-widget-container, {{WRAPPER}} .elementor-field-label' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'heading_style_label',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Label'),
                'separator' => 'before',
                'condition' => $show_gdpr,
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'label_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} .elementor-form label',
                'condition' => $show_gdpr,
            ]
        );

        $this->addControl(
            'label_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-form label' => 'color: {{VALUE}};',
                ],
                'condition' => $show_gdpr,
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
                    '{{WRAPPER}} .elementor-field-option .elementor-field-label' => 'padding-inline-start: {{SIZE}}{{UNIT}};',
                ],
                'condition' => $show_gdpr,
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_input_style',
            [
                'label' => __('Email', 'Shop.Forms.Labels'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'input_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} input[type=email]',
            ]
        );

        $this->startControlsTabs('tabs_input_colors');

        $this->startControlsTab(
            'tab_input_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'input_text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} input[type=email]' => 'color: {{VALUE}};',
                    '{{WRAPPER}} input[type=email]::placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'input_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} input[type=email]' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'input_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} input[type=email]' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_input_focus',
            [
                'label' => __('Focus'),
            ]
        );

        $this->addControl(
            'input_focus_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} input[type=email]:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} input[type=email]:focus::placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'input_background_focus_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} input[type=email]:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'input_focus_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} input[type=email]:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'input_border_width',
            [
                'label' => __('Border Width'),
                'type' => ControlsManager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} input[type=email]' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->addResponsiveControl(
            'input_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} input[type=email]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'input_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} input[type=email]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

        $this->addResponsiveControl(
            'button_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} button[type=submit]' => 'margin: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'button_typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} button[type=submit]',
            ]
        );

        $this->startControlsTabs('tabs_button_colors');

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
                    '{{WRAPPER}} button[type=submit]' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'button_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} button[type=submit]' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'button_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} button[type=submit]' => 'border-color: {{VALUE}};',
                ],
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
                    '{{WRAPPER}} button[type=submit]:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'button_background_hover_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} button[type=submit]:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'button_hover_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} button[type=submit]:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'hover_animation',
            [
                'label' => __('Animation'),
                'label_block' => false,
                'type' => ControlsManager::HOVER_ANIMATION,
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addControl(
            'button_border_width',
            [
                'label' => __('Border Width'),
                'type' => ControlsManager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} button[type=submit]' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->addResponsiveControl(
            'button_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} button[type=submit]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'button_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} button[type=submit]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
            'messages_position',
            [
                'label' => __('Position'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'before' => __('Before'),
                    'after' => __('After'),
                ],
                'selectors_dictionary' => [
                    'before' => -1,
                    'after' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-message' => 'order: {{VALUE}}',
                ],
                'default' => 'after',
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
        $button_text = ltrim($settings['button'] ?: __('Subscribe', 'Shop.Theme.Actions'));
        $action = Helper::$link->getModuleLink('creativeelements', 'ajax', [], null, null, null, true);

        $this->addRenderAttribute('email', [
            'placeholder' => $settings['placeholder'] ?: __('Your email address', 'Shop.Forms.Labels'),
            'inputmode' => 'email',
        ]);
        $this->addRenderAttribute('button', 'class', ['elementor-button', 'elementor-size-sm']);
        $button_text || $this->addRenderAttribute('button', 'title', __('Subscribe', 'Shop.Theme.Actions'));

        if ($settings['hover_animation']) {
            $this->addRenderAttribute('button', 'class', 'elementor-animation-' . $settings['hover_animation']);
        } ?>
        <form class="ce-subscribe-form elementor-form" method="post" action="<?php escape($action); ?>">
            <div class="elementor-form-fields-wrapper">
                <input type="hidden" name="action" value="0">
                <div class="elementor-field-group elementor-column elementor-col-100 elementor-field-type-subscribe">
                    <input type="email" name="email" class="elementor-field elementor-field-textual" <?php $this->printRenderAttributeString('email'); ?> required>
                    <button type="submit" name="submitNewsletter" value="1" <?php $this->printRenderAttributeString('button'); ?>>
                        <span class="elementor-button-content-wrapper">
                        <?php if ($icon = IconsManager::getBcIcon($settings, 'icon')) { ?>
                            <span class="elementor-button-icon elementor-align-icon-<?php escape($settings['icon_align']); ?>" aria-hidden="true"><?php echo $icon; ?></span>
                        <?php } ?>
                        <?php if ($button_text) { ?>
                            <span class="elementor-button-text"><?php echo $button_text; ?></span>
                        <?php } ?>
                        </span>
                    </button>
                </div>
            <?php if ($this->gdpr) { ?>
                <div class="elementor-field-type-checkbox">
                    <label class="elementor-field-option">
                        <input type="checkbox" name="<?php escape($this->gdpr); ?>" value="1" required><span class="elementor-field-label"><?php echo $this->gdpr_msg; ?></span>
                    </label>
                </div>
            <?php } ?>
            </div>
        </form>
        <?php
    }

    protected function contentTemplate()
    {
        $trans = [Helper::$translator, 'trans'];
        ?>
        <# var icon = elementor.helpers.getBcIcon( view, settings, 'icon' ); #>
        <form class="ce-subscribe-form elementor-form">
            <div class="elementor-form-fields-wrapper">
                <div class="elementor-field-group elementor-column elementor-col-100 elementor-field-type-subscribe">
                    <input type="email" class="elementor-field elementor-field-textual" placeholder="{{ settings.placeholder || <?php echo json_encode($trans('Your email address', [], 'Shop.Forms.Labels')); ?> }}" required>
                    <button type="submit" class="elementor-button elementor-size-sm elementor-animation-{{ settings.hover_animation }}">
                        <span class="elementor-button-content-wrapper">
                        <# if ( icon ) { #>
                            <span class="elementor-button-icon elementor-align-icon-{{ settings.icon_align }}" aria-hidden="true">{{{ icon }}}</span>
                        <# } #>
                        <# if (!settings.button || settings.button.trim()) { #>
                            <span class="elementor-button-text">{{ settings.button || <?php echo json_encode($trans('Subscribe', [], 'Shop.Theme.Actions')); ?> }}</span>
                        <# } #>
                        </span>
                    </button>
                </div>
            <?php if ($this->gdpr) { ?>
                <div class="elementor-field-type-checkbox">
                    <label class="elementor-field-option">
                        <input type="checkbox"><span class="elementor-field-label"><?php echo $this->gdpr_msg; ?></span>
                    </label>
                </div>
            <?php } ?>
            </div>
            <div class="elementor-message elementor-message-success" role="alert" hidden>
                <?php echo $trans('You have successfully subscribed to this newsletter.', [], 'Modules.Emailsubscription.Shop'); ?>
            </div>
            <div class="elementor-message elementor-message-danger" role="alert" hidden>
                <?php echo $trans('An error occurred during the subscription process.', [], 'Modules.Emailsubscription.Shop'); ?>
            </div>
        </form>
        <?php
    }

    public function __construct($data = [], $args = [])
    {
        $this->initGDPR();

        parent::__construct($data, $args);
    }

    protected function initGDPR()
    {
        if (\Module::isEnabled('psgdpr') && \Module::getInstanceByName('psgdpr')
            && \GDPRConsent::getConsentActive($id_module = \Module::getModuleIdByName('ps_emailsubscription'))
        ) {
            $this->gdpr = 'psgdpr_consent_checkbox';
            $this->gdpr_msg = \GDPRConsent::getConsentMessage($id_module, $GLOBALS['language']->id);
            _CE_ADMIN_ && $this->gdpr_cfg = Helper::$link->getAdminLink('AdminModules', true, [], [
                'configure' => 'psgdpr',
                'page' => 'dataConsent',
            ]);
        } elseif (\Module::isEnabled('gdprpro') && \Configuration::get('gdpr-pro_consent_newsletter_enable')) {
            $this->gdpr = 'gdpr_consent_chkbox';
            $this->gdpr_msg = \Configuration::get('gdpr-pro_consent_newsletter_text', $GLOBALS['language']->id);
            _CE_ADMIN_ && $this->gdpr_cfg = Helper::$link->getAdminLink('AdminGdprConfig');
        }
        // Strip <p> tags from GDPR message
        $this->gdpr_msg && $this->gdpr_msg = preg_replace('`</?p\b.*?>`i', '', $this->gdpr_msg);
    }
}
