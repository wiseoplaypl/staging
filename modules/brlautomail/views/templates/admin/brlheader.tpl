{*
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel fondBrlDegrade">
    <div class="row brlHeader">
        <div class="col-md-6 brlHeaderColWidth">
            <div class="brlHeaderCenterDoc">
                <a href="{$module_dir|escape:'htmlall':'UTF-8'}{l s='readme_en' mod='brlautomail'}.pdf" target="_blank" class="brlHeaderLink"><img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/readme.png" class="brlHeaderDoc"><br>
                <strong class="brlHeaderReadMe">{l s='READ ME' mod='brlautomail'}</strong></a>
            </div>
        </div>
        <div class="col-md-6 brlHeaderAlign">
            <a href="{l s='https://addons.prestashop.com/en/2_community-developer?contributor=217031' mod='brlautomail'}" target="_blank" class="brlHeaderLogoStyle"><img class="brlHeaderLogo" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/logo-autour-du-digital-addons-ps-blanc.png"></a>
			<a href="{l s='https://addons.prestashop.com/en/2_community-developer?contributor=217031' mod='brlautomail'}" target="_blank" class="brlHeaderLinkModulesStyle">
				{if (version_compare(_PS_VERSION_, '1.7.0.0', '<'))}
                    <i class="icon-AdminParentModules" style="font-size: 45px;"></i><br>
                {else}
                    <i class="material-icons brlHeaderIconModulesStyle">view_module</i><br>
                {/if}
                    <strong>{l s='Our modules' mod='brlautomail'}</strong>
			</a>
        </div>
    </div>
</div>

<div class="panel">
    <h3><i class="icon icon-tags"></i> {l s='Presentation' mod='brlautomail'}</h3>
    <p>
        {l s='Example: Send an email 4 days after the shipment to ask the customer to put a product review.' mod='brlautomail'}
    </p>
</div>