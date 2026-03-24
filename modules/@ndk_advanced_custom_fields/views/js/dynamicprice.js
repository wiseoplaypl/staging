/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
 */

var priceResult = 0;
var reduc_Array = false;
var label = labelTotal; //Libellé affiché
var type = $("meta[itemprop=priceCurrency]").attr("content");
var message = priceMessage;
var message_specific = priceMessageSpecific;
var productPriceUp = 0;
var priceInitiated = [];
var last_qtty_sp = 0;
var last_id_combination_sp = 0;
var selectedConfigValue = [];
var totalUnitPrice = 0;
var productPrice;
var ndkcfAttrStock = false;
var reduc_only_product = false;
var isItSelf = false;
var percent_array = [];
var groupMultiply = [];

$(document).ready(function () {
  if (isFields != 1 || $("#ndkcsfields-block").length < 1) return;

  if (ps_version == 16) ps_version = 1.6;
  if (ps_version == 17) ps_version = 1.7;

  checkItself();
  initPriceVars();
  //set price node
  setNdkcfPricesNodes();

  /*$("#quantity_wanted").keyup(function(){
		update_price_dynamic(0);
	});*/

  $("#quantity_wanted").change(function () {
    $("#quantity_wanted").trigger("keyup");
    if (ps_version > 1.6) {
      var productDetails = $("#product-details").data("product");
      if (typeof productDetails != "undefined") {
        if (ndkcfAttrStock) attrStock = ndkcfAttrStock;
        else attrStock = productDetails.quantity;

        if (
          parseFloat(attrStock) < $(this).val() &&
          parseInt(productDetails.allow_oosp) == 0 &&
          (editConfig == 0 || editAttr != $("#ndkcf_id_combination").val())
        ) {
          if (productDetails.available_later != "")
            out_of_stock_text = productDetails.available_later;
          $(".ndkcsfields-block").fadeOut(600);
          $("#product-availability")
            .fadeIn()
            //.text(out_of_stock_text)
            .find("i")
            .removeClass("product-available")
            .addClass("product-unavailable");
        } else {
          if (productDetails.available_now != "")
            in_stock_text = productDetails.available_now;

          $(".ndkcsfields-block").fadeIn(600);
          $("#product-availability")
            .fadeIn()
            //.text(in_stock_text)
            .find("i")
            .removeClass("product-unavailable")
            .addClass("product-available");
        }
      }
    }
  });

  setTimeout(function () {
    $("#quantity_wanted").trigger("keyup");
  }, 500);

  /*var resumeBlock = new HoverWatcher('#ndkcf_recap');
	$("#ndkcf_recap").hover(
		function() {
				$("#ndkcf_recap > .ndkcf_recap_content").stop(true, true).slideDown('slow');
				//$(this).addClass('showing');
		},
		function() {
			setTimeout(function() {
				if (!resumeBlock.isHoveringOver() )
					$("#ndkcf_recap > .ndkcf_recap_content").stop(true, true).slideUp('slow');
					//$(this).removeClass('showing');
			}, 6000);
		}
	);*/

  $(document).on("click", "#ndkcf_recap .ndkcf_recap_title", function () {
    $("#ndkcf_recap > .ndkcf_recap_content")
      .stop(true, true)
      .slideToggle("slow");
    $(this)
      .find(".toggleRecap")
      .toggleText("arrow_drop_up", "arrow_drop_down")
      .toggleClass("opened")
      .toggleClass("closed");
  });
});

//let ndkcfTargetPrice17 = ".product-prices:eq(0)";
var ndkcfTargetPrice17 = ".product-prices:eq(0)";
var ndkcfTargetPrice16 = ".current-price, #our_price_display";
function setNdkcfPricesNodes() {
  if (typeof setNdkcfPricesNodes_Override == "function") {
    return setNdkcfPricesNodes_Override();
  }
  if (
    !$.isEmptyObject(reduc_Array) &&
    typeof (reduc_Array["reduction_type"] != "undefined") &&
    !isItSelf
  ) {
    $("span#old_price_display").after(
      "<span id='oldPrice' class='specificBlock'></span><span id='specificReduct' class='specificBlock'></span><div class='blockPrice clear clearfix'><p class='contentPrice'><span class='labelPriceUp'>" +
        label +
        "</span><span class='price productPriceUp' itemprop='price'></span><span class='price productPriceUpHT' itemprop='price'></span></p></div>"
    ); //Ajout du contenu

    $("span#old_price_display").after("<span id='specificPrice'></span>");
  } else {
    if (ps_version > 1.6) {
      $(ndkcfTargetPrice17).after(
        "<span id='oldPrice' class='specificBlock'></span><span id='specificReduct' class='specificBlock'></span><div class='blockPrice clear clearfix'><p class='contentPrice'><span class='labelPriceUp'>" +
          label +
          "</span><span class='price productPriceUp' itemprop='price'></span><span class='price productPriceUpHT' itemprop='price'></span></p></div>"
      ); //Ajout du contenu
      $(ndkcfTargetPrice17).after("<span id='specificPrice'></span>");
    } else {
      $(ndkcfTargetPrice16)
        .parent()
        .append(
          "<span id='oldPrice' class='specificBlock'></span><span id='specificReduct' class='specificBlock'></span><div class='blockPrice clear clearfix'><p class='contentPrice'><span class='labelPriceUp'>" +
            label +
            "</span><span class='price productPriceUp' itemprop='price'></span><span class='price productPriceUpHT' itemprop='price'></span></p></div>"
        ); //Ajout du contenu
      $(ndkcfTargetPrice16).after("<span id='specificPrice'></span>");
    }
  }
}
$.fn.extend({
  toggleText: function (a, b) {
    return this.text(this.text() == b ? a : b);
  },
});

var startProductPrice;
function initPriceVars() {
  if (typeof initPriceVars_Override == "function") {
    return initPriceVars_Override();
  }
  if (isFields != 1 || $("#ndkcsfields-block").length < 1) return;

  id_product = $("#ndkcf_id_product").val();
  id_combination = $("input#idCombination").val();
  qtty = $("#quantity_wanted").val();
  $.ajax({
    async: true,
    type: "GET",
    global: false,
    dataType: "json",
    url: baseUrl + "modules/ndk_advanced_custom_fields/front_ajax.php",
    data: {
      id_product: id_product,
      id_product_attribute: id_combination,
      quantity: 1,
      action: "getAttributePrice",
    },
    success: function (data) {
      if (parseFloat(addProductPrice) > 0 && $(".ndk_itself").length < 1) {
        productPrice = data.price;
        startProductPrice = data.price;
      } else {
        productPrice = startProductPrice = 0;
      }

      //console.trace(productPrice);
      priceInitiated = true;
    },
  });

  var standard_reduc = false;

  last_qtty_sp = qtty;
  last_id_combination_sp = id_combination;

  $.ajax({
    async: true,
    type: "GET",
    global: false,
    dataType: "json",
    url: baseUrl + "modules/ndk_advanced_custom_fields/front_ajax.php",
    data: {
      id_product: id_product,
      id_product_attribute: id_combination,
      quantity: qtty,
      action: "getSpecificPrice",
    },
    success: function (data) {
      var reduc_Array = data;
      if (isItSelf) reduc_array = [];
      update_price_dynamic(0);
    },
  });
}

