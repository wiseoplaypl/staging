{*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2020 Presta.Site
* @license   LICENSE.txt
*}
<label>
    <input class="{$input.class|escape:'html':'UTF-8'} pstpf_{$filter_name|escape:'html':'UTF-8'}_cb pstpf_skip_input"
           type="checkbox"
           value="{$option[$input.id_key]|intval}"
           id="pstpf_{$filter_name|escape:'html':'UTF-8'}_{$option[$input.id_key]|intval}"
           data-option="pstpf_{$filter_name|escape:'html':'UTF-8'}_{$option[$input.id_key]|intval}_option"
           data-block="pstpf_{$filter_name|escape:'html':'UTF-8'}_block"
           data-color="{if isset($option.color)}{$option.color|escape:'html':'UTF-8'}{/if}"
           {if isset($input.value[$option[$input.id_key]]) && $input.value[$option[$input.id_key]]}checked{/if}
    >
    <span class="pstpf-status-name">{$option.name|escape:'html':'UTF-8'}</span>
    {if isset($option.color)}
        <span class="pstpf-status-marker" style="background: {$option.color|escape:'html':'UTF-8'};"></span>
    {/if}
</label>
