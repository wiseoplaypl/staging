/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2020 Hendrik Masson
 *  @license   Tous droits réservés
 */
var groupAdded = [];

var groupUnitPrice = [];
var customizationId = 0;
var mesures = [];
var defaultMesures = [];
var preloadImg = [];
var filtersTags = [];
var maxQttyAvailable = [];
var htmlOutput = [];
var customizationPrice = 0;
var initialValues = [];
//var scrollbarWidth=(window.innerWidth-$(window).width());
var scrollbarWidth = 0;
var checked = false;
var allFonts = [];
var typingTimer; //timer identifier
var doneTypingInterval = 1000; //time in ms, 5 second for example
var editTimerDuration = 4000;
var alreadyModify = false;
var compoImages = [];
var selectedIdValue = [];
var ndkBrowserVersion = ndkDetectIE();
var is_popup_mode;
var ndkcfCustomFont = [];
var addFontTechnicalData = true;
// var ndkLoader =
//   '<div class="ndk-loader blasting-ripple loader" id="ndkloader"><ul role="progressbar"><li role="presentation"></li><li role="presentation"></li><li role="presentation"></li><li role="presentation"></li><li role="presentation"></li><li role="presentation"></li><li role="presentation"></li></ul></div></div>';
var ndkLoader =
  '<div class="ndk-loader" id="ndkloader"><div class="blasting-ripple loader"></div></div>';

var editAttr = null;
var ndk3dViews = [];

conf_img_url = $("#bigpic").attr("data-original-image");
if (typeof contentOnly == "undefined") contentOnly = false;

