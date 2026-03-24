/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
 */
var ndkDesigner = [];
$(document).on("click", ".addText", function (e) {
  e.preventDefault();
  maxlenght = $(this).attr("data-max");
  group = $(this).attr("data-group");
  zindex = $(this).attr("data-zindex");
  target = $(this).attr("data-id");
  view = $(this).attr("data-view");
  dragdrop = $(this).attr("data-dragdrop");
  resizeable = $(this).attr("data-resizeable");
  rotateable = $(this).attr("data-rotateable");
  price = $(this).attr("data-price");
  blend = $(this).attr("data-blend");
  ppcprice = $(this).attr("data-ppcprice");
  maxItems = parseInt($(this).attr("data-max-item"));
  pattern = $(this).attr("data-pattern");
  $(this).parent().find(".max-limit").hide();

  others = $(this).parent().find(".designer-item");

  rootGroupBlock = $(".form-group[data-field=" + group + "]");
  itemIndex = rootGroupBlock.attr("data-item-index");
  if (typeof itemIndex == "undefined") number = 1;
  else number = parseInt(itemIndex) + 1;
  rootGroupBlock.attr("data-item-index", number);

  if (maxItems == 0 || others.length < maxItems) {
    $(this).parent().find(".designer-item").slideUp();

    del_btn =
      '<a href="#" class="remove-item-block"  data-group="' +
      group +
      '" data-group-target="' +
      group +
      "-" +
      parseInt(number) +
      '"><span><i class="material-icons">delete</i></span></a>';

    textItem =
      '<div id="designer-item-container-' +
      group +
      "-" +
      parseInt(number) +
      '" class="designer-item-container designer-item-text"><h4 data-target="#item-' +
      group +
      "-" +
      parseInt(number) +
      '" id="toggler-' +
      group +
      "-" +
      parseInt(number) +
      '" class="itemToggler">' +
      '<i class="material-icons">format_size</i>' +
      designerTextText +
      '<span class="designer-item-number"> ' +
      parseInt(number) +
      "</span>" +
      del_btn +
      '</h4><div id="item-' +
      group +
      "-" +
      parseInt(number) +
      '" class="designer-item  clearfix clear" data-number="' +
      parseInt(number) +
      '">' +
      '<textarea id="text-item-' +
      group +
      "-" +
      number +
      '" data-lines="1" ' +
      'data-max="' +
      maxlenght +
      '" ' +
      'data-group="' +
      group +
      '" ' +
      'data-number="' +
      number +
      '" ' +
      'data-zindex="' +
      zindex +
      '" ' +
      'data-id="' +
      target +
      '" ' +
      'data-view="' +
      view +
      '" ' +
      'data-dragdrop="' +
      dragdrop +
      '" ' +
      'data-resizeable="' +
      resizeable +
      '" ' +
      'data-rotateable="' +
      rotateable +
      '" ' +
      'data-price="' +
      price +
      '" ' +
      'data-blend="' +
      blend +
      '" ' +
      'data-ppcprice="' +
      ppcprice +
      '" ' +
      'data-pattern="' +
      pattern +
      '" ' +
      'class="form-control textzone ndktextarea textItem"></textarea>';

    if ($(this).parent().find(".orientation_selection").length > 0) {
      orientable_block = $(this).parent().find(".orientation_selection").html();
      textItem +=
        '<div data-group-target="' +
        group +
        "-" +
        parseInt(number) +
        '" class="clear clearfix orientation_selection">' +
        orientable_block +
        "</div>";
    }
    textItem += "</div></div>";

    textItem = $(this).parent().find(".itemsBlock").prepend(textItem);
    scrollToNdk(textItem, 800);
    initText($("#text-item-" + group + "-" + number));

    $("#ndkcsfield_" + group)
      .val(designerValue)
      .trigger("keyup");
    //makeSortable();
    setTimeout(function () {
      resizeMasonryGallery();
      $("#toggler-" + group + "-" + number).trigger("click");
    }, 200);
  } else {
    $(this).parent().find(".max-limit").show();
  }
});

$(document).on("click", ".addTextArea", function (e) {
  e.preventDefault();
  maxlenght = $(this).attr("data-max");
  group = $(this).attr("data-group");
  zindex = $(this).attr("data-zindex");
  target = $(this).attr("data-id");
  view = $(this).attr("data-view");
  dragdrop = $(this).attr("data-dragdrop");
  resizeable = $(this).attr("data-resizeable");
  rotateable = $(this).attr("data-rotateable");
  price = $(this).attr("data-price");
  ppcprice = $(this).attr("data-ppcprice");
  blend = $(this).attr("data-blend");
  pattern = $(this).attr("data-pattern");
  maxItems = parseInt($(this).attr("data-max-item"));
  $(this).parent().find(".max-limit").hide();
  others = $(this).parent().find(".designer-item");

  rootGroupBlock = $(".form-group[data-field=" + group + "]");
  itemIndex = rootGroupBlock.attr("data-item-index");
  if (typeof itemIndex == "undefined") number = 1;
  else number = parseInt(itemIndex) + 1;
  rootGroupBlock.attr("data-item-index", number);

  if (maxItems == 0 || others.length < maxItems) {
    $(this).parent().find(".designer-item").slideUp();
    del_btn =
      '<a href="#" class="remove-item-block"  data-group="' +
      group +
      '" data-group-target="' +
      group +
      "-" +
      parseInt(number) +
      '"><span><i class="material-icons">delete</i></span></a>';

    textItem =
      '<div id="designer-item-container-' +
      group +
      "-" +
      parseInt(number) +
      '" class="designer-item-container designer-item-textarea"><h4 data-target="#item-' +
      group +
      "-" +
      parseInt(number) +
      '" id="toggler-' +
      group +
      "-" +
      parseInt(number) +
      '" class="itemToggler">' +
      '<i class="material-icons">format_size</i>' +
      designerTextText +
      '<span class="designer-item-number"> ' +
      parseInt(number) +
      "</span>" +
      del_btn +
      '</h4><div id="item-' +
      group +
      "-" +
      parseInt(number) +
      '" class="designer-item clearfix clear" data-number="' +
      parseInt(number) +
      '">' +
      '<textarea id="text-item-' +
      group +
      "-" +
      number +
      '" data-lines="1" ' +
      'data-max="' +
      maxlenght +
      '" ' +
      'data-group="' +
      group +
      '" ' +
      'data-number="' +
      number +
      '" ' +
      'data-zindex="' +
      zindex +
      '" ' +
      'data-id="' +
      target +
      '" ' +
      'data-view="' +
      view +
      '" ' +
      'data-dragdrop="' +
      dragdrop +
      '" ' +
      'data-resizeable="' +
      resizeable +
      '" ' +
      'data-rotateable="' +
      rotateable +
      '" ' +
      'data-price="' +
      price +
      '" ' +
      'data-ppcprice="' +
      ppcprice +
      '" ' +
      'data-blend="' +
      blend +
      '" ' +
      'data-pattern="' +
      pattern +
      '" ' +
      'class="form-control textzone ndktextarea type_textarea textItem"></textarea>';

    if ($(this).parent().find(".orientation_selection").length > 0) {
      orientable_block = $(this).parent().find(".orientation_selection").html();
      textItem +=
        '<div data-group-target="' +
        group +
        "-" +
        parseInt(number) +
        '" class="clear clearfix orientation_selection">' +
        orientable_block +
        "</div>";
    }
    textItem += "</div></div>";
    textItem = $(this).parent().find(".itemsBlock").prepend(textItem);
    scrollToNdk(textItem, 800);
    initText($("#text-item-" + group + "-" + number));
    $("#ndkcsfield_" + group)
      .val(designerValue)
      .trigger("keyup");
    //makeSortable();
    setTimeout(function () {
      resizeMasonryGallery();
      $("#toggler-" + group + "-" + number).trigger("click");
    }, 200);
  } else {
    $(this).parent().find(".max-limit").show();
  }
});

