var ShapeModels = function (parameter) {
  const main = this;

  this.wallHeight = 300;
  this.loadedModel = 0;

  this.scenes = parameter;

  this.availableModelList = ["chair"];
  this.sceneLoaded = $.Callbacks();

  this.backendUrl = `https://3d.eventdraw.com.au/eventdraw_api/public/api/shapes/newversion?t=${Date.now().toString(
    36
  )}`;

  this.modelUrl = "https://3d.eventdraw.com.au/eventdraw_api/public/";

  main.fnInit = async function () {
    // main.fnLoadData();
    main.settings = await main.scenes.fnLoadData();
    main.shapes = main.settings.shapes;

    main.shapes.forEach((shape) => {
      shape.name = shape.name.trim();
      if (!main.availableModelList.includes(shape.name.trim())) {
        main.availableModelList.push(shape.name.trim());
      }
    });

    main.fnLoadData();

    // main.scenes.fireOnNewScene(main.fnReset);
  };

  main.fnReset = async () => {
    this.loadedModel = 0;
    const loadingDiv = document.createElement("div");
    loadingDiv.innerHTML = `
    <div class="counter ring">
      <p>Loading</p>
      <h1>O%</h1>
      <hr>
      <span></span>
    </div>
    `;
    loadingDiv.classList.add("loading-page");

    document.body.appendChild(loadingDiv);

    main.settings = main.scenes.settings;

    main.shapes = main.settings.shapes;

    main.shapes.forEach((shape) => {
      shape.name = shape.name.trim();
      if (!main.availableModelList.includes(shape.name.trim())) {
        main.availableModelList.push(shape.name.trim());
      }
    });

    main.fnLoadData();
  };

  main.getShapes = function () {
    return main.availableModelList;
  };

  main.getShapesInfo = function () {
    return main.models;
  };

  main.fnLoadData = async function () {
    let response = await fetch(this.backendUrl);

    // main.models = await response.json();
    const data = await response.json();

    // console.log(data);
    main.models = data.map((d) => ({
      ...d,
      type:
        d.shapeType === "Double_Door" ||
        d.shapeType === "Single_Door" ||
        d.shapeType === "Window_1"
          ? "door"
          : d.type,
    }));

    main.models.forEach((model) => {
      model.shapeType = model.shapeType.trim()
      if (!main.availableModelList.includes(model.name.trim())) {
        if (main.loading()) {
          this.engine.loadModel(main.settings);
        }

        return;
      }

      const gltfLoader = new THREE.GLTFLoader();
      const dracoLoader = new THREE.DRACOLoader();
      dracoLoader.setDecoderPath("./assets/js/draco/");
      gltfLoader.setDRACOLoader(dracoLoader);

      gltfLoader.load(
        // this.modelUrl + encodeURIComponent(model.file) + ?t=1213213"",
        `${this.modelUrl}${encodeURIComponent(
          model.file
        )}?t=${Date.now().toString(36)}`,
        (gltf) => {
          let tableModel = gltf.scene.clone();

          tableModel.traverse((child) => {
            if (child.isMesh) {
              // var materials = new THREE.MeshStandardMaterial({
              //     color: 0xd6d6d6,// red
              //     transparent:true,
              //     side: THREE.DoubleSide,
              //     opacity: 0.4
              // });
              // child.material = materials;
              // child.material.needsUpdate = true;
            }
          });

          var size = new THREE.Vector3();
          var boundingBox = new THREE.Box3().setFromObject(tableModel);
          boundingBox.getSize(size);

          if (model.name == "Window_1") {
            tableModel.traverse((child) => {
              if (child.name == "14330_30X49_Double_Hung_Window-Pine_v1_l1_1") {
                var materials = new THREE.MeshLambertMaterial({
                  color: 0xffffff,
                  side: THREE.DoubleSide,
                  opacity: 1, // 0.1 
                  transparent: true,
                });
                child.material = materials;
                child.material.needsUpdate = true;
              } else {
                var materials = new THREE.MeshLambertMaterial({
                  color: 0x777777,
                  side: THREE.DoubleSide,
                  opacity: 1, // 0.7
                  transparent: true,
                });
                child.material = materials;
                child.material.needsUpdate = true;
              }
            });
          }

          if (model.name == "Double_Door") {
            tableModel.traverse((child) => {
              if (child.name == "16687_72x96_Double_French_Door-Cherry_V1") {
                var materials = new THREE.MeshLambertMaterial({
                  color: 0xffffff,
                  side: THREE.DoubleSide,
                  opacity: 1, // 0.1
                  transparent: true,
                });
                child.material = materials;
                child.material.needsUpdate = true;
              } else if (
                child.name == "16687_72x96_Double_French_Door-Cherry_V1_3"
              ) {
                var materials = new THREE.MeshLambertMaterial({
                  color: 0xffffff,
                  side: THREE.DoubleSide,
                  opacity: 1, // 0.1
                  transparent: true,
                });
                child.material = materials;
                child.material.needsUpdate = true;
              } else if (
                child.name == "16687_72x96_Double_French_Door-Cherry_V1_2"
              ) {
                var materials = new THREE.MeshLambertMaterial({
                  color: 0x777777,
                  side: THREE.DoubleSide,
                  opacity: 1, //0.7
                  transparent: true,
                });
                child.material = materials;
                child.material.needsUpdate = true;
              }
            });
          }

          if (model.name == "Single_Door") {
            tableModel.traverse((child) => {
              if (child.name == "16688_36x96_French_Mullion_Door-Oak_V1") {
                var materials = new THREE.MeshLambertMaterial({
                  color: 0xffffff,
                  side: THREE.DoubleSide,
                  opacity: 1, // 0.4
                  transparent: true,
                });
                child.material = materials;
                child.material.needsUpdate = true;
              } else if (
                child.name === "16688_36x96_French_Mullion_Door-Oak_V1_1"
              ) {
                var materials = new THREE.MeshLambertMaterial({
                  color: 0xffffff,
                  side: THREE.DoubleSide,
                  opacity: 1, // 0.4
                  transparent: true,
                });
                child.material = materials;
                child.material.needsUpdate = true;
              } else if (
                child.name === "16688_36x96_French_Mullion_Door-Oak_V1_2"
              ) {
                var materials = new THREE.MeshLambertMaterial({
                  color: 0x777777,
                  side: THREE.DoubleSide,
                  opacity: 1, // 0.7
                  transparent: true,
                });
                child.material = materials;
                child.material.needsUpdate = true;
              } else if (
                child.name === "16688_36x96_French_Mullion_Door-Oak_V1_3"
              ) {
                var materials = new THREE.MeshLambertMaterial({
                  color: 0xffffff,
                  side: THREE.DoubleSide,
                  opacity: 1, // 0.4
                  transparent: true,
                });
                child.material = materials;
                child.material.needsUpdate = true;
              }
            });
          }

          if (model.name == "Banquet Chair Dark") {
            tableModel.traverse((child) => {
              if (child.isMesh) {
                if (child.name == "3DVG_Banquet_Chair_3") {
                  var materials = new THREE.MeshLambertMaterial({
                    color: 0x000000,
                    side: THREE.DoubleSide,
                    opacity: 1, // 0.4
                    transparent: true,
                  });
                  child.material = materials;
                  child.material.needsUpdate = true;
                }
              }
            });
          }

          if (
            model.name == "Generic Projector" ||
            model.name == "2265U 7ft" ||
            model.name == "2265U 8ft" ||
            model.name == "2265U 10ft" ||
            model.name == "2265U 13ft" ||
            model.name == "1470Ui 7ft"
          ) {
            tableModel.traverse((child) => {
              if (child.isMesh) {
                if (child.name == "3DVG_projetor &screen_10") {
                  var materials = new THREE.MeshLambertMaterial({
                    color: 0xffffff,
                    side: THREE.DoubleSide,
                    opacity: 1, // 0.1
                    transparent: true,
                  });
                  child.material = materials;
                  child.material.needsUpdate = true;
                }
              }
            });
          }

          if (model.name == "Stage_Piece_01") {
            tableModel.traverse((child) => {
              if (child.isMesh && child.name == "stagetop___2_x_1") {
                var materials = new THREE.MeshBasicMaterial({
                  color: 0x333333,
                  side: THREE.DoubleSide,
                  transparent: true,
                });
                child.material = materials;
                child.material.needsUpdate = true;
              }
            });
          }

          model.model = tableModel;
          model.size = size;

          if (main.loading()) {
            this.engine.loadModel(main.settings);
          }
        },
        (xhr) => {},
        (error) => {
          console.error("Load Model", model);
          console.log(this.modelUrl + encodeURIComponent(model.file));
        }
      );
    });
  };

  main.loading = function () {
    main.loadedModel++;

    $(".loading-page .counter h1").html(
      Math.floor((main.loadedModel * 100) / main.models.length) + "%"
    );
    $(".loading-page .counter hr").css(
      "width",
      Math.floor((main.loadedModel * 100) / main.models.length) + "%"
    );

    // console.log(main.loadedModel, main.models.length);
    if (main.loadedModel == main.models.length) {
      const loadingScreen = document.querySelector(".loading-page");
      loadingScreen.classList.add("fade-out");

      loadingScreen.addEventListener("transitionend", function (e) {
        e.target.remove();
      });
      
      main.sceneLoaded.fire();
      return true;
    }

    return false;
  };

  main.setEngine = function (engine) {
    this.engine = engine;
  };

  main.setScenes = function (scenes) {
    this.scenes = scenes;
  };

  main.getMeshByName = (name) => {
    // console.log(name, main.models);
    let savedModel = null;
    // console.log(main.models);
    // const testIndex = main.models.findIndex((d) => d.type === "door");
    // console.log(testIndex);

    main.models.every((model) => {
      if (model.name == name) {
        savedModel = model;
        return false;
      }
      return true;
    });

    return savedModel;
  };

  main.getModel = function (table_info, scene, textControl) {
    // let x = table_info.position.x;
    // let y = table_info.position.y;
    // let z = table_info.position.z;
    // console.log(table_info)
    let savedModel = main.getMeshByName(table_info.name);
    if (savedModel === null) {
      // console.log(table_info.name, table_info)
      // return new EventModel();
      const group = new EmptyModel();
      group.init(table_info, textControl);
      return group;
    }

    let group;
    // console.log(table_info.name, savedModel, savedModel.type);
    if (savedModel.type != "door") {
      group = new EventModel();
      if (savedModel.type == "table") {
        group = new TableObject();
      }
    } else {
      group = new WallModel();
    }

    // group.position.set(x, y, z);
    group.name = table_info.name;
    // console.log(savedModel);
    group.init(table_info, savedModel, main.getMeshByName("chair"));

    // main.models.forEach((model) => {
    //   if (model.name == table_info.name) {
    //     let tableModel = model.model.clone();
    //     tableModel.traverse((node) => {
    //       if (node.isMesh) {
    //         node.material = node.material.clone();
    //         node.castShadow = true;
    //       }
    //     });

    //     var size = tableModel.userData.size;
    //     const { dimension } = table_info;
    //     const { width, height, depth } = dimension;

    //     var t_W = width;
    //     var t_H = height;

    //     var size = model.size;
    //     if (model.type != "door") {
    //       var ratex = t_W / size.x;
    //       var ratey = t_H / size.z;
    //       var rateH = depth == 0 ? Math.min(ratex, ratey) : depth / size.y;
    //       // if (table_info.id == "gLhGem4pRPKaWnluh6HI-135") {console.error(depth, size, depth / size.y)}
    //       tableModel.scale.set(ratex, rateH, ratey);
    //       tableModel.rotation.y = (-Math.PI * table_info.rotation.y) / 180;
    //       tableModel.position.x = width / 2;
    //       tableModel.position.z = height / 2;

    //       if (model.type == "table") {
    //         const { show_table_number, chairs } = table_info;
    //         if (show_table_number === "1")
    //           main.createTableNumber(table_info, width, height, group);
    //         main.loadChair(chairs, x, y, z, group, scene, rateH);
    //       }

    //       if (model.default && depth == 0) {
    //         if (model.default.height) {
    //           const size = new THREE.Vector3();
    //           var boundingBox = new THREE.Box3().setFromObject(tableModel);
    //           boundingBox.getSize(size);

    //           var rateH = model.default.height / size.y;
    //           tableModel.scale.y *= rateH;
    //         }
    //         if (model.default.base) {
    //           group.position.y = model.default.base;
    //         }
    //       }

    //       group.add(tableModel);
    //     } else {
    //       var ratex = t_W / size.x;
    //       var rateH = 100 / size.y;

    //       tableModel.scale.set(ratex, rateH, 1);
    //       tableModel.rotation.y = (-Math.PI * table_info.rotation.y) / 180;
    //       tableModel.position.x = width / 2;
    //       tableModel.position.z = height / 2;

    //       tableModel.translateZ(height / 2 - 2);

    //       // Fix hole postition
    //       var materials1 = new THREE.MeshLambertMaterial({
    //         color: 0xff00ff,
    //         side: THREE.DoubleSide,
    //       });
    //       var holeGeometry = new THREE.BoxGeometry(width, 100, 30);
    //       var holeCube = new THREE.Mesh(holeGeometry, materials1);
    //       holeCube.userData = { type: "hole" };
    //       // holeCube.scale.set(ratex, rateH, ratey);
    //       holeCube.rotation.y = (-Math.PI * table_info.rotation.y) / 180;
    //       holeCube.position.copy(tableModel.position.clone());
    //       // holeCube.translateY(50);
    //       holeCube.position.y += 50;

    //       if (model.default && depth == 0) {
    //         if (model.default.height) {
    //           const size = new THREE.Vector3();
    //           var boundingBox = new THREE.Box3().setFromObject(tableModel);
    //           boundingBox.getSize(size);

    //           var rateH = model.default.height / size.y;
    //           group.scale.y *= rateH;
    //         }
    //         if (model.default.base) {
    //           group.position.y = model.default.base;
    //         }
    //       }

    //       group.add(holeCube);
    //       group.add(tableModel);
    //     }
    //   }
    // });

    return group;
  };

  main.loadWindow = function (window, cx, cy, cz, scene) {
    var mtlLoader = new THREE.MTLLoader();
    mtlLoader.setPath("assets/models/");
    mtlLoader.setMaterialOptions({ side: THREE.DoubleSide });
    var objLoader = new THREE.OBJLoader();
    objLoader.setPath("assets/models/");

    mtlLoader.load(
      "10057_wooden_door_v3_iterations-2.mtl",
      function (materials) {
        materials.preload();
        objLoader
          .setMaterials(materials)
          .load("10057_wooden_door_v3_iterations-2.obj", function (object) {
            object.rotation.x = Math.PI / 2;
            object.scale.x = 0.5;
            object.scale.y = 0.5;
            object.scale.z = 0.5;
            object.position.set(cx, cy, cz);
            scene.add(object);
          });
      }
    );
  };

  main.loadChair = function (chairs, cx, cy, cz, group, scene, rateH) {
    console.log("loadChair", chairs);

    chairs.forEach((chair) => {
      main.models.forEach((model) => {
        if (model.name == chair.name) {
          let obj = model.model.clone();
          obj.name = "chair";
          obj.traverse((node) => {
            if (node.isMesh) {
              node.material = node.material.clone();
            }
          });

          var size = new THREE.Vector3();
          var boundingBox = new THREE.Box3().setFromObject(obj);
          boundingBox.getSize(size);

          const { dimension } = chair;
          const { width, height } = dimension;

          var t_W = width;
          var rate = t_W / size.x;

          var rateH = model.default.height / size.y;

          obj.position.set(
            parseFloat(chair.position.x) + width / 2,
            chair.position.y,
            parseFloat(chair.position.z) + height / 2
          );
          obj.rotation.set(
            (Math.PI * chair.rotation.x) / 180,
            (-Math.PI * chair.rotation.y) / 180,
            (Math.PI * chair.rotation.z) / 180
          );
          obj.scale.set(rate, rateH, rate);
          group.add(obj);
        }
      });
    });
  };

  main.createWall = function (wall, group) {
    var wallGroup = new THREE.Group();

    const { dimension } = wall;
    if (dimension.width == 0 || dimension.height == 0) return wallGroup;

    const geometry = new THREE.BoxGeometry(
      dimension.width,
      this.wallHeight,
      dimension.height
    );

    const edges = new THREE.EdgesGeometry(geometry);
    const line = new THREE.LineSegments(
      edges,
      new THREE.LineBasicMaterial({ color: 0xffffff })
    );

    var material = new THREE.MeshStandardMaterial({
      color: 0xd6d6d6, // red
      transparent: true,
      side: THREE.DoubleSide,
      opacity: 1, // 0.4
    });

    const mesh = new THREE.Mesh(geometry, material);
    // line.position.set(parseFloat(wall.position.x) + dimension.width / 2, parseFloat(wall.position.y + this.wallHeight / 2), parseFloat(wall.position.z) + dimension.height / 2);
    // line.rotation.set(Math.PI * wall.rotation.x / 180, -Math.PI * wall.rotation.y / 180, Math.PI * wall.rotation.z / 180);

    // mesh.position.set(parseFloat(wall.position.x) + dimension.width / 2, parseFloat(wall.position.y) + this.wallHeight / 2, parseFloat(wall.position.z) + dimension.height / 2);
    // mesh.rotation.set(Math.PI * wall.rotation.x / 180, -Math.PI * wall.rotation.y / 180, Math.PI * wall.rotation.z / 180);
    wallGroup.add(line);
    wallGroup.add(mesh);

    mesh.castShadow = true;
    // mesh.receiveShadow = true;
    if (mesh.material.map) mesh.material.map.anisotropy = 16;

    wallGroup.position.set(
      parseFloat(wall.position.x) + dimension.width / 2,
      parseFloat(wall.position.y) + this.wallHeight / 2,
      parseFloat(wall.position.z) + dimension.height / 2
    );
    wallGroup.rotation.set(
      (Math.PI * wall.rotation.x) / 180,
      (-Math.PI * wall.rotation.y) / 180,
      (Math.PI * wall.rotation.z) / 180
    );

    return wallGroup;
  };

  main.createPilor = function (shape, group) {
    const { dimension, position } = shape;

    const width = dimension.width / 2;
    const height = dimension.height / 2;

    // shape.position
    var triangleShape = new THREE.Shape();
    triangleShape.moveTo(-width, -height);
    triangleShape.lineTo(-width, height);
    triangleShape.lineTo(width, height);
    triangleShape.lineTo(width, -height);
    triangleShape.lineTo(-width, -height);

    var extrusionSettings = {
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

    var geometry = new THREE.ExtrudeGeometry(triangleShape, extrusionSettings);

    var material = new THREE.MeshBasicMaterial({
      color: 0xffffff, // red
      transparent: true,
      side: THREE.DoubleSide,
      opacity: 1, // 0.4
    });

    var extrudedMesh = new THREE.Mesh(geometry, material);
    extrudedMesh.rotation.x = Math.PI / 2;
    extrudedMesh.position.x = position.x;
    extrudedMesh.position.y = position.y + 80;
    extrudedMesh.position.z = position.z;

    return extrudedMesh;
  };

  main.createText = function (shape) {
    const { dimension, position, text } = shape;

    var myText = new SpriteText(text, 40, "black");
    // myText.backgroundColor = "#ff0000";
    // myText.borderWidth = "1";
    // myText.borderColor = "#ffffff";
    // myText.padding = "4";

    // myText.fontSize = 1500;
    myText.position.x = position.x;
    myText.position.y = 250;
    myText.position.z = position.z;

    return myText;
  };

  main.fnInit();
};
