import { SceneComponent } from "./SceneComponent";

type IInput = {
  x: number;
  y: number;
};

class PointComponent extends SceneComponent {
  root: any;

  centerPointer: THREE.Mesh | null = null;

  inputs = {
    x: 0,
    y: 0,
  };

  onInit() {
    const THREE = this.context.three;

    this.root = new THREE.Object3D();
    this.outputs.objectRoot = this.root;
    this.outputs.collider = this.root;

    this.makeCenterPointer();
  }

  makeCenterPointer() {
    const THREE = this.context.three;

    if (this.centerPointer) {
    }

    const pointGeometry = new THREE.SphereBufferGeometry(0.5, 32, 32);
    const pointMaterial = new THREE.MeshBasicMaterial({ color: 0x00ff00 });

    this.centerPointer = new THREE.Mesh(pointGeometry, pointMaterial);

    this.root.add(this.centerPointer);
  }

  onInputsUpdated(oldInputs: IInput) {
    if (oldInputs.x !== this.inputs.x) {
      this.updatePosition();
    }

    if (oldInputs.y !== this.inputs.y) {
      this.updatePosition();
    }
  }

  updatePosition() {
    if (this.centerPointer) {
      this.centerPointer.position.x = this.inputs.x;
      this.centerPointer.position.z = this.inputs.y;
    }
  }
}

export const pointType = "mp.point";

export const makePoint = function () {
  return new PointComponent();
};
