/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
 */

zoneCurrent = 0;
selectionCurrent = null;
valueOfZoneEdited = null;
dragndrop = false;

// Last item is used to save the current zone and
// allow to replace it if user cancel the editing
lastEditedItem = null;

/* functions called by cropping events */

function showZone() {
  $("#large_scene_image").imgAreaSelect({ show: true });
}

function hideAutocompleteBox() {
  $(".ajax_choose_product")
    .fadeOut("fast")
    .find(".product_autocomplete_input")
    .val("");
}

function onSelectEnd(img, selection) {
  selectionCurrent = selection;
  percentRes = {};
  newWidth = img.clientWidth;
  newHeight = img.clientHeight;

  percentRes.width = ((selection.width / newWidth) * 100).toFixed(2);
  percentRes.height = ((selection.height / newHeight) * 100).toFixed(2);

  percentRes.x1 = ((selection.x1 / newWidth) * 100).toFixed(2);
  percentRes.y1 = ((selection.y1 / newHeight) * 100).toFixed(2);

  showAutocompleteBox(
    percentRes.x1,
    percentRes.y1,
    percentRes.height,
    percentRes.width
  );
}

function undoEdit() {
  //hideAutocompleteBox();
  $("#large_scene_image").imgAreaSelect({ hide: true });
  $(document).unbind("keydown");
}

/*
 ** Pointer function do handle event by key released
 */
function handlePressedKey(keyNumber, fct) {
  // KeyDown isn't handled correctly in editing mode
  $(document).keyup(function (event) {
    if (event.keyCode == keyNumber) fct();
  });
}

function showAutocompleteBox(x, y, h, w) {
  $("input[name='x_axis']").val(x);
  $("input[name='y_axis']").val(y);
  $("input[name='zone_width']").val(w);
  $("input[name='zone_height']").val(h);
  $("#zone_mode").remove();
  $("input[name='zone_height']").after(
    '<input id="zone_mode" class="serialize_input" name="false_options[zone_mode]" />'
  );
  $("input[name='false_options[zone_mode]']").val("percent").trigger("change");

  handlePressedKey("27", undoEdit);

  type = $("#type").val();
  if (dragndrop && (type == 17 || type == 22 || type == 24)) {
    zoneCurrent++;
    addZone(zoneCurrent, x, y, w, h);
  }
}

/* function called by cropping process (buttons clicks) */

function deleteProduct(index_zone) {
  $("#visual_zone_" + index_zone).fadeOut("fast", function () {
    $(this).remove();
  });
  return false;
}

function getProdsIds() {
  if ($("#inputAccessories").val() === undefined) return "";
  return $("#inputAccessories").val().replace(/\-/g, ",");
}

function addZone(zoneIndex, x1, y1, width, height, mode) {
  mode = mode || "";
  $("input[name='x_axis']").val(x1);
  $("input[name='y_axis']").val(y1);
  $("input[name='zone_width']").val(width);
  $("input[name='zone_height']").val(height);
  if (mode == "percent") unity = "%";
  else unity = "px";

  $("#large_scene_image")
    .imgAreaSelect({ hide: true })
    .before(
      '\
			<div class="fixed_zone" id="visual_zone_' +
        zoneIndex +
        '" style="color:black;overflow:hidden; left:' +
        x1 +
        unity +
        "; top:" +
        y1 +
        unity +
        "; width:" +
        width +
        unity +
        "; height :" +
        height +
        unity +
        '; background-color:white;border:1px solid black; position:absolute;">\
				<input type="hidden" name="zones[' +
        zoneIndex +
        '][x1]" value="' +
        (x1 -
          parseInt(
            $("#large_scene_image").css("margin-left").replace("px", "")
          )) +
        '"/>\
				<input type="hidden" name="zones[' +
        zoneIndex +
        '][y1]" value="' +
        (y1 -
          parseInt(
            $("#large_scene_image").css("margin-top").replace("px", "")
          )) +
        '"/>\
				<input type="hidden" name="zones[' +
        zoneIndex +
        '][width]" value="' +
        width +
        '"/>\
				<input type="hidden" name="zones[' +
        zoneIndex +
        '][height]" value="' +
        height +
        '"/>\
				<p class="zone_name">Zone ' +
        zoneIndex +
        '</p>\
				<a style="margin-left:' +
        (parseInt(width) / 2 - 16) +
        "px; margin-top:" +
        (parseInt(height) / 2 - 8) +
        'px; position:absolute;" href="#" onclick="{resetZone(' +
        zoneIndex +
        '); return false;}">\
					<img src="../img/admin/delete.gif" alt="" />\
				</a>\
				<a style="margin-left:' +
        parseInt(width) / 2 +
        "px; margin-top:" +
        (parseInt(height) / 2 - 8) +
        'px; position:absolute;" href="#" onclick="{editThisZone(this); return false;}">\
					\
				</a>\
			</div>\
		'
    );
  $(".fixed_zone").css("opacity", "0.8");
  $("#save_scene").fadeIn("slow");
  $(".ajax_choose_product:visible").find(".product_autocomplete_input").val("");
}

