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

use CE\CoreXBaseXModule as BaseModule;
use CE\CoreXResponsiveXResponsive as Responsive;

class ModulesXThemeXModule extends BaseModule
{
    public function getName()
    {
        return 'theme';
    }

    public function registerDocuments($documents)
    {
        $class_prefix = 'CE\ModulesXThemeXDocumentsX';

        $documents->registerDocumentType('header', $class_prefix . 'Header');
        $documents->registerDocumentType('footer', $class_prefix . 'Footer');
        $documents->registerDocumentType('page-index', $class_prefix . 'PageIndex');
        $documents->registerDocumentType('page-contact', $class_prefix . 'PageContact');
        $documents->registerDocumentType('page-not-found', $class_prefix . 'PageNotFound');
    }

    public function registerWidgets($widgets_manager)
    {
        foreach ([
            'SiteLogo',
            'SiteTitle',
            'NavMenu',
            'SignIn',
            'ShoppingCart',
            'AjaxSearch',
            'LanguageSelector',
            'CurrencySelector',
            'SkipLinks',
            'Breadcrumb',
            'PageTitle',
        ] as $class_suffix) {
            $widget_class = 'CE\ModulesXThemeXWidgetsX' . $class_suffix;

            $widgets_manager->registerWidgetType(new $widget_class());
        }
    }

    public function registerStyles()
    {
        $has_custom_breakpoints = Responsive::hasCustomBreakpoints();
        $min = _PS_MODE_DEV_ ? '' : '.min';

        foreach ([
            'ajax-search',
            'shopping-cart',
            'skip-links',
        ] as $widget_name) {
            wp_register_style(
                "widget-$widget_name",
                _CE_ASSETS_URL_ . "css/widget-$widget_name$min.css",
                ['elementor-frontend'],
                _CE_VERSION_
            );
        }

        // Widgets with responsive styles
        wp_register_style(
            'widget-nav-menu',
            $this->getFrontendFileUrl("widget-nav-menu$min.css", $has_custom_breakpoints),
            ['elementor-frontend'],
            $has_custom_breakpoints ? null : _CE_VERSION_
        );
    }

    public function __construct()
    {
        add_action('elementor/documents/register', [$this, 'registerDocuments']);
        add_action('elementor/widgets/widgets_registered', [$this, 'registerWidgets']);
        add_action('elementor/frontend/after_register_styles', [$this, 'registerStyles']);

        $filter = 'filter';
        $GLOBALS['smarty']->registerPlugin('modifier', 'ce' . $filter, function ($str) {
            echo $str;
        });
    }
}
