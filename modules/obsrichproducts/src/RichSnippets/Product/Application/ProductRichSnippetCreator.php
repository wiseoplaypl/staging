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

namespace OBSolutions\RichSnippets\Product\Application;

use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductRichSnippet;
use OBSolutions\RichSnippets\Product\Domain\ProductRichSnippetBuilder;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductRichSnippetCreator
{
    private $productRatingRetriever;
    private $productReviewRetriever;

    /**
     * @return ProductRichSnippet
     */
    public function create(ProductInterface $product)
    {
        $productRichSnippetBuilder = new ProductRichSnippetBuilder($product);

        if ($this->productRatingRetriever instanceof ProductRatingRetrieverInterface) {
            $productRichSnippetBuilder->withRating($this->productRatingRetriever->getProductRating($product));
        }

        if ($this->productReviewRetriever instanceof ProductReviewRetrieverInterface) {
            $productRichSnippetBuilder->withReviews($this->productReviewRetriever->getProductReviews($product));
        }

        $productRichSnippetBuilder->withImages();

        return $productRichSnippetBuilder->build();
    }

    public function setProductRatingRetriever(ProductRatingRetrieverInterface $productRatingRetriever)
    {
        $this->productRatingRetriever = $productRatingRetriever;
    }

    public function setProductReviewRetriever(ProductReviewRetrieverInterface $productReviewRetriever)
    {
        $this->productReviewRetriever = $productReviewRetriever;
    }
}
