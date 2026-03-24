/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
 */

function getOpositeColor(rgb) {
  // Like this : rgb(0, 0, 0);
  while (rgb.indexOf(" ") != -1) rgb = rgb.replace(" ", "");
  //Check if is formatted as RGB
  if ((x = /([0-9]{0,3}),([0-9]{0,3}),([0-9]{0,3})/.exec(rgb))) {
    //Extract colors
    color = {
      r: parseInt(x[1]),
      g: parseInt(x[2]),
      b: parseInt(x[3]),
    };
    //If is this operation be <= 128 return white, others else return black
    OpositeColor =
      0.3 * color["r"] + 0.59 * color["g"] + 0.11 * color["b"] <= 128
        ? "#FFF"
        : "#333";
    return OpositeColor;
  }
  return -1;
}

(function ($) {
  var settings;

  var refreshing = false;
  var methods = {
    init: function (options) {
      settings = $.extend(
        {
          hide_fallbacks: false,
          selected: function (style) {},
          selectedSize: function (size) {},
          selectedColor: function (color) {},
          initial: "",
          initialSize: "",
          initialColor: "",
          initialEffect: "",
          initialAlignment: "",
          fonts: [],
          sizes: [],
          colors: [],
          effects: [],
          alignments: [],
          showStroke: false,
        },
        options
      );

      var root = this;
      root.callback = settings["selected"];
      var visible = false;
      var selected = false;
      var selectedSize = false;
      var selectedColor = false;

      var displayName = function (font) {
        return font;
      };

      var select = function (font) {
        root
          .find("span.fontSelector")
          .html(displayName(font).replace(/["']{1}/gi, ""));
        root.css("font-family", font).attr("data-font-family", font);
        selected = font;
        root.trigger("ndkTextSet");
        root.callback(selected);
      };

      var selectSize = function (size) {
        root.find("span.sizeSelector").html(size);
        root.css("font-size", size);
        root.attr("data-font-size", size);
        root.find(".texteditor").attr("data-font-size", size);
        selectedSize = size;
        root.trigger("ndkTextSet");
        root.callback(selectedSize);
      };

      var selectColor = function (color) {
        root.find("span.colorSelector").html(color);
        root.find(".texteditor, .noborder").attr("data-texture", "");
        root.find("span.colorSelector").css("background", color).html(color);
        //this.css('color', color);
        if (color.indexOf("http") >= 0) {
          root.find(".texteditor, .noborder").attr("data-texture", color);
          root.find(".texteditor").css("color", settings["initialColor"]);
        } else root.find(".texteditor").css("color", color);

        selectedColor = color;
        if (typeof root.find(".texteditor").css("color") != "undefined")
          root
            .find(".textarea")
            .css(
              "background",
              getOpositeColor(root.find(".texteditor").css("color"))
            );
        root.trigger("ndkTextSet");
        root.callback(selectedColor);
      };

      var selectStrokeColor = function (color) {
        root.find("span.strokeSelector").css("border-color", color).html(color);
        //this.css('color', color);
        if (typeof root.find(".texteditor").css("color") != "undefined")
          root.find(".texteditor").attr("stroke-color", color);
        root
          .find(".noborder")
          .css(
            "text-shadow",
            color +
              "1px 1px, " +
              color +
              " -1px 1px, " +
              color +
              " -1px -1px, " +
              color +
              " 1px -1px"
          );
        selectedStrokeColor = color;
        root
          .find("span.strokeSelector")
          .css("border-color", selectedStrokeColor);

        root.trigger("ndkTextSet");
        root.callback(selectedStrokeColor);
      };

      var positionUl = function () {
        var left, top;
        left = $(root).offset().left;
        top = $(root).offset().top + 32;
        width = $(root).width();

        $(ul).css({
          position: "absolute",
          left: left + "px",
          top: top + "px",
        });
      };

      var positionUlS = function () {
        var left, top;
        left = $(root).offset().left;
        top = $(root).offset().top + 32;
        width = $(root).width();
        $(ulS).css({
          position: "absolute",
          left: left + "px",
          top: top + "px",
        });
      };

      var positionUlC = function () {
        var left, top;
        left = $(root).offset().left;
        top = $(root).offset().top + 32;
        width = $(root).width();
        $(ulC).css({
          position: "absolute",
          left: left + "px",
          top: top + "px",
          width: width,
        });
      };
      var positionUlSC = function () {
        var left, top;
        left = $(root).offset().left;
        top = $(root).offset().top + 32;
        width = $(root).width();
        $(ulSC).css({
          position: "absolute",
          left: left + "px",
          top: top + "px",
          width: width,
        });
      };

      // Setup markup
      //$(this).find('.arcSelector, .alignSelector, .fontSelector, .sizeSelector, .colorSelector, .fontSelectUl, .fontSizeSelectUl, .fontColorSelectUl').remove();

      if ($(this).find(".arcSelector").length == 0)
        $(this).prepend(
          '<span class="arcSelector"><i data-align="center" class="icon icon-circle-o-notch"><br><input class="arcText" type="range" min="-700" value="0" max="700" step="10"/></span>'
        );

      if ($(this).find(".alignSelector").length == 0) {
        alignHtml = '<span class="alignSelector">';

        //console.log(settings['alignments']);
        for (var i = 0; i < settings["alignments"].length; i++) {
          //if( $(this).find('.'+settings['alignments'][i]+'Selector').length == 0 )
          alignHtml +=
            '<i data-align="' +
            settings["alignments"][i] +
            '" class="icon-align icon icon-align-' +
            settings["alignments"][i] +
            " " +
            (settings["initialAlignment"] == settings["alignments"][i]
              ? "active"
              : "") +
            '"></i>';
        }

        alignHtml += "</span>";
        $(this).prepend(alignHtml);
      }

      for (var i = 0; i < settings["effects"].length; i++) {
        if ($(this).find("." + settings["effects"][i] + "Selector").length == 0)
          $(this).prepend(
            '<span class="' +
              settings["effects"][i] +
              "Selector effectButton " +
              (settings["initialEffect"] == settings["effects"][i]
                ? "active"
                : "") +
              '" data-effect="' +
              settings["effects"][i] +
              '"></span>'
          );
      }

      if ($(this).find(".toolSeparate").length == 0)
        $(this).prepend(' <p class="clear clearfix toolSeparate"></p>');

      if ($(this).find(".fontSelector").length == 0)
        $(this).prepend(
          '<span class="fontSelector">' +
            settings["initial"].replace(/'/g, "&#039;") +
            "</span>"
        );

      if ($(this).find(".sizeSelector").length == 0)
        $(this).prepend(
          '<span class="sizeSelector">' + settings["initialSize"] + "</span>"
        );

      if ($(this).find(".strokeSelector").length == 0 && settings["showStroke"])
        $(this).prepend(
          '<span class="strokeSelector">' + settings["initialColor"] + "</span>"
        );

      if ($(this).find(".colorSelector").length == 0)
        $(this).prepend(
          '<span class="colorSelector">' + settings["initialColor"] + "</span>"
        );

      if ($(this).find(".fontSelectUl").length == 0) {
        var ul = $('<ul class="fontSelectUl"></ul>').appendTo($(this));
        for (var i = 0; i < settings["fonts"].length; i++) {
          var item = $(
            "<li style=\"font-family:'" +
              settings["fonts"][i] +
              "'\">" +
              displayName(settings["fonts"][i]) +
              "</li>"
          ).appendTo(ul);
          //$(item).css('font-family', settings['fonts'][i]);
        }
      } else {
        var ul = $(this).find(".fontSelectUl");
        refreshing = true;
        //ul.find('.active').trigger('click');
      }

      if ($(this).find(".fontSizeSelectUl").length == 0) {
        var ulS = $('<ul class="fontSizeSelectUl"></ul>').appendTo($(this));
        for (var i = 0; i < settings["sizes"].length; i++) {
          mySize = settings["sizes"][i];
          // if($(window).width() < 768)
          // 	mySize = settings['sizes'][i]/2;

          var item = $("<li>" + mySize + "</li>").appendTo(ulS);
          $(item).css("font-size", mySize + "px");
          $(item).attr("data-font-size", mySize);
        }
      } else {
        var ulS = $(this).find(".fontSizeSelectUl");
        refreshing = true;
        //checkFontMobile(ulS);
      }

      if ($(this).find(".fontColorSelectUl").length == 0) {
        var ulC = $('<ul class="fontColorSelectUl"></ul>').appendTo($(this));
        for (var i = 0; i < settings["colors"].length; i++) {
          var item = $(
            `<li data-toggle="tooltip" title="${getNdkCfColorName(
              settings["colors"][i].replace(" ", "")
            )}">${settings["colors"][i].replace(" ", "")}</li>`
          ).appendTo(ulC);
          $(item).css("background", settings["colors"][i]);
        }
      } else {
        var ulC = $(this).find(".fontColorSelectUl");
        refreshing = true;
        //ulC.find('.active').trigger('click');
      }

      if ($(this).find(".strokeColorSelectUl").length == 0) {
        var ulSC = $('<ul class="strokeColorSelectUl"></ul>').appendTo($(this));
        for (var i = 0; i < settings["colors"].length; i++) {
          var item = $(
            `<li data-toggle="tooltip" title="${getNdkCfColorName(
              settings["colors"][i].replace(" ", "")
            )}">${settings["colors"][i].replace(" ", "")}</li>`
          ).appendTo(ulSC);
          $(item).css("background", settings["colors"][i]);
        }
      } else {
        var ulSC = $(this).find(".strokeColorSelectUl");
        refreshing = true;
        //ulC.find('.active').trigger('click');
      }
      //root.find('.alignSelector > i.active').trigger('click');
      ul.hide();
      ulS.hide();
      ulC.hide();
      ulSC.hide();

      if (settings["fonts"].length < 2) $(this).find(".fontSelector").hide();

      if (settings["colors"].length < 2) {
        $(this).find(".colorSelector").hide();
        $(this).find(".strokeSelector").hide();
      }

      if (settings["sizes"].length < 2) $(this).find(".sizeSelector").hide();

      if (settings["effects"].length < 2) $(this).find(".effectButton").hide();

      if (settings["alignments"].length < 2) $(this).find(".icon-align").hide();

      function checkFontMobile(ulS) {
        if ($(window).width() < 768) {
          ulS.find("li").each(function () {
            fsize = $(this).text();
            nfsize = parseFloat(fsize / 2);
            $(this)
              .text(nfsize)
              .css("font-size", parseFloat(nfsize) + "px");
          });
        } else {
          //ulS.find('.active').trigger('click');
        }
      }

      if (!refreshing) {
        if (settings["initial"] != "") select(settings["initial"]);

        if (settings["initialSize"] != "") selectSize(settings["initialSize"]);

        if (settings["initialColor"] != "")
          selectColor(settings["initialColor"]);

        if (settings["initialEffect"] != "")
          $(this)
            .find(".texteditor, .noborder")
            .attr("data-effect", settings["initialEffect"]);
        $(this)
          .find(".texteditor, .noborder")
          .css("text-align", settings["initialAlignment"]);
        $(this).find(".noborder").css("float", settings["initialAlignment"]);
      }

      ul.find("li").click(function () {
        if (!visible) return;

        //positionUl();
        ul.slideUp("fast", function () {
          visible = false;
        });
        ul.find("li").removeClass("active");
        $(this).addClass("active");
        select($(this).css("font-family"));

        //root.find('.submitText').trigger('click');
      });

      ulS.find("li").click(function () {
        if (!visible) return;
        //positionUlS();
        ulS.slideUp("fast", function () {
          visible = false;
        });
        ulS.find("li").removeClass("active");
        $(this).addClass("active");
        selectSize($(this).data("font-size"));
        //root.find('.submitText').trigger('click');
      });

      ulC.find("li").click(function () {
        if (!visible) return;
        //positionUlC();
        ulC.slideUp("fast", function () {
          visible = false;
        });
        ulC.find("li").removeClass("active");
        $(this).addClass("active");
        selectColor($(this).text());
        //root.find('.submitText').trigger('click');
      });

      ulSC.find("li").click(function () {
        if (!visible) return;
        //positionUlC();
        ulSC.slideUp("fast", function () {
          visible = false;
        });
        ulSC.find("li").removeClass("active");
        $(this).addClass("active");
        selectStrokeColor($(this).css("background-color"));
        //root.find('.submitText').trigger('click');
      });

      $(".sizeSelector").click(function (event) {
        if (visible) return;

        event.stopPropagation();
        //positionUlS();

        $(this)
          .parent()
          .find(".fontSizeSelectUl")
          .slideDown("fast", function () {
            visible = true;
          });
      });

      $(".colorSelector").click(function (event) {
        if (visible) return;

        event.stopPropagation();
        //positionUlC();
        $(this)
          .parent()
          .find(".fontColorSelectUl")
          .slideDown("fast", function () {
            visible = true;
          });
      });

      $(".strokeSelector").click(function (event) {
        if (visible) return;

        event.stopPropagation();
        //positionUlC();
        $(this)
          .parent()
          .find(".strokeColorSelectUl")
          .slideDown("fast", function () {
            visible = true;
          });
      });

      $(".fontSelector").click(function (event) {
        if (visible) return;

        event.stopPropagation();

        //positionUl();
        $(this)
          .parent()
          .find(".fontSelectUl")
          .slideDown("fast", function () {
            visible = true;
          });
      });

      $(".alignSelector > i").click(function () {
        $(this)
          .parent()
          .parent()
          .find(".alignSelector > i")
          .removeClass("active");
        $(this).addClass("active");
        $(this)
          .parent()
          .parent()
          .find(".texteditor, .noborder")
          .css("text-align", $(this).attr("data-align"));
        //$(this).parent().parent().find('.submitText').trigger('click');
        $(this)
          .parent()
          .parent()
          .find(".noborder")
          .css("float", $(this).attr("data-align"));
      });

      $(".effectButton").click(function () {
        $(this).parent().parent().find(".effectButton").removeClass("active");
        $(this).addClass("active");
        $(this)
          .parent()
          .parent()
          .find(".texteditor, .noborder")
          .attr("data-effect", $(this).attr("data-effect"));
        $(this)
          .parent()
          .parent()
          .find(".submitText, .submitTextItem")
          .trigger("click");
      });

      $("html").click(function () {
        if (visible) {
          ul.slideUp("fast", function () {
            visible = false;
          });
          ulS.slideUp("fast", function () {
            visible = false;
          });
          ulC.slideUp("fast", function () {
            visible = false;
          });
          ulSC.slideUp("fast", function () {
            visible = false;
          });
        }
      });
    },
    selected: function () {
      return root.css("font-family");
    },
    selectedSize: function () {
      return root.css("font-size");
    },
    selectedColor: function () {
      return root.css("color");
    },
    selectedStrokeColor: function () {
      return root.css("color");
    },
    select: function (font) {
      root
        .find("span.fontSelector")
        .html(font.substr(0, font.indexOf(",")).replace(/["']{1}/gi, ""));
      root.css("font-family", font).attr("data-font-family", font);
      selected = font;
    },
    selectSize: function (size) {
      root.find("span.sizeSelector").html(size);
      root.find(".texteditor").css("font-size", size);
      root.find(".texteditor").attr("data-font-size", size);
      root.css("font-size", size);
      root.attr("data-font-size", size);
      root.find(".noborder").trigger("change");
      selectedSize = size;
    },
    selectColor: function (color) {
      console.log(color);
      root.find(".texteditor, .noborder").attr("data-texture", "");
      root.find("span.colorSelector").css("background", color).html(color);
      //this.css('color', color);
      if (color.indexOf("http") >= 0) {
        root.find(".texteditor, .noborder").attr("data-texture", color);
        root.find(".texteditor").css("color", settings["initialColor"]);
      } else root.find(".texteditor").css("color", color);
      selectedColor = color;
      root.find("span.colorSelector").css("background", selectedColor);
    },

    selectStrokeColor: function (color) {
      root.find("span.strokeSelector").css("border-color", color).html(color);
      //this.css('color', color);
      root.find(".texteditor").attr("stroke-color", color);
      root
        .find(".noborder")
        .css(
          "text-shadow",
          color +
            "1px 1px, " +
            color +
            " -1px 1px, " +
            color +
            " -1px -1px, " +
            color +
            " 1px -1px"
        );
      selectedStrokeColor = color;
      root.find("span.strokeSelector").css("border-color", selectedStrokeColor);
    },
  };

  $.fn.fontSelector = function (method) {
    if (methods[method]) {
      return methods[method].apply(
        this,
        Array.prototype.slice.call(arguments, 1)
      );
    } else if (typeof method === "object" || !method) {
      return methods.init.apply(this, arguments);
    } else {
      $.error("Method " + method + " does not exist on jQuery.fontSelector");
    }
  };
})(jQuery);

//fitText
(function ($) {
  $.fn.fitText = function (kompressor, options) {
    // Setup options
    var compressor = kompressor || 1,
      settings = $.extend(
        {
          minFontSize: Number.NEGATIVE_INFINITY,
          maxFontSize: Number.POSITIVE_INFINITY,
        },
        options
      );

    return this.each(function () {
      // Store the object
      var $this = $(this);

      // Resizer() resizes items based on the object width divided by the compressor * 10
      var resizer = function () {
        $this.css(
          "font-size",
          Math.max(
            Math.min(
              $this.width() / (compressor * 10),
              parseFloat(settings.maxFontSize)
            ),
            parseFloat(settings.minFontSize)
          )
        );
      };

      // Call once to set.
      resizer();

      // Call on resize. Opera debounces their resize by default.
      $(window).on("resize.fittext orientationchange.fittext", resizer);
    });
  };
})(jQuery);
