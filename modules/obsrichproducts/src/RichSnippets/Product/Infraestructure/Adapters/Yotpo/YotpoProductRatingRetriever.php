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

namespace OBSolutions\RichSnippets\Product\Infraestructure\Adapters\Yotpo;

use OBSolutions\RichSnippets\Product\Application\ProductRatingRetrieverInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductInterface;
use OBSolutions\RichSnippets\Product\Domain\ProductRating;

if (!defined('_PS_VERSION_')) {
    exit;
}

class YotpoProductRatingRetriever implements ProductRatingRetrieverInterface
{
    /**
     * @return bool
     */
    public static function enabled()
    {
        return (bool) \Configuration::get('yotpo_app_key');
    }

    /**
     * @param int $productId
     *
     * @return ProductRating
     */
    public function getProductRating(ProductInterface $product)
    {
        $cacheKey = 'OBSRichProducts_ProductRating_'.$product->getId();
        if (!\Cache::isStored($cacheKey)) {
            $timeout = 3;
            $url = 'https://api.yotpo.com/products/'.\Configuration::get('yotpo_app_key').'/'.$product->getId().'/bottomline';

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Added by PrestaShop
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Added by PrestaShop
            $result = curl_exec($ch);
            curl_close($ch);

            $payload = json_decode($result);

            $productRating = new ProductRating(0, 5);
            if (isset($payload->status, $payload->status->code)
                && 200 == $payload->status->code
                && isset($payload->response, $payload->response->bottomline, $payload->response->bottomline->average_score, $payload->response->bottomline->total_reviews)
            ) {
                $productRating = new ProductRating($payload->response->bottomline->total_reviews, $payload->response->bottomline->average_score);
            }

            \Cache::store($cacheKey, $productRating);
        }

        return \Cache::retrieve($cacheKey);
    }
}