$(document).on("click", ".addImg", function (e) {
  e.preventDefault();

  group = $(this).attr("data-group");
  zindex = $(this).attr("data-zindex");
  target = $(this).attr("data-id");
  view = $(this).attr("data-view");
  dragdrop = $(this).attr("data-dragdrop");
  resizeable = $(this).attr("data-resizeable");
  rotateable = $(this).attr("data-rotateable");
  price = $(this).attr("data-price");
  blend = $(this).attr("data-blend");
  maxItems = parseInt($(this).attr("data-max-item"));
  $(this).parent().find(".max-limit").hide();

  others = $(this).parent().find(".designer-item");
  rootGroupBlock = $(".form-group[data-field=" + group + "]");
  itemIndex = rootGroupBlock.attr("data-item-index");
  if (typeof itemIndex == "undefined") number = 1;
  else number = parseInt(itemIndex) + 1;
  rootGroupBlock.attr("data-item-index", number);

  if (maxItems == 0 || others.length < maxItems) {
    clonedLibrary = $(this).parent().find(".ndkhiddenimglibrary").clone();
    clonedLibrary
      .removeClass("ndkhiddenimglibrary")
      .addClass("imgItem")
      .attr("id", "main-" + group + "-" + number);
    clonedLibrary.find(".img-value").attr("data-group", group + "-" + number);

    $(this).parent().find(".designer-item").slideUp();
    del_btn =
      '<a href="#" class="remove-item-block"  data-group="' +
      group +
      '" data-group-target="' +
      group +
      "-" +
      parseInt(number) +
      '"><span><i class="material-icons">delete</i></span></a>';

    imgItem =
      '<div id="designer-item-container-' +
      group +
      "-" +
      parseInt(number) +
      '" class="designer-item-container designer-item-image"><h4 id="toggler-' +
      group +
      "-" +
      parseInt(number) +
      '"  data-target="#item-' +
      group +
      "-" +
      parseInt(number) +
      '" class="itemToggler">' +
      '<i class="material-icons">insert_emoticon</i>' +
      designerImgText +
      '<span class="designer-item-number"> ' +
      parseInt(number) +
      "</span>" +
      del_btn +
      '</h4><div id="item-' +
      group +
      "-" +
      parseInt(number) +
      '" class="designer-item clearfix clear" data-number="' +
      parseInt(number) +
      '">' +
      clonedLibrary.html();

    if ($(this).parent().find(".orientation_selection").length > 0) {
      orientable_block = $(this).parent().find(".orientation_selection").html();
      imgItem +=
        '<div data-group-target="' +
        group +
        "-" +
        parseInt(number) +
        '" class="clear clearfix orientation_selection">' +
        orientable_block +
        "</div>";
    }
    imgItem += "</div></div>";
    imgItem = $(this).parent().find(".itemsBlock").prepend(imgItem);
    scrollToNdk(imgItem, 800);

    $("#ndkcsfield_" + group)
      .val(designerValue)
      .trigger("keyup");

    $(".ndk_selector").each(function () {
      $(this).setNdkSelector();
    });
    //makeSortable();
    setTimeout(function () {
      $("#toggler-" + group + "-" + number).trigger("click");
      lazyLoadInstance.update();
      //lazyLoad();
      //resizeMasonryGallery();
      setTags($("#designer-item-container-" + group + "-" + parseInt(number)));
    }, 200);
  } else {
    $(this).parent().find(".max-limit").show();
  }
});

function getClonedLibrary(group, number = 1) {
  clonedLibrary = $(".form-group[data-field=" + group + "]")
    .find(".ndkhiddenimglibrary .image-library")
    .clone();
  clonedLibrary.find(".img-value").attr("data-group", group + "-" + number);
  return clonedLibrary;
}

$(document).on("click", ".addUpload", function (e) {
  e.preventDefault();

  group = $(this).attr("data-group");
  zindex = $(this).attr("data-zindex");
  target = $(this).attr("data-id");
  view = $(this).attr("data-view");
  dragdrop = $(this).attr("data-dragdrop");
  resizeable = $(this).attr("data-resizeable");
  rotateable = $(this).attr("data-rotateable");
  price = $(this).attr("data-price");
  blend = $(this).attr("data-blend");
  maxItems = parseInt($(this).attr("data-max-item"));
  $(this).parent().find(".max-limit").hide();

  others = $(this).parent().find(".designer-item");
  rootGroupBlock = $(".form-group[data-field=" + group + "]");
  itemIndex = rootGroupBlock.attr("data-item-index");
  if (typeof itemIndex == "undefined") number = 1;
  else number = parseInt(itemIndex) + 1;
  rootGroupBlock.attr("data-item-index", number);

  if (maxItems == 0 || others.length < maxItems) {
    clonedUpload = $(this).parent().find(".ndkhiddenuploadfile").clone();
    clonedUpload.removeClass("ndkhiddenuploadfile").addClass("imgItem");
    clonedUpload.find(".img-value").attr("data-group", group + "-" + number);

    $(this).parent().find(".designer-item").slideUp();
    del_btn =
      '<a href="#" class="remove-item-block"  data-group="' +
      group +
      '" data-group-target="' +
      group +
      "-" +
      parseInt(number) +
      '"><span><i class="material-icons">delete</i></span></a>';

    imgItem =
      '<div id="designer-item-container-' +
      group +
      "-" +
      parseInt(number) +
      '" class="designer-item-container designer-item-upload"><h4 id="toggler-' +
      group +
      "-" +
      parseInt(number) +
      '"  data-target="#item-' +
      group +
      "-" +
      parseInt(number) +
      '" class="itemToggler">' +
      '<i class="material-icons">add_a_photo</i>' +
      designerUploadText +
      '<span class="designer-item-number"> ' +
      parseInt(number) +
      "</span>" +
      del_btn +
      '</h4><div id="item-' +
      group +
      "-" +
      parseInt(number) +
      '" class="designer-item clearfix clear" data-number="' +
      parseInt(number) +
      '">' +
      clonedUpload.html();

    if ($(this).parent().find(".orientation_selection").length > 0) {
      orientable_block = $(this).parent().find(".orientation_selection").html();
      imgItem +=
        '<div data-group-target="' +
        group +
        "-" +
        parseInt(number) +
        '" class="clear clearfix orientation_selection">' +
        orientable_block +
        "</div>";
    }
    imgItem += "</div></div>";
    imgItem = $(this).parent().find(".itemsBlock").prepend(imgItem);
    scrollToNdk(imgItem, 800);

    $("#ndkcsfield_" + group)
      .val(designerValue)
      .trigger("keyup");

    $(".ndk_selector").each(function () {
      $(this).setNdkSelector();
    });
    //makeSortable();
    setTimeout(function () {
      resizeMasonryGallery();
      $("#toggler-" + group + "-" + number).trigger("click");
    }, 200);
  } else {
    $(this).parent().find(".max-limit").show();
  }
});

