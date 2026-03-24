<?php
/**
 *  Tous droits réservés NDKDESIGN.
 *
 *  @author	Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*/
require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/models/ndkCfSpecificPrice.php';

class NdkCfValues extends ObjectModel
{
	public $id_ndk_customization_field;
	public $id_parent_value;
	public $price;
	public $set_quantity;
	public $quantity;
	public $value;
	public $description;
	public $tags;
	public $textmask;
	public $color;
	public $excludes_products;
	public $excludes_categories;
	public $quantity_min;
	public $quantity_max;
	public $influences_restrictions;
	public $influences_obligations;
	public $position;
	public $default_value;
	public $id_product_value;
	public $step_quantity;
	public $input_type;
	public $reference;
	public $type;
	public $options;

	public static $definition = [
		'table' => 'ndk_customization_field_value',
		'primary' => 'id_ndk_customization_field_value',
		'multilang' => true,
		'fields' => [
			'id_ndk_customization_field' => [
				'type' => ObjectModel::TYPE_INT,
				'lang' => false,
			],
		'id_parent_value' => [
				'type' => ObjectModel::TYPE_INT,
				'lang' => false,
			],
		'price' => [
			'type' => ObjectModel::TYPE_FLOAT,
			'required' => false,
		],
		'set_quantity' => [
			'type' => self::TYPE_BOOL,
			'validate' => 'isBool',
			'required' => false,
		],
		'quantity' => [
			'type' => ObjectModel::TYPE_INT,
			'required' => false,
		],
		'quantity_min' => [
			'type' => ObjectModel::TYPE_INT,
			'required' => false,
		],
		'quantity_max' => [
			'type' => ObjectModel::TYPE_INT,
			'required' => false,
		],
		'color' => [
			'type' => ObjectModel::TYPE_STRING,
			'required' => false,
		],
		'excludes_products' => [
			'type' => ObjectModel::TYPE_STRING,
			'required' => false,
		],
		'excludes_categories' => [
			'type' => ObjectModel::TYPE_STRING,
			'required' => false,
		],
		'influences_restrictions' => [
			'type' => ObjectModel::TYPE_STRING,
			'required' => false,
		],
		'influences_obligations' => [
			'type' => ObjectModel::TYPE_STRING,
			'required' => false,
		],
		'default_value' => [
			'type' => ObjectModel::TYPE_INT,
			'required' => false,
		],
		'step_quantity' => [
			'type' => ObjectModel::TYPE_STRING,
			'required' => false,
		],
		'input_type' => [
			'type' => ObjectModel::TYPE_STRING,
			'required' => false,
		],
		'position' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false],
		'id_product_value' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false],
		// Lang fields
		'value' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255],
		'description' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 5000],

		'tags' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => false, 'size' => 255],
		'textmask' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => false, 'size' => 255],
		'reference' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => false, 'size' => 255],
		'type' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => false, 'size' => 255],
		'options' => ['type' => self::TYPE_STRING],
	],
];

	public function __construct($id = null, $id_lang = null, $lite_result = false, $hide_lookbook_position = false)
	{
		parent::__construct($id, $id_lang);
		$this->image_dir = _PS_IMG_DIR_.'scenes/'.'ndkcf';
	}

	public static function isValues($id_ndk_customization_field, $value, $id_lang)
	{
		if (!Combination::isFeatureActive()) {
			return [];
		}

		$result = Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'ndk_customization_field` ag
			LEFT JOIN `'._DB_PREFIX_.'ndk_customization_field_lang` agl
				ON (ag.`id_ndk_customization_field` = agl.`id_ndk_customization_field` AND agl.`id_lang` = '.(int) $id_lang.')
			LEFT JOIN `'._DB_PREFIX_.'ndk_customization_field_value` a
				ON a.`id_ndk_customization_field` = ag.`id_ndk_customization_field`
			LEFT JOIN `'._DB_PREFIX_.'ndk_customization_field_value_lang` al
				ON (a.`id_ndk_customization_field_value` = al.`id_ndk_customization_field_value` AND al.`id_lang` = '.(int) $id_lang.')
			'.Shop::addSqlAssociation('ndk_customization_field', 'ag').'
			'.Shop::addSqlAssociation('ndk_customization_field_value', 'a').'
			WHERE al.`value` = \''.pSQL($value).'\' AND ag.`id_ndk_customization_field` = '.(int) $id_ndk_customization_field.'
			ORDER BY agl.`name`
		');

		return (int) $result > 0;
	}

	public static function duplicateSpecificPrice($old_id_field, $new_id_field, $old_id_value, $new_id_value)
	{
		$prices = NdkCfSpecificPrice::getSpecificPrices($old_id_field, $old_id_value);
		if (is_array($prices)) {
			foreach ($prices as $price) {
				$newSp = new NdkCfSpecificPrice();
				$newSp->id_ndk_customization_field = (int) $new_id_field;
				$newSp->id_ndk_customization_field_value = (int) $new_id_value;
				$newSp->reduction = $price['reduction'];
				$newSp->reduction_type = $price['reduction_type'];
				$newSp->from_quantity = $price['from_quantity'];
				$newSp->save();
			}
		}
	}

	public static function duplicateImages($old_id, $new_id)
	{
		$old_base_img_path = _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$old_id.'.jpg';
		$new_base_img_path = _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$new_id.'.jpg';

		$old_base_texture_path = _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$old_id.'-texture.jpg';
		$new_base_texture_path = _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$new_id.'-texture.jpg';

		if (file_exists($old_base_img_path)) {
			copy($old_base_img_path, $new_base_img_path);
			$images_types = ImageType::getImagesTypes('products');
			foreach ($images_types as $k => $image_type) {
				ImageManager::resize(
			$new_base_img_path,
			_PS_IMG_DIR_.'scenes/'.'ndkcf/thumbs/'.$new_id.'-'.Tools::stripslashes($image_type['name']).'.jpg',
			(int) $image_type['width'],
			(int) $image_type['height']);
			}
		}

		if (file_exists($old_base_texture_path)) {
			copy($old_base_texture_path, $new_base_texture_path);
			ImageManager::resize(
		 $new_base_texture_path,
		 _PS_IMG_DIR_.'scenes/'.'ndkcf/thumbs/'.$new_id.'-texture.jpg', 100, 100);
		}
	}

	public static function duplicateImagesSvg($old_id, $new_id)
	{
		$old_base_img_path = _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$old_id.'.svg';
		$new_base_img_path = _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$new_id.'.svg';
		if (file_exists($old_base_img_path)) {
			copy($old_base_img_path, $new_base_img_path);
		}
	}

	public static function duplicateMP3($old_id, $new_id)
	{
		$old_base_img_path = _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$old_id.'.mp3';
		$new_base_img_path = _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$new_id.'.mp3';
		if (file_exists($old_base_img_path)) {
			copy($old_base_img_path, $new_base_img_path);
		}
	}

	public static function isPartOfCustomization($id_product)
	{
		Db::getInstance()->execute('SET SQL_BIG_SELECTS=1');
		$sql = 'SELECT COUNT(cfv.`id_ndk_customization_field_value`)  as count	
		FROM `'._DB_PREFIX_.'ndk_customization_field_value` cfv
		WHERE cfv.id_product_value = '.(int) $id_product;
		//var_dump($sql);die;
		$fields = Db::getInstance()->getValue($sql);
		if ((int) $fields > 0) {
			return true;
		} else {
			return false;
		}
	}

	public static function getIdByReference($query)
	{
		if ('' == $query) {
			return 0;
		}

		$id_lang = Context::getContext()->language->id;
		$fields = Db::getInstance()->getValue('
				SELECT `id_ndk_customization_field_value` as id
				FROM `'._DB_PREFIX_.'ndk_customization_field_value`
				WHERE reference = "'.$query.'"');

		return $fields;
	}
}
