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

class ModulesXCmsXControlsXSelectCategory extends ControlSelect2
{
    const CONTROL_TYPE = 'select_cms_category';

    private static $_categories = [];

    public function getType()
    {
        return self::CONTROL_TYPE;
    }

    public static function getCategories()
    {
        if (_CE_ADMIN_ && !self::$_categories) {
            $id_lang = $GLOBALS['language']->id;
            $rows = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT c.`id_cms_category` id, cl.`name` FROM ' . _DB_PREFIX_ . 'cms_category c
                LEFT JOIN ' . _DB_PREFIX_ . 'cms_category_lang cl ON c.`id_cms_category` = cl.`id_cms_category`
                WHERE cl.`id_lang` = ' . (int) $id_lang . ' AND c.`active` = 1
                ORDER BY cl.`name`
            ');
            foreach ($rows as &$row) {
                self::$_categories[$row['id']] = "#$row[id] $row[name]";
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
