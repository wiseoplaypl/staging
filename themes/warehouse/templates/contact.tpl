{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='page.tpl'}


{block name='page_header_container'}
  <header class="page-header">
    <h1 class="h1 page-title"><span style="font-weight:700;">{l s='Contact us' d='Shop.Theme.Global'}</span></h1>
  </header>

  {* Desktop map only *}
  <div class="d-none d-lg-block">
    {widget name="iqitcontactpage" hook='displayContactMap'}
  </div>
{/block}

{block name='page_content'}
<!-- PAGE_CONTENT_FROM_CONTACT_TPL -->
<div style="padding:10px; background:#ff0; font-weight:bold;">
  PAGE CONTENT OVERRIDE ACTIVE
</div>
  <div class="row">
    <div class="hubspot-contact-form col-12">
      <div class="card p-3 p-lg-4">
        <script src="https://js-eu1.hsforms.net/forms/embed/147326521.js" defer></script>
        <div class="hs-form-frame"
             data-region="eu1"
             data-form-id="eb05f879-1552-422a-8363-82fb24c616df"
             data-portal-id="147326521"></div>

        {*
          Old PrestaShop contact form (kept commented as fallback)
          {widget name="contactform"}
        *}
      </div>
    </div>
  </div>
  {* Mobile map only (after form) *}
  <div class="d-block d-lg-none mt-3">
    {widget name="iqitcontactpage" hook='displayContactMap'}
  </div>
{/block}