import { SceneComponent } from "./SceneComponent";

class OrientedBox extends SceneComponent {
  inputs = {
    size: { x: 1, y: 1, z: 1 },
  };

  onInit() {
    const THREE = this.context.three;
    this.root = new THREE.Object3D();
    this.outputs.objectRoot = this.root;
    this.outputs.collider = this.root;

    this.makeBox();
  }

  onEvent(interactionType, eventData) {}

  onInputsUpdated(oldInputs) {
    if (
      oldInputs.size.x !== this.inputs.size.x ||
      oldInputs.size.y !== this.inputs.size.y ||
      oldInputs.size.z !== this.inputs.size.z
    ) {
      return;
    }
  }

  makeBox() {
    const THREE = this.context.three;

    if (this.box) {
      this.root.remove(this.box);
      this.box.material.dispose();
      this.box.geometry.dispose();
      this.box = null;
    }
    if (this.edges) {
      this.root.remove(this.edges);
      this.edges.material.dispose();
      this.edges.geometry.dispose();
      this.edges = null;
    }

    const boxGeometry = new THREE.BoxGeometry(
      this.inputs.size.x,
      this.inputs.size.y === 0 ? 1 : this.inputs.size.y,
      this.inputs.size.z
    );
    const boxMaterial = new THREE.MeshBasicMaterial({
      color: 0xff0000,
      depthWrite: false,
      transparent: true,
      side: THREE.DoubleSide,
      blending: THREE.AdditiveBlending,
      opacity: 0.0
    });

    this.box = new THREE.Mesh(boxGeometry, boxMaterial);
    this.box.position.y =
      this.inputs.size.y === 0 ? 0.5 : this.inputs.size.y / 2;
    // this.root.add(this.box);

    const edgesGeometry = new THREE.EdgesGeometry(boxGeometry);
    this.edges = new THREE.LineSegments(
      edgesGeometry,
      new THREE.LineBasicMaterial({
        transparent: true,
        color: 0x0000ff,
        linewidth: 1,
        opacity: 0.2,
      })
    );

    // const obj3D = this.context.root.obj3D;
    // const worldPos = new this.context.three.Vector3();
    // obj3D
    //   .getWorldPosition(worldPos)
    //   .add({
    //     x: 0,
    //     y: this.inputs.size.y === 0 ? 0.5 : this.inputs.size.y / 2,
    //     z: 0,
    //   });
    // this.edges.position.copy(worldPos);

    this.edges.position.y =
      this.inputs.size.y === 0 ? 0.5 : this.inputs.size.y / 2;

    // this.root.add(this.edges);
  }
}

export const orientedBoxType = "mp.orientedBox";
export const makeOrientedBox = function () {
  return new OrientedBox();
};
