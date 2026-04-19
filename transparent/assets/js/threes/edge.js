const fillerColor = 0xdddddd;
const sideColor = 0xcccccc;

var Edge = function (scene, edge) {
  const main = this;

  main.planes = [];
  main.basePlanes = [];

  main.edge = edge;
  main.wall = edge.wall;

  main.wallMaterial = new THREE.MeshBasicMaterial({
    side: THREE.DoubleSide,
    transparent: true,
    opacity: 1, // 0.3
  });

  main.init = function () {
    // main.updateTexture();
    main.edge.redrawCallbacks.add(main.redraw);
    main.updatePlanes();
    main.addToScene();
  };

  main.redraw = function () {
    main.removeFromScene();
    main.updatePlanes();
    main.addToScene();
  };

  main.setColor = function (color) {
    main.color = color;
  };

  main.updateTexture = function () {
    const textureData = edge.getTexture();
    const stretch = textureData.stretch;
    const url = textureData.url;
    const scale = textureData.scale;
    main.texture = new THREE.TextureLoader().load("assets/images/" + url, null);
    if (!stretch) {
      const height = wall.height;
      const width = edge.interiorDistance();
      main.texture.wrapT = THREE.RepeatWrapping;
      main.texture.wrapS = THREE.RepeatWrapping;
      main.texture.repeat.set(width / scale, height / scale);
      main.texture.needsUpdate = true;
    }
  };

  main.updatePlanes = function () {
    main.wallMaterial.map = main.wall.texture ? main.wall.texture : null;
    main.wallMaterial.color = main.wall.color;
    main.wallMaterial.opacity = main.wall.opacity;
    main.wallMaterial.needsUpdate = true;

    const fillerMaterial = new THREE.MeshBasicMaterial({
      color: fillerColor,
      side: THREE.DoubleSide,
      // transparent:true,
      // opacity: 0.5
    });

    // exterior plane
    main.planes.push(
      main.makeWall(
        edge.exteriorStart(),
        edge.exteriorEnd(),
        edge.exteriorTransform,
        edge.invExteriorTransform,
        main.wallMaterial
      )
    );

    main.planes.push(
      main.makeWall(
        edge.interiorStart(),
        edge.interiorEnd(),
        edge.interiorTransform,
        edge.invInteriorTransform,
        main.wallMaterial
      )
    );

    // top
    this.planes.push(
      main.buildFiller(edge, main.wall.height, THREE.DoubleSide, fillerColor)
    );

    this.planes.push(
      main.buildSideFilter(
        edge.interiorStart(),
        edge.exteriorStart(),
        this.wall.height,
        sideColor
      )
    );

    this.planes.push(
      main.buildSideFilter(
        edge.interiorEnd(),
        edge.exteriorEnd(),
        this.wall.height,
        sideColor
      )
    );
  };

  main.addToScene = function () {
    main.planes.forEach((plane) => {
      scene.add(plane);
    });
  };

  // three.js v129 version
  main.makeWall = function (start, end, transform, invTransform, material) {
    const v1 = main.toVec3(start);
    const v2 = main.toVec3(end);
    const v3 = v2.clone();
    v3.y = main.wall.height;
    const v4 = v1.clone();
    v4.y = main.wall.height;

    const points = [v1.clone(), v2.clone(), v3.clone(), v4.clone()];

    points.forEach((p) => {
      p.applyMatrix4(transform);
    });

    const shape = new THREE.Shape([
      new THREE.Vector2(points[0].x, points[0].y),
      new THREE.Vector2(points[1].x, points[1].y),
      new THREE.Vector2(points[2].x, points[2].y),
      new THREE.Vector2(points[3].x, points[3].y),
    ]);

    // add holes for each wall item
    main.wall.items.forEach((item) => {
      const pos = item.position.clone();
      pos.applyMatrix4(transform);
      const halfSize = item.halfSize;
      const min = halfSize.clone().multiplyScalar(-1);
      const max = halfSize.clone();
      min.add(pos);
      max.add(pos);

      const holePoints = [
        new THREE.Vector2(min.x, min.y),
        new THREE.Vector2(max.x, min.y),
        new THREE.Vector2(max.x, max.y),
        new THREE.Vector2(min.x, max.y),
      ];

      shape.holes.push(new THREE.Path(holePoints));
    });

    const geometry = new THREE.ShapeGeometry(shape);

    geometry.vertices.forEach((v) => {
      v.applyMatrix4(invTransform);
    });

    // make UVs
    const totalDistance = Util.distance(v1.x, v1.z, v2.x, v2.z);
    const height = main.wall.height;
    geometry.faceVertexUvs[0] = [];

    function vertexToUv(vertex) {
      var x = Util.distance(v1.x, v1.z, vertex.x, vertex.z) / totalDistance;
      var y = vertex.y / height;
      return new THREE.Vector2(x, y);
    }

    geometry.faces.forEach((face) => {
      const vertA = geometry.vertices[face.a];
      const vertB = geometry.vertices[face.b];
      const vertC = geometry.vertices[face.c];
      geometry.faceVertexUvs[0].push([
        vertexToUv(vertA),
        vertexToUv(vertB),
        vertexToUv(vertC),
      ]);
    });

    geometry.faceVertexUvs[1] = geometry.faceVertexUvs[0];

    geometry.computeFaceNormals();
    geometry.computeVertexNormals();

    const mesh = new THREE.Mesh(geometry, material);
    mesh.receiveShadow = false;
    mesh.castShadow = true;
    return mesh;
  };

  // three.js v140 version
  // main.makeWall = function (start, end, transform, invTransform, material) {
  //   var v1 = main.toVec3(start);
  //   var v2 = main.toVec3(end);
  //   var v3 = v2.clone();
  //   v3.y = main.wall.height;
  //   var v4 = v1.clone();
  //   v4.y = main.wall.height;

  //   var points = [v1.clone(), v2.clone(), v3.clone(), v4.clone()];

  //   points.forEach((p) => {
  //     p.applyMatrix4(transform);
  //   });

  //   var shape = new THREE.Shape([
  //     new THREE.Vector2(points[0].x, points[0].y),
  //     new THREE.Vector2(points[1].x, points[1].y),
  //     new THREE.Vector2(points[2].x, points[2].y),
  //     new THREE.Vector2(points[3].x, points[3].y),
  //   ]);

  //   // add holes for each wall item
  //   main.wall.items.forEach((item) => {
  //     var pos = item.position.clone();
  //     pos.applyMatrix4(transform);
  //     var halfSize = item.halfSize;
  //     var min = halfSize.clone().multiplyScalar(-1);
  //     var max = halfSize.clone();
  //     min.add(pos);
  //     max.add(pos);

  //     var holePoints = [
  //       new THREE.Vector2(min.x, min.y),
  //       new THREE.Vector2(max.x, min.y),
  //       new THREE.Vector2(max.x, max.y),
  //       new THREE.Vector2(min.x, max.y),
  //     ];

  //     shape.holes.push(new THREE.Path(holePoints));
  //   });

  //   // ! THREE.js V137
  //   var geometry = new THREE.ShapeGeometry(shape);
  //   geometry.applyMatrix4(invTransform);

  //   // geometry.vertices.forEach((v) => {
  //   //   v.applyMatrix4(invTransform);
  //   // });

  //   geometry.computeVertexNormals();

  //   // material.map = new THREE.TextureLoader().load('http://localhost/EventDraw-Main/assets/textures/brick/bricks_1007.jpg');
  //   // material.color = new THREE.Color(0xff0000);

  //   // make UVs
  //   var totalDistance = Util.distance(v1.x, v1.z, v2.x, v2.z);
  //   var height = main.wall.height;
  //   // geometry.faceVertexUvs[0] = [];

  //   function vertexToUv(vertex) {
  //     var x = Util.distance(v1.x, v1.z, vertex.x, vertex.z) / totalDistance;
  //     var y = vertex.y / height;
  //     return new THREE.Vector2(x, y);
  //   }

  //   // geometry.faces.forEach((face) => {
  //   //   var vertA = geometry.vertices[face.a];
  //   //   var vertB = geometry.vertices[face.b];
  //   //   var vertC = geometry.vertices[face.c];
  //   //   geometry.faceVertexUvs[0].push([
  //   //     vertexToUv(vertA),
  //   //     vertexToUv(vertB),
  //   //     vertexToUv(vertC)]);
  //   // });

  //   // geometry.faceVertexUvs[1] = geometry.faceVertexUvs[0];
  //   // geometry.computeFaceNormals();

  //   geometry.attributes.position.needsUpdate = true;
  //   geometry.computeVertexNormals();

  //   // ! make UVs three.js
  //   let uvAttribute = geometry.attributes.uv;
  //   for (var i = 0; i < uvAttribute.count; i++) {
  //     // var u = uvAttribute.getX(i);
  //     // var v = uvAttribute.getY(i);
  //     const x = geometry.attributes.position.getX(i);
  //     const y = geometry.attributes.position.getY(i);
  //     const z = geometry.attributes.position.getZ(i);
  //     const vc = vertexToUv(new THREE.Vector3(x, y, z));
  //     uvAttribute.setXY(i, vc.x, vc.y);
  //   }

  //   geometry.attributes.uv.needsUpdate = true;

  //   var mesh = new THREE.Mesh(geometry, material);
  //   mesh.receiveShadow = false;
  //   mesh.castShadow = true;
  //   return mesh;
  // };

  main.buildFiller = function (edge, height, side, color) {
    const points = [
      main.toVec2(edge.exteriorStart()),
      main.toVec2(edge.exteriorEnd()),
      main.toVec2(edge.interiorEnd()),
      main.toVec2(edge.interiorStart()),
    ];

    const fillerMaterial = new THREE.MeshBasicMaterial({
      color: color,
      side: side,
    });

    const shape = new THREE.Shape(points);
    const geometry = new THREE.ShapeGeometry(shape);

    const filler = new THREE.Mesh(geometry, fillerMaterial);
    filler.rotation.set(Math.PI / 2, 0, 0);
    filler.position.y = height;
    return filler;
  };

  main.buildSideFilter = function (p1, p2, height, color) {
    const points = [
      main.toVec3(p1),
      main.toVec3(p2),
      main.toVec3(p2, height),
      main.toVec3(p1, height),
    ];

    // ! Migrate V137
    const geometry = new THREE.BufferGeometry();
    geometry.setFromPoints(points);
    geometry.setIndex([0, 1, 2, 0, 2, 3]);
    geometry.computeVertexNormals();

    // var geometry = new THREE.Geometry();
    // points.forEach((p) => {
    //   geometry.vertices.push(p);
    // });
    // geometry.faces.push(new THREE.Face3(0, 1, 2));
    // geometry.faces.push(new THREE.Face3(0, 2, 3));

    const fillerMaterial = new THREE.MeshBasicMaterial({
      color: color,
      side: THREE.DoubleSide,
    });

    return new THREE.Mesh(geometry, fillerMaterial);
  };

  main.toVec2 = function (pos) {
    return new THREE.Vector2(pos.x, pos.y);
  };

  main.toVec3 = function (pos, height) {
    height = height || 0;
    return new THREE.Vector3(pos.x, height, pos.y);
  };

  main.remove = function () {
    main.removeFromScene();
    main.edge.redrawCallbacks.remove(main.redraw);
  };

  main.removeFromScene = function () {
    this.planes.forEach((plane) => {
      scene.remove(plane);
    });

    this.basePlanes.forEach((plane) => {
      scene.remove(plane);
    });

    this.planes = [];
    this.basePlanes = [];
  };

  main.init();
};