function resetZone(index_zone) {
  $("input[name='x_axis']").val(0);
  $("input[name='y_axis']").val(0);
  $("input[name='zone_width']").val(0);
  $("input[name='zone_height']").val(0);
  $("#visual_zone_" + index_zone).fadeOut("fast", function () {
    $(this).remove();
  });
  return false;
}

$(document).ready(function () {
  if ($(".ndk-main").length == 0) {
    $(".admin-form-tabs").remove();
  }
  $("#table-ndk_customization_field select").chosen();
  $(".ndk-tab.active").trigger("click");

  if (typeof parentType != "undefined") setTypesFields(parentType);

  $("#target").change(function () {
    getTargets();
  });
  $("#target").trigger("change");

  $("#type").change(function () {
    setTypesFields($(this).val());
  });

  $("#type").trigger("change");

  $("input.product-result").removeClass("ndk-visible").addClass("ndk-hidden");

  $(".setAdminName").trigger("keyup");

  if (typeof tryThisFilter != "undefined") {
    tryThisFilterBlock =
      '<span class="filter_attention">' + tryThisFilter + "</span>";
    $("[name='ndk_customization_fieldFilter_p!id_product']").after(
      tryThisFilterBlock
    );
  }
  $(".categoryprodcheckbox").change(function () {
    if ($(this).is(":checked")) {
      $(this).parent().parent().find(".prodcheckbox").prop("checked", true);
      $(this)
        .parent()
        .parent()
        .find(".tree-item-name")
        .addClass("tree-selected");
    } else {
      $(this).parent().parent().find(".prodcheckbox").prop("checked", false);
      $(this)
        .parent()
        .parent()
        .find(".tree-selected")
        .removeClass("tree-selected");
    }
  });

  setTimeout(function () {
    $(".serialize_input").trigger("change");
    $(".implode_input").trigger("change");
    makeFreeHack();
  }, 1500);
});

function getTargets() {
  if ($("#target_child").length > 0) {
    tChild = $("#target_child").val();
  } else {
    tChild = target_child;
  }
  $("#target_zoning").remove();
  $.ajax({
    type: "POST",
    //url: '../modules/ndk_advanced_custom_fields/admin_ajax.php',
    url:
      currentIndex +
      "&token=" +
      token +
      "&query_ajax_request&action=getTargetChild&ajax=1",
    data: {
      getTargetChild: 1,
      id_target: $("#target").val(),
      target_child: tChild,
      svg_path: svgPath,
    },
    success: function (data) {
      $("#target_chosen").after(data);
      initAreaSelect();
    },
  });
}

$(document).on("change", "#svg_path", function (e) {
  if ($(this).val != "") {
    $("path#" + $(this).val()).css("stroke", "blue");
    $("g#" + $(this).val()).css("stroke", "blue");
  }
});

