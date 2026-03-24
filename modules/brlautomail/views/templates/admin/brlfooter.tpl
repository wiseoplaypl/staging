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

<div class="panel panelFooterBrl">
    <div class="row">
        <div class="col-md-6">
            {if (version_compare(_PS_VERSION_, '1.7.0.0', '<'))}
            <h3 class="brlFooterIconAlign brlFooterH3"><strong>Informations</strong></h3>
            {else}
            <h3 class="brlFooterIconAlign brlFooterH3"><i class="material-icons brlFooterIcon">info_outline</i><strong>Informations</strong></h3>
            {/if}
            <p>
                {l s='Module version' mod='brlautomail'} : <strong>{$brl_module_version|escape:'htmlall':'UTF-8'}</strong><br>
                {l s='Prestashop version' mod='brlautomail'} : <strong>{$brl_ps_version|escape:'htmlall':'UTF-8'}</strong><br>
                {l s='PHP version' mod='brlautomail'} : <strong>{$brl_php_version|escape:'htmlall':'UTF-8'}</strong>
            </p><br><br>
        </div>
        <div class="col-md-6">
            {if (version_compare(_PS_VERSION_, '1.7.0.0', '<'))}
            <h3 class="brlFooterIconAlign brlFooterH3"><strong>{l s='Debug Mode' mod='brlautomail'}</strong></h3>
            {else}
            <h3 class="brlFooterIconAlign brlFooterH3"><i class="material-icons brlFooterIcon">bug_report</i><strong>{l s='Debug Mode' mod='brlautomail'}</strong></h3>
            {/if}
            {if ($brl_mode_debug == 1)}
                <a href="{$url_module|escape:'htmlall':'UTF-8'}&brl_mode_debug=0" class="btnBrlFooterModeDebug">{l s='Disable debug mode' mod='brlautomail'}</a><br>
                {if (file_exists($brl_nomFichierLog|escape:'htmlall':'UTF-8'))}
                    <a href="{$module_dir|escape:'htmlall':'UTF-8'}BRL_LOG_{$brl_nomModule|escape:'htmlall':'UTF-8'}.log" class="brlFooterIconAlign brlFooterLinkColor">  
                    {if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))}
                        <i class="material-icons brlFooterIcon">file_upload</i>
                    {/if}
                    {l s='Download logs' mod='brlautomail'} ({$brl_sizeFichierLog|escape:'htmlall':'UTF-8'} {l s='bytes' mod='brlautomail'})</a><br>
                <a href="{$url_module|escape:'htmlall':'UTF-8'}&brl_delete_logs=1" class="btnBrlFooterModeDebug btnBrlFooterDeleteLog">{l s='Delete logs' mod='brlautomail'}</a>
                {/if}
            {else}
                <a href="{$url_module|escape:'htmlall':'UTF-8'}&brl_mode_debug=1" class="btnBrlFooterModeDebug btnBrlFooterActiveModeDebug">{l s='Activate debug mode' mod='brlautomail'}</a><br><br>
            {/if}
        </div>
    </div>
</div>