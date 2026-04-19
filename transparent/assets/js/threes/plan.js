var Plan = function (scene, floorplan, controls) {
  const main = this;

  this.scene = scene; // Wall Group
  this.floorplan = floorplan;

  this.edges = [];

  main.init = function () {
    this.floorplan.fireOnUpdatedRooms(main.redraw);
  };

  main.redraw = function () {
    // console.log("Redraw", scene);

    main.edges.forEach((edge) => {
      edge.remove();
    });

    main.floorplan.wallEdges().forEach((edge) => {
      const threeEdge = new Edge(scene, edge);
      main.edges.push(threeEdge);
    });

    main.floorplan.wallEdgePlanes().forEach((edge) => {
      // console.log(edge);
      scene.add(edge);
    });
  };

  main.init();
};
