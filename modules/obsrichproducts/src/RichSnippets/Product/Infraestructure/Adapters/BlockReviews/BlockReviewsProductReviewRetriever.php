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

namespace OBSolutions\RichSnippets\Product\Infraestructure\Adapters\BlockReviews;

use OBSolutions\RichSnippets\Product\Application\ProductReviewRetrieverInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductReview;
use OBSolutions\RichSnippets\Product\Domain\ProductReviews;
use reviewshelp;

if (!defined('_PS_VERSION_')) {
    exit;
}

class BlockReviewsProductReviewRetriever implements ProductReviewRetrieverInterface
{
    public static function enabled()
    {
        if (!@include_once(_PS_MODULE_DIR_.'blockreviews/classes/reviewshelp.class.php')) {
            return false;
        }

        return class_exists('reviewshelp');
    }

    public function getProductReviews(ProductInterface $product)
    {
        $productReviews = array();

        $obj_reviewshelp = new reviewshelp();
        $data_in = $obj_reviewshelp->getReviews(array('id_product' => $product->getId(), 'start' => 0));

        if (is_array($data_in) && isset($data_in['reviews'])) {
            $reviewList = $data_in['reviews'];
            foreach ($reviewList as $comment) {
                if (!$comment['active'] || $comment['deleted']) {
                    continue;
                }

                $productReviews[] = new ProductReview(
                    strip_tags($comment['text_review']),
                    $comment['date_add'],
                    $comment['subject'],
                    $comment['rating'],
                    $comment['customer_name'] ? $comment['customer_name'] : 'Unknown'
                );
            }
        }

        return new ProductReviews(...$productReviews);
    }
}
