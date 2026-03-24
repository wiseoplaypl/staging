function makeSocialCompo(id_conf, popup) {
  if (typeof makeSocialCompo_Override == "function") {
    return makeSocialCompo_Override(id_conf, popup);
  }

  if (showSocialTools == 1) {
    if (!!$.prototype.fancybox && popup) {
      data = $(".ndkShareCompo").html();
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
              '<div class="popupSocialContainer clear clearfix">' +
              data +
              "</div>",
            beforeShow: function () {
              setSharedButtons(id_conf);
            },
          },
        ],
        {
          padding: 0,
        }
      );
    } else {
      setSharedButtons(id_conf);
    }
  }
}

async function setSharedButtons(id_conf) {
  if (typeof setSharedButtons_Override == "function") {
    return setSharedButtons_Override(id_conf);
  }
  sharing_url = removeParamUrl(
    "id_ndk_customization_field_configuration",
    window.location.href
  );
  sharing_url = addParameterToURL(
    "id_ndk_customization_field_configuration=" + id_conf,
    sharing_url
  );
  sharing_url = addParameterToURL("date=" + $.now(), sharing_url);

  sharing_name = "";

  img_url = $(".current_config_img").attr("src");

  $(".copyLinkInput").val(sharing_url);
  if (img_url != "") $(".shareImgDl").attr("href", img_url).show();
  else $(".shareImgDl").hide();

  if ($("#image-url-0").val() == "") {
    var photoTaken = await takePhoto(0, false, true);
    var conf_img_url = $("#image-url-0").val();
  }

  //console.log(conf_img_url);

  $.ajax({
    async: true,
    type: "GET",
    global: false,
    dataType: "html",
    url: baseUrl + "modules/ndk_advanced_custom_fields/front_ajax.php",
    data: { id_conf: id_conf, action: "getConfImage" },
    success: function (data) {
      //var conf_img_url = baseUrl+'img/scenes/'+data;

      $(".current_config_img:eq(0)").attr("src", conf_img_url).show();
      $("button.ndk-social-sharing:not(.shareImgDl), button.social-sharing").on(
        "click",
        function () {
          type = $(this).attr("data-type");
          if (type.length) {
            switch (type) {
              case "twitter":
                window.open(
                  "https://twitter.com/intent/tweet?text=" +
                    sharing_name +
                    " " +
                    encodeURIComponent(sharing_url),
                  "sharertwt",
                  "toolbar=0,status=0,width=640,height=445"
                );
                break;
              case "facebook":
                window.open(
                  "http://www.facebook.com/sharer.php?u=" + sharing_url,
                  "sharer",
                  "toolbar=0,status=0,width=660,height=445"
                );
                break;
              case "google-plus":
                window.open(
                  "https://plus.google.com/share?url=" + sharing_url,
                  "sharer",
                  "toolbar=0,status=0,width=660,height=445"
                );
                break;
              case "pinterest":
                window.open(
                  "http://www.pinterest.com/pin/create/button/?media=" +
                    conf_img_url +
                    "&url=" +
                    sharing_url,
                  "sharerpinterest",
                  "toolbar=0,status=0,width=660,height=445"
                );
                break;
              case "copyLink":
                copySocialLink();
                break;
            }
          }
        }
      );
      $(window).trigger("resize");
    },
  });
}

function copySocialLink() {
  var copyText = document.querySelector("#copyLinkInput");
  console.log(copyText.value);
  copyText.select();
  document.execCommand("copy");
}

function copyToClipboard(value) {
  var tempInput = document.createElement("input");
  tempInput.value = value;
  document.body.appendChild(tempInput);
  tempInput.select();
  document.execCommand("copy");
  document.body.removeChild(tempInput);
}
