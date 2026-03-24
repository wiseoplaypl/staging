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

use CE\ModulesXCmsXControlsXSelectCategory as SelectCMSCategory;
use CE\ModulesXThemeXDocumentsXThemePageDocument as ThemePageDocument;
use CE\ModulesXThemeXWidgetsXPageTitle as PageTitle;

class ModulesXCmsXDocumentsXCategory extends ThemePageDocument
{
    public function getName()
    {
        return 'cms-category';
    }

    public static function getTitle()
    {
        return __('CMS Category');
    }

    protected static function getEditorPanelCategories()
    {
        return [
            'cms-elements' => ['title' => __('CMS Category')],
        ] + parent::getEditorPanelCategories();
    }

    protected function getRemoteLibraryConfig()
    {
        $config = parent::getRemoteLibraryConfig();

        $config['category'] = 'cms category';

        return $config;
    }

    protected function getPermalinkUrl($id_lang, $id_shop, array $args, $relative = true)
    {
        $settings = $this->getData('settings');
        $id_cms_category = !empty($settings['id_cms_category']) ? $settings['id_cms_category'] : 1;

        return add_query_arg($args, Helper::$link->getCMSCategoryLink($id_cms_category, null, $id_lang, $id_shop, $relative));
    }

    protected function registerControls()
    {
        parent::registerControls();

        $this->startControlsSection(
            'preview_settings',
            [
                'label' => __('Preview Settings'),
                'tab' => ControlsManager::TAB_SETTINGS,
            ]
        );

        $this->addControl(
            'id_cms_category',
            [
                'label' => __('CMS Category'),
                'type' => SelectCMSCategory::CONTROL_TYPE,
                'label_block' => true,
                'select2options' => [
                    'allowClear' => false,
                ],
                'default' => 1,
                'export' => false,
            ]
        );

        $this->addControl(
            'apply_preview',
            [
                'type' => ControlsManager::BUTTON,
                'text' => __('Apply & Preview'),
                'event' => 'ceThemeBuilder:ApplyPreview',
            ]
        );

        $this->endControlsSection();
    }

    public static function registerWidgets($widgets_manager)
    {
        $class_prefix = 'CE\ModulesXCmsXWidgetsX';
        $widgets_manager->registerWidgetType(new PageTitle([], null, [
            'title' => __('Category Name'),
            'categories' => ['cms-elements' => 1],
            'help_url' => '',
        ]));
        foreach ([
            'CategoryList',
            'CategoryDescription',
            'Pages',
        ] as $widget_class) {
            $widget_class = $class_prefix . $widget_class;

            $widgets_manager->registerWidgetType(new $widget_class());
        }
    }

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        did_action('elementor/widgets/widgets_registered')
            ? static::registerWidgets(Plugin::$instance->widgets_manager)
            : add_action('elementor/widgets/widgets_registered', [static::class, 'registerWidgets']);
    }
}
