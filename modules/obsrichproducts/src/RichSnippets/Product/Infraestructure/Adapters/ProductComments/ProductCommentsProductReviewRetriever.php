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

namespace OBSolutions\RichSnippets\Product\Infraestructure\Adapters\ProductComments;

use Context;
use OBSolutions\RichSnippets\Product\Application\ProductReviewRetrieverInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductReview;
use OBSolutions\RichSnippets\Product\Domain\ProductReviews;
use ProductComment;
use ProductComments;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductCommentsProductReviewRetriever implements ProductReviewRetrieverInterface
{
    /**
     * @return bool
     */
    public static function enabled()
    {
        if (!class_exists('ProductComments')) {
            return false;
        }
        if (!class_exists('ProductComment')) {
            return false;
        }

        return is_callable('ProductComment', 'getByProduct');
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

        if (class_exists('ProductComments')) {
            $p = new ProductComments();
            if (method_exists(new ProductComments(), 'hookProductTab')) {
                $p->hookProductTab(array());   // we do not want the result, just want to force include of class ProductComment
            }
        }

        $comments = ProductComment::getByProduct($product->getId(), 1, null, $context->cookie->id_customer);
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
