class WallModel extends THREE.Group {
  width = 0;
  height = 0;
  depth = 0;
  doorHeight = 100;
  boundingBoxMesh = null;
  onChangeHeight = $.Callbacks();

  constructor() {
    super();
  }

  init(info, model, options) {
    const { dimension, rotation, position, number, chairs, property } = info;
    const { width, height, depth } = dimension;

    this.initialPosition = position;
    this.width = width;
    this.height = height;
    this.depth = parseFloat(depth);

    this.doorMesh = model.model.clone();

    const material = new THREE.MeshStandardMaterial({
      color: 0xd6d6d6, // red
      transparent: true,
      side: THREE.DoubleSide,
      opacity: 1, // 0.4
    });

    this.doorMesh.traverse((node) => {
      if (node.isMesh) {
        // node.material = node.material.clone();
        node.material = material;
        node.castShadow = true;
      }
    });

    const size = new THREE.Vector3();
    const boundingBox = new THREE.Box3().setFromObject(this.doorMesh);
    boundingBox.getSize(size);
    this.originSize = new THREE.Vector3(size.x, size.y, size.z);

    const ratex = width / size.x;
    const rateH = this.doorHeight / size.y;

    this.doorMesh.scale.set(ratex, rateH, 1);
    this.doorMesh.rotation.y = (-Math.PI * rotation.y) / 180;
    this.doorMesh.position.set(width / 2, 0, height / 2);
    this.doorMesh.translateZ(height / 2 - 2);

    this.position.set(this.initialPosition.x, 0, this.initialPosition.z);

    this.boundingBoxMesh = new THREE.Mesh(
      new THREE.BoxGeometry(width, this.doorHeight, 30),
      new THREE.MeshBasicMaterial({ color: 0xffcc55 })
    );
    this.boundingBoxMesh.rotation.y = (-Math.PI * rotation.y) / 180;
    this.boundingBoxMesh.position.copy(this.doorMesh.position);
    this.boundingBoxMesh.visible = false;

    this.add(this.doorMesh);
    this.add(this.boundingBoxMesh);

    this.setBaseElevation(this.initialPosition.y);
    if (this.depth !== 0) this.setHeight(this.depth);
  }

  getBaseElevation() {
    return this.doorMesh.position.y;
  }

  setBaseElevation(elevation) {
    this.doorMesh.position.y = elevation;
    this.boundingBoxMesh.position.y = elevation + this.doorHeight / 2;

    this.onChangeHeight.fire();
  }

  setHeight(height) {
    this.doorHeight = height;

    const preHeight = this.getHeight();

    const rateH =
      height == 0 ? 1 : (height / preHeight) * this.doorMesh.scale.y;
    this.doorMesh.scale.y = rateH;
    this.boundingBoxMesh.scale.y = this.doorHeight / 100;
    this.boundingBoxMesh.position.copy(this.doorMesh.position);
    this.boundingBoxMesh.position.y += this.doorHeight / 2;

    this.onChangeHeight.fire();
  }

  setDepth(depth) {
    // const size = new THREE.Vector3();
    // const boundingBox = new THREE.Box3().setFromObject(this.doorMesh);
    // boundingBox.getSize(size);

    // console.log("before", size, depth);
    // this.doorMesh.scale.z = (depth * 1.3) / this.originSize.z;
    // this.doorMesh.position.z = depth / 2;
    // const size1 = new THREE.Vector3();
    // const boundingBox1 = new THREE.Box3().setFromObject(this.doorMesh);
    // boundingBox1.getSize(size1);

    // console.log("after", this.originSize, depth);
    // this.doorMesh.visible = false;
  }

  getHeight() {
    const boundingBox = new THREE.Box3().setFromObject(this.doorMesh);
    const size = new THREE.Vector3();
    boundingBox.getSize(size);
    return size.y;
  }

  isTableType() {
    return false;
  }

  fireOnChangeWall(callback) {
    this.onChangeHeight.add(callback);
  }
}
