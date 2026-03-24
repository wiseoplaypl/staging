<?php
/**
 * OrderProductSupplier.php
 * This file was generated with automatic module generator created by SzpaQ <dev-bot>
 * File is part of module CartTools
 * @author SzpaQ
 * @copyright 2022 SzpaQ
 * @license All rights reserved
 * */

class OrderProductSupplier extends ObjectModel
{

    /** @var Integer id_order_product_supplier **/
    public $id_order_product_supplier;

    /** @var Integer id_order **/
    public $id_order;

    /** @var Integer id_product **/
    public $id_product;

    /** @var Integer id_supplier **/
    public $id_supplier;

    /** @var String supplier_name **/
    public $supplier_name;

    /** @var String product_reference **/
    public $product_reference;

    /** @var String product_reference **/
    public $order_reference;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        "fields" => array(
            "id_order_product_supplier" => array(
                "type" => self::TYPE_INT,
            ),
            "id_order" => array(
                "type" => self::TYPE_INT,
                'validate' => "isInt",
                'required' => true,
            ),
            "id_product" => array(
                "type" => self::TYPE_INT,
                'validate' => "isInt",
                'required' => true,
            ),
            "id_supplier" => array(
                "type" => self::TYPE_INT,
                'validate' => "isInt",
                'required' => true,
            ),
            "supplier_name" => array(
                "type" => self::TYPE_STRING,
                'validate' => "isString",
            ),
            "product_reference" => array(
                "type" => self::TYPE_STRING,
                'validate' => "isString",
                'required' => true,
            ),
            "order_reference" => array(
                "type" => self::TYPE_STRING,
                'validate' => "isString",
                'required' => true,
            ),
        ),
        'table' => 'order_product_supplier',
        'primary' => 'id_order_product_supplier',
    );
    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }
        /**
     * AddRow()
     * @description adds supplier reference to order
     * @var String $Productreference
     * @var String $Orderreference
     * @var Integer $Idsupplier
     * @return bool
     * */
    public static function AddRow(
        string $product_reference,
        string $order_reference,
        int $id_supplier,
        $id_product = 0,
        $id_order = 0
    ) : bool
    {
        /* * AddRow * */
        $supplier = new Supplier((int) $id_supplier);
        $object = self::getByReferences($product_reference, $order_reference) ?: new OrderProductSupplier;
        $object -> id_supplier = $id_supplier;
        $object -> supplier_name = $supplier->name;
        $object -> id_product = (int) $id_product;
        $object -> id_order = (int) $id_order;
        $object -> product_reference = $product_reference;
        $object -> order_reference = $order_reference;
        return (bool) $object -> save();

    }
    /**
     * getSupplierName()
     * @description get supplier name
     * @return string
     * */
    public function getSupplierName() : string
    {
        /* * GetSupplierName * */
        return $this->supplier_name ?: '';
    }

    /**
     * getByReferences()
     * @description return row by order and product references
     * @var String $Property if declared this will return specific property of object OrderProductSupplier
     * */
    public static function getByReferences(string $product_reference, string $order_reference)
    {
        /* * GetByReferences * */
        $object = new OrderProductSupplier(Db::getInstance()->getValue("
            SELECT id_order_product_supplier
            FROM ". _DB_PREFIX_ ."order_product_supplier
            WHERE
                product_reference = '". pSQL($product_reference) ."'
            AND
                order_reference = '". pSQL($order_reference) ."'
        "));
        return $object -> id
            ? $object
            : false;

    }
    /**
     * installTable()
     * @description Creates table in database if not exists
     * */
    public static function installTable()
    {
        return Db::getInstance()->execute(
            "CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."order_product_supplier (
                `id_order_product_supplier` INT(10) AUTO_INCREMENT,
                `id_order` INT(10) NOT NULL,
                `id_product` INT(10) NOT NULL,
                `id_supplier` INT(10) NOT NULL,
                `supplier_name` VARCHAR(255) NULL,
                `product_reference` VARCHAR(255) NOT NULL,
                `order_reference` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`id_order_product_supplier`)
            )"
        );
    }
}
