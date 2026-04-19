var SettingEnvironment = function (cameraControl) {
  const main = this;

  main.cameraControl = cameraControl;

  main.init = function () {
    $("#btn-setting-environment").click(() => {
      // $("mySidebar").style({});
      $(".sidebar").css("width", "0");
      document.getElementById("setting-environment").style.width = "250px";
    });

    $("#btn-close-environment").click(() => {
      document.getElementById("setting-environment").style.width = "0";
    });

    $("#btn-close-views").click(() => {
      document.getElementById("setting-views").style.width = "0";
    });

    $("#tabs").tabs();
    // $( function() {
    //     $("#tabs").tabs();
    // });

    $("#isometic-right").click(() => {
      $("#view-points-description").text("Isometric (Right)");

      main.setIsometicRight();
    });

    $("#isometic-left").click(() => {
      $("#view-points-description").text("Isometric (Left)");

      main.setIsometicLeft();
    });

    $("#cabinet").click(() => {
      $("#view-points-description").text("Cabinet");

      main.setCabinet();
    });

    $("#miltary-right").click(() => {
      $("#view-points-description").text("Military (Right)");
      main.setMiltaryRight();
    });

    $("#miltary-left").click(() => {
      $("#view-points-description").text("Military (Left)");
      main.setMiltaryLeft();
    });

    $("#cavalier").click(() => {
      $("#view-points-description").text("Cavalier");
      main.setCavalier();
    });

    $("#reset-button").click(() => {
      main.reset();
    });
  };

  main.setIsometicRight = function () {
    main.cameraControl.setCameraType(true);

    const floorRect = main.cameraControl.getFloorRect();
    // const properR = Math.sqrt(Math.pow(floorRect.width / 2, 2) + Math.pow(floorRect.height / 2, 2)) * 1.5;
    const target = main.cameraControl.getParameter();

    TweenLite.to(target, 2, {
      x: (-floorRect.width * 3) / 4,
      y: 500,
      z: (floorRect.height * 3) / 4,
      ease: Cubic.easeInOut,
      delay: 0.02,
      onUpdate: () => {
        main.cameraControl.controls.update();
        main.cameraControl.controls.target = new THREE.Vector3(0, 0, 0);
      },
      onComplete: () => {},
    });
  };

  main.setIsometicLeft = function () {
    main.cameraControl.setCameraType(true);

    const floorRect = main.cameraControl.getFloorRect();
    // const properR = Math.sqrt(Math.pow(floorRect.width / 2, 2) + Math.pow(floorRect.height / 2, 2)) * 1.5;
    const target = main.cameraControl.getParameter();

    TweenLite.to(target, 2, {
      x: (floorRect.width * 3) / 4,
      y: 500,
      z: (floorRect.height * 3) / 4,
      ease: Cubic.easeInOut,
      delay: 0.02,
      onUpdate: () => {
        main.cameraControl.controls.update();
        main.cameraControl.controls.target = new THREE.Vector3(0, 0, 0);
      },
      onComplete: () => {},
    });
  };

  main.setCabinet = function () {
    main.cameraControl.setCameraType(true);

    const floorRect = main.cameraControl.getFloorRect();
    const target = main.cameraControl.getParameter();

    TweenLite.to(target, 2, {
      x: (floorRect.width * 1) / 5,
      y: 500,
      z: (floorRect.height * 7) / 5,
      ease: Cubic.easeInOut,
      delay: 0.02,
      onUpdate: () => {
        main.cameraControl.controls.update();
        main.cameraControl.controls.target = new THREE.Vector3(0, 0, 0);
      },
      onComplete: () => {},
    });
  };

  main.setMiltaryRight = function () {
    main.cameraControl.setCameraType(false);

    const floorRect = main.cameraControl.getFloorRect();
    const target = main.cameraControl.getParameter();

    TweenLite.to(target, 2, {
      x: (-floorRect.width * 4) / 3,
      y: 500,
      z: (floorRect.height * 4) / 3,
      ease: Cubic.easeInOut,
      delay: 0.02,
      onUpdate: () => {
        main.cameraControl.controls.update();
        main.cameraControl.controls.target = new THREE.Vector3(0, 0, 0);
      },
      onComplete: () => {
        // main.cameraControl.controls.object.zoom = 0.5;
        main.cameraControl.controls.zoom0 = 0.5;
        main.cameraControl.controls.update();
      },
    });
  };

  main.setMiltaryLeft = function () {
    main.cameraControl.setCameraType(false);

    const floorRect = main.cameraControl.getFloorRect();
    var target = main.cameraControl.getParameter();

    TweenLite.to(target, 2, {
      x: (floorRect.width * 4) / 3,
      y: 500,
      z: (floorRect.height * 4) / 3,
      ease: Cubic.easeInOut,
      delay: 0.02,
      onUpdate: () => {
        main.cameraControl.controls.update();
        main.cameraControl.controls.target = new THREE.Vector3(0, 0, 0);
      },
      onComplete: () => {},
    });
  };

  main.setCavalier = function () {
    main.cameraControl.setCameraType(false);

    const floorRect = main.cameraControl.getFloorRect();
    const target = main.cameraControl.getParameter();

    TweenLite.to(target, 2, {
      x: (floorRect.width * 1) / 5,
      y: 500,
      z: (floorRect.height * 7) / 3,
      ease: Cubic.easeInOut,
      delay: 0.02,
      onUpdate: () => {
        main.cameraControl.controls.update();
        main.cameraControl.controls.target = new THREE.Vector3(0, 0, 0);
      },
      onComplete: () => {},
    });
  };

  main.reset = function () {
    main.cameraControl.setCameraType(true);

    const target = main.cameraControl.getParameter();
    TweenLite.to(target, 2, {
      distance: 800,
      theta: 0,
      phi: 80,
      ease: Cubic.easeInOut,
      delay: 0.02,
      onUpdate: () => {
        // this.camera.lookAt( this.lookPoint );
        main.cameraControl.setParameter(
          target.distance,
          target.theta,
          target.phi
        );
      },
      onComplete: () => {
        // this.camera.lookAt( this.lookPointFirstView );
        // main.camera.updateMatrix();
      },
    });
  };

  main.init();
};