$.jMaskGlobals = {
  maskElements: "input,td,span,div",
  dataMaskAttr: "*[data-mask]",
  dataMask: true,
  watchInterval: 300,
  watchInputs: true,
  watchDataMask: false,
  byPassKeys: [9, 16, 17, 18, 36, 37, 38, 39, 40, 91],
  translation: {
    0: { pattern: /\d/ },
    9: { pattern: /\d/, optional: true },
    "#": { pattern: /\d/, recursive: true },
    A: { pattern: /[a-zà-ÿÀ-ŸA-Z0-9]/, recursive: true },
    M: { pattern: /[A-Z0-9]/, recursive: true, optional: true },
    S: { pattern: /[a-zA-Z]/ },
    s: { pattern: /[a-z]/, optional: true },
    a: { pattern: /[a-zà-ÿ'-]/, recursive: true },
    z: {
      pattern: /[a-zà-ÿÀ-ŸA-Z0-9\!"#$%&'()*+,-. /:;<=>?@[\]^_{|}~]/,
      recursive: true,
      optional: true,
      reverse: true,
    },
    W: {
      pattern: /[A-Z0-9\!"#$%&'()*+,-. /:;<=>?@[\]^_{|}~]/,
      recursive: true,
      optional: true,
      reverse: true,
    },
  },
};

function formatCurrencyNdk(price) {
  if (typeof formatCurrencyNdk_Override == "function") {
    return formatCurrencyNdk_Override(price);
  }
  if (ps_version > 1.6) {
    /*if(parseFloat(price) in formatedPrices){
			return formatedPrices[parseFloat(price)];
		}
		else{
			var response = '';
			$.ajax({
						type: "GET",
						async: false,  
						url: baseUrl+'modules/ndk_advanced_custom_fields/front_ajax.php?action=formatPrice',
						data: {price : parseFloat(price)},
						success: function(data) {
							response =  data;
							formatedPrices[parseFloat(price)] = data;
						}
			 });
			 return response;
		}*/

    return formatCurrency17(
      parseFloat(price),
      currencyFormat17,
      currencySign,
      1
    );
  } else {
    return formatCurrency(
      parseFloat(price),
      currencyFormat,
      currencySign,
      currencyBlank
    );
  }
}

function formatCurrencyNdkCallback(price, element, prefix, suffix) {
  if (typeof formatCurrencyNdkCallback_Override == "function") {
    return formatCurrencyNdkCallback_Override(price, element, prefix, suffix);
  }
  prefix = prefix || "";
  suffix = suffix || "";
  if (ps_version > 1.6) {
    /*mask = currencyFormat17.split('0');
		myMask = mask[0].replace(/#/g, '0')+currencySign;
		console.log(myMask);
		$(element).html(prefix+price+suffix).mask("0,00 €");*/
    /*if(price in formatedPrices){
			$(element).html(prefix+formatedPrices[price]+suffix);
			console.log(formatedPrices);
			return true;
		}
		else{
			$.ajax({
						type: "GET",
						url: baseUrl+'modules/ndk_advanced_custom_fields/front_ajax.php?action=formatPrice',
						data: {price : parseFloat(price)},
						success: function(data) {
							$(element).html(prefix+data+suffix);
							formatedPrices[price] = data;
						}
			 });
		}*/

    $(element).html(
      prefix +
        formatCurrency17(parseFloat(price), currencyFormat17, currencySign, 1) +
        suffix
    );
  } else {
    $(element).html(
      prefix +
        formatCurrency(
          parseFloat(price),
          currencyFormat,
          currencySign,
          currencyBlank
        ) +
        suffix
    );
  }
}

function getIdCombinationNdk(adding_to_cart) {
  if (typeof getIdCombinationNdk_Override == "function") {
    return getIdCombinationNdk_Override(adding_to_cart);
  }

  adding_to_cart = adding_to_cart || false;
  $("#add-to-cart-or-refresh")
    .find(".small_loader_container #ndkLoader")
    .fadeOut()
    .remove();
  $("#add-to-cart-or-refresh")
    .append(ndkLoader)
    .addClass("small_loader_container");
  setTimeout(function () {
    var $form = $("#add-to-cart-or-refresh");
    //myDatas = $form.serialize();
    myDatas = $(".product-variants")
      .find(
        "input:not(.false_attribute), select:not(.false_attribute), textarea:not(.false_attribute)"
      )
      .serialize();
    //console.log(myDatas)
    $.ajax({
      type: "GET",
      url:
        baseUrl +
        "modules/ndk_advanced_custom_fields/front_ajax.php?action=getCombination&id_product=" +
        $("#ndkcf_id_product").val(),
      data: myDatas,
      dataType: "json",
      success: function (data) {
        //var data = $.parseJSON(response);

        if (data != null) {
          $("#ndkcf_id_combination, #idCombination").val(
            data.id_product_attribute
          );
          if ($(".view_tab.activeView").length == 0) {
            $("#bigpic")
              .attr("src", data.images[0])
              .attr("data-original-image", data.images[0]);
          }
          $("#bigpic").css("background", "url(" + data.images[0] + ") 100%");
          $("#image-block .colorize-cover-item").each(function () {
            mask = $(this).css("mask-image");
            if (mask.indexOf("scenes") == -1) {
              $(this).attr("src", data.images[0]);
              $(this)
                .css("mask-image", "url('" + data.images[0] + "')")
                .css("-webkit-mask-image", "url('" + data.images[0] + "')");
            }
          });
          $("#popup_product_name").html(data.product_name);
          if (data.images.length > 1) {
            thumbs = "";
            container_classList =
              document.querySelector("ul.product-images").classList;
            thumbs_classList =
              document.querySelector(".thumb-container").classList;
            for (var i = 0; i < data.images.length; i++) {
              thumbs +=
                '<li class="thumb-container"><img class="thumb js-thumb ' +
                thumbs_classList.value +
                '" data-image-medium-src="' +
                data.images[i] +
                '" data-image-large-src="' +
                data.images[i] +
                '" src="' +
                data.images[i] +
                '" itemprop="image"></li>';
            }
            $("ul.product-images")
              .html(thumbs)
              .addClass(container_classList.value);
            $("ul.product-images").html(thumbs);
            if (typeof coverImage == "function") {
              coverImage();
              imageScrollBox();
            }
          } else {
            //$('ul.product-images').html('');
          }

          if (ps_version > 1.6 && !adding_to_cart) {
            var productDetails = $("#product-details").data("product");
            if (typeof productDetails != "undefined") {
              ndkcfAttrStock = data.stock;
              if (
                parseFloat(data.stock) < 1 &&
                productDetails.allow_oosp == 0 &&
                (editConfig == 0 || editAttr != data.id_product_attribute)
              ) {
                $(".ndkcsfields-block").fadeOut(600);
                setTimeout(function () {
                  $("#submitNdkcsfields, .falseButton")
                    .attr("disabled", "disabled")
                    .addClass("disabled");
                }, 2500);

                $("#product-availability").fadeIn();
              } else {
                $(".ndkcsfields-block").fadeIn(600);
                $("#submitNdkcsfields, .falseButton")
                  .removeAttr("disabled")
                  .removeClass("disabled")
                  .removeClass("loadingButton");
                $("#product-availability").fadeOut();
              }
            }
          }
          //formatCurrencyNdkCallback(data.price, ".current-price span");
          initPriceVars();
        } else {
          $("#ndkcf_id_combination").val(0);
        }
      },
    });
  }, 200);
  if (!adding_to_cart) {
    setTimeout(function () {
      $("#quantity_wanted").trigger("change");
    }, 400);
  }

  setTimeout(function () {
    $("#main").removeClass("-combinations-loading");
    $("#add-to-cart-or-refresh").find("#ndkloader").fadeOut().remove();
  }, 1000);
}

function imageScrollBox() {
  if ($("#main .js-qv-product-images li").length > 1) {
    $("#main .js-qv-mask").addClass("scroll");
    $(".scroll-box-arrows").addClass("scroll");
    $("#main .js-qv-mask").scrollbox({
      direction: "h",
      distance: 113,
      autoPlay: false,
    });
    $(".scroll-box-arrows .left").click(function () {
      $("#main .js-qv-mask").trigger("backward");
    });
    $(".scroll-box-arrows .right").click(function () {
      $("#main .js-qv-mask").trigger("forward");
    });
  } else {
    $("#main .js-qv-mask").removeClass("scroll");
    $(".scroll-box-arrows").removeClass("scroll");
  }
}

function coverImage() {
  /*if (!!$.prototype.bxSlider){
		$('.product-cover .layer').addClass('hideImportant');
		$(".js-qv-product-images").unwrap().unwrap().parent().find('.bx-controls').remove();
		if (!!$.prototype.bxSlider)
		var mySlider = $(".js-qv-product-images").bxSlider({responsive:true,useCSS:false,pager:false,slideWidth:150,slideMargin:4});
		//mySlider.destroySlider();
		//mySlider.reloadSlider();
	}*/
}

$(document).on("click", ".ndkcfLoaded .js-thumb", function (event) {
  event.preventDefault();
  if ($(window).width() >= 0) {
    $(".js-thumb.selected").removeClass("selected");
    current_img = $("#bigpic").attr("data-original-image");

    if ($(".view_tab.activeView").length > 0) {
      img = $(event.currentTarget).data("image-large-src");
      view_img = $(".view_tab.activeView").attr("data-img");
    } else {
      view_img = img = $(event.currentTarget).data("image-large-src");
    }

    $(event.target).addClass("selected");

    if (is_visual && !is_popup_mode) {
      $(".js-qv-product-cover").attr("src", view_img).attr("srcset", view_img);
    } else {
      if ($(".view_tab.activeView").length > 0)
        $(".js-qv-product-cover")
          .attr("src", current_img)
          .attr("srcset", current_img);
      else
        $(".js-qv-product-cover")
          .attr("src", current_img)
          .attr("srcset", current_img);
    }

    $(".js-modal-product-cover").attr("src", img).attr("srcset", img);
    if (!$(event.target).parents().hasClass("js-product-images-modal"))
      $("[data-target='#product-modal']").trigger("click");
    else {
      $(".js-modal-product-cover")
        .attr("src", $(event.target).attr("data-image-large-src"))
        .attr("srcset", $(event.target).attr("data-image-large-src"));
    }
    //console.log(img);
  }
});

$(document).on(
  "change, click",
  ".ndkcfLoaded .product-variants input:not(.false_attribute)",
  function () {
    setTimeout(function () {
      $.when(getIdCombinationNdk()).done(function () {
        setTimeout(function () {
          initPriceVars();
        }, 800);
      });
    }, 500);
  }
);

$(document).on(
  "change",
  ".ndkcfLoaded .product-variants select:not(.false_attribute)",
  function () {
    setTimeout(function () {
      $.when(getIdCombinationNdk()).done(function () {
        setTimeout(function () {
          initPriceVars();
        }, 800);
      });
    }, 500);
  }
);

var default_colors = [
  "AliceBlue",
  "AntiqueWhite",
  "Aqua",
  "Aquamarine",
  "Azure",
  "Beige",
  "Bisque",
  "Black",
  "BlanchedAlmond",
  "Blue",
  "BlueViolet",
  "Brown",
  "BurlyWood",
  "CadetBlue",
  "Chartreuse",
  "Chocolate",
  "Coral",
  "CornflowerBlue",
  "Cornsilk",
  "Crimson",
  "Cyan",
  "DarkBlue",
  "DarkCyan",
  "DarkGoldenRod",
  "DarkGray",
  "DarkGrey",
  "DarkGreen",
  "DarkKhaki",
  "DarkMagenta",
  "DarkOliveGreen",
  "Darkorange",
  "DarkOrchid",
  "DarkRed",
  "DarkSalmon",
  "DarkSeaGreen",
  "DarkSlateBlue",
  "DarkSlateGray",
  "DarkSlateGrey",
  "DarkTurquoise",
  "DarkViolet",
  "DeepPink",
  "DeepSkyBlue",
  "DimGray",
  "DimGrey",
  "DodgerBlue",
  "FireBrick",
  "FloralWhite",
  "ForestGreen",
  "Fuchsia",
  "Gainsboro",
  "GhostWhite",
  "Gold",
  "GoldenRod",
  "Gray",
  "Grey",
  "Green",
  "GreenYellow",
  "HoneyDew",
  "HotPink",
  "IndianRed",
  "Indigo",
  "Ivory",
  "Khaki",
  "Lavender",
  "LavenderBlush",
  "LawnGreen",
  "LemonChiffon",
  "LightBlue",
  "LightCoral",
  "LightCyan",
  "LightGoldenRodYellow",
  "LightGray",
  "LightGrey",
  "LightGreen",
  "LightPink",
  "LightSalmon",
  "LightSeaGreen",
  "LightSkyBlue",
  "LightSlateGray",
  "LightSlateGrey",
  "LightSteelBlue",
  "LightYellow",
  "Lime",
  "LimeGreen",
  "Linen",
  "Magenta",
  "Maroon",
  "MediumAquaMarine",
  "MediumBlue",
  "MediumOrchid",
  "MediumPurple",
  "MediumSeaGreen",
  "MediumSlateBlue",
  "MediumSpringGreen",
  "MediumTurquoise",
  "MediumVioletRed",
  "MidnightBlue",
  "MintCream",
  "MistyRose",
  "Moccasin",
  "NavajoWhite",
  "Navy",
  "OldLace",
  "Olive",
  "OliveDrab",
  "Orange",
  "OrangeRed",
  "Orchid",
  "PaleGoldenRod",
  "PaleGreen",
  "PaleTurquoise",
  "PaleVioletRed",
  "PapayaWhip",
  "PeachPuff",
  "Peru",
  "Pink",
  "Plum",
  "PowderBlue",
  "Purple",
  "Red",
  "RosyBrown",
  "RoyalBlue",
  "SaddleBrown",
  "Salmon",
  "SandyBrown",
  "SeaGreen",
  "SeaShell",
  "Sienna",
  "Silver",
  "SkyBlue",
  "SlateBlue",
  "SlateGray",
  "SlateGrey",
  "Snow",
  "SpringGreen",
  "SteelBlue",
  "Tan",
  "Teal",
  "Thistle",
  "Tomato",
  "Turquoise",
  "Violet",
  "Wheat",
  "White",
  "WhiteSmoke",
  "Yellow",
  "YellowGreen",
];

var default_fonts = [
  "Arial,Arial,Helvetica,sans-serif",
  "Arial Black,Arial Black,Gadget,sans-serif",
  "Comic Sans MS,Comic Sans MS,cursive",
  "Courier New,Courier New,Courier,monospace",
  "Georgia,Georgia,serif",
  "Impact,Charcoal,sans-serif",
  "Times New Roman,Times,serif",
  "Indie Flower",
  "Lobster",
  "Chewy",
  "Alpha Slab One",
  "Rock Salt",
  "Comfortaa",
  "Audiowide",
  "Yellowtail",
  "Black Ops One",
  "Frijole",
  "Press Star 2p",
  "Kranky",
  "Meddon",
  "Love Ya Like A Sister",
  "Bree Serif, serif",
];

if (typeof fonts == "undefined" || fonts.length == 0) var fonts = default_fonts;

if (typeof colors == "undefined" || colors.length == 0)
  var colors = default_colors;

function getCustomizationPrice(price, group = 0, value) {
  discount = 0;
  groupUnitPrice[group] = parseFloat(price);
  linked_multiplicator = checkQuantityLink(group);
  value = value || false;
  //console.trace(price, group);
  if (group.toString().indexOf("-") > -1) {
    groupArr = group.split("-");
    var mainGroup = groupArr[0];
    groupAdded[mainGroup] = 0;
  }
  if (linked_multiplicator == 0) linked_multiplicator = 1;

  qtty_wanted = $("#quantity_wanted").val();

  if ((linked_multiplicator > 1 || qtty_wanted > 1) && price > 0) {
    if (typeof ndkSpecificPrices[group] != "undefined")
      for (i = 0; i < ndkSpecificPrices[group].length; i++) {
        row = ndkSpecificPrices[group][i];
        selectedConfigValue = getSelectedValuesForPrice();
        if (
          row.value == selectedConfigValue[group] &&
          linked_multiplicator * qtty_wanted >= row.from_quantity
        ) {
          if (row.reduction_type == "amount") {
            mydiscount = parseFloat(row.reduction);
          } else if (row.reduction_type == "percent") {
            mydiscount = price * (row.reduction / 100);
          }
          if (mydiscount > discount) discount = mydiscount;
        }
      }
  }

  price = parseFloat(price) - parseFloat(discount);
  price = price * linked_multiplicator;
  price = price * newTaxRatio;
  groupAdded[group] = parseFloat(price);
  //console.log(group, linked_multiplicator, price);

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
        multiplicator = groupAdded[idPrice] / 100;
        totalPrice =
          parseFloat(productPrice * 1) + parseFloat(customizationPrice * 1);
        toAdd = totalPrice * multiplicator;
        if (parseFloat(toAdd) > 0)
          customizationPrice = parseFloat(customizationPrice) + toAdd;
      }
    }
  }

  return customizationPrice;
}

function updatePriceNdk(price, group, value, skipCheck = false) {
  if (typeof updatePriceNdk_Override == "function") {
    return updatePriceNdk_Override(price, group, value);
  }
  value = value || 0;
  discount = 0;

  if (document.readyState == "complete") {
    $("#price_" + group)
      .val(price)
      .trigger("keyup");
    //    console.log(group);
    //var productPrice = $.trim($('#our_price_display').text().replace(currencySign, '').replace(',', '.').replace(/\ /g, '').replace('-', ''));
    customizationPrice = 0;
    if (isNaN(price)) price = 0;

    if (
      !isNaN(price) &&
      typeof price != "undefined" &&
      typeof group != "undefined" &&
      group != "undefined"
    ) {
      customizationPrice = getCustomizationPrice(price, group);

      new_price =
        parseFloat(productPrice * 1) + parseFloat(customizationPrice * 1);
      $(".additionnal_price").remove();
      if (customizationPrice > 0)
        $(".blockPrice .contentPrice").prepend(
          '<span id="additionnal_price" data-price="' +
            customizationPrice +
            '" class="price additionnal_price"></span>'
        );
      formatCurrencyNdkCallback(
        customizationPrice,
        ".additionnal_price",
        "(" + additionnalText + "+",
        ")"
      );
      $("#quantity_wanted").trigger("keyup");

      if (templateType == 1) {
        $("#timeline").trigger("mouseover");
        setTimeout(function () {
          $("#timeline").trigger("mouseout");
        }, 5000);
      }
    }
    update_price_dynamic(0);
    checkQuantityLinked(group);
    $("body").trigger({
      type: "ndkacf:updatePriceNdk",
      group: group,
      value: value,
    });
  }
  if (!skipCheck) {
    if (value > 0) checkFieldRestrictions(value, group);
    else {
      autoHideFieldForNoValue($(".form-group[data-field=" + group + "]"));
    }
  }
}

function updatePriceNdkGeneric(price, group) {
  if (typeof updatePriceNdkGeneric_Override == "function") {
    return updatePriceNdkGeneric_Override(price, group);
  }

  customizationPrice = 0;
  var productPrice = $.trim(
    $("#our_price_display")
      .text()
      .replace(currencySign, "")
      .replace(",", ".")
      .replace(/\ /g, "")
      .replace("-", "")
  );

  var idPrice;
  for (idPrice in groupUnitPrice) {
    customizationPrice = getCustomizationPrice(
      groupUnitPrice[idPrice],
      idPrice
    );
  }
  new_price = parseFloat(productPrice * 1) + parseFloat(customizationPrice * 1);
  $(".additionnal_price").remove();
  if (customizationPrice > 0)
    $(".blockPrice .contentPrice").prepend(
      '<span id="additionnal_price" data-price="' +
        customizationPrice +
        '" class="price additionnal_price"></span>'
    );
  formatCurrencyNdkCallback(
    customizationPrice,
    ".additionnal_price",
    "(" + additionnalText + "+",
    ")"
  );

  if (templateType == 1) {
    $("#timeline").trigger("mouseover");
    setTimeout(function () {
      $("#timeline").trigger("mouseout");
    }, 5000);
  }
}

function updateQuantityForValue(quantity, group) {
  if (typeof updateQuantityForValue_Override == "function") {
    return updateQuantityForValue_Override(quantity, group);
  }

  var maxQtty = 999999999999999;
  var customizationPrice = 0;
  if (
    typeof quantity != "undefined" &&
    typeof group != "undefined" &&
    group != "undefined"
  ) {
    //current_price = parseFloat($('#our_price_display').text().replace(currencySign, ''));
    if (quantity == "null") quantity = 999999999999999;

    maxQttyAvailable[group] = parseFloat(quantity);
  }

  var idQtty;
  for (idQtty in groupAdded) {
    if (
      typeof maxQttyAvailable[idQtty] != "undefined" &&
      parseFloat(maxQttyAvailable[idQtty]) <= parseFloat(maxQtty)
    )
      maxQtty = parseFloat(maxQttyAvailable[idQtty]);
  }

  $("#quantity_wanted").attr("max", quantity).trigger("keyup");
}

function resizeZones_old(el) {
  if (typeof resizeZones_Override == "function") {
    return resizeZones_Override(el);
  }
  lbkWidth = el.attr("original-width");
  lbkHeight = el.attr("original-height");

  newWidth = el.attr("data-zone-width");
  newHeight = el.attr("data-zone-height");
  el.width((newWidth / lbkWidth) * 100 + "%");
  el.height((newHeight / lbkHeight) * 100 + "%");

  newMarginL = el.attr("data-zone-left");
  newMarginT = el.attr("data-zone-top");

  el.css("left", (newMarginL / lbkWidth) * 100 + "%");
  el.css("top", (newMarginT / lbkHeight) * 100 + "%");
}

function resizeZones(el) {
  if (typeof resizeZones_Override == "function") {
    return resizeZones_Override(el);
  }
  zone_mode = el.attr("zone-mode");
  if (zone_mode != "percent") {
    return resizeZones_old(el);
  }

  newWidth = el.attr("data-zone-width");
  newHeight = el.attr("data-zone-height");
  el.width(newWidth + "%");
  el.height(newHeight + "%");
  newMarginL = el.attr("data-zone-left");
  newMarginT = el.attr("data-zone-top");

  el.css("left", newMarginL + "%");
  el.css("top", newMarginT + "%");
}

function redesignPage() {
  if (typeof redesignPage_Override == "function") {
    return redesignPage_Override();
  }

  if (ps_version > 1.6) {
    if (typeof contentOnly == "undefined") contentOnly = false;
    $("#content").parent().addClass("pb-left-column");
    $(".product-price:eq(0)").parent().addClass("pb-center-column");
  }

  //setTags();
  $(
    "#add_to_cart, .product-add-to-cart > *:not(.product-quantity), .add button, .add-to-cart:not(.falseButton), .product-customization"
  ).hide();
  //$("[data-target='#product-modal']").remove();
  $("h1").prependTo(".pb-left-column");
  $("#short_description_block").appendTo(".pb-left-column");
  $(".pb-left-column").removeClass("col-md-5").addClass("col-md-7");
  $(".pb-center-column").removeClass("col-sm-4").addClass("col-sm-5");
  $(".pb-right-column")
    .removeClass("col-sm-4")
    .removeClass("col-md-3")
    .removeClass("col-xs-12")
    .appendTo(".pb-left-column")
    .addClass("clearfix")
    .wrap('<div id="timeline" class="floatingBarre"></div>');

  if (typeof isFieldsPack != "undefined" && isFieldsPack == 1) {
    $(".replace-img-block").each(function () {
      viewsTabs = $(this).clone();
      if (viewsTabs.find(".view_tab").length < 2) viewsTabs.hide();
      //$(this).remove();
      $(this).parent().prepend(viewsTabs);
      $(this).remove();
    });
  } else {
    viewsTabs = $(".replace-img-block").clone().addClass("clonedTabs");
    $(".replace-img-block").remove();
    if (viewsTabs.find(".view_tab").length < 2) viewsTabs.hide();
    $("#image-block").before(viewsTabs);
  }

  $("#image-block").attr("data-view", 0);
  setTimeout(function () {
    $(".view_tab:first").trigger("click");
  }, 500);

  if ($(window).width() > 767) {
    $(".pb-right-column").addClass("clearfix");
    $(".box-info-product > div")
      .removeClass("clearfix")
      .addClass("col-md-4 col-sm-3 col-xs-6");
  }

  if ($(".ndkmask").length > 0) {
    var masks = $(".ndkmask");
    masks.each(function () {
      if ($(this).attr("data-zindex") > 0) zindex = $(this).attr("data-zindex");
      else zindex = "99";
      $("#image-block").append(
        `<img style="z-index:${zindex};" src="${$(this).attr(
          "data-src"
        )}" id="view-${$(this).attr("data-view")}" data-field="${$(this).attr(
          "data-field"
        )}" class="absolute-visu absolute-mask view-${$(this).attr(
          "data-view"
        )}"/>`
      );
      $("#image-block")
        .find(
          ":not(img, #view_full_size, #product-zoom, .fontSelect, canvas, .ui-rotatable-handle, .zone_limit)"
        )
        .hide();
    });
  }

  if ($(".zone_limit").length > 0) {
    var zones = $(".zone_limit");
    zones.each(function () {
      resizeZones($(this));
      cloned = $(this).clone();
      $("#image-block").append(cloned);
      $(this).remove();
    });
  }

  $(".colorize_svg").each(function () {
    myGroup = $(this).attr("data-group");
    myUl = $(this);
    if (window["fieldColors_" + myGroup].length > 0)
      myColors = window["fieldColors_" + myGroup];
    else myColors = colors;

    for (var i = 0; i < myColors.length; i++) {
      var item = $(
        `<li ${
          i == 0 ? 'class="initial_color"' : ""
        }><span data-toggle="tooltip" title="${getNdkCfColorName(
          myColors[i]
        )}" data-color="${myColors[i]}" style="background:${myColors[i]};">${
          myColors[i]
        }</span></li>`
      ).appendTo(myUl);
    }
  });

  setTimeout(function () {
    $(".initial_color").trigger("click");
  }, 500);

  $(".ndk_selector").each(function () {
    $(this).setNdkSelector();
  });

  $("#submitNdkcsfields").attr("disabled", false);

  /*$.when(restoreDesign()).done(function(){
		setTimeout(function(){
			$('.clonedTabs .view_tab:first').trigger('click');
		}, 2000);
	});*/
  //restoreForm();
  $(".tagify").tagify({ delimiters: [13, 188, 44], addTagPrompt: tagslabel });
  //$('#thumbs_list_frame li a, #views_block a').removeClass('fancybox');
  $(document).on("mouseover", "#views_block li a", function (e) {
    e.preventDefault();
  });

  /*$(document).on('click', 'li:visible .fancybox, .fancybox.shown', function(e){
		e.preventDefault();
	});*/

  var resumeBlock = new HoverWatcher("#timeline");
  $("#timeline").prepend("<h3>" + timelineText + "</h3>");
  setTimeout(function () {
    $(".pb-right-column").hide("slow");
  }, 4000);

  $("#timeline").hover(
    function () {
      $(".pb-right-column").stop(true, true).show(450);
      $(this).removeClass("active");
    },
    function () {
      setTimeout(function () {
        if (!resumeBlock.isHoveringOver())
          $(".pb-right-column").stop(true, true).hide(450);
        $(this).addClass("active");
      }, 200);
    }
  );
  $("#image-block").after(
    '<div id="layer-block" class="clearfix clear"></div>'
  );

  if (typeof is_visual != "undefined" && is_visual == true) {
    $("#views_block .shown").removeClass("shown");

    $(document).on("mouseover", "#views_block li a", function (e) {
      e.preventDefault();
      $(".view_tab.activeView").trigger("click");
      $("#views_block .shown").removeClass("shown");
    });
  }
}

function HoverWatcher(selector) {
  if (typeof HoverWatcher_Override == "function") {
    return HoverWatcher_Override(selector);
  }
  this.hovering = false;
  var self = this;

  this.isHoveringOver = function () {
    return self.hovering;
  };

  $(selector).hover(
    function () {
      self.hovering = true;
    },
    function () {
      self.hovering = false;
    }
  );
}

function restoreDesign() {
  if (typeof restoreDesign_Override == "function") {
    return restoreDesign_Override();
  }
  savedDesign = localStorage.getItem(
    "customNdk_" + $("#ndkcsfields-block").attr("data-key")
  );
  //console.log(savedDesign);
  if (
    savedDesign != null &&
    typeof savedDesign != "undefined" &&
    savedDesign != ""
  ) {
    setTimeout(function () {
      $("#image-block").html(savedDesign);
      $(".ui-draggable").draggable().rotatable({ wheelRotate: false });
      $(".ui-resizable > img").resizable();
    }, 3000);
  }
}

function restoreForm() {
  if (typeof restoreForm_Override == "function") {
    return restoreForm_Override();
  }
  savedForm = localStorage.getItem(
    "customNdkForm_" + $("#ndkcsfields-block").attr("data-key")
  );
  if (savedForm != null && typeof savedForm != "undefined" && savedForm != "") {
    setTimeout(function () {
      $("#ndkcsfields-block").html(savedForm);
    }, 3000);
  }
}
function redesignViewTabs() {
  if (typeof redesignViewTabs_Override == "function") {
    return redesignViewTabs_Override();
  }
  if (typeof isFieldsPack != "undefined" && isFieldsPack == 1) {
    $(".replace-img-block").each(function () {
      viewsTabs = $(this).clone();
      //$(this).remove();
      if (viewsTabs.find(".view_tab").length < 2) viewsTabs.hide();
      $(this).parent().prepend(viewsTabs);
      $(this).remove();
    });
  } else {
    viewsTabs = $(".replace-img-block").clone().addClass("clonedTabs");
    if (viewsTabs.find(".view_tab").length < 2) viewsTabs.hide();

    $(".replace-img-block").remove();
    $("#image-block").before(viewsTabs);
  }

  $("#image-block").attr("data-view", 0);
  setTimeout(function () {
    $(".view_tab:first").trigger("click");
  }, 500);
}
function redesignPageLight() {
  if (typeof redesignPageLight_Override == "function") {
    return redesignPageLight_Override();
  }
  $(
    "#add_to_cart, product-add-to-cart > *:not(.product-quantity), .add button:not(.falseButton), .add-to-cart:not(.falseButton), .product-customization"
  ).hide();
  //$("[data-target='#product-modal']").remove();
  //viewsTabs = $('.replace-img-block').clone();
  redesignViewTabs();

  if ($(".ndkmask").length > 0) {
    var masks = $(".ndkmask");
    masks.each(function () {
      if ($(this).attr("data-zindex") > 0) zindex = $(this).attr("data-zindex");
      else zindex = "99";
      $("#image-block").append(
        `<img style="z-index:${zindex};" src="${$(this).attr(
          "data-src"
        )}" id="view-${$(this).attr("data-view")}" data-field="${$(this).attr(
          "data-field"
        )}" class="absolute-visu absolute-mask view-${$(this).attr(
          "data-view"
        )}"/>`
      );
      $("#image-block")
        .find(
          ":not(img, #view_full_size, #product-zoom, .fontSelect, canvas, .ui-rotatable-handle, .zone_limit)"
        )
        .hide();
      $("#image-block")
        .find(
          ":not(img, #view_full_size, #product-zoom, .fontSelect, canvas, .ui-rotatable-handle, .zone_limit)"
        )
        .hide();
    });
  }

  if ($(".zone_limit").length > 0) {
    var zones = $(".zone_limit");
    zones.each(function () {
      resizeZones($(this));
      cloned = $(this).clone();
      $("#image-block").append(cloned);
      $(this).remove();
    });
  }
  //setTags();

  $(".colorize_svg").each(function () {
    myGroup = $(this).attr("data-group");
    myUl = $(this);
    if (window["fieldColors_" + myGroup].length > 0)
      myColors = window["fieldColors_" + myGroup];
    else myColors = colors;

    for (var i = 0; i < myColors.length; i++) {
      var item = $(
        `<li ${
          i == 0 ? 'class="initial_color"' : ""
        }><span data-toggle="tooltip" title="${getNdkCfColorName(
          myColors[i]
        )}" data-color="${myColors[i]}" style="background:${myColors[i]};">${
          myColors[i]
        }</span></li>`
      ).appendTo(myUl);
    }
  });

  setTimeout(function () {
    $(".initial_color").trigger("click");
  }, 500);

  $(".ndk_selector").each(function () {
    $(this).setNdkSelector();
  });

  $("#submitNdkcsfields").attr("disabled", false);
  $(".tagify").tagify({ delimiters: [13, 188, 44], addTagPrompt: tagslabel });
  //$('#thumbs_list_frame li a, #views_block a').removeClass('fancybox');

  if (typeof is_visual != "undefined" && is_visual == true) {
    $("#views_block .shown").removeClass("shown");

    $(document).on("mouseover", "#views_block li a", function (e) {
      e.preventDefault();
      $(".view_tab.activeView").trigger("click");
      $("#views_block .shown").removeClass("shown");
    });

    $("#image-block").after(
      '<div id="layer-block" class="clearfix clear"></div>'
    );
  }
  /*$(document).on('click', 'li:visible .fancybox, .fancybox.shown', function(e){
					e.preventDefault();
					$('.view_tab.activeView').trigger('click');
				});*/
}

function redesignPageLight_bak() {
  $("#add_to_cart, .product-add-to-cart, .add, .add-to-cart").hide();
  if ($(".ndkmask").length > 0) {
    $("#image-block").append(
      `<img style="z-index:${zindex};" src="${$(this).attr(
        "data-src"
      )}" id="view-${$(this).attr("data-view")}" data-field="${$(this).attr(
        "data-field"
      )}" class="absolute-visu absolute-mask view-${$(this).attr(
        "data-view"
      )}"/>`
    );
    $("#image-block")
      .find(
        ":not(img, #view_full_size,  #product-zoom, .fontSelect, canvas, .ui-rotatable-handle)"
      )
      .hide();
  }
  if ($(".zone_limit").length > 0) {
    var zones = $(".zone_limit");
    zones.each(function () {
      setTimeout(function () {
        resizeZones($(this));
        cloned = $(this).clone();
        $("#image-block").append(cloned);
        $(this).remove();
      }, 1000);
    });
  }
  viewsTabs = $(".replace-img-block").clone();
  $(".replace-img-block").remove();
  $("#image-block").before(viewsTabs);
  $("#image-block").attr("data-view", 0);
  setTimeout(function () {
    $(".clonedTabs .view_tab:first").trigger("click");
  }, 500);
}

function getMultiFieldDetails(group) {
  if (group.indexOf("-") > -1) {
    group = group.split("-");
    var group = group[0];
  }
  totalBlock = "";
  $("#main-" + group)
    .find(".designer-item-container")
    .each(function () {
      me = $(this);
      subTitle = me.find(".itemToggler .item-name").text();
      totalBlock += subTitle + "\n";
      me.find(".group-title").each(function () {
        totalBlock += $(this).text() + "\n";
      });
      totalBlock += "\n";
    });
  if (totalBlock != "")
    $("#ndkcsfield_" + group)
      .val(totalBlock)
      .trigger("keyup");
}

function setImgValue(img, id) {
  if (typeof setImgValue_Override == "function") {
    return setImgValue_Override(img, id);
  }
  $("#ndkcsfield_" + id)
    .val(img.attr("data-value"))
    .trigger("keyup");
  $(img)
    .parent()
    .parent()
    .find(".selected-value")
    .removeClass("selected-value");
  $(img)
    .parent()
    .parent()
    .parent()
    .find(".svg-container")
    .removeClass("selected-svg");
  $(".img-value-" + id).removeClass("selected-value");
  $(img).addClass("selected-value");
  if (img.parent().hasClass("svg-container"))
    img.parent().addClass("selected-svg");

  checkFieldRestrictions(img.attr("data-id-value"), img.attr("data-group"));
  setTimeout(function () {
    getMultiFieldDetails(img.attr("data-group"));
  }, 800);
  img.trigger("imgValueSet");
  $("body").trigger({
    type: "ndkacf:ndkImageSet",
    group: id,
    value: img.attr("data-id-value"),
    tax_ratio: img.attr("data-tax_ratio"),
    force_tax_rule: img.attr("data-force_tax_rule"),
    force_carrier: img.attr("data-force_carrier"),
  });
}

function calculateSurface(group, mesures, valuePrice) {
  if (typeof calculateSurface_Override == "function") {
    return calculateSurface_Override(group, mesures, valuePrice);
  }
  result = 1;
  //console.log(mesures);
  for (var i = 0; i < mesures.length; i++) {
    if (typeof mesures[i] != "undefined")
      result = parseFloat(parseFloat(result) * parseFloat(mesures[i]));
  }

  if (parseFloat(result) > 0) {
    $("#resultValue_" + group).html(parseFloat(result).toFixed(2));
    $(".resultValue_" + group).show();
    price = parseFloat(valuePrice * result);
  } else {
    $("#resultValue_" + group).html();
    $(".resultValue_" + group).hide();
    price = 0;
  }
  updatePriceNdk(price, group);
}

$(function () {
  if ($("#ndkcsfields-block").length < 1 || !isFields) {
    $(".ndkcsfields-block.config_boxes").remove();
    return;
  }

  $("section.product-customization").hide();
  $("body").addClass("ndkcfLoaded");
  $("body").addClass("is_customizable_product_ndk");

  $("body").append(ndkLoader);
});

function setNdkImgTooltip() {
  $(".img-item-row").each(function () {
    var me = $(this);
    var zoom_img = me.find(".img-value:eq(0)").attr("data-src");
    //me.attr('title', 'wait...');
    if (zoom_img != "") {
      html_img = '<img class="img-responsive" src="' + zoom_img + '"/>';
      me.tooltip({
        html: true,
        content: html_img,
        placement: "top",
      });
    }
  });
  $('[data-toggle="tooltip"]').tooltip();
}

function setTooltipsDesc() {
  $(".tooltipDescMark").each(function () {
    $(this).attr("title", "wait...");
    text = $(this).parent().find(".tooltipDescription").html();
    $(this).tooltip({
      html: true,
      content: text,
      placement: "auto",
    });
  });
}

initApplication = function () {
  if (isFields != 1 || $("#ndkcsfields-block").length < 1) return;
  if (is_popup_mode) {
    $("body").addClass("ndkcf_popup_mode");
    $("#ndkcf_recap").appendTo("#ndkacf-modal .header-pc");
    if (ps_version == 1.6) {
      $(".pb-left-column #bigpic").attr("id", "bigpicbckp");
    }
  }
  if (typeof initApplication_Override == "function") {
    return initApplication_Override();
  }
  $("section.product-customization").hide();
  $("body").addClass("ndkcfLoaded");
  //$('.product-refresh').remove();
  $("body").addClass("is_customizable_product_ndk");

  identifyResctictives();
  $("#submitNdkcsfields")
    .unbind("click")
    .click(function (event) {
      $("#ndkcsfields").ndkSubmit(event);
    });

  //console.log(editConfig)
  if (editConfig == 0) {
    emptyFormNdk($("#ndkcsfields"));
    if ($(".default_config").length > 0) {
      editConfig = $(".default_config:eq(0) .ndkLoadConfig").attr("data-id");
    }
  }

  if (typeof oldRef != "undefined") {
    editAttr = oldRef.split("-")[2];
    //console.log(editAttr);
  }

  if (parseFloat($("#quantity_wanted").val() == 0))
    $("#quantity_wanted").val("1");

  if (makeSlide == 1) {
    makeGroupFieldsSlide();
  }

  if (ps_version < 1.7 && $("#image-block").length > 0 && is_popup_mode) {
    $("#image-block").attr("id", "").addClass("image-block");
  }

  if (ps_version > 1.6 && $("#image-block").length == 0) {
    if ($(".images-container .slick-current").length > 0) {
      $(".images-container .slick-current:eq(0) > div").attr(
        "id",
        "image-block"
      );
      $(".images-container .slick-current:eq(0)")
        .find("img")
        .attr("id", "bigpic");
    } else {
      if ($(".product-cover").length > 0)
        $(".product-cover:eq(0)").attr("id", "image-block");
      if ($(".product-cover:eq(0) img[itemprop='image']").length > 0)
        $(".product-cover:eq(0) img[itemprop='image']").attr("id", "bigpic");
      else {
        $(".product-cover:eq(0) img.js-qv-product-cover").attr("id", "bigpic");
      }
    }

    if (is_visual && !is_popup_mode) {
      $(".images-container")
        .removeClass("images-container")
        .removeClass("js-images-container");
    }
  }
  $("#bigpic").attr("data-original-image", $("#bigpic").attr("src"));
  $("#bigpic").css(
    "background",
    "url(" + $("#bigpic").attr("data-original-image") + ") 100%"
  );
  if (typeof templateType == "undefined") templateType = 0;

  $(".colorize-ndk").each(function () {
    $(this).attr("data-src", $("#bigpic").attr("src"));
  });

  /*$(document).on('mouseover', '#image-block', function(){
		$(document).bind('mousewheel DOMMouseScroll',function(){ 
			stopWheel(window.event); 
		});
	}, function() {
		$(document).unbind('mousewheel DOMMouseScroll');
	});*/

  registerInitialValues();

  //console.log(initialValues);
  if (typeof contentOnly != "undefined" && !contentOnly) {
    if (templateType == 99) redesignPage();
    else redesignPageLight();
  } else {
    redesignPageLight();
  }
  //console.log(makeItFloat);

  $(".ndk_attribute_select").each(function () {
    $(this).trigger("change");
  });

  $(".ndkcf_totalprod_quantity").val(0);
  /*$('.ndk-accessory-quantity').each(function(){
		$(this).val($(this).attr('data-default-value'));
		if(parseFloat($(this).attr('data-default-value')) > 0)
		$(this).trigger('keyup')
	});*/

  $("input.surface").each(function () {
    if ($(this).attr("min") > 0) $(this).val($(this).attr("min"));

    $(this).trigger("change");
  });

  setTimeout(function () {
    if (editConfig > 0) {
      if ($("#configItem_" + editConfig + " .ndkLoadConfig").length > 0)
        $("#configItem_" + editConfig + " .ndkLoadConfig:eq(0)").trigger(
          "click"
        );
      else loadCustomization(editConfig);

      $("#configItem_" + editConfig).addClass("active");
    }

    if (editConfig == 0) applyDefaultValuesNdk();
  }, 50); //test timeout

  if (typeof is_visual != "undefined" && is_visual == true) {
    $("body").addClass("ndkCfIsVisual");
    if (!is_popup_mode) {
      $("#image-block")
        .parent()
        .append('<button href="#" class="ndkzoom">&nbsp;</button>');
    }
    $('.expander[data-toggle="modal"]').hide();

    //$('#image-block *[data-toggle = modal]').remove();
  }

  //$('#image-block').append('<canvas id="maskcanvas"></canvas>');
  $(".textzone").trigger("click");

  if ($(".textzone").length > 0)
    $(".textzone:not(.dontInit)").each(function () {
      initText($(this));
    });

  if ($(".simpleText").length > 0)
    $(".simpleText").each(function () {
      initTextLight($(this));
    });

  if (typeof contentOnly != "undefined" && !contentOnly) {
    if (!!$.prototype.fancybox)
      $(".fancybox").fancybox({
        hideOnContentClick: true,
        openEffect: "elastic",
        closeEffect: "elastic",
      });
  }

  $("#customizationForm").parent().hide();

  $(".datepicker").each(function () {
    me = $(this);
    dateFormat = "yy-mm-dd";
    if (typeof me.attr("data-pattern") != "undefined")
      dateFormat = me.attr("data-pattern");
    me.datepicker({
      prevText: "",
      nextText: "",
      dateFormat: dateFormat,
    });
  });

  if (letOpen == 0) {
    setTimeout(function () {
      $(".toggler").each(function () {
        $(this).removeClass("active");
        if (parseInt(letOpen) == 0) $(this).parent().find(".fieldPane").hide();
      });
    }, 50); //test timeout
  } else {
    $(".toggler").addClass("letOpen");
  }

  setTimeout(function () {
    $(".toggleGroupField").trigger("click");
    $(".userPanel").hide();
  }, 50); //test timeout

  $(".img-value:not(.ndk-lazy)").each(function () {
    if (typeof $(this).attr("data-thumb") != "undefined") {
      preloadImg.push($(this).attr("data-thumb"));
      $(this).attr("src", $(this).attr("data-thumb"));
    }
  });

  $.when(preload(preloadImg)).done(function () {
    setTimeout(function () {
      //equalheightNdkcf(".img-item-row");
      resizeMasonryGallery();
    }, 50); //test timeout
  });

  loadImgSvg();

  $("img.jpg").each(function () {
    var $img = $(this);
    $img.parent().find(".svg-container").html($img);
    //$img.hide();
  });

  if (!!$.prototype.fancybox)
    $(".accessory-more").fancybox({
      autoScale: true,
      minHeight: 30,
      showCloseButton: true,
      autoDimensions: false,
    });

  $(".toggleAccessoriesCutomization").fancybox({
    autoScale: true,
    minHeight: 300,
    maxWidth: "80%",
    showCloseButton: true,
    autoDimensions: false,
    afterShow: function () {
      reSetEditableFields();
    },
  });

  $("#submitNdkcsfields").attr("disabled", false);

  $("#layer_block").hide();

  setRecommends();

  $("label.toggler").each(function () {
    if (parseInt(letOpen) == 0)
      $(this).append('<span class="toggleText">' + toggleOpenText + "</span>");
  });

  setTooltipsDesc();

  setValueProdImage();

  if (showImgTooltips == 1) setNdkImgTooltip();

  if (showQuicknav == 1) setQuickNav();

  $(".product-variants select:not(.false_attribute)").trigger("change");
  $(".product-variants input:not(.false_attribute)").trigger("keyup");
  setScenario();
  coverImage();
  getIdCombinationNdk();

  equalheightNdkcf(".accessory-ndk .accessory-infos");
  if ($("#quantity_wanted").val() < 1)
    $("#quantity_wanted").val(1).trigger("change");

  $("#submitNdkcsfields").prop("disabled", false).removeClass("loadingButton");
  $(".falseButton").prop("disabled", false).removeClass("loadingButton");

  $(".ndk-colorpicker").mColorPicker();
  setDemoAccessoryBlock();
  $(".image-url").val("");
  $(".view_tab.glb").each(function () {
    ndk3dViews.push(
      $(this).ndk3DView({
        identifier: $(this).attr("data-view"),
        file3d: $(this).attr("data-img"),
      })
    );
  });
};

$(document).on("colorpicked", ".ndk-colorpicker", function () {
  clearTimeout(typingTimer);
  me = $(this);
  typingTimer = setTimeout(function () {
    color = me.val();
    if (color == "") color = "#ffffff";
    me.parent()
      .find(".color-ndk")
      .attr("data-value", color)
      .attr("data-color", color)
      .attr("data-src", 0)
      .trigger("click");
  }, doneTypingInterval);
});

$(document).on("blur", ".ndk-colorpicker", function () {
  $(this).trigger("colorpicked");
});

document.onreadystatechange = function () {
  if (document.readyState === "complete") {
    $("#ndkloader").remove();
  }
  setTimeout(function () {
    $("#ndkloader").remove();
  }, 3000);
};

//document ready
$(document).ready(function () {
  //var isFields = $('#ndkcsfields').length > 0;
  var isFields = $(".ndkcsfields-block").length > 0;
  //console.log(isFields)
  if (isFields) {
    //$('.product-refresh').remove();
    if (is_visual || $(".view_tab").length > 0) {
      if (!is_popup_mode)
        $(".images-container")
          .removeClass("images-container")
          .removeClass("js-images-container");
    }
    setTimeout(function () {
      $.when(initApplication()).then(function () {
        setTimeout(function () {
          //$('#ndkloader').remove();
          $(".resetZones").trigger("click");
          //$('#image-block').find('[data-target="#product-modal"]').remove();
          if (ps_version > 1.6)
            $("#content").parent().addClass("pb-left-column");

          if (makeItFloat > 0) {
            if (ps_version > 1.6) {
              if (typeof contentOnly == "undefined") contentOnly = false;
              $("#content").parent().addClass("pb-left-column");
              makeFloat(".pb-left-column:eq(0)");
              makeFloat(".pb-right-column", "#ndkcsfields");
            } else {
              makeFloat(".pb-left-column");
              makeFloat(".pb-right-column", "#ndkcsfields");
            }
          }
        }, 50); //test timeout

        setTimeout(function () {
          setTags();
        }, 500);
      });
    }, 150); //test timeout
  }
  setTimeout(function () {
    setOpenedStatus();
  }, 800);
  setTimeout(function () {
    $("#main").removeClass("-combinations-loading");
  }, 2000);
});

$(document).on("keyup", ".noborderSimple", function () {
  rootInput = $(this).parent().parent().find(".simpleText");
  group = rootInput.attr("data-group");
  price = rootInput.attr("data-price");
  ppcprice = rootInput.attr("data-ppcprice");
  el = $(this);
  texte = "";
  charsCount = 0;
  clearTimeout(typingTimer);
  inputNumber = el.parent().find(".noborderSimple").length;
  if (inputNumber > 1) separator = "\n";
  else separator = "";

  typingTimer = setTimeout(function () {
    el.parent()
      .find(".noborderSimple")
      .each(function () {
        if ($(this).val() != "") {
          texte += $(this).val() + separator;
          charsCount += $(this)
            .val()
            .replace(/\ |\n|\r|(\n\r)/g, "").length;
        }
      });

    if (texte == "" || texte == " ") {
      price = 0;
      texte = "";
    } else if (ppcprice > 0) {
      price = ppcprice * charsCount;
    } else {
      price = rootInput.attr("data-price");
    }

    if (texte == "" || texte == " ") {
      price = 0;
      texte = "";
    }
    rootInput.val(texte);
    applyTextValueForPrice(rootInput);
    //console.log(texte)
    //updatePriceNdk(price, group);
  }, doneTypingInterval);
});

$(document).on("keyup", ".simpleText", function () {
  clearTimeout(typingTimer);
  input = $(this);
  typingTimer = setTimeout(function () {
    applyTextValueForPrice(input);
  }, doneTypingInterval);
});

$(document).on("focusout", ".simpleText", function () {
  applyTextValueForPrice($(this));
});

function applyTextValueForPrice(input) {
  val = input.val();
  input.attr("data-val", val);
  group = input.attr("data-group");
  unitprice = input.attr("data-price");
  ppcprice = input.attr("data-ppcprice");
  if (ppcprice > 0) {
    charcount = val.replace(/\ |\n|\r|(\n\r)/g, "").length;
    price = ppcprice * charcount;
  } else {
    price = unitprice;
  }
  if (val == "") price = 0;
  updatePriceNdk(price, group);
}

/*$(document).on('keyup, change', '.surface', function(){
		group = $(this).attr('data-group');
		valuePrice = $(this).attr('data-price');
		//on garde le ratio
		if($(this).attr('data-preserve-ratio') == 1){
		
		}
		
		$('.surface_'+group).each(function(){
			$i = $(this).attr('data-val');
			mesures[$i] = parseFloat( $(this).val() );
		});
		
		calculateSurface(group, mesures, valuePrice);
	});*/

$(document).on("keyup, change", ".surface", function () {
  group = $(this).attr("data-group");
  valuePrice = $(this).attr("data-price");
  if (parseFloat($(this).attr("min")) > 0) {
    if (parseFloat($(this).val()) < parseFloat($(this).attr("min")))
      $(this).val($(this).attr("min"));
  }
  if (parseFloat($(this).attr("max")) > 0) {
    if (parseFloat($(this).val()) > parseFloat($(this).attr("max")))
      $(this).val($(this).attr("max"));
  }

  $(".surface_" + group).each(function () {
    $i = $(this).attr("data-val");
    mesures[$i] = parseFloat($(this).val());
    defaultMesures[$i] = parseFloat($(this).attr("min"));
  });
  //on garde le ratio
  if ($(this).attr("data-preserve-ratio") == 1) {
    defaultMesures.sort();
    ratio = defaultMesures[1] / defaultMesures[0];
    el = $(this);
    $(".surface_" + group)
      .not(this)
      .each(function () {
        if ($(this).attr("min") == defaultMesures[1]) newVal = el.val() * ratio;
        else newVal = el.val() / ratio;
        if ($(this).val() != newVal.toFixed(2))
          $(this).val(newVal.toFixed(2)).trigger("change");
      });
  }

  calculateSurface(group, mesures, valuePrice);
});

$(document).on("click", ".toggleGroupField", function () {
  if ($(this).hasClass("opened")) {
    $(".groupFieldBlock:visible").hide();
    $(this).removeClass("opened").addClass("closed");
    $("#bigpic").attr("src", $("#bigpic").attr("data-original-image"));
    $(".zone_limit, .absolute-visu, .editThisLayer").hide();
  } else {
    target = $(this).attr("target");
    $(".toggleGroupField").removeClass("opened").addClass("closed");
    if ($(target).is(":visible") && !$(target).hasClass("groupFieldBlock")) {
      $(this).removeClass("opened").addClass("closed");
      $(target).hide();
    } else {
      $(this).removeClass("closed").addClass("opened");
      $(target).show();
      $(target + " .view_tab:first").trigger("click");
    }
    $(".groupFieldBlock:visible:not(" + target + ")").hide();
    $(target).find(".ndkcfPagerItem:eq(0)").trigger("click");
    $(".img-item-row, .svg-container").css("height", "");
    setTimeout(function () {
      //equalheightNdkcf(".img-item-row");
      //equalheightNdkcf(".svg-container");
      resizeMasonryGallery();
    }, 500);
  }
});

$(document).on("submit", ".ajax_form", function (event) {
  event.preventDefault();
  $("#submitNdkcsfields").prop("disabled", true).addClass("loadingButton");
  $(".falseButton").prop("disabled", true).addClass("loadingButton");
  //convertPercent();
  resetZone();
  if (
    typeof is_visual != "undefined" &&
    is_visual == true &&
    showHdPreview == 1
  )
    loading_steps = 3;
  else loading_steps = 2;

  $("body").append(
    '<div class="ndk-loader" id="ndkloader"><h4 class="reveal-text"><span id="loader_step">(1/' +
      loading_steps +
      ") </span>" +
      loadingText +
      '</h4><div class="blasting-ripple loader"></div>'
  );
  $("#ndkcf_id_combination").val($("#idCombination").val()).trigger("keyup");
  if (ps_version > 1.6) getIdCombinationNdk(true);

  var $form = $(this),
    url = $form.attr("action");

  id_product = $("#ndkcf_id_product").val();
  qtty = $("#quantity_wanted").val();
  if (parseFloat(qtty) < 1) {
    qtty = 1;
    $("#quantity_wanted").val("1");
  }
  if (addProductPrice == 1) id_combination = $("#ndkcf_id_combination").val();
  else {
    id_combination = 0;
    $("#ndkcf_id_combination").val(0).trigger("keyup");
  }

  if (typeof oldRef != "undefined" && !alreadyModify) {
    old_ref = oldRef.split("-");
    old_id = refProd;
    old_id_customization = old_ref[4];
    old_conf = editConfig;
  } else {
    old_id = 0;
    old_id_customization = 0;
    old_conf = 0;
  }
  $(".form-group").removeClass("focusRequired");
  $(".form-group").find(".error").remove();
  myFormDatas = $form
    .find("input, select, textarea")
    .not(".ndk-accessory-quantity[value=0]")
    .not(".ndk_attribute_select")
    .not(".dontSend")
    .not(".image-url")
    .serialize();
  createPreview(async function () {
    //console.log(myDatas);
    $.ajax({
      type: "POST",
      url:
        url +
        (customizationPrice > 0 ||
        $(".ndk-accessory-quantity[value!=0]").length > 0
          ? "createNdkcsfields.php"
          : "createNdkcsfields.php"),
      data: `${myFormDatas}&qty=${qtty}&old_id=${old_id}&old_id_customization=${old_id_customization}&old_conf=${old_conf}&is_visual=${
        is_visual ? 1 : 0
      }&cover=''${
        newTaxRuleGroup ? "&force_taxe_rule_group=" + newTaxRuleGroup : ""
      }${newCarrierId ? "&force_carrier=" + newCarrierId : ""}`,
      // "&cover=" +
      // compoImages,
      dataType: "json",
      success: function (data) {
        $("#product_customization_id").val(data.id_customization);
        //convertPercent();
        alreadyModify = true;
        //on enregistre la config
        $("#image-block").removeClass("hight_quality");
        $(".tempSvg").remove();
        $(".hiddenForSnapshot").show().removeClass(".hiddenForSnapshot");
        $("#image-block svg").show();
        left_block = $("#image-block").html();
        lazyUnload();
        right_block = $("#ndkcsfields").html();
        $(left_block).find(".moveable-control-box").remove();
        //$(left_block).find(".textareaSvg").attr('viewBox', '')

        if ($("#layer-block").length > 0)
          layer_block = $("#layer-block").html();
        else layer_block = "";
        id_product = $("#ndkcf_id_product").val();
        id_customer = $("#ndkcf_id_customer").val();
        config_name =
          "custom-" +
          id_product +
          "-" +
          (parseFloat(id_combination) > 0 ? parseFloat(id_combination) : 0) +
          "-" +
          data.id_cart +
          "-" +
          data.id_customization;
        config_tags = "";

        //on enregistre la config
        $(right_block).find(".image-url").val("");

        leftDatas = ndkZipEncode(left_block);
        rightDatas = ndkZipEncode(right_block);
        layerDatas = ndkZipEncode(layer_block);
        //$('.image-url').val('');
        $("#loader_step").html("(2/" + loading_steps + ") ");
        if (showImgPreview == 0) compoImages = [];

        if (is_visual) {
          var photTaken = takePhoto(0, false);
        }
        compoImages = [$("#image-url-0").val()];

        configPrice = getPriceHt(totalUnitPrice, "", true);
        //json_values = getSelectedValuesFromConfig($.parseHTML(strReplaceAll(right_Block, '¬', '€')));
        if (allowEdit > 0)
          configdatas = {
            action: "saveConfig",
            leftBlock: leftDatas,
            rightBlock: rightDatas,
            layerBlock: layerDatas,
            idCustomer: id_customer,
            idProduct: id_product,
            idCustomization: data.id_customization,
            configName: config_name,
            configTags: config_tags,
            configImg: compoImages,
            preview_field: data.preview_field_img,
            base_url: baseUrl,
            skip: 0,
            price: configPrice,
          };
        else configdatas = { skip: 1 };
        $.ajax({
          type: "POST",
          url: baseUrl + "modules/ndk_advanced_custom_fields/saveConfig.php",
          data: configdatas,
          dataType: "html",
          success: function (id_config) {
            //$form.append('<p id="saved"><span>'+savedtext+'</span></p>');
            $form.append(
              '<p id="saved"><svg id="savedSvg" width="220" height="150"><path id="check" d="M30,50 l60,60 l95,-80"></path></svg></p>'
            );
            $("#ndkloader").fadeOut().remove();
            //$('#add_to_cart').fadeIn();
            if (ps_version > 1.6) {
              if (data.id_product > 0 && data.id_product != id_product) {
                $(
                  ".product-variants input:not(.false_attribute), .product-variants select:not(.false_attribute)"
                )
                  .val("")
                  .remove();
                $("#product_customization_id").val(data.id_customization);
                $("#idCombination").val(0);
                $("input[data-paypal-source-page=product]").val(
                  data.id_product
                );
                $("input[data-paypal-id-product-attribute]").val(0);

                $.when($("#product_page_product_id").val(data.id_product)).then(
                  function () {
                    $(
                      '#add-to-cart-or-refresh [data-button-action="add-to-cart"]:not(.falseButton), #add-to-cart-or-refresh [data-button-action="agile-add-to-cart"]:not(.falseButton)'
                    )
                      .prop("disabled", false)
                      .trigger("click");
                  }
                );
                setTimeout(function () {
                  //test restore product datas
                  var id_product = $("#ndkcf_id_product").val();
                  var qtty = $("#quantity_wanted").val();
                  var id_combination = $("#ndkcf_id_combination").val();
                  $("#product_page_product_id").val(id_product);
                  $("#product_customization_id").val(0);
                  $("#idCombination").val(id_combination);
                  prestashop.emit("updateProduct", {
                    reason: {
                      idProduct: id_product,
                      idProductAttribute: id_combination,
                      idCustomization: 0,
                    },
                  });
                }, 800);

                //console.log('added to cart');
              } else {
                //ajaxCart.add(id_product, (id_combination > 0 ? id_combination : null), false, null, qtty);
                $(
                  '#add-to-cart-or-refresh [data-button-action="add-to-cart"]:not(.falseButton), #add-to-cart-or-refresh [data-button-action="agile-add-to-cart"]:not(.falseButton)'
                )
                  .prop("disabled", false)
                  .trigger("click");
              }
            } else {
              if (data.id_product > 0 && data.id_product != id_product)
                ajaxCart.add(data.id_product, null, false, null, qtty);
              else {
                ajaxCart.add(
                  id_product,
                  id_combination > 0 ? id_combination : null,
                  false,
                  null,
                  qtty
                );
              }
            }
            //$('.current_config_img').attr('src', compoImages[0]).show();
            $(".blockPrice").hide();
            setTimeout(function () {
              $("#saved").remove();
              $(".snapshoot").removeClass("snapshoot");
              $("#submitNdkcsfields")
                .prop("disabled", false)
                .removeClass("loadingButton");
              $(".falseButton")
                .prop("disabled", false)
                .removeClass("loadingButton");
              if (allowEdit > 0) makeSocialCompo(id_config, true);
              $("body").addClass("ndkcf_done");
            }, 200);

            if (contentOnly) {
              //parent.jQuery.fancybox.close();
              //parent.document.location.reload(true);
            }
            $("#ndkacf-modal").modal("hide");
          },
          error: function () {},
        });

        customizationId = data.id_customization;

        if (
          typeof is_visual != "undefined" &&
          is_visual == true &&
          showHdPreview == 1
        ) {
          $("#loader_step").html("(3/" + loading_steps + ") ");
          //var htmlNodes = htmlOutput.filter(Boolean);
          //console.log(htmlOutput);
          //console.log(htmlNodes);
          setTimeout(function () {
            $.ajax({
              type: "POST",
              url: baseUrl + "modules/ndk_advanced_custom_fields/createPdf.php",
              data: {
                htmlNodes: htmlOutput.filter(Boolean),
                idCustomer: data.id_customer,
                idProduct: data.id_product,
                idCustomization: data.id_customization,
                preview_field: data.preview_field,
                base_url: baseUrl,
                images: "",
                fonts: allFonts.filter(Boolean),
              },
              dataType: "html",
              success: function (data) {},
              error: function () {},
            });
          }, 1500);
        } //if is_visual
        $("#saved, #ndkloader").remove();
      },
      error: function () {
        //alert('error handing here');
      },
    });
  });
});

function loadAccessoryAttrPrice(
  id_product,
  id_product_attribute,
  quantity,
  input,
  setPrice,
  qtty_total = 0
) {
  if (typeof loadAccessoryAttrPrice_Override == "function") {
    return loadAccessoryAttrPrice_Override(
      id_product,
      id_product_attribute,
      quantity,
      input,
      setPrice
    );
  }
  if (!id_product) return true;
  group = input.attr("data-group");
  price_overrided = false;
  override_price = false;
  if (parseFloat(input.attr("data-override-price")) > 0) {
    override_price = input.attr("data-override-price");
  }
  $.ajax({
    type: "GET",
    url: baseUrl + "modules/ndk_advanced_custom_fields/front_ajax.php",
    data: {
      id_product: id_product,
      id_product_attribute: id_product_attribute,
      quantity: quantity,
      qtty_total: qtty_total,
      group: group,
      id_value: input.attr("data-id-value"),
      override_price: override_price,
      action: "getAttributePrice",
    },
    dataType: "json",
    async: true,
    success: function (data) {
      id_value = input.attr("data-id-value");
      if (setPrice) input.attr("data-price", data.price);
      if (input.hasClass("ndk-accessory-comb-tab")) {
        qtty = $(
          "#ndkcf_totalprod_quantity_" + input.attr("data-id-value")
        ).val();
      } else {
        qtty = input.val();
      }

      qtty = input.val();
      if (setPrice) {
        if (parseFloat(input.attr("data-override-price")) > 0) {
          price_overrided = true;
          oldPrice = input.attr("data-override-price");
          if (oldPrice > data.price) {
            myPrice = data.price;
          } else {
            myPrice = oldPrice;
          }
          //console.log(oldPrice);
        } else {
          myPrice = data.price;
          oldPrice = data.old_price;
        }

        unitPrice = myPrice;
        price = qtty * unitPrice;

        valueId = input.attr("data-value-id");
        if (input.attr("data-original-price") == 0)
          input.attr("data-original-price", myPrice);

        $(
          ".ndk-accessory-quantity[data-group='" +
            group +
            "'][data-id-product-accessory='" +
            input.attr("data-id-product-accessory") +
            "'][value=0]"
        ).attr("data-updated-for-qtty", 0);
        $(
          ".ndk-accessory-quantity[data-group='" +
            group +
            "'][data-updated-for-qtty!='" +
            quantity +
            "'][data-id-product-accessory='" +
            input.attr("data-id-product-accessory") +
            "'][value!=0]"
        )
          .not(input)
          .trigger("keyup")
          .attr("data-updated-for-qtty", quantity);

        if (input.hasClass("ndk-accessory-comb-tab"))
          key = input.attr("data-value-id");
        else key = input.attr("data-value-id");

        if (input.hasClass("ndk-accessory-comb-tab")) {
          // qtty = $(
          //   "#ndkcf_totalprod_quantity_" + input.attr("data-id-value")
          // ).val();
          qtty = $(
            ".ndk-accessory-quantity[data-value-id='" + key + "']"
          ).val();
        }
        price = qtty * unitPrice;

        updatePriceNdk(price, key);
        price_key = key.split("-")[1];
        $(".final_price_" + price_key).html(formatCurrencyNdk(myPrice));
        if (!price_overrided) {
          $(".final_price_" + price_key)
            .parent()
            .find(".old_price")
            .remove();
        }
        if (oldPrice > myPrice && !price_overrided) {
          $(".final_price_" + price_key)
            .parent()
            .prepend(
              `<span class='old_price'>${formatCurrencyNdk(oldPrice)}</span>`
            );
        }
        $(
          "input.ndk-accessory-quantity[data-group='" +
            group +
            "'][data-id-value!=" +
            id_value +
            "][value!=0]"
        ).trigger("change");
        if (displayPriceHT == 1) {
          $(".final_price_" + price_key + " .priceht").remove();
          $(".final_price_" + price_key).append(
            '<span class="priceht clear clearfix"></span>'
          );
          getPriceHt(myPrice, ".final_price_" + price_key + " .priceht");
        }
        if (myPrice == 0) $(".final_price_" + price_key).hide();
        else $(".final_price_" + price_key).show();

        price_ratio = input.attr("data-price-ratio");
        if (price_ratio > 0) {
          unit_price = myPrice / price_ratio;
          input
            .parent()
            .parent()
            .parent()
            .find(".unit_price_display")
            .html(formatCurrencyNdk(unit_price));
        }
      }

      attr_stock = data.stock;

      if (attr_stock < 0) attr_stock = 0;

      input
        .parent()
        .parent()
        .find(".opt_qtty_available")
        .removeClass("qtty-warning");
      input.parent().parent().find(".opt_qtty_available b").html(data.stock);
      if (data.stock < 1)
        input
          .parent()
          .parent()
          .find(".opt_qtty_available")
          .addClass("qtty-warning");

      if (parseInt(data.oos) == 0) {
        input.attr("data-stock-available", attr_stock);
        if (
          attr_stock < input.attr("data-qtty-max") ||
          input.attr("data-qtty-max") == 0
        ) {
          input.attr("max", attr_stock);
        }
        if (input.val() > attr_stock) input.val(attr_stock).trigger("keyup");

        if (attr_stock < 1) {
          input.parent().parent().parent().removeClass("selected-accessory");
          input.parent().parent().find(".oos_msg").remove();
          input
            .parent()
            .parent()
            .append('<span class="oos_msg">' + out_of_stock_text + "</span>");
          input.parent().hide();
        } else {
          input.parent().parent().find(".oos_msg").remove();
          input.parent().show();
        }
      }

      productWeight = parseFloat(input.attr("data-product-weight"));
      if (data.weight) attrWeight = productWeight + parseFloat(data.weight);
      else attrWeight = productWeight;
      lastWeight = parseFloat(input.attr("data-weight"));
      input.attr("data-weight", attrWeight);

      if (
        lastWeight != attrWeight &&
        !isNaN(attrWeight) &&
        !isNaN(lastWeight)
      ) {
        //console.log(lastWeight+' - '+attrWeight)
        input.trigger("keyup");
      }
    },
  });
  //console.log(groupAdded);
}

function loadAccessoryAttrImg(
  id_product,
  id_product_attribute,
  link_rewrite,
  input
) {
  if (typeof loadAccessoryAttrImg_Override == "function") {
    return loadAccessoryAttrImg_Override(
      id_product,
      id_product_attribute,
      link_rewrite,
      input
    );
  }
  $.ajax({
    type: "GET",
    url: baseUrl + "modules/ndk_advanced_custom_fields/front_ajax.php",
    data: {
      id_product: id_product,
      id_product_attribute: id_product_attribute,
      link_rewrite: link_rewrite,
      action: "getAttributeImg",
    },
    dataType: "html",
    success: function (data) {
      input
        .parent()
        .parent()
        .parent()
        .find(".img-responsive")
        .attr("src", data);
      //input.parent().parent().parent().attr("data-src", data);
      if (input.val() > 0) {
        $(".accessory-ndk[data-id-product-value='" + id_product + "']")
          .trigger("click")
          .addClass("test");
      }
    },
  });
}

$(document).on("change", ".ndk_attribute_select", function () {
  id_product = $(this).attr("ref");
  var input = $(this)
    .parent()
    .parent()
    .find("input[data-id-product-accessory='" + id_product + "']");
  if (input.length > 0) {
    currName = input.attr("name");
    newName =
      currName.split("|")[0] +
      "|" +
      currName.split("|")[1] +
      "|" +
      $(this).val() +
      "]";
    input.attr(
      "data-attr-lang",
      input.attr("data-product-lang") +
        " - " +
        $(this).find("option:selected").text()
    );
    input.attr("name", newName).attr("data-id_combination", $(this).val());
    if (!input.hasClass("price_overrided_accessory")) {
      loadAccessoryAttrPrice(
        id_product,
        $(this).val(),
        input.val(),
        input,
        true
      );
    } else {
      loadAccessoryAttrPrice(
        id_product,
        $(this).val(),
        input.val(),
        input,
        false
      );
      if (displayPriceHT == 1 && input.val() > 0) {
        $(".final_price_" + input.attr("data-id-value") + " .priceht").remove();
        $(".final_price_" + input.attr("data-id-value")).append(
          '<span class="priceht clear clearfix"></span>'
        );
        getPriceHt(
          input.attr("data-price"),
          ".final_price_" + input.attr("data-id-value") + " .priceht"
        );
      }
    }
    loadAccessoryAttrImg(
      id_product,
      $(this).val(),
      $(this).attr("data-link-rewrite"),
      input
    );
    //$(".accessory-ndk[data-id-product-value='"+id_product+"']").trigger('click');
  }
});

$(document).on("change, keyup", "#ndkcsfields-block input", function () {
  $(this).attr("value", $(this).val());
});

$(document).on("change", "#ndkcsfields-block select", function () {
  $(this).attr("data-selected", $(this).val());
});

$(document).on("click", "#ndkSaveCustomization", function (e) {
  e.preventDefault();
  if ($("#ndkcf_config_name").val() != "") {
    convertPercent();
    saveCustomizationNdk();
  } else {
    $("#ndkcf_config_name").css("background", "#F2DEDE").focus();
    $("#ndkcf_config_name").parent().find(".error").remove();
    $("#ndkcf_config_name").after(
      '<span class="error alert-danger clear clearfix">' +
        $("#ndkcf_config_name").attr("data-message") +
        "</span>"
    );
  }
});

function saveCustomizationNdk(name) {
  if (typeof saveCustomizationNdk_Override == "function") {
    return saveCustomizationNdk_Override(name);
  }
  name = name || false;

  $("#ndkcf_config_tags").val($("#ndkcf_config_tags").tagify("serialize"));

  left_block = $("#image-block").html();
  $(left_block).find(".moveable-control-box").remove();
  right_block = $("#ndkcsfields").html();
  layer_block = $("#layer-block").html();
  id_product = $("#ndkcf_id_product").val();
  id_customer = $("#ndkcf_id_customer").val();
  if (name) {
    config_name = name;
    config_tags = "";
  } else {
    config_name = $("#ndkcf_config_name").val();
    config_tags = $("#ndkcf_config_tags").val();
  }
  $("body").append(ndkLoader);
  //on enregistre la config
  $(right_block).find(".image-url").val("");
  leftDatas = ndkZipEncode(left_block);
  rightDatas = ndkZipEncode(right_block);
  layerDatas = ndkZipEncode(layer_block);
  json_values = getSelectedValuesFromConfig(
    $.parseHTML(strReplaceAll(encodeURIComponent(right_block), "¬", "€"))
  );
  //$('.image-url').val('');
  configPrice = getPriceHt(totalUnitPrice, "", true);
  $.when(simuViews(true, true)).done(function () {
    $.ajax({
      type: "POST",
      url: baseUrl + "modules/ndk_advanced_custom_fields/saveConfig.php",
      data: {
        action: "saveConfig",
        leftBlock: leftDatas,
        rightBlock: rightDatas,
        layerBlock: layerDatas,
        idCustomer: id_customer,
        idProduct: id_product,
        idCustomization: 0,
        configName: config_name,
        configTags: config_tags,
        configImg: compoImages,
        price: configPrice,
        json_values: json_values,
      },
      dataType: "html",

      success: function (id_config) {
        $("#name_already_exists").hide();
        $("#ndkcsfields").append(
          '<p id="saved"><span>' + savedtext + "</span></p>"
        );
        $(".current_config_img").attr("src", compoImages[0]).show();
        makeSocialCompo(id_config, true);
        setTimeout(function () {
          $("#saved, #ndkloader").remove();
          $(".close-config-tool").trigger("click");
        }, 800);
      },
      error: function () {},
    });
  });
}

$(document).on("click", ".ndkLoadConfigImg", function () {
  $(this).parent().parent().find(".ndkLoadConfig").trigger("click");
});

$(document).on("click", ".ndkLoadConfig", function () {
  $(".tagify").tagify("destroy");
  var name = $(this).text();
  var tags = $(this).attr("data-tags");
  if ($(this).attr("data-id") != "0") {
    $("#ndkcf_config_name").val(name);
    $("#ndkcf_config_tags").val(tags);

    loadCustomization($(this).attr("data-id"));
    setTimeout(function () {
      $(".tagify").tagify({
        delimiters: [13, 188, 44],
        addTagPrompt: tagslabel,
      });
      $(".close-config-tool").trigger("click");
    }, 500);
    $(".configItem").removeClass("active");
    $(this).parent().addClass("active");
  } else {
    window.location.reload();
  }
});

function loadCustomization(id_config) {
  if (typeof loadCustomization_Override == "function") {
    return loadCustomization_Override(id_config);
  }

  var id_product = $("#ndkcf_id_product").val();
  var qtty = $("#quantity_wanted").val();
  var id_combination = $("#ndkcf_id_combination").val();

  $.ajax({
    type: "GET",
    url: baseUrl + "modules/ndk_advanced_custom_fields/saveConfig.php",
    data: { action: "getConfig", idConfig: id_config },
    dataType: "json",
    success: function (data) {
      var copyHtml = copyHtmlBlocks(data);
      $.when(copyHtml).then(function () {
        $(".fieldPane").show();
        loadInitialValues();

        applyConfigValuesNdk();
        reSetEditableFields();

        $(".pace").remove();
        if (parseInt(letOpen) == 0) $(".fieldPane").hide();

        if (makeSlide == 1) {
          makeGroupFieldsSlide();
        } else {
          $(".ndkackFieldItem").removeClass("sliderBlock");
        }

        $("#ndkcf_id_product").val(id_product);
        $("#ndkcf_id_combination").val(id_combination);
        $("#submitNdkcsfields").attr("disabled", false);
        $(".image-url").val("");
        $(".current_config_img").attr(
          "src",
          $("#configItem_" + id_config + " .ndkLoadConfigImg").attr("src")
        );
        setTimeout(function () {
          //equalheightNdkcf(".img-item-row");
          resizeMasonryGallery();
          makeSocialCompo(id_config, false);
          setTooltipsDesc();
          if (!!$.prototype.uniform) {
            //$('.ndk-checkbox, .ndk-radio').unwrap().unwrap().unwrap().unwrap();
            //$('.ndk-checkbox, .ndk-radio').uniform()
            //$.uniform.update('.ndk-checkbox, .ndk-radio')
          }
          $(".resetZones").trigger("click");
          setOpenedStatus();
          $(".ndk_tag_selector").val("all").trigger("change");
          $("#submitNdkcsfields")
            .unbind("click")
            .click(function (event) {
              $("#ndkcsfields").ndkSubmit(event);
            });

          $("#submitNdkcsfields")
            .prop("disabled", false)
            .removeClass("loadingButton")
            .find(".material-icons")
            .html("&#xE547;");
          $(".falseButton")
            .prop("disabled", false)
            .removeClass("loadingButton")
            .find(".material-icons")
            .html("&#xE547;");
          $("#submitNdkcsfields")
            .find("span:not(.material-icons)")
            .text(submitBtnText);
          $(".falseButton")
            .find("span:not(.material-icons)")
            .text(submitBtnText);
          $(".material-icons.zoom-in").html("&#xE8FF;");
          setTags();
          lazyLoadInstance.update();
          reCalculatePrice();
        }, 1000);
      });
    },
    error: function () {},
  });
}

function copyHtmlBlocks(data) {
  if (typeof copyHtmlBlocks_Override == "function") {
    return copyHtmlBlocks_Override(data);
  }
  leftDatas = $.parseHTML(
    strReplaceAll(decodeURIComponent(data.leftBlock), "¬", "€")
  );
  $(leftDatas).find(".moveable-control-box").remove();
  $("#image-block").html(leftDatas);

  $("#layer-block").html(
    $.parseHTML(strReplaceAll(decodeURIComponent(data.layerBlock), "¬", "€"))
  );
  $("#ndkcsfields").html(
    $.parseHTML(strReplaceAll(decodeURIComponent(data.rightBlock), "¬", "€"))
  );

  configToSet = getSelectedValuesFromConfig(
    $.parseHTML(strReplaceAll(decodeURIComponent(data.rightBlock), "¬", "€"))
  );
  //console.log(configToSet);
  for (idField in configToSet) {
    if (typeof configToSet[idField] != "undefined") {
      //triggerConfig(idField, configToSet[idField]);
    }
  }
}

function triggerConfig(group, value) {
  if (typeof triggerConfig_Override == "function") {
    return triggerConfig_Override(group, value);
  }
  $("#main-" + group)
    .find(".img-value[data-id-value='" + value + "']")
    .trigger("click");
  $("#main-" + group)
    .find(".color-ndk[data-id-value='" + value + "']")
    .trigger("click");
  $("#main-" + group)
    .find(".ndk-select")
    .val(value)
    .trigger("change");
  $("#main-" + group)
    .find(".ndk-radio[value='" + value + "']")
    .prop("checked", true)
    .trigger("click")
    .trigger("change");
  $("#main-" + group)
    .find(".ndk-checkbox[value='" + value + "']")
    .prop("checked", true)
    .trigger("click")
    .trigger("change");
  $("#main-" + group)
    .find(".simpleText, input.visual-text, .noborder")
    .val(value)
    .trigger("change")
    .trigger("keyup");
}

function applyConfigValuesNdk() {
  if (typeof applyConfigValuesNdk_Override == "function") {
    return applyConfigValuesNdk_Override();
  }
  $(".color-ndk.selected-value").trigger("click");

  $(".img-value.selected-value:not(.img-caracter)").each(function () {
    if ($(this).parent().find(".svg-container").length > 0)
      $(this).parent().find(".svg-container").trigger("click");
    else $(this).trigger("click");
  });

  $(".ndk-radio:checked, .checked .ndk-radio")
    .prop("checked", true)
    .trigger("click")
    .trigger("change");
  $(".ndk-checkbox:checked, .checked .ndk-checkbox,.ndk-checkbox[checkme='1']")
    .prop("checked", true)
    .trigger("change");
  $(".ndk-select").each(function () {
    $(this).find("option:selected").prop("selected", "selected");
    $(this).trigger("change");
  });

  $(".dimension_text").each(function () {
    $(this).trigger("keyup").trigger("change");
  });

  $(".form-group").removeClass("activeFormGroup");
  if (!!$.prototype.uniform)
    $("select.form-control,input[type='radio'],input[type='checkbox']")
      .not(".not_uniform")
      .uniform();

  for (idGroup in selectedConfig) {
    if (selectedConfig[idGroup] != "" && selectedConfig[idGroup] != 0) {
      $(".form-group[data-field=" + idGroup + "]")
        .find(".submitText")
        .trigger("click");
    }
  }
  document.querySelectorAll(".curvable-svg").forEach(function (e) {
    NdkAcfCurveText.getCurveTextDatas(e.getAttribute("group"));
    NdkAcfCurveText.setCurvable(e.getAttribute("group"));
  });
}

function reCalculatePrice() {
  if (typeof reCalculatePrice_Override == "function") {
    return reCalculatePrice_Override();
  }

  $(".selected-value, .ndk-radio:checked, .ndk-select option:selected").each(
    function () {
      group = $(this).attr("data-group");
      price = $(this).attr("data-price");
      updatePriceNdk(price, group);
    }
  );

  $(".selected-svg").each(function () {
    group = $(this).parent().find(".img-value").attr("data-group");
    price = $(this).parent().find(".img-value").attr("data-price");
    updatePriceNdk(price, group);
  });

  $(".noborderSimple").each(function () {
    rootInput = $(this).parent().parent().find(".simpleText");
    group = rootInput.attr("data-group");
    price = rootInput.attr("data-price");
    ppcprice = rootInput.attr("data-ppcprice");
    el = $(this);
    texte = "";
    charsCount = 0;
    inputNumber = el.parent().find(".noborderSimple").length;
    if (inputNumber > 1) separator = " \n ";
    else separator = "";

    el.parent()
      .find(".noborderSimple")
      .each(function () {
        if ($(this).val() != "") {
          texte += $(this).val() + separator;
          charsCount += $(this)
            .val()
            .replace(/\ |\n|\r|(\n\r)/g, "").length;
        }
      });

    if (texte == "" || texte == " ") {
      price = 0;
      texte = "";
    } else if (ppcprice > 0) {
      price = ppcprice * charsCount;
    } else {
      price = rootInput.attr("data-price");
    }

    if (texte == "" || texte == " ") {
      price = 0;
      texte = "";
    }
    rootInput.val(texte);
    //console.log(texte)
    updatePriceNdk(price, group);
  });

  $(".submitSimpleText").each(function () {
    group = $(this).parent().find(".visual-text").attr("data-group");
    price = $(this).parent().find(".visual-text").attr("data-price");
    ppcprice = $(this).parent().find(".visual-text").attr("data-ppcprice");
    charsCount = 0;

    charsCount = 0;
    if ($(this).parent().find("input.visual-text").length > 0) {
      texte = "";
      $(this)
        .parent()
        .find("input.visual-text")
        .each(function () {
          if ($(this).val() != "") {
            texte += $(this).val() + " ";
            charsCount += $(this)
              .val()
              .replace(/\ |\n|\r|(\n\r)/g, "").length;
          }
        });
    } else {
      texte = $(this).parent().find("textarea").text();
    }

    if (texte == "" || texte == " ") {
      price = 0;
      texte = "";
    } else if (ppcprice > 0) {
      price = ppcprice * charsCount;
    } else {
      price = $(this).parent().parent().find("textarea").attr("data-price");
    }

    if (texte == "" || texte == " ") {
      price = 0;
      texte = "";
    }
    updatePriceNdk(price, group);
  });

  $(".submitText").each(function () {
    group = $(this).parent().parent().find(".ndktextarea").attr("data-group");
    price = $(this).parent().parent().find(".ndktextarea").attr("data-price");
    ppcprice = $(this)
      .parent()
      .parent()
      .find(".ndktextarea")
      .attr("data-ppcprice");
    charsCount = 0;
    inputNumber = $(this).parent().find(".noborder").length;
    if (inputNumber > 1) separator = " \n ";
    else separator = "";

    if ($(this).parent().find(".noborder").length > 0) {
      texte = "";
      $(this)
        .parent()
        .find(".noborder")
        .each(function () {
          if ($(this).val() != "") {
            texte += $(this).val() + separator;
            charsCount += $(this)
              .val()
              .replace(/\ |\n|\r|(\n\r)/g, "").length;
          }
        });
    } else {
      texte = $(this).parent().find(".textarea").text();
    }

    if (texte == "" || texte == " ") {
      price = 0;
      texte = "";
    } else if (ppcprice > 0) {
      price = ppcprice * charsCount;
    } else {
      price = $(this).parent().parent().find(".ndktextarea").attr("data-price");
    }

    if (texte == "" || texte == " ") {
      price = 0;
      texte = "";
    }
    updatePriceNdk(price, group);
  });

  $("#ndkcsfields-block select").each(function () {
    $(this).val($(this).attr("data-selected")).trigger("change");
  });

  $(".ndk-accessory-quantity[value!=0]").each(function () {
    $(this).trigger("keyup");
  });

  $(".simpleText").each(function () {
    $(this).val($(this).attr("data-val"));
    $(this).trigger("keyup");
  });
}

function getSelectedValuesFromConfig(el) {
  if (typeof getSelectedValuesFromConfig_Override == "function") {
    return getSelectedValuesFromConfig_Override(el);
  }
  selectedConfig = [];
  el = $(el);
  el.find(".ndk-radio:checked").each(function () {
    group = $(this).attr("data-group");
    price = $(this).attr("data-price");
    selectedConfig[group] = $(this).val();
  });

  el.find(".ndk-checkbox:checked").each(function () {
    group = $(this).attr("data-value-id");
    price = $(this).attr("data-price");
    selectedConfig[group] = $(this).val();
  });

  el.find(".selected-value").each(function () {
    group = $(this).attr("data-group");
    price = $(this).attr("data-price");
    selectedConfig[group] = $(this).attr("data-id-value");
  });

  el.find(".selected-svg").each(function () {
    group = $(this).parent().find(".img-value").attr("data-group");
    price = $(this).parent().find(".img-value").attr("data-price");
    selectedConfig[group] = $(this)
      .parent()
      .find(".img-value")
      .attr("data-value");
  });

  el.find(".submitSimpleText").each(function () {
    group = $(this).parent().find(".visual-text").attr("data-group");
    price = $(this).parent().find(".visual-text").attr("data-price");
    ppcprice = $(this).parent().find(".visual-text").attr("data-ppcprice");
    charsCount = 0;

    charsCount = 0;
    if ($(this).parent().find("input.visual-text").length > 0) {
      texte = "";
      $(this)
        .parent()
        .find("input.visual-text")
        .each(function () {
          if ($(this).val() != "") {
            texte += $(this).val() + " ";
            charsCount += $(this)
              .val()
              .replace(/\ |\n|\r|(\n\r)/g, "").length;
          }
        });
    } else {
      texte = $(this).parent().find("textarea").text();
    }

    if (texte == "" || texte == " ") {
      price = 0;
      texte = "";
    } else if (ppcprice > 0) {
      price = ppcprice * charsCount;
    } else {
      price = $(this).parent().parent().find("textarea").attr("data-price");
    }

    if (texte == "" || texte == " ") {
      price = 0;
      texte = "";
    }
    selectedConfig[group] = texte;
  });

  el.find(".submitText").each(function () {
    group = $(this).parent().parent().find(".ndktextarea").attr("data-group");
    price = $(this).parent().parent().find(".ndktextarea").attr("data-price");
    ppcprice = $(this)
      .parent()
      .parent()
      .find(".ndktextarea")
      .attr("data-ppcprice");
    charsCount = 0;
    if ($(this).parent().find(".noborder").length > 0) {
      texte = "";
      $(this)
        .parent()
        .find(".noborder")
        .each(function () {
          if ($(this).val() != "") {
            texte += $(this).val() + " \n ";
            charsCount += $(this)
              .val()
              .replace(/\ |\n|\r|(\n\r)/g, "").length;
          }
        });
    } else {
      texte = $(this).parent().find(".textarea").text();
    }

    if (texte == "" || texte == " ") {
      price = 0;
      texte = "";
    } else if (ppcprice > 0) {
      price = ppcprice * charsCount;
    } else {
      price = $(this).parent().parent().find(".ndktextarea").attr("data-price");
    }

    if (texte == "" || texte == " ") {
      price = 0;
      texte = "";
    }
    selectedConfig[group] = texte;
  });

  /*el.find('.ndk-select').each(function(){
			selectedConfig[group] = $(this).attr('data-selected');
		});*/

  el.find(".ndk-accessory-quantity[value!=0]").each(function () {
    group = $(this).attr("data-group");
    selectedConfig[group] = $(this).attr("data-id-value");
  });

  el.find(".simpleText").each(function () {
    group = $(this).attr("data-group");
    selectedConfig[group] = $(this).val();
  });

  el.find(".ndk-select").each(function () {
    group = $(this).attr("data-group");
    price = $(this).attr("data-price");
    selectedConfig[group] = $(this).val();
  });

  return selectedConfig;
}

function reSetEditableFields() {
  if (typeof reSetEditableFields_Override == "function") {
    return reSetEditableFields_Override();
  }
  var e = window.event;
  var event = window.event;
  $(".textzone").each(function () {
    el = $(this);
    myGroup = $(this).attr("data-group");
    number = $(this).attr("data-number");
    myFonts = fonts;
    myColors = colors;
    mySizes = ["20", "30", "40", "50", "60", "70", "80", "90", "100"];
    myEffects = ["applatMe", "concavMe", "convexMe"];
    myAlignments = ["left", "center", "right"];

    if (window["fieldFonts_" + myGroup].length > 0)
      myFonts = window["fieldFonts_" + myGroup];

    if (window["fieldColors_" + myGroup].length > 0)
      myColors = window["fieldColors_" + myGroup];

    if (window["fieldSizes_" + myGroup].length > 0)
      mySizes = window["fieldSizes_" + myGroup];

    if (window["fieldEffects_" + myGroup].length > 0)
      myEffects = window["fieldEffects_" + myGroup];

    if (window["fieldAlignments_" + myGroup].length > 0)
      myAlignments = window["fieldAlignments_" + myGroup];

    $("#textZone" + el.attr("data-group")).fontSelector({
      hide_fallbacks: true,
      initial: myFonts[0],
      initialSize: $(window).width() < 768 ? mySizes[0] : mySizes[0],
      initialColor: myColors[0],
      initialEffect: myEffects[0],
      initialAlignment: myAlignments[0],
      selected: function (style) {},
      selectedSize: function (size) {},
      selectedColor: function (color) {
        $(".colorSelector").css("background", color);
      },
      fonts: myFonts,
      sizes: mySizes,
      colors: myColors,
      effects: myEffects,
      alignments: myAlignments,
      showStroke: stroke_color[myGroup],
    });

    $(
      "#textZone" + el.attr("data-group") + "-" + el.attr("data-number")
    ).fontSelector({
      hide_fallbacks: true,
      initial: myFonts[0],
      initialSize: $(window).width() < 768 ? mySizes[0] : mySizes[0],
      initialColor: myColors[0],
      initialEffect: myEffects[0],
      initialAlignment: myAlignments[0],
      selected: function (style) {},
      selectedSize: function (size) {},
      selectedColor: function (color) {
        $(".colorSelector").css("background", color);
      },
      fonts: myFonts,
      sizes: mySizes,
      colors: myColors,
      effects: myEffects,
      alignments: myAlignments,
      showStroke: stroke_color[myGroup],
    });
  });

  $(".ndk_selector").each(function () {
    $(this).setNdkSelector();
  });

  //equalheightNdkcf(".svg-container");

  $(".absolute-visu").each(function () {
    is_resizeable = $(this).attr("data-resizeable");
    is_dragdrop = $(this).attr("data-dragdrop");
    is_rotateable = $(this).attr("data-rotateable");

    zindex = $(this).attr("data-zindex");

    if ($(this).parent().hasClass("zone_limit")) containment = "parent";
    else containment = "";

    layerOptions = "";

    // $(this)
    //   .resizable()
    //   .resizable("destroy")
    //   .rotatable({ wheelRotate: false })
    //   .rotatable("destroy")
    //   .draggable()
    //   .draggable("destroy");

    if (is_resizeable == 1 || is_dragdrop == 1 || is_rotateable == 1)
      setEditable(
        "#" + $(this).attr("id"),
        zindex,
        containment,
        layerOptions,
        is_dragdrop,
        is_resizeable,
        is_rotateable
      );
  });

  $(".datepicker").each(function () {
    me = $(this);
    dateFormat = "yy-mm-dd";
    if (typeof me.attr("data-pattern") != "undefined")
      dateFormat = me.attr("data-pattern");
    me.datepicker({
      prevText: "",
      nextText: "",
      dateFormat: dateFormat,
    });
  });

  if ($(".view_tab").length > 0) {
    $("li.view_tab").first().trigger("click");
  }
}

$(document).on("change", ".upload_ndk_visu", function (event) {
  event.preventDefault();
  el = $(this);
  url = el.attr("data-action");
  blend = el.data("blend");
  group = el.data("group");
  special = el.data("special");
  rootBlock = $(".form-group[data-field=" + group + "]");
  bidon = false;
  if (
    window.File &&
    window.FileReader &&
    window.FileList &&
    window.Blob &&
    !bidon
  ) {
    var reader = new FileReader();
    el.parent().parent().append(ndkLoader);

    upFileType = "other";

    reader.onloadend = function () {
      extension = reader.result.split("base64,")[0]; //data:application/pdf;
      //array('image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
      if (
        extension.indexOf("jpg") > -1 ||
        extension.indexOf("png") > -1 ||
        extension.indexOf("png") > -1 ||
        extension.indexOf("gif") > -1 ||
        extension.indexOf("jpeg") > -1 ||
        extension.indexOf("pjpeg") > -1 ||
        extension.indexOf("x-png") > -1
      )
        upFileType = "image";

      dataToBeSent = reader.result.split("base64,")[1];
      var posting = $.post(url, {
        data: dataToBeSent,
        blend: blend,
        special: rootBlock.attr("data-custom_class"),
      });
      posting.done(function (data) {
        //console.log(baseUrl+data);
        el.parent().parent().find(".upload-error").remove();
        if (data == "Forbidden!") {
          el.parent()
            .parent()
            .append(
              '<p class="alert alert-danger upload-error">Bad extension</p>'
            );
          el.parent().parent().find(".remove-upload").trigger("click");
        } else {
          //el.parent().hide();
          if (upFileType == "image") {
            el.parent()
              .parent()
              .find(".img-value:eq(0)")
              .removeClass("hidden")
              .attr("src", baseUrl + data)
              .attr("data-src", baseUrl + data)
              .attr("data-value", baseUrl + data)
              .trigger("click");
          } else {
            el.parent()
              .parent()
              .find(".img-value:eq(0)")
              .removeClass("hidden")
              .attr(
                "src",
                baseUrl +
                  "modules/ndk_advanced_custom_fields/views/img/file_picto.png"
              )
              .attr("data-value", baseUrl + data)
              .addClass("pictoFileUpload")
              .trigger("click");
          }
          el.parent().parent().find(".remove-upload").show();
        }
        $("#ndkloader").fadeOut().remove();
      });
    };
    reader.readAsDataURL(this.files[0]);
  } else {
    uploadForSafari(event, $(this));
  }
  killer = $(this).parent().parent().parent().attr("data-killer");
  if (parseFloat(killer) > 0) {
    $(".form-group[data-field='" + killer + "'] .doneOption").remove();
    $(".form-group[data-field='" + killer + "']").append(
      '<span class="doneOption"></span>'
    );
    $(".form-group[data-field='" + killer + "']")
      .find(".ndk-select")
      .val("Oui")
      .trigger("change");
  }
});

function uploadForSafari(event, input) {
  if (typeof uploadForSafari_Override == "function") {
    return uploadForSafari_Override(event, input);
  }

  url = input.attr("data-action");
  blend = input.data("blend");
  special = input.data("special");
  el = input;
  el.parent().parent().append(ndkLoader);

  //get selected file
  files = event.target.files;
  upFileType = "other";
  //form data check the above bullet for what it is
  var data = new FormData();

  //file data is presented as an array
  for (var i = 0; i < files.length; i++) {
    var file = files[i];
    if (file.type.match("image.*")) {
      upFileType = "image";
    }
    data.append("file", file, file.name);
    data.append("safari", true);
    data.append("blend", blend);
    data.append("special", special);

    //create a new XMLHttpRequest
    var xhr = new XMLHttpRequest();

    //post file data for upload
    xhr.open("POST", url, true);
    xhr.send(data);
    xhr.onload = function () {
      //get response and show the uploading status
      var data = xhr.responseText;
      if (xhr.status === 200) {
        //console.log(baseUrl+data);
        el.parent().hide();
        if (upFileType == "image") {
          el.parent()
            .parent()
            .find(".img-value:eq(0)")
            .removeClass("hidden")
            .attr("src", baseUrl + data)
            .attr("data-src", baseUrl + data)
            .attr("data-value", baseUrl + data)
            .trigger("click");
        } else {
          el.parent()
            .parent()
            .find(".img-value:eq(0)")
            .removeClass("hidden")
            .attr(
              "src",
              baseUrl +
                "modules/ndk_advanced_custom_fields/views/img/file_picto.png"
            )
            .attr("data-value", baseUrl + data)
            .addClass("pictoFileUpload")
            .trigger("click");
        }
        el.parent().parent().find(".remove-upload").show();
        $("#ndkloader").fadeOut().remove();
      }
    };
  }

  upFileType = "other";
  var extension = input.val().split(".").pop().toLowerCase();
  if (
    extension.indexOf("jpg") > -1 ||
    extension.indexOf("png") > -1 ||
    extension.indexOf("png") > -1 ||
    extension.indexOf("gif") > -1 ||
    extension.indexOf("jpeg") > -1 ||
    extension.indexOf("pjpeg") > -1 ||
    extension.indexOf("x-png") > -1
  )
    upFileType = "image";
}

$(document).on("click", ".remove-upload", function (event) {
  event.preventDefault();
  group = $(this).parent().find(".img-value").attr("data-group");
  $(this).parent().find(".img-value").addClass("hidden");
  $(this).parent().parent().find(".uploader").show();
  $("input#ndkcsfield_" + group)
    .val("")
    .trigger("keyup");
  $("#visual_" + group).remove();
  $(this).hide();
  updatePriceNdk(0, group);
  $("#layer-edit-" + group).remove();
  killer = $(".form-group[data-field='" + group + "']").attr("data-killer");
  $(".form-group[data-field='" + killer + "']")
    .find(".ndk-select")
    .val("Non")
    .trigger("change");
  $(".form-group[data-field='" + killer + "'] .doneOption").remove();
});

$("tr.customization > .cart_quantity").html("");

$(document).on("click", ".ndkzoom", function (e) {
  e.preventDefault();
  $(".resetZones").trigger("click");
  //$('.ndkzoom').attr('disabled', 'disabled').addClass('loadingButton');
  convertPercent();
  //$('.ndkzoom').attr('disabled', false).removeClass('loadingButton');
  if (!!$.prototype.fancybox) {
    $.fancybox.open(
      [
        {
          type: "inline",
          autoScale: false,
          minHeight: 30,
          width: "80%",
          height: "80%",
          showCloseButton: false,
          autoDimensions: false,
          content:
            '<div class="popupPreviewContainer clear clearfix">' +
            $("#image-block").html() +
            "</div>",
          beforeShow: function () {
            $(
              ".popupPreviewContainer .svggradient, .popupPreviewContainer .svgfilter"
            ).remove();
          },
        },
      ],
      {
        padding: 0,
      }
    );
  }

  /*
		$.ajax({
					type: "GET",
					url: baseUrl+'modules/ndk_advanced_custom_fields/showPreview.php',
					data: {htmlNodes : $('#image-block').html()},
					dataType: "html",
					success: function(data) {
						$(data).find('.svggradient, .svgfilter').remove();
						if (!!$.prototype.fancybox)
								{
									$.fancybox.open([
									{
										type: 'inline',
										autoScale: false,
										minHeight: 30,
										width: '80%',
										height:'80%',
										showCloseButton: false,
										autoDimensions: false,
										content: '<div class="popupPreviewContainer clear clearfix">'+data+'</div>',
										beforeShow: function(){
											$('.popupPreviewContainer .svggradient, .popupPreviewContainer .svgfilter').remove();
										}
									}],
									{
										padding: 0
									});
						}
						$('.ndkzoom').attr('disabled', false).removeClass('loadingButton');
					},
					error: function(){
						  //alert('error handing here');
					}
				});
				*/
});

if (typeof is_visual != "undefined" && is_visual == true) {
  $(document).on("click", "#image-block", function (e) {
    //e.preventDefault();
    $("#custom-block-popup").removeClass("opened");
    $("#custom-block-popup").removeClass("slideInLeft");
    //return false;
  });
  $(document).on(
    "click",
    "body:not(.ndkcf_popup_mode) .js-qv-product-cover",
    function (e) {
      e.preventDefault();
      return false;
    }
  );
}

$(document).on("click", ".visual-effect", async function () {
  visu = $(this).attr("data-src");
  group = $(this).attr("data-group");
  zindex = $(this).attr("data-zindex");
  dragdrop = $(this).attr("data-dragdrop");
  resizeable = $(this).attr("data-resizeable");
  rotateable = $(this).attr("data-rotateable");
  view = $(this).attr("data-view");
  coloreffect = "normal";
  background = "";
  type = false;
  specialClass = "";
  if ($(this).hasClass("img-value")) {
    coloreffect = $(this).attr("data-blend");
    if (typeof $(this).attr("data-mask-image") != "undefined") {
      if ($(this).attr("data-mask-image") != "") {
        type = "colorize";
        if (!ndkBrowserVersion)
          background = "url('" + $(this).attr("data-src") + "')";
        else background = $(this).attr("data-src");

        visu = $(this).attr("data-mask-image");
      }
    }
  }

  if ($(this).hasClass("color-ndk")) {
    type = "color";
    specialClass = "colorNdk";
    coloreffect = $(this).attr("data-blend");

    if (
      $(this).hasClass("color_square") &&
      !$(this).parent().is(".ndkcf_col_title")
    ) {
      if (parseInt($(this).attr("qtty_total_attr")) < 1) {
        console.log("nothing selected");
        $("#visual_" + group).remove();
        $("#layer-edit-" + group).remove();
        return false;
      }
    }
    if ($(this).hasClass("colorize-ndk")) {
      type = "colorize";
      background = $(this).attr("data-color");
      //visu='';
    }

    if (typeof $(this).attr("data-mask-image") != "undefined") {
      if ($(this).attr("data-mask-image") != "") {
        type = "colorize";
        if (!ndkBrowserVersion) background = $(this).attr("data-color");
        else background = "url('" + $(this).attr("data-src") + "')";

        visu = $(this).attr("data-mask-image");
      }
    }
  }

  if ($(this).hasClass("accessory-ndk")) {
    //group = group+'-'+$(this).attr('data-id-product-value');
    if (visu == 0) visu = $(this).find(".accessory-img-block img").attr("src");
    if ($(this).find(".ndk-accessory-quantity:eq(0)").val() < 1) {
      designCompo(
        visu,
        group,
        view,
        zindex,
        dragdrop,
        resizeable,
        rotateable,
        0,
        0,
        type,
        coloreffect,
        background
      );
    } else {
      $("#visual_" + group).remove();
      $("#layer-edit-" + group).remove();
      $(this).find(".selected-value").removeClass("selected-value");
    }
  } else if ($(this).hasClass("img-caracter")) {
    composeCaracter(
      visu,
      group,
      view,
      zindex,
      dragdrop,
      resizeable,
      rotateable,
      0,
      0,
      type,
      coloreffect,
      background,
      $(this).attr("data-type")
    );
  } else {
    designCompo(
      visu,
      group,
      view,
      zindex,
      dragdrop,
      resizeable,
      rotateable,
      0,
      0,
      type,
      coloreffect,
      background,
      false,
      specialClass
    );
  }
});

$(document).on("change", ".visual-effect-select", function () {
  visu = $(this).find("option:selected").attr("data-src");
  group = $(this).find("option:selected").attr("data-group");
  zindex = $(this).find("option:selected").attr("data-zindex");
  dragdrop = $(this).find("option:selected").attr("data-dragdrop");
  resizeable = $(this).find("option:selected").attr("data-resizeable");
  rotateable = $(this).find("option:selected").attr("data-rotateable");

  view = $(this).find("option:selected").attr("data-view");
  designCompo(
    visu,
    group,
    view,
    zindex,
    dragdrop,
    resizeable,
    rotateable,
    0,
    0,
    false
  );
  if (typeof visu == "undefined") {
    $("#visual_" + group + ", #layer-edit-" + group).remove();
  }
});

$(document).on("click", ".ac_container .toggler.letOpen", function () {
  toggler = $(this);
  //$('.toggler').removeClass('active').find('.toggleText').html(toggleOpenText);
  thisFieldPane = $(this).parent().find(".fieldPane");
  //$('.fieldPane:visible').not(thisFieldPane).hide();
  $.when($(this).parent().find(".fieldPane").toggle()).then(function () {
    if ($(this).parent().find(".fieldPane").is(":visible")) {
      toggler.addClass("active");
      toggler.find(".toggleText").html(toggleCloseText);
    } else {
      toggler.removeClass("active");
      toggler.find(".toggleText").html(toggleOpenText);
    }
    scrollToNdk($(this).parent(), 800);
  });
});

if (letOpen == 0) {
  $(document).on("click", ".toggler", function () {
    toggler = $(this);
    //$('.toggler').removeClass('active').find('.toggleText').html(toggleOpenText);
    thisFieldPane = $(this).parent().find(".fieldPane");

    //$('.fieldPane:visible').not(thisFieldPane).hide();
    $.when($(this).parent().find(".fieldPane").toggle()).then(function () {
      if ($(this).parent().find(".fieldPane").is(":visible")) {
        toggler.addClass("active");
        toggler.parent().addClass("opened-form-group");
        toggler.find(".toggleText").html(toggleCloseText);
      } else {
        toggler.removeClass("active");
        toggler.parent().removeClass("opened-form-group");
        toggler.find(".toggleText").html(toggleOpenText);
      }
      scrollToNdk($(this).parent(), 800);
      //equalheightNdkcf(".img-item-row");
      resizeMasonryGallery();
    });
    $(".ndkQuickAccessBox-item").removeClass("active");
    $(
      ".ndkQuickAccessBox-item[data-target='" +
        $(this).parent().attr("data-field") +
        "']"
    ).addClass("active");
  });
} else {
  $(".toggler").addClass("letOpen");
}

$(document).on("click", ".color-ndk", function () {
  var group = $(this).attr("data-group");
  $("#temporary-ndk-color-image-" + group).remove();
  view = $(this).attr("data-view");
  price = $(this).attr("data-price");
  if (!$(this).hasClass("color_square")) {
    updatePriceNdk(price, group);
    setImgValue($(this), group);
  }

  $(this).parent().find(".color-ndk").removeClass("selected-color");
  $(this).addClass("selected-color");
  /*si effet visuel et pas d image*/
  myel = $(this);
  if (
    $(this).attr("data-quantity-available") != "null" &&
    !$(this).hasClass("color_square")
  )
    updateQuantityForValue(myel.attr("data-quantity-available"), group);

  if (
    $(".zone_limit[data-group='" + group + "']").length > 0 &&
    $(".view_tab[data-view='" + view + "']").length > 0
  ) {
    container = ".zone_limit[data-group='" + group + "']";
  } else {
    container = "#image-block";
  }

  if ($(this).hasClass("visual-effect") && $(this).attr("data-src") == "0") {
    if ($(this).attr("data-color").indexOf("url") > -1) {
      uri = $(this).attr("data-color").replace("url('", "").replace("')", "");
      $(this).attr("data-color", uri);
    } else {
      svg =
        '<svg xmlns="http://www.w3.org/2000/svg" width="' +
        $(container).width() +
        '" height="' +
        $(container).height() +
        '"><rect width="' +
        $(container).width() +
        '" height="' +
        $(container).height() +
        '" style="fill:rgb(' +
        hexToRgb($(this).attr("data-color")) +
        ');"></rect></svg>';
      uri = "data:image/svg+xml;base64," + btoa(svg);
    }
    myel.attr("data-src", uri).trigger("click");
  }
  $(document).trigger({
    type: "ndkacf:ndkColorSet",
    group: group,
    color: myel.attr("data-color"),
  });
});

function hexToRgb(hex) {
  hex = hex.replace(/[^0-9A-F]/gi, "");
  var bigint = parseInt(hex, 16);
  var r = (bigint >> 16) & 255;
  var g = (bigint >> 8) & 255;
  var b = bigint & 255;

  return [r, g, b].join();
}

$(document).on("click, change", ".ndk-radio", function () {
  group = $(this).attr("data-group");
  checkedRadio = $(".ndk-radio[data-group='" + group + "']:checked");
  $(".form-group[data-field='" + group + "']")
    .find(".selected_radio")
    .removeClass("selected_radio");
  checkedRadio.parent().addClass("selected_radio");
  //checkFieldRestrictions(checkedRadio.attr('data-id-value'), group);
  price = checkedRadio.attr("data-price");
  updatePriceNdk(price, group, checkedRadio.attr("data-id-value"));
  if (checkedRadio.attr("data-quantity-available") != "null")
    updateQuantityForValue(checkedRadio.attr("data-quantity-available"), group);
  if (checkedRadio.length > 0) {
    $("body").trigger({
      type: "ndkacf:ndkRadioSet",
      group: group,
      value: checkedRadio.attr("data-id-value"),
      tax_ratio: checkedRadio.attr("data-tax_ratio"),
      force_tax_rule: checkedRadio.attr("data-force_tax_rule"),
      force_carrier: checkedRadio.attr("data-force_carrier"),
    });
  } else {
    $("body").trigger({
      type: "ndkacf:ndkRadioSet",
      group: group,
      value: checkedRadio.attr("data-id-value"),
      tax_ratio: false,
      force_tax_rule: false,
      force_carrier: false,
    });
  }
});

$(document).on("change", ".ndk-select", function () {
  selectedOption = $(this).find("option:selected");
  group = selectedOption.attr("data-group");
  price = selectedOption.attr("data-price");
  //checkFieldRestrictions($(this).find('option:selected').attr('data-id-value'), group);

  updatePriceNdk(price, group, selectedOption.attr("data-id-value"));
  if (selectedOption.attr("data-quantity-available") != "null")
    updateQuantityForValue(
      selectedOption.attr("data-quantity-available"),
      group
    );
  $("body").trigger({
    type: "ndkacf:ndkSelectSet",
    group: selectedOption.attr("data-group"),
    value: selectedOption.attr("data-id-value"),
    tax_ratio: selectedOption.attr("data-tax_ratio"),
    force_tax_rule: selectedOption.attr("data-force_tax_rule"),
    force_carrier: selectedOption.attr("data-force_carrier"),
  });
});

//$('.textarea').lettering();
$(document).on("change", ".arcText", function () {
  $texteditor = $(".textarea");
  val = $(this).val();
  if (val > 680) val = 10000;

  if (val < -680) val = -10000;

  $texteditor.circleType({ radius: val });
  ghoape(".textarea");
});

/*PArtie z-index à garer pour les calques
	$('.form-group').on('click, mouseover', function(){
		  $('.form-group').removeClass('activeGroup');
		  $(this).addClass('activeGroup');
		  var others = $('.ui-wrapper');
		  var others_img = $('.absolute-visu');
	
		  others.each(function(){
			$(this).css('z-index', $(this).find('img').attr('data-zindex'));
		  });
			others_img.each(function(){
			$(this).css('z-index', $(this).attr('data-zindex'));
		  });	
		  var key = $(this).attr('data-field');
		  targetImg = $('#visual_'+key);
		  if(targetImg.hasClass('dragdrop'))
			targetImg.parent().css('z-index', 99);
			else
			targetImg.css('z-index', 99);
		});
	
	$('#image-block').on('mouseleave', function(){
		  resetZindex();
   });
   
   $('.submitContainer').on('click, mouseover', function(){
			 resetZindex();
   });
   
   function resetZindex(){
	if (typeof(resetZindex_Override) == 'function') { 
		return resetZindex_Override();
	}
	   $('.form-group').removeClass('activeGroup');
			 var others = $('.ui-wrapper');
			   var others_img = $('.absolute-visu');
	   
			 others.each(function(){
			   $(this).css('z-index', $(this).find('img').attr('data-zindex'));
			 });
			   others_img.each(function(){
			   $(this).css('z-index', $(this).attr('data-zindex'));
			 });	
   }
	*/

$(document).on("click", ".visible_layer", function () {
  hideLayer($(this));
});

$(document).on("click", ".hidden_layer", function () {
  showLayer($(this));
});

$(document).on("click", ".submitSimpleText", function () {
  group = $(this).parent().find(".visual-text").attr("data-group");
  zindex = $(this).parent().find(".visual-text").attr("data-zindex");
  price = $(this).parent().find(".visual-text").attr("data-price");
  ppcprice = $(this).parent().find(".visual-text").attr("data-ppcprice");
  view = $(this).parent().find(".visual-text").attr("data-view");
  dragdrop = $(this).parent().find(".visual-text").attr("data-dragdrop");
  resizeable = $(this).parent().find(".visual-text").attr("data-resizeable");
  rotateable = $(this).parent().find(".visual-text").attr("data-rotateable");
  charsCount = 0;

  if ($(this).parent().find("input.visual-text").length > 0) {
    texte = "";
    $(this)
      .parent()
      .find("input.visual-text")
      .each(function () {
        if ($(this).val() != "") {
          texte += $(this).val() + " ";
          charsCount += $(this)
            .val()
            .replace(/\ |\n|\r|(\n\r)/g, "").length;
        }
      });
  } else {
    texte = $(this).parent().find("textarea").text();
  }

  if (texte == "" || texte == " ") {
    price = 0;
    texte = "";
  } else if (ppcprice > 0) {
    price = ppcprice * charsCount;
  } else {
    price = $(this).parent().parent().find("textarea").attr("data-price");
  }

  if (texte == "" || texte == " ") {
    price = 0;
    texte = "";
  }
  height = $(this).parent().find(".visual-text").height();
  width = $(this).parent().find(".visual-text").width();
  updatePriceNdk(price, group);
});

function writeMyCanvas(group, width, height, texte) {
  if (typeof writeMyCanvas_Override == "function") {
    return writeMyCanvas_Override(group, width, height, texte);
  }
  var canvas = document.getElementById("myCanvas" + group);

  //If you really need to you can access the shadow inline SVG created by calling:

  //var context = canvas.getContext('2d');
  var context = new C2S(width, height);
  newWidth = width + 10;
  newHeight = height + 20;

  context.canvas.width = newWidth;
  context.canvas.height = newHeight;

  context.clearRect(0, 0, newWidth, newHeight);
  var x = newWidth / 1.9;
  var y = newHeight / 1.5;

  context.clearRect(0, 0, newWidth, newHeight);

  context.font = "bold 20px Helvetica";
  context.lineWidth = 3;
  context.textAlign = "center";

  context.fillStyle = "black";
  context.fillText(texte, x, y);

  //context.fill();
  //context.stroke();

  //serialize your SVG
  var svg = context.getSvg();
  var mySerializedSVG = context.getSerializedSvg(); //true here, if you need to convert named to numbered entities.
  return svg;
}

$(document).on(
  "click",
  ".fontSelectUl li, .fontColorSelectUl li, .fontSizeSelectUl li, .strokeColorSelectUl li, .alignSelector i, .effectButton",
  function () {
    input = $(this).parent().parent().parent().find(".ndktextarea");
    color = $(this)
      .parent()
      .parent()
      .find(".fontColorSelectUl li.active")
      .text();
    font = $(this).parent().parent().find(".fontSelectUl li.active").text();
    if ($(this).parent().parent().find(".noborder").length > 0) {
      texte = "";
      inputNumber = input.parent().find(".noborder").length;
      if (inputNumber > 1) separator = " \n ";
      else separator = "";

      $(this)
        .parent()
        .parent()
        .find(".noborder")
        .each(function () {
          texte += $(this).val() + separator;
          //$(this).trigger('keyup')
        });
    } else {
      texte = $(this).parent().parent().find(".ndktextarea").text();
    }
    applyTextAndFonts(texte, color, font, input);
    //input.val(texte).trigger('keyup');
    $(this).parent().parent().find(".noborder:eq(0)").trigger("keyup");
  }
);

$(document).on("click", ".svg-container", function () {
  var group = $(this).parent().find("img").attr("data-group");
  zindex = $(this).parent().find("img").attr("data-zindex");
  price = $(this).parent().find("img").attr("data-price");
  view = $(this).parent().find("img").attr("data-view");
  blend = $(this).parent().find("img").attr("data-blend");
  dragdrop = $(this).parent().find("img").attr("data-dragdrop");
  resizeable = $(this).parent().find("img").attr("data-resizeable");
  rotateable = $(this).parent().find("img").attr("data-rotateable");
  if ($(".zone_limit[data-group='" + group + "']").length > 0) {
    width = $(".zone_limit[data-group='" + group + "']").innerWidth();
    height = $(".zone_limit[data-group='" + group + "']").innerHeight();
  } else {
    width = $("#image-block").innerWidth();
    height = $("#image-block").innerHeight();
  }
  updatePriceNdk(price, group);
  $(".form-group[data-field=" + group + "]")
    .find(".remove-img-item")
    .show();
  if ($(this).parent().find("img").attr("data-quantity-available") != "null")
    updateQuantityForValue(
      $(this).parent().find("img").attr("data-quantity-available"),
      group
    );

  clonedSvg = $(this).find("svg").clone();

  if ($(this).parent().find("img.visual-effect").length > 0) {
    if ($(this).parent().find("img.visual-effect.img-caracter").length > 0) {
      textPath = false;
      if ($(this).find("svg").length > 0) {
        $(this)
          .find("path")
          .each(function () {
            pathId = $(this)
              .attr("id")
              .replace("_" + group, "");
            $(this).attr("id", group + "_" + pathId);
            $("#text-item-" + group).attr("data-path", group + "_" + pathId);
          });
        textPath = true;
      }
      composeCaracter(
        clonedSvg,
        group,
        view,
        zindex,
        dragdrop,
        resizeable,
        rotateable,
        width,
        height,
        "svg",
        blend,
        "",
        $(this).attr("data-type")
      );
      $("#textZone" + group)
        .find(".submitTextItem")
        .trigger("click");
      $("#textZone" + group)
        .find(".submitText")
        .trigger("click");
    } else {
      designCompo(
        clonedSvg,
        group,
        view,
        zindex,
        dragdrop,
        resizeable,
        rotateable,
        width,
        height,
        "svg",
        blend
      );
    }
  }
  setImgValue($(this).parent().find("img"), group);
  $(this)
    .parent()
    .parent()
    .parent()
    .find(".img-value")
    .removeClass("selected-value");
  $(this)
    .parent()
    .parent()
    .parent()
    .find(".svg-container")
    .removeClass("selected-svg");
  $(this).addClass("selected-svg");
  //$(this).css('width', '100%').css('height', '30px');
  //$('body').css('min-width', '').css('min-height', '');

  //equalheightNdkcf(".svg-container");
  $(".view_tab.activeView").trigger("click");

  applyTypeValue($(this).parent().find(".img-value"));
});

$(document).on("click", ".view_tab", function () {
  clickOnViewTab($(this));
});

function clickOnViewTab(tab) {
  if (typeof clickOnViewTab_Override == "function")
    return clickOnViewTab_Override(tab);

  $(".resetZones").trigger("click");
  if (makeItFloat > 0) {
    scrollToNdk($("#top-product-infos"), 800);
    $(".ndk-floating").css("top", "");
  }

  $("#image-block")
    .attr("data-view", tab.attr("data-view"))
    .attr("data-id", tab.attr("data-id"));

  changeSvgViewForTab(tab);

  $("#ndkcsfields-block .form-group").hide();
  $(
    ".form-group[data-view='" +
      tab.attr("data-view") +
      "']:not(.userPanel), .form-group[data-view='0']:not(.userPanel)"
  ).show();
  $(
    ".form-group[data-view*='" + tab.attr("data-view") + "|']:not(.userPanel)"
  ).show();
  $(
    ".form-group[data-view*='|" + tab.attr("data-view") + "']:not(.userPanel)"
  ).show();
  $(".view_tab").removeClass("activeView").removeClass("btn-primary");
  setLayerControlForTab(tab);
  set3dStatusFroTab(tab);
  setSlideStatusForTab(tab);
  tab.addClass("activeView").addClass("btn-primary");

  $(".absolute-visu").hide();
  $(".zone_limit").hide();

  $(".absolute-visu.view-" + tab.attr("data-view")).show();
  $(".zone_limit.view-" + tab.attr("data-view")).show();

  $("[class*='" + tab.attr("data-view") + "|']").show();
  $("[class*='|" + tab.attr("data-view") + "']").show();

  $(".absolute-visu.view-0").show();
  $(".zone_limit.view-0").show();
  $(".layer_view").addClass("visible_layer").removeClass("hidden_layer");
  $(".moveable-control-box").hide();
}

function set3dStatusFroTab(tab) {
  if (!tab.hasClass("glb"))
    $("#image-block").removeClass("three-d-view-active");
  $(".three-d-canvas").hide();
  $(
    ".three-d-canvas[data-view='" +
      tab.attr("data-view") +
      "'], .three-d-canvas[data-view='0']"
  ).show();
  $(".three-d-canvas[data-view*='" + tab.attr("data-view") + "|']").show();
  $(".three-d-canvas[data-view*='|" + tab.attr("data-view") + "']").show();
}
function setLayerControlForTab(tab) {
  $(".groupFieldBlock").css(
    "padding-bottom",
    $(".sliderBlock .ndkackFieldItem:visible:eq(0)").height()
  );
  $(".editThisLayer").hide();
  $(
    ".editThisLayer[data-view='" +
      tab.attr("data-view") +
      "'], .editThisLayer[data-view='0']"
  ).show();
  $(".editThisLayer[data-view*='" + tab.attr("data-view") + "|']").show();
  $(".editThisLayer[data-view*='|" + tab.attr("data-view") + "']").show();
}
function setSlideStatusForTab(tab) {
  if (makeSlide == 1 && !tab.hasClass("activeView")) {
    $(".ndkcfPagerItem").hide();
    $(
      ".ndkcfPagerItem[data-view='" +
        tab.attr("data-view") +
        "'], .ndkcfPagerItem[data-view='0']"
    ).show();
    $(".ndkcfPagerItem[data-view*='" + tab.attr("data-view") + "|']").show();
    $(".ndkcfPagerItem[data-view*='|" + tab.attr("data-view") + "']").show();
    ndkCfShowSlide($(".sliderBlock .ndkackFieldItem:visible:eq(0)"));
  }
}
function changeSvgViewForTab(tab) {
  $("#image-block").find(".ndk-svg-view").hide();
  if ($("#ndkcsfieldSVGView_" + tab.attr("data-view")).length > 0) {
    svgImage = $("#ndkcsfieldSVGView_" + tab.attr("data-view"))
      .find("image")
      .attr("xlink:href");

    image = new Image();

    image.onload = function () {
      document.getElementById("bigpic").src = this.src;
      document.getElementById("bigpic").srcset = this.src;
    };
    image.src = svgImage;

    if ($("#ndk-svg-view-" + tab.attr("data-view")).length == 0) {
      $.when(
        $("#image-block").append(
          '<div class="ndk-svg-view" id="ndk-svg-view-' +
            tab.attr("data-view") +
            '">' +
            $("#ndkcsfieldSVGView_" + tab.attr("data-view")).html() +
            "</div>"
        )
      ).done(function () {
        $("#ndk-svg-view-" + tab.attr("data-view"))
          .find("image")
          .each(function () {
            if (!$(this).parent().is("use")) $(this).remove();
          });

        $("#ndk-svg-view-" + tab.attr("data-view")).css(
          "mix-blend-mode",
          tab.attr("data-blend")
        );
      });
    }
  }

  if ($("#ndk-svg-view-" + tab.attr("data-view")).length > 0) {
    $("#ndk-svg-view-" + tab.attr("data-view")).show();
  } else {
    $("#bigpic")
      .attr("src", tab.attr("data-img"))
      .attr("srcset", tab.attr("data-img"));
  }
}

$(document).on("keyup", '#ndkcsfields-block input[type="text"]', function () {
  if ($(this).attr("id") != "search_query_top") {
    if ($(this).val() != "") {
      $(this).attr("size", $(this).val().length + 5);
      $(this).parent().css("width", $(this).innerWidth);
    } else {
      $(this).attr("size", 15);
      $(this).parent().css("width", $(this).innerWidth);
    }
  }
});

/*$(document).on('mouseout', '.noborder', function() {
			  $(this).blur();
		});*/

// The button to increment the product value
$(document).on("click", ".quantity-ndk-plus", function (e) {
  e.preventDefault();
  if (typeof $(this).attr("data-target-class") != "undefined")
    targetClass = "." + $(this).attr("data-target-class");
  else targetClass = ".ndk-accessory-quantity";

  input = $(this).parent().find(targetClass);

  if (typeof input.attr("step") != "undefined")
    step = parseFloat(input.attr("step"));
  else step = 1;

  currentVal = parseFloat(input.val());
  currentVal = currentVal.round(2);
  if (input.attr("data-qtty-available") > 0)
    quantityAvailableNdk = input.attr("data-qtty-available");
  else quantityAvailableNdk = 100000000;

  if (input.attr("data-qtty-max") > 0)
    quantityMaxNdk = input.attr("data-qtty-max");
  else quantityMaxNdk = quantityAvailableNdk;

  newVal = (currentVal + parseFloat(step)).round(2);
  if (
    !isNaN(currentVal) &&
    newVal < quantityAvailableNdk &&
    currentVal < quantityMaxNdk
  )
    input.val(newVal).trigger("keyup").trigger("change");
  else input.val(quantityMaxNdk).trigger("keyup").trigger("change");

  if (input.attr("data-step_quantity") != "") {
    stepQtty = input.attr("data-step_quantity").split(";").map(Number);
    //console.log(stepQtty);
    val = parseFloat(input.val());

    if ($.inArray(val, stepQtty) == -1) {
      input
        .val(goToStepQuantity(val, stepQtty, "+"))
        .trigger("keyup")
        .trigger("change");
    }
  }
});

function goToStepQuantity(val, stepQtty, direction) {
  if (typeof goToStepQuantity_Override == "function") {
    return goToStepQuantity_Override(val, stepQtty, direction);
  }
  nextVal = val;
  if (direction == "-") {
    last_encountred = 0;
    for (var i = 0; i < stepQtty.length; i++) {
      if (stepQtty[i] < val && stepQtty[i] >= last_encountred) {
        last_encountred = stepQtty[i];
        nextVal = stepQtty[i];
      }
    }
  } else {
    last_encountred = 99999999999999999;
    for (var i = 0; i < stepQtty.length; i++) {
      if (stepQtty[i] > val && stepQtty[i] <= last_encountred) {
        last_encountred = stepQtty[i];
        nextVal = stepQtty[i];
      }
    }
  }
  return nextVal;
}

// The button to decrement the product value
$(document).on("click", ".quantity-ndk-minus", function (e) {
  e.preventDefault();
  if (typeof $(this).attr("data-target-class") != "undefined")
    targetClass = "input." + $(this).attr("data-target-class");
  else targetClass = ".ndk-accessory-quantity";

  input = $(this).parent().find(targetClass);

  if (typeof input.attr("step") != "undefined")
    step = parseFloat(input.attr("step"));
  else step = 1;

  currentVal = parseFloat(input.val());
  currentVal = currentVal.round(2);
  if (input.attr("data-qtty-min") > 0)
    quantityMinNdk = input.attr("data-qtty-min");
  else quantityMinNdk = 0;

  if (input.attr("data-step_quantity") != "") {
    stepQtty = input.attr("data-step_quantity").split(";").map(Number);
    val = parseFloat(input.val());
    input
      .val(goToStepQuantity(val, stepQtty, "-"))
      .trigger("keyup")
      .trigger("change");
  } else {
    newVal = (currentVal - parseFloat(step)).round(2);
    if (!isNaN(currentVal) && newVal > quantityMinNdk)
      input.val(newVal).trigger("keyup").trigger("change");
    else input.val(quantityMinNdk).trigger("keyup").trigger("change");
  }
});

$(document).on("click", ".ndkcfTitle", function (event) {
  //$( this ).toggleClass('opened');
  nextElement = $(this)[0].nextSibling;

  $(nextElement).find(".toggler:eq(0)").trigger("click");
});

$(document).on("click", ".userPanelTitle", function (event) {
  $(".userPanel").toggle();
});

$(document).on(
  "change",
  ".ndk-accessory-quantity[data-step_quantity!='']",
  function (e) {
    if ($(this).attr("data-step_quantity") != "") {
      stepQtty = $(this).attr("data-step_quantity").split(";").map(Number);
      val = parseInt($(this).val());
      if ($.inArray(val, stepQtty) == -1) {
        $(this).val(goToStepQuantity(val, stepQtty, "+")).trigger("keyup");
      }
    }
  }
);

$(document).on("change", ".ndk-accessory-quantity", function (e) {
  setAccessoryCustomization($(this));
  //21.05
  //$(this).trigger("keyup");
});

$(document).on("click", ".trigger-close-fancybox", function (e) {
  e.preventDefault();
  $(".fancybox-close").trigger("click");
});

function setDemoAccessoryBlock() {
  $(".accessory_customization.justforexample").each(function () {
    customizationBlock = $(this);
    customizationBlock
      .find(".required_field")
      .addClass("required_field_back")
      .removeClass("required_field");
    customizationBlock.addClass("demo_a_c_b");
  });
}
function setAccessoryCustomization(input) {
  customizationBlock = $(
    "#accessory_customization_" + input.attr("data-id-value")
  );
  qtty_total = parseInt(input.val());
  id_product = input.attr("data-id-product-accessory");
  id_combination = input.attr("data-id_combination");
  attrName = input.attr("data-attr-lang");
  valueId = input.attr("data-id-value");
  cusCount = customizationBlock.find(".ndkackFieldItem").length;
  //$('.cloned_accessory_customization_'+input.attr('data-id-value')+'_'+id_combination).remove();

  customizationBlock.find("textarea, input, select").addClass("dontSend");

  for (var i = 0; i < qtty_total; i++) {
    if (cusCount > 0) {
      newCustomizationBlock = customizationBlock.clone();
      newCustomizationBlock
        .find(".required_field_back")
        .addClass("required_field")
        .removeClass("required_field_back");
      newCustomizationBlock.removeClass("demo_a_c_b");

      group = newCustomizationBlock
        .find(".ndkackFieldItem:eq(0)")
        .attr("data-field");
      newGroup =
        group +
        "_" +
        input.attr("data-id-value") +
        "_" +
        id_combination +
        "-" +
        i;
      customAllDescendants(newCustomizationBlock, group, newGroup);

      if (window["fieldFonts_" + group]) {
        window["fieldFonts_" + newGroup] = window["fieldFonts_" + group];
        window["fieldColors_" + newGroup] = window["fieldColors_" + group];
        window["fieldSizes_" + newGroup] = window["fieldSizes_" + group];
        window["fieldEffects_" + newGroup] = window["fieldEffects_" + group];
        window["fieldAlignments_" + newGroup] =
          window["fieldAlignments_" + group];
      }

      newCustomizationBlock
        .attr(
          "id",
          "accessory_customization_" +
            input.attr("data-id-value") +
            "_" +
            id_combination +
            "-" +
            i
        )
        .attr(
          "class",
          "col-xs-12 cloned_accessory_customization_" +
            input.attr("data-id-value") +
            "_" +
            id_combination +
            " cloned_accessory_customization_" +
            input.attr("data-id-value") +
            " accessory_customization cloned_accessory_customization clearfix"
        )
        .removeClass("hidden");
      newCustomizationBlock
        .find(".form-group .toggler")
        .removeClass("toggler")
        .addClass("smallToggler");
      newCustomizationBlock.find(".toggleText:eq(0)").remove();

      oldTitle = newCustomizationBlock.find(".toggler_title").text();
      newCustomizationBlock
        .find(".toggler_title")
        .text(oldTitle + " " + attrName);
      newCustomizationBlock.find("textarea, input, select").each(function () {
        if (typeof $(this).attr("name") != "undefined")
          if ($(this).attr("name").indexOf("ndkcsfield") > -1)
            $(this).attr(
              "name",
              $(this).attr("name") +
                "[accessory_customization][" +
                valueId +
                "|" +
                id_product +
                "|" +
                id_combination +
                "|" +
                i +
                "|" +
                attrName +
                "]"
            );

        //ndkcsfield[{$field.id_ndk_customization_field|escape:'intval'}][quantityProd][{$value.id|escape:'intval'}|{$value.id_product_value|escape:'intval'}|{$id_combination}]
      });
      newCustomizationBlock
        .find("textarea, input, select")
        .removeClass("dontSend");
      customizationBlock.parent().prepend(newCustomizationBlock);
      customizationBlock
        .find(".required_field")
        .addClass("required_field_back")
        .removeClass("required_field");
      newCustomizationBlock.find(".fieldPane").slideUp();
    }
  }

  attrBlockCount = $(
    ".cloned_accessory_customization_" +
      input.attr("data-id-value") +
      "_" +
      id_combination
  ).length;
  blockDiff = attrBlockCount - qtty_total;
  //console.log(blockDiff);
  if (blockDiff > 0) {
    for (var i = 0; i < blockDiff; i++) {
      $(
        "#accessory_customization_" +
          input.attr("data-id-value") +
          "_" +
          id_combination +
          "-" +
          i
      ).remove();
    }
  }

  prodBlockCount = $(
    ".cloned_accessory_customization_" + input.attr("data-id-value")
  ).length;
  //console.log(prodBlockCount);
  if (prodBlockCount > 0) {
    $("#t_a_c_" + input.attr("data-id-value")).show();
    if (prodBlockCount > 1)
      customizationBlock
        .parent()
        .find(".cloned_accessory_customization")
        .addClass("col-md-4");
    else
      customizationBlock
        .parent()
        .find(".cloned_accessory_customization")
        .removeClass("col-md-4");
  } else {
    $("#t_a_c_" + input.attr("data-id-value")).hide();
  }

  $(".textzone").each(function () {
    initText($(this));
  });
  lazyLoadInstance.update();
}

function customAllDescendants(node, group, newGroup) {
  node.find("*").each(function () {
    var child = $(this);
    customAllDescendants(child);
    if (typeof $(this).attr("data-group") != "undefined") {
      $(this).attr("data-group", newGroup);
    }
    if (typeof $(this).attr("data-field") != "undefined") {
      $(this).attr("data-field", newGroup);
    }
    if (typeof $(this).attr("id") != "undefined") {
      $(this).attr("id", $(this).attr("id").replace(group, newGroup));
    }
  });
}

$(document).on(
  "keyup",
  ".ndk-accessory-quantity, .ndk-accessory-quantity-block .ndk-accessory-quantity",
  //21.05
  //".ndk-accessory-quantity[value!=0], .ndk-accessory-quantity-block .ndk-accessory-quantity[value!=0]",
  function (e) {
    rootBlock = $(
      ".form-group[data-field='" + $(this).attr("data-group") + "']"
    );
    qttyCheckNode = rootBlock;
    current_row = $(this).attr("data-id-product-accessory");
    //console.log(current_row);
    rowNode = $(
      ".form-group[data-field='" + $(this).attr("data-group") + "']"
    ).find(".accessory-ndk[data-id-product-value='" + current_row + "']");
    //console.log(rowNode)
    max = parseInt(rootBlock.attr("data-qtty-max"));
    if (max == 0) {
      max = parseInt(rowNode.attr("data-qtty-max"));
      //qttyCheckNode = rowNode;
    }

    max_weight = parseFloat(rootBlock.attr("data-weight-max"));
    if ((max_weight = 0)) {
      max_weight = parseInt(rowNode.attr("data-weight-max"));
      //qttyCheckNode = rowNode;
    }

    if (max == 0) max = 9999999999;

    if (max_weight == 0) max_weight = 9999999999;

    min = parseInt(rootBlock.attr("data-qtty-min"));
    if (min == 0) {
      min = parseInt(rowNode.attr("data-qtty-min"));
      //qttyCheckNode = rowNode;
    }

    min_weight = parseFloat(rootBlock.attr("data-weight-min"));
    if (min_weight == 0) {
      min_weight = parseInt(rowNode.attr("data-weight-min"));
      //qttyCheckNode = rowNode;
    }

    qtty_total = 0;
    weight_total = 0;

    rootBlock.find(".quantity_error_up").fadeOut().delay(10000);
    //console.log(min);

    qttyCheckNode.find(".ndk-accessory-quantity[value!=0]").each(function () {
      qtty_total += parseInt($(this).val());
      weight_total +=
        parseInt($(this).val()) * parseFloat($(this).attr("data-weight"));
    });

    weight_total = weight_total.toPrecision(3);
    //console.log(qtty_total);
    parentBlock = rootBlock.find(
      ".accessory-ndk[data-id-product-value='" +
        $(this).attr("data-id-product-accessory") +
        "']"
    );
    parentBlock.addClass("selected-product-accessory");

    id_product = $(this).attr("data-id-product-accessory");
    //$('#attribute_combination_'+id_product).trigger('change');
    //console.log(min);

    rootBlock.find(".total_weight").html(parseFloat(weight_total).toFixed(3));
    rootBlock.find(".total_qtty").html(parseInt(qtty_total));

    if (parseInt(qtty_total) >= parseInt(min)) {
      rootBlock
        .find(".quantity_error_down")
        .removeClass("required_field")
        .fadeOut();
    } else {
      if (min > 0)
        rootBlock.find(".quantity_error_down").addClass("required_field");
    }
    if (parseFloat(weight_total) >= parseFloat(min_weight)) {
      rootBlock
        .find(".weight_error_down")
        .removeClass("required_field")
        .fadeOut();
    } else {
      rootBlock.find(".weight_error_down").addClass("required_field");
    }

    cancelFieldRestrictions(
      $(this).attr("data-id-value"),
      $(this).attr("data-group")
    );

    if (parseInt(qtty_total) > parseInt(max)) {
      rootBlock.find(".quantity_error_up").show();
      $(this).parent().find(".quantity-ndk-minus").trigger("click");
    } else if (parseFloat(weight_total) > parseFloat(max_weight)) {
      rootBlock.find(".weight_error_up").show();
      $(this).parent().find(".quantity-ndk-minus").trigger("click");
    } else {
      //rootBlock.find('.quantity_error_up').hide();
      qtty = parseInt(this.value);
      /*if($(this).hasClass('ndk-accessory-comb-tab')){
			qtty = 0;
			otherInputs = $(this).parent().parent().parent().find('.ndk-accessory-comb-tab');
			otherInputs.each(function(){
				qtty += parseInt($(this).val());
			});
			//console.log(qtty);
		}*/

      var input = $(this);
      unitPrice = input.attr("data-price");
      group = input.attr("data-group");
      price = qtty * unitPrice;
      valueId = input.attr("data-value-id");

      name = $(this).attr("data-value");
      if ($(this).val() == 0) {
        //$('.disabled_value_by_'+group).removeClass('disabled_value_by_'+group).addClass('enabled_value_by_'+group);
        parentBlock.removeClass("selected-product-accessory");
      } else {
        checkFieldRestrictions(
          $(this).attr("data-id-value") + "[" + qtty + "]",
          group
        );
      }

      //$(this).parent().parent().find('.ndk_attribute_select').trigger('change');

      id_product = $(this).attr("data-id-product-accessory");
      id_combination = $(this).attr("data-id_combination");

      group = input.attr("data-group");
      if (input.hasClass("ndk-accessory-comb-tab")) {
        qtty_total = 0;
        //parentBlock = $(this).parent().parent().parent().parent().parent();
        parentBlock = $(".form-group[data-field='" + group + "']");
        if (input.hasClass("ndk-accessory-comb-tab"))
          targets =
            ".ndk-accessory-quantity[value!=0][data-id-value=" +
            input.attr("data-id-value") +
            "]";
        else targets = ".ndk-accessory-quantity[value!=0]";
        parentBlock.find(targets).each(function () {
          qtty_total += parseInt($(this).val());
        });

        $("#ndkcf_totalprod_quantity_" + input.attr("data-id-value"))
          .val(qtty_total)
          .trigger("change");

        attrBlock = input.parent().parent().parent();
        qtty_total_attr = 0;
        attrBlock.find(".ndk-accessory-quantity[value!=0]").each(function () {
          qtty_total_attr += parseInt($(this).val());
        });
        attrBlock.parent().find(".color_counter").html(qtty_total_attr);
        attrBlock
          .parent()
          .find(".ndkcf_col_action")
          .attr("qtty_total_attr", qtty_total_attr);
        //.trigger("click");

        qtty = qtty_total;
        //qtty = input.val();
        //on cherche s'il y a des champs custom
        //setAccessoryCustomization(input, input.val());
        //FIN on cherche s'il y a des champs custom
      } else {
        qtty = $(this).val();
      }

      if ($(this).val() == 0) {
        updatePriceNdk(0, valueId);
      } else {
        if (!input.hasClass("price_overrided_accessory")) {
          if (qtty > 0 && id_combination > 0)
            loadAccessoryAttrPrice(
              id_product,
              id_combination,
              qtty,
              input,
              true,
              qtty_total
            );
          else
            loadAccessoryAttrPrice(
              id_product,
              id_combination,
              qtty,
              input,
              true,
              qtty_total
            );
          //updatePriceNdk(price, valueId);
        } else {
          loadAccessoryAttrPrice(
            id_product,
            id_combination,
            qtty,
            input,
            false,
            qtty_total
          );
          qttyDiscount = getPriceDiscount(
            $(this).attr("data-group"),
            $(this).attr("data-id-value"),
            qtty
          );
          price -= qttyDiscount;
          updatePriceNdk(price, valueId);
        }
      }
    }
    var input = $(this);
    if (displayPriceHT == 1 && qtty > 0) {
      $(".final_price_" + input.attr("data-id-value") + " .priceht").remove();
      $(".final_price_" + input.attr("data-id-value")).append(
        '<span class="priceht clear clearfix"></span>'
      );
      getPriceHt(
        input.attr("data-price"),
        ".final_price_" + input.attr("data-id-value") + " .priceht"
      );
    }

    //console.log(qtty_total);
    if (this.value > 0)
      getSubValues(
        $(this).attr("data-id-value"),
        group,
        input.parent().parent().parent()
      );
    else {
      $.when(
        $("#sub-" + group + "-" + $(this).attr("data-id-value"))
          .find(".ndk-accessory-quantity")
          .val(0)
          .trigger("keyup")
      ).done(function () {
        $("#sub-" + group + "-" + $(this).attr("data-id-value")).remove();
      });
    }
  }
);

function getPriceDiscount(group, value, qtty) {
  if (typeof getPriceDiscount_Override == "function") {
    return getPriceDiscount_Override(group, value, qtty);
  }
  qtty_wanted = $("#quantity_wanted").val();
  discount = 0;
  if (typeof ndkSpecificPrices[group] != "undefined") {
    for (i = 0; i < ndkSpecificPrices[group].length; i++) {
      row = ndkSpecificPrices[group][i];
      if (
        row.id_ndk_customization_field_value == value &&
        qtty * qtty_wanted >= row.from_quantity
      ) {
        if (row.reduction_type == "amount") {
          mydiscount = parseFloat(row.reduction);
        } else if (row.reduction_type == "percent") {
          mydiscount = price * (row.reduction / 100);
        }
        if (mydiscount > discount) discount = mydiscount;
      }
    }
  }
  return discount;
}

$(document).on("change", ".ndk-checkbox", function (e) {
  qtty = 1;
  unitPrice = $(this).attr("data-price");
  group = $(this).attr("data-group");
  price = qtty * unitPrice;
  valueId = $(this).attr("data-id-value");
  name = $(this).attr("data-value");

  rootBlock = $(".form-group[data-field='" + group + "']");
  max = parseInt(rootBlock.attr("data-qtty-max"));
  min = parseInt(rootBlock.attr("data-qtty-min"));
  rootBlock.find(".quantity_error_up").fadeOut().delay(10000);
  others = rootBlock.find(".ndk-checkbox:checked").length;

  if (parseInt(others) >= parseInt(min)) {
    rootBlock
      .find(".quantity_error_down")
      .removeClass("required_field")
      .fadeOut();
  } else {
    if (min > 0)
      rootBlock.find(".quantity_error_down").addClass("required_field");
  }

  if (parseInt(others) > parseInt(max) && parseInt(max) > 0) {
    rootBlock.find(".quantity_error_up").show();
    $(this).trigger("click");
  }

  //console.log(others)

  if (!$(this).is(":checked")) {
    $(".disabled_value_by_" + group)
      .removeClass("disabled_value_by_" + group)
      .addClass("enabled_value_by_" + group);
    updatePriceNdk(0, valueId);
    $(".recap_item_checkbox_" + valueId.replace("-", "_")).remove();
    if (
      $(".form-group[data-field='" + group + "']").find(".ndk-checkbox:checked")
        .length < 1
    ) {
      $(".recap_group_" + group).html("");
    }
    $("body").trigger({
      type: "ndkacf:ndkCheckboxSet",
      group: $(this).attr("data-group"),
      value: $(this).attr("data-id-value"),
      tax_ratio: 1,
      force_tax_rule: false,
      force_carrier: false,
    });
  } else {
    checkFieldRestrictions($(this).attr("data-value-id"), group);
    updatePriceNdk(price, valueId);
    $("body").trigger({
      type: "ndkacf:ndkCheckboxSet",
      group: $(this).attr("data-group"),
      value: $(this).attr("data-id-value"),
      tax_ratio: $(this).attr("data-tax_ratio"),
      force_tax_rule: $(this).attr("data-force_tax_rule"),
      force_carrier: $(this).attr("data-force_carrier"),
    });
  }
});

$(document).on("click", ".img-value", function () {
  var group = $(this).attr("data-group");
  price = $(this).attr("data-price");
  updatePriceNdk(price, group);
  if ($(this).attr("data-quantity-available") != "null")
    updateQuantityForValue($(this).attr("data-quantity-available"), group);
  setImgValue($(this), group);
  $(".form-group[data-field=" + group + "]")
    .find(".remove-img-item")
    .show();
  applyTypeValue($(this));
});

function applyTypeValue(el) {
  var group = el.attr("data-group");
  type = el.parent().attr("data-type");
  if (typeof type != "undefined" && type != "undefined") {
    $(
      "#item-" +
        group +
        ' .group-type[data-group-type="' +
        type +
        '"] .group-value'
    ).html(" : " + el.attr("title"));
    $(
      "#main-" +
        group +
        ' .group-type[data-group-type="' +
        type +
        '"] .group-value'
    ).html(" : " + el.attr("title"));
    $(
      "#item-" +
        group +
        ' .group-type[data-group-type="' +
        type +
        '"] .group_value_input'
    ).val(el.attr("title"));
    $(
      "#main-" +
        group +
        ' .group-type[data-group-type="' +
        type +
        '"] .group_value_input'
    ).val(el.attr("title"));
    el.trigger("typeValueSet");
  }
}
function strpos(haystack, needle, offset) {
  var i = (haystack + "").indexOf(needle, offset || 0);
  return i === -1 ? false : i;
}

function checkEmptyForm(event) {
  if (typeof checkEmptyForm_Override == "function") {
    return checkEmptyForm_Override(event);
  }
  event = event || false;
  emptyForm = true;
  $i = 0;
  $("*[name^='ndkcsfield[']").each(function () {
    rootBlock = "";
    if (
      $(this).hasClass("quantity_error_down") ||
      $(this).hasClass("quantity_error_up")
    ) {
      group = $(this).parent().parent().parent().attr("data-field");
      val = $(this).attr("val");
    } else {
      if (typeof $(this).attr("data-name") != "undefined")
        group = $(this).attr("data-name").split("ndkcsfield[");
      else group = $(this).attr("name").split("ndkcsfield[");

      group = group[1].split("]");
      group = group[0];

      val = $(this).val();
    }

    rootBlock = $(
      ".form-group[data-field='" + group + "']:not(.submitContainer)"
    );
    if ($(this).is(":radio")) {
      $k = 1;
      others = rootBlock.find('input[type="radio"]');
      if ($(this).is(":checked")) {
        for (var j = 0; j < others.length; j++) {
          $i++;
          $k++;
        }
      }
    } else if ($(this).is(":checkbox")) {
      $k = 0;
      group = $(this).attr("data-group");
      others = rootBlock.find('input[type="checkbox"]');
      if ($(this).is(":checked")) {
        for (var j = 0; j < others.length; j++) {
          $i++;
          $k++;
        }
      }
    } else {
      if (val == "" || val.slice(-2) == ": ") {
      } else {
        $i++;
      }
    }
  });
  //console.log($i);

  if ($i == 0) {
    if (event) event.preventDefault();

    $(
      "#add-to-cart-or-refresh .add-to-cart:not(.falseButton), #add_to_cart .exclusive:not(.falseButton), #add_to_cart button:not(.falseButton)"
    )
      .prop("disabled", false)
      .trigger("click");

    return false;
  } else {
    return true;
  }
}
var blockAlert = [];
function ndkMakeAlert(rootBlock) {
  if (typeof ndkMakeAlert_Override == "function") {
    return ndkMakeAlert_Override(rootBlock);
  }
  blockAlert.push(rootBlock);
  rootBlock.addClass("focusRequired").addClass("focusRequired").focus();
  rootBlock.find(".error").remove();
}

$(document).on("ndkacf:submitFormChecked", function (event) {
  if (blockAlert.length > 0) {
    if (makeSlide == 1) {
      ndkCfShowSlide(blockAlert[0]);
    }
    scrollToNdk(blockAlert[0], 800, true);
    displayNdkacfAlerts();
  }
});

$(document).on("click", ".close-alert-ndkacf", function () {
  me = $(this).parent();
  $(me).fadeOut("slow", function () {
    $(me).remove();
  });
});
$(document).on("click", ".alert-ndkacf", function (e) {
  if (!$(e.target).hasClass("close-alert-ndkacf")) {
    target = $('.form-group[data-field="' + $(this).attr("data-field") + '"]');
    if (makeSlide == 1) {
      ndkCfShowSlide(target);
    }
    scrollToNdk(target, 800, true);
  }
});

function displayNdkacfAlerts() {
  $(".bloc-alert-ndkacf").remove();
  $("body").append('<div class="bloc-alert-ndkacf" ></div>');
  $(blockAlert).each(function () {
    me = $(this);
    required_field_html = me[0];
    required_field_name = $(required_field_html).data("name");
    required_field_id_field = $(required_field_html).data("field");
    required_field_mesage = $(required_field_html)
      .find(".alert-danger:visible")
      .html();

    alert_ndkacf =
      '<div data-field="' +
      required_field_id_field +
      '" class="alert-ndkacf focusRequired"><span> ' +
      required_field_name +
      '</span><br/><i class="small">' +
      required_field_mesage +
      '</i><span class="material-icons pull-right close-alert-ndkacf">close</span></div>';
    $(".bloc-alert-ndkacf").append(alert_ndkacf);
  });
}

$.fn.ndkSubmit = function (event) {
  blockAlert = [];
  ndkChecked = [];
  if (typeof $.fn.ndkSubmit_Override == "function") {
    return $.fn.ndkSubmit_Override(event);
  }
  if (checked) {
    var checked = false;
    return true;
  }
  /* 
    call : $('#form').ndkSubmit(event);
    stop form from submitting normally */
  $("html, body").animate({ scrollTop: 0 }, "fast");
  var $form = $(this);
  var required = $(".form-group:not([class*='disabled_value_by'])").find(
    ".required_field"
  );

  /*check required fields*/
  $i = 0;
  required.each(function () {
    rootBlock = "";
    if (
      $(this).hasClass("quantity_error_down") ||
      $(this).hasClass("quantity_error_up")
    ) {
      group = $(this).parent().parent().parent().attr("data-field");
      val = $(this).attr("val");
    } else if ($(this).hasClass("group_value_input")) {
      group = $(this).attr("data-group");
      val = $(this).val();
    } else {
      if (typeof $(this).attr("data-name") != "undefined")
        group = $(this).attr("data-name").split("ndkcsfield[");
      else group = $(this).attr("name").split("ndkcsfield[");

      group = group[1].split("]");
      group = group[0];

      val = $(this).val();
    }

    rootBlock = $(
      ".form-group[data-field='" + group + "']:not(.submitContainer)"
    );
    if ($(this).is(":radio")) {
      $k = 1;
      rootBlock.find(".error").remove();
      others = rootBlock.find('input[type="radio"]');
      if ($(this).is(":checked")) {
        for (var j = 0; j < others.length; j++) {
          $i += 1;
          $k += 1;
        }
        rootBlock.removeClass("focusRequired");
        rootBlock.find(".error").remove();
      } else {
        c = rootBlock.find('input[type="radio"]:checked').length;
        if (c < 1) {
          if ($.inArray(group, ndkChecked) == -1) {
            ndkMakeAlert(rootBlock);
            ndkChecked.push(group);
          }

          if (rootBlock.parent().hasClass("groupFieldBlock")) {
            targetButtonPack = $(
              ".toggleGroupField[target='#" +
                rootBlock.parent().attr("id") +
                "']"
            );
            targetButtonPack.trigger("click");
          }

          if (
            typeof $(this).attr("data-message") != "undefined" &&
            $(this).attr("data-message") != "undefined"
          )
            rootBlock
              .find(".fieldPane")
              .append(
                '<span class="error alert-danger clear clearfix">' +
                  $(this).attr("data-message") +
                  "</span>"
              );
        } else {
          rootBlock.removeClass("focusRequired");
          rootBlock.find(".error").remove();
        }
      }
    } else if ($(this).is(":checkbox")) {
      $k = 0;
      group = $(this).attr("data-group");
      rootBlock.find(".error").remove();
      others = rootBlock.find('input[type="checkbox"]');
      if ($(this).is(":checked")) {
        for (var j = 0; j < others.length; j++) {
          $i++;
          $k++;
        }
        rootBlock.removeClass("focusRequired");
        rootBlock.find(".error").remove();
      } else {
        if ($k < others.length) {
          if ($.inArray(group, ndkChecked) == -1) {
            ndkMakeAlert(rootBlock);
            ndkChecked.push(group);
          }

          if (rootBlock.parent().hasClass("groupFieldBlock")) {
            targetButtonPack = $(
              ".toggleGroupField[target='#" +
                rootBlock.parent().attr("id") +
                "']"
            );
            targetButtonPack.trigger("click");
          }

          if (
            typeof $(this).attr("data-message") != "undefined" &&
            $(this).attr("data-message") != "undefined"
          )
            rootBlock
              .find(".fieldPane")
              .append(
                '<span class="error alert-danger clear clearfix">' +
                  $(this).attr("data-message") +
                  "</span>"
              );
        }
      }
    } else {
      if (val == "" || val.slice(-2) == ": ") {
        //$(this).parent().find('span').css('color', 'red').focus();
        rootBlock.find(".fieldPane").show().find(".error").remove();
        $(".view_tab[data-view='" + rootBlock.attr("data-view") + "']").trigger(
          "click"
        );
        if ($.inArray(group, ndkChecked) == -1) {
          ndkMakeAlert(rootBlock);
          ndkChecked.push(group);
        }

        if (rootBlock.parent().hasClass("groupFieldBlock")) {
          targetButtonPack = $(
            ".toggleGroupField[target='#" + rootBlock.parent().attr("id") + "']"
          );
          targetButtonPack.trigger("click");
        }

        if (
          typeof $(this).attr("data-message") != "undefined" &&
          $(this).attr("data-message") != "undefined"
        )
          rootBlock
            .find(".fieldPane")
            .append(
              '<span class="error alert-danger clear clearfix">' +
                $(this).attr("data-message") +
                "</span>"
            );
        if ($(this).is("p")) $(this).show();

        if (rootBlock.parent().parent().hasClass("ac_container")) {
          $("#" + rootBlock.parent().parent().attr("data-button-id")).trigger(
            "click"
          );
          rootBlock.parent().parent().find(".fieldPane").show();
        }
      } else {
        $i++;
        rootBlock.removeClass("focusRequired");
        rootBlock.find(".error").remove();
      }
    }
  });
  //console.log($i);
  //console.log(required.length);
  $(document).trigger({
    type: "ndkacf:submitFormChecked",
    complete: $i >= required.length,
  });

  if ($i >= required.length) {
    if (checked) {
      $(".popup_required").removeClass("focusOnMe");
      return checkEmptyForm(event);
    } else {
      checkRecommends($form, event);
    }
  } else {
    event.preventDefault();
    checked = false;
    if (!$("#ndkacf-modal").is(":visible"))
      $(".btn-ndkacf-popup").trigger("click");
    $(".popup_required").addClass("focusOnMe");

    return false;
  }
};

$(document).on("click", ".popup_required", function () {
  $('[data-target="#ndkacf-modal"]').trigger("click");
});

function setRecommends() {
  if (typeof setRecommends_Override == "function") {
    return setRecommends_Override();
  }
  for (var i = 0; i < recommended.length; i++) {
    //$('#ndkcsfield_'+recommended[i]).addClass('recommended_field');
    $(".form-group[data-field='" + recommended[i] + "']").addClass(
      "recommended_field"
    );
    $(".form-group[data-field='" + recommended[i] + "']")
      .find(
        "input:not([name*='ndkcsfieldPdf']):not('.noborder'),textarea:not([name*='ndkcsfieldPdf']), select:not(.ndk_tag_selector)"
      )
      .attr("data-group-recommend", recommended[i]);
  }
}

function checkRecommends($form, event) {
  if (typeof checkRecommends_Override == "function") {
    return checkRecommends_Override($form, event);
  }

  recommend_list = [];
  var required = $(
    ".form-group.recommended_field:not([class*='disabled_value_by'])"
  ).find(
    "input:not(.ndk_tag_selector, .dontCare),textarea:not(.ndk_tag_selector), select:not(.ndk_tag_selector, .ndk_attribute_select)"
  );

  /*check required fields*/
  $i = 0;
  required.each(function () {
    group = $(this).attr("data-group-recommend");
    val = $(this).val();
    rootBlock = $(".form-group[data-field='" + group + "']");
    groupTitleEl = rootBlock.find("label:eq(0)").clone();
    groupTitleEl.find(".toggleText").remove();
    groupTitleEl.find(".tooltipDescription").remove();
    groupTitle = groupTitleEl.text();

    if (
      typeof group == "undefined" ||
      $(this).parents().is(".tag-selector-container")
    ) {
      $i++;
    } else if ($(this).is(":radio")) {
      $k = 1;
      others = rootBlock.find('input[type="radio"]');
      //console.log(others.length);
      if ($(this).is(":checked")) {
        for (var j = 0; j < others.length; j++) {
          $i++;
          $k++;
        }
        //recommend_list.push($(this).parent().parent().parent().find('label:eq(0)').text());
      } else {
        if ($k < others.length) {
          recommend_list[group] = groupTitle;
        }
      }
    } else if ($(this).is(":checkbox")) {
      $k = 0;
      others = rootBlock.find('input[type="checkbox"]');
      console.log(others.length);
      if ($(this).is(":checked")) {
        for (var j = 0; j < others.length; j++) {
          $i++;
          $k++;
        }
        //recommend_list.push($(this).parent().parent().parent().find('label:eq(0)').text());
      } else {
        if ($k < others.length) {
          recommend_list[group] = groupTitle;
        }
      }
    } else if ($(this).is(".ndk-accessory-quantity")) {
      $k = 0;
      others = rootBlock.find(".ndk-accessory-quantity[value!=0]");
      console.log(others.length);
      if (parseFloat(val) > 0 || others.length > 0) {
        for (var j = 0; j < others.length; j++) {
          $i++;
          $k++;
        }
        //recommend_list.push($(this).parent().parent().parent().find('label:eq(0)').text());
      } else {
        if ($k <= others.length) {
          recommend_list[group] = groupTitle;
        }
      }
    } else {
      if (val == "") {
        recommend_list[group] = groupTitle;
      } else {
        $i++;
      }
    }
  });

  if ($i >= required.length) {
    checked = true;
    return checkEmptyForm(event);
  } else {
    event.preventDefault();
    ret = false;

    $("#recommends_list").html("");
    //recommend_list = $.unique(recommend_list);

    //console.log(recommend_list);
    var idGroupR;
    for (idGroupR in recommend_list) {
      if (recommend_list[idGroupR] != "")
        $("#recommends_list").append(
          '<li id="showRecommendItem_' +
            idGroupR +
            '"><span class="showRecommendItem  btn-default btn-primary">' +
            recommend_list[idGroupR] +
            "</span></li>"
        );
    }
    /*for(var i = 0;i < recommend_list.length;i++){
            $('#recommends_list').append('<li>'+recommend_list[i]+'</li>');
      }*/

    $.fancybox("#confirm_recommends", {
      modal: true,

      afterShow: function () {
        // $(".confirm_recommends").on("click", function (event) {
        //
        // });
      },
      afterClose: function () {},
    });
  }
}

$(document).on("click", ".confirm_recommends", function (event) {
  event.preventDefault();
  if ($(event.target).is(".yes")) {
    checked = true;
    if (checkEmptyForm(event)) $("#ndkcsfields").trigger("submit");
    $.fancybox.close();
  } else {
    checked = false;
    $.fancybox.close();
    $(".btn-ndkacf-popup").trigger("click");
  }
});

$(document).on("click", ".showRecommendItem", function () {
  isItem = false;
  el = $(this).parent();
  group = el.attr("id").replace("showRecommendItem_", "");
  if (group.indexOf("-") > -1) {
    isItem = true;
    number = group.split("-")[1];
    itemGroup = group.split("-")[0];
    group = itemGroup;
  }
  if (!$("#ndkacf-modal").is(":visible"))
    $(".btn-ndkacf-popup").trigger("click");

  $("#ndkcsfields .toggler").removeClass("active");
  if (parseInt(letOpen) == 0) $("#ndkcsfields .fieldPane").hide();

  formGroup = $(".form-group[data-field='" + group + "']");
  id_product_pack = formGroup.parent().attr("id");

  if (formGroup.parent().hasClass("groupFieldBlock")) {
    targetButtonPack = $(
      ".toggleGroupField.closed[target='#" +
        formGroup.parent().attr("id") +
        "']"
    );
    targetButtonPack.trigger("click");
  }
  $(
    ".view_tab[data-view='" +
      formGroup.attr("data-view") +
      "']:not(.activeView)"
  ).trigger("click");
  scrollToNdk(formGroup, 800, true);
  if (makeSlide == 1) {
    $(".sliderBlock .ndkackFieldItem").removeClass("activeItem");
    formGroup.addClass("activeItem");
  }
  $.fancybox.close();
  checked = false;
  formGroup.find(".toggler:not(.active)").trigger("click");
  scrollToNdk(formGroup.find(".toggler"), 800, true);

  // if (isItem) {
  //   scrollToNdk(
  //     formGroup.find(".designer-item[data-number='" + number + "']"),
  //     800,
  //     true
  //   );
  //   $(".designer-item[data-number='" + number + "']").show();
  // }
});

function checkCustomizations() {
  if (typeof checkCustomizations_Override == "function") {
    return checkCustomizations_Override();
  }
  return true;
}

function resizeInput() {
  if (typeof resizeInput_Override == "function") {
    return resizeInput_Override();
  }
  if ($(this).attr("id") != "search_query_top") {
    $(this).attr("size", $(this).val().length + 5);
    $(this).parent().css("width", $(this).innerWidth);
  }
}

var ghoape = function getHeightOfAbsolutelyPositionedElement(element) {
  if (typeof getHeightOfAbsolutelyPositionedElement_Override == "function") {
    return getHeightOfAbsolutelyPositionedElement_Override(element);
  }

  var max_y = 0;
  var max_x = 0;
  var dimensions = [];
  $.each($(element).find("*"), function (idx, desc) {
    max_y = Math.max(max_y, $(desc).offset().top + $(desc).height());
    max_x = Math.max(max_x, $(desc).offset().left + $(desc).width());
  });
  dimensions["y"] = max_y - $(element).offset().top;
  dimensions["x"] = max_x - $(element).offset().left;
  $(element)
    .css("width", dimensions["x"])
    .css("height", dimensions["y"])
    .css("position", "unset");
  return dimensions;
};

function preload(arrayOfImages) {
  if (typeof preload_Override == "function") {
    return preload_Override(arrayOfImages);
  }
  $(arrayOfImages).each(function () {
    $("<img/>")[0].src = this;
  });
}

$(document).on("click", ".colorize_svg li", function () {
  root = $(this).parent().parent().parent().parent().parent();
  color = $(this).find("span").text();
  $(this).parent().find("li").removeClass("selected");
  $(this).addClass("selected");
  $(this).parent().parent().find(".index-value").html($(this).html());
  root.find(".replaced-svg").each(function () {
    $(this).attr("fill", color);
    var target = $(this).parent();
    if ($(this).parent().hasClass("selected-svg")) {
      $.when(target.parent().parent().trigger("click")).done(function () {
        target.trigger("click");
      });
    }
  });
});

$.fn.setNdkSelector = function () {
  visible = false;
  var ul = $(this).find("ul");
  var firstLi = ul.find("li").first();
  if ($(this).find(".index-value").length == 0)
    $(this).prepend('<div class="index-value"></div>');

  var selectedLi = ul.find("li.selected");
  if (selectedLi.length) {
    //index.remove();
    $(this).find(".index-value").html(selectedLi.html());
  } else {
    $(this).find(".index-value").html(firstLi.html());
  }

  if (ul.find("li").length < 2) $(this).parent().hide();

  ul.hide();
  $(this).click(function () {
    if (visible) return;

    ul.show("fast", function () {
      visible = true;
    });

    var selectedLi = ul.find("li.selected");
    if (selectedLi.length) {
      //index.remove();
      $(this).find(".index-value").html(selectedLi.html());
    }
  });
  $("html").click(function () {
    if (visible) {
      $(".ndk_selector")
        .find("ul")
        .hide("fast", function () {
          visible = false;
        });
    }
  });
};

function setTags_Override__(parent) {}
function setTags(parent) {
  parent = parent || $("body");
  if (typeof setTags_Override == "function") {
    return setTags_Override(parent);
  }
  var tag_type = false;
  if (
    typeof parent.attr("data-group-type") != "undefined" &&
    !parent.is("body")
  )
    tag_type = parent.attr("data-group-type");

  if (parent.is("body")) tag_type = false;
  //console.log(tag_type)
  parent.find(".tag-selector-container").remove();
  var filtersTags = [];
  parent.find(".tagged").each(function () {
    fullClass = $(this).attr("class");
    $(this).attr(
      "class",
      "filterTag " +
        fullClass
          .replace(/[!\"#$%&'\(\)\*\+,\.\/:;<=>\?\@\[\\\]\^`\{\|\}~]/g, "")
          .toLowerCase()
    );

    tags = $(this).attr("data-tags");
    tags = tags.split("|");
    //console.log(tags);
    rootBlock = $(this).attr("data-group");
    for (var i = 0; i < tags.length; i++) {
      if ($.inArray(tags[i] + "|" + rootBlock, filtersTags) == -1) {
        filtersTags.push(tags[i] + "|" + rootBlock);
      }
    }
  });

  //on créé le block de tags
  encountred = [];
  for (var i = 0; i < filtersTags.length; i++) {
    value = filtersTags[i].split("|");
    if ($.inArray(value[1], encountred) == -1) {
      if (parent.is("body"))
        mySelector = $("#main-" + value[1]).find(".visu-tools");
      else mySelector = parent.find(".groupList, .visu-tools");

      mySelector.prepend(
        '<div id="tag-selector-container-' +
          value[1] +
          (tag_type ? "-" + tag_type + '"' : "") +
          '" class="tag-selector-container"><p class="clear clearfix" style="display:none"><label>' +
          filterText +
          '</label></p><select data-placeholder="' +
          filterText +
          '" data-group-target="' +
          value[1] +
          '" id="tag-select-' +
          value[1] +
          (tag_type ? "-" + tag_type + '"' : "") +
          '"' +
          (tag_type ? 'data-group-type="' + tag_type + '"' : "") +
          ' class=" ndk_tag_selector" multiple="multiple"><option selected="selected" disabled>' +
          filterText +
          '</option><option value="all">' +
          allText +
          "</option></select></div>"
      );
      encountred.push(value[1]);
    }
  }
  //console.log(filtersTags)
  for (var i = 0; i < filtersTags.length; i++) {
    value = filtersTags[i].split("|");
    properValue = value[0].replace(
      /[!\"#$%&'\(\)\*\+,\.\/:;<=>\?\@\[\\\]\^`\{\|\}~]/g,
      ""
    );
    parent
      .find("#tag-select-" + value[1] + (tag_type ? "-" + tag_type : ""))
      .append(
        '<option value="' +
          properValue.replace(/ /g, "-").toLowerCase() +
          '">' +
          value[0] +
          "</option>"
      );
  }

  parent.find(".ndk_tag_selector").each(function () {
    group = $(this).attr("data-group-target");
    if ($(this).find("option").length < 5) {
      $(
        "#tag-selector-container-" + group + (tag_type ? "-" + tag_type : "")
      ).remove();
    }
  });

  parent.find(".ndk_tag_selector").chosen("destroy");
  parent.find(".ndk_tag_selector").chosen({
    disable_search_threshold: 10,
  });

  $(document).on("change", ".ndk_tag_selector", function () {
    group = $(this).attr("data-group-target");
    val = $(this).val();
    if (val === undefined || val === null || val == "") {
      val = ["all"];
      //$(this).val('all').trigger('change')
    }
    if ($(".form-group[data-field='" + group + "']").length > 0) {
      containerTarget = $(".form-group[data-field='" + group + "']");
    } else {
      containerTarget = $(this).parent().parent().parent().parent();
    }

    containerTarget.find(".filterTag").hide();
    for (var i = 0; i < val.length; i++) {
      if (val[i] == "all") {
        containerTarget.find(".filterTag").show();
      } else {
        containerTarget.find("." + val[i]).show();
      }
    }
  });
}

equalheightNdkcf = function (container, parentRef) {
  parentRef = parentRef || ".product-miniature";
  var currentTallest = [],
    currentRowStart = 0,
    rowDivs = [],
    $el,
    topPosition = 0;
  $(container + ":visible").height("");
  $(container + ":visible").each(function () {
    $el = $(this);
    topPostion = parseInt($el.position().top);
    if (isNaN(currentTallest[topPostion])) currentTallest[topPostion] = 0;
    if (typeof rowDivs[topPostion] == "undefined")
      rowDivs[topPostion] = new Array();
    rowDivs[topPostion].push($el);
    currentTallest[topPostion] =
      parseFloat(currentTallest[topPostion]) < $el.height()
        ? parseFloat($el.height())
        : parseFloat(currentTallest[topPostion]);

    for (
      currentDiv = 0;
      currentDiv < rowDivs[topPostion].length;
      currentDiv++
    ) {
      rowDivs[topPostion][currentDiv].height(
        parseFloat(currentTallest[topPostion])
      );
    }
  });
  //$(container + ":space").addClass("ndkEmptyNode");
};

equalheightNdkcfbyRow = function (container) {
  var currentTallest = 0,
    currentRowStart = 0,
    rowDivs = new Array(),
    $el,
    topPosition = 0;
  $(container).each(function () {
    $el = $(this);
    $($el).height("auto");
    topPostion = $el.position().top;
    topPositionParent = $el.parent().parent().position().top;

    if (currentRowStart != topPostion) {
      for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
        rowDivs[currentDiv].height(currentTallest);
      }
      rowDivs.length = 0; // empty the array
      currentRowStart = topPostion;
      currentTallest = $el.height();
      rowDivs.push($el);
    } else if (currentRowStart != topPositionParent) {
      for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
        rowDivs[currentDiv].height(currentTallest);
      }
      rowDivs.length = 0; // empty the array
      currentRowStart = topPositionParent;
      currentTallest = $el.height();
      rowDivs.push($el);
    } else {
      rowDivs.push($el);
      currentTallest =
        currentTallest < $el.height() ? $el.height() : currentTallest;
    }
    for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
      rowDivs[currentDiv].height(currentTallest);
    }
  });
};

$(document).on("click", ".ndkcfLoaded .color_pick", function (e) {
  //updatePriceAttrNdk();
  updatePriceNdkGeneric();
  update_price_dynamic(0);
  snapShotLight();
});

/*$(document).on('change', '.our_price_display', function(){
	//updatePriceAttrNdk();
	updatePriceNdkGeneric();
	update_price_dynamic(0);
});*/

$(document).on("change", ".ndkcfLoaded .attribute_select", function () {
  //updatePriceAttrNdk();
  updatePriceNdkGeneric();
  update_price_dynamic(0);
  updateDisplayAttrNdk();
});

$(document).on("click", ".ndkcfLoaded .attribute_radio", function () {
  //updatePriceAttrNdk();
  updatePriceNdkGeneric();
  update_price_dynamic(0);
  updateDisplayAttrNdk();
});

function updatePriceAttrNdk() {
  if (typeof updatePriceAttrNdk_Override == "function") {
    return updatePriceAttrNdk_Override();
  }
  // Get combination prices
  var combID = $("#idCombination").val();
  if (typeof combinationsFromController == "undefined") return;
  var combination = combinationsFromController[combID];
  if (typeof combination == "undefined") return;

  // Set product (not the combination) base price
  var basePriceWithoutTax = +productPriceTaxExcluded;
  var basePriceWithTax = +productPriceTaxIncluded;
  var priceWithGroupReductionWithoutTax = 0;

  priceWithGroupReductionWithoutTax =
    basePriceWithoutTax * (1 - groupReduction);

  // Apply combination price impact (only if there is no specific price)
  // 0 by default, +x if price is inscreased, -x if price is decreased
  basePriceWithoutTax = basePriceWithoutTax + +combination.price;
  basePriceWithTax =
    basePriceWithTax + +combination.price * (taxRate / 100 + 1);

  // If a specific price redefine the combination base price
  if (combination.specific_price && combination.specific_price.price > 0) {
    basePriceWithoutTax = +combination.specific_price.price;
    basePriceWithTax = +combination.specific_price.price * (taxRate / 100 + 1);
  }

  var priceWithDiscountsWithoutTax = basePriceWithoutTax;
  var priceWithDiscountsWithTax = basePriceWithTax;

  if (default_eco_tax) {
    // combination.ecotax doesn't modify the price but only the display
    priceWithDiscountsWithoutTax =
      priceWithDiscountsWithoutTax +
      default_eco_tax * (1 + ecotaxTax_rate / 100);
    priceWithDiscountsWithTax =
      priceWithDiscountsWithTax + default_eco_tax * (1 + ecotaxTax_rate / 100);
    basePriceWithTax =
      basePriceWithTax + default_eco_tax * (1 + ecotaxTax_rate / 100);
    basePriceWithoutTax =
      basePriceWithoutTax + default_eco_tax * (1 + ecotaxTax_rate / 100);
  }

  // Apply specific price (discount)
  // We only apply percentage discount and discount amount given before tax
  // Specific price give after tax will be handled after taxes are added
  if (combination.specific_price && combination.specific_price.reduction > 0) {
    if (combination.specific_price.reduction_type == "amount") {
      if (
        typeof combination.specific_price.reduction_tax !== "undefined" &&
        combination.specific_price.reduction_tax === "0"
      ) {
        var reduction = combination.specific_price.reduction;
        if (combination.specific_price.id_currency == 0)
          reduction = reduction * currencyRate * (1 - groupReduction);
        priceWithDiscountsWithoutTax -= reduction;
        priceWithDiscountsWithTax -= reduction * (taxRate / 100 + 1);
      }
    } else if (combination.specific_price.reduction_type == "percentage") {
      priceWithDiscountsWithoutTax =
        priceWithDiscountsWithoutTax *
        (1 - +combination.specific_price.reduction);
      priceWithDiscountsWithTax =
        priceWithDiscountsWithTax * (1 - +combination.specific_price.reduction);
    }
  }

  // Apply Tax if necessary
  if (noTaxForThisProduct || customerGroupWithoutTax) {
    basePriceDisplay = basePriceWithoutTax;
    priceWithDiscountsDisplay = priceWithDiscountsWithoutTax;
  } else {
    basePriceDisplay = basePriceWithTax;
    priceWithDiscountsDisplay = priceWithDiscountsWithTax;
  }

  // If the specific price was given after tax, we apply it now
  if (combination.specific_price && combination.specific_price.reduction > 0) {
    if (combination.specific_price.reduction_type == "amount") {
      if (
        typeof combination.specific_price.reduction_tax === "undefined" ||
        (typeof combination.specific_price.reduction_tax !== "undefined" &&
          combination.specific_price.reduction_tax === "1")
      ) {
        var reduction = combination.specific_price.reduction;

        if (
          typeof specific_currency !== "undefined" &&
          specific_currency &&
          parseInt(combination.specific_price.id_currency) &&
          combination.specific_price.id_currency != currency.id
        )
          reduction = reduction / currencyRate;
        else if (!specific_currency) reduction = reduction * currencyRate;

        if (typeof groupReduction !== "undefined" && groupReduction > 0)
          reduction *= 1 - parseFloat(groupReduction);

        priceWithDiscountsDisplay -= reduction;
        // We recalculate the price without tax in order to keep the data consistency
        priceWithDiscountsWithoutTax =
          priceWithDiscountsDisplay - reduction * (1 / (1 + taxRate / 100));
      }
    }
  }

  // Compute discount value and percentage
  // Done just before display update so we have final prices
  if (basePriceDisplay != priceWithDiscountsDisplay) {
    var discountValue = basePriceDisplay - priceWithDiscountsDisplay;
    var discountPercentage =
      (1 - priceWithDiscountsDisplay / basePriceDisplay) * 100;
  }

  var unit_impact = +combination.unit_impact;
  if (productUnitPriceRatio > 0 || unit_impact) {
    if (unit_impact) {
      baseUnitPrice = productBasePriceTaxExcl / productUnitPriceRatio;
      unit_price = baseUnitPrice + unit_impact;

      if (!noTaxForThisProduct || !customerGroupWithoutTax)
        unit_price = unit_price * (taxRate / 100 + 1);
    } else unit_price = priceWithDiscountsDisplay / productUnitPriceRatio;
  }

  var newCustomizationPrice = 0;
  for (var i = 0; i < groupAdded.length; i++) {
    if (typeof groupAdded[i] != "undefined")
      newCustomizationPrice += parseFloat(groupAdded[i] * 1);
  }

  if (parseFloat(productPrice) > 0) {
    var productPrice = priceWithDiscountsDisplay;
    new_price =
      parseFloat(productPrice * 1) + parseFloat(newCustomizationPrice * 1);
    newCustomizationPrice = 0;
    setTimeout(function () {
      $("#our_price_display").text(formatCurrencyNdk(new_price * currencyRate));
    }, 50);
  }
  $(
    "#add_to_cart, .product-add-to-cart > *:not(.product-quantity), .add, .add-to-cart"
  ).hide();
}

function showLayer(caller) {
  if (typeof showLayer_Override == "function") {
    return showLayer_Override(caller);
  }
  view = caller.attr("data-view");
  group = caller.attr("data-group");
  if (view > 0 && $(".zone_limit[data-group='" + group + "']").length > 0)
    target = $(".zone_limit[data-group='" + group + "']");
  else target = $("#visual_" + group);

  //target = $("#visual_"+group);

  target.show();
  caller.addClass("visible_layer").removeClass("hidden_layer");
  $(".hidden_layer[data-group='" + group + "']").trigger("click");
}

function hideLayer(caller) {
  if (typeof hideLayer_Override == "function") {
    return hideLayer_Override(caller);
  }
  view = caller.attr("data-view");
  group = caller.attr("data-group");
  if (view > 0 && $(".zone_limit[data-group='" + group + "']").length > 0)
    target = $(".zone_limit[data-group='" + group + "']");
  else target = $("#visual_" + group);

  //target = $("#visual_"+group);

  target.hide();
  caller.addClass("hidden_layer").removeClass("visible_layer");
  $(".visible_layer[data-group='" + group + "']").trigger("click");
}

function makeFloat(el, parent) {
  if (typeof makeFloat_Override == "function") {
    return makeFloat_Override(el, parent);
  }
  if (!$("body").hasClass("ndkcfLoaded")) return false;

  parent = parent || "";
  var element = $(el);
  if (parent != "") $parent = $(parent);
  else $parent = $(element).parent();

  elOffset = $(el).offset();

  if ($(window).width() > 768 && contentOnly != true) {
    (function ($) {
      var element = $(el);

      if (typeof elOffset != "undefined") var originalY = elOffset.top;
      else var originalY = 0;
      var topMargin = 20;
      element.css("position", "relative").removeClass("ndk-floating");

      $(window).on("scroll", function (event) {
        var maxPosition = $parent.innerHeight() - $(element).innerHeight();
        var scrollTop = $(window).scrollTop();
        if (scrollTop - $parent.offset().top + topMargin < maxPosition) {
          topToSet =
            scrollTop < originalY ? 0 : scrollTop - originalY + topMargin;
          if (scrollTop > originalY) element.addClass("ndk-floating");
          else element.removeClass("ndk-floating");

          element.stop(false, false).animate(
            {
              top: topToSet,
              //marginBottom:  scrollTop < originalY ? 0 : scrollTop - originalY + topMargin
            },
            150
          );
        } else {
          //element.removeClass('ndk-floating');
        }
      });
    })(jQuery);
  }
}

$(document).on("change", ".ndkcfLoaded #quantity_wanted", function (e) {
  $("#max_options_quantity").remove();
  if (
    typeof $(this).attr("max") != "undefined" &&
    $(this).attr("max") != "null"
  ) {
    if (parseFloat($(this).val()) > parseFloat($(this).attr("max"))) {
      $(this).val($(this).attr("max")).trigger("keyup");
      $(this)
        .parent()
        .append(
          '<span class="quantity_warning" id="max_options_quantity">' +
            textMaxQuantity +
            " " +
            $(this).attr("max") +
            "</span>"
        );
    } else {
      $("#max_options_quantity").remove();
    }
  } else {
    $("#max_options_quantity").remove();
  }
  //test a surveiller
  //updatePriceNdkGeneric();

  setTimeout(function () {
    $("#main").removeClass("-combinations-loading");
  }, 2000);
});

$(document).on("keyup", ".dimension_text", function (e) {
  $(this).trigger("change");
});

function ndkacfShowHeight(groupf) {
  //@TRE added
  if (typeof window["ndk_dimensions_" + groupf] === "undefined") {
    return;
  }
  let matrix = window["ndk_dimensions_" + groupf];
  console.log(matrix);
  if ("width" in matrix) {
    $("#dimension_text_height_" + groupf)
      .find("option")
      .remove()
      .end()
      .append('<option value="">--</option>')
      .val("");
    width = $("#dimension_text_width_" + groupf).val();
    if (typeof matrix[width] === "undefined") {
      return;
    }

    matrix[width].forEach(function (element) {
      $("#dimension_text_height_" + groupf).append(
        `<option ${
          matrix[width].length < 2 ? 'selected="selected"' : ""
        } value="${element}">${element}</option>`
      );
    });
  }
}

$(document).on("change", ".dimension_text", function (e) {
  groupf = parseInt($(this).attr("data-group"));
  if ($(this).hasClass("dimension_text_width")) {
    //@TRE added
    ndkacfShowHeight(groupf);
  }
  width = $("#dimension_text_width_" + groupf).val();
  height = $("#dimension_text_height_" + groupf).val();
  if (height == null) {
    $("#dimension_text_height_" + groupf)
      .prop("selectedIndex", 0)
      .val();
    height = $("#dimension_text_height_" + groupf).val();
  }

  if (width != null && height != null && height != " " && width != " ") {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(function () {
      $.ajax({
        type: "GET",
        async: true,
        url:
          baseUrl +
          "modules/ndk_advanced_custom_fields/front_ajax.php?action=getRangePrice",
        data: {
          width: width,
          height: height,
          group: groupf,
          id_product: $("#ndkcf_id_product").val(),
        },
        success: function (data) {
          if (!isNaN(data)) {
            if (
              parseFloat(data) < 0 &&
              $("#dimension_text_height_" + groupf).attr("type") == "number" &&
              parseFloat(height) <
                $("#dimension_text_height_" + groupf).attr("max")
            ) {
              getMinHeight(groupf, width, height);
              //$('#dimension_text_height_'+groupf).val(parseFloat(height)+1).attr('min', parseFloat(height)+1).trigger('change');
            } else {
              if (
                parseFloat(height) >
                $("#dimension_text_height_" + groupf).attr("max")
              ) {
                $("#dimension_text_height_" + groupf)
                  .val($("#dimension_text_height_" + groupf).attr("max"))
                  .trigger("change");
              } else {
                //$('#dimension_text_height_'+groupf).attr('min', 0);
                updatePriceNdk(parseFloat(data), groupf);
                //console.log(groupf);
              }
            }
          }
          checkDimensionsLimit(groupf);
        },
      });
    }, 500);
  } else {
    updatePriceNdk(0, groupf);
  }
});

function checkDimensionsLimit(groupf) {
  width = parseFloat($("#dimension_text_width_" + groupf).val());
  height = parseFloat($("#dimension_text_height_" + groupf).val());
  max_width = parseFloat($("#dimension_text_width_" + groupf).attr("max"));
  min_width = parseFloat($("#dimension_text_width_" + groupf).attr("min"));
  max_height = parseFloat($("#dimension_text_height_" + groupf).attr("max"));
  min_height = parseFloat($("#dimension_text_height_" + groupf).attr("min"));

  console.log([width, max_width, min_width, height, min_height, max_height]);
  if (width > max_width) {
    $(".form-group[data-field=" + groupf + "] .quantity_error_width_up")
      .addClass("required_field")
      .fadeIn();
  } else {
    $(".form-group[data-field=" + groupf + "] .quantity_error_width_up")
      .removeClass("required_field")
      .fadeOut();
  }
  if (width < min_width) {
    $(".form-group[data-field=" + groupf + "] .quantity_error_width_down")
      .addClass("required_field")
      .fadeIn();
  } else {
    $(".form-group[data-field=" + groupf + "] .quantity_error_width_down")
      .removeClass("required_field")
      .fadeOut();
  }

  if (height > max_height) {
    $(".form-group[data-field=" + groupf + "] .quantity_error_height_up")
      .addClass("required_field")
      .fadeIn();
  } else {
    $(".form-group[data-field=" + groupf + "] .quantity_error_height_up")
      .removeClass("required_field")
      .fadeOut();
  }
  if (height < min_height) {
    $(".form-group[data-field=" + groupf + "] .quantity_error_height_down")
      .addClass("required_field")
      .fadeIn();
  } else {
    $(".form-group[data-field=" + groupf + "] .quantity_error_height_down")
      .removeClass("required_field")
      .fadeOut();
  }
}

function getMinHeight(group, width) {
  $.ajax({
    type: "GET",
    async: true,
    url:
      baseUrl +
      "modules/ndk_advanced_custom_fields/front_ajax.php?action=getMinHeight",
    data: { width: width, group: groupf },
    success: function (data) {
      if (!isNaN(data)) {
        $("#dimension_text_height_" + groupf)
          .val(parseFloat(data))
          .attr("min", parseFloat(data))
          .trigger("change");
      }
    },
  });
}

(function ($) {
  $.fn.rotationDegrees = function () {
    var matrix =
      this.css("-webkit-transform") ||
      this.css("-moz-transform") ||
      this.css("-ms-transform") ||
      this.css("-o-transform") ||
      this.css("transform");
    if (typeof matrix === "string" && matrix !== "none") {
      var values = matrix.split("(")[1].split(")")[0].split(",");
      var a = values[0];
      var b = values[1];
      var angle = Math.round(Math.atan2(b, a) * (180 / Math.PI));
    } else {
      var angle = 0;
    }
    return angle < 0 ? angle + 360 : angle;
  };
})(jQuery);

function cancelFieldRestrictions(id_value, group) {
  //console.log('disabled_value_by_'+group+'_'+id_value)
  $("[class*='disabled_value_by_" + group + "']")
    .removeClass("disabled_value_by_" + group)
    .addClass("enabled_value_by_" + group);
  $("[class*='disabled_value_by_" + group + "']").removeClass(function (
    index,
    className
  ) {
    return (className.match(/\bdisabled_value_by_\S+/g) || []).join(" ");
  });

  $(".disabled_value_by_" + group + "_" + id_value).removeClass(
    "disabled_value_by_" + group + "_" + id_value
  );
}

function checkFieldRestrictions(id_value, group, only_disable = false) {
  if (typeof checkFieldRestrictions_Override == "function") {
    return checkFieldRestrictions_Override(id_value, group);
  }

  selectedConfigValue = getSelectedValuesForPrice();
  var data = { restrictions: false, obligations: false };
  $(".form-group[data-field='" + group + "']")
    .removeClass("focusRequired")
    .find(".error")
    .remove();

  var my_id_value;
  if (id_value && id_value.indexOf("[") > -1) {
    idValArr = id_value.split("[");
    my_id_value = idValArr[0];
  } else my_id_value = id_value;

  if (
    typeof jsonDatas[group] != "undefined" &&
    typeof jsonDatas[group][my_id_value] != "undefined"
  )
    data = jsonDatas[group][my_id_value];

  if (
    typeof data != "undefined" &&
    data !== null &&
    data != "" &&
    ((typeof data.restrictions != "undefined" &&
      data.restrictions !== null &&
      data.restrictions != "") ||
      (typeof data.obligations != "undefined" &&
        data.obligations !== null &&
        data.obligations != ""))
  ) {
    //goTonextStep(group);
    zeroValue = false;
    if (
      typeof selectedConfigValue["ndk-accessory-quantity-" + my_id_value] !=
      "undefined"
    ) {
      if (!only_disable) {
        $(".disabled_value_by_" + group)
          .removeClass("disabled_value_by_" + group)
          .addClass("enabled_value_by_" + group);
      }
      //console.log(selectedConfigValue['ndk-accessory-quantity-'+my_id_value] )
      zeroValue =
        parseInt(
          selectedConfigValue["ndk-accessory-quantity-" + my_id_value]
        ) == 0;
      //zeroValue = parseInt( $('#ndk-accessory-quantity-'+my_id_value ).val() ) == 0;
      //console.log(zeroValue)
    }

    //if(selectedIdValue[group] != my_id_value && !zeroValue){
    if (!zeroValue) {
      selectedIdValue[group] = my_id_value;
      if (
        !$(".form-group[data-field='" + group + "']").hasClass(
          "hasRestrictions"
        )
      )
        return true;

      id_value = id_value || false;
      if (id_value && id_value.indexOf("[") > -1) {
        idValArr = id_value.split("[");
        killer = idValArr[0];
      } else {
        killer = 0;
      }
      $(".disabled_value_by_" + group).each(function () {
        selector =
          ".form-group[data-field='" + $(this).attr("data-field") + "']:eq(0)";
        //applyDefaultValuesNdk($(selector));
      });
      $(".form-group[data-field='" + group + "']")
        .removeClass("focusRequired")
        .find(".error")
        .remove();

      if (!only_disable) {
        $(".disabled_value_by_" + group)
          .removeClass("disabled_value_by_" + group)
          .addClass("enabled_value_by_" + group);
        $(
          ".disabled_value_by_" + group + (killer > 0 ? "_" + killer : "")
        ).removeClass(
          "disabled_value_by_" + group + (killer > 0 ? "_" + killer : "")
        );
      }
      my_restrictions = [];
      if (
        typeof data != "undefined" &&
        data !== null &&
        typeof data.restrictions != "undefined" &&
        data.restrictions !== null &&
        data.restrictions != ""
      ) {
        if (!only_disable)
          $(".disabled_value_by_" + group)
            .removeClass("disabled_value_by_" + group)
            .addClass("enabled_value_by_" + group);

        for (var i = 0; i <= data.restrictions.length; i++) {
          if (
            typeof (data.restrictions[i] != "undefined") &&
            data.restrictions[i] !== null &&
            data.restrictions[i]
          ) {
            splitted = data.restrictions[i].split("|");

            targetVal = splitted[1];
            targetValValue = splitted[2];
            targetGroup = splitted[0];
            targetGroup = targetGroup.replace("]", "").replace("[", "");

            if (
              $("#ndkcsfield_" + targetGroup).val() == targetValValue ||
              targetVal == "all"
            ) {
              rootBlock = $(".form-group[data-field='" + targetGroup + "']");
              $("#visual_" + targetGroup).remove();

              $(".absolute-visu[data-group^=" + targetGroup + "-]").remove();
              $("#ndkcsfield_" + targetGroup).val("");
              rootBlock
                .find(
                  ".noborder, .noborderSimple, .falsenoborder, .recipient-text"
                )
                .val("");
              rootBlock
                .find("input[type='checkbox']")
                .prop("checked", false)
                .trigger("change");
              rootBlock.find(".selected-value").removeClass("selected-value");

              if (
                rootBlock.find(
                  '.noborder[value!=""], .noborderSimple[value!=""], .falsenoborder[value!=""]'
                ).length > 0
              ) {
                rootBlock
                  .find(".noborder, .noborderSimple, .falsenoborder")
                  .val("");
                rootBlock
                  .find(".submitText, .submitSimpleText")
                  .trigger("click");
              }
              $("#layer-edit-" + targetGroup).remove();
              if (!!$.prototype.uniform)
                $.uniform.update("#ndkcsfields input, #ndkcsfields select");
              $("#dimension_text_width_" + targetGroup).val("");
              $("#dimension_text_height_" + targetGroup).val("");
              $(".recap_group_" + targetGroup).html("");

              updatePriceNdk(0, targetGroup);
              groupAdded[targetGroup] = 0;
              checkLayerChanges();
            }

            my_restrictions.push({
              targetVal: targetVal,
              group: group,
              targetGroup: targetGroup,
              killer: killer,
            });
            disableFieldRestriction(targetVal, group, targetGroup, killer);
          }
          // if (my_restrictions.length > 0)
          // for (var i = 0; i <= my_restrictions.length; i++) {
          //   if (typeof my_restrictions[i] != "undefined") {
          //     //console.log(my_obligations[i].value);
          //
          //     disableFieldRestriction(targetVal, group, targetGroup, killer);
          //   }
          // }

          splitted = targetVal = targetValValue = targetGroup = false;
        }
      } else {
        $(".disabled_value_by_" + group)
          .removeClass("disabled_value_by_" + group)
          .addClass("enabled_value_by_" + group);
        $(
          ".disabled_value_by_" + group + (killer > 0 ? "_" + killer : "")
        ).removeClass(
          "disabled_value_by_" + group + (killer > 0 ? "_" + killer : "")
        );
      }

      my_obligations = [];
      targetCurrentValue = false;

      if (typeof data === "object" && typeof data.obligations === "object") {
        for (var i = 0; i <= data.obligations.length; i++) {
          if (
            typeof (data.obligations[i] != "undefined") &&
            data.obligations[i] !== null &&
            data.obligations[i]
          ) {
            splitted = data.obligations[i].split("|");
            targetVal = splitted[1];
            targetValValue = splitted[2];
            targetGroup = splitted[0];
            targetGroup = targetGroup.replace("]", "").replace("[", "");
            targetCurrentValue = selectedConfigValue[targetGroup];
            rootBlock = $(".form-group[data-field='" + targetGroup + "']");
            //console.log(targetGroup+':'+targetCurrentValue );
            //console.log(targetValValue+' : '+targetVal)

            //if(targetCurrentValue != false && targetCurrentValue != targetValValue && selectedConfigValue[group] != '')
            my_obligations.push({
              block: rootBlock,
              value: '[data-id-value="' + targetVal + '"]',
            });
          }
        }
        if (my_obligations.length > 0)
          for (var i = 0; i <= my_obligations.length; i++) {
            if (typeof my_obligations[i] != "undefined") {
              //console.log(my_obligations[i].value);
              block = my_obligations[i].block;
              value = my_obligations[i].value;
              applyDefaultValuesNdk(block, value, false);
            }
          }
        splitted = targetVal = targetValValue = targetGroup = false;
      }
    }
  } else {
    $(".disabled_value_by_" + group)
      .removeClass("disabled_value_by_" + group)
      .addClass("enabled_value_by_" + group);
    //$('.disabled_value_by_'+group+(killer > 0 ? '_'+killer : '')).removeClass('disabled_value_by_'+group+(killer > 0 ? '_'+killer : ''));
    selectedIdValue[group] = id_value;
  }
  $(".view_tab.activeView").trigger("click");
  goTonextStep(group);
  getSubValues(id_value, group);
}

function getSubValues(id_value, group, targetEl) {
  if (typeof getSubValues_Override == "function") {
    return getSubValues_Override(id_value, group, targetEl);
  }
  targetEl = targetEl || $("#main-" + group);
  if (group.indexOf("-") > -1) mainGroup = group.split("-")[0];
  else mainGroup = group;

  group_block = $(".form-group[data-field='" + mainGroup + "']");
  //21.05
  // if (group_block.is(".field-type-27")) {
  //   return true;
  // }
  if (typeof jsonDatas[mainGroup] != "undefined") {
    $("#ndkloader").fadeOut().remove();

    myType = 0;

    type = jsonDatas[mainGroup].type;

    $.ajax({
      type: "GET",
      url:
        baseUrl +
        "modules/ndk_advanced_custom_fields/front_ajax.php?action=getSubValues",
      data: { id_field: group, id_value: id_value, type: type },
      success: function (data) {
        mytype = "";
        if (data.length > 0) {
          if ($("#sub-" + group + "-" + id_value).length < 1) {
            depth = $("#item-" + group).find(".subValues").length + 1;
            $("#item-" + group)
              .find(".subValues")
              .each(function () {
                if (
                  $(this).find("[data-id-value=" + id_value + "]").length == 0
                )
                  $(this).remove();
              });
            targetEl.find(".subValues").each(function () {
              if ($(this).find("[data-id-value=" + id_value + "]").length == 0)
                $(this).remove();
            });

            if ($("#item-" + group).length > 0) {
              $("#item-" + group + " .image-library:eq(0)").after(
                '<div id="sub-' +
                  group +
                  "-" +
                  id_value +
                  '" class="item-sub subValues image-library clear clearfix" data-depth="' +
                  depth +
                  '" data-parent-type="' +
                  mytype +
                  '">' +
                  data +
                  "</div>"
              );
              $("#sub-" + group + "-" + id_value)
                .find(".img-value")
                .attr("data-group", group);
              $("#sub-" + group + "-" + id_value)
                .find(".img-item-row")
                .attr("data-group", group);
            } else {
              if (type == "select") {
                if ($("#subselect-" + group).length == 0) {
                  $("select#ndkcsfield_" + group).removeAttr("name");
                  targetEl.append(
                    `<select name="ndkcsfield[${group}]" id="subselect-${group}" class="form-control-ndk ndk-select" data-group="${group}"></select>`
                  );
                }
                $("#subselect-" + group).html(data);
              } else {
                targetEl.append(
                  '<div id="sub-' +
                    group +
                    "-" +
                    id_value +
                    '" class="subValues clear clearfix image-library" data-depth="' +
                    depth +
                    '" data-parent-type="' +
                    mytype +
                    '">' +
                    data +
                    "</div>"
                );
              }

              $("#sub-" + group + "-" + id_value)
                .find(".img-value")
                .attr("data-group", group);
              $("#sub-" + group + "-" + id_value)
                .find(".img-item-row")
                .attr("data-group", group);
            }

            setTimeout(function () {
              reloadImg($("#sub-" + group + "-" + id_value));
              setTypesGroups("#sub-" + group + "-" + id_value);
              applyDefaultValuesNdk(targetEl);
              applyDefaultValuesNdk($("#item-" + group));
              $("#sub-" + group + "-" + id_value).trigger("subLoaded");
              lazyLoadInstance.update();
              $(".ndkackFieldItem[data-field=" + mainGroup + "]")
                .find(".small_loader_container #ndkloader")
                .fadeOut()
                .remove();
            }, 800);
          }
        }
      },
    });
  }
  $(".ndkackFieldItem[data-field=" + mainGroup + "]")
    .find(".small_loader_container #ndkloader")
    .fadeOut()
    .remove();
}

function setTypesGroups(target) {
  if (typeof setTypesGroups_Override == "function") {
    return setTypesGroups_Override(target);
  }

  $("#ndkloader").fadeOut().remove();
  $(target).append(ndkLoader).addClass("small_loader_container");

  var filtersTypes = [];
  target = target || "body";

  $(target)
    .find(".typed")
    .each(function () {
      type = $(this).attr("data-type");
      rootBlock = $(this).attr("data-group");
      filtersTypes.push(type + "|" + rootBlock);
    });

  //on créé le block de types
  encountred = [];
  for (var i = 0; i < filtersTypes.length; i++) {
    value = filtersTypes[i].split("|");
    if ($.inArray(value[0], encountred) == -1) {
      group = value[1];
      textValue = value[0];

      if (target == "body") my_target = "#item-" + group;
      else my_target = target;

      delTypeButton = "";
      if ($.inArray(textValue, ["with-baby", "accessories"]) > -1) {
        delTypeButton =
          '<a class="delTypeButton" data-type="' +
          textValue +
          '" data-group="' +
          group +
          '"><span><i class="material-icons">delete</i></span></a>';
      }
      if (
        $(my_target).find(".group-type[data-group-type=" + textValue + "]")
          .length == 0
      )
        $(my_target).append(
          '<div class="group-type clear clearfix" data-group-type="' +
            textValue +
            '"><p class="group-title">' +
            (typeof typeText[textValue] != "undefined"
              ? typeText[textValue]
              : textValue) +
            '<span class="group-value"></span>' +
            delTypeButton +
            '</p><div class="groupList"></div></div>'
        );

      encountred.push(value[0]);
    }
    // setTimeout(function(){
    // 	$(my_target).find('.group-title:eq(0):not(.active)').trigger('click');
    // }, 800)
  }

  $(target)
    .find(".typed")
    .each(function () {
      type = $(this).attr("data-type");
      rootBlock = $(this).attr("data-group");
      if (target == "body") my_target = "#item-" + rootBlock;
      else my_target = target;
      $(this)
        .detach()
        .appendTo(
          $(target).find(".group-type[data-group-type=" + type + "] .groupList")
        );
    });

  $(".group-type").each(function () {
    type = $(this).attr("data-group-type");
    setColorSelector(type, target);
  });

  $(target).find(".small_loader_container #ndkloader").fadeOut().remove();
  $(target).trigger("typesSet");
}

function reloadImg(parent) {
  parent = parent || $("body");
  if (typeof parent != "object") parent = $(parent);
  $(".img-value").each(function () {
    if (typeof $(this).attr("data-thumb") != "undefined") {
      preloadImg.push($(this).attr("data-thumb"));
      $(this).attr("src", $(this).attr("data-thumb"));
    }
  });

  $.when(preload(preloadImg)).done(function () {
    setTimeout(function () {
      //equalheightNdkcf(".img-item-row");
      resizeMasonryGallery();
    }, 50); //test timeout
  });

  loadImgSvg(parent);
}

function loadImgSvg(parent) {
  parent = parent || $("body");
  if (typeof parent != "object") parent = $(parent);
  $(parent)
    .find("img.svg")
    .each(function () {
      var $img = $(this);
      var imgID = $img.attr("id");
      var idVal = $img.attr("data-id-value");
      var imgClass = "";
      $img.hide();
      $svg = $img.parent().find(".svg-container").find("svg");
      if (typeof imgID !== "undefined") {
        $svg.attr("id", imgID);
      }
      // Add replaced image's classes to the new SVG
      if (typeof imgClass !== "undefined") {
        $svg.attr(
          "class",
          imgClass +
            " replaced-svg composition_element  composition_element-" +
            $img.attr("data-type")
        );
      }
      $svg.attr("data-type", $img.attr("data-type"));
      $svg.removeAttr("xmlns:a");
      $svg.find("use").each(function () {
        xlink = $(this).attr("xlink:href");
        my_xlink = xlink.replace("#", "") + "-" + idVal;
        $(this).attr("xlink:href", "#" + my_xlink);
        $svg.find(xlink).attr("id", my_xlink);
      });
      $img.addClass("loaded_svg");
    });
}

function goTonextStep(group) {
  formGroup = $(".form-group.steppedField[data-field='" + group + "']");
  required = formGroup.find(".required_field").length > 0;

  if (typeof selectedIdValue[group] != "undefined" || !required) {
    //console.log(group+'-'+selectedIdValue[group]+'-'+required)
    formIteration = parseInt(formGroup.attr("data-iteration"));
    nextIteration = parseInt(formIteration + 1);
    formGroup.addClass("stepDone").removeClass("stepTodo");

    nextBlock = formGroup.nextAll(".notReadyStep:visible:first");
    next_group = nextBlock.attr("data-field");
    nextBlock.removeClass("notReadyStep").find(".toggler").trigger("click");

    if (parseInt(next_group) > 0) goTonextStep(next_group);
  } else {
    return false;
    //console.log('stop')
  }
}

function setScenario() {
  var step_done = [];
  var step_to_do = [];
  $(".overDisabler").remove();
  for (var i = 0; i < scenario.length; i++) {
    $(".form-group[data-field='" + scenario[i] + "']")
      .addClass("steppedField notReadyStep stepTodo")
      .append('<div class="overDisabler"></div>');
  }
  $(".notReadyStep:eq(0)").removeClass("notReadyStep");
}

function setOpenedStatus() {
  if (isFields != 1 || $("#ndkcsfields-block").length < 1) return;
  for (var i = 0; i < opened_fields.length; i++) {
    $(".form-group[data-field='" + opened_fields[i] + "']")
      .addClass("opened-form-group")
      .find(".toggler:not(.active)")
      .trigger("click");
    setTimeout(function () {
      equalheightNdkcf(
        ".form-group[data-field='" + opened_fields[i] + "'] .img-item-row"
      );
    }, 500);
  }
  for (var i = 0; i < closed_fields.length; i++) {
    $(".form-group[data-field='" + closed_fields[i] + "']")
      .removeClass("opened-form-group")
      .find(".toggler.active")
      .trigger("click");
  }

  for (var i = 0; i < hidden_fields.length; i++) {
    $(".form-group[data-field='" + hidden_fields[i] + "']").addClass(
      "hidden hidden-important"
    );
  }
}

function identifyResctictives() {
  for (var i = 0; i < hasRestrictions.length; i++) {
    $(".form-group[data-field='" + hasRestrictions[i] + "']").addClass(
      "hasRestrictions"
    );
  }
}

function disableFieldRestriction(idVal, group, targetGroup, killer) {
  if (typeof disableFieldRestriction_Override == "function") {
    return disableFieldRestriction_Override(idVal, group, targetGroup, killer);
  }
  if (typeof idVal == "undefined" || idVal == "" || idVal == "undefined")
    return true;

  $(".disabled_value_by_" + targetGroup)
    .removeClass("disabled_value_by_" + targetGroup)
    .addClass("enabled_value_by_" + targetGroup);
  if (idVal == "all") {
    $(".form-group[data-field='" + targetGroup + "']:not(.submitContainer)")
      .addClass("disabled_value_by_" + group)
      .removeClass("enabled_value_by_" + group);
    $(".form-group[data-field='" + targetGroup + "']")
      .find(".ndk-accessory-quantity[value!=0]")
      .val(0)
      .trigger("change")
      .trigger("keyup")
      .addClass("disabled_value_by_" + group)
      .removeClass("enabled_value_by_" + group);
    $(
      ".ndkQuickAccessBox-item[data-target='" +
        targetGroup +
        "']:not(.submitContainer)"
    )
      .addClass("disabled_value_by_" + group)
      .removeClass("enabled_value_by_" + group);

    $(".absolute-mask[data-field='" + targetGroup + "']")
      .addClass("disabled_value_by_" + group)
      .removeClass("enabled_value_by_" + group);

    $(".absolute-visu[data-field='" + targetGroup + "']")
      .addClass("disabled_value_by_" + group)
      .removeClass("enabled_value_by_" + group);
  }

  //$('*').removeClass('disabled_value_by_'+group).addClass('enabled_value_by_'+group);
  if (idVal.indexOf("[") > -1) {
    idValArr = idVal.split("[");
    idValue = idValArr[0];
    qttyVal = idValArr[1].replace("]", "");
    if ($("#ndk-accessory-quantity-" + killer).val() == qttyVal) {
      //$('#ndk-accessory-quantity-'+idValue).addClass('disabled_value_by_'+group+'[value!=0]').val(0).trigger('change').trigger('keyup');
      $("#ndk-accessory-quantity-" + idValue)
        .addClass(
          "disabled_value_by_" + group + (killer > 0 ? "_" + killer : "")
        )
        .val(0)
        .trigger("change")
        .trigger("keyup");
      $("#ndk-accessory-quantity-" + idValue)
        .parent()
        .parent()
        .parent()
        .addClass("disabled_value_by_" + group)
        .removeClass("enabled_value_by_" + group);
      $("#ndk-accessory-quantity-" + idValue)
        .parent()
        .parent()
        .parent()
        .addClass(
          "disabled_value_by_" + group + (killer > 0 ? "_" + killer : "")
        )
        .val(0)
        .trigger("change")
        .trigger("keyup");
    }
  } else {
    //$('#ndk-accessory-quantity-'+idVal).addClass('disabled_value_by_'+group+'[value!=0]').val(0).trigger('change').trigger('keyup');
    $("#ndk-accessory-quantity-" + idVal)
      .addClass("disabled_value_by_" + group + (killer > 0 ? "_" + killer : ""))
      .val(0)
      .trigger("change")
      .trigger("keyup");
    $("#ndk-accessory-quantity-" + idVal)
      .parent()
      .parent()
      .parent()
      .addClass("disabled_value_by_" + group)
      .removeClass("enabled_value_by_" + group);
    $("#ndk-accessory-quantity-" + idVal)
      .parent()
      .parent()
      .parent()
      .addClass("disabled_value_by_" + group + (killer > 0 ? "_" + killer : ""))
      .val(0)
      .trigger("change")
      .trigger("keyup");
    //$("input[name='price_'"+idVal+"]").val(0);
  }

  $("[data-id-value='" + idVal + "']")
    .addClass("disabled_value_by_" + group)
    .removeClass("enabled_value_by_" + group);
  $(".ndk-radio[data-id-value='" + idVal + "']")
    .parent()
    .addClass("disabled_value_by_" + group)
    .removeClass("enabled_value_by_" + group);
  //$("[data-id-value='"+idVal+"']").parent(':not(select)').addClass('disabled_value_by_'+group).removeClass('enabled_value_by_'+group);
  // setTimeout(function(){
  // 	targetForDefault = $('.enabled_value_by_'+group+':visible').not('[class*="disabled_value_by"]');
  // 	applyDefaultValuesNdk(targetForDefault, '[data-default-value="1"]', false);
  //  }, 80);
  setTimeout(function () {
    applyDefaultValuesNdk(
      $(
        ".form-group.enabled_value_by_" +
          group +
          ":not([class*='disabled_value_by_'])"
      )
    );
  }, 250);
}

function convertPercent() {
  if (typeof convertPercent_Override == "function") {
    return convertPercent_Override();
  }

  $(".absolute-visu:visible").each(async function () {
    if ($(this).find("img.composition_element").length > 0) {
      var image = asyncImageLoader(
        $(this).find("img.composition_element:eq(0)").attr("src")
      );
      await image;
    }
    dragToPercent($(this));
  });

  $("#image-block").css({ width: "100%", height: "auto" });
}

function convertPercentBAK() {
  if (typeof convertPercentBAK_Override == "function") {
    return convertPercentBAK_Override();
  }
  $(".absolute-visu:visible").each(function () {
    group = $(this).attr("id").replace("visual_", "");

    if (group.indexOf("-") > -1) {
      group = group.split("-")[0];
    }

    //console.log(group);
    if ($(".zone_limit[data-group='" + group + "']").length > 0) {
      container = ".zone_limit[data-group='" + group + "']";
      target = 2;
    } else {
      container = "#image-block";
      target = 1;
    }
    //$(container).hide();
    containerWidth = $(container).width();
    containerHeight = $(container).height();

    elWidth = $(this).width();
    elHeight = $(this).height();

    //elLeft = $(this).css('left').replace('px', '');
    //elTop = $(this).css('top').replace('px', '');

    //elLeft = $(this).clone().appendTo('body').wrap('<div style="display: none"></div>').css('left');
    //elTop = $(this).clone().appendTo('body').wrap('<div style="display: none"></div>').css('top');
    elLeft = $(this)[0].style.left.replace("px", "");
    elTop = $(this)[0].style.top.replace("px", "");

    //convertDegree($(this).attr('id'));
    //console.log('left : '+elLeft);
    //console.log('top : '+ elTop);

    widthPercent = (elWidth / containerWidth) * 100 + "%";
    heightPercent = (elHeight / containerHeight) * 100 + "%";
    heightPercent = "auto";
    leftPercent = (elLeft / containerWidth) * 100 + "%";
    topPercent = (elTop / containerHeight) * 100 + "%";

    $(this).css({
      width: widthPercent,
      height: heightPercent,
      margin: "",
    });

    if (elLeft.indexOf("%") < 1) {
      $(this).css({
        left: leftPercent,
      });
    }
    if (elTop.indexOf("%") < 1) {
      $(this).css({
        top: topPercent,
      });
    }
    if (
      target != 1 &&
      target == 2 &&
      !$(this).parent().hasClass("zone_limit") &&
      $(this).parent().attr("id") != "#image-block"
    ) {
      $(this).parent().css({ width: widthPercent, height: heightPercent });
    }
    $("#image-block").css({ width: "100%", height: "auto" });

    //$(container).show();
  });
}

function convertDegree(id) {
  if (typeof convertDegree_Override == "function") {
    return convertDegree_Override(id);
  }
  var el = document.getElementById(id);
  var st = window.getComputedStyle(el, null);
  var tr =
    st.getPropertyValue("-webkit-transform") ||
    st.getPropertyValue("-moz-transform") ||
    st.getPropertyValue("-ms-transform") ||
    st.getPropertyValue("-o-transform") ||
    st.getPropertyValue("transform") ||
    "FAIL";

  var values = tr.split("(")[1].split(")")[0].split(",");
  var a = values[0];
  var b = values[1];
  var c = values[2];
  var d = values[3];

  var scale = Math.sqrt(a * a + b * b);

  //console.log('Scale: ' + scale);

  // arc sin, convert from radians to degrees, round
  var sin = b / scale;
  // next line works for 30deg but not 130deg (returns 50);
  // var angle = Math.round(Math.asin(sin) * (180/Math.PI));
  var angle = Math.round(Math.atan2(b, a) * (180 / Math.PI));
  /*$(id).css({
							'-webkit-transform': 'rotate(' + angle + 'deg)',
							'-moz-transform': 'rotate(' + angle + 'deg)',
							'-ms-transform': 'rotate(' + angle + 'deg)',
							'-o-transform': 'rotate(' + angle + 'deg)',
							'transform': 'rotate(' + angle + 'deg)',
							'zoom': 1
				});*/

  //console.log('Rotate: ' + angle + 'deg');
}

function autoHideFieldForNoValue(root) {
  root.find('*[data-hide-field="1"]').each(function () {
    if ($(this).is(":checkbox"))
      checkFieldRestrictions(
        $(this).attr("data-value-id"),
        $(this).attr("data-group"),
        true
      );
    else
      checkFieldRestrictions(
        $(this).attr("data-id-value"),
        $(this).attr("data-group"),
        true
      );
  });
}
function applyDefaultValuesNdk(root, defaultValue, checkHideField) {
  root =
    root ||
    $("#ndkcsfields-block .form-group:not([class*='disabled_value_by'])");
  defaultValue = defaultValue || '[data-default-value="1"]';
  checkHideField = checkHideField || false;
  if (typeof applyDefaultValuesNdk_Override == "function") {
    return applyDefaultValuesNdk_Override(root, defaultValue, checkHideField);
  }
  //[class*='disabled_value_by']
  if (checkHideField) {
    autoHideFieldForNoValue(root);
  }

  root.find(".accessory-ndk-no-quantity" + defaultValue).each(function () {
    toggleAccessoryNoQuantity($(this));
  });

  // root
  //   .find(
  //     ".accessory-ndk-no-quantity" + defaultValue + "  .ndk_attribute_select"
  //   )
  //   .trigger("change");
  // setTimeout(function () {
  //   root
  //     .find(
  //       ".accessory-ndk-no-quantity" +
  //         defaultValue +
  //         "  .ndk-accessory-quantity"
  //     )
  //     .trigger("change");
  // }, 500);

  //console.log(defaultValue);
  root.find(".img-value" + defaultValue).each(function () {
    if ($(this).parent().find(".svg-container").length > 0)
      $(this).parent().find(".svg-container").trigger("click");
    else $(this).trigger("click");
  });
  root.find(".color-ndk" + defaultValue).each(function () {
    $(this).trigger("click");
  });

  root
    .find(".ndk-radio" + defaultValue)
    .prop("checked", true)
    .trigger("change");
  root
    .find(".ndk-checkbox" + defaultValue)
    .prop("checked", true)
    .trigger("change");
  root.find(".ndk-select").each(function () {
    if ($(this).find("option" + defaultValue).length > 0) {
      $(this)
        .find("option" + defaultValue)
        .prop("selected", "selected");
      $(this).trigger("change");
    }
  });

  root.find(".dimension_text").each(function () {
    $(this).trigger("keyup").trigger("change");
  });

  root.find(".form-group").removeClass("activeFormGroup");
}

function scrollToNdk(el, speed, force) {
  if (typeof scrollToNdk_Override == "function") {
    return scrollToNdk_Override(el, speed);
  }
  speed = speed || 750;
  force = force || false;
  if (ndk_disableAutoScroll != 1 || force) {
    if (el.length) {
      $("html").animate(
        {
          scrollTop: el.offset().top,
          scrollLeft: el.offset().left,
        },
        speed
      );
    }
  }
}

$(document).on(
  "click",
  ".ndkcfLoaded #blockcart-modal [data-dismiss='modal']",
  function () {
    location.reload();
  }
);
$(document).on("hidden.bs.modal", ".ndkcfLoaded #blockcart-modal", function () {
  location.reload();
});

// prestashop.on('updateCart', function(){
//   if($('body').hasClass('ndkcfLoaded'))
//     location.reload();
// });

prestashop.on("updateCart", function () {
  if ($("#image-block").length > 0) {
    setTimeout(function () {
      html = $("#image-block").html();
      $("#blockcart-modal .product-image")
        .parent()
        .html(html)
        .addClass("image-block");
      $(".view_tab:first").trigger("click");
    }, 1000);
  }
});

function strReplaceAll(string, Find, Replace) {
  try {
    return string.replace(new RegExp(Find, "gi"), Replace);
  } catch (ex) {
    return string;
  }
}

function emptyFormNdk(form) {
  if (typeof emptyFormNdk_Override == "function") {
    return emptyFormNdk_Override(form);
  }

  checkbox = form.find('input[type="checkbox"]');
  radio = form.find('input[type="radio"]');
  text = form.find('input[type="text"], input[type="hidden"]');
  select = form.find("select");

  checkbox.prop("checked", false).trigger("change");
  radio.prop("checked", false).trigger("change");
  //select.val('').trigger('change');
  //text.val('').trigger('change');
}

function setQuickNav() {
  if (typeof setQuickNav_Override == "function") {
    return setQuickNav_Override();
  }
  $("#ndkQuickAccessBox").remove();
  if ($(".pb-right-column").length > 0)
    $(".pb-right-column").append(
      '<div id="ndkQuickAccessBox" class="quickFullWidth"><ul></ul></div>'
    );
  else
    $("#ndkcsfields-block")
      .append('<div id="ndkQuickAccessBox"><ul></ul></div>')
      .addClass("withQuickNav");

  $(".toggler:not(.dontquick)").each(function () {
    groupTitleEl = $(this).clone();
    groupTitleEl
      .find(".toggleText, .tooltipDescription, .tooltipDescMark")
      .remove();
    title = groupTitleEl.text();
    group = $(this).parent().attr("data-field");
    $("#ndkQuickAccessBox ul").append(
      '<li class="ndkQuickAccessBox-item" data-target="' +
        group +
        '">' +
        title +
        "</li>"
    );
  });

  $(document).on("click", ".ndkQuickAccessBox-item", function () {
    $(".ndkQuickAccessBox-item").removeClass("active");
    group = $(this).attr("data-target");
    $(this).addClass("active");
    $(".ndkackFieldItem[data-field='" + group + "'] label:eq(0)").trigger(
      "click"
    );
  });
  if ($(".pb-right-column").length < 1) makeFloat("#ndkQuickAccessBox");
}

$(document).on("change", ".ndk-radio, .ndk-checkbox", function () {
  if ($(this).is(":radio"))
    $("input[name='" + $(this).attr("name") + "']")
      .not(this)
      .removeAttr("checked")
      .removeAttr("checkme");

  if ($(this).is(":checked"))
    $(this).attr("checked", "checked").attr("checkme", "1");
  else {
    $(this).removeAttr("checked");
    $(this).removeAttr("checkme");
  }
});

function updateDisplayAttrNdk() {
  if (ps_version <= 1.6) {
    var productPriceDisplay = productPrice;
    var productPriceWithoutReductionDisplay = productPriceWithoutReduction;
    if (
      !selectedCombination["unavailable"] &&
      quantityAvailable > 0 &&
      productAvailableForOrder == 1
    ) {
      $(".ndkcsfields-block").fadeIn(600);
      $("#submitNdkcsfields, .falseButton").removeAttr("disabled");
    } else {
      //show the 'add to cart' button ONLY IF it's possible to buy when out of stock AND if it was previously invisible
      if (
        allowBuyWhenOutOfStock &&
        !selectedCombination["unavailable"] &&
        productAvailableForOrder
      ) {
        $(".ndkcsfields-block").fadeIn(600);
        $("#submitNdkcsfields, .falseButton").removeAttr("disabled");
      } else {
        if (editConfig == 0) {
          $(".ndkcsfields-block").fadeOut(600);
          $("#submitNdkcsfields, .falseButton").attr("disabled", "disabled");
        }
      }
    }
  }
}

$(document).on("focus", ".ndk-select", function () {
  $(this).find("option").removeAttr("disabled");
  $(this).find('[class*="disabled_value_by"]').attr("disabled", "disabled");
});

// prestashop.on('updatedProduct', function(){
//   setFalseButton();
// })

function setFalseButton() {
  if (typeof setFalseButton_Override == "function") {
    return setFalseButton_Override();
  }
  if ($(".ndkackFieldItem").length > 0) {
    $(".falseButtonContainer").remove();
    if (ps_version < 1.7) {
      node = $("#add_to_cart").clone();
      node.addClass("falseButtonContainer");
      node.attr("id", "ndkacf_add_to_cart").show().addClass("ndkcf_add");
      node.find("button").addClass("falseButton").addClass("add-to-cart");
      falseButton = node.find("button")[0].outerHTML;
      falseButton = falseButton
        .replace("<button", "<span")
        .replace("</button>", "</span>");
      $("#add_to_cart")
        .parent()
        .addClass("clear")
        .addClass("clearfix")
        .append('<p class="buttons_bottom_block">' + falseButton + "</p>");
    } else {
      node = $(".add .add-to-cart:eq(0)").parent().clone();
      node.addClass("falseButtonContainer");
      node = $(".add .add-to-cart:eq(0)").parent().clone();
      node.addClass("falseButtonContainer");
      node
        .find(".add-to-cart")
        .addClass("falseButton")
        .removeAttr("data-button-action");
      falseButton = node.find(".add-to-cart")[0].outerHTML;
      falseButton = falseButton
        .replace("<button", "<span")
        .replace("</button>", "</span>");
      $(".ndkcfLoaded .add .add-to-cart.falseButton").remove();
      $(".add .add-to-cart:eq(0)")
        .addClass("clear")
        .addClass("clearfix")
        .after(falseButton);
    }
    $("body").addClass("ndkacf-falseButton");
    $(".ndkcfLoaded .add .add-to-cart.falseButton")
      .removeAttr("disabled")
      .removeClass("disabled");
  }
}

$(document).on("click", ".ndkcfLoaded .add-to-cart.falseButton", function (e) {
  e.preventDefault();
  $(".ndkacf-close-modal").trigger("click");
  $("#submitNdkcsfields").trigger("click");
});

function setValueProdImage() {
  if (typeof setValueProdImage_Override == "function") {
    return setValueProdImage_Override();
  }
  $(".load_product_image").each(function () {
    prodImg = $("#bigpic").attr("data-original-image");
    $(this)
      .attr("src", prodImg)
      .attr("data-src", prodImg)
      .attr("data-thumb", prodImg);
  });
}

// prestashop.on("updatedProduct", function () {
//   $(".modal-backdrop").remove();
//   $("body").removeClass("modal-open");
// });
