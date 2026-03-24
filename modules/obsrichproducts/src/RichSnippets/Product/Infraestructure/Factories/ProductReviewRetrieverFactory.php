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

use OBSolutions\RichSnippets\Product\Application\ProductReviewRetrieverInterface;
use OBSolutions\RichSnippets\Product\Application\RetrieverNotAvailable;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductReviewRetrieverFactory
{
    /**
     * @throws RetrieverNotAvailable
     *
     * @return ProductReviewRetrieverInterface
     */
    public static function create()
    {
        /** @var array<ProductReviewRetrieverInterface> $adapters */
        $adapters = ConfigFactory::getProductReviewAdapters();

        foreach ($adapters as $adapterClass) {
            if ($adapterClass::enabled()) {
                return new $adapterClass();
            }
        }

        throw new RetrieverNotAvailable(ProductReviewRetrieverInterface::class);
    }
}
