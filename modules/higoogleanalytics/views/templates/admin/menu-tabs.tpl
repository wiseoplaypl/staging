{**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 *}
<div class="col-lg-2 hi-module-menu-container">
    <div class="list-group">
        {foreach from=$tabs key=tab_key item=tab}
            <a 
                {if $tab_key == 'version' || $tab_key == 'rateMe'} style="margin-top:30px;" {/if}
                class="list-group-item {if $tab_key == $active_tab || ($active_tab == '' && $tab_key == 'googleAnalytics4')}active{/if}"
                href="{if isset($tab.url)}{$tab.url nofilter}{else}{$module_url|escape:'htmlall':'UTF-8'}&{$module_tab_key}={$tab_key|escape:'htmlall':'UTF-8'}{/if}"
                {if isset($tab.url)}
                    target="_blank"
                {/if}
            >
                {if isset($tab.icon)}
                    <i class="{$tab.icon}" {if $tab_key == 'rateMe'}style="color: orange;"{/if}></i>
                {elseif isset($tab.iconImage)}
                    <img src="{$moduleDir|escape:'htmlall':'UTF-8'}/views/img/{$tab.iconImage|escape:'htmlall':'UTF-8'}" width="35" height="35">
                {/if}
                <div class="hi-module-menu-item-txt">
                    <span>
                        {if $tab_key != 'version'}
                            {$tab.title|escape:'htmlall':'UTF-8'}
                        {else}
                            {$tab.title|escape:'htmlall':'UTF-8'} - {$module_version|escape:'html':'UTF-8'}
                        {/if}
                    </span>
                    {if isset($tab.subtitle)}
                        <small>{$tab.subtitle}</small>
                    {/if}
                </div>
            </a>
        {/foreach}

        {if $displayMenuModule}
            <div class="hipresta-modules-ad hipresta-modules-menu-ad">
                <p class="hipresta-modules-ad-title">
                    {l s='Enable Google Consent mode with our Cookie Law module' mod='higoogleanalytics'}
                    <a href="#" class="hide-hm-menu-module"><i class="icon icon-close"></i></a>
                </p>
                <div class="module-item module-item-grid">
                    <div class="module-item-wrapper-grid">
                        <div class="module-item-heading-grid">
                            <div class="module-logo-thumb-grid">
                                <img src="https://hipresta.com/images/addons/cookie/logo.png" alt="Cookie Banner - EU Law GDPR Compliance">
                            </div>
                            <h3 title="Cookie Banner - EU Law GDPR Compliance" class="text-ellipsis module-name-grid">
                                <span>EU Cookie Law GDPR + Google Consent Mode V2</span>
                            </h3>
                        </div>
                        <div class="module-quick-description-grid no-padding mb-0">
                            <div class="module-quick-description-text">
                                {l s='An easy way to comply with complex and important EU law on data protection rules (GDPR) regarding cookies.' mod='higoogleanalytics'}
                                <span>...</span>
                            </div>
                            <div class="module-read-more-grid">
                                <a href="https://addons.prestashop.com/en/legal/48223-eu-cookie-law-gdpr-google-consent-mode-v2-by-hipresta.html" target="_blank">Read more</a>
                            </div>
                        </div>
                        <div class="module-container module-quick-action-grid clearfix">
                            <div class="badges-container">
                                <div>
                                    <img src="https://hipresta.com/images/hipresta.jpg">
                                    <span>Made by HiPresta</span></div>
                                </div>
                            <hr>
                            <div class="float-right module-price"><span>69.99€</span></div>
                            <div class="form-action-button-container">
                                <a href="https://addons.prestashop.com/en/legal/48223-eu-cookie-law-gdpr-google-consent-mode-v2-by-hipresta.html" target="_blank" class="btn btn-primary btn-primary-reverse btn-block btn-outline-primary light-button module_action_menu_go_to_addons">
                                Discover</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    </div>
</div>