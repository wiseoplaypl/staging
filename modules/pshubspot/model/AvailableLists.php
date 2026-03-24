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

use Tiralineas\PsHubspot\Connection;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'pshubspot/model/AbstractObjectModel.php';

class AvailableLists extends AbstractObjectModel
{
    // defining one variable for db column, name should be column name
    public $object_type;
    public $name;
    public $dynamic;
    public $filters;

    public static $definition = [
        'table' => 'tiralineas_available_lists',
        'primary' => 'id',
        'multilang' => false,
        'fields' => [
            'id' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'object_type' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
            ],
            'name' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
            ],
            'dynamic' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isBool',
                'required' => true,
            ],
            'filters' => [
                'type' => self::TYPE_STRING,
                'required' => true,
            ],
            'sync_at' => [
                'type' => self::TYPE_DATE,
                // 'validate' => 'isDateOrNull'
            ],
        ],
    ];

    protected static $neccessaryKeys = ['name', 'dynamic', 'filters'];

    public function sync()
    {
        if (!Connection::isValidClientIdsStored()) {
            return false;
        }
        $hs_list = (array) $this;
        self::removeUnnecessaryKeys($hs_list, self::$neccessaryKeys);
        $hs_list['dynamic'] = (bool) $this->dynamic;
        $hs_list['filters'] = json_decode($this->filters);
        $hs_list['portalId'] = \Configuration::get('PS_HUBSPOT_TOKEN_HUB_ID', '', '', '');
        if (self::$client->sendCreateListRequest($hs_list)
        ) {
            $this->sync_at = date('Y-m-d H:i:s');
            $this->save();

            return true;
        }

        return false;
    }

    public static function getLists($where = false)
    {
        $sql = 'SELECT `id`,`object_type`, `name`,`dynamic`,`filters`,`sync_at` FROM ' . _DB_PREFIX_ . self::$definition['table'];
        if ($where) {
            $sql .= ' ' . $where;
        }

        return DB::getInstance()->ExecuteS($sql);
    }

    public static function getSync($limit = false)
    {
        $where = 'WHERE sync_at is not null';
        if ($limit) {
            $where .= ' LIMIT ' . (int) $limit;
        }

        return self::getLists($where);
    }

    public static function getUnsync($limit = false)
    {
        $where = 'WHERE sync_at is null';
        if ($limit) {
            $where .= ' LIMIT ' . (int) $limit;
        }

        return self::getLists($where);
    }
}
