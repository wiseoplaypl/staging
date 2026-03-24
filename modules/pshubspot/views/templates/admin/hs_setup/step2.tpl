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
<div id="pshubspot" class="container-fluid">
    <div class="content-lists mt-4">
        <div class="content-lists--child setup">
            <h3>
                <i class="fas fa-asterisk mr-1"></i>
                {l s='Select the pipeline' mod='pshubspot'}
            </h3>
            <div class="content-lists--child__columns mt-3 mb-3 ml-3 mr-3">
                <div class="content-lists--child__columns-item w-100">
                    <select id="avaliable_pipelines" name="pipeline_id">
                        {foreach from=$pipelineAndStages item=pipeline}
                            <option value="{$pipeline->pipelineId|escape:'htmlall':'UTF-8'}"
                                {if $pipeline->pipelineId==$PIPELINE_ID} selected {/if}>
                                {$pipeline->label|escape:'htmlall':'UTF-8'}
                                ({$pipeline->pipelineId|escape:'htmlall':'UTF-8'})
                            </option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="content-lists mt-4">
        <div class="content-lists--child setup">
            <h3>
                <i class="fas fa-asterisk mr-1"></i>
                {l s='Map order statuses to deal stages in HubSpot' mod='pshubspot'}
            </h3>

            <div class="content-lists--child__columns mt-3 mb-3 ml-3 mr-3">
                <div class="content-lists--child__columns-item w-100">
                    {if $pipelineAndStages}
                        {foreach from=$pipelineAndStages item=pipeline key=key}
                            <form data-pipeline="{$pipeline->pipelineId|escape:'htmlall':'UTF-8'}"
                                action="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}" method="post"
                                enctype="multipart/form-data" {if $key != 0} style="display:none" {/if}>
                                <input type="hidden" name="pipeline_id"
                                    value="{$pipeline->pipelineId|escape:'htmlall':'UTF-8'}">
                                <input type="hidden" name="pipeline_label" value="{$pipeline->label|escape:'htmlall':'UTF-8'}">
                                <div class="row">
                                    {foreach $mappings as $mapping}
                                        {if $mapping.id}
                                            <div class="col-md-4 col-xs-12 px-1 py-1">
                                                <label class="pl-0"
                                                    for="map_{$mapping.id|escape:'htmlall':'UTF-8'}">{$mapping.name|escape:'htmlall':'UTF-8'}</label><br />
                                                <select id="map_{$mapping.id|escape:'htmlall':'UTF-8'}"
                                                    name="mappings[{$mapping.id|escape:'htmlall':'UTF-8'}]">
                                                    <option value="-1">{l s='Do not sync' mod='pshubspot'}</option>
                                                    {foreach $pipeline->stages as $stage}
                                                        <option value="{$stage->stageId|escape:'htmlall':'UTF-8'}"
                                                            {if $stage->stageId==$mapping['value']} selected {/if}>
                                                            {$stage->label|escape:'htmlall':'UTF-8'}
                                                            ({$stage->stageId|escape:'htmlall':'UTF-8'})
                                                        </option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        {/if}
                                    {/foreach}
                                </div>
                                <br />
                                <input type="hidden" name="next_step" value="3" />

                                <button name="ps-hubspot_submit" id="ps-hubspot_connect-account_btn"
                                    class="button-custom px-1 py-1 mr-0" type="submit">
                                    <i class="fas fa-list"></i>
                                    {l s='Save mapping' mod='pshubspot'}
                                </button>

                                <button name="ps-hubspot_submit_skip" id="ps-hubspot_connect-account_btn"
                                    class="button-custom px-1 py-1" type="submit">
                                    <i class="fas fa-sign-out-alt"></i>
                                    {l s='Skip this step' mod='pshubspot'}
                                </button>
                            </form>
                        {/foreach}
                    {else}
                        <p>{l s="It looks like you don't have any active Pipelines in your account. Please contact your HubSpot provider." mod='pshubspot'}
                        </p>
                        <a href="tiralineas.digital">{l s="Contact us" mod='pshubspot'}</a>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        //Pipeline selection
        $('#avaliable_pipelines').on('change', function() {
            $('#pshubspot form').hide();
            $("form[data-pipeline='" + $(this).val() + "']").show();
        });
    })
</script>