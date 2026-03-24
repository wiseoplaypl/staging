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
use CE\ModulesXCatalogXControlsXSelectCategory as SelectCategory;
use CE\ModulesXDynamicTagsXModule as TagsModule;

class ModulesXCatalogXTagsXCategoryImages extends DataTag
{
    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'category-images';
    }

    public function getTitle()
    {
        return __('Category Images');
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

    public static function getSubCategories($id_category, $caption, $description)
    {
        $controller = $GLOBALS['context']->controller;

        if ($id_category > 1) {
            $id = $id_category;
        } elseif ($controller instanceof \CategoryController) {
            if (!$id_category || $GLOBALS['smarty']->tpl_vars['subcategories']->value) {
                return $GLOBALS['smarty']->tpl_vars['subcategories']->value;
            }
            $id = $GLOBALS['smarty']->tpl_vars['category']->value['id_parent'];
        } elseif ($controller instanceof \ProductController) {
            $id = $controller->getProduct()->id_category_default;
        } else {
            $id = $GLOBALS['context']->shop->id_category;
        }

        if (array_intersect([$caption, $description], ['description', 'additional_description', 'meta_title', 'meta_description'])) {
            $category = new \Category();
            $category->id = $id;
            $subcategories = $category->getSubCategories($GLOBALS['language']->id);
        } else {
            $subcategories = \Category::getChildren($id, $GLOBALS['language']->id, true, $GLOBALS['context']->shop->id);
        }

        return $subcategories;
    }

    protected function registerControls()
    {
        $this->addControl(
            'id_category',
            [
                'label' => __('Category Root'),
                'label_block' => true,
                'type' => SelectCategory::CONTROL_TYPE,
                'select2options' => [
                    'allowClear' => false,
                ],
                'options' => [
                    '0' => __('Current Category') . ' / ' . __('Default'),
                    '1' => __('Current Category') . ' / ' . __('Parent Category'),
                ],
                'default' => 0,
            ]
        );

        $this->addControl(
            'image_size',
            [
                'label' => __('Image Size'),
                'label_block' => true,
                'type' => ControlsManager::SELECT,
                'options' => GroupControlImageSize::getAllImageSizes('categories', true),
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

        $options = _CE_ADMIN_ ? [
            '' => __('None'),
            'name' => __('Category Name'),
            'description' => __('Category Description'),
            'additional_description' => __('Additional Description'),
            'meta_title' => __('Meta title', 'Admin.Global'),
            'meta_description' => __('Meta description', 'Admin.Global'),
            'custom' => __('Custom'),
        ] : [];
        if ((int) _PS_VERSION_ < 8) {
            unset($options['additional_description']);
        }
        $this->addControl(
            'caption',
            [
                'label' => __('Caption'),
                'type' => ControlsManager::SELECT,
                'options' => $options,
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
        $settings = $this->getSettings();
        $cats = self::getSubCategories(
            $settings['id_category'],
            $caption = $settings['caption'],
            $description = $settings['description']
        );
        $dimensions = GroupControlImageSize::getDimensions('categories', $settings['image_size']);
        $extension = $dimensions && strpos(\Configuration::get('PS_IMAGE_FORMAT'), 'webp') !== false ? 'webp' : 'jpg';
        $items = [];

        foreach ($cats as &$cat) {
            isset($cat['id_image'])
                || $cat['id_image'] = \Tools::file_exists_cache(_PS_CAT_IMG_DIR_ . $cat['id_category'] . '.jpg') ? $cat['id_category'] : '';
            $items[] = [
                'image' => [
                    'url' => Helper::$link->getCatImageLink($cat['link_rewrite'], (int) $cat['id_image'] ?: $GLOBALS['language']->iso_code, $dimensions ? $settings['image_size'] : '', $extension),
                    'alt' => $cat['name'],
                    'loading' => $settings['loading'],
                ] + $dimensions,
                'link' => [
                    'url' => isset($cat['url']) ? $cat['url'] : Helper::$link->getCategoryLink($cat['id_category'], $cat['link_rewrite'], $GLOBALS['language']->id, null, $GLOBALS['context']->shop->id),
                ],
                'caption' => $caption ? (
                    'custom' === $caption ? $settings['caption_text'] : strip_tags($cat[$caption])
                ) : '',
                'description' => $description ? strip_tags(
                    'custom' === $description ? $settings['description_text'] : $cat[$description]
                ) : '',
            ];
        }

        return $items;
    }
}
