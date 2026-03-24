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

use CE\CoreXDynamicTagsXTag as Tag;
use CE\ModulesXDynamicTagsXModule as TagsModule;

class ModulesXCatalogXTagsXManufacturerName extends Tag
{
    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'manufacturer-name';
    }

    public function getTitle()
    {
        return __('Brand Name');
    }

    public function getGroup()
    {
        return TagsModule::CATALOG_GROUP;
    }

    public function getCategories()
    {
        return [TagsModule::TEXT_CATEGORY];
    }

    public function render()
    {
        $vars = &$GLOBALS['smarty']->tpl_vars;

        if (!empty($vars['product_manufacturer']->value->name)) {
            echo $vars['product_manufacturer']->value->name;
        } elseif (!empty($vars['product']->value['id_manufacturer'])) {
            echo (int) _PS_VERSION_ < 8
                ? \Manufacturer::getNameById($vars['product']->value['id_manufacturer'])
                : $vars['product']->value['manufacturer_name'];
        } elseif (isset($vars['manufacturer'])) {
            echo $vars['manufacturer']->value['name'];
        }
    }

    protected function renderSmarty()
    {
        echo (int) _PS_VERSION_ < 8
            ? '{if $product.id_manufacturer}{Manufacturer::getNameById($product.id_manufacturer)}{/if}'
            : '{$product.manufacturer_name}';
    }
}
