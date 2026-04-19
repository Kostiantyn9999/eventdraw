var SettingSize = function (param1, param2, param3, param4) {
  var main = this;

  const ALL_MODE = 0;
  const ONE_MODE = 1;
  const measKoeff = 39.37;

  this.selectionMode = ONE_MODE;
  this.selectedModel = null; // just selected model
  this.isFirstSet = false;
  this.group = param1;
  this.groupList = param2;
  this.store = param3;
  this.scene = param4;
  this.isLock = true;

  this.readySetHeight = false;
  this.selectedModels = [];

  this.onChangedShapeBaseElevation = $.Callbacks();
  this.onChangedShapeBaseHeight = $.Callbacks();
  this.onChangedShapeOpacity = $.Callbacks();
  this.onWallObjectChanged = $.Callbacks();
  this.onChangedTableProperty = $.Callbacks();

  this.elevationSlider;
  this.toggleTransparent = true;
  

  main.init = function () {
    // console.log(this.groupList);
    // document.getElementById("setting-pos-size").style.width = "250px";

    main.setMaterialEditor = new SettingTextureEditor(main.scene);
    $("#btn-setting-pos-size").click(() => {
      $(".sidebar").css("width", "0");
      document.getElementById("setting-pos-size").style.width = "250px";
      this.readySetHeight = true;

      //     this.groupList.forEach(element => {
      //         // console.log(element);

      //         var html = `<li>
      //             <input type="checkbox" class="check-height"></input>
      //             <span>${element.name}</span><span class="pos-y">${element.position.y}</span>
      //             <span class="badge">10</span>
      //         </li>`;

      //         $("#group-shape-lists").append(html);
      //     });

      //     $(".check-height").click(function(){
      //         console.log($(this));

      //         if ($(this).is(":checked"))
      //             $(this).parent().addClass("active");
      //         else
      //             $(this).parent().removeClass("active");
      //     });
    });
    
    $("#btn-all-transparent").click(() => {
      main.toggleTransparent = !main.toggleTransparent;
      main.updateTransparent();
    });
    
    $("#btn-close-pos-size").click(() => {
      this.readySetHeight = false;
      document.getElementById("setting-pos-size").style.width = "0";
    });

    main.elevationSlider = document.getElementById("slider-handles");

    noUiSlider.create(main.elevationSlider, {
      start: [0],
      range: {
        min: [0],
        max: [20],
      },
      connect: true,
    });

    main.heightSlider = document.getElementById("slider-height-handles");
    noUiSlider.create(main.heightSlider, {
      start: [0],
      range: {
        min: [0.1],
        max: [10],
      },
      connect: true,
      // tooltips: true,
    });

    main.opacitySlider = document.getElementById("slider-shape-opacity");
    noUiSlider.create(main.opacitySlider, {
      start: [1],
      range: {
        min: [0],
        max: [1],
      },
      connect: true,
    });

    main.elevationSlider.noUiSlider.on("update", function () {
      const baseElevation = main.elevationSlider.noUiSlider.get();
      $("#base-elevation").val(baseElevation);

      main.selectedModels.forEach((element) => {
        if (element.data) {
          main.onChangedShapeBaseElevation.fire(element, baseElevation * measKoeff);
        }
      });
    });

    main.heightSlider.noUiSlider.on("update", function () {
      $("#3d-height").val(main.heightSlider.noUiSlider.get());

      const depth = main.heightSlider.noUiSlider.get();

      main.selectedModels.forEach((element) => {
        if (element.data) {
          main.onChangedShapeBaseHeight.fire(element, depth * measKoeff);
        }
      });
    });

    main.opacitySlider.noUiSlider.on("update", function () {
      const opacity = main.opacitySlider.noUiSlider.get();

      main.selectedModels.forEach((element) => {
        if (element.data) {
          main.onChangedShapeOpacity.fire(element, opacity);
        }
      });
    });

    $("#3d-height").change(function (e) {
      main.heightSlider.noUiSlider.set(e.target.value);
    });

    $("#base-elevation").change(function (e) {
      main.elevationSlider.noUiSlider.set(e.target.value);
    });

    main.fireOnChangedShapeBaseElevation(this.fnChangedShapeBaseElevation);
    main.fireOnChangedShapeBaseHeight(this.fnChangedShapeBaseHeight);
    main.fireOnChangedShapeOpacity(this.fnChangedShapeOpacity);
    main.fireOnChangeTablePropery(this.fnChangeTableProperty);

    $("#model-selection-mode").change(function (e) {
      const checked = $(this).prop("checked");

      main.selectionMode = checked ? ALL_MODE : ONE_MODE;

      main.unSelectedModels();
      main.setSelectedModel(main.selectedModel);
    });

    $("#btn-reset-size").click(() => {
      main.selectedModels.forEach((element) => {
        if (element.data) {
          // const { id, position } = element.data.userData;
          const processedMesh = element.data;

          commandManager.executeCommand(processedMesh, 'setBaseElevation', processedMesh.getBaseElevation(), processedMesh.initialPosition.y)
          main.onChangedShapeBaseElevation.fire(
            element,
            processedMesh.resetElevation()
          );
          commandManager.executeCommand(processedMesh, 'setHeight', processedMesh.getHeight(), processedMesh.depth)
          main.onChangedShapeBaseHeight.fire(
            element,
            processedMesh.resetHeight()
          );
        }
      });
    });

    const parentElement1 = document.getElementById("table-color-setting");
    const parentElement2 = document.getElementById("chair-color-setting");

    $.getJSON("./assets/data/color.json").then((data) => {
      data.forEach((element) => {
        const colorElement1 = document.createElement("div");
        colorElement1.classList.add("wall-color-element");
        colorElement1.style.cssText = `background: rgb(${element.r}, ${element.g}, ${element.b})`;

        const colorElement2 = document.createElement("div");
        colorElement2.classList.add("wall-color-element");
        colorElement2.style.cssText = `background: rgb(${element.r}, ${element.g}, ${element.b})`;

        parentElement1.append(colorElement1);
        parentElement2.append(colorElement2);

        colorElement1.addEventListener("click", () => {
          main.selectedModels.forEach((e) => {
            const property = e.data.setProperty();
            if (e.data) {
              main.onChangedTableProperty.fire(e, {
                ...property,
                table: element,
              });
            }
          });
        });

        colorElement2.addEventListener("click", () => {
          main.selectedModels.forEach((e) => {
            if (e.data) {
              const property = e.data.setProperty();
              main.onChangedTableProperty.fire(e, {
                ...property,
                chair: element,
              });
            }
          });
        });
      });
    });

    /** Color Picker for Table and Chair */
    $("#table-color-picker")
      .colpick({
        layout: "hex",
        submit: 0,
        colorScheme: "light",
        onChange: function (hsb, hex, rgb) {
          $("#table-color-picker")
            .val(hex)
            .css("border-color", "#" + hex);

          main.selectedModels.forEach((e) => {
            const property = e.data.setProperty();
            if (e.data) {
              main.onChangedTableProperty.fire(e, {
                ...property,
                table: { r: rgb.r, g: rgb.g, b: rgb.b },
              });
            }
          });
        },
      })
      .keyup(function () {
        $(this).colpickSetColor(this.value);
      });

    $("#chair-color-picker")
      .colpick({
        layout: "hex",
        submit: 0,
        colorScheme: "light",
        onChange: function (hsb, hex, rgb) {
          $("#chair-color-picker")
            .val(hex)
            .css("border-color", "#" + hex);

          main.selectedModels.forEach((e) => {
            const property = e.data.setProperty();
            if (e.data) {
              main.onChangedTableProperty.fire(e, {
                ...property,
                chair: { r: rgb.r, g: rgb.g, b: rgb.b },
              });
            }
          });
        },
      })
      .keyup(function () {
        $(this).colpickSetColor(this.value);
      });

    $("#btn-reset-table-color").click(() => {
      main.selectedModels.forEach((e) => {
        const property = e.data.setProperty();
        if (e.data) {
          main.onChangedTableProperty.fire(e, {
            ...property,
            table: { r: 221, g: 221, b: 221 },
          });
        }
      });
    });

    $("#btn-reset-chair-color").click(() => {
      main.selectedModels.forEach((e) => {
        const property = e.data.setProperty();
        if (e.data) {
          main.onChangedTableProperty.fire(e, {
            ...property,
            chair: { r: 221, g: 221, b: 221 },
          });
        }
      });
    });

    $("#chb-selection-table-numbers").change(function (e) {
      const checked = $(this).prop("checked");
      // console.log(checked)
      main.selectedModels.forEach((e) => {
        const property = e.data.setProperty();
        if (e.data) {
          main.onChangedTableProperty.fire(e, {
            ...property,
            showTableNumber: !checked,
          });
        }
      });
    });
    // var parentElement = document.getElementById('colors-setting');

    // $.getJSON("./assets/data/color.json")
    // .then((data) =>  {
    //     data.forEach(element => {
    //         var colorElement = document.createElement("div");
    //         colorElement.classList.add("wall-color-element");

    //         colorElement.style.cssText = `background: rgb(${element.r}, ${element.g}, ${element.b})`;
    //         parentElement.append(colorElement);

    //         colorElement.addEventListener("click", () => {
    //             main.setMaterialEditor.setColor(element.r, element.g, element.b);
    //         });
    //     });
    // });

    //     var parent = document.querySelector('#parent');
    //     var picker = new Picker({
    //         parent: parent,
    //         popup: 'bottom',
    //     });

    //     picker.onDone = function(color) {

    //         parentElement.removeChild(parentElement.firstChild);

    //         console.log(color.rgbaString, color);

    //         var colorElement = document.createElement("div");
    //         colorElement.classList.add("wall-color-element");

    //         colorElement.style.cssText = `background: ${color.rgbaString}`;
    //         parentElement.append(colorElement);

    //         colorElement.addEventListener("click", () => {
    //             main.setMaterialEditor.setColor(color.rgba[0], color.rgba[1], color.rgba[2]);
    //         });
    //     };
  };

  main.updateTransparent = function () {
    main.groupList.forEach((element) => {
      if (element.children) {
        for (let i = 0; i < element.children.length; i++) {
          const modelGroup = element.children[i];
          if (modelGroup.type == 'Group') {
            modelGroup.children.forEach((model) => {
              model.traverse((item) => {
                if (item instanceof THREE.Mesh) {
                  item.material.transparent = true;
                  item.material.opacity = main.toggleTransparent ? 0.5 : 1;
                  item.material.needsUpdate = true;
                }
              });
            })
          } else {
            modelGroup.traverse((item) => {
              if (item instanceof THREE.Mesh) {
                item.material.transparent = true;
                item.material.opacity = main.toggleTransparent ? 0.5 : 1;
                item.material.needsUpdate = true;
              }
            });
          }
        }
      }
    })
  }

  main.setData = function (setting) {
    const { groups, shapes } = setting;

    main.shapes = shapes;

    groups.forEach((element) => {
      main.setTree(element);
    });

    $("#group-shape-lists").remove();

    $("#group-selection-control").append(
      `<div class="group-list" id="group-shape-lists" />`
    );
    $("#group-shape-lists")
      .jstree({
        core: {
          data: groups,
        },
        plugins: ["wholerow", "checkbox"],
      })
      .on("changed.jstree", function (evt, data) {
        // console.log("changed.jstree");
        main.selectedModels.forEach((selModel) => {
          selModel.data.traverse((child) => {
            if (child.isMesh && child.material.emissive) {
              child.material.emissive.setHex(child.currentHex);
            }
            if (child.isMesh && !child.material.emissive) {
              child.material.color.setHex(child.currentHex);
            }
          });
        });

        main.selectedModels = [];

        var selectedGroup = $("#group-shape-lists").jstree("get_checked", true);
        var ele = [];

        for (var i = 0; i < selectedGroup.length; i++) {
          // console.log("Change Model");
          if (selectedGroup[i].icon === "shape") {
            ele.push(selectedGroup[i].id);
          }
        }

        groups.forEach((shapeEle) => {
          main.findModel(shapeEle, ele);
        });

        main.selectedModels.forEach((selModel) => {
          selModel.data.traverse((child) => {
            if (child.isMesh && child.material.emissive) {
              child.currentHex = child.material.emissive.getHex();
              child.material.emissive.setHex(0xffff00);
            }
            if (child.isMesh && !child.material.emissive) {
              child.currentHex = child.material.color.getHex();
              child.material.color.setHex(0xffff00);
            }
          });
        });

        if (main.selectedModels.length === 1) {
          main.setMaterialEditor.setModelList(main.store.getShapes());
          main.setMaterialEditor.setModelsInfo(main.store.getShapesInfo());
          main.setMaterialEditor.setActiveShape(main.selectedModels[0]);
        } else {
        }
      });
  };

  main.findModel = function (element, ids) {
    // if (element.id == id) {
    //     main.selectedModels.push(element);
    // };
    for (let i = 0; i < ids.length; i++) {
      if (element.id === ids[i]) {
        main.selectedModels.push(element);
      }
    }

    if (!element.children) return;

    for (let i = 0; i < element.children.length; i++) {
      main.findModel(element.children[i], ids);
    }
  };

  main.setTree = function (tree) {
    tree.text = tree.type === "group" ? "group" : main.getShape(tree.id);
    tree.icon = tree.type;

    if (!tree.children) return;

    for (let i = 0; i < tree.children.length; i++) {
      if (tree.children[i]) main.setTree(tree.children[i]);
    }
  };

  main.getShape = function (id) {
    for (let i = 0; i < main.shapes.length; i++) {
      if (main.shapes[i].id === id) {
        return main.shapes[i].name;
      }
    }

    return "";
  };

  main.setObjects = function (object) {
    console.log(object.object.geometry);

    // const edges = new THREE.EdgesGeometry( object.object.geometry );
    // const line = new THREE.LineSegments( edges, new THREE.LineBasicMaterial( { color: 0xff0000 } ) );

    // console.log(object.object.rotation);
    // // line.position.set(object.object.position.clone());
    // // line.rotation.set(object.object.rotation);
    // this.group.add(line);
  };

  main.setSelectedModel = function (model) {
    // console.log("setSelectedModel", model);
    main.isFirstSet = true;
    main.selectedModel = model;
    if (main.selectedModel.isTableType()) {
      $("#table-control").show();
    } else {
      $("#table-control").hide();
    }

    const id = model.userData.id;
    if (main.selectionMode === ONE_MODE) {
      $("#group-shape-lists").jstree(true).select_node(id);

      const currentPosZ = model.getBaseElevation() / measKoeff;
      const currentHeight = model.getHeight() / measKoeff;
      $("#base-elevation").val(currentPosZ);

      main.elevationSlider.noUiSlider.set(currentPosZ);
      main.heightSlider.noUiSlider.set(currentHeight);
    } else if (main.selectionMode === ALL_MODE) {
      const shapes = main.store.shapes;
      let selectedShapeName = "";
      shapes.forEach((d) => {
        if (d.id === id) selectedShapeName = d.name;
      });

      shapes.forEach((d) => {
        if (d.name === selectedShapeName) {
          $("#group-shape-lists").jstree(true).select_node(d.id);
        }
      });
    }

    document.getElementById("setting-pos-size").style.width = "250px";
    main.isFirstSet = false;
  };

  main.unSelectedModels = function (model) {
    $("#group-shape-lists").jstree().deselect_all(true);

    main.selectedModels.forEach((selModel) => {
      // console.log(selModel);
      selModel.data.traverse((child) => {
        if (child.isMesh && child.material.emissive) {
          child.material.emissive.setHex(child.currentHex);
        }
        if (child.isMesh && !child.material.emissive) {
          child.material.color.setHex(child.currentHex);
        }
      });
    });

    main.selectedModels = [];

    document.getElementById("setting-pos-size").style.width = 0;
    $(".colpick").hide();
  };

  main.fireOnChangedShapeBaseElevation = (callback) => {
    this.onChangedShapeBaseElevation.add(callback);
  };

  main.fireOnChangedShapeBaseHeight = (callback) => {
    this.onChangedShapeBaseHeight.add(callback);
  };

  main.fireOnChangedShapeOpacity = (callback) => {
    this.onChangedShapeOpacity.add(callback);
  };

  main.fireOnChangeTablePropery = (callback) => {
    this.onChangedTableProperty.add(callback);
  };

  main.fnChangedShapeBaseElevation = (element, baseElevation) => {
    const selectedMesh = element.data;
    if (!main.isFirstSet) {
      commandManager.executeCommand(selectedMesh, 'setBaseElevation', selectedMesh.getBaseElevation(), baseElevation)
    }
    selectedMesh.setBaseElevation(baseElevation);

    if (
      element.name === "Single_Door" ||
      element.name === "Double_Door" ||
      element.name === "Window_1"
    )
      this.onWallObjectChanged.fire(false);
  };

  main.fnChangedShapeBaseHeight = (element, depth) => {
    const selectedMesh = element.data;
    if (!main.isFirstSet) {
      commandManager.executeCommand(selectedMesh, 'setHeight', selectedMesh.getHeight(), depth)
    }
    selectedMesh.setHeight(depth);

    if (
      element.name === "Single_Door" ||
      element.name === "Double_Door" ||
      element.name === "Window_1"
    )
      this.onWallObjectChanged.fire(false);
  };

  main.fnChangedShapeOpacity = (element, opacity) => {
    const selectedMesh = element.data;
    selectedMesh.traverse((m) => {
      if (m instanceof THREE.Mesh) {
        m.material.transparent = true;
        m.material.opacity = opacity;
      }
    });
  };

  main.fnChangeTableProperty = (element, property) => {
    const selectedMesh = element.data;
    selectedMesh.setProperty(property);
  };

  main.fnInitElevationElement = (height) => {
    $("#slider-handles").empty();
    $("#slider-handles").replaceWith(
      `<div class="setting-body" id="slider-handles"></div>`
    );

    main.elevationSlider = document.getElementById("slider-handles");
    noUiSlider.create(main.elevationSlider, {
      start: [0],
      range: {
        min: [0],
        max: [2 * height],
      },
      connect: true,
    });

    main.elevationSlider.noUiSlider.on("update", function () {
      const baseElevation = main.elevationSlider.noUiSlider.get();
      $("#base-elevation").val(baseElevation);

      main.selectedModels.forEach((element) => {
        if (element.data) {
          main.onChangedShapeBaseElevation.fire(element, baseElevation * measKoeff);
        }
      });
    });
  };

  main.fireOnWallObjectChanged = (callback) => {
    this.onWallObjectChanged.add(callback);
  };

  main.setIsLock = (isLock) => {
    $("#btn-setting-pos-size").closest('li').css('display', isLock ? 'none' : 'block');
  };

  main.init();
  main.setIsLock(true);
};