function checkItself() {
  id_product = $("#product_page_product_id").val();
  $("[data-id-product-value=" + id_product + "]").addClass("ndk_itself");

  /*$('.ndk_itself').each(function(){
    group = $(this).attr('data-group');
    $('.ndkackFieldItem[data-field='+group+']').hide();
  })*/

  if ($(".ndk_itself").length > 0) {
    var addProductPrice = 0;
    $("#quantity_wanted").hide().val(1).trigger("change");
    $("#quantity_wanted").parent().hide();
    $("body").addClass("ndkCfItself");
    isItSelf = true;
  }
}

function update_price_dynamic(value) {
  $("#ndkloader").fadeOut().remove();
  // $(".product-prices")
  //   .parent()
  //   .append(ndkLoader)
  //   .addClass("small_loader_container");
  setTimeout(function () {
    id_product = $("#ndkcf_id_product").val();
    id_combination = $("input#idCombination").val();
    qtty = $("#quantity_wanted").val();

    if (isItSelf) reduc_array = [];

    if (qtty != last_qtty_sp || id_combination != last_id_combination_sp) {
      last_qtty_sp = qtty;
      last_id_combination_sp = id_combination;
      $.ajax({
        async: true,
        type: "GET",
        global: false,
        dataType: "json",
        url: baseUrl + "modules/ndk_advanced_custom_fields/front_ajax.php",
        data: {
          id_product: id_product,
          id_product_attribute: id_combination,
          quantity: qtty,
          action: "getSpecificPrice",
        },
        success: function (data) {
          reduc_Array = data;
          if (reduc_Array["old_price"] === data["old_price"])
            display_price_dynamic(value, data);
          else {
            update_price_dynamic(value);
          }
        },
      });
    } else {
      if (reduc_Array["old_price"] > 0)
        display_price_dynamic(value, reduc_Array);
      else {
        //update_price_dynamic(value);
        display_price_dynamic(value);
      }
    }
    //console.log(reduc_Array)
    $(".small_loader_container #ndkloader").fadeOut().remove();
  }, 500);
}

/*function arraysEqual(a, b) {
  a = Array.isArray(a) ? a : [];
  b = Array.isArray(b) ? b : [];
  return a.length === b.length && a.every((el, ix) => el === b[ix]);
}*/

function getPriceHt(price, element, pure) {
  if (typeof getPriceHt_Override == "function") {
    return getPriceHt_Override(price, element, pure);
  }
  element = element || "";
  pure = pure || false;
  ht_price = price / (1 + ndk_taxe_rate / 100);
  if (pure) {
    return ht_price;
  } else {
    if (element != "")
      formatCurrencyNdkCallback(ht_price * 1, element, labelTotalHT);
    else return formatCurrencyNdk(ht_price);
  }
}

function getPriceHt_back(price, element) {
  if (typeof getPriceHt_Override == "function") {
    return getPriceHt_Override(price, element);
  }
  element = element || "";
  id_product = $("#ndkcf_id_product").val();
  $.ajax({
    async: true,
    type: "GET",
    global: false,
    dataType: "json",
    url: baseUrl + "modules/ndk_advanced_custom_fields/front_ajax.php",
    data: { price: price, id_product: id_product, action: "removePriceTaxes" },
    success: function (data) {
      if (element != "")
        formatCurrencyNdkCallback(data * 1, element, labelTotalHT);
      else return formatCurrencyNdk(data);
    },
  });
}

function getPricesDiscount(group, value) {
  if (typeof getPricesDiscount_Override == "function") {
    return getPricesDiscount_Override(group, value);
  }
  qtty = $("#quantity_wanted").val();
  id_product = $("#ndkcf_id_product").val();
  $.ajax({
    async: true,
    type: "GET",
    global: false,
    dataType: "json",
    url: baseUrl + "modules/ndk_advanced_custom_fields/front_ajax.php",
    data: {
      id_product: id_product,
      group: group,
      value: value,
      quantity: qtty,
      action: "getPricesDiscount",
    },
    success: function (data) {
      if (isNaN(data)) data = 0;
      price_type = $("#price_type_" + group).attr("data-price-type");

      if (groupAdded[group] > 0 && data != groupAdded[group] && data > 0) {
        if (parseFloat(data) > 0 && !isNaN(data)) groupAdded[group] = data;
        $("#quantity_wanted").trigger("change");
        $("#quantity_wanted").trigger("touchspin.stopspin");
      }
    },
  });
}

function getAllPricesDiscount() {
  if (typeof getAllPricesDiscount_Override == "function") {
    return getAllPricesDiscount_Override();
  }
  getSelectedValuesForPrice();
  qtty = $("#quantity_wanted").val();
  id_product = $("#ndkcf_id_product").val();
  $.ajax({
    async: true,
    type: "GET",
    global: false,
    dataType: "json",
    url: baseUrl + "modules/ndk_advanced_custom_fields/front_ajax.php",
    data: {
      id_product: id_product,
      group: selectedConfigValue,
      quantity: qtty,
      action: "getAllPricesDiscount",
    },
    success: function (data) {
      for (idG in data) {
        if (
          parseFloat(data[idG]) > 0 &&
          !isNaN(data[idG]) &&
          parseFloat(groupAdded[idG]) != data[idG]
        ) {
          groupAdded[idG] = data[idG];
          $("#quantity_wanted").trigger("change");
          $("#quantity_wanted").trigger("touchspin.stopspin");
        }
      }
    },
  });
}

function getSelectedValuesForPrice() {
  if (typeof getSelectedValuesForPrice_Override == "function") {
    return getSelectedValuesForPrice_Override();
  }
  selectedConfigValue = [];
  $("*[name^='ndkcsfield[']")
    .not(".recipient-field")
    .not(".orientation_input")
    .each(function () {
      //console.log($(this))
      group = $(this).attr("id").replace("ndkcsfield_", "");
      group = group.replace("subselect-", "");
      //on applique la quantité min si l'option a un stock
      checkStockOption(group);
      if ($(this).is(":radio")) {
        group = $(this).attr("data-group");
        if ($(this).is(":checked")) {
          selectedConfigValue[group] = $(this).val();
        }
      } else selectedConfigValue[group] = $(this).val();
    });
  return selectedConfigValue;
}

function checkStockOption(group) {
  if (typeof checkStockOption_Override == "function") {
    return checkStockOption_Override(group);
  }

  maxProductQuantity = 999999999;
  rootBlock = $(".form-group[data-field='" + group + "']");
  rootBlock.find("[data-quantity-available]").each(function () {
    if (
      ($(this).is(":checked") ||
        $(this).hasClass("selectedValue") ||
        $(this).is(":selected")) &&
      parseFloat($(this).attr("data-quantity-available")) < maxProductQuantity
    )
      maxProductQuantity = parseFloat($(this).attr("data-quantity-available"));
    if (maxProductQuantity < $("#quantity_wanted").attr("max"))
      $("#quantity_wanted").attr("max", maxProductQuantity);
  });
}

