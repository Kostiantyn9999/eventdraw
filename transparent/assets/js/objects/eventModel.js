const modelInfos = {
  "Banquet Chair": { mesh: ["3DVG_Banquet_Chair_3"], defaultColor: "#000000" },
  Stage_Piece_01: { mesh: ["stagetop___2_x_1"], defaultColor: null },
};

class EventModel extends THREE.Group {
  width = 0;
  height = 0;
  depth = 0;

  constructor() {
    super();
    this.group = new THREE.Group();
    this.add(this.group);
  }

  init(info, shapeM) {
    const { 
      dimension, 
      position, 
      rotation, 
      fill_color, 
      chair_number,
      show_chair_number
    } = info;
    const { width, height, depth } = dimension;
    
    this.initialPosition = position;
    this.position.set(
      this.initialPosition.x,
      this.initialPosition.y,
      this.initialPosition.z
    );

    const shapeMesh = shapeM.model.clone();
    shapeMesh.traverse((node) => {
      if (node.isMesh) {
        node.material = node.material.clone();
        node.castShadow = true;
        node.material.transparent = true;
        node.material.opacity = 1;
      }
    });

    if (shapeM.default) {
      if (shapeM.default.height) this.defaultHeight = shapeM.default.height;
      if (shapeM.default.base) this.defaultBase = shapeM.default.base;
    }

    this.width = width;
    this.height = height;

    const size = new THREE.Vector3();
    const boundingBox = new THREE.Box3().setFromObject(shapeMesh);
    boundingBox.getSize(size);

    const ratex = width / size.x;
    const ratey = height / size.z;

    const rateH =
      parseFloat(depth) === 0
        ? this.defaultHeight
          ? this.defaultHeight / size.y
          : Math.min(ratex, ratey)
        : depth / size.y;

    shapeMesh.scale.set(ratex, rateH, ratey);

    this.depth = parseFloat(depth) === 0 ? rateH * size.y : parseFloat(depth);

    const scaledBoundingBox = new THREE.Box3().setFromObject(shapeMesh);
    const scaledCenter = new THREE.Vector3();
    const scaledSize = new THREE.Vector3();
    scaledBoundingBox.getCenter(scaledCenter);
    scaledBoundingBox.getSize(scaledSize);

    
    shapeMesh.position.set(
      -scaledCenter.x,
      scaledSize.y / 2 - scaledCenter.y,
      -scaledCenter.z
    );

    this.position.x += scaledSize.x / 2
    this.position.z += scaledSize.z / 2

    this.rotation.y = (-Math.PI * rotation.y) / 180;

    this.shapeMesh = shapeMesh;
    this.group.add(this.shapeMesh);

    if (show_chair_number && show_chair_number != "0")  {
      this.createChairNumber(chair_number);
    }
  }

  getSpriteMaterial(number) {
    const textHeight = 40;

    const canvas = document.createElement("canvas");
    const context = canvas.getContext("2d");
    canvas.width = 60;
    canvas.height = 60;
    const radius = 25;

    context.beginPath();
    context.arc(
      canvas.width / 2,
      canvas.height / 2,
      radius,
      0,
      2 * Math.PI,
      false
    );
    context.fillStyle = "#222222";
    context.fill();
    context.lineWidth = 5;
    context.strokeStyle = "black";
    context.stroke();

    context.font = "normal " + textHeight + "px Arial";
    context.textAlign = "center";
    context.textBaseline = "middle";
    context.fillStyle = "#ffffff";
    context.fillText(number, canvas.width / 2, canvas.height / 2);

    const sprintTexture = new THREE.Texture(canvas);
    sprintTexture.needsUpdate = true;
    const spriteMaterial = new THREE.SpriteMaterial({
      map: sprintTexture,
    });
    return spriteMaterial;
  }

  createChairNumber(number) {
    const spriteMaterial = this.getSpriteMaterial(number);
    this.chairNumberMesh = new THREE.Sprite(spriteMaterial);
    this.chairNumberMesh.position.copy(this.shapeMesh.position)
    this.chairNumberMesh.position.y = this.getHeight() + 8;
    this.chairNumberMesh.scale.multiplyScalar(13);
    
    this.add(this.chairNumberMesh);
  }

  getBaseElevation() {
    return this.position.y;
  }

  setBaseElevation(elevation) {
    return (this.position.y = parseFloat(elevation));
  }

  getHeight() {
    const boundingBox = new THREE.Box3().setFromObject(this.group);
    const size = new THREE.Vector3();
    boundingBox.getSize(size);
    return size.y;
  }

  setHeight(height) {
    const preHeight = this.getHeight();

    const rateH = height == 0 ? 1 : (height / preHeight) * this.group.scale.y;
    this.group.scale.y = rateH;

    if (this.chairNumberMesh) {
      this.chairNumberMesh.position.y = this.getHeight() + 8;
    }
  }

  resetElevation() {
    return this.initialPosition.y;
  }

  resetHeight() {
    return this.depth;
  }

  setOldPosition() {
    this.oldPosition = this.position.clone();
  }

  setPosition(position) {
    this.position.set(
      position.x,
      position.y,
      position.z
    )
  }

  isTableType() {
    return false;
  }
}
