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

class LggooglereviewsPlace extends ObjectModel
{
    public $id_shop;
    public $google_place_id;
    public $name;
    public $photo;
    public $icon;
    public $address;
    public $rating;
    public $url;
    public $url_add_review;
    public $website;
    public $review_count;
    public $updated;
    public $num_reviews;
    public $order_reviews;
    public $display_snippets;
    public $show_header;
    public $show_image_header;
    public $show_link_business;
    public $show_link_all_reviews;
    public $show_link_add_review;
    public $show_powered_by_google;
    public $show_total_ratings;
    public $translated_reviews;
    public $show_user_links;
    public $validated;

    public static $definition = [
        'table' => 'lggooglereviews_place',
        'primary' => 'id_lggooglereviews_place',
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'google_place_id' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 80],
            'name' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 255],
            'photo' => ['type' => self::TYPE_STRING, 'size' => 255],
            'icon' => ['type' => self::TYPE_STRING, 'size' => 255],
            'display_snippets' => ['type' => self::TYPE_INT, 'required' => true],
            'num_reviews' => ['type' => self::TYPE_INT, 'required' => true],
            'order_reviews' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 80],
            'show_header' => ['type' => self::TYPE_INT, 'required' => true],
            'show_image_header' => ['type' => self::TYPE_INT, 'required' => true],
            'show_link_business' => ['type' => self::TYPE_INT, 'required' => true],
            'show_link_all_reviews' => ['type' => self::TYPE_INT, 'required' => true],
            'show_link_add_review' => ['type' => self::TYPE_INT, 'required' => true],
            'show_powered_by_google' => ['type' => self::TYPE_INT, 'required' => true],
            'show_total_ratings' => ['type' => self::TYPE_INT, 'required' => true],
            'translated_reviews' => ['type' => self::TYPE_INT, 'required' => true],
            'show_user_links' => ['type' => self::TYPE_INT, 'required' => true],
            'address' => ['type' => self::TYPE_STRING, 'size' => 255],
            'rating' => ['type' => self::TYPE_STRING, 'size' => 255],
            'url' => ['type' => self::TYPE_STRING, 'size' => 255],
            'url_add_review' => ['type' => self::TYPE_STRING, 'size' => 255],
            'website' => ['type' => self::TYPE_STRING, 'size' => 255],
            'review_count' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'updated' => ['type' => self::TYPE_INT],
            'validated' => ['type' => self::TYPE_INT, 'size' => 1],
        ],
    ];

    public static function getlist($all = true)
    {
        $sql = 'SELECT l.*,s.name as shop FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` l
            LEFT JOIN `' . _DB_PREFIX_ . 'shop` s 
                ON s.id_shop = l.id_shop';

        if (!$all) {
            $sql .= ' WHERE validated = true;';
        }

        return Db::getInstance()->executeS($sql);
    }

    public static function googlePlaceIDExists($google_place_id)
    {
        $query = new DbQuery();

        $query->select('a.`' . self::$definition['primary'] . '`');
        $query->from(self::$definition['table'], 'a');
        $query->where('a.`google_place_id` = \'' . $google_place_id . '\'');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
}
