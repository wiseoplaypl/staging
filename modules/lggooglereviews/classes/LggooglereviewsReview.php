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

class LggooglereviewsReview extends ObjectModel
{
    public $google_place_id;
    public $hash;
    public $rating;
    public $text;
    public $time;
    public $language;
    public $website;
    public $author_name;
    public $author_url;
    public $profile_photo_url;
    public $hide;

    public static $definition = [
        'table' => 'lggooglereviews_review',
        'primary' => 'id_lggooglereviews_review',
        'fields' => [
            'google_place_id' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 80],
            'hash' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 40],
            'rating' => ['type' => self::TYPE_STRING, 'size' => 255],
            'text' => ['type' => self::TYPE_STRING],
            'time' => ['type' => self::TYPE_INT],
            'language' => ['type' => self::TYPE_STRING, 'size' => 10],
            'author_name' => ['type' => self::TYPE_STRING, 'size' => 255],
            'author_url' => ['type' => self::TYPE_STRING, 'size' => 255],
            'profile_photo_url' => ['type' => self::TYPE_STRING, 'size' => 255],
            'hide' => ['type' => self::TYPE_STRING, 'size' => 1],
        ],
    ];

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
}
