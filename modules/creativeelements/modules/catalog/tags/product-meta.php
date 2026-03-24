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

use CE\CoreXDynamicTagsXTag as Tag;
use CE\ModulesXDynamicTagsXModule as TagsModule;

class ModulesXCatalogXTagsXProductMeta extends Tag
{
    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'product-meta';
    }

    public function getTitle()
    {
        return __('Product Meta');
    }

    public function getGroup()
    {
        return TagsModule::CATALOG_GROUP;
    }

    public function getCategories()
    {
        return [TagsModule::TEXT_CATEGORY];
    }

    public function getPanelTemplateSettingKey()
    {
        return 'type';
    }

    public static function getOptions()
    {
        return [
            'reference' => __('Reference', 'Admin.Catalog.Feature'),
            'quantity' => __('Quantity', 'Admin.Catalog.Feature'),
            'category' => __('Category', 'Admin.Global'),
            'manufacturer' => __('Brand', 'Admin.Global'),
            'availability_date' => __('Availability date', 'Admin.Catalog.Feature'),
            'delivery' => __('Delivery Time', 'Admin.Catalog.Feature'),
            'condition' => __('Condition', 'Admin.Catalog.Feature'),
            'references' => [
                'label' => __('Specific references', 'Admin.Catalog.Feature'),
                'options' => [
                    'isbn' => 'ISBN',
                    'ean13' => 'EAN-13 / JAN',
                    'upc' => 'UPC',
                    'mpn' => 'MPN',
                ],
            ],
            'supplier' => __('Supplier', 'Admin.Global'),
        ];
    }

    protected function registerControls()
    {
        $this->addControl(
            'type',
            [
                'label' => __('Field'),
                'type' => ControlsManager::SELECT,
                'groups' => _CE_ADMIN_ ? self::getOptions() : [],
                'default' => 'reference',
            ]
        );
    }

    public function render()
    {
        $vars = &$GLOBALS['smarty']->tpl_vars;
        $product = $vars['product']->value;
        $type = $this->getSettings('type');

        switch ($type) {
            case 'category':
                echo esc_html($product['category_name']);
                break;
            case 'manufacturer':
                if (isset($vars['product_manufacturer'])) {
                    echo esc_html($vars['product_manufacturer']->value->name);
                } elseif ($product['id_manufacturer']) {
                    echo esc_html(\Manufacturer::getNameById($product['id_manufacturer']));
                }
                break;
            case 'supplier':
                $product['id_supplier'] && print esc_html(\Supplier::getNameById($product['id_supplier']));
                break;
            case 'delivery':
                if (1 == $product['additional_delivery_times']) {
                    echo $product['delivery_information'];
                } elseif (2 == $product['additional_delivery_times']) {
                    if ($product['quantity'] > 0) {
                        echo $product['delivery_in_stock'];
                    } elseif ($product['add_to_cart_url']) {
                        echo $product['delivery_out_stock'];
                    }
                }
                break;
            case 'quantity':
                $product['show_quantities'] && print max(0, $product['quantity']) . ' ' . esc_html($product['quantity_label']);
                break;
            case 'condition':
                empty($product[$type]) || print esc_html($product[$type]['label']);
                break;
            case 'availability_date':
                echo esc_html(\Tools::displayDate($product[$type]));
                break;
            case 'reference':
                echo esc_html($product['reference_to_display']);
                break;
            default:
                // isbn, ean13, upc, mpn
                $attributes = $product['attributes'];
                $attribute = reset($attributes);

                if (!empty($attribute[$type])) {
                    echo esc_html($attribute[$type]);
                } elseif (!empty($product[$type])) {
                    echo esc_html($product[$type]);
                }
        }
    }

    protected function renderSmarty()
    {
        $type = $this->getSettings('type');

        switch ($type) {
            case 'category':
                echo '{$product.category_name}';
                break;
            case 'manufacturer':
                echo '{if $product.id_manufacturer}{Manufacturer::getNameById($product.id_manufacturer)}{/if}';
                break;
            case 'supplier':
                echo '{if $product.id_supplier}{Supplier::getNameById($product.id_supplier)}{/if}';
                break;
            case 'delivery':
                echo '{if 1 == $product.additional_delivery_times}{$product.delivery_information}';
                echo '{elseif 2 == $product.additional_delivery_times}{if $product.quantity > 0}{$product.delivery_in_stock}{elseif $product.add_to_cart_url}{$product.delivery_out_stock}{/if}';
                echo '{/if}';
                break;
            case 'quantity':
                echo '{if !empty($product.show_quantities)}{max(0, $product.quantity)} {$product.quantity_label}{/if}';
                break;
            case 'condition':
                echo '{if !empty($product.condition)}{$product.condition.label}{/if}';
                break;
            case 'availability_date':
                echo '{Tools::displayDate($product.availability_date)}';
                break;
            case 'reference':
                echo '{if !empty($product.reference_to_display)}{$product.reference_to_display}{/if}';
                break;
            default:
                // isbn, ean13, upc, mpn
                echo "{if !empty(\$product.$type)}{\$product.$type}{/if}";
        }
    }
}
