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

class ModulesXCatalogXTagsXProductQuickView extends DataTag
{
    public function getName()
    {
        return 'product-quick-view';
    }

    public function getTitle()
    {
        return __('Quick View');
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
    }

    public function getValue(array $options = [])
    {
        return Plugin::$instance->frontend->createActionHash('quickview', $this->getSettings());
    }
}
