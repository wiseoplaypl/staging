{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
{if $page.page_name == 'index'}
<div class="footer-top-wrapper">
  <div class="container">
    {block name='hook_footer_before'}
    {/block}
  </div>
</div>
{/if}
<div class="footer-container">
  <div class="container">
    <div class="row">
      <div class="footer-container-inner">
        {block name='hook_footer'}
          {hook h='displayFooter'}
          {hook h='displayFooter2'}
        {/block}
      </div>
    </div>
    <div class="row">
      {block name='hook_footer_after'}
      {/block}
    </div>
  </div>
</div>
<div class="footer-bottom">
  <div class="container">
      <div class="footer-bottom-inner">
        {hook h='displayFooterAfter'}
        <p class="copyright_link" style="color:#666">
          {block name='copyright_link'}
              {l s='%copyright% %year% - Ecommerce software by %prestashop%' sprintf=['%prestashop%' => 'PrestaShop™', '%year%' => 'Y'|date, '%copyright%' => '©'] d='Shop.Theme.Global'}
          {/block}
        </p>
        {hook h='displayFooterPayment'}
    </div>
  </div>
</div>