function initAreaSelect() {
  if ($("#large_scene_image").length > 0) {
    $("#large_scene_image > svg  path").each(function () {
      if (typeof $(this).attr("id") != "undefined") {
        $("#svg_path").append(
          '<option value="' +
            $(this).attr("id") +
            '">' +
            $(this).attr("id") +
            "</option>"
        );
      }
    });
    setTimeout(function () {
      $("#svg_path").val($("#svg_path").attr("data-value")).trigger("change");
    }, 1000);

    $("#large_scene_image").imgAreaSelect({
      borderWidth: 1,
      onSelectEnd: onSelectEnd,
      onSelectStart: showZone,
      //onSelectChange: hideAutocompleteBox,
      minHeight: 30,
      minWidth: 30,
      parent: "#zone_container",
    });

    zoneCurrent = startingData.length;
    //console.log(startingData)
    for (var i = 0; i < startingData.length; i++) {
      addZone(
        zoneCurrent,
        startingData[i][2] +
          parseInt(
            $("#large_scene_image").css("margin-left").replace("px", "")
          ),
        startingData[i][3] +
          parseInt($("#large_scene_image").css("margin-top").replace("px", "")),
        startingData[i][4],
        startingData[i][5],
        startingData[i][6]
      );
    }

    //zoneCurrent++;
  }
}

function setTypesFields_back(type) {
  console.log(type);
  $(".hidden-field")
    .parent()
    .parent()
    .removeClass("ndk-visible")
    .addClass("ndk-hidden");
  $(".visible-field")
    .parent()
    .parent()
    .removeClass("ndk-hidden")
    .addClass("ndk-visible");
  $("span.visible-field")
    .parent()
    .parent()
    .parent()
    .removeClass("ndk-hidden")
    .addClass("ndk-visible");
  $("span.hidden-field")
    .parent()
    .parent()
    .parent()
    .removeClass("ndk-visible")
    .addClass("ndk-hidden");

  $(".hidden-" + type)
    .parent()
    .parent()
    .removeClass("ndk-visible")
    .addClass("ndk-hidden");
  $("span.hidden-" + type)
    .parent()
    .parent()
    .parent()
    .removeClass("ndk-visible")
    .addClass("ndk-hidden");
  $(".visible-" + type)
    .parent()
    .parent()
    .removeClass("ndk-hidden")
    .addClass("ndk-visible");
  $("span.visible-" + type)
    .parent()
    .parent()
    .parent()
    .removeClass("ndk-hidden")
    .addClass("ndk-visible");

  $(".hidden-note").removeClass("ndk-visible").addClass("ndk-hidden");
  $(".visible-note").parent().removeClass("ndk-hidden").addClass("ndk-visible");
  $(".hidden-note-" + type)
    .removeClass("ndk-visible")
    .addClass("ndk-hidden");
  $(".visible-note-" + type)
    .removeClass("ndk-hidden")
    .addClass("ndk-visible");
}

function setTypesFields(type) {
  $(".hidden-field").removeClass("ndk-visible").addClass("ndk-hidden");
  $(".visible-field").removeClass("ndk-hidden").addClass("ndk-visible");
  $(".hidden-" + type)
    .removeClass("ndk-visible")
    .addClass("ndk-hidden");
  $(".visible-" + type)
    .removeClass("ndk-hidden")
    .addClass("ndk-visible");

  $(".hidden-note").removeClass("ndk-visible").addClass("ndk-hidden");
  $(".visible-note").parent().removeClass("ndk-hidden").addClass("ndk-visible");
  $(".hidden-note-" + type)
    .removeClass("ndk-visible")
    .addClass("ndk-hidden");
  $(".visible-note-" + type)
    .removeClass("ndk-hidden")
    .addClass("ndk-visible");
}

$(document).on("click", ".ndk-tab", function () {
  $(".ndk-tab").removeClass("active");
  $(this).addClass("active");
  $(".form-group").addClass("hidden-group").removeClass("visible-group");
  $("." + $(this).attr("data-target"))
    .addClass("visible-group")
    .removeClass("hidden-group");
  $("." + $(this).attr("data-target"))
    .find(".form-group")
    .removeClass("hidden-group");
  $("#type").trigger("change");
});

