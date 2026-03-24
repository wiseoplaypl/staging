<?php

/**
 * @author Łukasz Szpak ( info@dev-bot.pl )
 * @Copyright 2019 SzpaQ <dev-bot.pl>
 * @license ALL RIGHTS RESERVED
 * */

ini_set('memory_limit','2048M');

class Migrate
{
    public static $ID_LANG = 1;
    public static $CATEGORY_PREFIX = '';
    public static $CATEGORY_PREFIX_REWRITE = '';
    public static $CATEGORY_SUFFIX = '';
    public static $EXCLUDE = [10];
    public static $FEATURES = FALSE;
    public static $CATEGORIES = FALSE;
    public static $LANGUAGES = false;
    public static $IN_CATEGORY = 2;
    public static $ID_SUPPLIERS_IMPORTED = [];
    public static $PRODUCTS_IMPORTED = [];


    public static $updateImagesSkipping = true;
    public static $updateImages = false;
    public static $updateSuppliers = false;
    public static $updateFeatures = false;
    public static $updateQuantity = true;
    public static $updatePrice = true;
    public static $updateCategories = false;
    public static $updateProducts = false;
    public static $updateDetails = true;
    public static $updateAttributes = true;

    public static $importWithZeroPrice = false;
    public static $importWithZeroQuantity = false;

    public static $mapAttributes = [];

    public static $displayImportingProduct = false;

    public static $FEATURES_IMPORTED = [];
    public static $FEATURES_VALUES_IMPORTED = [];

