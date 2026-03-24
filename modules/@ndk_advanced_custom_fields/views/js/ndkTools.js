var formatedPrices = [];
const delay = (ms) => new Promise((resp) => setTimeout(resp, ms));

function resolveAfterTime(x) {
  return new Promise((resolve) => {
    setTimeout(() => {
      resolve(1);
    }, x);
  });
}

// uses :
// var image = asyncImageLoader(url)
// image.then( res => {
//     console.log(res)
// })
function asyncImageLoader(url) {
  return new Promise((resolve, reject) => {
    var image = new Image();
    image.src = url;
    image.onload = () => resolve(image);
    image.onerror = () => resolve(image);
    //image.onerror = () => reject(new Error("could not load image"));
  });
}

function formatCurrency17_back(
  price,
  currencyFormat17,
  currencySign,
  currencyBlank
) {
  // if you modified this function, don't forget to modify the PHP function displayPrice (in the Tools.php class)

  var currencyFormat = currencyFormat17;

  var blank = " ";
  var priceDisplayPrecision = 2;
  price = parseFloat(price.toFixed(10));
  price = ps_round(price, priceDisplayPrecision);
  if (currencyBlank > 0) blank = " ";
  if (currencyFormat == 1)
    return (
      currencySign +
      blank +
      formatNumber(price, priceDisplayPrecision, ",", ".")
    );
  if (currencyFormat == 2)
    return (
      formatNumber(price, priceDisplayPrecision, " ", ",") +
      blank +
      currencySign
    );
  if (currencyFormat == 3)
    return (
      currencySign +
      blank +
      formatNumber(price, priceDisplayPrecision, ".", ",")
    );
  if (currencyFormat == 4)
    return (
      formatNumber(price, priceDisplayPrecision, ",", ".") +
      blank +
      currencySign
    );
  if (currencyFormat == 5)
    return (
      currencySign +
      blank +
      formatNumber(price, priceDisplayPrecision, "'", ".")
    );
  return price;
}

function formatCurrency17(
  price,
  currencyFormat17,
  currencySign,
  currencyBlank,
  force_taxe_rule_group = false
) {
  if (parseFloat(price) in formatedPrices) {
    return formatedPrices[parseFloat(price)];
  } else {
    var response = "";
    $.ajax({
      type: "GET",
      async: false,
      url:
        baseUrl +
        "modules/ndk_advanced_custom_fields/front_ajax.php?action=formatPrice" +
        (force_taxe_rule_group ? force_taxe_rule_group : ""),
      data: {
        price: parseFloat(price),
        id_product: parseInt(id_product),
      },
      success: function (data) {
        response = data;
        formatedPrices[parseFloat(price)] = data;
      },
    });
    return response;
  }
}

function formatNumber(value, numberOfDecimal, thousenSeparator, virgule) {
  value = value.toFixed(numberOfDecimal);
  var val_string = value + "";
  var tmp = val_string.split(".");
  var abs_val_string = tmp.length === 2 ? tmp[0] : val_string;
  var deci_string = ("0." + (tmp.length === 2 ? tmp[1] : 0)).substr(2);
  var nb = abs_val_string.length;

  for (var i = 1; i < 4; i++)
    if (value >= Math.pow(10, 3 * i))
      abs_val_string =
        abs_val_string.substring(0, nb - 3 * i) +
        thousenSeparator +
        abs_val_string.substring(nb - 3 * i);

  if (parseInt(numberOfDecimal) === 0) return abs_val_string;
  return abs_val_string + virgule + (deci_string > 0 ? deci_string : "00");
}
Number.prototype.round = function (p) {
  p = p || 10;
  return parseFloat(this.toFixed(p));
};

function ps_round(value, places) {
  if (typeof roundMode === "undefined") roundMode = 2;
  if (typeof places === "undefined") places = 2;

  var method = roundMode;

  if (method === 0) return ceilf(value, places);
  else if (method === 1) return floorf(value, places);
  else if (method === 2) return ps_round_half_up(value, places);
  else if (method == 3 || method == 4 || method == 5) {
    // From PHP Math.c
    var precision_places = 14 - Math.floor(ps_log10(Math.abs(value)));
    var f1 = Math.pow(10, Math.abs(places));

    if (precision_places > places && precision_places - places < 15) {
      var f2 = Math.pow(10, Math.abs(precision_places));
      if (precision_places >= 0) tmp_value = value * f2;
      else tmp_value = value / f2;

      tmp_value = ps_round_helper(tmp_value, roundMode);

      /* now correctly move the decimal point */
      f2 = Math.pow(10, Math.abs(places - precision_places));
      /* because places < precision_places */
      tmp_value /= f2;
    } else {
      /* adjust the value */
      if (places >= 0) tmp_value = value * f1;
      else tmp_value = value / f1;

      if (Math.abs(tmp_value) >= 1e15) return value;
    }

    tmp_value = ps_round_helper(tmp_value, roundMode);
    if (places > 0) tmp_value = tmp_value / f1;
    else tmp_value = tmp_value * f1;

    return tmp_value;
  }
}

