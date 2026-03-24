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

use Tiralineas\PsHubspot\AccessToken;
use Tiralineas\PsHubspot\Connection;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'pshubspot/model/AbstractObjectModel.php';

class HsStore extends AbstractObjectModel
{
    public static $definition = [
        'table' => 'tiralineas_hs_store',
        'primary' => 'id',
        'multilang' => false,
        'fields' => [
            'id' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'sync_as' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
            ],
            'sync_at' => [
                'type' => self::TYPE_DATE,
                // 'validate' => 'isDateOrNull'
            ],
            'sync_attempt_at' => [
                'type' => self::TYPE_DATE,
                // 'validate' => 'isDateOrNull'
            ],
        ],
    ];

    public static function getStores()
    {
        // AccessToken::refresh();
        self::init();

        return self::$client->getStoresRequest();
    }

    public static function generateExternalIdfromId($id)
    {
        $shopurls = ShopUrl::getShopUrls($id);
        $shopName = 'Shop_' . $id;

        if (isset($shopurls[0])) {
            $shopName = $shopurls[0]->domain_ssl;
        }

        return $id . '-' . $shopName;
    }

    public function sync($shop = null)
    {
        if (!Connection::isValidClientIdsStored()) {
            return false;
        }
        if (is_null($shop)) {
            $shop = [];
        }
        if (!isset($shop['label'])) {
            $shop['label'] = (new \Shop($this->id))->name;
        }

        if (!isset($shop['ref'])) {
            $shop['ref'] = self::generateExternalIdfromId($this->id);
        } else {
            // $shop['ref'] = Tools::str2url($shop['ref']);
            $shop['ref'] = str_replace(' ', '', trim($shop['ref']));
        }

        if (isset($shop['sourcestore']) && $shop['sourcestore'] != '-1') {
            $shop['ref'] = $shop['sourcestore'];
        }

        $json = self::$client->sendCreateStoreRequest(
            $shop['ref'],
            $shop['label'],
            _PS_BASE_URL_SSL_ . ((__PS_BASE_URI__) ? __PS_BASE_URI__ : '') . DIRECTORY_SEPARATOR . basename(_PS_ADMIN_DIR_) . DIRECTORY_SEPARATOR . 'index.php'
        );

        if (
            !empty($json)
            && isset($json['legacyEcommBridgeInstalled']) && (bool) $json['legacyEcommBridgeInstalled']
            && $json['totalCreatedProperties'] > 0
        ) {
            Configuration::deleteByName('PS_HUBSPOT_MIGRATION_DATE_FROM');
            Configuration::updateValue('PS_HUBSPOT_MIGRATION_MANDATORY', 1);
        }

        if (
            !empty($json) && isset($json['properties']) && count((array) $json['properties'])
        ) {
            $this->sync_as = $shop['ref'];
            $this->sync_at = date('Y-m-d H:i:s');
            $this->save();

            return true;
        }

        return false;
    }
}
