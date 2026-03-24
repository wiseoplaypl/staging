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
<div id="pshubspot_container">
    <div class="container-fluid mt-4">
        <div class="content-lists mb-5">
            <div class="content-lists--child contacts">
                <h3>
                    <i class="fas fa-cog mr-1"></i>
                    {$PS_HUBSPOT_DEAL_PIPELINE_LABEL|escape:'htmlall':'UTF-8'} -
                    {l s='Deals settings' mod='pshubspot'}
                </h3>

                <div class="content-lists--child__columns mt-3 mb-3 ml-3 mr-3">
                    <div class="content-lists--child__columns-item w-100">
                        <form method="POST" id="form_pipeline_mapping">
                            <div class="row">
                                {foreach $mappings as $mapping}
                                    <div class="col-md-4 px-1 py-1">
                                        <label class="pl-0" for="map_{$mapping.id|escape:'htmlall':'UTF-8'}">{$mapping.name|escape:'htmlall':'UTF-8'}</label><br />
                                        <select id="map_{$mapping.id|escape:'htmlall':'UTF-8'}" name="mappings[{$mapping.id|escape:'htmlall':'UTF-8'}]">
                                            <option value="-1">{l s='Do not sync' mod='pshubspot'}</option>
                                            {foreach $stages as $stage}
                                                <option value="{$stage->stageId|escape:'htmlall':'UTF-8'}" {if $stage->stageId==$mapping.value}selected{/if}>
                                                    {$stage->label|escape:'htmlall':'UTF-8'} ({$stage->stageId|escape:'htmlall':'UTF-8'})</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                {/foreach}
                            </div>

                            <br />

                            <button name="ps-hubspot_save_pipeline_mapping" id="ps-hubspot_save_pipeline_mapping"
                                class="button-custom px-1 py-1 ml-0 mr-0 mb-0" type="submit">
                                <i class="fas fa-save mr-1 color-white"></i>
                                {l s='Save' mod='pshubspot'}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="pshubspot" class="container-fluid">
        <div class="content-lists mb-5">
            <div class="content-lists--child contacts">
                <h3>
                    <i class="fas fa-filter mr-1"></i>
                    {l s='Deals date filter setting' mod='pshubspot'}
                </h3>

                <div class="content-lists--child__columns mt-3 mb-3 ml-3 mr-3">
                    <div class="content-lists--child__columns-item w-100">
                        <form method="POST" id="form_deal_filter">
                            <div class="row">
                                <div class="col-md-4 px-1 py-1">
                                    <label class="pl-0" for="PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED">
                                        {l s='Enable date filter' mod='pshubspot'}
                                    </label>

                                    <input type="checkbox" name="PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED"
                                        id="PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED"
                                        name="PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED" onclick="tiralineas_check_date_filter_enabled()"
                                        {if $PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED}checked="true" {/if} />
                                </div>

                                <div class="col-md-4 px-1 py-1">
                                    <label class="pl-0" for="PS_HUBSPOT_DEAL_DATE_FILTER">
                                        {l s='Only sync orders created since:' mod='pshubspot'}
                                    </label>

                                    <br />

                                    <input type="date" name="PS_HUBSPOT_DEAL_DATE_FILTER"
                                        id="PS_HUBSPOT_DEAL_DATE_FILTER" name="PS_HUBSPOT_DEAL_DATE_FILTER"
                                        value="{$PS_HUBSPOT_DEAL_DATE_FILTER|escape:'htmlall':'UTF-8'}"
                                        {if $PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED!='on'}disabled="true" {/if} />
                                </div>

                                <div class="col-md-4 px-1 py-1">
                                    <label class="pl-0" for="PS_HUBSPOT_DEALSABANDONED_SENIORITY">
                                        {l s='Time, in minutes, to consider a cart as abandoned:' mod='pshubspot'}
                                    </label>

                                    <input type="number" name="PS_HUBSPOT_DEALSABANDONED_SENIORITY"
                                        id="PS_HUBSPOT_DEALSABANDONED_SENIORITY"
                                        name="PS_HUBSPOT_DEALSABANDONED_SENIORITY"
                                        value="{$PS_HUBSPOT_DEALSABANDONED_SENIORITY|escape:'htmlall':'UTF-8'}" step=1 min="1" />
                                </div>
                            </div>

                            <br />

                            <button name="ps-hubspot_save_deal_filter" id="ps-hubspot_save_deal_filter"
                                class="button-custom px-1 py-1 ml-0 mr-0 mb-0" type="submit">
                                <i class="fas fa-save mr-1 color-white"></i>
                                {l s='Save' mod='pshubspot'}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>