function display_price_dynamic(value, reduc_Array) {
  if (typeof display_price_dynamic_Override == "function") {
    return display_price_dynamic_Override(value, reduc_Array);
  }

  if (isItSelf) reduc_Array = [];
  //test provisoire : getAllPricesDiscount()
  $(".product_quantity_up").attr("onclick", "update_price_dynamic(1);"); //Ajout la fonction onclick à l'élément  [+]
  $(".product_quantity_down").attr("onclick", "update_price_dynamic(-1);"); //Ajout la fonction onclick à l'élément  [-]
  //$('#quantity_wanted').val(1);//Initialise la quantité par défaut à 1

  //console.log(reduc_Array)
  //var productPrice = $.trim($('#our_price_display').text().replace(currencySign, '').replace(',', '.').replace(' ', '').replace('-', ''));
  id_product = $("#ndkcf_id_product").val();
  id_combination = $("input#idCombination").val();
  productPrice = startProductPrice;
  if (!$.isEmptyObject(reduc_Array) && reduc_Array["old_price"] != null) {
    if (ps_version > 1.6) {
      formatCurrencyNdkCallback(
        parseFloat(reduc_Array["old_price"]),
        "#old_price_display > span.price",
        "",
        ""
      );
    }
    if (
      addProductPrice == 1 &&
      $(".ndk_itself").length < 1 &&
      parseFloat(reduc_Array["public_price"]) > 0
    ) {
      //productPrice = parseFloat(reduc_Array['old_price']);
      //productPrice = parseFloat(reduc_Array["public_price"]);
    } else productPrice = 0;

    //console.trace(productPrice);
  }
  productPrice = productPrice * newTaxRatio;
  $("#our_price_display").show();
  $("#specificPrice").text("");
  var id_combination = $("input#idCombination").val();
  var specific_length_general = 0;
  var specific_length = 0;
  var total_specific_length = 0;
  var reduction_used_customization = 0;
  if (id_combination == "") {
    id_combination = 0;
  }

  var qty_wanted = $("#quantity_wanted").val(); //Récupération de la quantité sélectionnée
  qty_wanted = parseFloat(qty_wanted);

  if (qty_wanted == quantityAvailable && value == 1) {
    qty_wanted = quantityAvailable - 1;
  }
  if (value == -1) {
    var value_available = 1;
  } else {
    var value_available = 0;
  }

  qty_wanted = parseFloat(qty_wanted);
  //qty_wanted = qty_wanted+value;
  $(".quantityAvailableAlert").hide();
  $(".contentPrice").show();
  selectedConfigValue = getSelectedValuesForPrice();

  var customizationPrice = 0;
  var idPrice;
  for (idPrice in groupAdded) {
    if (typeof groupAdded[idPrice] != "undefined") {
      price_type = $("#price_type_" + idPrice).attr("data-price-type");
      //console.log(price_type);
      if (price_type != "percent" && parseFloat(groupAdded[idPrice]) > 0) {
        if (price_type == "one_time")
          customizationPrice =
            parseFloat(customizationPrice) +
            parseFloat(groupAdded[idPrice]) / $("#quantity_wanted").val();
        else
          customizationPrice =
            parseFloat(customizationPrice) + parseFloat(groupAdded[idPrice]);
      }
    }
  }

  for (idPrice in groupAdded) {
    if (typeof groupAdded[idPrice] != "undefined") {
      price_type = $("#price_type_" + idPrice).attr("data-price-type");
      //console.log(productPrice);
      if (typeof price_type != "undefined" && price_type == "percent") {
        percent_array[idPrice] = groupAdded[idPrice];
      }
    }
  }

  customizationPricePercent = 0;
  for (idPrice in percent_array) {
    multiplicator = percent_array[idPrice] / 100;
    totalPrice =
      parseFloat(productPrice * 1) +
      parseFloat(customizationPrice * 1) +
      parseFloat(customizationPricePercent * 1);
    toAdd = totalPrice * multiplicator;
    //console.log(toAdd)
    if (parseFloat(toAdd) > 0) customizationPricePercent += toAdd;
  }
  customizationPrice += customizationPricePercent;

  /*if(typeof priceWithDiscountsDisplay != 'undefined'){
			productPriceUp = priceWithDiscountsDisplay;
		}*/

  if (isItSelf) reduc_Array = [];
  if (
    !$.isEmptyObject(reduc_Array) &&
    typeof (reduc_Array["reduction_type"] != "undefined") &&
    typeof (reduc_Array["reduction"] != "undefined")
  ) {
    //vérifie l'existance d'un prix spécifique

    var reduction = [];
    var reduction_type = [];
    var qty_required = [];
    var qty_required_value = [];
    var type_used = 0;
    var reduction_used = 0;
    var qty_required_used = 0;
    var libelle_reduction = 0;
    var new_price = "";
    var initial_price = productPrice + customizationPrice;

    $("#reduction_amount").hide();
    $("#reduction_percent").hide();

    if (
      !$.isEmptyObject(reduc_Array) &&
      typeof (reduc_Array["reduction_type"] != "undefined")
    ) {
      type_used = reduc_Array["reduction_type"];
      standard_reduc = true;
      qty_required_used = 1;
      reduction_used = parseFloat(reduc_Array["reduction"]);
    }

    if (type_used == "percentage") {
      libelle_reduction = parseInt(reduction_used * 100) + "%";
      $("#reduction_percent_display").text("-" + libelle_reduction);
      $("#reduction_amount").hide();
      $("#reduction_percent").show();

      oldCustomizationPrice = formatCurrencyNdk(customizationPrice);

      if (reduc_only_product) {
        reduction_used = (productPriceUp - customizationPrice) * reduction_used;
        reduction_customization_used = 0;
        customizationPrice = customizationPrice - reduction_customization_used;
      } else {
        //reduction_used = productPriceUp*reduction_used;
        reduction_customization_used = customizationPrice * reduction_used;
        customizationPrice = customizationPrice - reduction_customization_used;
        // formatCurrencyNdkCallback(customizationPrice , '.additionnal_price', '('+additionnalText+'+', ') <span class="old_price">(+'+oldCustomizationPrice+')</span>');
        formatCurrencyNdkCallback(
          customizationPrice,
          ".additionnal_price",
          "(" + additionnalText + "+",
          ") "
        );
        reduction_used = productPriceUp * reduction_used;
      }
    }

    if (type_used == "amount") {
      reduction_used = parseFloat(reduction_used);
      libelle_reduction = formatCurrencyNdk(parseFloat(reduction_used));
      if ($("#reduction_amount_display").text().indexOf("%") == -1)
        $("#reduction_amount_display").text("-" + libelle_reduction);
      $("#reduction_amount").show();
      $("#reduction_percent").hide();
    }

    //new_price = productPriceUp-reduction_used;
    new_price = productPriceUp;
    productPriceUp = productPriceUp;
    //reduced_price = productPrice-reduction_used;
    reduced_price = productPrice;

    $("#specificReduct").text("-" + libelle_reduction);
    $("#specificReduct").addClass("specificReductStyle");
    $("#specificPrice").text(new_price + " " + currencySign);

    /*if(!isNaN(new_price))
		formatCurrencyNdkCallback(parseFloat(reduced_price) , '#our_price_display', '', '');*/

    if (
      !$.isEmptyObject(reduc_Array) &&
      typeof (reduc_Array["reduction_type"] != "undefined") &&
      parseFloat(reduction_used) > 0
    ) {
      formatCurrencyNdkCallback(
        parseFloat(reduc_Array["old_price"]),
        "#oldPrice"
      );
      priceResult = qty_wanted * new_price;
      if (parseFloat(priceResult) > 0 && !standard_reduc) {
        $(".specificBlock").slideDown("slow");
      }
    } else {
      priceResult = qty_wanted * productPriceUp;
      //console.log('no discount');
      if (parseFloat(priceResult) > 0) {
        $("span#our_price_display").show();
        $(".specificBlock").fadeOut("normal");
        $("#specificPrice").hide();
      }
    }
  } else {
    //console.log('no discount');
    if (parseFloat(priceResult) > 0) {
      $("span#our_price_display").show();
      $(".specificBlock").fadeOut("normal");
      $("#specificPrice").hide();
      formatCurrencyNdkCallback(priceResult * 1, ".productPriceUp");
      formatCurrencyNdkCallback(productPriceUp * 1, "#unit_price_display");
      if (displayPriceHT == 1) {
        setTimeout(function () {
          getPriceHt(priceResult, ".productPriceUpHT");
          getPriceHt(productPriceUp, "#unit_price_display");
        }, 500);
      }
    }
  }
  if (parseFloat(priceResult) > 0) {
    $("span#our_price_display").show();
    $(".specificBlock").fadeOut("normal");
    $("#specificPrice").hide();
    formatCurrencyNdkCallback(priceResult * 1, ".productPriceUp");
    formatCurrencyNdkCallback(productPriceUp * 1, "#unit_price_display");
    if (displayPriceHT == 1) {
      setTimeout(function () {
        getPriceHt(priceResult, ".productPriceUpHT");
        getPriceHt(productPriceUp, "#unit_price_display");
      }, 500);
    }
    //console.log([priceResult, productPrice]);
    setTimeout(function () {
      if (priceResult > productPrice) {
        $(".blockPrice").addClass("ndk-visible").removeClass("ndk-hidden");
      } else {
        $(".blockPrice").addClass("ndk-hidden").removeClass("ndk-visible");
      }
    }, 500);
  }

  if (!isItSelf) {
    if (ps_version > 1.6) {
      formatCurrencyNdkCallback(
        productPrice * 1,
        ".current-price [itemprop='price']"
        //".current-price [itemprop='price']:eq(0)",
      );
    } else {
      // formatCurrencyNdkCallback(
      //   productPrice * 1,
      //   ".our_price_display [itemprop='price']"
      //   //".our_price_display [itemprop='price']:eq(0)"
      // );
    }
  }
  //console.log(customizationPrice);
  productPriceUp = parseFloat(productPrice);
  productPriceUp += parseFloat(customizationPrice);
  priceResult = qty_wanted * productPriceUp;

  if (customizationPrice > 0) $(".blockPrice").slideDown();
  else $(".blockPrice").slideUp();
  //console.log(priceResult);

  if (showRecap == 1) {
    $("#ndkcf_recap").show();
    $("#ndkcf_recap_linear").parent().show();
    setRecap();
  } else {
    setRecap();
    $("#ndkcf_recap").hide();
    $("#ndkcf_recap_linear").parent().hide();
  }

  totalUnitPrice = priceResult / qty_wanted;
  $("body").trigger({
    type: "ndkacf:priceChange",
    price: priceResult,
  });
}

