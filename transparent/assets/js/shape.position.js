var ShapePosition = function (parameter) {
  const main = this;

  this.newJsonImported = $.Callbacks();

  main.settings = {};

  main.fnInit = function () {
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

    $("#attachment").on("input", async (e) => {
      const file = $("#attachment").prop("files")[0];
      const fr = new FileReader();
      fr.onload = function () {
        const text = fr.result;

        main.importData(text);
      };
      fr.readAsText(file);
    });
  };

  main.fnLoadData = async function () {
    if (main.mode === "TOKEN") {
      const formData = new FormData();
      formData.append("token", main.token);
      const url =
        "https://test.eventdraw.com.au/frontend/web/site/get-threed-json";

      let response = await fetch(url, {
        method: "POST",
        body: formData,
      });

      main.settings = await response.json();
      if (main.settings === "Incorrect Token") {
        // Swal.fire({
        //   title: "Error",
        //   text: `Incorrect Token!`,
        //   showCancelButton: false,
        //   confirmButtonText: "Okay",
        //   reverseButtons: false,
        // }).then(function (result) {
        //   window.location.href = "404.html";
        // });
        window.location.href = "404.html";
      } 
    } else if (main.mode === "SHARED") {
      try {
        const response = await fetch(`./uploads_scenes/${main.shared}.eventdraw`);
        main.settings = await response.json();
      } catch (e) {
        window.location.href = "404.html";
      }
    }

    return main.settings;
  };

  main.getParameterByName = function (name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    const regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
      results = regex.exec(location.search);
    return results === null
      ? ""
      : decodeURIComponent(results[1].replace(/\+/g, " "));
  };

  main.importData = (jsonStr) => {
    const data = JSON.parse(jsonStr);

    main.settings = data;
    this.newJsonImported.fire();
  };

  main.getCurrentExportData = (fileName) => {
    const {
      groups,
      layout,
      setting,
      shapes,
      platform = {},
      arts = [],
      imageInformation
    } = main.settings;

    const filterData = (data) => {
      const { id, type, children } = data;

      if (children) {
        const newChildren = children.map((d) => {
          return filterData(d);
        });

        return { id, type, children: newChildren };
      }

      return { id, type };
    };

    const exportGroup = groups.map((d) => {
      return filterData(d);
    });

    const exportJson = {
      groups: exportGroup,
      layout,
      setting,
      shapes,
      platform,
      arts,
      imageInformation
    };

    return exportJson;
  };

  main.exportData = (fileName) => {
    const {
      groups,
      layout,
      setting,
      shapes,
      platform = {},
      arts = [],
    } = main.settings;

    const linkElement = document.createElement("a");

    const filterData = (data) => {
      const { id, type, children } = data;

      if (children) {
        const newChildren = children.map((d) => {
          return filterData(d);
        });

        return { id, type, children: newChildren };
      }

      return { id, type };
    };

    const exportGroup = groups.map((d) => {
      return filterData(d);
    });

    const exportJson = {
      groups: exportGroup,
      layout,
      setting,
      shapes,
      platform,
      arts,
    };

    const dataStr = JSON.stringify(exportJson);
    const file = new Blob([dataStr], { type: "application/json" });
    linkElement.href = URL.createObjectURL(file);
    linkElement.download = fileName;
    linkElement.click();

    /* Export Json File Option1 */

    // const dataStr = JSON.stringify({groups, layout, settings, shapes});
    // const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
    // const exportFileDefaultName = 'data.json';

    // const linkElement = document.createElement('a');
    // linkElement.setAttribute('href', dataUri);
    // linkElement.setAttribute('download', exportFileDefaultName);
    // linkElement.click();
  };

  main.onChangedBackground = (backgroundStyle) => {
    const { platform } = main.settings;
    main.settings = {
      ...main.settings,
      platform: { ...platform, background: backgroundStyle },
    };
  };

  main.onChangedWallHeight = (newHeight) => {
    const { platform } = main.settings;
    main.settings = {
      ...main.settings,
      platform: { ...platform, wallheight: newHeight },
    };
  };

  main.onChangedWallOpacity = (newOpacity) => {
    const { platform } = main.settings;
    main.settings = {
      ...main.settings,
      platform: { ...platform, wallOpacity: newOpacity },
    };
  };

  main.onChangedWallTexture = (type, value) => {
    const { platform } = main.settings;
    main.settings = {
      ...main.settings,
      platform: { ...platform, wallTexture: { type, value } },
    };
  };

  main.onChangedFloorOpacity = (newOpacity) => {
    const { platform } = main.settings;
    main.settings = {
      ...main.settings,
      platform: { ...platform, floorOpacity: newOpacity },
    };
  };

  main.onChangedFloorTexture = (value) => {
    const { platform } = main.settings;
    main.settings = {
      ...main.settings,
      platform: { ...platform, floorTexture: value },
    };
  };

  main.onChangedShapeBaseElevation = (element, value) => {
    const { shapes } = main.settings;
    const { id } = element;
    // console.log(main.settings);
    const newShapes = shapes.map((d) => {
      const newPosition = { x: d.position.x, y: value, z: d.position.z };

      if (d.id === id) {
        return { ...d, position: newPosition };
      } else {
        return d;
      }
    });

    main.settings = { ...main.settings, shapes: newShapes };
  };

  main.onChangedShapeHeight = (element, value) => {
    const { shapes } = main.settings;
    const { id } = element;
    const newShapes = shapes.map((d) => {
      const newDimension = {
        width: d.dimension.width,
        height: d.dimension.height,
        depth: value,
      };

      if (d.id === id) {
        return { ...d, dimension: newDimension };
      } else {
        return d;
      }
    });

    main.settings = { ...main.settings, shapes: newShapes };
  };

  main.onChangedTableProperty = (element, value) => {
    const { shapes } = main.settings;
    const { id } = element;
    const newShapes = shapes.map((d) => {
      if (d.id === id) {
        return { ...d, property: value };
      } else {
        return d;
      }
    });

    main.settings = { ...main.settings, shapes: newShapes };
  }

  main.onImportedNewArt = (type, id, path) => {
    const { arts = [] } = main.settings;
    const newArt = { type, id, path };

    main.settings = { ...main.settings, arts: [...arts, { ...newArt }] };
  };

  main.onMovedArt = (id, position) => {
    const { arts = [] } = main.settings;

    const newArts = arts.map((d) => {
      if (d.id === id) {
        return { ...d, position: position };
      } else {
        return d;
      }
    });

    main.settings = { ...main.settings, arts: newArts };
  };

  main.onRotatedArt = (id, rotation) => {
    const { arts = [] } = main.settings;

    const newArts = arts.map((d) => {
      if (d.id === id) {
        return { ...d, rotation };
      } else {
        return d;
      }
    });

    main.settings = { ...main.settings, arts: newArts };
  };

  main.onRemovedArt = (id) => {
    const { arts = [] } = main.settings;

    const newArts = arts.filter((d) => {
      return d.id !== id;
    });

    main.settings = { ...main.settings, arts: newArts };
  };

  main.fireOnNewScene = (callback) => {
    this.newJsonImported.add(callback);
  };

  main.fnInit();
};
