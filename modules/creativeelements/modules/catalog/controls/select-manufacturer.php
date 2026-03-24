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

class ModulesXCatalogXControlsXSelectManufacturer extends ControlSelect2
{
    const CONTROL_TYPE = 'select_manufacturer';

    private static $_manufacturers;

    public function getType()
    {
        return self::CONTROL_TYPE;
    }

    public static function getManufacturers()
    {
        if (null === self::$_manufacturers) {
            self::$_manufacturers = [];
            $rows = \Db::getInstance()->executeS(
                'SELECT `id_manufacturer` AS `id`, `name` FROM ' . _DB_PREFIX_ . 'manufacturer WHERE `active` = 1 ORDER BY `name`'
            ) ?: [];

            foreach ($rows as &$row) {
                self::$_manufacturers[$row['id']] = "#$row[id] $row[name]";
            }
        }

        return self::$_manufacturers;
    }

    public function onImport($id_manufacturer, array $control_data)
    {
        return isset(self::$_manufacturers[$id_manufacturer]) ? $id_manufacturer : $control_data['default'];
    }

    public function contentTemplate()
    {
        $manufacturers = json_encode(self::getManufacturers());

        echo "<# data.options = $.extend( $manufacturers, data.options ) #>";

        parent::contentTemplate();
    }
}
