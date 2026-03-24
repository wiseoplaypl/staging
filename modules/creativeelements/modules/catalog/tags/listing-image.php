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

class ModulesXCatalogXTagsXListingImage extends DataTag
{
    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'listing-image';
    }

    public function getTitle()
    {
        return __('Listing Image');
    }

    public function getGroup()
    {
        return TagsModule::CATALOG_GROUP;
    }

    public function getCategories()
    {
        return [TagsModule::IMAGE_CATEGORY];
    }

    protected function registerControls()
    {
        $display_suppliers = \Configuration::get('PS_DISPLAY_SUPPLIERS');
        $display_manufacturers = version_compare(_PS_VERSION_, '1.7.7', '<') ? $display_suppliers : \Configuration::get('PS_DISPLAY_MANUFACTURERS');

        $this->startControlsTabs('listing_tabs');

        $this->startControlsTab(
            'listing_tab_categoty',
            [
                'label' => __('Category'),
            ]
        );

        $this->addControl(
            'category_size',
            [
                'label' => __('Image Size'),
                'type' => ControlsManager::SELECT,
                'options' => GroupControlImageSize::getAllImageSizes('categories', true),
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'listing_tab_manuafacturer',
            [
                'label' => __('Brand'),
                'condition' => $display_manufacturers ? null : [
                    '_id' => '',
                ],
            ]
        );

        $this->addControl(
            'manufacturer_size',
            [
                'label' => __('Image Size'),
                'type' => ControlsManager::SELECT,
                'options' => GroupControlImageSize::getAllImageSizes('manufacturers', true),
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'listing_tab_supplier',
            [
                'label' => __('Supplier'),
                'condition' => $display_suppliers ? null : [
                    '_id' => '',
                ],
            ]
        );

        $this->addControl(
            'supplier_size',
            [
                'label' => __('Image Size'),
                'type' => ControlsManager::SELECT,
                'options' => GroupControlImageSize::getAllImageSizes('suppliers', true),
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();
    }

    public function getValue(array $options = [])
    {
        $vars = &$GLOBALS['smarty']->tpl_vars;
        $value = ['url' => ''];

        if (!empty($vars['category']->value['image'])) {
            $category = &$vars['category']->value;
            $size = $this->getSettings('category_size');
            isset($category['image']['bySize'][$size])
                ? $value = $category['image']['bySize'][$size]
                : $value['url'] = Helper::$link->getCatImageLink($category['link_rewrite'], $category['id'], $size);
            $value['alt'] = $category['name'];
        } elseif (!empty($vars['manufacturer']->value['id'])) {
            $manufacturer = &$vars['manufacturer']->value;
            $size = $this->getSettings('manufacturer_size');
            $value['url'] = Helper::$link->getManufacturerImageLink($manufacturer['id'], $size);
            $size && ($image_type = @array_column(\ImageType::getImagesTypes('manufacturers'), null, 'name')[$size]) && $value += [
                'width' => $image_type['width'],
                'height' => $image_type['height'],
            ];
            $value['alt'] = $manufacturer['name'];
        } elseif (!empty($vars['supplier']->value['id'])) {
            $supplier = &$vars['supplier']->value;
            $size = $this->getSettings('supplier_size');
            $value['url'] = Helper::$link->getSupplierImageLink($supplier['id'], $size);
            $size && ($image_type = @array_column(\ImageType::getImagesTypes('suppliers'), null, 'name')[$size]) && $value += [
                'width' => $image_type['width'],
                'height' => $image_type['height'],
            ];
            $value['alt'] = $supplier['name'];
        }

        return $value;
    }
}
