var ndkSelectedAttributes = [];

$(document).ready(function () {
  $(".stepped-accessory-ndk").each(function () {
    initStepCombnation($(this).data("id-value"));
  });
});

function initStepCombnation(id_value) {
  mainBlock = $(".stepped-accessory-ndk[data-id-value='" + id_value + "']");
  mainBlock.find(".ndkcf_step_comb_list_items").each(function () {
    $(this).find(".ndkcf_unique_comb:eq(0)").trigger("click");
  });
}

$(document).on("click", ".ndkcf_unique_comb", function (e) {
  me = $(this);
  me.parent().find(".ndkcf_unique_comb").attr("data-selected", 0);
  me.attr("data-selected", 1);
  if (typeof ndkSelectedAttributes[me.data("id-value")] == "undefined")
    ndkSelectedAttributes[me.data("id-value")] = [];
  if (
    typeof ndkSelectedAttributes[me.data("id-value")][
      me.data("id_attribute_group")
    ] == "undefined"
  )
    ndkSelectedAttributes[me.data("id-value")][me.data("id_attribute_group")] =
      [];

  ndkSelectedAttributes[me.data("id-value")][me.data("id_attribute_group")] =
    me.data("id_attribute");

  displayAccorrdingAttributes(me.data("id-value"));
});

function displayAccorrdingAttributes(id_value) {
  mainBlock = $(".stepped-accessory-ndk[data-id-value='" + id_value + "']");
  mainBlock.find(".ndkcf_multiple_comb").hide();
  mainBlock.find(".ndk-accessory-quantity").val(0).trigger("change");
  //console.log(ndkSelectedAttributes[id_value]);
  selector = "";
  for (idGroup in ndkSelectedAttributes[id_value]) {
    id_attribute = ndkSelectedAttributes[id_value][idGroup];
    selector += "[data-id_attribute*='" + id_attribute + ",']";
    //selector += "[data-id_attribute_group='"+idGroup+"']";
  }

  console.log(selector);
  mainBlock.find(".ndkcf_multiple_comb" + selector).show();
}
