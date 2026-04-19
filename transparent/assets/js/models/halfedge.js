var HalfEdge = function (room, wall, front) {
  const main = this;

  this.wall = wall;

  /** transform from world coords to wall planes (z=0) */
  main.interiorTransform = new THREE.Matrix4();

  /** transform from world coords to wall planes (z=0) */
  main.invInteriorTransform = new THREE.Matrix4();

  /** transform from world coords to wall planes (z=0) */
  main.exteriorTransform = new THREE.Matrix4();

  /** transform from world coords to wall planes (z=0) */
  main.invExteriorTransform = new THREE.Matrix4();

  main.redrawCallbacks = $.Callbacks();

  main.init = function () {
    this.front = front || false;
    this.offset = this.wall.thickness / 2.0;
    this.height = this.wall.height;

    if (this.front) {
      this.wall.frontEdge = this;
    } else {
      this.wall.backEdge = this;
    }
  };

  main.getStart = function () {
    if (this.front) {
      return this.wall.getStart();
    } else {
      return this.wall.getEnd();
    }
  };

  main.getEnd = function () {
    if (this.front) {
      return this.wall.getEnd();
    } else {
      return this.wall.getStart();
    }
  };

  main.interiorStart = function () {
    var vec = this.halfAngleVector(this.prev, this);
    return {
      x: this.getStart().x + vec.x,
      y: this.getStart().y + vec.y,
    };
  };

  main.interiorEnd = function () {
    var vec = this.halfAngleVector(this, this.next);
    return {
      x: this.getEnd().x + vec.x,
      y: this.getEnd().y + vec.y,
    };
  };

  main.exteriorEnd = function () {
    var vec = this.halfAngleVector(this, this.next);

    return {
      x: this.getEnd().x - vec.x,
      y: this.getEnd().y - vec.y,
    };
  };

  main.exteriorStart = function () {
    var vec = this.halfAngleVector(this.prev, this);

    return {
      x: this.getStart().x - vec.x,
      y: this.getStart().y - vec.y,
    };
  };

  /**
   * Gets CCW angle from v1 to v2
   */
  main.halfAngleVector = function (v1, v2) {
    // console.log(v1, v2);

    // make the best of things if we dont have prev or next
    if (!v1) {
      var v1startX = v2.getStart().x - (v2.getEnd().x - v2.getStart().x);
      var v1startY = v2.getStart().y - (v2.getEnd().y - v2.getStart().y);
      var v1endX = v2.getStart().x;
      var v1endY = v2.getStart().y;
    } else {
      var v1startX = v1.getStart().x;
      var v1startY = v1.getStart().y;
      var v1endX = v1.getEnd().x;
      var v1endY = v1.getEnd().y;
    }

    if (!v2) {
      var v2startX = v1.getEnd().x;
      var v2startY = v1.getEnd().y;
      var v2endX = v1.getEnd().x + (v1.getEnd().x - v1.getStart().x);
      var v2endY = v1.getEnd().y + (v1.getEnd().y - v1.getStart().y);
    } else {
      var v2startX = v2.getStart().x;
      var v2startY = v2.getStart().y;
      var v2endX = v2.getEnd().x;
      var v2endY = v2.getEnd().y;
    }

    // CCW angle between edges
    var theta = Util.angle2pi(
      v1startX - v1endX,
      v1startY - v1endY,
      v2endX - v1endX,
      v2endY - v1endY
    );

    // cosine and sine of half angle
    var cs = Math.cos(theta / 2.0);
    var sn = Math.sin(theta / 2.0);

    // rotate v2
    var v2dx = v2endX - v2startX;
    var v2dy = v2endY - v2startY;

    var vx = v2dx * cs - v2dy * sn;
    var vy = v2dx * sn + v2dy * cs;

    // normalize
    var mag = Util.distance(0, 0, vx, vy);
    var desiredMag = this.offset / sn;
    var scalar = desiredMag / mag;

    var halfAngleVector = {
      x: vx * scalar,
      y: vy * scalar,
    };

    return halfAngleVector;
  };

  main.generatePlane = function () {
    function transformCorner(corner) {
      return new THREE.Vector3(corner.x, 0, corner.y);
    }

    var v1 = transformCorner(this.interiorStart());
    var v2 = transformCorner(this.interiorEnd());
    var v3 = v2.clone();
    v3.y = this.wall.height;
    var v4 = v1.clone();
    v4.y = this.wall.height;

    // ! Migrate V137
    var geometry = new THREE.BufferGeometry();
    geometry.setFromPoints([v1, v2, v3, v4]);
    geometry.setIndex([0, 1, 2, 0, 2, 3]);
    geometry.computeVertexNormals();

    // // geometry.vertices = [v1, v2, v3, v4];
    // // geometry.faces.push(new THREE.Face3(0, 1, 2));
    // // geometry.faces.push(new THREE.Face3(0, 2, 3));
    // // geometry.computeFaceNormals();
    // geometry.computeVertexNormals();
    // geometry.computeBoundingBox();

    this.plane = new THREE.Mesh(
      geometry,
      new THREE.MeshBasicMaterial({
        alphaTest: 0,
        visible: false,
        side: THREE.DoubleSide,
      })
    );
    this.plane.visible = false;
    this.plane.edge = this; // js monkey patch

    this.computeTransforms(
      this.interiorTransform,
      this.invInteriorTransform,
      this.interiorStart(),
      this.interiorEnd()
    );
    this.computeTransforms(
      this.exteriorTransform,
      this.invExteriorTransform,
      this.exteriorStart(),
      this.exteriorEnd()
    );

    // console.table(this.interiorTransform, this.invInteriorTransform);
  };

  main.computeTransforms = function (transform, invTransform, start, end) {
    var v1 = start;
    var v2 = end;

    var angle = Util.angle(1, 0, v2.x - v1.x, v2.y - v1.y);

    var tt = new THREE.Matrix4();
    tt.makeTranslation(-v1.x, 0, -v1.y);
    var tr = new THREE.Matrix4();
    tr.makeRotationY(-angle);
    transform.multiplyMatrices(tr, tt);
    invTransform.getInverse(transform);
  };

  main.init();
};
