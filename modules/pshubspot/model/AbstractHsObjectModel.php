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

require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsStore.php';

abstract class AbstractHsObjectModel extends AbstractObjectModel
{
    const UNSYNCED_TAG = '--';
    // Let's use an offset to have a newer sync_at date than the date_upd which can be set after the hook
    const UPDATED_TIME_OFFSET = 2 * 60 * 1000;
    protected static $source_table;
    protected static $source_table_id;
    protected static $objectType;

    /**
     * Main prestashop object model for this model
     *
     * @var ObjectModel
     */
    protected $psObjectModel;
    protected static $psObjectClass;
    /**
     * @var array
     */
    public static $propety_names;
    public static $mapped_propety_names = [];
    protected static $protectedObjectModelKeys = [];
    protected static $standardObjectModelKeys = [];
    protected static $dateObjectModelKeys = [];
    protected static $boolObjectModelKeys = [];

    protected static function init()
    {
        parent::init();
        if (is_null(static::$propety_names)) {
            static::$propety_names = array_merge(
                static::$client->getApiPropertyNames(static::$objectType),
                static::$mapped_propety_names
            );
        }
    }

    public static function getObjectClassKey()
    {
        $class = static::$psObjectClass;

        return $class::$definition['primary'];
    }

    protected static function getObjectClassTable()
    {
        $class = static::$psObjectClass;

        return $class::$definition['table'];
    }

    public static function syncCount()
    {
        $sql = 'SELECT count(' . static::getObjectClassKey() . ') FROM ' . _DB_PREFIX_ . static::getObjectClassTable() .
            ' p join ' . _DB_PREFIX_ . static::$definition['table'] . ' t on p.' . static::getObjectClassKey() . '=t.' . static::$definition['primary'] . ' ' .
            'WHERE t.sync_at>=p.date_upd and sync_as!="' . self::UNSYNCED_TAG . '"';
        $sql = static::applyFilters($sql);

        return DB::getInstance()->getValue($sql);
    }

    public static function unsyncCount()
    {
        $sql = 'SELECT count(' . static::getObjectClassKey() . ') FROM ' . _DB_PREFIX_ . static::getObjectClassTable() .
            ' p left join ' . _DB_PREFIX_ . static::$definition['table'] . ' t on p.' . static::getObjectClassKey() . '=t.' . static::$definition['primary'] . ' ' .
            'WHERE (t.id is null or t.sync_at<p.date_upd or t.sync_as="' . self::UNSYNCED_TAG . '")';
        $sql = static::applyFilters($sql);

        return DB::getInstance()->getValue($sql);
    }

    protected static function applyFilters($sql)
    {
        return $sql;
    }

    public static function getUnsync($limit = false)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . static::getObjectClassTable() .
            ' p left join ' . _DB_PREFIX_ . static::$definition['table'] . ' t on p.' . static::getObjectClassKey() . '=t.' . static::$definition['primary'] . ' ' .
            'WHERE (t.id is null or t.sync_at<p.date_upd or t.sync_as="' . self::UNSYNCED_TAG . '")';
        $sql = static::applyFilters($sql);
        $sql .= ' ORDER BY sync_attempt_at ASC';
        if ($limit && $limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }

