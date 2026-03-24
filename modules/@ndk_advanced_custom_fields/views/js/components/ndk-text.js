function svg_textMultiline(element, text, width, fontSize, txtanchord, x) {
  var y = 1;
  var x = x;
  var new_line = 1;
  var last_line = 1;
  element = document.getElementById(element);
  if (element == null) return true;
  /* split the words into array */
  var words = text.split(" ");
  //var words = text.match(/.{32}/g);
  var line = "";

  /* Make a tspan for testing */
  element.innerHTML =
    '<tspan style="font-size:' +
    fontSize +
    'px;" ' +
    txtanchord +
    ' id="PROCESSING">busy</tspan >';
  mainBlock = $('.form-group[data-field="' + group + '"]');
  text_line_spacing = +parseFloat(mainBlock.data("text_line_spacing")) || 0;

  for (var n = 0; n < words.length; n++) {
    var testLine = line + words[n] + " ";
    //console.log(testLine)
    var testElem = document.getElementById("PROCESSING");
    /*  Add line in testElement */
    testElem.innerHTML = testLine;
    /* Messure textElement */
    var metrics = testElem.getBoundingClientRect();
    if (new_line > last_line) var testWidth = 0;
    else var testWidth = metrics.width;
    testWidth += 20;

    myY = fontSize * y;
    myY += text_line_spacing * y;

    last_line = y;
    if (words[n] == "±") {
      element.innerHTML +=
        '<tspan style="font-size:' +
        fontSize +
        'px;" ' +
        txtanchord +
        ' x="' +
        x +
        '" y="' +
        myY +
        '">' +
        line +
        "</tspan>";
      line = " ";
      y++;
      // } else if (testWidth > width && n > 0) {
      //   element.innerHTML +=
      //     '<tspan style="font-size:' +
      //     fontSize +
      //     'px;" ' +
      //     txtanchord +
      //     ' x="' +
      //     x +
      //     '" y="' +myY+
      //     '">' +
      //     line +
      //     "</tspan>";
      //   line = words[n] + " ";
      //   y++;
    } else {
      line = testLine;
    }

    new_line = y;
  }

  element.innerHTML +=
    '<tspan style="font-size:' +
    fontSize +
    'px;" ' +
    txtanchord +
    ' x="' +
    x +
    '" y="' +
    myY +
    '">' +
    line +
    "</tspan>";
  document.getElementById("PROCESSING").remove();
}

function setTextViewBox(group) {
  $("#visual_" + group + " .textareaSvg:visible").each(function () {
    svg = $(this);
    textNode = svg.find("text:eq(0)");
    width = textNode[0].getBBox().width;
    height = textNode[0].getBBox().height;
    height += parseInt(textNode.find("tspan").attr("y"));
    viewBox = "0 0 " + width + " " + height;
    svg[0].setAttribute("viewBox", viewBox);
    svg[0].setAttribute("height", "auto");
    svg[0].setAttribute("width", "auto");
  });
}

