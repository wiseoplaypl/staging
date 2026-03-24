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

class ModulesXCatalogXControlsXSelectSupplier extends ControlSelect2
{
    const CONTROL_TYPE = 'select_supplier';

    private static $_suppliers;

    public function getType()
    {
        return self::CONTROL_TYPE;
    }

    public static function getSuppliers()
    {
        if (null === self::$_suppliers) {
            self::$_suppliers = [];
            $rows = \Db::getInstance()->executeS(
                'SELECT `id_supplier` AS `id`, `name` FROM ' . _DB_PREFIX_ . 'supplier WHERE `active` = 1 ORDER BY `name`'
            ) ?: [];

            foreach ($rows as &$row) {
                self::$_suppliers[$row['id']] = "#$row[id] $row[name]";
            }
        }

        return self::$_suppliers;
    }

    public function onImport($id_supplier, array $control_data)
    {
        return isset(self::$_suppliers[$id_supplier]) ? $id_supplier : $control_data['default'];
    }

    public function contentTemplate()
    {
        $suppliers = json_encode(self::getSuppliers());

        echo "<# data.options = $.extend( $suppliers, data.options ) #>";

        parent::contentTemplate();
    }
}