/*$(document).on('change', '#our_price_display, #quantity_wanted, input#idCombination', function(){
	update_price_dynamic(0);
});*/

$(document).on(
  "change",
  ".ndkcfLoaded #quantity_wanted, input#idCombination",
  function () {
    for (idPrice in groupUnitPrice) {
      customizationPrice = getCustomizationPrice(
        groupUnitPrice[idPrice],
        idPrice
      );
    }
    update_price_dynamic(0);
  }
);

$(document).on(
  "touchspin.stopspin",
  ".ndkcfLoaded #quantity_wanted",
  function () {
    update_price_dynamic(0);
  }
);

//recap option
function setRecap() {
  if (typeof setRecap_Override == "function") {
    return setRecap_Override();
  }
  $(".recap_group").removeClass("recap_filled");
  getSelectedValuesForPrice();
  $("#ndkcf_recap").show();
  $("#ndkcf_recap .recap_group").html("");
  //initPriceVars();

  //$("#ndkcf_recap > .ndkcf_recap_content").stop(true, true).slideDown('slow');
  //$('.toggleRecap.closed').trigger('click');
  //$('.recap_group').remove();

  if (!isItSelf) setRecapItemBaseProduct();

  setRecapRecipient();

  for (idGroup in selectedConfigValue) {
    if (
      selectedConfigValue[idGroup] != "" &&
      selectedConfigValue[idGroup] != 0
    ) {
      input = $("#" + idGroup);

      if (input.length < 1)
        input = $(".ndkackFieldItem[data-field='" + idGroup + "']")
          .find("input:checked")
          .eq(0);

      if (input.length < 1) input = $("#ndkcsfield_" + idGroup);

      if (input.hasClass("ndk-accessory-comb-tab")) {
        setRecapItemCombTab(idGroup, input);
      } else if (input.hasClass("dimension_text")) {
        setRecapItemDimensions(idGroup, input);
      } else if (input.hasClass("surface")) {
        setRecapItemSurface(idGroup, input);
      } else if (input.hasClass("ndk-checkbox")) {
        setRecapItemcheckbox(idGroup, input);
      } else if (input.hasClass("ndk-radio")) {
        setRecapItemRadio(idGroup, input);
      } else if (input.hasClass("ndk-accessory-quantity")) {
        //setRecapItemAccessory(idGroup, input);
        setRecapItemCombTab(idGroup, input);
      } else {
        setRecapItemGlobal(idGroup, input);
      }
    } else {
      $(".recap_item_" + idGroup).remove();
      //$('.recap_group_'+idGroup).remove();
    }
    $("#ndkcf_recap").trigger({
      type: "ndkacf:recapSet",
      group: idGroup,
    });
    if (
      !$.isEmptyObject(reduc_Array) &&
      typeof (reduc_Array["reduction_type"] != "undefined")
    ) {
      type_used = reduc_Array["reduction_type"];
      standard_reduc = true;
      qty_required_used = 1;
      reduction_used = parseFloat(reduc_Array["reduction"]);

      if (type_used == "percentage") {
        reduction_customization_used = customizationPrice * reduction_used;
        reduction_used =
          (reduc_Array["old_price"] + customizationPrice) * reduction_used;
      }
      if (type_used == "amount") {
        reduction_used = parseFloat(reduction_used);
      }
      if (parseFloat(reduction_used) > 0) {
        libelle_reduction = formatCurrencyNdk(parseFloat(reduction_used));
        $(".reduc_total").remove();
        $(".ndkcf_recap_content .ndkcf_recap_total").prepend(
          '<p class="reduc_total">-' + libelle_reduction + "</p>"
        );
      }
    }

    formatCurrencyNdkCallback(priceResult * 1, ".ndkcf_recap_total > .price");
    formatCurrencyNdkCallback(priceResult * 1, ".productPriceUp");
    if (displayPriceHT == 1)
      getPriceHt(priceResult, ".ndkcf_recap_total > .priceht");
  }

  if ($(window).width() > 480) {
    $("#ndkcf_recap_linear").html($("#ndkcf_recap").html());
    //$('#ndkcf_recap_linear .groupTotalPriceNo').parent().parent().html('');
    $("#ndkcf_recap_linear .ndkcf_recap_title")
      .removeClass("ndkcf_recap_title")
      .addClass("recap_title");
    //$("#ndkcf_recap_linear > .ndkcf_recap_content").stop(true, true).slideDown();
    if (displayPriceHT == 1) {
      $("#ndkcf_recap_linear .groupTotalPrice").each(function () {
        id_group = $(this).parent().parent().attr("data-group");
        $(this).after(
          '<span class="priceht" id="priceht_' + id_group + '"></span>'
        );
        getPriceHt($(this).attr("content"), "#priceht_" + id_group);
      });
    }
  }

  // setTimeout(function(){
  // 		//$("#ndkcf_recap > .ndkcf_recap_content").stop(true, true).slideUp('slow');
  // 		$('.toggleRecap.opened').trigger('click');
  // }, 15000);

  if ($("#ndkcf_recap .recap_group_0").length > 1) {
    $("#ndkcf_recap .recap_group_0:eq(0)").remove();
  }
}

