if (showImgPreview == 99) {
  window.html2canvas = function (nodeList, options) {
    $(".ndkzoom").attr("disabled", false).removeClass("loadingButton");
    return true;
  };
}

function designCompo(
  visu,
  group,
  view,
  zindex,
  dragdrop,
  resizeable,
  rotateable,
  width,
  height,
  type,
  coloreffect,
  background,
  target,
  specialClass,
  category
) {
  if (typeof designCompo_Override == "function") {
    return designCompo_Override(
      visu,
      group,
      view,
      zindex,
      dragdrop,
      resizeable,
      rotateable,
      width,
      height,
      type,
      coloreffect,
      background,
      target,
      specialClass,
      category
    );
  }
  target = target || false;
  coloreffect = coloreffect || "normal";
  background = background || "";
  specialClass = specialClass || "";
  category = category || "";
  specifiView = true;
  isItem = false;
  number = 0;
  itemGroup = 0;
  viewClass = " ";

  if (group.indexOf("-") > -1) {
    isItem = true;
    number = group.split("-")[1];
    itemGroup = group.split("-")[0];
  }

  mainBlock = $('.form-group[data-field="' + group + '"]');
  specialClass += " " + mainBlock.attr("data-custom_class");

  originalWidth = width + "px";
  //console.log('w:'+originalWidth);
  //on cherce si une zone est définie
  if (target) {
    //console.log(target);
    container = target;
    containment = "parent";
    maxwidth = $(target).width() + "px";
    maxheight = $(target).height() + "px";
    height = "auto";
    maxwidth = "100%";
    maxheight = "100%";
  } else if (
    $(".zone_limit[data-group='" + (itemGroup > 0 ? itemGroup : group) + "']")
      .length > 0 &&
    $(".view_tab[data-view='" + view + "']").length > 0
  ) {
    container =
      ".zone_limit[data-group='" + (itemGroup > 0 ? itemGroup : group) + "']";
    containment = "parent";
    maxwidth =
      $(
        ".zone_limit[data-group='" + (itemGroup > 0 ? itemGroup : group) + "']"
      ).width() + "px";
    maxheight =
      $(
        ".zone_limit[data-group='" + (itemGroup > 0 ? itemGroup : group) + "']"
      ).height() + "px";
    height = "auto";
    maxwidth = "100%";
    maxheight = "100%";

    $(
      ".zone_limit[data-group='" + (itemGroup > 0 ? itemGroup : group) + "']"
    ).css("mix-blend-mode", coloreffect);
    if ($(".form-group[data-field=" + group + "]").attr("data-mask-image")) {
      zone_mask = $(".form-group[data-field=" + group + "]").attr(
        "data-mask-image"
      );
      $(".zone_limit[data-group='" + (itemGroup > 0 ? itemGroup : group) + "']")
        .css("mask-image", "url('" + zone_mask + "')")
        .css("-webkit-mask-image", "url('" + zone_mask + "')")
        .addClass("mask-image-zone");
    }
    //width = $(".zone_limit[data-group='"+(itemGroup > 0 ? itemGroup : group)+"']").width()+'px';
    //height = 'auto';
  } else {
    container = "#image-block";
    containment = "";
    maxwidth = "100%";
    maxheight = "100%";
    height = "auto";
    //width = $("#image-block").width()+'px';
    //width = '100%';
  }
  if (resizeable > 0) {
    maxwidth = "5000px";
    maxheight = "5000px";
  }

  $(container).css("z-index", zindex > -1 ? zindex : 0);

  editIndex = "";

  editButton =
    "<span " + editIndex + ' class="editIt" data-html2canvas-ignore></span>';

  if ($("#image-block[data-view='" + view + "']").length == 0 || view == 0)
    specifiView = false;

  //console.log(specifiView);

  if (
    $("#image-block").attr("data-view") == view ||
    view == 0 ||
    !specifiView
  ) {
    if (typeof visu != "undefined") {
      if ($("#image-block .group-" + group).length > 0) {
        //$("#image-block .group-" + group).fadeOut("fast");

        if (type == "canvas" || type == "text" || type == "svg") {
          if (target) $(target).append(visu).attr("alt", "composition_element");
          else
            $("#image-block .group-" + group + " .composition_element")
              .replaceWith(visu)
              .attr("alt", "composition_element");
        } else if (type == "colorize_new") {
          //console.log(background)
          $("#image-block .group-" + group + " svg").remove();
          $("#image-block .group-" + group + " object").remove();
          ndkImagetoDataURLForSvg(background, function (bgImage) {
            png2svg(bgImage, visu, background, group);
          });
        } else if (type == "colorize") {
          //console.log(visu);
          if (!ndkBrowserVersion) {
            $("#image-block .group-" + group + " .colorize-cover-item")
              .css("background", background)
              .attr("data-bgcolor", background);
            //$('#image-block .group-'+group+' .colorize-cover-item').css('mask-image', 'url(\''+$('.js-qv-product-cover:eq(0)').attr('src')+'\')');
          } else {
            $("#image-block .group-" + group + " svg").remove();
            $("#image-block .group-" + group + " object").remove();
            ndkImagetoDataURLForSvg(background, function (bgImage) {
              png2svg(bgImage, visu, background, group);
            });
          }
        } else {
          if (target)
            $(target).append(
              '<img alt="composition_element" class="composition_element img-reponsive ' +
                category +
                '" src="' +
                visu +
                '"/>'
            );
          else
            $(
              "#image-block .group-" + group + " .composition_element"
            ).replaceWith(
              '<img alt="composition_element" class="composition_element img-reponsive ' +
                category +
                '" src="' +
                visu +
                '"/>'
            );
        }

        //$("#image-block .group-" + group).fadeIn("slow");

        if (width != 0) {
          //$('#image-block .group-'+group).css('height', height).css('width', width).css('max-width', maxwidth).css('max-height', maxheight);
          //$('#image-block .group-'+group).parent(':not(.zone_limit)').css('height', height).css('width', width);
        }

        if (dragdrop == 1 || resizeable == 1 || rotateable == 1) {
          layerOptions = "";

          if (type == "canvas" || type == "text")
            layerOptions +=
              '<span id="layer-edit-' +
              group +
              '" data-view="' +
              view +
              '" data-group="' +
              group +
              '" class="editThisLayer">' +
              visu +
              "</span>";
          else if (type == "svg") {
            if (typeof visu == "object") output = visu.prop("outerHTML");
            else output = visu;

            //console.log(typeof(visu));
            layerOptions +=
              '<span id="layer-edit-' +
              group +
              '" data-view="' +
              view +
              '" data-group="' +
              group +
              '" class="editThisLayer">' +
              output +
              "</span>";
          } else
            layerOptions +=
              '<span id="layer-edit-' +
              group +
              '" data-view="' +
              view +
              '" data-group="' +
              group +
              '" class="editThisLayer"><img class="img-responsive very-small-img" src="' +
              visu +
              '"/></span>';

          if ($("#layer-edit-" + group).length > 0) {
            $("#layer-edit-" + group).replaceWith(layerOptions);
            layerOptions = "";
          } else {
            $("#layer-block").append(layerOptions).show();
          }
          //setEditable('#visual_'+group, zindex, containment, layerOptions, dragdrop, resizeable, rotateable );

          if (
            width != 0 &&
            type != "canvas" &&
            type != "text" &&
            type != "svg"
          ) {
            //$('#visual_'+group).css('height', height).css('width', width).css('max-width', maxwidth).css('max-height', maxheight);
            //$('#visual_'+group).parent().css('height', height).css('width', width);
          }
        }
        $("#image-block .group-" + group).css("mix-blend-mode", coloreffect);
      } else {
        viewClass = " ";
        if (view.indexOf("|") > -1) {
          views = view.split("|");
          for (var i = 0; i < views.length; i++) {
            viewClass += " view-" + views[i] + " ";
          }
        }

        $("#image-block .group-" + group).remove();

        if (type == "canvas") {
          $(container).append(
            '<div data-group="' +
              group +
              '" data-zindex="' +
              zindex +
              '" id="visual_' +
              group +
              '" style="margin:none;width:auto; height:' +
              height +
              "; max-width:" +
              maxwidth +
              "; max-height:" +
              maxheight +
              "; z-index:" +
              zindex +
              "; mix-blend-mode:" +
              coloreffect +
              '" class="' +
              (dragdrop == 1 ? "dragdrop" : "") +
              " absolute-visu group-" +
              group +
              " view-" +
              view +
              viewClass +
              specialClass +
              '"></div>'
          );
          $("#visual_" + group).html(visu + editButton);
        } else if (type == "svg") {
          $(container).append(
            '<div data-group="' +
              group +
              '" data-zindex="' +
              zindex +
              '" id="visual_' +
              group +
              '" style="margin:none; height:100%' +
              "; max-width:" +
              maxwidth +
              "; max-height:" +
              maxheight +
              "; z-index:" +
              zindex +
              ";mix-blend-mode:" +
              coloreffect +
              '" class="' +
              (dragdrop == 1 ? "dragdrop" : "") +
              " absolute-visu group-" +
              group +
              " view-" +
              view +
              viewClass +
              specialClass +
              ' absolute-svg-text" data-text_vertical_align="' +
              mainBlock.data("text_vertical_align") +
              '"><div class="composition_element ' +
              category +
              '"></div>' +
              editButton +
              "</div>"
          );
          $("#image-block .group-" + group + " .composition_element")
            .replaceWith(visu)
            .attr("alt", "composition_element");
          // if (resizeable != 1) {
          //   setTextSvgDimension(group);
          // }
        } else if (type == "text") {
          $(container).append(
            '<div data-group="' +
              group +
              '" data-zindex="' +
              zindex +
              '" id="visual_' +
              group +
              '" style="margin:none;width:' +
              originalWidth +
              "; height:" +
              height +
              "; max-width:" +
              maxwidth +
              "; max-height:" +
              maxheight +
              "; z-index:" +
              zindex +
              "; mix-blend-mode:" +
              coloreffect +
              '" class="' +
              (dragdrop == 1 ? "dragdrop" : "") +
              " absolute-visu group-" +
              group +
              " view-" +
              view +
              viewClass +
              specialClass +
              '">' +
              visu +
              editButton +
              "</div>"
          );
        } else if (type == "colorize") {
          if (!ndkBrowserVersion) {
            $(container).append(
              '<div data-group="' +
                group +
                '" data-zindex="' +
                zindex +
                '" id="visual_' +
                group +
                '" style="height:' +
                height +
                "; max-width:" +
                maxwidth +
                "; max-height:" +
                maxheight +
                "; z-index:" +
                zindex +
                " ;mix-blend-mode:" +
                coloreffect +
                ';" class="' +
                (dragdrop == 1 ? "dragdrop" : "") +
                " absolute-visu group-" +
                group +
                " view-" +
                view +
                viewClass +
                specialClass +
                " absolute-img " +
                (type == "color" ? "multiply-mode-color" : "") +
                '"><div  style="background:' +
                background +
                "; mask-image: url('" +
                visu +
                "');-webkit-mask-image: url('" +
                visu +
                '\');" class="colorize-cover-item"><img alt="composition_element" class=" composition_element img-reponsive ' +
                category +
                '" src="' +
                visu +
                '"/></div>' +
                editButton +
                "</div>"
            );
          } else {
            $(container).append(
              '<div data-group="' +
                group +
                '" data-zindex="' +
                zindex +
                '" id="visual_' +
                group +
                '" style="height:' +
                height +
                "; max-width:" +
                maxwidth +
                "; max-height:" +
                maxheight +
                "; z-index:" +
                zindex +
                " ;mix-blend-mode:" +
                coloreffect +
                ';" class="' +
                (dragdrop == 1 ? "dragdrop" : "") +
                " absolute-visu group-" +
                group +
                " view-" +
                view +
                viewClass +
                specialClass +
                " absolute-img " +
                (type == "color" ? "multiply-mode-color" : "") +
                '">' +
                editButton +
                "</div>"
            );

            ndkImagetoDataURLForSvg(background, function (bgImage) {
              png2svg(bgImage, visu, background, group);
            });
          }
        } else if (type == "colorize_new") {
          $(container).append(
            '<div data-group="' +
              group +
              '" data-zindex="' +
              zindex +
              '" id="visual_' +
              group +
              '" style="height:' +
              height +
              "; max-width:" +
              maxwidth +
              "; max-height:" +
              maxheight +
              "; z-index:" +
              zindex +
              " ;mix-blend-mode:" +
              coloreffect +
              ';" class="' +
              (dragdrop == 1 ? "dragdrop" : "") +
              " absolute-visu group-" +
              group +
              " view-" +
              view +
              viewClass +
              specialClass +
              " absolute-img " +
              (type == "color" ? "multiply-mode-color" : "") +
              '">' +
              editButton +
              "</div>"
          );

          ndkImagetoDataURLForSvg(background, function (bgImage) {
            png2svg(bgImage, visu, background, group);
          });
        } else {
          $(container).append(
            '<div data-group="' +
              group +
              '" data-zindex="' +
              zindex +
              '" id="visual_' +
              group +
              '" style="margin:none;width:auto; height:' +
              height +
              "; max-width:" +
              maxwidth +
              "; max-height:" +
              maxheight +
              "; z-index:" +
              zindex +
              " ;mix-blend-mode:" +
              coloreffect +
              ';" class="' +
              (dragdrop == 1 ? "dragdrop" : "") +
              " absolute-visu group-" +
              group +
              " view-" +
              view +
              viewClass +
              specialClass +
              " absolute-img " +
              (type == "color" ? "multiply-mode-color" : "") +
              '"><img  alt="composition_element" class=" composition_element img-reponsive ' +
              category +
              '" src="' +
              visu +
              '"/>' +
              editButton +
              "</div>"
          );
          //$(container).append('<img data-zindex="'+zindex+'" id="visual_'+group+'" style="margin:auto;width:auto; height:'+height+'; max-width:'+maxwidth+'; max-height:'+maxheight+'; z-index:'+zindex+'" src="'+visu+'" class="'+((dragdrop == 1) ? 'dragdrop' : '')+' absolute-visu group-'+group+' view-'+view+'"/>');
        }

        if (dragdrop == 1 || resizeable == 1 || rotateable == 1) {
          layerOptions = "";

          if (type == "canvas" || type == "text")
            layerOptions +=
              '<span id="layer-edit-' +
              group +
              '" data-view="' +
              view +
              '" data-group="' +
              group +
              '" class="editThisLayer">' +
              visu +
              "</span>";
          else if (type == "svg") {
            if (typeof visu == "object") output = visu.prop("outerHTML");
            else output = visu;

            //console.log(typeof(visu));
            layerOptions +=
              '<span id="layer-edit-' +
              group +
              '" data-view="' +
              view +
              '" data-group="' +
              group +
              '" class="editThisLayer">' +
              output +
              "</span>";
          } else
            layerOptions +=
              '<span id="layer-edit-' +
              group +
              '" data-view="' +
              view +
              '" data-group="' +
              group +
              '" class="editThisLayer"><img class="img-responsive very-small-img" src="' +
              visu +
              '"/></span>';

          //layerOptions = '';

          if (type == "canvas")
            setEditable(
              "#visual_" + group + " > canvas",
              zindex,
              containment,
              layerOptions,
              dragdrop,
              resizeable,
              rotateable,
              group
            );
          else
            setEditable(
              "#visual_" + group,
              zindex,
              containment,
              layerOptions,
              dragdrop,
              resizeable,
              rotateable,
              group
            );

          //$('#cecft_'+group).css({height : height, width : width});
          if (
            width != 0 &&
            type != "canvas" &&
            type != "text" &&
            type != "svg"
          ) {
            //$('#visual_'+group).css('height', height).css('width', width).css('max-width', maxwidth).css('max-height', maxheight);
            //$('#visual_'+group).parent().css('height', height).css('width', width);
          }
        }
      }
    }
    scrollToNdk($(container), 800);

    //test provisoire : snapShotLight();
  }

  //on met à jour les calques
  /*$(".layers[data-view='"+view+"']").show();
        layerRowId = 'layer_'+$(el).attr('id');
        rowTitle = $(".form-group[data-field='"+group+"']").find('.toggler').text();
        if($('#'+layerRowId).length > 0)
            $('#'+layerRowId).html(rowTitle);
        else
            $(".layers[data-view='"+view+"']").append('<div>test</div>');
        */
  setTimeout(function () {
    $(".orientation_selection[data-group-target='" + group + "']")
      .find(".active_orientation")
      .trigger("click");
  }, 800);
  checkElementResizeMax(group);
  $(document).trigger({
    type: "ndkacf:ndkCompoDesigned",
    group: group,
    resizeable: resizeable,
    dragdrop: dragdrop,
    rotateable: rotateable,
  });

  $(document).trigger("ndkCompoDesigned");

  //restoreViewBox(group);
}

