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

namespace OBSolutions\RichSnippets\Product\Domain;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductRichSnippet
{
    private $name;
    private $description;
    private $url;
    private $ean13;
    private $brandName;
    private $sku;
    private $isbn;
    private $price;
    private $priceValidUntil;
    private $priceCurrency;
    private $isNew;
    private $hasStock;
    private $imagesList = array();
    private $ratingCount;
    private $averageRating;
    private $reviews = array();

    public function setName($name)
    {
        // REMOVE QUOTES
        $this->name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

        return $this;
    }

    public function setDescription($description)
    {
        // REMOVE ALL HTML TAGS
        $description = strip_tags($description);

        // DECODE HTML CHARACTERS
        $description = html_entity_decode($description);

        // REMOVE QUOTES
        $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');

        // REMOVE EXTRA SPACES
        $description = trim($description);

        $this->description = $description;

        return $this;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function setEan13($ean13)
    {
        $this->ean13 = $ean13;

        return $this;
    }

    public function setBrandName($brandName)
    {
        $this->brandName = $brandName;

        return $this;
    }

    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    public function setPriceValidUntil($priceValidUntil)
    {
        $this->priceValidUntil = $priceValidUntil;

        return $this;
    }

    public function setPriceCurrency($priceCurrency)
    {
        $this->priceCurrency = $priceCurrency;

        return $this;
    }

    public function setIsNew($isNew)
    {
        $this->isNew = (bool) $isNew;

        return $this;
    }

    public function setHasStock($hasStock)
    {
        $this->hasStock = (bool) $hasStock;

        return $this;
    }

    public function addImageUrl($imageUrl)
    {
        $this->imagesList[] = $imageUrl;

        return $this;
    }

    public function setProductRating(ProductRating $productRating)
    {
        if ($productRating->getRatingCount() > 0) {
            $this->ratingCount = $productRating->getRatingCount();
            $this->averageRating = $productRating->getAverageRating();
        }

        return $this;
    }

    public function addReview(ProductReview $review)
    {
        $this->reviews[] = $review;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getEan13()
    {
        return $this->ean13;
    }

    public function getIsbn()
    {
        return $this->isbn;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getBrandName()
    {
        return $this->brandName;
    }

    public function getImagesList()
    {
        return $this->imagesList;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getPriceValidUntil()
    {
        return $this->priceValidUntil;
    }

    public function getPriceCurrency()
    {
        return $this->priceCurrency;
    }

    public function hasStock()
    {
        return $this->hasStock;
    }

    public function isNew()
    {
        return $this->isNew;
    }

    public function getRatingCount()
    {
        return $this->ratingCount;
    }

    public function getAverageRating()
    {
        return $this->averageRating;
    }

    /**
     * @return ProductReview[]
     */
    public function getReviews()
    {
        return $this->reviews;
    }
}
