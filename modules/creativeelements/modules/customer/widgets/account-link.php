<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks, Elementor
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 */
namespace CE;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ModulesXCustomerXWidgetsXAccountLink extends WidgetIconList
{
    const HELP_URL = '';

    private $properties;

    public function getName()
    {
        return 'account-link';
    }

    public function getTitle()
    {
        return isset($this->properties['title']) ? $this->properties['title'] : __('Account Link');
    }

    public function getIcon()
    {
        return 'eicon-link';
    }

    public function getCategories()
    {
        return ['customer-elements'];
    }

    public function getKeywords()
    {
        return isset($this->properties['keywords']) ? $this->properties['keywords'] : ['account', 'link'];
    }

    protected function isDynamicContent()
    {
        return true;
    }

    protected function registerControls()
    {
        parent::registerControls();

        $this->updateControl('section_icon', [
            'label' => $this->getTitle(),
        ]);

        $this->updateControl('view', [
            'default' => 'inline',
        ]);

        $this->updateControl('icon_list', [
            'item_actions' => [
                'duplicate' => false,
            ],
            'default' => isset($this->properties['icon_list_default']) ? $this->properties['icon_list_default'] : [],
        ]);

        $this->updateControl('section_icon_list', [
            'label' => $this->getTitle(),
        ]);

        $this->updateControl('divider_color', ['scheme' => '']);

        $this->updateControl('icon_color', ['scheme' => '']);

        $this->updateControl('icon_color_hover', [
            'selectors' => [
                '{{WRAPPER}} a:hover .elementor-icon-list-icon *' => 'color: {{VALUE}};',
            ],
        ]);

        $this->updateControl('text_color', [
            'scheme' => '',
            'selectors' => [
                '{{WRAPPER}} .elementor-icon-list-item' => 'color: {{VALUE}};',
            ],
        ]);

        $this->startInjection([
            'of' => 'text_color',
        ]);

        $this->addControl(
            'link_color',
            [
                'label' => __('Link Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a:not(#e)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'link_color_hover',
            [
                'label' => __('Link Hover Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a:not(#e):hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->endInjection();

        $this->removeControl('text_color_hover');

        $this->updateControl('icon_typography_font_family', ['scheme' => '']);
        $this->updateControl('icon_typography_font_weight', ['scheme' => '']);
    }

    protected function getHtmlWrapperClass()
    {
        return parent::getHtmlWrapperClass() . ' elementor-widget-icon-list';
    }

    public function __construct(array $data = [], $args = null, array $properties = [])
    {
        $this->properties = $properties;

        parent::__construct($data, $args);
    }
}