        return DB::getInstance()->ExecuteS($sql);
    }

    public static function getPrepareForMigration($limitFrom = 0, $limit = false)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . static::getObjectClassTable();
        $sql = static::applyMigrationFilters($sql);
        $sql .= ' ORDER BY ' . static::getObjectClassKey() . ' ASC';
        if ($limit && $limit > 0) {
            $sql .= ' LIMIT ' . $limitFrom . ',' . (int) $limit;
        }

        return DB::getInstance()->ExecuteS($sql);
    }

    public static function applyMigrationFilters($sql)
    {
        $date = date('Y-m-d');
        if (Configuration::get('PS_HUBSPOT_MIGRATION_DATE_END')) {
            $date = Configuration::get('PS_HUBSPOT_MIGRATION_DATE_END');
        }

        return $sql . '  WHERE date_add < "' . $date . '" ';
    }

    public static function getMigratedTotal()
    {
        return 0;
    }

    public static function getTotalToMigrate()
    {
        return count(static::getPrepareForMigration()) - static::getMigratedTotal();
    }

    protected static function updateMigratedTotal($add)
    {
    }

    public static function dryRunSync($id = false)
    {
        (new static($id))->printSyncJson();
    }

    // Pequeño cambio para que las migraciones masivas sean más rápidas, pero perdemos el feedback correcto de sync.
    // esto es, ya no esperamos a que devuelva "true" para marcar como sincronizado
    public static function syncChunk($limit = 1, $chunkSize = 5)
    {
        $results = static::getUnsync($limit);
        $chunks = array_chunk($results, $chunkSize);
        array_walk(
            $chunks,
            function ($itemList) {
                $id = self::getObjectClassKey(true);
                $messageList = [];
                foreach ($itemList as $item) {
                    $object = new static($item[$id]);
                    $object->psObjectModel = $object->getPsObjectModel();

                    // Marcamos como intentando Sync
                    $object->sync_attempt_at = date('Y-m-d H:i:s');
                    $object->sync_as = self::UNSYNCED_TAG;
                    $object->save();

                    $messageArray = $object->buildMessages();

                    $ignore = false;
                    // TODO: Migrate this type of IF to the model
                    if (in_array(static::$objectType, ['DEAL'])) {
                        $stage = $object->getHsCurrentState();
                        if ($stage->sync_as == HsPipeline::DO_NOT_SYNC) {
                            $ignore = true;
                        }
                    }
                    if ($object->dontSyncThis() == true) {
                        $ignore = true;
                    }

                    if (!$ignore && count($messageArray) > 0) {
                        $messageList[] = current($messageArray);
                    }
                }

                $synced = static::$client->upsertMessages(
                    (new \HsStore(Context::getContext()->shop->id))->sync_as,
                    static::$objectType,
                    $messageList
                );
                if (!$synced) {
                    return false;
                }

                usleep(1000);

                foreach ($itemList as $item) {
                    $object = new static($item[$id]);
                    $object->psObjectModel = $object->getPsObjectModel();
                    $object->sync_as = $object->getExternalObjectId();
                    $object->sync_at = date('Y-m-d H:i:s', time() + 1); // $this->getLastSyncDate();

                    // TODO: Migrate this type of IF to the model
                    if (in_array(static::$objectType, ['DEAL']) && $object->dontSyncThis() != true) {
                        $stage = $object->getHsCurrentState();
                        if ($stage->sync_as != HsPipeline::DO_NOT_SYNC) {
                            $object->syncLineItems();
                        }
                    }

                    $object->save();
                }
            }
        );

        return $limit > count($results);
    }

    public static function migrateItems($limit = 1, $chunkSize = 5)
    {
        if (in_array(static::$objectType, ['DEALABANDONED'])) {
            $chunkSize = 4; // To prevent max JSON size
        }
        $results = static::getPrepareForMigration(static::getMigratedTotal(), $limit);
        $chunks = array_chunk($results, $chunkSize);
        array_walk(
            $chunks,
            function ($itemList) {
                $messageList = [];
                $id = self::getObjectClassKey(true);

                foreach ($itemList as $item) {
                    $object = new static($item[$id]);
                    $object->psObjectModel = $object->getPsObjectModel();
                    $messageArray = $object->buildMessages();
                    if (count($messageArray) > 0) {
                        $messageList[] = current($messageArray);
                    }
                }

                static::$client->migrationMessages(
                    (new \HsStore(Context::getContext()->shop->id))->sync_as,
                    static::$objectType,
                    $messageList
                );

                if (in_array(static::$objectType, ['DEAL'])) {
                    foreach ($itemList as $item) {
                        $object = new static($item[$id]);
                        $object->psObjectModel = $object->getPsObjectModel();
                        $object->syncLineItems(true);
                    }
                }
            }
        );
        if (!empty($results)) {
            static::updateMigratedTotal(count($results));
        }

        return $limit > count($results);
    }

    public static function migrateProperties()
    {
        $object = new static();
        static::$client->migrateProperties(
            (new \HsStore(Context::getContext()->shop->id))->sync_as
        );

        return true;
    }

    public static function setToReSyncIds(array $ids)
    {
        $where = ' WHERE ' . static::$definition['primary'] . ' in ( \'' .
            implode("','", $ids) .
            '\' )';

        return self::setToReSync(-1, $where);
    }

    public static function setToReSync($limit = -1, $where = '')
    {
        $sql = 'UPDATE ' . _DB_PREFIX_ . static::$definition['table'] . ' set sync_at="1970-01-01" ' .
            $where .
            ($limit > 0 ? ' LIMIT ' . (int) $limit : '');

        return DB::getInstance()->Execute($sql);
    }

    public static function setToReSyncFrom($from = '')
    {
        $where = " WHERE sync_at > '" . $from . "' ";
        self::setToReSync(-1, $where);
    }

    /**
     * @return ObjectModel
     */
    abstract public function getPsObjectModel();

    public function dontSyncThis($forceSync = false)
    {
        $store = (new \HsStore(Context::getContext()->shop->id));
        if (is_null($store->sync_as)) {
            return true;
        }

        return false;
    }

    public function sync($forceSync = false)
    {
        $this->psObjectModel = $this->getPsObjectModel();
        $externalObjectId = $this->getExternalObjectId();

        if ($this->dontSyncThis($forceSync) == true) {
            return false;
        }

        // Al menos 4 segundos entre actualizaciones!
        if (strtotime($this->sync_attempt_at) + 4 > time()) {
            return true;
        }

        $this->sync_attempt_at = date('Y-m-d H:i:s');
        $this->sync_as = self::UNSYNCED_TAG;
        $this->save();
        if (static::$client->upsertMessages(
            (new \HsStore(Context::getContext()->shop->id))->sync_as,
            static::$objectType,
            $this->buildMessages('UPSERT')
        )
        ) {
            $this->sync_as = $externalObjectId;
            $this->sync_at = date('Y-m-d H:i:s'); // $this->getLastSyncDate();
            $this->save();

            return true;
        }
    }

    public function printSyncJson()
    {
        $this->psObjectModel = $this->getPsObjectModel();
        $payload = [
            'storeId' => (new \HsStore(Context::getContext()->shop->id))->sync_as,
            'objectType' => static::$objectType,
            'messages' => $this->buildMessages('UPSERT'),
        ];
        echo json_encode($payload);
    }

    protected function getLastSyncDate()
    {
        $retries_left = 20;
        $status = null;
        while (!$status && $retries_left > 0) {
            $status = static::$client->checkSyncStatus(
                (new \HsStore(Context::getContext()->shop->id))->sync_as,
                static::$objectType,
                $this->getExternalObjectId()
            );
            --$retries_left;
        }
        $hasErrors = !$status ||
            (
                count($status->errors) > 0 &&
                $status->errors[0]->erroredAt >= $status->lastProcessedAt
            );
        if ($hasErrors) {
            \PrestaShopLogger::addLog(
                $status ? $status->errors[0]->details : 'No status response',
                3,
                null,
                'Hubspot_Message',
                null,
                true
            );
        }

        return $hasErrors ?
            $this->sync_at :
            date('Y-m-d H:i:s', round(($status->lastProcessedAt + self::UPDATED_TIME_OFFSET) / 1000));
    }

    public function buildMessages($action = 'UPSERT')
    {
        $messages = [];
        $properties = $this->build();
        $message = [
            'action' => $action,
            'externalObjectId' => static::getExternalObjectIdFromId($this->psObjectModel->id),
            'properties' => $properties,
        ];
        // if (isset($this->psObjectModel->date_upd)) {
        //     $message["changedAt"] = strtotime($this->psObjectModel->date_upd)."000";
        // }
        $messages[] = $message;

        return $messages;
    }

    abstract public function build();

    protected function staticObjectModelData()
    {
        $data = $this->psObjectModel->validateFields($die = false) ? $this->psObjectModel->getFields() : [];
        self::removeProtectedKeys($data, static::$protectedObjectModelKeys);
        self::translateKeys($data, static::$standardObjectModelKeys, $this->psObjectModel);
        self::convertDatetimeFields($data, static::$dateObjectModelKeys);
        self::convertBooleanFields($data, static::$boolObjectModelKeys);

        return $data;
    }

    protected function getExternalObjectId()
    {
        return static::getExternalObjectIdFromId($this->id);
    }

    public static function getExternalObjectIdFromId($id)
    {
        return static::$objectType . '_' . $id;
    }
}
