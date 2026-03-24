<?php
/**
 * 2007-2015 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2015 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
$sql = [];
$sqlIndexes = [];
$sqlUpdate = [];
$prefix = _DB_PREFIX_;
$engine = _MYSQL_ENGINE_;
$tables = [
    [
        'name' => 'ndk_customization_field',
        'primary' => 'id_ndk_customization_field',
        'cols' => [
            [
                'name' => 'id_ndk_customization_field',
                'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT',
            ],
            ['name' => 'products', 'opts' => 'text NOT NULL'],
            ['name' => 'categories', 'opts' => 'text NOT NULL'],
            ['name' => 'type', 'opts' => 'int(1) NOT NULL'],
            ['name' => 'nb_lines', 'opts' => 'int(1) NOT NULL'],
            ['name' => 'maxlength', 'opts' => 'int(10) NOT NULL'],
            ['name' => 'feature', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            ['name' => 'target', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            ['name' => 'target_child', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            ['name' => 'x_axis', 'opts' => 'float NOT NULL'],
            ['name' => 'y_axis', 'opts' => 'float NOT NULL'],
            ['name' => 'svg_path', 'opts' => 'varchar(255) NOT NULL'],
            ['name' => 'zone_width', 'opts' => 'float NOT NULL'],
            ['name' => 'zone_height', 'opts' => 'float NOT NULL'],
            ['name' => 'id_shop', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            ['name' => 'position', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            ['name' => 'ref_position', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            ['name' => 'required', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'],
            ['name' => 'recommend', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'],
            ['name' => 'is_visual', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'],
            [
                'name' => 'configurator',
                'opts' => 'tinyint(4) NOT NULL DEFAULT 0',
            ],
            ['name' => 'draggable', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'],
            ['name' => 'resizeable', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'],
            ['name' => 'rotateable', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'],
            [
                'name' => 'orienteable',
                'opts' => 'tinyint(4) NOT NULL DEFAULT 0',
            ],
            ['name' => 'price', 'opts' => 'float NOT NULL'],
            ['name' => 'unit', 'opts' => 'varchar(255) NOT NULL'],
            [
                'name' => 'preserve_ratio',
                'opts' => 'tinyint(4) NOT NULL DEFAULT 0',
            ],
            [
                'name' => 'price_type',
                'opts' => 'varchar(255) NOT NULL DEFAULT "amount"',
            ],
            ['name' => 'price_per_caracter', 'opts' => 'float NOT NULL'],
            ['name' => 'show_price', 'opts' => 'tinyint(4) NOT NULL DEFAULT 1'],
            ['name' => 'validity', 'opts' => 'float NOT NULL'],
            /**add 29/02/16*/
            ['name' => 'zindex', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            ['name' => 'fonts', 'opts' => 'varchar(2500) NOT NULL'],
            ['name' => 'colors', 'opts' => 'varchar(2500) NOT NULL'],
            [
                'name' => 'stroke_color',
                'opts' => 'tinyint(4) NOT NULL DEFAULT 0',
            ],
            ['name' => 'sizes', 'opts' => 'varchar(2500) NOT NULL'],
            ['name' => 'effects', 'opts' => 'varchar(255) NOT NULL'],
            ['name' => 'alignments', 'opts' => 'varchar(255) NOT NULL'],
            [
                'name' => 'color_effect',
                'opts' => 'varchar(255) NOT NULL DEFAULT "normal"',
            ],
            ['name' => 'influences', 'opts' => 'varchar(2500) NOT NULL'],
            ['name' => 'quantity_min', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            ['name' => 'quantity_max', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            ['name' => 'weight_min', 'opts' => 'float NOT NULL DEFAULT 0'],
            ['name' => 'weight_max', 'opts' => 'float NOT NULL DEFAULT 0'],
            [
                'name' => 'open_status',
                'opts' => 'tinyint(4) NOT NULL DEFAULT 0',
            ],
            ['name' => 'quantity_link', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            [
                'name' => 'values_from_id',
                'opts' => 'int(10) NOT NULL DEFAULT 0',
            ],
            ['name' => 'options', 'opts' => 'text NOT NULL'],
        ],
    ],
    [
        'name' => 'ndk_customization_field_lang',
        'index' => ['id_ndk_customization_field', 'id_lang'],
        'primary' => '',
        'cols' => [
            [
                'name' => 'id_ndk_customization_field',
                'opts' => 'int(10) NOT NULL',
            ],
            ['name' => 'id_lang', 'opts' => 'int(10) NOT NULL'],
            ['name' => 'name', 'opts' => 'varchar(255) NOT NULL '],
            ['name' => 'admin_name', 'opts' => 'varchar(255) NOT NULL'],
            ['name' => 'notice', 'opts' => 'text NOT NULL'],
            ['name' => 'tooltip', 'opts' => 'text NOT NULL'],
        ],
    ],
    [
        'name' => 'ndk_customization_field_value',
        'primary' => 'id_ndk_customization_field_value',
        'cols' => [
            [
                'name' => 'id_ndk_customization_field_value',
                'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT',
            ],
            [
                'name' => 'id_ndk_customization_field',
                'opts' => 'int(10) NOT NULL',
            ],
            ['name' => 'price', 'opts' => 'float NOT NULL'],
            [
                'name' => 'set_quantity',
                'opts' => 'tinyint(4) NOT NULL DEFAULT 0',
            ],
            ['name' => 'quantity', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            ['name' => 'color', 'opts' => 'varchar(255) NOT NULL'],
            ['name' => 'excludes_products', 'opts' => 'text NOT NULL'],
            ['name' => 'excludes_categories', 'opts' => 'text NOT NULL'],
            /**add 29/02/16*/
            ['name' => 'quantity_min', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            /**add 29/02/16*/
            ['name' => 'quantity_max', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            [
                'name' => 'influences_restrictions',
                'opts' => 'varchar(2500) NOT NULL',
            ],
            [
                'name' => 'influences_obligations',
                'opts' => 'varchar(2500) NOT NULL',
            ],
            ['name' => 'position', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            [
                'name' => 'default_value',
                'opts' => 'tinyint(4) NOT NULL DEFAULT 0',
            ],
            [
                'name' => 'id_product_value',
                'opts' => 'int(10) NOT NULL DEFAULT 0',
            ],
            ['name' => 'step_quantity', 'opts' => 'text NOT NULL'],
            [
                'name' => 'input_type',
                'opts' => 'varchar(255) NOT NULL DEFAULT "select"',
            ],
            ['name' => 'reference', 'opts' => 'varchar(255) NOT NULL '],
            [
                'name' => 'id_parent_value',
                'opts' => 'int(10) NOT NULL DEFAULT 0',
            ],
            ['name' => 'type', 'opts' => 'varchar(255) NOT NULL '],
            ['name' => 'options', 'opts' => 'text NOT NULL'],
        ],
    ],
    [
        'name' => 'ndk_customization_field_value_lang',
        'index' => ['id_ndk_customization_field_value', 'id_lang'],
        'primary' => '',
        'cols' => [
            [
                'name' => 'id_ndk_customization_field_value',
                'opts' => 'int(10) NOT NULL',
            ],
            ['name' => 'id_lang', 'opts' => 'int(10) NOT NULL'],
            ['name' => 'value', 'opts' => 'varchar(255) NOT NULL '],
            ['name' => 'tags', 'opts' => 'varchar(255) NOT NULL'],
            ['name' => 'textmask', 'opts' => 'varchar(255) NOT NULL'],
            /**add 29/02/16*/
            ['name' => 'description', 'opts' => 'varchar(2500) NOT NULL'],
        ],
    ],
    [
        'name' => 'ndk_customization_field_shop',
        'index' => ['id_ndk_customization_field', 'id_shop'],
        'primary' => '',
        'cols' => [
            [
                'name' => 'id_ndk_customization_field',
                'opts' => 'int(10) NOT NULL',
            ],
            ['name' => 'id_shop', 'opts' => 'int(10) NOT NULL'],
        ],
    ],
    [
        'name' => 'ndk_customization_field_recipient',
        'primary' => 'id_ndk_customization_field_recipient',
        'cols' => [
            [
                'name' => 'id_ndk_customization_field_recipient',
                'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT',
            ],
            [
                'name' => 'id_ndk_customization_field',
                'opts' => 'int(10) NOT NULL',
            ],
            ['name' => 'id_cart', 'opts' => 'int(10) NOT NULL'],
            ['name' => 'id_product', 'opts' => 'int(10) NOT NULL'],
            ['name' => 'id_combination', 'opts' => 'int(10) NOT NULL'],
            ['name' => 'id_customization', 'opts' => 'int(10) NOT NULL'],
            ['name' => 'id_order', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            ['name' => 'firstname', 'opts' => 'varchar(255) NOT NULL'],
            ['name' => 'lastname', 'opts' => 'varchar(255) NOT NULL'],
            ['name' => 'email', 'opts' => 'varchar(255) NOT NULL'],
            ['name' => 'message', 'opts' => 'varchar(2500) NOT NULL'],
            ['name' => 'who_offers', 'opts' => 'varchar(2500) NOT NULL'],
            ['name' => 'details', 'opts' => 'varchar(2500) NOT NULL'],
            ['name' => 'code', 'opts' => 'varchar(255) NOT NULL'],
            ['name' => 'availability', 'opts' => 'varchar(255) NOT NULL'],
            ['name' => 'title', 'opts' => 'varchar(255) NOT NULL'],
            ['name' => 'date', 'opts' => 'datetime NOT NULL'],
            ['name' => 'send_mail', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'],
        ],
    ],
    [
        'name' => 'ndk_customization_field_configuration',
        'primary' => 'id_ndk_customization_field_configuration',
        'cols' => [
            [
                'name' => 'id_ndk_customization_field_configuration',
                'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT',
            ],
            ['name' => 'id_user', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            ['name' => 'id_guest', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
            [
                'name' => 'id_lang_default',
                'opts' => 'int(10) NOT NULL DEFAULT 0',
            ],
            ['name' => 'id_product', 'opts' => 'int(10) NOT NULL'],
            [
                'name' => 'id_customization',
                'opts' => 'int(10) NOT NULL DEFAULT 0',
            ],
            ['name' => 'is_admin', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'],
            [
                'name' => 'default_config',
                'opts' => 'tinyint(4) NOT NULL DEFAULT 0',
            ],
            ['name' => 'price', 'opts' => 'float NOT NULL'],
            ['name' => 'json_values', 'opts' => 'text NOT NULL'],
        ],
    ],

    [
        'name' => 'ndk_customization_field_csv',
        'primary' => 'id_ndk_customization_field_csv',
        'cols' => [
            [
                'name' => 'id_ndk_customization_field_csv',
                'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT',
            ],
            [
                'name' => 'id_ndk_customization_field',
                'opts' => 'int(10) NOT NULL DEFAULT 0',
            ],
            ['name' => 'width', 'opts' => 'varchar(255) NOT NULL'],
            ['name' => 'height', 'opts' => 'varchar(255) NOT NULL DEFAULT 0'],
            ['name' => 'price', 'opts' => 'float NOT NULL DEFAULT 0'],
        ],
    ],

    [
        'name' => 'ndk_customization_field_configuration_lang',
        'index' => ['id_ndk_customization_field_configuration', 'id_lang'],
        'primary' => '',
        'cols' => [
            [
                'name' => 'id_ndk_customization_field_configuration',
                'opts' => 'int(10) NOT NULL ',
            ],
            ['name' => 'id_lang', 'opts' => 'int(10) NOT NULL'],
            ['name' => 'name', 'opts' => 'varchar(255) NOT NULL'],
            ['name' => 'tags', 'opts' => 'varchar(255) NOT NULL'],
        ],
    ],

    [
        'name' => 'ndk_customization_field_group',
        'primary' => 'id_ndk_customization_field_group',
        'cols' => [
            [
                'name' => 'id_ndk_customization_field_group',
                'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT',
            ],
            ['name' => 'fields', 'opts' => 'varchar(2500) NOT NULL'],
            ['name' => 'products', 'opts' => 'text NOT NULL'],
            ['name' => 'categories', 'opts' => 'text NOT NULL'],
            ['name' => 'mode', 'opts' => 'int(10) NOT NULL'],
            ['name' => 'options', 'opts' => 'text NOT NULL'],
        ],
    ],

    [
        'name' => 'ndk_customization_field_group_lang',
        'index' => ['id_ndk_customization_field_group', 'id_lang'],
        'primary' => '',
        'cols' => [
            [
                'name' => 'id_ndk_customization_field_group',
                'opts' => 'int(10) NOT NULL',
            ],
            ['name' => 'id_lang', 'opts' => 'int(10) NOT NULL'],
            ['name' => 'name', 'opts' => 'varchar(255) NOT NULL'],
        ],
    ],

    [
        'name' => 'ndk_customization_field_group_shop',
        'index' => ['id_ndk_customization_field_group', 'id_shop'],
        'primary' => '',
        'cols' => [
            [
                'name' => 'id_ndk_customization_field_group',
                'opts' => 'int(10) NOT NULL',
            ],
            ['name' => 'id_shop', 'opts' => 'int(10) NOT NULL'],
        ],
    ],

    [
        'name' => 'ndk_customization_field_specific_price',
        'primary' => 'id_ndk_customization_field_specific_price',
        'cols' => [
            [
                'name' => 'id_ndk_customization_field_specific_price',
                'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT',
            ],
            [
                'name' => 'id_ndk_customization_field',
                'opts' => 'int(10) NOT NULL',
            ],
            [
                'name' => 'id_ndk_customization_field_value',
                'opts' => 'int(10) NOT NULL',
            ],
            ['name' => 'reduction', 'opts' => 'float NOT NULL'],
            ['name' => 'reduction_type', 'opts' => 'varchar(255) NOT NULL'],
            ['name' => 'from_quantity', 'opts' => 'int(10) NOT NULL'],
        ],
    ],

    [
        'name' => 'ndk_customization_field_cache',
        'primary' => 'id_cache',
        'cols' => [
            [
                'name' => 'id_cache',
                'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT',
            ],
            ['name' => 'key_cache', 'opts' => 'varchar(255) NOT NULL'],
            ['name' => 'content', 'opts' => 'LONGTEXT NOT NULL'],
            ['name' => 'expire', 'opts' => 'int(10) NOT NULL'],
        ],
    ],
];

foreach ($tables as $table) {
    $sql[$table['name']] =
        'CREATE TABLE IF NOT EXISTS '.
        $prefix.
        $table['name'].
        ' ( remove_me_after float NOT NULL';
    $sql[$table['name']] .=
        ' )  ENGINE='.pSQL($engine).' DEFAULT CHARSET=utf8';
    $sql[] =
        'ALTER TABLE '.
        pSQL($prefix.$table['name']).
        ' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';
    foreach ($table['cols'] as $col) {
        if ('ndk_customization_field_configuration_lang' == $table['name']) {
            $sqlIndexes[] =
                'ALTER TABLE '.
                $prefix.
                $table['name'].
                ' CHANGE `id_ndk_customization_field_configuration` `id_ndk_customization_field_configuration` INT(10) NOT NULL';
        }

        if ('ndk_customization_field_group_lang' == $table['name']) {
            $sqlIndexes[] =
                'ALTER TABLE '.
                $prefix.
                $table['name'].
                ' CHANGE `id_ndk_customization_field_group` `id_ndk_customization_field_group` INT(10) NOT NULL';
        }

        //check if col exists
        $sqlCheck =
            'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
	 WHERE table_name = "'.
            $prefix.
            $table['name'].
            '" 
	 AND table_schema = "'.
            _DB_NAME_.
            '" 
	 AND column_name = "'.
            $col['name'].
            '" ';

        $check = Db::getInstance()->executeS($sqlCheck);

        if (0 == sizeof($check)) {
            $sql[] =
                'ALTER TABLE `'.
                $prefix.
                $table['name'].
                '` ADD  `'.
                $col['name'].
                '` '.
                $col['opts'];
        }
    }

    //on enlève la premiere colonne
    //check if col exists
    $sqlCheckRemove =
        'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
	 WHERE table_name = "'.
        $prefix.
        $table['name'].
        '" 
	 AND table_schema = "'.
        _DB_NAME_.
        '" 
	 AND column_name = "remove_me_after" ';

    $checkRemove = Db::getInstance()->executeS($sqlCheckRemove);

    if (sizeof($checkRemove) > 0) {
        $sql[] =
            'ALTER TABLE '.
            $prefix.
            $table['name'].
            ' DROP COLUMN remove_me_after';
    }
}

foreach ($sql as $query) {
    if (false == Db::getInstance()->execute($query)) {
        return false;
    }
}

foreach ($tables as $table) {
    //INDEXES
    if ('ndk_customization_field_shop' == $table['name']) {
        //ndkSqlInstall::debugDuplicateNdk($table);
    }

    if (isset($table['index'])) {
        if (sizeof($table['index']) > 0) {
            $chekIndex = Db::getInstance()->executeS(
                'SHOW INDEX FROM '.$prefix.$table['name']
            );
            if (sizeof($chekIndex) > 0) {
                $sqlIndexes[] =
                    'ALTER TABLE '.
                    $prefix.
                    $table['name'].
                    ' DROP PRIMARY KEY';
            }

            $sqlIndexes[] =
                'ALTER TABLE '.
                $prefix.
                $table['name'].
                ' ADD PRIMARY KEY '.
                implode('_', $table['index']).
                ' ('.
                implode(',', $table['index']).
                ')';
        }
    }
}

foreach ($sqlIndexes as $query) {
    if (false == Db::getInstance()->execute($query)) {
        return false;
    }
}

Db::getInstance()->execute(
    'ALTER TABLE `'.
        $prefix.
        'customized_data` CHANGE `value` `value` VARCHAR(2500)'
);

$shop_query =
    'SELECT id_ndk_customization_field FROM '.
    $prefix.
    'ndk_customization_field_shop';
$result = Db::getInstance()->executeS($shop_query);
if (0 == count($result)) {
    Db::getInstance()->execute(
        'INSERT IGNORE INTO '.
            $prefix.
            'ndk_customization_field_shop (id_ndk_customization_field, id_shop) SELECT id_ndk_customization_field, '.
            (int) Configuration::get('PS_SHOP_DEFAULT').
            '  FROM '.
            $prefix.
            'ndk_customization_field'
    );
}

ndkSqlInstall::install();
