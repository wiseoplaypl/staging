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
{extends file="helpers/list/list_content.tpl"}

{block name="td_content"}
    {if $key == 'status'}
        <a data-id="{$tr.id_event|intval}" data-status="{$tr.active|intval}" class="{$table|escape:'htmlall':'UTF-8'}-status btn {if $tr.active == '0'}btn-danger{else}btn-success{/if}" 
        href="#" title="{if $tr.active == '0'}{l s='Disabled' mod='higoogleanalytics'}{else}{l s='Enabled' mod='higoogleanalytics'}{/if}">
            <i class="{if $tr.active == '0'}icon-remove {else}icon-check{/if}"></i>
        </a>
    {elseif isset($params.type) && $params.type == 'actionButton'}
        <a
            data-id-element="{$tr[$identifier]|intval}"
            data-action-type="{$params.actionType|escape:'htmlall':'UTF-8'}"
            class="btn btn-default hi-presta-module-action-button hi-presta-module-action-button-{$params.actionType|escape:'htmlall':'UTF-8'}"
            href="#"
            title="{$params.actionTitle|escape:'htmlall':'UTF-8'}"
        >
            <i class="{$params.actionIcon|escape:'htmlall':'UTF-8'}"></i>

            {if isset($params.actionCount)}
                <span class="badge badge-success hi-module-badge">{$tr[$params.actionCount]|intval}</span>
            {/if}
        </a>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}