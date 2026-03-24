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

class TranslationProvider
{
    public function __construct($saved_data)
    {
        $this->saved_data = $saved_data;
        $this->errors = array();
        $this->supported_languages = array();
        $this->iso_substitutions = array(
            'gb' => 'en',
            'si' => 'sl', // Slovenian is represented by si in previous PS versions
            'vn' => 'vi', // Tieng Viet (Vietnamese)
            'nn' => 'no', // Nynorsk (Norwegian)
            'qc' => 'fr', // Francais CA (French)
            'br' => 'pt', // Brazilian (Portuguese)
            'mx' => 'es', // Mexican (Spanish)
            'tw' => 'zh', // Taiwanese (Chinese)
        );
        $this->processed_chars_num = 0;
    }

    public function getCredentials($key)
    {
        return isset($this->saved_data['credentials'][$key]) ?
        $this->saved_data['credentials'][$key] : '';
    }

    public function getNotSupportedLanguages($shop_languages)
    {
        $not_supported_languages = array();
        foreach (array_diff($shop_languages, $this->supported_languages) as $iso) {
            if (!in_array($this->compatibleISO($iso), $this->supported_languages)) {
                $not_supported_languages[] = $iso;
            }
        }
        return $not_supported_languages;
    }

    public function supportsModel($from, $to)
    {
        return in_array($this->compatibleISO($from), $this->supported_languages)
            && in_array($this->compatibleISO($to), $this->supported_languages);
    }

    public function translate($content, $from, $to)
    {
        $translation_keys = $this->prepareContentForTranslation($content);
        $translation = $this->getTranslation($content, $this->compatibleISO($from), $this->compatibleISO($to));
        if (!$this->errors) {
            $this->formatTranslation($translation, $translation_keys);
        }
        return $translation;
    }


    public function compatibleISO($iso_code)
    {
        return isset($this->iso_substitutions[$iso_code]) ? $this->iso_substitutions[$iso_code] : $iso_code;
    }

    public function prepareContentForTranslation(&$content)
    {
        $keys = array();
        if (is_array($content)) {
            $keys = array_keys($content);
            $content = array_values($content);
            foreach ($content as $c) {
                $this->processed_chars_num += Tools::strlen($c);
            }
        } else {
            $this->processed_chars_num += Tools::strlen($content);
        }
        return $keys;
    }

    public function formatTranslation(&$translation, $translation_keys)
    {
        if ($translation_keys) {
            if (count($translation) != count($translation_keys)) {
                $translation = array();
                $this->errors[] = 'error';
            } else {
                $translation_assoc = array();
                foreach ($translation as $k => $t) {
                    $translation_assoc[$translation_keys[$k]] = $t;
                }
                $translation = $translation_assoc;
            }
        } elseif (is_array($translation)) {
            $translation = current($translation);
        }
        return $translation;
    }

    public function curlRequest($data, $decode_response = true)
    {
        $response = $decode_response ? array() : '';
        if (function_exists('curl_init')) {
            $session = curl_init();
            if (!empty($data['get_fields'])) {
                if (is_array($data['get_fields'])) {
                    $data['get_fields'] = http_build_query($data['get_fields']);
                }
                $data['url'] .= '?'.$data['get_fields'];
            }
            curl_setopt($session, CURLOPT_URL, $data['url']);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);
            if (!empty($data['headers'])) {
                curl_setopt($session, CURLOPT_HTTPHEADER, $data['headers']);
            }
            if (!empty($data['post_fields'])) {
                if (is_array($data['post_fields'])) {
                    $data['post_fields'] = Tools::jsonEncode($data['post_fields']);
                }
                curl_setopt($session, CURLOPT_POSTFIELDS, $data['post_fields']);
            }
            if (!empty($data['login:password'])) {
                curl_setopt($session, CURLOPT_USERPWD, $data['login:password']);
            }
            $response = curl_exec($session);
            $possible_error = curl_error($session);
            curl_close($session);
            if ($possible_error) {
                $this->errors[] = $possible_error;
            } else {
                $response = $decode_response ? Tools::jsonDecode($response, true) : $response;
            }
        } else {
            $this->errors[] = 'no_curl';
        }
        return $response;
    }
}
