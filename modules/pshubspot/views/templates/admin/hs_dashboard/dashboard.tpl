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
 <div id="pshubspot_container" class="container-fluid font-family-default">
    <div class="text-center mx-3">
         {if $PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED}
            <h3 class="mt-3">
                <p><b><i class="fas fa-clock mr-1"></i>{l s='Synchronized deals from %s on' sprintf=[$PS_HUBSPOT_DEAL_DATE_FILTER] mod='pshubspot'}</b></p>
            </h3>
         {/if}
        <p>
            <span id="CONTACT">
                <b class='sync'>{$CONTACT_sync|escape:'htmlall':'UTF-8'}</b>&nbsp;{l s='of' mod='pshubspot'}
                <b class='total'>{$CONTACT_unsync + $CONTACT_sync|escape:'htmlall':'UTF-8'}</b>&nbsp;{l s='Contacts' mod='pshubspot'}
            </span> -
            <span id="PRODUCT">
                <b class='sync'>{$PRODUCT_sync|escape:'htmlall':'UTF-8'}</b>&nbsp;{l s='of' mod='pshubspot'}
                <b class='total'>{$PRODUCT_unsync + $PRODUCT_sync|escape:'htmlall':'UTF-8'}</b>&nbsp;{l s='Products' mod='pshubspot'}
            </span> -
            <span id="DEAL"><b class='sync'>{$DEAL_sync|escape:'htmlall':'UTF-8'}</b>&nbsp;{l s='of' mod='pshubspot'}
                <b class='total'>{$DEAL_unsync + $DEAL_sync|escape:'htmlall':'UTF-8'}</b>&nbsp;{l s='Deals' mod='pshubspot'}
            </span>
        </p>
        <p>
            <span id="DEALABANDONED"><b class='sync'>{$DEALABANDONED_sync|escape:'htmlall':'UTF-8'}</b>&nbsp;{l s='of' mod='pshubspot'}
                <b class='total'>{$DEALABANDONED_unsync + $DEALABANDONED_sync|escape:'htmlall':'UTF-8'}</b>&nbsp;{l s='Carts synchronize' mod='pshubspot'}
            </span>
        </p>

        <button class="mt-3 button-custom px-1 py-1" type="button" onclick="psHubspotSyncrhonize()" id="ps-hubspot_sync_btn">
            <i class="fas fa-sync mr-1"></i>
             {l s='Synchronize' mod='pshubspot'}
        </button>
    </div>

    <div class="mt-3"> <!-- alert-custom -->
        <!-- {l s='During the month of June 2021, for the same price of your addon, you have the configuration included. Leave it to us, we connect PrestaSyncro for you! This configuration includes the installation of the addon, the connection with the HubSpot account, the synchronization from a desired date and the creation of a standard workflow for the shopping cart abandonment recovery.' mod='pshubspot'} -->
        <!--HubSpot Call-to-Action Code -->
        <span class="hs-cta-wrapper" id="hs-cta-wrapper-d7f4dd49-5712-474a-97e3-3fefc0404b5c">
            <span class="hs-cta-node hs-cta-d7f4dd49-5712-474a-97e3-3fefc0404b5c" id="hs-cta-d7f4dd49-5712-474a-97e3-3fefc0404b5c">
                <!--[if lte IE 8]>
                <div id="hs-cta-ie-element"></div><![endif]-->
                <a href="https://cta-redirect.hubspot.com/cta/redirect/2070477/d7f4dd49-5712-474a-97e3-3fefc0404b5c" target="_blank" rel="noopener">
                    <img class="hs-cta-img" id="hs-cta-img-d7f4dd49-5712-474a-97e3-3fefc0404b5c" style="border-width:0px;"
                         src="https://no-cache.hubspot.com/cta/default/2070477/d7f4dd49-5712-474a-97e3-3fefc0404b5c.png"
                         alt="During the month of June 2021, for the same price of your addon, you have the  configuration included. Leave it to us, we connect PrestaSyncro for you! This  configuration includes the installation of the addon, the connection with the  HubSpot account, the synchronization from a desired date and the creation of a  standard workflow for the shopping cart abandonment recovery.  Ask for configuration"/>
                </a>
            </span>

            <script charset="utf-8" src="https://js.hscta.net/cta/current.js"></script>
            <script type="text/javascript">
                {fetch file="{$smarty.current_dir}/dashboard-cta-1.js"}
            </script>
        </span>
        <!-- end HubSpot Call-to-Action Code -->
    </div>

    <div class=" content-lists mt-5">
        <div class="content-lists--child mr-2 col-md-4 col-sm-12 contacts"> <!-- flex-wrap dashboard-provisional -->
            <h3>
                <i class="fas fa-filter mr-1"></i>
                 {l s='Resources' mod='pshubspot'}
            </h3>

            <div class="text-center p-5">
                <p class="pb-5">
                    <small>
                        <!-- CTA -->
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
                        <!--HubSpot Call-to-Action Code -->
                        <span class="hs-cta-wrapper" id="hs-cta-wrapper-abf5b977-285e-4c76-afe1-5f0ab5441f2b">
                            <span class="hs-cta-node hs-cta-abf5b977-285e-4c76-afe1-5f0ab5441f2b" id="hs-cta-abf5b977-285e-4c76-afe1-5f0ab5441f2b">
                                <!--[if lte IE 8]>
                                <div id="hs-cta-ie-element"></div><![endif]-->
                                <a href="https://cta-redirect.hubspot.com/cta/redirect/2070477/abf5b977-285e-4c76-afe1-5f0ab5441f2b">
                                    <img class="hs-cta-img" id="hs-cta-img-abf5b977-285e-4c76-afe1-5f0ab5441f2b" style="border-width:0px;"
                                         src="https://no-cache.hubspot.com/cta/default/2070477/abf5b977-285e-4c76-afe1-5f0ab5441f2b.png"
                                         alt="Here you will find resources and ideas to get the most out of your PrestaShop integration with HubSpot. We provide you guides and templates to make your  abandoned shopping carts profitable, activate promotions…"/>
                                </a>
                            </span>

                            <script charset="utf-8" src="https://js.hscta.net/cta/current.js"></script>
                            <script type="text/javascript">
                                {fetch file="{$smarty.current_dir}/dashboard-cta-2.js"}
                            </script>
                        </span>
                        <!-- end HubSpot Call-to-Action Code -->
                    </small>
                </p>
            </div>
        </div>

        <div class="content-lists--child col-md-4 col-sm-12 contacts"> <!-- flex-wrap dashboard-provisional -->
            <h3>
                <i class="fas fa-filter mr-1"></i>
                 {l s='Resources' mod='pshubspot'}
            </h3>

            <div class="text-center p-5">
                <p class="pb-5">
                    <small>
                        <!-- CTA -->
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
                        <!--HubSpot Call-to-Action Code -->
                        <span class="hs-cta-wrapper" id="hs-cta-wrapper-abf5b977-285e-4c76-afe1-5f0ab5441f2b">
                            <span class="hs-cta-node hs-cta-abf5b977-285e-4c76-afe1-5f0ab5441f2b" id="hs-cta-abf5b977-285e-4c76-afe1-5f0ab5441f2b">
                                <!--[if lte IE 8]>
                                <div id="hs-cta-ie-element"></div><![endif]-->
                                <a href="https://cta-redirect.hubspot.com/cta/redirect/2070477/abf5b977-285e-4c76-afe1-5f0ab5441f2b">
                                    <img class="hs-cta-img" id="hs-cta-img-abf5b977-285e-4c76-afe1-5f0ab5441f2b" style="border-width:0px;"
                                         src="https://no-cache.hubspot.com/cta/default/2070477/abf5b977-285e-4c76-afe1-5f0ab5441f2b.png"
                                         alt="Here you will find resources and ideas to get the most out of your PrestaShop integration with HubSpot. We provide you guides and templates to make your  abandoned shopping carts profitable, activate promotions…"/>
                                </a>
                            </span>

                            <script charset="utf-8" src="https://js.hscta.net/cta/current.js"></script>
                            <script type="text/javascript">
                                {fetch file="{$smarty.current_dir}/dashboard-cta-3.js"}
                            </script>
                        </span>
                        <!-- end HubSpot Call-to-Action Code -->
                    </small>
                </p>
            </div>
        </div>

        <div class="content-lists--child ml-2 col-md-4 col-sm-12 contacts"> <!-- flex-wrap dashboard-provisional -->
            <h3>
                <i class="fas fa-filter mr-1"></i>
                 {l s='Resources' mod='pshubspot'}
            </h3>

            <div class="text-center p-5">
                <p class="pb-5">
                    <small>
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
                        <!--HubSpot Call-to-Action Code -->
                        <span class="hs-cta-wrapper" id="hs-cta-wrapper-abf5b977-285e-4c76-afe1-5f0ab5441f2b">
                            <span class="hs-cta-node hs-cta-abf5b977-285e-4c76-afe1-5f0ab5441f2b" id="hs-cta-abf5b977-285e-4c76-afe1-5f0ab5441f2b">
                                <!--[if lte IE 8]>
                                <div id="hs-cta-ie-element"></div><![endif]-->
                                <a href="https://cta-redirect.hubspot.com/cta/redirect/2070477/abf5b977-285e-4c76-afe1-5f0ab5441f2b">
                                    <img class="hs-cta-img" id="hs-cta-img-abf5b977-285e-4c76-afe1-5f0ab5441f2b" style="border-width:0px;"
                                         src="https://no-cache.hubspot.com/cta/default/2070477/abf5b977-285e-4c76-afe1-5f0ab5441f2b.png"
                                         alt="Here you will find resources and ideas to get the most out of your PrestaShop integration with HubSpot. We provide you guides and templates to make your  abandoned shopping carts profitable, activate promotions…"/>
                                </a>
                            </span>

                            <script charset="utf-8" src="https://js.hscta.net/cta/current.js"></script>
                            <script type="text/javascript">
                                {fetch file="{$smarty.current_dir}/dashboard-cta-4.js"}
                            </script>
                        </span>
                        <!-- end HubSpot Call-to-Action Code -->
                    </small>
                </p>
            </div>
        </div>
    </div>

    <div class="content-lists mx-5">
        <div class="content-lists--child contacts hubspot-connected-account mr-2">
            <h3>
                <i class="fas fa-plug mr-1"></i>
                 {l s='Connected Hubspot Account' mod='pshubspot'}
            </h3>

            <div class="content-lists--child__columns mt-3">
                <div class="content-lists--child__columns-item w-100">

                    <label>{l s='You are connected as ' mod='pshubspot'} <a target="_blank" href="https://app.hubspot.com/contacts/{$hub_id|escape:'htmlall':'UTF-8'}"><b>{$hub_id|escape:'htmlall':'UTF-8'}</b></a></label><br/>
                    <label>{l s='Deals are imported to pipeline id ' mod='pshubspot'} <a target="_blank" href="https://app.hubspot.com/pipelines-settings/{$hub_id|escape:'htmlall':'UTF-8'}/object/0-3/{$pipeline_id|escape:'htmlall':'UTF-8'}"><b>{$pipeline_id|escape:'htmlall':'UTF-8'}</b></a></label><br/>
                    <label id="pshubspot">{l s='This integrations secure_key is ' mod='pshubspot'} <b><a target="_blank" href="{$sync_endpoint|escape:'htmlall':'UTF-8'}">{$secure_key|escape:'htmlall':'UTF-8'}</a></b></label><br/><br/>

                    <button id="ps-hubspot_reauthorize-account_btn" class="ml-3 button-custom px-1 py-1"
                             {* title="Queue Ref. {$sync_endpoint}" *}
                            onclick=document.location.href="{$connect_account_link|escape:'htmlall':'UTF-8'}">
                        <i class="fas fa-undo-alt mr-1"></i>
                         {l s='Re-Authorize your Account' mod='pshubspot'}
                    </button>
                </div>
            </div>
        </div>
        <div class="content-lists--child contacts module-subscription-info ml-2">
            <h3>
                <i class="fas fa-plug mr-1"></i>
                 {l s='Connected Prestashop Account' mod='pshubspot'}
            </h3>
            <div class="content-lists--child__columns mt-3">
                <div class="content-lists--child__columns-item w-100">
                    <prestashop-accounts></prestashop-accounts>
                    <br/><br/>
                    <label>{l s='You can change you subscription plan' mod='pshubspot'} <a href="{$settings_url|escape:'htmlall':'UTF-8'}">{l s='here' mod='pshubspot'}</a>.</label>
                </div>
            </div>
        </div>
    </div>

    <div class="content-footer p-3 mx-3">
        <div class="text-right">
            <a class="color-white" href="#" onclick="window.location= $('.hs-cta-wrapper a').last().attr('href')">
                 {l s='Support' mod='pshubspot'}
            </a>
        </div>
    </div>
