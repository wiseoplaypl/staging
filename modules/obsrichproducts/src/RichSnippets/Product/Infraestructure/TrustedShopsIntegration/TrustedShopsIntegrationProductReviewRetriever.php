<?php
/**
 * 2011-2022 OBSOLUTIONS WD S.L. All Rights Reserved.
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
 *  @copyright 2011-2022 OBSOLUTIONS WD S.L.
 *  @license   OBSOLUTIONS WD S.L. All Rights Reserved
 *  International Registered Trademark & Property of OBSOLUTIONS WD S.L.
 */

namespace OBSolutions\RichSnippets\Product\Infraestructure\TrustedShopsIntegration;

use OBSolutions\RichSnippets\Product\Application\ProductReviewRetrieverInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductReview;
use OBSolutions\RichSnippets\Product\Domain\ProductReviews;

class TrustedShopsIntegrationProductReviewRetriever extends TrustedShopsIntegrationProductRetriever implements ProductReviewRetrieverInterface
{
    /**
     * @param int $productId
     *
     * @return ProductReviews
     */
    public function getProductReviews(ProductInterface $product)
    {
        if (!$product->getSku()) {
            return new ProductReviews();
        }

        $encodedSku = $this->encodeSku($product->getSku());

        $reviewsJson = file_get_contents('https://cdn1.api.trustedshops.com/shops/'.self::$trustedShopId.'/products/skus/'.$encodedSku.'/productreviewstickers/v1/reviews.json');
        $reviews = json_decode($reviewsJson);

        $productReviews = array();

        if (isset($reviews->response->data->product->reviews)) {
            foreach ($reviews->response->data->product->reviews as $comment) {
                $productReviews[] = new ProductReview(
                    $comment->comment,
                    $comment->creationDate,
                    $product->getName(),
                    $comment->mark,
                    'unknown'
                );
            }
        }

        return new ProductReviews(...$productReviews);
    }
}
