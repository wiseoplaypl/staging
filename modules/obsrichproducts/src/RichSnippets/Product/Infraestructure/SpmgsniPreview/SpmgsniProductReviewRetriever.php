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

namespace OBSolutions\RichSnippets\Product\Infraestructure\SpmgsniPreview;

use OBSolutions\RichSnippets\Product\Application\ProductReviewRetrieverInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductReview;
use OBSolutions\RichSnippets\Product\Domain\ProductReviews;

class SpmgsniProductReviewRetriever implements ProductReviewRetrieverInterface
{
    public static function enabled()
    {
        if (!@include_once(_PS_MODULE_DIR_.'spmgsnipreview/classes/spmgsnipreviewhelp.class.php')) {
            return false;
        }

        if (!class_exists('spmgsnipreviewhelp')) {
            return false;
        }

        return is_callable('spmgsnipreviewhelp', 'getReviews');
    }

    public function getProductReviews(ProductInterface $product)
    {
        $productReviews = array();

        $obj = new \spmgsnipreviewhelp();
        $reviews = $obj->getReviews(
            array(
                'id_product' => $product->getId(),
                'start' => 0,
                'frat' => '',
                'is_search' => 0,
                'search' => '',
                'is_sort' => 0,
                'sort_condition' => '',
            )
        );

        if (is_array($reviews['reviews'])) {
            foreach ($reviews['reviews'] as $comment) {
                $productReviews[] = new ProductReview(
                    strip_tags($comment['text_review']),
                    $comment['review_date_update'],
                    $comment['title_review'],
                    $comment['rating'],
                    $comment['customer_name'] ? $comment['customer_name'] : 'Unknown'
                );
            }
        }

        return new ProductReviews(...$productReviews);
    }
}
