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

class ModulesXCatalogXTagsXCategoryImage extends DataTag
{
    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'category-image';
    }

    public function getTitle()
    {
        return __('Category Image');
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
            'id_category',
            [
                'label' => __('Category'),
                'label_block' => true,
                'type' => SelectCategory::CONTROL_TYPE,
                'select2options' => [
                    'allowClear' => false,
                ],
                'options' => [
                    '0' => __('Current'),
                ],
                'default' => 0,
                'classes' => 'ce-miniature--hidden',
            ]
        );

        $this->addControl(
            'image_size',
            [
                'label' => __('Image Size'),
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
    }

    public function getValue(array $options = [])
    {
        $id_image = 0;
        $value = ['url' => ''];
        $vars = &$GLOBALS['smarty']->tpl_vars;

        if (!$id_category = $this->getSettings('id_category')) {
            if ($GLOBALS['context']->controller instanceof \CategoryController && $vars['category']->value['image']) {
                // Category Page
                $id_image = $vars['category']->value['id'];
                $link_rewrite = $vars['category']->value['link_rewrite'];
                $value['alt'] = $vars['category']->value['name'];
            } elseif (!empty($vars['product']->value['id_category_default'])) {
                // Product Page or Product Miniature
                empty($vars['category']->value->id_image) || $id_image = $vars['category']->value->id_image;
                $id_category = $vars['product']->value['id_category_default'];
                $link_rewrite = $vars['product']->value['category'];
                $value['alt'] = $vars['product']->value['category_name'];
            }
        }
        if ($id_image || $id_category && file_exists(_PS_CAT_IMG_DIR_ . (int) $id_category . '.jpg') && $id_image = $id_category) {
            $image_size = $this->getSettings('image_size');
            $dimensions = GroupControlImageSize::getDimensions('categories', $image_size);
            $extension = $dimensions && strpos(\Configuration::get('PS_IMAGE_FORMAT'), 'webp') !== false ? 'webp' : 'jpg';
            $value['url'] = isset($link_rewrite)
                ? Helper::$link->getCatImageLink($link_rewrite, $id_image, $dimensions ? $image_size : '', $extension)
                : Helper::$link->getMediaLink(_THEME_CAT_DIR_ . $id_image . ($dimensions ? "-$image_size" : '') . ".$extension");
            empty($value['alt']) && $value['alt'] = __('Category');
            $dimensions && $value += $dimensions;
        }
        $value['loading'] = $this->getSettings('loading');

        return $value;
    }

    protected function getSmartyValue(array $options = [])
    {
        $image_size = $this->getSettings('image_size');
        $dimensions = GroupControlImageSize::getDimensions('categories', $image_size);
        $extension = $dimensions && strpos(\Configuration::get('PS_IMAGE_FORMAT'), 'webp') !== false ? 'webp' : 'jpg';
        $dimensions || $image_size = 'null';
        // $image_size = $dimensions ? "-$image_size" : 'null';

        return [
            'url' => '{*://*}' . // Tmp fix: Absolute URLs need to contain ://
                '{call_user_func([$link, getCatImageLink], $product.category, (' .
                    'file_exists($smarty.const._PS_CAT_IMG_DIR_|cat:$product.id_category_default|cat:substr(.1,1,1)|cat:jpg)' .
                ") ? \$product.id_category_default : \$language.iso_code, $image_size, $extension)}",
            'alt' => '{$product.category_name}',
            'loading' => $this->getSettings('loading'),
        ] + $dimensions;
    }
}
