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

namespace OBSolutions\RichSnippets\Product\Infraestructure\Adapters\LGComments;

use LGProductComment;
use OBSolutions\RichSnippets\Product\Application\ProductRatingRetrieverInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductRating;

if (!defined('_PS_VERSION_')) {
    exit;
}

class LGCommentsProductRatingRetriever implements ProductRatingRetrieverInterface
{
    /**
     * @return bool
     */
    public static function enabled()
    {
        if (!class_exists('LGProductComment')) {
            return false;
        }

        return is_callable('LGProductComment', 'getExtraRightDetails');
    }

    /**
     * @param int $productId
     *
     * @return ProductRating
     */
    public function getProductRating(ProductInterface $product)
    {
        $ratingDetails = LGProductComment::getExtraRightDetails();

        $ratingCount = (int) $ratingDetails['numberofreviews'];
        $average = $ratingDetails['averagecomments'] / 2;

        return new ProductRating($ratingCount, round($average));
    }
}
