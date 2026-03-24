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

class ModulesXCatalogXWidgetsXProductXName extends WidgetHeading
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/89-widgets/product-widgets/365-product-name-widget';

    public function getName()
    {
        return 'product-name';
    }

    public function getTitle()
    {
        return __('Product Name');
    }

    public function getIcon()
    {
        return 'eicon-product-title';
    }

    public function getCategories()
    {
        return ['product-elements'];
    }

    public function getKeywords()
    {
        return ['shop', 'store', 'title', 'name', 'heading', 'product'];
    }

    protected function isDynamicContent()
    {
        return true;
    }

    public function getStyleDepends()
    {
        return ['widget-product'];
    }

    protected function registerControls()
    {
        parent::registerControls();

        $this->updateControl('title', [
            'dynamic' => [
                'active' => true,
                'default' => Plugin::$instance->dynamic_tags->tagDataToTagText(null, 'product-name'),
            ],
            'default' => '',
        ]);

        $this->addControl(
            'link_to',
            [
                'label' => __('Link'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('None'),
                    'custom' => __('Product', 'Shop.Theme.Catalog'),
                ],
                'separator' => 'before',
            ],
            [
                'position' => ['of' => 'title'],
            ]
        );

        $this->updateControl('link', [
            'show_label' => false,
            'dynamic' => [
                'active' => true,
                'default' => Plugin::$instance->dynamic_tags->tagDataToTagText(null, 'product-url'),
            ],
            'separator' => '',
            'condition' => [
                'link_to!' => '',
            ],
        ]);

        $this->updateControl('header_size', ['default' => 'h1']);

        $this->updateControl('title_color', ['scheme' => '']);

        $this->startInjection([
            'type' => 'section',
            'at' => 'start',
            'of' => 'section_title_style',
        ]);

        $this->addControl(
            'title_multiline',
            [
                'label' => __('Allow Multiline'),
                'type' => ControlsManager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->addResponsiveControl(
            'line_clamp',
            [
                'label' => __('Max Lines'),
                'type' => ControlsManager::NUMBER,
                'min' => 1,
                'placeholder' => 'Auto',
                'selectors_dictionary' => [
                    '' => 'none',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ce-line-clamp: {{VALUE}}',
                ],
                'render_type' => 'template',
                'condition' => [
                    'title_multiline' => 'yes',
                ],
            ]
        );

        $this->addResponsiveControl(
            'min_height',
            [
                'label' => __('Min Height'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ce-product-name' => 'min-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endInjection();

        $this->updateControl('typography_font_family', ['scheme' => '']);
        $this->updateControl('typography_font_weight', ['scheme' => '']);
    }

    protected function getHtmlWrapperClass()
    {
        return parent::getHtmlWrapperClass() . ' elementor-widget-heading';
    }

    protected function render()
    {
        $settings = $this->getSettingsForDisplay();
        // Backward compatibility
        isset($settings['__dynamic__']) || $this->setSettings('__dynamic__', [
            'title' => Plugin::$instance->dynamic_tags->tagDataToTagText(null, 'product-name'),
            'link_to' => Plugin::$instance->dynamic_tags->tagDataToTagText(null, 'product-url'),
        ]);

        $this->addRenderAttribute('title', 'class', 'ce-product-name');
        $settings['title_multiline'] && !$settings['line_clamp'] && $this->addRenderAttribute('title', 'class', 'ce-product-name--full');

        parent::render();
    }

    protected function contentTemplate()
    {
        ?>
        <#
        settings.link.url = settings.link_to && settings.link.url;
        view.addRenderAttribute( 'title', 'class', 'ce-product-name' );
        settings.title_multiline && !settings.line_clamp && view.addRenderAttribute( 'title', 'class', 'ce-product-name--full' );
        #>
        <?php
        parent::contentTemplate();
    }

    public function renderPlainContent()
    {
    }
}