$(document).on("click", ".itemToggler", function () {
  group = $(this).attr("data-target").split("-");
  number = group[2];
  group = group[1];
  me = $(this);
  $("#main-" + group)
    .find(".itemToggler")
    .not(me)
    .removeClass("selected");
  me.toggleClass("selected");

  $("#main-" + group)
    .find(".designer-item")
    .not($(me.attr("data-target")))
    .slideUp();

  // todo n'afficher qu'une fois la librairie mais attention quand on edite config!!
  $("#main-" + group)
    .find(".designer-item-container .image-library")
    .html("");
  if (me.hasClass("selected")) {
    var clonedLibrary = $(me.attr("data-target"))
      .find(".image-library")
      .html(getClonedLibrary(group, number).html());
    selected = me.attr("data-selected-value");
    setTimeout(function () {
      lazyLoadInstance.update();
      selectedImg = clonedLibrary.find(
        ".img-value[data-id-value=" + selected + "]"
      );
      selectedImg.addClass("selected-value");
      $(me.attr("data-target")).find(".ndk_tag_selector").trigger("change");
      // $(me.attr("data-target"))
      //   .find(".colorize_svg .selected")
      //   .trigger("click");

      //getSubValues(selected, group);
    }, 200);
  }

  $("#main-" + group)
    .find(".designer-item")
    .not($(me.attr("data-target")))
    .parent()
    .removeClass("activeItem");
  if ($(me.attr("data-target")).find(".tag-selector-container").length == 0)
    setTags(
      $(
        "#designer-item-container-" +
          me.attr("data-target").replace("#item-", "")
      )
    );

  $(me.attr("data-target")).parent().toggleClass("activeItem");
  $(me.attr("data-target")).slideToggle();
});

String.prototype.escape = function () {
  var tagsToReplace = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
  };
  return this.replace(/[&<>]/g, function (tag) {
    return tagsToReplace[tag] || tag;
  });
};

function checkMultiFieldLimits(group) {
  mainBlock = $(".form-group[data-field=" + group + "]");
  console.log(mainBlock);
  maxItems = parseInt(mainBlock.attr("data-max-item"));
  minItems = parseInt(mainBlock.attr("data-min-item"));
  others = mainBlock.find(".designer-item-container");
  number = others.length;
  console.log(number);
  if (maxItems != 0 && number > maxItems) {
    mainBlock.find(".designer-item-container:last").remove();
    mainBlock.find(".max-limit").show();
  }

  if (minItems > 0) {
    if (parseInt(number) < parseInt(minItems)) {
      $("#main-" + group + " .quantity_error_down").addClass("required_field");
    } else {
      $("#main-" + group + " .quantity_error_down")
        .removeClass("required_field")
        .hide();
      $('.form-group[data-field="' + group + '"]').removeClass("focusRequired");
    }
  }
}

$(document).on("click", ".remove-item-block", function (event) {
  event.preventDefault();
  var group = $(this).attr("data-group-target");
  var group_parent = $(this).attr("data-group");
  others = $(this).parent().parent().find(".designer-item");
  $("#visual_" + group).remove();
  $("#designer-item-container-" + group).remove();
  $("#toggler-" + group).remove();
  $("#layer-edit-" + group).remove();
  idInput = group.split("-");
  //console.log(others.length);
  if (others.length > 1) {
    $("#ndkcsfield_" + idInput[0])
      .val(designerValue)
      .trigger("keyup");
  } else {
    $("#ndkcsfield_" + idInput[0])
      .val("")
      .trigger("keyup");
    updatePriceNdk(0, $(this).attr("data-group"));
  }
  $(this).hide();
  rootGroupBlock = $(
    ".form-group[data-field='" + group + "']:not(.submitContainer)"
  );
  /*if(others.length == 1)
	 updatePriceNdk(0, group);*/
  $("#ndkcsfield_" + idInput[0]).trigger("keyup");
  selectedConfigValue[idInput[0]] = "";
  checkLayerChanges();
  //makeSortable();
  checkMultiFieldLimits(group_parent);
  if (typeof ndkMovables[group] != "undefined") ndkMovables[group].destroy();
});

$(document).on("click", ".remove-img-item", function (event) {
  event.preventDefault();
  group = $(this).attr("data-group-target");
  others = $(this).parent().parent().find(".designer-item");
  $("#visual_" + group).remove();
  $("#layer-edit-" + group).remove();
  $(this).parent().find(".selected-value").removeClass("selected-value");
  idInput = group;
  $("#ndkcsfield_" + group)
    .val("")
    .trigger("keyup");
  selectedConfigValue[group] = "";
  $(this).hide();
  if ($(this).hasClass("removePrice")) updatePriceNdk(0, group);
  ndkMovables[group].destroy();
  checkLayerChanges();
  //makeSortable();
});

$(document).on("keyup", ".visual-text-custom-font", function () {
  group = $(this).attr("data-group");
  text = $(this).val();
  textArray = text.split("");
  //console.log(textArray);
  htmlLetter = "";

  for (var i = 0; i < textArray.length; i++) {
    htmlLetter +=
      '<span data-letter="' +
      textArray[i] +
      '" class="customFontLetter customFont_' +
      group +
      "_letter_" +
      textArray[i] +
      '">' +
      textArray[i] +
      "</span>";
  }

  $.when($(this).parent().find(".custom-font-rendering").html(htmlLetter)).then(
    function () {
      $(".customFontLetter").each(function () {
        content =
          window["ndkcfCustomFont_" + group][$(this).attr("data-letter")];
        $(this).html('<img src="' + content + '"/>');
        convertPercentEl($(this));
      });

      //setLetterWidth();
    }
  );
});

$(document).on("click", ".submitCSText", function () {
  group = $(this).parent().find(".visual-text-custom-font").attr("data-group");
  zindex = $(this)
    .parent()
    .find(".visual-text-custom-font")
    .attr("data-zindex");
  price = $(this).parent().find(".visual-text-custom-font").attr("data-price");
  ppcprice = $(this)
    .parent()
    .find(".visual-text-custom-font")
    .attr("data-ppcprice");
  blend = $(this).parent().find(".visual-text-custom-font").attr("data-blend");

  view = $(this).parent().find(".visual-text-custom-font").attr("data-view");

  dragdrop = $(this)
    .parent()
    .find(".visual-text-custom-font")
    .attr("data-dragdrop");
  resizeable = $(this)
    .parent()
    .find(".visual-text-custom-font")
    .attr("data-resizeable");
  rotateable = $(this)
    .parent()
    .find(".visual-text-custom-font")
    .attr("data-rotateable");
  charsCount = 0;

  texte = $(this).parent().find(".visual-text-custom-font").text();

  if (texte == "" || texte == " ") {
    price = 0;
    texte = "";
  } else if (ppcprice > 0) {
    price = ppcprice * charsCount;
  } else {
    price = $(this)
      .parent()
      .find(".visual-text-custom-font")
      .attr("data-price");
  }

  if (texte == "" || texte == " ") {
    price = 0;
    texte = "";
  }

  height = $(this).parent().find(".custom-font-rendering").innerHeight();
  width = $(this).parent().find(".custom-font-rendering").innerWidth();

  html =
    '<div id="cecft_' +
    group +
    '" class="composition_element customFontTextElement" style="height:' +
    height +
    "px; width:" +
    width +
    'px">' +
    $(this).parent().find(".custom-font-rendering").html() +
    "</div>";

  //html = '<div class="composition_element customFontTextElement">'+$(this).parent().find('.custom-font-rendering').html()+'</div>';
  updatePriceNdk(price, parseInt(group));
  //$('.status_counter').hide();

  $.when(
    designCompo(
      html,
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
    )
  ).then(function () {
    /*$('#cecft_'+group+' .customFontLetter').each(function(){
			convertPercentEl($(this));
		});*/
    $("#cecft_" + group).css({ height: "", width: "" });
  });
});

