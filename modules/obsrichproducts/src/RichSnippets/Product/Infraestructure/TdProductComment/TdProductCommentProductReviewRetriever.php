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

namespace OBSolutions\RichSnippets\Product\Infraestructure\TdProductComment;

use Context;
use OBSolutions\RichSnippets\Product\Application\ProductReviewRetrieverInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductReview;
use OBSolutions\RichSnippets\Product\Domain\ProductReviews;
use TdProductComment;

class TdProductCommentProductReviewRetriever implements ProductReviewRetrieverInterface
{
    /**
     * @return bool
     */
    public static function enabled()
    {
        return is_callable(array('TdProductComment', 'getAverageGrade'));
    }

    /**
     * @param int $productId
     *
     * @return ProductReviews
     */
    public function getProductReviews(ProductInterface $product)
    {
        $context = Context::getContext();
        $productReviews = array();

        $comments = TdProductComment::getByProduct($product->getId(), 1, null, $context->cookie->id_customer);
        if ($comments) {
            foreach ($comments as $comment) {
                $productReviews[] = new ProductReview(
                    $comment['content'],
                    $comment['date_add'],
                    $comment['title'],
                    $comment['grade'],
                    $comment['customer_name']
                );
            }
        }

        return new ProductReviews(...$productReviews);
    }
}
