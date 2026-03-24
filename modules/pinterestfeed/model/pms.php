<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA MILOSZ MYSZCZUK VATEU: PL9730945634
 * @copyright 2010-2022 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class pms extends ObjectModel
{
    public $id_pms;
    public $name;
    public $conf;
    public static $definition = array(
        'table' => 'pms',
        'primary' => 'id_pms',
        'multilang' => false,
        'fields' => array(
            'id_pms' => array('type' => ObjectModel :: TYPE_INT),
            'name' => array('type' => ObjectModel :: TYPE_STRING),
            'conf' => array('type' => ObjectModel :: TYPE_NOTHING),
        ),
    );

    public static function getAll()
    {
        $shop = new ShopUrl(Context::getContext()->shop->id);
        $url = $shop->getUrl(true).'modules/pinterestfeed/feed.php?feed=';
        $array = array();
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->Executes('SELECT *, concat("'.$url.'", id_pms) as url FROM `' . _DB_PREFIX_ . 'pms`');
    }

    public static function getConfig($id_pms)
    {
        $pms = new pms($id_pms);
        return $pms->conf;
    }

}