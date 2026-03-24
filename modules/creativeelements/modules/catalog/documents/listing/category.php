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

use CE\ModulesXCatalogXControlsXSelectCategory as SelectCategory;
use CE\ModulesXCatalogXDocumentsXListingXPage as ListingPageDocument;
use CE\ModulesXCatalogXWidgetsXCategoryXAdditionalDescription as AdditionalDescription;
use CE\ModulesXCatalogXWidgetsXCategoryXList as CategoryList;
use CE\ModulesXCatalogXWidgetsXCategoryXSubcategories as Subcategories;
use CE\ModulesXCatalogXWidgetsXListingXDescription as ListingDescription;
use CE\ModulesXCatalogXWidgetsXListingXImage as ListingImage;
use CE\ModulesXThemeXWidgetsXPageTitle as PageTitle;

class ModulesXCatalogXDocumentsXListingXCategory extends ListingPageDocument
{
    public function getName()
    {
        return 'listing-category';
    }

    public static function getTitle()
    {
        return __('Category Page');
    }

    protected static function getEditorPanelCategories()
    {
        return [
            'listing-elements' => [
                'title' => __('Category'),
            ],
        ] + parent::getEditorPanelCategories();
    }

    protected function getRemoteLibraryConfig()
    {
        $config = parent::getRemoteLibraryConfig();

        $config['category'] = 'category';

        return $config;
    }

    public static function getTagClasses()
    {
        return array_merge(
            [
                'CategoryName',
                'CategoryUrl',
            ],
            parent::getTagClasses()
        );
    }

    protected function getPermalinkUrl($id_lang, $id_shop, array $args, $relative = true)
    {
        $settings = $this->getData('settings');
        $id_category = !empty($settings['id_category'])
            ? $settings['id_category']
            : \Configuration::get('PS_HOME_CATEGORY', null, null, $id_shop ?: null);

        return add_query_arg($args, Helper::$link->getCategoryLink($id_category, null, $id_lang, null, $id_shop, $relative));
    }

    protected function registerPreviewSettings()
    {
        $this->addControl(
            'id_category',
            [
                'label' => __('Category'),
                'type' => SelectCategory::CONTROL_TYPE,
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
        $widgets_manager->registerWidgetType(new Subcategories());

        parent::registerWidgets($widgets_manager);

        $widgets_manager->registerWidgetType(new PageTitle([], null, [
            'title' => __('Category Name'),
            'icon' => 'eicon-product-title',
            'categories' => ['listing-elements' => 1],
            'keywords' => ['heading', 'listing', 'page title'],
            'help_url' => 'https://docs.webshopworks.com/creative-elements/107-widgets/listing-widgets/416-category-name-widget',
            'dynamic_tag_name' => 'category-name',
        ]));
        $widgets_manager->registerWidgetType(new CategoryList([], null, [
            'title' => __('Subcategory List'),
            'categories' => ['listing-elements'],
            'help_url' => 'https://docs.webshopworks.com/creative-elements/107-widgets/listing-widgets/421-subcategory-list-widget',
        ]));
        $widgets_manager->registerWidgetType(new ListingImage([], null, [
            'title' => __('Category Image'),
            'keywords' => ['shop', 'store', 'image', 'picture', 'category', 'cover', 'thumbnail'],
            'help_url' => 'https://docs.webshopworks.com/creative-elements/107-widgets/listing-widgets/423-category-image-widget',
            'dynamic_tag_name' => 'category-image',
        ]));
        $widgets_manager->registerWidgetType(new ListingDescription([], null, [
            'title' => __('Category Description'),
            'keywords' => ['shop', 'store', 'text', 'description', 'category'],
            'help_url' => 'https://docs.webshopworks.com/creative-elements/107-widgets/listing-widgets/426-category-description-widget',
        ]));
        (int) _PS_VERSION_ < 8 || $widgets_manager->registerWidgetType(new AdditionalDescription());
    }
}
