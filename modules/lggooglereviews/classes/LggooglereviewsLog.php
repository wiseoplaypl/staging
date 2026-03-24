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

class LggooglereviewsLog extends ObjectModel
{
    const LIMIT_LOG = 25;

    public $id_lggoogle_reviews_place;
    public $id_shop;
    public $google_place_id;
    public $lang;
    public $result;
    public $date_add;

    public static $definition = [
        'table' => 'lggooglereviews_log',
        'primary' => 'id_lggooglereviews_log',
        'fields' => [
            'id_lggoogle_reviews_place' => ['type' => self::TYPE_INT, 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'required' => true],
            'google_place_id' => ['type' => self::TYPE_STRING, 'required' => true],
            'result' => ['type' => self::TYPE_STRING],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    public static function register($id_lggoogle_reviews_place = 0, $id_shop = 0, $google_place_id = 0, $result = '')
    {
        if (Configuration::get('LGGOOGLEREVIEWS_LOGGING')
            && !empty($id_shop)
        ) {
            $sql = 'INSERT INTO `' . _DB_PREFIX_ . self::$definition['table'] . '`
            (
                `id_lggoogle_reviews_place`,
                `id_shop`,
                `google_place_id`,
                `result`,
                `date_add`
            )
            VALUES (
                "' . $id_lggoogle_reviews_place . '",
                "' . $id_shop . '",
                "' . pSQL($google_place_id) . '",
                "' . pSQL($result) . '",
                NOW()
            )';

            return Db::getInstance()->execute($sql);
        }
        return false;
    }

    public static function getlist($limit = 25)
    {
        $sql = 'SELECT l.*,s.name as shop FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` l
            LEFT JOIN `' . _DB_PREFIX_ . 'shop` s 
                ON s.id_shop = l.id_shop
            ';
        $sql .= ' 
            ORDER BY l.date_add DESC
            LIMIT 0,' . $limit;

        return Db::getInstance()->executeS($sql);
    }

    public static function cleanDebug($full = false)
    {
        if ($full) {
            $sql = 'TRUNCATE TABLE `' . _DB_PREFIX_ . LggooglereviewsLog::$definition['table'] . '`;';
            Db::getInstance()->execute($sql);
        } else {
            $sql = 'DELETE FROM `' . _DB_PREFIX_ . LggooglereviewsLog::$definition['table'] . '` 
                WHERE date_add < CURRENT_DATE() - INTERVAL 1 DAY';
            Db::getInstance()->execute($sql);
        }
    }
}
