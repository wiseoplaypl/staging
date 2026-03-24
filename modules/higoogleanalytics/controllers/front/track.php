<?php
/**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class HiGoogleAnalyticsTrackModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $this->secureKey = Tools::getValue('secureKey');
        parent::__construct();
    }

    public function init()
    {
        if ($this->ajax && $this->secureKey == $this->module->secureKey) {
            if (Tools::getValue('action') == 'getProductDetails') {
                $idProduct = (int) Tools::getValue('idProduct');
                $idProductAttribute = (int) Tools::getValue('idProductAttribute');
                $qty = (int) Tools::getValue('qty', 1);
                $currency = Tools::getValue('currency');

                $product = new Product($idProduct, false, $this->context->language->id);
                if (!Validate::isLoadedObject($product)) {
                    exit(json_encode([
                        'hasError' => true,
                        'error' => $this->module->l('Product not found!', 'track'),
                    ]));
                }

                // To-Do: we may need to add an option for this
                $useTax = true;
                // To-Do: Product::getPriceStatic doesn't work because it's required cart_id
                // $price = Product::getPriceStatic($idProduct, $useTax, $idProductAttribute);
                $price = $product->price;
                $data = [
                    'value' => $price * $qty,
                    'currency' => $currency,
                ];

                $items = [];
                $category = new Category($product->id_category_default, $this->context->language->id);
                $item = [
                    'item_id' => $product->id,
                    'item_name' => $product->name,
                    'item_brand' => Manufacturer::getNameById($product->id_manufacturer),
                    'item_category' => $category->name,
                    'price' => $price,
                    'quantity' => $qty,
                ];

                if ($idProductAttribute) {
                    $combination = new Combination($idProductAttribute);
                    $attributes = $combination->getAttributesName($this->context->language->id);
                    $attributeNames = '';
                    if (is_array($attributes) && $attributes) {
                        foreach ($attributes as $attribute) {
                            $attributeNames .= $attribute['name'] . ' - ';
                        }
                    }
                    $attributeNames = rtrim($attributeNames, ' - ');

                    $item['item_variant'] = $attributeNames;
                }

                array_push($items, $item);

                $data['items'] = $items;

                exit(json_encode([
                    'hasError' => false,
                    'data' => $data,
                ]));
            }
        }

        exit;
    }
}
