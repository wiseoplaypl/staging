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

class ProductRichSnippetBuilder
{
    private $product;
    private $productRating;

    /** @var ProductReviews */
    private $productReviews;
    private $imageList = array();

    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    public function withImages()
    {
        $this->imageList = $this->product->getImageUrlList();
    }

    public function withRating(ProductRating $productRating)
    {
        $this->productRating = $productRating;
    }

    public function withReviews(ProductReviews $productReviews)
    {
        $this->productReviews = $productReviews;
    }

    public function build()
    {
        $productRichSnippet = new ProductRichSnippet();

        // BASIC DATA
        $productRichSnippet->setName($this->product->getName())
            ->setDescription($this->product->getDescription())
            ->setEan13($this->product->getEan13())
            ->setIsbn($this->product->getIsbn())
            ->setUrl($this->product->getUrl())
            ->setSku($this->product->getSku())
            ->setPrice($this->product->getPrice())
            ->setPriceValidUntil($this->product->getPriceValidUntil())
            ->setPriceCurrency($this->product->getPriceCurrency())
            ->setIsNew($this->product->isNew())
            ->setHasStock($this->product->hasStock())
            ->setBrandName($this->product->getBrandName())
        ;

        // IMAGES
        if ($this->imageList) {
            foreach ($this->imageList as $imageUrl) {
                $productRichSnippet->addImageUrl($imageUrl);
            }
        }

        if ($this->productRating) {
            $productRichSnippet->setProductRating($this->productRating);
        }

        if ($this->productReviews) {
            foreach ($this->productReviews->getReviews() as $review) {
                $productRichSnippet->addReview($review);
            }
        }

        return $productRichSnippet;
    }
}
