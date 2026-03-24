<?php
/**
* DISCLAIMER
*
* Do not edit or add to this file.
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FMM Modules
*  @copyright FME Modules 2021
*  @license   Single domainn
*/

class Locator extends SearchCore
{
    public static function findProduct($keyword = '', $id_lang = null, $id_shop = null)
    {
        if (!$keyword) {
            return false;
        }

        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->langguage->id;
        }
        if (!$id_shop) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        $product_query = new DbQuery();
        $product_query->select('p.`id_product`');
        $product_query->select('IFNULL(pa.`id_product_attribute`,0) AS `id_product_attribute`'); // @todo Remove id_product_attribute if possible
        $product_query->select('IF(pa.`id_product_attribute` > 0, 1, 0) AS `has_combinations`');
        $product_query->select('p.`reference` AS `reference`');
        $product_query->select('pl.`name` AS `name`');
        $product_query->select('stock.`quantity`');
        $product_query->from('product', 'p');
        $product_query->leftJoin('product_attribute', 'pa', 'pa.`id_product` = p.`id_product`');
        $product_query->join(Shop::addSqlAssociation('product', 'p'));
        $product_query->leftJoin('product_lang', 'pl', 'p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int) $id_lang . ' AND pl.`id_shop` = ' . (int) $id_shop);
        $product_query->join(Product::sqlStock('p', null, false, Context::getContext()->shop));
        $where = array();
        $where[] = 'pl.`name` LIKE \'%' . pSQL($keyword) . '%\'';
        $where[] = 'p.`reference` LIKE \'%' . pSQL($keyword) . '%\'';
        $where[] = 'p.`ean13` LIKE \'%' . pSQL($keyword) . '%\'';
        $product_query->where(implode(' OR ', $where));
        $product_query->groupBy('p.`id_product`');
        return Db::getInstance()->executeS($product_query);
    }

    public static function getAllStores($id_product = 0, $id_lang = 0)
    {
        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        $sql = new DbQuery();
        $sql->select('t.*,s.iso_code');
        $sql->from('store', 't');
        $sql->leftJoin('state', 's', 's.id_state = t.id_state');
        $sql->where('t.`active` = 1');
        $sql->orderBy('t.id_store');

        if ($id_product) {
            $sql->where('t.related_products LIKE "%' . (int) $id_product . '%"');
        }
        if (true === Tools::version_compare(_PS_VERSION_, '1.7.3.0', '>=')) {
            $sql->select('tl.*');
            $sql->leftJoin('store_lang', 'tl', 'tl.id_store = t.id_store AND tl.id_lang = ' . (int) $id_lang);
        }
        return Db::getInstance()->executeS($sql);
    }

