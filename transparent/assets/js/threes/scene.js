var Scene = function (model) {
  const main = this;

  this.model = model;

  main.init = function () {
    main.scene = new THREE.Scene();

    main.manager = new ModelManager(this.model);
  };

  main.getScene = function () {
    return main.scene;
  };

  main.add = function (mesh) {
    main.scene.add(mesh);
  };

  main.remove = function (mesh) {
    this.scene.remove(mesh);
  };

  main.setAvailableModelList = function (model_list) {
    main.manager.setModelList(model_list);
  };

  main.init();
};
