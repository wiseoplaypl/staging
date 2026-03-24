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

namespace OBSolutions\RichSnippets\Product\Infraestructure\Adapters\NetReviews;

use NetReviewsModel;
use OBSolutions\RichSnippets\Product\Application\ProductReviewRetrieverInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductReview;
use OBSolutions\RichSnippets\Product\Domain\ProductReviews;

if (!defined('_PS_VERSION_')) {
    exit;
}

class NetReviewsProductReviewRetriever implements ProductReviewRetrieverInterface
{
    /**
     * @return bool
     */
    public static function enabled()
    {
        if (!class_exists('NetReviewsModel')) {
            return false;
        }

        return is_callable('NetReviewsModel', 'getUrlsProduct');
    }

    /**
     * @param int $productId
     *
     * @return ProductReviews
     */
    public function getProductReviews(ProductInterface $product)
    {
        $productReviews = array();

        $netReviews = new NetReviewsModel();
        $productReviewDetails = $netReviews->getProductReviews($product->getId());

        foreach ($productReviewDetails as $comment) {
            $productReviews[] = new ProductReview(
                strip_tags($comment['review']),
                $comment['horodate'],
                $product->getName(),
                $comment['rate'],
                $comment['customer_name'] ? $comment['customer_name'] : 'Unknown'
            );
        }

        return new ProductReviews(...$productReviews);
    }
}
