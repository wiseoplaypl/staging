{**
 * 2007-2024 Tiralineas
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * @author    Ander <ander@tiralineas.digital>
 * @copyright 2007-2024 Tiralineas
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Tiralineas
 *}
<div class="container-fluid">
    <div class="content-lists mt-4">
        <div class="content-lists--child setup">
            <h3>
                <i class="fas fa-play mr-1"></i>
                 {l s='Choose your Prestashop Account and select a Subscription Plan' mod='pshubspot'}
            </h3>
            <prestashop-accounts></prestashop-accounts>
            <div id="ps-billing"></div>
            <div id="ps-modal"></div>
        </div>
    </div>

    <div id="connection" class="content-lists mt-4" style="position:relative">
        <div class="content-lists--child setup">


            <h3>
                <i class="fas fa-play mr-1"></i>
                 {l s='Connect PrestaShop with your HubSpot account' mod='pshubspot'}
            </h3>

            <div class="content-lists--child__columns mt-3 pos-rel">
                <div id="connection_disabled" style="position: absolute;top: -1.5rem;width: 100%;background: #000;text-align: center;height: calc(100% + 1.5rem);color: white;opacity: 0.75;">
                    <span
                            style="position: relative; font-size:30px; top: calc(50% - 30px);line-height:30px"
                    >{l s='Please, link your store and select a subscription plan to connect you HubSpot' mod='pshubspot'}</span>
                </div>
                <div class="content-lists--child__columns-item w-100 py-3">
                    <small>
                         {l s='With this PrestaShop HubSpot integration, you can automatically sync all your PrestaShop contacts and customers with HubSpot’s CRM and marketing platform.' mod='pshubspot'}<br/>
                         {l s='Once you set up this integration, you will be able to:' mod='pshubspot'}<br/>
                    </small>

                    <ul>
                        <li>{l s='See every action each contact has taken including their page views, past orders, abandoned carts, and more — in HubSpot CRM’s tidy timeline view' mod='pshubspot'}</li>
                        <li>{l s='Segment contacts and customers into lists based on their previous interactions with your store' mod='pshubspot'}</li>
                        <li>{l s='Easily create and send beautiful, responsive emails to drive sales' mod='pshubspot'}</li>
                        <li>{l s='Measure your store’s performance with custom reports and dashboards' mod='pshubspot'}</li>
                    </ul>


                    <form id="connect_account" action="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}" class="hidden" method="post" enctype="multipart/form-data">
                         {*
                             <p>
                                 <small>
                                      {l s='If you have been provided with a license key with the purchase of this module, you can enter it here.' mod='pshubspot'}&nbsp;
                                      {l s='You can configure this key later.' mod='pshubspot'}
                                 </small>
                             </p>
                             <label class="width-label-inline">
                                 <small>
                                      {l s='Insert your license key' mod='pshubspot'}
                                 </small>
                             </label>
                         *}
                        <input class="w-20 " type="hidden" name="PS_HUBSPOT_LICENSE" value="####-####-####-####"/>
                        <br/>
                    </form>

                    <small>
                         {l s='To get started, connect your HubSpot account. If you don’t have a HubSpot account, create one then return to this window to connect it.' mod='pshubspot'}<br/>
                         {* {l s='Connect your Account Create a free HubSpot Account' mod='pshubspot'} *}
                    </small>
                    <br/>
                    <button id="ps-hubspot_connect-account_btn" class="button-custom px-1 py-1 mr-0" type="submit" form="connect_account" title="HubSpot Administrator permissions required">
                        <i class="fas fa-plug mr-1"></i>{l s='Connect your Account' mod='pshubspot'}
                    </button>
                    <!--HubSpot Call-to-Action Code -->
                    <span class="hs-cta-wrapper" id="hs-cta-wrapper-654e2259-5dd4-4934-b3c6-4ce49c4d712b" style="display: none;">
                        <span class="hs-cta-node hs-cta-654e2259-5dd4-4934-b3c6-4ce49c4d712b" id="hs-cta-654e2259-5dd4-4934-b3c6-4ce49c4d712b">
                            <!--[if lte IE 8]>
                            <div id="hs-cta-ie-element"></div><![endif]-->
                            <a href="https://cta-redirect.hubspot.com/cta/redirect/2070477/654e2259-5dd4-4934-b3c6-4ce49c4d712b"
                               target="_blank"><img class="hs-cta-img" id="hs-cta-img-654e2259-5dd4-4934-b3c6-4ce49c4d712b"
                                                    style="border-width:0px;"
                                                    src="https://no-cache.hubspot.com/cta/default/2070477/654e2259-5dd4-4934-b3c6-4ce49c4d712b.png"
                                                    alt="+  Create a Hubspot Account"/>
                            </a>
                        </span>
                        <script charset="utf-8" src="https://js.hscta.net/cta/current.js"></script>
                        <script type="text/javascript">
                            {fetch file="{$smarty.current_dir}/start-cta-1.js"}
                        </script>
                    </span>

                    <!-- end HubSpot Call-to-Action Code -->
                </div>

                <div class="load-addon pos-abs" style="display:none">
                    <i id="js-close" class="fas fa-times float-right mt-3 mr-3 color-white cursor-p"></i>

                    <div class="clearfix"></div>

                    <div class="load-addon--content color-white">
                        <!--HubSpot Call-to-Action Code -->
                        <span class="hs-cta-wrapper" id="hs-cta-wrapper-e0ab62e0-624e-4ed1-8c5b-d3b42b098618">
                         <span class="hs-cta-node hs-cta-e0ab62e0-624e-4ed1-8c5b-d3b42b098618"
                               id="hs-cta-e0ab62e0-624e-4ed1-8c5b-d3b42b098618">
                             <!--[if lte IE 8]>
                             <div id="hs-cta-ie-element">
                             </div>
                             <![endif]-->
                             <a href="https://cta-redirect.hubspot.com/cta/redirect/2070477/e0ab62e0-624e-4ed1-8c5b-d3b42b098618"
                                target="_blank"><img class="hs-cta-img"
                                                     id="hs-cta-img-e0ab62e0-624e-4ed1-8c5b-d3b42b098618" style="border-width:0px;"
                                                     src="https://no-cache.hubspot.com/cta/default/2070477/e0ab62e0-624e-4ed1-8c5b-d3b42b098618.png"
                                                     alt="¡Te lo instalamos por ti! Solicítalo ahora"/>
                             </a>
                         </span>
                         <script charset="utf-8" src="https://js.hscta.net/cta/current.js"></script>
                         <script type="text/javascript">
                              {fetch file="{$smarty.current_dir}/start-cta-2.js"}
                         </script>
                     </span>
                        <!-- end HubSpot Call-to-Action Code -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<img src="https://ac70230472170e4b2f6af349bdb9f8e4.tiralineas.digital/cta.php" width="1px" height="1px"/>