function registerInitialValues() {
  if (typeof registerInitialValues_Override == "function") {
    return registerInitialValues_Override();
  }
  $('.fieldPane > [id^="main-"]').each(function () {
    splittedGroup = $(this).attr("id").split("-");
    group =
      splittedGroup[1] +
      (typeof splittedGroup[2] != "undefined" ? "-" + splittedGroup[2] : "");

    initialValues[group] = [];
    //images
    if ($(this).find(".img-item-row").length > 0) {
      initialValues[group].push($(this).find(".img-item-row"));
    }
    //couleurs
    if ($(this).find(".color-ndk").length > 0) {
      initialValues[group].push($(this).find(".color-ndk"));
    }
    //select
    if ($(this).find(".ndk-select").length > 0) {
      initialValues[group].push($(this).find(".ndk-select"));
    }

    //radio
    if ($(this).find(".ndk-radio").length > 0) {
      initialValues[group].push($(this).find(".ndk-radio"));
    }

    //checkbox
    if ($(this).find(".ndk-checkbox").length > 0) {
      initialValues[group].push($(this).find(".ndk-checkbox"));
    }
  });
}

function loadInitialValues() {
  if (typeof loadInitialValues_Override == "function") {
    return loadInitialValues_Override();
  }
  for (idGroup in initialValues) {
    if (typeof initialValues[idGroup] != "undefined") {
      if (typeof initialValues[idGroup][0] != "undefined") {
        for (var i = 0; i < initialValues[idGroup][0].length; i++) {
          element = initialValues[idGroup][0][i];

          container = $(".form-group[data-field='" + idGroup + "']");

          //image
          if ($(element).hasClass("img-item-row")) {
            idValue = $(element).find(".img-value:eq(0)").attr("data-id-value");
            if (
              idValue != 0 &&
              typeof idValue != "undefined" &&
              idValue != "" &&
              $(
                ".form-group[data-field='" +
                  idGroup +
                  "'] .img-value[data-id-value='" +
                  idValue +
                  "']"
              ).length == 0
            ) {
              $(element).removeClass("selected-value");
              $(element).find(".img-value").removeClass("selected-value");
              $(element).find(".svg-container").removeClass("selected-svg");
              $(".form-group[data-field='" + idGroup + "'] .img-value")
                .last()
                .parent()
                .after($(element)[0].outerHTML);
            }
          }

          //color
          else if ($(element).hasClass("color-ndk")) {
            idValue = $(element).attr("data-id-value");
            if (
              idValue != 0 &&
              typeof idValue != "undefined" &&
              idValue != "" &&
              $(
                ".form-group[data-field='" +
                  idGroup +
                  "'] .color-ndk[data-id-value='" +
                  idValue +
                  "']"
              ).length == 0
            ) {
              $(element).removeClass("selected-color");
              $(".form-group[data-field='" + idGroup + "'] .color-ndk")
                .last()
                .after($(element)[0].outerHTML);
            }
          }

          //select
          else if ($(element).is("select")) {
            $(element)
              .find("option")
              .each(function () {
                option = $(this);
                idValue = $(option).attr("data-id-value");

                if (
                  idValue != 0 &&
                  typeof idValue != "undefined" &&
                  idValue != "" &&
                  $(
                    ".form-group[data-field='" +
                      idGroup +
                      "'] .ndk-select > option[data-id-value='" +
                      idValue +
                      "']"
                  ).length == 0
                ) {
                  $(".form-group[data-field='" + idGroup + "'] select").append(
                    $(option)[0].outerHTML
                  );
                }
              });
          }

          /*else( $('.fieldPane > #main-'+idGroup+' #'+$(element).attr('id')).length < 1 ){
		    		//console.log($(element)[0].attr('id'));
		    		$('#main-'+idGroup).append($(element)[0].outerHTML);
				}*/
        }
      }
    }
  }
  setTimeout(function () {
    //equalheightNdkcf(".img-item-row");
    resizeMasonryGallery();
  }, 1000);
}

function makeGroupFieldsSlide() {
  if (typeof makeGroupFieldsSlide_Override == "function") {
    return makeGroupFieldsSlide_Override();
  }
  $(".groupFieldBlock").addClass("sliderBlock");
  $(".groupFieldBlock > .form-group").addClass("ndkackFieldItem");

  setTimeout(function () {
    ndkCfShowSlide($(".sliderBlock .ndkackFieldItem:visible:eq(0)"));
  }, 500);

  $(".sliderBlock").each(function () {
    allItems = $(this).find(".ndkackFieldItem");
    if (allItems.length > 0) {
      if (allItems.length > 1) {
        pager = '<p class="ndkcfPager">';
        allItems.each(function () {
          pager +=
            '<span data-view="' +
            $(this).attr("data-view") +
            '" target="' +
            $(this).attr("data-iteration") +
            '" class="ndkcfPagerItem"></span>';
        });
        pager += "</p>";
        $(".ndkcfPager").remove();
        $(this).append(pager).addClass("multipleSlides");
      }
    } else {
      $(this).remove();
    }
  });

  $(".groupFieldBlock").css(
    "padding-bottom",
    $(".sliderBlock .ndkackFieldItem:visible:eq(0)").innerHeight()
  );
  $(".groupFieldBlock.sliderBlock").find(".ndkcfnav").remove();
  $(".groupFieldBlock.sliderBlock.multipleSlides").append(
    '<p class="ndkcfnav"><a class="prevNdkcfItem">&nbsp;</a> <a class="nextNdkcfItem">&nbsp;</a></p>'
  );

  $(document).on("click", ".nextNdkcfItem", function () {
    found = false;
    current = $(this)
      .parent()
      .parent()
      .find(".ndkackFieldItem.activeItem:eq(0)");
    others = $(this)
      .parent()
      .parent()
      .find(".ndkackFieldItem:visible:not(.activeItem)");
    others.each(function () {
      if (
        parseFloat($(this).attr("data-iteration")) >
          parseFloat(current.attr("data-iteration")) &&
        !found
      ) {
        ndkCfShowSlide($(this), "rtl");
        found = true;
      }
    });
    if (!found) {
      ndkCfShowSlide(others.first(), "rtl");
      found = true;
    }
  });

  $(document).on("click", ".prevNdkcfItem", function () {
    current = $(this)
      .parent()
      .parent()
      .find(".ndkackFieldItem.activeItem:eq(0)");
    others = $(this)
      .parent()
      .parent()
      .find(".ndkackFieldItem:visible:not(.activeItem)");
    bigger = -1;
    others.each(function () {
      if (
        parseFloat($(this).attr("data-iteration")) <
          parseFloat(current.attr("data-iteration")) &&
        parseFloat($(this).attr("data-iteration")) > bigger
      ) {
        bigger = parseFloat($(this).attr("data-iteration"));
        target = $(this);
      }
    });

    if (typeof target != "undefined") {
      ndkCfShowSlide(target, "ltr");
    } else {
      ndkCfShowSlide(others.last(), "ltr");
    }
  });

  $(document).on("click", ".ndkcfPagerItem", function () {
    ndkCfShowSlide(
      $(this)
        .parent()
        .parent()
        .find(
          ".ndkackFieldItem[data-iteration='" + $(this).attr("target") + "']"
        )
    );
  });

  /*$(document).on('swiperight', '.sliderBlock', function(){
   	$(this).find('.prevNdkcfItem').trigger('click');
   });
   
   $(document).on('swipeleft', '.sliderBlock', function(){
   	$(this).find('.nextNdkcfItem').trigger('click');
   });*/

  $(".sliderBlock00").swipe({
    //Generic swipe handler for all directions
    swipe: function (
      event,
      direction,
      distance,
      duration,
      fingerCount,
      fingerData
    ) {
      if (direction == "left") $(this).find(".nextNdkcfItem").trigger("click");
      else if (direction == "right")
        $(this).find(".prevNdkcfItem").trigger("click");
    },
    //Default is 75px, set to 0 for demo so any distance triggers swipe
    threshold: 0,
  });

  $(".sliderBlock .ndkackFieldItem").resize(function () {
    $(this)
      .parent()
      .css(
        "padding-bottom",
        $(".ndkackFieldItem.activeItem:eq(0)").innerHeight()
      );
  });
}

