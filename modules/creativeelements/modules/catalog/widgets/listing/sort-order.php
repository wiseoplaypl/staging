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

class ModulesXCatalogXWidgetsXListingXSortOrder extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/107-widgets/listing-widgets/428-sorting-widget';

    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'listing-sort-order';
    }

    public function getTitle()
    {
        return __('Sorting');
    }

    public function getIcon()
    {
        return 'eicon-select';
    }

    public function getCategories()
    {
        return ['listing-elements'];
    }

    public function getKeywords()
    {
        return ['shop', 'store', 'category', 'products', 'sorting', 'asc', 'desc'];
    }

    public function getStyleDepends()
    {
        return ['widget-form', 'widget-listing'];
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_sort_orders',
            [
                'label' => __('Sorting'),
            ]
        );

        $this->addControl(
            'layout',
            [
                'label' => __('Layout'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'inline' => __('Inline'),
                    'stacked' => __('Stacked'),
                ],
                'default' => 'stacked',
                'prefix_class' => 'ce-sort-order--layout-',
                'render_type' => 'template',
            ]
        );

        $this->addControl(
            'label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'reset' => true,
                'placeholder' => Helper::$translator->trans('Sort by:', [], 'Shop.Theme.Global'),
            ]
        );

        $this->addControl(
            'select_size',
            [
                'label' => __('Size'),
                'type' => ControlsManager::SELECT,
                'default' => 'sm',
                'options' => [
                    'xs' => __('Extra Small'),
                    'sm' => __('Small'),
                    'md' => __('Medium'),
                    'lg' => __('Large'),
                    'xl' => __('Extra Large'),
                ],
            ]
        );

        _CE_ADMIN_ && $this->addControl(
            'configure',
            [
                'label' => __('Additional Options'),
                'type' => ControlsManager::BUTTON,
                'text' => '<i class="eicon-external-link-square"></i>' . __('Configure', 'Admin.Actions'),
                'link' => [
                    'url' => Helper::$link->getAdminLink('AdminPPreferences') . '#pagination_default_order_by',
                    'is_external' => true,
                ],
                'separator' => 'before',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_sort_orders_style',
            [
                'label' => __('Sorting'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'heading_style_label',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Label'),
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
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-group' => 'gap: {{SIZE}}{{UNIT}};',
                ],
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

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .elementor-form label',
            ]
        );

        $this->addControl(
            'heading_style_field',
            [
                'type' => ControlsManager::HEADING,
                'label' => __('Field'),
                'separator' => 'before',
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
    }

    protected function render()
    {
        $listing = &$GLOBALS['smarty']->tpl_vars['listing']->value;

        if (!$listing['products'] && empty(${'_GET'}['q'])) {
            return;
        }
        $id = $this->getId();
        $settings = $this->getSettingsForDisplay();
        $label = ltrim($settings['label']);
        ?>
        <div class="ce-sort-order elementor-form elementor-field-group">
            <label class="<?php echo $label || !$settings['label'] ? 'elementor-field-label' : 'screen-reader-text'; ?>" for="order-<?php escape($id); ?>">
                <?php echo $label ?: __('Sort by:', 'Shop.Theme.Global'); ?>
            </label>
            <div class="elementor-select-wrapper">
                <select name="order" id="order-<?php escape($id); ?>" class="elementor-field elementor-field-textual elementor-size-<?php escape($settings['select_size']); ?>"
                    onchange="prestashop.emit('updateFacets', this.children[this.selectedIndex].dataset.url)">
                <?php foreach ($listing['sort_orders'] as $sort_order) { ?>
                    <option value="<?php escape($sort_order['urlParameter']); ?>"<?php $sort_order['current'] && print ' selected'; ?> data-url="<?php escape($sort_order['url']); ?>">
                        <?php echo $sort_order['label']; ?>&emsp;
                    </option>
                <?php } ?>
                </select>
            </div>
        </div>
        <?php
    }

    public function renderPlainContent()
    {
    }
}
