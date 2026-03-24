{**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 *}
<div class="credentials-container col-lg-8">
    <div class="environment-selector">
        <label class="radio-inline environment-text">
            <input type="radio" class="environment-switch production" value="{$klarnapayment.production_current_page_url|escape:'htmlall':'UTF-8'}">{l s='Production' mod='klarnapayment'}
        </label>
        <label class="radio-inline environment-text">
            <input type="radio" class="environment-switch sandbox" value="{$klarnapayment.sandbox_current_page_url|escape:'htmlall':'UTF-8'}">{l s='Playground' mod='klarnapayment'}
        </label>
        <p><b>By activating Klarna using API credentials you agree to and accept the <a href="{$klarnapayment.merchant_privacy_notice_url|escape:'htmlall':'UTF-8'}" target="_blank">Klarna Merchant Privacy Notice</a></b></p>
        <p>To unlock product features, enter your credentials below. Get the client identifier and the API credentials from the <a href="{$klarnapayment.merchant_portal_url|escape:'htmlall':'UTF-8'}" target="_blank">Klarna Merchant portal</a> under settings</p>
    </div>

    {foreach $klarnapayment.regions as $regionKey => $regionData}
        <div class="region-container">
            <p class="locale-title">
                <span class="klarna-credential-trigger">
                    <b>
                        {$regionData.regionTitle|escape:'htmlall':'UTF-8'}
                    </b>
                    {if $regionData.isConnected}
                        <span class="badge badge-success">{l s='Connected' mod='klarnapayment'}</span>
                    {/if}
                    <i class="icon-chevron-down"></i>
                </span>
            </p>
            <div id="credentials-{$regionKey|escape:'htmlall':'UTF-8'}" class="credentials-container">
                <div class="row mb-0">
                    <div class="form-group mb-0" style="margin-bottom: 10px">
                        <label class="col-sm-2 control-label credentials-label">{l s='Test:' mod='klarnapayment'}</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" value="{$regionData.sandboxUsername|escape:'htmlall':'UTF-8'}" placeholder="{l s='Username (Test)' mod='klarnapayment'}" name="sandboxUsername[{$regionKey|escape:'htmlall':'UTF-8'}]" autocomplete="off">
                        </div>
                        <div class="col-sm-3">
                            <input type="password" class="form-control" value="{$regionData.sandboxPassword|escape:'htmlall':'UTF-8'}" placeholder="{l s='Password (Test)' mod='klarnapayment'}" name="sandboxPassword[{$regionKey|escape:'htmlall':'UTF-8'}]" autocomplete="new-password">
                        </div>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" value="{$regionData.sandboxClientId|escape:'htmlall':'UTF-8'}" placeholder="{l s='Client ID (e.g. klarna_test_client_xxxxxxx)' mod='klarnapayment'}" name="sandboxClientId[{$regionKey|escape:'htmlall':'UTF-8'}]" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-2 control-label credentials-label">{l s='Production:' mod='klarnapayment'}</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" value="{$regionData.prodUsername|escape:'htmlall':'UTF-8'}" placeholder="{l s='Username (Production)' mod='klarnapayment'}" name="prodUsername[{$regionKey|escape:'htmlall':'UTF-8'}]" autocomplete="off">
                        </div>
                        <div class="col-sm-3">
                            <input type="password" class="form-control" value="{$regionData.prodPassword|escape:'htmlall':'UTF-8'}" placeholder="{l s='Password (Production)' mod='klarnapayment'}" name="prodPassword[{$regionKey|escape:'htmlall':'UTF-8'}]" autocomplete="new-password">
                        </div>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" value="{$regionData.prodClientId|escape:'htmlall':'UTF-8'}" placeholder="{l s='Client ID (e.g. klarna_live_client_xxxxxxx)' mod='klarnapayment'}" name="prodClientId[{$regionKey|escape:'htmlall':'UTF-8'}]" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
</div>