function setRecapItemBaseProduct() {
  if (typeof setRecapItemBaseProduct_Override == "function") {
    return setRecapItemBaseProduct_Override();
  }
  productUnitPrice = $("#our_price_display").attr("content");
  //productUnitPrice = productPrice;
  productName = $("h1[itemprop='name']:eq(0)").text();
  if (isNaN(productPrice)) {
    productPrice = 0;
  }

  $.when($(".recap_group_0").remove()).done(function () {
    if ($(".recap_group_0").length < 1)
      $("#ndkcf_recap > .ndkcf_recap_content").prepend(
        '<div class="recap_group recap_group_0" data-group="0"><p class="recap_group_title">' +
          productName +
          ' : <span class="groupTotalPrice" content="' +
          productPrice +
          '">' +
          (productPrice > 0 ? formatCurrencyNdk(productPrice) : "") +
          "</span></p></div>"
      );
  });

  if ($("#ndkcf_recap .recap_group_0").length > 1) {
    $("#ndkcf_recap .recap_group_0:eq(0)").remove();
  }
}

function setRecapItemGlobal(idGroup, input) {
  if (typeof setRecapItemGlobal_Override == "function") {
    return setRecapItemGlobal_Override(idGroup, input);
  }
  price_percent = false;
  visu = input.val();
  rootGroup = idGroup;
  rootGroupBlock = $(
    ".form-group[data-field='" + rootGroup + "']:not(.submitContainer)"
  );
  groupTitleEl = rootGroupBlock.find("label:eq(0)").clone();
  groupTitleEl
    .find(".toggleText, .tooltipDescription, .tooltipDescMark")
    .remove();
  groupTitle = groupTitleEl.text();
  idprice = rootGroup;
  idpriceGroup = idprice;
  groupTotalPrice = groupAdded[idprice];

  price_type = $("#price_type_" + idprice).attr("data-price-type");

  if (typeof price_type != "undefined" && price_type == "percent") {
    /*multiplicator = groupAdded[idprice]/100;
  	totalPrice = parseFloat(productPrice*1) + parseFloat(customizationPrice*1);
  	groupTotalPrice = totalPrice*multiplicator;
  	console.log(totalPrice);
  	*/
    price_percent = true;
  }

  $(".recap_item_" + rootGroup + "_" + idGroup).remove();
  if (
    $("#ndkcf_recap .recap_group_" + rootGroup + " .recap_group_title").length <
    1
  )
    $("#ndkcf_recap .recap_group_" + rootGroup).append(
      '<p class="recap_group_title">' +
        groupTitle +
        '<span class="point_separator"> : </span>' +
        (groupTotalPrice > 0
          ? '<span class="groupTotalPrice">' +
            (price_percent
              ? "+" + groupTotalPrice + "%"
              : formatCurrencyNdk(groupTotalPrice)) +
            "</span>"
          : '<span class="groupTotalPrice groupTotalPriceNo">&nbsp;</span>') +
        "</p>"
    );

  itemPrice = groupAdded[idprice];
  /*if(typeof(price_type) != 'undefined' && price_type == 'percent'){
  	multiplicator = groupAdded[idprice]/100;
  	totalPrice = parseFloat(productPrice*1)+ parseFloat(customizationPrice*1);
  	itemPrice = totalPrice*multiplicator;
  }*/

  recapHtml = `<div class="recap_item recap_item_${rootGroup}_${idGroup}">${strReplaceAll(
    visu,
    "¶|\n|\r\n",
    "</br>"
  )}${getGroupDisplayMultiplicator(rootGroup)}${
    parseFloat(itemPrice) > 0
      ? " : " +
        (price_percent
          ? "+" + itemPrice + "%"
          : formatCurrencyNdk(itemPrice / groupMultiply[rootGroup].quantity))
      : ""
  }</div>`;

  $(".recap_group_" + rootGroup)
    .append(recapHtml)
    .addClass("recap_filled");

  if (parseFloat(groupTotalPrice) > 0)
    $(".recap_group_" + rootGroup)
      .find(".groupTotalPrice")
      .html(
        price_percent
          ? "+" + groupTotalPrice + "%"
          : formatCurrencyNdk(groupTotalPrice)
      )
      .removeClass("groupTotalPriceNo")
      .attr("content", groupTotalPrice);
  else
    $(".recap_group_" + rootGroup)
      .find(".groupTotalPrice")
      .html("")
      .addClass("groupTotalPriceNo");

  if (rootGroupBlock.attr("class").indexOf("disabled_value_by") > -1) {
    $(".recap_group_" + rootGroup).addClass("disabled_value_by");
  } else {
    $(".recap_group_" + rootGroup).removeClass("disabled_value_by");
    $("#ndkcf_recap").trigger({
      type: "recapSet",
      title: groupTitle,
      group: idGroup,
      value: visu,
      price:
        parseFloat(itemPrice) > 0
          ? " : " +
            (price_percent
              ? "+" + itemPrice + "%"
              : formatCurrencyNdk(itemPrice))
          : "",
    });
  }
}