$(document).on("click", ".toggleLayer", function () {
  $("#layer-block").toggleClass("visible-layer");
});

$(document).on("ndkacf:ndkCompoDesigned", function (e) {
  setEditableDimension(e);
  setTimeout(function () {
    closePopupConfigBlock();
  }, 500);
});

async function setEditableDimension(e) {
  var editable = $("#visual_" + e.group);
  if (editable.find(".textareaSvg").length < 1) {
    if (e.dragdrop == 1 || e.resizable == 1 || e.rotatable == 1) {
      editable.css({
        height: "auto",
      });
      if (typeof ndkMovables[e.group] != "undefined")
        ndkMovables[e.group].updateTarget();
    }
  }
}

var ndkMovables = [];
async function setEditable(
  el,
  zindex,
  ctnmt,
  layerOptions,
  dragdrop,
  resizeable,
  rotateable,
  group,
  zone = false
) {
  if (typeof setEditable_Override == "function") {
    return setEditable_Override(
      el,
      zindex,
      ctnmt,
      layerOptions,
      dragdrop,
      resizeable,
      rotateable,
      group,
      (zone = false)
    );
  }
  //console.log($(el).parent().height());
  //var wait = await resolveAfterTime(500);
  ctnmt = $(el).parent();
  group = group || $(el).attr("id").replace("visual_", "");
  mainGroup = group;
  if (group.indexOf("-") > -1) {
    isItem = true;
    number = group.split("-")[1];
    mainGroup = group.split("-")[0];
  }
  await resolveAfterTime(500);
  if (!$(el).hasClass("hasBeenDragged") && !zone) {
    setSvgLineHeight(
      group,
      $(".form-group[data-field='" + mainGroup + "']").attr("data-line_spacing")
    );
    setTextSvgDimension(group, resizeable);
  }

  view = $(el).parent().attr("data-view");
  if (dragdrop == 1 || resizable == 1 || rotatable == 1) {
    ndkMovables[group] = new window.ndkMovable(ctnmt[0], {
      target: el,
      //container: el,
      draggable: dragdrop == 1,
      resizable: resizeable == 1,
      rotatable: rotateable == 1,
      pinchable: dragdrop == 1 && resizeable == 1 && rotateable == 1, // ["resizable", "scalable", "rotatable"]
      warpable: false,
      clippable: false,
      defaultClipPath: "inset",
      customClipPath: "",
      clipRelative: true,
      clipArea: false,
      dragWithClip: true,
      clipTargetBounds: true,
      defaultClipPath: "inset(5%)",
      keepRatio: !zone,
      throttleResize: 0,
      throttleRotate: 5,
      elementGuidelines: [
        document.querySelector(".absolute-visu"),
        document.querySelector(".zone_limit"),
      ],
      snappable: true,
      //verticalGuidelines: [0, 200, 400],
      //horizontalGuidelines: [0, 200, 400],
      snapThreshold: 5,
      isDisplaySnapDigit: true,
      snapGap: true,
      snapElement: true,
      snapVertical: true,
      snapHorizontal: true,
      snapCenter: true,
      snapDigit: 0,
      snapContainer: document.querySelector("#image-block"),
      renderDirections: ["nw", "n", "ne", "w", "e", "sw", "s", "se"],
      edge: false,
      zoom: 1,
      origin: true,
      className:
        "ndkacf-movable-" + group + (zone ? " ndkacf-movable-zone" : ""),
    });
  }

  if (resizeable == 1) {
    $(el).removeClass("notResizeable").addClass("ui-resizable");
  } else {
    $(el).addClass("notResizeable");
  }
  if (dragdrop == 1) {
    $(el).addClass("ui-draggable");
  }
  if (rotateable == 1) {
    $(el).addClass("rotatable").addClass("ui-rotatable");
  }
  rotDragEl = $(el);

  $(el)
    .attr("data-dragdrop", dragdrop)
    .attr("data-rotateable", rotateable)
    .attr("data-resizeable", resizeable);
  $(el).css("z-index", zindex).attr("data-zindex", zindex);

  //group = rotDragEl.attr("data-group").split("-");

  if ($(el).parent().find(".layer_view").length < 1)
    $("#layer-block").append(layerOptions);

  $("#image-block").find(".fontSelect >*, canvas > *").show();
  $("#image-block").find(".ui-wrapper").show().css("position", "absolute");

  var editTimer;
  var startUiAction = (target, group) => {
    clearTimeout(editTimer);
    checkElementResizeMax(group);
    $(".moveable-control-box,.moveable-control").addClass("panzoom-exclude");
    initPanZoom();
    if (!$(target).hasClass("activeZone") && group)
      $(".form-group[data-field='" + group[0] + "']").trigger("click");
    $("#image-block").addClass("editing");
    $(target).addClass("hasBeenDragged");
    $(target).addClass("editing_item");
    $(target).parent().addClass("activeZone");
    $(".moveable-control-box,.moveable-control, .ndkMovable").addClass(
      "panzoom-exclude"
    );
    mainGroup = group;
    if (group.indexOf("-") > -1) {
      mainGroup = group.split("-")[0];
    }
    //zindex
    $("#ndk-movable-tools").attr("data-group", group).show();
    if (group.indexOf("-") > -1) {
      $(".ndk-designer-only").show();
    } else {
      $(".ndk-designer-only").hide();
    }

    if ($(target).hasClass("zone_limit")) {
      $(".tool-item").hide();
      $(".ndk-zone-only").show();
    } else {
      $(".ndk-zone-only").hide();
    }

    //rotatable
    if ($(target).hasClass("rotatable")) {
      $(".tool-item.ndk-rotate").show();
    } else {
      $(".tool-item.ndk-rotate").hide();
    }

    //clippable
    clippable = $(".form-group[data-field='" + mainGroup + "']").attr(
      "data-clippable"
    );
    if (
      typeof clippable == "undefined" ||
      clippable != 1 ||
      clippable == "undefined" ||
      $("#visual_" + group + " .textareaSvg").length > 0
    ) {
      $(".tool-item.ndk-crop-tool").hide();
    } else {
      $(".tool-item.ndk-crop-tool").show();
    }
  };

  var stopUiAction = async (target, group) => {
    convertPercent();
    clearTimeout(editTimer);
    $(".editing").removeClass("editing");

    $(target).css("z-index", zindex);

    checkLayerChanges();
    editTimer = setTimeout(function () {
      $(".moveable-control-box").hide();
      $("#ndk-movable-tools").hide();
      $(".ndk-tool-zone-save").hide();
      $(".resetZones").trigger("click");
      resizeBar = document.querySelector("#ndk-resize-bar-" + group + " input");
      if (typeof resizeBar != "undefined" && resizeBar != null)
        resizeBar.value = target.offsetWidth;
      $(".editing_item").removeClass("editing_item");
    }, editTimerDuration);
  };

  /* draggable */
  var frame = {
    translate: [0, 0],
  };
  ndkMovables[group]
    .on("dragStart", ({ target, clientX, clientY }) => {
      target.classList.add("ui-draggable");
      startUiAction(target, group);
    })
    .on(
      "drag",
      ({
        target,
        transform,
        left,
        top,
        right,
        bottom,
        beforeDelta,
        beforeDist,
        delta,
        dist,
        clientX,
        clientY,
      }) => {
        target.style.left = `${left}px`;
        target.style.top = `${top}px`;
        makeToolFollowing(target);
      }
    )
    .on("dragEnd", ({ target, isDrag, clientX, clientY }) => {
      stopUiAction(target, group);
    });

  // ndkMovables[group]
  //   .on("dragStart", (e) => {
  //     e.set(frame.translate);
  //   })
  //   .on("drag", (e) => {
  //     frame.translate = e.beforeTranslate;
  //     e.target.style.transform.translate = `(${e.beforeTranslate[0]}px, ${e.beforeTranslate[1]}px)`;
  //   });

  /* resizable */

  ndkMovables[group]
    .on("resizeStart", ({ target, clientX, clientY }) => {
      target.classList.add("ui-resizable");
      startUiAction(target, group);
    })
    // .on(
    //   "resize",
    //   ({ target, width, height, dist, delta, clientX, clientY }) => {
    //     delta[0] && (target.style.width = `${width}px`);
    //     delta[1] && (target.style.height = `${height}px`);
    //     makeToolFollowing(target);
    //   }
    // )

    .on("resize", (e) => {
      var group = e.target.getAttribute("data-group");

      e.delta[0] && (e.target.style.width = `${e.width}px`);
      e.delta[1] && (e.target.style.height = `${e.height}px`);
      makeToolFollowing(e.target);
      e.target.style.top = `${e.drag.top}px`;
      e.target.style.left = `${e.drag.left}px`;
    })
    .on("resizeEnd", ({ target, isDrag, clientX, clientY }) => {
      stopUiAction(target, group);
    });

  /* rotatable */
  ndkMovables[group]
    .on("rotateStart", ({ target, clientX, clientY }) => {
      target.classList.add("ui-rotatable");
      startUiAction(target, group);
    })
    .on("rotate", (e) => {
      //e.target.style.transform = e.transform;
      changeRotate(e.target, e.rotate);
      makeToolFollowing(e.target);
    })
    .on("rotateEnd", ({ target, isDrag, clientX, clientY }) => {
      stopUiAction(target, group);
    });

  /* wrappable */
  var wrap_frame = {
    matrix: [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1],
  };
  ndkMovables[group]
    .on("warpStart", (e) => {
      startUiAction(target, group);
      e.set(wrap_frame.matrix);
    })
    .on("warp", (e) => {
      wrap_frame.matrix = e.matrix;
      //e.target.style.transform = `matrix3d(${e.matrix.join(",")})`;
      changeMatrix(e.target, e.matrix.join(","));
    });

  /* pinchable */
  // Enabling pinchable lets you use events that
  // can be used in draggable, resizable, scalable, and rotateable.
  ndkMovables[group]
    .on("pinchStart", ({ target, clientX, clientY }) => {
      startUiAction(target, group);
    })
    .on("pinch", ({ target, clientX, clientY, datas }) => {})
    .on("pinchEnd", ({ isDrag, target, clientX, clientY, datas }) => {
      stopUiAction(target, group);
    });

  /* clipable */
  ndkMovables[group]
    .on("clipStart", ({ target, clientX, clientY }) => {
      startUiAction(target, group);
    })
    .on("clip", (e) => {
      if (e.clipType === "rect") {
        e.target.style.clip = e.clipStyle;
      } else {
        e.target.style.clipPath = e.clipStyle;
      }
      frame.clipStyle = e.clipStyle;
    })
    .on("clipEnd", ({ target, clientX, clientY }) => {
      stopUiAction(target, group);
    });

  $(el).addClass("ndkMovable").trigger("mousedown");
  //ndkMovables[group].request("draggable", { deltaX: 0, deltaY: 0 }, true);

  if (!$(el).hasClass("hasBeenDragged") && !zone) {
    dragToCenter(group);
    applySpecialsMoves(group);
  }

  ndkMovables[group].updateTarget();
}

