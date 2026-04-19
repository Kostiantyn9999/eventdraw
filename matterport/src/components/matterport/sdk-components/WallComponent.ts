import { SceneComponent } from "./SceneComponent";

type IInput = {
  hidden: boolean;
  height: number;
  opacity: number;
  color: string;
  texture: string | null;
  points: Array<{ x: number; y: number }>;
  type: string;
};

export class WallComponent extends SceneComponent {
  root: any;
  material?: THREE.MeshBasicMaterial;

  inputs: IInput = {
    hidden: false,
    height: 3,
    opacity: 0.2,
    points: [],
    color: "#ffffff",
    texture: null,
    type: "brick",
  };

  xScale = 1;
  yScale = 1;
  planes: THREE.PlaneBufferGeometry[] = [];

  onInit() {
    const THREE = this.context.three;
    this.root = new THREE.Object3D();

    this.xScale = 1;
    this.yScale = 1;

    this.outputs.objectRoot = this.root;
    this.outputs.collider = this.root;

    this.material = new THREE.MeshBasicMaterial({
      color: new THREE.Color(this.inputs.color),
      transparent: true,
      opacity: this.inputs.opacity,
      side: THREE.DoubleSide,
    });

    let i = 1;
    while (i < this.inputs.points.length) {
      const p1 = this.inputs.points[i - 1];
      const p2 = this.inputs.points[i];

      const point1 = new THREE.Vector3(p1.x, 0, p1.y);
      const point2 = new THREE.Vector3(p2.x, 0, p2.y);

      const vector12 = new THREE.Vector3().copy(point2).sub(point1);
      const point3 = new THREE.Vector3()
        .copy(vector12)
        .multiplyScalar(0.5)
        .add(point1);

      const plane = new THREE.BoxBufferGeometry(1, 1, 0.1);
      const wall = new THREE.Mesh(plane, this.material);
      wall.position.copy(point3);
      wall.position.y = this.inputs.height / 2;
      wall.scale.x = vector12.length();
      wall.scale.y = this.inputs.height;
      wall.rotation.y = -Math.atan2(vector12.z, vector12.x);

      this.xScale = vector12.length();
      this.yScale = wall.position.y;

      this.root.add(wall);
      i++;

      this.planes.push(plane);
    }
    this.updateUV(this.inputs.type);
  }

  onInputsUpdated(oldInputs: IInput) {
    const THREE = this.context.three;
    
    if (oldInputs.height !== this.inputs.height) {
      if (this.root.children) {
        this.root.children.forEach((child: any) => {
          child.position.y = this.inputs.height / 2;
          this.yScale = child.position.y;
          child.scale.y = this.inputs.height;
        });
        this.updateUV(this.inputs.type);
      }
    }

    if (oldInputs.hidden !== this.inputs.hidden) {
      if (this.material) this.material.visible = !this.inputs.hidden;
    }

    if (oldInputs.opacity !== this.inputs.opacity) {
      if (this.material) this.material.opacity = this.inputs.opacity;
    }

    if (oldInputs.color !== this.inputs.color) {
      if (this.material)
        this.material.color = new THREE.Color(this.inputs.color);
    }

    if (oldInputs.texture !== this.inputs.texture) {
      if (this.material && this.inputs.texture) {
        new THREE.TextureLoader().load(this.inputs.texture, (texture: any) => {
          texture.wrapS = texture.wrapT = THREE.RepeatWrapping;
          (this.material as any).map = texture;
          (this.material as any).color = new THREE.Color(0xfffffff);
        });
      }
    }

    if (oldInputs.type !== this.inputs.type) {
      this.updateUV(this.inputs.type);
    }
  }

  updateUV(type: string) {
    this.planes.forEach((plane) => {
      const pos = plane.getAttribute("position");
      const uv = plane.getAttribute("uv");

      for (let i = 0; i < pos.count; i++) {
        if (type === "brick") {
          const x = this.xScale * (pos.getX(i) + 0.5) * 0.5,
            y = this.yScale * 2 * (pos.getY(i) + 0.5) * 0.5,
            z = 1 * (pos.getZ(i) + 0.5) * 0.5;

          if (i < 8) uv.setXY(i, y, z);
          else if (i < 16) uv.setXY(i, z, x);
          else uv.setXY(i, x, y);
        } else if (type === "wall") {
          const x = this.xScale * (pos.getX(i) + 0.5) * 0.5,
            y = pos.getY(i) + 0.5,
            z = 1 * (pos.getZ(i) + 0.5) * 0.5;

          if (i < 8) uv.setXY(i, y, z);
          else if (i < 16) uv.setXY(i, z, x);
          else uv.setXY(i, x, y);
        }
      }

      uv.needsUpdate = true;
    });
  }
}

export const wallType = "mp.wall";

export const makeWall = function () {
  return new WallComponent();
};
