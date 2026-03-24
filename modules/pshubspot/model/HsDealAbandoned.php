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

use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'pshubspot/model/AbstractHsObjectModel.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsContact.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsPipeline.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsProduct.php';

class HsDealAbandoned extends AbstractHsObjectModel
{
    protected static $objectType = 'DEAL';
    protected static $psObjectClass = 'Cart';

    private $fixedDealAbandonedStageId = 999;

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

    public function getPsObjectModel()
    {
        return $this->psObjectModel = new \Cart($this->id);
    }

    public function buildMessages($action = 'UPSERT')
    {
        $messages = parent::buildMessages($action);

        return array_map(
            function ($message) {
                $message['associations'] = [
                    'CONTACT' => [
                        HsContact::getExternalObjectIdFromId($message['properties']['id_customer']),
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
        return new HsPipeline($this->fixedDealAbandonedStageId);
    }

    public function build()
    {
        $data = $this->staticObjectModelData();
        $cart = $this->psObjectModel;
        Context::getContext()->cart = $cart;
        Context::getContext()->currency = new Currency((int) $cart->id_currency);
        $seniority = time() - strtotime($cart->date_upd);
        $data['stage'] = $this->getHsCurrentState()->sync_as;
        $data['name'] = $cart->id;
        $data['id_shop'] = $cart->id_shop;
        $data['amount'] = number_format($cart->getOrderTotal(), 2, '.', '');

        $discounts = array_map(
            function ($discount) {
                return $discount['code'];
            },
            $cart->getCartRules()
        );
        $data['coupon_code'] = implode(',', $discounts);

        $data['cart_products_html'] = $this->getAbandonedCartHTML();

        // Clean / "minify" HTML
        if (!empty($data['cart_products_html'])) {
            $data['cart_products_html'] = preg_replace('~>\s+<~', '><', $data['cart_products_html']);
        }
        // If the length is still big for this field, try to chop
        if (strlen($data['cart_products_html']) > 11500) {
            $data['cart_products_html'] = strip_tags($data['cart_products_html'], '<tr><td><a>');
        }
        if (strlen($data['cart_products_html']) > 11500) {
            $data['cart_products_html'] = substr($data['cart_products_html'], 0, 11500);
        }

        $data['abandoned_cart_url'] = Context::getContext()->link->getPageLink(
            'order',
            true,
            (int) $cart->id_lang,
            'step=3&recover_cart=' . (int) $cart->id . '&token_cart=' .
            md5(_COOKIE_KEY_ . 'recover_cart_' . (int) $cart->id)
        );
        $data['pipeline'] = \Configuration::get('PS_HUBSPOT_DEAL_PIPELINE_ID', '', '', '');

        $data += $this->address('billing_', new Address((int) $data['id_address_invoice']));
        $data += $this->address('shipping_', new Address((int) $data['id_address_delivery']));

        return $data;
    }

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

    public function dontSyncThis($forceSync = false)
    {
        if (parent::dontSyncThis()) {
            return true;
        }

        $cart = $this->psObjectModel;
        Context::getContext()->cart = $cart;
        Context::getContext()->currency = new Currency((int) $cart->id_currency);

        // if (!isset($cart->current_state) || $cart->current_state == 0) {
        //     return true;
        // }

        $stage = (new HsPipeline($this->fixedDealAbandonedStageId));
        if ($stage->sync_as == HsPipeline::DO_NOT_SYNC) {
            return true;
        }
        // Comprobamos SENIORITY. Esto afecta a los carritos nuevos y su actividad no aparece en HS hasta que pase el tiempo definido en Seniority
        // Esto hace que sea MUY importante que la gente tenga un cronjob o en su defecto pulsen el click en update
        $seniority = time() - strtotime($cart->date_upd);
        if ($forceSync == false && $seniority < (int) \Configuration::get('PS_HUBSPOT_DEALSABANDONED_SENIORITY') * 60) {
            return true;
        }

        // No sincronizamos carritos con valor 0.
        // NOTA: Algunos carritos podrían tener valor 0 por haberse removido un producto. En ese caso, quedarían en HubSpot con ese valor indefinidamente
        //       si el cliente no hace una compra con ese ítem. Por mi correcto, ya que es información relevante para marketing.
        if (!$cart->getOrderTotal()) {
            return true;
        }

        // No sincronizamos carritos si ya tienen un pedido cerrado asociado (para evitar bailes de Etapas en pipeline)
        if ($cart->orderExists()) {
            return true;
        }

        return false;
    }

    public function sync($forceSync = false)
    {
        if (strtotime($this->sync_attempt_at) + 4 > time()) {
            return true;
        }

        if (!parent::sync($forceSync)) {
            return false;
        }

        // Lets create temporary line items for cart
        $this->syncLineItems();

        return true;
    }

    public function syncLineItems($is_migration = false)
    {
        $cart = $this->psObjectModel;
        $store = new HsStore(isset($cart->id_shop) ? $cart->id_shop : 1);
        $product_array = [];
        $link = new \Link();

        foreach ($cart->getProducts() as $item) {
            // Así hacíamos las cosas en el año 2000 (o_ _)ﾉ
            $product_price_wt = (round($item['price_wt'] * 100)) / 100;
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
            if (!empty($item['id_product']) && $item['id_product'] > 0) {
                $imageLink = $this->getLineItemImageURL($item['id_product'], $item['id_product_attribute']);
            }
            $productName = $item['name'];
            if (!empty($item['attributes'])) {
                $productName = $item['name'] . ' ' . $item['attributes'];
            }

            $lineItemId = $cart->id . '_' . $item['id_product'] . '_' . $item['id_product_attribute'];
            if (!empty($item['id_customization'])) {
                $lineItemId .= '_' . $item['id_customization'];
            }

            $productMsgData = [
                'action' => 'UPSERT',
                // "externalObjectId" => 'CART_ITEM_' . $cart->id . '_' . $item['id_product'],
                'externalObjectId' => 'CART_ITEM_' . $lineItemId,
                'properties' => [
                    'name' => $productName,
                    'sku' => !empty($item['reference']) ? strip_tags($item['reference']) . '/' . $item['id_product'] : 'undefined',
                    'product_quantity' => $item['quantity'],
                    'tax_amount' => number_format($product_price_wt - $product_price, 2, '.', ''),
                    'product_price' => number_format($product_price_wt, 2, '.', ''),
                    'id_cart_product' => $lineItemId,
                    'unique_id' => $item['unique_id'],
                    'id_cart' => $cart->id,
                    'id_product' => $item['id_product'],
                    'manufacturer' => $manufacturerName,
                    'image_url' => $imageLink,
                    'hs_price_usd' => number_format($product_price_wt, 2, '.', ''),
                    'hs_price_eur' => number_format($product_price_wt, 2, '.', ''),
                    'url' => !empty($item['id_product']) ? $link->getProductLink($item['id_product']) : '',
                    'date_upd' => date('H:i:s Y-m-d'),
                ],
                'associations' => [
                    'PRODUCT' => [HsProduct::getExternalObjectIdFromId($item['id_product'])],
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

    public function removeLineItem($idProduct, $idProductAttribute)
    {
        $item = new Product($idProduct);
        $sku = strip_tags($item->reference) . '/' . $idProduct;
        $productName = ($item->name) ? current($item->name) : 'Deleted product';

        if ($idProductAttribute > 0) {
            $getProductAttribute = $item->getAttributesParams($idProduct, $idProductAttribute);
            if ($getProductAttribute) {
                $productName = $productName . ' ' . $getProductAttribute[0]['group'] . ' : ' . $getProductAttribute[0]['name'];
            }
        }

        $lineItemId = $this->id . '_' . $idProduct . '_' . $idProductAttribute;
        if (!empty($item->id_customization)) {
            $lineItemId .= '_' . $item->id_customization;
        }

        $product_array[] = [
            'action' => 'UPSERT',
            'externalObjectId' => 'CART_ITEM_' . $lineItemId,
            'properties' => [
                'name' => $productName,
                'product_quantity' => 0,
                'sku' => $sku,
                'id_cart_product' => $lineItemId,
            ],
            'associations' => [
                'PRODUCT' => [HsProduct::getExternalObjectIdFromId($idProduct)],
                'DEAL' => [$this->sync_as],
            ],
        ];

        foreach (array_chunk($product_array, 60) as $prdct_array) {
            static::$client->upsertMessages(
                (new \HsStore(Context::getContext()->shop->id))->sync_as,
                'LINE_ITEM',
                $prdct_array
            );
        }
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
        $where .= ' sync_as NOT IN (' .
            "SELECT CONCAT('DEAL_',id_cart) as syncDealName FROM  " . _DB_PREFIX_ . \Order::$definition['table'] .
            ') ';
        parent::setToReSync($limit, $where);
    }

    protected static function applyFilters($sql)
    {
        $stage = (new HsPipeline(999));
        if ($stage->sync_as == HsPipeline::DO_NOT_SYNC) {
            return $sql .= ' AND true = false ';
        }

        $sql .= ' AND id_cart NOT IN (' .
            'SELECT id_cart FROM  ' . _DB_PREFIX_ . \Order::$definition['table'] .
            ')';
        $sql .= ' AND id_cart NOT IN (' .
            'SELECT pc.id_cart FROM ' . _DB_PREFIX_ . \Cart::$definition['table'] . ' pc
                LEFT JOIN ' . _DB_PREFIX_ . \Cart::$definition['table'] . '_product pcp ON (pcp.id_cart  = pc.id_cart)
                WHERE pcp.id_product  is null ORDER by id_cart ' .
            ') ';

        $sql .= ' AND date_upd < "' . (date('Y-m-d H:i:s', time() - ((int) \Configuration::get('PS_HUBSPOT_DEALSABANDONED_SENIORITY') * 60))) . '" ';

        if (Configuration::get('PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED', '', '', '') != 'on') {
            return $sql;
        }

        return $sql . '  AND p.date_add > "' . Configuration::get('PS_HUBSPOT_DEAL_DATE_FILTER', '', '', '') . '" ';
    }

    protected function getCartProductsHTML()
    {
        try {
            $presenter = new CartPresenter();
            $presented_cart = $presenter->present($this->psObjectModel, $shouldSeparateGifts = true);
            Context::getContext()->smarty->assign(
                [
                    'cart' => $presented_cart,
                ]
            );

            return Context::getContext()->smarty->fetch(
                _PS_MODULE_DIR_ . 'pshubspot/views/templates/front/cart_products.tpl'
            );
        } catch (Exception $ex) {
            return '';
        }
    }

    protected function getCartProductsHTML_16()
    {
        Context::getContext()->smarty->assign(
            [
                'products' => $this->psObjectModel->getProducts(),
            ]
        );

        return Context::getContext()->smarty->fetch(_PS_MODULE_DIR_ . 'pshubspot/views/templates/front/cart_products_16.tpl');
    }

    protected function getAbandonedCartHTML()
    {
        $html = '';
        $cart = $this->psObjectModel;
        $products = $this->psObjectModel->getProducts();
        if ($products) {
            $formatNameFunction = is_callable("\ImageType", 'getFormattedName') ? 'getFormattedName' : 'getFormatedName';
            foreach ($products as $product) {
                $html .= '<tr>';
                $productLink = Context::getContext()->link->getProductLink($product);
                if (!empty($product['id_image'])) {
                    $imageLink = Context::getContext()->link->getImageLink($product['link_rewrite'], $product['id_image'], \ImageType::$formatNameFunction('small'));
                }
                $html .= "<td><span class='product-image media-middle'><img src='" . $imageLink . "' alt='" . $product['name'] . "' /></span></td>";
                $html .= "<td><a class='label' href='" . $productLink . "' data-id_customization='" . (int) $product['id_customization'] . "' >" . $product['name'] . '</a></td>';
                $html .= '<td>';

                if ($product['reduction']) {
                    $html .= " <div class='product-discount'>
                        <span class='regular-price' style='text-decoration: line-through;'>" . number_format($product['price_without_reduction'], 2, ',', '.') . ' €</span>';
                    if ($product['specific_prices']['reduction_type'] == 'percentage') {
                        $html .= "<span class='discount discount-percentage'> -" . (int) ($product['specific_prices']['reduction'] * 100) . '%</span>';
                    } else {
                        if (!empty($product['discount_to_display'])) {
                            // $html .= "<span class='discount discount-amount'>-" . $product['discount_to_display'] . "</span>";
                        }
                    }
                    $html .= '</div>';
                }

                $html .= "<div class='current-price'>";
                $html .= "<span class='price'>" . number_format($product['price'], 2, ',', '.') . ' €</span>';
                $html .= '</div></td>';
                $html .= '<td>';
                if (isset($product['attributes'])) {
                    $html .= "<div class='product-line-info'><span class='label'>" . $product['attributes'] . '</span></div>';
                }
                $html .= '</td>';
                $html .= "<td><span class='quantity'>" . $product['quantity'] . '</span></td>';
                $html .= "<td><span class='product-price'><strong>" . number_format($product['total'], 2, ',', '.') . ' €</strong></span></td>';
                $html .= '</tr>';
            }
        }

        return $html;
    }

    public static function getMigratedTotal()
    {
        return (int) Configuration::get('PS_HUBSPOT_MIGRATION_CARTS');
    }

    public static function applyMigrationFilters($sql)
    {
        $dateEnd = date('Y-m-d');
        if (Configuration::get('PS_HUBSPOT_MIGRATION_DATE_END')) {
            $dateEnd = Configuration::get('PS_HUBSPOT_MIGRATION_DATE_END');
        }
        $dateIni = date('2000-01-01');
        if (Configuration::get('PS_HUBSPOT_MIGRATION_DATE_FROM')) {
            $dateIni = Configuration::get('PS_HUBSPOT_MIGRATION_DATE_FROM');
        }

        if (Configuration::get('PS_HUBSPOT_MIGRATION_UPDATED_LEGACY')) {
            $sql .= ' p left join ' . _DB_PREFIX_ . static::$definition['table'] . ' t on p.' . static::getObjectClassKey() . '=t.' . static::$definition['primary'] . ' ';
        }

        $sql .= ' WHERE checkout_session_data IS NOT null AND id_cart NOT IN ( SELECT id_cart FROM  ' . _DB_PREFIX_ . 'orders ) AND date_add >= "' . $dateIni . '" AND  date_add <= "' . $dateEnd . '" ';

        if (Configuration::get('PS_HUBSPOT_MIGRATION_UPDATED_LEGACY')) {
            $sql .= '  AND sync_as <> "--" ';
        }

        return $sql;
    }

    protected static function updateMigratedTotal($add)
    {
        Configuration::updateValue('PS_HUBSPOT_MIGRATION_CARTS', (int) Configuration::get('PS_HUBSPOT_MIGRATION_CARTS') + (int) $add);
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
