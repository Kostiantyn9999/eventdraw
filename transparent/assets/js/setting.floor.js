var SettingFloor = function (scene, msmControl) {
  const main = this;
  main.msmControl = msmControl;
  main.cw = 0;
  main.ch = 0;

  this.onChangedFloorTexture = $.Callbacks();
  this.onChangedFloorOpacity = $.Callbacks();
  this.onShowFloor2DImage = $.Callbacks();

  main.init = function () {
    $("#btn-floor").click(() => {
      $(".sidebar").css("width", "0");
      document.getElementById("setting-floor").style.width = "250px";
    });

    $("#btn-close-setting-floor").click(() => {
      document.getElementById("setting-floor").style.width = "0";
    });

    this.parentElement = document.getElementById("floor-setting");

    // $.getJSON("./assets/data/parquet.json")
    // .then((data) =>  {
    //     data.forEach(element => {
    //         var colorElement = document.createElement("div");
    //         colorElement.classList.add("wall-color-element");
    //         colorElement.style.cssText = `background: url('assets/${element.path}'); background-size: contain;`;
    //         this.parentElement.append(colorElement);

    //         colorElement.addEventListener("click", () => {
    //             var texture = new THREE.TextureLoader().load( "assets/" + element.path );
    //             texture.wrapS = texture.wrapT = THREE.RepeatWrapping;
    //             texture.offset.set( 0, 0 );
    //             texture.repeat.set(main.cw / 100, main.ch / 100);
    //             // texture.repeat.set(30, 30);

    //             texture.anisotropy = 16;

    //             main.plane.material.map = texture;
    //             main.plane.material.needsUpdate= true;
    //         });
    //     });
    // });

    $.getJSON("./assets/data/color.json").then((data) => {
      data.forEach((element) => {
        var colorElement = document.createElement("div");
        colorElement.classList.add("wall-color-element");

        colorElement.style.cssText = `background: rgb(${element.r}, ${element.g}, ${element.b})`;
        this.parentElement.append(colorElement);

        colorElement.addEventListener("click", () => {
          main.onChangedFloorTexture.fire(element);
        });
      });
    });

    $("#floor-picker")
      .colpick({
        layout: "hex",
        submit: 0,
        colorScheme: "light",
        onChange: function (hsb, hex, rgb) {
          $("#floor-picker")
            .val(hex)
            .css("border-color", "#" + hex);
          main.onChangedFloorTexture.fire(rgb);
        },
      })
      .keyup(function () {
        $(this).colpickSetColor(this.value);
      });

    // floor opacity slider
    var handlesSlider = document.getElementById("slider-floor-opacity");
    noUiSlider.create(handlesSlider, {
      start: [0],
      range: {
        min: [0],
        max: [1],
      },
      connect: true,
    });

    handlesSlider.noUiSlider.set(1);

    handlesSlider.noUiSlider.on("update", function () {
      const opaicty = handlesSlider.noUiSlider.get();
      main.onChangedFloorOpacity.fire(opaicty);
    });

    // dimension text elevation slider
    var handlesDimTextSlider = document.getElementById("slider-dim-text-elevation");
    noUiSlider.create(handlesDimTextSlider, {
      start: [0],
      range: {
        min: [0],
        max: [100],
      },
      connect: true,
    });

    handlesDimTextSlider.noUiSlider.set(20);
    main.msmControl.setTextElevation(20);

    handlesDimTextSlider.noUiSlider.on("update", function () {
      const dimTextElevation = handlesDimTextSlider.noUiSlider.get();
      console.log('dimTextElevation', dimTextElevation)
      main.msmControl.setTextElevation(dimTextElevation);
    });

    $("#btn-reset-floor").click(() => {
      main.onChangedFloorTexture.fire({ r: 85, g: 85, b: 85 });
    });

    $("#btn-reset-floor-opacity").click(() => {
      handlesSlider.noUiSlider.set(1);
    });

    main.fireOnChangedFloorTexture(main.fnChangeTexture);
    main.fireOnChangedFloorOpacity(main.fnChangeFloorOpacity);
    main.fireOnShowFloor2DImage(main.fnShowFloor2DImage);

    $("#2d_image_show").on('change', function () {
      main.onShowFloor2DImage.fire($(this).is(":checked"));
    });
  };

  main.fireOnChangedFloorOpacity = (callback) => {
    this.onChangedFloorOpacity.add(callback);
  };

  main.fireOnChangedFloorTexture = (callback) => {
    this.onChangedFloorTexture.add(callback);
  };

  main.fireOnShowFloor2DImage = (callback) => {
    this.onShowFloor2DImage.add(callback);
  };

  main.fnChangeFloorOpacity = (opacity) => {
    if (main.plane) {
      main.plane.material.opacity = opacity;
      main.plane.material.needsUpdate = true;
    }
  };

  main.fnChangeDimTextElevation = (elevation) => {
    if (main.plane) {
      main.plane.material.opacity = opacity;
      main.plane.material.needsUpdate = true;
    }
  };

  main.fnChangeTexture = (value) => {
    const { r, g, b } = value;

    if (main.plane) {
      main.plane.material.color = new THREE.Color().setRGB(
        r / 256,
        g / 256,
        b / 256
      );
      main.plane.material.needsUpdate = true;
    }
  };

  main.fnShowFloor2DImage = (value) => {
    if (!main.plane) {
      return;
    }
    if (value) {
      main.plane.material.color.set(0xffffff);
      main.plane.material.map = main.planeTexture;
    } else {
      main.plane.material.color.set(0x555555);
      main.plane.material.map = null;
    }
    main.plane.material.needsUpdate = true;
  };

  main.setFloorObject = function (model, cw, ch, texture) {
    main.plane = model;
    main.cw = cw;
    main.ch = ch;
    main.planeTexture = texture;
  };

  main.moveArt = function (raycaster, activedArt, cx, cy) {
    var intersects = raycaster.intersectObject(main.plane, false);

    if (intersects.length > 0) {
      // console.log(intersects[0], activeArt);
      // activedArt.position.x = intersects[0].point.x + cx / 2 ;
      // activedArt.position.y = 200;
      // activedArt.position.z = intersects[0].point.z + cy / 2 ;

      const id = activedArt.userData.id;
      const x = intersects[0].point.x + cx / 2;
      const y = activedArt.position.y;
      const z = intersects[0].point.z + cy / 2;

      main.onArtMoved.fire(id, new THREE.Vector3(x, y, z));
    }
  };

  main.setMovedArtCallback = (callback) => {
    main.onArtMoved = callback;
  };

  main.init();
};
