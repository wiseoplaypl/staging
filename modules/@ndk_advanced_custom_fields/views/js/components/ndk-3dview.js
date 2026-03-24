$.fn.ndk3DView = function (options) {
  var el = this,
    defaults = {
      scene: null,
      file3d: null,
      camera: null,
      renderer: null,
      container: null,
      controls: null,
      clock: null,
      stats: null,
      elPath: null,
      parentContainer: document.getElementById("image-block"),
      mainContainer: document.getElementById("canvas_3D"),
      screen_width: 0,
      screen_height: 0,
      identifier: 0,
    },
    obj = {};
  el.settings = $.extend({}, defaults, options);

  el.init = function () {
    // Initialization
    //console.log(el);
    el.createMainScene();
    el.prepareContainer();
    //el.settings.addGround();
    el.addCamera();
    el.addControls();
    //el.addSpot("0xffffff", "90");
    //el.addSpot("0xffffff", "-90");
    el.addLight();

    // prepare clock
    el.settings.clock = new THREE.Clock();

    // load a model
    el.loadModel();
    el.animate();
  };
  el.createMainScene = function () {
    // create main scene
    el.settings.mainContainer = document.createElement("div");
    el.settings.mainContainer.setAttribute("data-view", el.settings.identifier);
    el.settings.mainContainer.setAttribute(
      "class",
      "three-d-canvas view-" + el.settings.identifier
    );
    el.settings.parentContainer.appendChild(el.settings.mainContainer);
    el.settings.mainContainer.innerHTML = "";

    el.settings.scene = new THREE.Scene();
    el.settings.scene.fog = new THREE.FogExp2(0xf1f1f1, 0.0003);
    el.settings.screen_width = el.settings.mainContainer.offsetWidth;
    el.settings.screen_height = el.settings.mainContainer.offsetHeight;

    window.addEventListener("resize", el.onWindowResize);
  };
  el.addCamera = function () {
    el.settings.camera = new THREE.PerspectiveCamera(
      5,
      el.settings.screen_width / el.settings.screen_height,
      1,
      2000
    );
    el.settings.camera.position.set(0, 1, 5);
    el.settings.camera.lookAt(new THREE.Vector3(0, 0, 0));
  };
  el.prepareContainer = function () {
    // prepare renderer
    el.settings.renderer = new THREE.WebGLRenderer({
      antialias: true,
      alpha: true,
    });
    el.settings.renderer.setSize(
      el.settings.screen_width,
      el.settings.screen_height
    );
    el.settings.renderer.setClearColor(el.settings.scene.fog.color);
    //el.settings.renderer.shadowMapEnabled = true;
    el.settings.renderer.shadowMapSoft = true;

    el.settings.renderer.setPixelRatio(window.devicePixelRatio);
    el.settings.renderer.outputEncoding = THREE.sRGBEncoding;
    el.settings.renderer.shadowMap.enabled = true;
    el.settings.renderer.physicallyCorrectLights = true;

    // prepare container
    el.settings.container = document.createElement("div");
    el.settings.mainContainer.appendChild(el.settings.container);
    el.settings.container.appendChild(el.settings.renderer.domElement);
  };
  el.addControls = function () {
    // prepare controls (OrbitControls)
    el.settings.controls = new THREE.OrbitControls(
      el.settings.camera,
      el.settings.renderer.domElement
    );
    el.settings.controls.target = new THREE.Vector3(0, 0, 0);
    el.settings.controls.maxDistance = 2000;
  };
  el.addGround = function () {
    // add simple ground
    var ground = new THREE.Mesh(
      new THREE.PlaneGeometry(200, 200, 10, 10),
      new THREE.MeshLambertMaterial({ color: 0x999999 })
    );
    ground.receiveShadow = true;
    ground.position.set(0, 0, 0);
    ground.rotation.x = -Math.PI / 2;
    el.settings.scene.add(ground);
  };
  el.loadModel = function () {
    const loader = new THREE.GLTFLoader();
    const dracoLoader = new THREE.DRACOLoader();
    loader.setDRACOLoader(dracoLoader);

    loader.load(
      el.settings.file3d,
      function (gltf) {
        el.settings.scene.add(gltf.scene);
        gltf.scene.traverse(function (child) {
          if (child.isMesh) child.castShadow = true;
        });
      },
      function (xhr) {
        console.log((xhr.loaded / xhr.total) * 100 + "% loaded");
        //renderer.render(scene, camera);
      },
      function (error) {
        console.log(error);
      }
    );
  };
  el.addLight = function () {
    const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444);
    hemiLight.position.set(0, 20, 0);
    el.settings.scene.add(hemiLight);
    const dirLight = new THREE.DirectionalLight(0xffffff);
    dirLight.position.set(-3, 10, -10);
    dirLight.castShadow = true;
    dirLight.shadow.camera.top = 2;
    dirLight.shadow.camera.bottom = -2;
    dirLight.shadow.camera.left = -2;
    dirLight.shadow.camera.right = 2;
    dirLight.shadow.camera.near = 0.1;
    dirLight.shadow.camera.far = 40;
    //dirLight.intensity = 100;
    el.settings.scene.add(dirLight);
  };
  el.addSpot = function (color, angle, intensity, far, near) {
    // add spot light
    far = far || 2000;
    near = near || 20;
    intensity = intensity || 10;
    var spotLight = new THREE.SpotLight(color);
    spotLight.position.set(
      -el.settings.screen_width / 2,
      -el.settings.screen_height,
      el.settings.screen_width / 2
    );
    spotLight.castShadow = true;
    spotLight.intensity = 10;
    spotLight.angle = angle;
    spotLight.shadow.mapSize.width = el.settings.screen_width;
    spotLight.shadow.mapSize.height = el.settings.screen_height;
    spotLight.shadow.camera.near = near;
    spotLight.shadow.camera.far = far;
    spotLight.shadow.camera.fov = 30;
    el.settings.scene.add(spotLight);
  };
  el.setColor = function (color, name) {
    name = name || false;
    el.settings.scene.traverse(function (child) {
      if (child.isMesh) {
        if (name) {
          if (child.material.name == name) {
            console.log(color);
            child.material = new THREE.MeshLambertMaterial({ color: color });
            child.material.color.set(color);
            child.material.name = name;
            child.material.blending = THREE.NoBlending;
          }
        } else {
          child.material.color.set(color);
        }
      }
    });
  };

  el.setTexture = function (texture_file, name, blending) {
    name = name || false;
    blending = blending || THREE.NoBlending;
    if (blending == "multiply") blending = THREE.MultiplyBlending;

    const applyTexture = function (child, texture_file, blending) {
      var texture = new THREE.TextureLoader().load(
        texture_file,
        function (texture) {
          texture.matrixAutoUpdate = false;
          texture.minFilter = THREE.LinearFilter;
          var box = new THREE.Box3().setFromObject(child);
          texture.repeat = new THREE.Vector2(1, 1);
          texture.offset = new THREE.Vector2(box.getSize().x, box.getSize().y);
          child.material.map = texture;
          child.material.blending = blending;
          child.material.needsUpdate = true;
          el.update();
        }
      );
    };

    el.settings.scene.traverse(function (child) {
      if (child.isMesh) {
        if (name) {
          if (child.material.name == name) {
            applyTexture(child, texture_file, blending);
          }
        } else {
          applyTexture(child, texture_file, blending);
        }
      }
    });
  };

  el.setCanvasTexture = function (canvas, name, blending) {
    name = name || false;
    blending = blending || THREE.NoBlending;
    if (blending == "multiply") blending = THREE.MultiplyBlending;
    console.log(canvas);
    applyCanvasTexture = function (child, canvas, blending) {
      texture = new THREE.CanvasTexture(canvas);
      texture.matrixAutoUpdate = false;
      texture.minFilter = THREE.LinearFilter;
      //box = new THREE.Box3().setFromelect(child);
      texture.repeat = new THREE.Vector2(1, 1);
      //texture.offset = new THREE.Vector2(box.getSize().x, box.getSize().y);
      child.material.map = texture;
      child.material.blending = blending;
    };
    el.settings.scene.traverse(function (child) {
      if (child.isMesh) {
        if (name) {
          if (child.material.name == name) {
            applyCanvasTexture(child, canvas, blending);
          }
        } else {
          applyCanvasTexture(child, canvas, blending);
        }
      }
    });
  };
  el.animateScene = function () {};
  el.onWindowResize = function () {
    el.settings.screen_width = el.settings.mainContainer.offsetWidth;
    el.settings.screen_height = el.settings.mainContainer.offsetHeight;
    el.settings.camera.aspect =
      el.settings.screen_width / el.settings.screen_height;
    el.settings.camera.updateProjectionMatrix();
    el.settings.renderer.setSize(
      el.settings.screen_width,
      el.settings.screen_height
    );
  };
  el.update = function () {
    el.settings.controls.update(el.settings.clock.getDelta());
  };
  el.render = function () {
    if (el.settings.renderer) {
      el.settings.renderer.render(el.settings.scene, el.settings.camera);
    }
  };
  el.animate = function () {
    requestAnimationFrame(el.animate);
    el.render();
    el.update();
  };
  el.init();
  el.animate();
  return el;
};

