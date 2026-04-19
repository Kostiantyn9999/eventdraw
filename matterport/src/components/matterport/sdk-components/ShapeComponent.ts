import { SHAPE } from "types/shape";
import { ComponentInteractionType, SceneComponent } from "./SceneComponent";

declare global {
  interface Window {
    mms: Record<string, THREE.Object3D>;
  }
}

type IInput = {
  model: string | null;
  type: string;
  size: { x: number; y: number; z: number };
  rotation: { x: number; y: number; z: number };
  baseElevation: number;
  scaleY: number;
  color: string | null;
  lock: boolean;
};

const modelInfos: Record<
  string,
  { mesh: Array<string>; defaultColor: string | null }
> = {
  "Banquet Chair": { mesh: ["3DVG_Banquet_Chair"], defaultColor: "#ffffff" },
  Stage_Piece_01: { mesh: ["stagetop___2_x_1"], defaultColor: null },
};

export class ShapeComponent extends SceneComponent {
  root: any;
  cloned: boolean = false;
  private obj: THREE.Object3D | null = null;

  events = {
    [ComponentInteractionType.CLICK]: true,
    [ComponentInteractionType.HOVER]: true,
  };

  inputs: IInput = {
    model: null,
    type: "test",
    size: { x: 1, y: 2, z: 1 },
    rotation: { x: 0, y: 0, z: 0 },
    baseElevation: 0,
    scaleY: 1,
    color: null,
    lock: true,
  };

  onInit() {
    const THREE = this.context.three;
    this.root = new THREE.Object3D();
    this.outputs.objectRoot = this.root;
    this.outputs.collider = this.root;

    this.cloned = false;

    if (this.inputs.model) {
      const om: any = window.mms[this.inputs.model];
      const m = om ? (om.gltf.scene as THREE.Object3D).clone() : null;

      if (m) {
        const target = new THREE.Vector3();
        const box = new THREE.Box3().setFromObject(m).getSize(target);
        m.scale.x = this.inputs.size.x / box.x;
        m.scale.z = this.inputs.size.z / box.z;
        m.scale.y =
          this.inputs.size.y === 0
            ? Math.min(m.scale.x, m.scale.z)
            : this.inputs.size.y / box.y;

        if (this.obj) {
          this.root.remove(this.obj);
        }

        const scaledBoundingBox = new THREE.Box3().setFromObject(m);
        const scaledCenter = new THREE.Vector3();
        const scaledSize = new THREE.Vector3();
        scaledBoundingBox.getCenter(scaledCenter);
        scaledBoundingBox.getSize(scaledSize);
        
        m.position.x -= scaledCenter.x,
        m.position.y = scaledSize.y / 2 - scaledCenter.y,
        m.position.z -= scaledCenter.z,
        
        this.root.rotation.y = -THREE.MathUtils.degToRad(this.inputs.rotation.y);
        this.root.add(m);
        this.obj = m;
      }
    }
    
    if (this.inputs.color) {
      if (this.obj) {
        const availableMeshs = modelInfos[this.inputs.type] ? modelInfos[this.inputs.type].mesh : [];

        this.obj.traverse((m) => {
          if (
            m instanceof THREE.Mesh &&
            (availableMeshs.includes(m.name) || availableMeshs.length === 0)
          ) {
            if (!this.cloned) {
              (m as any).material = (m as any).material.clone();

              if (this.inputs.color)
                (m as any).material.color = new THREE.Color(this.inputs.color);
              this.cloned = true;
            } else {
              if (this.inputs.color)
                (m as any).material.color = new THREE.Color(this.inputs.color);
            }
          }
        });
      }
    }

    this.root.position.y = this.inputs.baseElevation / this.inputs.scaleY;

    this.changeLock(this.inputs.lock);
  }

  onInputsUpdated(previousInputs: IInput) {
    const THREE = this.context.three;
    if (previousInputs.model !== this.inputs.model || previousInputs.size != this.inputs.size) {
      const om: any = window.mms[this.inputs.model as string];
      const m = om ? (om.gltf.scene as THREE.Object3D).clone() : null;

      if (m) {
        const target = new THREE.Vector3();
        const box = new THREE.Box3().setFromObject(m).getSize(target);
        m.scale.x = this.inputs.size.x / box.x;
        m.scale.z = this.inputs.size.z / box.z;
        m.scale.y =
          this.inputs.size.y === 0
            ? Math.min(m.scale.x, m.scale.z)
            : this.inputs.size.y / box.y;

        if (this.obj) {
          this.root.remove(this.obj);
        }

        const scaledBoundingBox = new THREE.Box3().setFromObject(m);
        const scaledCenter = new THREE.Vector3();
        const scaledSize = new THREE.Vector3();
        scaledBoundingBox.getCenter(scaledCenter);
        scaledBoundingBox.getSize(scaledSize);
        
        m.position.x -= scaledCenter.x,
        m.position.y = scaledSize.y / 2 - scaledCenter.y,
        m.position.z -= scaledCenter.z,

        this.root.rotation.y = -THREE.MathUtils.degToRad(this.inputs.rotation.y);
        this.root.add(m);
        this.obj = m;

        const availableMeshs = modelInfos[this.inputs.type] ? modelInfos[this.inputs.type].mesh : [];

        this.obj.traverse((m) => {
          if (m instanceof THREE.Mesh) {
            if (
              availableMeshs.includes(m.name) ||
              availableMeshs.length === 0
            ) {
              (m as any).material = (m as any).material.clone();
            }
          }
        });

        this.changeColor(this.inputs.color);
        this.changeLock(this.inputs.lock);
      }
    }

    if (previousInputs.baseElevation !== this.inputs.baseElevation) {
      this.root.position.y = this.inputs.baseElevation / this.inputs.scaleY;
    }

    if (previousInputs.color !== this.inputs.color) {
      if (this.obj) {
        this.changeColor(this.inputs.color);
      }
    }

    if (previousInputs.lock !== this.inputs.lock) {
      if (this.obj) {
        this.changeLock(this.inputs.lock);
      }
    }
  }

  onEvent(eventType: string, eventData: any) {
    if (eventType === ComponentInteractionType.CLICK) {
      this.notify(ComponentInteractionType.CLICK, {
        node: this.context.root,
        type: SHAPE.TABLE,
      });
    }
  }

  changeColor(color: string | null) {
    const THREE = this.context.three;

    if (this.obj) {
      const availableMeshs = modelInfos[this.inputs.type]
        ? modelInfos[this.inputs.type].mesh
        : [];

      const defaultColor = modelInfos[this.inputs.type]
        ? modelInfos[this.inputs.type].defaultColor
        : null;

      this.obj.traverse((m) => {
        if (
          m instanceof THREE.Mesh &&
          (availableMeshs.includes(m.name) || availableMeshs.length === 0)
        ) {
          if (color !== null && color !== "null") {
            if (color !== null) {
              (m as any).material.color = new THREE.Color(color);
            }
          }

          if (color === null) {
            if (defaultColor !== null) {
              (m as any).material.color = new THREE.Color(defaultColor);
            }
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
}

export const shapeType = "mp.shape";
export const makeShape = function () {
  return new ShapeComponent();
};
