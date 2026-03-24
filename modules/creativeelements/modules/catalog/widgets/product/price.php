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

class ModulesXCatalogXWidgetsXProductXPrice extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/89-widgets/product-widgets/369-product-price-widget';

    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'product-price';
    }

    public function getTitle()
    {
        return __('Product Price');
    }

    public function getIcon()
    {
        return 'eicon-product-price';
    }

    public function getCategories()
    {
        return ['product-elements'];
    }

    public function getKeywords()
    {
        return ['shop', 'store', 'price', 'product', 'sale', 'discount'];
    }

    public function getStyleDepends()
    {
        return ['widget-product'];
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_product_price',
            [
                'label' => __('Product Price'),
            ]
        );

        $this->addControl(
            'layout',
            [
                'label' => __('Layout'),
                'type' => ControlsManager::SELECT,
                'default' => 'stacked',
                'options' => [
                    'inline' => __('Inline'),
                    'stacked' => __('Stacked'),
                ],
                'prefix_class' => 'ce-product-prices--layout-',
            ]
        );

        $this->addControl(
            'regular',
            [
                'label' => __('Regular Price', 'Shop.Theme.Catalog'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'return_value' => 'show',
                'default' => 'show',
            ]
        );

        $this->addControl(
            'discount',
            [
                'label' => __('Discount', 'Shop.Theme.Catalog'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'return_value' => 'show',
                'default' => 'show',
            ]
        );

        $this->addControl(
            'unit_price',
            [
                'label' => __('Unit Price', 'Shop.Theme.Catalog'),
                'type' => ControlsManager::SWITCHER,
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
                'return_value' => 'show',
                'default' => 'show',
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
                    '{{WRAPPER}} .ce-product-prices' => 'justify-content: {{VALUE}}',
                ],
                'prefix_class' => 'elementor%s-align-',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_product_price_style',
            [
                'label' => __('Product Price'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'typography',
                'scheme' => SchemeTypography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .ce-product-prices',
            ]
        );

        $this->addControl(
            'price_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'scheme' => [
                    'type' => SchemeColor::getType(),
                    'value' => SchemeColor::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-prices' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            'space_between',
            [
                'label' => __('Space Between'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-prices' => 'gap: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->startControlsTabs('tabs_style_prices');

        $this->startControlsTab(
            'tab_regular',
            [
                'label' => __('Regular'),
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'regular_typography',
                'selector' => '{{WRAPPER}} .ce-product-price-regular',
            ]
        );

        $this->addControl(
            'regular_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-price-regular' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_sale',
            [
                'label' => __('Sale'),
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'sale_typography',
                'selector' => '{{WRAPPER}} .ce-has-discount',
            ]
        );

        $this->addControl(
            'sale_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-has-discount' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_unit',
            [
                'label' => __('Unit'),
                'condition' => [
                    'unit_price!' => '',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'unit_typography',
                'selector' => '{{WRAPPER}} .ce-product-price-unit',
            ]
        );

        $this->addControl(
            'unit_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-price-unit' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        if ($this->getName() === 'product-price') {
            $this->startControlsTabs('tabs_style_others');

            $this->startControlsTab(
                'tab_tax_excl',
                [
                    'label' => __('Tax excl.'),
                ]
            );

            $this->addGroupControl(
                GroupControlTypography::getType(),
                [
                    'name' => 'tax_excl_typography',
                    'selector' => '{{WRAPPER}} .ce-product-price-without-taxes',
                ]
            );

            $this->addControl(
                'tax_excl_color',
                [
                    'label' => __('Text Color'),
                    'type' => ControlsManager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ce-product-price-without-taxes' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->endControlsTab();

            $this->startControlsTab(
                'tab_pack',
                [
                    'label' => __('Pack', 'Shop.Theme.Catalog'),
                ]
            );

            $this->addGroupControl(
                GroupControlTypography::getType(),
                [
                    'name' => 'pack_typography',
                    'selector' => '{{WRAPPER}} .ce-product-price-pack',
                ]
            );

            $this->addControl(
                'pack_color',
                [
                    'label' => __('Text Color'),
                    'type' => ControlsManager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ce-product-price-pack' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->endControlsTab();

            $this->startControlsTab(
                'tab_tax',
                [
                    'label' => __('Tax'),
                ]
            );

            $this->addGroupControl(
                GroupControlTypography::getType(),
                [
                    'name' => 'tax_typography',
                    'selector' => '{{WRAPPER}} .ce-tax-shipping-delivery-label',
                ]
            );

            $this->addControl(
                'tax_color',
                [
                    'label' => __('Text Color'),
                    'type' => ControlsManager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ce-tax-shipping-delivery-label' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->addControl(
                'heading_ecotax',
                [
                    'label' => __('Ecotax'),
                    'type' => ControlsManager::HEADING,
                ]
            );

            $this->addGroupControl(
                GroupControlTypography::getType(),
                [
                    'name' => 'ecotax_typography',
                    'selector' => '{{WRAPPER}} .ce-product-price-ecotax',
                ]
            );

            $this->addControl(
                'ecotax_color',
                [
                    'label' => __('Text Color'),
                    'type' => ControlsManager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ce-product-price-ecotax' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->endControlsTab();

            $this->endControlsTabs();
        }

        $this->endControlsSection();

        $this->startControlsSection(
            'section_product_price_discount_style',
            [
                'label' => __('Discount', 'Shop.Theme.Catalog'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'discount!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            'discount_spacing',
            [
                'label' => __('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-badge-sale' => 'margin-inline-start: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'discount_typography',
                'selector' => '{{WRAPPER}} .ce-product-badge-sale',
            ]
        );

        $this->addControl(
            'discount_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-badge-sale' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'discount_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-badge-sale' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'discount_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-badge-sale' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'discount_border_width',
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
                    '{{WRAPPER}} .ce-product-badge-sale' => 'border-style: solid; border-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addControl(
            'discount_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-badge-sale' => 'border-radius: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            'discount_padding',
            [
                'label' => __('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .ce-product-badge-sale' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();
    }

    protected function getHtmlWrapperClass()
    {
        return parent::getHtmlWrapperClass() . ' elementor-overflow-hidden';
    }

    protected function render()
    {
        $vars = &$GLOBALS['smarty']->tpl_vars;
        $product = $vars['product']->value;

        if (!$product['show_price']) {
            return;
        }
        $settings = $this->getSettingsForDisplay();
        $priceDisplay = $vars['priceDisplay']->value;
        $displayUnitPrice = $settings['unit_price'] && $vars['displayUnitPrice']->value;
        $displayPackPrice = $vars['displayPackPrice']->value;
        $noPackPrice = $vars['noPackPrice']->value;
        $configuration = &$vars['configuration']->value;
        ?>
        <div class="ce-product-prices">
        <?php if ($settings['regular'] && $product['has_discount']) { ?>
            <?php echo \Hook::exec('displayProductPriceBlock', ['product' => $product, 'type' => 'old_price']); ?>
            <del class="ce-product-price-regular"><span class="screen-reader-text"><?php _e('Regular price', 'Shop.Theme.Catalog'); ?>:</span> <?php echo $product['regular_price']; ?></del>
        <?php } ?>
            <div class="ce-product-price<?php $product['has_discount'] && print ' ce-has-discount'; ?>">
                <span class="screen-reader-text"><?php _e($product['has_discount'] ? 'Reduced price' : 'Price', 'Shop.Theme.Catalog'); ?>:</span>
                <span><?php echo \Hook::exec('displayProductPriceBlock', ['product' => $product, 'type' => 'custom_price', 'hook_origin' => 'product_sheet']) ?: $product['price']; ?></span
        <?php if ($settings['discount'] && $product['has_discount']) { ?>
            <?php if ('percentage' === $product['discount_type']) { ?>
                ><span class="ce-product-badge ce-product-badge-sale ce-product-badge-sale-percentage">
                    <?php _e('Save %percentage%', 'Shop.Theme.Catalog', ['%percentage%' => $product['discount_percentage_absolute']]); ?>
                </span
            <?php } else { ?>
                ><span class="ce-product-badge ce-product-badge-sale ce-product-badge-sale-amount">
                    <?php _e('Save %amount%', 'Shop.Theme.Catalog', ['%amount%' => $product['discount_to_display']]); ?>
                </span
            <?php } ?>
        <?php } ?>>
            </div>
        <?php if ($displayUnitPrice) { ?>
            <div class="ce-product-price-unit">
                <span class="screen-reader-text"><?php _e('Unit price', 'Shop.Theme.Catalog'); ?>:</span>
                <?php _e('(%unit_price%)', 'Shop.Theme.Catalog', ['%unit_price%' => $product['unit_price_full']]); ?>
            </div>
        <?php } ?>
        <?php if (2 == $priceDisplay) { ?>
            <div class="ce-product-price-without-taxes" aria-label="<?php escape("$product[price_tax_exc] " . __('Tax excluded', 'Shop.Theme.Checkout')); ?>">
                <?php _e('%price% tax excl.', 'Shop.Theme.Catalog', ['%price%' => $product['price_tax_exc']]); ?>
            </div>
        <?php } ?>
        <?php if ($displayPackPrice) { ?>
            <div class="ce-product-price-pack">
                <?php _e('Instead of %price%', 'Shop.Theme.Catalog', ['%price%' => $noPackPrice]); ?>
            </div>
        <?php } ?>
        <?php if ($product['ecotax']['amount'] > 0) { ?>
            <div class="ce-product-price-ecotax">
                <?php _e('Including %amount% for ecotax', 'Shop.Theme.Catalog', ['%amount%' => $product['ecotax']['value']]); ?>
            <?php if ($product['has_discount']) { ?>
                <?php _e('(not impacted by the discount)', 'Shop.Theme.Catalog'); ?>
            <?php } ?>
            </div>
        <?php } ?>
            <?php echo \Hook::exec('displayProductPriceBlock', ['product' => $product, 'type' => 'weight', 'hook_origin' => 'product_sheet']); ?>
        <?php ob_start(); ?>
        <?php if (isset($configuration['taxes_enabled']) && !$configuration['taxes_enabled']) { ?>
            <?php _e('No tax', 'Shop.Theme.Catalog'); ?>
        <?php } elseif ($configuration['display_taxes_label']) { ?>
            <?php echo $product['labels']['tax_long']; ?>
        <?php } ?>
            <?php echo \Hook::exec('displayProductPriceBlock', ['product' => $product, 'type' => 'price']); ?>
            <?php echo \Hook::exec('displayProductPriceBlock', ['product' => $product, 'type' => 'after_price']); ?>
        <?php if ($tax_shipping = trim(ob_get_clean())) { ?>
            <div class="ce-tax-shipping-delivery-label"><?php echo $tax_shipping; ?></div>
        <?php } ?>
        </div>
        <?php
    }

    public function renderPlainContent()
    {
    }
}
