{*
* 2007-2019 PrestaShop SA and Contributors
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
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2019 PrestaShop SA and Contributors
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
  var ajaxUrl = "{$ajaxUrl|escape:'javascript':'UTF-8'}";
</script>

<div id="the-bridge" style="display: none;" class="panel bridge">
  <h3><i class="icon icon-plug"></i> {l s='Connection' mod='stocksync'}</h3>
  <div class="message"></div>
  <div>
    <div>
      <p id="connector-installed-txt">
        After installing this module, please click the button below to establish a connection between your store and
        Stock Sync before subscribing to any plan.
      </p>
      <div id="content-block-manage">
        <b>Connection with Stock Sync is established!</b>
        <br><br>
        1. Please link your store to a PrestaShop account. <br>
        2. Click the button below to complete your registration on the Stock Sync app page. <br>
        &nbsp;&nbsp;&nbsp; After registering, you can return to the Stock Sync app page at any time by clicking the
        button below. <br>
        <div>
          <button id="btnStocksyncCallback" class="btn-callback">Go to Stock Sync</button>
        </div>
        3. Subscribe to the plan that best suits your store to enable Stock Sync. <br>
        &nbsp;&nbsp;&nbsp; Note: Please register for Stock Sync first by following Step 1. Failure to do so may result
        in a delay in enabling Stock Sync.
        <br><br>
      </div>
    </div>

    <div>
      <div class="progress progress-dark progress-small progress-striped active">
        <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
      </div>

      <div class="store-key">
        <span class="store-key-title">STORE KEY : </span>
        <span class="store-key-content" id="storeKey">{$storeKey|escape:'htmlall':'UTF-8'}</span>
        <button id="updateBridgeStoreKey" class="btn-update-store-key">Update Store Key</button>
      </div>

      <button id="bridgeConnectionUninstall" class="btn-disconnect btn-setup">Disconnect with Stock Sync</button>
      <button id="bridgeConnectionInstall" class="btn-connect btn-setup">Connect with Stock Sync</button>
    </div>

    <table>
      <tr>
        <td>Your store url :</td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td><span id="callbackUrl">{$callbackUrl|escape:'htmlall':'UTF-8'}</span></td>
      </tr>
      <tr>
        <td>Your email :</td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td><span id="callbackEmail">{$callbackEmail|escape:'htmlall':'UTF-8'}</span></td>
      </tr>
    </table>

    <input type="hidden" id="showButton" value="{$showButton|escape:'htmlall':'UTF-8'}">
    <div class="clearfix"></div>
  </div>
</div>