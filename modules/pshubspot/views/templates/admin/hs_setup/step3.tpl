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
<form action="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}"
                          method="post" enctype="multipart/form-data">
<div id="pshubspot" class="container-fluid">
    <div class="content-lists mt-4">
        <div class="content-lists--child setup">
            <h3>
                <i class="fas fa-plug mr-1"></i>
                 {l s='Configure deals to sync' mod='pshubspot'}
            </h3>
            <div class="content-lists--child__columns mt-3 mb-3 ml-3 mr-3">
                <div class="content-lists--child__columns-item w-100">
                    <div class="row">
                        <div class="col-md-4 col-xs-12 px-1 py-1">
                            <label class="pl-0 width-reset" for="PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED">
                                    {l s='Enable date filter' mod='pshubspot'}
                            </label>

                            <input type="checkbox" name="PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED"
                                    id="PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED"
                                    name="PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED" onclick="tiralineas_check_date_filter_enabled()"
                                        {if $PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED} checked="checked" {/if} />
                        </div>

                        <div class="col-md-4 px-1 py-1">
                            <label class="pl-0" for="PS_HUBSPOT_DEAL_DATE_FILTER">
                                    {l s='Only sync orders created since:' mod='pshubspot'}
                            </label>

                            <br/>

                            <input type="date" name="PS_HUBSPOT_DEAL_DATE_FILTER"
                                    id="PS_HUBSPOT_DEAL_DATE_FILTER" name="PS_HUBSPOT_DEAL_DATE_FILTER"
                                    class="ml-0 width-reset"
                                    value="{$PS_HUBSPOT_DEAL_DATE_FILTER|escape:'htmlall':'UTF-8'}"
                                        {if $PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED!='on'} disabled="disabled" {/if} />
                        </div>

                        <div class="col-md-4 px-1 py-1">
                            <label class="pl-0" for="PS_HUBSPOT_DEALSABANDONED_SENIORITY">
                                    {l s='Time, in minutes, to consider a cart as abandoned:' mod='pshubspot'}
                            </label>

                            <br/>

                            <input type="number"
                                    id="PS_HUBSPOT_DEALSABANDONED_SENIORITY"
                                    class="ml-0"
                                    name="PS_HUBSPOT_DEALSABANDONED_SENIORITY"
                                    value="{$PS_HUBSPOT_DEALSABANDONED_SENIORITY|escape:'htmlall':'UTF-8'}" step=1/>
                        </div>
                    </div>
                    <br/>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="pshubspot" class="container-fluid">
    <div class="content-lists mt-4">
        <div class="content-lists--child setup">
            <h3>
                <i class="fas fa-users mr-1"></i>
                 {l s='Customer settings' mod='pshubspot'}
            </h3>
            <div class="container-fluid">
                <div class="col-md-12 col-xs-12 px-1 py-1">
                    <p>
                        {l s='Move and drag the customer groups between the different containers to modify the clients to be synchronised.' mod='pshubspot'}
                        <br />
                    </p>
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
        </div>
    </div>
</div>

<div id="pshubspot" class="container-fluid">
    <div class="content-lists mt-4">
        <div class="content-lists--child setup">
            <div style="margin-left: 10px;">
                <br />
                <input type="hidden" name="next_step" value="4"/>
                <input id="PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC" type="hidden" name="PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC" value="" />

                <button name="ps-hubspot_submit" id="ps-hubspot_connect-account_btn"
                class="button-custom px-1 py-1 mr-0" type="submit">
                    <i class="fas fa-list"></i>
                    {l s='Save' mod='pshubspot'}
                </button>

                <button name="ps-hubspot_submit_skip" id="ps-hubspot_connect-account_btn"
                class="button-custom px-1 py-1" type="submit">
                    <i class="fas fa-sign-out-alt"></i>
                    {l s='Skip this step' mod='pshubspot'}
                </button>
            </div>
        </div>
    </div>
</div>

</form>
<script>
    $(function() {
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