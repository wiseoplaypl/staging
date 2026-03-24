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
<div class="block_newsletter section-margin-top parallax">
  <div class="container">
    <div class="row">
  <div class="newsletter-block-content">
    <div class="block-newsletter-label">
        <h5 class="widget-title">{l s='Get Our Newsletter' d='Shop.Theme.Global'}</h5>
        <div class="newsletter-description">{l s='Lorem Ipsum is simply dummy text of the printing and typesetting industry. 
Lorem Ipsum has been the industry standard.' d='Shop.Theme.Global'}</div>
      </div>
    <div id="footer_newsletter" class="block_newsletter_form">
      <form action="{$urls.pages.index}#footer" method="post">
        <div class="block_newsletter_form_inner">
          <div class="input-wrapper">
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
              <i class="icon-plane" aria-hidden="true"></i>
            </button>
            <input type="hidden" name="action" value="0">
          </div>
        </div>
        <div class="block_newsletter_info">
            {if $conditions}
              <p class="hidden-xs-up">{$conditions}</p>
            {/if}
            {if $msg}
              <p class="alert {if $nw_error}alert-danger{else}alert-success{/if}">
                {$msg}
              </p>
            {/if}
            {if isset($id_module)}
              {hook h='displayGDPRConsent' id_module=$id_module}
            {/if}
        </div>
      </form>
    </div>
  </div>
</div>
</div>
</div>
