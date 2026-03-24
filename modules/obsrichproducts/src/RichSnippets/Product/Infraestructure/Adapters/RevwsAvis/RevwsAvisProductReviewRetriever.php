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

namespace OBSolutions\RichSnippets\Product\Infraestructure\Adapters\RevwsAvis;

use OBSolutions\RichSnippets\Product\Application\ProductReviewRetrieverInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductReview;
use OBSolutions\RichSnippets\Product\Domain\ProductReviews;
use Revws;
use RevwsReview;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RevwsAvisProductReviewRetriever implements ProductReviewRetrieverInterface
{
    public static function enabled()
    {
        if (!@include_once(_PS_MODULE_DIR_.'revws/model/review.php')) {
            return false;
        }

        return is_callable('RevwsReview', 'getAverageGrade');
    }

    public function getProductReviews(ProductInterface $product)
    {
        $productReviews = array();

        $revws = new Revws();
        $revsSettings = $revws->getSettings();
        $reviewList = RevwsReview::findReviews($revsSettings, array(
            'product' => $product->getId(),
            'deleted' => false,
            'validated' => true,
        ));

        if (is_array($reviewList) && $reviewList['total'] > 0) {
            /** @var RevwsReview $comment */
            foreach ($reviewList['reviews'] as $comment) {
                $grades = $comment->grades;
                $rating = null;
                if (is_array($grades)) {
                    foreach ($grades as $key => $value) {
                        $rating = (int) $value;

                        break;
                    }
                }

                if (is_null($rating)) {
                    continue;
                }

                $productReviews[] = new ProductReview(
                    strip_tags($comment->content),
                    $comment->date_add,
                    $comment->title,
                    $rating,
                    $comment->display_name ? $comment->display_name : 'Unknown'
                );
            }
        }

        return new ProductReviews(...$productReviews);
    }
}
