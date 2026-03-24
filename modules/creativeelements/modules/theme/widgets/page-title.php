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

class ModulesXThemeXWidgetsXPageTitle extends WidgetHeading
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/88-widgets/site-widgets/480-page-title-widget';

    private $properties;

    public function getName()
    {
        return 'page-title';
    }

    public function getTitle()
    {
        return isset($this->properties['title']) ? $this->properties['title'] : __('Page Title');
    }

    public function getIcon()
    {
        return isset($this->properties['icon']) ? $this->properties['icon'] : 'eicon-archive-title';
    }

    public function getHelpUrl()
    {
        return isset($this->properties['help_url']) ? $this->properties['help_url'] : static::HELP_URL;
    }

    public function getCategories()
    {
        return isset($this->properties['categories']) ? $this->properties['categories'] : [
            'theme-elements' => 9,
            'contact-elements' => 1,
            'partner-elements' => 1,
            'customer-elements' => 1,
            'listing-elements' => 1,
        ];
    }

    public function getKeywords()
    {
        return isset($this->properties['keywords']) ? $this->properties['keywords'] : ['page', 'title', 'name', 'heading'];
    }

    protected function getDynamicTagName()
    {
        return isset($this->properties['dynamic_tag_name']) ? $this->properties['dynamic_tag_name'] : 'page-title';
    }

    protected function isDynamicContent()
    {
        return true;
    }

    protected function registerControls()
    {
        parent::registerControls();

        $this->updateControl('title', [
            'dynamic' => [
                'active' => true,
                'default' => Plugin::$instance->dynamic_tags->tagDataToTagText(null, $this->getDynamicTagName()),
            ],
            'default' => '',
        ]);

        $this->updateControl('header_size', ['default' => 'h1']);

        $this->updateControl('title_color', ['scheme' => '']);

        $this->updateControl('typography_font_family', ['scheme' => '']);
        $this->updateControl('typography_font_weight', ['scheme' => '']);
    }

    protected function getHtmlWrapperClass()
    {
        return parent::getHtmlWrapperClass() . ' elementor-widget-heading';
    }

    public function renderPlainContent()
    {
    }

    public function __construct(array $data = [], $args = null, array $properties = [])
    {
        $this->properties = $properties;

        parent::__construct($data, $args);
    }
}
