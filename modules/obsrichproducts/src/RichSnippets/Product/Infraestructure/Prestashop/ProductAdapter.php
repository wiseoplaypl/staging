<?php
/**
 * 2011-2025 OBSOLUTIONS WD S.L. All Rights Reserved.
 *
 * NOTICE:  All information contained herein is, and remains
 * the property of OBSOLUTIONS WD S.L. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to OBSOLUTIONS WD S.L.
 * and its suppliers and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from OBSOLUTIONS WD S.L.
 *
 *  @author    OBSOLUTIONS WD S.L. <http://addons.prestashop.com/en/65_obs-solutions>
 *  @copyright 2011-2025 OBSOLUTIONS WD S.L.
 *  @license   OBSOLUTIONS WD S.L. All Rights Reserved
 *  International Registered Trademark & Property of OBSOLUTIONS WD S.L.
 */

namespace OBSolutions\RichSnippets\Product\Infraestructure\Prestashop;

use Context;
use ImageType;
use Manufacturer;
use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use Product as PrestashopProduct;
use Tools;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductAdapter implements ProductInterface
{
    private $product;
    private $context;

    public function __construct($prestashopProduct)
    {
        $this->product = $prestashopProduct;
        $this->context = Context::getContext();
    }

    public function getId()
    {
        return $this->getProperty('id');
    }

    public function getName()
    {
        return $this->getProperty('name');
    }

    public function getBrandName()
    {
        $manufacturerId = $this->getProperty('id_manufacturer');
        if ($manufacturerId) {
            $manufacturer = new Manufacturer($manufacturerId);
            if ($manufacturer->name) {
                return $manufacturer->name;
            }
        }

        return '';
    }

    public function getDescription()
    {
        $desc = $this->getProperty('description');

        return $desc ? $desc : $this->getProperty('description_short');
    }

    public function getEan13()
    {
        $ean13 = $this->getProperty('ean13');

        if (!$ean13) {
            $attributes = $this->getProperty('attributes');
            if (is_array($attributes) and count($attributes) > 0) {
                foreach ($attributes as $attribute) {
                    if (array_key_exists('ean13', $attribute)) {
                        $ean13 = $attribute['ean13'];
                    }

                    break;
                }
            }

            if (!$ean13) {
                if ($this->product instanceof PrestashopProduct) {
                    $productAttribute = $this->product->getAttributeCombinationsById(
                        PrestashopProduct::getDefaultAttribute($this->getProperty('id')),
                        $this->context->language->id
                    );
                    if (
                        is_array($productAttribute)
                        && count($productAttribute) > 0
                        && array_key_exists('ean13', $productAttribute[0])) {
                        $ean13 = $productAttribute[0]['ean13'];
                    }
                }
            }
        }

        return $ean13;
    }

    public function getIsbn()
    {
        $isbn = $this->getProperty('isbn');

        if (!$isbn) {
            $isbn = $this->getUpc();
        }

        return $isbn;
    }

    public function getSku()
    {
        return $this->getProperty('reference');
    }

    public function getUrl()
    {
        if (is_callable(array($this->product, 'getUrl'))) {
            $productUrl = $this->product->getUrl();
        } else {
            $productUrl = $this->context->link->getProductLink($this->product);
        }

        return $productUrl;
    }

    public function getImageUrlList()
    {
        $imageUrlList = array();

        // TODO: get all not cover images
        $cover = $this->getProperty('cover');
        if ($cover) {
            $imageUrlList[] = $cover['large']['url'];
        } else {
            $poductId = $this->getProperty('id');
            $linkRewrite = $this->getProperty('link_rewrite');

            $cover = PrestashopProduct::getCover($poductId);

            $imageType = $this->getImageType('large');
            $coverUrl = $this->context->link->getImageLink($linkRewrite, $cover['id_image'], $imageType);
            $imageUrlList[] = $coverUrl;
        }

        return $imageUrlList;
    }

    public function getPrice()
    {
        /*
        // FIX FOR A CUSTOMER TO GET PRICES WITH TAX ALWAYS
        $price_tax_inc = $this->getProperty('price_amount');
        $price_tax_exc = $this->getProperty('price_tax_exc');

        if(abs($price_tax_inc - $price_tax_exc) > 0.01) {
            $price = $price_tax_inc;
        } else {
            $price = round($price_tax_exc * 1.19, 2);
        }

        if (!$price && is_callable([$this->product, 'getPrice'])) {
            $price = $this->product->getPrice(true, null, 2);
        }

        return $price;*/
        $price = $this->getProperty('price_amount');

        if (!$price && is_callable(array($this->product, 'getPrice'))) {
            $price = $this->product->getPrice(true, null, 2);
        }

        return $price;
    }

    public function getPriceValidUntil()
    {
        $priceValidUntil = false;
        $specific_prices = $this->getProperty('specific_prices');
        if (is_array($specific_prices) && array_key_exists('to', $specific_prices)) {
            $priceValidUntil = $specific_prices['to'];
        }

        if ($priceValidUntil && '0000-00-00 00:00:00' != $priceValidUntil) {
            return $priceValidUntil;
        }

        return (new \DateTimeImmutable('+1 year'))->format('Y-m-d H:i:s');
    }

    public function getPriceCurrency()
    {
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $currency = Tools::setCurrency();
            $currency = $currency->iso_code;
        } else {
            $currency = $this->context->currency->iso_code;
        }

        return $currency;
    }

    public function hasStock()
    {
        $availability = $this->getProperty('availability');

        if ((!$availability || !is_string($availability)) && is_callable(array($this->product, 'checkQty'))) {
            $hasStock = $this->product->checkQty(1);
        } else {
            $hasStock = 'unavailable' != $availability;
        }

        return (bool) $hasStock;
    }

    public function isNew()
    {
        $condition = $this->getProperty('condition');

        if (!$condition || is_array($condition)) {
            $getCondition = $condition;
            if (is_callable(array($this->product, 'getCondition'))) {
                $getCondition = $this->product->getCondition();
            }
            if (is_array($getCondition) and array_key_exists('type', $getCondition)) {
                $condition = $getCondition['type'];
            }
        }

        return (bool) (!$condition || 'new' == $condition);
    }

    private function getUpc()
    {
        return $this->getProperty('upc');
    }

    private function getProperty($property)
    {
        if ($this->product instanceof PrestashopProduct) {
            if (isset($this->product->{$property})) {
                return $this->product->{$property};
            }

            return null;
        }
        if (isset($this->product[$property])) {
            return $this->product[$property];
        }

        return null;
    }

    private function getImageType($type)
    {
        if (is_callable(array('ImageType', 'getFormattedName'))) {
            return ImageType::getFormattedName($type);
        }

        return $type.'_default';
    }
}
