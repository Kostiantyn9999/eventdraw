import { SHAPE } from "types/shape";
import { ComponentInteractionType, SceneComponent } from "./SceneComponent";

type IInput = {
  id: string;
  model: string | null;
  size: { x: number; y: number; z: number };
  localLot: { x: number; y: number; z: number };
  localPos: { x: number; y: number; z: number };
  offset: { x: number; y: number; z: number };
  color: string | null;
  baseElevation: number;
  lock: boolean;
  scale: 1,
};

export class ChairComponent extends SceneComponent {
  root: any;
  cloned: boolean = false;
  private obj: THREE.Object3D | null = null;

  events = {
    [ComponentInteractionType.CLICK]: true,
    [ComponentInteractionType.HOVER]: true,
  };

  inputs: IInput = {
    id: "",
    model: null,
    size: { x: 1, y: 2, z: 1 },
    localLot: { x: 1, y: 2, z: 1 },
    localPos: { x: 1, y: 2, z: 1 },
    offset: { x: 1, y: 2, z: 1 },
    color: null,
    baseElevation: 0,
    lock: true,
    scale: 1
  };

  onInit() {
    const THREE = this.context.three;
    this.root = new THREE.Object3D();
    this.outputs.objectRoot = this.root;
    this.outputs.collider = this.root;

    if (this.inputs.model) {
      const om: any = window.mms[this.inputs.model];
      const m = om ? (om.scene as THREE.Object3D).clone() : null;

      if (m) {
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

        if (this.obj) {
          this.root.remove(this.obj);
        }
        this.obj = m;
        this.root.add(m);
      }
    }
  }

  onInputsUpdated(previousInputs: IInput) {
    const THREE = this.context.three;
    if (previousInputs.model !== this.inputs.model) {
      const om: any = window.mms[this.inputs.model as string];
      const m = om ? (om.scene as THREE.Object3D).clone() : null;

      if (m) {
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

        if (this.obj) {
          this.root.remove(this.obj);
        }
        this.obj = m;
        this.root.add(m);
        
        this.obj.traverse((m) => {
          if (m instanceof THREE.Mesh) {
            (m as any).material.color = new THREE.Color(0xffffff);
          }
        });

        this.changeLock(this.inputs.lock);
      }
    }

    if (previousInputs.color !== this.inputs.color) {
      if (this.obj) {
        this.obj.traverse((m) => {
          if (m instanceof THREE.Mesh) {
            (m as any).material = (m as any).material.clone();
            (m as any).material.color = new THREE.Color(
              this.inputs.color
            );
          }
        });
      }
    }

    if (previousInputs.lock !== this.inputs.lock) {
      if (this.obj) {
        this.changeLock(this.inputs.lock);
      }
    }
  }

  onEvent(eventType: string, eventData: unknown) {
    if (eventType === ComponentInteractionType.CLICK) {
      this.notify(ComponentInteractionType.CLICK, {
        node: this.context.root,
        type: SHAPE.CHAIR,
      });
    }
  }

  changeColor(color: string | null) {
    const THREE = this.context.three;

    if (this.obj) {
      this.obj.traverse((m) => {
        if (m instanceof THREE.Mesh) {
          (m as any).material = (m as any).material.clone();
          if (color !== null && color !== "null") {
            (m as any).material.color = new THREE.Color(color);
          }

          if (color === null) {
            (m as any).material.color = new THREE.Color(0xffffff);
          }
        }
      });
    }
  }

  changeLock(lock: boolean) {
    const THREE = this.context.three;

    if (this.obj) {
      this.obj.traverse((m) => {
        if (m instanceof THREE.Mesh) {
          (m as any).material.transparent = true;
          (m as any).material.opacity = lock ? 1 : 0.6;

          if (lock) {
            this.changeColor(this.inputs.color);
          } else {
            this.changeColor("#ffffff");
          }
        }
      });
    }
  }

  createName() {
    const THREE = this.context.three;
    this.root = new THREE.Object3D();
    this.outputs.objectRoot = this.root;
    this.outputs.collider = this.root;

    const textHeight = 20;

    const canvas = document.createElement("canvas");
    const context = canvas.getContext("2d") as CanvasRenderingContext2D;
    const text = "Name";
    canvas.width = text.length * textHeight;
    canvas.height = 20;
    context.fillStyle = "#ffffff";
    context.fillRect(0, 0, canvas.width, canvas.height);
    context.fill();

    context.font = "normal " + textHeight + "px Arial";
    context.textAlign = "center";
    context.textBaseline = "middle";
    context.fillStyle = "#ff0000";
    context.fillText("Name", canvas.width / 2, canvas.height / 2);

    const sprintTexture = new THREE.Texture(canvas);
    sprintTexture.needsUpdate = true;
    const spriteMaterial = new THREE.SpriteMaterial({
      map: sprintTexture,
    });

    const m = new THREE.Sprite(spriteMaterial);

    m.position.x =
      this.inputs.localPos.x - this.inputs.offset.x + this.inputs.size.x / 2;
    m.position.y = this.inputs.localPos.y - this.inputs.offset.y;
    m.position.z =
      this.inputs.localPos.z - this.inputs.offset.z + this.inputs.size.z / 2;
    m.position.y = 45 / this.inputs.scale;

    m.scale.multiplyScalar(15 / this.inputs.scale);
    this.root.add(m);
  }
}

export const chairType = "mp.chair";
export const makeChair = function () {
  return new ChairComponent();
};