var td_need_canvas = [];

$(document).on("click", ".view_tab.glb", function (e) {
  $("#image-block").addClass("three-d-view-active");
  $(".editThisLayer").hide();
  $(".absolute-visu").hide();
  $(".zone_limit").hide();
  scrollToNdk($("#image-block"), 100);
  $(".resetZones").trigger("click");

  $(".zone_limit").each(function () {
    me = $(this);
    group = me.data("group");
    if (!me.is(":empty")) {
      prepare3dCanvas(group);
    }
  });
});

$(document).on("ndkacf:ndkCompoDesigned", function (e) {
  $("#visual_" + e.group)
    .find(".ui-resizable-handle, .editIt, .ui-rotatable-handle")
    .attr("data-html2canvas-ignore", true);
});

async function runZoneFor3dRender(group) {
  await prepare3dCanvas(group);
}

async function prepare3dCanvas(group) {
  //var a = await resolveAfterTime(500);
  zone = $(".zone_limit[data-group='" + group + "']");
  rootBlock = $(".form-group[data-field='" + group + "']");
  material_name = rootBlock.data("material_name");

  if (material_name) {
    if (
      $("#visual_" + group).hasClass("ui-resizable") ||
      $("#visual_" + group).hasClass("ui-draggable") ||
      $("#visual_" + group).hasClass("rotatable") ||
      $("#visual_" + group).hasClass("absolute-svg-text") ||
      $("#visual_" + group).hasClass("absolute-svg")
    ) {
      zone.addClass("three-d-rendering");
      zone.find(".absolute-visu").show();
      //td_need_canvas[group] = true;
      ndkHtmlToPng(zone[0])
        .then(function (dataUrl) {
          td_need_canvas[group] = dataUrl;
          zone.removeClass("three-d-rendering");
          generate3dTexture(group, dataUrl);
          return new Promise(function (resolve) {
            resolve(group);
          });
        })
        .catch(function (error) {
          console.error("oops, something went wrong!", error);
          zone.removeClass("three-d-rendering");
        });
    } else {
      zone.removeClass("three-d-rendering");
      td_need_canvas[group] = false;
      generate3dTexture(group);
      return new Promise(function (resolve) {
        resolve(group);
      });
    }
  }
}

