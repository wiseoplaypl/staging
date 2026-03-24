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

class ModulesXCatalogXWidgetsXProductXCustomization extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/89-widgets/product-widgets/443-product-customization-widget';

    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'product-customization';
    }

    public function getTitle()
    {
        return __('Product Customization');
    }

    public function getIcon()
    {
        return 'eicon-custom';
    }

    public function getCategories()
    {
        return ['product-elements'];
    }

    public function getKeywords()
    {
        return ['shop', 'store', 'product', 'customization'];
    }

    public function getStyleDepends()
    {
        return ['widget-form', 'widget-product-customization'];
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_customization',
            [
                'label' => __('Product Customization'),
            ]
        );

        $this->addControl(
            'title_text',
            [
                'label' => __('Title & Description'),
                'type' => ControlsManager::TEXT,
                'reset' => true,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => Helper::$translator->trans('Product customization', [], 'Shop.Theme.Catalog'),
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
                'placeholder' => Helper::$translator->trans("Don't forget to save your customization to be able to add to cart", [], 'Shop.Forms.Help'),
                'rows' => 5,
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
            'title_tag',
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

        $this->addControl(
            'input_heading',
            [
                'label' => __('Form Fields'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'input_size',
            [
                'label' => __('Size'),
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
            'mark_required',
            [
                'label' => __('Required Mark'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
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
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => Helper::$translator->trans('Save Customization', [], 'Shop.Theme.Actions'),
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
                'default' => 'start',
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
                    'fa-solid' => [
                        'floppy-disk',
                    ],
                    'fa-regular' => [
                        'floppy-disk',
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

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_customization',
            [
                'label' => __('Title & Description'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'heading_title',
            [
                'label' => __('Title'),
                'type' => ControlsManager::HEADING,
            ]
        );

        $this->addResponsiveControl(
            'title_align',
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
                    '{{WRAPPER}} .elementor-heading-title' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'title_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-heading-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .elementor-heading-title',
            ]
        );

        $this->addGroupControl(
            GroupControlTextStroke::getType(),
            [
                'name' => 'text_stroke',
                'selector' => '{{WRAPPER}} .elementor-heading-title',
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'title_shadow',
                'selector' => '{{WRAPPER}} .elementor-heading-title',
            ]
        );

        $this->addResponsiveControl(
            'title_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-heading-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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

        $this->addResponsiveControl(
            'description_align',
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
                    '{{WRAPPER}} .elementor-text-editor' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'description_color',
            [
                'label' => __('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-text-editor' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .elementor-text-editor',
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'description_shadow',
                'selector' => '{{WRAPPER}} .elementor-text-editor',
            ]
        );

        $this->addResponsiveControl(
            'description_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-text-editor' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_form',
            [
                'label' => __('Form Fields'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'row_gap',
            [
                'label' => __('Space Between'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'max' => 60,
                    ],
                    'em' => [
                        'max' => 6,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-form-fields-wrapper' => 'row-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'heading_style_label',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Label'),
                'separator' => 'before',
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

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .elementor-form label',
            ]
        );

        $this->addControl(
            'label_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 0,
                ],
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
            ]
        );

        $this->addControl(
            'field_text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-field' => 'color: {{VALUE}};',
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

        $this->endControlsSection();
    }

    protected function shouldPrintEmpty()
    {
        return true;
    }

    protected function getHtmlWrapperClass()
    {
        return parent::getHtmlWrapperClass() . ' elementor-widget-heading elementor-widget-text-editor elementor-widget-contact-form';
    }

    protected function render()
    {
        $vars = &$GLOBALS['smarty']->tpl_vars;
        $product = $vars['product']->value;

        if ($vars['configuration']->value['is_catalog'] || !$product['customizations']['fields']) {
            return;
        }
        $id = $this->getId();
        $settings = $this->getSettings();
        $extensions = (int) _PS_VERSION_ < 8 ? ['png', 'jpg', 'gif'] : \ImageManager::EXTENSIONS_SUPPORTED;

        if ($title = ltrim($settings['title_text'] ?: __('Product customization', 'Shop.Theme.Catalog'))) {
            $title_tag = Utils::validateHtmlTag($settings['title_tag']);
            $this->addRenderAttribute('title_text', [
                'class' => 'elementor-heading-title',
                'id' => $title_id = 'ce-product-customization__title-' . $this->getId(),
            ]);
            $settings['title_display'] && $this->addRenderAttribute('title_text', 'class', 'ce-display-' . $settings['title_display']);
            $settings['title_text'] && $this->addInlineEditingAttributes('title_text', 'none');

            echo "<$title_tag {$this->getRenderAttributeString('title_text')}>$title</$title_tag>";

            $this->addRenderAttribute('form', 'aria-labelledby', $title_id);
        } else {
            $this->addRenderAttribute('form', 'aria-label', __('Product customization', 'Shop.Theme.Catalog'));
        }

        if ($description = ltrim($settings['description_text'] ?: __("Don't forget to save your customization to be able to add to cart", 'Shop.Forms.Help'))) {
            $this->addRenderAttribute('description_text', 'class', 'elementor-text-editor');
            $settings['description_text'] && $this->addInlineEditingAttributes('description_text');

            echo "<div {$this->getRenderAttributeString('description_text')}>$description</div>";
        }
        $mark_class = $settings['mark_required'] ? ' elementor-mark-required' : '';
        $this->addRenderAttribute('button', 'class', 'elementor-button elementor-size-' . $settings['button_size']);
        ?>
        <form class="ce-product-customization elementor-form" method="post" action="<?php escape($product['url']); ?>" enctype="multipart/form-data" <?php $this->printRenderAttributeString('form'); ?>>
            <div class="elementor-form-fields-wrapper">
            <?php foreach ($product['customizations']['fields'] as $field) { ?>
                <div class="elementor-field-group elementor-column elementor-col-100 elementor-field-type-<?php escape($field['type'] . ($field['required'] ? $mark_class : '')); ?>">
                    <label class="elementor-field-label" for="<?php escape($field_id = "field-$field[input_name]-$id"); ?>"><?php echo $field['label']; ?></label>
                <?php if ('text' === $field['type']) { ?>
                    <textarea class="elementor-field elementor-field-textual elementor-size-<?php escape($settings['input_size']); ?>" name="<?php escape($field['input_name']); ?>"
                        id="<?php escape($field_id); ?>" rows="1" maxlength="250"<?php $field['required'] && print ' required'; ?>><?php echo $field['text']; ?></textarea>
                    <?php if ($field['is_customized']) { ?>
                        <small><?php _e('Your customization:', 'Shop.Theme.Catalog'); ?> <strong><?php echo $field['text']; ?></strong></small>
                    <?php } ?>
                <?php } elseif ('image' === $field['type']) { ?>
                    <div class="elementor-field elementor-field-textual elementor-size-<?php echo esc_attr($settings['input_size']) . ($field['is_customized'] ? '' : ' ce-empty'); ?>">
                    <?php if (!$field['is_customized']) { ?>
                        <label class="ce-upload-text">
                            <?php echo __('Drag & Drop to Upload') . ' / ' . __('Choose file', 'Shop.Theme.Actions'); ?><br>
                            <small>
                                .<?php echo implode(' .', $extensions); ?>
                                (<?php _e('Up to %discount%', 'Shop.Theme.Catalog', ['%discount%' => round(\Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE', 2) / 1048576) . ' MB']); ?>)
                            </small>
                        </label>
                        <input class="ce-upload-file" type="file" name="<?php escape($field['input_name']); ?>" id="<?php escape($field_id); ?>" accept=".<?php escape(implode(', .', $extensions)); ?>"<?php $field['required'] && print ' required'; ?>>
                    <?php } ?>
                        <img class="ce-upload-preview" src="<?php echo $field['image'] ? esc_attr($field['image']['large']['url']) : 'data:,'; ?>" alt="" loading="lazy" tabindex="0" aria-label="<?php esc_attr_e('Selected', 'Shop.Theme.Checkout'); ?>" width="<?php echo (int) \Configuration::get('PS_PRODUCT_PICTURE_WIDTH'); ?>" height="<?php echo (int) \Configuration::get('PS_PRODUCT_PICTURE_HEIGHT'); ?>">
                        <label class="ce-upload-details">
                            <?php _e('Successful upload'); ?><br>
                            <small>(<?php echo $field['image'] && file_exists($file = _PS_UPLOAD_DIR_ . substr($field['image']['large']['url'], -32)) ? round(filesize($file) / 1024) . ' KB' : ''; ?>)</small>
                        </label>
                        <a href="<?php echo $field['image'] ? esc_attr($field['remove_image_url']) : 'javascript:;'; ?>" class="ce-upload-remove ceicon-close" role="button"
                            title="<?php esc_attr_e('Remove Image', 'Shop.Theme.Actions'); ?>" aria-label="<?php esc_attr_e('Remove Image', 'Shop.Theme.Actions'); ?>"></a>
                    </div>
                <?php } ?>
                </div>
            <?php } ?>
                <div class="elementor-field-group elementor-column elementor-col-100 elementor-field-type-submit">
                    <button type="submit" name="submitCustomizedData" <?php $this->printRenderAttributeString('button'); ?>>
                        <span class="elementor-button-content-wrapper">
                        <?php if ($settings['selected_icon']['value']) { ?>
                            <span class="elementor-button-icon elementor-align-icon-<?php escape($settings['icon_align']); ?>" aria-hidden="true"><?php IconsManager::renderIcon($settings['selected_icon']); ?></span>
                        <?php } ?>
                            <span class="elementor-button-text"><?php echo $settings['button_text'] ?: __('Save Customization', 'Shop.Theme.Actions'); ?></span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
        <?php
    }
}
