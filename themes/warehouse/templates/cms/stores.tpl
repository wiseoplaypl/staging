{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='page.tpl'}

{block name='page_title'}
  {l s='Our stores' d='Shop.Theme.Global'}
{/block}

{block name='page_content_container'}

{* LocalBusiness JSON-LD schema for each store — Murphy Furniture *}
{foreach $stores as $store}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FurnitureStore",
  "name": "{$store.name|escape:'html':'UTF-8'}",
  {if !empty($store.image.bySize.stores_default.url)}"image": "{$store.image.bySize.stores_default.url}",{/if}
  {if $store.phone}"telephone": "{$store.phone|escape:'html':'UTF-8'}",{/if}
  {if $store.email}"email": "{$store.email|escape:'html':'UTF-8'}",{/if}
  "url": "https://www.murphyfurniture.ie/content/4-stores",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "{if !empty($store.address.address1)}{$store.address.address1|escape:'html':'UTF-8'}{if !empty($store.address.address2)}, {$store.address.address2|escape:'html':'UTF-8'}{/if}{/if}",
    "addressLocality": "{if !empty($store.address.city)}{$store.address.city|escape:'html':'UTF-8'}{/if}",
    "addressRegion": "IE",
    "postalCode": "{if !empty($store.address.postcode)}{$store.address.postcode|escape:'html':'UTF-8'}{/if}",
    "addressCountry": "IE"
  },
  "openingHoursSpecification": [
    {foreach from=$store.business_hours item="day" name="hours_loop"}
    {foreach from=$day.hours item="h" name="h_loop"}
    {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": "https://schema.org/{$day.day|escape:'html':'UTF-8'}",
      "opens": "{$h|regex_replace:'/^([0-9:]+)\s*-.*$/':'\\1'|escape:'html':'UTF-8'}",
      "closes": "{$h|regex_replace:'/^.*-\s*([0-9:]+)$/':'\\1'|escape:'html':'UTF-8'}"
    }{if !$smarty.foreach.hours_loop.last || !$smarty.foreach.h_loop.last},{/if}
    {/foreach}
    {/foreach}
  ],
  "parentOrganization": {
    "@type": "Organization",
    "name": "Murphy Furniture",
    "url": "https://www.murphyfurniture.ie"
  }
}
</script>
{/foreach}

  <section id="content" class="page-content page-stores">

    {foreach $stores as $store}
      <article id="store-{$store.id}" class="store-item">
        <div class="store-item-container clearfix">
          <div class="col-md-3 store-picture hidden-sm-down">
            <picture>
              {if !empty($store.image.bySize.stores_default.sources.avif)}<source srcset="{$store.image.bySize.stores_default.sources.avif}" type="image/avif">{/if}
              {if !empty($store.image.bySize.stores_default.sources.webp)}<source srcset="{$store.image.bySize.stores_default.sources.webp}" type="image/webp">{/if}
              <img
                src="{$store.image.bySize.stores_default.url}"
                class="img-fluid"
                loading="lazy"
                {if !empty($store.image.legend)}
                  alt="{$store.image.legend}"
                  title="{$store.image.legend}"
                {else}
                  alt="{$store.name}"
                {/if}
              >
            </picture>

          </div>
          <div class="col-md-5 col-sm-7 col-xs-12 store-description">
            <p class="h3 card-title">{$store.name}</p>
            <address>{$store.address.formatted nofilter}</address>
            {if $store.note || $store.phone || $store.fax || $store.email}
              <a data-bs-toggle="collapse" href="#about-{$store.id}" aria-expanded="false" aria-controls="about-{$store.id}"><strong>{l s='About and Contact' d='Shop.Theme.Global'}</strong><i class="fa fa-angle-right" aria-hidden="true"></i></a>
            {/if}
          </div>
          <div class="col-md-4 col-sm-5 col-xs-12 divide-left">
            <table>
              {foreach $store.business_hours as $day}
              <tr>
                <th>{$day.day|truncate:4:'.'}</th>
                <td>
                  <ul>
                  {foreach $day.hours as $h}
                    <li>{$h}</li>
                  {/foreach}
                  </ul>
                </td>
              </tr>
              {/foreach}
            </table>
          </div>
        </div>
        <footer id="about-{$store.id}" class="collapse">
          <div class="store-item-footer divide-top">
            {if $store.note}
            <div class="card-body">
                <p class="text-justify">{$store.note}<p>
            </div>
            {/if}
            <ul class="card-body">
              {if $store.phone}
                <li><i class="fa fa-phone" aria-hidden="true"></i><a href="tel:{$store.phone}">{$store.phone}</a></li>
              {/if}
              {if $store.fax}
                <li><i class="fa fa-fax" aria-hidden="true"></i>{$store.fax}</li>
              {/if}
              {if $store.email}
                <li><i class="fa fa-envelope-o" aria-hidden="true"></i><a href="mailto:{$store.email}">{$store.email}</a></li>
              {/if}
            </ul>
          </div>
        </footer>
      </article>
    {/foreach}

  </section>
{/block}
