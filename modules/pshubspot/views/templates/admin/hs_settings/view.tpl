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
 <div id="pshubspot_container" class="container-fluid">
    <div class="content-lists mb-5">
        <div class="content-lists--child setup">
            <h3>
                <i class="fas fa-cogs mr-1"></i>
                {l s='Choose your Prestashop Account and select a Subscription Plan' mod='pshubspot'}
            </h3>
            <prestashop-accounts></prestashop-accounts>
            <div id="ps-billing"></div>
            <div id="ps-modal"></div>
        </div>
    </div>
    <form method="POST" id="form_hs_sttings">
        <div class="content-lists mb-5">
            <div class="content-lists--child contacts">
                <h3>
                    <i class="fas fa-cogs mr-1"></i>
                    {l s='Extra settings' mod='pshubspot'}
                </h3>

                {* <div class="content-lists--child__columns mt-3 mb-3 ml-3 mr-3">
                     <div class="content-lists--child__columns-item w-100">
                         <div class="row">
                             <div class="col-md-4 px-1 py-1">
                                 <label class="pl-0" for="PS_HUBSPOT_LICENSE">
                                     {l s='Insert your license key' mod='pshubspot'}
                                 </label>
                                 <p>
                                     <small>
                                         {l s='If you have been provided with a license key with the purchase of this module, you can enter it here.' mod='pshubspot'}<br/>
                                         <b>{l s='If you are starting to have problems with your data synchronization, try filling out this field.' mod='pshubspot'}</b>
                                     </small>
                                 </p>
                                 <input type="text" name="PS_HUBSPOT_LICENSE" id="PS_HUBSPOT_LICENSE"
                                     value="{$PS_HUBSPOT_LICENSE}"/>
                             </div>
                         </div>
                     </div>
                 </div> *}

                <div class="content-lists--child__columns mt-3 mb-3 ml-3 mr-3">
                    <div class="content-lists--child__columns-item w-100">
                        <div class="row">
                            <div class="col-md-6 px-1 py-1">
                                <label class="pl-0" for="PS_HUBSPOT_USE_TRACKING">
                                    {l s='Enable tracking script' mod='pshubspot'}
                                </label>

                                <input type="checkbox" name="PS_HUBSPOT_USE_TRACKING" id="PS_HUBSPOT_USE_TRACKING"
                                    onclick="tiralineas_check_date_filter_enabled()"
                                    {if $PS_HUBSPOT_USE_TRACKING=='on'}checked="true" {/if} />
                            </div>
                        </div>

                        <br />
                    </div>
                </div>

                <input id="PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC" type="hidden" name="PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC" value="" />
                <div class="content-lists--child__columns mt-3 mb-3 ml-3 mr-3">
                    <button name="ps-hubspot_save_settings" id="ps-hubspot_save_settings"
                        class="button-custom px-1 py-1 ml-0 mr-0 mb-0" type="submit">
                        <i class="fas fa-save mr-1 color-white"></i>
                        {l s='Save' mod='pshubspot'}
                    </button>
                </div>
            </div>
        </div>
    </form>
    <div class="content-lists mb-5">
            <div class="content-lists--child contacts">
                <h3>
                    <i class="fas fa-users mr-1"></i>
                    {l s='Customer settings' mod='pshubspot'}
                </h3>

                <div class="content-lists--child__columns mt-3 mb-3 ml-3 mr-3">
                    <div class="col-md-12 col-xs-12 px-1 py-1">
                        {l s='Move and drag the customer groups between the different containers to modify the clients to be synchronised.' mod='pshubspot'}
                        <p><i> {l s='Changes will not cause clients to be synchronised.' mod='pshubspot'} </i></p>

                        <div class="customer_groups-container">
                        <div class="customer_groups-container_child col-md-4">
                            <p> 
                                <b> {l s='To Synchronize' mod='pshubspot'} </b>
                            </p>
                            <div class="customer_groups-container_childs">
                                <ul id="sortable_sync" class="customer_groups_sortable">
                                    {foreach $PS_CUSTOMER_GROUPS_AVAILABLE as $grupos}
                                        {if !empty($grupos)}
                                            <li id="group_{$grupos['id_group']|escape:'htmlall':'UTF-8'}"
                                                class="customer_groups_sortable__item">
                                                <p class="customer_group_sortable__item_name">
                                                    {$grupos['name']|escape:'htmlall':'UTF-8'}
                                                    <span class="customer_group_sortable__item_id">ID:
                                                        {$grupos['id_group']|escape:'htmlall':'UTF-8'}</span>
                                                </p>

                                            </li>
                                        {/if}
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-1 customer_groups-container_child-arrows">
                            <span class="set_sync_btn" id="sync_all_btn"><<</span>
                            <span class="set_sync_btn" id="unsync_all_btn">>></span>
                        </div>
                        <div class="customer_groups-container_child col-md-4">
                            <p> 
                                <b> {l s='Not to Synchronize' mod='pshubspot'} </b>
                            </p>
                            <div class="customer_groups-container_childs">
                                <ul id="sortable_unsync" class="customer_groups_sortable">
                                    {foreach $PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC as $grupos_unsync}
                                        {if !empty($grupos_unsync)}
                                            <li id="group_{$grupos_unsync['id_group']|escape:'htmlall':'UTF-8'}"
                                                class="customer_groups_sortable__item">
                                                <p class="customer_group_sortable__item_name">
                                                    {$grupos_unsync['name']|escape:'htmlall':'UTF-8'}
                                                    <span class="customer_group_sortable__item_id">ID:
                                                        {$grupos_unsync['id_group']|escape:'htmlall':'UTF-8'}</span>
                                                </p>

                                            </li>
                                        {/if}
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                
                <div class="content-lists--child__columns mt-3 mb-3 ml-3 mr-3">
                    <button id="ps-hubspot_save_settings_customer"
                        class="button-custom px-1 py-1 ml-0 mr-0 mb-0" type="submit">
                        <i class="fas fa-save mr-1 color-white"></i>
                        {l s='Save' mod='pshubspot'}
                    </button>
                </div>
            </div>
        </div>
