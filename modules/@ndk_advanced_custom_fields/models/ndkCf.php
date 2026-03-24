<?php
/**
 *  Tous droits réservés NDKDESIGN.
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2017 Hendrik Masson
 *  @license   Tous droits réservés
 */
require_once _PS_MODULE_DIR_.
    'ndk_advanced_custom_fields/tools/http_build_url.php';
require_once _PS_MODULE_DIR_.
'ndk_advanced_custom_fields/import/ndkCfImporter.php';

class NdkCf extends ObjectModel
{
    public $products;
    public $categories;
    public $type;
    public $nb_lines;
    public $maxlength;
    public $feature;
    public $target;
    public $target_child;
    public $x_axis;
    public $y_axis;
    public $svg_path;
    public $zone_width;
    public $zone_height;
    public $position;
    public $price;
    public $unit;
    public $preserve_ratio;
    public $price_type;
    public $price_per_caracter;
    /** @var string Name */
    public $name;
    public $admin_name;
    public $notice;
    public $tooltip;
    public $required = false;
    public $recommend = false;
    public $is_visual = false;
    public $configurator = false;
    public $draggable = false;
    public $resizeable = false;
    public $rotateable = false;
    public $orienteable = false;
    public $zindex;
    public $validity;
    public $colors;
    public $stroke_color;
    public $fonts;
    public $sizes;
    public $effects;
    public $alignments;
    public $color_effect;
    public $influences;
    public $quantity_min;
    public $quantity_max;
    public $weight_min;
    public $weight_max;
    public $open_status;
    public $ref_position;
    public $show_price;
    public $quantity_link;
    public $values_from_id;
    public $options;

