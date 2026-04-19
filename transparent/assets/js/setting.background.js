var SettingBackground = function () {
  const main = this;
  this.newOnBackground = $.Callbacks();

  this.defaultColor = "linear-gradient(0.0deg,rgba(255,255,255,1) 0.0,rgba(255,255,255,1) 100.0%)";
  this.colorType = "linear-gradient";
  this.colorArray = [
    { color: "rgba(255,255,255,1)", per: 0 },
    { color: "rgba(255,255,255,1)", per: 100 },
  ];

  this.angle = 0;

  main.init = function () {
    this.parentElement = document.getElementById("color-setting");
    this.parentTextureElement = document.getElementById("ground-setting");

    $.getJSON("./assets/data/color.json").then((data) => {
      data.forEach((element) => {
        var colorElement = document.createElement("div");
        colorElement.classList.add("wall-color-element");

        colorElement.style.cssText = `background: rgb(${element.r}, ${element.g}, ${element.b})`;
        this.parentElement.append(colorElement);

        colorElement.addEventListener("click", () => {
          main.colorType = "linear-gradient";
          main.colorArray = [
            { color: "rgba(85,84,106,1)", per: 0 },
            {
              color: `rgba(${element.r},${element.g},${element.b},1)`,
              per: 20,
            },
            {
              color: `rgba(${element.r},${element.g},${element.b},1)`,
              per: 80,
            },
            { color: "rgba(85,84,106,1)", per: 100 },
          ];
          main.angle = 0;

          const backgroundStyle = `linear-gradient(0deg, rgba(85,84,106,1) 0%, rgba(${element.r},${element.g},${element.b},1) 20%, rgba(${element.r},${element.g},${element.b},1) 80%, rgba(85,84,106,1) 100%)`;

          // main.scene.background = new THREE.Color().setRGB(element.r/ 256, element.g / 256, element.b / 256);
          // main.scene.fog =  new THREE.Fog( new THREE.Color().setRGB(element.r/ 256, element.g / 256, element.b / 256), 500, 10000 );
          this.newOnBackground.fire(backgroundStyle);
        });
      });
    });

    $("#btn-background").click(() => {
      // $("mySidebar").style({});

      $(".sidebar").css("width", "0");
      document.getElementById("mySidebar").style.width = "250px";
    });

    $("#btn-close-sliderbar").click(() => {
      document.getElementById("mySidebar").style.width = "0";
    });

    // $.getJSON("./assets/data/ground.json")
    // .then((data) =>  {
    //     data.maps.forEach(element => {
    //         var colorElement = document.createElement("div");
    //         colorElement.classList.add("wall-color-element");
    //         colorElement.style.cssText = `background: url('assets/${element.path}'); background-size: contain;`;
    //         this.parentTextureElement.append(colorElement);

    //         colorElement.addEventListener("click", () => {
    //             var texture = new THREE.TextureLoader().load( "assets/" + element.path );
    //             texture.wrapS = texture.wrapT = THREE.RepeatWrapping;
    //             texture.offset.set( 0, 0 );
    //             texture.repeat.set(10, 10);
    //             // texture.repeat.set(100, 100);
    //             texture.anisotropy = 16;

    //             main.plane.material.map = texture;
    //             main.plane.material.needsUpdate= true;
    //         });
    //     });
    // });

    $("#chb-ground").change(function (e) {
      const checked = $(this).prop("checked");

      if (checked) {
        const opacity = { opacity: 1 };
        TweenLite.to(opacity, 1, {
          opacity: 0,
          ease: Cubic.easeInOut,
          delay: 0.02,
          onUpdate: () => {
            main.plane.material.opacity = opacity.opacity;
            main.plane.material.needsUpdate = true;
          },
          onComplete: () => {
            // main.camera.updateMatrix();
          },
        });
      } else {
        const opacity = { opacity: 0 };
        TweenLite.to(opacity, 1, {
          opacity: 1,
          ease: Cubic.easeInOut,
          delay: 0.02,
          onUpdate: () => {
            main.plane.material.opacity = opacity.opacity;
            main.plane.material.needsUpdate = true;
          },
          onComplete: () => {
            // main.camera.updateMatrix();
          },
        });
      }
    });

    $("#background-picker")
      .colpick({
        layout: "hex",
        submit: 0,
        colorScheme: "light",
        onChange: function (hsb, hex, rgb) {
          $("#background-picker button div").css("background", "#" + hex);

          main.colorType = "solid";
          main.colorArray = [{ color: `#${hex}` }];

          const backgroundStyle = `#${hex}`;
          main.newOnBackground.fire(backgroundStyle);
          // main.scene.background = new THREE.Color().setRGB(rgb.r/ 256, rgb.g / 256, rgb.b / 256);
          // main.scene.fog =  new THREE.Fog( new THREE.Color().setRGB(rgb.r/ 256, rgb.g / 256, rgb.b / 256), 500, 10000 );
        },
      })
      .keyup(function () {
        $(this).colpickSetColor(this.value);
      });

    var xncolorpicker = new XNColorPicker({
      selector: "#background-gradient-picker",
      color: main.defaultColor,
      showprecolor: true,
      prevcolors: null,
      showhistorycolor: true,
      historycolornum: 16,
      format: "hsla",
      showPalette: true,
      show: false,
      lang: "en",
      colorTypeOption: "linear-gradient",
      canMove: false,
      alwaysShow: false,
      autoConfirm: true,
      onError: function (e) {},
      onCancel: function (color) {
        console.log("cancel", color);
      },
      onChange: function (color) {
        main.colorType = "linear-gradient";
        main.colorArray = color.color.arry.colors;
        main.angle = color.color.arry.angle;

        document.body.style.background = color.color.str;
      },
      onConfirm: function (color) {
        // console.log("confirm",color)
        // linear-gradient(0.0deg,rgba(185,110,235,1) 0.0,rgba(255,193,5,1) 51.0%)
      },
    });

    $("#btn-reset-background").click(() => {
      this.newOnBackground.fire(main.defaultColor);
    });

    this.fireOnBackground(this._fnOnBackground);
    this.newOnBackground.fire(main.defaultColor);
  };

  main.setPlaneObject = function (model) {
    this.plane = model;
  };

  main.fireOnBackground = (callback) => {
    this.newOnBackground.add(callback);
  };

  main._fnOnBackground = (backgroundStyle) => {
    document.body.style.background = backgroundStyle;
  };

  main.init();
};