$(window).load(function () {
  $(".editable-value").attr("onclick", "");
  loadBot();
  if ($(".product_autocomplete_input").length > 0) {
    /* function autocomplete */
    $(".product_autocomplete_input")
      .autocomplete(
        currentIndex.replace("Group", "") +
          "&token=" +
          ndkacf_token +
          "&query_ajax_request&exclude_packs=false&excludeVirtuals=false&action=ajaxGetProducts&ajax=1",
        {
          minChars: 1,
          autoFill: true,
          max: 200,
          matchContains: true,
          mustMatch: true,
          scroll: false,
          //extraParams: {excludeIds : getProdsIds()}
          extraParams: { excludeIds: "9999999" },
        }
      )
      .result(afterTextInserted);
  }

  initAreaSelect();
});

function afterTextInserted(event, data, formatted) {
  if (data == null) return false;

  console.log(event.currentTarget);
  if (lastEditedItem != null) lastEditedItem.remove();
  lastEditedItem = null;
  zoneCurrent++;
  var idProduct = data[1];
  var nameProduct = data[0];
  targetInput = $(event.currentTarget)
    .parent()
    .parent()
    .parent()
    .parent()
    .find(".product-result");
  console.log(targetInput);
  oldVal = targetInput.val();
  oldValArray = oldVal.split(",");
  if (oldVal == "") separator = "";
  else separator = ",";

  if (targetInput.hasClass("only_one")) {
    newVal = idProduct;
    $(event.currentTarget).parent().parent().find(".prodrow").remove();
    $(".autoFillValue").each(function () {
      $(this).val(nameProduct);
    });
  } else {
    newVal = oldVal + separator + idProduct;
  }

  if ($.inArray(idProduct, oldValArray) == -1) {
    targetInput.val(newVal);
    newRow =
      '<button data-id="' +
      idProduct +
      '" class="btn btn-default prodrow" type="button"><i class="icon-remove"></i>' +
      nameProduct +
      "</button>";
    $(event.currentTarget).parent().parent().find(".prodlist").append(newRow);
  }
}

$(".prodrow").live("click", function () {
  idProduct = $(this).attr("data-id");
  oldVal = $(this)
    .parent()
    .parent()
    .parent()
    .parent()
    .find(".product-result")
    .val();
  oldValArray = oldVal.split(",");

  oldValArray.splice($.inArray(idProduct, oldValArray), 1);
  newVal = "";
  console.log(oldValArray);
  for (var i = 0; i < oldValArray.length; i++) {
    if (typeof oldValArray[i] != "undefined") {
      newVal += oldValArray[i] + (i < oldValArray.length - 1 ? "," : "");
    }
  }
  $(this)
    .parent()
    .parent()
    .parent()
    .parent()
    .find(".product-result")
    .val(newVal);
  $(this).remove();
});

var typingTimer;
var doneTypingInterval = 2000;

$(document).on("keyup", ".setAdminName", function () {
  newVal = $(this).val();
  clearTimeout(typingTimer);
  typingTimer = setTimeout(function () {
    $(".adminName").each(function () {
      if ($(this).val() == "") $(this).val(newVal);
    });
  }, doneTypingInterval);
});

$(document).on("click", "td.editable-value", function (e) {
  e.preventDefault();
  $(this).attr("contenteditable", "true").addClass("editableDom").focus();
});

$(document).on("blur", "td.editable-value.editableDom", function (e) {
  e.preventDefault();
  el = $(this);
  id_ndk_customization_field = $(this).parent().attr("id").split("_")[2];
  if ($(this).hasClass("set_positionndk_customization_field"))
    action = "set_positionndk_customization_field";
  else if ($(this).hasClass("set_zindexndk_customization_field"))
    action = "set_zindexndk_customization_field";
  else if ($(this).hasClass("set_ref_positionndk_customization_field"))
    action = "set_ref_positionndk_customization_field";

  position = parseFloat($(this).text());
  url =
    currentIndex +
    "&token=" +
    token +
    "&id_ndk_customization_field=" +
    id_ndk_customization_field +
    "&" +
    action +
    "=" +
    position;
  $.ajax({
    type: "GET",
    url: url,
    success: function (data) {
      el.attr("contenteditable", "false").removeClass("editableDom");
    },
  });
});

