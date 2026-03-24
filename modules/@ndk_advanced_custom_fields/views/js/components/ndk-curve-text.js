var NdkAcfCurveText = {};
var svgTextNdkacf = [];

NdkAcfCurveText.setCurvable = function (group) {
  svg = document.querySelector("#visual_" + group + " .textareaSvg");

  svg.classList.add("curvable-svg-active");
  "text-item-, ndkcsfield_".split(",").map((s) => {
    if (document.contains(document.getElementById(s + group)))
      document
        .getElementById(s + group)
        .setAttribute("data-path", "curve_" + group);
  });
  NdkAcfCurveText.curveText(group);
};
NdkAcfCurveText.unsetCurvable = function (group) {
  svg = document.querySelector("#visual_" + group + " .textareaSvg");
  "text-item-, ndkcsfield_".split(",").map((s) => {
    if (document.contains(document.getElementById(s + group)))
      document.getElementById(s + group).setAttribute("data-path", "");
  });
  "p1,p2,c1,l1,l2,curve,textPath".split(",").map((s) => {
    if (document.contains(document.getElementById(s + "_" + group)))
      document.getElementById(s + "_" + group).remove();
  });
  svg.querySelector("tspan").style.display = "";
};

NdkAcfCurveText.getCurveTextDatas = function (group) {
  var svg = document.querySelector("#visual_" + group + " .textareaSvg");
  var NS = svg.getAttribute("xmlns"),
    vb = svg
      .getAttribute("viewBox")
      .split(" ")
      .map((v) => +v);

  parentBox = svg.parentNode.getBoundingClientRect();
  //console.log(parentBox);
  if (typeof svgTextNdkacf[group] === "undefined") svgTextNdkacf[group] = [];
  svgTextNdkacf[group]["box"] = {
    xMin: vb[0],
    xMax: vb[0] + vb[2],
    yMin: vb[1],
    yMax: vb[1] + vb[3],
    xMiddle: vb[0] + vb[2] / 2,
    yMiddle: vb[1] + vb[3] / 2,
  };
};
NdkAcfCurveText.curveText = function (group, size_only = false) {
  var svg = document.querySelector("#visual_" + group + " .textareaSvg");
  if (!Boolean(svg)) return false;
  //NdkAcfCurveText.getCurveTextDatas(group);

  if (!svg.classList.contains("curvable-svg")) {
    NdkAcfCurveText.addCurveAndControls(svg, group);
  } else {
    if (!size_only) NdkAcfCurveText.updateCurvedText(svg, group);
  }
  "p1,p2,c1,l1,l2,curve,textPath".split(",").map((s) => {
    svgTextNdkacf[group][s] = document.getElementById(s + "_" + group);
  });

  // events
  svg.addEventListener("pointerdown", NdkAcfCurveText.dragHandler);
  svg.addEventListener("pointermove", NdkAcfCurveText.dragHandler);
  svg.addEventListener("pointerup", NdkAcfCurveText.dragHandler);

  svg.addEventListener("touchstart", NdkAcfCurveText.dragHandler);
  svg.addEventListener("touchmove", NdkAcfCurveText.dragHandler);
  svg.addEventListener("touchend", NdkAcfCurveText.dragHandler);
  NdkAcfCurveText.drawCurve(group);
};

NdkAcfCurveText.addCurveAndControls = function (svg, group) {
  svg.setAttribute("group", group);
  "p1,p2,c1,l1,l2,curve,textPath".split(",").map((s) => {
    if (document.contains(document.getElementById(s + "_" + group)))
      document.getElementById(s + "_" + group).remove();
  });
  svg.classList.add("curvable-svg");
  svg.parentNode.classList.add("has-curvable-svg");
  NdkAcfCurveText.getCurveTextDatas(group);
  var node = svgTextNdkacf[group];

  if (typeof svgTextNdkacf[group].c1 != "undefined") {
    circles =
      svgTextNdkacf[group].p1.outerHTML +
      svgTextNdkacf[group].p2.outerHTML +
      svgTextNdkacf[group].c1.outerHTML;
  } else {
    circles = `<circle id="p1_${group}" cx="${node.box.xMin + 1}" cy="${
      node.box.yMiddle
    }" r="5%" class="svg-control-circle control" /><circle id="p2_${group}" cx="${
      node.box.xMax - 1
    }" cy="${
      node.box.yMiddle
    }" r="5%" class="svg-control-circle control " /> <circle id="c1_${group}" cx="${
      node.box.xMiddle
    }" cy="${
      node.box.yMiddle
    }" r="4%" class="c1 svg-control-circle control" />`;
  }
  if (typeof svgTextNdkacf[group].l1 != "undefined") {
    lines =
      svgTextNdkacf[group].l1.outerHTML + svgTextNdkacf[group].l2.outerHTML;
  } else {
    lines = `<line id="l1_${group}" class="svg-control-line" x1="${node.box.xMin}" y1="${node.box.yMax}" x2="${node.box.xMax}" y2="${node.box.yMin}" /><line id="l2_${group}" class="svg-control-line" x1="${node.box.xMax}" y1="${node.box.yMiddle}" x2="${node.box.xMiddle}" y2="${node.box.yMiddle}" />`;
  }

  if (typeof svgTextNdkacf[group].curve != "undefined") {
    path = svgTextNdkacf[group].curve.outerHTML;
  } else {
    path = `<path class="curve" id="curve_${group}" d="M${node.box.xMin + 1},${
      node.box.yMiddle
    } Q${node.box.xMiddle},${node.box.yMiddle} ${node.box.xMax},${
      node.box.yMiddle
    }" fill="transparent"/>`;
  }

  svg.innerHTML += lines;
  svg.innerHTML += path;
  svg.innerHTML += circles;
  //NdkAcfCurveText.ensureViewBox(group);
  NdkAcfCurveText.updateCurvedText(svg, group);
};

