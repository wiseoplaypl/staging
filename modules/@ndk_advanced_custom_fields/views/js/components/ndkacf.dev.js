//utile pour plugger des select attributs ensembles
$(document).on(
  "change",
  "[data-custom_class='attr_1'] .ndk_attribute_select",
  function () {
    search = $(this).find("option:selected").attr("title");
    $("[data-custom_class='attr_2'] .ndk_attribute_select").each(function () {
      var newVal = false;
      me = $(this);
      me.find("option").each(function () {
        $(this).prop("disabled", true);
        lookAt = $(this).text();
        //console.log(search, lookAt, lookAt.indexOf(search));
        if (lookAt.indexOf(search) > -1) {
          $(this).prop("disabled", false);
          newVal = $(this).attr("value");
        }
      });
      if (newVal) {
        me.val(newVal);
      }
    });
  }
);

//utile pour fixer le quota d'un champs accessoire depuis un select
$(document).on(
  "change",
  "[data-custom_class^='setQuota'] .ndk-select",
  function (e) {
    me = $(this);
    group = me.attr("data-group");
    rootGroupBlock = $(".form-group[data-field='" + group + "']");
    selector =
      "[data-custom_class='" + rootGroupBlock.attr("data-custom_class") + "']";
    var groupsTarget = $(selector);
    currentIndex = groupsTarget.index(rootGroupBlock);
    groupsTarget.attr("data-qtty-min", parseInt(me.val()));
    groupsTarget.attr("data-qtty-max", parseInt(me.val()));
    groupsTarget.find(".quantity_error_up span").html(parseInt(me.val()));
    groupsTarget.find(".quantity_error_down span").html(parseInt(me.val()));
    groupsTarget.find(".ndk-accessory-quantity[value!=0]").trigger("change");
  }
);

//utile pour coloriser un autre champs en appliquant le masque
$(document).on("click", "[data-custom_class='colorize_1'] img", function () {
  data_group = $(this).attr("data-group");
  target_id = $(".form-group[data-custom_class='colorize_2']").attr(
    "data-field"
  );
  colorizeDynamiqueMaskNdk(target_id, $(this));
});

function colorizeDynamiqueMaskNdk(data_group, el) {
  value = el.attr("data-src");
  $("#visual_" + data_group).remove();
  $('.form-group[data-field="' + data_group + '"] .ndk_color_list li').attr(
    "data-mask-image",
    value
  );
  $(
    '.form-group[data-field="' +
      data_group +
      '"] .ndk_color_list .selected-value'
  ).click();
}

//utile pour mettre en avant la line quantity discount en fonction de la quantité choisie
function hightLightQttyDiscount() {
  $(".table-quantity-discount-container").each(function () {
    id_group = $(this).attr("data-id-group");
    id_value = $(this).attr("data-id-value");
    if (typeof groupMultiply[id_group] != "undefined") {
      qtty = parseInt(groupMultiply[id_group].quantity);
      $(".quantityDiscount_" + id_value).each(function () {
        if (parseInt($(this).attr("data-discount-quantity")) < qtty) {
          $(".quantityDiscount_" + id_value).removeClass("hightlight");
          $(this).addClass("hightlight");
        }
      });
    }
  });
}

$(document).on("ndkacf:updatePriceNdk", function () {
  hightLightQttyDiscount();
});

//modification Taxe Rule Group
$(document).on("ndkacf:ndkRadioSet", function (e) {
  setnewTaxRatio(e);
  if (e.force_carrier) setNewCarrier(e);
});
$(document).on("ndkacf:ndkCheckboxSet", function (e) {
  setnewTaxRatio(e);
  if (e.force_carrier) setNewCarrier(e);
});
$(document).on("ndkacf:ndkSelectSet", function (e) {
  setnewTaxRatio(e);
});
$(document).on("ndkacf:ndkImageSet", function (e) {
  setnewTaxRatio(e);
  if (e.force_carrier) setNewCarrier(e);
});

var forceTaxRatio = [];
var newTaxRatio = 1;
var newTaxRuleGroup = false;
var forceCarrier = [];
var newCarrierId = false;

