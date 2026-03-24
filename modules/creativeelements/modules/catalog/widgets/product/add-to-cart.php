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

class ModulesXCatalogXWidgetsXProductXAddToCart extends WidgetButton
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/89-widgets/product-widgets/378-add-to-cart-widget';

    const REMOTE_RENDER = true;

    protected $inlineEditing = true;

    public function getName()
    {
        return 'product-add-to-cart';
    }

    public function getTitle()
    {
        return __('Add to Cart');
    }

    public function getIcon()
    {
        return 'eicon-product-add-to-cart';
    }

    public function getCategories()
    {
        return ['product-elements'];
    }

    public function getKeywords()
    {
        return ['shop', 'store', 'product', 'button', 'add to cart', 'buy now'];
    }

    protected function isDynamicContent()
    {
        return true;
    }

    protected function registerControls()
    {
        parent::registerControls();

        $this->updateControl(
            'button_type',
            [
                'label' => !_CE_ADMIN_ ?: __('Action', 'Admin.Global'),
                'options' => [
                    'add-to-cart' => __('Add to Cart'),
                    'buy-now' => __('Buy Now'),
                    'full-details' => __('View Full Details'),
                ],
                'default' => 'add-to-cart',
                'render_type' => 'template',
                'prefix_class' => null,
                'style_transfer' => false,
            ]
        );

        $this->addControl(
            'type',
            [
                'label' => __('Type'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Default'),
                    'primary' => __('Primary'),
                    'secondary' => __('Secondary'),
                ],
                'prefix_class' => 'elementor-button-',
                'style_transfer' => true,
            ],
            [
                'position' => [
                    'of' => 'button_type',
                ],
            ]
        );

        $this->updateControl(
            'link',
            [
                'type' => ControlsManager::HIDDEN,
                'default' => '',
            ]
        );

        $this->updateControl(
            'text',
            [
                'default' => '',
                'placeholder' => __('Default'),
            ]
        );

        $this->updateControl(
            'selected_icon',
            [
                'default' => [
                    'value' => 'ceicon-basket-solid',
                    'library' => 'ce-icons',
                ],
                'recommended' => [
                    'ce-icons' => [
                        'cart-light',
                        'cart-medium',
                        'cart-solid',
                        'trolley-light',
                        'trolley-medium',
                        'trolley-solid',
                        'trolley-bold',
                        'basket-light',
                        'basket-medium',
                        'basket-solid',
                        'bag-light',
                        'bag-medium',
                        'bag-solid',
                        'bag-rounded-o',
                        'bag-rounded',
                        'bag-trapeze-o',
                        'bag-trapeze',
                    ],
                    'fa-solid' => [
                        'bag-shopping',
                        'basket-shopping',
                        'cart-shopping',
                        'cart-plus',
                    ],
                ],
            ]
        );

        $this->removeControl('button_css_id');

        $this->updateControl('background_color', ['scheme' => '']);

        $this->updateControl('typography_font_family', ['scheme' => '']);
        $this->updateControl('typography_font_weight', ['scheme' => '']);

        $this->startInjection([
            'at' => 'before',
            'of' => 'border_border',
        ]);

        $tab_args = [
            'tabs_wrapper' => 'tabs_button_style',
            'inner_tab' => 'tab_button_disabled',
            'condition' => [
                'button_type!' => 'full-details',
            ],
        ];

        $this->addControl(
            $tab_args['inner_tab'],
            [
                'label' => __('Disabled'),
                'type' => ControlsManager::TAB,
                'tabs_wrapper' => $tab_args['tabs_wrapper'],
                'condition' => $tab_args['condition'],
            ]
        );

        $this->addControl(
            'disabled_color',
            [
                'label' => __('Text Color'),
                'inner_tab' => 'tab_button_disabled',
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button[aria-disabled]:not(#e)' => 'color: {{VALUE}};',
                ],
            ] + $tab_args
        );

        $this->addControl(
            'disabled_background_color',
            [
                'label' => __('Background Color'),
                'type' => ControlsManager::COLOR,
                'default' => 'rgba(129,138,145,0.35)',
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button[aria-disabled]' => 'background-color: {{VALUE}};',
                ],
            ] + $tab_args
        );

        $this->addControl(
            'disabled_border_color',
            [
                'label' => __('Border Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button[aria-disabled]' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'border_border!' => '',
                ] + $tab_args['condition'],
            ] + $tab_args
        );

        $this->addControl(
            'disabled_cursor',
            [
                'label' => __('Cursor'),
                'label_block' => false,
                'type' => ControlsManager::CHOOSE,
                'options' => [
                    'default' => [
                        'icon' => 'fas fa-arrow-pointer',
                    ],
                    'not-allowed' => [
                        'icon' => 'eicon-ban',
                    ],
                ],
                'toggle' => false,
                'default' => 'not-allowed',
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button[aria-disabled]' => 'pointer-events: auto; cursor: {{VALUE}};',
                ],
            ] + $tab_args
        );

        $this->addControl(
            'disabled_opacity',
            [
                'label' => __('Opacity'),
                'type' => ControlsManager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} a.elementor-button[aria-disabled]' => 'opacity: {{SIZE}};',
                ],
            ] + $tab_args
        );

        $this->endInjection();
    }

    protected function getHtmlWrapperClass()
    {
        return parent::getHtmlWrapperClass() . ' elementor-widget-button';
    }

    protected function addInlineEditingAttributes($key, $toolbar = 'basic')
    {
        $this->inlineEditing && parent::addInlineEditingAttributes($key, $toolbar);
    }

    protected function render()
    {
        $vars = &$GLOBALS['smarty']->tpl_vars;
        $product = $vars['product']->value;
        $button_type = $this->getSettings('button_type');

        if ('full-details' === $button_type) {
            $this->setSettings('link', ['url' => $product['url']]);
        } elseif (!$product['available_for_order'] || $vars['configuration']->value['is_catalog']) {
            return;
        } else {
            $this->setSettings('link', []); // BC: Clear link
            $this->addRenderAttribute('button', 'href', '#ce-action=' . \Tools::toCamelCase($button_type));
            $product['add_to_cart_url'] || $this->addRenderAttribute('button', 'aria-disabled', 'true');
        }

        if ('' === $this->getSettings('text')) {
            $this->inlineEditing = false;
            $this->setSettings(
                'text',
                'add-to-cart' === $button_type ? __('Add to Cart') : (
                    'buy-now' === $button_type ? __('Buy Now') : __('View Full Details')
                )
            );
        }
        parent::render();
    }

    public function renderPlainContent()
    {
    }

    protected function contentTemplate()
    {
    }
}
