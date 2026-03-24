{**
 * Copyright 2024 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *}

<!-- Google Reviews Hook -->
{if $reviews}
    {foreach from=$reviews item=place key=google_place_id}
        <div class='lggooglereviews_place container'>
            <script type="text/javascript">
                var lggooglereviews_owl_{$place['id_lggooglereviews_place']|intval} = {$place['num_reviews']|intval};
            </script>
            {if $place.show_header}
                <div class='lggooglereviews_header'>
                    <div class='lg-google-place'>
                        {if $place.show_image_header}
                            <div class='lg-google-left'>
                                {if $place['logo']!=''}
                                    <img src="{$place['logo']|escape:'html':'UTF-8'}" alt="{$place['api']['name']|escape:'html':'UTF-8'}"
                                        width="80" height="80" title="{$place['api']['name']|escape:'html':'UTF-8'}" />
                                {else}
                                    <img src="{$place['api']['icon']|escape:'html':'UTF-8'}" alt="{$place['api']['name']|escape:'html':'UTF-8'}"
                                        width="80" height="80" title="{$place['api']['name']|escape:'html':'UTF-8'}" />
                                {/if}
                            </div>
                        {/if}
                        <div class='lg-google-right'>
                            <div class='lg-google-name'>
                                <div>
                                    {$place['api']['name']|escape:'html':'UTF-8'}
                                </div>
                                {if $place.show_link_business}
                                    <a href="{$place['api']['url']|escape:'html':'UTF-8'}"
                                        target="_blank" rel="nofollow noopener" class="header_link_reviews">
                                        <span>{l s='See more' mod='lggooglereviews'}</span>
                                    </a>
                                {/if}
                            </div>
                        </div>
                        {if $place.show_total_ratings}
                            <div>
                                <span class='lg-google-rating'>{$place['api']['rating']|escape:'html':'UTF-8'}</span>
                                <span class='lg-google-stars'>
                                    <span class='lg-stars'>
                                        {if $is_17}
                                            {include file="module:lggooglereviews/views/templates/hook/stars.tpl" rate=$place['api']['rating']}
                                        {else}
                                            {include file = './stars.tpl' rate=$place['api']['rating']}
                                        {/if}
                                    </span>
                                </span>
                            </div>
                            <div class='lg-google-based'>
                                {l s='Based on' mod='lggooglereviews'}
                                {$place['api']['user_ratings_total']|escape:'html':'UTF-8'}
                                {l s='reviews' mod='lggooglereviews'}
                            </div>
                        {/if}
                        {if $place.show_powered_by_google}
                            <div class='lg-google-powered'>
                                <img src="{$url_img|escape:'html':'UTF-8'}powered_by_google_on_white.png"
                                    alt="powered by Google" width="144" height="18" title="powered by Google" />
                            </div>
                        {/if}
                        {if $place.show_link_add_review && $place.url_add_review!=''}
                            <a href="{$place['url_add_review']|escape:'html':'UTF-8'}"
                                class="lg-google-url" target="_blank" rel="nofollow noopener">
                                {l s='Add a new comment' mod='lggooglereviews'}
                            </a>
                        {/if}
                        {if $place.show_link_all_reviews}
                            <a href="https://search.google.com/local/reviews?placeid={$place['api']['place_id']|escape:'html':'UTF-8'}"
                                class="lg-google-url" target="_blank" rel="nofollow noopener">
                                {l s='Read all comments' mod='lggooglereviews'}
                            </a>
                        {/if}
                    </div>
                </div>
            {/if}

            <div class='lggooglereviews_list' {if $place['display_snippets'] && count($place['api']['reviews'])} itemscope itemtype='https://schema.org/LocalBusiness'{/if}>
                {if $place['display_snippets'] && count($place['api']['reviews'])}
                    <meta itemprop="image" content="{$place['api']['icon']|escape:'htmlall':'UTF-8'}"/>
                    <meta itemprop="name" content="{$place['api']['name']|escape:'quotes':'UTF-8'}"/>
                    <span itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                        {if isset($place['api']['address_components'][0]) ||
                            isset($place['api']['address_components'][1]) ||
                            isset($place['api']['address_components'][2])}
                            <meta itemprop="streetAddress" content="{if $place['api']['address_components'][2]}{$place['api']['address_components'][2]['long_name']|escape:'quotes':'UTF-8'}{/if} {if $place['api']['address_components'][1]}{$place['api']['address_components'][1]['long_name']|escape:'quotes':'UTF-8'}{/if} {if $place['api']['address_components'][0]}{$place['api']['address_components'][0]['long_name']|escape:'quotes':'UTF-8'}{/if}"/>
                        {/if}
                        {if isset($place['api']['address_components'][7])}
                            <meta itemprop="postalCode" content="{$place['api']['address_components'][7]['long_name']|escape:'quotes':'UTF-8'}"/>
                        {/if}
                        {if isset($place['api']['address_components'][3])}
                            <meta itemprop="addressLocality" content="{$place['api']['address_components'][3]['long_name']|escape:'quotes':'UTF-8'}"/>
                        {/if}
                        {if isset($place['api']['address_components'][4])}
                            <meta itemprop="addressRegion" content="{$place['api']['address_components'][4]['long_name']|escape:'quotes':'UTF-8'}"/>
                        {/if}
                        {if isset($place['api']['address_components'][6])}
                            <meta itemprop="addressCountry" content="{$place['api']['address_components'][6]['long_name']|escape:'quotes':'UTF-8'}"/>
                        {/if}
                    </span>
                    {if isset($place['api']['formatted_phone_number'])}
                        <meta itemprop="telephone" content="{$place['api']['formatted_phone_number']|escape:'quotes':'UTF-8'}"/>
                    {/if}
                    <meta itemprop="url" content="{$place['api']['url']|escape:'quotes':'UTF-8'}"/>
                    <span itemprop="aggregateRating" itemscope="itemscope" itemtype="https://schema.org/AggregateRating">
                        <meta content="{$place['api']['rating']|escape:'quotes':'UTF-8'}" itemprop="ratingValue"/>
                        <meta content="5" itemprop="bestRating"/>
                        <meta content="{$place['api']['name']|escape:'quotes':'UTF-8'}" itemprop="itemReviewed"/>
                        <meta content="{$place['api']['user_ratings_total']|escape:'quotes':'UTF-8'}" itemprop="ratingCount"/>
                    </span>
                {/if}

                <div id="lggooglereviews-owl-{$place['id_lggooglereviews_place']|intval}" data-id={$place['id_lggooglereviews_place']|intval} class="lggooglereviews-owl owl-carousel owl-theme">
                    {foreach from=$place['api']['reviews'] item=review key=review_id}
                        <div class="item">
                            <div class="slide-container"{if $place['display_snippets']} itemprop="review" itemscope itemtype="https://schema.org/Review"{/if}>
                                <div class="lg-google-review">
                                    <div class="lg-google-left">
                                        <img src="{$review['profile_photo_url']|escape:'html':'UTF-8'}"
                                            class="user-avatar"
                                            alt="{$review['author_name']|escape:'html':'UTF-8'}" width="50" height="50"
                                            title="{$review['author_name']|escape:'html':'UTF-8'}"
                                            onerror="if(this.src!='https://lh3.googleusercontent.com/-8hepWJzFXpE/AAAAAAAAAAI/AAAAAAAAAAA/I80WzYfIxCQ/s50-c/114307615494839964028.jpg')this.src='https://lh3.googleusercontent.com/-8hepWJzFXpE/AAAAAAAAAAI/AAAAAAAAAAA/I80WzYfIxCQ/s50-c/114307615494839964028.jpg';"/>
                                    </div>
                                    <div class="lg-google-right">
                                        {if $place.show_user_links}
                                            <a href="{$review['author_url']|escape:'html':'UTF-8'}"
                                            class="lg-google-name" target="_blank" rel="nofollow noopener">
                                        {/if}
                                            <span class="slide-name" {if $place['display_snippets']} itemprop="author" itemscope itemtype="https://schema.org/Person"{/if}>
                                                {if $place['display_snippets']}
                                                    <span itemprop="name">{$review['author_name']|escape:'quotes':'UTF-8'|truncate:18}</span>
                                                {else}
                                                    {$review['author_name']|escape:'quotes':'UTF-8'|truncate:18}
                                                {/if}
                                            </span>
                                        {if $place.show_user_links}
                                            </a>
                                        {/if}
                                        <div class="lg-google-time" data-time="{$review['time']|escape:'quotes':'UTF-8'}" {if $place['display_snippets']}itemprop="datePublished" content="{$review.time|escape:'quotes':'UTF-8'}"{/if}>
                                            {$review['relative_time_description']|escape:'quotes':'UTF-8'}
                                        </div>
                                        <div class="lg-google-feedback">
                                            <span class="lg-google-stars">
                                                {if $is_17}
                                                    {include file="module:lggooglereviews/views/templates/hook/stars.tpl" rate=$review['rating']}
                                                {else}
                                                    {include file = './stars.tpl' rate=$review['rating']}
                                                {/if}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg-google-text" {if $place['display_snippets']} itemprop="description"{/if}>
                                    {$review['text']|escape:'quotes':'UTF-8'|truncate:250}
                                </div>
                                {if $place['display_snippets']}
                                    <span itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating" class="rating-hidden">
                                        <meta itemprop="ratingValue" content="{$review['rating']|intval|escape:'htmlall':'UTF-8'}"/>
                                        <meta itemprop="bestRating" content="5"/>
                                        <meta itemprop="worstRating" content="0"/>
                                    </span>
                                {/if}
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    {/foreach}
{/if}
<!-- End Google Reviews Hook -->
