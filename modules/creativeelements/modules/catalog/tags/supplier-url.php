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

class ModulesXCatalogXTagsXSupplierUrl extends DataTag
{
    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'supplier-url';
    }

    public function getTitle()
    {
        return __('Supplier URL');
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
        $product = $GLOBALS['smarty']->tpl_vars['product']->value;

        return $product['id_supplier'] ? Helper::$link->getSupplierLink($product['id_supplier'], null, $GLOBALS['language']->id) : '';
    }

    protected function getSmartyValue(array $options = [])
    {
        return
            '{if $product.id_supplier}' .
                '{call_user_func([$link, getSupplierLink], $product.id_supplier, null, $language.id)}' .
            '{else}' .
                'javascript:;' .
            '{/if}';
    }
}
