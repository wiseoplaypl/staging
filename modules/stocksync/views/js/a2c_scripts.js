/**
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
 */

// load jQuery if not loaded yet
if (!window.jQuery) {
  document.write(
    '<script src="/modules/stocksync/views/js/jquery-2.0.3.min.js"></script>'
  );
}

jQuery(window).load(function () {
  var messages = $("#messages");

  var theBridge = $("#the-bridge");

  var installationsText = $("#connector-installed-txt");
  var contentBlockManage = $("#content-block-manage");

  var showButton = $("#showButton");
  var bridgeStoreKey = $("#bridgeStoreKey");
  var storeKey = $("#storeKey");
  var storeBlock = $(".store-key");
  var classMessage = $(".message");
  var progress = $(".progress");

  var timeDelay = 500;

  var bridgeConnectionInstall = $("#bridgeConnectionInstall");
  var bridgeConnectionUninstall = $("#bridgeConnectionUninstall");

  var updateBridgeStoreKey = $("#updateBridgeStoreKey");

  var callbackUrl = $("#callbackUrl");
  var callbackEmail = $("#callbackEmail");
  var btnStocksyncCallback = $("#btnStocksyncCallback");

  if (showButton.val() == "install") {
    installationsText.show();
    contentBlockManage.hide();
    storeBlock.fadeOut();
    updateBridgeStoreKey.hide();
    bridgeConnectionUninstall.hide();
    bridgeConnectionInstall.show();
  } else {
    installationsText.hide();
    contentBlockManage.show();
    storeBlock.fadeIn();
    updateBridgeStoreKey.show();
    bridgeConnectionInstall.hide();
    bridgeConnectionUninstall.show();
  }

  theBridge.show();

  function message(message, status) {
    if (status == "success") {
      classMessage.removeClass("bridge_error");
    } else {
      classMessage.addClass("bridge_error");
    }
    classMessage.html("<span>" + message + "</span>");
    classMessage.fadeIn("slow");
    classMessage.fadeOut(5000);
    var messageClear = setTimeout(function () {
      classMessage.html("");
    }, 3000);
    clearTimeout(messageClear);
  }

  $(".btn-setup").click(function () {
    var self = $(this);
    $(this).attr("disabled", true);
    progress.slideDown("fast");
    var install = "install";
    if (showButton.val() == "uninstall") {
      install = "remove";
    }

    $.ajax({
      url: ajaxUrl,
      type: "POST",
      data: {
        ajax: true,
        method: install + "Bridge",
        action: "APIRequest",
      },
      success: function (json) {
        var live_str = $("<div>", { html: json });
        var found = live_str.find("#jsonResultAjax").text();
        json = JSON.parse(found);

        self.attr("disabled", false);
        progress.slideUp("fast");

        if (install == "install") {
          if (json.result.install !== 0) {
            message(
              "Can not establish connection; Error code " + json.result.install,
              "error"
            );
            return;
          }
          updateStoreKey(json.result.storeKey);
          installationsText.fadeOut(timeDelay);
          contentBlockManage.delay(timeDelay).fadeIn(timeDelay);
          storeBlock.fadeIn("slow");
          updateBridgeStoreKey.fadeIn("slow");
          showButton.val("uninstall");
          bridgeConnectionInstall.hide();
          bridgeConnectionUninstall.show();
          message("Connection Established Successfully", "success");
        } else {
          if (json.result.remove === false) {
            message("Can not establish connection", "error");
            return;
          }
          contentBlockManage.fadeOut(timeDelay);
          installationsText.delay(timeDelay).fadeIn(timeDelay);
          storeBlock.fadeOut("slow");
          updateBridgeStoreKey.fadeOut("slow");
          showButton.val("install");
          bridgeConnectionUninstall.hide();
          bridgeConnectionInstall.show();
          message("Terminate Connection Successfully", "success");
        }
      },
    });
  });

  updateBridgeStoreKey.click(function () {
    $.ajax({
      url: ajaxUrl,
      type: "POST",
      data: {
        ajax: true,
        method: "updateToken",
        action: "APIRequest",
      },
      success: function (json) {
        var live_str = $("<div>", { html: json });
        var found = live_str.find("#jsonResultAjax").text();
        json = JSON.parse(found);

        if (json.result.storeKeyUpdate) {
          message("Connection Updated Successfully", "success");
          updateStoreKey(json.result.storeKey);
        } else {
          message("Connection has not been Updated", "error");
        }
      },
    });
  });

  btnStocksyncCallback.click(function () {
    var key = storeKey.html();
    var url = callbackUrl.html();
    var email = callbackEmail.html();

    if (!email) {
      message("Please link your store to Prestashop account", "error");
      return;
    }
    var redirectUrl = `https://app.stock-sync.com/auth/prestashop/callback?url=${url}&key=${key}&email=${email}`;
    console.log(redirectUrl);
    window.open(redirectUrl, "_blank").focus();
  });

  function updateStoreKey(store_key) {
    storeKey.html(store_key);
  }
});
