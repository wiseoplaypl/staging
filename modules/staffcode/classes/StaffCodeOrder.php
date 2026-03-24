<?php
/**
 * StaffCodeOrder.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module staffcode (Staff Code)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */

class StaffCodeOrder extends ObjectModel
{

    /** @var Integer id_staff_code_order */
    public $id_staff_code_order;

    /** @var Integer id_staff_code */
    public $id_staff_code;

    /** @var Integer id_order */
    public $id_order;

    /** @var Date date_add */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'fields' => array(
            'id_staff_code_order' => array('type' => self::TYPE_INT,),
            'id_staff_code' => array('type' => self::TYPE_INT,),
            'id_order' => array('type' => self::TYPE_INT,),
            'date_add' => array('type' => self::TYPE_DATE,),
        ),
        'table' => 'staff_code_order',
        'primary' => 'id_staff_code_order',
    );
    public static function assignOrder(StaffCodes $code, $id_order)
    {
        $StaffCodeOrder = new StaffCodeOrder;
        $StaffCodeOrder -> id_order = (int) $id_order;
        $StaffCodeOrder -> id_staff_code = $code->id;
        return $StaffCodeOrder -> save();
    }
    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }
    public static function installTable()
    {
        return Db::getInstance()->execute(
            "CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."staff_code_order (
                `id_staff_code_order` INT(10) AUTO_INCREMENT,
                `id_staff_code` INT(10) NULL,
                `id_order` INT(10) NULL,
                `date_add`DATETIME NOT NULL,
                PRIMARY KEY (`id_staff_code_order`)
            )"
        );
    }
}
