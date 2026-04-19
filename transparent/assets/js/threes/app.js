var App = function () {
  const main = this;

  main.init = function () {
    main.model = new Model();

    const threeViewer = new Main(main.model, "#container");

    main.floorplaner = new FloorPlaner(
      "floorplanner-canvas",
      main.model.floorplan
    );
  };

  main.init();
};
