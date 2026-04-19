const Cursor = function (scene) {
  const main = this;

  main.init = function () {
    console.log("Cursor");

    this._createRingMesh();
  };

  main._createRingMesh = function () {
    const geometry = new THREE.PlaneGeometry(100, 100);
    const material = new THREE.MeshBasicMaterial({
      color: 0xff0000,
      // depthTest: false,
      // depthWrite: false,
    });
    const mesh = new THREE.Mesh(geometry, material);

    // scene.add(mesh);
    // console.log(mesh.up)
    // console.log(scene, mesh)
    // mesh.position.y = 1;
    // mesh.renderOrder = 1;
    mesh.lookAt(new THREE.Vector3(0, 1, 0));
    mesh.position.y = 0.3;

    console.log(mesh.position);
  };

  main.init();
};