</div>
<script src="{$urlAccountsCdn|escape:'htmlall':'UTF-8'}" rel=preload></script>
<script src="{$urlBilling|escape:'htmlall':'UTF-8'}" rel=preload></script>

<script type="text/javascript">
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
        });
    }

    $(function() {
        document.querySelector("#ps-hubspot_save_settings_customer").addEventListener("click", function(){
            
            document.querySelector("#ps-hubspot_save_settings").click()
        })
        $("#sortable_sync, #sortable_unsync").sortable({
            connectWith: ".customer_groups_sortable",
            placeholder: "customer_groups_sortable__item-placeholder"
        }).disableSelection();
        $("#sortable_unsync").on("sortreceive", function(event, ui) {
            setUnSyncValue(Array.from(event.target.children))
        })
        $("#sortable_unsync").on("sortremove", function(event, ui) {
            setUnSyncValue(Array.from(event.target.children))
        })

        document.querySelector("#sync_all_btn").addEventListener("click", () => {
            let sync_container = document.querySelector("#sortable_sync")
            let unsync_values = Array.from(document.querySelector("#sortable_unsync").children)
            if (unsync_values) {
                unsync_values.forEach((element) => {
                    sync_container.appendChild(element)
                })
            }
            setUnSyncValue()
        })
        document.querySelector("#unsync_all_btn").addEventListener("click", () => {
            let unsync_container = document.querySelector("#sortable_unsync")
            let sync_values = Array.from(document.querySelector("#sortable_sync").children)
            if (sync_values) {
                sync_values.forEach((element) => {
                    unsync_container.appendChild(element)
                })
            }
            setUnSyncValue(Array.from(unsync_container.children))
        })

        function setUnSyncValue(readFrom = []) {
            let children = readFrom;
            let newValue = ''
            document.querySelector("#PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC").value = ''
            children.forEach((element, index) => {
                if (index > 0) {
                    newValue += ","
                }
                newValue += element.id
            })
            document.querySelector("#PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC").value = newValue.replaceAll("group_", '')
        }
    });
</script>