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

function upgrade_module_1_4_0($module)
{
    Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'higacustomevent` (
            `id_event` int(10) unsigned not null AUTO_INCREMENT,
            `active` TINYINT  NOT NULL,
            `action` varchar(50) not null,
            `selector` varchar(255) not null,
            `event_category` varchar(255) not null,
            `event_action` varchar(255) not null,
            `event_label` varchar(255) not null,
            `event_value` varchar(255) not null,
            `date_add` datetime not null,
            `date_upd` datetime not null,
            PRIMARY KEY (`id_event`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
    ');

    Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'higacustomevent_shop` (
            `id_event` int(10) unsigned NOT NULL,
            `id_shop` int(10) unsigned NOT NULL,
        PRIMARY KEY (`id_event`, `id_shop`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
    ');

    $module->hiPrestaClass->createTabs('AdminHiGoogleAnalytics', 'Admin Google Analytics', 'CONTROLLER_TABS_HI_GA', 0);

    return true;
}