function checkElementResizeMax(group) {
  if (group.indexOf("-zone") > -1) return true;
  if (group.indexOf("-") > -1) {
    isItem = true;
    number = group.split("-")[1];
    rootGroup = group.split("-")[0];
  } else {
    rootGroup = group;
  }
  rootBlock = document.querySelector(
    '.form-group[data-field="' + rootGroup + '"]'
  );
  var target = document.querySelector("#visual_" + group);
  if (typeof rootBlock.getAttribute("data-resize-max-height") != "undefined") {
    maxHpercent = rootBlock.getAttribute("data-resize-max-height");
    if (maxHpercent > 0) {
      target.classList.add("resize-max-height");
      target.style.maxHeight = `${maxHpercent}%`;
    }
  }
  if (typeof rootBlock.getAttribute("data-resize-max-width") != "undefined") {
    maxWpercent = rootBlock.getAttribute("data-resize-max-width");
    if (maxWpercent > 0) {
      target.classList.add("resize-max-width");
      target.style.maxWidth = `${maxWpercent}%`;
    }
  }
}

$(document).on("mouseenter", ".absolute-visu", function (e) {
  if ($(".editing_item").length < 1) {
    displayMovableControls($(this).attr("data-group"));
  }
});
$(document).on("mouseenter", ".zone_limit.ndkMovable", function (e) {
  if ($(".editing_item").length < 1) {
    displayMovableControls($(this).attr("data-view") + "-zone");
  }
});

