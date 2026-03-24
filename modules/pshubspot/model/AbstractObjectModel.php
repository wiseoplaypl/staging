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

use Tiralineas\HubspotApi\Client;
use Tiralineas\PsHubspot\AccessToken;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class AbstractObjectModel extends ObjectModel
{
    protected static $client;

    // defining one variable for db column, name should be column name
    public $sync_as;
    public $sync_at = null;
    public $sync_attempt_at = null;
    public $force_id = true;
    protected $is_new = false;

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        if (is_null($this->id)) {
            $this->id = $id;
            $this->is_new = true;
        }
        static::init();
    }

    protected static function init()
    {
        if (is_null(static::$client)) {
            static::$client = new Client(
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . AccessToken::get(),
                        'Shop-Uuid' => Configuration::get('PS_HUBSPOT_SHOP_UUID', '', '', ''),
                    ],
                ]
            );
        }
    }

    public function add($null_values = false, $auto_date = true)
    {
        $id = $this->id;
        $ret = parent::add($auto_date, $null_values);
        $this->is_new = $this->is_new && !$ret;
        if ($this->force_id) {
            $this->id = $id;
        }

        return $ret;
    }

    public function save($null_values = false, $auto_date = true)
    {
        return (int) $this->id > 0 && !$this->is_new ? $this->update($null_values) : $this->add($auto_date, $null_values);
    }

    public function is_new()
    {
        return $this->is_new;
    }

    public static function removeProtectedKeys(&$data, $keys)
    {
        foreach ($keys as $key) {
            unset($data[$key]);
        }
    }

    public static function removeUnnecessaryKeys(&$data, $necessaryKeys)
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $necessaryKeys)) {
                unset($data[$key]);
            }
        }
    }

    public static function translateKeys(&$data, $keys, $object = null)
    {
        foreach ($keys as $api => $my) {
            if ($object) {
                unset($data[$my]);
                $data[$api] = '' . $object->{$my};
            } elseif ($api != $my && array_key_exists($my, $data)) {
                $data[$api] = is_null($data[$my]) ? '' : $data[$my];
                unset($data[$my]);
            }
        }
    }

    public static function truncateKeys(&$data, $keys)
    {
        foreach ($keys as $key => $length) {
            if ($length > 0 && isset($data[$key])) {
                $data[$key] = mb_substr($data[$key], 0, $length);
            } else {
                unset($data[$key]);
            }
        }
    }

    public static function convertDatetimeFields(&$data, $keys)
    {
        foreach ($keys as $key) {
            $time = isset($data[$key]) ? strtotime($data[$key]) : 0;
            if ($time <= 0) {
                unset($data[$key]);
            } else {
                $data[$key] = '' . ($time * 1000);
            }
        }
    }

    public static function convertBooleanFields(&$data, $keys)
    {
        foreach ($keys as $key) {
            $data[$key] = isset($data[$key]) && $data[$key] ? 'yes' : 'no';
        }
    }
}
