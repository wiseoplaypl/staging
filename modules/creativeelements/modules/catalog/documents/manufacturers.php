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

use CE\ModulesXCatalogXWidgetsXManufacturers as Manufacturers;
use CE\ModulesXThemeXDocumentsXThemePageDocument as ThemePageDocument;

class ModulesXCatalogXDocumentsXManufacturers extends ThemePageDocument
{
    public function getName()
    {
        return 'page-manufacturers';
    }

    public static function getTitle()
    {
        return __('Brands Page');
    }

    protected static function getEditorPanelCategories()
    {
        return [
            'partner-elements' => [
                'title' => __('Brands', 'Shop.Theme.Catalog'),
            ],
        ] + parent::getEditorPanelCategories();
    }

    protected function getRemoteLibraryConfig()
    {
        $config = parent::getRemoteLibraryConfig();

        $config['category'] = 'brands';

        return $config;
    }

    protected function getPermalinkUrl($id_lang, $id_shop, array $args, $relative = true)
    {
        return Helper::$link->getPageLink('manufacturer', null, $id_lang, $args, false, $id_shop, $relative);
    }

    public static function registerWidgets($widgets_manager)
    {
        $widgets_manager->registerWidgetType(new Manufacturers());
    }

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        did_action('elementor/widgets/widgets_registered')
            ? static::registerWidgets(Plugin::$instance->widgets_manager)
            : add_action('elementor/widgets/widgets_registered', [static::class, 'registerWidgets']);
    }
}
