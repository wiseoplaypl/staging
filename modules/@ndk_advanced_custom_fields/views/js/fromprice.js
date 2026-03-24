/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2017 Hendrik Masson
 *  @license   Tous droits réservés
 */

$(document).ready(function () {
  hideCartButtons();
  setTimeout(function () {
    setFromPrices();
  }, 2000);
});

if (typeof prestashop != "undefined") {
  prestashop.on("updatedProduct", function () {
    hideCartButtons();
    setTimeout(function () {
      setFromPrices();
    }, 200);
  });
}

function ndkDetectIE() {
  var ua = window.navigator.userAgent;
  var msie = ua.indexOf("MSIE ");
  if (msie > 0) {
    // IE 10 or older => return version number
    return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)), 10);
  }

  var trident = ua.indexOf("Trident/");
  if (trident > 0) {
    // IE 11 => return version number
    var rv = ua.indexOf("rv:");
    return parseInt(ua.substring(rv + 3, ua.indexOf(".", rv)), 10);
  }

  var edge = ua.indexOf("Edge/");
  if (edge > 0) {
    // Edge (IE 12+) => return version number
    return parseInt(ua.substring(edge + 5, ua.indexOf(".", edge)), 10);
  }

  // other browser
  return false;
}

/*$(document).on('click', '.quick-view', function(){
	setTimeout(function(){
		hideCartButtons();
		setFromPrices();
	}, 1000)
	
});*/

function hideCartButtons() {
  $(".hideThisAddToCart").each(function () {
    id_product = $(this).attr("data-id-product");
    cartButton = $(this)
      .parent()
      .parent()
      .parent()
      .find(".ajax_add_to_cart_button:not(.falseButton)");
    cartButton.attr("disabled", "disabled").addClass("disabled");
    $(this)
      .parent()
      .parent()
      .parent()
      .find(".lnk_view span")
      .text($(this).val());
    //link = $(this).attr('data-link');
  });
}

$(document).on("mouseover", ".quickview", function () {
  if (!$(this).hasClass("overedNdkCart")) {
    id_product = $(this).attr("id").split("-")[2];
    console.log(id_product);
    if (parseInt(id_product) > 0) {
      link = $(".hideThisAddToCart[data-id-product='" + id_product + "']").attr(
        "data-link"
      );
      if (typeof link != "undefined") {
        cartButtonModal = $("[id*='quickview-modal-" + id_product + "']").find(
          ".add-to-cart"
        );
        cartButtonModal.attr("disabled", "disabled").addClass("disabled");
        $(this).addClass("overedNdkCart");
        $(this)
          .find(".product-actions")
          .append(
            '<a class="btn btn-primary customize-btn" href="' +
              link +
              '"><span>' +
              customizeText +
              "</span></a>"
          );
      }
    }
  }
});

function setFromPrices() {
  $(".ndkcfFromPriceProduct").each(function () {
    var me = $(this);
    //console.log(me);
    id_product = $(this).attr("data-id-product");
    priceBlock = $(
      "body.product-" +
        id_product +
        " #our_price_display, body.product-id-" +
        id_product +
        " .current-price:eq(0)"
    );
    $("#fromPrice_" + id_product).remove();
    priceBlock.addClass("hideImportant").hide();
    //$("#additionnal_price").before("" + $(this).val() + "");
  });

  $(".ndkcfFromPrice").each(function () {
    var me = $(this);
    id_product = $(this).attr("data-id-product");

    priceBlockList = me.parent().find(".product-price, .price");
    //priceBlockList.parent().find('.fromPrice').remove();
    priceBlockList.addClass("hideImportant").hide();
    //priceBlockList.before('<span class="fromPrice">'+me.val()+'</span>');

    $(document).on(
      "mouseover",
      "[id*='quickview-modal-" +
        id_product +
        "'], [class*='quickview-modal-" +
        id_product +
        "']",
      function () {
        if (!$(this).hasClass("overedNdkPrice")) {
          $(this).addClass("overedNdkPrice");
          priceBlockModal = $(
            "[id*='quickview-modal-" +
              id_product +
              "'], [class*='quickview-modal-" +
              id_product +
              "']"
          ).find(".current-price:eq(0), .product-price:eq(0)");
          priceBlockModal.addClass("hideImportant").hide();
          priceBlockModal.before(
            '<span class="fromPrice">' + me.val() + "</span>"
          );
        }
      }
    );
  });

  $("#category .ndkcfFromPrice").each(function () {
    //priceBlock = $(this).parent().parent().parent().find('.price');
    //priceBlock.html($(this).val());
  });
}

var ndkBrowserVersion = ndkDetectIE();

