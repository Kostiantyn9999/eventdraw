import { ComponentInteractionType, SceneComponent } from "./SceneComponent";
import * as THREE from "three";
import { SHAPE } from "types/shape";

type IInputs = {
  url: string | HTMLVideoElement | null;
  x: number;
  y: number;
  z: number;
  rotation: number;
};

export class VideoComponent extends SceneComponent {
  private root: THREE.Object3D;
  private video: HTMLVideoElement;
  private texture: THREE.Texture;
  private plane: THREE.Mesh;

  events = {
    [ComponentInteractionType.CLICK]: true,
    [ComponentInteractionType.HOVER]: false,
  };

  inputs: IInputs = {
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

    this.createTexture();
  }

  onInputsUpdated(previousInputs: IInputs) {
    if (
      previousInputs.x !== this.inputs.x ||
      previousInputs.y !== this.inputs.y ||
      previousInputs.z !== this.inputs.z
    ) {
      this.plane?.position.set(this.inputs.x, this.inputs.y, this.inputs.z);
    }

    if (previousInputs.rotation !== this.inputs.rotation) {
      if (this.plane)
        this.plane.rotation.y = (this.inputs.rotation * Math.PI) / 180;
    }
    // if (previousInputs.position !== this.inputs.position) {
    //   this.plan?.position.copy(this.inputs.position)
    //   console.log(this.plan?.position)
    // }
  }

  private createTexture() {
    this.releaseTexture();

    const THREE = this.context.three;
    if (!this.inputs.url && this.video) {
      this.video.src = "";
      return;
    }

    if (this.inputs.url instanceof HTMLVideoElement) {
      this.video = this.inputs.url;
    } else {
      this.video = this.createVideoElement();

      if (typeof this.inputs.url === "string") {
        this.video.src = this.inputs.url;
      } else {
        this.video.srcObject = this.inputs.url;
      }

      this.video.load();
    }

    this.texture = new THREE.VideoTexture(this.video);
    this.texture.minFilter = THREE.LinearFilter;
    this.texture.magFilter = THREE.LinearFilter;
    this.texture.format = THREE.RGBFormat;

    this.video.play();

    const geometry = new THREE.PlaneBufferGeometry();
    const material = new THREE.MeshBasicMaterial({
      map: this.texture,
      side: THREE.DoubleSide,
    });
    this.plane = new THREE.Mesh(geometry, material);
    this.root.add(this.plane);
  }

  onDestory() {}

  releaseTexture() {
    if (this.texture) {
      this.texture.dispose();
    }
  }

  private createVideoElement() {
    const video = document.createElement("video");
    video.crossOrigin = "anonymous";
    video.autoplay = true;
    video.muted = true;
    video.loop = true;

    return video;
  }

  onEvent(eventType: string, eventData: unknown) {
    if (eventType === ComponentInteractionType.CLICK) {
      this.notify(ComponentInteractionType.CLICK, {
        node: this.context.root,
        type: SHAPE.VIDEO,
      });
    }
  }
}

export const artVideoType = "mp.artVideo";
export const makeArtVideo = function () {
  return new VideoComponent();
};
