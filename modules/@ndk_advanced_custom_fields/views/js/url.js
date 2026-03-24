var text1_url, text2_url, color_url;
$(function () {
  text1_url = getParameterByName("text1");
  text2_url = getParameterByName("text2");
  color_url = getParameterByName("color");
});

function setValuesByUrl() {
  color_url = color_url.replace("@", "#");

  var writeText = function () {
    if (text1_url != "") $(".noborder:eq(0)").val(text1_url).trigger("blur");
    if (text2_url != "") $(".noborder:eq(1)").val(text2_url).trigger("blur");
    //$('.colorSelector').trigger('click')
  };

  $.when(writeText()).then(function () {
    setTimeout(function () {
      designUrlFromText();
    }, 4500);
  });
}

function designUrlFromText() {
  newUrl = updateQueryStringParameter(
    window.location.href,
    "text1",
    $(".noborder:eq(0)").val()
  );
  newUrl = updateQueryStringParameter(
    newUrl,
    "text2",
    $(".noborder:eq(1)").val()
  );
  newUrl = updateQueryStringParameter(
    newUrl,
    "color",
    $(".colorSelector:eq(0)").text().replace("#", "@")
  );
  if (history.pushState) {
    window.history.pushState("", "", newUrl);
  } else {
    document.location.href = newUrl;
  }
}

$(document).on("ndkTextSet", ".ndktextarea", function () {
  designUrlFromText();
});