    public static function getCategoryChildren($id_parent = 2, $recursive = true)
    {
        $categories = DB::getInstance()->executeS("
            SELECT cl.*
            FROM ". _DB_PREFIX_ ."category c
            LEFT JOIN ". _DB_PREFIX_ ."category_lang cl
            ON cl.id_category = c.id_category
            WHERE id_lang = ". self::$ID_LANG ."
            AND id_parent = $id_parent
        ");
        if (!$categories) {
            return false;
        }
        $result = [];
        foreach ($categories as $v) {
            if (in_array($v['id_category'], self::$EXCLUDE)) {
                continue;
            }
            if ($recursively === true) {
                $v['children'] = self::getChildren($v['id_category'], true);
            }
            if (self::$CATEGORY_PREFIX) {
                $v['name'] = self::$CATEGORY_PREFIX.$v['name'];
            }
            if (self::$CATEGORY_PREFIX_REWRITE) {
                $v['link_rewrite'] = self::$CATEGORY_PREFIX_REWRITE.$v['link_rewrite'];
            }
            $result[] = $v;
        }
        return $result;
    }
    public static function getCategoriesChildrenId($id_parent, $recursively = true) : array
    {
        $result = [];
        $categories = DB::getInstance()->executeS("
            SELECT id_category
            FROM ". _DB_PREFIX_ ."category
            WHERE id_parent = $id_parent
        ");
        if ($categories) {
            foreach ($categories as $k => $v) {
                $result[] = (int) $v['id_category'];
                if ($recursively == true) {
                    $result = array_merge($result, self::getCategoriesChildrenId($v['id_category']));
                }
            }
        }
        return $result;
    }
    public static function getCategoryPath($id_category)
    {
        if ($id_category < 3) {
            return '';
        }
        $sql = "
            SELECT cl.*, c.id_parent
            FROM ". _DB_PREFIX_ ."category c
            LEFT JOIN ". _DB_PREFIX_ ."category_lang cl
            ON cl.id_category = c.id_category
            WHERE id_lang = ". self::$ID_LANG ."
            AND c.id_category = $id_category
        ";
        $category = DB::getInstance()->getRow($sql);
        $name = [self::$CATEGORY_PREFIX.$category['name']];
        if ($category['id_parent']) {
            array_unshift($name, self::getCategoryPath($category['id_parent']));
            $name = array_filter($name);
        }
        return implode('//', $name);
    }
    public static function getCategories($id_product = false) : array
    {
        if (SELF::$CATEGORIES === FALSE) {
            $categories = DB::getInstance()->executeS("
                SELECT id_category FROM ". _DB_PREFIX_ ."category
            ");
            foreach ($categories as $k => $v) {
                SELF::$CATEGORIES[$v['id_category']] = self::getCategoryPath($v['id_category']);
            }
        }
        if ($id_product) {
            $result = [];
            $sql = "SELECT id_category FROM ". _DB_PREFIX_ ."category_product WHERE id_product = $id_product";
            if ($sql = Db::getInstance()->executeS($sql)) {
                foreach ($sql as $v) {
                    if (isset(SELF::$CATEGORIES[$v['id_category']])) {
                        $result[] = SELF::$CATEGORIES[$v['id_category']];
                    }
                }
            }
            return $result;
        }
        return SELF::$CATEGORIES;
    }
    public static function getExcludedCategories()
    {
        $result = self::$EXCLUDE;
        if (count(self::$EXCLUDE)) {
            foreach (self::$EXCLUDE as $k => $v) {
                if ($children = self::getCategoriesChildrenId($v)) {
                    $result = array_merge($result, $children);
                }
            }
        }
        return $result;
    }
    public static function getFeatures($id_product) : array
    {
        $sql = "
            SELECT pl.name, pvl.value
            FROM ". _DB_PREFIX_ ."feature_product pf
            LEFT JOIN ". _DB_PREFIX_ ."feature_lang pl
            ON pl.id_feature = pf.id_feature AND pl.id_lang = ". self::$ID_LANG ."
            LEFT JOIN ". _DB_PREFIX_ ."feature_value_lang pvl
            ON pf.id_feature_value = pvl.id_feature_value AND pvl.id_lang = ". self::$ID_LANG ."
            WHERE pf.id_product = $id_product
        ";
        $result = [];
        if ($features = DB::getInstance()->executeS($sql)) {
            foreach ($features as $k => $v) {
                $result[$v['name']] = $v['value'];
            }
        }
        return $result;
    }
    public static function getAttributes($id_product)
    {
        $result = [];
        $sql = "
            SELECT * FROM ". _DB_PREFIX_ ."product_attribute WHERE id_product = $id_product
            ORDER BY default_on
        ";
        $attributes = Db::getInstance()->executeS($sql);
        foreach ($attributes as $k => $v) {
            $sql = "
                SELECT agl.public_name as gname, al.name as aname
                FROM ". _DB_PREFIX_."product_attribute_combination pac
                LEFT JOIN ". _DB_PREFIX_."attribute a
                ON pac.id_attribute = a.id_attribute
                LEFT JOIN ". _DB_PREFIX_."attribute_group ag
                ON a.id_attribute_group = ag.id_attribute_group
                LEFT JOIN ". _DB_PREFIX_."attribute_group_lang agl
                ON agl.id_attribute_group = ag.id_attribute_group AND agl.id_lang = '". self::$ID_LANG ."'
                LEFT JOIN ". _DB_PREFIX_."attribute_lang al
                ON a.id_attribute = al.id_attribute AND al.id_lang = '". self::$ID_LANG ."'
                WHERE pac.id_product_attribute = '". $v['id_product_attribute'] ."'
            ";
            $sql = Db::getInstance()->executeS($sql);
            $a = [];
            foreach ($sql as $attribute) {
                $a[$attribute['gname']] = $attribute['aname'];
            }
            $result[] = [
                'price' => $v['price'],
                'weight' => $v['reference'],
                'attributes' => $a
            ];
        }
        return $result;
    }
    public static function getSuppliers($id_product)
    {
        $result = [];
        $sql = "
            SELECT sl.*, s.name
            FROM ". _DB_PREFIX_ ."product_supplier ps
            LEFT JOIN ". _DB_PREFIX_ ."supplier s
            ON s.id_supplier = ps.id_supplier
            LEFT JOIN ". _DB_PREFIX_ ."supplier_lang sl
            ON s.id_supplier = sl.id_supplier AND sl.id_lang = ". self::$ID_LANG ."
            WHERE ps.id_product = $id_product
        ";
        return Db::getInstance()->executeS($sql) ?: [];
    }
    public static function getProducts()
    {
        $result = [];
        $sql = "
            SELECT
                p.id_product,
                p.reference,
                pl.name,
                pl.description_short,
                pl.description,
                pl.meta_title,
                pl.meta_keywords,
                pl.meta_description,
                pl.link_rewrite
            FROM ". _DB_PREFIX_ ."product p
            LEFT JOIN ". _DB_PREFIX_ ."product_lang pl
            ON p.id_product = pl.id_product AND pl.id_lang = ". self::$ID_LANG ."
        ";
        if (count(self::$EXCLUDE)) {
            $exclude = self::getExcludedCategories();
            $sql .= "
            WHERE p.id_product
            NOT IN(
                SELECT id_product
                FROM ". _DB_PREFIX_ ."category_product
                WHERE id_category IN(". implode(', ', $exclude) .")
            )";
        }
        $sql .= " LIMIT 10";
        $products = DB::getInstance()->executeS($sql);
        foreach ($products as $k => $v) {
            $v['categories'] = self::getCategories($v['id_product']);
            $v['attributes'] = self::getAttributes($v['id_product']);
            $v['features'] = self::getFeatures($v['id_product']);
            $v['suppliers'] = self::getSuppliers($v['id_product']);
            $result[] = $v;
        }
        return $result;
    }

    /**
     *
     * IMPORT
     * */
    public static function importCategory($category, $id_parent = 2)
    {
        $new = new Category(self::categoryExist($category['name'], $id_parent));
        if (!$new->id) {
            foreach (self::getLanguages() as $v) {
                $new->name[$v['id_lang']] = $category['name'];
                $new->description[$v['id_lang']] = $category['description'];
                $new->link_rewrite[$v['id_lang']] = $category['link_rewrite'];
                $new->meta_title[$v['id_lang']] = $category['meta_title'];
                $new->meta_keywords[$v['id_lang']] = $category['meta_keywords'];
                $new->meta_description[$v['id_lang']] = $category['meta_description'];
            }
            $new->id_parent = $id_parent;
            $new->save();
        }
        if ($category['children']) {
            foreach ($category['children'] as $v) {
                self::importCategory($v, $new->id);
            }
        }
    }
    public static function categoryExist($name, $id_parent = 2)
    {
        if ($id_category = (int) Db::getInstance()->getValue("
            SELECT cl.*
            FROM ". _DB_PREFIX_ ."category c
            LEFT JOIN ". _DB_PREFIX_ ."category_lang cl
            ON cl.id_category = c.id_category
            WHERE
                id_parent = $id_parent
            AND name = '". pSQL($name) ."'
        ")) {
            return $id_category;
        } else {
            $category = new Category;
            foreach (Language::getLanguages() as $k => $v) {
                $category -> name[$v['id_lang']] = $name;
                $category -> link_rewrite[$v['id_lang']] = Tools::str2url($name);
            }
            $category -> id_parent = $id_parent;
            $category -> save();
            return $category->id;
        }
    }
    public static function getLanguages()
    {
        if (self::$LANGUAGES == false) {
            self::$LANGUAGES = Language::getLanguages();
        }
        return self::$LANGUAGES;
    }
    public static function productExists($reference)
    {
        return Db::getInstance()->getValue("
            SELECT id_product
            FROM ". _DB_PREFIX_ ."product
            WHERE reference = '". $reference ."'
        ");
    }
    public static function importProduct($v, $extra = [])
    {
        if (self::$importWithZeroQuantity === false && !$v['quantity']) {
            return;
        }
        if (self::$importWithZeroPrice === false && !$v['price']) {
            return;
        }
        if (self::productExists($v['reference']) && self::$updateProducts === false) {
            return;
        }
        if (!trim($v['name']) || !trim($v['reference'])) {
            return;
        }

        $product = new Product(Db::getInstance()->getValue("
            SELECT id_product
            FROM ". _DB_PREFIX_ ."product
            WHERE reference = '". pSQL($v['reference']) ."'
        "));
        $id_product = $product->id;
        /**/
        if (!$product -> id) {
            $v['categories'] = ['To Assign'];
        }
        if (!$id_product || ($product -> id && self::$updateCategories === true)) {
            $categories = self::importGetCategoriesIds($v);
            $product -> id_category_default = end($categories) ?: 0;
        }
        if (!$id_product || ($product->id && self::$updateDetails === true)) {
            $langs = self::getLanguages();
            if (count($langs) > 1) {
                foreach ($langs as $vl) {
                    $product->name[$vl['id_lang']] = trim($v['name']);
                    $product->description[$vl['id_lang']] = $v['description'];
                    $product->description_short[$vl['id_lang']] = $v['description_short'];
                    $product->link_rewrite[$vl['id_lang']] = $v['link_rewrite'];
                    $product->meta_title[$vl['id_lang']] = $v['meta_title'];
                    $product->meta_keywords[$vl['id_lang']] = $v['meta_keywords'];
                    $product->meta_description[$vl['id_lang']] = $v['meta_description'];
                }
            } else {
                $product->name = trim($v['name']);
                $product->description = $v['description'];
                $product->description_short = $v['description_short'];
                $product->link_rewrite = $v['link_rewrite'];
                $product->meta_title = $v['meta_title'];
                $product->meta_keywords = $v['meta_keywords'];
                $product->meta_description = $v['meta_description'];
            }
            $product -> reference = $v['reference'];
            $product -> ean13 = ctype_digit($v['ean']) && strlen($v['ean']) == 13 ? $v['ean'] : '';
        }
        if (!$product -> id || ($product -> id && self::$updatePrice === true)) {
            $product -> price = $v['price'];
        }
        $product -> active = $v['quantity'] > 0 ? 1 : 0;
        try {
            if (!$product -> save()) {
              //  throw new Exception('nie udało się zapisać produktu');
            }
        } catch (Exception $e) {

        }
        if (!$id_product || ($product -> id && self::$updateImages === true)) {
            if (self::$updateImagesSkipping === true) {
                if (self::hasProductImages($product->id)) {
                    self::importImages($product, $v);
                }
            } else {
                self::importImages($product, $v);
            }
        }
        if (!$id_product || ($product -> id && self::$updateAttributes === true)) {
            self::importAttributes($product, $v);
        }
        self::clearProductPrice($product);
        if (!$id_product || ($product -> id && self::$updatePrice === true)) {
            self::importSpecificPrice($product, $v);
        }
        if (!$id_product || ($product -> id && self::$updateFeatures === true)) {
            self::importFeatures($product, $v);
        }
        if (!$id_product || ($product -> id && self::$updateSuppliers === true)) {
            $suppliers = self::importGetSuppliersIds($v);
            self::importSuppliers($product, $v);
        } else {
            /** Add product with supplier to array */
            foreach (self::getSuppliers($product->id) as $supplier) {
                if (!isset(self::$PRODUCTS_IMPORTED[$supplier['id_supplier']])) {
                    self::$PRODUCTS_IMPORTED[$supplier['id_supplier']] = [];
                }
                self::$PRODUCTS_IMPORTED[$supplier['id_supplier']][] = $product->id;
            }
        }
        if (!$id_product || ($product -> id && self::$updateCategories === true)) {
            self::addToCategories($product->id, $categories);
        }
        if (!$id_product || ($product -> id && self::$updateQuantity == true)) {
            $product->quantity = (int) $v['quantity'];
            StockAvailable::setQuantity($product->id, 0, $v['quantity']);
            if ($v['quantity'] > 1) {
                $product->active = 1;
                $product->save();
            } elseif ($v['quantity'] < 1) {
                $product->active = 0;
                $product->save();
            }
        }
        if ($product->price == 0 || isset($extra['disable'])) {
            $product -> active = 0;
            $product -> save();
        }

        self::fixIdCategoryDefault($product);
        if (self::$displayImportingProduct === true) {
            echo 'Product '. $product -> reference .' '. $v['name'].' added successfully.<br>'."\n";
        } else {
            echo '.';
        }
        return true;
    }
    public static function addToCategories($id_product, $categories)
    {
        Db::getInstance()->execute("
            DELETE FROM ". _DB_PREFIX_ ."category_product WHERE id_product = '".$id_product."'
        ");
        foreach ($categories as $k => $v) {
            $position = (int) Db::getInstance()->getValue("
                SELECT position FROM ". _DB_PREFIX_ ."category_product WHERE id_category = '".$v."'
            ");
            $position++;
            Db::getInstance()->execute("
                INSERT IGNORE INTO ". _DB_PREFIX_ ."category_product (id_product, id_category, position) VALUES ('".$id_product."', '". $v ."', '". $position."')
            ");
        }
    }
    public static function fixIdCategoryDefault(Product $product)
    {
        if (!Db::getInstance()->getValue("
                SELECT id_category FROM ". _DB_PREFIX_ ."category_product
                WHERE id_product = '". $product -> id ."'
                AND id_category = '". $product -> id_category_default ."'
            ")
        ) {
            $product->id_category_default = Db::getInstance()->getValue("
                SELECT id_category FROM ". _DB_PREFIX_ ."category_product WHERE id_product = '". $product -> id ."'
                ORDER BY id_category DESC
            ");
            $product->save();
        }
        return $product;
    }
    public static function clearProductPrice(Product $product)
    {
        return Db::getInstance()->executeS("
            SELECT id_specific_price
            FROM ". _DB_PREFIX_ ."specific_price
            WHERE id_product = ". $product -> id ."
        ");
    }
    public static function importSpecificPrice(Product $product, $v)
    {
        $sql = Db::getInstance()->executeS("
            SELECT id_specific_price
            FROM ". _DB_PREFIX_ ."specific_price
            WHERE id_product = ". $product -> id ."
        ");
        if ($sql) {
            foreach ($sql as $k => $v) {
                $p = new SpecificPrice($v['id_specific_price']);
                $p -> delete();
            }
        }
        if (!isset($v['specific_price'])) {
            return;
        }

        $spExclude = [
            'id_specific_price',
            'id_specific_price_rule',
            'id_cart',
            'id_specific_price',
        ];
        foreach ($v['specific_price'] as $pr) {
            if (isset(self::$mapAttributes[$pr['id_product_attribute']])) {
                $pr['id_product_attribute'] = self::$mapAttributes[$pr['id_product_attribute']];
            }
            $price = new SpecificPrice;
            foreach ($pr as $k => $p) {
                if ($k == 'id_specific_price') {
                    continue;
                }
                $price -> $k = $p;
            }
            $price -> id_product = $product -> id;
            try {
                $price -> save();
            } catch (Exception $e) {

            }
        }
    }
    public static function hasProductImages($id_product)
    {
        return Db::getInstance()->getValue("
            SELECT id_image FROM ". _DB_PREFIX_ ."image WHERE id_product = '". $id_product ."'
        ");
    }
    public static function importFeatures(Product $product, $v, $delete = true)
    {
        if ($delete === true) {
            Db::getInstance()->execute("
                DELETE FROM "._DB_PREFIX_."feature_product
                WHERE id_product = ". $product -> id ."
            ");
        }
        /** clear unused feature values */
      /*  $features = Db::getInstance()->executeS("
            SELECT id_feature_value
            FROM ". _DB_PREFIX_ ."feature_value
            WHERE id_feature_value NOT IN(
                SELECT id_feature_value FROM ". _DB_PREFIX_ ."feature_product
            )
        ");
        if ($features && count($features)) {
            foreach ($features as $f) {
                $feature = new FeatureValue($f['id_feature_value']);
                $feature->delete();
            }
        }*/
        foreach ($v['features'] as $feature_name =>  $value) {
            if (!$value || !trim($value) || !trim($feature_name)) {
                continue;
            }
            $id_feature = self::addFeature($feature_name);
            $id_feature_value = self::addFeatureValue($id_feature, $value);
            Product::addFeatureProductImport($product -> id, $id_feature, $id_feature_value);
        }
    }
    public static function addFeature($feature_name)
    {

  //      $feature_name = preg_replace("/[^a-zA-Z0-9 \/\-]+/", "", $feature_name);
        if (isset(self::$FEATURES_IMPORTED[$feature_name])) {
            return self::$FEATURES_IMPORTED[$feature_name];
        }
        $sql = "
            SELECT id_feature
            FROM ". _DB_PREFIX_ ."feature_lang
            WHERE name LIKE '". $feature_name."'
        ";
        if ($result = Db::getInstance()->getValue($sql)) {
            return $result;
        }
        $feature = new Feature;
        $l = self::getLanguages();
        foreach (self::getLanguages() as $k => $v) {
            $feature -> name[$v['id_lang']] = $feature_name;
        }
        $feature -> position = 1;
        $feature -> save();
        self::$FEATURES_IMPORTED[$feature_name] = $feature->id;
        $feature -> position = $feature -> id;
        $feature -> save();
        return $feature -> id;
    }
    public static function addFeatureValue($id_feature, $feature_value)
    {
        $feature_value = str_replace("/", " ", $feature_value);
  //      $feature_value = preg_replace("/[^a-zA-Z0-9 \-\/]+/", "", $feature_value);
        $feature_value = trim($feature_value);
        $values = Db::getInstance()->executeS("SELECT id_feature_value FROM ". _DB_PREFIX_ ."feature_value_lang WHERE value = '". $feature_value ."'");
        $value = false;
        foreach ($values as $k => $v) {
            $fv = new FeatureValue((int) $v['id_feature_value']);
            if ($fv->custom == 0 && $fv->id_feature == $id_feature) {
                return $fv->id;
            }
        }
        $value = new FeatureValue();
        $value -> id_feature = $id_feature;
        foreach (self::getLanguages() as $k => $v) {
            $value -> value[$v['id_lang']] = $feature_value;
        }
        $value -> custom = 0;
        $value -> save();
        SELF::$FEATURES_VALUES_IMPORTED[] = $value;
        return $value -> id;
    }
    public static function getAttributeGroupId($name)
    {
        $db = Db::getInstance()->getValue("
            SELECT id_attribute_group
            FROM ". _DB_PREFIX_ ."attribute_group_lang
            WHERE name = '" . pSQL($name) ."'
        ");
        if ($db) {
            return $db;
        }
        $attribute = new AttributeGroup;
        foreach (Language::getLanguages() as $k => $v) {
            $attribute->name[$v['id_lang']] = $name;
            $attribute->public_name[$v['id_lang']] = $name;
        }
        $attribute->group_type = 'select';
        $attribute->save();
        return $attribute -> id;
    }
    public static function getAttributeId($name, $id_attribute_group)
    {
        $query = "
            SELECT a.id_attribute
            FROM ". _DB_PREFIX_ ."attribute a
            LEFT JOIN ". _DB_PREFIX_ ."attribute_lang al
            ON a.id_attribute = al.id_attribute
            WHERE a.id_attribute_group = '". $id_attribute_group ."'
            AND al.name = '". pSQL($name) ."'
        ";
        $db = Db::getInstance()->getValue($query);
        if ($db) {
            return $db;
        }
        $attribute = new Attribute;
        $attribute->id_attribute_group = $id_attribute_group;
        foreach (Language::getLanguages() as $k => $v) {
            $attribute->name[$v['id_lang']] = $name;
        }
        $attribute -> position = (int) Db::getInstance()->getValue("
            SELECT MAX(position)
            FROM ". _DB_PREFIX_ ."attribute
            WHERE id_attribute_group = '". $id_attribute_group ."'
        ");
        $attribute->position++;
        $attribute -> save();
        return $attribute->id;
        /**/
        header('Content-Type:application/json');
        echo json_encode($attribute);
        exit;
        /**/
    }
    public static function deleteAttributes($id_product)
    {
        $sql = Db::getInstance()->executeS("
            SELECT id_product_attribute
            FROM ". _DB_PREFIX_ ."product_attribute
            WHERE id_product = ". (int) $id_product ."
        ");
        foreach ($sql as $k => $v) {
            $combination = new Combination($v['id_product_attribute']);
            $combination -> delete();
        }
        return;
    }
    public static function importAttributes(Product $product, $v, $delete = true)
    {
        if (!isset($v['attributes'])) {
            return;
        }
        self::deleteAttributes($product->id);
        foreach ($v['attributes'] as $k => $v) {

            $attributes = [];
            foreach ($v['attributes'] as $attribute_group => $attribute) {
                $id_attribute_group = self::getAttributeGroupId($attribute_group);
                $attributes[] = self::getAttributeId($attribute, $id_attribute_group);
            }
            $combination = new Combination;
            $combination -> id_product = $product->id;
            $combination -> weight = $v['weight'];
            $combination -> price = $v['price'];
            $combination -> reference = $v['reference'];
            $combination -> save();
            $combination -> setAttributes($attributes);
            self::$mapAttributes[$v['id']] = $combination->id;
        }
    }
    public static function importSuppliers(Product $product, $v)
    {
        Db::getInstance()->execute("
            DELETE FROM ". _DB_PREFIX_."product_supplier
            WHERE id_product = '". $id_product ."'
        ");
        $product -> id_supplier = null;
        foreach ($v['suppliers'] as $supplier) {
            if (!$id_supplier = Db::getInstance()->getValue("
                SELECT id_supplier FROM ". _DB_PREFIX_ ."supplier
                WHERE name = '". $supplier ."'
            ")) {
                $s = new Supplier;
                $s -> name = $supplier;
                $s -> active = 1;
                $s -> save();
                $id_supplier = $s->id;
            }
            $product->addSupplierReference($id_supplier, 0);
            if (!$product-> id_supplier) {
                $product->id_supplier = $id_supplier;
                $product->save();
            }
            if (!isset(self::$PRODUCTS_IMPORTED[$id_supplier])) {
                self::$PRODUCTS_IMPORTED[$id_supplier] = [];
            }
            self::$PRODUCTS_IMPORTED[$id_supplier][] = $product->id;
        }
    }
    public static function importGetCategoriesIds($v)
    {
        $result = [];
        foreach ($v['categories'] as $v) {
            $tree = explode('//', $v);
            $id_parent = self::$IN_CATEGORY;
            foreach ($tree as $child) {
                if (trim($child)) {
                    $id_parent = self::categoryExist($child, $id_parent);
                }
            }
            if ($id_parent) {
                $result[] = $id_parent;
            }
        }
        return $result;
    }
    public static function importGetSuppliersIds($v)
    {
        if (count($v['suppliers'])) {

        }
        return [];
    }
    public static function importImages(Product $product, $v)
    {
        if (!isset($v['images'])) {
            return;
        }
        $ids = Db::getInstance()->executeS("SELECT id_image FROM ". _DB_PREFIX_ ."image WHERE id_product = '". $product -> id ."'");
        foreach ($ids as $i) {
            $image = new Image($i['id_image']);
            $image->delete();
        }
        Db::getInstance()->execute("
            DELETE FROM "._DB_PREFIX_."image
            WHERE id_product = ". $product -> id ."
        ");
        Db::getInstance()->execute("
            DELETE FROM "._DB_PREFIX_."image_shop
            WHERE id_product = ". $product -> id ."
        ");
        $covered = false;
        $images = $v['images'];
        foreach ($images as $img) {
            if (preg_match('/\@/', $img)) {
                $imurl = $img;
                $x = explode('//', $imurl, 2);
                $protocol = array_shift($x);
                $url = explode('@', array_shift($x), 2);
                $auth = array_shift($url);
                $auth = base64_encode($auth);
                $context = stream_context_create([
                    "http" => [
                        "header" => "Authorization: Basic $auth"
                    ]
                ]);
                $temp_file = tempnam(sys_get_temp_dir(), 'IMAGE-MIGRATE');
                $im = file_get_contents($protocol. '//'. array_shift($url), false, $context);
                file_put_contents($temp_file, $im);
                $img = $temp_file;
            }
            $image = new Image();
            $image->id_product = (int) $product->id;
            $image->position = Image::getHighestPosition($product->id) + 1;
            if ($covered === false) {
                $image->cover = $covered = true;
            }
            $image->add();
            if (!self::copyImg($product->id, $image->id, $img, 'products', !Tools::getValue('regenerate'))) {
                $image->delete();
            }
        }
    }
    public static function copyImg($id_entity, $id_image, $url, $entity = 'products', $regenerate = true)
    {
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));
        switch ($entity) {
            default:
            case 'products':
                $image_obj = new Image($id_image);
                $path = $image_obj->getPathForCreation();
                break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_ . (int) $id_entity;
                break;
            case 'manufacturers':
                $path = _PS_MANU_IMG_DIR_ . (int) $id_entity;
                break;
            case 'suppliers':
                $path = _PS_SUPP_IMG_DIR_ . (int) $id_entity;
                break;
        }
        $url = str_replace(' ', '%20', trim($url));


        // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
      /*  if (!ImageManager::checkImageMemoryLimit($url))
            return false;*/

        // 'file_exists' doesn't work on distant file, and getimagesize makes the import slower.
        // Just hide the warning, the processing will be the same.
        if (Tools::copy($url, $tmpfile)) {
            ImageManager::resize($tmpfile, $path . '.jpg');
            $images_types = ImageType::getImagesTypes($entity);
            if ($regenerate)
                foreach ($images_types as $image_type) {
                    ImageManager::resize($tmpfile, $path . '-' . stripslashes($image_type['name']) . '.jpg', $image_type['width'], $image_type['height']);
                    if (in_array($image_type['id_image_type'], $watermark_types))
                        Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
                }
        }
        else {
            unlink($tmpfile);
            return false;
        }
        unlink($tmpfile);
        return true;
    }
    public static function getProductsToDisable()
    {
        $disable = [];
        foreach (array_keys(self::$PRODUCTS_IMPORTED) as $v) {
            $products = Db::getInstance()->executeS("
                SELECT
                    id_product
                FROM
                    ". _DB_PREFIX_ ."product_supplier
                WHERE
                    id_supplier = '". $v ."'
            ");
            foreach ($products as $row) {
                if (!in_array($row['id_product'], self::$PRODUCTS_IMPORTED[$v])) {
                    $disable[] = $row['id_product'];
                }
            }
        }
        return $disable;
    }
    public static function disableMissingProducts()
    {
        foreach (self::getProductsToDisable() as $id_product) {
            $product = new Product((int) $id_product);
            if (!$product->id) {
                continue;
            }
            if ($product -> active == 1) {
                $product -> active = 0;
                $product -> save();
                self::insertLog($product, 'Product disabled cuz its missing in files.');
                echo 'Product '. $product -> reference .' has been disabled.<br>'."\n";
            }
        }
    }
    public static function insertLog($product, $log)
    {
        $date = date('Y-m-d H:i:s');
        $employee = 12;
        $message = $product -> reference .' ( '. $product -> id .' ): '. $log;
        return Db::getInstance()->execute("
            INSERT INTO ". _DB_PREFIX_ ."log (
                `id_log`,
                `severity`,
                `error_code`,
                `message`,
                `object_type`,
                `object_id`,
                `id_employee`,
                `date_add`,
                `date_upd`
            ) VALUES (
                NULL,
                '1',
                '0',
                '". pSQL($message) ."',
                'Product',
                '". $product -> id ."',
                '$employee',
                '$date',
                '$date'
            )
        ");
    }
}
