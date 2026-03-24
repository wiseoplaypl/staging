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
use CE\ModulesXCatalogXControlsXSelectManufacturer as SelectManufacturer;
use CE\ModulesXDynamicTagsXModule as TagsModule;

class ModulesXCatalogXTagsXManufacturerImage extends DataTag
{
    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'manufacturer-image';
    }

    public function getTitle()
    {
        return __('Brand Logo');
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
            'id_manufacturer',
            [
                'label' => __('Brand'),
                'label_block' => true,
                'type' => SelectManufacturer::CONTROL_TYPE,
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
    }

    public function getValue(array $options = [])
    {
        $vars = &$GLOBALS['smarty']->tpl_vars;
        $value = ['url' => ''];

        if (!$id_manufacturer = $this->getSettings('id_manufacturer')) {
            if (!empty($vars['manufacturer']->value['id'])) {
                $id_manufacturer = $vars['manufacturer']->value['id'];
                $value['alt'] = $vars['manufacturer']->value['name'];
            } elseif (!empty($vars['product']->value['id_manufacturer'])) {
                $id_manufacturer = $vars['product']->value['id_manufacturer'];
                $value['alt'] = $vars['product']->value['manufacturer_name'];
            }
        }
        if ($id_manufacturer) {
            $image_size = $this->getSettings('image_size');
            $dimensions = GroupControlImageSize::getDimensions('manufacturers', $image_size);
            $extension = $dimensions && strpos(\Configuration::get('PS_IMAGE_FORMAT'), 'webp') !== false ? 'webp' : 'jpg';
            $value['url'] = Helper::$link->getManufacturerImageLink($id_manufacturer, $dimensions ? $image_size : '', $extension);
            empty($value['alt']) && $value['alt'] = __('logo', 'Shop.Theme.Global');
            $dimensions && $value += $dimensions;
        }
        $value['loading'] = $this->getSettings('loading');

        return $value;
    }

    protected function getSmartyValue(array $options = [])
    {
        $image_size = $this->getSettings('image_size');
        $dimensions = GroupControlImageSize::getDimensions('manufacturers', $image_size);
        $extension = $dimensions && strpos(\Configuration::get('PS_IMAGE_FORMAT'), 'webp') !== false ? 'webp' : 'jpg';
        $dimensions || $image_size = 'null';

        return [
            'url' => '{*://*}' . // Tmp fix: Absolute URLs need to contain ://
                '{if $product.id_manufacturer}' .
                    "{call_user_func([\$link, getManufacturerImageLink], \$product.id_manufacturer, $image_size, $extension)}" .
                '{else}' .
                    'data:image/svg+xml;base64,PHN2ZyB4bWxucz0naHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmcnIGhlaWdodD0nMCcvPg' .
                '{/if}',
            'alt' => (int) _PS_VERSION_ < 8 ? __('logo', 'Shop.Theme.Global') : '{$product.manufacturer_name}',
            'loading' => $this->getSettings('loading'),
        ] + $dimensions;
    }
}
