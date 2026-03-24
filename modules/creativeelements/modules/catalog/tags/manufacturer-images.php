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

class ModulesXCatalogXTagsXManufacturerImages extends DataTag
{
    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'manufacturer-images';
    }

    public function getTitle()
    {
        return __('Brand Images');
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
            'image_size',
            [
                'label' => __('Image Size'),
                'label_block' => true,
                'type' => ControlsManager::SELECT,
                'options' => GroupControlImageSize::getAllImageSizes('manufacturers', true),
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
                'options' => $options = [
                    '' => __('None'),
                    'name' => __('Brand Name'),
                    'short_description' => __('Short Description'),
                    'description' => __('Brand Description'),
                    'custom' => __('Custom'),
                ],
                'default' => 'name',
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

        $this->addControl(
            'description',
            [
                'label' => __('Description'),
                'type' => ControlsManager::SELECT,
                'options' => $options,
            ]
        );

        $this->addControl(
            'description_text',
            [
                'show_label' => false,
                'label_block' => true,
                'type' => ControlsManager::TEXTAREA,
                'rows' => 2,
                'placeholder' => __('Enter your description'),
                'condition' => [
                    'description' => 'custom',
                ],
            ]
        );
    }

    public function getValue(array $options = [])
    {
        $brands = \Manufacturer::getManufacturers(false, $GLOBALS['language']->id);
        $settings = $this->getSettings();
        $dimensions = GroupControlImageSize::getDimensions('manufacturers', $settings['image_size']);
        $extension = $dimensions && strpos(\Configuration::get('PS_IMAGE_FORMAT'), 'webp') !== false ? 'webp' : 'jpg';
        $caption = $settings['caption'];
        $description = $settings['description'];
        $items = [];

        foreach ($brands as &$brand) {
            $items[] = [
                'image' => [
                    'url' => Helper::$link->getManufacturerImageLink($brand['id_manufacturer'], $dimensions ? $settings['image_size'] : '', $extension),
                    'alt' => $brand['name'],
                    'loading' => $settings['loading'],
                ] + $dimensions,
                'link' => [
                    'url' => Helper::$link->getManufacturerLink($brand['id_manufacturer'], $brand['link_rewrite'], $GLOBALS['language']->id, $GLOBALS['context']->shop->id),
                ],
                'caption' => $caption ? (
                    'custom' === $caption ? $settings['caption_text'] : strip_tags($brand[$caption])
                ) : '',
                'description' => $description ? strip_tags(
                    'custom' === $description ? $settings['description_text'] : $brand[$description]
                ) : '',
            ];
        }

        return $items;
    }
}
