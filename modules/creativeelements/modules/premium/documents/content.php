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

use CE\CoreXBaseXDocument as Document;
use CE\CoreXDocumentTypesXPost as Post;

class ModulesXPremiumXDocumentsXContent extends Document
{
    public static function getProperties()
    {
        $properties = parent::getProperties();

        $properties['support_kit'] = true;

        return $properties;
    }

    public function getName()
    {
        return 'content';
    }

    public static function getTitle()
    {
        return __('Content');
    }

    public function getCssWrapperSelector()
    {
        return '.elementor.elementor-' . uidval($this->getMainId())->toDefault();
    }

    protected function registerControls()
    {
        parent::registerControls();
        if (UId::parse($this->getMainId())->id_type === UId::CONTENT && $selector = \Configuration::get('elementor_page_wrapper_selector')) {
            $this->addControl(
                'full_width',
                [
                    'label' => __('Clear Content Wrapper'),
                    'type' => ControlsManager::SWITCHER,
                    'description' => sprintf(__(
                        'Not working? You can set a different selector for the content wrapper ' .
                        'in the <a href="%s" target="_blank">Settings page</a>.'
                    ), Helper::getSettingsLink(['subTab' => 'style'])),
                    'selectors' => [
                        $selector => 'min-width: 100%; margin: 0 !important; padding: 0 !important; background: inherit !important; color: inherit !important; font: inherit !important; box-shadow: none !important',
                    ],
                ],
                [
                    'position' => [
                        'of' => 'post_status',
                    ],
                ]
            );
        }

        Post::registerStyleControls($this);

        $this->updateControl(
            'section_page_style',
            [
                'label' => __('Style'),
            ]
        );
    }

    public static function unregisterNonMaintenanceWidgets($widgets_manager)
    {
        foreach ([
            // Premium
            'product-grid',
            'product-carousel',
            'product-box',
            'layer-slider',
            'contact-form',
            'email-subscription',
            'image-slider',
            'category-tree',
            'ps-widget-module',
            // Site
            'nav-menu',
            'user-menu',
            'shopping-cart',
            'ajax-search',
            'language-selector',
            'currency-selector',
            'breadcrumb',
        ] as $widget_type) {
            $widgets_manager->unregisterWidgetType($widget_type);
        }
    }

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        if (isset($this->post->_obj->hook) && !strcasecmp($this->post->_obj->hook, 'displayMaintenance')) {
            add_action('elementor/dynamic_tags/register_tags', function ($dynamic_tags) {
                $dynamic_tags->unregisterTag('page-title');
            });
            did_action('elementor/widgets/widgets_registered')
                ? static::unregisterNonMaintenanceWidgets(Plugin::$instance->widgets_manager)
                : add_action('elementor/widgets/widgets_registered', [static::class, 'unregisterNonMaintenanceWidgets'])
            ;
        }
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
