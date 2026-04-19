import { SceneComponent } from "./SceneComponent";

class Chair extends SceneComponent {
  inputs = {
    size: { x: 1, y: 2, z: 1 },
    localLot: { x: 1, y: 2, z: 1 },
    localPos: { x: 1, y: 2, z: 1 },
    offset: { x: 1, y: 2, z: 1 },
  };

  onInit() {
    const THREE = this.context.three;
    this.root = new THREE.Object3D();
    this.outputs.objectRoot = this.root;
    this.outputs.collider = this.root;

    this.loadModel();
  }

  loadModel() {
    const THREE = this.context.three;
    const loader = new THREE.GLTFLoader();
    const dracoLoader = new THREE.DRACOLoader();
    dracoLoader.setDecoderPath(
      "https://raw.githubusercontent.com/mrdoob/three.js/147/examples/js/libs/draco/"
    );
    loader.setDRACOLoader(dracoLoader);

    loader.load(
      `https://3d.eventdraw.com.au/eventdraw_api/public/models/3DVG_Banquet_Chair.gltf?t=${Date.now().toString(36)}`,
      (sc) => {
        const m = _.get(sc, "scene");
        const target = new THREE.Vector3();
        const box = new THREE.Box3().setFromObject(m).getSize(target);

        m.scale.x = this.inputs.size.x / box.x;
        m.scale.z = this.inputs.size.z / box.z;
        m.scale.y = Math.min(m.scale.x, m.scale.z);
        m.rotation.y = -THREE.MathUtils.degToRad(this.inputs.localLot.y) + 3.14;

        m.position.x =
          this.inputs.localPos.x -
          this.inputs.offset.x +
          this.inputs.size.x / 2;
        m.position.y = this.inputs.localPos.y - this.inputs.offset.y;
        m.position.z =
          this.inputs.localPos.z -
          this.inputs.offset.z +
          this.inputs.size.z / 2;

        // m.rotation.x = this.inputs.rotation.x;
        // m.rotation.z = this.inputs.rotation.z;

        this.root.add(m);
      }
    );
  }
}

export const chairType = "mp.chair";
export const makeChair = function () {
  return new Chair();
};