function setRecapItemRadio(idGroup, input) {
  if (typeof setRecapItemRadio_Override == "function") {
    return setRecapItemRadio_Override(idGroup, input);
  }
  if ($(input).is(":checked")) {
    visu = input.val();
    rootGroup = input.attr("data-group");
    rootGroupBlock = $(
      ".form-group[data-field='" + rootGroup + "']:not(.submitContainer)"
    );
    groupTitleEl = rootGroupBlock.find("label:eq(0)").clone();
    groupTitleEl
      .find(".toggleText, .tooltipDescription, .tooltipDescMark")
      .remove();
    groupTitle = groupTitleEl.text();
    idprice = rootGroup;
    idpriceGroup = rootGroup;
    itemPrice = groupAdded[idprice];
    groupTotalPrice = itemPrice;

    $(".recap_item_" + rootGroup).remove();
    if (
      $("#ndkcf_recap .recap_group_" + rootGroup + " .recap_group_title")
        .length < 1
    )
      $("#ndkcf_recap .recap_group_" + rootGroup).append(
        '<p class="recap_group_title">' +
          groupTitle +
          '<span class="point_separator"> : </span>' +
          (groupTotalPrice > 0
            ? '<span class="groupTotalPrice">' +
              formatCurrencyNdk(groupTotalPrice) +
              "</span>"
            : '<span class="groupTotalPrice groupTotalPriceNo">&nbsp;</span>') +
          "</p>"
      );

    recapHtml = `<div class="recap_item recap_item_${rootGroup}_${idGroup}">${visu}${getGroupDisplayMultiplicator(
      rootGroup
    )}${
      parseFloat(itemPrice) > 0
        ? " : " +
          formatCurrencyNdk(itemPrice / groupMultiply[rootGroup].quantity)
        : ""
    }</div>`;

    $(".recap_group_" + rootGroup)
      .append(recapHtml)
      .addClass("recap_filled");
    if (parseFloat(groupTotalPrice) > 0)
      $(".recap_group_" + rootGroup)
        .find(".groupTotalPrice")
        .html(formatCurrencyNdk(groupTotalPrice))
        .removeClass("groupTotalPriceNo")
        .attr("content", groupTotalPrice);
    else
      $(".recap_group_" + rootGroup)
        .find(".groupTotalPrice")
        .html("")
        .addClass("groupTotalPriceNo");
  }

  if (rootGroupBlock.attr("class").indexOf("disabled_value_by") > -1) {
    $(".recap_group_" + rootGroup).addClass("disabled_value_by");
  } else {
    $(".recap_group_" + rootGroup).removeClass("disabled_value_by");
  }
}

function setRecapItemCombTab(idGroup, input) {
  if (typeof setRecapItemCombTab_Override == "function") {
    return setRecapItemCombTab_Override(idGroup, input);
  }
  visu = input.attr("data-attr-lang");
  rootGroup = input.attr("data-group");
  rootGroupBlock = $(
    ".form-group[data-field='" + rootGroup + "']:not(.submitContainer)"
  );
  groupTitleEl = rootGroupBlock.find("label:eq(0)").clone();
  groupTitleEl
    .find(".toggleText, .tooltipDescription, .tooltipDescMark")
    .remove();
  groupTitle = groupTitleEl.text();
  idprice = rootGroup + "-" + idGroup.replace("ndk-accessory-quantity-", "");

  idPriceArr = idprice.split("-");
  if (input.hasClass("ndk-accessory-comb-tab"))
    idprice = idPriceArr[0] + "-" + idPriceArr[1] + "-" + idPriceArr[3];
  idpriceGroup = idprice.split("-");
  idpriceGroup = idpriceGroup[0];
  groupTotalPrice = getPriceGroup(idpriceGroup);

  $(".recap_item_" + rootGroup + "_" + idGroup).remove();
  if (
    $("#ndkcf_recap .recap_group_" + rootGroup + " .recap_group_title").length <
    1
  )
    $("#ndkcf_recap .recap_group_" + rootGroup).append(
      '<p class="recap_group_title">' +
        groupTitle +
        '<span class="point_separator"> : </span>' +
        (groupTotalPrice > 0
          ? '<span class="groupTotalPrice">' +
            formatCurrencyNdk(groupTotalPrice) +
            "</span>"
          : '<span class="groupTotalPrice groupTotalPriceNo">&nbsp;</span>') +
        "</p>"
    );

  itemPrice = groupAdded[idprice];
  recapHtml = `<div class="recap_item recap_comb recap_item_${rootGroup}_${idGroup}">${visu} ${
    parseFloat(itemPrice) > 0
      ? parseInt(selectedConfigValue[idGroup]) > 1
        ? "(" +
          formatCurrencyNdk(
            itemPrice / parseInt(selectedConfigValue[idGroup])
          ) +
          ")"
        : ""
      : ""
  } x${selectedConfigValue[idGroup]}${getGroupDisplayMultiplicator(rootGroup)}${
    parseFloat(itemPrice) > 0 ? " : " + formatCurrencyNdk(itemPrice) : ""
  }</div>`;

  $(".recap_group_" + rootGroup)
    .append(recapHtml)
    .addClass("recap_filled");
  if (parseFloat(groupTotalPrice) > 0)
    $(".recap_group_" + rootGroup)
      .find(".groupTotalPrice")
      .html(formatCurrencyNdk(groupTotalPrice))
      .removeClass("groupTotalPriceNo")
      .attr("content", groupTotalPrice);
  else
    $(".recap_group_" + rootGroup)
      .find(".groupTotalPrice")
      .html("")
      .addClass("groupTotalPriceNo");

  if (rootGroupBlock.attr("class").indexOf("disabled_value_by") > -1) {
    $(".recap_group_" + rootGroup).addClass("disabled_value_by");
  } else {
    $(".recap_group_" + rootGroup).removeClass("disabled_value_by");
  }
}

function setRecapItemAccessory(idGroup, input) {
  if (typeof setRecapItemAccessory_Override == "function") {
    return setRecapItemAccessory_Override(idGroup, input);
  }
  visu = input.parent().parent().parent().find("b").html();
  rootGroup = input.parent().parent().parent().attr("data-group");
  rootGroupBlock = $(
    ".form-group[data-field='" + rootGroup + "']:not(.submitContainer)"
  );
  groupTitleEl = rootGroupBlock.find("label:eq(0)").clone();
  groupTitleEl
    .find(".toggleText, .tooltipDescription, .tooltipDescMark")
    .remove();
  groupTitle = groupTitleEl.text();
  idprice = rootGroup + "-" + idGroup.replace("ndk-accessory-quantity-", "");
  idpriceGroup = idprice.split("-");
  idpriceGroup = idpriceGroup[0];
  groupTotalPrice = getPriceGroup(idpriceGroup);

  $(".recap_item_" + rootGroup + "_" + idGroup).remove();
  $("#ndkcf_recap .recap_group_" + rootGroup).html("");
  if (
    $("#ndkcf_recap .recap_group_" + rootGroup + " .recap_group_title").length <
    1
  )
    $("#ndkcf_recap .recap_group_" + rootGroup).append(
      `<p class="recap_group_title">${groupTitle}<span class="point_separator"> : </span>${
        groupTotalPrice > 0
          ? '<span class="groupTotalPrice">' +
            formatCurrencyNdk(groupTotalPrice) +
            "</span>"
          : '<span class="groupTotalPrice groupTotalPriceNo">&nbsp;</span>'
      }</p>`
    );

  itemPrice = groupAdded[idprice];
  recapHtml = `<div class="recap_item recap_item_${rootGroup}_${idGroup}">${visu} x${
    selectedConfigValue[idGroup]
  }${getGroupDisplayMultiplicator(rootGroup)}${
    parseFloat(itemPrice) > 0
      ? " : " + formatCurrencyNdk(itemPrice / groupMultiply[rootGroup].quantity)
      : ""
  }  </div>`;

  $(".recap_group_" + rootGroup)
    .append(recapHtml)
    .addClass("recap_filled");
  if (parseFloat(groupTotalPrice) > 0)
    $(".recap_group_" + rootGroup)
      .find(".groupTotalPrice")
      .html(formatCurrencyNdk(groupTotalPrice))
      .removeClass("groupTotalPriceNo")
      .attr("content", groupTotalPrice);
  else
    $(".recap_group_" + rootGroup)
      .find(".groupTotalPrice")
      .html("")
      .addClass("groupTotalPriceNo");

  if (rootGroupBlock.attr("class").indexOf("disabled_value_by") > -1) {
    $(".recap_group_" + rootGroup).addClass("disabled_value_by");
  } else {
    $(".recap_group_" + rootGroup).removeClass("disabled_value_by");
  }
}