$(document).on("mousedown touchstart", ".absolute-visu", function (e) {
  displayMovableControls($(this).attr("data-group"));
  // setTimeout(function () {
  //   $(".ndk-movable-setting").trigger("click");
  // }, 150);
});

$(document).on("mousedown touchstart", ".zone_limit.ndkMovable", function (e) {
  displayMovableControls($(this).attr("data-group") + "-zone");
});

$(document).on("mouseenter", ".zone_limit:empty:not(.ndkMovable)", function () {
  if ($(".editing_item").length < 1) {
    $(".activeZone").removeClass("activeZone");
    $(this).addClass("activeZone");
  }
});

$(document).on("mouseleave", ".zone_limit:empty:not(.ndkMovable)", function () {
  $(this).removeClass("activeZone");
});

$(document).on("click", ".zone_limit:empty:not(.ndkMovable)", function () {
  targetGroup = $(
    ".form-group[data-field=" + $(this).attr("data-group") + "]:eq(0)"
  );
  $(".toggler.active").removeClass("active");
  $(".fieldPane").hide();

  targetGroup.find(".toggler").trigger("click");
  targetGroup.addClass("activeFormGroup");
  setTimeout(function () {
    openPopupConfigBlock();
  }, 500);
  scrollToNdk(targetGroup, 800, true);
});

