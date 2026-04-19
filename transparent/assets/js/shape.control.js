var ShapeControl = function () {
  const main = this;

  /**
   *   0: height
   *   1: width
   */
  main.seletedOption = 0;

  main.fnInit = function () {
    main.control_Height = document.querySelector(".sety");
    main.control_Position = document.querySelector(".setxz");

    main.control_Height.addEventListener("click", (event) => {
      main.seletedOption = 1;
    });

    main.control_Position.addEventListener("click", (event) => {
      main.seletedOption = 0;
    });

    main.elevationContainer = document.getElementById("base-elevation-form");

    main.handlesHeightSlider = document.getElementById("slider-set-height");
    noUiSlider.create(main.handlesHeightSlider, {
      start: [0],
      range: {
        min: [0],
        max: [1000],
      },
      connect: true,
      // tooltips: true,
    });

    main.handlesHeightSlider.noUiSlider.on("update", function () {
      $("#shape-base-elevation").val(main.handlesHeightSlider.noUiSlider.get());

      if (main.selectedModel)
        main.selectedModel.position.y =
          main.handlesHeightSlider.noUiSlider.get();
    });

    $("#shape-base-elevation").change(function (e) {
      main.handlesHeightSlider.noUiSlider.set(e.target.value);
    });
  };

  main.setSelectedModel = function (model) {
    main.unsetSelectedModel();

    main.selectedModel = model;

    main.handlesHeightSlider.noUiSlider.set(model.position.y);

    main.selectedModel.traverse((child) => {
      if (child.isMesh) {
        child.currentHex = child.material.emissive.getHex();
        child.material.emissive.setHex(0xff0000);
      }
    });
  };

  main.unsetSelectedModel = function () {
    if (main.selectedModel)
      main.selectedModel.traverse((child) => {
        if (child.isMesh) {
          child.material.emissive.setHex(child.currentHex);
        }
      });

    main.selectedModel = null;
  };

  main.setHeight = function (height) {
    main.handlesHeightSlider.noUiSlider.set(height);
  };

  main.setVisible = function (x, y) {
    // main.control_Height.style.display = "block";
    // main.control_Position.style.display = "block";

    // main.control_Height.style.transform = `translate(-50%, -50%) translate(${x - 20}px,${y}px)`;
    // main.control_Position.style.transform = `translate(-50%, -50%) translate(${x + 20}px,${y}px)`;

    main.control_Position.style.display = "block";
    main.control_Position.style.transform = `translate(-50%, -50%) translate(${x}px,${y}px)`;

    main.elevationContainer.style.display = "block";
    main.elevationContainer.style.transform = `translate(-50%, -50%) translate(${
      x + 80
    }px,${y}px)`;
  };

  main.setInvisible = function () {
    main.control_Height.style.display = "none";
    main.control_Position.style.display = "none";
    main.elevationContainer.style.display = "none";
  };

  main.getOption = function () {
    return main.seletedOption;
  };

  main.fnInit();
};
