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

namespace OBSolutions\RichSnippets\Product\Infraestructure\Factory;

use OBSolutions\RichSnippets\Product\Application\ProductRatingRetrieverInterface;
use OBSolutions\RichSnippets\Product\Application\RetrieverNotAvailable;
use OBSolutions\RichSnippets\Product\Infraestructure\GSnippetsReviews\GSnippetsReviewsProductRatingRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\LGComments\LGCommentsProductRatingRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\MyPrestaComments\MyPrestaCommentsProductRatingRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\NetReviews\NetReviewsProductRatingRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\ProductComments\ProductCommentsProductRatingRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\SpmgsniPreview\SpmgsniProductRatingRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\TdProductComment\TdProductCommentProductRatingRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\TrustedShopsIntegration\TrustedShopsIntegrationProductRatingRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\Yotpo\YotpoProductRatingRetriever;

class ProductRatingRetrieverFactory
{
    /**
     * @throws RetrieverNotAvailable
     *
     * @return ProductRatingRetrieverInterface
     */
    public static function create()
    {
        $productRatingRetriever = null;

        $productRatingRetriever = TdProductCommentProductRatingRetriever::enabled() ? new TdProductCommentProductRatingRetriever() : $productRatingRetriever;
        $productRatingRetriever = ProductCommentsProductRatingRetriever::enabled() ? new ProductCommentsProductRatingRetriever() : $productRatingRetriever;
        $productRatingRetriever = GSnippetsReviewsProductRatingRetriever::enabled() ? new GSnippetsReviewsProductRatingRetriever() : $productRatingRetriever;
        $productRatingRetriever = TrustedShopsIntegrationProductRatingRetriever::enabled() ? new TrustedShopsIntegrationProductRatingRetriever() : $productRatingRetriever;
        $productRatingRetriever = LGCommentsProductRatingRetriever::enabled() ? new LGCommentsProductRatingRetriever() : $productRatingRetriever;
        $productRatingRetriever = YotpoProductRatingRetriever::enabled() ? new YotpoProductRatingRetriever() : $productRatingRetriever;
        $productRatingRetriever = SpmgsniProductRatingRetriever::enabled() ? new SpmgsniProductRatingRetriever() : $productRatingRetriever;
        $productRatingRetriever = NetReviewsProductRatingRetriever::enabled() ? new NetReviewsProductRatingRetriever() : $productRatingRetriever;
        $productRatingRetriever = MyPrestaCommentsProductRatingRetriever::enabled() ? new MyPrestaCommentsProductRatingRetriever() : $productRatingRetriever;

        self::isValidRetriever($productRatingRetriever);

        return $productRatingRetriever;
    }

    /**
     * @param $productRatingRetriever
     *
     * @throws RetrieverNotAvailable
     */
    private static function isValidRetriever($productRatingRetriever)
    {
        if (!$productRatingRetriever instanceof ProductRatingRetrieverInterface) {
            throw new RetrieverNotAvailable(ProductRatingRetrieverInterface::class);
        }
    }
}
