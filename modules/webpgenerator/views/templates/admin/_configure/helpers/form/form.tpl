{*
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 *}

{extends file="helpers/form/form.tpl"}
{block name="input"}
    {if $input.type == 'radio-with-disable-option'}
        {foreach $input.values as $value}
			<div class="radio {if isset($input.class)}{$input.class}{/if}">
				{strip}
				<label>
				<input type="radio"	name="{$input.name}" id="{$value.id}" value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($value.disabled) && $value.disabled} disabled="disabled"{/if} {if $value@first} required{/if}/>
					{$value.label}
					{if isset($value.p) && $value.p}
						<span class="label-tooltip question-tooltip" data-toggle="tooltip" data-html="true" title="{$value.p|escape:'quotes':'UTF-8'}"><span class="icon-question-sign"></span></span>
					{/if}
				</label>
				{/strip}
			</div>
		{/foreach}
    {/if}
	{if $input.type == 'html-title'}
        <div class="html-title">
			<h3>
				{if isset($input.icon)}
					<i class="{$input.icon|escape:'html':'UTF-8'}"></i>				
				{/if}
				{if isset($input.title)}{$input.title|escape:'html':'UTF-8'}{/if}
			</h3>
		</div>
    {/if}
	{if $input.type == 'switch-with-class'}
		<span class="switch prestashop-switch fixed-width-lg {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}">
			{foreach $input.values as $value}
				<input type="radio" name="{$input.name}"{if $value.value == 1} id="{$input.name}_on"{else} id="{$input.name}_off"{/if} value="{$value.value}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
				{strip}
					<label {if $value.value == 1} for="{$input.name}_on"{else} for="{$input.name}_off"{/if}>
						{if $value.value == 1}
							{l s='Yes'}
						{else}
							{l s='No'}
						{/if}
					</label>
				{/strip}
			{/foreach}
			<a class="slide-button btn"></a>
		</span>
		{if isset($input.switch_desc) && !empty($input.switch_desc)}
			<p class="help-block">{$input.switch_desc|escape:'html':'UTF-8'}</p>
		{/if}
	{/if}
	{if $input.type == 'html-line-separator'}
        <hr class="html-separator">
    {/if}
    {$smarty.block.parent}
{/block}