function displayMovableControls(group) {
  $(".moveable-control-box").hide();
  $(".ndkacf-movable-" + group).show();
}

function makeToolFollowing(target) {
  var rectPan = document.getElementById("image-block").getBoundingClientRect();
  if ($(window).width() < 768) var rect = rectPan;
  else var rect = target.getBoundingClientRect();

  toolBox = document.querySelector("#ndk-movable-tools");
  toolBoxRect = toolBox.getBoundingClientRect();
  newTop = rect.top + rect.height;
  rectPanBottom = rectPan.top + rectPan.height;
  toolBox.style.top = (newTop < rectPanBottom ? newTop : rectPanBottom) + "px";
  toolBox.style.left =
    rect.left - (toolBoxRect.width / 2 - rect.width / 2) + "px";

  //save zone

  $(".ndk-tool-zone-save").hide();
  group = $(target).attr("data-group");
  if ($("#ndk-tool-zone-save-" + group + "-zone").length > 0) {
    saveBtn = document.querySelector("#ndk-tool-zone-save-" + group + "-zone");
    //console.log(group);
    $(saveBtn).show();
    //console.log(target);
    saveBtnRect = saveBtn.getBoundingClientRect();
    saveBtn.style.top = target.style.top;
    saveBtn.style.left = target.style.left;
  }
}

