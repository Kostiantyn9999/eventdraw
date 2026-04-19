var Main = function (model, element, canvasElement) {
  const main = this;

  main.element = $(element);

  main.scene = model.scene;
  main.floorplan = model.floorplan;

  main.init = function () {
    main.init3DScene();

    main.animate();
    main.updateWindowSize();
    main.centerCamera();

    model.floorplan.fireOnUpdatedRooms(main.centerCamera);

    $(window).resize(main.updateWindowSize);
  };

  main.init3DScene = function () {
    main.domElement = main.element.get(0);

    main.camera = new THREE.PerspectiveCamera(45, 1, 1, 10000);
    main.renderer = new THREE.WebGLRenderer({
      antialias: true,
      preserveDrawingBuffer: true,
    });

    (main.renderer.autoClear = false), (main.renderer.shadowMap.Enabled = true);
    main.renderer.shadowMapSoft = true;
    main.renderer.shadowMap.Type = THREE.PCFSoftShadowMap;

    main.domElement.appendChild(main.renderer.domElement);

    const lights = new Light(main.scene, main.floorplan);

    // main.controls = new THREE.OrbitControls( main.camera, main.renderer.domElement );
    this.plan = new Plan(main.scene, main.floorplan, main.controls);
  };

  main.updateWindowSize = function () {
    main.heightMargin = main.element.offset().top;
    main.widthMargin = main.element.offset().left;

    main.elementWidth = main.element.innerWidth();
    main.elementHeight = main.element.innerHeight();
    main.camera.aspect = main.elementWidth / main.elementHeight;
    main.camera.updateProjectionMatrix();

    main.renderer.setSize(main.elementWidth, main.elementHeight);

    main.needsUpdate = true;
  };

  main.shouldRender = function () {
    return true;
  };

  main.render = function () {
    if (main.shouldRender()) {
      // console.log(main.scene.getScene());
      main.renderer.clear();
      main.renderer.render(main.scene.getScene(), main.camera);
      // main.renderer.clearDepth();
      // renderer.render(hud.getScene(), camera);
    }
  };

  main.animate = function () {
    var delay = 50;
    setTimeout(function () {
      requestAnimationFrame(main.animate);
    }, delay);
    main.render();
  };

  main.centerCamera = function () {
    const yOffset = 150;

    const pan = model.floorplan.getCenter();
    pan.y = yOffset;

    main.controls.target = pan;

    const distance = model.floorplan.getSize().z * 1.5;
    const offset = pan.clone().add(new THREE.Vector3(0, distance, distance));
    main.camera.position.copy(offset);

    main.controls.update();
  };

  main.init();
};
