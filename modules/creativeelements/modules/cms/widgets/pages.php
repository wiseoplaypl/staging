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

class ModulesXCmsXWidgetsXPages extends Articles
{
    const HELP_URL = '';

    public function getName()
    {
        return 'cms-pages';
    }

    public function getTitle()
    {
        return __('Pages');
    }

    public function getCategories()
    {
        return ['cms-elements'];
    }

    public function getKeywords()
    {
        return ['cms', 'pages', 'grid', 'item', 'loop', 'cards'];
    }

    protected function registerControls()
    {
        parent::registerControls();

        $this->updateControl('source', [
            'type' => ControlsManager::HIDDEN,
            'options' => null,
        ]);

        $this->updateControl('id_cms_category', [
            'type' => ControlsManager::HIDDEN,
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
