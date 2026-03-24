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
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'pshubspot/model/AbstractHsObjectModel.php';

class HsContact extends AbstractHsObjectModel
{
    protected static $objectType = 'CONTACT';
    protected static $psObjectClass = 'Customer';

    protected static $protectedObjectModelKeys = ['secure_key', 'passwd', 'last_passwd_gen'];
    protected static $dateObjectModelKeys = ['date_of_birth', 'newsletter_date_add'];
    protected static $boolObjectModelKeys = ['newsletter', 'active', 'deleted', 'optin'];
    protected static $standardObjectModelKeys = [ // @Todo make this dynamic in future phases, mapping
        'date_of_birth' => 'birthday',
    ];

    public function getPsObjectModel()
    {
        return $this->psObjectModel = new \Customer($this->id, Context::getContext()->shop->id, Context::getContext()->language->id);
    }

    public function build()
    {
        $data = $this->staticObjectModelData();
        $birthday = '';

        if (isset($data['date_of_birth'])) {
            $birthday = $data['date_of_birth'];
        }

        // static::removeUnnecessaryKeys($data, static::$propety_names);
        $data['gender'] = $this->getGenderName();
        $data['language'] = $this->getLanguageName();
        $data['customer_group'] = $this->getGroupName();
        $data['customer_groups'] = $this->getGroups();
        $data['birthday'] = $birthday;

        if (key_exists('company', $data)) {
            $keyValue = $data['company'];
            unset($data['company']);
            $data['hs_company'] = $keyValue;
        }

        return $data;
    }

    private function getGenderName()
    {
        $genders = \Gender::getGenders((int) Context::getContext()->language->id);
        foreach ($genders as $gender) {
            if ($gender->id_gender == $this->psObjectModel->id_gender) {
                return $gender->name;
            }
        }

        return 'N/A';
    }

    private function getLanguageName()
    {
        foreach (\Language::getLanguages() as $language) {
            if ($language['id_lang'] == $this->psObjectModel->id_lang) {
                return $language['name'];
            }
        }
    }

    private function getGroupName()
    {
        foreach (\Group::getGroups((int) Context::getContext()->language->id) as $group) {
            if ($group['id_group'] == $this->psObjectModel->id_default_group) {
                return $group['name'];
            }
        }
    }

    private function getGroups()
    {
        return implode(';', $this->psObjectModel->getGroups());
    }

    public function dontSyncThis($forceSync = false)
    {
        if (parent::dontSyncThis()) {
            return true;
        }
        // Revisar que no pertenezca a un grupo que no haya que sincronizar
        $contact = $this->psObjectModel;
        $contactGroups = $contact->getGroups();
        $customerGroupUnSync = explode(',', Configuration::get('PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC'));
        foreach ($contactGroups as $key => $value) {
            if (in_array($value, $customerGroupUnSync)) {
                return true;
            }
        }
        // Revisar también si su grupo por defecto no hay que sincronizar
        if (in_array($contact->id_default_group, $customerGroupUnSync)) {
            return true;
        }

        return false;
    }

    protected static function applyFilters($sql)
    {
        $sql .= ' AND p.deleted = 0 ';

        $customerGroupUnSync = Configuration::get('PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC');
        if ($customerGroupUnSync) {
            $sql .= ' AND  p.id_customer IN ( SELECT id_customer FROM ' . _DB_PREFIX_ . 'customer_group WHERE id_group NOT IN (' . $customerGroupUnSync . ') ) ';
        }

        if (Configuration::get('PS_HUBSPOT_CONTACT_DATE_FILTER_ENABLED', '', '', '') != 'on') {
            return $sql;
        }

        return $sql . '  AND p.date_add > "' . Configuration::get('PS_HUBSPOT_CONTACT_DATE_FILTER', '', '', '') . '"';
    }

    public static function getMigratedTotal()
    {
        return (int) Configuration::get('PS_HUBSPOT_MIGRATION_CUSTOMERS');
    }

    public static function applyMigrationFilters($sql)
    {
        if (Configuration::get('PS_HUBSPOT_MIGRATION_UPDATED_LEGACY')) {
            $sql .= ' p join ' . _DB_PREFIX_ . static::$definition['table'] . ' t on p.' . static::getObjectClassKey() . '=t.id ';
        }

        $sql .= ' WHERE active = 1 AND date_add < "' . Configuration::get('PS_HUBSPOT_MIGRATION_DATE_END') . '" ';

        if (Configuration::get('PS_HUBSPOT_MIGRATION_UPDATED_LEGACY')) {
            $sql .= ' AND sync_as<>"--" ';
        }

        return $sql;
    }

    protected static function updateMigratedTotal($add)
    {
        Configuration::updateValue('PS_HUBSPOT_MIGRATION_CUSTOMERS', (int) Configuration::get('PS_HUBSPOT_MIGRATION_CUSTOMERS') + (int) $add);
    }

    public static function getExternalObjectIdFromId($id)
    {
        // return static::$objectType . '_' . $id;
        return (new static::$psObjectClass($id))->email;
    }

    public static $definition = [
        'table' => 'tiralineas_hs_contact',
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
}
