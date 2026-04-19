class HorizontalWallModel extends THREE.Group {
  wallHeight = 3 * 39.37;
  wallObjectMesh = [];

  wallThikness = 1;

  constructor() {
    super();
  }

  init(info, wallHeight) {
    const { dimension, position, rotation } = info;
    this.modelPosition = position;
    this.modelRotation = rotation;
    this.dimension = dimension;
    this.wallHeight = wallHeight;

    this.material = new THREE.MeshStandardMaterial({
      color: 0xd6d6d6, // red
      transparent: true,
      side: THREE.DoubleSide,
      opacity: 1,
    });

    this.createMesh();
  }

  createMesh() {
    const { x, y, z } = this.modelPosition;
    const { width, height, depth } = this.dimension;
    this.wallThikness = height;

    const geometry = new THREE.BoxGeometry(width, this.wallHeight, height);
    this.wallMesh = new THREE.Mesh(geometry, this.material);
    this.wallMesh.castShadow = true;
    if (this.wallMesh.material.map) this.wallMesh.material.map.anisotropy = 16;

    const edges = new THREE.EdgesGeometry(geometry);
    this.lineMesh = new THREE.LineSegments(
      edges,
      new THREE.LineBasicMaterial({ color: 0xffffff })
    );

    this.wallMesh.position.set(
      parseFloat(x) + width / 2,
      parseFloat(y) + this.wallHeight / 2,
      parseFloat(z) + height / 2
    );

    this.wallMesh.rotation.set(
      (Math.PI * this.modelRotation.x) / 180,
      (-Math.PI * this.modelRotation.y) / 180,
      (Math.PI * this.modelRotation.z) / 180
    );

    this.lineMesh.position.set(
      parseFloat(x) + width / 2,
      parseFloat(y) + this.wallHeight / 2,
      parseFloat(z) + height / 2
    );

    this.lineMesh.rotation.set(
      (Math.PI * this.modelRotation.x) / 180,
      (-Math.PI * this.modelRotation.y) / 180,
      (Math.PI * this.modelRotation.z) / 180
    );

    this.holedMesh = this.wallMesh.clone();
    this.holedMesh.visible = true;

    this.add(this.lineMesh);
    this.add(this.wallMesh);
  }

  setHeight(height) {
    this.remove(this.wallMesh);
    this.remove(this.lineMesh);
    this.remove(this.holedMesh);
    this.wallHeight = height;

    this.createMesh();
    this.createHole();
  }

  // ! Need to check this - Got error
  addWallObjectMesh(object) {
    // console.log(object);
    object.fireOnChangeWall(this.createHoles);
    this.wallObjectMesh.push(object);
  }

  checkOverlap(object) {
    const boundingBox1 = new THREE.Box3().setFromObject(this);
    const boundingBox2 = new THREE.Box3().setFromObject(object);

    return boundingBox1.intersectsBox(boundingBox2);
  }

  createHole() {
    this.remove(this.holedMesh);

    this.wallObjectMesh.forEach((door) => {
      const wallPos = new THREE.Vector3();
      this.wallMesh.getWorldPosition(wallPos);

      const doorPos = new THREE.Vector3();
      door.doorMesh.getWorldPosition(doorPos);

      const wallOrigin = this.holedMesh.clone();
      wallOrigin.position.copy(wallPos);

      door.boundingBoxMesh.position.copy(doorPos);
      door.boundingBoxMesh.position.y += door.doorHeight / 2;
      door.setDepth(this.wallThikness);

      // console.log(wallOrigin);
      const csgPrimaryCube = new ThreeBSP(wallOrigin);

      // console.log(door.boundingBoxMesh);
      const csgSecondaryCube = new ThreeBSP(door.boundingBoxMesh);
      const subtraction = csgPrimaryCube.subtract(csgSecondaryCube);
      const convertedMesh = subtraction.toMesh();
      convertedMesh.material = this.wallMesh.material;
      convertedMesh.needsUpdate = true;
      convertedMesh.position.copy(this.wallMesh.position);
      this.holedMesh = convertedMesh;
    });

    this.add(this.holedMesh);
    this.remove(this.wallMesh);
  }

  createHoles() {
    this.remove(this.wallMesh);
    this.remove(this.lineMesh);
    this.remove(this.holedMesh);

    this.createMesh();
    this.createHole();
  }

  setOpacity(opacity) {}
}