$(document).on("click", ".submitSpecificPrice", function (e) {
  e.preventDefault();
  saveSpecificPrice($(this));
});

function saveSpecificPrice(button) {
  myDatas = button.parent().find("input, select").serialize();
  $.ajax({
    type: "POST",
    //url: '../modules/ndk_advanced_custom_fields/admin_ajax.php?action=saveSpecificPrice',
    url:
      currentIndex +
      "&token=" +
      token +
      "&query_ajax_request&action=saveSpecificPrice&ajax=1",
    data: myDatas,
    success: function (data) {
      button.parent().find("input.id_specific_price").val(data);
      button.parent().append('<div id="saved">OK</div>');
      setTimeout(function () {
        $("#saved").remove();
      }, 2000);
    },
  });
}

$(document).on("click", ".removeSpecificPrice", function (e) {
  e.preventDefault();
  deleteSpecificPrice($(this));
});

function deleteSpecificPrice(button) {
  myDatas = button.parent().find("input, select").serialize();
  $.ajax({
    type: "POST",
    //url: '../modules/ndk_advanced_custom_fields/admin_ajax.php?action=deleteSpecificPrice',
    url:
      currentIndex +
      "&token=" +
      token +
      "&query_ajax_request&action=deleteSpecificPrice&ajax=1",
    data: myDatas,
    success: function (data) {
      button.parent().remove();
    },
  });
}

$(document).on("click", ".addSpecificPrice", function () {
  cloned = $(".specificPriceBlock_matrix").clone();
  topaste =
    '<div class="clear clearfix specificPriceBlock">' +
    cloned.html() +
    "</div>";
  $(".specificPriceBlock_matrix").after(topaste);
});

$(document).on("click", ".AjaxremoveFile", function () {
  parentBlock = $(this).parent();
  $.ajax({
    type: "POST",
    //url: '../modules/ndk_advanced_custom_fields/admin_ajax.php?action=deleteFile',
    url:
      currentIndex +
      "&token=" +
      token +
      "&query_ajax_request&action=deleteFile&ajax=1",
    data: { file: $(this).attr("data-file") },
    success: function () {
      parentBlock.fadeOut();
    },
  });
});

$(document).on("change", ".prodcheckbox", function () {
  me = $(this);
  sames = $(".prodcheckbox[value=" + me.val() + "]").not(me);

  if (me.is(":checked")) {
    sames.each(function () {
      $(this).prop("checked", true).parent().addClass("tree-selected");
    });
  } else {
    sames.each(function () {
      $(this).prop("checked", false).parent().removeClass("tree-selected");
    });
  }
});

$(document).on("keyup", "#quantity_min", function () {
  $("#weight_min, #weight_max").val(0);
});
$(document).on("keyup", "#quantity_max", function () {
  $("#weight_min, #weight_max").val(0);
});

$(document).on("keyup", "#weight_min", function () {
  $("#quantity_min, #quantity_max").val(0);
});
$(document).on("keyup", "#weight_max", function () {
  $("#quantity_min, #quantity_max").val(0);
});

//évite la modification du POST dans le controller
$(document).on(
  "change",
  "input.serialize_input, select.serialize_input, textarea.serialize_input, .serialize_input input, serialize_input select",
  function () {
    input_name = $(this).attr("name").split("[")[0];
    ndkModifyPost(input_name, "serialize");
  }
);

