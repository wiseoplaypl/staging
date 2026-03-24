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
        <p>
            <span id="PROPERTIES" style="display: none"><b class='sync'>{if $PS_HUBSPOT_MIGRATION_PROPERTIES}✅{else}🔁{/if}</b> {l s='Property migration' mod='pshubspot'}</span>
        </p>
    </div>
    <div class="text-center mx-3">
        <p>
            <span id="PRODUCT"><b class='sync'>{$PS_HUBSPOT_MIGRATION_PRODUCTS|escape:'htmlall':'UTF-8'}</b> {l s='of' mod='pshubspot'} <b class='total'>{$products_to_migrate|escape:'htmlall':'UTF-8'}</b> {l s='Product' mod='pshubspot'}</span> -
             {* <span id="CONTACT"><b class='sync'>{$PS_HUBSPOT_MIGRATION_CUSTOMERS|escape:'htmlall':'UTF-8'}</b> {l s='of' mod='pshubspot'} <b class='total'>{$customers_to_migrate|escape:'htmlall':'UTF-8'}</b> {l s='Customers' mod='pshubspot'}</span> *}
        </p>
        <p>
            <span id="DEALABANDONED"><b class='sync'>{$PS_HUBSPOT_MIGRATION_CARTS|escape:'htmlall':'UTF-8'}</b> {l s='of' mod='pshubspot'} <b class='total'>{$carts_to_migrate|escape:'htmlall':'UTF-8'}</b> {l s='Carts' mod='pshubspot'}</span>-
            <span id="DEAL"><b class='sync'>{$PS_HUBSPOT_MIGRATION_ORDERS|escape:'htmlall':'UTF-8'}</b> {l s='of' mod='pshubspot'} <b class='total'>{$orders_to_migrate|escape:'htmlall':'UTF-8'}</b> {l s='Orders' mod='pshubspot'}</span>
        </p>
    </div>

    <div class="text-center mx-3">
        <button class="mt-3 button-custom px-1 py-1" type="button" onclick="psHubspotMigrate()" id="ps-hubspot_sync_btn"><i class="fas fa-sync mr-1"></i>{l s='Migrate' mod='pshubspot'}</button>
    </div>

    <div class="row content-lists mt-5">
        <div class="content-lists--child mr-2 col-md-4 col-sm-12 contacts"> <!-- flex-wrap dashboard-provisional -->
            <h3>
                <i class="fas fa-filter mr-1"></i>
                 {l s='Help' mod='pshubspot'}
            </h3>

            <div class="text-center p-5">
                <p class="pb-5">
                    <small>
                         {l s='We have detected a previous installation. Due to changes in HubSpot, some settings need to be migrated.' mod='pshubspot'}
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
                    <a href="https://addons.prestashop.com/contact-form.php?id_product=51992">{l s="Contact us" mod='pshubspot'}</a>
                </p>
            </div>
        </div>

    </div>

    <div class="content-lists mx-5">
        <div class="content-lists--child contacts">
            <h3>
                <i class="fas fa-plug mr-1"></i>
                 {l s='Connected Hubspot Account' mod='pshubspot'}
            </h3>

            <div class="content-lists--child__columns mt-3">
                <div class="content-lists--child__columns-item w-100">

                    <button id="ps-hubspot_reauthorize-account_btn" class="ml-3 button-custom px-1 py-1"
                            onclick="document.location.href=''">
                        <i class="fas fa-undo-alt mr-1"></i>
                         {l s='Re-Authorize your Account' mod='pshubspot'}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="content-footer p-3 mx-3">
        <div class="text-right">
            <a class="color-white" href="#">
                 {l s='Support' mod='pshubspot'}
            </a>
        </div>
    </div>
</div>

<script type="text/javascript">
    const tiralineasHsImporterInstance = new tiralineasHsImporter("{$sync_endpoint|escape:'htmlall':'UTF-8'}");

    var propiertiesMigrated = {if $PS_HUBSPOT_MIGRATION_PROPERTIES} true {else} false {/if};

    async function psHubspotMigrate(object_type) {
        document.getElementById('ps-hubspot_sync_btn').innerText = "{l s='Synchronizing' mod='pshubspot'}";
        document.getElementById('ps-hubspot_sync_btn').disabled = true;

        if (!propiertiesMigrated) {
            document.getElementById('PROPERTIES').style.display = 'block';
            await tiralineasHsImporterInstance.start('PROPERTIES', psHubspotStatusRefresh);
            document.getElementById('PROPERTIES').style.display = 'none';
        }

        // await tiralineasHsImporterInstance.start('CONTACT', psHubspotStatusRefresh);
        await tiralineasHsImporterInstance.start('PRODUCT', psHubspotStatusRefresh);
        await tiralineasHsImporterInstance.start('DEAL', psHubspotStatusRefresh);
        await tiralineasHsImporterInstance.start('DEALABANDONED', psHubspotStatusRefresh);
        document.getElementById('ps-hubspot_sync_btn').innerText = '🎉 {l s='Done' mod='pshubspot'}';
        setTimeout(() => {
            document.location.reload();
        }, 3000);
    }

    function psHubspotStatusRefresh(status) {
        document.getElementById('ps-hubspot_sync_btn').innerText = "{l s='Synchronizing' mod='pshubspot'}";

        // document.querySelector('#CONTACT .sync').innerText = status.CONTACT_sync;
        // document.querySelector('#CONTACT .total').innerText = (parseInt(status.CONTACT_unsync));
        document.querySelector('#PRODUCT .sync').innerText = status.PRODUCT_sync;
        document.querySelector('#PRODUCT .total').innerText = (parseInt(status.PRODUCT_unsync));
        document.querySelector('#DEAL .sync').innerText = status.DEAL_sync;
        document.querySelector('#DEAL .total').innerText = (parseInt(status.DEAL_unsync));
        document.querySelector('#DEALABANDONED .sync').innerText = status.DEALABANDONED_sync;
        document.querySelector('#DEALABANDONED .total').innerText = (parseInt(status.DEALABANDONED_unsync));

        if (status.done)
            document.getElementById('ps-hubspot_sync_btn').innerText = "{l s='Synced' mod='pshubspot'}";
    }

</script>