function setRecapItemDimensions(idGroup, input) {
  if (typeof setRecapItemDimensions_Override == "function") {
    return setRecapItemDimensions_Override(idGroup, input);
  }
  //console.log(input.prev().text())
  visu = input.prev().text() + " : " + input.val();
  rootGroup = input.attr("data-group");
  rootGroupBlock = $(
    ".form-group[data-field='" + rootGroup + "']:not(.submitContainer)"
  );
  groupTitleEl = rootGroupBlock.find("label:eq(0)").clone();
  groupTitleEl
    .find(".toggleText, .tooltipDescription, .tooltipDescMark")
    .remove();
  groupTitle = groupTitleEl.text();
  idprice = rootGroup;
  idpriceGroup = idprice;
  groupTotalPrice = groupAdded[idprice];

  $(".recap_item_" + rootGroup + "_" + idGroup).remove();
  if (
    $("#ndkcf_recap .recap_group_" + rootGroup + " .recap_group_title").length <
    1
  )
    $("#ndkcf_recap .recap_group_" + rootGroup).append(
      '<p class="recap_group_title">' +
        groupTitle +
        '<span class="point_separator"> : </span>' +
        (groupTotalPrice > 0
          ? '<span class="groupTotalPrice">' +
            formatCurrencyNdk(groupTotalPrice) +
            "</span>"
          : '<span class="groupTotalPrice groupTotalPriceNo">&nbsp;</span>') +
        "</p>"
    );

  itemPrice = groupAdded[idprice];
  recapHtml = `<div class="recap_item recap_item_${rootGroup}_${idGroup}">${visu}${getGroupDisplayMultiplicator(
    rootGroup
  )}</div>`;

  $(".recap_group_" + rootGroup)
    .append(recapHtml)
    .addClass("recap_filled");
  if (parseFloat(groupTotalPrice) > 0)
    $(".recap_group_" + rootGroup)
      .find(".groupTotalPrice")
      .html(formatCurrencyNdk(groupTotalPrice))
      .removeClass("groupTotalPriceNo")
      .attr("content", groupTotalPrice);
  else
    $(".recap_group_" + rootGroup)
      .find(".groupTotalPrice")
      .html("")
      .addClass("groupTotalPriceNo");

  if (rootGroupBlock.attr("class").indexOf("disabled_value_by") > -1) {
    $(".recap_group_" + rootGroup).addClass("disabled_value_by");
  } else {
    $(".recap_group_" + rootGroup).removeClass("disabled_value_by");
  }
}

function setRecapItemSurface(idGroup, input) {
  if (typeof setRecapItemSurface_Override == "function") {
    return setRecapItemSurface_Override(idGroup, input);
  }
  visu = input.attr("placeholder") + " : " + input.val();
  rootGroup = input.attr("data-group");
  rootGroupBlock = $(
    ".form-group[data-field='" + rootGroup + "']:not(.submitContainer)"
  );
  groupTitleEl = rootGroupBlock.find("label:eq(0)").clone();
  groupTitleEl
    .find(".toggleText, .tooltipDescription, .tooltipDescMark")
    .remove();
  groupTitle = groupTitleEl.text();
  idprice = rootGroup;
  idpriceGroup = idprice;
  groupTotalPrice = groupAdded[idprice];

  $(".recap_item_" + rootGroup + "_" + idGroup).remove();
  if (
    $("#ndkcf_recap .recap_group_" + rootGroup + " .recap_group_title").length <
    1
  )
    $("#ndkcf_recap .recap_group_" + rootGroup).append(
      `<p class="recap_group_title">${groupTitle}<span class="point_separator"> : </span>${
        groupTotalPrice > 0
          ? '<span class="groupTotalPrice">' +
            formatCurrencyNdk(groupTotalPrice) +
            "</span>"
          : '<span class="groupTotalPrice groupTotalPriceNo">&nbsp;</span>'
      }</p>`
    );

  itemPrice = groupAdded[idprice];
  recapHtml = `<div class="recap_item recap_item_${rootGroup}_${idGroup}">${visu}${getGroupDisplayMultiplicator(
    rootGroup
  )}</div>`;

  $(".recap_group_" + rootGroup)
    .append(recapHtml)
    .addClass("recap_filled");
  if (parseFloat(groupTotalPrice) > 0)
    $(".recap_group_" + rootGroup)
      .find(".groupTotalPrice")
      .html(formatCurrencyNdk(groupTotalPrice))
      .removeClass("groupTotalPriceNo")
      .attr("content", groupTotalPrice);
  else
    $(".recap_group_" + rootGroup)
      .find(".groupTotalPrice")
      .html("")
      .addClass("groupTotalPriceNo");

  if (rootGroupBlock.attr("class").indexOf("disabled_value_by") > -1) {
    $(".recap_group_" + rootGroup).addClass("disabled_value_by");
  } else {
    $(".recap_group_" + rootGroup).removeClass("disabled_value_by");
  }
}