</div>

<script>
    const tiralineasHsImporterInstnace = new tiralineasHsImporter("{$sync_endpoint|escape:'javascript':'UTF-8'}");

    async function psHubspotSyncrhonize(object_type) {
        document.getElementById('ps-hubspot_sync_btn').innerText = "{l s='Synchronizing' mod='pshubspot'}" + " " + "{l s='Contacts' mod='pshubspot'}";
        document.getElementById('ps-hubspot_sync_btn').disabled = true;
        await tiralineasHsImporterInstnace.start('CONTACT', psHubspotStatusRefresh);
        document.getElementById('ps-hubspot_sync_btn').innerText = "{l s='Synchronizing' mod='pshubspot'}" + " " + "{l s='Products' mod='pshubspot'}";
        await tiralineasHsImporterInstnace.start('PRODUCT', psHubspotStatusRefresh);
        document.getElementById('ps-hubspot_sync_btn').innerText = "{l s='Synchronizing' mod='pshubspot'}" + " " + "{l s='Deals' mod='pshubspot'}";
        await tiralineasHsImporterInstnace.start('DEAL', psHubspotStatusRefresh);
        document.getElementById('ps-hubspot_sync_btn').innerText = "{l s='Synchronizing' mod='pshubspot'}" + " " + "{l s='Carts' mod='pshubspot'}";
        await tiralineasHsImporterInstnace.start('DEALABANDONED', psHubspotStatusRefresh);
        document.getElementById('ps-hubspot_sync_btn').innerText = "🎉 {l s='Done' mod='pshubspot'}";
    }

    function psHubspotStatusRefresh(status) {
        document.querySelector('#CONTACT .sync').innerText = status.CONTACT_sync;
        document.querySelector('#CONTACT .total').innerText = (parseInt(status.CONTACT_unsync) + parseInt(status.CONTACT_sync));
        document.querySelector('#PRODUCT .sync').innerText = status.PRODUCT_sync;
        document.querySelector('#PRODUCT .total').innerText = (parseInt(status.PRODUCT_unsync) + parseInt(status.PRODUCT_sync));
        document.querySelector('#DEAL .sync').innerText = status.DEAL_sync;
        document.querySelector('#DEAL .total').innerText = (parseInt(status.DEAL_unsync) + parseInt(status.DEAL_sync));
        document.querySelector('#DEALABANDONED .sync').innerText = status.DEALABANDONED_sync;
        document.querySelector('#DEALABANDONED .total').innerText = (parseInt(status.DEALABANDONED_unsync) + parseInt(status.DEALABANDONED_sync));
    }

    function getHSSyncErrors() {
        return fetch('https://api.hubapi.com/extensions/ecomm/v2/sync/errors/', {
            method: 'get',
            headers: new Headers({
                "Authorization": "Bearer {$access_token|escape:'javascript':'UTF-8'}",
                "Content-Type": "application/json"
            })
        });
    }
</script>

<script src="{$urlAccountsCdn|escape:'htmlall':'UTF-8'}" rel=preload></script>
<style>
    #psaccounts header {
        display: none;
    }
</style>
<script type="text/javascript">
    window?.psaccountsVue?.init();
    if (window.psaccountsVue.isOnboardingCompleted() == true) {

    }
</script>