$(document).on("change", ".implode_input", function () {
  input_name = $(this).attr("name").split("[")[0];
  inputs = $("[name^='" + input_name + "']");
  values = [];
  selected = $(this).val();

  inputs.each(function () {
    if ($(this).is(":checked") || $(this).is(":selected"))
      if (
        $(this).val() != "" &&
        $(this).val() != " " &&
        typeof $(this).val() != "undefined" &&
        $(this).val() != "undefined"
      )
        values.push($(this).val());
  });

  if (Array.isArray(selected)) {
    for (i = 0; i <= selected.length; i++) {
      if (
        selected[i] != "" &&
        selected[i] != " " &&
        typeof selected[i] != "undefined"
      )
        values.push(selected[i]);
    }
  }
  //console.log(values.join(','))
  if ($("[name^='" + input_name.replace("false_", "") + "']").length == 0)
    $("[name^='" + input_name + "']:eq(0)").after(
      '<input type="hidden" class="modified_post" name="' +
        input_name.replace("false_", "") +
        '" />'
    );

  joined = values.join(",");

  $("input[name^='" + input_name.replace("false_", "") + "']").val(joined);
});

function ndkModifyPost(input_name, my_function) {
  values = $("[name^='" + input_name + "']").serializeObject();

  data = JSON.stringify(values);
  reg = new RegExp(input_name, "g");
  data = data.replace(reg, "");
  data = data.replace(/[\][]/g, "");
  //console.log(data)
  //console.log(values)
  if ($("[name^='" + input_name.replace("false_", "") + "']").length == 0)
    $("[name^='" + input_name + "']:eq(0)").after(
      '<input type="hidden" name="' + input_name.replace("false_", "") + '" />'
    );
  if (data.length == 0)
    $("input[name^='" + input_name.replace("false_", "") + "']").val("");
  else $("input[name^='" + input_name.replace("false_", "") + "']").val(data);
}

$.fn.serializeObject = function () {
  var o = {};
  var a = this.serializeArray();
  $.each(a, function () {
    if (o[this.name]) {
      if (!o[this.name].push) {
        o[this.name] = [o[this.name]];
      }
      o[this.name].push(this.value || "");
    } else {
      o[this.name] = this.value || "";
    }
  });
  return o;
};
//fin évite la modification du POST dans le controller

function loadBot() {
  if (typeof wpIframeUrl != "undefined") {
    document.getElementsByTagName("html")[0].style.background = "unset";
    document.getElementsByTagName("html")[0].style.backgroundColor = "unset";
    document.getElementsByTagName("body")[0].style.background = "#ffffff00";

    document.body.innerHTML +=
      '<div class="wpbot_embed_container"><iframe style="border:none;" id="wpbot_embed_iframe" src="' +
      wpIframeUrl +
      '" scrolling="no" width="100%" ></iframe></div><style type="text/css">#main{padding-bottom:120px}.wpbot_embed_container{position:fixed;bottom:10px;right:10px;width: 70px;z-index:9999;}#wpbot_embed_iframe{height:auto} #wpbot_embed_iframe html{margin-top : 0 !important}</style>';

    setTimeout(function () {
      if (document.getElementsByClassName("circleRollButton").length > 0) {
        document.getElementsByClassName("circleRollButton")[0].style.display =
          "none";
      }
      if (document.getElementById("moove_gdpr_save_popup_settings_button")) {
        document.getElementById(
          "moove_gdpr_save_popup_settings_button"
        ).style.display = "none";
      }
    }, 3000);

    document
      .querySelector("#wpbot_embed_iframe")
      .addEventListener("load", function () {
        var json = {
          msg: "parent",
          val: "I am from parent window",
        };
        document
          .querySelector("#wpbot_embed_iframe")
          .contentWindow.postMessage(json, "*");

        var eventMethod = window.addEventListener
          ? "addEventListener"
          : "attachEvent";
        var eventer = window[eventMethod];
        var messageEvent =
          eventMethod == "attachEvent" ? "onmessage" : "message";

        // Listen to message from child window
        eventer(
          messageEvent,
          function (e) {
            console.log(e.data);
            if (e.data.msg == "chatbot_open") {
              setTimeout(function () {
                document.getElementById("wpbot_embed_iframe").style.height =
                  "672px";
                document.getElementsByClassName(
                  "wpbot_embed_container"
                )[0].style.width = "401px";
              }, 10);
            }

            if (e.data.msg == "chatbot_close") {
              setTimeout(function () {
                document.getElementById("wpbot_embed_iframe").style.height = "";
                document.getElementsByClassName(
                  "wpbot_embed_container"
                )[0].style.width = "";
              }, 10);
            }
          },
          false
        );
      });
  }
}

