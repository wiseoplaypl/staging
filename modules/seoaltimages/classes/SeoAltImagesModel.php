<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class SeoAltImagesModel extends ObjectModel
{
    public $id_seoaltimages;
    public $title;
    public $status;
    public $rule;
    public $type;
    
    public static $definition = array(
        'table' => 'seoaltimages',
        'primary' => 'id_seoaltimages',
        'multilang' => false,
        'fields' => array(
            'id_seoaltimages' => array('type' => self::TYPE_INT),
            'status' => array('type' => self::TYPE_BOOL),
            'title' => array('type' => self::TYPE_STRING),
            'rule' => array('type' => self::TYPE_STRING),
            'type' => array('type' => self::TYPE_INT),
        ),
    );
    
    public static function saveShops($id, $shops)
    {
        $store_shops = Shop::getShops(true, null, false);
        if ($shops[0] == 0) {
            foreach ($store_shops as $shop) {
                Db::getInstance()->insert('seoaltimages_shop',
                array(
                    'id_seoaltimages' => (int)$id,
                    'id_shop' => (int)$shop['id_shop'])
                );
            }
        }
        else {
            foreach ($shops as $shop) {
                Db::getInstance()->insert('seoaltimages_shop',
                array(
                    'id_seoaltimages' => (int)$id,
                    'id_shop' => (int)$shop)
                );
            }
        }
    }
    
    public static function saveCategories($id, $categories)
    {
        foreach ($categories as $category) {
            Db::getInstance()->insert('seoaltimages_categories',
            array(
                'id_seoaltimages' => (int)$id,
                'id_category' => (int)$category)
            );
        }
    }
    
    public static function saveProducts($id, $products)
    {
        foreach ($products as $product) {
            Db::getInstance()->insert('seoaltimages_products',
            array(
                'id_seoaltimages' => (int)$id,
                'id_product' => (int)$product)
            );
        }
    }
    
    public static function getRelativeProducts($id)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $products = Db::getInstance()->executeS('
		SELECT `id_product`
		FROM `'._DB_PREFIX_.'seoaltimages_products`
		WHERE `id_seoaltimages` = '.(int)$id);
        if (!empty($products)) {
            foreach ($products as &$product) {
                $product = new Product((int)$product['id_product'], true, (int)$id_lang);
                $product->id_product_attribute = (int)Product::getDefaultAttribute($product->id) > 0 ? (int)Product::getDefaultAttribute($product->id) : 0;
                $_cover = ((int)$product->id_product_attribute > 0) ? Product::getCombinationImageById((int)$product->id_product_attribute, $id_lang) : Product::getCover($product->id);
                if (!is_array($_cover)) {
                   $_cover = Product::getCover($product->id);
                }
                $product->id_image = $_cover['id_image'];
            }
        }
        return $products;
    }
    
    public static function getRelativeShops($id)
    {
        $shops = Db::getInstance()->executeS('
		SELECT `id_shop`
		FROM `'._DB_PREFIX_.'seoaltimages_shop`
		WHERE `id_seoaltimages` = '.(int)$id);
        $new_array = array();
		foreach ($shops as $key => $value) {
			$new_array[$key] = $value['id_shop'];
		}
		return $new_array;
    }
    
    public static function getRelativeCategories($id)
    {
        $categories = Db::getInstance()->executeS('
		SELECT `id_category`
		FROM `'._DB_PREFIX_.'seoaltimages_categories`
		WHERE `id_seoaltimages` = '.(int)$id);
        $new_array = array();
		foreach ($categories as $key => $value) {
			$new_array[$key] = $value['id_category'];
		}
		return $new_array;
    }
    
    public static function saveLangs($id, $languages)
    {
        $store_languages = Language::getLanguages();
        if ($languages[0] == 0 || empty($languages)) {
            foreach ($store_languages as $lang) {
                Db::getInstance()->insert('seoaltimages_lang',
                array(
                    'id_seoaltimages' => (int)$id,
                    'id_lang' => (int)$lang['id_lang'])
                );
            }
        }
        else {
            foreach ($languages as $lang) {
                Db::getInstance()->insert('seoaltimages_lang',
                array(
                    'id_seoaltimages' => (int)$id,
                    'id_lang' => (int)$lang)
                );
            }
        }
    }
    
    public static function getRelativeLangs($id)
    {
        $shops = Db::getInstance()->executeS('
		SELECT `id_lang`
		FROM `'._DB_PREFIX_.'seoaltimages_lang`
		WHERE `id_seoaltimages` = '.(int)$id);
        $new_array = array();
		foreach ($shops as $key => $value) {
			$new_array[$key] = $value['id_lang'];
		}
		return $new_array;
    }
    
    public static function getActiveRulesCount($id_shop, $id_lang)
    {
        $result = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(a.`id_seoaltimages`) AS count
		FROM `'._DB_PREFIX_.'seoaltimages` a
        LEFT JOIN `'._DB_PREFIX_.'seoaltimages_shop` s ON s.`id_seoaltimages` = a.`id_seoaltimages`
        LEFT JOIN `'._DB_PREFIX_.'seoaltimages_lang` l ON l.`id_seoaltimages` = a.`id_seoaltimages`
		WHERE s.`id_shop` = '.(int)$id_shop.'
		AND l.`id_lang` = '.(int)$id_lang.'
        AND a.`status` < 1');
        return $result;
    }
    
    public static function getRuleGlobal($id_shop, $id_lang)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT a.`rule`
		FROM `'._DB_PREFIX_.'seoaltimages` a
        LEFT JOIN `'._DB_PREFIX_.'seoaltimages_shop` s ON s.`id_seoaltimages` = a.`id_seoaltimages`
        LEFT JOIN `'._DB_PREFIX_.'seoaltimages_lang` l ON l.`id_seoaltimages` = a.`id_seoaltimages`
		WHERE s.`id_shop` = '.(int)$id_shop.'
		AND l.`id_lang` = '.(int)$id_lang.'
        AND a.`type` < 1
        AND a.`status` < 1');
        return $result;
    }
    
    public static function getSingleProductRule($id_shop, $id_lang, $id_product)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT a.`rule`
		FROM `'._DB_PREFIX_.'seoaltimages` a
        LEFT JOIN `'._DB_PREFIX_.'seoaltimages_shop` s ON s.`id_seoaltimages` = a.`id_seoaltimages`
        LEFT JOIN `'._DB_PREFIX_.'seoaltimages_lang` l ON l.`id_seoaltimages` = a.`id_seoaltimages`
        LEFT JOIN `'._DB_PREFIX_.'seoaltimages_products` p ON p.`id_seoaltimages` = a.`id_seoaltimages`
		WHERE s.`id_shop` = '.(int)$id_shop.'
		AND l.`id_lang` = '.(int)$id_lang.'
        AND a.`type` = 2
        AND a.`status` < 1
        AND p.`id_product` = '.(int)$id_product);
        return $result;
    }
    
    public static function getCategoriesRuleCount($id_shop, $id_lang)
    {
        $result = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(a.`id_seoaltimages`) AS count
		FROM `'._DB_PREFIX_.'seoaltimages` a
        LEFT JOIN `'._DB_PREFIX_.'seoaltimages_shop` s ON s.`id_seoaltimages` = a.`id_seoaltimages`
        LEFT JOIN `'._DB_PREFIX_.'seoaltimages_lang` l ON l.`id_seoaltimages` = a.`id_seoaltimages`
        LEFT JOIN `'._DB_PREFIX_.'seoaltimages_categories` c ON c.`id_seoaltimages` = a.`id_seoaltimages`
		WHERE s.`id_shop` = '.(int)$id_shop.'
		AND l.`id_lang` = '.(int)$id_lang.'
        AND a.`type` = 1
        AND a.`status` < 1');
        return $result;
    }
    
    public static function getCategoriesRule($id_shop, $id_lang, $id_category)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT a.`rule`
		FROM `'._DB_PREFIX_.'seoaltimages` a
        LEFT JOIN `'._DB_PREFIX_.'seoaltimages_shop` s ON s.`id_seoaltimages` = a.`id_seoaltimages`
        LEFT JOIN `'._DB_PREFIX_.'seoaltimages_lang` l ON l.`id_seoaltimages` = a.`id_seoaltimages`
        LEFT JOIN `'._DB_PREFIX_.'seoaltimages_categories` c ON c.`id_seoaltimages` = a.`id_seoaltimages`
		WHERE s.`id_shop` = '.(int)$id_shop.'
		AND l.`id_lang` = '.(int)$id_lang.'
        AND a.`type` = 1
        AND a.`status` < 1
        AND c.`id_category` = '.(int)$id_category);
        return $result;
    }
}
