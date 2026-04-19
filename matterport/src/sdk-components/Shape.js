import { SceneComponent } from "./SceneComponent";
import _ from "lodash";

import { orientedBoxType } from "./OrientedBox";

class Shape extends SceneComponent {
  inputs = {
    model: null,
    type: "test",
    size: { x: 1, y: 2, z: 1 },
    rotation: {x: 0, y: 0, z: 0}
  };

  onInit() {
    const rootE = this.context.root;
    const THREE = this.context.three;

    this.root = new THREE.Object3D();
    this.outputs.objectRoot = this.root;
    this.outputs.collider = this.root;

    let box = null;

    for (const component of rootE.componentIterator()) {
      if (component.componentType === orientedBoxType) {
        box = component;
      }
    }

    this.box = box;
    this.box.inputs.size = this.inputs.size;

    this.loadModel();
  }

  onEvent(interactionType, eventData) {}

  onInputsUpdated(oldInputs) {
    // console.error("onInputsUpdated");

    if (this.box) {
      this.box.inputs.size = this.inputs.size;
    }
  }

  loadModel() {
    const THREE = this.context.three;
    const loader = new THREE.GLTFLoader();
    const dracoLoader = new THREE.DRACOLoader();
    dracoLoader.setDecoderPath(
      "https://raw.githubusercontent.com/mrdoob/three.js/147/examples/js/libs/draco/"
    );
    loader.setDRACOLoader(dracoLoader);

    loader.load(`${this.inputs.model}?t=${Date.now().toString(36)}`,
    sc => {
      const m = _.get(sc, 'scene')
      const target = new THREE.Vector3();
      const box = new THREE.Box3().setFromObject(m).getSize(target);

      console.log(m);

      m.scale.x = this.inputs.size.x / box.x
      m.scale.z = this.inputs.size.z / box.z
      m.scale.y = Math.min(m.scale.x, m.scale.z)

      m.rotation.y = -THREE.MathUtils.degToRad(this.inputs.rotation.y)
      this.root.add(m);
    })
  }
}

export const shapeType = "mp.shape";
export const makeShape = function () {
  return new Shape();
};
