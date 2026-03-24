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

use Context;
use Module;
use OBSolutions\RichSnippets\Product\Domain\ProductRichSnippet;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductRichSnippetJsonLdPresenter
{
    /** @var Module */
    private $module;
    private $smarty;

    private $productRichSnippet;

    public function __construct(Module $module, ProductRichSnippet $productRichSnippet)
    {
        $this->smarty = Context::getContext()->smarty;
        $this->module = $module;
        $this->productRichSnippet = $productRichSnippet;
    }

    public function present()
    {
        $this->smarty->assign('productName', $this->productRichSnippet->getName());
        $this->smarty->assign('productDescription', $this->productRichSnippet->getDescription());
        $this->smarty->assign('brandName', $this->productRichSnippet->getBrandName());
        $this->smarty->assign('productImages', $this->productRichSnippet->getImagesList());
        $this->smarty->assign('productSku', $this->productRichSnippet->getSku());
        $this->smarty->assign('productIsbn', $this->productRichSnippet->getIsbn());
        $this->smarty->assign('productPrice', $this->productRichSnippet->getPrice());
        $this->smarty->assign('productPriceValidUntil', $this->productRichSnippet->getPriceValidUntil());
        $this->smarty->assign('productPriceCurrency', $this->productRichSnippet->getPriceCurrency());
        $this->smarty->assign('productHasStock', $this->productRichSnippet->hasStock());
        $this->smarty->assign('productIsNew', $this->productRichSnippet->isNew());
        $this->smarty->assign('productUrl', $this->productRichSnippet->getUrl());
        $this->smarty->assign('productGtin13', $this->productRichSnippet->getEan13());

        $this->smarty->assign('ratingCount', $this->productRichSnippet->getRatingCount());
        $this->smarty->assign('averageRating', $this->productRichSnippet->getAverageRating());
        $this->smarty->assign('reviews', $this->productRichSnippet->getReviews());

        return $this->module->display($this->module->name, 'views/templates/hook/productJsonLd.tpl');
    }
}
