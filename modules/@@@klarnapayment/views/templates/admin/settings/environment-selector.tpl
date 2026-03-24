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
<div class="environment-selector">
  <label class="radio-inline environment-text">
    <input type="radio" class="environment-switch production" value="{$klarnapayment.production_current_page_url|escape:'htmlall':'UTF-8'}">{l s='Production' mod='klarnapayment'}
  </label>
  <label class="radio-inline environment-text">
    <input type="radio" class="environment-switch sandbox" value="{$klarnapayment.sandbox_current_page_url|escape:'htmlall':'UTF-8'}">{l s='Playground' mod='klarnapayment'}
  </label>
</div>
