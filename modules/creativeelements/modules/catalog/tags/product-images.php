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

class ModulesXCatalogXTagsXProductImages extends DataTag
{
    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'product-images';
    }

    public function getTitle()
    {
        return __('Product Images');
    }

    public function getGroup()
    {
        return TagsModule::CATALOG_GROUP;
    }

    public function getCategories()
    {
        return [TagsModule::GALLERY_CATEGORY];
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
            ]
        );

        $this->addControl(
            'image_size',
            [
                'label' => __('Image Size'),
                'label_block' => true,
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

        $this->addControl(
            'caption',
            [
                'label' => __('Caption'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('None'),
                    'caption' => __('Caption'),
                    'custom' => __('Custom'),
                ],
                'default' => 'caption',
            ]
        );

        $this->addControl(
            'caption_text',
            [
                'show_label' => false,
                'label_block' => true,
                'type' => ControlsManager::TEXT,
                'placeholder' => __('Enter your image caption'),
                'condition' => [
                    'caption' => 'custom',
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
            'start',
            [
                'label' => __('Start'),
                'type' => ControlsManager::NUMBER,
                'min' => 1,
                'placeholder' => 1,
            ]
        );

        $this->addControl(
            'limit',
            [
                'label' => __('Limit'),
                'type' => ControlsManager::NUMBER,
                'min' => 1,
                'placeholder' => __('None'),
            ]
        );

        $this->endControlsSection();
    }

    public function getValue(array $options = [])
    {
        $id_int = $this->getIdInt();
        $settings = $this->getSettings();
        $caption = $settings['caption'];
        $items = [];

        if (!$settings['id_product'] && $GLOBALS['context']->controller instanceof \ProductController) {
            $images = $GLOBALS['smarty']->tpl_vars['product']->value['images'];
        } else {
            $product = new \Product($settings['id_product'], false, $GLOBALS['language']->id);
            $imageRetriever = new \PrestaShop\PrestaShop\Adapter\Image\ImageRetriever(Helper::$link);
            $settings['id_product_attribute'] = $product->cache_default_attribute;
            $images = $imageRetriever->getProductImages($settings, $GLOBALS['language']);
        }

        if ($settings['start'] || $settings['limit']) {
            $images = array_slice($images, ($settings['start'] ?: 1) - 1, $settings['limit'] ?: null);
        }

        foreach ($images as &$image) {
            $bySize = $image['bySize'][$settings['image_size']];
            $items[] = [
                'image' => [
                    'url' => isset($bySize['sources']['webp']) ? $bySize['sources']['webp'] : $bySize['url'],
                    'alt' => $image['legend'],
                    'width' => $bySize['width'],
                    'height' => $bySize['height'],
                    'loading' => $settings['loading'],
                ],
                'link' => [
                    'url' => Helper::getProductImageLink($image),
                    'custom_attributes' => 'data-elementor-open-lightbox|yes,data-elementor-lightbox-slideshow|g-' . (int) $id_int,
                ],
                'caption' => 'caption' === $caption ? $image['legend'] : (
                    'custom' === $caption ? $settings['caption_text'] : ''
                ),
                'description' => '',
            ];
        }

        return $items;
    }
}