window.addEventListener("scroll", function () {
  // target = document.querySelector(".activeZone .ndkMovable");
  // if (typeof target != "undefined" && target != null) makeToolFollowing(target);
  $("#ndk-movable-tools").hide();
  $(".ndk-tool-zone-save").hide();
});

function clipStyleToPercent(node, clipStyles, clipType) {
  clipStyleCss = `${clipType}(${
    (parseFloat(clipStyles[0]) / parseFloat(node.offsetHeight)) * 100
  }% ${(parseFloat(clipStyles[1]) / parseFloat(node.offsetWidth)) * 100}% ${
    (parseFloat(clipStyles[2]) / parseFloat(node.offsetHeight)) * 100
  }% ${(parseFloat(clipStyles[3]) / parseFloat(node.offsetWidth)) * 100}%)`;
  console.log(clipStyleCss);
  return clipStyleCss;
}

function dragToCenter(group) {
  editable = $("#visual_" + group);
  parent = editable.parent();

  if (parent.width() > parent.height()) {
    ndkMovables[group].request(
      "resizable",
      { offsetHeight: parent.height() },
      true
    );
    position_y = 0;
    position_x = parent.width() / 2 - editable.width() / 2;
  } else {
    ndkMovables[group].request(
      "resizable",
      { offsetWidth: parent.width(), offsetHeight: parent.height() },
      true
    );
    position_y = parent.height() / 2 - editable.height() / 2;
    position_x = 0;
  }

  ndkMovables[group].request(
    "draggable",
    {
      //x: parent.width() / 2 - editable.width() / 2,
      x: position_x,
      y: position_y,
    },
    true
  );
  ndkMovables[group].request("resize");
}

function applySpecialsMoves(group) {
  editable = $("#visual_" + group);
  parent = editable.parent();
  mainGroup = group.split("-")[0];
  custom_class = $(".form-group[data-field='" + mainGroup + "']").attr(
    "data-custom_class"
  );
  var moveTocenter = function () {
    ndkMovables[group].request(
      "draggable",
      {
        x: parent.width() / 2 - editable.width() / 2,
        y: parent.height() / 2 - editable.height() / 2,
      },
      true
    );
  };
  if (typeof ndkMovables[group] != "undefined") {
    if (custom_class == "small_text") {
      ndkMovables[group].request("resizable", { offsetHeight: 80 }, true);
      moveTocenter();
    }
  }
}

function dragToPercent($elm) {
  if (typeof dragToPercent_Override == "function") {
    return dragToPercent_Override($elm);
  }
  var pos = $elm.position(),
    parentSizes = {
      height: $elm.parent().height(),
      width: $elm.parent().width(),
    };
  var elTop = parseFloat($elm.css("top").replace("px", ""));
  var elLeft = parseFloat($elm.css("left").replace("px", ""));

  //console.log(elLeft);
  $elm
    .css("top", (elTop / parentSizes.height) * 100 + "%")
    .css("left", (elLeft / parentSizes.width) * 100 + "%")
    .css("width", ($elm.width() / parentSizes.width) * 100 + "%")
    .css("height", ($elm.height() / parentSizes.height) * 100 + "%")
    .addClass("percented");
}

function dragToPercentBak(el) {
  if (typeof dragToPercentBak_Override == "function") {
    return dragToPercentBak_Override(el);
  }
  if (!el.hasClass("percented")) {
    var l =
      (100 * parseFloat(el.css("left"))) /
        parseFloat(el.parent().css("width")) +
      "%";
    var t =
      (100 * parseFloat(el.css("top"))) /
        parseFloat(el.parent().css("height")) +
      "%";
    var w =
      (100 * parseFloat(el.css("width"))) /
        parseFloat(el.parent().css("width")) +
      "%";
    el.css("left", l);
    el.css("top", t);
    el.css("width", w);
    el.css("height", "auto");
    el.addClass("percented");
  }
}

function convertPercentEl(el) {
  if (typeof convertPercentEl_Override == "function") {
    return convertPercentEl_Override(el);
  }
  container = el.parent();
  containerWidth = container.width();
  containerHeight = container.height();

  elWidth = el.width();
  elHeight = el.height();

  elLeft = el.css("left").replace("px", "");
  elTop = el.css("top").replace("px", "");

  widthPercent = (elWidth / containerWidth) * 100 + "%";
  heightPercent = (elHeight / containerHeight) * 100 + "%";
  heightPercent = "auto";
  leftPercent = (elLeft / containerWidth) * 100 + "%";
  topPercent = (elTop / containerHeight) * 100 + "%";

  el.css({
    width: widthPercent,
    height: heightPercent,
    left: leftPercent,
    top: topPercent,
    margin: "",
  });
}

function restoreViewBox(group) {
  $("#visual_" + group + " .viewBoxed:not(.restored)").each(function () {
    me = $(this);
    viewBox = me.attr("data-viewbox");
    if (typeof viewBox != "undefined")
      if (viewBox != "0 0 0 0")
        me[0].setAttribute("viewBox", me.attr("data-viewbox"));

    me.addClass("restored");
  });
}

