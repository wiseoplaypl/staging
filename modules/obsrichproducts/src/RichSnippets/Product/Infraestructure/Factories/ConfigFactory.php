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

namespace OBSolutions\RichSnippets\Product\Infraestructure\Factories;

use OBSolutions\RichSnippets\Product\Infraestructure\Adapters\BlockReviews;
use OBSolutions\RichSnippets\Product\Infraestructure\Adapters\GSnippetsReviews;
use OBSolutions\RichSnippets\Product\Infraestructure\Adapters\LGComments;
use OBSolutions\RichSnippets\Product\Infraestructure\Adapters\MyPrestaComments;
use OBSolutions\RichSnippets\Product\Infraestructure\Adapters\NetReviews;
use OBSolutions\RichSnippets\Product\Infraestructure\Adapters\ProductComments;
use OBSolutions\RichSnippets\Product\Infraestructure\Adapters\RevwsAvis;
use OBSolutions\RichSnippets\Product\Infraestructure\Adapters\SpmgsniPreview;
use OBSolutions\RichSnippets\Product\Infraestructure\Adapters\TdProductComment;
use OBSolutions\RichSnippets\Product\Infraestructure\Adapters\TrustedShopsIntegration;
use OBSolutions\RichSnippets\Product\Infraestructure\Adapters\Yotpo;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ConfigFactory
{
    public static function getProductRatingAdapters()
    {
        return array(
            BlockReviews\BlockReviewsProductRatingRetriever::class,
            GSnippetsReviews\GSnippetsReviewsProductRatingRetriever::class,
            LGComments\LGCommentsProductRatingRetriever::class,
            MyPrestaComments\MyPrestaCommentsProductRatingRetriever::class,
            NetReviews\NetReviewsProductRatingRetriever::class,
            ProductComments\ProductCommentsProductRatingRetriever::class,
            RevwsAvis\RevwsAvisProductRatingRetriever::class,
            SpmgsniPreview\SpmgsniProductRatingRetriever::class,
            TdProductComment\TdProductCommentProductRatingRetriever::class,
            TrustedShopsIntegration\TrustedShopsIntegrationProductRatingRetriever::class,
            Yotpo\YotpoProductRatingRetriever::class,
        );
    }

    public static function getProductReviewAdapters()
    {
        return array(
            BlockReviews\BlockReviewsProductReviewRetriever::class,
            GSnippetsReviews\GSnippetsReviewsProductReviewRetriever::class,
            LGComments\LGCommentsProductReviewRetriever::class,
            MyPrestaComments\MyPrestaCommentsProductReviewRetriever::class,
            NetReviews\NetReviewsProductReviewRetriever::class,
            ProductComments\ProductCommentsProductReviewRetriever::class,
            RevwsAvis\RevwsAvisProductReviewRetriever::class,
            SpmgsniPreview\SpmgsniProductReviewRetriever::class,
            TdProductComment\TdProductCommentProductReviewRetriever::class,
            TrustedShopsIntegration\TrustedShopsIntegrationProductReviewRetriever::class,
            Yotpo\YotpoProductReviewRetriever::class,
        );
    }
}
