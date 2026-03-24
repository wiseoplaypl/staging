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

class ModulesXCatalogXControlsXSelectCategory extends ControlSelect2
{
    const CONTROL_TYPE = 'select_category';

    private static $_categories = [];

    public function getType()
    {
        return self::CONTROL_TYPE;
    }

    public static function getCategories()
    {
        if (_CE_ADMIN_ && !self::$_categories) {
            foreach (\Category::getAllCategoriesName($GLOBALS['context']->shop->getCategory(), $GLOBALS['language']->id) as &$category) {
                self::$_categories[$category['id_category']] = "#$category[id_category] $category[name]";
            }
        }

        return self::$_categories;
    }

    public function onImport($id_category, array $control_data)
    {
        return isset(self::$_categories[$id_category]) ? $id_category : $control_data['default'];
    }

    public function contentTemplate()
    {
        $categories = json_encode(self::getCategories());

        echo "<# data.options = $.extend( $categories, data.options ) #>";

        parent::contentTemplate();
    }
}