let createPreview = async function (callback) {
  if (typeof createPreview_Override == "function") {
    return createPreview_Override(callback);
  }
  onlyHtml = true;
  if (showImgPreview == 1 && is_visual == true) {
    onlyHtml = false;
  }
  if (
    customizationPrice > 0 ||
    (typeof is_visual != "undefined" && is_visual == true)
  ) {
    //convertPercent();

    $(".ndk-img-url").val("").trigger("keyup");
    viewTabs = $(".view_tab:not(.glb)");
    if (viewTabs.length > 0) {
      $("li.view_tab").first().trigger("click");
    }
  }
  await snapShot(false, false, onlyHtml).then(function () {
    return callback();
  });
};

let snapShot = async function (force = false, first = false, onlyHtml = false) {
  if (typeof snapShot_Override == "function") {
    return snapShot_Override(force, first, onlyHtml);
  }
  if (showImgPreview == 1 && is_visual == true)
    $("#image-block").addClass("hight_quality");

  force = force || false;
  first = first || false;
  onlyHtml = onlyHtml || false;

  if (
    customizationPrice > 0 ||
    (typeof is_visual != "undefined" && is_visual == true)
  ) {
    //convertPercent();

    dataView = 0;
    if ($("li.view_tab").length == 0) {
      htmlOutput[0] =
        '<div class="print-page-breaker">' +
        $("#image-block").html() +
        "</div>";
    }
    compoImages = [];
    await processTabs(force, onlyHtml, first);
    $(".image-url").each(function () {
      compoImages.push($(this).val());
    });
    $("#image-block").removeClass("hight_quality");
  }
};

let simuViews = async function (force = false, first = false) {
  if (typeof simuViews_Override == "function") {
    return simuViews_Override(force);
  }
  force = force || false;
  first = first || false;
  return await snapShot(force, first);
};

let clickView = async function (tab) {
  var a = await resolveAfterTime(800);
  tab.trigger("click");
  return new Promise(function (resolve) {
    resolve(tab.attr("data-view"));
  });
};

let processTabs = async (force = false, onlyHtml = false, first = false) => {
  if (!first) var tabs = $("li.view_tab:not(.glb)");
  else var tabs = $("li.view_tab:eq(0)");

  if (tabs.length > 0) {
    i = 0;
    tabs.each(async function () {
      var tab = $(this);
      if (i > 0) onlyHtml = true;
      clickView(tab).then(async function (response) {
        await processTabForSnapShot(parseFloat(response), force, onlyHtml);
      });
      i++;
    });
  } else {
    return await processTabForSnapShot(0, force, onlyHtml);
  }
};

let processTabForSnapShot = async function (dataView, force, onlyHtml) {
  if (typeof processTabForSnapShot_Override == "function") {
    return processTabForSnapShot_Override(dataView, force, onlyHtml);
  }

  if (showImgPreview == 0) onlyHtml = true;
  cloneViewForScreenshot(dataView, force, onlyHtml).then(function (response) {
    htmlOutput[dataView] =
      '<div class="print-page-breaker">' + response + "</div>";
    //console.log(htmlOutput);
    clonedView.remove();
    if (!force) $(this).addClass("snapshoot");
    if (!onlyHtml) {
      resetZone();
    } else {
      resetZone();
    }
  });
  return await takePhoto(dataView, onlyHtml);
};

let cloneViewForScreenshot = async function (dataView, force, onlyHtml) {
  clonedView = $("#image-block[data-view ='" + dataView + "']").clone();
  //console.log(dataView, clonedView);
  $(clonedView)
    .find(
      "> :not(.view-" +
        dataView +
        " , .view-0, #view_full_size, #product-zoom, .ndk-svg-view, #bigpic, .tempSvg, .middle-content, .cover-content, .product-images-large, .swiper-slide)"
    )
    .remove();
  $(clonedView)
    .find(".hiddenForSnapshot")
    .show()
    .removeClass(".hiddenForSnapshot");
  $(clonedView)
    .find("#image-block[data-view ='" + dataView + "'] svg")
    .show();

  return new Promise(function (resolve) {
    console.log("cloned");
    resolve($(clonedView).html());
  });
};

let takePhoto = async function (dataView = 0, onlyHtml = false, force = false) {
  if (showImgPreview == 0 || onlyHtml) {
    if (!force) return true;
  }

  if ($(".view_tab[data-view='" + dataView + "']").length > 0)
    $(".view_tab[data-view='" + dataView + "']").trigger("click");
  else {
    $(".view_tab:eq(0)").trigger("click");
  }
  await ndkHtmlToPng($("#image-block")[0])
    .then(function (dataURL) {
      $("#image-url-" + dataView)
        .val(dataURL)
        .trigger("keyup");
      $(".tempSvg").remove();
      $(".hiddenForSnapshot").show().removeClass(".hiddenForSnapshot");
      $("#image-block[data-view ='" + dataView + "']")
        .find("svg")
        .show();
      $(".current_config_img").attr("src", dataURL).show();
      $("#image-url-0").val(dataURL).trigger("keyup");
      return dataURL;
    })
    .catch(function (error) {
      console.error("oops, something went wrong!", error);
      return "null";
    });
};

function svgToCanvas(targetElem) {
  if (typeof svgToCanvas_Override == "function") {
    return svgToCanvas_Override(targetElem);
  }
  if (showImgPreview > 0) {
    var svgElem = targetElem.find("svg");
    $(".tempSvg").remove();
    svgElem.each(function (index, node) {
      el = $(this);
      el.show();
      var image = new Image();
      var parentNode = node.parentNode;
      var svg = node.outerHTML;

      width = el[0].getBoundingClientRect().width;
      height = el[0].getBoundingClientRect().height;

      image.src = "data:image/svg+xml," + escape(svg);
      parentNode.appendChild(image);
      $(image).addClass("tempSvg replaced-svg composition_element");
      el.addClass("hiddenForSnapshot");

      $(image).load(function () {
        var canvas = document.createElement("canvas");
        image.width = width;
        image.height = height;
        canvas.width = width;
        canvas.height = height;
        var context = canvas.getContext("2d");
        $.when(context.drawImage(image, 0, 0)).done(function () {
          image.src = canvas.toDataURL();
        });
      });
      el.hide();
    });
  }
}

function snapShotFirst() {
  if (typeof snapShotFirst_Override == "function") {
    return snapShotFirst_Override();
  }
  if (showImgPreview == 1) {
    ndkHtmlToPng($("#image-block")[0])
      .then(function (dataURL) {
        $("#image-url-0").val(dataURL).trigger("keyup");
        $(".ndkzoom").attr("href", dataURL);
      })
      .catch(function (error) {
        console.error("oops, something went wrong!", error);
      });
  }
}