NdkAcfCurveText.ensureViewBox = function (group) {
  var svg = document.querySelector("#visual_" + group + " .textareaSvg");
  if (!Boolean(svg)) return false;

  if (
    typeof svg.parentNode.getAttribute("data-viewbox") != "undefined" &&
    svg.parentNode.getAttribute("data-viewbox") != null
  ) {
    viewbox = svg.parentNode.getAttribute("data-viewbox");
  } else {
    parentBox = svg.parentNode.getBoundingClientRect();
    var viewbox = `0 0 ${parentBox.right - parentBox.left} ${
      parentBox.bottom - parentBox.top
    }`;
    svg.parentNode.setAttribute("data-viewbox", viewbox);
  }

  svg.setAttribute("viewBox", viewbox);
};

NdkAcfCurveText.updateCurvedText = function (svg, group) {
  NdkAcfCurveText.getCurveTextDatas(group);
  var element = svg.querySelector("tspan");
  var newElement = document.createElementNS(
    "http://www.w3.org/2000/svg",
    "textPath"
  );
  newElement.innerHTML = element.innerHTML;
  newElement.setAttribute("xlink:href", "#curve_" + group);
  newElement.setAttribute("id", "textPath_" + group);
  newElement.setAttribute("text-anchor", element.getAttribute("text-anchor"));
  newElement.setAttribute("startOffset", element.getAttribute("x"));
  newElement.style.fontSize = element.style.fontSize;
  textNode = svg.querySelector("text");
  textNode.appendChild(newElement);
  element.style.display = "none";
  svg.innerHTML += "";
};

// drag handler
var drag;
var draggingTimer;
NdkAcfCurveText.dragHandler = function (event) {
  clearTimeout(draggingTimer);
  var closestSvg = event.target.closest(".textareaSvg");
  if (!closestSvg.classList.contains("curvable-svg-active")) {
    return false;
  }

  group = closestSvg.getAttribute("group");
  var node = svgTextNdkacf[group];
  //console.log(svgTextNdkacf)
  var target = event.target,
    type = event.type,
    svgP = NdkAcfCurveText.svgPoint(svg, event.clientX, event.clientY);

  // fill toggle
  if (
    !drag &&
    type === "pointerdown" &&
    target === svgTextNdkacf[group].curve
  ) {
    event.preventDefault();
    // svgTextNdkacf[group].curve.classList.toggle("fill");
    // NdkAcfCurveText.drawCurve(group);
  }

  // start drag
  if (
    !drag &&
    (type === "pointerdown" || type === "touchstart") &&
    target.classList.contains("control")
  ) {
    event.preventDefault();
    drag = {
      node: target,
      start: NdkAcfCurveText.getControlPoint(target),
      cursor: svgP,
    };

    drag.node.classList.add("drag");
  }

  // move element
  if (drag && (type === "pointermove" || type === "touchmove")) {
    closestSvg.parentNode.classList.add("editing_item");
    NdkAcfCurveText.updateElement(drag.node, {
      cx: Math.max(
        node.box.xMin - 50,
        Math.min(drag.start.x + svgP.x - drag.cursor.x, node.box.xMax + 50)
      ),
      cy: Math.max(
        node.box.yMin - 50,
        Math.min(drag.start.y + svgP.y - drag.cursor.y, node.box.yMax + 50)
      ),
    });

    NdkAcfCurveText.drawCurve(group);
  }

  // stop drag
  var editTimer;
  if (drag && (type === "pointerup" || type === "touchend")) {
    clearTimeout(editTimer);
    drag.node.classList.remove("drag");
    drag = null;
    //NdkAcfCurveText.ensureViewBox(group);
    NdkAcfCurveText.curveText(group, true);
    editTimer = setTimeout(function () {
      if (closestSvg != null)
        closestSvg.parentNode.classList.remove("editing_item");
    }, editTimerDuration);
  }
};

