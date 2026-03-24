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

class LggooglereviewsTools
{
    const GOOGLE_PLACE_URL = 'https://maps.googleapis.com/maps/api/place/';

    public static function getReviews($place, $lang = '', $cached = 0)
    {
        if ($lang == '') {
            $lang = Context::getContext()->language->iso_code;
        }

        if ($cached) {
            $reviews_place = self::getCache((int) $place->id, $lang);

            if (!$reviews_place) {
                $reviews_place = self::importReviews($place, $lang);
                self::setCache((int) $place->id, $lang, pSql(self::jsonEncode($reviews_place)));
            }
        } else {
            $reviews_place = self::importReviews($place, $lang);

            self::setCache((int) $place->id, $lang, pSql(self::jsonEncode($reviews_place)));
        }

        return $reviews_place;
    }

    public static function importReviews($place, $lang = '')
    {
        if ($lang == '') {
            $lang = Context::getContext()->language->iso_code;
        }

        $url = self::getApiReviewUrl($place->google_place_id, $lang);
        $response = self::urlOpen($url);

        $response_data = $response['data'];
        $response_json = self::jsonDecode($response_data, 1);

        if ($response_json && isset($response_json['result'])) {
            $response_json['result']['business_photo'] = self::businessAvatar($response_json['result']);
            // self::grw_save_reviews($response_json->result);
            $result = $response_json['result'];

            $status = 'success';

            $place->url = $result['url'];
            $place->rating = isset($result['rating']) ? $result['rating'] : 0;
            $place->review_count = isset($result['user_ratings_total']) ?
                $result['user_ratings_total'] : 0;

            $place->validated = true;
        } else {
            $result = null;

            $status = 'failed';

            $place->url = '';
            $place->rating = 0;
            $place->review_count = 0;
            $place->validated = false;
        }

        $response = compact('status', 'result');

        $place->save();

        return $response;
    }

    public static function getCache($place_id, $lang)
    {
        $sql = 'SELECT data FROM `' . _DB_PREFIX_ . 'lggooglereviews_cache` 
        WHERE id_lggooglereviews_place = ' . $place_id . ' AND
        lang = "' . $lang . '" AND
        date_request > CURRENT_DATE() - INTERVAL 1 DAY';

        $data = Db::getInstance()->getValue($sql);

        if ($data != '') {
            return self::jsonDecode($data, 1);
        } else {
            return false;
        }
    }

    public static function setCache($place_id, $lang, $data)
    {
        $data_set = [];
        $data_set['id_lggooglereviews_place'] = $place_id;
        $data_set['lang'] = $lang;
        $data_set['data'] = $data;
        $data_set['date_request'] = date('Y-m-d H:i:s');

        Db::getInstance()->insert('lggooglereviews_cache', $data_set, false, false, DB::REPLACE);
    }

    public static function businessAvatar($response_result_json)
    {
        if (isset($response_result_json['photos'])) {
            $request_url = self::getApiPhotoUrl($response_result_json['photos'][0]['photo_reference']);
            $response = self::urlOpen($request_url);
            foreach ($response['headers'] as $header) {
                if (strpos($header, 'Location: ') !== false) {
                    return str_replace('Location: ', '', $header);
                }
            }
        }
        return null;
    }

    public static function getApiReviewUrl($placeid, $reviews_lang = '')
    {
        $api_key = Configuration::get('LGGOOGLEREVIEWS_APIKEY');
        $orderby = Tools::getValue('order_reviews');

        if ($orderby == 'Newest') {
            $orderby = 'newest';
        } else {
            $orderby = 'most_relevant';
        }

        $url = self::GOOGLE_PLACE_URL . 'details/json?placeid=' . $placeid . '&key=' . $api_key . '&reviews_sort=' . $orderby;

        // Opción para mostrar las reseñas en el idioma original o en el idioma de la tienda
        $translated_reviews = (int) Tools::getValue('translated_reviews');
        if ($translated_reviews == 1) {
            $iso_lang = Context::getContext()->language->iso_code;
            $grw_language = Tools::strlen($reviews_lang) > 0 ? $reviews_lang : $iso_lang;
            if (Tools::strlen($grw_language) > 0) {
                $url = $url . '&language=' . $grw_language;
            }
        } else {
            $url = $url . '&reviews_no_translations=true'; // Muestra las reseñas en el idioma original sin traducir
        }

        return $url;
    }