function ps_round_half_up(value, precision) {
  var mul = Math.pow(10, precision);
  var val = value * mul;

  var next_digit = Math.floor(val * 10) - 10 * Math.floor(val);
  if (next_digit >= 5) val = Math.ceil(val);
  else val = Math.floor(val);

  return val / mul;
}

// colorize
var low = { r: 255, g: 255, b: 0 },
  mid = { r: 255, g: 0, b: 255 }, // mid
  high = { r: 0, g: 200, b: 200 }; // high

function updateColorLabels() {
  // set the swatch color
  $($("#LowColorPicker .ColorBlotch")[0]).css(
    "background-color",
    toRGBCSS(low)
  );
  $($("#MidColorPicker .ColorBlotch")[0]).css(
    "background-color",
    toRGBCSS(mid)
  );
  $($("#HighColorPicker .ColorBlotch")[0]).css(
    "background-color",
    toRGBCSS(high)
  );

  // show the color in the label (rgb | hex)
  $("#LowColorPicker .label").html(
    "Outline: rgb(" +
      low.r +
      ", " +
      low.b +
      ", " +
      low.g +
      ")  " +
      NDKrgbToHex(low.r, low.b, low.g)
  );
  $("#MidColorPicker .label").html(
    "Body: rgb(" +
      mid.r +
      ", " +
      mid.b +
      ", " +
      mid.g +
      ") " +
      NDKrgbToHex(mid.r, mid.b, mid.g)
  );
  $("#HighColorPicker .label").html(
    "Highlight: rgb(" +
      high.r +
      ", " +
      high.b +
      ", " +
      high.g +
      ") " +
      NDKrgbToHex(high.r, high.b, high.g)
  );
}

function updateView(_low, _mid, _high, options) {
  // colorize the first image
  colorizeImg("#headImage", "#headCanvas", "#headTarget", low, mid, high, {
    base: 100,
    high: 190,
  });
}

function toRGBCSS(rgb) {
  // convert object to css data
  return "rgb(" + rgb.r + "," + rgb.b + "," + rgb.g + ")";
}

function cleanUpColor(str) {
  // convert css string to object
  var color = str
    .replace(" ", "")
    .replace(" ", "")
    .replace("rgb(", "")
    .replace(")", "")
    .split(",");
  return { r: color[0], b: color[1], g: color[2] };
}

// click events for swatch changes
$("#LowColorPicker .ColorBlotch").bind("click", function () {
  updateView((low = cleanUpColor($(this).css("background-color"))), mid, high);
});

// [r|g|b]->hex
function NDKcomponentToHex(c) {
  var hex = Math.floor(c).toString(16);
  if (hex.length < 2) hex = "0" + hex;
  return hex;
}

// [r,g,b]->hex
function NDKrgbToHex(r, g, b) {
  return (
    "#" + NDKcomponentToHex(r) + NDKcomponentToHex(g) + NDKcomponentToHex(b)
  );
}

function NDKhexToRgb(hex) {
  // http://stackoverflow.com/questions/5623838/rgb-to-hex-and-hex-to-rgb
  // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
  var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
  hex = hex.replace(shorthandRegex, function (m, r, g, b) {
    return r + r + g + g + b + b;
  });

  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result
    ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16),
      }
    : null;
}

// convert original img src to new img with a canvas pass-through and alter colors
function colorizeImg(
  sourceImg,
  targetCanvas,
  targetImg,
  low,
  mid,
  high,
  options
) {
  // colors
  lowColor = low;
  midColor = mid;
  highColor = high;

  // threshold
  var base = 70;
  var high = 200;

  // get source data
  // grab image width/height from data
  image = $(sourceImg).get(0);
  var width = image.width;
  var height = image.height;

  // customize threshold
  if (options != undefined) {
    if (options["base"] != undefined) base = options["base"];
    if (options["high"] != undefined) high = options["high"];
    if (options["width"] != undefined) width = options["width"];
    if (options["height"] != undefined) height = options["height"];
  }

  // console.log(width + "  " + height);
  $(targetCanvas)[0].setAttribute("width", width);
  $(targetCanvas)[0].setAttribute("height", height);

  var canvas = $(targetCanvas).get(0);
  (context = canvas.getContext("2d")), context.drawImage(image, 0, 0);

  // grab the pixel data
  var imgd = context.getImageData(0, 0, width, height),
    pixels = imgd.data,
    pixelsLen = pixels.length;

  // Loop through all pixels
  for (var i = 0, n = pixelsLen; i < n; i += 4) {
    // average the pixel colors to get threshold
    var average = (pixels[i] + pixels[i + 1] + pixels[i + 2]) / 3;
    var color = lowColor;
    if (average > base) {
      color = average > high ? highColor : midColor;
    }

    // set colors
    pixels[i] = color.r; // RED
    pixels[i + 1] = color.b; // BLUE
    pixels[i + 2] = color.g; // GREEN
    //pixels[i+3] transparency
  }
  // apply the new pixel info to the canvas
  context.putImageData(imgd, 0, 0);

  // apply the next data to the target image src
  $(targetImg).attr("src", canvas.toDataURL("image/png"));
}