    public static function loadAllStores()
    {
        return Db::getInstance()->executeS('
            SELECT s.*, cl.name country, st.iso_code state
            FROM ' . _DB_PREFIX_ . 'store s
            ' . Shop::addSqlAssociation('store', 's') . '
            LEFT JOIN ' . _DB_PREFIX_ . 'country_lang cl ON (cl.id_country = s.id_country)
            LEFT JOIN ' . _DB_PREFIX_ . 'state st ON (st.id_state = s.id_state)
            WHERE s.active = 1
            AND cl.id_lang = ' . (int) Context::getContext()->language->id);
    }

    public static function getStores($distance = 50, $all = true)
    {
        $stores = array();
        $distanceUnit = Configuration::get('PS_DISTANCE_UNIT');
        if (!in_array($distanceUnit, array('km', 'mi'))) {
            $distanceUnit = 'km';
        }

        $multiplicator = ($distanceUnit == 'km' ? 6371 : 3959);

        if ($all && true === Tools::version_compare(_PS_VERSION_, '1.7.3.0', '<')) {
            $stores = Db::getInstance()->executeS('
                SELECT s.*,
                cl.name country,
                st.iso_code state,
                (' . (int) ($multiplicator) . '
                    * acos(
                        cos(radians(' . (float) (Tools::getValue('latitude')) . '))
                        * cos(radians(latitude))
                        * cos(radians(longitude) - radians(' . (float) (Tools::getValue('longitude')) . '))
                        + sin(radians(' . (float) (Tools::getValue('latitude')) . '))
                        * sin(radians(latitude))
                    )
                ) distance
                FROM ' . _DB_PREFIX_ . 'store s
                ' . Shop::addSqlAssociation('store', 's') . '
                LEFT JOIN ' . _DB_PREFIX_ . 'country_lang cl ON (cl.id_country = s.id_country)
                LEFT JOIN ' . _DB_PREFIX_ . 'state st ON (st.id_state = s.id_state)
                WHERE s.active = 1 AND cl.id_lang = ' . (int) Context::getContext()->language->id);
        } elseif ($all && true === Tools::version_compare(_PS_VERSION_, '1.7.3.0', '>=')) {
            $stores = Db::getInstance()->executeS('
                SELECT s.*,
                cl.`name` country,
                st.`iso_code` state,
                sl.`name`, sl.`address1`, sl.`address2`, sl.`hours`, sl.`note`,
                (' . (int) ($multiplicator) . '
                    * acos(
                        cos(radians(' . (float) (Tools::getValue('latitude')) . '))
                        * cos(radians(latitude))
                        * cos(radians(longitude) - radians(' . (float) (Tools::getValue('longitude')) . '))
                        + sin(radians(' . (float) (Tools::getValue('latitude')) . '))
                        * sin(radians(latitude))
                    )
                ) distance
                FROM ' . _DB_PREFIX_ . 'store s
                ' . Shop::addSqlAssociation('store', 's') . '
                LEFT JOIN ' . _DB_PREFIX_ . 'country_lang cl ON (cl.id_country = s.id_country)
                LEFT JOIN ' . _DB_PREFIX_ . 'state st ON (st.id_state = s.id_state)
                LEFT JOIN ' . _DB_PREFIX_ . 'store_lang sl ON (s.id_store = sl.id_store)
                WHERE s.active = 1
                AND cl.id_lang = ' . (int) Context::getContext()->language->id . '
                AND sl.id_lang = ' . (int) Context::getContext()->language->id);
        } elseif (!$all && true === Tools::version_compare(_PS_VERSION_, '1.7.3.0', '<')) {
            $stores = Db::getInstance()->executeS('
                SELECT s.*, cl.name country, st.iso_code state,
                (' . (int) ($multiplicator) . '
                    * acos(
                        cos(radians(' . (float) (Tools::getValue('latitude')) . '))
                        * cos(radians(latitude))
                        * cos(radians(longitude) - radians(' . (float) (Tools::getValue('longitude')) . '))
                        + sin(radians(' . (float) (Tools::getValue('latitude')) . '))
                        * sin(radians(latitude))
                    )
                ) distance,
                cl.id_country id_country
                FROM ' . _DB_PREFIX_ . 'store s
                ' . Shop::addSqlAssociation('store', 's') . '
                LEFT JOIN ' . _DB_PREFIX_ . 'country_lang cl ON (cl.id_country = s.id_country)
                LEFT JOIN ' . _DB_PREFIX_ . 'state st ON (st.id_state = s.id_state)
                WHERE s.active = 1 AND cl.id_lang = ' . (int) Context::getContext()->language->id . '
                HAVING distance < ' . (int) ($distance) . '
                ORDER BY distance ASC
                LIMIT 0, 20');
        } elseif (!$all && true === Tools::version_compare(_PS_VERSION_, '1.7.3.0', '>=')) {
            $stores = Db::getInstance()->executeS('
                SELECT s.*, sl.*, cl.name country, st.iso_code state,
                (' . (int) ($multiplicator) . '
                    * acos(
                        cos(radians(' . (float) (Tools::getValue('latitude')) . '))
                        * cos(radians(latitude))
                        * cos(radians(longitude) - radians(' . (float) (Tools::getValue('longitude')) . '))
                        + sin(radians(' . (float) (Tools::getValue('latitude')) . '))
                        * sin(radians(latitude))
                    )
                ) distance,
                cl.id_country id_country
                FROM ' . _DB_PREFIX_ . 'store s
                ' . Shop::addSqlAssociation('store', 's') . '
                LEFT JOIN ' . _DB_PREFIX_ . 'country_lang cl ON (cl.id_country = s.id_country)
                LEFT JOIN ' . _DB_PREFIX_ . 'state st ON (st.id_state = s.id_state)
                LEFT JOIN ' . _DB_PREFIX_ . 'store_lang sl ON (sl.id_store = s.id_store AND sl.id_lang = ' . (int) Context::getContext()->language->id . ')
                WHERE s.active = 1 AND
                cl.id_lang = ' . (int) Context::getContext()->language->id . '
                HAVING distance < ' . (int) ($distance) . '
                ORDER BY distance ASC
                LIMIT 0,20');
        }
        return $stores;
    }

    public static function getStoreById($id_store, $id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        return Db::getInstance()->executeS('
            SELECT s.*,
            cl.name country,
            st.iso_code state
            FROM ' . _DB_PREFIX_ . 'store s
            ' . Shop::addSqlAssociation('store', 's') . '
            LEFT JOIN ' . _DB_PREFIX_ . 'country_lang cl ON (cl.id_country = s.id_country)
            LEFT JOIN ' . _DB_PREFIX_ . 'state st ON (st.id_state = s.id_state)
            WHERE s.active = 1
            AND s.id_store = ' . (int) $id_store . '
            AND cl.id_lang = ' . (int) $id_lang);
    }

    public static function getMissingStoreField($id_store, $field, $id_lang)
    {
        if (true === Tools::version_compare(_PS_VERSION_, '1.7.3.0', '>=')) {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `' . pSQL($field) . '`
            FROM ' . _DB_PREFIX_ . 'store_lang
            WHERE `id_store` = ' . (int) $id_store . ' AND `id_lang` = ' . (int) $id_lang);
        } else {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `' . pSQL($field) . '`
            FROM ' . _DB_PREFIX_ . 'store
            WHERE `id_store` = ' . (int) $id_store);
        }
    }

    public static function getStoresByProduct($id_product, $id_lang = null)
    {
        if (!$id_product) {
            return false;
        }

        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        if (true === Tools::version_compare(_PS_VERSION_, '1.7.3.0', '<')) {
            $sql = 'SELECT t.*
            FROM ' . _DB_PREFIX_ . 'store t
            WHERE t.`active` = 1
            AND FIND_IN_SET('.(int)$id_product.', s.related_products)
            ORDER BY t.id_store';
        } else {
            $sql = 'SELECT s.*, sl.*
            FROM ' . _DB_PREFIX_ . 'store s
            LEFT JOIN ' . _DB_PREFIX_ . 'store_lang sl
                ON (sl.id_store = s.id_store AND sl.id_lang = ' . (int) $id_lang . ')
            WHERE s.`active` = 1
            AND FIND_IN_SET('.(int)$id_product.', s.related_products)
            ORDER BY s.id_store';
        }
        return Db::getInstance()->executeS($sql);
    }

    /**
     * getting store id_address
     * @param int   $id_store
     * @return bool|int
     */
    public static function getStoreAddressId($id_store)
    {
        if (!$id_store) {
            return false;
        }

        $sql = new DbQuery();
        $sql->select('id_address');
        $sql->from('storelocator_address');
        $sql->where('id_store = ' . (int) $id_store);
        return (int) Db::getInstance()->getValue($sql);
    }

    /**
     * getting store data
     * @param int   $id_cart
     * @return array
     */
    public static function getStoreByCart($id_cart)
    {
        if (!$id_cart) {
            return false;
        }

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('storelocator_cart');
        $sql->where('id_cart = ' . (int) $id_cart);
        return Db::getInstance()->getRow($sql);
    }

    /**
     * getting store data
     * @param int   $id_order
     * @return array
     */
    public static function getStoreByOrder($id_order, $id_lang = null)
    {
        if (!$id_order) {
            return false;
        }

        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('storelocator_cart');
        $sql->where('id_order = ' . (int) $id_order);

        $pickup = Db::getInstance()->getRow($sql);
        if (isset($pickup) &&
            $pickup &&
            isset($pickup['id_store']) &&
            $pickup['id_store'] &&
            Validate::isLoadedObject($store = new Store($pickup['id_store'], $id_lang))) {
            $id_address = self::getStoreAddressId($store->id);
            if ($id_address && Validate::isLoadedObject($address = new Address($id_address))) {
                $pickup['store_name'] = $store->name;
                $st_address = AddressFormat::generateAddress($address, array(), ' ', ' ');
                $st_address = str_replace($store->name, '', $st_address);
                $st_address = str_replace($address->firstname . ' ' . $address->lastname, '', $st_address);
                $pickup['store_address'] = ltrim($st_address);
            }
        }
        return $pickup;
    }

    /**
     * getting store id
     * @param int   $id_order
     * @return bool|int
     */
    public static function getIdStoreByOrder($id_order)
    {
        if (!$id_order) {
            return false;
        }

        $sql = new DbQuery();
        $sql->select('id_store');
        $sql->from('storelocator_cart');
        $sql->where('id_order = ' . (int) $id_order);
        return (int) Db::getInstance()->getValue($sql);
    }

    public static function addStoreAddress($data)
    {
        if (!isset($data) && !isset($data['id_store']) && !isset($data['id_address'])) {
            return false;
        }

        return (bool) Db::getInstance()->insert(
            'storelocator_address',
            $data,
            false,
            false,
            Db::ON_DUPLICATE_KEY
        );
    }

    public static function pushStore($data)
    {
        if (!isset($data) && !isset($data['id_store']) && !isset($data['id_cart'])) {
            return false;
        }

        return (bool) Db::getInstance()->insert(
            'storelocator_cart',
            $data,
            false,
            false,
            Db::ON_DUPLICATE_KEY
        );
    }

    public static function updateStoreByCart($id_cart, $data)
    {
        if (!$id_cart) {
            return false;
        }
        
        return (bool) Db::getInstance()->update(
            'storelocator_cart',
            $data,
            'id_cart = ' . (int) $id_cart
        );
    }

    public static function updateCustomizationAddress($id_cart, $data)
    {
        if (!$id_cart) {
            return false;
        }

        return (bool) Db::getInstance()->update(
            'customization',
            $data,
            'id_cart = ' . (int) $id_cart
        );
    }

    public static function popStore($id_cart)
    {
        if (!$id_cart) {
            return false;
        }

        return (bool) Db::getInstance()->delete(
            'storelocator_cart',
            'id_cart = ' . (int) $id_cart
        );
    }

    public static function tableExists($table)
    {
        return (bool) Db::getInstance()->executeS('SHOW TABLES LIKE \'' . _DB_PREFIX_ . pSQL($table) . '\'');
    }

    public static function createTable($table)
    {
        $sql = false;
        $result = true;
        switch ($table) {
            case 'storelocator_address':
                $sql = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'storelocator_address (
                    `id_store`                  INT(11) UNSIGNED NOT NULL,
                    `id_address`                INT(11) UNSIGNED NOT NULL,
                    PRIMARY KEY                 (`id_store`, `id_address`)
                    ) ENGINE=InnoDB             CHARSET=utf8';
                break;
            case 'storelocator_cart':
                $sql = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'storelocator_cart (
                    `id_store`                  INT(11) UNSIGNED NOT NULL,
                    `id_cart`                   INT(11) UNSIGNED NOT NULL,
                    `id_order`                  INT(11) UNSIGNED NOT NULL DEFAULT 0,
                    `id_carrier`                INT(11) UNSIGNED NOT NULL DEFAULT 0,
                    `email_alert`               TINYINT(2) NOT NULL DEFAULT 0,
                    `pickup_date`               DATETIME DEFAULT NULL,
                    PRIMARY KEY                 (`id_cart`)
                    ) ENGINE=InnoDB             CHARSET=utf8';
                break;
        }

        if (isset($sql) && $sql) {
            $result &= Db::getInstance()->execute($sql);
        }
        return $result;
    }

    public static function getStoreOrders($id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        $sql = 'SELECT sc.`id_store`, sc.`pickup_date`, sc.`email_alert`,
        o.*, o.`id_order` AS id_pdf,
		CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
		osl.`name` AS `osname`,
		os.`color`,
		IF((
            SELECT so.`id_order` FROM `' . _DB_PREFIX_ . 'orders` so
            WHERE so.`id_customer` = o.`id_customer`
            AND so.`id_order` < o.`id_order` LIMIT 1) > 0, 0, 1
        ) as new,
		IF(o.valid, 1, 0) badge_success';

        $sql .= '
        FROM `' . _DB_PREFIX_ . 'storelocator_cart` sc
        LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON (sc.id_order = o.id_order)
		LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = o.`id_customer`)
		LEFT JOIN `' . _DB_PREFIX_ . 'address` address ON address.id_address = o.id_address_delivery
		LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = o.`current_state`)
        LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl
            ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' . (int) $id_lang . ')';
        $sql .= '
        WHERE sc.id_order > 0';
        $sql .= '
        ORDER BY o.id_order DESC';

        $orders = Db::getInstance()->executeS($sql);

        if (isset($orders) && $orders) {
            foreach ($orders as &$order) {
                $order['store_name'] = null;
                $order['store_email'] = null;
                $order['store_address'] = null;

                if ($order['id_store'] && Validate::isLoadedObject($store = new Store($order['id_store'], $id_lang))) {
                    $order['store_name'] = $store->name;
                    $order['store_email'] = $store->email;
                    $id_address = self::getStoreAddressId($store->id);

                    if ($id_address && Validate::isLoadedObject($address = new Address($id_address))) {
                        $st_address = AddressFormat::generateAddress($address, array(), ' ', ' ');
                        $st_address = str_replace($store->name, '', $st_address);
                        $st_address = str_replace($address->firstname . ' ' . $address->lastname, '', $st_address);
                        $order['store_address'] = ltrim($st_address);
                    }
                }
            }
        }

        return $orders;
    }

    public static function getAllRelatedProds($id_store)
    {
        if (!$id_store) {
            return false;
        }

        $sql = 'SELECT `related_products` FROM `' . _DB_PREFIX_ . 'store` WHERE `id_store` = ' . (int) $id_store;

        $relatedProducts = array();
        $arrResult = Db::getInstance()->getRow($sql);
        if (isset($arrResult) && $arrResult && isset($arrResult['related_products']) && $arrResult['related_products']) {
            $relatedProducts = explode(',', $arrResult['related_products']);
        }

        return $relatedProducts;
    }

    public static function getAllProds()
    {
        $sQuery = 'SELECT SQL_CALC_FOUND_ROWS
            image.`id_image`,
            p.`id_product`,
            p.`price`,
            p.`id_tax_rules_group`,
            p.`wholesale_price`,
            p.`reference`,
            p.`supplier_reference`,
            p.`id_supplier`,
            p.`id_manufacturer`,
            p.`upc`,
            p.`ecotax`,
            p.`weight`,
            p.`quantity`,
            p.`available_for_order`,
            p.`date_add`,
            p.`show_price`,
            p.`online_only`,
            p.`condition`,
            p.`id_shop_default`,
            pl.`id_lang`,
            IF(p.active = 1,\'1\',\'0\') as status,
            pl.`name`,
            GROUP_CONCAT(DISTINCT(cl.`name`) SEPARATOR \',\') as categories,
            pl.`description_short`,
            pl.`description`,
            pl.`meta_title`,
            pl.`meta_keywords`,
            pl.`meta_description`,
            pl.`link_rewrite`,
            pl.`available_now`,
            pl.`available_later`
            FROM ' . _DB_PREFIX_ . 'product p
            LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON (p.id_product = pl.id_product)
            LEFT JOIN ' . _DB_PREFIX_ . 'image image ON (p.id_product = image.id_product)
            LEFT JOIN ' . _DB_PREFIX_ . 'category_product cp ON (p.id_product = cp.id_product)
            LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON (cp.id_category = cl.id_category)
            LEFT JOIN ' . _DB_PREFIX_ . 'category c ON (cp.id_category = c.id_category)
            LEFT JOIN ' . _DB_PREFIX_ . 'product_tag pt ON (p.id_product = pt.id_product)
            WHERE pl.id_lang = ' . (int) Context::getContext()->language->id . '
            AND cl.id_lang = ' . (int) Context::getContext()->language->id . '
            AND p.id_shop_default = 1
            AND c.id_shop_default = 1
            GROUP BY p.id_product';

        return Db::getInstance()->executeS($sQuery);
    }

    public static function pushStoreContact($id_store, $products)
    {
        if (!$id_store) {
            return false;
        }

        return (bool) Db::getInstance()->update(
            'store',
            $products,
            'id_store = ' . (int) $id_store
        );
    }

    public static function getFieldMissingValue($id, $field)
    {
        if (!$id) {
            return false;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `' . $field . '`
            FROM ' . _DB_PREFIX_ . 'store
            WHERE `id_store` = ' . (int) $id);
    }

    public static function getAllStoreAddresses()
    {
        $sql = new DbQuery();
        $sql->select('id_address');
        $sql->from('storelocator_address');

        $result = Db::getInstance()->executeS($sql);

        $stores = array();
        if (isset($result) && $result) {
            foreach ($result as $st) {
                $stores[] = (int)$st['id_address'];
            }
        }
        return $stores;
    }
}
