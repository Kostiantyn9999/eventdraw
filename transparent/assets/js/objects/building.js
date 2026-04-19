const option = {
  0: {
    model: "building1.gltf",
    scale: 2.0,
    rotation: {
      x: -Math.PI / 2,
      y: 0,
      z: Math.PI / 11,
    },
    offset: 20,
  },
  1: {
    model: "building2.gltf",
    scale: 1.0,
    rotation: {
      x: 0,
      y: Math.PI / 2,
      z: 0,
    },
    offset: 20,
  },
  2: {
    model: "building3.gltf",
    scale: 1.0,
    rotation: {
      x: 0.02,
      y: 1.25,
      z: null,
    },
    offset: 22,
  },
  3: {
    model: "building4.gltf",
    scale: 1.0,
    rotation: {
      x: Math.PI / 2,
      y: 1.25,
      z: null,
    },
    offset: 22,
  },
  4: {
    model: "test 04_1_final model_01.glb",
    token: "pQn672NZBEZ3F7lNWZRI",
    scale: 1.0,
    rotation: {
      x: 0,
      y: 0,
      z: 0,
    },
    dimension: {
      x: 0.473,
      y: 0.635,
    },
    position: {
      x: 0.283,
      y: 0.338,
    },
    offset: 22,
  },
  5: {
    model: "20230626.glb",
    token: "pQn672NZBEZ3F7lNWZRI",
    scale: 1.0,
    rotation: {
      x: 0,
      y: 0,
      z: 0,
    },
    dimension: {
      x: 0.473,
      y: 0.635,
    },
    position: {
      x: 0.283,
      y: 0.338,
    },
    offset: 22,
  },
};

class BuildingModel extends THREE.Group {
  constructor(realistic, cameraControl) {
    super();

    this.realistic = realistic;
    this.cameraControl = cameraControl;
  }

  getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    const regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
      results = regex.exec(location.search);
    return results === null
      ? ""
      : decodeURIComponent(results[1].replace(/\+/g, " "));
  }

  async init(x, y) {
    const building = 5;

    const res = await fetch(
      `https://test.eventdraw.com.au/frontend/web/site/realistics-get?id=${
        this.realistic
      }&t=${Date.now().toString(36)}`
    );
    const jsonData = await res.json();
    const opt = building === "" ? option["0"] : option[building];

    const xDimension = x * jsonData.widthx;
    const zDimension = y * jsonData.widthy;
    const leftX = x * jsonData.startx;
    const leftZ = y * jsonData.starty;

    const cameraPosX = x * jsonData.posx - x / 2;
    const cameraPosY = y * jsonData.posy - y / 2;
    // console.log(this.cameraControl);
    this.cameraControl.setCameraInitialPosition(cameraPosX, cameraPosY);
    this.cameraControl.setCameraDefaultHeight(jsonData.posh * 100 || 120);

    const gltfLoader = new THREE.GLTFLoader();
    const dracoLoader = new THREE.DRACOLoader();
    dracoLoader.setDecoderPath("./assets/js/draco/");
    gltfLoader.setDRACOLoader(dracoLoader);

    gltfLoader.load(
      `https://3d.eventdraw.com.au/eventdraw_api/public/${
        jsonData.model
      }?t=${Date.now().toString(36)}`,
      (gltf) => {
        $(".building-loading-page").hide();

        const sc = gltf.scene;

        sc.traverse((m) => {
          if (m.isMesh) {
            m.material.metalness = 0.5;
            m.material.roughness = 1.0;
          }
        });
        sc.rotation.x =
          opt.rotation.x === null ? sc.rotation.x : opt.rotation.x;
        sc.rotation.y =
          opt.rotation.y === null ? sc.rotation.y : opt.rotation.y;
        sc.rotation.z =
          opt.rotation.z === null ? sc.rotation.z : opt.rotation.z;

        const size = new THREE.Vector3();
        const boundingBox = new THREE.Box3().setFromObject(sc);
        boundingBox.getSize(size);
        const con = xDimension / size.x;

        sc.scale.y = con;
        sc.scale.x = xDimension / size.x;
        sc.scale.z = zDimension / size.z;

        const size1 = new THREE.Vector3();
        const boundingBox1 = new THREE.Box3().setFromObject(sc);
        boundingBox1.getSize(size1);

        sc.position.x = -x / 2 + leftX;
        sc.position.z = -y / 2 + leftZ;
        this.add(sc);

        this.cameraControl.setMode("tour");

        // const planeGeometry = new THREE.PlaneBufferGeometry(x, y);
        // const planeMaterial = new THREE.MeshStandardMaterial({
        //   color: 0x555555, // red
        //   side: THREE.DoubleSide,
        //   flatShading: true,
        //   // opacity: 0.4
        // });
        // const plane = new THREE.Mesh(planeGeometry, planeMaterial);
        // plane.rotation.x = -0.5 * Math.PI;
        // this.add(plane);
      }
    );
  }

  createMesh() {}
}