    public static $definition = [
        'table' => 'ndk_customization_field',
        'primary' => 'id_ndk_customization_field',
        'multilang' => true,
        'fields' => [
            'required' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false,
            ],
            'recommend' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false,
            ],
            'is_visual' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false,
            ],
            'configurator' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false,
            ],
            'draggable' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false,
            ],
            'resizeable' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false,
            ],
            'rotateable' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false,
            ],
            'orienteable' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false,
            ],
            'type' => [
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => true,
            ],
            'nb_lines' => [
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ],
            'maxlength' => [
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ],
            'feature' => [
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ],
            'target' => [
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ],
            'target_child' => [
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ],

            'x_axis' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
                'required' => false,
            ],
            'y_axis' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
                'required' => false,
            ],
            'zone_width' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
                'required' => false,
            ],
            'zone_height' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
                'required' => false,
            ],
            'svg_path' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
            ],

            'position' => [
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
                'required' => true,
            ],
            'ref_position' => [
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
                'required' => false,
            ],
            'zindex' => [
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
                'required' => false,
            ],
            'validity' => [
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ],
            'products' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
            ],
            'categories' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
            ],
            'price' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
                'required' => false,
            ],
            'show_price' => ['type' => self::TYPE_INT, 'required' => false],
            'unit' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
            ],
            'preserve_ratio' => [
                'type' => self::TYPE_INT,
                'validate' => 'isGenericName',
            ],
            'price_type' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
            ],
            'price_per_caracter' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
                'required' => false,
            ],
            // Lang fields
            'name' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isCleanHtml',
                'required' => true,
                'size' => 255,
            ],
            'admin_name' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isCleanHtml',
                'required' => true,
                'size' => 255,
            ],
            'notice' => [
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHtml',
                'size' => 5000,
            ],
            'tooltip' => [
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHtml',
                'size' => 5000,
            ],
            'fonts' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'size' => 5000,
            ],
            'colors' => [
                'type' => self::TYPE_HTML,
                'lang' => false,
                'validate' => 'isCleanHtml',
                'size' => 5000,
            ],
            'stroke_color' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false,
            ],
            'sizes' => [
                'type' => self::TYPE_HTML,
                'lang' => false,
                'validate' => 'isCleanHtml',
                'size' => 5000,
            ],
            'effects' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
            ],
            'alignments' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
            ],
            'color_effect' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
            ],
            'influences' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
            ],

            'quantity_min' => [
                'type' => ObjectModel::TYPE_INT,
                'required' => false,
            ],
            'quantity_max' => [
                'type' => ObjectModel::TYPE_INT,
                'required' => false,
            ],
            'weight_min' => [
                'type' => ObjectModel::TYPE_FLOAT,
                'required' => false,
            ],
            'weight_max' => [
                'type' => ObjectModel::TYPE_FLOAT,
                'required' => false,
            ],
            'open_status' => [
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ],
            'quantity_link' => [
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ],
            'values_from_id' => [
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false,
            ],
            'options' => ['type' => self::TYPE_STRING],
        ],
    ];

    public function add($autodate = true, $nullValues = false)
    {
        return parent::add($autodate);
    }

    public function update($nullValues = false)
    {
        $return = parent::update($nullValues);

        return $return;
    }

    public function deleteImage($force_delete = false)
    {
        // Hack to prevent the main lookbook image from being deleted in AdminController::uploadImage() when a thumb image is uploaded
        if (
            isset($_FILES['thumb']) &&
            (!isset($_FILES['image']) || empty($_FILES['image']['name']))
        ) {
            return true;
        }

        if (parent::deleteImage()) {
            if (
                file_exists(
                    $this->image_dir.
                        'thumbs/'.
                        $this->id.
                        '-thumb_scene.'.
                        $this->image_format
                ) &&
                !unlink(
                    $this->image_dir.
                        'thumbs/'.
                        $this->id.
                        '-thumb_scene.'.
                        $this->image_format
                )
            ) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    public static function isCustomizable($id_product = 0, $id_category = 0)
    {
        if ('all' == Configuration::get('NDK_CATEGORY_ASSOCIATION_MODE')) {
            $id_category = implode(
                '|',
                Product::getProductCategories($id_product)
            );
        }
        if (0 == $id_category) {
            $id_category = (int) Db::getInstance()->getValue(
                'SELECT id_category_default FROM `'.
                    _DB_PREFIX_.
                    'product` WHERE id_product = '.
                    (int) $id_product
            );
        }
        $fields = Db::getInstance()->getRow(
            '
				SELECT COUNT(cf.`id_ndk_customization_field`)
				FROM `'.
                _DB_PREFIX_.
                'ndk_customization_field` cf
				INNER JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field_shop` cfs ON (cfs.`id_ndk_customization_field`= cf.`id_ndk_customization_field` AND cfs.`id_shop` = '.
                (int) Context::getContext()->shop->id.
                ')
				WHERE FIND_IN_SET( '.
                (int) $id_product.
                ', cf.`products`) OR CONCAT(",", cf.`categories`, ",") REGEXP ",('.
                $id_category.
                ')," 
				AND cf.type NOT IN(99)'
        );

        if ($fields['COUNT(cf.`id_ndk_customization_field`)'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function isRequiredCustomization($id_product, $id_category)
    {
        if ('all' == Configuration::get('NDK_CATEGORY_ASSOCIATION_MODE')) {
            $id_category = implode(
                '|',
                Product::getProductCategories($id_product)
            );
        }
        $id_address = (int) Context::getContext()->cart->id_address_invoice;
        $address = Address::initialize($id_address, true);
        $tax_manager = TaxManagerFactory::getManager(
            $address,
            Product::getIdTaxRulesGroupByIdProduct(
                (int) $id_product,
                Context::getContext()
            )
        );
        $product_tax_calculator = $tax_manager->getTaxCalculator();

        $id_lang = Context::getContext()->language->id;

        Db::getInstance()->execute('SET SQL_BIG_SELECTS=1');

        $fields = Db::getInstance()->getRow(
            '
				SELECT COUNT(cf.`id_ndk_customization_field`)
				FROM `'.
                _DB_PREFIX_.
                'ndk_customization_field` cf
				INNER JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field_shop` cfs ON (cfs.`id_ndk_customization_field`= cf.`id_ndk_customization_field`AND cfs.`id_shop` = '.
                (int) Context::getContext()->shop->id.
                ')
				WHERE FIND_IN_SET( '.
                (int) $id_product.
                ', cf.`products`) OR CONCAT(",", cf.`categories`, ",") REGEXP ",('.
                $id_category.
                '),"
				AND cf.type NOT IN(99)
				AND (cf.required = 1 OR cf.quantity_min > 0 OR cf.weight_min > 0)'
        );
        if ($fields['COUNT(cf.`id_ndk_customization_field`)'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function mayShowPrice()
    {
        $catalog_mode_without_price =
            1 == (int) Configuration::get('PS_CATALOG_MODE') &&
            1 != (int) Configuration::get('PS_CATALOG_MODE_WITH_PRICES');
        $customer_group = new Group(
            Context::getContext()->customer->id_default_group
        );
        $group_hide_price = 0 == $customer_group->show_prices;

        if ($catalog_mode_without_price || $group_hide_price) {
            return false;
        } else {
            return true;
        }
    }

    public static function getCustomFields(
        $id_product,
        $id_category,
        $type = false,
        $id_field = false,
        $id_value = 0
    ) {
        if ('all' == Configuration::get('NDK_CATEGORY_ASSOCIATION_MODE')) {
            $id_category = implode(
                '|',
                Product::getProductCategories($id_product)
            );
        }

        $id_address = (int) Context::getContext()->cart->id_address_invoice;
        $address = Address::initialize($id_address, true);
        $tax_manager = TaxManagerFactory::getManager(
            $address,
            Product::getIdTaxRulesGroupByIdProduct(
                (int) $id_product,
                Context::getContext()
            )
        );
        $product_tax_calculator = $tax_manager->getTaxCalculator();
        $jsonDatas = [];
        $id_lang = Context::getContext()->language->id;
        $types = [];
        $module = Module::getInstanceByName('ndk_advanced_custom_fields');
        $all_types = $module->types;
        $allowed_types = [];
        $customer_group = new Group(
            Context::getContext()->customer->id_default_group
        );
        $show_prices = self::mayShowPrice();
        foreach ($all_types as $row) {
            $allowed_types[] = $row['id_type'];
        }

        Db::getInstance()->execute('SET SQL_BIG_SELECTS=1');

        $sql =
            '
				SELECT cf.`id_ndk_customization_field`, cf.`values_from_id`, cf.`type`, cf.`options`, cf.`feature`, cf.`target`, cf.`target_child`, cf.`price`, cf.`show_price`, cf.`quantity_link` , cf.`unit`, cf.`preserve_ratio`, cf.`price_type`, cf.`price_per_caracter`, cf.`nb_lines`, cf.`maxlength`, cf.`x_axis`, cf.`y_axis`, cf.`zone_width`, cf.`svg_path`, cf.`zone_height`, cf.`required`, cf.`recommend`, cf.`is_visual`, cf.`configurator`, cf.`draggable`, cf.`resizeable`, cf.`rotateable`, cf.`orienteable`, cf.`position`, cf.`zindex`, cf.`fonts`, cf.`colors`,  cf.`stroke_color`, cf.`sizes`, cf.effects, cf.alignments, cf.color_effect, cf.`validity`, cf.`quantity_min`, cf.`quantity_max`,cf.`weight_min`, cf.`weight_max`, cf.`open_status`, cf.influences, cfl.`name`, cfl.`admin_name`, cfl.`notice`, cfl.`tooltip`, g.id_ndk_customization_field_group, g.mode, g.options as group_options
				FROM `'.
            _DB_PREFIX_.
            'ndk_customization_field` cf
				LEFT JOIN `'.
            _DB_PREFIX_.
            'ndk_customization_field_lang` cfl ON (cfl.`id_ndk_customization_field`= cf.`id_ndk_customization_field`AND cfl.`id_lang` = '.
            (int) $id_lang.
            ' )
				LEFT JOIN `'.
            _DB_PREFIX_.
            'ndk_customization_field_group` g ON (FIND_IN_SET( cf.`id_ndk_customization_field`, g.`fields`))
				INNER JOIN `'.
            _DB_PREFIX_.
            'ndk_customization_field_shop` cfs ON (cfs.`id_ndk_customization_field`= cf.`id_ndk_customization_field`AND cfs.`id_shop` = '.
            (int) Context::getContext()->shop->id.
            ')
				WHERE '.
            ($id_field
                ? '  cf.`id_ndk_customization_field`= '.$id_field
                : ' (FIND_IN_SET( '.
                    (int) $id_product.
                    ', cf.`products`) OR CONCAT(",", cf.`categories`, ",") REGEXP ",('.
                    $id_category.
                    '),") AND (cf.type IN ('.
                    implode(',', $allowed_types).
                    ') AND cf.type NOT IN(99) '.
                    ($type ? ' AND cf.type = '.$type : '').
                    ')').
            '
				 GROUP BY  cf.`id_ndk_customization_field` ORDER BY cf.`position`';
        $fields = Db::getInstance()->executeS($sql);
        $i = 0;

        foreach ($fields as $field) {
            if (!in_array($field['type'], $types)) {
                $types[] = $field['type'];
            }

            if ('percent' == $field['price_type']) {
                $usetax = false;
            } else {
                $usetax = Group::getPriceDisplayMethod(
                    Group::getPriceDisplayMethod(
                        Context::getContext()->customer->id_default_group
                    )
                );

                if (0 == Product::$_taxCalculationMethod) {
                    $usetax = true;
                } else {
                    $usetax = false;
                }
            }

            if (!$show_prices) {
                $fields[$i]['show_price'] = 0;
            }

            $options_fields = [
                'purpose_text',
                'purpose_images',
                'purpose_image',
                'purpose_upload',
                'purpose_textsimple',
            ];
            $fields[$i]['options'] = Tools::jsonDecode($field['options'], true);
            foreach ($options_fields as $opt) {
                if (!isset($fields[$i]['options'][$opt])) {
                    $fields[$i]['options'][$opt] = false;
                }
            }
            if (!isset($fields[$i]['options']['zone_mode'])) {
                $fields[$i]['options']['zone_mode'] = '';
            }
            $fields[$i]['options']['is_editable'] =
                1 == $field['resizeable'] ||
                1 == $field['draggable'] ||
                1 == $field['rotateable'];
            $fields[$i]['group_options'] = Tools::jsonDecode(
                $field['group_options'],
                true
            );
            $fields[$i]['fonts'] = '';
            $fields[$i]['fontLink'] = '';
            $fields[$i]['colors'] = '';
            $fields[$i]['sizes'] = '';
            $fields[$i]['effects'] = '';
            $fields[$i]['alignments'] = '';
            if (
                file_exists(
                    _PS_IMG_DIR_.
                        'scenes/'.
                        'ndkcf/'.
                        $field['target_child'].
                        '.jpg'
                )
            ) {
                list($width, $height, $type, $attr) = getimagesize(
                    _PS_IMG_DIR_.
                        'scenes/'.
                        'ndkcf/'.
                        $field['target_child'].
                        '.jpg'
                );
                $fields[$i]['target_original_width'] = $width;
                $fields[$i]['target_original_height'] = $height;
            } elseif (
                file_exists(
                    _PS_IMG_DIR_.
                        'scenes/'.
                        'ndkcf/'.
                        $field['target_child'].
                        '.svg'
                )
            ) {
                preg_match(
                    "#viewbox=[\"']\d* \d* (\d*) (\d*)#i",
                    Tools::file_get_contents(
                        _PS_IMG_DIR_.
                            'scenes/'.
                            'ndkcf/'.
                            $field['target_child'].
                            '.svg'
                    ),
                    $d
                );
                if (!empty($d)) {
                    $width = $d[1];
                    $height = $d[2];
                    $fields[$i]['target_original_width'] = $width;
                    $fields[$i]['target_original_height'] = $height;
                } else {
                    $fields[$i]['target_original_width'] = 0;
                    $fields[$i]['target_original_height'] = 0;
                }
            } else {
                $fields[$i]['target_original_width'] = 0;
                $fields[$i]['target_original_height'] = 0;
            }

            if (0 == $field['target']) {
                $fields[$i]['target_child'] = 0;
                $fields[$i]['y_axis'] = 0;
                $fields[$i]['x_axis'] = 0;
                $fields[$i]['svg_path'] = 0;
            } elseif ($field['target'] > 0) {
                if (0 == $field['target_child']) {
                    $target = new NdkCf((int) $field['target']);
                    $targetValues = [];
                    foreach ($target->getValuesId() as $value) {
                        array_push($targetValues, $value['id']);
                    }

                    $fields[$i]['target_child'] = implode('|', $targetValues);
                }
            }

            if ('' != $field['fonts']) {
                $fields[$i]['fontLink'] = $field['fonts'];
                Context::getContext()->controller->addCSS(
                    $field['fonts'],
                    'all'
                );
                $fields[$i]['fonts'] = NdkCf::fontLinkToArray($field['fonts']);
            }

            if (1 == $field['is_visual']) {
                $fields[$i]['configurator'] = 1;
            }

            if ('' != $field['colors']) {
                $fields[$i]['colors'] = explode(';', $field['colors']);
            } else {
                $fields[$i]['colors'] = explode(
                    ';',
                    Configuration::get('NDK_ACF_COLORS')
                );
            }

            if ('' != $field['sizes']) {
                $fields[$i]['sizes'] = explode(';', $field['sizes']);
            }

            if ('' != $field['effects']) {
                $fields[$i]['effects'] = explode(';', $field['effects']);
            }
            if ('' != $field['alignments']) {
                $fields[$i]['alignments'] = explode(';', $field['alignments']);
            }

            $fields[$i]['is_picto'] = file_exists(
                _PS_IMG_DIR_.
                    'scenes/ndkcf/pictos/'.
                    $field['id_ndk_customization_field'].
                    '.jpg'
            );
            $fields[$i]['is_mask_image'] = file_exists(
                _PS_IMG_DIR_.
                    'scenes/ndkcf/mask/'.
                    $field['id_ndk_customization_field'].
                    '.jpg'
            );

            if (
                file_exists(
                    _PS_IMG_DIR_.
                        'scenes/'.
                        'ndkcf/'.
                        $field['id_ndk_customization_field'].
                        '.csv'
                )
            ) {
                $csv_file =
                    _PS_IMG_DIR_.
                    'scenes/'.
                    'ndkcf/'.
                    $field['id_ndk_customization_field'].
                    '.csv';
                //mise à jour csv si besoin
                $reqs = Db::getInstance()->getRow(
                    'SELECT COUNT(id_ndk_customization_field) as nb FROM '.
                        _DB_PREFIX_.
                        'ndk_customization_field_csv WHERE id_ndk_customization_field = '.
                        (int) $field['id_ndk_customization_field']
                );
                if ($reqs['nb'] < 1) {
                    Ndkcf::recordCsv($field['id_ndk_customization_field']);
                }

                $fields[$i]['is_csv'] = true;

                $fields[$i]['price_range_width'] = Db::getInstance()->executeS(
                    'SELECT DISTINCT(width) FROM '.
                        _DB_PREFIX_.
                        'ndk_customization_field_csv WHERE id_ndk_customization_field = '.
                        (int) $field['id_ndk_customization_field']
                        .' AND price > -1 ' // @TRE added
                );

                $fields[$i]['price_range_height'] = Db::getInstance()->executeS(
                    'SELECT DISTINCT(height) FROM '.
                        _DB_PREFIX_.
                        'ndk_customization_field_csv WHERE id_ndk_customization_field = '.
                        (int) $field['id_ndk_customization_field']
                        .' AND price > -1 ' // @TRE added
                );

                /* @TRE added */
                $tmp = Db::getInstance()->executeS(
                    'SELECT width, height FROM '.
                    _DB_PREFIX_.
                    'ndk_customization_field_csv WHERE id_ndk_customization_field = '.
                    (int) $field['id_ndk_customization_field']
                    .' AND price > -1 '
                );
                $matrix = [];
                foreach ($tmp as $single) {
                    if (!isset($matrix[$single['width']])) {
                        $matrix[$single['width']] = [];
                    }
                    $matrix[$single['width']][] = $single['height'];
                }
                $fields[$i]['price_matrix'] = $matrix;
                /* @TRE end */

                if (21 == $fields[$i]['type']) {
                    $fields[$i]['price_range_min_width'] = 0;
                    $fields[$i]['price_range_min_height'] = 0;
                    $fields[$i]['price_range_max_width'] = 9999999999;
                    $fields[$i]['price_range_max_height'] = 9999999999;
                } else {
                    $fields[$i][
                        'price_range_min_width'
                    ] = Db::getInstance()->getRow(
                        'SELECT MIN(width + 0.0) as min FROM '.
                            _DB_PREFIX_.
                            'ndk_customization_field_csv WHERE id_ndk_customization_field = '.
                            (int) $field['id_ndk_customization_field'].
                            ' AND width !=""'
                    )['min'];

                    $fields[$i][
                        'price_range_min_height'
                    ] = Db::getInstance()->getRow(
                        'SELECT MIN(height + 0.0) as min FROM '.
                            _DB_PREFIX_.
                            'ndk_customization_field_csv WHERE id_ndk_customization_field = '.
                            (int) $field['id_ndk_customization_field'].
                            ' AND height !=""'
                    )['min'];

                    $fields[$i][
                        'price_range_max_width'
                    ] = Db::getInstance()->getRow(
                        'SELECT MAX(width + 0.0) as max FROM '.
                            _DB_PREFIX_.
                            'ndk_customization_field_csv WHERE id_ndk_customization_field = '.
                            (int) $field['id_ndk_customization_field']
                    )['max'];

                    $fields[$i][
                        'price_range_max_height'
                    ] = Db::getInstance()->getRow(
                        'SELECT MAX(height + 0.0) as max FROM '.
                            _DB_PREFIX_.
                            'ndk_customization_field_csv WHERE id_ndk_customization_field = '.
                            (int) $field['id_ndk_customization_field']
                    )['max'];
                }
            } else {
                $fields[$i]['is_csv'] = false;
            }
            $fields[$i]['is_csv'] = false;
            $fields[$i]['values'] = self::getFieldValues(
                (int) $field['values_from_id'] > 0
                    ? (int) $field['values_from_id']
                    : (int) $field['id_ndk_customization_field'],
                $id_product,
                $id_category,
                $id_value
            );

            if ('percent' == $fields[$i]['price_type']) {
                $usetax = false;
            }

            $fields[$i]['price'] = $usetax
                ? $product_tax_calculator->addTaxes($field['price'])
                : $field['price'];
            $fields[$i]['price_per_caracter'] = $usetax
                ? $product_tax_calculator->addTaxes(
                    $field['price_per_caracter']
                )
                : $field['price_per_caracter'];

            $j = 0;
            $colorizesvg = false;

            $jsonDatas[$fields[$i]['id_ndk_customization_field']][
                'type'
            ] = self::getTypeTechName($field['type']);

            /*
                        if(in_array((int)$fields[$i]['type'], array(17, 24, 22, 26, 27)) && count($fields[$i]['values']) == 0)
                        {
                            $prod = new Product((int)$id_product, (int)Context::getContext()->language->id);
                            $fields[$i]['values'][0]['value'] = $prod->name;
                            $fields[$i]['values'][0]['id_product_value'] = $prod->id;
                        }
            */

            foreach ($fields[$i]['values'] as $value) {
                $fields[$i]['values'][$j]['is3D'] = false;
                $fields[$i]['values'][$j]['options'] = Tools::jsonDecode($value['options'], true);
                if (isset($fields[$i]['values'][$j]['options']['force_tax_rule'])) {
                    $new_id_tax_rule = (int) $fields[$i]['values'][$j]['options']['force_tax_rule'];
                    if ($new_id_tax_rule > 0) {
                        $old_id_tax_rule = Product::getIdTaxRulesGroupByIdProduct(
                            (int) $id_product,
                            Context::getContext()
                        );
                        $fields[$i]['values'][$j]['options']['tax_ratio'] = $fields[$i]['values'][$j]['tax_ratio'] = self::getTaxConversionRatio($old_id_tax_rule, $new_id_tax_rule);
                    }
                }
                if (
                    in_array((int) $fields[$i]['type'], [
                        17,
                        24,
                        22,
                        26,
                        27,
                        101,
                    ]) &&
                    '[:SELF]' == $value['value']
                ) {
                    $prod = new Product(
                        (int) $id_product,
                        (int) Context::getContext()->language->id
                    );
                    $fields[$i]['values'][$j]['value'] = $prod->name;
                    $fields[$i]['values'][$j]['id_product_value'] = $prod->id;
                }

                if ('percent' == $fields[$i]['price_type']) {
                    $usetax = false;
                }

                $fields[$i]['values'][$j]['price'] = $usetax
                    ? $product_tax_calculator->addTaxes($value['price'])
                    : $value['price'];

                if (
                    file_exists(
                        _PS_IMG_DIR_.
                            'scenes/'.
                            'ndkcf/'.
                            $value['id'].
                            '.svg'
                    )
                ) {
                    $fields[$i]['values'][$j]['issvg'] = true;
                    if ('null' != $field['colors']) {
                        $colorizesvg = true;
                        $fields[$i]['values'][$j]['svgcode'] = str_replace(
                            ']>',
                            '',
                            Tools::file_get_contents(
                                _PS_ROOT_DIR_.
                                    '/img/scenes/ndkcf/'.
                                    $value['id'].
                                    '.svg'
                            )
                        );
                    } else {
                        $colorizesvg = false;
                        $fields[$i]['values'][$j]['svgcode'] = false;
                    }
                } else {
                    $fields[$i]['values'][$j]['issvg'] = false;
                }
                if (
                    file_exists(
                        _PS_IMG_DIR_.
                            'scenes/'.
                            'ndkcf/'.
                            $value['id'].
                            '.glb'
                    )
                ) {
                    $fields[$i]['values'][$j]['is3D'] = true;
                }
                if (
                    file_exists(
                        _PS_IMG_DIR_.
                            'scenes/'.
                            'ndkcf/thumbs/'.
                            $value['id'].
                            '-texture.jpg'
                    )
                ) {
                    $fields[$i]['values'][$j]['is_texture'] = true;
                } else {
                    $fields[$i]['values'][$j]['is_texture'] = false;
                }

                if (
                    file_exists(
                        _PS_IMG_DIR_.
                            'scenes/'.
                            'ndkcf/'.
                            $value['id'].
                            '.jpg'
                    )
                ) {
                    $fields[$i]['values'][$j]['is_image'] = true;
                } else {
                    $fields[$i]['values'][$j]['is_image'] = false;
                }

                if ('' != $value['influences_restrictions']) {
                    $i_values = explode(',', $value['influences_restrictions']);
                    foreach ($i_values as $v) {
                        if ('' != $v) {
                            if ('all' == Tools::substr($v, 0, 3)) {
                                $jsonDatas[
                                    $fields[$i]['id_ndk_customization_field']
                                ][$value['id']]['restrictions'][] =
                                    explode('-', $v)[1].'|all|all';
                            } else {
                                $vObj = Db::getInstance()->getRow(
                                    '
										SELECT v.id_ndk_customization_field, vl.value
										FROM '.
                                        _DB_PREFIX_.
                                        'ndk_customization_field_value v
										LEFT JOIN '.
                                        _DB_PREFIX_.
                                        'ndk_customization_field_value_lang vl ON (vl.id_ndk_customization_field_value = v.id_ndk_customization_field_value AND vl.id_lang = '.
                                        (int) Context::getContext()->language
                                            ->id.
                                        ')
										WHERE v.id_ndk_customization_field_value = '.
                                        (int) $v
                                );
                                $jsonDatas[
                                    $fields[$i]['id_ndk_customization_field']
                                ][$value['id']]['restrictions'][] =
                                    $vObj['id_ndk_customization_field'].
                                    '|'.
                                    $v.
                                    '|'.
                                    $vObj['value'];
                            }
                        } else {
                            $jsonDatas[
                                $fields[$i]['id_ndk_customization_field']
                            ][$value['id']]['restrictions'][] = '';
                        }
                    }
                }

                if ('' != $value['influences_obligations']) {
                    $i_values = explode(',', $value['influences_obligations']);
                    foreach ($i_values as $v) {
                        if ('' != $v) {
                            if ('all' == Tools::substr($v, 0, 3)) {
                                $jsonDatas[
                                    $fields[$i]['id_ndk_customization_field']
                                ][$value['id']]['obligations'][] =
                                    explode('-', $v)[1].'|all|all';
                            } else {
                                $vObj = Db::getInstance()->getRow(
                                    '
										SELECT v.id_ndk_customization_field, vl.value
										FROM '.
                                        _DB_PREFIX_.
                                        'ndk_customization_field_value v
										LEFT JOIN '.
                                        _DB_PREFIX_.
                                        'ndk_customization_field_value_lang vl ON (vl.id_ndk_customization_field_value = v.id_ndk_customization_field_value AND vl.id_lang = '.
                                        (int) Context::getContext()->language
                                            ->id.
                                        ')
										WHERE v.id_ndk_customization_field_value = '.
                                        (int) $v
                                );

                                $jsonDatas[
                                    $fields[$i]['id_ndk_customization_field']
                                ][$value['id']]['obligations'][] =
                                    $vObj['id_ndk_customization_field'].
                                    '|'.
                                    $v.
                                    '|'.
                                    $vObj['value'];
                            }
                        } else {
                            $jsonDatas[
                                $fields[$i]['id_ndk_customization_field']
                            ][$value['id']]['obligations'][] = '';
                        }
                    }
                }

                ++$j;
            }

            $fields[$i]['colorizesvg'] = $colorizesvg;

            ++$i;
        }

        $return = [
            'fields' => $fields,
            'jsonDatas' => $jsonDatas,
            'types' => $types,
        ];

        return $return;
    }

    public static function fontLinkToArray($link)
    {
        $isV2 = strpos($link, 'css2');
        $families = explode('family=', $link);
        $f = 0;
        $output = [];
        if ($isV2) {
            //dump($families);
            foreach ($families as $family) {
                if ($f > 0) {
                    $font_name = str_replace(
                        '+',
                        ' ',
                        explode('&', $family)[0]
                    );
                    $output[] = explode(':', $font_name)[0];
                }
                ++$f;
            }
        } else {
            if (isset($families[1])) {
                $families = $families[1];
                $fonts = explode('|', $families);
                $fonts = str_replace(['\'', '"', "'"], '', $fonts);

                foreach ($fonts as $key => $value) {
                    $output[] = str_replace('+', ' ', $value);
                    ++$f;
                }
            }
        }
        //dump($output);
        return $output;
    }

    public static function getFieldValues(
        $id_ndk_customization_field,
        $id_product = 0,
        $id_category = 0,
        $id_parent_value = 0
    ) {
        return Db::getInstance()->executeS(
            '
				SELECT cfv.`id_ndk_customization_field_value` as id, cfvl.`value`, cfvl.`textmask`, cfvl.`description`, cfvl.`tags`, cfv.`price`, cfv.`color`, cfv.`set_quantity`, cfv.`quantity`, cfv.`default_value`,cfv.`input_type` ,  cfv.`quantity_min`, cfv.`quantity_max`, cfv.`step_quantity`, cfv.`id_product_value`, cfv.`influences_restrictions`, cfv.`influences_obligations`, cfv.`reference`, cfv.`type`, cfv.`type`, cfv.`id_parent_value`, cfv.`options`
				FROM `'.
                _DB_PREFIX_.
                'ndk_customization_field_value` cfv
				LEFT JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field_value_lang` cfvl ON (cfvl.`id_ndk_customization_field_value`= cfv.`id_ndk_customization_field_value`AND cfvl.`id_lang` = '.
                (int) Context::getContext()->language->id.
                ')
				WHERE cfv.`id_parent_value` = '.
                (int) $id_parent_value.
                ' AND cfv.`id_ndk_customization_field` = '.
                (int) $id_ndk_customization_field.
                '  AND NOT FIND_IN_SET( '.
                (int) $id_product.
                ', cfv.`excludes_products`) AND NOT FIND_IN_SET( '.
                (int) $id_category.
                ', cfv.`excludes_categories`) AND cfvl.`id_lang` = '.
                (int) Context::getContext()->language->id.
                ' GROUP BY cfv.`id_ndk_customization_field_value` ORDER BY cfv.position asc, cfvl.value asc'
        );
    }

    public static function recordCsv($id)
    {
        if (
            file_exists(
                _PS_IMG_DIR_.'scenes/'.'ndkcf/'.(int) $id.'.csv'
            )
        ) {
            $csv_file =
                _PS_IMG_DIR_.'scenes/'.'ndkcf/'.(int) $id.'.csv';
            //$csv_array = Ndkcf::csvToPriceRange($csv_file);
            $csv_array = NdkCfImporter::csvToArray($csv_file);

            Db::getInstance()->execute(
                '
				   DELETE FROM `'.
                    _DB_PREFIX_.
                    'ndk_customization_field_csv`
				   WHERE id_ndk_customization_field = '.
                    (int) $id
            );

            $i = 0;
            $sql = [];

            foreach ($csv_array as $lines) {
                $j = 0;
                foreach ($lines as $k => $v) {
                    if (0 == $j) {
                        $width = $v;
                    } else {
                        if ($j > 0 && !empty($v) && !empty($width)) {
                            $sql[] =
                        'INSERT into '.
                        _DB_PREFIX_.
                        'ndk_customization_field_csv (id_ndk_customization_field, width, height, price) VALUES ('.
                        (int) $id.
                        ', \''.
                        pSQL($width).
                        '\', \''.
                        pSQL($k).
                        '\', '.
                        (float) $v.')';
                        }
                    }
                    ++$j;
                }
                ++$i;
            }
            // dump($csv_array);
            // dump($sql);
            // exit();
            foreach ($sql as $req) {
                Db::getInstance()->execute($req);
            }
        }
    }

    public static function csvToPriceRange($csv_file)
    {
        $data_csv = Tools::file_get_contents($csv_file);
        //$data_csv_lines = explode("\n", $data_csv);
        $data_csv_lines = preg_split('/\\r\\n|\\r|\\n/', $data_csv);
        $dcl = 0;
        $csv_lines_array = [];
        $csv_col_array = [];
        $csv_array = [];
        //dump($data_csv_lines);
        exit();
        foreach ($data_csv_lines as $key => $value) {
            $line_csv = explode(';', $value);
            if (0 === $dcl) {
                $removed = array_shift($line_csv);
            }

            $csv_lines_array[] = $line_csv;
            if (0 === $dcl) {
                foreach ($line_csv as $l => $v) {
                    $csv_col_array[] = $v;
                }
            }

            ++$dcl;
        }
        foreach ($csv_col_array as $col => $value_col) {
            foreach ($csv_lines_array as $line => $values_line) {
                $csv_array[$value_col][$values_line[0]] =
                    $values_line[$col];
            }
        }

        return $csv_array;
    }

    public static function getPriceTab($field)
    {
        if (
            file_exists(
                _PS_IMG_DIR_.'scenes/'.'ndkcf/'.(int) $field.'.csv'
            )
        ) {
            $csv_file =
                _PS_IMG_DIR_.'scenes/'.'ndkcf/'.(int) $field.'.csv';
            $csv_array = NdkCf::csvToPriceRange($csv_file);

            $data_csv = Tools::file_get_contents($csv_file);
            $data_csv_lines = explode("\r", $data_csv);
            $table = [];
            foreach ($data_csv_lines as $key => $value) {
                $new_line = explode(';', $value);
                $table[] = $new_line;
            }

            return $table;
        }
    }

    //lier à la bdd CSV
    public static function getDimensionPrice_old($field, $width, $height)
    {
        if (
            file_exists(
                _PS_IMG_DIR_.'scenes/'.'ndkcf/'.(int) $field.'.csv'
            )
        ) {
            $csv_file =
                _PS_IMG_DIR_.'scenes/'.'ndkcf/'.(int) $field.'.csv';
            $csv_array = NdkCf::csvToPriceRange($csv_file);

            $widthKey = [];
            $heightKey = [];

            $field = new NdkCf((int) $field);
            if (21 == $field->type || 19 == $field->type) {
                foreach ($csv_array as $key => $value) {
                    if ($width == $key) {
                        $widthKey[] = $key;
                        $line = $value;
                    }
                }

                $item_price = str_replace(',', '.', $line[$height]);

                return $item_price;
            } else {
                foreach ($csv_array as $key => $value) {
                    if ($width <= $key) {
                        $widthKey[] = $key;
                        $line = $value;
                    }
                }
                $line = $csv_array[min($widthKey)];

                foreach ($line as $key => $value) {
                    if ($height <= $key) {
                        $heightKey[] = $value;
                    }
                }

                return min($heightKey);
            }
        }
    }

    public static function getDimensionPrice($field, $width, $height)
    {
        $field = new NdkCf((int) $field);
        if (21 == $field->type || 19 == $field->type) {
            //on cherche la valeur exacte
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
                'SELECT price FROM '.
                    _DB_PREFIX_.
                    'ndk_customization_field_csv
						WHERE id_ndk_customization_field = '.
                    (int) $field->id.
                    '
						AND width = \''.
                    $width.
                    '\' AND height = \''.
                    $height.
                    '\''
            );
            //var_dump($result);
            $item_price = str_replace(',', '.', $result['price']);

            return $item_price;
        } else {
            // $sql = 'SELECT price FROM '._DB_PREFIX_.'ndk_customization_field_csv
            // 		WHERE id_ndk_customization_field = '.(int)$field->id.'
            // 		ORDER BY ABS(width-'.$width.') ASC, ABS(height-'.$height.') ASC LIMIT 1';
            $sql =
                'SELECT price FROM '.
                _DB_PREFIX_.
                'ndk_customization_field_csv
			WHERE id_ndk_customization_field = '.
                (int) $field->id.
                '
			AND width >= \''.
                $width.
                '\' AND height >= \''.
                $height.
                '\' 
			LIMIT 1';
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if ($result) {
                $item_price = str_replace(',', '.', $result[0]['price']);

                return $item_price;
            }
        }
    }

    public static function getRangePrice($field, $width, $height)
    {
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT * FROM '.
                _DB_PREFIX_.
                'ndk_customization_field_csv
				WHERE id_ndk_customization_field = '.
                (int) $field->id.
                '
				AND width >= '.
                $width.
                ' AND height >= '.
                $height.
                '
				ORDER BY width ASC'
        );

        if ($results) {
            //var_dump($results[0]);
            $item_price = str_replace(',', '.', $results[0]['price']);

            return $item_price;
        }
    }

    public static function getCustomFieldsForCreation($id_product, $id_category)
    {
        if ('all' == Configuration::get('NDK_CATEGORY_ASSOCIATION_MODE')) {
            $id_category = implode(
                '|',
                Product::getProductCategories($id_product)
            );
        }
        $id_address = (int) Context::getContext()->cart->id_address_invoice;
        $address = Address::initialize($id_address, true);
        $tax_manager = TaxManagerFactory::getManager(
            $address,
            Product::getIdTaxRulesGroupByIdProduct(
                (int) $id_product,
                Context::getContext()
            )
        );
        $product_tax_calculator = $tax_manager->getTaxCalculator();
        $usetax = Group::getPriceDisplayMethod(
            Group::getPriceDisplayMethod(
                Context::getContext()->customer->id_default_group
            )
        );
        if (0 == Product::$_taxCalculationMethod) {
            $usetax = true;
        } else {
            $usetax = false;
        }

        $id_lang = Context::getContext()->language->id;
        $fields = Db::getInstance()->executeS(
            '
				SELECT cf.`id_ndk_customization_field`, cf.`price`, cf.`options`, cf.`show_price`, cf.`quantity_link`, cf.`unit`, cf.`price_type`, cf.`price_per_caracter`, cfl.`name`, cfv.`id_ndk_customization_field_value`, cfvl.`value`, cfv.`price` as valuePrice , cfv.`set_quantity`, cfv.`quantity`, cfv.`default_value`,cfv.`input_type`
				FROM `'.
                _DB_PREFIX_.
                'ndk_customization_field` cf
				LEFT JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field_lang` cfl ON (cfl.`id_ndk_customization_field`= cf.`id_ndk_customization_field`AND cfl.`id_lang` = '.
                (int) $id_lang.
                ' )
				LEFT JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field_value` cfv ON (cfv.`id_ndk_customization_field`= cf.`id_ndk_customization_field`)
				LEFT JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field_value_lang` cfvl ON (cfvl.`id_ndk_customization_field_value`= cf.`id_ndk_customization_field`AND cfvl.`id_lang` = '.
                (int) $id_lang.
                ')
				WHERE (FIND_IN_SET( '.
                (int) $id_product.
                ', cf.`products`) OR CONCAT(",", cf.`categories`, ",") REGEXP ",('.
                $id_category.
                '),") AND cf.type NOT IN(5, 99)
				 GROUP BY  cf.`id_ndk_customization_field` ORDER BY cf.`ref_position`'
        );

        $i = 0;
        foreach ($fields as $field) {
            $fields[$i]['options'] = Tools::jsonDecode($field['options'], true);
            $fields[$i]['values'] = Db::getInstance()->executeS(
                'SELECT cfv.`id_ndk_customization_field_value` as id, cfvl.`value`, cfv.`reference`, cfvl.`description`,  cfv.`price`, cfv.`color`, cfv.`set_quantity`, cfv.`quantity`, cfv.`default_value`,cfv.`input_type` , cfv.`quantity_min`, cfv.`quantity_max`, cfv.`step_quantity`
				FROM `'.
                    _DB_PREFIX_.
                    'ndk_customization_field_value` cfv
				LEFT JOIN `'.
                    _DB_PREFIX_.
                    'ndk_customization_field_value_lang` cfvl ON (cfvl.`id_ndk_customization_field_value`= cfv.`id_ndk_customization_field_value`AND cfvl.`id_lang` = '.
                    (int) $id_lang.
                    ')
				WHERE cfv.`id_ndk_customization_field` = '.
                    (int) $field['id_ndk_customization_field'].
                    '  AND NOT FIND_IN_SET( '.
                    (int) $id_product.
                    ', cfv.`excludes_products`) AND NOT FIND_IN_SET( '.
                    (int) $id_category.
                    ', cfv.`excludes_categories`) '
            );

            if ('percent' == $fields[$i]['price_type']) {
                $usetax = false;
            }

            if ($usetax) {
                $fields[$i]['price'] = $product_tax_calculator->addTaxes(
                    $field['price']
                );
                $fields[$i][
                    'price_per_caracter'
                ] = $product_tax_calculator->addTaxes(
                    $field['price_per_caracter']
                );
            }

            $j = 0;
            foreach ($fields[$i]['values'] as $value) {
                $fields[$i]['values'][$j]['price'] = $usetax
                    ? $product_tax_calculator->addTaxes($value['price'])
                    : $value['price'];
                ++$j;
            }

            ++$i;
        }

        return $fields;
    }

    public static function getFieldFromPrice($id_product, $id_category)
    {
        if ('all' == Configuration::get('NDK_CATEGORY_ASSOCIATION_MODE')) {
            $id_category = implode(
                '|',
                Product::getProductCategories($id_product)
            );
        }
        $id_address = (int) Context::getContext()->cart->id_address_invoice;
        $address = Address::initialize($id_address, true);
        $tax_manager = TaxManagerFactory::getManager(
            $address,
            Product::getIdTaxRulesGroupByIdProduct(
                (int) $id_product,
                Context::getContext()
            )
        );
        $product_tax_calculator = $tax_manager->getTaxCalculator();
        $usetax = Group::getPriceDisplayMethod(
            Group::getPriceDisplayMethod(
                Context::getContext()->customer->id_default_group
            )
        );
        if (0 == Product::$_taxCalculationMethod) {
            $usetax = true;
        } else {
            $usetax = false;
        }

        $id_lang = Context::getContext()->language->id;
        $fields = Db::getInstance()->executeS(
            '
				SELECT cf.`price`, cf.`unit`
				FROM `'.
                _DB_PREFIX_.
                'ndk_customization_field` cf
				WHERE (FIND_IN_SET( '.
                (int) $id_product.
                ', cf.`products`) OR CONCAT(",", cf.`categories`, ",") REGEXP ",('.
                $id_category.
                '),") AND cf.type = 99
				 GROUP BY  cf.`id_ndk_customization_field` ORDER BY cf.`id_ndk_customization_field` LIMIT 1'
        );

        $i = 0;
        foreach ($fields as $field) {
            if ($usetax) {
                $fields[$i]['price'] = $product_tax_calculator->addTaxes(
                    $field['price']
                );
            }

            ++$i;
        }

        return $fields;
    }

    /**
     * @param mixed $id_product
     * @param mixed $id_category
     * @param mixed $name
     *
     * @return mixed
     *
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    public static function findByName($id_product, $id_category, $name)
    {
        if ('all' == Configuration::get('NDK_CATEGORY_ASSOCIATION_MODE')) {
            $id_category = implode(
                '|',
                Product::getProductCategories($id_product)
            );
        }
        $id_lang = Context::getContext()->language->id;
        $fields = Db::getInstance()->executeS(
            '
				SELECT cf.`id_ndk_customization_field`
				FROM `'.
                _DB_PREFIX_.
                'ndk_customization_field` cf
				LEFT JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field_lang` cfl ON (cfl.`id_ndk_customization_field`= cf.`id_ndk_customization_field`AND cfl.`id_lang` = '.
                (int) $id_lang.
                ' )
				WHERE (FIND_IN_SET( '.
                (int) $id_product.
                ', cf.`products`) OR CONCAT(",", cf.`categories`, ",") REGEXP ",('.
                $id_category.
                '),")
				AND cfl.`name` = \''.
                mysql_real_escape_string($name).
                '\''
        );
        if (sizeof($fields) > 0) {
            return $fields[0]['id_ndk_customization_field'];
        } else {
            return 0;
        }
    }

    /**
     * @return array|false|mysqli_result|PDOStatement|resource
     *
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    public static function getAllCustomFields()
    {
        $id_lang = Context::getContext()->language->id;
        $fields = Db::getInstance()->executeS(
            '
				SELECT cf.`id_ndk_customization_field`, cfl.`name`, CONCAT(cfl.`name`, \' (\', cfl.`admin_name`, \')\') as adminname,  cf.`is_visual`, cf.`configurator` ,cf.`draggable`,cf.`resizeable`, cf.`rotateable`, cf.`orienteable`
				FROM `'.
                _DB_PREFIX_.
                'ndk_customization_field` cf
				LEFT JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field_lang` cfl ON (cfl.`id_ndk_customization_field`= cf.`id_ndk_customization_field`AND cfl.`id_lang` = '.
                (int) $id_lang.
                ' )
				WHERE cfl.`id_lang` = '.
                (int) $id_lang.
                '
				GROUP BY  cf.`id_ndk_customization_field` ORDER BY cf.`id_ndk_customization_field`'
        );

        $i = 0;
        foreach ($fields as $field) {
            $fields[$i]['values'] = Db::getInstance()->executeS(
                '
				SELECT cfv.`id_ndk_customization_field_value` as id, cfvl.`value`,  cfvl.`description`, cfv.`price`, cfv.`color`, cfv.`set_quantity`, cfv.`quantity`, cfv.`default_value`,cfv.`input_type` ,  cfv.`quantity_min`, cfv.`quantity_max`, cfv.`step_quantity`
				FROM `'.
                    _DB_PREFIX_.
                    'ndk_customization_field_value` cfv
				LEFT JOIN `'.
                    _DB_PREFIX_.
                    'ndk_customization_field_value_lang` cfvl ON (cfvl.`id_ndk_customization_field_value`= cfv.`id_ndk_customization_field`AND cfvl.`id_lang` = '.
                    (int) $id_lang.
                    ')
				WHERE cfv.`id_ndk_customization_field` = '.
                    (int) $field['id_ndk_customization_field']
            );

            ++$i;
        }

        return $fields;
    }

    /**
     * @return array|false|mysqli_result|PDOStatement|resource|null
     *
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    public function getValues()
    {
        $id_lang = Context::getContext()->language->id;
        $key_from_id =
            (int) $this->values_from_id > 0
                ? (int) $this->values_from_id
                : (int) $this->id;
        $sql =
            'SELECT cfv.`id_ndk_customization_field_value` as id, cfvl.`value`,  cfvl.`description`, cfv.`price`, cfv.`color`, cfv.`set_quantity`, cfv.`quantity`, cfv.`default_value`,cfv.`input_type` , cfv.`quantity_min`, cfv.`quantity_max`, cfv.`step_quantity`, cfv.`influences_restrictions`
			FROM `'.
            _DB_PREFIX_.
            'ndk_customization_field_value` cfv
			LEFT JOIN `'.
            _DB_PREFIX_.
            'ndk_customization_field_value_lang` cfvl ON (cfvl.`id_ndk_customization_field_value`= cfv.`id_ndk_customization_field`AND cfvl.`id_lang` = '.
            (int) $id_lang.
            ')
			WHERE cfv.`id_ndk_customization_field` = '.
            (int) $key_from_id;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $result;
    }

    /**
     * @return array|false|mysqli_result|PDOStatement|resource|null
     *
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    public function getValuesId()
    {
        $id_lang = Context::getContext()->language->id;
        $sql =
            'SELECT cfv.`id_ndk_customization_field_value` as id
			FROM `'.
            _DB_PREFIX_.
            'ndk_customization_field_value` cfv
			WHERE cfv.`id_ndk_customization_field` = '.
            (int) $this->id;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $result;
    }

    public static function getFieldValuesId($id)
    {
        $id_lang = Context::getContext()->language->id;
        $sql =
            'SELECT cfv.`id_ndk_customization_field_value` as id
			FROM `'.
            _DB_PREFIX_.
            'ndk_customization_field_value` cfv
			WHERE cfv.`id_ndk_customization_field` = '.
            (int) $id;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $result;
    }

    public static function getCartProductCustomization(
        $id_cart,
        $id_product,
        $id_product_attribute = 0,
        $ref_product = 0
    ) {
        if ($id_product_attribute < 1) {
            $id_product_attribute = 0;
        }

        if (0 == $ref_product) {
            $ref_product = $id_product;
        }

        $prod = new Product($ref_product);
        $categories = $prod->getCategories();
        $id_lang = Context::getContext()->language->id;

        $id_address = (int) Context::getContext()->cart->id_address_invoice;
        $address = Address::initialize($id_address, true);
        $tax_manager = TaxManagerFactory::getManager(
            $address,
            Product::getIdTaxRulesGroupByIdProduct(
                (int) $id_product,
                Context::getContext()
            )
        );
        $product_tax_calculator = $tax_manager->getTaxCalculator();
        $usetax = Group::getPriceDisplayMethod(
            Group::getPriceDisplayMethod(
                Context::getContext()->customer->id_default_group
            )
        );
        if (0 == Product::$_taxCalculationMethod) {
            $usetax = true;
        } else {
            $usetax = false;
        }

        $sql =
            'SELECT c.id_customization, c.quantity as orderQuantity, ncf.`price` as price, ncf.`unit`, ncf.`price_type`, ncf.`price_per_caracter`, ncfv.`price`as valuePrice, cd.value as value, ncfvl.value as optionValue, ncfv.`id_ndk_customization_field_value`, ncfv.`set_quantity`, ncfv.`quantity`, ncfv.`default_value`,ncfv.`input_type`
			FROM `'.
            _DB_PREFIX_.
            'customization` c
			LEFT JOIN `'.
            _DB_PREFIX_.
            'customized_data` cd ON (cd.`id_customization`= c.`id_customization`)
			LEFT JOIN `'.
            _DB_PREFIX_.
            'customization_field` cf ON (cf.`id_customization_field`= cd.`index`)
			LEFT JOIN `'.
            _DB_PREFIX_.
            'customization_field_lang` cfl ON (cfl.`id_customization_field`= cf.`id_customization_field`AND cfl.`id_lang` = '.
            (int) $id_lang.
            ' )
			LEFT JOIN `'.
            _DB_PREFIX_.
            'ndk_customization_field_lang` ncfl ON (ncfl.`name`= cfl.`name`AND ncfl.`id_lang` = '.
            (int) $id_lang.
            ' )
			LEFT JOIN `'.
            _DB_PREFIX_.
            'ndk_customization_field` ncf ON (ncf.`id_ndk_customization_field`= ncfl.`id_ndk_customization_field`)
			LEFT JOIN `'.
            _DB_PREFIX_.
            'ndk_customization_field_value` ncfv ON (ncfv.`id_ndk_customization_field`= ncf.`id_ndk_customization_field`)
			LEFT JOIN `'.
            _DB_PREFIX_.
            'ndk_customization_field_value_lang` ncfvl ON (ncfvl.`id_ndk_customization_field_value`= ncfv.`id_ndk_customization_field_value` AND ncfvl.`id_lang` = '.
            (int) $id_lang.
            ' )

			WHERE c.`id_product` = '.
            (int) $id_product.
            '  AND c.`id_product_attribute` = '.
            (int) $id_product_attribute.
            '
			AND c.`id_cart` = '.
            $id_cart.
            ' AND c.`in_cart` = 1
			AND ncfvl.`value` <> ""
			AND ncfvl.`id_lang` = '.
            (int) $id_lang.
            '
			AND ( FIND_IN_SET( '.
            (int) $ref_product.
            ', ncf.`products`)
			OR FIND_IN_SET( '.
            (int) $categories.
            ', ncf.`categories`))';

        //var_dump($sql);die();
        $fields = Db::getInstance()->executeS($sql);

        $i = 0;
        foreach ($fields as $field) {
            if ($usetax) {
                $fields[$i]['price'] = $product_tax_calculator->addTaxes(
                    $field['price']
                );
                $fields[$i][
                    'price_per_caracter'
                ] = $product_tax_calculator->addTaxes(
                    $field['price_per_caracter']
                );
                $fields[$i]['valuePrice'] = $product_tax_calculator->addTaxes(
                    $field['valuePrice']
                );
            }

            ++$i;
        }

        return $fields;
    }

    public static function getCustomizationPrice($id_field, $value, $id_product)
    {
        $id_lang = Context::getContext()->language->id;

        $values_from_id = Db::getInstance()->getValue(
            '
				SELECT `values_from_id`
				FROM `'.
                _DB_PREFIX_.
                'ndk_customization_field`
				WHERE id_ndk_customization_field = '.
                (int) $id_field
        );

        if ((int) $values_from_id > 0) {
            $id_field = (int) $values_from_id;
        }

        $id_address = (int) Context::getContext()->cart->id_address_invoice;
        $address = Address::initialize($id_address, true);
        $tax_manager = TaxManagerFactory::getManager(
            $address,
            Product::getIdTaxRulesGroupByIdProduct(
                (int) $id_product,
                Context::getContext()
            )
        );
        $product_tax_calculator = $tax_manager->getTaxCalculator();
        $usetax = Group::getPriceDisplayMethod(
            Group::getPriceDisplayMethod(
                Context::getContext()->customer->id_default_group
            )
        );
        if (0 == Product::$_taxCalculationMethod) {
            $usetax = true;
        } else {
            $usetax = false;
        }
        //$value = utf8_decode($value);
        //CONVERT(_utf8  "'.pSQL($value).'" USING utf8) COLLATE utf8_general_ci)

        $sql =
            'SELECT ncf.`id_ndk_customization_field`,ncf.`type`, ncf.`price` as price, ncf.`quantity_link`, ncf.`unit`, ncf.`price_type`, ncf.`price_per_caracter`, ncfv.`id_ndk_customization_field_value`, ncfv.`price`as valuePrice, ncfvl.value, ncfv.`reference`, ncf.`show_price`
			FROM `'.
            _DB_PREFIX_.
            'ndk_customization_field` ncf
			LEFT JOIN `'.
            _DB_PREFIX_.
            'ndk_customization_field_value` ncfv ON (ncfv.`id_ndk_customization_field`= ncf.`id_ndk_customization_field` )
			LEFT JOIN `'.
            _DB_PREFIX_.
            'ndk_customization_field_value_lang` ncfvl ON (ncfvl.`id_ndk_customization_field_value`= ncfv.`id_ndk_customization_field_value` AND ncfvl.`value` = "'.
            pSQL($value).
            '" AND ncfvl.`id_lang` = '.
            (int) $id_lang.
            ' AND ncfvl.`value` <> "" )
			WHERE (ncf.`id_ndk_customization_field` = '.
            (int) $id_field.
            ' AND (ncf.price != 0 OR ncf.price_per_caracter > 0) ) OR (ncfv.`id_ndk_customization_field` = '.
            (int) $id_field.
            ' AND `value` IS NOT NULL AND ncfvl.`value` = "'.
            pSQL($value).
            '" AND ncfvl.`id_lang` = '.
            (int) $id_lang.
            ')';

        // $sql =
        // 'SELECT ncf.`id_ndk_customization_field`,ncf.`type`, ncf.`price` as price, ncf.`quantity_link`, ncf.`unit`, ncf.`price_type`, ncf.`price_per_caracter`, ncfv.`id_ndk_customization_field_value`, ncfv.`price`as valuePrice, ncfvl.value, ncfv.`reference`, ncf.`show_price`
        // FROM `'._DB_PREFIX_.'ndk_customization_field` ncf
        // LEFT JOIN `'._DB_PREFIX_.'ndk_customization_field_value` ncfv ON (ncfv.`id_ndk_customization_field`= ncf.`id_ndk_customization_field` )
        // LEFT JOIN `'._DB_PREFIX_.'ndk_customization_field_value_lang` ncfvl ON (ncfvl.`id_ndk_customization_field_value`= ncfv.`id_ndk_customization_field_value` AND ncfvl.`value` = "'.pSQL($value).'" AND ncfvl.`id_lang` = '.(int)$id_lang.' AND ncfvl.`value` <> "" )
        // WHERE ncf.`id_ndk_customization_field` = '.(int)$id_field.'
        // AND (
        // 	(ncf.price > 0 OR ncf.price_per_caracter > 0)
        // 	OR
        // 	(ncfvl.`value` = "'.pSQL($value).'" AND ncfvl.`id_lang` = '.(int)$id_lang.')
        // 	)
        // )';

        //dump($sql);
        $fields = Db::getInstance()->executeS($sql);
        //var_dump($fields);
        $i = 0;
        foreach ($fields as $field) {
            if ($usetax) {
                $fields[$i]['price'] = $product_tax_calculator->addTaxes(
                    $field['price']
                );
                $fields[$i][
                    'price_per_caracter'
                ] = $product_tax_calculator->addTaxes(
                    $field['price_per_caracter']
                );
                $fields[$i]['valuePrice'] = $product_tax_calculator->addTaxes(
                    $field['valuePrice']
                );
            } else {
                $fields[$i]['price'] = $field['price'];
                $fields[$i]['price_per_caracter'] =
                    $field['price_per_caracter'];
                $fields[$i]['valuePrice'] = $field['valuePrice'];
            }

            ++$i;
        }

        return $fields;
    }

    public static function getValueRef($id_field, $value, $id_product)
    {
        $id_lang = Context::getContext()->language->id;
        $sql =
            'SELECT ncfv.`reference`
			FROM `'.
            _DB_PREFIX_.
            'ndk_customization_field` ncf
			LEFT JOIN `'.
            _DB_PREFIX_.
            'ndk_customization_field_value` ncfv ON (ncfv.`id_ndk_customization_field`= ncf.`id_ndk_customization_field` )
			LEFT JOIN `'.
            _DB_PREFIX_.
            'ndk_customization_field_value_lang` ncfvl ON (ncfvl.`id_ndk_customization_field_value`= ncfv.`id_ndk_customization_field_value` AND ncfvl.`value` = "'.
            pSQL($value).
            '" AND ncfvl.`id_lang` = '.
            (int) $id_lang.
            ' AND ncfvl.`value` <> "" )
			WHERE ncfv.`id_ndk_customization_field` = '.
            (int) $id_field.
            ' AND ncfvl.`value` = "'.
            pSQL($value).
            '"';

        //var_dump($sql);
        $reference = Db::getInstance()->getRow($sql);

        return $reference['reference'];
    }

    public function updatePosition($way, $position)
    {
        if (
            !($res = Db::getInstance()->executeS(
                '
					SELECT ncf.`position`, ncf.`id_ndk_customization_field`
					FROM `'.
                    _DB_PREFIX_.
                    'ndk_customization_field` ncf
					WHERE ncf.`id_ndk_customization_field` = '.
                    (int) Tools::getValue('id').
                    '
					ORDER BY ncf.`position` ASC'
            ))
        ) {
            return false;
        }

        foreach ($res as $ndk_customization_field) {
            if (
                (int) $ndk_customization_field['id_ndk_customization_field'] ==
                (int) $this->id
            ) {
                $moved = $ndk_customization_field;
            }
        }

        if (!isset($moved) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return Db::getInstance()->execute(
            '
					UPDATE `'.
                _DB_PREFIX_.
                'ndk_customization_field`
					SET `position`= `position` '.
                ($way ? '- 1' : '+ 1').
                '
					WHERE `position`
					'.
                ($way
                    ? '> '.
                        (int) $moved['position'].
                        ' AND `position` <= '.
                        (int) $position
                    : '< '.
                        (int) $moved['position'].
                        ' AND `position` >= '.
                        (int) $position)
        ) &&
            Db::getInstance()->execute(
                '
					UPDATE `'.
                    _DB_PREFIX_.
                    'ndk_customization_field`
					SET `position` = '.
                    (int) $position.
                    '
					WHERE `id_ndk_customization_field`='.
                    (int) $moved['id_ndk_customization_field']
            );
    }

    public static function calculateFieldsPriceDisplay($id_product, $fields)
    {
        $id_address = (int) Context::getContext()->cart->id_address_invoice;
        $address = Address::initialize($id_address, true);
        $tax_manager = TaxManagerFactory::getManager(
            $address,
            Product::getIdTaxRulesGroupByIdProduct(
                (int) $id_product,
                Context::getContext()
            )
        );
        $product_tax_calculator = $tax_manager->getTaxCalculator();
        $usetax = Group::getPriceDisplayMethod(
            Group::getPriceDisplayMethod(
                Context::getContext()->customer->id_default_group
            )
        );

        $i = 0;
        foreach ($fields as $field) {
            if ('percent' == $fields[$i]['price_type']) {
                $usetax = false;
            }

            $fields[$i]['price'] = !$usetax
                ? $product_tax_calculator->removeTaxes($field['price'])
                : $field['price'];
            ++$i;
        }

        return $fields;
    }

    public static function getTargetsFields()
    {
        $id_lang = Context::getContext()->language->id;
        $fields = Db::getInstance()->executeS(
            '
				SELECT cf.`id_ndk_customization_field` as id, cfl.`admin_name` as name, gl.name as groupname 
				FROM `'.
                _DB_PREFIX_.
                'ndk_customization_field` cf
				LEFT JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field_lang` cfl ON (cfl.`id_ndk_customization_field`= cf.`id_ndk_customization_field` AND cfl.`id_lang` = '.
                (int) $id_lang.
                ' )
                LEFT JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field_group` g ON (FIND_IN_SET( cf.`id_ndk_customization_field`, g.`fields`))
                    LEFT JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field_group_lang` gl ON (gl.`id_ndk_customization_field_group`= g.`id_ndk_customization_field_group` AND gl.`id_lang` = '.
                (int) Context::getContext()->language->id.
                ')
				WHERE cf.`type` = 10 GROUP BY  cf.id_ndk_customization_field'
        );
        foreach ($fields as &$field) {
            $field['name'] =
                $field['name'].
                ('' != $field['groupname']
                    ? ' ['.$field['groupname'].']'
                    : '');
        }

        return $fields;
    }

    public static function getTargetsChilds($id)
    {
        $id_lang = Context::getContext()->language->id;
        $fields = Db::getInstance()->executeS(
            '
				SELECT cfv.`id_ndk_customization_field_value` as id, CONCAT(cfvl.`value`,\' [\', cfv.`reference`,\']\' , \' (\', cfv.`type`, \')\') as value
				FROM `'.
                _DB_PREFIX_.
                'ndk_customization_field_value` cfv
				LEFT JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field_value_lang` cfvl ON (cfvl.`id_ndk_customization_field_value`= cfv.`id_ndk_customization_field_value` AND cfvl.`id_lang` = '.
                (int) $id_lang.
                ' )
				WHERE cfv.`id_ndk_customization_field` = '.
                (int) $id.
                ' GROUP BY  cfv.id_ndk_customization_field_value'
        );

        return $fields;
    }

    public static function getIdByAdminName($query)
    {
        if ('' == $query) {
            return 0;
        }
        $id_lang = Context::getContext()->language->id;
        $fields = Db::getInstance()->getValue(
            '
				SELECT `id_ndk_customization_field` as id
				FROM `'.
                _DB_PREFIX_.
                'ndk_customization_field_lang`
				WHERE admin_name = "'.
                $query.
                '"'
        );

        return $fields;
    }

    public static function getFieldsLight($current, $types = false)
    {
        $id_lang = Context::getContext()->language->id;
        $sql =
            '
				SELECT cf.`id_ndk_customization_field` as id, cfl.`admin_name` as name , gl.name as groupname
				FROM `'.
            _DB_PREFIX_.
            'ndk_customization_field` cf
				LEFT JOIN `'.
            _DB_PREFIX_.
            'ndk_customization_field_group` g ON (FIND_IN_SET( cf.`id_ndk_customization_field`, g.`fields`))
				LEFT JOIN `'.
            _DB_PREFIX_.
            'ndk_customization_field_group_lang` gl ON (gl.`id_ndk_customization_field_group`= g.`id_ndk_customization_field_group` AND gl.`id_lang` = '.
            (int) Context::getContext()->language->id.
            ')

				LEFT JOIN `'.
            _DB_PREFIX_.
            'ndk_customization_field_lang` cfl ON (cfl.`id_ndk_customization_field`= cf.`id_ndk_customization_field` AND cfl.`id_lang` = '.
            (int) $id_lang.
            ' )
				WHERE  cf.`id_ndk_customization_field` != '.
            (int) $current.
            ' '.
            ($types ? 'AND cf.type IN ('.$types.')' : ' ').
            ' GROUP BY  cf.id_ndk_customization_field ORDER BY cfl.`admin_name`';

        $fields = Db::getInstance()->executeS($sql);
        foreach ($fields as &$field) {
            $field['name'] =
                $field['name'].
                ('' != $field['groupname']
                    ? ' ['.$field['groupname'].']'
                    : '');
        }

        return $fields;
    }

    public static function getInfluencesFields($ids, $qtty = false)
    {
        $id_lang = Context::getContext()->language->id;
        $fields = Db::getInstance()->executeS(
            '
				SELECT cf.`id_ndk_customization_field`, cfl.`admin_name`
				FROM `'.
                _DB_PREFIX_.
                'ndk_customization_field` cf
				LEFT JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field_lang` cfl ON (cfl.`id_ndk_customization_field`= cf.`id_ndk_customization_field` AND cfl.`id_lang` = '.
                (int) $id_lang.
                ' )
				WHERE cf.`id_ndk_customization_field` IN ('.
                $ids.
                ') GROUP BY  cf.id_ndk_customization_field'
        );

        $i = 0;

        foreach ($fields as $field) {
            $fields[$i]['values'] = Db::getInstance()->executeS(
                '
					SELECT cfv.`id_ndk_customization_field_value` as id, cfvl.`value` as name
					FROM `'.
                    _DB_PREFIX_.
                    'ndk_customization_field_value` cfv
					LEFT JOIN `'.
                    _DB_PREFIX_.
                    'ndk_customization_field_value_lang` cfvl ON (cfvl.`id_ndk_customization_field_value`= cfv.`id_ndk_customization_field_value` AND cfvl.`id_lang` = '.
                    (int) $id_lang.
                    ' )
					WHERE cfv.`id_ndk_customization_field`= ('.
                    $field['id_ndk_customization_field'].
                    ')'
            );

            if ($qtty && $qtty > 0) {
                $j = 0;
                foreach ($fields[$i]['values'] as $value) {
                    $fields[$i]['values'][$j]['id'] =
                        $value['id'].'['.$qtty.']';
                    ++$j;
                }
            }

            ++$i;
        }

        return $fields;
    }

    public static function createProductCustom(
        $product,
        $id_combination = 0,
        $price,
        $cusText
    ) {
        $id_lang = Context::getContext()->language->id;
        $languages = Language::getLanguages(false);
        $customProd = new Product(
            null,
            false,
            null,
            Context::getContext()->shop->id
        );
        $id_address = (int) Context::getContext()->cart->id_address_invoice;
        $address = Address::initialize($id_address, true);
        $tax_manager = TaxManagerFactory::getManager(
            $address,
            Product::getIdTaxRulesGroupByIdProduct(
                (int) $product->id,
                Context::getContext()
            )
        );
        $product_tax_calculator = $tax_manager->getTaxCalculator();
        $usetax = Group::getPriceDisplayMethod(
            Group::getPriceDisplayMethod(
                Context::getContext()->customer->id_default_group
            )
        );
        if (0 == Product::$_taxCalculationMethod) {
            $usetax = true;
        } else {
            $usetax = false;
        }

        if ($usetax) {
            $price = $product_tax_calculator->removeTaxes($price);
        }

        if ($id_combination > 0) {
            $combNames = $product->getAttributesResume($id_lang);
            foreach ($combNames as $row) {
                if ($row['id_product_attribute'] == $id_combination) {
                    $combName = $row['attribute_designation'];
                }
            }
        } else {
            $combName = false;
        }
        $combName = false;

        foreach ($languages as $lang) {
            $name =
                $product->name[$id_lang].
                (isset($combName) && '' != $combName ? ' - '.$combName : '');

            $customProd->name[$lang['id_lang']] = Tools::truncateString(
                $name,
                125
            );

            $link_rewrite = preg_replace('/[\s\'\:\/\[\]\-\|]+/', ' ', $name);
            $link_rewrite = str_replace([' ', '/', '|'], '-', $link_rewrite);
            $link_rewrite = str_replace(
                ['--', '---', '----'],
                '-',
                $link_rewrite
            );
            $link_rewrite = Tools::truncateString(
                $link_rewrite.' '.$name,
                100
            );
            $customProd->link_rewrite[$lang['id_lang']] = Tools::str2url(
                $link_rewrite.'-00'
            );
            $customProd->description_short[$lang['id_lang']] =
                $cusText.' :'.$name;
        }

        $customProd->reference = Tools::str2url(
            'custom-'.
                $product->id.
                '-'.
                $id_combination.
                '-'.
                Context::getContext()->cart->id
        );
        $customProd->supplier_reference = Tools::str2url('myndkcustomprod');

        //$customProd->id_category_default = (int)Configuration::get('NDK_ACF_CAT');
        $customProd->id_category_default = (int) $product->id_category_default;

        //$customProd->advanced_stock_managment = $product->advanced_stock_managment;
        $customProd->customizable = 1;
        $customProd->id_supplier = (int) $product->id_supplier;
        $customProd->id_manufacturer = (int) $product->id_manufacturer;
        $customProd->indexed = 0;
        $customProd->condition = $product->condition;

        $customProd->is_virtual = $product->is_virtual;
        $customProd->cache_is_pack = true;
        //forpack
        // $customProd->cache_is_pack = 1;
        // if (Configuration::get('NDK_SPLIT_PACK') == 1) {
        //     $customProd->pack_stock_type = 0;
        // } else {
        //     $customProd->pack_stock_type = 1;
        // }

        $customProd->id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct(
            (int) $product->id,
            Context::getContext()
        );
        $customProd->out_of_stock = $product->out_of_stock;
        $customProd->additional_shipping_cost =
            $product->additional_shipping_cost;
        $customProd->visibility = 'none';
        $customProd->price = $price;
        $customProd->quantity = 1;
        $customProd->minimal_quantity = 0;
        $customProd->uploadable_files = 99;
        $customProd->text_fields = 99;

        $customProd->width = $product->width;
        $customProd->height = $product->height;
        $customProd->depth = $product->depth;
        $weight = $product->weight;
        $customProd->location = $product->location;
        if ($id_combination > 0) {
            $comb = new Combination((int) $id_combination);
            $weight = $weight + $comb->weight;
            $customProd->location = $comb->location;
        }

        $customProd->weight = $weight;

        $customProd->ecotax = $product->ecotax;
        $customProd->upc = $product->upc;

        $customProd->advanced_stock_management =
            $product->advanced_stock_management;
        $customProd->depends_on_stock = $product->depends_on_stock;

        foreach ($languages as $lang) {
            $customProd->link_rewrite[$lang['id_lang']] = Tools::str2url(
                $link_rewrite.'-'.time()
            );
        }

        $customProd->add();

        //$customProd->updateCategories( (int)Configuration::get('NDK_ACF_CAT') );
        Db::getInstance()->execute(
            'INSERT INTO `'.
                _DB_PREFIX_.
                'category_product` (`id_category`, `id_product`) VALUES ('.
                (int) Configuration::get('NDK_ACF_CAT').
                ', '.
                (int) $customProd->id.
                ')'
        );
        Db::getInstance()->execute(
            'UPDATE `'.
                _DB_PREFIX_.
                'stock_available` SET out_of_stock = '.
                (int) $product->out_of_stock.
                ' WHERE id_product = '.
                (int) $customProd->id
        );

        $warehouses = Db::getInstance()->executeS(
            ' SELECT DISTINCT(id_warehouse),  location FROM `'.
                _DB_PREFIX_.
                'warehouse_product_location` WHERE id_product = '.
                $product->id.
                ' AND id_product_attribute = '.
                (int) $id_combination
        );
        foreach ($warehouses as $warehouse) {
            Db::getInstance()->execute(
                '
			INSERT INTO `'.
                    _DB_PREFIX_.
                    'warehouse_product_location` (`id_warehouse`,`id_product`,`id_product_attribute`,`location`)
			 	VALUES ('.
                    (int) $warehouse['id_warehouse'].
                    ', '.
                    (int) $customProd->id.
                    ', 0, "'.
                    pSQL($warehouse['location']).
                    '") '
            );
        }

        //$carrier_list = $product->getCarriers();
        //$customProd->setCarriers($carrier_list);
        $customProd->setCarriers(self::getCarriersIds((int) $product->id));
        //Product::duplicateSpecificPrices((int)$product->id, $customProd->id);
        foreach (
            SpecificPrice::getByProductId(
                (int) $product->id,
                (int) $id_combination
            )
            as $data
        ) {
            $specific_price = new SpecificPrice(
                (int) $data['id_specific_price']
            );
            if ((int) $specific_price->reduction > 0) {
                $specific_price->id_product = (int) $customProd->id;
                $specific_price->id_product_attribute = 0;
                unset($specific_price->id);
                $specific_price->add();
            }
        }

        Product::duplicateAccessories((int) $product->id, $customProd->id);

        GroupReduction::duplicateReduction((int) $product->id, $customProd->id);
        //$combination_images = Product::duplicateAttributes((int)$product->id, $customProd->id);

        NdkCf::duplicateProductImagesAttibute(
            (int) $product->id,
            $customProd->id,
            $id_combination
        );
        //Image::duplicateProductImages((int)$product->id, $customProd->id, array());
        NdkCf::duplicateProductCategories($product->id, $customProd->id);

        return (int) $customProd->id;
    }

    public static function duplicateProductCategories($id_old, $id_new)
    {
        $sql =
            'SELECT `id_category`
					FROM `'.
            _DB_PREFIX_.
            'category_product`
					WHERE `id_product` = '.
            (int) $id_old;
        $result = Db::getInstance()->executeS($sql);

        $row = [];
        if ($result) {
            foreach ($result as $i) {
                $row[] =
                    '('.
                    implode(', ', [
                        (int) $id_new,
                        $i['id_category'],
                        '(SELECT tmp.max + 1 FROM (
						SELECT MAX(cp.`position`) AS max
						FROM `'.
                        _DB_PREFIX_.
                        'category_product` cp
						WHERE cp.`id_category`='.
                        (int) $i['id_category'].
                        ') AS tmp)',
                    ]).
                    ')';
            }
        }

        $flag = Db::getInstance()->execute(
            '
				INSERT IGNORE INTO `'.
                _DB_PREFIX_.
                'category_product` (`id_product`, `id_category`, `position`)
				VALUES '.
                implode(',', $row)
        );

        return $flag;
    }

    public static function duplicateProductImagesAttibute(
        $id_product_old,
        $id_product_new,
        $id_combination
    ) {
        $images_types = ImageType::getImagesTypes('products');
        if ((int) $id_combination > 0) {
            $result = Db::getInstance()->executeS(
                '
	        	SELECT `id_image`
	        	FROM `'.
                    _DB_PREFIX_.
                    'product_attribute_image`
	        	WHERE `id_product_attribute` = '.
                    (int) $id_combination
            );
        } else {
            $result = Db::getInstance()->executeS(
                '
	        	SELECT `id_image`
	        	FROM `'.
                    _DB_PREFIX_.
                    'image`
	        	WHERE `id_product` = '.
                    (int) $id_product_old.
                    ' AND cover=1'
            );
        }

        if (0 == sizeof($result)) {
            Image::duplicateProductImages(
                (int) $id_product_old,
                $id_product_new,
                []
            );
        } else {
            foreach ($result as $row) {
                $image_old = new Image($row['id_image']);
                $image_new = clone $image_old;
                unset($image_new->id);
                $image_new->id_product = (int) $id_product_new;
                //$image_new->cover = 1;

                // A new id is generated for the cloned image when calling add()
                if ($image_new->add()) {
                    $new_path = $image_new->getPathForCreation();
                    foreach ($images_types as $image_type) {
                        if (
                            file_exists(
                                _PS_PROD_IMG_DIR_.
                                    $image_old->getExistingImgPath().
                                    '-'.
                                    $image_type['name'].
                                    '.jpg'
                            )
                        ) {
                            if (!Configuration::get('PS_LEGACY_IMAGES')) {
                                $image_new->createImgFolder();
                            }
                            copy(
                                _PS_PROD_IMG_DIR_.
                                    $image_old->getExistingImgPath().
                                    '-'.
                                    $image_type['name'].
                                    '.jpg',
                                $new_path.'-'.$image_type['name'].'.jpg'
                            );
                            if (Configuration::get('WATERMARK_HASH')) {
                                $old_image_path =
                                    _PS_PROD_IMG_DIR_.
                                    $image_old->getExistingImgPath().
                                    '-'.
                                    $image_type['name'].
                                    '-'.
                                    Configuration::get('WATERMARK_HASH').
                                    '.jpg';
                                if (file_exists($old_image_path)) {
                                    copy(
                                        $old_image_path,
                                        $new_path.
                                            '-'.
                                            $image_type['name'].
                                            '-'.
                                            Configuration::get(
                                                'WATERMARK_HASH'
                                            ).
                                            '.jpg'
                                    );
                                }
                            }
                        }
                    }

                    if (
                        file_exists(
                            _PS_PROD_IMG_DIR_.
                                $image_old->getExistingImgPath().
                                '.jpg'
                        )
                    ) {
                        copy(
                            _PS_PROD_IMG_DIR_.
                                $image_old->getExistingImgPath().
                                '.jpg',
                            $new_path.'.jpg'
                        );
                    }

                    //NdkCf::replaceAttributeImageAssociationId($combination_images, (int)$image_old->id, (int)$image_new->id);

                    // Duplicate shop associations for images
                    $image_new->duplicateShops($id_product_old);
                } else {
                    return false;
                }
            }
        }
        //return Image::duplicateAttributeImageAssociations($combination_images);
    }

    public static function getCarriersIds($id_product)
    {
        $return = [];
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
			SELECT pc.id_carrier_reference
			FROM `'.
                _DB_PREFIX_.
                'product_carrier` pc
			WHERE pc.`id_product` = '.
                (int) $id_product.
                '
				AND pc.`id_shop` = '.
                (int) Context::getContext()->shop->id
        );
        foreach ($results as $row) {
            $return[] = $row['id_carrier_reference'];
        }

        return $return;
    }

    public static function deleteTempPackFromCart(
        $id_product,
        $id_customization = 0,
        $id_product_attribute = 0
    ) {
        $tempProd = new Product($id_product);
        if ('myndkcustomprod' == $tempProd->supplier_reference) {
            $tempProd->delete();
        }

        if ($id_customization > 0) {
            $customisation = new Customization((int) $id_customization);
            $search = Db::getInstance()->executeS(
                'SELECT fc.id_ndk_customization_field_configuration as id FROM '.
                    _DB_PREFIX_.
                    'ndk_customization_field_configuration fc WHERE fc.id_customization = '.
                    (int) $id_customization
            );
            if (sizeof($search) > 0) {
                //print(Tools::jsonEncode($search[0]['name']));
                $config = new ndkCfConfig((int) $search[0]['id']);
                if (Validate::isLoadedObject($config)) {
                    $config->delete();
                }
            }
            $customisation->delete();
        }
    }

    public static function deleteTempPackFromCart17(
        $id_product,
        $id_customization = 0,
        $id_product_attribute = 0
    ) {
        $tempProd = new Product($id_product);
        if ('myndkcustomprod' == $tempProd->supplier_reference) {
            $tempProd->delete();
        }

        if ((int) $id_customization > 0) {
            Db::getInstance()->execute(
                '
				   DELETE FROM `'.
                    _DB_PREFIX_.
                    'customized_data`
				   WHERE id_customization = '.
                    (int) $id_customization
            );

            Db::getInstance()->execute(
                '
				   DELETE FROM `'.
                    _DB_PREFIX_.
                    'customization`
				   WHERE id_customization = '.
                    (int) $id_customization
            );

            $search = Db::getInstance()->executeS(
                'SELECT fc.id_ndk_customization_field_configuration as id FROM '.
                    _DB_PREFIX_.
                    'ndk_customization_field_configuration fc WHERE fc.id_customization = '.
                    (int) $id_customization
            );
            if (sizeof($search) > 0) {
                //print(Tools::jsonEncode($search[0]['name']));
                $config = new ndkCfConfig((int) $search[0]['id']);
                $config->delete();
            }
        }

        Db::getInstance()->execute(
            '
			   DELETE FROM `'.
                _DB_PREFIX_.
                'cart_product`
			   WHERE id_product = '.
                (int) $id_product.
                ' AND id_product_attribute='.
                (int) $id_product_attribute.
                ' AND id_cart ='.
                (int) Context::getContext()->cart->id
        );
    }

    public static function copyImg(
        $id_entity,
        $id_image = null,
        $url,
        $entity = 'products',
        $regenerate = true
    ) {
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

        switch ($entity) {
            default:
            case 'products':
                $image_obj = new Image($id_image);
                $path = $image_obj->getPathForCreation();
                break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_.(int) $id_entity;
                break;
            case 'manufacturers':
                $path = _PS_MANU_IMG_DIR_.(int) $id_entity;
                break;
            case 'suppliers':
                $path = _PS_SUPP_IMG_DIR_.(int) $id_entity;
                break;
        }

        $url = str_replace(' ', '%20', trim($url));
        $url = urldecode($url);
        $parced_url = parse_url($url);

        if (isset($parced_url['path'])) {
            $uri = ltrim($parced_url['path'], '/');
            $parts = explode('/', $uri);
            foreach ($parts as &$part) {
                $part = urlencode($part);
            }
            unset($part);
            $parced_url['path'] = '/'.implode('/', $parts);
        }

        if (isset($parced_url['query'])) {
            $query_parts = [];
            parse_str($parced_url['query'], $query_parts);
            $parced_url['query'] = http_build_query($query_parts);
        }

        if (!function_exists('http_build_url')) {
            require_once _PS_TOOL_DIR_.'http_build_url/http_build_url.php';
        }

        $url = http_build_url('', $parced_url);

        // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
        if (!ImageManager::checkImageMemoryLimit($url)) {
            return false;
        }

        $orig_tmpfile = $tmpfile;

        // 'file_exists' doesn't work on distant file, and getimagesize makes the import slower.
        // Just hide the warning, the processing will be the same.
        if (Tools::copy($url, $tmpfile)) {
            $tgt_width = $tgt_height = 0;
            $src_width = $src_height = 0;
            $error = 0;
            ImageManager::resize(
                $tmpfile,
                $path.'.jpg',
                null,
                null,
                'jpg',
                false,
                $error,
                $tgt_width,
                $tgt_height,
                5,
                $src_width,
                $src_height
            );
            $images_types = ImageType::getImagesTypes($entity, true);

            if ($regenerate) {
                $previous_path = null;
                $path_infos = [];
                $path_infos[] = [$tgt_width, $tgt_height, $path.'.jpg'];
                foreach ($images_types as $image_type) {
                    $tmpfile = self::get_best_path(
                        $image_type['width'],
                        $image_type['height'],
                        $path_infos
                    );

                    if (
                        ImageManager::resize(
                            $tmpfile,
                            $path.
                                '-'.
                                Tools::stripslashes($image_type['name']).
                                '.jpg',
                            $image_type['width'],
                            $image_type['height'],
                            'jpg',
                            false,
                            $error,
                            $tgt_width,
                            $tgt_height,
                            5,
                            $src_width,
                            $src_height
                        )
                    ) {
                        // the last image should not be added in the candidate list if it's bigger than the original image
                        if (
                            $tgt_width <= $src_width &&
                            $tgt_height <= $src_height
                        ) {
                            $path_infos[] = [
                                $tgt_width,
                                $tgt_height,
                                $path.
                                '-'.
                                Tools::stripslashes($image_type['name']).
                                '.jpg',
                            ];
                        }
                    }
                    if (
                        in_array($image_type['id_image_type'], $watermark_types)
                    ) {
                        Hook::exec('actionWatermark', [
                            'id_image' => $id_image,
                            'id_product' => $id_entity,
                        ]);
                    }
                }
            }
        } else {
            @unlink($orig_tmpfile);

            return false;
        }
        unlink($orig_tmpfile);

        return true;
    }

    public static function get_best_path($tgt_width, $tgt_height, $path_infos)
    {
        $path_infos = array_reverse($path_infos);
        $path = '';
        foreach ($path_infos as $path_info) {
            list($width, $height, $path) = $path_info;
            if ($width >= $tgt_width && $height >= $tgt_height) {
                return $path;
            }
        }

        return $path;
    }

    public static function getproductsLight($ids_product)
    {
        $id_lang = Context::getContext()->language->id;
        if ($ids_product && !empty($ids_product)) {
            return Db::getInstance()->executeS(
                '
					SELECT p.`id_product`, p.`reference`, pl.`name`
					FROM `'.
                    _DB_PREFIX_.
                    'product`p
					LEFT JOIN `'.
                    _DB_PREFIX_.
                    'product_lang` pl ON (
						p.`id_product` = pl.`id_product`
						AND pl.`id_lang` = '.
                    (int) $id_lang.
                    '
					)
					WHERE p.id_product IN ('.
                    $ids_product.
                    ') GROUP BY id_product'
            );
        }

        return false;
    }

    public static function getCategoryproductsLight(
        $id_category,
        $exclude_custom = true
    ) {
        $id_lang = Context::getContext()->language->id;

        return Db::getInstance()->executeS(
            '
				SELECT p.`id_product`, p.`reference`, pl.`name`
				FROM `'.
                _DB_PREFIX_.
                'product`p 
				LEFT JOIN `'.
                _DB_PREFIX_.
                'product_shop` ps
					ON (p.`id_product` = ps.`id_product`)
				LEFT JOIN `'.
                _DB_PREFIX_.
                'category_product` cp
					ON (p.`id_product` = cp.`id_product`)
				LEFT JOIN `'.
                _DB_PREFIX_.
                'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.
                (int) $id_lang.
                '
				)
				WHERE ps.id_shop = '.
                (int) Context::getContext()->shop->id.
                ' AND cp.id_category = '.
                (int) $id_category.
                ($exclude_custom
                    ? ' AND p.reference not like "custom-%"'
                    : '').
                ' GROUP BY p.id_product ORDER by name'
        );
    }

    public static function getProductInfos(
        $id_product,
        $id_product_attribute = 0,
        $id_group = 0,
        $id_value = 0,
        $only_active = true,
        $id_lang = null,
        $lite_result = true,
        Context $context = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        $id_lang = Context::getContext()->language->id;

        $sql =
            'SELECT p.*, product_shop.*, p.price as price_tax_exc, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, MAX(product_attribute_shop.id_product_attribute) id_product_attribute, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`, if(coalesce(pa.reference,"")="",p.reference,pa.reference) as reference,
							pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, MAX(image_shop.`id_image`) id_image,
							il.`legend`, m.`name` AS manufacturer_name, cl.`name` AS category_default,
							DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
							INTERVAL '.
            (Validate::isUnsignedInt(
                Configuration::get('PS_NB_DAYS_NEW_PRODUCT')
            )
                ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT')
                : 20).
            '
								DAY)) > 0 AS new, product_shop.price AS orderprice
						FROM `'.
            _DB_PREFIX_.
            'product` p
						'.
            Shop::addSqlAssociation('product', 'p').
            '
						LEFT JOIN `'.
            _DB_PREFIX_.
            'product_attribute` pa
						ON (p.`id_product` = pa.`id_product`)
						'.
            Shop::addSqlAssociation(
                'product_attribute',
                'pa',
                false,
                'product_attribute_shop.`default_on` = 1'
            ).
            '
						'.
            Product::sqlStock(
                'p',
                'product_attribute_shop',
                false,
                $context->shop
            ).
            '
						LEFT JOIN `'.
            _DB_PREFIX_.
            'category_lang` cl
							ON (product_shop.`id_category_default` = cl.`id_category`
							AND cl.`id_lang` = '.
            (int) $id_lang.
            Shop::addSqlRestrictionOnLang('cl').
            ')
						LEFT JOIN `'.
            _DB_PREFIX_.
            'category_product` cp
							ON (product_shop.`id_product` = cp.`id_product`
							AND cl.`id_lang` = '.
            (int) $id_lang.
            Shop::addSqlRestrictionOnLang('cl').
            ')
						LEFT JOIN `'.
            _DB_PREFIX_.
            'product_lang` pl
							ON (p.`id_product` = pl.`id_product`
							AND pl.`id_lang` = '.
            (int) $id_lang.
            Shop::addSqlRestrictionOnLang('pl').
            ')
						LEFT JOIN `'.
            _DB_PREFIX_.
            'image` i
							ON (i.`id_product` = p.`id_product`)'.
            Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').
            '
						LEFT JOIN `'.
            _DB_PREFIX_.
            'image_lang` il
							ON (image_shop.`id_image` = il.`id_image`
							AND il.`id_lang` = '.
            (int) $id_lang.
            ')
						LEFT JOIN `'.
            _DB_PREFIX_.
            'manufacturer` m
							ON m.`id_manufacturer` = p.`id_manufacturer`
						WHERE product_shop.`id_shop` = '.
            (int) $context->shop->id.
            '
							AND p.id_product = '.
            (int) $id_product.
            ($only_active ? ' AND product_shop.`active` = 1' : '').
            ' GROUP BY p.id_product';

        $sql .= ' ORDER BY p.id_product';

        //var_dump($sql);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $products = Product::getProductsProperties($id_lang, $result);
        $i = 0;
        foreach ($products as $product) {
            if ((int) $id_product > 0) {
                if (!isset($products[$i]['cover_image_id'])) {
                    $products[$i]['cover_image_id'] = $product['id_image'];
                }
                $products[$i]['price'] = NdkCfSpecificPrice::getPriceDiscounted(
                $product['price'],
                (int) $id_group,
                (int) $id_value,
                1,
                $product['id_product']
            );

                ++$i;
            }
        }

        if (!$result) {
            return [];
        }

        return $products;
        /* Modify SQL result */
//         if ((float) _PS_VERSION_ > 1.6) {
//             $i = 0;
//             foreach ($result as $row) {
//                 $result[$i]['price'] = self::setPriceTaxe(
//                     $row['price'],
//                     $id_product
//                 );
//                 ++$i;
//             }
//
//             return $result;
//         } else {
//             return Product::getProductsProperties($id_lang, $result);
//         }
    }

    public static function setPriceTaxe($price, $id_product)
    {
        $context = Context::getContext();
        $quantity = 1;
        $id_address = (int) Context::getContext()->cart->id_address_invoice;
        $address = Address::initialize($id_address, true);
        $tax_manager = TaxManagerFactory::getManager(
            $address,
            Product::getIdTaxRulesGroupByIdProduct(
                (int) $id_product,
                Context::getContext()
            )
        );
        $product_tax_calculator = $tax_manager->getTaxCalculator();
        $usetax = Group::getPriceDisplayMethod(
            Group::getPriceDisplayMethod(
                Context::getContext()->customer->id_default_group
            )
        );
        $usetax = PS_TAX_INC == Product::$_taxCalculationMethod;

        return $usetax ? $product_tax_calculator->addTaxes($price) : $price;
    }

    public static function getProductAttributeCombinations($id_product)
    {
        $combinations = [];
        $context = Context::getContext();
        $product = new Product($id_product, $context->language->id);
        $attributes_groups = $product->getAttributesGroups(
            $context->language->id
        );
        $att_grps = '';
        foreach ($attributes_groups as $k => $row) {
            $combinations[$row['id_product_attribute']]['attributes_values'][
                $row['id_attribute_group']
            ] = $row['attribute_name'];
            $combinations[$row['id_product_attribute']]['attributes_group'][
                $row['id_attribute_group']
            ] = $row['public_group_name'];

            $combinations[$row['id_product_attribute']][
                'attributes_groups'
            ] = @implode(
                ', ',
                $combinations[$row['id_product_attribute']]['attributes_group']
            );
            $att_grps =
                $combinations[$row['id_product_attribute']][
                    'attributes_groups'
                ];
            $combinations[$row['id_product_attribute']][
                'attributes_names'
            ] = @implode(
                ', ',
                $combinations[$row['id_product_attribute']]['attributes_values']
            );
            //dump($combinations[$row['id_product_attribute']]);
            // $combinations[$row['id_product_attribute']][
            //     'attributes_names'
            // ] = $row['group_name'].' : '.$row['attribute_name'];

            $combinations[$row['id_product_attribute']]['attributes'][] =
                (int) $row['id_attribute'];
            $combinations[$row['id_product_attribute']]['price'] =
                (float) $row['price'];

            // Call getPriceStatic in order to set $combination_specific_price
            if (
                !isset(
                    $combination_prices_set[(int) $row['id_product_attribute']]
                )
            ) {
                Product::getPriceStatic(
                    (int) $product->id,
                    false,
                    $row['id_product_attribute'],
                    6,
                    null,
                    false,
                    true,
                    1,
                    false,
                    null,
                    null,
                    null,
                    $combination_specific_price
                );
                $combination_prices_set[
                    (int) $row['id_product_attribute']
                ] = true;
                $combinations[$row['id_product_attribute']][
                    'specific_price'
                ] = $combination_specific_price;
            }
            $combinations[$row['id_product_attribute']]['ecotax'] =
                (float) $row['ecotax'];
            $combinations[$row['id_product_attribute']]['weight'] =
                (float) $row['weight'];
            $combinations[$row['id_product_attribute']]['quantity'] =
                (int) $row['quantity'];
            $combinations[$row['id_product_attribute']]['reference'] =
                $row['reference'];
            $combinations[$row['id_product_attribute']]['unit_impact'] =
                $row['unit_price_impact'];
            $combinations[$row['id_product_attribute']]['minimal_quantity'] =
                $row['minimal_quantity'];
            if ('0000-00-00' != $row['available_date']) {
                $combinations[$row['id_product_attribute']]['available_date'] =
                    $row['available_date'];
                $combinations[$row['id_product_attribute']][
                    'date_formatted'
                ] = Tools::displayDate($row['available_date']);
            } else {
                $combinations[$row['id_product_attribute']]['available_date'] =
                    '';
            }
            foreach ($combinations as $id_product_attribute => $comb) {
                $attribute_list = '';
                foreach ($comb['attributes'] as $id_attribute) {
                    $attribute_list .= '\''.(int) $id_attribute.'\',';
                }
                $attribute_list = rtrim($attribute_list, ',');
                $combinations[$id_product_attribute]['list'] = $attribute_list;
            }
        }
        $comb = [
            'attribute_groups' => $att_grps,
            'values' => $combinations,
        ];

        return $comb;
    }

    public static function getIdCombination($id_product, $attr1, $attr2)
    {
        /*if((int)$attr1 == 0)
        return (int)$attr2;

        if((int)$attr2 == 0)
        return (int)$attr1;*/

        $idProductAttribute = Db::getInstance()->getValue(
            '
					    SELECT
					        pac.`id_product_attribute`
					    FROM
					        `'.
                _DB_PREFIX_.
                'product_attribute_combination` pac
					        INNER JOIN `'.
                _DB_PREFIX_.
                'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
					    WHERE
					        pa.id_product = '.
                $id_product.
                '
					        AND pac.id_attribute IN ('.
                (int) $attr1.
                ','.
                (int) $attr2.
                ')
					    GROUP BY
					        pac.`id_product_attribute`'.
                ((int) $attr1 > 0 && (int) $attr2 > 0
                    ? 'HAVING COUNT(pa.id_product) = 2'
                    : '')
        );

        return (int) $idProductAttribute;
    }

    public static function getProductAttributeCombinationsStep(
        $id_product,
        $unique = false
    ) {
        $combinations = [];
        $context = Context::getContext();
        $product = new Product($id_product, $context->language->id);
        $attributes_groups = $product->getAttributesGroups(
            $context->language->id
        );
        $by_group = [];
        foreach ($attributes_groups as $value) {
            $id = $value['id_attribute_group'];
            if (!isset($by_group[$id])) {
                $by_group[$id] = [];
            }
            $by_group[$id][] = $value;
        }

        $stepped = [];
        $uniques = [];
        $multiples = [];
        $i = 1;

        foreach ($by_group as $group) {
            $set_unique = false;
            foreach ($group as $value) {
                $id = $value['id_attribute_group'];
                if (
                    (!$unique && $i < count($by_group)) ||
                    in_array($id, $unique)
                ) {
                    $set_unique = true;
                }

                if (!isset($stepped[$id])) {
                    $stepped[$id] = [];
                }

                if ($set_unique) {
                    $uniques[$id] = $id;
                    $stepped[$id][$value['id_attribute']] = $value;
                } else {
                    $multiples[$id] = $id;
                    $value['id_attribute'] =
                        self::getCombinationAttributes(
                            (int) $value['id_product_attribute']
                        ).',';
                    $stepped[$id][] = $value;
                }
            }
            ++$i;
        }
        $response = [
            'uniques' => [],
            'multiples' => [],
        ];
        if (count($uniques) > 0) {
            foreach ($uniques as $value) {
                $response['uniques'][] = $stepped[$value];
            }
            foreach ($multiples as $value) {
                $response['multiples'][] = $stepped[$value];
            }
        } else {
            $response = $stepped;
        }
        //dump($response);
        return $response;
    }

    public static function getCombinationAttributes($id_combnation)
    {
        return Db::getInstance()->getValue(
            'SELECT GROUP_CONCAT(id_attribute)
        FROM '.
                _DB_PREFIX_.
                'product_attribute_combination
        WHERE id_product_attribute = '.
                (int) $id_combnation
        );
    }

    public static function getProductAttributeCombinationsTab($id_product)
    {
        $combinations = [];
        $context = Context::getContext();
        $product = new Product($id_product, $context->language->id);
        $attributes_groups = $product->getAttributesGroups(
            $context->language->id
        );
        $attributes_groups_ok = [];

        foreach ($attributes_groups as $value) {
            $id = $value['id_attribute'];
            if (!isset($attributes_groups_ok[$id])) {
                $attributes_groups_ok[$id] = [];
            }

            $attributes_groups_ok[$id] = $value;
        }

        $attr = [];
        $i = 0;
        foreach ($attributes_groups_ok as $item) {
            if (0 == $i) {
                $col_group = $item['id_attribute_group'];
            }

            if ($item['id_attribute_group'] == $col_group) {
                $attr['cols'][] = $item;
            } else {
                $attr['rows'][] = $item;
            }

            /*if($item['is_color_group'] == 1)
                    $attr['cols'][] = $item;
                else
                    $attr['rows'][] = $item;*/

            ++$i;
        }
        if (!isset($attr['rows'])) {
            $attr['rows'] = false;
        }

        return $attr;
    }

    public static function getAttributeImageAssociations(
        $id_product_attribute,
        $id_product,
        $allow_no_picture = false
    ) {
        $combination_images = [];
        $data = Db::getInstance()->executeS(
            '
				SELECT `id_image`
				FROM `'.
                _DB_PREFIX_.
                'product_attribute_image`
				WHERE `id_product_attribute` = '.
                (int) $id_product_attribute
        );

        foreach ($data as $row) {
            $combination_images[] = (int) $row['id_image'];
        }
        if (sizeof($combination_images) > 0) {
            return $combination_images[0];
        } elseif ($allow_no_picture) {
            return false;
        } else {
            return product::getCover($id_product)['id_image'];
        }
    }

    public static function getAttributeImagesAssociations(
        $id_product_attribute,
        $id_product
    ) {
        $combination_images = [];
        $cover_images = [];
        $data = Db::getInstance()->executeS(
            '
				SELECT `'.
                _DB_PREFIX_.
                'product_attribute_image`.`id_image`
				FROM `'.
                _DB_PREFIX_.
                'product_attribute_image`
				JOIN `'.
                _DB_PREFIX_.
                'image`
				ON `'.
                _DB_PREFIX_.
                'product_attribute_image`.`id_image` = `'.
                _DB_PREFIX_.
                'image`.`id_image`
				WHERE `id_product_attribute` = '.
                (int) $id_product_attribute.
                '
				ORDER BY `position` ASC'
        );
        foreach ($data as $row) {
            $combination_images[] = (int) $row['id_image'];
        }
        $cover_images[] = product::getCover($id_product)['id_image'];
        if (sizeof($combination_images) > 0) {
            return $combination_images;
        } else {
            return $cover_images;
        }
    }

    public static function getQuantityDiscount($id_product)
    {
        $context = Context::getContext();
        $id_customer = isset($context->customer)
            ? (int) $context->customer->id
            : 0;
        $id_group = (int) Group::getCurrent()->id;
        $id_country = $id_customer
            ? (int) Customer::getCurrentCountry($id_customer)
            : (int) Tools::getCountry();

        $quantity_discounts = SpecificPrice::getQuantityDiscounts(
            (int) $id_product,
            (int) $context->shop->id,
            (int) $context->cookie->id_currency,
            (int) $id_country,
            (int) $id_group,
            null,
            true,
            (int) $context->customer->id
        );
        foreach ($quantity_discounts as &$quantity_discount) {
            if ($quantity_discount['id_product_attribute']) {
                $combination = new Combination(
                    (int) $quantity_discount['id_product_attribute']
                );
                $attributes = $combination->getAttributesName(
                    (int) $context->language->id
                );
                foreach ($attributes as $attribute) {
                    $quantity_discount['attributes'] =
                        $attribute['name'].' - ';
                }
                $quantity_discount['attributes'] = rtrim(
                    $quantity_discount['attributes'],
                    ' - '
                );
            }
            if (
                0 == (int) $quantity_discount['id_currency'] &&
                'amount' == $quantity_discount['reduction_type']
            ) {
                $quantity_discount['reduction'] = Tools::convertPriceFull(
                    $quantity_discount['reduction'],
                    null,
                    Context::getContext()->currency
                );
            }
        }
        $product = new Product((int) $id_product);
        $product_price = $product->getPrice(
            PS_TAX_INC == Product::$_taxCalculationMethod,
            false
        );
        $tax = (float) $product->getTaxesRate(
            new Address(
                (int) $context->cart
                    ->{Configuration::get('PS_TAX_ADDRESS_TYPE')}
            )
        );
        $ecotax_rate = (float) Tax::getProductEcotaxRate(
            $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}
        );
        $ecotax_tax_amount = Tools::ps_round($product->ecotax, 2);
        if (
            PS_TAX_INC == Product::$_taxCalculationMethod &&
            (int) Configuration::get('PS_TAX')
        ) {
            $ecotax_tax_amount = Tools::ps_round(
                $ecotax_tax_amount * (1 + $ecotax_rate / 100),
                2
            );
        }

        return NdkCf::formatQuantityDiscounts(
            $quantity_discounts,
            $product_price,
            (float) $tax,
            $ecotax_tax_amount
        );
    }

    public static function formatQuantityDiscounts(
        $specific_prices,
        $price,
        $tax_rate,
        $ecotax_amount
    ) {
        foreach ($specific_prices as $key => &$row) {
            $row['quantity'] = &$row['from_quantity'];
            if ($row['price'] >= 0) {
                // The price may be directly set

                $cur_price =
                    (!$row['reduction_tax']
                        ? $row['price']
                        : $row['price'] * (1 + $tax_rate / 100)) +
                    (float) $ecotax_amount;

                if ('amount' == $row['reduction_type']) {
                    $cur_price -= $row['reduction_tax']
                        ? $row['reduction']
                        : $row['reduction'] / (1 + $tax_rate / 100);
                    $row['reduction_with_tax'] = $row['reduction_tax']
                        ? $row['reduction']
                        : $row['reduction'] / (1 + $tax_rate / 100);
                } else {
                    $cur_price *= 1 - $row['reduction'];
                }

                $row['real_value'] =
                    $price > 0 ? $price - $cur_price : $cur_price;
            } else {
                if ('amount' == $row['reduction_type']) {
                    if (PS_TAX_INC == Product::$_taxCalculationMethod) {
                        $row['real_value'] =
                            1 == $row['reduction_tax']
                                ? $row['reduction']
                                : $row['reduction'] * (1 + $tax_rate / 100);
                    } else {
                        $row['real_value'] =
                            0 == $row['reduction_tax']
                                ? $row['reduction']
                                : $row['reduction'] / (1 + $tax_rate / 100);
                    }
                    $row['reduction_with_tax'] = $row['reduction_tax']
                        ? $row['reduction']
                        : $row['reduction'] +
                            ($row['reduction'] * $tax_rate) / 100;
                } else {
                    $row['real_value'] = $row['reduction'] * 100;
                }
            }
            $row['nextQuantity'] = isset($specific_prices[$key + 1])
                ? (int) $specific_prices[$key + 1]['from_quantity']
                : -1;
        }

        return $specific_prices;
    }

    public static function l($string, $specific = false)
    {
        return Translate::getModuleTranslation(
            'ndk_advanced_custom_fields',
            $string,
            'ndkcf'
        );
    }

    public static function duplicateGroupReductionCache(
        $id_product_old,
        $id_product_new
    ) {
        $query =
            '
				SELECT *
				FROM `'.
            _DB_PREFIX_.
            'product_group_reduction_cache`
				WHERE `id_product` = '.
            (int) $id_product_old;

        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (sizeof($rows) > 0) {
            foreach ($rows as $row) {
                $check = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
                    '
						SELECT *
						FROM `'.
                        _DB_PREFIX_.
                        'product_group_reduction_cache`
						WHERE `id_group` = '.
                        $row['id_group'].
                        ' AND `id_product` = '.
                        (int) $id_product_new,
                    false
                );

                $query =
                    'INSERT INTO `'.
                    _DB_PREFIX_.
                    'product_group_reduction_cache` (`id_product`, `id_group`, `reduction`)
						VALUES ('.
                    $id_product_new.
                    ', '.
                    $row['id_group'].
                    ', '.
                    $row['reduction'].
                    ')';
                Db::getInstance()->execute($query);
            }
        }

        return true;
    }

    public static function getAttributesLang(
        $id_product,
        $id_combination,
        $id_lang,
        $attribute_value_separator = ' - ',
        $attribute_separator = ', '
    ) {
        $combName = '';
        if (!Combination::isFeatureActive()) {
            return [];
        }

        $combinations = Db::getInstance()->executeS(
            'SELECT pa.*, product_attribute_shop.*
		        				FROM `'.
                _DB_PREFIX_.
                'product_attribute` pa
		        				'.
                Shop::addSqlAssociation('product_attribute', 'pa').
                '
		        				WHERE pa.`id_product` = '.
                (int) $id_product.
                '
		        				GROUP BY pa.`id_product_attribute`'
        );

        if (!$combinations) {
            return false;
        }

        $product_attributes = [];
        foreach ($combinations as $combination) {
            $product_attributes[] = (int) $combination['id_product_attribute'];
        }

        $lang = Db::getInstance()->executeS(
            'SELECT pac.id_product_attribute, GROUP_CONCAT(agl.`name`, \''.
                pSQL($attribute_value_separator).
                '\',al.`name` ORDER BY agl.`id_attribute_group` SEPARATOR \''.
                pSQL($attribute_separator).
                '\') as attribute_designation
		        				FROM `'.
                _DB_PREFIX_.
                'product_attribute_combination` pac
		        				LEFT JOIN `'.
                _DB_PREFIX_.
                'attribute` a ON a.`id_attribute` = pac.`id_attribute`
		        				LEFT JOIN `'.
                _DB_PREFIX_.
                'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
		        				LEFT JOIN `'.
                _DB_PREFIX_.
                'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.
                (int) $id_lang.
                ')
		        				LEFT JOIN `'.
                _DB_PREFIX_.
                'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.
                (int) $id_lang.
                ')
		        				WHERE pac.id_product_attribute IN ('.
                implode(',', $product_attributes).
                ')
		        				GROUP BY pac.id_product_attribute'
        );

        foreach ($lang as $k => $row) {
            $combinations[$k]['attribute_designation'] =
                $row['attribute_designation'];
        }

        //Get quantity of each variations
        foreach ($combinations as $key => $row) {
            $cache_key =
                $row['id_product'].
                '_'.
                $row['id_product_attribute'].
                '_quantity';

            if (!Cache::isStored($cache_key)) {
                $result = StockAvailable::getQuantityAvailableByProduct(
                    $row['id_product'],
                    $row['id_product_attribute']
                );
                Cache::store($cache_key, $result);
                $combinations[$key]['quantity'] = $result;
            } else {
                $combinations[$key]['quantity'] = Cache::retrieve($cache_key);
            }
        }

        foreach ($combinations as $row) {
            if ($row['id_product_attribute'] == (int) $id_combination) {
                $combName = $row['attribute_designation'];
            }
        }

        return $combName;
    }

    public static function getAttributePrice($id_product, $id_product_attribute)
    {
        $context = Context::getContext();
        $quantity = 1;
        $id_address = (int) Context::getContext()->cart->id_address_invoice;
        $address = Address::initialize($id_address, true);
        $tax_manager = TaxManagerFactory::getManager(
            $address,
            Product::getIdTaxRulesGroupByIdProduct(
                (int) $id_product,
                Context::getContext()
            )
        );
        $product_tax_calculator = $tax_manager->getTaxCalculator();
        $usetax = Group::getPriceDisplayMethod(
            Group::getPriceDisplayMethod(
                Context::getContext()->customer->id_default_group
            )
        );
        $usetax = PS_TAX_INC == Product::$_taxCalculationMethod;

        if (0 == (int) $id_product_attribute) {
            $id_product_attribute = null;
        } else {
            $id_product_attribute = (int) $id_product_attribute;
        }

        return Product::getPriceStatic(
            (int) $id_product,
            $usetax,
            $id_product_attribute,
            6,
            null,
            false,
            true,
            $quantity,
            false,
            (int) $context->customer->id,
            (int) $context->cart->id
        );
    }

    public static function getNdkTaxeRate($id_product)
    {
        $context = Context::getContext();
        $id_address = (int) Context::getContext()->cart->id_address_invoice;
        $address = Address::initialize($id_address, true);
        $tax_manager = TaxManagerFactory::getManager(
            $address,
            Product::getIdTaxRulesGroupByIdProduct(
                (int) $id_product,
                Context::getContext()
            )
        );
        $product_tax_calculator = $tax_manager->getTaxCalculator();

        return $product_tax_calculator->getTotalRate();
    }

    public static function class_exists_ndk($class)
    {
        return class_exists($class);
    }

    public static function clearAllCache()
    {
        Db::getInstance()->Execute(
            'TRUNCATE TABLE `'._DB_PREFIX_.'ndk_customization_field_cache`'
        );
    }

    public static function splitTempPackFromOrder(
        $id_product,
        $order,
        $id_order_invoice,
        $qtty = 1
    ) {
        if ((float) _PS_VERSION_ > 1.6) {
            Pack::resetStaticCache();
        }
        $items = Pack::getItems($id_product, $order->id_lang);
        $order_detail_list = [];
        $i = 0;
        $products = [];
        $id_address = (int) $order->id_address_invoice;
        $address = Address::initialize($id_address, true);

        foreach ($items as $item) {
            $tax_manager = TaxManagerFactory::getManager(
                $address,
                Product::getIdTaxRulesGroupByIdProduct(
                    (int) $id_product,
                    Context::getContext()
                )
            );
            $product_tax_calculator = $tax_manager->getTaxCalculator();
            $prod = (array) $item;
            $prod['id_product'] = $prod['id'];
            $prod_qtty = (int) $prod['pack_quantity'] * $qtty;
            $prod['cart_quantity'] = $prod_qtty;

            $prod['id_customization'] = 0;
            $prod['weight_attribute'] = 0;
            $prod['stock_quantity'] = (int) Product::getQuantity(
                (int) $prod['id'],
                $prod['id_pack_product_attribute'],
                null
            );
            $prod['id_shop'] = $order->id_shop;

            $price = Product::getPriceStatic(
                (int) $prod['id'],
                false,
                $prod['id_pack_product_attribute']
                    ? (int) $prod['id_pack_product_attribute']
                    : 0,
                6,
                null,
                false,
                true,
                $prod_qtty,
                false,
                (int) $order->id_customer,
                (int) $order->id_cart
            );
            $price_wt = $product_tax_calculator->addTaxes($price);
            // $prod['price_wt'] = $price_wt;
            // $prod['price'] = $price;
            // $prod['total_wt'] = $price_wt * $prod_qtty;
            // $prod['total'] = $price * $prod_qtty;
            $prod['price_wt'] = 0;
            $prod['price'] = 0;
            $prod['total_wt'] = 0;
            $prod['total'] = 0;

            $prod['id_product_attribute'] = $prod['id_pack_product_attribute'];
            //self::createOrderDetail($order, new Cart($order->id_cart), $prod, $order->current_state, 0);
            $products[
                (int) $prod['id'].
                    '-'.
                    (int) $prod['id_pack_product_attribute']
            ] = $prod;
        }

        Db::getInstance()->execute(
            'DELETE FROM `'.
                _DB_PREFIX_.
                'pack` WHERE id_product_pack = '.
                (int) $id_product
        );
        $vprod = new Product((int) $id_product);
        $vprod->is_virtual = true;
        $vprod->save();

        return $products;
    }

    public static function createOrderDetail(
        Order $order,
        Cart $cart,
        $product,
        $id_order_state,
        $id_order_invoice = 0,
        $use_taxes = true,
        $id_warehouse = 0
    ) {
        $order_detail = new OrderDetail(null, null, Context::getContext());
        $order_detail->id = null;
        $order_detail->id_order = $order->id;
        $order_detail->product_id = (int) $product['id_product'];
        $order_detail->product_attribute_id = $product['id_product_attribute']
            ? (int) $product['id_product_attribute']
            : 0;
        $order_detail->id_customization = isset($product['id_customization'])
            ? (int) $product['id_customization']
            : 0;
        $order_detail->product_name =
            $product['name'].
            (isset($product['attributes']) && null != $product['attributes']
                ? ' - '.$product['attributes']
                : '');

        $order_detail->product_quantity = (int) $product['cart_quantity'];
        $order_detail->product_ean13 = empty($product['ean13'])
            ? null
            : pSQL($product['ean13']);
        $order_detail->product_isbn = empty($product['isbn'])
            ? null
            : pSQL($product['isbn']);
        $order_detail->product_upc = empty($product['upc'])
            ? null
            : pSQL($product['upc']);
        $order_detail->product_reference = empty($product['reference'])
            ? null
            : pSQL($product['reference']);
        $order_detail->product_supplier_reference = empty(
            $product['supplier_reference']
        )
            ? null
            : pSQL($product['supplier_reference']);
        $order_detail->product_weight =
            $product['id_product_attribute'] && $product['weight_attribute']
                ? (float) $product['weight_attribute']
                : (float) $product['weight'];
        $order_detail->id_warehouse = $id_warehouse;

        $product_quantity = (int) Product::getQuantity(
            (int) $product['id_product'],
            $product['id_product_attribute'],
            null,
            $cart
        );

        $order_detail->product_quantity_in_stock =
            $product_quantity - (int) $product['cart_quantity'] < 0
                ? $product_quantity
                : (int) $product['cart_quantity'];
        $p_price = Product::getPriceStatic(
            (int) $product['id_product'],
            false,
            $product['id_product_attribute']
                ? (int) $product['id_product_attribute']
                : 0,
            6,
            null,
            false,
            true,
            (int) $product['cart_quantity'],
            false,
            (int) $order->id_customer,
            (int) $order->id_cart
        );
        $order_detail->product_price = (float) $p_price;
        // Set order invoice id
        $order_detail->id_order_invoice = (int) $id_order_invoice;

        // Set shop id
        $order_detail->id_shop = (int) $order->id_shop;

        // Add new entry to the table
        $order_detail->save();
    }

    public static function getTypeTechName($id_type)
    {
        $rep = false;
        $types = Module::getInstanceByName('ndk_advanced_custom_fields')->types;
        foreach ($types as $type) {
            if ($type['id_type'] == $id_type) {
                $rep = $type['technical'];
            }
        }

        return $rep;
    }

    public static function smartyClassname($classname)
    {
        $classname = Tools::replaceAccentedChars(Tools::strtolower($classname));
        $classname = preg_replace('/[^A-Za-z0-9]/', '-', $classname);
        $classname = preg_replace('/[-]+/', '-', $classname);

        return $classname;
    }

    public static function getBrutPrice(
        $id_product,
        $id_product_attribute,
        $usetax = false,
        $reduc = false
    ) {
        $specific_price_output = null;

        return Product::getPriceStatic(
            (int) $id_product,
            $usetax,
            (int) $id_product_attribute,
            6,
            null,
            false,
            $reduc,
            1,
            false,
            null,
            null,
            null,
            $specific_price_output,
            false,
            false,
            null,
            false
        );
    }

    public static function reGenerateThumbs($delete_existing = false, $fields = false)
    {
        if (!$fields) {
            $fields = self::getFieldsLight(0);
        } else {
            $delete_existing = false;
            $i = 0;
            foreach ($fields as $field) {
                $fields[$i] = ['id' => $field];
                ++$i;
            }
        }

        $thumb_dir = _PS_IMG_DIR_.'scenes/'.'ndkcf/thumbs/';
        if ($delete_existing) {
            Tools::deleteDirectory($thumb_dir, false);
        }
        foreach ($fields as $field) {
            self::generateThumbs($field['id']);
            foreach (self::getFieldValuesId($field['id']) as $value) {
                self::generateThumbs($value['id']);
            }
        }
    }

    public static function generateThumbs($id)
    {
        $possibles_files = [
            $id.'.jpg',
            $id.'-mask.jpg',
            $id.'-texture.jpg',
            $id.'-picto.jpg',
            $id.'.csv',
            $id.'.svg',
            $id.'.mp3',
        ];

        $images_types = [];
        $images_types[] = ImageType::getByNameNType(
            Configuration::get('NDK_IMAGE_SIZE'),
            'products'
        );
        $images_types[] = ImageType::getByNameNType(
            Configuration::get('NDK_IMAGE_LARGE_SIZE'),
            'products'
        );

        //var_dump($images_types); die();
        $base_img_path = _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$id.'.jpg';

        if (file_exists($base_img_path)) {
            foreach ($images_types as $k => $image_type) {
                ImageManager::resize(
                    $base_img_path,
                    _PS_IMG_DIR_.
                        'scenes/'.
                        'ndkcf/thumbs/'.
                        $id.
                        '-'.
                        Tools::stripslashes($image_type['name']).
                        '.jpg',
                    (int) $image_type['width'],
                    (int) $image_type['height']
                );
            }
            if (file_exists(str_replace('.jpg', '.webp', $base_img_path))) {
                unlink(str_replace('.jpg', '.webp', $base_img_path));
            }
        }
        $base_texture_path =
            _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$id.'-texture.jpg';
        if (file_exists($base_texture_path)) {
            $image_type = ImageType::getByNameNType(
                Configuration::get('NDK_IMAGE_SIZE'),
                'products'
            );
            ImageManager::resize(
                $base_texture_path,
                _PS_IMG_DIR_.
                    'scenes/'.
                    'ndkcf/thumbs/'.
                    $id.
                    '-texture.jpg',
                (int) $image_type['width'],
                (int) $image_type['height']
            );
            if (file_exists(str_replace('.jpg', '.webp', $base_texture_path))) {
                unlink(str_replace('.jpg', '.webp', $base_texture_path));
            }
        }
    }

    public static function cleanAssociations()
    {
        $products = self::selectElement('id_product', 'product', '');
        $categories = self::selectElement('id_category', 'category', '');
        $fields = self::selectElement(
            'products, categories, id_ndk_customization_field',
            'ndk_customization_field',
            ''
        );
        $groups = self::selectElement(
            'products, categories, id_ndk_customization_field_group',
            'ndk_customization_field_group',
            ''
        );
        self::existElement($products, $fields, 'products', 'id_product');
        self::existElement($categories, $fields, 'categories', 'id_category');
        self::existElement($products, $groups, 'products', 'id_product');
        self::existElement($categories, $groups, 'categories', 'id_category');
    }

    public static function selectElement($element, $table, $condition)
    {
        $sql =
            'SELECT '.
            $element.
            ',"'.
            $table.
            '" as table_key
		FROM '.
            _DB_PREFIX_.
            $table.
            $condition;
        $values = Db::getInstance()->executeS($sql);

        return $values;
    }

    public static function existElement($array, $elements, $col, $flat_col)
    {
        $array = self::arrayFlat($array, $flat_col);
        foreach ($elements as $value) {
            $result = [];
            $ids = explode(',', $value[$col]);
            foreach ($ids as $id) {
                if (in_array($id, $array)) {
                    $result[] = $id;
                }
            }

            if (count(array_diff($ids, $result)) > 0) {
                $sql =
                    'UPDATE '.
                    _DB_PREFIX_.
                    $value['table_key'].
                    '
				SET '.
                    $col.
                    ' = "'.
                    implode(',', $result).
                    '" WHERE id_'.
                    $value['table_key'].
                    ' ='.
                    (int) $value['id_'.$value['table_key']];
                Db::getInstance()->execute($sql);
            }
        }
    }

    public static function arrayFlat($array, $key)
    {
        $result = [];
        foreach ($array as $row) {
            $result[] = $row[$key];
        }

        return $result;
    }

    public static function sketchImage(
        $imagePath,
        $title = false,
        $output = false
    ) {
        $img_size = 1200;
        $origine = new Imagick(realpath($imagePath));
        $origine->scaleImage($img_size, $img_size, true);
        $origine->setImageType(Imagick::IMGTYPE_GRAYSCALEMATTE);
        $origine->brightnessContrastImage(15, 25, 134217727);

        $calque1 = new Imagick(realpath($imagePath));
        $calque1->scaleImage($img_size, $img_size, true);
        $calque1->setImageType(Imagick::IMGTYPE_GRAYSCALEMATTE);
        $calque1->brightnessContrastImage(20, 25, 134217727);
        $calque1->negateImage(false);

        for ($i = 0; $i < 6; ++$i) {
            $calque1->gaussianBlurImage(5, 8, 134217727);
        }

        $origine->compositeImage($calque1, imagick::COMPOSITE_COLORDODGE, 0, 0);

        //là que ça se joue
        $origine->brightnessContrastImage(-5, 5, 134217727);

        $origine->contrastImage(1);
        $origine->contrastImage(0);

        //$origine->sharpenimage(8, 2, 134217727);
        //$origine->tintImage(0, 1);
        if ($output) {
            $origine->writeImage(_PS_UPLOAD_DIR_.'sketch-'.$title);
        } else {
            $origine->writeImage($imagePath);
        }

        $origine->clear();
        $calque1->clear();
    }

    public function engravingImage($imagePath,
        $title = false,
        $output = false)
    {
        $img_size = 1200;
        $origine = new Imagick(realpath($imagePath));
        $origine->scaleImage($img_size, $img_size, true);
        $origine->setImageType(Imagick::IMGTYPE_GRAYSCALEMATTE);
        $origine->brightnessContrastImage(30, 1);
        if ($output) {
            $origine->writeImage(_PS_UPLOAD_DIR_.'sketch-'.$title);
        } else {
            $origine->writeImage($imagePath);
        }

        $origine->clear();
    }

    public static function duplicateSpecificPrices($old_product_id, $product_id)
    {
        foreach (SpecificPrice::getIdsByProductId((int) $old_product_id) as $data) {
            $specific_price = new SpecificPrice((int) $data['id_specific_price']);
            //dump($specific_price);
            if ($specific_price->reduction > 0) {
                if (!$specific_price->duplicate((int) $product_id)) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function getTaxConversionRatio($old_id, $new_id)
    {
        $id_address = (int) Context::getContext()->cart->id_address_invoice;
        $address = Address::initialize($id_address, true);
        $old_tax_manager = TaxManagerFactory::getManager(
            $address,
            $old_id
        );
        $new_tax_manager = TaxManagerFactory::getManager(
            $address,
            $new_id
        );
        $old_ratio = (1 + $old_tax_manager->getTaxCalculator()->getTotalRate() / 100);
        $new_ratio = (1 + $new_tax_manager->getTaxCalculator()->getTotalRate() / 100);
        $converted_ratio = $new_ratio / $old_ratio;

        return $converted_ratio;
    }
}
