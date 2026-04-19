var LightControl = function () {
  const main = this;

  main.radius = 500;

  main.light1 = new THREE.DirectionalLight();
  main.light2 = new THREE.DirectionalLight();
  // main.light2 = new THREE.HemisphereLight(0xffffff, 0x444444, 0.35);
  main.light3 = new THREE.HemisphereLight(0xffffff, 0x444444, 0.35);

  // main.light3.position.set(0, 500, 0);

  main.themeData = [
    {
      url: "1.png",
      light1: {
        r: 255,
        g: 255,
        b: 255,
        intensity: 100,
      },
      light2: {
        r: 255,
        g: 255,
        b: 255,
        intensity: 100,
      },
      light3: {
        r: 255,
        g: 255,
        b: 255,
        intensity: 100,
      },
    },
    {
      url: "2.png",
      light1: {
        r: 53,
        g: 34,
        b: 122,
        intensity: 48,
      },
      light2: {
        r: 255,
        g: 255,
        b: 188,
        intensity: 15,
      },
      light3: {
        r: 255,
        g: 68,
        b: 0,
        intensity: 200,
      },
    },
    {
      url: "3.png",
      light1: {
        r: 255,
        g: 40,
        b: 201,
        intensity: 160,
      },
      light2: {
        r: 110,
        g: 234,
        b: 226,
        intensity: 164,
      },
      light3: {
        r: 211,
        g: 46,
        b: 46,
        intensity: 66,
      },
    },
    {
      url: "4.png",
      light1: {
        r: 45,
        g: 255,
        b: 52,
        intensity: 180,
      },
      light2: {
        r: 232,
        g: 232,
        b: 232,
        intensity: 19,
      },
      light3: {
        r: 0,
        g: 0,
        b: 0,
        intensity: 0,
      },
    },
    {
      url: "5.png",
      light1: {
        r: 255,
        g: 250,
        b: 173,
        intensity: 97,
      },
      light2: {
        r: 52,
        g: 46,
        b: 119,
        intensity: 74,
      },
      light3: {
        r: 255,
        g: 255,
        b: 255,
        intensity: 13,
      },
    },
    {
      url: "6.png",
      light1: {
        r: 255,
        g: 194,
        b: 168,
        intensity: 88,
      },
      light2: {
        r: 73,
        g: 76,
        b: 255,
        intensity: 117,
      },
      light3: {
        r: 0,
        g: 0,
        b: 0,
        intensity: 0,
      },
    },
    {
      url: "7.png",
      light1: {
        r: 0,
        g: 0,
        b: 0,
        intensity: 0,
      },
      light2: {
        r: 255,
        g: 255,
        b: 255,
        intensity: 200,
      },
      light3: {
        r: 255,
        g: 255,
        b: 255,
        intensity: 0,
      },
    },
    {
      url: "8.png",
      light1: {
        r: 151,
        g: 199,
        b: 201,
        intensity: 111,
      },
      light2: {
        r: 255,
        g: 251,
        b: 232,
        intensity: 92,
      },
      light3: {
        r: 255,
        g: 248,
        b: 239,
        intensity: 43,
      },
    },
    {
      url: "9.png",
      light1: {
        r: 255,
        g: 144,
        b: 0,
        intensity: 200,
      },
      light2: {
        r: 84,
        g: 106,
        b: 255,
        intensity: 170,
      },
      light3: {
        r: 0,
        g: 0,
        b: 0,
        intensity: 0,
      },
    },
  ];

  main.fnInit = function () {
    $("#accordion").accordion();

    main.fnInitSlider();
    main.fnInitThemeList();

    main.fnInitLight1();
    main.fnInitLight2();
    main.fnInitLight3();

    const initColorTheme = {
      light1: {
        r: 255,
        g: 255,
        b: 255,
        intensity: 0.3,
      },
      light2: {
        r: 255,
        g: 255,
        b: 255,
        intensity: 0.3,
      },
      light3: {
        r: 255,
        g: 255,
        b: 255,
        intensity: 0.35,
      },
    };

    $("#btn-reset-theme").click(() => {
      // console.log(main.light1, main.light2, main.light3)
      main.light1.color.setRGB(
        initColorTheme.light1.r / 256,
        initColorTheme.light1.g / 256,
        initColorTheme.light1.b / 256
      );
      main.light2.color.setRGB(
        initColorTheme.light2.r / 256,
        initColorTheme.light2.g / 256,
        initColorTheme.light2.b / 256
      );
      main.light3.color.setRGB(
        initColorTheme.light3.r / 256,
        initColorTheme.light3.g / 256,
        initColorTheme.light3.b / 256
      );
      main.light1.intensity = initColorTheme.light1.intensity;
      main.light2.intensity = initColorTheme.light2.intensity;
      main.light3.intensity = initColorTheme.light3.intensity;
    });
  };

  main.fnInitThemeList = function () {
    console.log("%c SetTheme", "background: #f00");

    this.parentElement = document.getElementById("choose-theme-selection");
    main.themeData.forEach((theme) => {
      var colorElement = document.createElement("div");
      colorElement.style.cssText = `background: url('assets/images/${theme.url}'); background-size: contain;`;
      this.parentElement.append(colorElement);

      colorElement.addEventListener("click", () => {
        main.fnChangeTheme(theme);
      });
    });
  };

  main.fnChangeTheme = function (theme) {
    $("#light1-red").val(theme.light1.r);
    $("#light1-green").val(theme.light1.g);
    $("#light1-blue").val(theme.light1.b);

    $("#light2-red").val(theme.light2.r);
    $("#light2-green").val(theme.light2.g);
    $("#light2-blue").val(theme.light2.b);

    $("#light3-red").val(theme.light3.r);
    $("#light3-green").val(theme.light3.g);
    $("#light3-blue").val(theme.light3.b);

    $("#light1-intensity").val(theme.light1.intensity);
    $("#light2-intensity").val(theme.light2.intensity);
    $("#light3-intensity").val(theme.light3.intensity);

    main.setLight1Color();
    main.setLight2Color();
    main.setLight3Color();

    main.setLight1Intensity();
    main.setLight2Intensity();
    main.setLight3Intensity();
  };

  main.fnInitSlider = function () {
    var $container = $("#rotationSliderContainer");
    var $slider = $("#rotationSlider");
    var $degrees = $("#rotationSliderDegrees");

    var sliderWidth = $slider.width();
    var sliderHeight = $slider.height();
    var radius = $container.width() / 2;
    var deg = 0;

    X = Math.round(radius * Math.sin((deg * Math.PI) / 180));
    Y = Math.round(radius * -Math.cos((deg * Math.PI) / 180));

    $slider.css({
      left: X + radius - sliderWidth / 2,
      top: Y + radius - sliderHeight / 2,
    });

    let mdown = false;
    $container
      .mousedown(function (e) {
        mdown = true;
        e.originalEvent.preventDefault();
      })
      .mouseup(function (e) {
        mdown = false;
      })
      .mousemove(function (e) {
        if (mdown) {
          // firefox compatibility
          if (
            typeof e.offsetX === "undefined" ||
            typeof e.offsetY === "undefined"
          ) {
            const targetOffset = $(e.target).offset();
            e.offsetX = e.pageX - targetOffset.left;
            e.offsetY = e.pageY - targetOffset.top;
          }

          if ($(e.target).is("#rotationSliderContainer"))
            var mPos = { x: e.offsetX, y: e.offsetY };
          else
            var mPos = {
              x: e.target.offsetLeft + e.offsetX,
              y: e.target.offsetTop + e.offsetY,
            };

          const atan = Math.atan2(mPos.x - radius, mPos.y - radius);
          deg = -atan / (Math.PI / 180) + 180; // final (0-360 positive) degrees from mouse position

          // for attraction to multiple of 90 degrees
          const distance = Math.abs(deg - Math.round(deg / 90) * 90);

          if (distance <= 5) deg = Math.round(deg / 90) * 90;

          if (deg === 360) deg = 0;

          X = Math.round(radius * Math.sin((deg * Math.PI) / 180));
          Y = Math.round(radius * -Math.cos((deg * Math.PI) / 180));

          $slider.css({
            left: X + radius - sliderWidth / 2,
            top: Y + radius - sliderHeight / 2,
          });

          var roundDeg = Math.round(deg);

          $degrees.html(roundDeg + "&deg;");
          $("#imageRotateDegrees").val(roundDeg);

          main.roundDeg = roundDeg;

          main.replaceLights();
        }
      });
  };

  main.replaceLights = function () {
    const firstDelta = (main.roundDeg * 720) / 360;
    const secondDelta = firstDelta + 180;
    const thirdDelta = firstDelta + 540;

    main.replaceLight(main.light1, firstDelta);
    main.replaceLight(main.light2, secondDelta);
    main.replaceLight(main.light3, thirdDelta);
  };

  main.setLight1Color = function () {
    const r = $("#light1-red").val();
    const g = $("#light1-green").val();
    const b = $("#light1-blue").val();

    main.light1.color.setRGB(r / 256, g / 256, b / 256);

    const theColour = "rgb(" + r + "," + g + "," + b + ")";

    $("#light1-pan").css("background-color", theColour);
    console.log("Color Changed");
  };

  main.setLight2Color = function () {
    const r = $("#light2-red").val();
    const g = $("#light2-green").val();
    const b = $("#light2-blue").val();

    main.light2.color.setRGB(r / 256, g / 256, b / 256);
    const theColour = "rgb(" + r + "," + g + "," + b + ")";

    $("#light2-pan").css("background-color", theColour);
  };

  main.setLight3Color = function () {
    const r = $("#light3-red").val();
    const g = $("#light3-green").val();
    const b = $("#light3-blue").val();

    main.light3.color.setRGB(r / 256, g / 256, b / 256);
    const theColour = "rgb(" + r + "," + g + "," + b + ")";

    $("#light3-pan").css("background-color", theColour);
  };

  main.setLight1Intensity = function () {
    main.light1.intensity = $("#light1-intensity").val() * 0.01 * 0.3;
  };

  main.setLight2Intensity = function () {
    main.light2.intensity = $("#light2-intensity").val() * 0.01;
  };

  main.setLight3Intensity = function () {
    main.light3.intensity = $("#light3-intensity").val() * 0.01;
  };

  main.replaceLight = function (light, theta) {
    const phi = 80;

    const x =
      main.radius *
      Math.sin((theta * Math.PI) / 360) *
      Math.cos((phi * Math.PI) / 360);
    const y = main.radius * Math.sin((phi * Math.PI) / 360);
    const z =
      main.radius *
      Math.cos((theta * Math.PI) / 360) *
      Math.cos((phi * Math.PI) / 360);

    light.position.set(x, y, z);
  };

  main.fnInitLight1 = function () {
    const r = 255;
    const g = 255;
    const b = 255;

    $("#light1-red").change(function (e) {
      $("#light1-t-red").val(e.target.value);
      main.setLight1Color();
    });

    $("#light1-green").change(function (e) {
      $("#light1-t-green").val(e.target.value);
      main.setLight1Color();
    });

    $("#light1-blue").change(function (e) {
      $("#light1-t-blue").val(e.target.value);
      main.setLight1Color();
    });

    $("#light1-intensity").change(function (e) {
      $("#light1-t-intensity").val(e.target.value);
      main.setLight1Intensity();
    });

    $("#light1-t-red").change(function (e) {
      $("#light1-red").val(e.target.value);
      main.setLight1Color();
    });

    $("#light1-t-green").change(function (e) {
      $("#light1-green").val(e.target.value);
      main.setLight1Color();
    });

    $("#light1-t-blue").change(function (e) {
      $("#light1-blue").val(e.target.value);
      main.setLight1Color();
    });

    $("#light1-t-intensity").change(function (e) {
      $("#light1-intensity").val(e.target.value);
      main.setLight1Intensity();
    });

    const theta = 0;
    const phi = 80;

    const x =
      main.radius *
      Math.sin((theta * Math.PI) / 360) *
      Math.cos((phi * Math.PI) / 360);
    const y = main.radius * Math.sin((phi * Math.PI) / 360);
    const z =
      main.radius *
      Math.cos((theta * Math.PI) / 360) *
      Math.cos((phi * Math.PI) / 360);

    main.light1.color.setRGB(r / 255, g / 255, b / 255);
    main.light1.intensity = 0.3;

    main.light1.position.set(x, y, z);
    main.light1.castShadow = true;
    main.light1.shadow.camera.top = 1000;
    main.light1.shadow.camera.bottom = -1000;
    main.light1.shadow.camera.left = -1000;
    main.light1.shadow.camera.right = 1000;
    main.light1.shadow.mapSize.width = 1000; // default
    main.light1.shadow.mapSize.height = 1000; // default
    // light.shadow.camera.near = 0.5; // default
    main.light1.shadow.camera.far = 2000; // default
  };

  main.fnInitLight2 = function () {
    // var r = 255;
    // var g = 255;
    // var b = 255;

    // main.light2.color.setRGB(r, g, b);
    // main.light2.intensity = 0.3;
    // main.light2.color.setRGB(r / 255, g / 255, b / 255);
    // console.error(main.light2.color);
    // main.light2.color = 0xff0000;

    const theta = 180;
    const phi = 80;

    const x =
      main.radius *
      Math.sin((theta * Math.PI) / 360) *
      Math.cos((phi * Math.PI) / 360);
    const y = main.radius * Math.sin((phi * Math.PI) / 360);
    const z =
      main.radius *
      Math.cos((theta * Math.PI) / 360) *
      Math.cos((phi * Math.PI) / 360);

    main.light2.position.set(x, y, z);
    main.light2.intensity = 0.3;

    $("#light2-red").change(function (e) {
      $("#light2-t-red").val(e.target.value);
      main.setLight2Color();
    });

    $("#light2-green").change(function (e) {
      $("#light2-t-green").val(e.target.value);
      main.setLight2Color();
    });

    $("#light2-blue").change(function (e) {
      $("#light2-t-blue").val(e.target.value);
      main.setLight2Color();
    });

    $("#light2-intensity").change(function (e) {
      $("#light2-t-intensity").val(e.target.value);
      main.setLight2Intensity();
    });

    $("#light2-t-red").change(function (e) {
      $("#light2-red").val(e.target.value);
      main.setLight2Color();
    });

    $("#light2-t-green").change(function (e) {
      $("#light2-green").val(e.target.value);
      main.setLight2Color();
    });

    $("#light2-t-blue").change(function (e) {
      $("#light2-blue").val(e.target.value);
      main.setLight2Color();
    });

    $("#light2-t-intensity").change(function (e) {
      $("#light2-intensity").val(e.target.value);
      main.setLight2Intensity();
    });
  };

  main.fnInitLight3 = function () {
    const theta = 540;
    const phi = 80;

    const x =
      main.radius *
      Math.sin((theta * Math.PI) / 360) *
      Math.cos((phi * Math.PI) / 360);
    const y = main.radius * Math.sin((phi * Math.PI) / 360);
    const z =
      main.radius *
      Math.cos((theta * Math.PI) / 360) *
      Math.cos((phi * Math.PI) / 360);

    main.light3.position.set(x, y, z);

    $("#light3-red").change(function (e) {
      $("#light3-t-red").val(e.target.value);
      main.setLight3Color();
    });

    $("#light3-green").change(function (e) {
      $("#light3-t-green").val(e.target.value);
      main.setLight3Color();
    });

    $("#light3-blue").change(function (e) {
      $("#light3-t-blue").val(e.target.value);
      main.setLight3Color();
    });

    $("#light3-intensity").change(function (e) {
      $("#light3-t-intensity").val(e.target.value);
      main.setLight3Intensity();
    });

    $("#light3-t-red").change(function (e) {
      $("#light3-red").val(e.target.value);
      main.setLight3Color();
    });

    $("#light3-t-green").change(function (e) {
      $("#light3-green").val(e.target.value);
      main.setLight3Color();
    });

    $("#light3-t-blue").change(function (e) {
      $("#light3-blue").val(e.target.value);
      main.setLight3Color();
    });

    $("#light3-t-intensity").change(function (e) {
      $("#light3-intensity").val(e.target.value);
      main.setLight3Intensity();
    });
  };

  main.fnInit();
};
