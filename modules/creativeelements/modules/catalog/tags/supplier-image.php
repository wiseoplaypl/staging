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
use CE\ModulesXCatalogXControlsXSelectSupplier as SelectSupplier;
use CE\ModulesXDynamicTagsXModule as TagsModule;

class ModulesXCatalogXTagsXSupplierImage extends DataTag
{
    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'supplier-image';
    }

    public function getTitle()
    {
        return __('Supplier Logo');
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
            'id_supplier',
            [
                'label' => __('Supplier'),
                'label_block' => true,
                'type' => SelectSupplier::CONTROL_TYPE,
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
                'options' => GroupControlImageSize::getAllImageSizes('suppliers', true),
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

        if (!$id_supplier = $this->getSettings('id_supplier')) {
            if (!empty($vars['supplier']->value['id'])) {
                $id_supplier = $vars['supplier']->value['id'];
                $value['alt'] = $vars['supplier']->value['name'];
            } elseif (!empty($vars['product']->value['id_supplier'])) {
                $id_supplier = $vars['product']->value['id_supplier'];
            }
        }
        if ($id_supplier) {
            $image_size = $this->getSettings('image_size');
            $dimensions = GroupControlImageSize::getDimensions('suppliers', $image_size);
            $extension = $dimensions && strpos(\Configuration::get('PS_IMAGE_FORMAT'), 'webp') !== false ? 'webp' : 'jpg';
            $value['url'] = Helper::$link->getSupplierImageLink($id_supplier, $dimensions ? $image_size : '', $extension);
            empty($value['alt']) && $value['alt'] = __('logo', 'Shop.Theme.Global');
            $dimensions && $value += $dimensions;
        }
        $value['loading'] = $this->getSettings('loading');

        return $value;
    }

    protected function getSmartyValue(array $options = [])
    {
        $image_size = $this->getSettings('image_size');
        $dimensions = GroupControlImageSize::getDimensions('suppliers', $image_size);
        $extension = $dimensions && strpos(\Configuration::get('PS_IMAGE_FORMAT'), 'webp') !== false ? 'webp' : 'jpg';
        $dimensions || $image_size = 'null';

        return [
            'url' => '{*://*}' . // Tmp fix: Absolute URLs need to contain ://
                '{if $product.id_supplier}' .
                    "{call_user_func([\$link, getSupplierImageLink], \$product.id_supplier, $image_size, $extension)}" .
                '{else}' .
                    'data:image/svg+xml;base64,PHN2ZyB4bWxucz0naHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmcnIGhlaWdodD0nMCcvPg' .
                '{/if}',
            'alt' => __('logo', 'Shop.Theme.Global'),
            'loading' => $this->getSettings('loading'),
        ] + $dimensions;
    }
}
