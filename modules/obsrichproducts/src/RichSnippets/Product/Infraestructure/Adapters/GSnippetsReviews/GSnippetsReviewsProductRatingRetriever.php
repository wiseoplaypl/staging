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

namespace OBSolutions\RichSnippets\Product\Infraestructure\Adapters\GSnippetsReviews;

if (!defined('_PS_VERSION_')) {
    exit;
}

use BT_ReviewCtrl;
use OBSolutions\RichSnippets\Product\Application\ProductRatingRetrieverInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductRating;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GSnippetsReviewsProductRatingRetriever implements ProductRatingRetrieverInterface
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
     * @return ProductRating
     */
    public function getProductRating(ProductInterface $product)
    {
        $ratingCount = (int) BT_ReviewCtrl::create()->run(
            'countRatings',
            array(
                'productId' => $product->getId(),
                'langId' => false,
            )
        );
        $average = BT_ReviewCtrl::create()->run(
            'average',
            array(
                'iProductId' => $product->getId(),
                'langId' => false,
            )
        );

        $averageRating = null;
        if (array_key_exists('iAverage', $average)) {
            $averageRating = $average['iAverage'];
            if (array_key_exists('bHalf', $average) && $average['bHalf']) {
                $averageRating /= 2;
            }
        } elseif (array_key_exists('fDetailAverage', $average)) {
            $averageRating = $average['fDetailAverage'];
        }

        return new ProductRating($ratingCount, $averageRating);
    }
}