$(document).on("click", ".color-ndk-list", function () {
  visu = $(this).attr("data-src");
  coloreffect = "normal";
  background = "";
  type = false;
  id_product = $(this).attr("data-target-product");
  container = $("[data-id-product=" + id_product + "] .product-thumbnail");
  $(this).parent().find(".selected-color").removeClass("selected-color");
  $(this).addClass("selected-color");

  type = "color";
  coloreffect = $(this).attr("data-blend");
  if ($(this).hasClass("colorize-ndk-list")) {
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
  if (background.indexOf("http") > -1) background = "url('" + background + "')";

  $(container).find("img:eq(0)").addClass("ndk-colorized");
  $(container).find(".colorize-cover-item").removeClass("zoomIn");

  visu = $(container).find("img:eq(0)").attr("src");
  width = $(container).find("img:eq(0)").width();
  offset = $(container).find("img:eq(0)").offset();
  $(container).find(".colorize-cover-item");

  if (!ndkBrowserVersion) {
    if ($(container).find(".colorize-cover-item").length > 0) {
      $(container)
        .find(".colorize-cover-item")
        .css({ background: background, width: width })
        .addClass("zoomIn");
    } else {
      $(container).append(
        '<div style=" mix-blend-mode:' +
          coloreffect +
          ';" class="  absolute-visu absolute-img ' +
          (type == "color" ? "multiply-mode-color" : "") +
          '"><div  style="width:' +
          width +
          "px; background:" +
          background +
          "; mask-image: url('" +
          visu +
          "');-webkit-mask-image: url('" +
          visu +
          '\');" class="colorize-cover-item bounceInUp"><img alt="composition_element" class="composition_element img-reponsive " src="' +
          visu +
          '"/>'
      );
    }
  } else {
    $(container).find(".absolute-visu").remove();
    $(container).append(
      '<div style="mix-blend-mode:' +
        coloreffect +
        ';" class="absolute-visu absolute-img ' +
        (type == "color" ? "multiply-mode-color" : "") +
        '"></div>'
    );
    ndkImagetoDataURLForSvg(background, function (bgImage) {
      png2svg(bgImage, visu, background, group);
    });
  }
});

function png2svg(bgImage, maskUrl, color, group) {
  //var options = { numberofcolors : 2, strokewidth : 0 , viewbox : true};
  var options = {
    corsenabled: false,
    ltres: 1,
    qtres: 1,
    pathomit: 1,
    rightangleenhance: true,

    // Color quantization
    colorsampling: 2,
    numberofcolors: 2,
    mincolorratio: 0,
    colorquantcycles: 3,

    // Layering method
    layering: 0,

    // SVG rendering
    strokewidth: 0,
    linefilter: true,
    scale: 1,
    roundcoords: 1,
    viewbox: true,
    desc: false,
    lcpr: 0,
    qcpr: 0,

    // Blur
    blurradius: 0,
    blurdelta: 10,
  };

  if (bgImage) {
    width = bgImage.naturalWidth;
    height = bgImage.naturalHeight;
    textureEffect = bgImage.src;

    background =
      '<defs><pattern xmlns="http://www.w3.org/2000/svg" id="texture_' +
      group +
      '" patternUnits="userSpaceOnUse" height="' +
      height +
      '" width="' +
      width +
      '" overflow="visible">';
    background +=
      '<image xlink:href="' +
      textureEffect +
      '" height="' +
      height +
      '" width="' +
      width +
      '" x="0" y="0" />';
    background += "</pattern></defs>";
    ImageTracer.imageToSVG(
      maskUrl,
      function (svgstr) {
        $("#visual_" + group).prepend(svgstr);
        $("#visual_" + group)
          .find("path")
          .attr("fill", "url(#texture_" + group + ")");
        svgNode = $("#visual_" + group).find("svg")[0];
        newSvgStr = svgNode.outerHTML;
        fullSvgStr = newSvgStr.replace(
          'xml:space="preserve">',
          'xml:space="preserve">' + background
        );
        imgNode =
          '<object class="replaced-svg composition_element" data="' +
          "data:image/svg+xml;base64," +
          window.btoa(fullSvgStr) +
          '" type="image/svg+xml">' +
          '<img src="maskUrl"/>' +
          "</object>";
        $("#visual_" + group).prepend(imgNode);
        $("#visual_" + group)
          .find("svg")
          .remove();
      },
      options
    );
  } else {
    ImageTracer.imageToSVG(
      maskUrl,
      function (svgstr) {
        $("#visual_" + group)
          .find("svg")
          .remove();
        $("#visual_" + group).prepend(svgstr);
        $("#visual_" + group)
          .find("path")
          .attr("fill", color);
        $("#visual_" + group)
          .find("svg")
          .addClassSVG("replaced-svg composition_element")
          .attr("id", "traced_svg_" + group);
      },
      options
    );
  }
  //$('.orientation_selection[data-group-target='+group+']').find('.active_orientation').trigger('click');
}
