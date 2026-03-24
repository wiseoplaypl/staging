<?php
/**
 * Copyright 2024 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . LggooglereviewsLog::$definition['table'] . '` (
	`' . LggooglereviewsLog::$definition['primary'] . '` int(11) NOT NULL AUTO_INCREMENT,
    `id_shop` int(10) NOT NULL,
    `reason` text NOT NULL,
	`date_add` datetime NOT NULL,
    PRIMARY KEY  (`' . LggooglereviewsLog::$definition['primary'] . '`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . LggooglereviewsPlace::$definition['table'] . '` (
    `' . LggooglereviewsPlace::$definition['primary'] . '` BIGINT(20) NOT NULL AUTO_INCREMENT,
    `id_shop` int(10) NOT NULL,
    `google_place_id` VARCHAR(80) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `photo` VARCHAR(255),
    `icon` VARCHAR(255),
    `display_snippets` TINYINT(1) NOT NULL,

    `num_reviews` INT(5) NOT NULL,
    `order_reviews` VARCHAR(20) NOT NULL,
    `show_header` TINYINT(1) NOT NULL,
    `show_image_header` TINYINT(1) NOT NULL,
    `show_link_business` TINYINT(1) NOT NULL,
    `show_link_all_reviews` TINYINT(1) NOT NULL,
    `show_link_add_review` TINYINT(1) NOT NULL,
    `show_powered_by_google` TINYINT(1) NOT NULL,
    `show_total_ratings` TINYINT(1) NOT NULL,
    `translated_reviews` TINYINT(1) NOT NULL,
    `show_user_links` TINYINT(1) NOT NULL,
    
    `address` VARCHAR(255),
    `rating` DOUBLE PRECISION,
    `url` VARCHAR(255),
    `url_add_review` VARCHAR(255),
    `website` VARCHAR(255),
    `review_count` INTEGER,
    `updated` BIGINT(20),
    `validated` TINYINT(1) NOT NULL,
    PRIMARY KEY  (`' . LggooglereviewsPlace::$definition['primary'] . '`),
    UNIQUE INDEX google_place_id (`google_place_id`,`id_shop`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . LggooglereviewsReview::$definition['table'] . '` (
    `' . LggooglereviewsReview::$definition['primary'] . '` BIGINT(20) NOT NULL AUTO_INCREMENT,
    `google_place_id` BIGINT(20) UNSIGNED NOT NULL,
    `hash` VARCHAR(40) NOT NULL,
    `rating` INTEGER NOT NULL,
    `text` TEXT,
    `time` INTEGER NOT NULL,
    `language` VARCHAR(10),
    `author_name` VARCHAR(255),
    `author_url` VARCHAR(255),
    `profile_photo_url` VARCHAR(255),
    `hide` VARCHAR(1) DEFAULT "" NOT NULL,
    PRIMARY KEY  (`' . LggooglereviewsReview::$definition['primary'] . '`),
    UNIQUE INDEX google_review_hash (`hash`),
    INDEX google_place_id (`google_place_id`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lggooglereviews_cache` (
    `' . LggooglereviewsPlace::$definition['primary'] . '` int(10) NOT NULL,
    `lang` VARCHAR(255) NOT NULL,
    `data` TEXT NOT NULL,
    `date_request` DATETIME NOT NULL,
    UNIQUE INDEX google_review_hash (`' . LggooglereviewsPlace::$definition['primary'] . '`,`lang`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
