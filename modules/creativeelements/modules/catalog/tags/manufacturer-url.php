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

class ModulesXCatalogXTagsXManufacturerUrl extends DataTag
{
    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'manufacturer-url';
    }

    public function getTitle()
    {
        return __('Brand URL');
    }

    public function getGroup()
    {
        return TagsModule::CATALOG_GROUP;
    }

    public function getCategories()
    {
        return [TagsModule::URL_CATEGORY];
    }

    public function getValue(array $options = [])
    {
        $vars = &$GLOBALS['smarty']->tpl_vars;

        if (isset($vars['product_brand_url'])) {
            return $vars['product_brand_url']->value;
        }
        $product = $vars['product']->value;

        return $product['id_manufacturer'] ? Helper::$link->getSupplierLink($product['id_manufacturer'], null, $GLOBALS['language']->id) : '';
    }

    protected function getSmartyValue(array $options = [])
    {
        return
            '{if $product.id_manufacturer}' .
                '{call_user_func([$link, getManufacturerLink], $product.id_manufacturer, null, $language.id)}' .
            '{else}' .
                'javascript:;' .
            '{/if}';
    }
}
