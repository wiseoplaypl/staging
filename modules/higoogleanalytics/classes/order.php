<?php
/**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class HiGoogleAnalyticsOrder extends ObjectModel
{
    public $id_track;
    public $id_order;
    public $id_state;
    public $tracked;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'higaorder',
        'primary' => 'id_track',
        'multilang' => false,
        'fields' => [
            'id_order' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'id_state' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'tracked' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false],
        ],
    ];

    public static function getOrdersToTrack()
    {
        $query = new DbQuery();
        $query
            ->select('o.*')
            ->from('higaorder', 'o')
            ->where('o.`tracked` = 0');

        return Db::getInstance()->executeS($query);
    }
}
