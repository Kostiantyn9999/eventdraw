/*
* {
*   container: "element-id",
    scenes: json data
* }
*/

/**
 * version: 1.0
 * @param {*} parameter
 */

const PlanViewer = function (parameter) {
  const main = this;

  main.engineVersion = "v1.88";

  main.raycaster = new THREE.Raycaster();
  main.elemID = parameter.container;
  main.scenes = parameter.scenes;
  main.store = parameter.store;

  main.mouse = new THREE.Vector2();
  main.plane = new THREE.Plane();
  main.pNormal = new THREE.Vector3(0, 1, 0); // plane's normal
  main.planeIntersect = new THREE.Vector3(); // point of intersection with the plane
  main.pIntersect = new THREE.Vector3(); // point of intersection with an object (plane's point)
  main.shift = [];

  main.onOffCubes = [];
  main.selectedGroup = null;

  main.tempV = new THREE.Vector3();
  main.isDragObj = false;
  main.isMouseDown = false;
  main.isPan = false;
  main.isLock = true;

  // Camera setting
  main.view_angle = 60;
  main.near = 1;
  main.far = 100000;
  main.camera_x = 50;
  main.camera_y = 400;
  main.camera_z = 600;

  this.floorGroup = new THREE.Group();
  this.parentGroup = new THREE.Group();
  this.wallGroup = new THREE.Group();
  this.lineGroup = new THREE.Group();

  const clock = new THREE.Clock();
  main.realistic = false;

  main.fnInit = function () {
    console.log(
      `%c ${main.engineVersion}`,
      "background: #222; color: #bada55; font-size:40px"
    );

    main.realistic = main.getParameterByName("realistic") !== "";
    if (main.realistic) {
      $(".building-loading-page").show();
    } else {
      $(".building-loading-page").hide();
    }

    main.clock = new THREE.Clock();

    main.dragObjectControl = new ShapeControl();
    main.textControl = new SettingText();

    main.container = document.getElementById(main.elemID);

    main.fnInitScene();
    main.fnInitCamera();
    main.fnInitRenderer();
    main.fnInitLights();
    main.fnInitElement();
    main.fnInitControl();

    main.msmControl = new MeasurementControl(main.scene, main.camera, main.screen_width, main.screen_height);

    // main.fnLoadBuilding();

    // main.placeShape("table1", 60, 0, 10);
    // main.placeShape("chair1", 20, 0, 20);
    // main.placeShape("chair2", 20, 0, 20);

    // main.placeWall("wall1", 10, 0, 40);

    // main.placeWindow("sd", 0, 105, 295)
    // main.createWall();
    // main.createPilor();

    // main.loadModel();
    main.test();
    main.fnRender();

    // main.settingBackground = new SettingBackground(main.scene);
    main.settingFloor = new SettingFloor(main.scene, main.msmControl);
    // main.settingPanoram = new SettingPanorama(main.scene);
    main.settingWall = new SettingWall(this, main.wallGroup, this.model);
    main.settingFloor.setMovedArtCallback(main.settingWall.onArtMoved);
    main.settingPosSize = new SettingSize(
      main.lineGroup,
      main.onOffCubes,
      main.store,
      main.scene
    );
    main.settingEnvironment = new SettingEnvironment(main.cameraControl);

    main.curorManagement = new Cursor(main.scene);

    main.store.setEngine(this);
    main.fnInitEvent();

    // Set Reset Event
    main.scenes.fireOnNewScene(main.reset);

    // main.settingWall.fireOnChangedWallHeight(main.scenes.onChangedWallHeight);
    main.settingWall.fireOnChangedWallHeight(
      main.settingPosSize.fnInitElevationElement
    );

    main.settingWall.fireOnChangedWallOpacity(main.scenes.onChangedWallOpacity);
    main.settingWall.fireOnChangedWallTexture(main.scenes.onChangedWallTexture);
    main.settingWall.fireOnChangedWallHeight(main.scenes.onChangedWallHeight);
    main.settingWall.fireOnImportedArt(main.scenes.onImportedNewArt);
    main.settingWall.fireOnMovedArt(main.scenes.onMovedArt);
    main.settingWall.fireOnRotatedArt(main.scenes.onRotatedArt);
    main.settingWall.fireOnRemovedArt(main.scenes.onRemovedArt);

    main.settingFloor.fireOnChangedFloorOpacity(
      main.scenes.onChangedFloorOpacity
    );
    main.settingFloor.fireOnChangedFloorTexture(
      main.scenes.onChangedFloorTexture
    );

    main.settingPosSize.fireOnChangedShapeBaseElevation(
      main.scenes.onChangedShapeBaseElevation
    );
    main.settingPosSize.fireOnChangedShapeBaseHeight(
      main.scenes.onChangedShapeHeight
    );

    main.settingPosSize.fireOnWallObjectChanged(
      main.settingWall.createHoleAndDoor
    );

    main.settingPosSize.fireOnChangeTablePropery(
      main.scenes.onChangedTableProperty
    );

    $(".version_name").text(main.engineVersion);
  };

  main.reset = () => {
    this.wallGroup.traverse((m) => {
      if (m.isMesh) {
        m.geometry.dispose();
        m.material.dispose();
        if (m.material.map) m.material.map.dispose();
      }
    });

    this.floorGroup.traverse((m) => {
      if (m.isMesh) {
        this.floorGroup.remove(m);
        m.geometry.dispose();
        m.material.dispose();
        if (m.material.map) m.material.map.dispose();
      }
    });

    this.parentGroup.traverse((m) => {
      if (m.isMesh) {
        m.geometry.dispose();
        m.material.dispose();
        if (m.material.map) m.material.map.dispose();
      }
    });

    this.scene.remove(this.parentGroup);
    this.scene.remove(this.lineGroup);
    this.scene.remove(this.floorGroup);

    this.floorGroup = new THREE.Group();
    this.parentGroup = new THREE.Group();
    this.wallGroup = new THREE.Group();
    this.lineGroup = new THREE.Group();

    this.parentGroup.add(this.wallGroup);

    this.scene.add(this.parentGroup);
    this.scene.add(this.lineGroup);
    this.scene.add(this.floorGroup);

    // const planeX = new THREE.Plane(new THREE.Vector3(1, 1, 1), 1);
    // const helper = new THREE.PlaneHelper( planeX, 10000, 0xff0000 );
    // this.scene.add( helper );

    main.settingWall.setGroup(this.wallGroup);
    main.settingWall.doors = [];

    main.store.fnReset();
  };

  main.fireBackground = (callback) => {
    main.newOnBackground = callback;
  };

  main.setPlatform = () => {
    const { platform = {}, arts = [] } = main.scenes.settings;
    // console.log("---------Platform----------", main.scenes.settings)
    const {
      background = null,
      wallheight = null,
      wallOpacity = null,
      wallTexture = null,
      floorOpacity = null,
      floorTexture = null,
    } = platform;

    if (background) main.newOnBackground.fire(background);
    if (wallheight) main.settingWall.onChangedWallHeight.fire(wallheight);
    if (wallOpacity) main.settingWall.onChangedWallOpacity.fire(wallOpacity);
    if (wallTexture)
      main.settingWall.onChangedWallTexture.fire(
        wallTexture.type,
        wallTexture.value
      );
    if (floorTexture)
      main.settingFloor.onChangedFloorTexture.fire(floorTexture);
    if (floorOpacity)
      main.settingFloor.onChangedFloorOpacity.fire(floorOpacity);

    arts.forEach((d) => {
      const { position = null, rotation = null, type, id, path } = d;

      main.settingWall.onArtImported.fire(type, id, path);
      if (position) main.settingWall.onArtMoved.fire(id, position);
      if (rotation) main.settingWall.onArtRotated.fire(id, rotation);
    });
  };

  main.fnInitScene = function () {
    this.scene = new THREE.Scene();
    this.cssScene = new THREE.Scene();

    // main.scene.background = new THREE.Color( 0x000000 );
    // this.scene.background = new THREE.Color().setRGB(12/ 256, 23 / 256, 23 / 256);
    // this.scene.background = new THREE.Color( 0xcce0ff );
    // this.scene.fog = new THREE.Fog( 0xcce0ff, 500, 10000 );

    this.parentGroup.add(this.wallGroup);
    this.scene.add(this.parentGroup);
    this.scene.add(this.lineGroup);
    this.scene.add(this.floorGroup);

    this.model = new Model(this.wallGroup);
    this.plan = new Plan(main.wallGroup, main.model.floorplan);
  };

  main.fnInitCamera = function () {
    main.screen_width = document.getElementById(main.elemID).offsetWidth;
    main.screen_height = document.getElementById(main.elemID).offsetHeight;

    var ASPECT = main.screen_width / main.screen_height;

    main.camera1 = new THREE.PerspectiveCamera(
      main.view_angle,
      ASPECT,
      main.near,
      main.far
    );
    main.camera2 = new THREE.OrthographicCamera(
      main.screen_width / -2,
      main.screen_width / 2,
      main.screen_height / 2,
      main.screen_height / -2,
      main.near,
      main.far
    );
    // main.camera2.zoom = 0.5;
    // main.scene.add( main.camera );

    // main.camera.position.set(main.camera_x, main.camera_y, main.camera_z);
    // main.camera.lookAt(0, 0, 0);

    main.camera = main.camera1;
  };

  /**
   * for exporting, set preserveDrawingBuffer:true ref: https://stackoverflow.com/questions/15558418/how-do-you-save-an-image-from-a-three-js-canvas
   */
  main.fnInitRenderer = function () {
    main.renderer = new THREE.WebGLRenderer({
      preserveDrawingBuffer: true,
      antialias: true,
      alpha: true,
    });
    main.renderer.setPixelRatio(window.devicePixelRatio);
    main.renderer.setSize(main.screen_width, main.screen_height);

    main.renderer.shadowMap.enabled = true;
    main.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    main.renderer.shadowMapCullFace = THREE.CullFaceFrontBack;

    main.renderer.shadowCameraNear = 0.1;
    main.renderer.shadowCameraFar = 200000;
    main.renderer.shadowCameraFov = 45;

    main.renderer.outputEncoding = THREE.sRGBEncoding;
    main.renderer.gammaOutput = true;
    // main.renderer.autoClear = false;
    // console.log(main.renderer.sortObjects)
    // main.renderer.sortObjects = false;

    // main.renderer.domElement.style.position = 'absolute';
    // main.renderer.domElement.style.zIndex = 1;
    // main.renderer.domElement.style.top = 0;

    // main.renderer.setClearColor(0x000000, 0);
  };

  main.fnInitLights = function () {
    main.lightControl = new LightControl();

    main.ambientLight = new THREE.AmbientLight(0xffffff, 0.3);
    main.scene.add(main.ambientLight);

    // main.light = new THREE.HemisphereLight(0xffffff, 0x444444, 0.35);
    // main.light.position.set(0, 500, 0);
    // main.scene.add(main.light);

    main.scene.add(main.lightControl.light1);
    main.scene.add(main.lightControl.light2);
    main.scene.add(main.lightControl.light3);

    // main.light1 = new THREE.DirectionalLight(0xffffff);
    // main.light1.position.set(0, 200, 0);
    // main.light1.castShadow = true;
    // main.light1.shadow.camera.top = 1000;
    // main.light1.shadow.camera.bottom = - 1000;
    // main.light1.shadow.camera.left = - 1000;
    // main.light1.shadow.camera.right = 1000;
    // main.scene.add(main.light1);

    // var hemiLight = new THREE.HemisphereLight(0xffeeb1, 0x080820, 1);
    // main.scene.add(hemiLight);

    // var light = new THREE.SpotLight(0xffffff,4);
    // light.position.set(-50,50,50);
    // light.castShadow = true;
    // main.scene.add( light );

    // const light = new THREE.DirectionalLight(0xffffff, 1, 500);
    // light.castShadow = true;
    // light.shadow.camera.top = 1000;
    // light.shadow.camera.bottom = - 1000;
    // light.shadow.camera.left = - 1000;
    // light.shadow.camera.right = 1000;
    // light.shadow.mapSize.width = 1000; // default
    // light.shadow.mapSize.height = 1000; // default
    // // light.shadow.camera.near = 0.5; // default
    // light.shadow.camera.far = 2000; // default

    // light.position.set(0, 500, 500);
    // light.target.position.set(-5, 0, 0);
    // main.scene.add(light);

    // const helper1 = new THREE.CameraHelper( main.lightControl.light1.shadow.camera );
    // main.scene.add( helper1 );
    // const helper2 = new THREE.CameraHelper( main.lightControl.light2.shadow.camera );
    // main.scene.add( helper2 );
    // const helper3 = new THREE.CameraHelper( main.lightControl.light3.shadow.camera );
    // main.scene.add( helper3 );

    // var light = new THREE.SpotLight(0xffffff,4);
    // light.position.set(-50,50,50);
    // light.castShadow = true;
    // main.scene.add( light );

    // const light = new THREE.PointLight(0xffffff, 1, 500);
    // light.position.set(-200, 200, 200);
    // main.scene.add(light);

    // const light1 = new THREE.PointLight(0xffffff, 1, 500);
    // light1.position.set(200, 200, 200);
    // main.scene.add(light1);

    // const light2= new THREE.PointLight(0xffffff, 1, 500);
    // light2.position.set(200, 200, -200);
    // main.scene.add(light2);

    // const light3 = new THREE.PointLight(0xffffff, 1, 500);
    // light3.position.set(-200, 200, -200);
    // main.scene.add(light3);

    // const light4 = new THREE.PointLight(0xffffff, 1, 500);
    // light4.position.set(-300, 200, 0);
    // main.scene.add(light4);

    // const helper = new THREE.PointLightHelper(light);
    // main.scene.add(helper);
  };

  main.fnInitElement = function () {
    document.getElementById(main.elemID).appendChild(main.renderer.domElement);

    main.cameraControl = new CameraControl(
      main.scene,
      main.camera,
      main.controls,
      main
    );
  };

  main.fnInitControl = function () {
    // main.controls = new THREE.OrbitControls(main.camera, main.renderer.domElement);
    // main.controls.update();
  };

  main.fnInitEvent = function () {
    $("#export-gltf-icon").click(() => {
      Swal.fire({
        title: "Save As Gltf",
        html: `File Name <input id="gltf-file-name">`,
        showCancelButton: true,
        allowOutsideClick: false,
        preConfirm: () => {
          const fileName =
            Swal.getPopup().querySelector("#gltf-file-name").value;
          if (!fileName) {
            Swal.showValidationMessage(`Please input filename`);
          }
          return fileName;
        },
      }).then((result) => {
        if (!result.value) return;

        const exporter = new THREE.GLTFExporter();
        exporter.parse(main.scene, (done) => {
          let downloadLink = document.createElement("a");
          downloadLink.setAttribute("download", result.value + ".gltf");
          downloadLink.setAttribute("href", "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(done)));
          downloadLink.click();
        });
      });
    });

    $("#export-scene-dae-button").click(() => {
      Swal.fire({
        title: "Save As Obj",
        html: `File Name <input id="dae-file-name">`,
        showCancelButton: true,
        allowOutsideClick: false,
        preConfirm: () => {
          const fileName =
            Swal.getPopup().querySelector("#dae-file-name").value;
          if (!fileName) {
            Swal.showValidationMessage(`Please input filename`);
          }
          return fileName;
        },
      }).then((result) => {
        if (!result.value) return;

        const exporter = new THREE.ColladaExporter();
        const resultObj = exporter.parse(main.scene, null, {
          upAxis: "Y_UP",
          unitName: "millimeter",
          unitMeter: 0.001,
        });

        let downloadLink = document.createElement("a");
        downloadLink.setAttribute("download", result.value + ".dae");
        downloadLink.setAttribute(
          "href",
          URL.createObjectURL(
            new Blob([resultObj.data], { type: "text/plain" })
          )
        );
        downloadLink.click();
        console.log(resultObj);
        resultObj.textures.forEach((tex) => {
          console.log(tex);
        });
      });
    });

    $("#share-icon-button").click(() => {
      const exD = main.scenes.getCurrentExportData();
      fetch("upload_scene.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(exD),
      })
        .then((response) => response.json())
        .then(({ success, link }) => {
          if (success) {
            Swal.fire({
              title: "Share Plan via link",
              html: `<input id='share_link' type='text' style="width: 100%" value='${window.location.origin}${window.location.pathname}?shared=${link}' />`,
              showCancelButton: false,
              confirmButtonText: "Copy",
              reverseButtons: false,
            }).then((clicked) => {
              if (clicked.isConfirmed) {
                const copyText = document.querySelector("#share_link");
                copyText.select();
                document.execCommand("copy");

                Swal.fire("Copied!");
              }
            });
          } else {
            Swal.fire("Failed !");
          }
        });
    });

    $("#share-frame-button").click(() => {
      const exD = main.scenes.getCurrentExportData();
      fetch("upload_scene.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(exD),
      })
        .then((response) => response.json())
        .then(({ success, link }) => {
          if (success) {
            Swal.fire({
              title: "Please copy this code snippet in html",
              html: `<textarea id='share_iframe' style="width: 100%"><iframe title="EventDraw" width=800 height=600 src="${window.location.origin}${window.location.pathname}?shared=${link}" title="description"/></textarea>`,
              showCancelButton: false,
              confirmButtonText: "Copy",
              reverseButtons: false,
            }).then((clicked) => {
              if (clicked.isConfirmed) {
                const copyText = document.querySelector("#share_iframe");
                copyText.select();
                document.execCommand("copy");

                Swal.fire("Copied!");
              }
            });
          } else {
            Swal.fire("Failed !");
          }
        });
    });

    $("#export-scene-icon").click(() => {
      main.scenes.exportData("data.eventdraw");
    });

    $("#import-scene-icon").click(() => {
      $("#attachment").click();
    });

    $("#mode-selection div[data-mode]").click(function () {
      const mode = $(this).attr("data-mode");
      main.cameraControl.setMode(mode);
    });

    $("#mode-selection #lock").click(function () {
      const isLock = $(this).hasClass("lock");
      main.isLock = !isLock;
      main.settingPosSize.setIsLock(!isLock);
      main.settingPosSize.unSelectedModels();

      if (isLock) {
        $(this).removeClass('lock').addClass('unlock');
        $(this).attr('data-title', 'Unlock');
      } else {
        $(this).removeClass('unlock').addClass('lock');
        $(this).attr('data-title', 'Lock');
      }
    });

    const onMouseMove = (event) => {
      main.mouse.x = (event.clientX / main.screen_width) * 2 - 1;
      main.mouse.y = -(event.clientY / main.screen_height) * 2 + 1;

      if (main.msmControl && main.msmControl.mode) {
        main.msmControl.onMouseMove(event);
        return;
      }

      if (main.selectedGroup && main.isDragObj) {
        if (main.dragObjectControl.getOption() === 0) {
          const vector = new THREE.Vector3(main.mouse.x, main.mouse.y, 0.5);
          vector.unproject(main.camera);
          const dir = vector.sub(main.camera.position).normalize();
          const distance = -main.camera.position.z / dir.z;
          const pos = main.camera.position
            .clone()
            .add(dir.multiplyScalar(distance));

          const plane = new THREE.Plane();
          const planeNormal = new THREE.Vector3();
          const point = new THREE.Vector3();

          const target = new THREE.Vector3();
          main.selectedGroup.getWorldPosition(target);

          planeNormal.copy(main.camera.position).normalize();
          planeNormal.y = 0;

          plane.setFromNormalAndCoplanarPoint(planeNormal, target);
          main.raycaster.setFromCamera(main.mouse, main.camera);
          main.raycaster.ray.intersectPlane(plane, point);

          if (point.y > 0) {
            // main.selectedGroup.position.y = point.y;
            main.dragObjectControl.setHeight(point.y);

            main.selectedGroup.updateWorldMatrix(true, false);
            main.selectedGroup.getWorldPosition(main.tempV);
            main.tempV.project(main.camera);

            const x = (main.tempV.x * 0.5 + 0.5) * main.screen_width;
            const y = (main.tempV.y * -0.5 + 0.5) * main.screen_height;

            main.dragObjectControl.setVisible(x, y);
            main.isDragObj = true;
          }
        } else if (main.dragObjectControl.getOption() === 1) {
          const vector = new THREE.Vector3(main.mouse.x, main.mouse.y, 0.5);
          vector.unproject(main.camera);
          const dir = vector.sub(main.camera.position).normalize();
          const distance = -main.camera.position.y / dir.y;
          const pos = main.camera.position
            .clone()
            .add(dir.multiplyScalar(distance));

          main.selectedGroup.position.x = pos.x;
          main.selectedGroup.position.z = pos.z;
        }
      } else if (main.settingPosSize.selectedModels.length > 0 && main.isDragObj) {
        main.cameraControl.controls.enabled = false;
        main.raycaster.setFromCamera(main.mouse, main.camera);
        main.raycaster.ray.intersectPlane(main.plane, main.planeIntersect);
        const count = main.settingPosSize.selectedModels.length;
        for (let i = 0; i < count; i++) {
          main.settingPosSize.selectedModels[i].data.position.addVectors(main.planeIntersect, main.shift[i]);
        }
      } else if (main.isPan) {
        // main.cameraControl.pan(event);
      } else {
        if (main.isMouseDown) {
          main.cameraControl.controls.enabled = false;

          main.raycaster.setFromCamera(main.mouse, main.camera);
          if (
            !main.settingWall.moveArt(main.raycaster, main.width, main.height)
          ) {
            if (main.cameraControl.mode !== "tour")
              main.cameraControl.controls.enabled = true;
          } else {
            main.settingFloor.moveArt(
              main.raycaster,
              main.settingWall.activedArt,
              main.width,
              main.height
            );

            // if (main.cameraControl.mode !== "tour")
            //     main.cameraControl.controls.enabled = true;
          }
        }
      }
    };

    function getValidParent(obj) {
      while (obj.parent) {
        if (main.onOffCubes.includes(obj.parent)) {
          return obj.parent; // Found a valid parent in onOffCubes
        }
        obj = obj.parent;
      }
      return null; // No valid parent found
    }

    //https://jsfiddle.net/xa9uscme/1/
    const onMouseDown = (event) => {
      if (main.isLock) return;

      if (main.msmControl && main.msmControl.mode) {
        main.msmControl.onMouseDown(event);
        return;
      }
      // main.video.play();
      if (event.which === 1) {
        main.raycaster.setFromCamera(main.mouse, main.camera);

        const checkedWall = main.settingWall.checkWall(main.raycaster);
        const checkedArt = main.settingWall.checkArt(main.raycaster);

        if (checkedArt) {
          main.isMouseDown = true;
          return;
        }

        const intersects = main.raycaster.intersectObjects(
          main.scene.children,
          true
        );

        if (intersects.length > 0) {
          const intersectedParent = getValidParent(intersects[0].object);
          if (intersectedParent) {
            if (!event.ctrlKey || event.which !== 1) {
              main.settingPosSize.unSelectedModels();
            }
            main.settingPosSize.setSelectedModel(intersectedParent);
            main.shift = [];
            const count = main.settingPosSize.selectedModels.length;
            main.pIntersect.copy(intersects[0].point);
            main.plane.setFromNormalAndCoplanarPoint(main.pNormal, main.pIntersect);
            for (let i = 0; i < count; i++) {
              const shift = new THREE.Vector3();
              shift.subVectors(main.settingPosSize.selectedModels[i].data.position, intersects[0].point);
              if (typeof main.settingPosSize.selectedModels[i].data['setOldPosition'] === "function") {
                main.settingPosSize.selectedModels[i].data['setOldPosition']();
              }
              main.shift.push(shift);
            }
            main.selectedGroup = null;
            main.isDragObj = true;
          }
        }

        if (!main.isDragObj) {
          main.dragObjectControl.setInvisible();
          main.dragObjectControl.unsetSelectedModel();
          main.settingPosSize.unSelectedModels();
        }

        main.isMouseDown = true;
        // main.cameraControl.mouseDown(event);
      } else if (event.which === 3) {
        main.isPan = true;
        // main.cameraControl.mouseRightDown(event);
      }
    };

    const onMouseUp = (event) => {
      if (main.msmControl && main.msmControl.mode) {
        main.msmControl.onMouseUp(event);
        return;
      }
      main.cameraControl.controls.enabled = true;
      main.isDragObj = false;
      main.isMouseDown = false;
      // main.cameraControl.mouseUp(event);
      main.isPan = false;
      // main.cameraControl.mouseRightUp(event);
      const count = main.settingPosSize.selectedModels.length;
      for (let i = 0; i < count; i++) {
        const mesh = main.settingPosSize.selectedModels[i].data;
        if (mesh.oldPosition && !mesh.position.equals(mesh.oldPosition)) {
          commandManager.executeCommand(mesh, 'setPosition', mesh.oldPosition.clone(), mesh.position.clone());
        }
      }
    };

    main.renderer.domElement.addEventListener(
      "pointermove",
      onMouseMove,
      false
    );
    main.renderer.domElement.addEventListener(
      "pointerdown",
      onMouseDown,
      false
    );

    main.renderer.domElement.addEventListener("pointerup", onMouseUp, false);

    document.addEventListener("contextmenu", (event) => event.preventDefault());
    window.addEventListener("resize", (event) => {
      main.camera.aspect = window.innerWidth / window.innerHeight;
      main.camera.updateProjectionMatrix();
      main.renderer.setSize(window.innerWidth, window.innerHeight);
    });
  };

  main.createFloor = function (x, y) {
    const cw = x;
    const ch = y;

    const color = 0xffffff;
    const planeGeometry = new THREE.PlaneBufferGeometry(cw, ch);
    let planeMaterial;
    let texture = null;

    if (main.image) {
      const loader = new THREE.TextureLoader();
      texture = loader.load(main.image);
      texture.wrapS = THREE.ClampToEdgeWrapping;
      texture.wrapT = THREE.ClampToEdgeWrapping;
      texture.magFilter = THREE.NearestFilter;
      texture.repeat.set(1, 1);
      planeMaterial = new THREE.MeshStandardMaterial({
        color: color,
        map: texture,
        side: THREE.DoubleSide,
        flatShading: true,
      });
    } else {
      planeMaterial = new THREE.MeshStandardMaterial({
        color: color,
        side: THREE.DoubleSide,
        flatShading: true,
      });
    }

    plane = new THREE.Mesh(planeGeometry, planeMaterial);
    plane.receiveShadow = true;

    plane.rotation.x = -0.5 * Math.PI;

    if (main.realistic) {
      const realPlane = new BuildingModel(
        main.getParameterByName("realistic"),
        main.cameraControl
      );
      realPlane.init(x, y);
      this.floorGroup.add(realPlane);
    } else {
      this.floorGroup.add(plane);
    }

    main.settingFloor.setFloorObject(plane, cw, ch, texture);
  };

  main.placeShape = function (model_name, x, y, z) {
    main.scenes.forEach((scene) => {
      if (scene.name === model_name) {
        main.mtlLoader.load(scene.file + ".mtl", function (materials) {
          materials.preload();

          main.objLoader
            .setMaterials(materials)
            .load(scene.file + ".obj", function (object) {
              object.position.set(x, y, z);

              main.scene.add(object);
            });
        });
      }
    });
  };

  main.placeWindow = function (wall_name, x, y, z) {
    main.store.loadWindow("sdfsdfsd", x, y, z, main.scene);
  };

  main.fnRender = function () {
    if (main.settingWall) main.settingWall.fnUpdate();
    main.renderer.render(main.scene, main.camera);

    // main.cameraControl.controls.update(clock.getDelta());
    // main.controls.update();
    main.cameraControl.fnRender();
    requestAnimationFrame(main.fnRender);
  };

  main.loadModel = async function (settings) {
    // console.log("Loading Model in Engine");

    // Swal.fire({
    //   title: 'Select Scene Mode',
    //   input: 'select',
    //   inputOptions: {
    //     '0': 'Transparent',
    //     '1': 'Realistic',
    //   },
    //   inputPlaceholder: 'Select Scene Mode',
    //   showCancelButton: true,
    //   // inputValidator: async function (value) {
    //   //   return new Promise(function (resolve, reject) {
    //   //     if (value === '0') {
    //   //       resolve()
    //   //     } else {
    //   //       reject('You need to select Ukraine :)')
    //   //     }
    //   //   })
    //   // }
    // }).then(function (result) {
    // console.log(result)
    // const mode = result.value ? result.value : "0";

    Swal.fire({
      type: "success",
      // html: `You selected ${main.realistic ? "Realistic" : "Common"}`
      html: "Transparent scene selected",
    });

    main.settingPosSize.setData(settings);

    const { groups, shapes, imageInformation } = settings;
    const layout = imageInformation ? imageInformation.layout : (settings.layout || {});
    const defaultWidth = (typeof layout.width === "number" && layout.width > 0) ? layout.width : 1000;
    const defaultHeight = (typeof layout.height === "number" && layout.height > 0) ? layout.height : 1000;

    main.image = (imageInformation && imageInformation.layoutImage) ? imageInformation.layoutImage : null;
    main.shapes = shapes;
    main.createFloor(defaultWidth, defaultHeight);
    main.parentGroup.position.set(-defaultWidth / 2, 0, -defaultHeight / 2);

    main.width = defaultWidth;
    main.height = defaultHeight;

    (groups || []).forEach((element) => {
      var group = new THREE.Group();
      group.name = element.id;
      main.parentGroup.add(group);
      element.model = group;
      main.setTree(element, group);
    });

    main.settingWall.createHoleAndDoor();
    main.setPlatform();
    
    const aspect = main.screen_width / main.screen_height;
    const fovRadians = main.camera.fov * (Math.PI / 180);
    const distanceV = (main.height / 2) / Math.tan(fovRadians / 2);
    const hFOV = 2 * Math.atan(Math.tan(fovRadians / 2) * aspect);
    const distanceH = (main.width / 2) / Math.tan(hFOV / 2);
    const distance = Math.max(distanceV, distanceH);
    main.camera.position.y = distance;

    main.settingPosSize.updateTransparent();
  };

  main.setTree = function (tree, group) {
    // console.log(tree);
    if (tree.type === "shape") {
      let shape = main.getShape(tree.id);

      if (!shape) return;
      if (shape.name === "Wall Horizontal") {
        if (!main.realistic) {
          const gp = new HorizontalWallModel(shape);
          gp.init(shape, 3 * 39.37);
          group.add(gp);
          main.settingWall.fnSetWallGroups(gp);
          tree.data = gp;
        }
      } else if (shape.name === "Wall") {
        // console.log("detected wall");
        // console.log(shape);
        // this.model.addWall(shape.points);
      } else if (shape.name === "Pillar") {
        const gp = main.store.createPilor(shape);
        group.add(gp);
      } else if (shape.name === "Text") {
        const gp = main.store.createText(shape);
        main.textControl.addText(gp);
        group.add(gp);
      } else {
        if (
          shape.name === "Single_Door" ||
          shape.name === "Double_Door" ||
          shape.name === "Window_1"
        ) {
          // Disable Door and Window for realistic scene
          if (!main.realistic) {
            const gp = main.store.getModel(shape, group, main.textControl);
            gp.userData = { id: shape.id, position: shape.position };
            main.onOffCubes.push(gp);
            group.add(gp);
            tree.data = gp;

            gp.userData = {
              id: shape.id,
              position: shape.position,
              dimension: shape.dimension,
            };
            main.settingWall.fnAddDoor(gp);
          }
        } else {
          const gp = main.store.getModel(shape, group, main.textControl);
          gp.userData = { id: shape.id, position: shape.position };
          main.onOffCubes.push(gp);
          group.add(gp);
          tree.data = gp;
        }
      }
    } else {
      const gp = new THREE.Group();
      gp.name = tree.id;
      group.add(gp);

      tree.data = gp;

      for (let i = 0; i < tree.children.length; i++) {
        if (tree.children[i]) main.setTree(tree.children[i], group);
      }
    }
  };

  main.getShape = function (id) {
    for (let i = 0; i < main.shapes.length; i++) {
      if (main.shapes[i].id === id) {
        return main.shapes[i];
      }
    }

    return false;
  };

  main.createPilor = function (shape, group) {
    const triangleShape = new THREE.Shape();
    triangleShape.moveTo(-20, -20);
    triangleShape.lineTo(-20, 20);
    triangleShape.lineTo(20, 20);
    triangleShape.lineTo(20, -20);
    triangleShape.lineTo(-20, -20);

    // Create a new geometry by extruding the triangleShape
    // The option: 'amount' is how far to extrude, 'bevelEnabled: false' prevents beveling
    const extrusionSettings = {
      curveSegments: 5,
      steps: 10,
      depth: 0,
      bevelEnabled: true,
      bevelThickness: 80,
      bevelSize: 0,
      bevelSegments: 8,
      material: 0,
      extrudeMaterial: 1,
    };

    const geometry = new THREE.ExtrudeGeometry(
      triangleShape,
      extrusionSettings
    );

    const material = new THREE.MeshBasicMaterial({
      color: 0xd6d6d6, // red
      transparent: true,
      side: THREE.DoubleSide,
      opacity: 1, // 0.4
    });

    // Geometry doesn't do much on its own, we need to create a Mesh from it
    const extrudedMesh = new THREE.Mesh(geometry, material);
    extrudedMesh.rotation.x = Math.PI / 2;
    extrudedMesh.position.y = 80;
    main.scene.add(extrudedMesh);

    const circleRadius = 30;
    const circleShape = new THREE.Shape();
    circleShape.moveTo(circleRadius, 0);
    circleShape.absarc(0, 0, circleRadius, 0, 2 * Math.PI, false);

    const geometry1 = new THREE.ExtrudeGeometry(circleShape, extrusionSettings);

    // Geometry doesn't do much on its own, we need to create a Mesh from it
    const circleExtrudedMesh = new THREE.Mesh(geometry1, material);
    circleExtrudedMesh.rotation.x = Math.PI / 2;

    circleExtrudedMesh.position.x = -300;
    circleExtrudedMesh.position.y = 80;
    main.scene.add(circleExtrudedMesh);
  };

  main.createWall = function () {
    const shape = new THREE.Shape();

    const thin = 5;
    const width = 400;
    const height = 300;

    shape.moveTo(-width, -height);
    shape.lineTo(-width, height);
    shape.lineTo(width, height);
    shape.lineTo(width, -height);
    shape.lineTo(-(width - thin), -height);
    shape.lineTo(-(width - thin), -(height - thin));
    shape.lineTo(-(width - thin), -(height - thin));
    shape.lineTo(width - thin, -(height - thin));
    shape.lineTo(width - thin, height - thin);
    shape.lineTo(-(width - thin), height - thin);
    shape.lineTo(-(width - thin), -height);
    shape.lineTo(-width, -height);

    const extrusionSettings = {
      curveSegments: 5,
      steps: 10,
      depth: 0,
      bevelEnabled: true,
      bevelThickness: 80,
      bevelSize: 0,
      bevelSegments: 8,
      material: 0,
      extrudeMaterial: 1,
    };

    const geometry = new THREE.ExtrudeGeometry(shape, extrusionSettings);
    // const material = new THREE.MeshBasicMaterial( { color: 0x00ff00 } );
    const material = new THREE.MeshBasicMaterial({
      color: 0xd6d6d6, // red
      transparent: true,
      side: THREE.DoubleSide,
      opacity: 1, // 0.4
    });

    const mesh = new THREE.Mesh(geometry, material);

    mesh.rotation.x = Math.PI / 2;
    mesh.position.y = 80;
    main.scene.add(mesh);
  };

  main.switchCamera = function (type) {
    if (type) {
      main.camera = main.camera1;
    } else {
      main.camera = main.camera2;
    }

    main.cameraControl.setCamera(main.camera);
  };

  main.test = function () {
    // const div = document.createElement( 'div' );
    // div.style.width = '480px';
    // div.style.height = '360px';
    // div.style.backgroundColor = '#000';

    // const iframe = document.createElement( 'iframe' );
    // iframe.style.width = '480px';
    // iframe.style.height = '360px';
    // iframe.style.border = '0px';
    // iframe.src = [ 'https://www.youtube.com/embed/tgbNymZ7vqY'];
    // div.appendChild( iframe );

    // const object = new THREE.CSS3DObject( div );
    // object.position.set( 0, 0, 0 );
    // object.rotation.y = 0;

    // main.cssScene = new THREE.Scene();
    // main.cssScene.add(object);

    // main.renderer2 = new THREE.CSS3DRenderer();
    // main.renderer2.setSize( window.innerWidth, window.innerHeight );
    // main.renderer2.domElement.style.position = 'absolute';
    // main.renderer2.domElement.style.zIndex = 0;
    // main.renderer2.domElement.style.top = 0;

    // document.getElementById(main.elemID).appendChild(main.renderer2.domElement);
    // main.renderer2.domElement.appendChild(main.renderer.domElement);

    // document.querySelector('#css').appendChild( main.renderer2.domElement );

    // test canvas
    // main.video1 = document.createElement("canvas");
    // main.video1.width = 400;
    // main.video1.height = 300;
    // document.body.appendChild(main.video1);
    // main.tempcanvasctx = main.video1.getContext('2d');

    // main.video = document.createElement('video');
    // main.video.src = "assets/video/sintel.ogv";
    // main.video.load();

    // var videocanvas = document.createElement('canvas');
    // videocanvas.width = 400;
    // videocanvas.height = 300;

    // main.videocanvasctx = videocanvas.getContext('2d');
    // main.videocanvasctx.fillStyle = "#ff0000";
    // main.videocanvasctx.fillRect(0,0,400,300);

    // main.videoTexture = new THREE.Texture(videocanvas);
    // main.videoTexture.minFilter = THREE.LinearFilter;
    // main.videoTexture.magFilter = THREE.LinearFilter;

    // var movieMaterial = new THREE.MeshBasicMaterial({
    //     side: THREE.DoubleSide,
    //     overdraw: 0.5
    // });
    // movieMaterial.map = main.videoTexture;

    // var movieGeometry = new THREE.PlaneGeometry(240, 100, 4, 4);

    // var movieGeometry = new THREE.Mesh(movieGeometry, movieMaterial);

    // main.scene.add(movieGeometry);

    const group = new THREE.Group();

    // test code for substract operation

    // const primaryCubeGeometry = new THREE.BoxGeometry();
    // const primaryMaterial = new THREE.MeshPhongMaterial({ color: 0x6666FF });
    // const primaryCube = new THREE.Mesh(primaryCubeGeometry, primaryMaterial);

    // const secondaryCubeGeometry = new THREE.BoxGeometry(0.75, 0.75, 0.75);
    // const secondartMaterial = new THREE.MeshPhongMaterial({ color: 0xFFAFAF });
    // const secondaryCube = new THREE.Mesh(secondaryCubeGeometry, secondartMaterial);

    // const placeholderCubeGeometry = new THREE.BoxBufferGeometry(0.75, 0.75, 0.75, 1, 1, 1);
    // const placeholderEdges = new THREE.EdgesGeometry(placeholderCubeGeometry);
    // const placeholderCube = new THREE.LineSegments(placeholderEdges, new THREE.LineBasicMaterial({ color: 0xFFAFAF }));

    // secondaryCube.position.set(-0.3, 0.3, 0);
    // placeholderCube.position.set(-0.3, 0.3, 0);

    // // use csg to perform subtraction/boolean operation
    // const csgPrimaryCube = new ThreeBSP(primaryCube);
    // const csgSecondaryCube = new ThreeBSP(secondaryCube);

    // const subtraction = csgPrimaryCube.subtract(csgSecondaryCube);
    // const subtractionMesh = subtraction.toMesh();

    // subtractionMesh.material = primaryMaterial;

    // // add shapes to scene
    // group.add(subtractionMesh);
    // group.add(placeholderCube);

    // var geometry = new THREE.BoxGeometry(200, 2, 200);
    // var materials = new THREE.MeshLambertMaterial({color: 0xb0cee0,side:THREE.DoubleSide})
    // var result = new THREE.Mesh(geometry,materials);

    // var totalBSP = new ThreeBSP(result);
    // var holeGeometry = new THREE.BoxGeometry(100, 100, 100);
    // var holeCube = new THREE.Mesh( holeGeometry);

    // var clipBSP = new ThreeBSP(holeCube);
    // var resultBSP = totalBSP.subtract(clipBSP);
    // result = resultBSP.toMesh();

    // result.material = materials;
    // group.add(result);

    // // group.scale.x = 100;
    // // group.scale.y = 100;
    // // group.scale.z = 100;
    // group.position.y = 100;

    // main.scene.add(group);

    // const placeholderCubeGeometry = new THREE.BoxBufferGeometry(3000, 100, 3000, 1, 1, 1);
    // const placeholderEdges = new THREE.EdgesGeometry(placeholderCubeGeometry);
    // const placeholderCube = new THREE.LineSegments(placeholderEdges, new THREE.LineBasicMaterial({ color: 0xFFAFAF }));
    // main.scene.add(placeholderCube);
  };

  main.fnLoadBuilding = function () {
    console.log("fnLoadBuilding");

    let gltfLoader = new THREE.GLTFLoader();
    const dracoLoader = new THREE.DRACOLoader();
    dracoLoader.setDecoderPath("./assets/js/draco/");
    gltfLoader.setDRACOLoader(dracoLoader);

    const glod_diffusemaps = new THREE.TextureLoader().load(
      "assets/textures/building/gold/gold_leaf_fine_Base_Color.png"
    );
    const glod_normalMap = new THREE.TextureLoader().load(
      "assets/textures/building/gold/gold_leaf_fine_Normal.png"
    );
    const glod_roughnessMap = new THREE.TextureLoader().load(
      "assets/textures/building/gold/gold_leaf_fine_Roughness.png"
    );
    const glod_metalnessMap = new THREE.TextureLoader().load(
      "assets/textures/building/gold/gold_leaf_fine_Metallic.png"
    );
    const gold_bumpMap = new new THREE.TextureLoader().load(
      "assets/textures/building/gold/gold_leaf_fine_Height.png"
    );
    const glod_specularMap = new THREE.TextureLoader().load(
      "assets/textures/building/gold/gold_leaf_fine_Specular.png"
    );

    // var glod_Material = new THREE.MeshStandardMaterial({
    //     // color: 0xff0000,
    //     side: THREE.DoubleSide,
    //     normalMap: glod_normalMap,
    //     roughnessMap: glod_roughnessMap,
    //     map: glod_diffusemaps,
    //     shininess: 100,
    //     metalness: .2,
    // });

    const macael_diffusemaps = new THREE.TextureLoader().load(
      "assets/textures/building/macael/macael_marble_grid_tiles_Diffuse.png"
    );
    macael_diffusemaps.wrapT = 1000;
    macael_diffusemaps.wrapS = 1000;
    macael_diffusemaps.repeat.set(10, 10);
    const macael_normalMap = new THREE.TextureLoader().load(
      "assets/textures/building/macael/macael_marble_grid_tiles_Normal.png"
    );
    macael_normalMap.wrapT = 1000;
    macael_normalMap.wrapS = 1000;
    macael_normalMap.repeat.set(10, 10);
    const macael_roughnessMap = new THREE.TextureLoader().load(
      "assets/textures/building/macael/macael_marble_grid_tiles_Roughness.png"
    );
    macael_roughnessMap.wrapT = 1000;
    macael_roughnessMap.wrapS = 1000;
    macael_roughnessMap.repeat.set(10, 10);
    const macael_metalness = new THREE.TextureLoader().load(
      "assets/textures/building/macael/macael_marble_grid_tiles_Metallic.png"
    );
    macael_metalness.wrapT = 1000;
    macael_metalness.wrapS = 1000;
    macael_metalness.repeat.set(10, 10);

    const wallpaper_diffusemaps = new THREE.TextureLoader().load(
      "assets/textures/building/wallpaper/wallpaper_lotus_pattern_Diffuse.png"
    );
    wallpaper_diffusemaps.wrapT = 1000;
    wallpaper_diffusemaps.wrapS = 1000;
    wallpaper_diffusemaps.repeat.set(3, 3);

    const wallpaper_normalMap = new THREE.TextureLoader().load(
      "assets/textures/building/wallpaper/wallpaper_lotus_pattern_Normal.png"
    );
    wallpaper_normalMap.wrapT = 1000;
    wallpaper_normalMap.wrapS = 1000;
    wallpaper_normalMap.repeat.set(3, 3);

    const wallpaper_roughnessMap = new THREE.TextureLoader().load(
      "assets/textures/building/wallpaper/wallpaper_lotus_pattern_Roughness.png"
    );
    wallpaper_roughnessMap.wrapT = 1000;
    wallpaper_roughnessMap.wrapS = 1000;
    wallpaper_roughnessMap.repeat.set(3, 3);

    const wallpaper_metalnessMap = new THREE.TextureLoader().load(
      "assets/textures/building/wallpaper/wallpaper_lotus_pattern_Metallic.png"
    );
    wallpaper_metalnessMap.wrapT = 1000;
    wallpaper_metalnessMap.wrapS = 1000;
    wallpaper_metalnessMap.repeat.set(3, 3);

    // var macael_Material = new THREE.MeshStandardMaterial({
    //     // color: 0xff0000,
    //     side: THREE.DoubleSide,
    //     normalMap: glod_normalMap,
    //     roughnessMap: glod_roughnessMap,
    //     map: glod_diffusemaps,
    //     shininess: 100,
    //     metalness: .2,
    // });

    gltfLoader.load("assets/test/ballroom.gltf", (gltf) => {
      let tableModel = gltf.scene.clone();

      // console.log(gltf);
      tableModel.traverse((child) => {
        // console.log(child);
        if (child.isMesh) {
          child.material.depthWrite = true;
          // child.material.transparent = false;

          if (child.material.name === "Mat.1") {
            // child.material.side  = THREE.DoubleSide;
            // child.material.color = 0x999999;
            child.material.needsUpdate = true;
            child.material.metalness = 0.5;
            // console.log(child.material);
          } else if (child.material.name === "gold_natural") {
            child.material.map = glod_diffusemaps;
            child.material.normalMap = glod_normalMap;
            child.material.roughnessMap = glod_roughnessMap;
            child.material.metalnessMap = glod_metalnessMap;
            child.material.metalness = 0.5;

            child.material.needsUpdate = true;
          } else if (child.material.name === "wallpaper_lotus_pattern") {
            // console.log(child.material);
            child.material.map = wallpaper_diffusemaps;
            child.material.normalMap = wallpaper_normalMap;
            child.material.roughnessMap = wallpaper_roughnessMap;
            child.material.roughnessMap = wallpaper_roughnessMap;
            child.material.metalnessMap = wallpaper_metalnessMap;
            // child.material.side  = THREE.DoubleSide;
            child.material.metalness = 0.5;
            child.material.needsUpdate = true;
          } else if (child.material.name === "macael_marble_grid_tiles") {
            child.material.map = macael_diffusemaps;
            child.material.normalMap = macael_normalMap;
            child.material.roughnessMap = macael_roughnessMap;
            child.material.metalnessMap = macael_metalness;
            child.material.side = THREE.DoubleSide;

            child.material.needsUpdate = true;
          } else if (child.material.name === "default") {
            // child.material.map = macael_diffusemaps;
            // child.material.normalMap = macael_normalMap;
            // child.material.roughnessMap = macael_roughnessMap;
            // child.material.metalnessMap = macael_metalness;
            // child.material.needsUpdate = true;
            // var materials = new THREE.MeshStandardMaterial({
            //     color: 0x00ff00,// red
            //     transparent:true,
            //     side: THREE.DoubleSide,
            //     // opacity: 0.4
            // });
            // child.material = materials;
            // child.material.needsUpdate = true;
          } else if (child.material.name === "Glow White 2") {
            var materials = new THREE.MeshStandardMaterial({
              color: 0xffffff, // red
              transparent: true,
              side: THREE.DoubleSide,
            });

            child.material = materials;
            child.material.needsUpdate = true;
          } else if (child.material.name === "Privacy_sandblasted") {
            // child.material.map = macael_diffusemaps;
            // child.material.normalMap = macael_normalMap;
            // child.material.roughnessMap = macael_roughnessMap;
            // child.material.metalnessMap = macael_metalness;
            // child.material.needsUpdate = true;
            // var materials = new THREE.MeshStandardMaterial({
            //     color: 0x00ffff,// red
            //     transparent:true,
            //     side: THREE.DoubleSide,
            //     // opacity: 0.4
            // });
            // child.material = materials;
            // child.material.needsUpdate = true;
          } else {
            console.log(child.material);
          }
          // console.log(child.name);
          // if (child.name.includes("Sweep") || child.name.includes("Sphere") || child.name.includes("Cylinder") || child.name.includes("Ceiling")) {
          //     child.material = glod_Material;
          //     child.material.needsUpdate = true;
          // } else if (child.name == "Floor") {
          //     // console.log(child.material);
          //     child.material.map = macael_diffusemaps;
          //     child.material.normalMap = macael_normalMap;
          //     child.material.roughnessMap = macael_roughnessMap;
          //     // child.normalMap = macael_normalMap;
          //     child.material.needsUpdate = true;
          // // Mat1
          // } else if (child.name.includes("Rounding") || child.name.includes("Extrude") || child.name.includes("?????") || child.name.includes("Wall")) {
          //     // console.log("AAAAAAAA");
          //     // var materials = new THREE.MeshStandardMaterial({
          //     //     color: 0xff0000,// red
          //     //     transparent:true,
          //     //     side: THREE.DoubleSide,
          //     //     opacity: 0.4
          //     // });

          //     // child.material = materials;
          //     // child.material.needsUpdate = true;
          //     console.log(child.material);
          // // Wall Map
          // } else if (child.name.includes("Cap")) {
          //     child.material.map = wallpaper_diffusemaps;
          //     child.material.normalMap = wallpaper_normalMap;
          //     child.material.roughnessMap = wallpaper_roughnessMap;
          //     child.material.needsUpdate = true;
          // // Grow White2
          // } else if (child.name.includes("Light_Panel")) {

          // // Privacy sandblasted
          // } else if (child.name.includes("Oil_Tank")) {

          // } else {
          //     console.log(child.name);

          //     var materials = new THREE.MeshStandardMaterial({
          //         color: 0xff0000,// red
          //         transparent:true,
          //         side: THREE.DoubleSide,
          //         opacity: 0.4
          //     });

          //     child.material = materials;
          //     child.material.needsUpdate = true;
          // }
        }
      });

      tableModel.scale.x = 0.3;
      tableModel.scale.y = 0.3;
      tableModel.scale.z = 0.3;

      main.scene.add(tableModel);

      // const light = new THREE.PointLight( 0xff0000, 1, 100 );
      // light.position.set( 50, 50, 50 );
      // main.scene.add( light );
    });
  };

  main.getParameterByName = function (name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    const regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
      results = regex.exec(location.search);
    return results === null
      ? ""
      : decodeURIComponent(results[1].replace(/\+/g, " "));
  };

  main.fnInit();
};
