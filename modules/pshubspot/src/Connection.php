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
use Tiralineas\HubspotApi\OAuth2;
use Tools;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Helper Class to deal with the Connection
 */
class Connection
{
    public static function getConnectAccountLink()
    {
        $url = str_replace('http://', 'https://', Navigator::getDashboardUrl(true));
        \Configuration::updateValue(
            'PS_HUBSPOT_OAUTH_REDIRECT_URL',
            $url,
            false,
            '',
            ''
        );

        return OAuth2::getAuthUrl(
            Configuration::get('PS_HUBSPOT_CLIENT_ID', '', '', ''),
            Configuration::get('PS_HUBSPOT_OAUTH_REDIRECT_URL', '', '', ''),
            ['oauth', 'contacts'],
            ['automation', 'files', 'timeline', 'content', 'forms', 'integration-sync', 'e-commerce']
        // ['automation', 'files', 'timeline', 'content', 'forms', 'e-commerce']
        );
    }

    public static function getCreateAccountLink()
    {
        $url = 'https://app.hubspot.com/signup-v2/crm/step/user-info';
        $params = [
            'utm_medium' => 'integration',
            'utm_source' => 'synchrosuites',
            'utm_campaign' => 'prestashop-integrations',
        ];

        return $url . '?' . http_build_query($params);
    }

    public static function getReconnectAccountLink()
    {
        return Navigator::getSetupUrl(-1);
    }

    /**
     * Check if valid hubspot client Ids is stored.
     *
     * @return bool true/false
     *
     * @since  1.0.0
     */
    public static function fetchAccessTokenFromCode()
    {
        return Tools::getValue('code') &&
            AccessToken::create(Tools::getValue('code'));
    }

    /**
     * Check if valid hubspot client Ids is stored.
     *
     * @return bool true/false
     *
     * @since  1.0.0
     */
    public static function isValidClientIdsStored()
    {
        $hapikey = Configuration::get('PS_HUBSPOT_CLIENT_ID', '', '', '');
        $hseckey = Configuration::get('PS_HUBSPOT_LICENSE', '', '', '');
        if ($hapikey && $hseckey) {
            return Configuration::get('PS_HUBSPOT_CLIENT_IDS_STORED', '', '', '');
        }

        return false;
    }
}
