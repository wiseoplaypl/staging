<?php
/**
* 2007-2020 Amazzing
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*
*  @author    Amazzing <mail@amazzing.ru>
*  @copyright 2007-2020 Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

class YandexTranslate extends TranslationProvider
{
    public function __construct($saved_data)
    {
        parent::__construct($saved_data);
        $this->info = array (
            'name' => 'Yandex.Translate',
            'required_credentials' => array('api_key'),
            'links' => array(
                'credentials' => 'http://api.yandex.com/key/form.xml?service=trnsl',
                'pricing' => 'https://translate.yandex.com/developers/prices',
            ),
        );
        $this->supported_languages = array(
            'af', 'am', 'ar', 'az', 'ba', 'be', 'bg', 'bn', 'bs', 'ca', 'ceb','cs', 'cy', 'da', 'de', 'el',
            'en', 'eo', 'es', 'et', 'eu', 'fa', 'fi', 'fr', 'ga', 'gd', 'gl', 'gu', 'he', 'hi', 'hr', 'ht',
            'hu', 'hy', 'id', 'is', 'it', 'ja', 'jv', 'ka', 'kk', 'km', 'kn', 'ko', 'ky', 'la', 'lb', 'lo',
            'lt', 'lv', 'mg', 'mhr', 'mi', 'mk', 'ml', 'mn', 'mr', 'mrj', 'ms', 'mt', 'my', 'ne', 'nl', 'no',
            'pa', 'pap', 'pl', 'pt', 'ro', 'ru', 'si', 'sk', 'sl', 'sq', 'sr', 'su', 'sv', 'sw', 'ta', 'te',
            'tg', 'th', 'tl', 'tr', 'tt', 'udm', 'uk', 'ur', 'uz', 'vi', 'xh', 'yi', 'zh'
        );
        $this->free_limit = array('d' => '1 000 000', 'm' => '10 000 000');
        $this->api_version = '1.5';
        $this->stringify_separator = '<i class="ntrns"></i>';
    }

    public function prepareContentForTranslation(&$content)
    {
        if ($keys = parent::prepareContentForTranslation($content)) {
            $this->processed_chars_num += (count($keys) - 1) * Tools::strlen($this->stringify_separator);
            $content = implode($this->stringify_separator, $content);
        }
        return $keys;
    }

    public function formatTranslation(&$translation, $translation_keys)
    {
        if ($translation_keys) {
            $translation = explode($this->stringify_separator, $translation);
        }
        parent::formatTranslation($translation, $translation_keys);
    }

    public function getTranslation($content, $from, $to)
    {
        $data = array(
            'url' => 'https://translate.yandex.net/api/v'.$this->api_version.'/tr.json/translate',
            'headers' => array('Content-Type: application/x-www-form-urlencoded'),
            'post_fields' => http_build_query(array(
                'key'    => $this->getCredentials('api_key'),
                'lang'   => $from.'-'.$to,
                'text'   => $content.'',
                'format' => 'html'
            )),
        );
        $response = $this->curlRequest($data);
        if (!$this->detectPossibleErrors($response)) {
            return html_entity_decode($response['text'][0], ENT_QUOTES | ENT_XML1, 'UTF-8');
        }
    }

    public function detectPossibleErrors($response)
    {
        if ($response['code'] !== 200) {
            $this->errors[] = isset($response['message']) ? $response['message'] : 'unknown_error';
        } elseif (!isset($response['text'][0])) {
            $this->errors[] = 'unknown_error';
        }
        return $this->errors;
    }

    public function getSupportedLanguages()
    {
        $data = array(
            'url' => 'https://translate.yandex.net/api/v'.$this->api_version.'/tr.json/getLangs',
            'post_fields' => http_build_query(array(
                'key'    => $this->getCredentials('api_key'),
                'ui'   => 'en',
            )),
        );
        $response = $this->curlRequest($data); // '\''.implode('\', \'', $keys).'\''
        return isset($response['langs']) ? array_keys($response['langs']) : 'error';
    }
}
