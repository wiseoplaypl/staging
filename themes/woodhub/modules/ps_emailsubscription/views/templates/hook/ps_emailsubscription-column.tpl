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

<div class="email_subscription block_newsletter" id="blockEmailSubscription_{$hookName}">
  <h2 class="h2 product-section-title">{l s='Newsletter' d='Shop.Theme.Global'}</h2>
  <div id="footer_newsletter" class="block_newsletter_form">
    <i class="icon-plane"></i>
    <p id="block-newsletter-label" class="block-newsletter-label">{l s='Get e-mail updates about our latest shop and special offers.' d='Shop.Theme.Global'}</p>
    {if $msg}
      <p class="notification {if $nw_error}notification-error{else}notification-success{/if}">{$msg}</p>
    {/if}
    <form action="{$urls.current_url}#blockEmailSubscription_{$hookName}" method="post">
      <div class="block_newsletter_info">
        {if $conditions}
          <p class="hidden-xs-up">{$conditions nofilter}</p>
        {/if}
        {hook h='displayNewsletterRegistration'}
        {hook h='displayGDPRConsent' id_module=$id_module}
      </div>
      <div class="block_newsletter_form_inner">
          <input
            name="email"
            type="email"
            value="{$value}"
            placeholder="{l s='Your email address' d='Shop.Forms.Labels'}"
            aria-labelledby="block-newsletter-label"
            required
          >
          <button
            class="btn"
            name="submitNewsletter"
            type="submit"
            >
            {l s='Subscribe' d='Shop.Theme.Global'}
          </button>
          <input type="hidden" name="action" value="0">
        </div>
    </form>
  </div>
</div>
