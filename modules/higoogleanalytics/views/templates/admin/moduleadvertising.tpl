{**
* 2012 - 2023 HiPresta
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License 3.0 (AFL-3.0).
* It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
*
* @author    HiPresta <support@hipresta.com>
* @copyright HiPresta 2023
* @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
* @website   https://hipresta.com
*}
<div class="col-lg-12 hipresta-modules-ad">
    <div class="clearfix">
        {*<div class="col-lg-12 module-sorting-menu">
            {if !$free_module}
                {l s='Check our modules' mod='higoogleanalytics'}
            {else}
                {l s='Check our free modules' mod='higoogleanalytics'}
            {/if}
        </div>*}

        {if $show_module}
            {foreach from=$modules key=k item=module}
                {if $k|substr:0:1 == 'A'}
                    <div class="module-item module-item-grid col-md-12 col-lg-4 col-xl-3">
                        <div class="module-item-wrapper-grid">
                            <div class="module-item-heading-grid">
                                <div class="module-logo-thumb-grid">
                                    <img src="{$module->image_link}" alt="{$module->display_name}">
                                </div>
                                <h3 title="{$module->display_name}" class="text-ellipsis module-name-grid">
                                    <span>{$module->display_name}</span>
                                </h3>
                            </div>
                            <div class="module-quick-description-grid no-padding mb-0">
                                {if isset($module->desc_short) && $module->desc_short}
                                    <div class="module-quick-description-text">
                                        {$module->desc_short}
                                        <span>...</span>
                                    </div>
                                {/if}
                                <div class="module-read-more-grid">
                                    <a href="https://hipresta.com/module/hiprestashopapi/prestashopapi?secure_key=6db77b878f95ee7cb56d970e4f52f095&redirect=1&module_key={$k}" target="_blank">{l s='Read more' mod='higoogleanalytics'}</a>
                                </div>
                            </div>
                            <div class="module-container module-quick-action-grid clearfix">
                                <div class="badges-container">
                                    <div>
                                        <img src="https://hipresta.com/images/hipresta.jpg">
                                        <span>{l s='Made by HiPresta' mod='higoogleanalytics'}</span></div>
                                    </div>
                                <hr>
                                <div class="float-right module-price"><span>{$module->price}</span></div>
                                <div class="form-action-button-container">
                                    <a href="https://hipresta.com/module/hiprestashopapi/prestashopapi?secure_key=6db77b878f95ee7cb56d970e4f52f095&redirect=1&module_key={$k}" target="_blank" class="btn btn-primary btn-primary-reverse btn-block btn-outline-primary light-button module_action_menu_go_to_addons">
                                    {l s='Discover' mod='higoogleanalytics'}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
            {/foreach}
        {/if}
    </div>
</div>
<div class="clear"></div>