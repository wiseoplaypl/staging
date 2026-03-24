<?php
/**
 * CustomCarrier.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module customcarrier (Custom Carrier)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */

class CustomCarrier extends ObjectModel
{

    /** @var Integer id_custom_carrier */
    public $id_custom_carrier;

    /** @var String name */
    public $name;

    /** @var String delay */
    public $delay;

    /** @var Float price */
    public $price;

    /** @var Float price */
    public $priority = 1;

    /** @var Float price */
    public $is_category = 0;

    /** @var Float price */
    public $price_each_product = 1;

    public static $checkProducts = [];

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'fields' => array(
            'id_custom_carrier' => array('type' => self::TYPE_INT,),
            'id_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true,),
            'price' => array('type' => self::TYPE_FLOAT, 'required' => true, 'validate' => 'isPrice',),
            'id_feature' => array('type' => self::TYPE_INT, 'required' => true,),
            'is_category' => array('type' => self::TYPE_INT, 'required' => true,),
            'priority' => array('type' => self::TYPE_INT, 'required' => true,),
            'is_price_feature' => array('type' => self::TYPE_INT, 'required' => true,),
            'feature_multiplier' => array('type' => self::TYPE_INT, 'required' => true,),
            'price_each_product' => array('type' => self::TYPE_INT, 'required' => true,),
        ),
        'table' => 'custom_carrier',
        'primary' => 'id_custom_carrier',
    );

    public function setCategories($array = [])
    {
        Db::GetInstance()->execute("
            DELETE FROM ". _DB_PREFIX_ ."custom_carrier_category
            WHERE id_custom_carrier = '". $this->id ."'"
        );
        if (!$array) {
            return;
        }
        foreach ($array as $v) {
            $category = new CustomCarrierCategory;
            $category -> id_custom_carrier = $this->id;
            $category -> id_category = $v;
            $category -> save();
        }

    }

    public function getCategoriesId()
    {
        $sql = Db::getInstance()->executeS("
            SELECT id_category FROM ". _DB_PREFIX_ ."custom_carrier_category
            WHERE id_custom_carrier = '". $this->id ."'"
        );
        $result = [];
        foreach ($sql as $v) {
            $result[] = $v['id_category'];
        }
        return $result;
    }
    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }
    public function getByCarrierId($id)
    {
        $customCarriers = [];
        $sql = "SELECT id_custom_carrier FROM ". _DB_PREFIX_ ."custom_carrier WHERE id_carrier = ". (int) $id ." ORDER BY priority ASC";
        foreach (Db::getInstance()->executeS($sql) as $v) {
            $customCarriers[] = new CustomCarrier($v['id_custom_carrier']);
        }
        return $customCarriers;
    }
    public function isProductCategory($id_product)
    {
        if (isset(self::$checkProducts[$id_product])) {
            return self::$checkProducts[$id_product];
        }
        $categories = $this->getCategoriesId();
        return Db::getInstance()->getValue("
            SELECT id_product FROM ". _DB_PREFIX_ ."category_product
            WHERE id_category IN (". implode(',', $categories) .")
            AND id_product = '". $id_product ."'
        ");

        return self::$checkProducts[$id_product];
    }
    public static function installTable()
    {
        return Db::getInstance()->execute(
            "CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."custom_carrier (
                `id_custom_carrier` INT(10) AUTO_INCREMENT,
                `id_carrier` INT(10)  NOT NULL,
                `price` decimal(20,6) NOT NULL,
                `is_price_feature`  INT(10)  NOT NULL,
                `price_each_product`  INT(10)  NOT NULL,
                `id_feature`  INT(10)  NOT NULL,
                `is_category`  INT(10)  NOT NULL,
                `priority`  INT(10)  NOT NULL,
                `feature_multiplier` decimal(20,6) NOT NULL,
                PRIMARY KEY (`id_custom_carrier`)
            )"
        );
    }
    public static function getCarriers()
    {
        $carriers = [];
        $sql = Db::getInstance()->executeS("SELECT id_custom_carrier FROM ". _DB_PREFIX_ ."custom_carrier ORDER BY priority ASC");
        foreach ($sql as $v) {
            $carriers[] = new CustomCarrier($v['id_custom_carrier']);
        }
        return $carriers;
    }
}