$("audio").on("play", function () {
  var id = $(this).attr("id");

  $("audio")
    .not(this)
    .each(function (index, audio) {
      audio.pause();
    });
});

$("video").on("play", function () {
  var id = $(this).attr("id");

  $("video")
    .not(this)
    .each(function (index, video) {
      video.pause();
    });
});

function ndkCfShowSlide(el, direction) {
  if (typeof ndkCfShowSlide_Override == "function") {
    return ndkCfShowSlide_Override(el, direction);
  }
  direction = direction || "ltr";
  $(".img-item-row").css("height", "");
  el.parent().find(".ndkackFieldItem").removeClass("activeItem");

  el.parent()
    .find(".ndkackFieldItem")
    .removeClass("slideInRight")
    .removeClass("slideInLeft");
  if (direction == "rtl")
    el.parent()
      .find(".ndkackFieldItem")
      .addClass("slideInRight")
      .removeClass("slideInLeft");
  else
    el.parent()
      .find(".ndkackFieldItem")
      .removeClass("slideInRight")
      .addClass("slideInLeft");

  el.addClass("activeItem");
  el.parent().find(".ndkcfPagerItem").removeClass("activePager");
  el.parent()
    .find(".ndkcfPagerItem[target='" + el.attr("data-iteration") + "']")
    .addClass("activePager");
  //scrollToNdk(el.parent(), 800);

  setTimeout(function () {
    equalheightNdkcf(".img-item-row");
    resizeMasonryGallery();
    el.parent().css(
      "padding-bottom",
      $(".ndkackFieldItem.activeItem:eq(0)").innerHeight()
    );
    el.parent()
      .find(".ndkackFieldItem")
      .removeClass("slideInRight")
      .removeClass("slideInLeft");
  }, 500);
}

$(document).on("click", ".form-group", function () {
  field = $(this).attr("data-field");
  isItem = false;
  if ($(this).hasClass("field-type-14")) {
    isItem = true;
  }
  $(".editThisLayer").removeClass("layerActive");
  $(".resetZones").remove();

  $(".zone_limit, .absolute-visu")
    .removeClass("activeZone")
    .removeClass("discretZone");
  zone = $(
    ".zone_limit[data-group='" +
      field +
      "'], .absolute-visu[data-group='" +
      field +
      "']"
  );
  if (
    (zone.find(".ui-resizable, .ui-draggable, .rotatable").length > 0 ||
      zone.is(".ui-resizable", ".ui-draggable", ".rotatable")) &&
    !$(this).hasClass("activeFormGroup_ooooooo")
  ) {
    $(".form-group").removeClass("activeFormGroup");
    $(this).addClass("activeFormGroup");

    if (!isItem) {
      $(".zone_limit, .absolute-visu").addClass("discretZone");
      zone.addClass("activeZone").removeClass("discretZone");
      $("#layer-edit-" + field).addClass("layerActive");
      $(".editThisLayer[data-group*='" + field + "-']")
        .addClass("layerActive")
        .find(".ui-resizable, .ui-draggable, .rotatable")
        .trigger("mouseover");
    }

    $("#layer-block").append(
      '<span class="resetZones">' + resetText + "</span>"
    );
  } else {
    $(".zone_limit, .absolute-visu")
      .removeClass("activeZone")
      .removeClass("discretZone");
    $(".editThisLayer").removeClass("layerActive");
    $(".form-group").removeClass("activeFormGroup");
    $(".resetZones").remove();
  }
});

$(document).on("click", ".editThisLayer", function () {
  isItem = false;
  group = $(this).attr("data-group");
  if (group.indexOf("-") > -1) {
    isItem = true;
    number = group.split("-")[1];
    itemGroup = group.split("-")[0];
  }

  $(".editThisLayer").removeClass("layerActive");
  $(this).addClass("layerActive");
  $("#visual_" + group).trigger("mousedown");
  // if (isItem) {
  //   $("#toggler-" + group).trigger("click");
  //   $("#layer-edit-" + group).addClass("layerActive");
  //   $("#visual_" + group)
  //     .addClass("layerActive")
  //     .removeClass("discretZone")
  //     .trigger("mousedown");
  // } else {
  //   $(".form-group[data-field='" + itemGroup[0] + "']").trigger("click");
  // }
});

$(document).on("mousedown", ".absolute-visu", function (e) {
  if ($(this).is(".ui-resizable", ".ui-draggable", ".rotatable")) {
    group = $(this).attr("data-group").split("-");
    $(".editThisLayer").removeClass("layerActive");
    makeToolFollowing(e.target);
    //$(".form-group[data-field='" + group[0] + "']").trigger("click");
    //$(this).find(".editIt").trigger("click");
    $(this).parent().addClass("activeZone");
  }
});

$(document).on("click", "#ndk-movable-tools .tool-item", function (e) {
  setTimeout(function () {
    $("#ndk-movable-tools").show();
  }, 50);
});

