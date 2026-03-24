<?php
/**
 * CustomCarrierCategory.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module customcarrier (Custom Carrier)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */

class CustomCarrierCategory extends ObjectModel
{

    /** @var Integer id_custom_carrier_category */
    public $id_custom_carrier_category;

    /** @var Integer id_custom_carrier */
    public $id_custom_carrier;

    /** @var Integer id_category */
    public $id_category;


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'fields' => array(
            'id_custom_carrier_category' => array('type' => self::TYPE_INT, 'validate' => 'isInt',),
            'id_custom_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true,),
            'id_category' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true,),
        ),
        'table' => 'custom_carrier_category',
        'primary' => 'id_custom_carrier_category',
    );


    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }
    public static function installTable()
    {
        return Db::getInstance()->execute(
            "CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."custom_carrier_category (
                `id_custom_carrier_category` INT(10) AUTO_INCREMENT,
                `id_custom_carrier` INT(10) NOT NULL,
                `id_category` INT(10) NOT NULL,
                PRIMARY KEY (`id_custom_carrier_category`)
            )"
        );
    }
}
