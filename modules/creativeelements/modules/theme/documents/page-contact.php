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

use CE\ModulesXThemeXDocumentsXThemePageDocument as ThemePageDocument;

class ModulesXThemeXDocumentsXPageContact extends ThemePageDocument
{
    public function getName()
    {
        return 'page-contact';
    }

    public static function getTitle()
    {
        return __('Contact Page');
    }

    protected static function getEditorPanelCategories()
    {
        return [
            'contact-elements' => [
                'title' => !_CE_ADMIN_ ?: __('Contact', 'Admin.Navigation.Menu'),
            ],
        ] + parent::getEditorPanelCategories();
    }

    protected function getPermalinkUrl($id_lang, $id_shop, array $args, $relative = true)
    {
        return Helper::$link->getPageLink('contact', null, $id_lang, $args, false, $id_shop, $relative);
    }

    protected function getRemoteLibraryConfig()
    {
        $config = parent::getRemoteLibraryConfig();

        $config['category'] = '';
        $config['type'] = 'page';
        $config['default_route'] = 'templates/pages';

        return $config;
    }
}
