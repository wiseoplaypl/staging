<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2020 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class PstProductFilterSet extends ObjectModel
{
    public $name;
    public $filters = [];
    public $columns = [];
    public $order_by;
    public $order_way;
    public $admin_filter;

    public static $definition = [
        'table' => 'pstproductfilter_set',
        'primary' => 'id_pstproductfilter_set',
        'fields' => [
            // String
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true],
            'order_by' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'],
            'order_way' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'],
            'admin_filter' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
        ],
    ];

    public static function getByName($name)
    {
        $id_set = (int) Db::getInstance()->getValue(
            'SELECT `id_pstproductfilter_set` FROM `' . _DB_PREFIX_ . 'pstproductfilter_set`
             WHERE `name` = "' . pSQL($name) . '"'
        );

        $set = new PstProductFilterSet($id_set);
        $set->name = $name;

        return $set;
    }

    public function save($null_values = false, $autodate = true)
    {
        $saved = parent::save($null_values, $autodate);

        if ($saved) {
            // reset filters
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'pstproductfilter_set_filter`
                 WHERE `id_pstproductfilter_set` = ' . (int) $this->id
            );
            // reset columns
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'pstproductfilter_set_column`
                 WHERE `id_pstproductfilter_set` = ' . (int) $this->id
            );

            // save filters
            if (is_array($this->filters) && $this->filters) {
                foreach ($this->filters as $key => $filter) {
                    Db::getInstance()->execute(
                        'INSERT INTO `' . _DB_PREFIX_ . 'pstproductfilter_set_filter`
                         (`id_pstproductfilter_set`, `filter`, `active`, `position`, `value`, `strict`)
                         VALUES
                         (' . (int) $this->id . ', "' . pSQL($key) . '", ' . (int) $filter['active'] . ',
                          ' . (int) $filter['position'] . ', "' . pSQL($filter['value']) . '", ' . (int) $filter['strict'] . ')'
                    );
                }
            }
            // save columns
            if (is_array($this->columns) && $this->columns) {
                foreach ($this->columns as $key => $column) {
                    Db::getInstance()->execute(
                        'INSERT INTO `' . _DB_PREFIX_ . 'pstproductfilter_set_column`
                         (`id_pstproductfilter_set`, `column`, `active`, `position`)
                         VALUES
                         (' . (int) $this->id . ', "' . pSQL($key) . '", ' . (int) $column['active'] . ', ' . (int) $column['position'] . ')'
                    );
                }
            }
        }

        return $saved;
    }

    public function delete()
    {
        $result = parent::delete();

        if ($result) {
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'pstproductfilter_set_filter`
                 WHERE `id_pstproductfilter_set` = ' . (int) $this->id
            );
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'pstproductfilter_set_column`
                 WHERE `id_pstproductfilter_set` = ' . (int) $this->id
            );
        }

        return $result;
    }

    public function validateAllFields()
    {
        $errors = [];
        $module = Module::getInstanceByName('pstproductfilter');

        $valid = $this->validateFields(false, true);
        if ($valid !== true) {
            $errors[] = $valid . "\n";
        }
        $valid_lang = $this->validateFieldsLang(false, true);
        if ($valid_lang !== true) {
            $errors[] = $valid_lang . "\n";
        }
        $name_already_exists = Db::getInstance()->getValue(
            'SELECT `name` FROM `' . _DB_PREFIX_ . 'pstproductfilter_set`
             WHERE `id_pstproductfilter_set` != ' . (int) $this->id
        );
        if ($name_already_exists) {
            $errors[] = $module->l('Name must be unique');
        }

        return $errors;
    }

    public function validateField($field, $value, $id_lang = null, $skip = [], $human_errors = true)
    {
        return parent::validateField($field, $value, $id_lang, $skip, $human_errors);
    }

    public static function getAvailableSets()
    {
        $sets = self::getCollection('PstProductFilterSet');
        $sets->orderBy('name');

        return $sets;
    }

    public static function getCollection($class, $all_languages = false)
    {
        $context = Context::getContext();
        $id_lang = null;
        if (!$all_languages) {
            $id_lang = $context->language->id;
        }

        if (class_exists('PrestaShopCollection')) {
            $collection = new PrestaShopCollection($class, $id_lang);
        } else {
            $collection = new Collection($class, $id_lang);
        }

        return $collection;
    }

    public function getFilters()
    {
        $filters_raw = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'pstproductfilter_set_filter`
             WHERE `id_pstproductfilter_set` = ' . (int) $this->id
        );

        $module = Module::getInstanceByName('pstproductfilter');
        $initial_filters = $module->getFiltersRawData();
        $array_type_fields = $module->getArrayTypeFields();
        $result = [];
        foreach ($filters_raw as $row) {
            if (in_array($row['filter'], $array_type_fields)) {
                if ($row['value']) {
                    $tmp = explode(':', $row['value']);
                    $row['value'] = array_combine($tmp, $tmp); // make keys the same as values
                } else {
                    $row['value'] = [];
                }
            }
            $result[$row['filter']] = [
                'active' => $row['active'],
                'position' => $row['position'],
                'value' => $row['value'],
                'strict' => $row['strict'],
            ];
            if (isset($initial_filters[$row['filter']])) {
                $result[$row['filter']] = array_merge($initial_filters[$row['filter']], $result[$row['filter']]);
            }
        }

        return $result;
    }

    public function getColumns($only_pstpf = false)
    {
        $columns_raw = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'pstproductfilter_set_column`
             WHERE `id_pstproductfilter_set` = ' . (int) $this->id . '
             ORDER BY `position` ASC, `id_pstproductfilter_set_column` ASC'
        );

        $module = Module::getInstanceByName('pstproductfilter');
        $initial_columns = $module->getColumnsRawData($only_pstpf);
        $result = [];
        foreach ($columns_raw as $row) {
            if ($only_pstpf && !isset($initial_columns[$row['column']])) {
                continue;
            }

            $result[$row['column']] = [
                'active' => $row['active'],
            ];
            if (isset($initial_columns[$row['column']])) {
                $result[$row['column']] = array_merge($initial_columns[$row['column']], $result[$row['column']]);
            }
        }

        return $result;
    }
}
