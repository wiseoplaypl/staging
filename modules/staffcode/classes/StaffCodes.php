<?php
/**
 * StaffCodes.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module staffcode (Staff Code)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */

class StaffCodes extends ObjectModel
{

    /** @var Integer id_staff_code */
    public $id_staff_code;

    /** @var String code */
    public $code;

    /** @var String email_address */
    public $email_address;

    /** @var Date date_add */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'fields' => array(
            'id_staff_code' => array('type' => self::TYPE_INT,),
            'code' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true,),
            'email_address' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true,),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate',),
        ),
        'table' => 'staff_codes',
        'primary' => 'id_staff_code',
    );

    public static function emailExists($email)
    {
        return Db::getInstance()->getValue("
            SELECT id_staff_code
            FROM ". _DB_PREFIX_ ."staff_codes
            WHERE email_address = '". pSQL($email) ."'
        ");
    }
    public static function codeExists($code)
    {
        return Db::getInstance()->getValue("
            SELECT id_staff_code
            FROM ". _DB_PREFIX_ ."staff_codes
            WHERE code = '". pSQL($code) ."'
        ");
    }
    public static function addCode($code, $email)
    {
        $StaffCodes = new StaffCodes;
        $StaffCodes -> code = $code;
        $StaffCodes -> email_address = $email;
        return $StaffCodes -> save();
    }
    public static function getByCode($code)
    {
        $id = Db::getInstance()->getValue("
            SELECT id_staff_code
            FROM ". _DB_PREFIX_ ."staff_codes
            WHERE code LIKE '". pSQL($code) ."'
        ");
        return $id ? new StaffCodes($id) : false;

    }
    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }
    public static function installTable()
    {
        return Db::getInstance()->execute(
            "CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."staff_codes (
                `id_staff_code` INT(10) AUTO_INCREMENT,
                `code` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                `email_address` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                `date_add`DATETIME NOT NULL,
                PRIMARY KEY (`id_staff_code`)
            )"
        );
    }
}
