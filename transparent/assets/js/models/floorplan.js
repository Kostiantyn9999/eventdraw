var FloorPlan = function () {
  const main = this;

  this.walls = [];
  this.corners = [];
  this.rooms = [];

  this.updated_rooms = $.Callbacks();

  main.init = function () {};

  main.loadFloorplan = function (floorplan) {
    floorplan.shapes.forEach((shape) => {
      if (shape.name == "Wall") {
        // console.log(floorplan);
        var newWall = main.newWalls(shape.points);
      }
    });
  };

  main.newWalls = function (points) {
    if (points == undefined) return;

    var start = null;

    points.forEach((element) => {
      var corner = main.newCorner(element.x, element.y);

      // console.log(start);
      if (start != null) {
        // console.log("New Wall");
        main.newWall(start, corner);
      }

      start = corner;
    });

    this.update();
  };

  main.newCorner = function (x, y) {
    var corner = new Corner(this, x, y);
    // corner.fireOnDelete((corner) => {

    // });
    this.corners.push(corner);
    return corner;
  };

  // points list from api
  main.newWall = function (start, end) {
    var wall = new Wall(start, end);
    // wall.fireOnDelete(() => {

    // });
    this.walls.push(wall);
    return wall;
  };

  main.update = function () {
    // console.log(this.walls, this.corners);
    // console.log("Update");

    this.assignOrphanEdges();
    this.updated_rooms.fire();
  };

  main.assignOrphanEdges = function () {
    // kinda hacky
    // find orphaned wall segments (i.e. not part of rooms) and
    // give them edges
    var orphanWalls = [];
    this.walls.forEach((wall) => {
      if (!wall.backEdge && !wall.frontEdge) {
        wall.orphan = true;
        var back = new HalfEdge(null, wall, false);
        back.generatePlane();
        // var front = new HalfEdge(null, wall, true);
        // front.generatePlane();
        orphanWalls.push(wall);
      }
    });
  };

  // All Edge of plan
  main.wallEdges = function () {
    var edges = [];

    this.walls.forEach((wall) => {
      if (wall.frontEdge) {
        edges.push(wall.frontEdge);
      }
      if (wall.backEdge) {
        edges.push(wall.backEdge);
      }
    });

    return edges;
  };

  main.wallEdgePlanes = function () {
    var planes = [];
    this.walls.forEach((wall) => {
      if (wall.frontEdge) {
        planes.push(wall.frontEdge.plane);
      }
      if (wall.backEdge) {
        planes.push(wall.backEdge.plane);
      }
    });
    return planes;
  };

  main.fireOnUpdatedRooms = function (callbacks) {
    main.updated_rooms.add(callbacks);
  };

  main.getCenter = function () {
    return this.getDimension(true);
  };

  main.getSize = function () {
    return this.getDimension(false);
  };

  main.getDimension = function (center) {
    var xMin = Infinity;
    var xMax = -Infinity;
    var zMin = Infinity;
    var zMax = -Infinity;

    this.corners.forEach((corner) => {
      if (corner.x < xMin) xMin = corner.x;
      if (corner.x > xMax) xMax = corner.x;
      if (corner.y < zMin) zMin = corner.y;
      if (corner.y > zMax) zMax = corner.y;
    });

    var ret;
    if (
      xMin == Infinity ||
      xMin == Infinity ||
      xMin == Infinity ||
      xMin == Infinity
    ) {
      ret = new THREE.Vector3();
    } else {
      if (center) {
        ret = new THREE.Vector3((xMin + xMax) * 0.5, 0, (zMin + zMax) * 0.5);
      } else {
        ret = new THREE.Vector3(xMax - xMin, 0, zMax - zMin);
      }
    }

    return ret;
  };

  main.init();
};
