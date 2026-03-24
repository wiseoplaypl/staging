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

class AvailableGroups extends AbstractObjectModel
{
    // defining one variable for db column, name should be column name
    public $object_type;
    public $name;
    public $label;
    public $filters;

    public static $definition = [
        'table' => 'tiralineas_available_groups',
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
            'label' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
            ],
            'sync_at' => [
                'type' => self::TYPE_DATE,
                // 'validate' => 'isDateOrNull'
            ],
        ],
    ];

    protected static $neccessaryKeys = ['name', 'label'];

    public function sync($object_type, $group)
    {
        if (!Connection::isValidClientIdsStored()) {
            return false;
        }
        $hs_group = $group;
        self::removeUnnecessaryKeys($hs_group, self::$neccessaryKeys);
        if (self::$client->sendCreatePropertyGroupRequest($object_type, $hs_group)
        ) {
            $this->sync_as = $group['name'];
            $this->sync_at = date('Y-m-d H:i:s');
            $this->save();

            return true;
        }

        return false;
    }

    public static function getUnsync($object_type = null)
    {
        $sql = 'SELECT `id`,`object_type`, `name`,`label` FROM ' . _DB_PREFIX_ . self::$definition['table'] .
            ' WHERE sync_at is null';
        if ($object_type) {
            $sql .= ' AND object_type = "' . $object_type . '"';
        }

        return DB::getInstance()->ExecuteS($sql);
    }
}
