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

/**
 * This function updates your module from previous versions to the version 1.1,
 * usefull when you modify your database, or register a new hook ...
 * Don't forget to create one file per version.
 */
function upgrade_module_0_1_3($module)
{
    $sql = [];
    unset($module);

    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . LggooglereviewsPlace::$definition['table'] . '`
            ADD COLUMN `show_header` TINYINT(1) NOT NULL AFTER `display_snippets`,
            ADD COLUMN `show_image_header` TINYINT(1) NOT NULL AFTER `show_header`,
            ADD COLUMN `show_link_business` TINYINT(1) NOT NULL AFTER `show_image_header`,
            ADD COLUMN `show_link_all_reviews` TINYINT(1) NOT NULL AFTER `show_link_business`,
            ADD COLUMN `show_link_add_review` TINYINT(1) NOT NULL AFTER `show_link_all_reviews`,
            ADD COLUMN `show_powered_by_google` TINYINT(1) NOT NULL AFTER `show_link_add_review`,
            ADD COLUMN `show_total_ratings` TINYINT(1) NOT NULL AFTER `show_powered_by_google`,
            ADD COLUMN `show_list_reviews` TINYINT(1) NOT NULL AFTER `show_total_ratings`,
            ADD COLUMN `show_user_links` TINYINT(1) NOT NULL AFTER `show_list_reviews`
            ';

    foreach ($sql as $query) {
        Db::getInstance()->execute($query);
    }

    return true;
}
