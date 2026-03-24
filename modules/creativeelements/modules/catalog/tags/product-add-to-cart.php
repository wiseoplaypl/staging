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

use CE\CoreXDynamicTagsXDataTag as DataTag;
use CE\ModulesXDynamicTagsXModule as TagsModule;

class ModulesXCatalogXTagsXProductAddToCart extends DataTag
{
    const ACTION = 'addToCart';

    public function getName()
    {
        return 'product-add-to-cart';
    }

    public function getTitle()
    {
        return __('Add to Cart');
    }

    public function getGroup()
    {
        return TagsModule::ACTION_GROUP;
    }

    public function getCategories()
    {
        return [TagsModule::URL_CATEGORY];
    }

    protected function registerControls()
    {
        $this->addControl(
            'id_product',
            [
                'label' => __('Product', 'Shop.Theme.Catalog'),
                'type' => ControlsManager::SELECT2,
                'label_block' => true,
                'select2options' => [
                    'placeholder' => __('Current Product'),
                    'ajax' => [
                        'get' => 'Products',
                        'url' => Helper::getAjaxProductsListLink(),
                    ],
                ],
            ]
        );

        $this->addControl(
            'qty',
            [
                'label' => __('Product Quantity'),
                'type' => ControlsManager::NUMBER,
                'placeholder' => 1,
                'min' => 1,
                'condition' => [
                    'id_product!' => '',
                ],
            ]
        );
    }

    public function getValue(array $options = [])
    {
        return Plugin::$instance->frontend->createActionHash(static::ACTION, $this->getSettings());
    }

    public function getSmartyValue(array $options = [])
    {
        $this->getSettings('id_product') || $this->setSettings('qty', '{$product.minimal_quantity}');

        return str_replace('{"', '{literal}{"{/literal}', $this->getValue($options));
    }
}