$(document).on("click", ".submitText", function () {
  group = $(this).parent().parent().find(".ndktextarea").attr("data-group");
  zindex = $(this).parent().parent().find(".textzone").attr("data-zindex");
  price = $(this).parent().parent().find(".ndktextarea").attr("data-price");
  ppcprice = $(this)
    .parent()
    .parent()
    .find(".ndktextarea")
    .attr("data-ppcprice");
  blend = $(this).parent().parent().find(".ndktextarea").attr("data-blend");

  view = $(this).parent().parent().find(".ndktextarea").attr("data-view");
  visual_effect = $(this)
    .parent()
    .parent()
    .find(".ndktextarea")
    .hasClass("visual-effect");
  dragdrop = $(this)
    .parent()
    .parent()
    .find(".ndktextarea")
    .attr("data-dragdrop");
  resizeable = $(this)
    .parent()
    .parent()
    .find(".ndktextarea")
    .attr("data-resizeable");
  rotateable = $(this)
    .parent()
    .parent()
    .find(".ndktextarea")
    .attr("data-rotateable");
  coloreffect = $(this)
    .parent()
    .parent()
    .find(".ndktextarea")
    .attr("data-blend");
  svgPath = "";
  svgPath = $(this).parent().parent().find(".ndktextarea").attr("data-path");
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
        } else {
          //$(this).css('height', 0);
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

  //$(this).parent().parent().find('.fontColorSelectUl li.active').trigger('click');
  //$(this).parent().parent().find('.ndktextarea').val(texte).trigger('keyup');
  input = $(this).parent().parent().find(".ndktextarea");
  color = $(this).parent().parent().find(".colorSelector").text();
  font = $(this).parent().parent().find(".fontSelector").text();
  applyTextAndFonts(texte, color, font, input);

  //texte = texte.replace('||', '');
  /*if(texte !='')
                $(this).parent().find('.fontSelectUl li.active').trigger('click');*/

  if (texte == "" || texte == " ") {
    price = 0;
    texte = "";
  }

  verticalPadding = 10;
  horizontalPadding = 0;
  $(this).parent().find(".status_counter").hide();
  $(this).parent().find(".noborder").css("position", "relative");
  height =
    $(this).parent().find(".textarea").innerHeight() -
    parseFloat(verticalPadding);
  width =
    $(this).parent().find(".textarea").innerWidth() -
    parseFloat(horizontalPadding) -
    scrollbarWidth;
  $(this).parent().find(".noborder").css("position", "");

  updatePriceNdk(price, group);
  $(".status_counter").hide();
  if (!visual_effect) return;
  if (
    $(".zone_limit[data-group='" + group + "']").length > 0 &&
    $(".view_tab[data-view='" + view + "']").length > 0 &&
    $(this).parent().find("textarea.noborder").length > 0
  ) {
    container = ".zone_limit[data-group='" + group + "']";
    zwidth = $(container).width();
    zheight = $(container).height();
    if (zwidth > 0 && zheight > 0)
      $(this).parent().find(".textarea").css({ width: zwidth });
    height = zheight;
    width = zwidth;
  }

  svglines = "";
  fontSize = parseFloat($(this).parent().attr("data-font-size"));
  alignment = $(this).parent().find(".texteditor").css("text-align");
  x = "0%";
  txtanchord = 'text-anchor="start"';
  startOffset = ' startOffset="50%"';

  if (alignment == "left") {
    x = "0%";
    txtanchord = 'text-anchor="start"';
    startOffset = ' startOffset="0%"';
  } else if (alignment == "center") {
    x = "50%";
    txtanchord = 'text-anchor="middle"';
    startOffset = ' startOffset="50%"';
  } else if (alignment == "right") {
    x = "100%";
    txtanchord = 'text-anchor="end"';
    startOffset = ' startOffset="100%"';
  }

  if (svgPath == 0) svgPath = "";

  mainBlock = $('.form-group[data-field="' + group + '"]');
  text_line_spacing = +parseFloat(mainBlock.data("text_line_spacing")) || 0;

  if ($(this).parent().find("textarea.noborder").length > 0) {
    var lines = $(this).parent().find("textarea.noborder").val().split("\n");
    y = 1;
    onlyText = "";

    for (var i = 0; i < lines.length; i++) {
      if (svgPath != "") {
        svglines +=
          '<textPath style="z-index:' +
          zindex +
          ';" ' +
          txtanchord +
          startOffset +
          ' xlink:href="#' +
          svgPath +
          '">' +
          lines[i] +
          "</textPath>";
      } else {
        myY = fontSize * y;
        myY += text_line_spacing * y;
        svglines +=
          "<tspan style='font-size:" +
          fontSize +
          "px;'" +
          txtanchord +
          ' x="' +
          x +
          '" y="' +
          myY +
          '">' +
          lines[i].escape() +
          "</tspan>";
      }
      y++;
    }
    textToWrite = $(this).parent().find("textarea.noborder").val();
  } else {
    textToWrite = "";
    y = 1;
    $(this)
      .parent()
      .find(".noborder")
      .each(function () {
        //textToWrite += $(this).val()+' '+'\n'+' ';
        if (svgPath != "") {
          textToWrite +=
            '<textPath style="z-index:' +
            zindex +
            ';" ' +
            txtanchord +
            startOffset +
            '  xlink:href="#' +
            svgPath +
            '">' +
            $(this).val() +
            "</textPath>";
          onlyText = $(this).val();
        } else {
          //textToWrite +='<tspan '+txtanchord+' x="'+x+'" y="'+fontSize*y+'">'+$(this).val();+'</tspan>';
          textToWrite += $(this).val() + "" + "\n" + "";
        }
        y++;
      });
  }

  style = $(this).parent().attr("style").replace('"', "'").replace('"', "'");

  fontSize = parseInt(
    $(this).parent().find(".texteditor").attr("data-font-size")
  );
  fontFamily = $(this).parent().find(".texteditor").css("font-family");
  fontFamily = fontFamily.replace('"', "'").replace('"', "'");
  var effect3d = false;

  var metalEffect = [];
  metalEffect["effect"] = "";
  metalEffect["fill"] = "";
  metalEffect["fillLight"] = "";
  metalEffect["fillShadow"] = "";

  //gold effect
  if ($(this).parent().find(".texteditor").css("color") == "rgb(255, 215, 0)") {
    effect3d = true;
    metalEffect = metaleffect(
      "#efd8a2",
      "#a28156",
      "#efd8a2",
      "#2f1f05",
      "#fff3c6",
      group,
      textToWrite,
      width,
      height,
      fontFamily,
      fontSize,
      effect3d,
      false
    );
  }

  //silver effect
  else if (
    $(this).parent().find(".texteditor").css("color") == "rgb(192, 192, 192)"
  ) {
    effect3d = true;
    metalEffect = metaleffect(
      "#888888",
      "#dedede",
      "#F5F5F5",
      "#444444",
      "#dedede",
      group,
      textToWrite,
      width,
      height,
      fontFamily,
      fontSize,
      effect3d,
      false
    );
  } else if (
    $(this).parent().find(".texteditor").attr("data-effect") == "concavMe"
  ) {
    color1 = $(this).parent().find(".texteditor").css("color");
    color2 = darkerColor(
      $(this).parent().find(".texteditor").css("color"),
      0.2
    );
    shadowcolor = darkerColor(
      $(this).parent().find(".texteditor").css("color"),
      0.4
    );
    lightcolor = lighterColor(
      $(this).parent().find(".texteditor").css("color"),
      0.2
    );
    strokecolor = lighterColor(
      $(this).parent().find(".texteditor").css("color"),
      0.2
    );
    effect3d = true;
    metalEffect = metaleffect(
      color2,
      color1,
      strokecolor,
      lightcolor,
      shadowcolor,
      group,
      textToWrite,
      width,
      height,
      fontFamily,
      fontSize,
      effect3d,
      false
    );
  } else if (
    $(this).parent().find(".texteditor").attr("data-effect") == "convexMe"
  ) {
    color1 = $(this).parent().find(".texteditor").css("color");
    color2 = lighterColor(
      $(this).parent().find(".texteditor").css("color"),
      0.2
    );
    shadowcolor = darkerColor(
      $(this).parent().find(".texteditor").css("color"),
      0.2
    );
    lightcolor = lighterColor(
      $(this).parent().find(".texteditor").css("color"),
      0.3
    );
    strokecolor = darkerColor(
      $(this).parent().find(".texteditor").css("color"),
      0.2
    );
    effect3d = true;
    metalEffect = metaleffect(
      color1,
      color2,
      strokecolor,
      shadowcolor,
      lightcolor,
      group,
      textToWrite,
      width,
      height,
      fontFamily,
      fontSize,
      effect3d,
      false
    );
  } else if ($(this).parent().find(".texteditor").attr("data-texture") != "") {
    color1 = $(this).parent().find(".texteditor").css("color");
    color2 = darkerColor(
      $(this).parent().find(".texteditor").css("color"),
      0.2
    );
    shadowcolor = darkerColor(
      $(this).parent().find(".texteditor").css("color"),
      0.4
    );
    lightcolor = lighterColor(
      $(this).parent().find(".texteditor").css("color"),
      0.2
    );
    strokecolor = lighterColor(
      $(this).parent().find(".texteditor").css("color"),
      0.2
    );
    effect3d = false;
    texture = $(this)
      .parent()
      .find(".texteditor")
      .attr("data-texture")
      .replace('url("', "")
      .replace('")', "");
    metalEffect = metaleffect(
      color2,
      color1,
      strokecolor,
      lightcolor,
      shadowcolor,
      group,
      textToWrite,
      width,
      height,
      fontFamily,
      fontSize,
      effect3d,
      texture
    );
  } else if (
    $(this).parent().find(".texteditor").attr("data-effect") == "outlineMe"
  ) {
    color1 = "#ffffff";
    strokecolor = $(this).parent().find(".texteditor").css("color");
    effect3d = false;
    metalEffect = metaleffect(
      color1,
      color1,
      strokecolor,
      "transparent",
      "transparent",
      group + "-" + number,
      textToWrite,
      width,
      height,
      fontFamily,
      fontSize,
      effect3d,
      false,
      true
    );
  } else {
    color1 = $(this).parent().find(".texteditor").css("color");
    strokecolor = $(this).parent().find(".texteditor").attr("stroke-color");
    effect3d = false;
    metalEffect = metaleffect(
      color1,
      color1,
      strokecolor,
      "transparent",
      "transparent",
      group,
      textToWrite,
      width,
      height,
      fontFamily,
      fontSize,
      effect3d,
      false,
      true
    );
  }

  if (
    svgPath != "" &&
    typeof svgPath != "undefined" &&
    svgPath != 0 &&
    $(this).parent().parent().find(".ndktextarea").hasClass("visual-effect")
  ) {
    $("#svgText_" + group).remove();
    $("#svgUse_" + group).remove();

    $(
      ".ndk-svg-view:visible > svg > [data-group-text='" + group + "']"
    ).remove();
    writeCurve = $(".ndk-svg-view:visible > svg ").append(
      metalEffect["textPathEffect"] +
        metalEffect["pattern"] +
        '<text data-font-family="' +
        fontFamily +
        '" data-group-text="' +
        group +
        '" id="svgText_' +
        group +
        '" style="font-family:' +
        fontFamily +
        " ;font-size:" +
        fontSize +
        "px;fill:" +
        metalEffect["fill"] +
        ";z-index:" +
        zindex +
        ';" >' +
        textToWrite +
        "</text>"
    );

    $.when(writeCurve).then(function () {
      setTimeout(function () {
        $("#ndk-svg-view-" + view + " > svg").html(
          $("#ndk-svg-view-" + view + " > svg").html()
        );
        $("#ndk-svg-view-" + view)
          .css("z-index", zindex)
          .css("mix-blend-mode", coloreffect);
      }, 500);
    });
  } else {
    if (
      $(this).parent().parent().find(".ndktextarea").hasClass("caracter-text")
    ) {
      composeCaracter(
        metalEffect["svg"],
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
        "text"
      );
    } else {
      designCompo(
        metalEffect["svg"],
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

  if (
    $(this).parent().find("textarea.noborder").length > 0 &&
    typeof textToWrite != "undefined" &&
    textToWrite != ""
  ) {
    svg_textMultiline(
      "svgText_" + group,
      textToWrite.replace(/\n/g, " ± "),
      width,
      fontSize,
      txtanchord,
      x
    );
    if (effect3d) {
      svg_textMultiline(
        "svgText_" + group + "-shadow",
        textToWrite.replace(/\n/g, " ± "),
        width,
        fontSize,
        txtanchord,
        x
      );
      svg_textMultiline(
        "svgText_" + group + "-light",
        textToWrite.replace(/\n/g, " ± "),
        width,
        fontSize,
        txtanchord,
        x
      );
    }
  }

  //$(this).parent().parent().parent().find('.fontColorSelectUl li.active').trigger('click');
  //$(this).parent().find('.noborder').css('height', '');
});
var list_color;
function getNdkCfColorName(color) {
  color_name = color;
  if (list_color) {
    if (color.indexOf("#") != -1) {
      if (list_color.hasOwnProperty(color)) {
        color_name = list_color[color];
      }
    }
  }
  return color_name;
}

function applyTextAndFonts(text, color, font, textarea) {
  if (typeof applyTextAndFonts_Override == "function") {
    return applyTextAndFonts_Override(text, color, font, textarea);
  }
  color = getNdkCfColorName(color);
  charsCount = texte.replace(/\ |\n|\r|(\n\r)/g, "").length;
  //sauvegarde typo
  if (charsCount > 0) {
    if (addFontTechnicalData) {
      textarea.val(texte + " [" + color + " - " + font + "]").trigger("keyup");
    } else {
      textarea.val(texte);
    }
  } else {
    textarea.val("");
  }
  $(".form-group[data-field='" + textarea.attr("data-group") + "']")
    .attr("data-text-color", color)
    .attr("data-text-font", font);
  textarea.trigger("ndkTextSet");
  setTimeout(function () {
    textarea.trigger({
      type: "ndkacf:ndkTextSet",
      group: textarea.attr("data-group"),
      color: color,
      font: font,
      text: text,
    });
  }, 500);
}

//on keyup, start the countdown
var typingTimer;
//var doneTypingInterval = 1000;
$(document).on("keyup", ".textarea", function () {
  clearTimeout(typingTimer);
  button = $(this).parent().parent().find(".submitText, .submitTextItem");
  typingTimer = setTimeout(function () {
    button.trigger("click");
  }, doneTypingInterval);
});

$(document).on("keyup", ".visual-text", function () {
  clearTimeout(typingTimer);
  button = $(this).parent().find(".submitSimpleText");
  typingTimer = setTimeout(function () {
    button.trigger("click");
  }, doneTypingInterval);
});

async function initText(el) {
  if (typeof initText_Override == "function") {
    return initText_Override(el);
  }
  var existing = el.parent().find(".texteditor");
  var myGroup = el.attr("data-group").split("_")[0];
  myGroup = el.attr("data-group").split("-")[0];
  var myNumber = el.attr("data-number");
  if (typeof myNumber == "undefined") myNumber = false;
  if (
    el.attr("data-pattern") != "" &&
    typeof el.attr("data-pattern") != "undefined"
  )
    pattern = el.attr("data-pattern");
  else pattern = "|";

  pattern = pattern.split("|");

  canvasDom =
    '<canvas id="myCanvas' +
    el.attr("data-group") +
    '" width="0" height="0"></canvas>';

  if (!existing.length || existing.length == 0) {
    if (el.attr("data-lines") > 0) {
      if (el.hasClass("type_textarea")) {
        zone_text = '<span class="textarea">';
        zone_text +=
          "<textarea " +
          (el.attr("data-max") > 0
            ? 'maxlength="' + el.attr("data-max") + '"'
            : "") +
          ' class="noborder countmychars" data-pattern="' +
          pattern[0] +
          '"></textarea>';
        zone_text +=
          "</span><style>.arcSelector{display:none!important;}</style>";
        el.after(
          '<div class="fontSelect " id="textZone' +
            el.attr("data-group") +
            (myNumber != false ? "-" + myNumber : "") +
            '"><span class="texteditor">' +
            zone_text +
            '</span><span class="submitText' +
            (myNumber != false ? "Item" : "") +
            '">' +
            applyText +
            '</span></div><div class="conter-container"></div>'
        );
      } else {
        zone_text = '<span class="textarea">';
        for (var i = 0; i < el.attr("data-lines"); i++) {
          zone_text +=
            '<input data-pattern="' +
            pattern[i] +
            '" type="text" ' +
            (el.attr("data-max") > 0
              ? 'maxlength="' + el.attr("data-max") + '"'
              : "") +
            ' size="15" value="" class="noborder" placeholder="Text line ' +
            (i + 1) +
            '"/>';
        }
        zone_text +=
          "</span><style>.arcSelector{display:none!important;}</style>";
        el.after(
          '<div class="fontSelect " id="textZone' +
            el.attr("data-group") +
            (myNumber != false ? "-" + myNumber : "") +
            '"><span class="texteditor">' +
            zone_text +
            '</span><span class="submitText' +
            (myNumber != false ? "Item" : "") +
            '">' +
            applyText +
            "</span></div>"
        );
      }
    } else {
      if (el.hasClass("type_textarea")) {
        width = "auto";
        height = "auto";
        if ($(".zone_limit[data-group='" + myGroup + "']").length > 0) {
          container = ".zone_limit[data-group='" + myGroup + "']";
          width = $(container).width();
          height = $(container).height();
        }

        zone_text = '<span class="textarea">';
        zone_text +=
          '<textarea style="width:' +
          width +
          "; height:" +
          height +
          ' " ' +
          (el.attr("data-max") > 0
            ? 'maxlength="' + el.attr("data-max") + '"'
            : "") +
          ' type="text" class="noborder"></textarea>';
        zone_text += '<div id="poptext_' + myGroup + '" class="poptext"></div>';
        zone_text +=
          "</span><style>.arcSelector{display:none!important;}</style>";
        el.after(
          '<div class="fontSelect " "textZone' +
            el.attr("data-group") +
            (myNumber != false ? "-" + myNumber : "") +
            '"><span class="texteditor">' +
            zone_text +
            '</span><span class="submitText' +
            (myNumber != false ? "Item" : "") +
            '">' +
            applyText +
            "</span></div>"
        );
      } else {
        zone_text = '<span class="textarea">';
        zone_text +=
          "<input " +
          (el.attr("data-max") > 0
            ? 'maxlength="' + el.attr("data-max") + '"'
            : "") +
          ' type="text" size="15" value="Votre texte " class="noborder"/>';
        zone_text +=
          "</span><style>.arcSelector{display:none!important;}</style>";
        el.after(
          '<div class="fontSelect " "textZone' +
            el.attr("data-group") +
            (myNumber != false ? "-" + myNumber : "") +
            '"><span class="texteditor">' +
            zone_text +
            '</span><span class="submitText' +
            (myNumber != false ? "Item" : "") +
            '">' +
            applyText +
            "</span></div>"
        );
      }
    }

    el.after(canvasDom);
    $(
      ".noborder:not(.ignoreMaxLength), .simpleText:not(.ignoreMaxLength), .noborderSimple:not(.ignoreMaxLength)"
    ).each(function () {
      maxChars = $(this).attr("maxlength");
      if (parseInt(maxChars) > 0)
        $(this).maxlength({ slider: true, maxCharacters: maxChars });

      textmask = $(this).attr("data-pattern");
      if (textmask != "" && typeof textmask != "undefined") {
        mask = textmask.split("&&");

        if (mask[0] != "" && typeof mask[0] != "undefined")
          $(this).mask(mask[0], { placeholder: mask[1] });
        else if (mask[1] != "" && typeof mask[1] != "undefined")
          $(this).attr("placeholder", mask[1]);
      }
    });
  }

  var myFonts = fonts;
  myColors = colors;
  mySizes = ["20", "30", "40", "50", "60", "70", "80", "90", "100"];
  myEffects = ["applatMe", "concavMe", "convexMe", "outlineMe"];
  myAlignments = ["left", "center", "right"];

  if (window["fieldFonts_" + myGroup].length > 0) {
    myFonts = window["fieldFonts_" + myGroup];
  }

  if (window["fieldColors_" + myGroup].length > 0)
    myColors = window["fieldColors_" + myGroup];

  //console.log(myColors);

  if (window["fieldSizes_" + myGroup].length > 0)
    mySizes = window["fieldSizes_" + myGroup];

  if (window["fieldEffects_" + myGroup].length > 0)
    myEffects = window["fieldEffects_" + myGroup];

  if (window["fieldAlignments_" + myGroup].length > 0)
    myAlignments = window["fieldAlignments_" + myGroup];

  allFonts.push(myFonts);
  color_url = getParameterByName("color").replace("@", "#");
  /*
                            if(myFonts.indexOf(color_url) < 0)
                                color_url = '';
                    */

  $(
    "#textZone" +
      el.attr("data-group") +
      (myNumber != false ? "-" + myNumber : "")
  ).fontSelector({
    hide_fallbacks: true,
    initial: myFonts[0],
    initialSize: $(window).width() < 768 ? mySizes[0] : mySizes[0],
    initialColor: color_url != "" ? color_url : myColors[0],
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

  $("#textZone" + el.attr("data-group")).css("font-size", mySizes[0] + "px");
  $(".fontSelectUl").each(function () {
    $(this).find("li").first().addClass("active");
  });
  $(".fontColorSelectUl").each(function () {
    $(this).find("li").first().addClass("active");
  });
  $(document).trigger({
    type: "ndkacf:ndkTextInitiated",
    group: el.attr("data-group"),
  });
}

$(document).on("ndkacf:ndkTextInitiated", function (e) {
  var me = $(".form-group[data-field=" + e.group + "]");
  var input;
  var autoFillText = async function (group) {
    var a = await resolveAfterTime(500);
    if (me.data("fill_placeholder") == 1 && editConfig == 0) {
      for (i = 0; i <= me.find(".noborder").length; i++) {
        input = me.find(".noborder")[i];
        if (typeof $(input).attr("placeholder") != "undefined") {
          $(input).val($(input).attr("placeholder"));
          console.log($(input).attr("placeholder"));
        }
      }
      return a;
    } else {
      return 0;
    }
  };

  autoFillText(e.group).then((v) => {
    if (v == 1) me.find(".submitText").trigger("click");
  });

  //force majuscule si necessary
  document
    .querySelectorAll("[data-pattern^='M'], [data-pattern^='W']")
    .forEach((item) => {
      item.addEventListener("keypress", forceKeyPressUppercase, false);
    });
});

function initTextLight(el) {
  if (typeof initTextLight_Override == "function") {
    return initTextLight_Override(el);
  }
  var existing = el.hasClass("initiated");
  var myGroup = el.attr("data-group");
  var myNumber = el.attr("data-number");
  if (typeof myNumber == "undefined") myNumber = false;

  if (
    el.attr("data-pattern") != "" &&
    typeof el.attr("data-pattern") != "undefined"
  )
    pattern = el.attr("data-pattern");
  else pattern = "";

  pattern = pattern.split("|");
  if (!existing) {
    if (el.attr("data-lines") > 0) {
      zone_text = '<div class="zone_text_inputs">';
      for (var i = 0; i < el.attr("data-lines"); i++) {
        zone_text +=
          '<input data-pattern="' +
          (typeof pattern[i] != "undefined" ? pattern[i] : "") +
          '" type="text" ' +
          (el.attr("data-max") > 0
            ? 'maxlength="' + el.attr("data-max") + '"'
            : "") +
          ' size="15" value="" class="' +
          (el.hasClass("required_field") ? "required_field_test" : "") +
          ' noborderSimple" placeholder="Text line ' +
          (i + 1) +
          '" data-message="' +
          el.attr("data-message") +
          '"/>';
      }
      zone_text += "</div>";
      el.after(zone_text).addClass("initiated").hide();
    }

    $(
      ".noborderSimple:not(.ignoreMaxLength), .simpleText:not(.ignoreMaxLength), .noborderSimple:not(.ignoreMaxLength)"
    ).each(function () {
      maxChars = $(this).attr("maxlength");
      if (parseInt(maxChars) > 0)
        $(this).maxlength({ slider: true, maxCharacters: maxChars });

      textmask = $(this).attr("data-pattern");
      if (textmask != "" && typeof textmask != "undefined") {
        mask = textmask.split("&&");

        if (mask[0] != "" && typeof mask[0] != "undefined")
          $(this).mask(mask[0], { placeholder: mask[1] });
        else if (mask[1] != "" && typeof mask[1] != "undefined")
          $(this).attr("placeholder", mask[1]);
      }
    });
  }
}

$(document).on(
  "change, keyup",
  "#ndkcsfields-block textarea.noborder",
  function () {
    $(this).html($(this).val());
  }
);

$(document).on("click", ".submitTextItem", function () {
  group = parseInt(
    $(this).parent().parent().find(".ndktextarea:eq(0)").attr("data-group")
  );
  mainBlock = $('.form-group[data-field="' + group + '"]');
  text_line_spacing = +parseFloat(mainBlock.data("text_line_spacing")) || 0;
  number = $(this).parent().parent().find(".ndktextarea").attr("data-number");
  zindex = $(this).parent().parent().find(".textzone").attr("data-zindex");
  price = $(this).parent().parent().find(".ndktextarea").attr("data-price");
  ppcprice = $(this)
    .parent()
    .parent()
    .find(".ndktextarea")
    .attr("data-ppcprice");
  blend = $(this).parent().parent().find(".ndktextarea").attr("data-blend");
  is_caracter = $(this)
    .parent()
    .parent()
    .find(".ndktextarea")
    .hasClass("caracter-text");

  view = $(this).parent().parent().find(".ndktextarea").attr("data-view");

  dragdrop = $(this)
    .parent()
    .parent()
    .find(".ndktextarea")
    .attr("data-dragdrop");
  resizeable = $(this)
    .parent()
    .parent()
    .find(".ndktextarea")
    .attr("data-resizeable");
  rotateable = $(this)
    .parent()
    .parent()
    .find(".ndktextarea")
    .attr("data-rotateable");
  charsCount = 0;

  svgPath = "";
  svgPath = $(this).parent().parent().find(".ndktextarea").attr("data-path");

  if ($(this).parent().find(".noborder").length > 0) {
    texte = "";
    $(this)
      .parent()
      .find(".noborder")
      .each(function () {
        if ($(this).val() != "") {
          texte += $(this).val() + " ";
          charsCount += $(this).val().replace(/\ /g, "").length;
        } else {
          //$(this).css('height', 0);
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

  $(this).parent().parent().find(".ndktextarea").val(texte).trigger("keyup");

  $(this)
    .parent()
    .find(".textarea")
    .css({ width: "auto", height: "auto", display: "table" });

  /*if(texte !='')
          $(this).parent().parent().find('.fontSelectUl li.active').trigger('click');*/

  if (texte == "" || texte == " ") {
    price = 0;
    texte = "";
  }

  verticalPadding = 10;
  horizontalPadding = 0;
  $(this).parent().find(".status_counter").hide();

  height =
    $(this).parent().find(".textarea").innerHeight() -
    parseFloat(verticalPadding);
  width =
    $(this).parent().find(".textarea").innerWidth() -
    parseFloat(horizontalPadding) -
    scrollbarWidth;
  //updatePriceNdk(price, group);
  $(".status_counter").hide();

  if (
    $(".zone_limit[data-group='" + group + "']").length > 0 &&
    $(".view_tab[data-view='" + view + "']").length > 0 &&
    $(this).parent().find("textarea.noborder").length > 0
  ) {
    container = ".zone_limit[data-group='" + group + "']";
    zwidth = $(container).width();
    zheight = $(container).height();
    $(this).parent().find(".textarea").css({ width: zwidth });
    width = zwidth;
  }

  svglines = "";
  fontSize = parseFloat($(this).parent().attr("data-font-size"));
  alignment = $(this).parent().find(".texteditor").css("text-align");
  x = "0%";
  txtanchord = 'text-anchor="start"';
  startOffset = ' startOffset="50%"';

  if (alignment == "left") {
    x = "0%";
    txtanchord = 'text-anchor="start"';
    startOffset = ' startOffset="0%"';
  } else if (alignment == "center") {
    x = "50%";
    txtanchord = 'text-anchor="middle"';
    startOffset = ' startOffset="50%"';
  } else if (alignment == "right") {
    x = "100%";
    txtanchord = 'text-anchor="end"';
    startOffset = ' startOffset="100%"';
  }

  if (svgPath == 0) svgPath = "";

  if ($(this).parent().find("textarea.noborder").length > 0) {
    var lines = $(this).parent().find("textarea.noborder").val().split("\n");
    y = 1;
    onlyText = "";
    for (var i = 0; i < lines.length; i++) {
      if (svgPath != "" && typeof svgPath != "undefined" && svgPath != 0) {
        svglines +=
          '<textPath style="z-index:' +
          zindex +
          ';" ' +
          txtanchord +
          startOffset +
          ' xlink:href="#' +
          svgPath +
          '">' +
          lines[i] +
          "</textPath>";
      } else {
        myY = fontSize * y;
        myY += text_line_spacing * y;
        svglines +=
          "<tspan style='font-size:" +
          fontSize +
          "px;'" +
          txtanchord +
          ' x="' +
          x +
          '" y="' +
          myY +
          '">' +
          lines[i].escape() +
          "</tspan>";
      }
      y++;
    }
    textToWrite = $(this).parent().find("textarea.noborder").val();
  } else {
    textToWrite = "";
    y = 1;
    $(this)
      .parent()
      .find(".noborder")
      .each(function () {
        //textToWrite += $(this).val()+' '+'\n'+' ';
        if (svgPath != "" && typeof svgPath != "undefined" && svgPath != 0) {
          textToWrite +=
            '<textPath style="z-index:' +
            zindex +
            ';" ' +
            txtanchord +
            startOffset +
            '  xlink:href="#' +
            svgPath +
            '">' +
            $(this).val() +
            "</textPath>";
          onlyText = $(this).val();
        } else {
          //textToWrite +='<tspan '+txtanchord+' x="'+x+'" y="'+fontSize*y+'">'+$(this).val();+'</tspan>';
          textToWrite += $(this).val() + "" + "\n" + "";
        }
        y++;
      });
  }
  style = "";
  if (typeof $(this).parent().attr("style") != "undefined") {
    style = $(this).parent().attr("style").replace('"', "'").replace('"', "'");
  }

  var effect3d = false;

  var metalEffect = [];
  metalEffect["effect"] = "";
  metalEffect["fill"] = "";
  metalEffect["fillLight"] = "";
  metalEffect["fillShadow"] = "";

  fontFamily = $(this).parent().find(".texteditor").css("font-family");
  fontFamily = fontFamily.replace('"', "'").replace('"', "'");
  //gold effect
  if ($(this).parent().find(".texteditor").css("color") == "rgb(255, 215, 0)") {
    effect3d = true;
    metalEffect = metaleffect(
      "#efd8a2",
      "#a28156",
      "#efd8a2",
      "#2f1f05",
      "#fff3c6",
      group + "-" + number,
      textToWrite,
      width,
      height,
      fontFamily,
      fontSize,
      effect3d,
      false
    );
  }

  //silver effect
  else if (
    $(this).parent().find(".texteditor").css("color") == "rgb(192, 192, 192)"
  ) {
    effect3d = true;
    metalEffect = metaleffect(
      "#888888",
      "#dedede",
      "#F5F5F5",
      "#444444",
      "#dedede",
      group + "-" + number,
      textToWrite,
      width,
      height,
      fontFamily,
      fontSize,
      effect3d,
      false
    );
  } else if (
    $(this).parent().find(".texteditor").attr("data-effect") == "concavMe"
  ) {
    color1 = $(this).parent().find(".texteditor").css("color");
    color2 = darkerColor(
      $(this).parent().find(".texteditor").css("color"),
      0.2
    );
    shadowcolor = darkerColor(
      $(this).parent().find(".texteditor").css("color"),
      0.4
    );
    lightcolor = lighterColor(
      $(this).parent().find(".texteditor").css("color"),
      0.2
    );
    strokecolor = darkerColor(
      $(this).parent().find(".texteditor").css("color"),
      0.2
    );
    effect3d = true;
    metalEffect = metaleffect(
      color2,
      color1,
      strokecolor,
      lightcolor,
      shadowcolor,
      group + "-" + number,
      textToWrite,
      width,
      height,
      fontFamily,
      fontSize,
      effect3d,
      false
    );
  } else if (
    $(this).parent().find(".texteditor").attr("data-effect") == "convexMe"
  ) {
    color1 = $(this).parent().find(".texteditor").css("color");
    color2 = lighterColor(
      $(this).parent().find(".texteditor").css("color"),
      0.2
    );
    shadowcolor = darkerColor(
      $(this).parent().find(".texteditor").css("color"),
      0.2
    );
    lightcolor = lighterColor(
      $(this).parent().find(".texteditor").css("color"),
      0.3
    );
    strokecolor = lighterColor(
      $(this).parent().find(".texteditor").css("color"),
      0.2
    );
    effect3d = true;
    metalEffect = metaleffect(
      color1,
      color2,
      strokecolor,
      shadowcolor,
      lightcolor,
      group + "-" + number,
      textToWrite,
      width,
      height,
      fontFamily,
      fontSize,
      effect3d,
      false
    );
  } else if (
    $(this).parent().find(".texteditor").attr("data-effect") == "outlineMe"
  ) {
    color1 = "#ffffff";
    strokecolor = $(this).parent().find(".texteditor").css("color");
    effect3d = false;
    metalEffect = metaleffect(
      color1,
      color1,
      strokecolor,
      "transparent",
      "transparent",
      group + "-" + number,
      textToWrite,
      width,
      height,
      fontFamily,
      fontSize,
      effect3d,
      false,
      true
    );
  } else {
    color1 = $(this).parent().find(".texteditor").css("color");
    strokecolor = $(this).parent().find(".texteditor").attr("stroke-color");
    effect3d = false;
    metalEffect = metaleffect(
      color1,
      color1,
      strokecolor,
      "transparent",
      "transparent",
      group + "-" + number,
      textToWrite,
      width,
      height,
      fontFamily,
      fontSize,
      effect3d,
      false,
      true
    );
  }

  if (
    svgPath != "" &&
    typeof svgPath != "undefined" &&
    svgPath != 0 &&
    $(this).parent().parent().find(".ndktextarea").hasClass("visual-effect")
  ) {
    $("#svgText_" + group + "-" + number).remove();
    $("#svgUse_" + group + "-" + number).remove();

    $(
      ".caracter-container > svg > [data-group-text='" +
        group +
        "-" +
        number +
        "']"
    ).remove();
    writeCurve = $(".caracter-container > svg ").append(
      metalEffect["textPathEffect"] +
        metalEffect["pattern"] +
        '<text dominant-baseline="middle" dy="0.1em"  data-font-family="' +
        fontFamily +
        '" data-group-text="' +
        group +
        "-" +
        number +
        '" id="svgText_' +
        group +
        "-" +
        number +
        '" style="font-family:' +
        fontFamily +
        " ;font-size:" +
        fontSize +
        "px;fill:" +
        metalEffect["fill"] +
        ";z-index:" +
        zindex +
        ';" >' +
        textToWrite +
        "</text>"
    );

    $.when(writeCurve).then(function () {
      setTimeout(function () {
        $("#caracter-container-" + group + "-" + number + " > svg").html(
          $("#caracter-container-" + group + "-" + number + " > svg").html()
        );
        $("#caracter-container-" + group + "-" + number)
          .css("z-index", zindex)
          .css("mix-blend-mode", coloreffect);
      }, 500);
    });
  } else {
    if (is_caracter) {
      composeCaracter(
        metalEffect["svg"],
        group + "-" + number,
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
        "text"
      );
    } else {
      //console.log(metalEffect);
      designCompo(
        metalEffect["svg"],
        group + "-" + number,
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

  if ($(this).parent().find("textarea.noborder").length > 0) {
    svg_textMultiline(
      "svgText_" + group + "-" + number,
      textToWrite.replace(/\n/g, " ± "),
      width,
      fontSize,
      txtanchord,
      x
    );
    svg_textMultiline(
      "svgText_" + group + "-" + number + "-shadow",
      textToWrite.replace(/\n/g, " ± "),
      width,
      fontSize,
      txtanchord,
      x
    );
    svg_textMultiline(
      "svgText_" + group + "-" + number + "-light",
      textToWrite.replace(/\n/g, " ± "),
      width,
      fontSize,
      txtanchord,
      x
    );
  }
  //console.log(parseInt(group));
  if (textToWrite != "") updatePriceNdk(price, parseInt(group));
  //$(this).parent().find('.noborder').css('height', '');
  textarea = $(this).parent().parent().find(".ndktextarea:eq(0)");
  setTimeout(function () {
    textarea.trigger({
      type: "ndkacf:ndkTextSet",
      group: textarea.attr("data-group") + "-" + number,
      color: color1,
      font: fontFamily,
      text: textToWrite,
    });
  }, 500);
});

function setTextSvgDimension(group, resizeable = 0) {
  if (typeof setTextSvgDimension_Override == "function") {
    return setTextSvgDimension_Override(group);
  }
  svg = $("#visual_" + group + " .textareaSvg:eq(0)")[0];
  if (typeof svg == "undefined") {
    return false;
  }
  // if ($(svg).parent().attr("data-resizeable") == 1) {
  //   resizeable = 1;
  // }
  svg.removeAttribute("viewBox");
  svg.removeAttribute("height");
  svg.removeAttribute("width");
  // svg.setAttribute("height", "100%");
  // svg.setAttribute("width", "100%");
  textAnchord = "";
  lineNumber = 0;
  padding = 0;
  var bbox = svg.getBBox();
  viewBox = 0 + " " + bbox.y + " " + bbox.width + " " + bbox.height;
  $(svg)
    .find("tspan")
    .each(function () {
      me = $(this);
      textAnchord = me.attr("text-anchor");
      lineNumber++;
    });
  $(svg).addClass("svg-align-" + textAnchord);
  $(svg).parent().attr("data-line-number", lineNumber);
  if (resizeable == 1) {
    $(svg)
      .parent()
      .css("height", bbox.height * 4)
      .css("width", bbox.width * 4);
  }
  //svg.setAttribute("width", bbox.width + padding);
  //svg.setAttribute("height", bbox.height + bbox.y + bbox.y);
  svg.setAttribute("viewBox", viewBox);
}

function setTextSvgDimension_test(group, resizeable = 0) {
  if (typeof setTextSvgDimension_Override == "function") {
    return setTextSvgDimension_Override(group);
  }

  var svg = document.querySelector("#visual_" + group + " .textareaSvg");
  if (!Boolean(svg)) return false;

  if (typeof svg == "undefined") {
    return false;
  }

  textAnchord = "";
  lineNumber = 0;
  padding = 0;
  var bbox = svg.getBBox();
  viewBox = 0 + " " + bbox.y + " " + bbox.width + " " + bbox.height;
  $(svg)
    .find("tspan")
    .each(function () {
      me = $(this);
      textAnchord = me.attr("text-anchor");
      lineNumber++;
    });
  $(svg).addClass("svg-align-" + textAnchord);
  $(svg).parent().attr("data-line-number", lineNumber);
  if (resizeable == 1) {
    // $(svg)
    //   .parent()
    //   .css("height", bbox.height * 4)
    //   .css("width", bbox.width * 4);
  }
  perfectFeetSvg(group, resizeable);
}

function perfectFeetSvg(group, resizeable = 0) {
  clearTimeout(typingTimer);

  var svg = document.querySelector("#visual_" + group + " .textareaSvg");
  currentviewBox = svg.getAttribute("viewBox");
  parentBox = svg.parentNode.getBoundingClientRect();
  if (!Boolean(svg)) return false;
  if (svg.classList.contains("perfectFeeting")) return false;
  svg.classList.remove("perfectFeeting");
  margin = 0;
  if (svg.classList.contains("curvable-svg")) margin = 1.5;
  var { xMin, xMax, yMin, yMax } = [...svg.children].reduce((acc, el) => {
    var { x, y, width, height } = el.getBBox();
    if (!acc.xMin || x < acc.xMin) acc.xMin = x;
    if (!acc.xMax || x + width > acc.xMax) acc.xMax = x + width * margin;
    if (!acc.yMin || y < acc.yMin) acc.yMin = y;
    if (!acc.yMax || y + height > acc.yMax) acc.yMax = y + height * margin;
    return acc;
  }, {});
  if (svg.classList.contains("curvable-svg")) {
    var viewbox = `0 0 ${parentBox.right - parentBox.left} ${
      parentBox.bottom - parentBox.top
    }`;
    //var viewbox = currentviewBox;
  } else var viewbox = `${xMin} ${yMin} ${xMax - xMin} ${yMax - yMin}`;
  svg.removeAttribute("width");
  svg.removeAttribute("height");
  svg.setAttribute("viewBox", viewbox);
  svg.innerHTML += "";

  textBox = svg.getBoundingClientRect();
  ratio = parentBox.width / textBox.height;
  //console.log(ratio);

  if (
    typeof ndkMovables[group] != "undefined" &&
    !svg.classList.contains("curvable-svg")
  ) {
    console.log("perfect_resize");

    setTimeout(function () {
      svg.parentNode.style.height = `${textBox.height}px`;
      svg.parentNode.style.width = `${textBox.width}px`;
      ndkMovables[group].updateTarget();
    });
  }
  typingTimer = setTimeout(function () {
    svg.classList.remove("perfectFeeting");
  }, doneTypingInterval);
}

$(document).on("ndkacf:ndkCompoDesigned", function (e) {
  setTimeout(function () {
    setSvgLineHeight(
      e.group,
      $(".form-group[data-field='" + e.group + "']").attr("data-line_spacing")
    );
    setTextSvgDimension(e.group);
    setResizeRange(e.group);
  }, 500);
});

function metaleffect(
  startColor,
  stopColor,
  strokeColor,
  shadowColor,
  lightColor,
  group,
  textToWrite,
  width,
  height,
  fontFamily,
  fontSize,
  effect3d,
  textureEffect,
  applat
) {
  if (typeof metaleffect_Override == "function") {
    return metaleffect_Override(
      startColor,
      stopColor,
      strokeColor,
      shadowColor,
      lightColor,
      group,
      textToWrite,
      width,
      height,
      fontFamily,
      fontSize,
      effect3d,
      textureEffect,
      applat
    );
  }
  applat = applat || false;
  var metalEffect = [];
  //console.log(width)
  //console.log(height)

  if (!applat) {
    metalEffect["effect"] =
      '<linearGradient class="svggradient" data-group-text="' +
      group +
      '"  id="metalEffect_' +
      group +
      '" x1="0%" y1="100%" y2="0%" x2="80%">';
    metalEffect["effect"] +=
      '<stop stop-color="' + startColor + '" offset="0%"></stop>';
    metalEffect["effect"] +=
      '<stop stop-color="' + stopColor + '" offset="30%"></stop>';
    metalEffect["effect"] +=
      '<stop stop-color="' + startColor + '" offset="70%"></stop>';
    metalEffect["effect"] +=
      '<stop stop-color="' + stopColor + '" offset="100%"></stop>';
    metalEffect["effect"] += "</linearGradient>";

    metalEffect["effect"] +=
      '<filter class="svgfilter" data-group-text="' +
      group +
      '" id="shadow_' +
      group +
      '" height="140%" y="20%" width="140%" x="-30%"><feGaussianBlur result="shadow" stdDeviation="0 0"></feGaussianBlur><feGaussianBlur result="shadow" stdDeviation="1 0.5"></feGaussianBlur><feOffset dx="0" dy="2"></feOffset></filter>';

    metalEffect["effect"] +=
      '<filter class="svgfilter" data-group-text="' +
      group +
      '"  id="shadow2_' +
      group +
      '" height="140%" y="20%" width="140%" x="-30%"><feGaussianBlur result="shadow" stdDeviation="0.2 0.2"></feGaussianBlur><feOffset dx="0" dy="-0.7"></feOffset></filter>';

    metalEffect["effect"] +=
      '<filter class="svgfilter" data-group-text="' +
      group +
      '" id="light_' +
      group +
      '" height="140%" y="20%" width="140%" x="-30%"><feGaussianBlur result="shadow" stdDeviation="0 0"></feGaussianBlur><feGaussianBlur result="shadow" stdDeviation="0 0"></feGaussianBlur><feOffset dy="-1" dx="0"></feOffset></filter>';

    metalEffect["fill"] =
      (textureEffect
        ? "url(#texture_" + group + ")"
        : "url(#metalEffect_" + group + ")") +
      " ;stroke: " +
      (textureEffect ? "url(#texture_" + group + ")" : strokeColor) +
      "; " +
      (textureEffect ? "stroke-width:1" : "stroke-width:0.8");
    metalEffect["fillShadow"] =
      shadowColor + "; filter: url(#shadow_" + group + ")";
    metalEffect["fillLight"] =
      lightColor + "; filter: url(#light_" + group + ")";
  } else {
    metalEffect["effect"] = "";
    metalEffect["fill"] =
      startColor + " ;stroke: " + strokeColor + "; stroke-width:0.8";
    metalEffect["fillShadow"] = "";
    metalEffect["fillLight"] = "";
  }

  textPathEffect = "";
  svg =
    '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" class="textareaSvg composition_element composition_element-text" viewBox="0 0 ' +
    width +
    " " +
    (height + 5) +
    '" preserveAspectRatio="xMidYMid meet" style="' +
    style +
    '" height="' +
    height +
    '" width="' +
    width +
    '">';

  svg += metalEffect["effect"];

  fontUrl = fontFamily.replace(/\s/g, "+");
  fontUrl = fontUrl.replace(/\"/g, "");
  fontUrl = fontUrl.replace(/\'/g, "");
  fullFontUrl = "https://fonts.googleapis.com/css?family=" + fontUrl;
  styleContent = "@import url('" + fullFontUrl + "')";
  svg += '<defs><style type="text/css" >' + styleContent + "</style></defs>";
  textPathEffect += metalEffect["effect"];
  mainBlock = $('.form-group[data-field="' + group + '"]');
  text_line_spacing = +parseFloat(mainBlock.data("text_line_spacing")) || 0;
  var lines = textToWrite.split("\n");
  if (lines.length > 1) {
    y = 1;
    for (var i = 0; i < lines.length; i++) {
      if (lines[i].escape().replace(/\s/g, "") != "") {
        myX = x;
        if (txtanchord == 'text-anchor="start"' && y > 1) myX = 0;
        myY = fontSize * y;
        myY += text_line_spacing * y;

        svglines +=
          "<tspan style='font-size:" +
          fontSize +
          "px;'" +
          txtanchord +
          ' x="' +
          myX +
          '" y="' +
          myY +
          '">' +
          lines[i].escape() +
          "</tspan>";
        y++;
      }
    }
  }

  if (effect3d == true && !applat) {
    svg +=
      '<text dominant-baseline="middle" dy="0.1em"  data-font-family="' +
      fontFamily +
      '" id="svgText_' +
      group +
      '-light" x="0%" y="10" style="fill:' +
      metalEffect["fillLight"] +
      ';"font-family:' +
      fontFamily +
      " ;>" +
      svglines +
      "</text>";
    svg +=
      '<text dominant-baseline="middle" dy="0.1em"  data-font-family="' +
      fontFamily +
      '" id="svgText_' +
      group +
      '-shadow" x="0%" y="10" style="fill:' +
      metalEffect["fillShadow"] +
      ";font-family:" +
      fontFamily +
      ' ;">' +
      svglines +
      "</text>";

    textPathEffect +=
      '<text dominant-baseline="middle" dy="0.1em"  data-font-family="' +
      fontFamily +
      '" data-group-text="' +
      group +
      '" id="svgText_' +
      group +
      "-" +
      number +
      '-light" style="font-family:' +
      fontFamily +
      " ;font-size:" +
      fontSize * 1 +
      "px; fill:" +
      metalEffect["fillLight"] +
      ';">' +
      svglines +
      "</text>";

    textPathEffect +=
      '<text dominant-baseline="middle" dy="0.1em"  data-font-family="' +
      fontFamily +
      '" data-group-text="' +
      group +
      '" id="svgText_' +
      group +
      "-" +
      number +
      '-shadow" style="font-family:' +
      fontFamily +
      " ;font-size:" +
      fontSize * 1 +
      "px; fill:" +
      metalEffect["fillShadow"] +
      ';">' +
      svglines +
      "</text>";
  }

  svgPattern = "";
  metalEffect["pattern"] = "";
  if (textureEffect) {
    svgPattern +=
      '<pattern id="texture_' +
      group +
      '" patternUnits="userSpaceOnUse" ' +
      'height="1000" width="1000">';
    svgPattern +=
      '<image xlink:href="' + textureEffect + '" height="1000" width="1000"/>';
    svgPattern += "</pattern>";
    svg += svgPattern;
    metalEffect["pattern"] = svgPattern;
  }

  svg +=
    '<text dominant-baseline="middle" dy="0.1em"  data-font-family="' +
    fontFamily +
    '" id="svgText_' +
    group +
    '"  x="0%" y="10" style="fill:' +
    metalEffect["fill"] +
    ";font-family:" +
    fontFamily +
    ';">';

  svg += svglines;
  svg += "</text>";
  svg += "</svg>";

  metalEffect["textPathEffect"] = textPathEffect;
  metalEffect["svg"] = svg;

  return metalEffect;
}

function forceKeyPressUppercase(e) {
  var charInput = e.keyCode;
  if (charInput >= 97 && charInput <= 122) {
    // lowercase
    if (!e.ctrlKey && !e.metaKey && !e.altKey) {
      // no modifier key
      var newChar = charInput - 32;
      var start = e.target.selectionStart;
      var end = e.target.selectionEnd;
      e.target.value =
        e.target.value.substring(0, start) +
        String.fromCharCode(newChar) +
        e.target.value.substring(end);
      e.target.setSelectionRange(start + 1, start + 1);
      e.preventDefault();
    }
  }
}

// Ratio is between 0 and 1
var changeColor = function (color, ratio, darker) {
  // Trim trailing/leading whitespace
  color = color.replace(/^\s*|\s*$/, "");

  // Expand three-digit hex
  color = color.replace(/^#?([a-f0-9])([a-f0-9])([a-f0-9])$/i, "#$1$1$2$2$3$3");

  // Calculate ratio
  var difference = Math.round(ratio * 256) * (darker ? -1 : 1),
    // Determine if input is RGB(A)
    rgb = color.match(
      new RegExp(
        "^rgba?\\(\\s*" +
          "(\\d|[1-9]\\d|1\\d{2}|2[0-4][0-9]|25[0-5])" +
          "\\s*,\\s*" +
          "(\\d|[1-9]\\d|1\\d{2}|2[0-4][0-9]|25[0-5])" +
          "\\s*,\\s*" +
          "(\\d|[1-9]\\d|1\\d{2}|2[0-4][0-9]|25[0-5])" +
          "(?:\\s*,\\s*" +
          "(0|1|0?\\.\\d+))?" +
          "\\s*\\)$",
        "i"
      )
    ),
    alpha = !!rgb && rgb[4] != null ? rgb[4] : null,
    // Convert hex to decimal
    decimal = !!rgb
      ? [rgb[1], rgb[2], rgb[3]]
      : color
          .replace(
            /^#?([a-f0-9][a-f0-9])([a-f0-9][a-f0-9])([a-f0-9][a-f0-9])/i,
            function () {
              return (
                parseInt(arguments[1], 16) +
                "," +
                parseInt(arguments[2], 16) +
                "," +
                parseInt(arguments[3], 16)
              );
            }
          )
          .split(/,/),
    returnValue;

  // Return RGB(A)
  return !!rgb
    ? "rgb" +
        (alpha !== null ? "a" : "") +
        "(" +
        Math[darker ? "max" : "min"](
          parseInt(decimal[0], 10) + difference,
          darker ? 0 : 255
        ) +
        ", " +
        Math[darker ? "max" : "min"](
          parseInt(decimal[1], 10) + difference,
          darker ? 0 : 255
        ) +
        ", " +
        Math[darker ? "max" : "min"](
          parseInt(decimal[2], 10) + difference,
          darker ? 0 : 255
        ) +
        (alpha !== null ? ", " + alpha : "") +
        ")"
    : // Return hex
      [
        "#",
        pad(
          Math[darker ? "max" : "min"](
            parseInt(decimal[0], 10) + difference,
            darker ? 0 : 255
          ).toString(16),
          2
        ),
        pad(
          Math[darker ? "max" : "min"](
            parseInt(decimal[1], 10) + difference,
            darker ? 0 : 255
          ).toString(16),
          2
        ),
        pad(
          Math[darker ? "max" : "min"](
            parseInt(decimal[2], 10) + difference,
            darker ? 0 : 255
          ).toString(16),
          2
        ),
      ].join("");
};
var lighterColor = function (color, ratio) {
  return changeColor(color, ratio, false);
};
var darkerColor = function (color, ratio) {
  return changeColor(color, ratio, true);
};

// function curveText(group, radius)
// {
//   svg = $('#visual_'+group).find('svg:eq(0)');
//   width = svg[0].getBoundingClientRect().width;
//   height = svg[0].getBoundingClientRect().height;
//
//
//   console.log(width, height, radius);
//   $('#curvedTextPath_'+group).remove();
//   svg.find('defs').append('<path id="curvedTextPath_'+group+'" d="'+getPathData(width, height, radius)+'"></path>')
//   svg.append('<circle cx="'+width/2+'" cy="'+height/2+'" r="'+radius+'" id="mainCircle_'+group+'"></circle>')
//   svg.find('tspan').wrapAll('<textPath startOffset="50%" xlink:href="#curvedTextPath_'+group+'"></textPath>');
//
// }
/*

function getPathData(width, height, radius) {
  // adjust the radius a little so our text's baseline isn't sitting directly on the circle
  var r = radius * 0.95;
  var startX = width/2 - r;
  return 'm' + startX + ',' + (height/2) + ' ' +'a' + r + ',' + r + ' 0 0 0 ' + (2*r) + ',0';
}*/

//utile pour connecter des couleurs de texte
var ndkTextConfigs = false;
ndkTextConfigs = [];

$(document).on(
  "ndkacf:ndkTextSet",
  "[data-custom_class^='sameColor'] .ndktextarea",
  async function (e) {
    ndkTextConfigs[e.group] = {
      color: e.color,
      font: e.font,
    };
    me = $(this);
    rootGroupBlock = $(".form-group[data-field='" + e.group + "']");
    selector =
      "[data-custom_class='" +
      rootGroupBlock.attr("data-custom_class") +
      "']:not(.slaved)";
    var groupsTarget = $(selector);
    currentIndex = groupsTarget.index(rootGroupBlock);
    var nextObj = false;
    if (typeof groupsTarget[currentIndex + 1] != "undefined") {
      nextObj = $(groupsTarget[currentIndex + 1]);
    } else {
      if (currentIndex != 0) nextObj = $(groupsTarget[0]);
    }
    if (nextObj) {
      await linkTextColor(e.color, nextObj);
    }
  }
);

$(document).on("ndkacf:ndkColorSet", async function (e) {
  rootGroupBlock = $(
    '.form-group[data-field="' + e.group + '"][data-custom_class^=linkColor]'
  );
  if (rootGroupBlock.length > 0) {
    selector =
      ".field-type-0[data-custom_class='" +
      rootGroupBlock.attr("data-custom_class") +
      "'], .field-type-2[data-custom_class='" +
      rootGroupBlock.attr("data-custom_class") +
      "'], .field-type-0[data-custom_class='" +
      rootGroupBlock
        .attr("data-custom_class")
        .replace("linkColor", "sameColor") +
      "']";
    var groupsTarget = $(selector);
    currentIndex = groupsTarget.index(rootGroupBlock);
    myTargets = [];
    groupsTarget.each(async function () {
      nextObj = $(this);
      nextObjIndex = groupsTarget.index(nextObj);
      if (nextObjIndex != currentIndex) {
        myTargets.push(nextObj);
      }
    });

    for (i = 0; i <= myTargets.length; i++) {
      if (typeof myTargets[i] != "undefined")
        await linkTextColor(e.color, myTargets[i], true);
    }
  }
});

$(document).on("ndkacf:ndkTextSet", ".ndktextarea", function (e) {
  $("#visual_" + e.group)
    .attr("data-font", e.font)
    .attr("data-color", e.color);
});

async function linkTextColor(color, groupTarget, disable_other = false) {
  return delay(800).then(async function () {
    if (groupTarget.hasClass("field-type-2")) {
      currentSelector = ".index-value:eq(0)";
      listSelector = ".colorize_svg li";
      firstSelector = ".colorize_svg li:eq(0) span";
    } else {
      currentSelector = ".colorSelector:eq(0)";
      listSelector = ".fontColorSelectUl li";
      firstSelector = ".fontColorSelectUl li:eq(0)";
    }
    group = groupTarget.attr("data-field");
    if (typeof ndkTextConfigs[group] != "undefined") {
      groupTargetColor = ndkTextConfigs[group].color;
    } else {
      groupTargetColor = groupTarget.find(currentSelector).text();
    }
    if (groupTargetColor != color) {
      if (typeof ndkTextConfigs[group] == "undefined") {
        ndkTextConfigs[group] = {
          color: color,
        };
      } else {
        ndkTextConfigs[group].color = color;
      }
      targetColor = groupTarget
        .find(listSelector)
        .filter(function () {
          return $(this).text() === color;
        })
        .first();

      if (disable_other) {
        groupTarget.addClass("slaved");
        targetColor = groupTarget.find(firstSelector);
        targetColor.css("background", color).text(color);
        groupTarget
          .find(listSelector)
          .not(targetColor)
          .attr("disabled", "disabled")
          .hide();
      }
      targetColor.removeAttr("disabled").show();
      groupTarget.find(currentSelector).trigger("click");
      await delay(300).then(function () {
        targetColor.trigger("click");
      });
    }
  });
}
