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

class ModulesXCatalogXWidgetsXProductXBlock extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/89-widgets/product-widgets/384-product-block-widget';

    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'product-block';
    }

    public function getTitle()
    {
        return __('Product Block');
    }

    public function getIcon()
    {
        return 'eicon-product-meta';
    }

    public function getCategories()
    {
        return ['product-elements'];
    }

    public function getKeywords()
    {
        return ['shop', 'store', 'product', 'block'];
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_product_block',
            [
                'label' => __('Product Block'),
            ]
        );

        $this->addControl(
            'block',
            [
                'label_block' => true,
                'type' => ControlsManager::SELECT,
                'groups' => _CE_ADMIN_ ? [
                    '' => __('Select...'),
                    'ce_page_content' => __('Product Cover'),
                    'ce_product_flags' => '&emsp;' . __('Product Badges'),
                    'ce_product_cover_thumbnails' => '&emsp;' . __('Product Images'),
                    'ce_product_prices' => __('Product Price'),
                    // 'ce_product-variants' => __('Product Variants'),
                    'ce_product_customization' => __('Product Customization'),
                    'ce_product_pack' => __('Product Pack'),
                    'ce_product_discounts' => __('Volume discounts', 'Shop.Theme.Catalog'),
                    'ce_product_actions' => __('Product Actions'),
                    'ce_product_availability' => __('Product Availability'),
                    'ce_product_minimal_quantity' => __('Minimum quantity', 'Admin.Catalog.Feature'),
                    'ce_product_additional_info' => __('Additional Info'),
                    'ce_hook_display_reassurance' => __('Reassurance'),
                    'ce_product_tabs' => __('Product Tabs'),
                    'product_details' => [
                        'label' => __('Product Details', 'Shop.Theme.Catalog'),
                        'options' => [
                            'ce_product_reference' => __('Reference', 'Admin.Catalog.Feature'),
                            'ce_product_quantities' => __('Quantities', 'Admin.Catalog.Feature'),
                            'ce_product_availability_date' => __('Availability date', 'Admin.Catalog.Feature'),
                            'ce_product_out_of_stock' => __('Out-of-Stock'),
                            'ce_product_features' => __('Product Features'),
                            'ce_product_specific_references' => __('Specific references', 'Admin.Catalog.Feature'),
                            'ce_product_condition' => __('Condition', 'Admin.Catalog.Feature'),
                        ],
                    ],
                    'ce_product_accessories' => __('Product Accessories'),
                    'ce_product_footer' => __('Product Footer'),
                ] : [],
            ]
        );

        $this->endControlsSection();
    }

    protected function render()
    {
        if (!$block = $this->getSettings('block')) {
            return;
        }
        $product = $GLOBALS['smarty']->tpl_vars['product']->value;

        if (!$html = \CESmarty::get(_CE_TEMPLATES_ . 'front/theme/catalog/_partials/product-block.tpl', $block)) {
            return is_preview() ? print '<div class="elementor-alert elementor-alert-danger">Selected block is missing from your theme!</div>' : null;
        }
        // Fix: Restore original product
        $GLOBALS['smarty']->tpl_vars['product']->value = $product;

        echo trim($html);
    }

    public function renderPlainContent()
    {
    }
}
