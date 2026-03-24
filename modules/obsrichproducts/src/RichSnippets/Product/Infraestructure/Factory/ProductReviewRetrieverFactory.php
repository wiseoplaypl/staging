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

use OBSolutions\RichSnippets\Product\Application\ProductReviewRetrieverInterface;
use OBSolutions\RichSnippets\Product\Application\RetrieverNotAvailable;
use OBSolutions\RichSnippets\Product\Infraestructure\GSnippetsReviews\GSnippetsReviewsProductReviewRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\LGComments\LGCommentsProductReviewRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\MyPrestaComments\MyPrestaCommentsProductReviewRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\NetReviews\NetReviewsProductReviewRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\ProductComments\ProductCommentsProductReviewRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\SpmgsniPreview\SpmgsniProductReviewRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\TdProductComment\TdProductCommentProductReviewRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\TrustedShopsIntegration\TrustedShopsIntegrationProductReviewRetriever;
use OBSolutions\RichSnippets\Product\Infraestructure\Yotpo\YotpoProductReviewRetriever;

class ProductReviewRetrieverFactory
{
    /**
     * @throws RetrieverNotAvailable
     *
     * @return ProductReviewRetrieverInterface
     */
    public static function create()
    {
        $productReviewRetriever = null;

        $productReviewRetriever = TdProductCommentProductReviewRetriever::enabled() ? new TdProductCommentProductReviewRetriever() : $productReviewRetriever;
        $productReviewRetriever = ProductCommentsProductReviewRetriever::enabled() ? new ProductCommentsProductReviewRetriever() : $productReviewRetriever;
        $productReviewRetriever = GSnippetsReviewsProductReviewRetriever::enabled() ? new GSnippetsReviewsProductReviewRetriever() : $productReviewRetriever;
        $productReviewRetriever = TrustedShopsIntegrationProductReviewRetriever::enabled() ? new TrustedShopsIntegrationProductReviewRetriever() : $productReviewRetriever;
        $productReviewRetriever = LGCommentsProductReviewRetriever::enabled() ? new LGCommentsProductReviewRetriever() : $productReviewRetriever;
        $productReviewRetriever = YotpoProductReviewRetriever::enabled() ? new YotpoProductReviewRetriever() : $productReviewRetriever;
        $productReviewRetriever = SpmgsniProductReviewRetriever::enabled() ? new SpmgsniProductReviewRetriever() : $productReviewRetriever;
        $productReviewRetriever = NetReviewsProductReviewRetriever::enabled() ? new NetReviewsProductReviewRetriever() : $productReviewRetriever;
        $productReviewRetriever = MyPrestaCommentsProductReviewRetriever::enabled() ? new MyPrestaCommentsProductReviewRetriever() : $productReviewRetriever;

        self::isValidRetriever($productReviewRetriever);

        return $productReviewRetriever;
    }

    /**
     * @param $productReviewRetriever
     *
     * @throws RetrieverNotAvailable
     */
    private static function isValidRetriever($productReviewRetriever)
    {
        if (!$productReviewRetriever instanceof ProductReviewRetrieverInterface) {
            throw new RetrieverNotAvailable(ProductReviewRetrieverInterface::class);
        }
    }
}