// translate page to SVG co-ordinate
NdkAcfCurveText.svgPoint = function (element, x, y) {
  svg = document.querySelector("#visual_" + group + " .textareaSvg");
  var pt = svg.createSVGPoint();
  pt.x = x;
  pt.y = y;
  return pt.matrixTransform(element.getScreenCTM().inverse());
};

// update element
NdkAcfCurveText.updateElement = function (element, attr) {
  for (a in attr) {
    var v = attr[a];
    element.setAttribute(a, isNaN(v) ? v : Math.round(v));
  }
};

// get control point location
NdkAcfCurveText.getControlPoint = function (circle) {
  return {
    x: Math.round(+circle.getAttribute("cx")),
    y: Math.round(+circle.getAttribute("cy")),
  };
};

// update curve
NdkAcfCurveText.drawCurve = function (group) {
  var p1 = NdkAcfCurveText.getControlPoint(svgTextNdkacf[group].p1),
    p2 = NdkAcfCurveText.getControlPoint(svgTextNdkacf[group].p2),
    c1 = NdkAcfCurveText.getControlPoint(svgTextNdkacf[group].c1);

  // control line 1
  NdkAcfCurveText.updateElement(svgTextNdkacf[group].l1, {
    x1: p1.x,
    y1: p1.y,
    x2: c1.x,
    y2: c1.y,
  });

  // control line 2
  NdkAcfCurveText.updateElement(svgTextNdkacf[group].l2, {
    x1: p2.x,
    y1: p2.y,
    x2: c1.x,
    y2: c1.y,
  });

  // curve
  var d =
    `M${p1.x},${p1.y} Q${c1.x},${c1.y} ${p2.x},${p2.y}` +
    (svgTextNdkacf[group].curve.classList.contains("fill") ? " Z" : "");

  NdkAcfCurveText.updateElement(svgTextNdkacf[group].curve, {
    d,
  });
};

NdkAcfCurveText.setCurveType = function (group, type = "straight") {
  node = svgTextNdkacf[group];
  switch (type) {
    case "straight":
      //straight
      NdkAcfCurveText.updateElement(node.p1, {
        cx: node.box.xMin,
        cy: node.box.yMiddle,
      });
      NdkAcfCurveText.updateElement(node.c1, {
        cx: node.box.xMiddle,
        cy: node.box.yMiddle,
      });
      NdkAcfCurveText.updateElement(node.p2, {
        cx: node.box.xMax,
        cy: node.box.yMiddle,
      });
      break;
    case "concav":
      //concav
      NdkAcfCurveText.updateElement(node.p1, {
        cx: node.box.xMin,
        cy: node.box.yMax,
      });
      NdkAcfCurveText.updateElement(node.c1, {
        cx: node.box.xMiddle,
        cy: node.box.yMax * -1,
      });
      NdkAcfCurveText.updateElement(node.p2, {
        cx: node.box.xMax,
        cy: node.box.yMax,
      });
      break;
    case "convex":
      //convex
      NdkAcfCurveText.updateElement(node.p1, {
        cx: node.box.xMin,
        cy: node.box.yMin,
      });
      NdkAcfCurveText.updateElement(node.c1, {
        cx: node.box.xMiddle,
        cy: node.box.yMax,
      });
      NdkAcfCurveText.updateElement(node.p2, {
        cx: node.box.xMax,
        cy: node.box.yMin,
      });
      break;
  }

  NdkAcfCurveText.drawCurve(group);
  //NdkAcfCurveText.ensureViewBox(group);
};

$(document).on("ndkacf:ndkCompoDesigned", function (e) {
  //curvable
  mainGroup = e.group;
  if (e.group.indexOf("-") > -1) {
    mainGroup = e.group.split("-")[0];
  }
  curvable = $(".form-group[data-field='" + mainGroup + "']").attr(
    "data-curvable"
  );
  if (
    curvable == 1 &&
    $("#visual_" + e.group + " .textareaSvg.curvable-svg-active").length < 1
  ) {
    setTimeout(async function () {
      if ($("#visual_" + e.group + " .textareaSvg tspan").length == 1) {
        NdkAcfCurveText.ensureViewBox(e.group);
        NdkAcfCurveText.unsetCurvable(e.group);
        NdkAcfCurveText.setCurvable(e.group);
      }
    }, doneTypingInterval);
  }
});
