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

class PstProductSearch
{
    protected static $instance;
    protected $product_alias;
    protected $product_shop_alias;
    protected $stock_alias;

    public function __construct($v2 = false)
    {
        if ($v2) {
            $this->product_alias = 'p';
            $this->product_shop_alias = 'ps';
            $this->stock_alias = 'sa';
        } else {
            $this->product_alias = 'p';
            $this->product_shop_alias = 'sa';
            $this->stock_alias = 'sav';
        }
    }

    public static function getInstance($v2 = false)
    {
        if (empty(self::$instance)) {
            self::$instance = new self($v2);
        }

        return self::$instance;
    }

    public function searchProductsByDesc($query)
    {
        return
            pSQL($this->product_alias) . '.`id_product` IN (
                SELECT `id_product`
                FROM `' . _DB_PREFIX_ . 'product_lang`
                WHERE `description` LIKE "%' . pSQL($query) . '%"
                 OR `description_short` LIKE "%' . pSQL($query) . '%"
             )';
    }

    public function searchProductsByName($query)
    {
        return
            pSQL($this->product_alias) . '.`id_product` IN (
                SELECT `id_product`
                FROM `' . _DB_PREFIX_ . 'product_lang`
                WHERE `name` LIKE "%' . pSQL($query) . '%"
             )';
    }

    public function searchProductsByManufacturer($values)
    {
        return
            pSQL($this->product_alias) . '.`id_product` IN (
                SELECT `id_product`
                FROM `' . _DB_PREFIX_ . 'product`
                WHERE `id_manufacturer` IN (' . implode(',', array_map('intval', $values)) . ')
             )';
    }

    public function searchProductsBySupplier($values, $strict = false)
    {
        $query = [];

        $filtered_array = array_filter($values);

        if ($filtered_array) {
            $query[] = pSQL($this->product_alias) . '.`id_product` IN (
                SELECT `id_product`
                FROM `' . _DB_PREFIX_ . 'product_supplier`
                WHERE `id_supplier` IN (' . implode(',', array_map('intval', $values)) . ')
                GROUP BY `id_product`
                ' . ($strict ? ' HAVING COUNT(DISTINCT `id_supplier`) >= ' . count($values) : '') . '
             )';
        }
        if (in_array(0, $values)) {
            if ($strict && $filtered_array) {
                return ' false '; // impossible situation
            }

            $query[] = pSQL($this->product_alias) . '.`id_product` NOT IN (
                SELECT `id_product`
                FROM `' . _DB_PREFIX_ . 'product_supplier`
            )';
        }

        return '(' . implode(' OR ', $query) . ')';
    }

    public function searchProductsByWithCombinations($value)
    {
        return
            pSQL($this->product_alias) . '.`id_product`
            ' . ($value == 'no' ? ' NOT ' : '') . '
             IN (
                SELECT pa.`id_product`
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                WHERE ' . pSQL($this->product_alias) . '.`id_product` = pa.`id_product`
             )';
    }

    public function searchProductsByReference($query)
    {
        return
            pSQL($this->product_alias) . '.`reference` LIKE "' . pSQL($query) . '%"
            OR
            ' . pSQL($this->product_alias) . '.`id_product` IN (
                SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product_attribute`
                WHERE `reference` LIKE "' . pSQL($query) . '%"
            )';
    }

    public function searchProductsBySupplierReference($value)
    {
        return
            pSQL($this->product_alias) . '.`id_product` IN (
                SELECT `id_product`
                FROM `' . _DB_PREFIX_ . 'product_supplier`
                WHERE `product_supplier_reference` LIKE "%' . pSQL($value) . '%"
             )';
    }

    public function searchProductsByWholesalePrice($range)
    {
        $range = explode('-', $range);

        if (is_array($range) && count($range) == 2) {
            return
                pSQL($this->product_shop_alias) . '.`wholesale_price` >= ' . (float) $range[0] . ' AND ' .
                pSQL($this->product_shop_alias) . '.`wholesale_price` <= ' . (float) $range[1];
        }
    }

    public function searchProductsByFeature($values, $strict = false)
    {
        $query = [];

        $filtered_array = array_filter($values);

        if ($filtered_array) {
            $query[] = pSQL($this->product_alias) . '.`id_product` IN (
                SELECT `id_product`
                FROM `' . _DB_PREFIX_ . 'feature_product`
                WHERE `id_feature` IN (' . implode(',', array_map('intval', $filtered_array)) . ')
                GROUP BY `id_product`
                ' . ($strict ? ' HAVING COUNT(DISTINCT `id_feature`) >= ' . count($values) : '') . '
            )';
        }
        if (in_array(0, $values)) {
            if ($strict && $filtered_array) {
                return ' false '; // impossible situation
            }

            $query[] = pSQL($this->product_alias) . '.`id_product` NOT IN (
                SELECT `id_product`
                FROM `' . _DB_PREFIX_ . 'feature_product`
            )';
        }

        return '(' . implode(' OR ', $query) . ')';
    }

    public function searchProductsByFeatureValue($values, $strict = false)
    {
        $values = array_unique($values);

        return
            pSQL($this->product_alias) . '.`id_product` IN (
                SELECT `id_product`
                FROM `' . _DB_PREFIX_ . 'feature_product`
                WHERE `id_feature_value` IN (' . implode(',', array_map('intval', $values)) . ')
                GROUP BY `id_product`
                ' . ($strict ? ' HAVING COUNT(DISTINCT `id_feature_value`) >= ' . count($values) : '') . '
             )';
    }

    public function searchProductsByAttributeGroup($values, $strict = false)
    {
        $values = array_unique($values);

        return
            pSQL($this->product_alias) . '.`id_product` IN (
                SELECT DISTINCT `id_product`
                FROM `' . _DB_PREFIX_ . 'product_attribute_shop` pas
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                 ON pac.`id_product_attribute` = pas.`id_product_attribute`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a
                 ON a.`id_attribute` = pac.`id_attribute`
                WHERE 1
                 ' . (Shop::isFeatureActive()
                    ? ' AND pas.`id_shop` IN (' . implode(',', array_map('intval', Shop::getContextListShopID())) . ')'
                    : '') . '
                 AND a.`id_attribute_group` IN (' . implode(',', array_map('intval', $values)) . ')
                 GROUP BY `id_product`
                 ' . ($strict ? ' HAVING COUNT(DISTINCT a.`id_attribute_group`) >= ' . count($values) : '') . '
             )';
    }

    public function searchProductsByAttribute($values, $strict = false)
    {
        $values = array_unique($values);

        return
            pSQL($this->product_alias) . '.`id_product` IN (
                SELECT DISTINCT `id_product`
                FROM `' . _DB_PREFIX_ . 'product_attribute_shop` pas
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                 ON pac.`id_product_attribute` = pas.`id_product_attribute`
                WHERE 1
                 ' . (Shop::isFeatureActive()
                    ? ' AND pas.`id_shop` IN (' . implode(',', array_map('intval', Shop::getContextListShopID())) . ')'
                    : '') . '
                 AND pac.`id_attribute` IN (' . implode(',', array_map('intval', $values)) . ')
                 GROUP BY `id_product`
                 ' . ($strict ? ' HAVING COUNT(DISTINCT pac.`id_attribute`) >= ' . count($values) : '') . '
             )';
    }

    public function searchProductsByType($values)
    {
        $query = [];

        if (in_array(0, $values)) {
            $query[] = '(' . pSQL($this->product_alias) . '.`cache_is_pack` = 0' .
                ' AND ' . pSQL($this->product_alias) . '.`is_virtual` = 0)';
        }
        if (in_array(Product::PTYPE_PACK, $values)) {
            $query[] = pSQL($this->product_alias) . '.`cache_is_pack` = 1';
        }
        if (in_array(Product::PTYPE_VIRTUAL, $values)) {
            $query[] = pSQL($this->product_alias) . '.`is_virtual` = 1';
        }

        return '(' . implode(' OR ', $query) . ')';
    }

    public function searchProductsByTaxRule($values)
    {
        return pSQL($this->product_alias) . '.`id_tax_rules_group` IN (' . implode(',', array_map('intval', $values)) . ')';
    }

    public function searchProductsByWeight($range)
    {
        $range = explode('-', $range);

        if (is_array($range) && count($range) == 2) {
            return
                pSQL($this->product_alias) . '.`weight` >= ' . (float) $range[0] . ' AND ' .
                pSQL($this->product_alias) . '.`weight` <= ' . (float) $range[1];
        }
    }

    public function searchProductsByWithImages($value)
    {
        return
            pSQL($this->product_alias) . '.`id_product`
            ' . ($value == 'no' ? ' NOT ' : '') . '
             IN (
                SELECT ish.`id_product`
                FROM `' . _DB_PREFIX_ . 'image_shop` ish
                WHERE ' . pSQL($this->product_alias) . '.`id_product` = ish.`id_product`'
                . (Shop::isFeatureActive()
                    ? ' AND ish.`id_shop` IN (' . implode(',', array_map('intval', Shop::getContextListShopID())) . ')'
                    : '') . '
             )';
    }

    public function searchProductsByWithDiscounts($value)
    {
        $tz = Configuration::get('PS_TIMEZONE');
        $dt_now = new DateTime('now', new DateTimeZone($tz));
        $shops = Shop::getContextListShopID();
        $shops[] = '0';

        return
            pSQL($this->product_alias) . '.`id_product`
            ' . ($value == 'no' ? ' NOT ' : '') . '
             IN (
                SELECT sp.`id_product`
                FROM `' . _DB_PREFIX_ . 'specific_price` sp
                WHERE ' . pSQL($this->product_alias) . '.`id_product` = sp.`id_product`'
                . (Shop::isFeatureActive()
                    ? ' AND sp.`id_shop` IN (' . implode(',', array_map('intval', $shops)) . ')'
                    : '') . '
                AND (`from` = "0000-00-00 00:00:00" OR `from` <= "' . pSQL($dt_now->format('Y-m-d H:i:s')) . '")'
                . ' AND (`to` = "0000-00-00 00:00:00" OR `to` >= "' . pSQL($dt_now->format('Y-m-d H:i:s')) . '")
             )';
    }

    public function searchProductsByAllowOOS($value)
    {
        if (Configuration::get('PS_STOCK_MANAGEMENT')) {
            $ps_default = Configuration::get('PS_ORDER_OUT_OF_STOCK');

            if ($value == 'no') {
                return
                    pSQL($this->stock_alias) . '.`out_of_stock` = 0' .
                    (!$ps_default ? ' OR ' . pSQL($this->stock_alias) . '.`out_of_stock` = 2' : '');
            } else {
                return
                    pSQL($this->stock_alias) . '.`out_of_stock` = 1' .
                    ($ps_default ? ' OR ' . pSQL($this->stock_alias) . '.`out_of_stock` = 2' : '');
            }
        }

        return '';
    }

    public function searchProductsByCarrier($values, $strict = false)
    {
        return '(' .
            pSQL($this->product_alias) . '.`id_product` IN (
                SELECT DISTINCT `id_product`
                FROM `' . _DB_PREFIX_ . 'product_carrier`
                WHERE 1
                 ' . (Shop::isFeatureActive()
                    ? ' AND `id_shop` IN (' . implode(',', array_map('intval', Shop::getContextListShopID())) . ')'
                    : '') . '
                 AND `id_carrier_reference` IN (' . implode(',', array_map('intval', $values)) . ')
                 GROUP BY `id_product`
                 ' . ($strict ? ' HAVING COUNT(DISTINCT `id_carrier_reference`) >= ' . count($values) : '') . '
             ) OR ' . pSQL($this->product_alias) . '.`id_product` NOT IN (
                SELECT DISTINCT `id_product`
                FROM `' . _DB_PREFIX_ . 'product_carrier`
                WHERE 1
                 ' . (Shop::isFeatureActive()
                    ? ' AND `id_shop` IN (' . implode(',', array_map('intval', Shop::getContextListShopID())) . ')'
                    : '') . '
             )
             )';
    }

    public function searchProductsByWebOnly($value)
    {
        $online_only = ($value == 'no' ? 0 : 1);

        return pSQL($this->product_shop_alias) . '.`online_only` = ' . (int) $online_only;
    }

    public function searchProductsByTag($query)
    {
        return
            pSQL($this->product_alias) . '.`id_product` IN (
                SELECT pt.`id_product`
                FROM `' . _DB_PREFIX_ . 'product_tag` pt
                LEFT JOIN `' . _DB_PREFIX_ . 'tag` t ON pt.`id_tag` = t.`id_tag`
                WHERE t.`name` LIKE "' . pSQL($query) . '%"
             )';
    }

    public function searchProductsByCondition($values)
    {
        $conditions = [1 => 'new', 2 => 'used', 3 => 'refurbished'];

        foreach ($values as $key => $value) {
            if (isset($conditions[$value])) {
                $values[$key] = $conditions[$value];
            }
        }

        return pSQL($this->product_shop_alias) . '.`condition` IN ("' . implode('","', array_map('pSQL', $values)) . '")';
    }

    public function searchProductsByEan13($query)
    {
        return
            pSQL($this->product_alias) . '.`ean13` LIKE "' . pSQL($query) . '%"
            OR
            ' . pSQL($this->product_alias) . '.`id_product` IN (
                SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product_attribute`
                WHERE `ean13` LIKE "' . pSQL($query) . '%"
            )';
    }

    public function searchProductsByUpc($query)
    {
        return
            pSQL($this->product_alias) . '.`upc` LIKE "' . pSQL($query) . '%"
            OR
            ' . pSQL($this->product_alias) . '.`id_product` IN (
                SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product_attribute`
                WHERE `upc` LIKE "' . pSQL($query) . '%"
            )';
    }

    public function searchProductsByIsbn($query)
    {
        return
            pSQL($this->product_alias) . '.`isbn` LIKE "' . pSQL($query) . '%"
            OR
            ' . pSQL($this->product_alias) . '.`id_product` IN (
                SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product_attribute`
                WHERE `isbn` LIKE "' . pSQL($query) . '%"
            )';
    }

    public function searchProductsByMPN($query)
    {
        // not avail until ps1.7.7
        if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
            return '';
        }

        return
            pSQL($this->product_alias) . '.`mpn` LIKE "' . pSQL($query) . '%"
            OR
            ' . pSQL($this->product_alias) . '.`id_product` IN (
                SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product_attribute`
                WHERE `mpn` LIKE "' . pSQL($query) . '%"
            )';
    }

    public function searchProductsByCustomizable($value)
    {
        return pSQL($this->product_shop_alias) . '.`customizable` ' . ($value == 'no' ? ' = 0' : ' > 0');
    }

    public function searchProductsByHasAttachments($value)
    {
        return pSQL($this->product_alias) . '.`cache_has_attachments` ' . ($value == 'no' ? ' = 0' : ' > 0');
    }

    public function searchProductsByNumberOfSales($range)
    {
        $range = explode('-', $range);

        if (is_array($range) && count($range) == 2) {
            $zero_query = pSQL($this->product_alias) . '.`id_product` NOT IN (
                SELECT `product_id`
                FROM `' . _DB_PREFIX_ . 'order_detail`
                WHERE 1 ' . (Shop::isFeatureActive()
                ? ' AND `id_shop` IN (' . implode(',', array_map('intval', Shop::getContextListShopID())) . ')'
                : '') . '
            )';

            if ($range[0] == '0' && $range[1] == '0') {
                return $zero_query;
            } else {
                $query = pSQL($this->product_alias) . '.`id_product` IN (
                    SELECT `product_id`
                    FROM `' . _DB_PREFIX_ . 'order_detail`
                    WHERE 1 ' . (Shop::isFeatureActive()
                     ? ' AND `id_shop` IN (' . implode(',', array_map('intval', Shop::getContextListShopID())) . ')'
                     : '') . '
                    GROUP BY `product_id`
                    HAVING SUM(`product_quantity`) >= ' . (float) $range[0] . ' AND SUM(`product_quantity`) <= ' . (float) $range[1] . '
                )';

                if (in_array('0', $range)) {
                    $query = '(' . $query . ' OR ' . $zero_query . ')';
                }

                return $query;
            }
        }

        return '';
    }

    public function searchProductsByVisibility($values)
    {
        $options = [1 => 'both', 2 => 'catalog', 3 => 'search', 4 => 'none'];
        foreach ($values as $key => $value) {
            if (isset($options[$value])) {
                $values[$key] = $options[$value];
            }
        }

        return pSQL($this->product_shop_alias) . '.`visibility` 
            IN ("' . implode('","', array_map('pSQL', $values)) . '")';
    }

    public function searchProductsByStockLocation($query)
    {
        return pSQL($this->product_alias) . '.`id_product` IN (
            SELECT `id_product`
            FROM `' . _DB_PREFIX_ . 'stock_available`
            WHERE `location` LIKE "%' . pSQL($query) . '%"
        )';
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
}
