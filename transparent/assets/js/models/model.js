var Model = function () {
  const main = this;

  this.floorplan = null;
  this.serverUrl =
    "https://test.eventdraw.com.au/frontend/web/site/get-threed-json";

  this.loaded_ready_models = $.Callbacks();

  main.init = function () {
    this.floorplan = new FloorPlan();

    main.scene = new Scene(this);

    main.loadDataFromServer();
  };

  main.loadDataFromServer = function () {
    main.token = main.getParameterByName("token");
    main.shared = main.getParameterByName("shared");
    main.mode = main.token !== "" ? "TOKEN" : "SHARED";

    if (main.token === "" && main.shared === "") {
      Swal.fire({
        title: "Error",
        text: `You are not valid user!`,
        showCancelButton: false,
        confirmButtonText: "Okay",
        reverseButtons: false,
      }).then(function (result) {
        window.location.href = "404.html";
      });
    }

    if (main.mode === "TOKEN") {
      const formData = new FormData();
      formData.append("token", main.token);

      fetch(this.serverUrl, {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          // console.log(data);
          main.loadSerialized(data);
        });
    } else if (main.mode === "SHARED") {
      fetch(`./uploads_scenes/${main.shared}.eventdraw`)
        .then((res) => res.json())
        .then((data) => {
          main.loadSerialized(data);
        })
        .catch((error) => {
          window.location.href = "404.html";
        });
    }
  };

  main.loadSerialized = function (data) {
    const availableModelList = [];

    data.shapes.forEach((shape) => {
      if (!availableModelList.includes(shape.name.trim())) {
        availableModelList.push(shape.name.trim());
      }
    });

    main.scene.setAvailableModelList(availableModelList);

    main.floorplan.loadFloorplan(data);
  };

  main.getParameterByName = function (name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    const regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
      results = regex.exec(location.search);
    return results === null
      ? ""
      : decodeURIComponent(results[1].replace(/\+/g, " "));
  };

  main.loadedModel = function () {
    this.loaded_ready_models.fire();
  };

  main.readyModel = function (callback) {
    main.loaded_ready_models.add(callback);
  };

  main.addWallItem = function (geometry, planeMaterial, position) {
    // console.log("AAAAAAAAAAAAAA");
  };

  main.init();
};