    public static function getApiPhotoUrl($photo_reference)
    {
        $api_key = Configuration::get('LGGOOGLEREVIEWS_APIKEY');

        $url = 'https://maps.googleapis.com/maps/api/place/photo?' .
            'photoreference=' . $photo_reference . '&' .
            'key=' . $api_key . '&' .
            'maxwidth=300&' .
            'maxheight=300';

        return $url;
    }

    public static function urlOpen($url, $postdata = false, $headers = [])
    {
        $response = [
            'data' => '',
            'code' => 0,
        ];

        $url = preg_replace('/\s+/', '+', $url);

        if (function_exists('curl_init') && function_exists('curl_setopt_array')) {
            self::curlUrlOpen($url, $postdata, $headers, $response);
        } elseif (ini_get('allow_url_fopen') && function_exists('stream_get_contents')) {
            self::fopenUrlOpen($url, $postdata, $headers, $response);
        } else {
            self::fsockopenUrlOpen($url, $postdata, $headers, $response);
        }
        return $response;
    }

    public static function curlUrlOpen($url, $postdata, $headers, &$response)
    {
        $c = curl_init($url);
        $postdata_str = self::getQueryString($postdata);

        $c_options = [
            // CURLOPT_USERAGENT => RPLG_USER_AGENT,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => ($postdata_str ? 1 : 0),
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => array_merge(['Expect:'], $headers),
            // CURLOPT_TIMEOUT => RP_SOCKET_TIMEOUT
        ];
        if ($postdata) {
            $c_options[CURLOPT_POSTFIELDS] = $postdata_str;
        }
        curl_setopt_array($c, $c_options);

        $open_basedir = ini_get('open_basedir');
        if (empty($open_basedir) && filter_var(ini_get('safe_mode'), FILTER_VALIDATE_BOOLEAN) === false) {
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
        }
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);

        $data = curl_exec($c);

        // cURL automatically handles Proxy rewrites, remove the 'HTTP/1.0 200 Connection established' string
        if (stripos($data, 'HTTP/1.0 200 Connection established\r\n\r\n') !== false) {
            $data = str_replace('HTTP/1.0 200 Connection established\r\n\r\n', '', $data);
        }

        list($resp_headers, $response['data']) = explode("\r\n\r\n", $data, 2);

