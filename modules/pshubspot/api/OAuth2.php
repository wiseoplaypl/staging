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

namespace Tiralineas\HubspotApi;

use Tiralineas\HubspotApi\Client;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OAuth2
{
    const AUTHORIZE_PATH = '/api/authorize';

    /**
     * Initiate an Integration with OAuth 2.0.
     *
     * @param string $clientId the Client ID of your app
     * @param string $redirectURI The URL that you want the visitor redirected to after granting access to your app. For security reasons, this URL must use https.
     * @param array $scopesArray a set of scopes that your app will need access to
     * @param array $optionalScopesArray a set of optional scopes that your app will need access to
     *
     * @return string
     */
    public static function getAuthUrl($clientId, $redirectURI, array $scopesArray = [], array $optionalScopesArray = [])
    {
        return Client::TLAPI_BASE_URL . self::AUTHORIZE_PATH . '?' .
            http_build_query(
                [
                    'client_id' => $clientId,
                    'redirect_uri' => $redirectURI,
                    'scope' => implode(' ', $scopesArray),
                    'optional_scope' => implode(' ', $optionalScopesArray),
                ],
                '',
                '&',
                PHP_QUERY_RFC3986
            );
    }
}
