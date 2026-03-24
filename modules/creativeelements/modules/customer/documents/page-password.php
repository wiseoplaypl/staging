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

use CE\ModulesXCustomerXWidgetsXAccountLink as AccountLink;
use CE\ModulesXCustomerXWidgetsXPasswordForm as PasswordForm;
use CE\ModulesXThemeXDocumentsXThemePageDocument as ThemePageDocument;

class ModulesXCustomerXDocumentsXPagePassword extends ThemePageDocument
{
    public function getName()
    {
        return 'page-password';
    }

    public static function getTitle()
    {
        return __('Password Page');
    }

    protected function getRemoteLibraryConfig()
    {
        $config = parent::getRemoteLibraryConfig();

        $config['category'] = 'password';

        return $config;
    }

    protected static function getEditorPanelCategories()
    {
        return [
            'customer-elements' => ['title' => __('Customer')],
        ] + parent::getEditorPanelCategories();
    }

    protected function getPermalinkUrl($id_lang, $id_shop, array $args, $relative = true)
    {
        return Helper::$link->getPageLink('password', true, $id_lang, $args, false, $id_shop, $relative);
    }

    public static function registerWidgets($widgets_manager)
    {
        $widgets_manager->registerWidgetType(new PasswordForm());
        $widgets_manager->registerWidgetType(new AccountLink([], null, [
            'title' => __('Back Link'),
            'keywords' => ['back', 'login', 'sign in', 'link'],
            'icon_list_default' => [
                [
                    '__dynamic__' => [
                        'link' => Plugin::$instance->dynamic_tags->tagDataToTagText(null, 'internal-url', ['type' => 'authentication']),
                    ],
                    'text' => Helper::$translator->trans('Back to Login', [], 'Shop.Theme.Actions'),
                    'selected_icon' => [
                        'value' => 'ceicon-chevron-left',
                        'library' => 'ce-icons',
                    ],
                ],
            ],
        ]));
    }

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        did_action('elementor/widgets/widgets_registered')
            ? static::registerWidgets(Plugin::$instance->widgets_manager)
            : add_action('elementor/widgets/widgets_registered', [static::class, 'registerWidgets']);

        if (!_CE_ADMIN_ && version_compare(_PS_VERSION_, '1.7.7', '<')) {
            // BC Fix for Breadcrumb & Page Title
            $vars = &$GLOBALS['smarty']->tpl_vars;
            $breadcrumb = &$vars['breadcrumb']->value;
            $breadcrumb['links'][1] = [
                'title' => __('Reset your password', 'Shop.Theme.Customeraccount'),
                'url' => $vars['urls']->value['pages']['password'],
            ];
            $breadcrumb['count'] = 2;
        }
    }
}