function utf8_to_b64(str) {
  return window.btoa(decodeURIComponent(encodeURIComponent(str)));
}

function b64_to_utf8(str) {
  return decodeURIComponent(encodeURIComponent(window.atob(str)));
}

function ndkZipEncode(data) {
  return utf8_to_b64(RawDeflate.deflate(encodeURIComponent(data)));
}

function addSymbolsToText() {
  var symbols = ["♬", "♛", "☂"];

  $(".symbol-select").remove();
  $(".noborder")
    .after('<select class="symbol-select"></select>')
    .trigger("blur");

  $(".symbol-select").append('<option value="">symbols</option>');
  for (var i = 0; i < symbols.length; i++) {
    $(".symbol-select").append(
      '<option value="' + symbols[i] + '">' + symbols[i] + "</option>"
    );
  }

  $(document).on("change", ".symbol-select", function () {
    targetField = $(this).prev(".noborder");
    currVal = targetField.val();
    targetField.val(currVal + $(this).val());
  });
}

function getParameterByName(name) {
  name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
  return results == null
    ? ""
    : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function updateQueryStringParameter(uri, key, value) {
  var re = new RegExp("([?|&])" + key + "=.*?(&|$)", "i");
  var separator = uri.indexOf("?") !== -1 ? "&" : "?";
  if (uri.match(re)) {
    return uri.replace(re, "$1" + key + "=" + value + "$2");
  } else {
    return uri + separator + key + "=" + value;
  }
}

$.fn.getVisibleOffset = function () {
  var scrollTop = $(window).scrollTop(),
    scrollBot = scrollTop + $(window).height(),
    elTop = $(this).offset().top,
    elBottom = elTop + $(this).outerHeight(),
    visibleTop = elTop < scrollTop ? scrollTop : elTop,
    visibleBottom = elBottom > scrollBot ? scrollBot : elBottom;
  return visibleBottom - visibleTop;
};

// Resize popup
$(window).resize(function () {
  if ($(".ndk-imgs-autoHeight").length > 0) autoHeightPopup();
});
$("#ndkacf-modal").on("shown.bs.modal", function (e) {
  if ($(".ndk-imgs-autoHeight").length > 0) autoHeightPopup();
});
function autoHeightPopup() {
  $(".ndk-imgs-autoHeight").css({
    width: "",
    height: "",
  });
  o_width = $(".ndk-imgs-autoHeight").innerWidth();
  o_height = $(".ndk-imgs-autoHeight").innerHeight();
  p_height = $("#imgs-bloc-popup").height();
  ratio = o_height / o_width;

  new_height = p_height;
  new_width = p_height / ratio;

  $(".ndk-imgs-autoHeight").css({
    width: new_width,
    height: new_height,
  });
}
// Resize popup

function linkAttrToNdkAcf(id_attr, id_ndkacf) {
  $(".form-group[data-field=" + id_ndkacf + "]").hide();
  $("#group_" + id_attr).trigger("change");
  $(document).on("change", "#group_" + id_attr, function () {
    selected = $(this).find("option:selected").attr("title");
    $("select#ndkcsfield_" + id_ndkacf)
      .val(selected)
      .trigger("change");
  });
}

//resize bar
$.widget("ui.resizable", $.ui.resizable, {
  resizeTo: function (newSize) {
    var start = new $.Event("mousedown", { pageX: 0, pageY: 0 });
    this._mouseStart(start);
    this.axis = "se";
    var end = new $.Event("mouseup", {
      pageX: newSize.width - this.originalSize.width,
      pageY: newSize.height - this.originalSize.height,
    });
    this._mouseDrag(end);
    this._mouseStop(end);
  },
});

function setResizeRange(group) {
  $("#ndk-resize-bar-" + group).remove();
  $("#visual_" + group + ".absolute-visu.ui-resizable").each(function () {
    container = $(this).parent();
    container = $(".form-group[data-field='" + group + "'] .fieldPane");
    if (container.length == 0) container = $("#item-" + group);
    resizeBar =
      '<div class="ndk-resize-bar clear clearfix" id="ndk-resize-bar-' +
      group +
      '"><input data-target="#' +
      $(this).attr("id") +
      '" type="range" data-group="' +
      group +
      '" min="0" max="2000" value="' +
      $(this).width() +
      '"/></div>';

    container.append(resizeBar);
  });
}

$(document).on("change", ".ndk-resize-bar input", function () {
  newWidth = $(this).val();
  ndkMovables[$(this).data("group")].request(
    "resizable",
    { offsetWidth: newWidth },
    true
  );
});
// $(document).on("change", ".ndk-resize-bar input", function () {
//   newWidth = $(this).val();
//   $($(this).data("target")).resizable("resizeTo", {
//     width: newWidth,
//     height: newWidth,
//   });
// });

function setSvgLineHeight(group, option) {
  if (typeof option == "undefined") option = 0;
  option = parseInt(option);
  if (typeof option == "number" && option > 0) {
    fontSize = parseInt(
      $("#visual_" + group + " .textareaSvg:eq(0)").css("font-size")
    );
    i = 1;
    line_height = fontSize + option;
    $("#svgText_" + group)
      .find("tspan")
      .each(function () {
        $(this).attr("y", line_height * i);
        i++;
      });
  }
}

function addParameterToURL(param, sourceURL) {
  _url = sourceURL;
  _url += (_url.indexOf("?") !== -1 ? "&" : "?") + param;
  return _url;
}

function removeParamUrl(key, sourceURL) {
  var rtn = sourceURL.split("?")[0],
    param,
    params_arr = [],
    queryString = sourceURL.indexOf("?") !== -1 ? sourceURL.split("?")[1] : "";
  if (queryString !== "") {
    params_arr = queryString.split("&");
    for (var i = params_arr.length - 1; i >= 0; i -= 1) {
      param = params_arr[i].split("=")[0];
      if (param === key) {
        params_arr.splice(i, 1);
      }
    }
    rtn = rtn + (params_arr.lenght > 0 ? "?" + params_arr.join("&") : "");
  }
  return rtn;
}

//resize bar

String.prototype.replaceAll = function (str1, str2, ignore) {
  return this.replace(
    new RegExp(
      str1.replace(/([\/\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g, "\\$&"),
      ignore ? "gi" : "g"
    ),
    typeof str2 == "string" ? str2.replace(/\$/g, "$$$$") : str2
  );
};

var pad = function (num, totalChars) {
  var pad = "0";
  num = num + "";
  while (num.length < totalChars) {
    num = pad + num;
  }
  return num;
};

function resizeMasonryItem(grid, item) {
  var rowGap = parseInt(
      window.getComputedStyle(grid).getPropertyValue("grid-row-gap")
    ),
    rowHeight = 0;
  var rowSpan = Math.ceil(
    (item.getBoundingClientRect().height + rowGap) / (rowHeight + rowGap)
  );
  /* Set the spanning as calculated above (S) */
  item.parentNode.style.gridRowEnd = "span " + rowSpan;
}
function resizeMasonryGallery(group = false) {
  //on fait rien
}
function resizeMasonryGallery_keep(group = false) {
  if (group)
    var galleries = $(".form-group[data-field=" + group + "]").find(
      ".image-library"
    );
  else var galleries = $(".image-library");
  galleries.each(function () {
    resizeAllMasonryItems($(this)[0]);
  });
}

function resizeAllMasonryItems(grid) {
  var allItems = grid.querySelectorAll(".img-value, .svg-container");
  for (var i = 0; i < allItems.length; i++) {
    resizeMasonryItem(grid, allItems[i]);
  }
}

function changeScale(elem, newScale) {
  if (elem.style.transform == "")
    elem.style.transform = "scale(" + newScale + ")";
  else
    elem.style.transform = elem.style.transform.replace(
      /scale\([0-9|\.]*\)/,
      "scale(" + newScale + ")"
    );
}

function changeMatrix(elem, newMartix) {
  if (elem.style.transform == "")
    elem.style.transform = "matrix3d(" + newMartix + ")";
  else
    elem.style.transform = elem.style.transform.replace(
      /matrix3d\([0-9|\.]*\)/,
      "matrix3d(" + newMartix + ")"
    );
}

function changeRotate(elem, newRotate) {
  if (elem.style.transform == "")
    elem.style.transform = "rotate(" + newRotate + "deg)";
  else
    elem.style.transform = elem.style.transform.replace(
      /rotate\(([^\)]+)\).*/,
      "rotate(" + newRotate + "deg)"
    );
}

$(document).on("click", ".btn-ndkacf-scroll", function (e) {
  e.preventDefault();
  $(".ndkackFieldItem .toggler:not(.active)").trigger("click");
  scrollToNdk($($(this).attr("data-target")), 800, true);
});
