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
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsContact.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsProduct.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsPipeline.php';

class HsDeal extends AbstractHsObjectModel
{
    protected static $objectType = 'DEAL';
    protected static $psObjectClass = 'Order';
    protected static $psObjectClassKey = 'id_cart';
    protected static $standardObjectModelKeys = [
        'amount' => 'total_paid',
        'closedate' => 'date_add',
    ];
    public $order_id = '';
    protected static $dateObjectModelKeys = ['closedate', 'delivery_date', 'invoice_date'];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->order_id = $id;
        parent::__construct((new \Order($id))->id_cart, $id_lang, $id_shop);
        $this->order_id = $id;
    }

    public function getPsObjectModel()
    {
        return $this->psObjectModel = new \Order($this->order_id);
    }

    public static function getObjectClassKey($isSync = false)
    {
        if ($isSync) {
            return 'id_order';
        }

        return 'id_cart';
    }

    public function buildMessages($action = 'UPSERT')
    {
        $messages = parent::buildMessages($action);

        $messages[0]['externalObjectId'] = static::getExternalObjectIdFromId($this->psObjectModel->id_cart);

        return array_map(
            function ($message) {
                $message['associations'] = [
                    'CONTACT' => [
                        isset($message['properties']['id_customer']) ? HsContact::getExternalObjectIdFromId($message['properties']['id_customer']) : '', // Todo: enviar email!
                    ],
                ];

                $store = new \HsStore(isset($message['properties']['id_shop']) ? $message['properties']['id_shop'] : 1);
                if (!empty($store)) {
                    $message['associations']['STORE'] = [$store->sync_as];
                }

                return $message;
            },
            $messages
        );
    }

    public function getHsCurrentState()
    {
        return new HsPipeline($this->psObjectModel->current_state);
    }

    public function build()
    {
        $data = $this->staticObjectModelData();
        $data['name'] = $this->psObjectModel->id_cart . '/' . $this->psObjectModel->reference;
        $data['order_number'] = $this->psObjectModel->id;
        $data['stage'] = $this->getHsCurrentState()->sync_as;
        $data['id_shop'] = $this->psObjectModel->id_shop;
        $data['pipeline'] = \Configuration::get('PS_HUBSPOT_DEAL_PIPELINE_ID', '', '', '');
        $data['order_status'] = $this->getOrderStatusName();
        $discounts = array_map(
            function ($discount) {
                $return = $discount['name'];
                if (!empty($discount['id_cart_rule'])) {
                    $discount_data = new CartRule($discount['id_cart_rule']);
                    if (!empty($discount_data) && !empty($discount_data->code)) {
                        $return .= ' (' . $discount_data->code . ')';
                    }
                }

                return $return;
            },
            $this->psObjectModel->getCartRules()
        );
        $data['coupon_code'] = implode(',', $discounts);
        if (version_compare(_PS_VERSION_, '8.0.0', '>=') && !key_exists('shipping_number', $data)) {
            $data['shipping_number'] = $this->psObjectModel->getShippingNumber();
        }
        $data += $this->address('billing_', new Address((int) $this->psObjectModel->id_address_invoice));
        $data += $this->address('shipping_', new Address((int) $this->psObjectModel->id_address_delivery));

        return $data;
    }

    public function dontSyncThis($forceSync = false)
    {
        if (parent::dontSyncThis()) {
            return true;
        }

        if (!isset($this->psObjectModel->current_state) || $this->psObjectModel->current_state == 0) {
            return true;
        }

        if (!$this->psObjectModel->id_cart) {
            return true;
        }

        $stage = (new HsPipeline($this->psObjectModel->current_state));
        if ($stage->sync_as == HsPipeline::DO_NOT_SYNC) {
            return true;
        }
    }

    public function sync($forceSync = false)
    {
        // Esto no tendría que estar encima del parent:sync?
        $this->psObjectModel = $this->getPsObjectModel();
        $stage = (new HsPipeline($this->psObjectModel->current_state));
        if ($stage->sync_as == HsPipeline::DO_NOT_SYNC) {
            return false;
        }
        $sync_attempt_at = $this->sync_attempt_at;
        if (!parent::sync()) {
            return false;
        }

        if (strtotime($sync_attempt_at) + 4 > time()) {
            return true;
        }

        // Lets create line items for order
        $this->syncLineItems();

        return true;
    }

    public function syncLineItems($is_migration = false)
    {
        $order = $this->psObjectModel;
        $store = new HsStore(isset($order->id_shop) ? $order->id_shop : 1);
        // $id_lang = $order->getCustomer()->id_lang;
        $product_array = [];
        $link = new \Link();
        foreach ($order->getProducts() as $item) {
            // Así hacíamos las cosas en el año 2000 (o_ _)ﾉ
            $product_price_wt = (round($item['product_price_wt'] * 100)) / 100;
            $product_price = (round($item['price'] * 100)) / 100;

            $manufacturerName = '';

            try {
                $manufacturer = new ManufacturerCore((int) $item['id_manufacturer']);
                if (isset($manufacturer) && !empty($manufacturer)) {
                    $manufacturerName = isset($manufacturer->name) ? $manufacturer->name : '';
                }
            } catch (\Exception $e) {
            }

            $imageLink = '';
            if (!empty($item['product_id']) && $item['product_id'] > 0) {
                $imageLink = $this->getLineItemImageURL($item['product_id'], $item['product_attribute_id']);
            }

            $lineItemId = $order->id_cart . '_' . $item['product_id'] . '_' . $item['product_attribute_id'];
            if (!empty($item['customization_id'])) {
                $lineItemId .= '_' . $item['customization_id'];
            }

            $productMsgData = [
                'action' => 'UPSERT',
                'externalObjectId' => 'CART_ITEM_' . $lineItemId,
                'properties' => [
                    'name' => $item['product_name'], // $product->name,
                    'sku' => !empty($item['reference']) ? strip_tags($item['reference']) . '/' . $item['product_id'] : 'undefined',
                    'product_quantity' => $item['product_quantity'],
                    'tax_amount' => number_format($product_price_wt - $product_price, 2, '.', ''),
                    'product_price' => number_format($product_price_wt, 2, '.', ''),
                    'id_cart_product' => $lineItemId,
                    'reduction_amount_tax_incl' => $item['reduction_amount_tax_incl'],
                    'manufacturer' => $manufacturerName,
                    'image_url' => $imageLink,
                    'url' => !empty($item['product_id']) ? $link->getProductLink($item['product_id']) : '',
                    'date_upd' => date('Y-m-d H:i:s'),
                ],
                'associations' => [
                    'PRODUCT' => [HsProduct::getExternalObjectIdFromId($item['product_id'])],
                    'DEAL' => [$this->sync_as],
                ],
            ];
            if ($item['id_category_default']) {
                $productCategory = new Category($item['id_category_default']);
                if (!empty($productCategory)) {
                    $productMsgData['properties']['main_category'] = $productCategory->getName();
                }
            }
            if ($item['id_product']) {
                $productMsgData['properties']['categories'] = $this->ps_getProductCategories($item['id_product']);
            }
            if (!empty($store)) {
                $productMsgData['associations']['STORE'] = [$store->sync_as];
            }
            $product_array[] = $productMsgData;
        }

        $actionMessages = 'upsertMessages';
        if ($is_migration) {
            $actionMessages = 'migrationMessages';
        }
        foreach (array_chunk($product_array, 60) as $prdct_array) {
            static::$client->$actionMessages(
                (new \HsStore(Context::getContext()->shop->id))->sync_as,
                'LINE_ITEM',
                $prdct_array
            );
        }
    }

    private function ps_getProductCategories($id_product, $separator = ',')
    {
        $productCategories = \Product::getProductCategoriesFull($id_product);

        if (is_array($productCategories) && !empty($productCategories)) {
            $nameFromCategories = array_column($productCategories, 'name');
            if (!empty($nameFromCategories)) {
                return implode($separator, $nameFromCategories);
            }
        }

        return '';
    }

    private function getLineItemImageURL($id_product, $product_attribute_id)
    {
        $formatNameFunction = is_callable("\ImageType", 'getFormattedName') ? 'getFormattedName' : 'getFormatedName';

        $product = new Product((int) $id_product);
        $img = \Product::getCover((int) $id_product);

        $imageRewrite = is_array($product->link_rewrite) ? $product->link_rewrite[1] : $product->link_rewrite;
        $productCombinationImageInfo = Product::getCombinationImageById((int) $product_attribute_id, 1);
        $id_image = $productCombinationImageInfo ? $productCombinationImageInfo['id_image'] : $img['id_image'];

        $imageLink = '';
        if ($id_product > 0 && $id_image > 0) {
            $imageLink = Context::getContext()->link->getImageLink($imageRewrite, $id_product . '-' . $id_image, \ImageType::$formatNameFunction('medium'));
        }

        return $imageLink;
    }

    public static function setToReSyncFrom($from = '')
    {
        $where = " WHERE sync_at > '" . $from . "' ";
        self::setToReSync(-1, $where);
    }

    public static function setToReSync($limit = -1, $where = '')
    {
        if (!empty($where)) {
            $where .= ' AND ';
        } else {
            $where = ' WHERE ';
        }
        $where .= ' sync_as IN (' .
            "SELECT CONCAT('DEAL_',id_cart) as syncDealName FROM  " . _DB_PREFIX_ . \Order::$definition['table'] .
            ') ';
        parent::setToReSync($limit, $where);
    }

    public static function getExternalObjectIdFromId($id)
    {
        $order = new \Order(
            self::getOrderByCartId($id),
            Context::getContext()->shop->id,
            Context::getContext()->language->id
        );

        return static::$objectType . '_' . $order->id_cart;
    }

    /*
        La función original Order::getOrderByCartId que se usaba en getExternalObjectIdFromId() introduce el contexto de multitienda
        en la SQL, dando pie a que seleccione el Id_shop incorrecto y no encuentre el Id Order, devolviendo el valor 'DEAL_'.
        Para evitar eso, se crea esta función con la misma funcionalidad, quitando el tema de multitienda.
    */
    private static function getOrderByCartId($id)
    {
        $sql = 'SELECT `id_order`
            FROM `' . _DB_PREFIX_ . 'orders`
            WHERE `id_cart` = ' . (int) $id;

        $result = Db::getInstance()->getValue($sql);

        return !empty($result) ? (int) $result : false;
    }

    private function getOrderStatusName()
    {
        $states = OrderState::getOrderStates((int) Context::getContext()->language->id);
        foreach ($states as $state) {
            if ($state['id_order_state'] == $this->psObjectModel->current_state) {
                return $state['name'];
            }
        }
    }

    protected static function applyFilters($sql)
    {
        // $sql .= ' ' . _DB_PREFIX_ . static::$definition['table'] . '.deleted <> 1 ';
        // Tener en cuenta estados que no se sincronizan
        $sql .= ' AND p.current_state NOT IN (SELECT id FROM ' . _DB_PREFIX_ . 'tiralineas_hs_pipeline WHERE sync_as = "-1") ';
        $sql .= ' AND p.id_cart != 0 ';
        $sql .= ' AND p.current_state IN ( SELECT id_order_state FROM ' . _DB_PREFIX_ . 'order_state ) ';

        if (Configuration::get('PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED', '', '', '') != 'on') {
            return $sql;
        }

        return $sql . '  AND p.date_add > "' . Configuration::get('PS_HUBSPOT_DEAL_DATE_FILTER', '', '', '') . '"';
    }

    protected static $standardAddressKeys = [
        'given_names' => 'firstname',
        'surnames' => 'lastname',
        'company' => 'company',
        'address_line_1' => 'address1',
        'address_line_2' => 'address2',
        'postal_code' => 'postcode',
        'city' => 'city',
        'phone' => 'phone',
        'mobile_phone' => 'phone_mobile',
        'extra' => 'other',
        'vat_number' => 'vat_number',
    ];

    protected static function address($prefix, $address)
    {
        $data = $address->validateFields($die = false) ? $address->getFields() : [];
        static::translateKeys($data, self::$standardAddressKeys, $address);
        $data['country'] = '-';
        $data['state'] = '-';
        if (!empty($address->id_country)) {
            $country = new \Country((int) $address->id_country);
            $data['country'] = $country->iso_code ? $country->iso_code : '';
        }
        if (!empty($address->id_state)) {
            $state = \State::getNameById((int) $address->id_state);
            $data['state'] = $state ? $state : '';
        }
        if (!isset($data['dni']) || !$data['dni']) {
            $data['dni'] = $data['vat_number'];
        }

        return self::appenPrefixToKey($data, $prefix);
    }

    protected static function appenPrefixToKey($data, $prefix)
    {
        $ret = [];
        foreach ($data as $key => $value) {
            $ret[$prefix . $key] = $value;
        }

        return $ret;
    }

    public static function getMigratedTotal()
    {
        return (int) Configuration::get('PS_HUBSPOT_MIGRATION_ORDERS');
    }

    public static function applyMigrationFilters($sql)
    {
        $dateIni = date('2000-01-01');
        if (Configuration::get('PS_HUBSPOT_MIGRATION_DATE_FROM')) {
            $dateIni = Configuration::get('PS_HUBSPOT_MIGRATION_DATE_FROM');
        }

        $dateEnd = date('Y-m-d');
        if (Configuration::get('PS_HUBSPOT_MIGRATION_DATE_END')) {
            $dateEnd = Configuration::get('PS_HUBSPOT_MIGRATION_DATE_END');
        }

        if (Configuration::get('PS_HUBSPOT_MIGRATION_UPDATED_LEGACY')) {
            $sql .= ' p left join ' . _DB_PREFIX_ . static::$definition['table'] . ' t on p.' . static::getObjectClassKey() . '=t.' . static::$definition['primary'] . ' ';
        }

        $sql .= ' WHERE date_add >= "' . $dateIni . '" AND date_add <= "' . $dateEnd . '" ';

        if (Configuration::get('PS_HUBSPOT_MIGRATION_UPDATED_LEGACY')) {
            $sql .= ' AND sync_as <> "--" ';
        }

        return $sql;
    }

    protected static function updateMigratedTotal($add)
    {
        Configuration::updateValue('PS_HUBSPOT_MIGRATION_ORDERS', (int) Configuration::get('PS_HUBSPOT_MIGRATION_ORDERS') + (int) $add);
    }

    // //////////////////////////////////////////////////////////////////
    public static $definition = [
        'table' => 'tiralineas_hs_deal',
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
