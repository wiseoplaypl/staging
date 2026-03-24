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

namespace OBSolutions\RichSnippets\Product\Infraestructure\GSnippetsReviews;

use BT_Rating;
use BT_ReviewCtrl;
use OBSolutions\RichSnippets\Product\Application\ProductReviewRetrieverInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductReview;
use OBSolutions\RichSnippets\Product\Domain\ProductReviews;

class GSnippetsReviewsProductReviewRetriever implements ProductReviewRetrieverInterface
{
    public function __construct()
    {
        require_once _GSR_PATH_LIB_REVIEWS.'review-ctrl_class.php';
    }

    /**
     * @return bool
     */
    public static function enabled()
    {
        return defined('_GSR_PATH_LIB_REVIEWS') and require_once _GSR_PATH_LIB_REVIEWS.'review-ctrl_class.php';
    }

    /**
     * @param int $productId
     *
     * @return ProductReviews
     */
    public function getProductReviews(ProductInterface $product)
    {
        // set params for getting ratings and reviews
        $aReviewsParams = array(
            'bOnlyReview' => true,
            'bCommentCustomer' => false,
            'bRatingCustomer' => true,
            'orderBy' => 'dateAdd DESC',
            'productId' => $product->getId(),
            'interval' => '0,1000',
            'langId' => false,
            'report' => true,
        );

        $productReviews = array();

        $comments = BT_ReviewCtrl::create()->run(
            'getReviewsOnProduct',
            $aReviewsParams
        );

        if ($comments) {
            foreach ($comments as $comment) {
                $productReviews[] = new ProductReview(
                    $comment['review']['data']['sComment'],
                    $comment['review']['date'],
                    $comment['review']['data']['sTitle'],
                    $comment['note'],
                    $comment['firstname'].' '.$comment['lastname']
                );
            }
        } else {
            $productReviews = $this->getReviewsFromVersion329($product);
        }

        return new ProductReviews(...$productReviews);
    }

    private function getReviewsFromVersion329(ProductInterface $product)
    {
        $productReviews = array();

        $comments = BT_ReviewCtrl::create()->run('getReviews', array('productId' => $product->getId(), 'customer' => true, 'report' => true));

        if ($comments) {
            foreach ($comments as $comment) {
                $rating = BT_Rating::get(array('id' => $comment['ratingId'], 'customer' => true, 'report' => true));

                if (!$rating) {
                    continue;
                }

                $productReviews[] = new ProductReview(
                    $comment['data']['sComment'],
                    date('c', $comment['dateAdd']),
                    $comment['data']['sTitle'],
                    $rating[0]['note'],
                    $comment['firstname'].' '.$comment['lastname']
                );
            }
        }

        return $productReviews;
    }
}