function setNewCarrier(e) {
  isForced = false;
  carrier_id = newCarrierId;
  if (typeof e.force_carrier != "undefined") {
    forceCarrier[e.group] = {
      id_carrier: e.force_carrier,
    };
  } else if (e.force_tax_rule === false) {
    delete forceCarrier[e.group];
  } else {
    forceCarrier[e.group] = false;
  }
  for (idGroup in forceCarrier) {
    if (forceCarrier[idGroup]) {
      isForced = true;
      carrier_id = forceCarrier[idGroup].id_carrier;
    }
  }
  if (!isForced || carrier_id == "") {
    carrier_id = false;
  }
  newCarrierId = carrier_id;
  console.log(newCarrierId);
}
function setnewTaxRatio(e) {
  //console.log(e.group, e.tax_ratio, e.force_tax_rule, e.force_carrier);
  newTaxRatio = 1;
  isForced = false;
  tax_ratio = newTaxRatio;
  force_tax_rule = newTaxRuleGroup;
  if (typeof e.tax_ratio != "undefined") {
    forceTaxRatio[e.group] = {
      tax_ratio: 1 * e.tax_ratio,
      back_ratio: 1 / e.tax_ratio,
      force_tax_rule: e.force_tax_rule,
    };
  } else if (e.force_tax_rule === false) {
    delete forceTaxRatio[e.group];
  } else {
    forceTaxRatio[e.group] = false;
  }
  for (idGroup in forceTaxRatio) {
    if (forceTaxRatio[idGroup]) {
      isForced = true;
      tax_ratio = forceTaxRatio[idGroup].tax_ratio;
      force_tax_rule = forceTaxRatio[idGroup].force_tax_rule;
    }
  }
  if (!isForced || tax_ratio == "") {
    tax_ratio = 1;
    force_tax_rule = false;
  }
  if (ndkPriceDisplay == 1) {
    newTaxRatio = 1;
  } else {
    newTaxRatio = tax_ratio;
    setTimeout(function () {
      recalculatePrices(newTaxRatio);
    }, 500);
  }

  newTaxRuleGroup = force_tax_rule;
}

function recalculatePrices(tax_ratio = newTaxRatio) {
  //console.log(newTaxRatio, newTaxRuleGroup);
  for (idPrice in groupUnitPrice) {
    updatePriceNdk(groupUnitPrice[idPrice], idPrice, 0, true);
  }
  setTimeout(function () {
    recalculateItemsPrices();
  }, 500);
}

function recalculateItemsPrices() {
  document
    .querySelectorAll(".ndkcf-value-price:not(.dontConvertRatio)")
    .forEach(function (node) {
      myTaxRatio = newTaxRatio;
      nodePrice = node.textContent.replace(currencySign, "");
      nodePrice = parseFloat(nodePrice.replace(",", "."));
      if (typeof node.dataset.originalPrice == "undefined") {
        node.dataset.originalPrice = nodePrice;
      }
      newPrice = parseFloat(node.dataset.originalPrice) * myTaxRatio;
      node.dataset.taxRatio = newTaxRatio;
      node.textContent = formatCurrencyNdk(newPrice);
    });
}

$(document).ready(function () {
  if ($(".zone_limit").length < 1) $(".activate-zone-edit").hide();
});

$(document).on("click", ".activate-zone-edit", function (e) {
  if (!ndk_admin_loggued) {
    alert("petit coquin!");
    return false;
  }
  if ($(this).hasClass("active")) {
    $(this).removeClass("active");
    unsetZonesEditables();
  } else {
    $(this).addClass("active");
    setZonesEditables();
  }
});
function unsetZonesEditables() {
  $(".ndk-tool-zone-save").remove();
  $("body").removeClass("ndk-zones-editing");
  $(".zone_limit").each(function () {
    group = me.attr("data-group") + "-zone";
    if (typeof ndkMovables[group] == "object") ndkMovables[group].destroy();
  });
}

function setZonesEditables() {
  $("body").addClass("ndk-zones-editing");
  $(".zone_limit").each(function () {
    me = $(this);
    group = me.attr("data-group") + "-zone";
    containment = me.parent();
    zindex = 2;
    layerOptions = "";

    dragdrop = 1;
    resizeable = 1;
    rotateable = 0;
    setEditable(
      me,
      zindex,
      containment,
      layerOptions,
      dragdrop,
      resizeable,
      rotateable,
      group,
      true
    );
    saveButton =
      '<span id="ndk-tool-zone-save-' +
      group +
      '" data-group="' +
      group +
      '" class="material-icons-outlined ndk-tool-zone-save tool-item ndk-zone-only" style="display:none" title="save">save</span>';

    $(this).after(saveButton);
  });
}

$(document).on("click", ".tool-item.ndk-tool-zone-save", function (e) {
  e.preventDefault();
  me = $(this);
  group = $(this).attr("data-group").replace("-zone", "");
  el = $(".zone_limit[data-group=" + group + "]");
  dragToPercent(el);
  offset = {
    width: el[0].style.width.replace("%", ""),
    height: el[0].style.height.replace("%", ""),
    top: el[0].style.top.replace("%", ""),
    left: el[0].style.left.replace("%", ""),
  };
  $.ajax({
    type: "GET",
    url:
      baseUrl +
      "modules/ndk_advanced_custom_fields/front_ajax.php?action=setZoneAjax",
    data: {
      group: group,
      width: el[0].style.width.replace("%", ""),
      height: el[0].style.height.replace("%", ""),
      top: el[0].style.top.replace("%", ""),
      left: el[0].style.left.replace("%", ""),
    },
    success: function (data) {
      el.addClass("zone-saved");
      setTimeout(function () {
        el.removeClass("zone-saved");
      }, 2000);
    },
  });
  //console.log(offset);
});
