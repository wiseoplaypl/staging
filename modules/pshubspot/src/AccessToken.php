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

namespace Tiralineas\PsHubspot;

use Configuration;
use PrestaShopLogger;
use Tiralineas\HubspotApi\Client;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Helper Class to deal with AccesTokens
 */
class AccessToken
{
    public static function get()
    {
        if (self::isAccessTokenExpired()) {
            self::refresh();
        }

        return Configuration::get('PS_HUBSPOT_ACCESS_TOKEN', '', '', '');
    }

    public static function create($code)
    {
        return self::getToken('authorization_code', $code);
    }

    public static function refresh()
    {
        return self::getToken('refresh_token');
    }

    private static function getToken($grant_type, $code = null)
    {
        try {
            $client_id = Configuration::get('PS_HUBSPOT_CLIENT_ID', '', '', '');
            $secret_key = Configuration::get('PS_HUBSPOT_LICENSE', '', '', '') ? Configuration::get('PS_HUBSPOT_LICENSE', '', '', '') : Configuration::get('PS_HUBSPOT_LICENSE');
            $refresh_token = null;
            if (is_null($code)) {
                $refresh_token = Configuration::get('PS_HUBSPOT_REFRESH_TOKEN', '', '', '');
            }
            if ($client_id && $secret_key && ($code || $refresh_token)) {
                $client = new Client();
                $response = $client->sendTokenRequest(
                    $grant_type,
                    $client_id,
                    $secret_key,
                    Configuration::get('PS_HUBSPOT_OAUTH_REDIRECT_URL', '', '', ''),
                    $code,
                    $refresh_token
                );

                if (empty($response)) {
                    return false;
                }
                $token = json_decode($response->getBody());
                if (!empty($token->refresh_token) && !empty($token->access_token) && !empty($token->expires_in)) {
                    self::authorize($token);
                    Configuration::updateValue('PS_HUBSPOT_CLIENT_IDS_STORED', true, false, '', '');

                    return true;
                }
            }
        } catch (\Exception $ex) {
            try {
                PrestaShopLogger::addLog($ex->getMessage());
            } catch (\Exception $e) {
            }
        }

        return false;
    }

    /**
     * Authorizes the plugin with given oauth credentials by storing them in the options DB.
     *
     * @param object token
     */
    public static function authorize($token)
    {
        if (!isset($token->access_token) || !isset($token->refresh_token)) {
            return;
        }
        Configuration::updateValue('PS_HUBSPOT_ACCESS_TOKEN', $token->access_token, null, '', '');
        Configuration::updateValue('PS_HUBSPOT_REFRESH_TOKEN', $token->refresh_token, null, '', '');
        Configuration::updateValue('PS_HUBSPOT_TOKEN_EXPIRY', time() + $token->expires_in, null, '', '');
        $metadata = (string) (new Client())->sendGetTokenMetadataRequest($token->access_token)->getBody();
        Configuration::updateValue('PS_HUBSPOT_TOKEN_METADATA', $metadata, null, '', '');
        Configuration::updateValue('PS_HUBSPOT_TOKEN_HUB_ID', json_decode($metadata)->hub_id, null, '', '');
    }

    /**
     * Check if access token is expired.
     *
     * @return bool true/false
     *
     * @since  1.0.0
     */
    private static function isAccessTokenExpired()
    {
        $get_expiry = Configuration::get('PS_HUBSPOT_TOKEN_EXPIRY', '', '', '');
        if ($get_expiry) {
            $current_time = time();
            if (($get_expiry > $current_time) && ($get_expiry - $current_time) <= 50) {
                return true;
            } elseif ($current_time > $get_expiry) {
                return true;
            }
        }

        return false;
    }
}
