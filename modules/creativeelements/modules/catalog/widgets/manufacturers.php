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

use CE\ModulesXPremiumXWidgetsXArticles as Articles;

class ModulesXCatalogXWidgetsXManufacturers extends Articles
{
    const HELP_URL = '';

    public function getName()
    {
        return 'manufacturers';
    }

    public function getTitle()
    {
        return __('Brands', 'Shop.Theme.Catalog');
    }

    public function getCategories()
    {
        return ['partner-elements'];
    }

    public function getKeywords()
    {
        return ['catalog', 'manufacturers', 'brands', 'grid', 'item', 'loop', 'cards'];
    }

    protected function registerControls()
    {
        parent::registerControls();

        $this->updateControl('source', [
            'type' => ControlsManager::HIDDEN,
            'options' => null,
            'default' => 'manufacturer',
        ]);

        $this->updateControl('order_by', [
            'options' => [
                '' => __('Default'),
                'title' => __('Title'),
                'id' => __('Date'),
                'rand' => __('Random'),
            ],
        ]);

        $this->updateControl('heading', [
            'type' => ControlsManager::HIDDEN,
        ]);

        $this->updateControl('subtitle', [
            'options' => _CE_ADMIN_ ? [
                '' => __('None'),
                'nb_products' => __('Counter'),
                'meta_title' => __('Meta title', 'Admin.Global'),
                'meta_description' => __('Meta description', 'Admin.Global'),
                'custom' => __('Custom'),
            ] : [],
            'default' => 'nb_products',
        ]);

        $this->updateResponsiveControl('image_object_fit', [
            'default' => 'contain',
        ]);

        // Clear parent schemes
        $this->updateControl('title_color', ['scheme' => '']);

        $this->updateControl('title_typography_font_family', ['scheme' => '']);
        $this->updateControl('title_typography_font_weight', ['scheme' => '']);

        $this->updateControl('subtitle_typography_font_family', ['scheme' => '']);
        $this->updateControl('subtitle_typography_font_weight', ['scheme' => '']);

        $this->updateControl('excerpt_typography_font_family', ['scheme' => '']);
        $this->updateControl('excerpt_typography_font_weight', ['scheme' => '']);

        $this->updateControl('cta_color', ['scheme' => '']);

        $this->updateControl('cta_typography_font_family', ['scheme' => '']);
        $this->updateControl('cta_typography_font_weight', ['scheme' => '']);

        $this->updateControl('empty_color', ['scheme' => '']);

        $this->updateControl('empty_typography_font_family', ['scheme' => '']);
        $this->updateControl('empty_typography_font_weight', ['scheme' => '']);
    }

    protected function getHtmlWrapperClass()
    {
        return parent::getHtmlWrapperClass() . ' elementor-widget-articles';
    }
}
