<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2020 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class PstProductColumn
{
    protected static $instance;
    protected $avail_suppliers = [];
    protected $supplier_names = [];
    protected $module;
    protected $cache_combi_references = [];

    public function __construct()
    {
        $this->module = Module::getInstanceByName('pstproductfilter');
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function displayProductShortDesc($product)
    {
        return strip_tags($product->description_short);
    }

    public function displayProductManufacturer($product)
    {
        return $product->manufacturer_name;
    }

    public function displayProductDefaultSupplier($product)
    {
        $id_default_supplier = $product->id_supplier;

        if (!$this->checkSupplierAvailForThisShop($id_default_supplier)) {
            return '';
        }

        return $this->getSupplierName($id_default_supplier);
    }

    public function displayProductSuppliers($product)
    {
        $product_suppliers = Db::getInstance()->executeS(
            'SELECT ps.`id_supplier`, s.`name`
             FROM `' . _DB_PREFIX_ . 'product_supplier` ps
             LEFT JOIN `' . _DB_PREFIX_ . 'supplier` s ON ps.`id_supplier` = s.`id_supplier`
             WHERE `id_product` = ' . (int) $product->id . '
             GROUP BY ps.`id_supplier`'
        );

        if (!$product_suppliers) {
            return '';
        }

        if (Shop::isFeatureActive()) {
            $avail_suppliers = $this->getAvailSuppliers();
            foreach ($product_suppliers as $key => $supplier) {
                if (!in_array($supplier['id_supplier'], $avail_suppliers)) {
                    unset($product_suppliers[$key]);
                }
            }
        }

        $names = [];
        foreach ($product_suppliers as $supplier) {
            $names[] = $supplier['name'];
        }

        return implode(', ', $names);
    }

    public function displayProductHasCombinations($product)
    {
        return Db::getInstance()->getValue(
            'SELECT COUNT(`id_product_attribute`)
             FROM `' . _DB_PREFIX_ . 'product_attribute`
             WHERE `id_product` = ' . (int) $product->id
        );
    }

    public function displayProductSupplierReferences($product)
    {
        $references = Db::getInstance()->executeS(
            'SELECT `id_supplier`, `product_supplier_reference`
             FROM `' . _DB_PREFIX_ . 'product_supplier`
             WHERE `id_product` = ' . (int) $product->id . '
              AND `product_supplier_reference` != ""
             ORDER BY `id_product_supplier` ASC'
        );

        if (Shop::isFeatureActive()) {
            $avail_suppliers = $this->getAvailSuppliers();
            foreach ($references as $key => $reference) {
                if (!in_array($reference['id_supplier'], $avail_suppliers)) {
                    unset($references[$key]);
                }
            }
        }

        $result = [];
        foreach ($references as $reference) {
            $result[] = $reference['product_supplier_reference'];
        }

        return implode(', ', $result);
    }

    public function displayProductWholesalePrice($product)
    {
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $price = (float) $product->wholesale_price;

        return $price ? self::displayPrice($price, $currency) : '';
    }

    public function displayProductFeatures($product)
    {
        $context = Context::getContext();
        $features = $product->getFrontFeatures($context->language->id);
        $result = [];

        foreach ($features as $feature) {
            $result[$feature['id_feature']] = $feature['name'];
        }

        return implode(', ', $result);
    }

    public function displayProductFeatureValues($product)
    {
        $context = Context::getContext();
        $features = $product->getFrontFeatures($context->language->id);
        $result = [];

        foreach ($features as $feature) {
            $result[$feature['id_feature']] = $feature['name'] . ': ' . $feature['value'];
        }

        return implode(",\r\n", $result);
    }

    public function displayProductCombinations($product)
    {
        $attributes = Db::getInstance()->executeS('
        SELECT DISTINCT pa.`id_product_attribute`, a.`id_attribute`, a.`id_attribute_group`, al.`name` as `attribute`,
                        agl.`name` as `group`, pa.`reference`, pa.`id_product`
        FROM `' . _DB_PREFIX_ . 'attribute` a
        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
            ON (a.`id_attribute` = al.`id_attribute`
             AND al.`id_lang` = ' . (int) Context::getContext()->language->id . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl
            ON (a.`id_attribute_group` = agl.`id_attribute_group` 
             AND agl.`id_lang` = ' . (int) Context::getContext()->language->id . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac
            ON (a.`id_attribute` = pac.`id_attribute`)
        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
            ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
        ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
        ' . Shop::addSqlAssociation('attribute', 'pac') . '
        WHERE pa.`id_product` = ' . (int) $product->id);

        $combinations = [];
        foreach ($attributes as $attribute) {
            if (isset($combinations[$attribute['id_product_attribute']])
                && $combinations[$attribute['id_product_attribute']]
            ) {
                $combinations[$attribute['id_product_attribute']] .=
                    ', ' . $attribute['group'] . ': ' . $attribute['attribute'];
            } else {
                $combinations[$attribute['id_product_attribute']] = $attribute['group'] . ': ' . $attribute['attribute'];
            }
        }

        sort($combinations);

        return implode(",\r\n", $combinations);
    }

    public function displayProductAttributes($product)
    {
        $id_lang = Context::getContext()->language->id;

        $attributes = Db::getInstance()->getValue(
            'SELECT GROUP_CONCAT(DISTINCT agl.`name` SEPARATOR ", ")
             FROM `' . _DB_PREFIX_ . 'attribute_group_lang` agl
             LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a
                ON (a.`id_attribute_group` = agl.`id_attribute_group`)
             LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                ON (a.`id_attribute` = pac.`id_attribute`)
             LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
                ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
             ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
             ' . Shop::addSqlAssociation('attribute', 'a') . '
             WHERE pa.`id_product` = ' . (int) $product->id . ' AND agl.`id_lang` = ' . (int) $id_lang
        );

        return $attributes;
    }

    public function displayProductType($product)
    {
        return $this->module->getProductTypeName($product->getType());
    }

    public function displayProductTaxRule($product)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT `name`
             FROM `' . _DB_PREFIX_ . 'tax_rules_group`
             WHERE `id_tax_rules_group` = ' . (int) $product->id_tax_rules_group
        );
    }

    public function displayProductWeight($product)
    {
        if ((float) $product->weight) {
            return ($product->weight * 1) . ' ' . Configuration::get('PS_WEIGHT_UNIT');
        }

        return '';
    }

    public function displayProductTitle($product)
    {
        return $product->meta_title;
    }

    public function displayProductAvail($product)
    {
        return $product->checkQty(1) ? '1' : '0';
    }

    public function displayProductDiscounts($product)
    {
        $tz = Configuration::get('PS_TIMEZONE');
        $dt_now = new DateTime('now', new DateTimeZone($tz));
        $shops = Shop::getContextListShopID();
        $shops[] = '0';
        $discounts = Db::getInstance()->executeS(
            'SELECT *
             FROM `' . _DB_PREFIX_ . 'specific_price`
             WHERE `id_product` = ' . (int) $product->id .
             (Shop::isFeatureActive()
                ? ' AND `id_shop` IN (' . implode(',', array_map('intval', $shops)) . ')' : '') . '
              AND (`from` = "0000-00-00 00:00:00" OR `from` <= "' . pSQL($dt_now->format('Y-m-d H:i:s')) . '")'
              . ' AND (`to` = "0000-00-00 00:00:00" OR `to` >= "' . pSQL($dt_now->format('Y-m-d H:i:s')) . '")'
        );

        $result = [];
        foreach ($discounts as $discount) {
            if ((float) $discount['reduction'] > 0) {
                $result[] = ($discount['reduction_type'] == 'amount'
                    ? self::displayPrice($discount['reduction'])
                    : ($discount['reduction'] * 100) . '%');
            } elseif ((float) $discount['price'] > -1) {
                $result[] = '=' . self::displayPrice($discount['price']);
            }
        }

        return implode(', ', $result);
    }

    public function displayProductOosOrders($product)
    {
        return $product->isAvailableWhenOutOfStock(StockAvailable::outOfStock($product->id));
    }

    public function displayProductCarriers($product)
    {
        $carriers = $product->getCarriers();

        $result = [];
        foreach ($carriers as $carrier) {
            $result[] = ($carrier['name'] ? $carrier['name'] : Configuration::get('PS_SHOP_NAME'));
        }

        if (!$result) {
            $result = [$this->module->l('All', 'pstproductcolumn')];
        }

        return implode(', ', $result);
    }

    public function displayProductWebOnly($product)
    {
        return $product->online_only ? '1' : '0';
    }

    public function displayProductTags($product)
    {
        $result = [];
        if (is_array($product->tags) && $product->tags) {
            foreach ($product->tags as $id_lang => $tags) {
                $result[Language::getIsoById($id_lang)] = $tags; // convert ID lang keys to ISO keys
            }
        }

        return $result;
    }

    public function displayProductCondition($product)
    {
        switch ($product->condition) {
            case 'new':
                return $this->module->l('New');
                break;
            case 'used':
                return $this->module->l('Used');
                break;
            case 'refurbished':
                return $this->module->l('Refurbished');
                break;
        }

        return $product->condition;
    }

    public function displayProductReferences($product)
    {
        $combi_refs = $this->getCombinationReferences($product->id, 'reference');

        return $this->displayAllReferences($product->reference, $combi_refs);
    }

    public function displayProductEan13($product)
    {
        $combi_refs = $this->getCombinationReferences($product->id, 'ean13');

        return $this->displayAllReferences($product->ean13, $combi_refs);
    }

    public function displayProductUpc($product)
    {
        $combi_refs = $this->getCombinationReferences($product->id, 'upc');

        return $this->displayAllReferences($product->upc, $combi_refs);
    }

    public function displayProductIsbn($product)
    {
        $combi_refs = $this->getCombinationReferences($product->id, 'isbn');

        return $this->displayAllReferences($product->isbn, $combi_refs);
    }

    public function displayProductMpn($product)
    {
        // not avail until ps1.7.7
        if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
            return '';
        }

        $combi_refs = $this->getCombinationReferences($product->id, 'mpn');

        return $this->displayAllReferences($product->mpn, $combi_refs);
    }

    public function displayProductCustomizable($product)
    {
        return $product->customizable ? '1' : '0';
    }

    public function displayProductHasAttachments($product)
    {
        return $product->cache_has_attachments ? '1' : '0';
    }

    public function displayProductSaleNumber($product)
    {
        return (int) Db::getInstance()->getValue(
            'SELECT SUM(`product_quantity`)
             FROM `' . _DB_PREFIX_ . 'order_detail`
             WHERE `product_id` = ' . (int) $product->id .
              (Shop::isFeatureActive()
                ? ' AND `id_shop` IN (' . implode(',', array_map('intval', Shop::getContextListShopID())) . ')' : '')
        );
    }

    public function displayProductSaleNumberDetailed($product)
    {
        // check if this product has any combinations
        $has_combinations = $this->displayProductHasCombinations($product);

        if (!$has_combinations) {
            // if no combinations, just return the product sales number
            return $this->displayProductSaleNumber($product);
        } else {
            $sales = Db::getInstance()->executeS(
                'SELECT `product_attribute_id`, SUM(`product_quantity`) AS `sales`
                 FROM `' . _DB_PREFIX_ . 'order_detail`
                 WHERE `product_id` = ' . (int) $product->id .
                (Shop::isFeatureActive()
                    ? ' AND `id_shop` IN (' . implode(',', array_map('intval', Shop::getContextListShopID())) . ')'
                    : '') .
                ' GROUP BY `product_attribute_id`'
            );

            if ($sales && is_array($sales)) {
                $result = [];
                foreach ($sales as $row) {
                    $result[] = self::getCombinationName($row['product_attribute_id']) . ' (' . $row['sales'] . ')';
                }

                return implode(",\r\n", $result);
            } else {
                return $this->displayProductSaleNumber($product);
            }
        }
    }

    public function displayProductVisibility($product)
    {
        $values = [
            'both' => $this->module->l('Everywhere', 'pstproductcolumn'),
            'catalog' => $this->module->l('Catalog', 'pstproductcolumn'),
            'search' => $this->module->l('Search', 'pstproductcolumn'),
            'none' => $this->module->l('Nowhere', 'pstproductcolumn'),
        ];

        if (isset($values[$product->visibility])) {
            return $values[$product->visibility];
        }

        return '';
    }

    public function displayProductDimensions($product)
    {
        // if at least one dimension is set
        if ((float) $product->width || (float) $product->height || (float) $product->depth) {
            return $product->width * 1 . ' x ' . $product->height * 1 . ' x ' . $product->depth * 1
                . ' ' . Configuration::get('PS_DIMENSION_UNIT');
        }

        return '';
    }

    public function displayProductStockLocation($product)
    {
        // check if this product has any combinations
        $has_combinations = $this->displayProductHasCombinations($product);

        if (!$has_combinations) {
            // if no combinations, just get the only location
            $query = new DbQuery();
            $query->select('`location`');
            $query->from('stock_available');
            $query->where('id_product = ' . $product->id);
            $query->where('id_product_attribute = 0');
            $query = StockAvailable::addSqlShopRestriction($query);

            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        } else {
            // if combinations, get combinations grouped by locations
            // get combinations and other data
            $query = '
            SELECT DISTINCT pa.`id_product_attribute`, a.`id_attribute`, a.`id_attribute_group`, al.`name` as `attribute`,
                            agl.`name` as `group`, pa.`reference`, pa.`id_product`, sa.`location`
            FROM `' . _DB_PREFIX_ . 'attribute` a
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
                ON (a.`id_attribute` = al.`id_attribute`
                 AND al.`id_lang` = ' . (int) Context::getContext()->language->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl
                ON (a.`id_attribute_group` = agl.`id_attribute_group` 
                 AND agl.`id_lang` = ' . (int) Context::getContext()->language->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                ON (a.`id_attribute` = pac.`id_attribute`)
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
                ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
            LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa
                ON (pa.`id_product_attribute` = sa.`id_product_attribute`)
            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            ' . Shop::addSqlAssociation('attribute', 'pac') . '
            ' . StockAvailable::addSqlShopRestriction(null, null, 'sa') . '
            WHERE pa.`id_product` = ' . (int) $product->id;

            $attributes = Db::getInstance()->executeS($query);

            // build location-combination array
            $locations = [];
            foreach ($attributes as $attribute) {
                if ($attribute['location']) {
                    $loc_key = bin2hex($attribute['location']);
                    $locations[$loc_key] = (isset($locations[$loc_key]) ? $locations[$loc_key] : []);

                    if (!empty($locations[$loc_key][$attribute['id_product_attribute']])) {
                        $locations[$loc_key][$attribute['id_product_attribute']] .=
                            ', ' . $attribute['group'] . ': ' . $attribute['attribute'];
                    } else {
                        $locations[$loc_key][$attribute['id_product_attribute']] =
                            $attribute['group'] . ': ' . $attribute['attribute'];
                    }
                }
            }

            ksort($locations);

            $result = [];
            foreach ($locations as $location => $combinations) {
                sort($combinations);
                $result[] = hex2bin($location) . ' (' . implode(' | ', $combinations) . ')';
            }

            return implode(",\r\n", $result);
        }
    }

    public function displayProductCategories($product)
    {
        $context = Context::getContext();
        $category_ids = $product->getCategories();

        return Db::getInstance()->getValue(
            'SELECT GROUP_CONCAT(cl.`name` SEPARATOR ", ")
             FROM `' . _DB_PREFIX_ . 'category_lang` cl
             WHERE cl.`id_category` IN (' . implode(',', array_map('intval', $category_ids)) . ')
              AND cl.`id_lang` = ' . (int) $context->language->id
        );
    }

    public function displayProductMargin($product)
    {
        if ($product->wholesale_price > 0) {
            $diff = $product->price - $product->wholesale_price;
            $margin = $diff / $product->price * 100;

            return round($margin, 2) . '%';
        }

        return '';
    }

    public function displayProductPublicPrice($product)
    {
        $price = $product->getPublicPrice();

        return self::displayPrice($price);
    }

    public static function displayPrice($price, $currency = null)
    {
        if (!is_numeric($price)) {
            return $price;
        }

        if (method_exists('Tools', 'getContextLocale')) {
            $context = Context::getContext();
            $currency = ($currency ? $currency : $context->currency);

            if (is_numeric($currency)) {
                $currency = Currency::getCurrencyInstance($currency);
            }

            $locale = Tools::getContextLocale($context);
            $currencyCode = is_array($currency) ? $currency['iso_code'] : $currency->iso_code;

            return $locale->formatPrice($price, $currencyCode);
        } else {
            if (is_numeric($currency)) {
                $currency = Currency::getCurrencyInstance($currency);
            }

            return Tools::displayPrice($price, $currency);
        }
    }

    protected function checkSupplierAvailForThisShop($id_supplier)
    {
        if (Shop::isFeatureActive()) {
            $avail_suppliers = $this->getAvailSuppliers();
            if (!in_array($id_supplier, $avail_suppliers)) {
                return false;
            }
        }

        return true;
    }

    protected function getAvailSuppliers()
    {
        if (!$this->avail_suppliers) {
            $this->avail_suppliers = [];
            $suppliers = Db::getInstance()->executeS(
                'SELECT `id_supplier` FROM `' . _DB_PREFIX_ . 'supplier_shop`
                 WHERE `id_shop` IN (' . implode(',', array_map('intval', Shop::getContextListShopID())) . ')'
            );

            foreach ($suppliers as $supplier) {
                $this->avail_suppliers[] = $supplier['id_supplier'];
            }
        }

        return $this->avail_suppliers;
    }

    protected function getSupplierName($id_supplier)
    {
        $supplier_names = $this->getAllSupplierNames();

        if (isset($supplier_names[$id_supplier])) {
            return $supplier_names[$id_supplier];
        }

        return '';
    }

    protected function getAllSupplierNames()
    {
        if (!$this->supplier_names) {
            $this->supplier_names = [];
            $suppliers = Db::getInstance()->executeS('SELECT `id_supplier`, `name` FROM `' . _DB_PREFIX_ . 'supplier`');

            foreach ($suppliers as $supplier) {
                $this->supplier_names[$supplier['id_supplier']] = $supplier['name'];
            }
        }

        return $this->supplier_names;
    }

    public static function sqlArrayToList($array, $column)
    {
        if (!is_array($array)) {
            return [0];
        }

        $result = [];

        foreach ($array as $item) {
            $result[] = $item[$column];
        }

        if (!count($result)) {
            $result = [0];
        }

        return $result;
    }

    public static function getCombinationName($id_product_attribute = null, $id_lang = null)
    {
        // use the lang in the context if $id_lang is not defined
        if (!$id_lang) {
            $id_lang = (int) Context::getContext()->language->id;
        }

        $name = Db::getInstance()->getValue(
            'SELECT GROUP_CONCAT(DISTINCT agl.`name`, ": ", al.name SEPARATOR ", ") as name
             FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
             LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a
              ON a.id_attribute = pac.id_attribute
             LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl
              ON agl.id_attribute_group = a.id_attribute_group
              AND agl.id_lang = ' . (int) $id_lang . '
             LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
              ON al.id_attribute = pac.id_attribute
              AND al.id_lang = ' . (int) $id_lang . '
             WHERE pac.id_product_attribute = ' . (int) $id_product_attribute
        );

        return trim($name);
    }

    protected function getCombinationReferences($id_product, $column)
    {
        // check the cache:
        if (!empty($this->cache_combi_references[$id_product])
            && isset($this->cache_combi_references[$id_product][$column])
        ) {
            return $this->cache_combi_references[$id_product][$column];
        }

        $mpn_too = version_compare(_PS_VERSION_, '1.7.7.0', '>=');

        // get the data from db:
        $combinations = Db::getInstance()->executeS(
            'SELECT `reference`, `ean13`, `isbn`, `upc` ' . ($mpn_too ? ', `mpn`' : '') . '
             FROM `' . _DB_PREFIX_ . 'product_attribute`
             WHERE `id_product` = ' . (int) $id_product
        );

        // format it properly:
        $result = [];
        foreach ($combinations as $combination) {
            foreach ($combination as $col => $value) {
                if (empty($result[$col])) {
                    $result[$col] = [];
                }
                $result[$col][] = $value;
            }
        }

        if (isset($result[$column])) {
            // cache
            // add the new data:
            $this->cache_combi_references[$id_product] = $result;

            return $result[$column];
        }

        return [];
    }

    protected function displayAllReferences($main_reference, $combination_references)
    {
        $refs = [$main_reference];
        $refs = array_merge($refs, $combination_references);
        $refs = array_filter($refs);
        $refs = array_unique($refs);

        return implode(', ', $refs);
    }
}