function setRecapItemcheckbox(idGroup, input) {
  if (typeof setRecapItemcheckbox_Override == "function") {
    return setRecapItemcheckbox_Override(idGroup, input);
  }
  rootGroup = input.attr("data-group");
  rootGroupBlock = $(
    ".form-group[data-field='" + rootGroup + "']:not(.submitContainer)"
  );
  if ($(input).is(":checked")) {
    visu = input.val();

    //console.log(groupAdded);

    groupTitleEl = rootGroupBlock.find("label:eq(0)").clone();
    groupTitleEl
      .find(".toggleText, .tooltipDescription, .tooltipDescMark")
      .remove();
    groupTitle = groupTitleEl.text();

    idprice = input.attr("data-value-id");
    idpriceGroup = rootGroup;
    groupTotalPrice = 0;

    group = $(input).attr("data-group");
    rootBlock = $(".ndkackFieldItem[data-field='" + group + "']");
    others = rootBlock.find('input[type="checkbox"]');
    others.each(function () {
      if (typeof groupAdded[$(this).attr("data-id-value")] != "undefined")
        groupTotalPrice += parseFloat(
          groupAdded[$(this).attr("data-id-value")]
        );
    });

    $(".recap_item_" + rootGroup + "_" + idGroup).remove();
    if (
      $("#ndkcf_recap .recap_group_" + rootGroup + " .recap_group_title")
        .length < 1
    )
      $("#ndkcf_recap .recap_group_" + rootGroup).append(
        '<p class="recap_group_title">' +
          groupTitle +
          '<span class="point_separator"> : </span>' +
          (groupTotalPrice > 0
            ? '<span class="groupTotalPrice">' +
              formatCurrencyNdk(groupTotalPrice) +
              "</span>"
            : '<span class="groupTotalPrice groupTotalPriceNo">&nbsp;</span>') +
          "</p>"
      );

    itemPrice = groupAdded[idprice];
    recapHtml = `<div class="recap_item recap_item_${rootGroup}_${idGroup}">${visu}${getGroupDisplayMultiplicator(
      rootGroup
    )}${
      parseFloat(itemPrice) > 0
        ? " : " +
          formatCurrencyNdk(itemPrice / groupMultiply[rootGroup].quantity)
        : ""
    }</div>`;

    $(".recap_group_" + rootGroup)
      .append(recapHtml)
      .addClass("recap_filled");
    if (parseFloat(groupTotalPrice) > 0)
      $(".recap_group_" + rootGroup)
        .find(".groupTotalPrice")
        .html(formatCurrencyNdk(groupTotalPrice))
        .removeClass("groupTotalPriceNo")
        .attr("content", groupTotalPrice);
    else
      $(".recap_group_" + rootGroup)
        .find(".groupTotalPrice")
        .html("")
        .addClass("groupTotalPriceNo");
  }

  if (rootGroupBlock.attr("class").indexOf("disabled_value_by") > -1) {
    $(".recap_group_" + rootGroup).addClass("disabled_value_by");
  } else {
    $(".recap_group_" + rootGroup).removeClass("disabled_value_by");
  }
}

function getGroupDisplayMultiplicator(rootGroup) {
  output = "";
  if (typeof groupMultiply[rootGroup] != "undefined") {
    if (groupMultiply[rootGroup].quantity > 1)
      output = " (x" + groupMultiply[rootGroup].quantity + ")";
  }
  return output;
}

$(document).on("blur", ".recipient-field", function () {
  setRecapRecipient();
});

function setRecapRecipient() {
  if (typeof setRecapRecipient_Override == "function") {
    return setRecapRecipient_Override();
  }
  recipientRecap = [];
  rootGroupBlock = false;
  $(".recipient-group").each(function () {
    rootGroup = $(this).attr("data-field");
    rootGroupBlock = $(
      ".form-group[data-field='" + rootGroup + "']:not(.submitContainer)"
    );
    groupTitleEl = rootGroupBlock.find("label:eq(0)").clone();
    groupTitleEl
      .find(".toggleText, .tooltipDescription, .tooltipDescMark")
      .remove();
    groupTitle = groupTitleEl.text();
    groupTotalPrice = 0;
    group = rootGroup;
    rootBlock = $(".ndkackFieldItem[data-field='" + group + "']");

    firstnameInput = rootGroupBlock.find(
      "[name='ndkcsfield[" + rootGroup + "][recipient][firstname]']"
    );
    lastnameInput = rootGroupBlock.find(
      "[name='ndkcsfield[" + rootGroup + "][recipient][lastname]']"
    );
    emailInput = rootGroupBlock.find(
      "[name='ndkcsfield[" + rootGroup + "][recipient][email]']"
    );
    messageInput = rootGroupBlock.find(
      "[name='ndkcsfield[" + rootGroup + "][recipient][message]']"
    );

    content =
      '<div class="recap_item recap_item_' +
      rootGroup +
      '">' +
      firstnameInput.parent().find("label").text() +
      " : " +
      firstnameInput.val() +
      "</div>";
    content +=
      '<div class="recap_item recap_item_' +
      rootGroup +
      '">' +
      lastnameInput.parent().find("label").text() +
      " : " +
      lastnameInput.val() +
      "</div>";
    content +=
      '<div class="recap_item recap_item_' +
      rootGroup +
      '">' +
      emailInput.parent().find("label").text() +
      " : " +
      emailInput.val() +
      "</div>";
    content +=
      '<div class="recap_item recap_item_' +
      rootGroup +
      '">' +
      messageInput.parent().find("label").text() +
      " : " +
      messageInput.val() +
      "</div>";
    $(".recap_group_" + rootGroup).remove();
    if ($("#ndkcf_recap .recap_group_" + rootGroup).length < 1)
      $("#ndkcf_recap .recap_items").append(
        '<div class="recap_group recap_group_' +
          rootGroup +
          '" data-group="' +
          rootGroup +
          '"><p class="recap_group_title">' +
          groupTitle +
          '<span class="point_separator"> : </span>' +
          (groupTotalPrice > 0
            ? '<span class="groupTotalPrice">' +
              formatCurrencyNdk(groupTotalPrice) +
              "</span>"
            : '<span class="groupTotalPrice groupTotalPriceNo">&nbsp;</span>') +
          "</p>" +
          content +
          "</div>"
      );
  });
  if (rootGroupBlock) {
    if (rootGroupBlock.attr("class").indexOf("disabled_value_by") > -1) {
      $(".recap_group_" + rootGroup).addClass("disabled_value_by");
    } else {
      $(".recap_group_" + rootGroup).removeClass("disabled_value_by");
    }
  }
}

function getPriceGroup(idpriceGroup) {
  if (typeof getPriceGroup_Override == "function") {
    return getPriceGroup_Override(idpriceGroup);
  }
  priceGroup = 0;
  for (idPrice in groupAdded) {
    if (idPrice.indexOf(idpriceGroup + "-") > -1) {
      priceGroup += groupUnitPrice[idPrice];
    } else if (idPrice == idpriceGroup) {
      if (typeof groupUnitPrice[idPrice] != "undefined")
        priceGroup = groupUnitPrice[idPrice];
    }
  }
  return priceGroup;
}

function checkQuantityLink(group) {
  group = group.toString().split("-")[0];
  multiply = 1;
  currentBlock = $(".form-group[data-field='" + group + "']");
  qlink = currentBlock.attr("data-quantity-link");
  if (typeof qlink == "undefined") qlink = 0;
  if (qlink > 0) {
    multiply = 0;
    linkedBlock = $(".form-group[data-field='" + qlink + "']");
    linkedBlock.find(".ndk-accessory-quantity").each(function () {
      multiply += $(this).val() * 1;
    });
  }

  groupMultiply[group] = { multiplicator: qlink, quantity: multiply };
  return multiply;
}

function checkQuantityLinked(group) {
  if (typeof group == "undefined") return;
  group = group.toString().split("-")[0];
  isLinked = $(".form-group[data-quantity-link='" + group + "']");
  isLinked.each(function () {
    mainBlock = $(this);
    mainBlock.find(".ndk-accessory-quantity[value!=0]").trigger("change");
    if (mainBlock.hasClass("field-type-4")) {
      mainBlock.find(".ndk-checkbox:checked").trigger("change");
    } else {
      grp = $(this).attr("data-field");
      updatePriceNdk(getPriceGroup(grp), grp);
      //updatePriceNdk($("#price_" + grp).val(), grp);
    }
  });
}
