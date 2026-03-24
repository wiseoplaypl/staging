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

namespace OBSolutions\RichSnippets\Product\Infraestructure\Adapters\TrustedShopsIntegration;

use OBSolutions\RichSnippets\Product\Application\ProductRatingRetrieverInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductRating;

if (!defined('_PS_VERSION_')) {
    exit;
}

class TrustedShopsIntegrationProductRatingRetriever extends TrustedShopsIntegrationProductRetriever implements ProductRatingRetrieverInterface
{
    /**
     * @param int $productId
     *
     * @return ProductRating
     */
    public function getProductRating(ProductInterface $product)
    {
        if (!$product->getSku()) {
            return new ProductRating(0, 5);
        }

        $encodedSku = $this->encodeSku($product->getSku());

        $ratingJson = Tools::file_get_contents('https://cdn1.api.trustedshops.com/shops/'.self::$trustedShopId.'/products/skus/'.$encodedSku.'/productstickersummaries/v1/quality/reviews.json');
        $rating = json_decode($ratingJson);

        if (!isset($rating->response->data->product->qualityIndicators->reviewIndicator)) {
            return new ProductRating(0, 5);
        }

        return new ProductRating(
            $rating->response->data->product->qualityIndicators->reviewIndicator->totalReviewCount,
            $rating->response->data->product->qualityIndicators->reviewIndicator->overallMark
        );
    }
}