//setup before functions
var typingTimer; //timer identifier

function snapShotLight() {
  if (typeof snapShotLight_Override == "function") {
    return snapShotLight_Override();
  }

  if (
    customizationPrice > 0 ||
    (typeof is_visual != "undefined" && is_visual == true)
  ) {
    if (showImgPreview == 1) {
      $(".ndkzoom").attr("disabled", "disabled").addClass("loadingButton");
      dataView = 0;
      dataView = $("#image-block").attr("data-view");
      if (typeof (dataView == "undefined")) dataView = 0;
      if (!dataView.length || dataView.length == 0) dataView = 0;

      ndkHtmlToPng($("#image-block")[0])
        .then(function (dataURL) {
          $("#image-url-" + dataView)
            .val(dataURL)
            .trigger("keyup");
          $(".ndkzoom").attr("href", dataURL);
          $(".ndkzoom").attr("disabled", false).removeClass("loadingButton");
        })
        .catch(function (error) {
          console.error("oops, something went wrong!", error);
        });
    }
  }

  /*if($('.image-url').length > 0)
                 $('#image-url-0').val('');*/
}

function setToZone(el, zone) {
  if (typeof setToZone_Override == "function") {
    return setToZone_Override(el, zone);
  }
  left = $(zone).offset().left;
  top = $(zone).offset().top + $(zone).outerHeight();
  $(el).css({
    position: "absolute",
    left: left + "px",
    top: top + "px",
  });
}

function stopWheel(e) {
  if (typeof stopWheel_Override == "function") {
    return stopWheel_Override(e);
  }
  if (!e) {
    /* IE7, IE8, Chrome, Safari */
    e = window.event;
  }
  if (e.preventDefault) {
    /* Chrome, Safari, Firefox */
    e.preventDefault();
  }
  e.returnValue = false; /* IE7, IE8 */
}

function setLetterWidth() {
  if (typeof setLetterWidth_Override == "function") {
    return setLetterWidth_Override();
  }
  $(".customFontLetter").each(function () {
    parentWidth = $(this).parent().width();
    elWidth = $(this).width();
    $(this).width((elWidth / parentWidth) * 100 + "%");
  });
  equalheightNdkcf(".customFontLetter");
}

$(document).on("mouseover", ".form-group", function () {
  $(".zone_limit").removeClass("activeZone");
  if ($(this).attr("data-is_editable") == 1)
    $(".zone_limit[data-group='" + $(this).attr("data-field") + "']").addClass(
      "activeZone"
    );
});

$(document).on("mouseleave", "#image-block", function () {
  $(".zone_limit").removeClass("activeZone");
});

$(document).on("dblclick", ".zone_limit, .absolute-visu", function (e) {
  zoomOnZone($(this), e);
  $(".ndk-movable-setting").trigger("click");
});

function zoomOnZone(zone, pointerEvent) {
  pan = $("#ndk-panzoom");

  z_width = pan.width() / zone.width();
  z_height = pan.height() / zone.height();

  x_focal = zone.offset().left - pan.offset().left;
  y_focal = zone.offset().top - pan.offset().top;

  if (z_width > z_height) zoom = z_height;
  else zoom = z_width;
  if (panZoomInitiated) panZoom.zoomToPoint(zoom, pointerEvent);
}
var panZoom, panZoomInitiated;

function initPanZoom() {
  //if ($(window).width() < 768) return false;
  if (panZoomInitiated || !is_popup_mode) return false;

  var zoomControls =
    '<i class="material-icons toggleLayer" title="layers">layers</i><div class="ndk-panzoom-control"><i class="material-icons ndk-zoomIn ndk-zoomControl">zoom_in</i><i class="material-icons ndk-zoomOut ndk-zoomControl">zoom_out</i></div>';

  $("#image-block").wrap(
    '<div class="ndk-panzoom-container">' +
      '<div id="ndk-panzoom" class="ndk-panzoom"></div></div>'
  );

  $(".ndk-panzoom-container").prepend(zoomControls);

  myArea = document.getElementById("ndk-panzoom");
  panZoom = Panzoom(myArea, {
    maxScale: 5,
    minScale: 0.5,
    exlude: document.querySelectorAll(
      ".moveable-control-box, .moveable-control-box *, .ndkMovable, .ndkMovable *, .moveable-control"
    ),
  });

  myArea.parentNode.addEventListener("wheel", function (event) {
    if (!event.shiftKey) return;
    panZoom.zoomWithWheel(event);
  });
  myArea.addEventListener("panzoomchange", (event) => {
    $("#ndk-movable-tools").hide();
    $(".ndk-tool-zone-save").hide();
    var excludes = document.querySelectorAll(".moveable-control"),
      i;
    controls_scale = 1 / event.detail.scale;
    for (i = 0; i < excludes.length; ++i) {
      changeScale(excludes[i], controls_scale);
    }
  });

  document
    .querySelector(".ndk-zoomIn")
    .addEventListener("click", panZoom.zoomIn);
  document
    .querySelector(".ndk-zoomOut")
    .addEventListener("click", panZoom.zoomOut);

  document.addEventListener("click", (event) => {
    if (
      !event.target.matches(
        "#image-block, #image-block *, .ndk-panzoom-container, .ndk-panzoom-container *"
      )
    ) {
      panZoom.reset();
    }
  });
  window.addEventListener("resize", function (event) {
    panZoom.reset();
  });

  panZoomInitiated = true;
}

/*
 * .addClassSVG(className)
 * Adds the specified class(es) to each of the set of matched SVG elements.
 */
$.fn.addClassSVG = function (className) {
  $(this).attr("class", function (index, existingClassNames) {
    return (
      (existingClassNames !== undefined ? existingClassNames + " " : "") +
      className
    );
  });
  return this;
};

/*
 * .removeClassSVG(className)
 * Removes the specified class to each of the set of matched SVG elements.
 */
$.fn.removeClassSVG = function (className) {
  $(this).attr("class", function (index, existingClassNames) {
    var re = new RegExp("\\b" + className + "\\b", "g");
    return existingClassNames.replace(re, "");
  });
  return this;
};
