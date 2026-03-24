<?php
/**
 * PriceChangeLog.php
 * This file was generated with automatic module generator created by SzpaQ <dev-bot>
 * File is part of module Ordermax
 * @author SzpaQ
 * @copyright 2023 SzpaQ
 * @license All rights reserved
 * */

class PriceChangeLog extends ObjectModel
{

    /** @var Integer id_price_change_log **/
    public $id_price_change_log;

    /** @var Float price_before **/
    public $price_before;

    /** @var Float price_after **/
    public $price_after;

    /** @var String source **/
    public $source;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        "fields" => array(
            "id_price_change_log" => array(
                "type" => self::TYPE_INT,
            ),
            "price_before" => array(
                "type" => self::TYPE_FLOAT,
            ),
            "price_after" => array(
                "type" => self::TYPE_FLOAT,
            ),
            "source" => array(
                "type" => self::TYPE_STRING,
            ),
        ),
        'table' => 'price_change_log',
        'primary' => 'id_price_change_log',
    );
    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }
    /**
     * addChangelog()
     * @description If price change add the information to database
     * @var $price_before
     * @var $price_after
     * @var $source
     * @return bool
     * */
    public static function addChangelog($price_before, $price_after, $source) : bool
    {
        /* * addChangelog * */
        $changeLog = new PriceChangeLog;
        $changeLog -> price_before = $price_before;
        $changeLog -> price_after = $price_after;
        $changeLog -> source = $source;
        return $changeLog->save();

    }

    /**
     * installTable()
     * @description Creates table in database if not exists
     * */
    public static function installTable()
    {
        return Db::getInstance()->execute(
            "CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."price_change_log (
                `id_price_change_log` INT AUTO_INCREMENT,
                `price_before` DECIMAL(20,6) NULL,
                `price_after` DECIMAL(20,6) NULL,
                `source` VARCHAR(255) NULL,
                PRIMARY KEY (`id_price_change_log`)
            )"
        );
    }
}