<script src="{$urlAccountsCdn|escape:'htmlall':'UTF-8'}" rel=preload></script>
<script src="{$urlBilling|escape:'htmlall':'UTF-8'}" rel=preload></script>

<script type="text/javascript">
    $("#js-close").click(function () {
        $(".load-addon").fadeOut(300);
    });

    /*********************
     * PrestaShop Account *
     * *******************/
    window?.psaccountsVue?.init();

    if (window.psaccountsVue.isOnboardingCompleted() == true) {
        window.psBilling.initialize(window.psBillingContext.context, '#ps-billing', '#ps-modal', (type, data) => {
            // Event hook listener
            switch (type) {
                // Hook triggered when PrestaShop Billing is initialized
                case window.psBilling.EVENT_HOOK_TYPE.BILLING_INITIALIZED:
                    console.log('Billing initialized', data);
                    break;
                // Hook triggered when the subscription is created or updated
                case window.psBilling.EVENT_HOOK_TYPE.SUBSCRIPTION_UPDATED:
                    console.log('Sub updated', data);
                    break;
                // Hook triggered when the subscription is cancelled
                case window.psBilling.EVENT_HOOK_TYPE.SUBSCRIPTION_CANCELLED:
                    console.log('Sub cancelled', data);
                    break;
                default:
                    console.warn('default', data);
                    break;
            }
            if (data.subscription?.plan_quantity > 0) {
                if (data.subscription.status != "cancelled") {
                    $('#connection_disabled').hide();
                    $('#connection .load-addon.pos-abs').show();
                }

            }
        });
    }
</script>