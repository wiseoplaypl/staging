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

class ModulesXCatalogXWidgetsXListingXInfo extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/107-widgets/listing-widgets/431-listing-info-widget';

    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'listing-info';
    }

    public function getTitle()
    {
        return __('Listing Info');
    }

    public function getIcon()
    {
        return 'eicon-product-info';
    }

    public function getCategories()
    {
        return ['listing-elements'];
    }

    public function getKeywords()
    {
        return ['shop', 'store', 'listing', 'info', 'pagination', 'total', 'items', 'shown'];
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_listing_info',
            [
                'label' => __('Listing Info'),
            ]
        );

        $this->addControl(
            'info',
            [
                'label' => __('Info'),
                'type' => ControlsManager::SELECT,
                'groups' => [
                    'listing' => [
                        'label' => __('Listing'),
                        'options' => [
                            'label' => __('Label'),
                        ],
                    ],
                    'pagination' => [
                        'label' => __('Pagination'),
                        'options' => [
                            'total_items' => __('Total Items'),
                            'items_shown' => __('Items Shown'),
                        ],
                    ],
                ],
                'default' => 'total_items',
            ]
        );

        $this->addResponsiveControl(
            'align',
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
                    '{{WRAPPER}} .ce-listing-info' => 'text-align: {{VALUE}};',
                ],
                'style_transfer' => true,
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style',
            [
                'label' => __('Listing Info'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'text_color',
            [
                'label' => __('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            GroupControlTypography::getType(),
            [
                'name' => 'typography',
            ]
        );

        $this->addGroupControl(
            GroupControlTextShadow::getType(),
            [
                'name' => 'text_shadow',
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
        $listing = &$GLOBALS['smarty']->tpl_vars['listing']->value;
        $info = $this->getSettings('info');

        'label' !== $info && $this->addRenderAttribute('_wrapper', [
            'role' => 'status',
            'aria-live' => 'polite',
        ]);

        if ('items_shown' === $info && !$listing['products'] && empty(${'_GET'}['q'])) {
            return;
        }
        $pagination = &$listing['pagination'];

        echo '<div class="ce-listing-info">';
        if ('label' === $info) {
            echo $listing[$info];
        } elseif ('total_items' === $info) {
            _e(1 == $pagination['total_items'] ? 'There is 1 product.' : 'There are %product_count% products.', 'Shop.Theme.Catalog', [
                '%product_count%' => (int) $pagination['total_items'],
            ]);
        } elseif ('items_shown' === $info) {
            if (isset($_SERVER['HTTP_X_CE_PAGINATION'])) {
                $pagination['items_shown_from'] = 1;
                $pagination['items_shown_to'] = ($pagination['current_page'] - 1) * \Configuration::get('PS_PRODUCTS_PER_PAGE') + count($listing['products']);
            }
            _e('Showing %from%-%to% of %total% item(s)', 'Shop.Theme.Catalog', [
                '%from%' => $pagination['items_shown_from'],
                '%to%' => $pagination['items_shown_to'],
                '%total%' => $pagination['total_items'],
            ]);
        }
        echo '</div>';
    }

    public function renderPlainContent()
    {
    }
}