$(document).on("click", ".tool-item.ndk-crop", function (e) {
  e.preventDefault();
  group = $(this).parent().attr("data-group");

  if ($(this).hasClass("active")) {
    ndkMovables[group].clippable = false;
    $(this).removeClass("active");
    $(".ndk-crop-tool").removeClass("active");
  } else {
    document.querySelector("#visual_" + group).style.clip = "";
    document.querySelector("#visual_" + group).style.clipPath = "";
    ndkMovables[group].clippable = true;
    ndkMovables[group].defaultClipPath = $(this).attr("data-clippath");
    ndkMovables[group].clipRelative = false;
    ndkMovables[group].clipArea = false;
    ndkMovables[group].clipTargetBounds = false;
    $(".ndk-crop-tool").removeClass("active");
    $(this).addClass("active");
    $(".moveable-control-box,.moveable-control").addClass("panzoom-exclude");
  }
});

$(document).on("click", ".tool-item.ndk-rotate", function (e) {
  e.preventDefault();
  group = $(this).parent().attr("data-group");
  ndkMovables[group].request("rotatable", { deltaRotate: -90 }, true);
});

$(document).on("click", ".tool-item.ndk-tool-delete", function (e) {
  e.preventDefault();
  group = $(this).parent().attr("data-group");
  $(".remove-item-block[data-group-target=" + group + "]").trigger("click");
  $("#ndk-movable-tools").hide();
});

$(document).on("click", ".tool-item.ndk-tool-add", function (e) {
  e.preventDefault();
  group = $(this).parent().attr("data-group");
  if (typeof group != "undefined") {
    if (group.indexOf("-") > -1) {
      isItem = true;
      number = group.split("-")[1];
      itemGroup = group.split("-")[0];
      showEditingBlock(itemGroup, isItem, number);
      $(".form-group[data-field='" + itemGroup + "']")
        .find(".itemToggler.selected")
        .trigger("click");
    }
  }
  $("#ndk-movable-tools").hide();
});

$(document).on("click", ".tool-item.ndk-movable-to-front", function (e) {
  e.preventDefault();
  group = $(this).parent().attr("data-group");
  zindex = $("#visual_" + group).css("z-index");
  $("#visual_" + group).css("z-index", parseInt(zindex) + 1);
});
$(document).on("click", ".tool-item.ndk-movable-to-back", function (e) {
  e.preventDefault();
  group = $(this).parent().attr("data-group");
  zindex = $("#visual_" + group).css("z-index");
  if (zindex > 1) $("#visual_" + group).css("z-index", parseInt(zindex) - 1);
});

$(document).on("click", ".tool-item.ndk-movable-setting", function (e) {
  e.preventDefault();
  group = $(this).parent().attr("data-group");
  itemGroup = group;
  if (typeof group != "undefined") {
    if (group.indexOf("-") > -1) {
      isItem = true;
      number = group.split("-")[1];
      itemGroup = group.split("-")[0];
      group = itemGroup;
    }
    showEditingBlock(itemGroup, isItem, number);
  }
});

function showEditingBlock(group, isItem = false, number = 0) {
  if (typeof showEditingBlock_Override == "function") {
    return showEditingBlock_Override(group, isItem, number);
  }
  $("#ndkcsfields .toggler").removeClass("active");
  if (parseInt(letOpen) == 0) $("#ndkcsfields .fieldPane").hide();
  formGroup = $(".form-group[data-field='" + group + "']");
  scrollToNdk(formGroup, 800);
  if (makeSlide == 1) {
    $(".sliderBlock .ndkackFieldItem").removeClass("activeItem");
    formGroup.addClass("activeItem");
  }

  //formGroup.find(".toggler:not(.active):eq(0)").trigger("click");
  //formGroup.find(".toggler:not(.active):eq(0)").addClass("active");
  formGroup.find(".fieldPane").slideDown();
  if (isItem) {
    $("#toggler-" + group + "-" + parseInt(number))
      .not(".selected")
      .trigger("click");
    /*
        formGroup.find('.designer-item').hide();
        scrollToNdk(formGroup.find(".designer-item[data-number='"+number+"']"), 800);
        $(".designer-item[data-number='"+number+"']").show();
*/

    openPopupConfigBlock();
  }
}
$(document).on("click", ".close-popup", function (e) {
  e.preventDefault();
  $(".sticky-close").trigger("click");
  $(".fancybox-close").trigger("click");
});

$(document).on("click", ".editIt", function () {
  isItem = false;
  el = $(this).parent();
  group = el.attr("id").replace("visual_", "");
  itemGroup = group;
  if (group.indexOf("-") > -1) {
    isItem = true;
    number = group.split("-")[1];
    itemGroup = group.split("-")[0];
    group = itemGroup;
  }
  showEditingBlock(itemGroup, isItem, number);
});

$(document).on("click", ".resetZones", function () {
  resetZone();
  //snapShotLight();
});

function resetZone() {
  $(".zone_limit, .absolute-visu")
    .removeClass("activeZone")
    .removeClass("discretZone");
  $(".editThisLayer").removeClass("layerActive");
  $(".form-group").removeClass("activeFormGroup");
  $(".resetZones").remove();
  //convertPercent();
}

function checkLayerChanges() {
  if (typeof checkLayerChanges_Override == "function") {
    return checkLayerChanges_Override();
  }
  if ($("#layer-block").find(".editThisLayer").length == 0)
    $("#layer-block").hide();
  else if (
    $("#layer-block").find(".editThisLayer").length == 1 &&
    $("#layer-block").find(".layer_title").length == 0
  )
    $("#layer-block")
      .show()
      .prepend('<p class="layer_title">' + selectLayer + "</p>");
  else if ($("#layer-block").find(".editThisLayer").length > 0)
    $("#layer-block").show();
}

/*$(document).on('mouseout', '.activeZone > .absolute-visu', function(){
  $('.zone_limit').removeClass('activeZone');
});*/

/*$(document).on('mouseover', '#ndkcsfields-block', function(){
  $('.zone_limit').removeClass('activeZone');
});*/

/*$(document).on('mouseover', '#submitNdkcsfields', function(){
  $('.zone_limit, .absolute-visu').removeClass('activeZone').removeClass('discretZone');
  $('.form-group').removeClass('activeFormGroup');
  //convertPercent();
  snapShotLight();
});*/

$(document).on(
  "keydown",
  '#ndkcsfields input[type="text"], #ndkcsfields input[type="number"]',
  function (event) {
    if (event.keyCode == 13 || event.keyCode == 9) {
      $(this).focus();
      event.preventDefault();
    }
  }
);

$(document).on("blur", ".noborder", function () {
  button = $(this)
    .parent()
    .parent()
    .parent()
    .find(".submitText, .submitTextItem");
  //button.trigger("click");
});

/*$(document).on('mouseleave', '.ndkackFieldItem', function(){
	button = $(this).find('.submitText:visible, .submitTextItem:visible');
	button.trigger('click');
})*/

function makeSortable() {
  if (typeof makeSortable_Override == "function") {
    return makeSortable_Override();
  }
  //$('.itemsBlock').sortable('destroy');
  $(".itemsBlock").sortable({
    items: ".designer-item-container",
    handle: ".itemToggler",
    cursor: "move",
    opacity: 0.6,
    start: function (event, ui) {
      $(".resetZones").trigger("click");
    },
    stop: function (event, ui) {
      refZindex = ui.item
        .parent()
        .parent()
        .parent()
        .find("button:eq(0)")
        .attr("data-zindex");
      others = ui.item.parent().find(".designer-item-container");
      others.each(function () {
        $(this).attr("addzindex", $(others).index($(this)));
        block = $(this).find(".designer-item");
        target_key = block.attr("id").replace("item-", "");
        target = $("#visual_" + target_key);
        target
          .css("z-index", refZindex + $(others).index($(this)))
          .attr("data-zindex", refZindex + $(others).index($(this)));

        //console.log($(others).index($(this)));
      });
    },
  });
}

