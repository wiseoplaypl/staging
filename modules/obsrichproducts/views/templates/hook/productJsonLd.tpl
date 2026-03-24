{*
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
 *}
 
 {*
<script type="application/ld+json" data-keepinline>
    {
        "@context": "https://schema.org/",
        "@type": "Product",
        "@id": "{$productUrl nofilter}/#product",
        "name": "{$productName}",
        "url": "{$productUrl nofilter}",
        {if $productImages}"image": [
            {foreach $productImages as $productImage}"{$productImage nofilter}"{if !$productImage@last},
            {/if}{/foreach}

        ],{/if}

        {if $productDescription}"description": "{$productDescription}",{/if}

        {if $productSku}"sku": "{$productSku}",{/if}

        {if $productGtin13}"gtin13": "{$productGtin13}",{/if}

        {if $productIsbn}"mpn": "{$productIsbn}",{/if}

        {if $brandName}"brand": {
            "@type": "Brand",
            "name": "{$brandName}"
        },{/if}

        "offers": {
            "@type": "Offer",
            "url": "{$productUrl nofilter}",
            "priceCurrency": "{$productPriceCurrency}",
            "price": "{$productPrice}",
            {if $productPriceValidUntil}"priceValidUntil": "{$productPriceValidUntil}",{/if}
            "itemCondition": "https://schema.org/{if $productIsNew}NewCondition{else}UsedCondition{/if}",
            "availability": "https://schema.org/{if $productHasStock}InStock{else}OutOfStock{/if}"
        }{if (isset($ratingCount) && $ratingCount > 0) || (isset($reviews) && count($reviews) > 0)},{/if}

        {if isset($ratingCount) && $ratingCount > 0}
        "aggregateRating" : {
            "@type": "AggregateRating",
            "worstRating": "0",
            "ratingValue": "{$averageRating}",
            "bestRating": "5",
            "ratingCount": "{$ratingCount}"
        }{if isset($reviews) && count($reviews) > 0},{/if}
        {/if}

        {if isset($reviews) && count($reviews) > 0}
        "review" : [
            {foreach $reviews as $review}
                {if $review->getContent()}
                {
                    "@type": "Review",
                    "datePublished": "{$review->getDateAdd()}",
                    "name": "{$review->getTitle()}",
                    "reviewBody": "{$review->getContent()|escape:'html':'UTF-8'|nl2br nofilter}",
                    "reviewRating": {
                        "@type": "Rating",
                        "worstRating": "0",
                        "ratingValue": "{$review->getRating()|escape:'html':'UTF-8'}",
                        "bestRating": "5"
                    },
                    "author": {
                        "@type": "Person",
                        "name": "{$review->getAuthor()|escape:'html':'UTF-8'}"
                    }
                }{if !$review@last},{/if}
                {/if}
            {/foreach}
        ]
        {/if}
    }

</script>

 *}
 
 <script type="application/ld+json" data-keepinline>
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "@id": "{$productUrl nofilter}/#product",
  "name": "{$productName}",
  "url": "{$productUrl nofilter}",
  {if $productImages}
  "image": [
    {foreach $productImages as $productImage}"{$productImage nofilter}"{if !$productImage@last},{/if}{/foreach}
  ],
  {/if}

  {if $productDescription}
"description": "{$productDescription|strip_tags|regex_replace:"/[\r\n]+/":" "|escape:'javascript'}",
  {/if}

  {if $productSku}"sku": "{$productSku}",{/if}
  {if $productGtin13}"gtin13": "{$productGtin13}",{/if}
  {if $productIsbn}"mpn": "{$productIsbn}",{/if}

  "brand": {
    "@type": "Brand",
    "name": "Murphy Furniture"
  },

  "offers": {
    "@type": "Offer",
    "url": "{$productUrl nofilter}",
    "priceCurrency": "{$productPriceCurrency}",
    "price": "{$productPrice}",
    {if $productPriceValidUntil}"priceValidUntil": "{$productPriceValidUntil}",{/if}
    "itemCondition": "https://schema.org/{if $productIsNew}NewCondition{else}UsedCondition{/if}",
    "availability": "https://schema.org/{if $productHasStock}InStock{else}OutOfStock{/if}"
  }{if (isset($ratingCount) && $ratingCount > 0) || (isset($reviews) && count($reviews) > 0)},{/if}

  {if isset($ratingCount) && $ratingCount > 0}
  "aggregateRating": {
    "@type": "AggregateRating",
    "worstRating": "0",
    "ratingValue": "{$averageRating}",
    "bestRating": "5",
    "ratingCount": "{$ratingCount}"
  }{if isset($reviews) && count($reviews) > 0},{/if}
  {/if}

  {if isset($reviews) && count($reviews) > 0}
  "review": [
    {foreach $reviews as $review}
      {if $review->getContent()}
      {
        "@type": "Review",
        "datePublished": "{$review->getDateAdd()}",
        "name": "{$review->getTitle()}",
        "reviewBody": "{$review->getContent()|escape:'html':'UTF-8'|nl2br nofilter}",
        "reviewRating": {
          "@type": "Rating",
          "worstRating": "0",
          "ratingValue": "{$review->getRating()|escape:'html':'UTF-8'}",
          "bestRating": "5"
        },
        "author": {
          "@type": "Person",
          "name": "{$review->getAuthor()|escape:'html':'UTF-8'}"
        }
      }{if !$review@last},{/if}
      {/if}
    {/foreach}
  ]
  {/if}
}
</script>
