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

use CE\ModulesXCatalogXControlsXSelectSupplier as SelectSupplier;
use CE\ModulesXCatalogXDocumentsXListingXPage as ListingPageDocument;
use CE\ModulesXCatalogXWidgetsXListingXDescription as ListingDescription;
use CE\ModulesXCatalogXWidgetsXListingXImage as ListingImage;
use CE\ModulesXThemeXWidgetsXPageTitle as PageTitle;

class ModulesXCatalogXDocumentsXListingXSupplier extends ListingPageDocument
{
    public function getName()
    {
        return 'listing-supplier';
    }

    public static function getTitle()
    {
        return __('Supplier Page');
    }

    protected static function getEditorPanelCategories()
    {
        return [
            'listing-elements' => [
                'title' => __('Supplier'),
            ],
        ] + parent::getEditorPanelCategories();
    }

    protected function getRemoteLibraryConfig()
    {
        $config = parent::getRemoteLibraryConfig();

        $config['category'] = 'supplier';

        return $config;
    }

    public static function getTagClasses()
    {
        $tag_classes = parent::getTagClasses();
        $tag_classes[] = 'SupplierName';

        return $tag_classes;
    }

    protected function getPermalinkUrl($id_lang, $id_shop, array $args, $relative = true)
    {
        $settings = $this->getData('settings');
        $id_supplier = !empty($settings['id_supplier'])
            ? $settings['id_supplier']
            : Helper::getLastUpdatedSupplierId();

        return add_query_arg($args, Helper::$link->getSupplierLink($id_supplier, null, $id_lang, $id_shop, $relative));
    }

    protected function registerPreviewSettings()
    {
        $this->addControl(
            'id_supplier',
            [
                'label' => __('Supplier'),
                'type' => SelectSupplier::CONTROL_TYPE,
                'label_block' => true,
                'select2options' => [
                    'allowClear' => false,
                ],
                'options' => [
                    '0' => __('Default'),
                ],
                'default' => 0,
                'export' => false,
            ]
        );
    }

    public static function registerWidgets($widgets_manager)
    {
        parent::registerWidgets($widgets_manager);

        $widgets_manager->registerWidgetType(new PageTitle([], null, [
            'title' => __('Supplier Name'),
            'icon' => 'eicon-product-title',
            'categories' => ['listing-elements' => 1],
            'keywords' => ['heading', 'listing', 'page title'],
            'help_url' => 'https://docs.webshopworks.com/creative-elements/107-widgets/listing-widgets/464-supplier-name-widget',
            'dynamic_tag_name' => 'supplier-name',
        ]));
        $widgets_manager->registerWidgetType(new ListingImage([], null, [
            'title' => __('Supplier Logo'),
            'icon' => 'eicon-logo',
            'keywords' => ['shop', 'store', 'supplier', 'image', 'picture'],
            'help_url' => 'https://docs.webshopworks.com/creative-elements/107-widgets/listing-widgets/463-supplier-logo-widget',
            'dynamic_tag_name' => 'supplier-image',
        ]));
        $widgets_manager->registerWidgetType(new ListingDescription([], null, [
            'title' => __('Supplier Description'),
            'keywords' => ['shop', 'store', 'text', 'description', 'supplier'],
            'help_url' => 'https://docs.webshopworks.com/creative-elements/107-widgets/listing-widgets/462-supplier-description-widget',
        ]));
    }
}
