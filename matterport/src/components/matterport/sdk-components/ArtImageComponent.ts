import { SHAPE } from "types/shape";
import { ComponentInteractionType, SceneComponent } from "./SceneComponent";

type IInput = {
  url: string | null;
  x: number;
  y: number;
  z: number;
  rotation: number;
  // position: { x: number; y: number; z: number };
};

export class ArtImageComponent extends SceneComponent {
  root: any;
  plan: THREE.Mesh | null = null;

  events = {
    [ComponentInteractionType.CLICK]: true,
    [ComponentInteractionType.HOVER]: false,
  };

  inputs: IInput = {
    url: null,
    x: 0,
    y: 0,
    z: 0,
    rotation: 0,
  };

  onInit() {
    const THREE = this.context.three;
    this.root = new THREE.Object3D();
    this.outputs.objectRoot = this.root;
    this.outputs.collider = this.root;

    this.createArtPlane();
  }

  onInputsUpdated(previousInputs: IInput) {
    if (
      previousInputs.x !== this.inputs.x ||
      previousInputs.y !== this.inputs.y ||
      previousInputs.z !== this.inputs.z
    ) {
      this.plan?.position.set(this.inputs.x, this.inputs.y, this.inputs.z);
    }

    if (previousInputs.rotation !== this.inputs.rotation) {
      if (this.plan)
        this.plan.rotation.y = (this.inputs.rotation * Math.PI) / 180;
    }
  }

  createArtPlane() {
    const THREE = this.context.three;

    const texture = new THREE.TextureLoader().load(this.inputs.url);
    const geometry = new THREE.PlaneBufferGeometry();
    const material = new THREE.MeshBasicMaterial({
      map: texture,
      side: THREE.DoubleSide,
    });
    this.plan = new THREE.Mesh(geometry, material);
    this.root.add(this.plan);
  }

  onEvent(eventType: string, eventData: unknown) {
    if (eventType === ComponentInteractionType.CLICK) {
      this.notify(ComponentInteractionType.CLICK, {
        node: this.context.root,
        type: SHAPE.ART,
      });
    }
  }
}

export const artImageType = "mp.artImage";
export const makeArtImage = function () {
  return new ArtImageComponent();
};
