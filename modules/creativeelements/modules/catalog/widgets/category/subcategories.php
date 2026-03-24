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

class ModulesXCatalogXWidgetsXCategoryXSubcategories extends Articles
{
    const HELP_URL = '';

    public function getName()
    {
        return 'subcategories';
    }

    public function getTitle()
    {
        return __('Subcategories');
    }

    public function getCategories()
    {
        return ['listing-elements'];
    }

    public function getKeywords()
    {
        return ['catalog', 'subcategories', 'category', 'grid', 'item', 'loop', 'cards'];
    }

    protected function registerControls()
    {
        parent::registerControls();

        $this->updateControl('source', [
            'type' => ControlsManager::HIDDEN,
            'options' => null,
            'default' => 'category',
        ]);

        $this->updateControl('id_category', [
            'type' => ControlsManager::HIDDEN,
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
