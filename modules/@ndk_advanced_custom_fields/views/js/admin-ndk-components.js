/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2021 Hendrik Masson
 *  @license   Tous droits réservés
 */

zoneCurrent = 0;
selectionCurrent = null;
valueOfZoneEdited = null;

// Last item is used to save the current zone and
// allow to replace it if user cancel the editing
lastEditedItem = null;

/*
 ** Pointer function do handle event by key released
 */
function handlePressedKey(keyNumber, fct) {
  // KeyDown isn't handled correctly in editing mode
  $(document).keyup(function (event) {
    if (event.keyCode == keyNumber) fct();
  });
}

/* @group category tree */
$(document).on(
  "click",
  "#expand-all-categories-tree, #collapse-all-categories-tree",
  function () {
    setTimeout(function () {
      $('[name="false_categories[]"]').addClass("implode_input");
    }, 500);
  }
);

$(document).on(
  "click",
  "#check-all-categories-tree, #uncheck-all-categories-tree",
  function () {
    $('[name="false_categories[]"]').trigger("change");
  }
);

/* @end category tree */

/* @group auto complete */
lastEditedItem = null;
zoneCurrent = 0;
selectionCurrent = null;
valueOfZoneEdited = null;

$(window).load(function () {
  $(".editable-value").attr("onclick", "");
  /* function autocomplete */
  $(".product_autocomplete_input")
    .autocomplete(
      currentIndex +
        "&token=" +
        token +
        "&query_ajax_request&exclude_packs=false&excludeVirtuals=false&action=ajaxGetProducts&ajax=1",
      {
        minChars: 1,
        autoFill: true,
        max: 20,
        matchContains: true,
        mustMatch: true,
        scroll: false,
        //extraParams: {excludeIds : getProdsIds()}
        extraParams: { excludeIds: "9999999" },
      }
    )
    .result(afterTextInserted);
});

function afterTextInserted(event, data, formatted) {
  if (data == null) return false;
  if (lastEditedItem != null) lastEditedItem.remove();
  lastEditedItem = null;
  zoneCurrent++;
  var idProduct = data[1];
  var nameProduct = data[0];
  targetInput = $($(event.currentTarget).data("target"));
  console.log(targetInput);
  oldVal = targetInput.val();
  oldValArray = oldVal.split(",");
  if (oldVal == "") separator = "";
  else separator = ",";
  newVal = oldVal + separator + idProduct;
  if ($.inArray(idProduct, oldValArray) == -1) {
    targetInput.val(newVal);
    newRow =
      '<button data-id="' +
      idProduct +
      '" class="btn btn-default prodrow" type="button" data-target="' +
      $(event.currentTarget).data("target") +
      '"><i class="icon-remove"></i>' +
      nameProduct +
      "</button>";
    $(event.currentTarget).parent().parent().find(".prodlist").append(newRow);
  }
}

$(".prodrow").live("click", function () {
  idProduct = $(this).attr("data-id");
  oldVal = $($(this).data("target")).val();
  oldValArray = oldVal.split(",");

  oldValArray.splice($.inArray(idProduct, oldValArray), 1);
  newVal = "";
  console.log(oldValArray);
  for (var i = 0; i < oldValArray.length; i++) {
    if (typeof oldValArray[i] != "undefined") {
      newVal += oldValArray[i] + (i < oldValArray.length - 1 ? "," : "");
    }
  }
  $($(this).data("target")).val(newVal);
  $(this).remove();
});
/* @end auto complete*/

/* @group post implode input*/
$(document).on(
  "change",
  "input.serialize_input, select.serialize_input, textarea.serialize_input, .serialize_input input, serialize_input select, input[name='false_query_cond[id_category]']",
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
  // console.log(values.join(','))
  // console.log(input_name)
  if ($("[name^='" + input_name.replace("false_", "") + "']").length == 0)
    $("[name^='" + input_name + "']:eq(0)").after(
      '<input type="hidden" class="modified_post" name="' +
        input_name.replace("false_", "") +
        '" />'
    );

  joined = values.join(",");

  $("input[name^='" + input_name.replace("false_", "") + "']").val(joined);
});

$(document).on("change", ".form-group[class*=toggle_] input", function () {
  targetClass = "";
  classList = $(this).parents().filter(".form-group")[0].classList;
  classList.forEach(function (value) {
    if (value.indexOf("toggle_") > -1) {
      targetClass = value.replace("toggle_", "");
    }
  });

  if (parseInt($(this).val()) == 1) {
    $(targetClass).slideDown();
  } else {
    $("." + targetClass).slideUp();
  }
});

function ndkModifyPost(input_name, my_function) {
  values = $("[name^='" + input_name + "']").serializeObject();

  data = JSON.stringify(values);
  reg = new RegExp(input_name, "g");
  data = data.replace(reg, "");
  data = data.replace(/[\][]/g, "");

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
/* @end post implode input*/

/* @group remove file*/
$(document).on("click", ".AjaxremoveFile", function () {
  parentBlock = $(this).parent();
  $.ajax({
    type: "POST",
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
/* @end remove file*/

/* @group ndk tabs*/
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
/* @end ndk tabs*/

function makeFreeHack() {
  $(".price-input.makeItFree").after(
    '<span class="makeFreeButton btn btn-primary">free</span>'
  );
  $(document).on("click", ".makeFreeButton", function () {
    $(this).parent().find(".price-input").val(0.0001);
  });
}
