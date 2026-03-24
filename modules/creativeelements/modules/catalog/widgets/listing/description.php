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

class ModulesXCatalogXWidgetsXListingXDescription extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/107-widgets/listing-widgets/478-listing-description-widget';

    const REMOTE_RENDER = true;

    private $properties;

    public function getName()
    {
        return 'listing-description';
    }

    public function getTitle()
    {
        return isset($this->properties['title']) ? $this->properties['title'] : __('Listing Description');
    }

    public function getIcon()
    {
        return 'eicon-text';
    }

    public function getHelpUrl()
    {
        return isset($this->properties['help_url']) ? $this->properties['help_url'] : static::HELP_URL;
    }

    public function getCategories()
    {
        return ['listing-elements'];
    }

    public function getKeywords()
    {
        return isset($this->properties['keywords'])
            ? $this->properties['keywords']
            : ['shop', 'store', 'text', 'category description', 'brand description', 'manufacturer description', 'supplier description'];
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_style',
            [
                'label' => __('Style'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'show_first',
            [
                'label' => __('Show'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('on All Pages'),
                    'yes' => __('on First Page Only'),
                ],
            ]
        );

        $this->addResponsiveControl(
            'text_align',
            [
                'label' => __('Alignment'),
                'type' => ControlsManager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            'text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-widget-container' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .elementor-widget-container',
            ]
        );

        $this->endControlsSection();
    }

    protected function getHtmlWrapperClass()
    {
        return parent::getHtmlWrapperClass() . ' elementor-widget-text-editor';
    }

    protected function render()
    {
        if ($this->getSettings('show_first') && (int) \Tools::getValue('page', 1) > 1) {
            return print '<!-- listing-description -->';
        }
        $vars = &$GLOBALS['smarty']->tpl_vars;

        if (isset($vars[$page = $vars['page']->value['page_name']])) {
            echo $vars[$page]->value['description'];
        }
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
