import { SceneComponent } from "./SceneComponent";

type IInput = {
  scale: number;
  number: number;
};

class TableNumberComponent extends SceneComponent {
  root: any;

  inputs: IInput = {
    scale: 1,
    number: 1,
  };

  onInit() {
    // const THREE = this.context.three;
    // this.root = new THREE.Object3D();
    // this.outputs.objectRoot = this.root;
    // this.outputs.collider = this.root;
    //
    // const textHeight = 40;
    //
    // const canvas = document.createElement("canvas");
    // const context = canvas.getContext("2d") as CanvasRenderingContext2D;
    // canvas.width = 60;
    // canvas.height = 60;
    // const radius = 25;
    //
    // context.beginPath();
    // context.arc(
    //   canvas.width / 2,
    //   canvas.height / 2,
    //   radius,
    //   0,
    //   2 * Math.PI,
    //   false
    // );
    // context.fillStyle = "#222222";
    // context.fill();
    // context.lineWidth = 5;
    // context.strokeStyle = "black";
    // context.stroke();
    //
    // context.font = "normal " + textHeight + "px Arial";
    // context.textAlign = "center";
    // context.textBaseline = "middle";
    // context.fillStyle = "#ffffff";
    // context.fillText(
    //   this.inputs.number.toString(),
    //   canvas.width / 2,
    //   canvas.height / 2
    // );
    //
    // const sprintTexture = new THREE.Texture(canvas);
    // sprintTexture.needsUpdate = true;
    // const spriteMaterial = new THREE.SpriteMaterial({
    //   map: sprintTexture,
    // });
    //
    // const m = new THREE.Sprite(spriteMaterial);
    // console.log(this.inputs.scale);
    // m.position.y = 45 / this.inputs.scale;
    // m.scale.multiplyScalar(15 / this.inputs.scale);
    // this.root.add(m);
  }

  onInputsUpdated(previousInputs: IInput) {}

  onEvent(eventType: string, eventData: unknown) {}

  onTick(delta: number) {}
}

export const tableNumberType = "mp.tableNumber";
export const makeTableNumber = function () {
  return new TableNumberComponent();
};