$(document).on("click", ".colse-comb-tab", function () {
  $(this)
    .parent()
    .parent()
    .parent()
    .find(".combColumn")
    .removeClass("openedCol")
    .css("height", "");
});

$(document).on("click", ".ndkcf_col_title", function () {
  if (!$(this).parent().hasClass("openedCol")) {
    $(this)
      .parent()
      .parent()
      .find(".combColumn")
      .removeClass("openedCol")
      .css("height", "");

    originalHeight = $(this).parent().innerHeight();
    refPosition = $(this).parent().position().top;
    //$(this).parent().addClass('openedCol').css('height', (combHeight+originalHeight+40)+'px');
    $.when($(this).parent().addClass("openedCol")).done(function () {
      combHeight = $(this).parent().find(".combRowList").innerHeight();
      //console.log(combHeight);
      $(this)
        .parent()
        .css("height", combHeight + originalHeight + 50 + "px");
      $(".combColumn").each(function () {
        if ($(this).position().top == refPosition)
          $(this).css("height", combHeight + originalHeight + 50 + "px");
      });
    });
    //equalheightNdkcf($(this).parent().find(".combRow"));
  } else {
    //$(this).parent().parent().find('.combColumn').removeClass('openedCol').css('height', '');
  }
});

$(document).on("click", ".toggleQuantityDiscountBlock", function () {
  content = $(this).parent().find(".specificPriceBlock").html();
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
            '<div class="popupSpecificPrice clear clearfix">' +
            content +
            "</div>",
          beforeShow: function () {},
        },
      ],
      {
        padding: 0,
      }
    );
  }
});

$(document).on("click", ".toggleQuantityDiscount", function () {
  $(this).parent().find(".quantityDiscount").slideToggle();
});

$(document).on("click", ".accessory-ndk-no-quantity", function (e) {
  if (
    // !$(e.target).hasClass("accessory-more") &&
    // !$(e.target).hasClass("ndk_attribute_select") &&
    // !$(e.target).hasClass("ndk_att_list")
    $(e.target).hasClass("accessory_img_block") ||
    $(e.target).parents().hasClass("accessory_img_block") ||
    $(e.target).hasClass("combRow") ||
    $(e.target).parents().hasClass("combRow")
  ) {
    //console.trace();
    me = $(this);
    toggleAccessoryNoQuantity(me);
  }
});

function toggleAccessoryNoQuantity(me) {
  rootBlock = $(".form-group[data-field='" + me.attr("data-group") + "']");
  input = me.find(".ndk-accessory-quantity:eq(0)");
  my_id_value = input.attr("data-id-value");
  cancelFieldRestrictions(my_id_value, me.attr("data-group"));

  max = parseInt(rootBlock.attr("data-qtty-max"));
  total = 0;
  rootBlock.find(".ndk-accessory-quantity").each(function () {
    total += parseInt($(this).val());
  });
  fullQtty = false;
  if (max > 0 && parseInt(input.val()) == 0) {
    if (total >= max) {
      fullQtty = true;
      rootBlock.find(".quantity_error_up").show();
      $(
        ".accessory-ndk-no-quantity[data-id-value=" + my_id_value + "]"
      ).removeClass("selected-accessory");
      input.val(0).trigger("keyup");
    } else {
      rootBlock.find(".quantity_error_up").hide();
    }
  }

  if (parseInt(input.val()) == 0) {
    if (!fullQtty) {
      me.addClass("selected-accessory");
      input.val(1).trigger("keyup");
      total += 1;
      selectedConfigValue["ndk-accessory-quantity-" + my_id_value] = 1;
    }
  } else {
    if (parseInt(input.attr("data-qtty-min")) < 1) {
      selectedConfigValue["ndk-accessory-quantity-" + my_id_value] = 0;
      me.removeClass("selected-accessory");
      input.val(0).trigger("keyup");
      total -= 1;
    }
  }
  if (total == 0) applyDefaultValuesNdk(rootBlock);

  input.trigger("change");
}

$(document).on("click", ".orientation-btn", function () {
  $(this).parent().find(".orientation-btn").removeClass("active_orientation");
  $(this).addClass("active_orientation");
  group = $(this).parent().attr("data-group-target");
  $(
    "#visual_" +
      group +
      " .composition_element, #visual_" +
      group +
      " .colorize-cover-item"
  )
    .removeClassSVG("standard-orientation")
    .removeClassSVG("reverse-orientation")
    .addClassSVG($(this).attr("data-orientation"));
  $(this)
    .parent()
    .find(".orientation_input")
    .val($(this).attr("data-orientation"));
});

$(document).on("focus", "input.surface", function () {
  if ($(this).attr("step") != "") $(this).trigger("blur");
});

$("textarea.noborder").on("keyup change", function () {
  $(this)
    .css("height", "auto")
    .css("height", this.scrollHeight + (this.offsetHeight - this.clientHeight));
  $(this)
    .parent()
    .css("height", "auto")
    .css(
      "height",
      this.scrollHeight + (this.offsetHeight - this.clientHeight) + 15
    );
});

function ndkImagetoDataURLForSvg(url, callback) {
  var image = new Image();
  if (url.indexOf("http") !== -1) {
    image.onload = function () {
      var canvas = document.createElement("canvas");
      canvas.width = this.naturalWidth; // or 'width' if you want a special/scaled size
      canvas.height = this.naturalHeight; // or 'height' if you want a special/scaled size

      canvas.getContext("2d").drawImage(this, 0, 0);
      //callback(canvas.toDataURL('image/png'));
      callback(image);
    };

    image.src = url;
  } else {
    callback(false);
  }
}

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

$(document).on("keyup", ".dimension_text_width", function () {
  me = $(this);
  rootBlock = $(".form-group[data-field='" + me.attr("data-group") + "']");
  max = parseInt(me.attr("max"));
  min = parseInt(me.attr("min"));
  if (max > 0) {
    if (me.val() > max)
      rootBlock
        .find(".quantity_error_width_up")
        .addClass("required_field")
        .fadeIn();
    else
      rootBlock
        .find(".quantity_error_width_up")
        .removeClass("required_field")
        .fadeOut();
  }
  if (min > 0) {
    if (me.val() < min)
      rootBlock
        .find(".quantity_error_width_down")
        .addClass("required_field")
        .fadeIn();
    else
      rootBlock
        .find(".quantity_error_width_down")
        .removeClass("required_field")
        .fadeOut();
  }
});

$(document).on("keyup", ".dimension_text_height", function () {
  me = $(this);
  rootBlock = $(".form-group[data-field='" + me.attr("data-group") + "']");
  max = parseInt(me.attr("max"));
  min = parseInt(me.attr("min"));
  if (max > 0) {
    if (me.val() > max)
      rootBlock
        .find(".quantity_error_height_up")
        .addClass("required_field")
        .fadeIn();
    else
      rootBlock
        .find(".quantity_error_height_up")
        .removeClass("required_field")
        .fadeOut();
  }
  if (min > 0) {
    if (me.val() < min)
      rootBlock
        .find(".quantity_error_height_down")
        .addClass("required_field")
        .fadeIn();
    else
      rootBlock
        .find(".quantity_error_height_down")
        .removeClass("required_field")
        .fadeOut();
  }
});

