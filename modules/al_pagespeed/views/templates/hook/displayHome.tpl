<style>
.top-column .row {
   justify-content: center;display:flex
}
@media (max-width: 768px) {
    .top-column .row {
        flex-direction: column;
    }

    .top-column .col-md-6.col-lg-5 {
        max-width: 100%;
        flex: 0 0 100%;
    }
}
</style>

<div class="container mt-1 top-column text-center">
    <div class="row" style="">
        <div class="col-md-6 col-lg-5 text-center"><h3 style="padding:8px; background:#2E3F50;color:#fff; font-weight:bold;">Delivery Nationwide</h3></div>
        <div class="col-md-6 col-lg-5 text-center"><h3 style="padding:8px; background:#2E3F50;color:#fff; font-weight:bold;">Free Collection from our 5 stores</h3></div>
        <div class="col-md-6 col-lg-5 text-center"><h3 style="padding:8px; background:#2E3F50;color:#fff;font-weight:bold;">Shops in Dublin Naas Carlow Gorey Wexford</h3></div>
    </div>
</div>


<section class="section-margin-top promo-banner-wrapper banner-box clearfix">
    <div class="promo-banner-inner">
        <div class="banner1">
            <div class="promo-banner">
                <a href="/furniture-shop/mattresses">
                    <img src="/img/promo/{$mprefix}1.webp" alt="Banner1"  {$sizes nofilter}/>
                </a>
                <div class="promo-text-wrapper">
                    <div class="promo-text">
                        <h5 class="promo-title">{l s='Mattresses' mod='al_pagespeed'}</h5>
                        <div class="promo-desc"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="banner2">
            <div class="promo-banner">
                <a href="/furniture-shop/sofas">
                    <img src="/img/promo/{$mprefix}2.webp" alt="Banner2"  {$sizes nofilter}/>
                </a>
                <div class="promo-text-wrapper">
                    <div class="promo-text">
                        <h5 class="promo-title"><span>{l s='Sofas' mod='al_pagespeed'}</span></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="banner3">
            <div class="promo-banner">
                <a href="/furniture-shop/beds">
                    <img src="/img/promo/{$mprefix}3.webp" alt="Banner3" {$sizes nofilter}/>
                </a>
                <div class="promo-text-wrapper">
                    <div class="promo-text"><h5 class="promo-title">{l s='Beds' mod='al_pagespeed'}</h5></div>
                </div>
            </div>
        </div>
    </div>
</section>
