var CameraControl = function (scene, camera, controls, engine) {
  const main = this;

  main.camera = camera;
  main.engine = engine;

  main.backTemp = new THREE.Vector3(0, 1500, 0);

  // Camera Initial Position when switch virtual Tour mode.
  main.cameraInitialPos = new THREE.Vector3(0, 120, 0);

  main.fnInit = function () {
    main.createOrbitControl();

    main.lookPoint = new THREE.Vector3(0, 0, 0);

    const geometry = new THREE.RingGeometry(35, 40, 32);
    const material = new THREE.MeshBasicMaterial({
      color: 0xaaaaaa,
      side: THREE.DoubleSide,
    });
    const mesh = new THREE.Mesh(geometry, material);
    mesh.rotation.x = Math.PI / 2;
    mesh.position.y = 2;
    mesh.visible = false;

    this.mesh = mesh;
    scene.add(this.mesh);

    $("#orbit-up").on({
      mousedown: function () {
        $(this).data(
          "timer",
          setInterval(function () {
            const direction = new THREE.Vector3();
            main.camera.getWorldDirection(direction);
            direction.y = 0;
            direction.normalize();

            // var initVec = main.camera.position.clone();
            // main.camera.translateY( 10 );
            // var afterVec = main.camera.position.clone();
            // main.lookPoint.add(afterVec.sub(initVec));
            // main.controls.target.copy(main.lookPoint);
            // main.controls.update();
            // main.camera.updateProjectionMatrix();

            // const direction = new THREE.Vector3;
            // main.camera.getWorldDirection(direction);
            // console.log(direction);
            // direction.projectOnPlane(new THREE.Vector3(0, 1, 0));
            // main.camera.position.add( direction.multiplyScalar(10) );
            // main.lookPoint.add(direction.multiplyScalar(10));
            // main.controls.target.copy(main.lookPoint);

            // main.controls.update();
            // main.camera.updateProjectionMatrix();

            // console.log(direction.multiplyScalar(5));

            // Enable All Up/Down Direction
            var moveVector = direction.multiplyScalar(10);

            main.lookPoint.copy(main.controls.target);
            main.lookPoint.add(moveVector);

            main.camera.position.copy(main.camera.position.add(moveVector));
            main.controls.target.copy(main.lookPoint);
            main.camera.updateProjectionMatrix();

            // Disable Up/Down Direction
            if (main.controls.enabled) main.controls.update();
          }, 100)
        );
      },
      mouseup: function () {
        clearTimeout($(this).data("timer"));
      },
      mouseout: function () {
        clearTimeout($(this).data("timer"));
      },
      mouseleave: function () {
        clearTimeout($(this).data("timer"));
      },
    });

    $("#orbit-right").on({
      mousedown: function () {
        $(this).data(
          "timer",
          setInterval(function () {
            // main.lookPoint.copy(main.controls.target);
            // main.lookPoint.add(new THREE.Vector3(10, 0, 0));

            // // main.mesh.position.copy(main.lookPoint);
            // main.camera.position.copy(main.camera.position.add(new THREE.Vector3(10, 0, 0)));
            // main.controls.target.copy(main.lookPoint);
            // main.camera.updateProjectionMatrix();

            // const direction = new THREE.Vector3;
            // main.camera.getWorldDirection(direction);

            // var axis = new THREE.Vector3( 0, 1, 0 );
            // var angle = Math.PI / 2;
            // direction.applyAxisAngle( axis, angle );

            // // console.log(angle);
            // main.camera.position.copy(direction.addScaledVector(10))
            // /** Get Camera direction vector */
            // // var vector = new THREE.Vector3( 0, 0, - 1 );
            // // vector.applyQuaternion( main.camera.quaternion );
            // var initVec = main.camera.position.clone();
            // main.camera.translateX( 10 );
            // var afterVec = main.camera.position.clone();
            // main.lookPoint.add(afterVec.sub(initVec));
            // main.controls.target.copy(main.lookPoint);
            // main.camera.updateProjectionMatrix();

            // if (main.controls.enabled) main.controls.update();

            const direction = new THREE.Vector3();
            main.camera.getWorldDirection(direction);
            direction.y = 0;
            direction.normalize();
            direction.cross(new THREE.Vector3(0, -1, 0));

            var moveVec = direction.multiplyScalar(10);
            main.lookPoint.copy(main.controls.target);
            main.lookPoint.sub(moveVec);

            // main.mesh.position.copy(main.lookPoint);
            main.camera.position.copy(main.camera.position.sub(moveVec));
            main.controls.target.copy(main.lookPoint);
            main.camera.updateProjectionMatrix();

            if (main.controls.enabled) main.controls.update();
          }, 100)
        );
      },
      mouseup: function () {
        clearTimeout($(this).data("timer"));
      },
      mouseout: function () {
        clearTimeout($(this).data("timer"));
      },
      mouseleave: function () {
        clearTimeout($(this).data("timer"));
      },
    });

    $("#orbit-left").on({
      mousedown: function () {
        $(this).data(
          "timer",
          setInterval(function () {
            // var initVec = main.camera.position.clone();
            // main.camera.translateX( -10 );
            // var afterVec = main.camera.position.clone();
            // main.lookPoint.add(afterVec.sub(initVec));
            // main.controls.target.copy(main.lookPoint);
            // main.camera.updateProjectionMatrix();

            // if (main.controls.enabled) main.controls.update();

            const direction = new THREE.Vector3();
            main.camera.getWorldDirection(direction);
            direction.y = 0;
            direction.normalize();
            direction.cross(new THREE.Vector3(0, 1, 0));

            // const origin = new THREE.Vector3( 2, 2, 2 );
            // const length = 10;
            // const hex = 0xffff00;

            // const arrowHelper = new THREE.ArrowHelper( direction, origin, length, hex );
            // scene.add( arrowHelper );

            var moveVec = direction.multiplyScalar(10);
            main.lookPoint.copy(main.controls.target);
            main.lookPoint.sub(moveVec);

            // main.mesh.position.copy(main.lookPoint);
            main.camera.position.copy(main.camera.position.sub(moveVec));
            main.controls.target.copy(main.lookPoint);
            main.camera.updateProjectionMatrix();

            if (main.controls.enabled) main.controls.update();
          }, 100)
        );
      },
      mouseup: function () {
        clearTimeout($(this).data("timer"));
      },
      mouseout: function () {
        clearTimeout($(this).data("timer"));
      },
      mouseleave: function () {
        clearTimeout($(this).data("timer"));
      },
    });

    $("#orbit-down").on({
      mousedown: function () {
        $(this).data(
          "timer",
          setInterval(function () {
            const direction = new THREE.Vector3();
            main.camera.getWorldDirection(direction);

            direction.y = 0;

            //normalize the direction vector (convert to vector of length 1)
            direction.normalize();

            // const origin = new THREE.Vector3( 2, 2, 2 );
            // const length = 10;
            // const hex = 0xffff00;

            // const arrowHelper = new THREE.ArrowHelper( direction, origin, length, hex );
            // scene.add( arrowHelper );

            const moveVec = direction.multiplyScalar(10);
            main.lookPoint.copy(main.controls.target);
            main.lookPoint.sub(moveVec);

            // main.mesh.position.copy(main.lookPoint);
            main.camera.position.copy(main.camera.position.sub(moveVec));
            main.controls.target.copy(main.lookPoint);
            main.camera.updateProjectionMatrix();

            if (main.controls.enabled) main.controls.update();
          }, 100)
        );
      },
      mouseup: function () {
        clearTimeout($(this).data("timer"));
      },
      mouseout: function () {
        clearTimeout($(this).data("timer"));
      },
      mouseleave: function () {
        clearTimeout($(this).data("timer"));
      },
    });

    $("#orbit-rot-left").on({
      mousedown: function () {
        $(this).data(
          "timer",
          setInterval(function () {
            if (main.controls.enabled) {
              main.controls.rotateLeft(0.01);
              main.controls.update();
            } else {
            }
          }, 100)
        );
      },
      mouseup: function () {
        clearTimeout($(this).data("timer"));
      },
      mouseout: function () {
        clearTimeout($(this).data("timer"));
      },
      mouseleave: function () {
        clearTimeout($(this).data("timer"));
      },
    });

    $("#orbit-rot-right").on({
      mousedown: function () {
        $(this).data(
          "timer",
          setInterval(function () {
            if (main.controls.enabled) {
              main.controls.rotateLeft(-0.01);
              main.controls.update();
            } else {
            }
          }, 100)
        );
      },
      mouseup: function () {
        clearTimeout($(this).data("timer"));
      },
      mouseout: function () {
        // console.log("mouseout");
        clearTimeout($(this).data("timer"));
      },
      mouseleave: function () {
        // console.log("mouseleave");
        clearTimeout($(this).data("timer"));
      },
    });

    $("#orbit-zoom-in").on({
      mousedown: function () {
        $(this).data(
          "timer",
          setInterval(function () {
            // main.camera.fov = main.camera.fov;
            main.camera.zoom += 0.1;
            main.camera.updateProjectionMatrix();
          }, 100)
        );
      },
      mouseup: function () {
        clearTimeout($(this).data("timer"));
      },
      mouseout: function () {
        clearTimeout($(this).data("timer"));
      },
      mouseleave: function () {
        clearTimeout($(this).data("timer"));
      },
    });

    $("#orbit-zoom-out").on({
      mousedown: function () {
        $(this).data(
          "timer",
          setInterval(function () {
            // main.camera.fov = main.camera.fov;
            main.camera.zoom -= 0.1;
            main.camera.updateProjectionMatrix();
          }, 100)
        );
      },
      mouseup: function () {
        clearTimeout($(this).data("timer"));
      },
      mouseout: function () {
        clearTimeout($(this).data("timer"));
      },
      mouseleave: function () {
        clearTimeout($(this).data("timer"));
      },
    });

    /** Action
     * eye height is moved up when key w
     * eye height is moved down when key s
     * move forwards/backwards/left/right
     * rotate left/right/up/down
     * http://stemkoski.github.io/Three.js/Chase-Camera.html
     */
    $(document).keypress((event) => {
      // console.log(event.which);
      if (this.mode !== "tour") return;
      switch (event.which) {
        // keyboard W
        case 119:
          main.camera.translateZ(-10);
          break;
        // keyboard S
        case 115:
          main.camera.translateZ(10);
          break;
        // keyboard A
        case 97:
          main.camera.translateX(-10);
          break;
        // keyboard D
        case 100:
          main.camera.translateX(10);
          break;
        // keyboard Q
        case 113:
          main.camera.rotateOnAxis(new THREE.Vector3(0, 1, 0), 0.05);
          break;
        // keyboard E
        case 101:
          main.camera.rotateOnAxis(new THREE.Vector3(0, 1, 0), -0.05);
          break;
        // keyboard Z
        case 122: {
          const pos = main.camera.position.clone();
          if (pos.y + 10 < 200) {
            TweenLite.to(main.camera.position, 0.5, {
              x: pos.x,
              y: pos.y + 10,
              z: pos.z,
              ease: Cubic.easeInOut,
              delay: 0.02,
              onUpdate: () => {},
              onComplete: () => {
                main.camera.updateMatrix();
              },
            });
          }
          break;
        }
        // Keyboard C
        case 99: {
          const pos = main.camera.position.clone();
          if (pos.y - 10 > 0) {
            TweenLite.to(main.camera.position, 0.5, {
              x: pos.x,
              y: pos.y - 10,
              z: pos.z,
              ease: Cubic.easeInOut,
              delay: 0.02,
              onUpdate: () => {},
              onComplete: () => {
                main.camera.updateMatrix();
              },
            });
          }
          break;
        }
        default:
          break;
      }
    });
  };

  main.createOrbitControl = function () {
    if (main.controls) main.controls.dispose();

    main.camera.position.x = 0;
    main.camera.position.y = 1500;
    main.camera.position.z = 0;

    main.controls = new THREE.OrbitControls(
      main.camera,
      engine.renderer.domElement
    );
    main.controls.minPolarAngle = -Math.PI;
    main.controls.maxPolarAngle = Math.PI / 2 - 0.1;

    // main.controls.enableDamping = true; // an animation loop is required when either damping or auto-rotation are enabled
    // main.controls.dampingFactor = 0.5;
    main.controls.screenSpacePanning = false;
    main.controls.update();

    main.controls.addEventListener("start", function () {});
    main.controls.addEventListener("end", function () {});
    main.controls.addEventListener("change", function () {
      main.mesh.position.copy(main.controls.target.setY(3));
    });
  };

  main.createFirstViewControl = function () {};

  main.setCameraType = function (type) {
    main.engine.switchCamera(type);
  };

  main.setMode = function (mode) {
    if (mode === "views") {
      document.getElementById("setting-views").style.width = "250px";
    } else {
      this.mode = mode;
      main.engine.switchCamera(true);
      this.resetView(mode);
    }

    $("#mode-selection div:not(#measurement-button).active").removeClass("active");
    $('#mode-selection div[data-mode="' + mode + '"]').addClass("active");
  };

  main.resetView = function (mode) {
    if (this.mode === "360") {
      document.removeEventListener("pointerdown", main.tempfunc);
      document.removeEventListener("wheel", main.tempFunc2);

      main.camera.position.copy(main.backTemp.clone());

      TweenLite.to(main.camera.position, 2, {
        x: 0,
        y: 1500,
        z: 0,
        ease: Cubic.easeInOut,
        delay: 0.02,
        onUpdate: () => {},
        onComplete: () => {
          main.camera.updateMatrix();
        },
      });

      main.controls.enabled = true;
    } else if (this.mode === "tour") {
      let isUserInteracting = false,
        onPointerDownMouseX = 0,
        onPointerDownMouseY = 0,
        lon = 0,
        onPointerDownLon = 0,
        lat = 0,
        onPointerDownLat = 0,
        phi = 0,
        theta = 0;

      main.controls.enabled = false;
      main.camera.position.copy(main.backTemp.clone());

      TweenLite.to(main.camera.position, 2, {
        x: main.cameraInitialPos.x,
        y: main.cameraInitialPos.y,
        z: main.cameraInitialPos.z,
        ease: Cubic.easeInOut,
        delay: 0.02,
        onUpdate: () => {},
        onComplete: () => {
          main.camera.lookAt(new THREE.Vector3(100, 120, 0));
          main.camera.updateMatrix();
        },
      });

      document.addEventListener("pointerdown", onPointerDown, false);
      document.addEventListener("wheel", onDocumentMouseWheel);

      main.tempfunc = onPointerDown;
      main.tempfunc2 = onDocumentMouseWheel;

      function onPointerDown(event) {
        if (event.isPrimary === false) return;
        isUserInteracting = true;
        onPointerDownMouseX = event.clientX;
        onPointerDownMouseY = event.clientY;
        onPointerDownLon = lon;
        onPointerDownLat = lat;
        document.addEventListener("pointermove", onPointerMove);
        document.addEventListener("pointerup", onPointerUp);
      }

      function onPointerMove(event) {
        // if (event.isPrimary === false) return;
        // lon = (onPointerDownMouseX - event.clientX) * 0.1 + onPointerDownLon;
        // lat = (event.clientY - onPointerDownMouseY) * 0.1 + onPointerDownLat;
        //
        // lat = Math.max(-85, Math.min(85, lat));
        // phi = THREE.MathUtils.degToRad(90 - lat);
        // theta = THREE.MathUtils.degToRad(lon);
        //
        // const x = 500 * Math.sin(phi) * Math.cos(theta);
        // const y = 500 * Math.cos(phi);
        // const z = 500 * Math.sin(phi) * Math.sin(theta);
        //
        // main.camera.lookAt(x, y, z);
        if (event.isPrimary === false) return;

        lon = (onPointerDownMouseX - event.clientX) * -0.001;
        onPointerDownMouseX = event.clientX;
        onPointerDownMouseY = event.clientY;

        main.camera.rotateOnAxis(new THREE.Vector3(0, 1, 0), lon);
      }

      function onPointerUp(event) {
        if (event.isPrimary === false) return;
        isUserInteracting = false;
        document.removeEventListener("pointermove", onPointerMove);
        document.removeEventListener("pointerup", onPointerUp);
      }

      function onDocumentMouseWheel(event) {
        const fov = main.camera.fov + event.deltaY * 0.05;
        main.camera.fov = THREE.MathUtils.clamp(fov, 10, 75);
        main.camera.updateProjectionMatrix();
      }
    }
  };

  main.setCamera = function (camera) {
    if (main.camera) {
      main.backTemp.copy(main.camera.position.clone());
    }

    main.camera = camera;

    main.controls.dispose();
    main.createOrbitControl();
  };

  main.getParameter = function () {
    return main.camera.position;
  };

  main.getFloorRect = function () {
    return { width: main.engine.width, height: main.engine.height };
  };

  main.setCameraInitialPosition = (x, y) => {
    main.cameraInitialPos.setX(x);
    main.cameraInitialPos.setZ(y);
  };

  main.setCameraDefaultHeight = (height) => {
    main.cameraInitialPos.setY(height);
    console.log(height);
  };

  main.setParameter = function (distance, theta, phi) {};

  main.fnRender = function () {
    if (main.controls.enabled) main.controls.update();
  };

  main.fnInit();
};