        $response['headers'] = self::getResponseHeaders($resp_headers, $response);
        $response['code'] = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);
    }

    // fopen
    public static function fopenUrlOpen($url, $postdata, $headers, &$response)
    {
        $params = [];

        if ($postdata) {
            $params = [
                'http' => [
                    'method' => 'POST',
                    'header' => implode('\r\n', array_merge(['Content-Type: application/x-www-form-urlencoded'], $headers)),
                    'content' => self::getQueryString($postdata),
                    'timeout' => RPLG_SOCKET_TIMEOUT,
                ],
            ];
        } else {
            $params = [
                'http' => [
                    'header' => implode('\r\n', $headers),
                ],
            ];
        }

        ini_set('user_agent', RPLG_USER_AGENT);
        $ctx = stream_context_create($params);
        $fp = fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            return false;
        }

        /* $http_response_header => Var native in PHP 4-8 */
        list($unused1, $response['code'], $unused2) = explode(' ', $http_response_header[0], 3);

        unset($unused1);
        unset($unused2);

        $resp_headers = array_slice($http_response_header, 1);

        foreach ($resp_headers as $header) {
            $header = explode(':', $header);
            $header[0] = trim($header[0]);
            $header[1] = trim($header[1]);
            $resp_headers[Tools::strtolower($header[0])] = Tools::strtolower($header[1]);
        }
        $response['data'] = stream_get_contents($fp);
        $response['headers'] = $resp_headers;
    }

    // fsockpen
    public static function fsockopenUrlOpen($url, $postdata, $headers, &$response)
    {
        $buf = '';
        $req = '';
        $length = 0;
        $postdata_str = self::getQueryString($postdata);
        $url_pieces = parse_url($url);
        $host = $url_pieces['host'];

        if (!isset($url_pieces['port'])) {
            switch ($url_pieces['scheme']) {
                case 'http':
                    $url_pieces['port'] = 80;
                    break;
                case 'https':
                    $url_pieces['port'] = 443;
                    $host = 'ssl://' . $url_pieces['host'];
                    break;
            }
        }

        if (!isset($url_pieces['path'])) {
            $url_pieces['path'] = '/';
        }

        if (($url_pieces['port'] == 80 && $url_pieces['scheme'] == 'http')
            || ($url_pieces['port'] == 443 && $url_pieces['scheme'] == 'https')) {
            $req_host = $url_pieces['host'];
        } else {
            $req_host = $url_pieces['host'] . ':' . $url_pieces['port'];
        }

        $fp = @fsockopen($host, $url_pieces['port'], $errno, $errstr, RPLG_SOCKET_TIMEOUT);
        if (!$fp) {
            return false;
        }

        $path = $url_pieces['path'];
        if (isset($url_pieces['query'])) {
            $path .= '?' . $url_pieces['query'];
        }

        $req .= ($postdata_str ? 'POST' : 'GET') . ' ' . $path . ' HTTP/1.1\r\n';
        $req .= 'Host: ' . $req_host . '\r\n';
        $req .= self::getHttpHeaders($postdata_str, $headers);
        if ($postdata_str) {
            $req .= '\r\n\r\n' . $postdata_str;
        }
        $req .= '\r\n\r\n';

        fwrite($fp, $req);
        while (!feof($fp)) {
            $buf .= fgets($fp, 4096);
        }

        list($headers, $response['data']) = explode('\r\n\r\n', $buf, 2);

        $headers = self::getResponseHeaders($headers, $response);

        if (isset($headers['transfer-encoding']) && 'chunked' == Tools::strtolower($headers['transfer-encoding'])) {
            $chunk_data = $response['data'];
            $joined_data = '';
            while (true) {
                list($chunk_length, $chunk_data) = explode('\r\n', $chunk_data, 2);
                $chunk_length = hexdec($chunk_length);
                if (!$chunk_length || !Tools::strlen($chunk_data)) {
                    break;
                }

                $joined_data .= Tools::substr($chunk_data, 0, $chunk_length);
                $chunk_data = Tools::substr($chunk_data, $chunk_length + 1);
                $length += $chunk_length;
            }
            $response['data'] = $joined_data;
        } else {
            $length = $headers['content-length'];
        }
        $response['headers'] = $headers;
    }

    // helpers
    public static function getQueryString($params)
    {
        $query = '';

        if ($params) {
            foreach ($params as $key => $value) {
                $query .= urlencode($key) . '=' . urlencode($value) . '&';
            }
        }
        return $query;
    }

    public static function getResponseHeaders($headers, &$response)
    {
        $headers = explode('\r\n', $headers);
        list($unused1, $response['code'], $unused2) = explode(' ', $headers[0], 3);

        unset($unused1);
        unset($unused2);

        $headers = array_slice($headers, 1);
        foreach ($headers as $header) {
            $header = explode(':', $header);
            $header[0] = trim($header[0]);
            $header[1] = trim($header[1]);
            $headers[Tools::strtolower($header[0])] = $header[1];
        }
        return $headers;
    }

    public static function getHttpHeaders($content, $headers)
    {
        $req_headers = [];
        $req_headers[] = 'User-Agent: ' . RPLG_USER_AGENT;
        $req_headers[] = 'Connection: close';
        if ($content) {
            $req_headers[] = 'Content-Length: ' . Tools::strlen($content);
            $req_headers[] = 'Content-Type: application/x-www-form-urlencoded';
        }
        return implode('\r\n', array_merge($req_headers, $headers));
    }

    public static function getDateFormat()
    {
        $format = Db::getInstance()->getValue(
            'SELECT date_format_lite ' .
            'FROM ' . _DB_PREFIX_ . 'lang ' .
            'WHERE id_lang = ' . (int) Context::getContext()->language->id
        );
        return $format;
    }

    public static function createDefaultConfig($values)
    {
        $created = Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY', '1');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY_TYPE', '1');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY_SIDE', '5');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY_LANGUAGE', '0');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_PER_PAGE', '20');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_TEXTCOLOR', '777777');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_TEXTCOLOR2', '777777');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_BACKCOLOR2', 'FBFBFB');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY_COMMENTS', '1');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY_DEFAULT', '3');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY_MORE', '10');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY_ZEROSTAR', '0');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY_LANGUAGE2', '0');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY_SNIPPETS', '0');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY_SNIPPETS2', '0');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY_PROD_SCHE', '0');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY_PROD_SCHE2', '0');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_PRICE_RANGE', '$$');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY_ORDER', '2');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY_ORDER2', '2');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DISPLAY_SLIDER', '1');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_OWLCAROUSEL_DISABLED', '0');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_SLIDER_BLOCKS', '4');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_SLIDER_TOTAL', '12');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_OPINION_FORM', '1');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_SCALE', '10');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_CATTOPMARGIN', '0');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_CATBOTMARGIN', '0');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_PRODTOPMARGIN', '0');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_PRODBOTMARGIN', '0');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_CROSS', '1');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_STORE_FILTER', '1');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_PRODUCT_FILTER', '1');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_PRODUCT_FILTER_NB', '3');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_STORE_FORM', '1');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_PRODUCT_FORM', '1');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_BGDESIGN1', 'vertical');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_BGDESIGN2', 'greylight');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_STARDESIGN1', 'plain');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_STARDESIGN2', 'yellow');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_STARSIZE', '120');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_STARS_TYPE', '1');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_CSS_CONF', self::jsonEncode($values['extraright_css_config']));
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_BACKGROUND5', 'f6f6f6');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_BORDERSIZE5', '1');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_TAB_CONTENT', $values['tab_type']);
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_BORDERCOLOR5', '555555');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_RATECOLOR5', '555555');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_RATESIZE5', '22');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_RATEFAMILY5', 'arial');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_COMMENTCOLOR5', '555555');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_COMMENTSIZE5', '18');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_COMMENTFAMILY5', 'arial');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_COMMENTALIGN5', 'center');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DATECOLOR5', '8C8C8C');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DATESIZE5', '12');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DATEFAMILY5', 'arial');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DATEALIGN5', 'left');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_EMAIL_ALERTS', '1');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_SUBJECT_CRON', $values['subject_cron']);
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_SUBJECT_NEWREVIEWS', $values['subject_newreviews']);
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DIAS', '7');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DIAS2', '30');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_EMAIL_TWICE', '0');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_DAYS_AFTER', '10');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_VALIDATION', '1');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_BOXES', '1');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_TOP6', '70');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_LEFT6', '0');
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_STARST_POSITION', $values['prod_anchor_position']);
        $created &= Configuration::updateValue('PS_LGGOOGLEREVIEWS_EMAIL_CRON', Configuration::get('PS_SHOP_EMAIL'));

        // One status selected by default
        $created &= Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'lgcomments_status` VALUES (5)');

        // One group selected by default
        $created &= Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'lgcomments_customergroups` VALUES (3)');

        // All shops selected by default
        $shops = Db::getInstance()->executeS('SELECT `id_shop` FROM `' . _DB_PREFIX_ . 'shop`');

        foreach ($shops as $shop) {
            $created &= Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'lgcomments_multistore` VALUES (' . (int) $shop['id_shop'] . ')'
            );
        }
        return $created;
    }

    public static function addCSS($path, $id, $context = null, $force_old_method = false)
    {
        if (is_null($context)) {
            $context = Context::getContext();
        }
        if (version_compare(_PS_VERSION_, '1.7.0', '>') && !$force_old_method) {
            $context->controller->registerStylesheet(
                $id,
                $path,
                [
                    'media' => 'all',
                    'priority' => 150,
                ]
            );
        } else {
            Context::getContext()->controller->addCSS($path);
        }
    }

    public static function addJS($path, $id = null)
    {
        if (version_compare(_PS_VERSION_, '1.7.0', '>')) {
            Context::getContext()->controller->registerJavascript(
                $id,
                $path,
                [
                    'position' => 'bottom',
                    'priority' => 150,
                ]
            );
        } else {
            Context::getContext()->controller->addJS($path);
        }
    }

    public static function jsonEncode($data, $options = 0, $depth = 512)
    {
        return method_exists('Tools', 'jsonEncode') ?
            Tools::jsonEncode($data) :
            json_encode($data, $options, $depth);
    }

    public static function jsonDecode($data, $assoc = false, $depth = 512, $options = 0)
    {
        return method_exists('Tools', 'jsonDecode') ?
            Tools::jsonDecode($data, $assoc) :
            json_decode($data, $assoc, $depth, $options);
    }

    public static function getMediaBasePath($module)
    {
        if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
            return 'modules/' . $module->name . '/';
        } else {
            return $module->getPathUri();
        }
    }

    public static function getFormattedName($size)
    {
        if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
            return ImageType::getFormattedName($size);
        } elseif (version_compare(_PS_VERSION_, '1.5.3', '>=')) {
            return ImageType::getFormatedName($size);
        } else {
            return $size;
        }
    }
}