$(document).on("click", ".autoCopy", function () {
  var copyText = $(this);
  $(this).select();
  document.execCommand("copy");
  alert("Copied the text: " + $(this).val());
});

jQuery.uaMatch = function (ua) {
  ua = ua.toLowerCase();

  var match =
    /(chrome)[ /]([w.]+)/.exec(ua) ||
    /(webkit)[ /]([w.]+)/.exec(ua) ||
    /(opera)(?:.*version|)[ /]([w.]+)/.exec(ua) ||
    /(msie) ([w.]+)/.exec(ua) ||
    (ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([w.]+)|)/.exec(ua)) ||
    [];

  return {
    browser: match[1] || "",
    version: match[2] || "0",
  };
};

// Don't clobber any existing jQuery.browser in case it's different
if (!jQuery.browser) {
  matched = jQuery.uaMatch(navigator.userAgent);
  browser = {};

  if (matched.browser) {
    browser[matched.browser] = true;
    browser.version = matched.version;
  }

  // Chrome is Webkit, but Webkit is also Safari.
  if (browser.chrome) {
    browser.webkit = true;
  } else if (browser.webkit) {
    browser.safari = true;
  }

  jQuery.browser = browser;
}

$(document).on("change", "input[name=is_visual]", function () {
  console.log($(this).val());
  if ($(this).val() == 1) {
    $("#configurator_on").trigger("click");
  }
});

var typingTimer; //timer identifier
var doneTypingInterval = 1000;
var currencyFormatter;
var numberFormatter;

var NdkTools = {
  parseFloatFromString: function (value, coerce) {
    value = String(value).trim();

    if ("" === value) {
      return 0;
    }

    // check if the string can be converted to float as-is
    var parsed = parseFloat(value);
    if (String(parsed) === value) {
      return parsed;
    }

    // replace arabic numbers by latin
    value = value
      // arabic
      .replace(/[\u0660-\u0669]/g, function (d) {
        return d.charCodeAt(0) - 1632;
      })
      // persian
      .replace(/[\u06F0-\u06F9]/g, function (d) {
        return d.charCodeAt(0) - 1776;
      });

    // remove all non-digit characters
    var split = value.split(/[^\dE-]+/);

    if (1 === split.length) {
      // there's no decimal part
      return parseFloat(value);
    }

    for (var i = 0; i < split.length; i++) {
      if ("" === split[i]) {
        return coerce ? 0 : NaN;
      }
    }

    // use the last part as decimal
    var decimal = split.pop();

    // reconstruct the number using dot as decimal separator
    return parseFloat(split.join("") + "." + decimal);
  },
};

$(document).on("keyup", ".price-input", function () {
  clearTimeout(typingTimer);
  me = $(this);
  typingTimer = setTimeout(function () {
    me.val(NdkTools.parseFloatFromString(me.val(), true));
  }, doneTypingInterval);
});

function makeFreeHack() {
  $(".price-input.makeItFree").after(
    '<span class="makeFreeButton btn btn-primary">free</span>'
  );
  $(document).on("click", ".makeFreeButton", function () {
    $(this).parent().find(".price-input").val(0.0001);
    $(this).addClass("active");
  });
  $(document).on("keyup", ".price-input.makeItFree", function () {
    if ($(this).val() == "0.0001")
      $(this).parent().find(".makeFreeButton").addClass("active");
    else $(this).parent().find(".makeFreeButton").removeClass("active");
  });
  $(".price-input.makeItFree").trigger("keyup");
}
