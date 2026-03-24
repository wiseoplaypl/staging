<?php
/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
 */

class NdkCfGroup extends ObjectModel
{
    public $products;
    public $categories;
    public $fields;
    public $name;
    public $mode;
    public $options;

    public static $definition = [
        'table' => 'ndk_customization_field_group',
        'primary' => 'id_ndk_customization_field_group',
        'multilang' => true,
        'fields' => [
            'products' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
            ],
            'mode' => [
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ],
            'categories' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
            ],
            'fields' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
            ],
            'name' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => false,
            ],
            'options' => ['type' => self::TYPE_STRING],
        ],
    ];

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }

    public function delete()
    {
        return parent::delete();
    }

    public static function getFieldsLight($current_group)
    {
        $id_lang = Context::getContext()->language->id;
        $sql_fields =
            'SELECT cfg.`fields` 
		FROM `' .
            _DB_PREFIX_ .
            'ndk_customization_field_group` cfg 
		WHERE cfg.id_ndk_customization_field_group != ' .
            (int) $current_group;

        $locked_fields = Db::getInstance()->executeS($sql_fields);
        $field_list = [];
        $field_list[] = (int) 0;
        foreach ($locked_fields as $row) {
            $row_f = explode(',', $row['fields']);
            foreach ($row_f as $f) {
                $field_list[] = (int) $f;
            }
        }

        $sql =
            '
			SELECT cf.`id_ndk_customization_field` as id, cfl.`admin_name` as name  
			FROM `' .
            _DB_PREFIX_ .
            'ndk_customization_field` cf 
			LEFT JOIN `' .
            _DB_PREFIX_ .
            'ndk_customization_field_lang` cfl ON (cfl.`id_ndk_customization_field`= cf.`id_ndk_customization_field` AND cfl.`id_lang` = ' .
            (int) $id_lang .
            ' ) 
			WHERE  cf.`id_ndk_customization_field` NOT IN (' .
            implode(',', $field_list) .
            ') 
			GROUP BY  cf.id_ndk_customization_field';

        $fields = Db::getInstance()->executeS($sql);
        return $fields;
    }

    public static function getProductsLight()
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        $sql =
            '
			SELECT p.`id_product` as id, CONCAT(\'#\', p.id_product,\' [\', cl.`name`, \'] \',  pl.`name`, \'  - ref: \' ,p.reference) as name  
			FROM `' .
            _DB_PREFIX_ .
            'product_shop` ps 
			LEFT JOIN `' .
            _DB_PREFIX_ .
            'product` p ON p.`id_product`= ps.`id_product`  
			LEFT JOIN `' .
            _DB_PREFIX_ .
            'product_lang` pl ON (pl.`id_product`= ps.`id_product` AND pl.`id_lang` = ' .
            (int) $id_lang .
            ' ) 
			LEFT JOIN `' .
            _DB_PREFIX_ .
            'category_lang` cl ON (cl.`id_category`= ps.`id_category_default` AND cl.`id_lang` = ' .
            (int) $id_lang .
            ' ) 
			WHERE p.supplier_reference NOT LIKE \'myndkcustomprodPack\' AND p.id_category_default !=' .
            (int) Configuration::get('NDK_ACF_CAT') .
            ' AND ps.`id_shop` = ' .
            (int) $id_shop .
            ' GROUP BY p.id_product ORDER BY cl.name, pl.name';

        $products = Db::getInstance()->executeS($sql);
        return $products;
    }

    public static function getIdByName($query)
    {
        if ($query == '') {
            return 0;
        }
        $id_lang = Context::getContext()->language->id;
        $fields = Db::getInstance()->getValue(
            '
				SELECT `id_ndk_customization_field_group` as id
				FROM `' .
                _DB_PREFIX_ .
                'ndk_customization_field_group_lang`
				WHERE name = "' .
                $query .
                '"'
        );
        return $fields;
    }

    public static function restoreTargets(
        $id_fields,
        $fields_mapping = [],
        $values_mapping = []
    ) {
        $fields = Db::getInstance()->executeS(
            'SELECT id_ndk_customization_field, influences , target, target_child 
        FROM ' .
                _DB_PREFIX_ .
                'ndk_customization_field 
        WHERE id_ndk_customization_field IN(' .
                implode(',', $id_fields) .
                ') AND target <> ""'
        );
        foreach ($fields as $field) {
            $req =
                'UPDATE ' .
                _DB_PREFIX_ .
                'ndk_customization_field 
            set target = ' .
                (int) $fields_mapping[$field['target']] .
                ', 
            target_child =  ' .
                (int) $values_mapping[$field['target_child']] .
                '
            WHERE id_ndk_customization_field = ' .
                (int) $field['id_ndk_customization_field'];
            Db::getInstance()->execute($req);
        }
    }
    public static function restoreInfluences(
        $id_fields,
        $fields_mapping = [],
        $values_mapping = []
    ) {
        $fields = Db::getInstance()->executeS(
            'SELECT id_ndk_customization_field, influences , target, target_child 
        FROM ' .
                _DB_PREFIX_ .
                'ndk_customization_field 
        WHERE id_ndk_customization_field IN(' .
                implode(',', $id_fields) .
                ') AND influences <> ""'
        );
        foreach ($fields as $field) {
            $old_ids = explode(',', $field['influences']);
            $new_ids = [];
            foreach ($old_ids as $id) {
                $new_ids[] = $fields_mapping[$id];
            }

            $req =
                'UPDATE ' .
                _DB_PREFIX_ .
                'ndk_customization_field 
            set influences ="' .
                implode(',', $new_ids) .
                '" 
            WHERE id_ndk_customization_field = ' .
                (int) $field['id_ndk_customization_field'];

            Db::getInstance()->execute($req);

            $values = Db::getInstance()->executeS(
                'SELECT id_ndk_customization_field_value, influences_restrictions, influences_obligations 
            FROM ' .
                    _DB_PREFIX_ .
                    'ndk_customization_field_value 
            WHERE id_ndk_customization_field =' .
                    (int) $field['id_ndk_customization_field']
            );

            foreach ($values as $value) {
                if (!empty($value['influences_obligations'])) {
                    $new_ids_o = self::reDesignInfluence(
                        $value['influences_obligations'],
                        $fields_mapping,
                        $values_mapping
                    );
                    if ($new_ids_o) {
                        $req_o =
                            'UPDATE ' .
                            _DB_PREFIX_ .
                            'ndk_customization_field_value set influences_obligations ="' .
                            implode(',', $new_ids_o) .
                            '" 
                        WHERE id_ndk_customization_field_value = ' .
                            (int) $value['id_ndk_customization_field_value'];
                        Db::getInstance()->execute($req_o);
                    }
                }

                if (!empty($value['influences_restrictions'])) {
                    $new_ids_r = self::reDesignInfluence(
                        $value['influences_restrictions'],
                        $fields_mapping,
                        $values_mapping
                    );
                    if ($new_ids_r) {
                        $req_r =
                            'UPDATE ' .
                            _DB_PREFIX_ .
                            'ndk_customization_field_value set influences_restrictions ="' .
                            implode(',', $new_ids_r) .
                            '" 
                        WHERE id_ndk_customization_field_value = ' .
                            (int) $value['id_ndk_customization_field_value'];
                        Db::getInstance()->execute($req_r);
                    }
                }
            }
        }
    }

    public static function reDesignInfluence(
        $old_ids,
        $fields_mapping = [],
        $values_mapping = []
    ) {
        $old_ids = explode(',', $old_ids);
        $new_ids = [];

        foreach ($old_ids as $id) {
            $old_ids_r_arr = explode('-', $id);
            $my_row = [];
            if (sizeof($old_ids_r_arr) < 2) {
                return false;
            }

            if (array_key_exists($old_ids_r_arr[0], $values_mapping)) {
                $my_row[] = $values_mapping[$old_ids_r_arr[0]];
            } else {
                $my_row[] = $old_ids_r_arr[0];
            }

            if (array_key_exists($old_ids_r_arr[1], $fields_mapping)) {
                $my_row[] = $fields_mapping[$old_ids_r_arr[1]];
            } else {
                $my_row[] = $old_ids_r_arr[1];
            }

            $new_ids[] = implode('-', $my_row);
        }
        return $new_ids;
    }
}
?>
