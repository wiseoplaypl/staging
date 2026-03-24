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

class AvailableProperties extends AbstractObjectModel
{
    // defining one variable for db column, name should be column name
    public $object_type;
    public $name;
    public $groupName;
    public $label;
    public $type;
    public $fieldType;
    public $formField;
    public $options_callback;
    public $value_callback;

    public static $definition = [
        'table' => 'tiralineas_available_properties',
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
            'groupName' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => false,
            ],
            'label' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
            ],
            'type' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
            ],
            'fieldType' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
            ],
            'formField' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isBool',
                'required' => true,
            ],
            'options_callback' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => false,
            ],
            'value_callback' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => false,
            ],
            'sync_at' => [
                'type' => self::TYPE_DATE,
                // 'validate' => 'isDateOrNull'
            ],
        ],
    ];

    protected static $neccessaryKeys = ['name', 'groupName', 'options', 'label', 'type', 'fieldType', 'formField'];

    public function sync()
    {
        if (!Connection::isValidClientIdsStored()) {
            return false;
        }
        if ($this->options_callback) {
            $this->options = call_user_func(['AvailableProperties', $this->options_callback]) ?: [];
        }
        $hs_property = (array) $this;
        self::removeUnnecessaryKeys($hs_property, self::$neccessaryKeys);
        if (empty($_SESSION['getApiPropertyNames'][$this->object_type])) {
            $hsPropertyList = self::$client->getApiPropertyNames($this->object_type);
            $_SESSION['getApiPropertyNames'][$this->object_type] = $hsPropertyList;
        } else {
            $hsPropertyList = $_SESSION['getApiPropertyNames'][$this->object_type];
        }
        $hs_property['formField'] = (bool) $hs_property['formField'];
        if (
            array_key_exists($hs_property['name'], array_flip($hsPropertyList))
            || self::$client->sendCreatePropertyRequest($this->object_type, $hs_property)
        ) {
            $this->sync_at = date('Y-m-d H:i:s');
            $this->save();

            return true;
        }

        return false;
    }

    public static function getGroups($object_type = null)
    {
        $sql = 'SELECT DISTINCT group_name FROM ' . _DB_PREFIX_ . self::$definition['table'];
        if ($object_type) {
            $sql .= ' WHERE object_type = "' . $object_type . '"';
        }

        return DB::getInstance()->ExecuteS($sql);
    }

    public static function getProperties($where = false)
    {
        $sql = 'SELECT `id`,`object_type`, `name`,`groupName`,`label`,`type`,`fieldType`,`formField`,`options_callback`,`sync_at` FROM ' . _DB_PREFIX_ . self::$definition['table'];
        if ($where) {
            $sql .= ' ' . $where;
        }

        return DB::getInstance()->ExecuteS($sql);
    }

    public static function getSync($object_type = null)
    {
        $where = ' WHERE sync_at is not null';
        if ($object_type) {
            $where .= ' AND object_type = "' . $object_type . '"';
        }

        return self::getProperties($where);
    }

    public static function getUnsync($object_type = null)
    {
        $where = ' WHERE sync_at is null';
        if ($object_type) {
            $where .= ' AND object_type = "' . $object_type . '"';
        }

        return self::getProperties($where);
    }

    public static function getCustomerGroups()
    {
        $groups = \Group::getGroups((int) Context::getContext()->language->id);

        return array_map(
            function ($group) {
                return [
                    'label' => $group['name'],
                    'value' => $group['id_group'],
                ];
            },
            $groups
        );
    }

    public static function getOrderStatuses()
    {
        $statuses = OrderState::getOrderStates((int) Context::getContext()->language->id);

        return array_map(
            function ($status) {
                return [
                    'label' => $status['name'],
                    'value' => $status['id_order_state'],
                ];
            },
            $statuses
        );
    }

    public static function getYesNoOption()
    {
        return [
            ['label' => 'Yes', 'value' => 'yes'],
            ['label' => 'No', 'value' => 'no'],
        ];
    }

    public static function getUserMarketingAction()
    {
        return [
            ['label' => 'Yes', 'value' => 'yes'],
            ['label' => 'No', 'value' => 'no'],
        ];
    }

    public static function getUserMarketingSources()
    {
        return [
            ['label' => 'Checkout', 'value' => 'checkout'],
            ['label' => 'Registration', 'value' => 'registration'],
            ['label' => 'Others', 'value' => 'others'],
        ];
    }

    public static function getRfmRating()
    {
        return [
            ['label' => '1', 'value' => 1],
            ['label' => '2', 'value' => 2],
            ['label' => '3', 'value' => 3],
            ['label' => '4', 'value' => 4],
            ['label' => '5', 'value' => 5],
        ];
    }

    public static function hsNewOrderStatus()
    {
        return [
            ['label' => 'Yes', 'value' => 'yes'],
            ['label' => 'No', 'value' => 'no'],
        ];
    }

    public static function hsCampaignConversionOptions()
    {
        return [
            ['label' => 'Yes', 'value' => 'yes'],
            ['label' => 'No', 'value' => 'no'],
        ];
    }

    public static function getAbandonedCartStatus()
    {
        return [
            ['label' => 'Yes', 'value' => 'yes'],
            ['label' => 'No', 'value' => 'no'],
        ];
    }

    public static function getAllCampaignNames()
    {
        return [
            [
                'label' => 'MQL Nurture & Conversion',
                'value' => 'MQL Nurture & Conversion',
            ],
            [
                'label' => 'New Customer Welcome & Get a 2nd Order',
                'value' => 'New Customer Welcome & Get a 2nd Order',
            ],
            [
                'label' => '2nd Order Thank You & Get a 3rd Order',
                'value' => '2nd Order Thank You & Get a 3rd Order',
            ],
            [
                'label' => '3rd Order Thank You',
                'value' => '3rd Order Thank You',
            ],
            [
                'label' => 'Customer Reengagement',
                'value' => 'Customer Reengagement',
            ],
            [
                'label' => 'Customer Rewards',
                'value' => 'Customer Rewards',
            ],
            [
                'label' => 'Abandoned Cart Recovery',
                'value' => 'Abandoned Cart Recovery',
            ],
            [
                'label' => 'None',
                'value' => 'None',
            ],
        ];
    }
}
