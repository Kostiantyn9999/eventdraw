var ModelManager = function (model) {
  const main = this;

  this.model = model;

  main.url = "assets/test/";
  // main.serverUrl = "assets/data/scenes.json";

  main.serverUrl = `https://3d.eventdraw.com.au/eventdraw_api/public/api/shapes/newversion?t=${Date.now().toString(
    36
  )}`;
  main.availableList = [];
  main.model_list = [];
  // this.loaded_model_lists = $.Callbacks();
  // this.loaded_model_lists = $.Callbacks();

  main.init = function () {
    main.loadModelData();

    // this.loaded_model_lists.add(main.loadModels);
  };

  main.loadModelData = function () {
    let response = fetch(this.serverUrl, {
      // method: 'POST',
    })
      .then((res) => res.json())
      .then((data) => {
        // console.log(data);
        // this.loaded_model_lists.fire(main.loadModels);
        main.model_list = data;
      });
  };

  main.loadModels = function () {
    main.model_list.forEach((model) => {
      // var gltfLoader = new THREE.GLTFLoader();
      // if (!main.availableList.includes(model.name)) {
      //     return;
      // }
      // gltfLoader.load(main.url + model.file + ".gltf", (gltf) => {
      // });
    });

    this.model.loadedModel();
  };

  main.setModelList = function (list) {
    main.availableList = list;

    main.loadModels();
  };

  main.init();
};