function generate3dTexture(group, canvas = false) {
  console.log(group);
  rootBlock = $(".form-group[data-field='" + group + "']");
  zone = $(".zone_limit[data-group='" + group + "']");
  //console.log(td_need_canvas);

  material_name = rootBlock.data("material_name");
  applyNdkTexture = function (url, zone, blend) {
    for (i = 0; i < ndk3dViews.length; i++) {
      ndk3dViews[i].setTexture(url, zone, blend);
    }
  };

  if (material_name) {
    if (canvas) {
      zone.show().find(".absolute-visu").show();
      var canvas = td_need_canvas[group];
      //console.log(canvas);
      applyNdkTexture(canvas, material_name, "multiply");
    } else {
      //on colorise
      if ($("#visual_" + group).find(".colorize-cover-item").length > 0) {
        color = $("#visual_" + group)
          .find(".colorize-cover-item:eq(0)")
          .attr("data-bgcolor");
        if (typeof color != "undefined") {
          for (i = 0; i < ndk3dViews.length; i++) {
            ndk3dViews[i].setColor(color, material_name);
          }
        }
      } else {
        //une image non dimensionnable pas besoin de snapshot
        image_url = $("#visual_" + group)
          .find("img:eq(0)")
          .attr("src");
        for (i = 0; i < ndk3dViews.length; i++) {
          ndk3dViews[i].setTexture(image_url, material_name, "multiply");
        }
      }
      zone.hide();
    }
  }
  zone.hide();
}
