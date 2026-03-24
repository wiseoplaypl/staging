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

class HsProduct extends AbstractHsObjectModel
{
    protected static $objectType = 'PRODUCT';
    protected static $psObjectClass = 'Product';
    protected static $protectedObjectModelKeys = ['description', 'description_short', 'last_passwd_gen'];
    protected static $standardObjectModelKeys = [];

    public function getPsObjectModel()
    {
        return $this->psObjectModel = new \Product($this->id, true, Context::getContext()->language->id, Context::getContext()->shop->id);
    }

    public function build()
    {
        // $data = $this->psObjectModel->validateFields($die = false) ? $this->psObjectModel->getFields() : array();
        // $data = json_decode(json_encode($this->psObjectModel), true);
        // $data = array_filter(
        //     json_decode(json_encode($this->psObjectModel), true),
        //     function ($item) {
        //         return is_numeric($item) || is_string($item);
        //     }
        // );
        $link = new \Link();
        $img = $this->psObjectModel->getCover($this->psObjectModel->id);
        $data = [
            'url' => $link->getProductLink($this->psObjectModel),
            'product_description' => strip_tags($this->psObjectModel->description_short),
            'id_product' => $this->psObjectModel->id,
            'manufacturer' => $this->psObjectModel->manufacturer_name ?: '', // Manufacturer add
            'product_name' => strip_tags($this->psObjectModel->name),
            'sku' => strip_tags($this->psObjectModel->reference) . '/' . $this->psObjectModel->id,
            // (\Configuration::get('PS_HUBSPOT_UNIQUE_PROD_REF')?'':'/'.$this->psObjectModel->id),
            'price' => $this->psObjectModel->price,
            'hs_price_usd' => $this->psObjectModel->price,
            'hs_price_eur' => $this->psObjectModel->price,
            'stock_quantity' => StockAvailable::getQuantityAvailableByProduct($this->psObjectModel->id),
        ];

        if (isset($img) && !empty($img)) {
            $formatNameFunction = is_callable("\ImageType", 'getFormattedName') ? 'getFormattedName' : 'getFormatedName';
            $data['image_url'] = Context::getContext()->link->getImageLink($this->psObjectModel->link_rewrite, (int) $img['id_image'], \ImageType::$formatNameFunction('medium'));

            $getProductImages = $this->getPsObjectModel()->getImages(Context::getContext()->language->id);
            $productImages = [];
            if (!empty($getProductImages)) {
                foreach ($getProductImages as $key => $value) {
                    array_push($productImages, Context::getContext()->link->getImageLink($this->psObjectModel->link_rewrite, (int) $value['id_image'], \ImageType::$formatNameFunction('medium')));
                }
                $data['images'] = implode(',', $productImages);
            }
        }

        $productCategory = new Category($this->psObjectModel->id_category_default);
        if ($productCategory) {
            $data['main_category'] = $productCategory->getName();
        }
        $data['categories'] = $this->ps_getProductCategories();

        // HsProduct::removeProtectedKeys($data, self::$protectedObjectModelKeys);
        // HsProduct::translateKeys($data, self::$standardObjectModelKeys, $this->psObjectModel);
        return $data;
    }

    private function ps_getProductCategories($separator = ', ')
    {
        $productCategories = $this->psObjectModel->getProductCategoriesFull($this->psObjectModel->id);

        if (is_array($productCategories) && !empty($productCategories)) {
            $nameFromCategories = array_column($productCategories, 'name');
            if (!empty($nameFromCategories)) {
                return implode($separator, $nameFromCategories);
            }
        }

        return '';
    }

    public function dontSyncThis($forceSync = false)
    {
        if (parent::dontSyncThis()) {
            return true;
        }

        return false;
    }

    public static function getMigratedTotal()
    {
        return (int) Configuration::get('PS_HUBSPOT_MIGRATION_PRODUCTS', '', '', 1);
    }

    protected static function updateMigratedTotal($add)
    {
        Configuration::updateValue(
            'PS_HUBSPOT_MIGRATION_PRODUCTS',
            (int) Configuration::get('PS_HUBSPOT_MIGRATION_PRODUCTS', '', '', 1) + (int) $add
        );
    }

    public static function applyMigrationFilters($sql)
    {
        if (Configuration::get('PS_HUBSPOT_MIGRATION_UPDATED_LEGACY', '', '', '')) {
            $sql .= ' p join ' . _DB_PREFIX_ . static::$definition['table'] . ' t on p.' . static::getObjectClassKey() . '=t.id ';
        }

        $sql .= ' WHERE  date_add < "' . Configuration::get('PS_HUBSPOT_MIGRATION_DATE_END', '', '', '') . '" AND active = 1 ';

        if (Configuration::get('PS_HUBSPOT_MIGRATION_UPDATED_LEGACY', '', '', '')) {
            $sql .= ' AND sync_as<>"--" ';
        }

        return $sql;
    }

    // //////////////
    public static $definition = [
        'table' => 'tiralineas_hs_product',
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
