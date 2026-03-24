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

namespace OBSolutions\RichSnippets\Product\Application;

use OBSolutions\RichSnippets\Product\Infraestructure\Factories\ProductRatingRetrieverFactory;
use OBSolutions\RichSnippets\Product\Infraestructure\Factories\ProductReviewRetrieverFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GetProductRichSnippetQueryHandler
{
    private $productRichSnippetCreator;

    public function __construct()
    {
        $this->productRichSnippetCreator = new ProductRichSnippetCreator();
    }

    public function handle(GetProductRichSnippetQuery $command)
    {
        $product = $command->getProduct();

        try {
            $productRatingRetriever = ProductRatingRetrieverFactory::create();
            $this->productRichSnippetCreator->setProductRatingRetriever($productRatingRetriever);
        } catch (RetrieverNotAvailable $exception) {
        }

        try {
            $productReviewRetriever = ProductReviewRetrieverFactory::create();
            $this->productRichSnippetCreator->setProductReviewRetriever($productReviewRetriever);
        } catch (RetrieverNotAvailable $exception) {
        }

        return $this->productRichSnippetCreator->create($product);
    }
}
