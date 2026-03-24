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
use CE\ModulesXCustomerXWidgetsXLoginForm as LoginForm;
use CE\ModulesXThemeXDocumentsXThemePageDocument as ThemePageDocument;

class ModulesXCustomerXDocumentsXPageAuthentication extends ThemePageDocument
{
    public function getName()
    {
        return 'page-authentication';
    }

    public static function getTitle()
    {
        return __('Login Page');
    }

    protected function getRemoteLibraryConfig()
    {
        $config = parent::getRemoteLibraryConfig();

        $config['category'] = 'login';

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
        return Helper::$link->getPageLink('authentication', true, $id_lang, $args, false, $id_shop, $relative);
    }

    public static function registerWidgets($widgets_manager)
    {
        $widgets_manager->registerWidgetType(new LoginForm());
        $widgets_manager->registerWidgetType(new AccountLink([], null, [
            'title' => __('Register Link'),
            'keywords' => ['registration', 'sign up', 'link'],
            'icon_list_default' => [
                [
                    'text' => Helper::$translator->trans('No account?', [], 'Shop.Theme.Customeraccount'),
                    'selected_icon' => [
                        'value' => '',
                        'library' => '',
                    ],
                ],
                [
                    '__dynamic__' => [
                        'link' => Plugin::$instance->dynamic_tags->tagDataToTagText(null, 'internal-url', ['type' => 'registration']),
                    ],
                    'text' => Helper::$translator->trans('Create an account', [], 'Shop.Theme.Customeraccount'),
                    'selected_icon' => [
                        'value' => 'fas fa-user-plus',
                        'library' => 'fa-solid',
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
                'title' => __('Log in to your account', 'Shop.Theme.Customeraccount'),
                'url' => $vars['urls']->value['pages']['authentication'],
            ];
            $breadcrumb['count'] = 2;
        }
    }
}
