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

use CE\ModulesXCatalogXWidgetsXSuppliers as Suppliers;
use CE\ModulesXThemeXDocumentsXThemePageDocument as ThemePageDocument;

class ModulesXCatalogXDocumentsXSuppliers extends ThemePageDocument
{
    public function getName()
    {
        return 'page-suppliers';
    }

    public static function getTitle()
    {
        return __('Suppliers Page');
    }

    protected static function getEditorPanelCategories()
    {
        return [
            'partner-elements' => [
                'title' => __('Suppliers', 'Shop.Theme.Catalog'),
            ],
        ] + parent::getEditorPanelCategories();
    }

    protected function getRemoteLibraryConfig()
    {
        $config = parent::getRemoteLibraryConfig();

        $config['category'] = 'suppliers';

        return $config;
    }

    protected function getPermalinkUrl($id_lang, $id_shop, array $args, $relative = true)
    {
        return Helper::$link->getPageLink('supplier', null, $id_lang, $args, false, $id_shop, $relative);
    }

    public static function registerWidgets($widgets_manager)
    {
        $widgets_manager->registerWidgetType(new Suppliers());
    }

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        did_action('elementor/widgets/widgets_registered')
            ? static::registerWidgets(Plugin::$instance->widgets_manager)
            : add_action('elementor/widgets/widgets_registered', [static::class, 'registerWidgets']);
    }
}
