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

use OBSolutions\RichSnippets\Product\Application\ProductRatingRetrieverInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductRating;

class SpmgsniProductRatingRetriever implements ProductRatingRetrieverInterface
{
    /**
     * @return bool
     */
    public static function enabled()
    {
        if (!@include_once(_PS_MODULE_DIR_.'spmgsnipreview/classes/spmgsnipreviewhelp.class.php')) {
            return false;
        }

        if (!class_exists('spmgsnipreviewhelp')) {
            return false;
        }

        return is_callable('spmgsnipreviewhelp', 'getAvgReview')
            && is_callable('spmgsnipreviewhelp', 'getCountReviews');
    }

    /**
     * @param int $productId
     *
     * @return ProductRating
     */
    public function getProductRating(ProductInterface $product)
    {
        $obj = new \spmgsnipreviewhelp();
        $avgReview = $obj->getAvgReview(array('id_product' => $product->getId()));
        $countReview = $obj->getCountReviews(array('id_product' => $product->getId()));

        return new ProductRating(
            $countReview,
            $avgReview['avg_rating_decimal']
        );
    }
}
