var Light = function (scene, floorplan) {
  const main = this;

  const tol = 1;
  const height = 300;

  main.init = function () {
    const light = new THREE.HemisphereLight(0xffffff, 0x888888, 1.1);
    light.position.set(0, height, 0);
    scene.add(light);

    // dirLight = new THREE.DirectionalLight(0xffffff, 0);
    // dirLight.color.setHSL(1, 1, 0.1);

    // dirLight.castShadow = true;

    // dirLight.shadowMapWidth = 1024;
    // dirLight.shadowMapHeight = 1024;

    // dirLight.shadowCameraFar = height + tol;
    // dirLight.shadowBias = -0.0001;
    // dirLight.shadowDarkness = 0.2;
    // dirLight.visible = true;
    // dirLight.shadowCameraVisible = false;

    // scene.add(dirLight);
    // scene.add(dirLight.target);
  };

  main.init();
};
