<?php
/**
 * CustomCarrierProduct.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module customcarrier (Custom Carrier)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */

class CustomCarrierProduct extends ObjectModel
{

    /** @var Integer id_custom_carrier_product */
    public $id_custom_carrier_product;

    /** @var Integer id_custom_carrier */
    public $id_custom_carrier;

    /** @var Integer id_product */
    public $id_product;

    /** @var Float price */
    public $price;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'fields' => array(
            'id_custom_carrier_product' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true,),
            'id_custom_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true,),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true,),
            'price' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true,),
        ),
        'table' => 'custom_carrier_product',
        'primary' => 'id_custom_carrier_product',
    );


    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }
    public static function installTable()
    {
        return Db::getInstance()->execute(
            "CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."custom_carrier_product (
                `id_custom_carrier_product` INT(10) AUTO_INCREMENT,
                `id_custom_carrier` INT(10) NOT NULL,
                `id_product` INT(10) NOT NULL,
                `price`  decimal(20,6) NOT NULL,
                PRIMARY KEY (`id_custom_carrier_product`)
            )"
        );
    }
}
