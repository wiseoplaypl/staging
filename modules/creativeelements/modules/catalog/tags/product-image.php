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

use CE\CoreXDynamicTagsXDataTag as DataTag;
use CE\ModulesXDynamicTagsXModule as TagsModule;

class ModulesXCatalogXTagsXProductImage extends DataTag
{
    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'product-image';
    }

    public function getTitle()
    {
        return __('Product Image');
    }

    public function getGroup()
    {
        return TagsModule::CATALOG_GROUP;
    }

    public function getCategories()
    {
        return [TagsModule::IMAGE_CATEGORY];
    }

    public function getPanelTemplateSettingKey()
    {
        return 'image_size';
    }

    protected function registerControls()
    {
        $this->addControl(
            'id_product',
            [
                'label' => __('Product', 'Shop.Theme.Catalog'),
                'type' => ControlsManager::SELECT2,
                'label_block' => true,
                'select2options' => [
                    'placeholder' => __('Current Product'),
                    'ajax' => [
                        'get' => 'Products',
                        'url' => Helper::getAjaxProductsListLink(),
                    ],
                ],
                'classes' => 'ce-miniature--hidden',
            ]
        );

        $index_options = range(0, 10);
        $index_options[0] = __('Cover');

        $this->addControl(
            'image_index',
            [
                'label' => __('Image'),
                'type' => ControlsManager::SELECT2,
                'select2options' => [
                    'tags' => true,
                    'allowClear' => false,
                ],
                'options' => &$index_options,
                'default' => '0',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'id_product',
                            'operator' => '<',
                            'value' => 1,
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'image_size',
            [
                'label' => __('Image Size'),
                'type' => ControlsManager::SELECT,
                'options' => $size_options = GroupControlImageSize::getAllImageSizes('products'),
                'default' => key($size_options),
            ]
        );

        $this->addControl(
            'loading',
            [
                'label' => __('Loading'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Lazy'),
                    'eager' => __('Eager'),
                ],
            ]
        );
    }

    protected function registerAdvancedSection()
    {
        $this->startControlsSection(
            'advanced',
            [
                'label' => __('Advanced'),
            ]
        );

        $this->addControl(
            'fallback_image',
            [
                'label' => __('Fallback'),
                'type' => ControlsManager::MEDIA,
            ]
        );

        $this->endControlsSection();
    }

    public function getValue(array $options = [])
    {
        $settings = $this->getSettings();
        $size = $settings['image_size'];

        if ($settings['id_product']) {
            if (!$cover = \Product::getCover($settings['id_product'])) {
                return $settings['fallback_image'];
            }
            $dimensions = GroupControlImageSize::getDimensions('products', $size);
            $extension = $dimensions && strpos(\Configuration::get('PS_IMAGE_FORMAT'), 'webp') !== false ? 'webp' : 'jpg';

            return [
                'url' => Helper::$link->getMediaLink(Helper::getProductImageLink($cover, $dimensions ? $size : '', $extension)),
                'alt' => '',
            ] + $dimensions;
        }

        $vars = &$GLOBALS['smarty']->tpl_vars;

        if (empty($vars['product']->value['images'])) {
            return $settings['fallback_image'];
        }
        $product = $vars['product']->value;
        $index = $settings['image_index'] - 1;

        if ($index < 0 && $product['cover']) {
            $image = $product['cover'];
        } elseif (isset($product['images'][$index])) {
            $image = $product['images'][$index];
        } else {
            return $settings['fallback_image'];
        }
        $imageBySize = $image['bySize'][$size];

        return [
            'url' => isset($imageBySize['sources']['webp']) ? $imageBySize['sources']['webp'] : $imageBySize['url'],
            'alt' => $image['legend'],
            'width' => $imageBySize['width'],
            'height' => $imageBySize['height'],
            'loading' => $settings['loading'],
        ];
    }

    protected function getSmartyValue(array $options = [])
    {
        $settings = $this->getSettings();
        $fi = $settings['fallback_image'];
        $size = $settings['image_size'];
        $index = (int) $settings['image_index'] - 1;

        if ($index < 0) {
            return [
                // Tmp fix: Absolute URLs need to contain "://"
                'url' => '{*://*}' .
                    "{if \$product.cover}{(isset(\$product.cover.bySize.$size.sources.webp)) ? \$product.cover.bySize.$size.sources.webp : \$product.cover.bySize.$size.url}" . (empty($fi['url']) ? '' :
                    '{else}{Tools::getShopProtocol()}{Tools::getMediaServer($product.id_product)}' .
                        '{$smarty.const.__PS_BASE_URI__}' . $fi['url']) .
                    '{/if}',
                'alt' => '{if $product.cover}{$product.cover.legend}{/if}',
                'width' => "{if \$product.cover}{\$product.cover.bySize.$size.width}" . (empty($fi['width']) ? '' : '{else}' . (int) $fi['width']) . '{/if}',
                'height' => "{if \$product.cover}{\$product.cover.bySize.$size.height}" . (empty($fi['height']) ? '' : '{else}' . (int) $fi['height']) . '{/if}',
                'loading' => $settings['loading'],
            ];
        }

        return [
            // Tmp fix: Absolute URLs need to contain "://"
            'url' => "{*://*}{\$pi = (isset(\$product.images.$index)) ? \$product.images.$index : []}" .
                "{if \$pi}{(isset(\$pi.bySize.$size.sources.webp)) ? \$pi.bySize.$size.sources.webp : \$pi.bySize.$size.url}" . (!empty($fi['url']) ?
                '{else}{Tools::getShopProtocol()}{Tools::getMediaServer($product.id_product)}' .
                    '{$smarty.const.__PS_BASE_URI__}' . $fi['url'] : '') .
                '{/if}',
            'alt' => '{if $pi}{$pi.legend}{/if}',
            'width' => "{if \$pi}{\$pi.bySize.$size.width}" . (empty($fi['width']) ? '' : '{else}' . (int) $fi['width']) . '{/if}',
            'height' => "{if \$pi}{\$pi.bySize.$size.height}" . (empty($fi['height']) ? '' : '{else}' . (int) $fi['height']) . '{/if}',
            'loading' => $settings['loading'],
        ];
    }
}
