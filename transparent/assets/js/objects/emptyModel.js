class EmptyModel extends THREE.Group {
  constructor() {
    super();
  }

  init(info, textControl) {
    const { dimension, position, rotation, name } = info;
    const { width, height, depth } = dimension;

    this.initialPosition = position;
    this.position.set(
      this.initialPosition.x,
      this.initialPosition.y,
      this.initialPosition.z
    );

    const objectHeight = parseFloat(depth) === 0 ? 2 : parseFloat(depth);

    const geo = new THREE.BoxBufferGeometry(width, objectHeight, height);
    const mat = new THREE.MeshBasicMaterial({
      color: 0xffffff,
      transparent: true,
      opacity: 1, // 0.1
    });
    const cube = new THREE.Mesh(geo, mat);

    cube.position.set(width / 2, objectHeight / 2, height / 2);
    cube.rotation.y = (-Math.PI * rotation.y) / 180;

    const myText = new SpriteText(name, 10, "black");
    myText.position.set(width / 2, 25, height / 2);

    const edges = new THREE.EdgesGeometry(geo);
    const line = new THREE.LineSegments(
      edges,
      new THREE.LineBasicMaterial({ color: 0xffffff })
    );

    line.position.set(width / 2, objectHeight / 2, height / 2);
    line.rotation.y = (-Math.PI * rotation.y) / 180;

    this.add(cube);
    this.add(myText);
    this.add(line);

    textControl.addText(myText);
  }

  isTableType() {
    return false;
  }
}
