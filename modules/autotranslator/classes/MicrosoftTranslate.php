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

class MicrosoftTranslate extends TranslationProvider
{
    public function __construct($saved_data)
    {
        parent::__construct($saved_data);
        $this->info = array (
            'name' => 'Microsoft Translate',
            'required_credentials' => array('api_key'),
            'links' => array(
                'credentials' => 'https://docs.microsoft.com/en-us/azure/cognitive-services/'.
                'translator/translator-text-how-to-signup',
                'pricing' => 'https://azure.microsoft.com/en-us/pricing/details/cognitive-services/'.
                'translator-text-api/',
            ),
        );
        $this->supported_languages = array(
            'af', 'ar', 'bg', 'bn', 'bs', 'ca', 'cs', 'cy', 'da', 'de', 'el', 'en', 'es', 'et', 'fa', 'fi',
            'fil', 'fj', 'fr', 'he', 'hi', 'hr', 'ht', 'hu', 'id', 'is', 'it', 'ja', 'ko', 'lt', 'lv', 'mg',
            'mi', 'ms', 'mt', 'mww', 'nb', 'nl', 'otq', 'pl', 'pt', 'ro', 'ru', 'sk', 'sl', 'sm', 'sr-Cyrl',
            'sr-Latn', 'sv', 'sw', 'ta', 'te', 'th', 'tlh', 'to', 'tr', 'ty', 'uk', 'ur', 'vi', 'yua', 'yue',
            'zh-Hans', 'zh-Hant'
        );
        $this->iso_substitutions['zh'] = 'zh-Hans';
        $this->iso_substitutions['tw'] = 'zh-Hant';
        $this->free_limit = array('m' => '2 000 000');
        $this->api_version = '3.0';
        // $this->stringify_separator = '<i class="notranslate"></i>';
    }

    public function prepareContentForTranslation(&$content)
    {
        if ($keys = parent::prepareContentForTranslation($content)) {
            foreach ($content as &$c) {
                $c = array('text' => $c.'');
            }
        } else {
            $content = array(array('text' => $content.''));
        }
        return $keys;
    }

    public function getTranslation($content, $from, $to)
    {
        $data = array(
            'url' => 'https://api.cognitive.microsofttranslator.com/translate',
            'get_fields' => array(
                'from' => $from,
                'to' => $to,
                'api-version' => $this->api_version,
                'textType' => 'html',
            ),
            'post_fields' => $content,
            'headers' => array(
                'Content-Type: application/json; charset=UTF-8',
                'Ocp-Apim-Subscription-Key: '.$this->getCredentials('api_key'),
            ),
        );
        $response = $this->curlRequest($data);
        if (!$this->detectPossibleErrors($response)) {
            foreach ($response as &$r) {
                $r = $r['translations'][0]['text'];
            }
            return $response;
        }
    }

    public function detectPossibleErrors($response)
    {
        if (isset($response['error'])) {
            $this->errors[] = $response['error']['message'];
        } elseif (!isset($response[0]['translations'][0]['text'])) {
            $this->errors[] = 'error';
        }
        return $this->errors;
    }

    public function getSupportedLanguages()
    {
        $data = array(
            'url' => 'https://api.cognitive.microsofttranslator.com/languages',
            'get_fields' => array('api-version' => $this->api_version, 'scope' => 'translation'),
        );
        $response = $this->curlRequest($data);
        return isset($response['translation']) ? array_keys($response['translation']) : 'error';
    }
}
