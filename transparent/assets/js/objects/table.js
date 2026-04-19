class TableObject extends THREE.Group {
  width = 0;
  height = 0;
  depth = 0;
  chairNumberMeshs = [];

  constructor() {
    super();

    this.group = new THREE.Group();
    this.add(this.group);
  }

  init(info, model, chairModel) {
    const {
      dimension,
      rotation,
      position,
      number,
      show_table_number,
      show_chair_numbers,
      fill_color,
      chairs,
      property,
    } = info;

    const { width, height, depth } = dimension;

    this.initialPosition = position;
    this.position.set(
      this.initialPosition.x,
      this.initialPosition.y,
      this.initialPosition.z
    );

    this.tableMesh = model.model.clone();
    this.tableMesh.traverse((node) => {
      if (node.isMesh) {
        node.material = node.material.clone();
        if (fill_color !== null) {
          node.material.color = new THREE.Color(fill_color);
        } else {
          node.material.color = new THREE.Color(0x000000);
        }

        node.castShadow = true;
      }
    });
    const chairMesh = chairModel.model.clone();
    chairMesh.traverse((node) => {
      if (node.isMesh) {
        node.material = node.material.clone();
        node.castShadow = true;
      }
    });

    if (model.default) {
      if (model.default.height) this.defaultHeight = model.default.height;
      if (model.default.base) this.defaultBase = model.default.base;
    }

    const size = new THREE.Vector3();
    const boundingBox = new THREE.Box3().setFromObject(this.tableMesh);
    boundingBox.getSize(size);

    this.width = width;
    this.height = height;

    const ratex = width / size.x;
    const ratey = height / size.z;
    const rateH =
      parseFloat(depth) == 0
        ? this.defaultHeight
          ? this.defaultHeight / size.y
          : Math.min(ratex, ratey)
        : depth / size.y;

    this.tableMesh.scale.set(ratex, rateH, ratey);
    this.tableMesh.rotation.y = (-Math.PI * rotation.y) / 180;
    this.tableMesh.position.set(width / 2, 0, height / 2);

    this.depth = parseFloat(depth) === 0 ? rateH * size.y : parseFloat(depth);

    this.group.add(this.tableMesh);

    this.loadChairs(chairs, chairMesh, show_chair_numbers);

    this.setProperty(property);
    if (show_table_number && show_table_number != "0")  {
      this.createTableNumber(number);
    }
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

    if (this.numberMesh) {
      this.numberMesh.position.y = this.getHeight() + 18;
    }
    this.chairNumberMeshs.forEach((numberMesh) => {
      numberMesh.position.y = this.getHeight() + 8;
    })
  }

  getProperty() {
    return this.property;
  }

  setProperty({ table, chair, showTableNumber } = {}) {
    if (table) {
      this.property = { ...this.property, table };

      // console.log("Change Table");
      const { r, g, b } = table;
      this.tableMesh.traverse((node) => {
        if (node.isMesh) {
          node.material.color = new THREE.Color().setRGB(
            r / 256,
            g / 256,
            b / 256
          );
          node.material.needsUpdate = true;
        }
      });
    }

    if (chair) {
      this.property = { ...this.property, chair };

      const { r, g, b } = chair;
      this.group.children.forEach((node) => {
        if (node.uuid !== this.tableMesh.uuid) {
          node.traverse((n) => {
            if (n.isMesh) {
              if (n.name == "3DVG_Banquet_Chair_3") {
                n.material.color = new THREE.Color().setRGB(
                  r / 256,
                  g / 256,
                  b / 256
                );
                n.material.needsUpdate = true;
              }
            }
          });
        }
      });
    }

    if (showTableNumber != undefined) {
      this.numberMesh.visible = showTableNumber;
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

  createTableNumber(number) {
    const spriteMaterial = this.getSpriteMaterial(number);
    this.numberMesh = new THREE.Sprite(spriteMaterial);
    this.numberMesh.position.y = 50;
    this.numberMesh.scale.multiplyScalar(20);
    this.numberMesh.position.x = this.width / 2;
    this.numberMesh.position.z = this.height / 2;

    this.add(this.numberMesh);
  }

  createChairNumbers(number, position) {
    const spriteMaterial = this.getSpriteMaterial(number);
    const numberMesh = new THREE.Sprite(spriteMaterial);
    numberMesh.position.copy(position);
    numberMesh.position.y = 40;
    numberMesh.scale.multiplyScalar(13);
    this.chairNumberMeshs.push(numberMesh);

    this.add(numberMesh);
  }

  loadChairs(chairs, cm, show_chair_numbers) {
    const size = new THREE.Vector3();
    const boundingBox = new THREE.Box3().setFromObject(cm);
    boundingBox.getSize(size);

    chairs.forEach((chair) => {
      const chairMesh = cm.clone();
      chairMesh.traverse((node) => {
        if (node.isMesh) {
          node.material = node.material.clone();
          if (node.name === "3DVG_Banquet_Chair_3") {
            node.material.color = new THREE.Color("#000000");
          }
          node.castShadow = true;
        }
      });

      const { dimension, rotation, position, number } = chair;
      const { width, height } = dimension;
      const scaleX = width / size.x;

      chairMesh.position.set(
        parseFloat(position.x) + width / 2,
        position.y,
        parseFloat(position.z) + height / 2
      );

      chairMesh.rotation.set(
        (Math.PI * rotation.x) / 180,
        (-Math.PI * rotation.y) / 180,
        (Math.PI * rotation.z) / 180
      );

      chairMesh.scale.set(scaleX, 30 / size.y, scaleX);
      this.group.add(chairMesh);
      
      if (show_chair_numbers && show_chair_numbers != "0") {
        this.createChairNumbers(number, chairMesh.position);
      }
    });
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
    return true;
  }
}
