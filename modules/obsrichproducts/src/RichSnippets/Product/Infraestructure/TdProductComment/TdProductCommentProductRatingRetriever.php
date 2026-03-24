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

use OBSolutions\RichSnippets\Product\Application\ProductRatingRetrieverInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductRating;
use TdProductComment;

class TdProductCommentProductRatingRetriever implements ProductRatingRetrieverInterface
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
     * @return ProductRating
     */
    public function getProductRating(ProductInterface $product)
    {
        $ratingCount = (int) TdProductComment::getCommentNumber($product->getId());
        $average = TdProductComment::getAverageGrade($product->getId());

        return new ProductRating($ratingCount, $average['grade']);
    }
}
