<?php
/**
 * 2007-2024 Tiralineas
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Ander <ander@tiralineas.digital>
 * @copyright 2007-2024 Tiralineas
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Tiralineas
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tiralineas_available_properties` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `object_type` ENUM ("contact","company","deal","ticket","line_item","product") NOT NULL,
    `name` VARCHAR(64) NOT NULL,
    `groupName` VARCHAR(64) NOT NULL,
    `label` VARCHAR(256) NOT NULL,
    `type` ENUM ("string", "enumeration", "number", "bool", "datetime", "date"),
    `fieldType` ENUM ("textarea","text","select","number","date","checkbox","booleancheckbox"),
    `formField` INT(1) NOT NULL,
    `options_callback` VARCHAR(256) NULL, 
    `value_callback` VARCHAR(256) NULL,
    `sync_at` DATETIME default NULL,
    PRIMARY KEY (`id`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tiralineas_available_lists` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `object_type` ENUM ("contact","company","deal","ticket","line_item","product") NOT NULL default "contact",
    `name` VARCHAR(64) NOT NULL,
    `dynamic` INT(1) NOT NULL,
    `filters` TEXT NULL,
    `sync_at` DATETIME default NULL,
    PRIMARY KEY (`id`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tiralineas_available_workflows` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(64) NOT NULL,
    `enabled` INT(1) NOT NULL,
    `actions` TEXT NULL,
    `enrollOnCriteriaUpdate` TEXT NULL,
    `reEnrollmentTriggerSets` TEXT NULL,
    `sync_at` DATETIME default NULL,
    PRIMARY KEY (`id`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tiralineas_available_groups` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `object_type` ENUM ("contact","company","deal","ticket","line_item","product") NOT NULL default "contact",
    `name` VARCHAR(64) NOT NULL,
    `label` VARCHAR(256) NULL,
    `sync_at` DATETIME default NULL,
    PRIMARY KEY (`id`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tiralineas_hs_store` (
    `id` INT(11) NOT NULL,
    `sync_as` VARCHAR(256) NOT NULL,
    `sync_at` DATETIME default CURRENT_TIMESTAMP,
    `sync_attempt_at` DATETIME default NULL,
    PRIMARY KEY (`id`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tiralineas_hs_contact` (
    `id` INT(11) NOT NULL,
    `sync_as` VARCHAR(256) NOT NULL,
    `sync_at` DATETIME default CURRENT_TIMESTAMP,
    `sync_attempt_at` DATETIME default NULL,
    PRIMARY KEY (`id`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tiralineas_hs_product` (
    `id` INT(11) NOT NULL,
    `sync_as` VARCHAR(256) NOT NULL,
    `sync_at` DATETIME default CURRENT_TIMESTAMP,
    `sync_attempt_at` DATETIME default NULL,
    PRIMARY KEY (`id`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tiralineas_hs_deal` (
    `id` INT(11) NOT NULL,
    `sync_as` VARCHAR(256) NOT NULL,
    `sync_at` DATETIME default CURRENT_TIMESTAMP,
    `sync_attempt_at` DATETIME default NULL,
    PRIMARY KEY (`id`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tiralineas_hs_pipeline` (
    `id` INT(11) NOT NULL,
    `sync_as` VARCHAR(256) NOT NULL,
    `sync_at` DATETIME default CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

$sql = [];
$data_dir = _PS_MODULE_DIR_ . 'pshubspot/sql/data';
foreach (scandir($data_dir) as $custom_file) {
    if (!preg_match('/.*\.php$/', $custom_file)) {
        continue;
    }
    if ($custom_file == 'index.php') {
        continue;
    }
    if (is_file($data_dir . DIRECTORY_SEPARATOR . $custom_file)) {
        include_once $data_dir . DIRECTORY_SEPARATOR . $custom_file;
    }
}

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