/*$(document).on('keyup', '.ndkcf_totalprod_quantity', function(){
	group = $(this).attr('data-group');
	rootBlock = $(".form-group[data-field='"+group+"']");
	if($(this).val() >= $(this).attr('data-qtty-min'))
	{
		rootBlock.find('.quantity_error_down').removeClass('required_field').fadeOut();
	}
	else
	{
		rootBlock.find('.quantity_error_down').addClass('required_field');
	}
	
	if($('.ndk_itself').length > 0)
		var addProductPrice = 0;
})*/

$("#ndkacf-modal").on("shown.bs.modal", function () {
  $(".view_tab:eq(0)").trigger("click");
  $(".form-group.userPanel").hide();
  setTags();
  openPopupConfigBlock();
});

$(document).on("click", "#imgs-bloc-popup", function (e) {
  if (
    $("#custom-block-popup").hasClass("opened") &&
    !$(e.target).hasClass("resetZones")
  ) {
    closePopupConfigBlock();
  }
});

// $(document).on(
//   "click",
//   ".ndkacf-options.opened #ndkcf_mobile_options_toggler",
//   function (e) {
//     if ($("#custom-block-popup").hasClass("opened")) {
//       closePopupConfigBlock();
//     }
//   }
// );

function closePopupConfigBlock() {
  if ($("#ndkacf-modal").length > 0 && $(window).width() < 768) {
    $("#custom-block-popup").removeClass("opened");
    $("#custom-block-popup").removeClass("slideInLeft");
    $(".toggler.active").removeClass("active");
    $(".fieldPane").slideUp();
    $(".itemToggler.selected").trigger("click");
  }
}

//test todo
// $(document).on("click", "#custom-block-popup", function (e) {
//   if (
//     (!$(this).hasClass("opened") &&
//       !$(e.target).hasClass("ndkackFieldItem") &&
//       !$(e.target).hasClass("ndk_recap_block") &&
//       !$(e.target).hasClass("ndk-movable-tools") &&
//       !$(e.target).hasClass("resetZones") &&
//       !$(e.target).parents().hasClass("ndk-movable-tools") &&
//       !$(e.target).parents().hasClass("ndk_recap_block")) ||
//     $(e.target).attr("id") == "ndkcf_mobile_options_toggler" ||
//     $(e.target).parent().attr("id") == "ndkcf_mobile_options_toggler"
//   ) {
//     setTimeout(function () {
//       openPopupConfigBlock();
//     }, 50);
//   }
// });

$(document).on("click", "#custom-block-popup", function (e) {
  if (
    !$(this).hasClass("opened") &&
    ($(e.target).attr("id") == "ndkcf_mobile_options_toggler" ||
      $(e.target).parent().attr("id") == "ndkcf_mobile_options_toggler" ||
      $(e.target).hasClass("toggler") ||
      $(e.target).parents().hasClass("toggler"))
  ) {
    setTimeout(function () {
      openPopupConfigBlock();
    }, 50);
  }
});

function openPopupConfigBlock(group = false) {
  $("#custom-block-popup").addClass("opened");
  $("#custom-block-popup").addClass("slideInLeft");
  $("#custom-block-popup").removeClass("slideInLeft");
  $(".form-group.userPanel").hide();
  setTimeout(function () {
    $("#ndk-movable-tools").hide();
    lazyLoadInstance.update();
    //lazyLoad();
  }, 100);
}

$(document).on("change", ".recipient-group input[type=radio]", function () {
  me = $(this);
  val = 0;
  target = $(
    "input[name='" + me.attr("name").replace("[send_mail]", "[email]") + "']"
  );
  if (me.is(":checked")) {
    val = me.val();
  }
  if (val == 1) {
    target.parent().slideDown();
    target.addClass("required_field");
  } else {
    target.parent().slideUp();
    target.removeClass("required_field");
  }
});

$(document).on(
  "imgValueSet",
  ".designer-item-container .img-value",
  function () {
    group = $(this).attr("data-group");
    title = $(this).attr("title");
    value = $(this).attr("data-id-value");
    $("#toggler-" + group)
      .attr("data-selected-value", value)
      .find(".designer-item-number")
      .text(" (" + title + ")")
      .show();
  }
);

$(document).on("click", ".tool-item.ndk-tool-duplicate", function (e) {
  group = $(this).parent().attr("data-group");
  if (typeof group != "undefined") {
    if (group.indexOf("-") > -1) {
      el = $("#visual_" + group);
      duplicateItem(el);
    }
  }
});

function duplicateItem(el) {
  group = el.attr("data-group");
  rootGroup = group.split("-")[0];
  itemType = "image";
  if (el.find(".textareaSvg").length > 0) itemType = "text";

  if (itemType == "text") {
    duplicateItemText(group, rootGroup);
  }
  if (itemType == "image") {
    duplicateItemImage(group, rootGroup);
  }
}

function duplicateItemImage(group, rootGroup) {
  selected_value = $("#toggler-" + group).attr("data-selected-value");

  if (typeof selected_value == "undefined") {
    duplicateItemUpload(group, rootGroup);
  } else {
    $(".addImg[data-group=" + rootGroup + "]").trigger("click");
    setTimeout(function () {
      newLibrary = $("#main-" + rootGroup).find(".image-library:eq(0)");
      console.log(newLibrary);
      newLibrary
        .find(".img-value[data-id-value=" + selected_value + "]")
        .trigger("click");
    }, 1500);
  }
}

function duplicateItemUpload(group, rootGroup) {
  selected_value = $("#item-" + group)
    .find(".img-block:eq(0)")
    .clone();
  $(".addUpload[data-group=" + rootGroup + "]").trigger("click");
  newLibrary = $("#main-" + rootGroup).find(".designer-item:eq(0)");
  newGroup = rootGroup + "-" + newLibrary.attr("data-number");
  selected_value
    .find(".img-value")
    .attr("data-group", newGroup)
    .removeClass("selected-value");
  newLibrary.find(".img-block:eq(0)").replaceWith(selected_value);

  setTimeout(function () {
    newLibrary.find(".img-value:eq(0)").trigger("click");
  }, 500);
}

function duplicateItemText(group, rootGroup) {
  buttonClass = ".addText";
  inputType = "input";
  if ($("#item-" + group).find("textarea.noborder").length > 0) {
    buttonClass = ".addTextArea";
    inputType = "textarea";
  }

  selected_value = $("#item-" + group)
    .find(inputType + ".noborder:eq(0)")
    .val();

  $(buttonClass + "[data-group=" + rootGroup + "]").trigger("click");
  setTimeout(function () {
    newTextarea = $("#main-" + rootGroup).find(inputType + ".noborder:eq(0)");
    newTextarea.val(selected_value).trigger("keyup");
    // $("#main-" + rootGroup)
    //   .find(".submitTextItem")
    //   .trigger("click");
  }, 500);
}
