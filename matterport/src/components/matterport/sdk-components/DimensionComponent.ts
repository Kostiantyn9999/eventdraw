import { SceneComponent } from "./SceneComponent";

type IInput = {
  xMin: number;
  xMax: number;
  yMin: number;
  yMax: number;
  axis: boolean;
  elevation: number;
  rotation: number;
  comparedBackImage: { image: HTMLImageElement } | null;
};

class DimensionComponent extends SceneComponent {
  root: any;
  rotationPivot: any;
  box: THREE.Mesh | null = null;
  pivot: THREE.Mesh | null = null;
  xline: THREE.Mesh | null = null;
  yline: THREE.Mesh | null = null;
  boxMaterial: THREE.MeshBasicMaterial | null = null;

  inputs: IInput = {
    xMin: 0,
    xMax: 1,
    yMin: 0,
    yMax: 1,
    axis: false,
    elevation: 0,
    rotation: 0,
    comparedBackImage: null,
  };

  onInit() {
    const THREE = this.context.three;

    this.root = new THREE.Object3D();

    this.outputs.objectRoot = this.root;
    this.outputs.collider = this.root;

    this.rotationPivot = new THREE.Object3D();
    this.root.add(this.rotationPivot);

    this.boxMaterial = new THREE.MeshBasicMaterial({
      color: 0xff0000,
      depthWrite: false,
      transparent: true,
      side: THREE.DoubleSide,
      blending: THREE.AdditiveBlending,
      opacity: 1,
    });

    this.makeDimension();
  }

  makeDimension() {
    const THREE = this.context.three;

    if (this.box) {
      this.rotationPivot.remove(this.box);
      (this.box.material as THREE.Material).dispose();
      this.box.geometry.dispose();
      this.box = null;
    }

    if (this.pivot) {
      this.rotationPivot.remove(this.pivot);
      (this.pivot.material as THREE.Material).dispose();
      this.pivot.geometry.dispose();
      this.pivot = null;
    }

    if (this.xline) {
      this.rotationPivot.remove(this.xline);
      (this.xline.material as THREE.Material).dispose();
      this.xline.geometry.dispose();
      this.xline = null;
    }

    if (this.yline) {
      this.rotationPivot.remove(this.yline);
      (this.yline.material as THREE.Material).dispose();
      this.yline.geometry.dispose();
      this.yline = null;
    }

    const boxGeometry = new THREE.BoxGeometry(
      this.inputs.xMax - this.inputs.xMin,
      2,
      this.inputs.yMax - this.inputs.yMin
    );

    this.box = new THREE.Mesh(
      boxGeometry,
      this.boxMaterial ?? new THREE.MeshBasicMaterial()
    );
    this.box.position.y = 1;
    this.box.position
      .addVectors(
        new THREE.Vector3(this.inputs.xMin, 0, this.inputs.yMin),
        new THREE.Vector3(this.inputs.xMax, 0, this.inputs.yMax)
      )
      .divideScalar(2);

    const pivotGeometry = new THREE.BoxGeometry(1, 1, 1);
    const pivotMaterial = new THREE.MeshBasicMaterial({
      color: 0x0000ff,
      depthWrite: false,
      transparent: true,
      side: THREE.DoubleSide,
      blending: THREE.AdditiveBlending,
      opacity: 1,
    });

    this.pivot = new THREE.Mesh(pivotGeometry, pivotMaterial);
    this.pivot.position.set(
      this.inputs.axis ? this.inputs.xMax : this.inputs.xMin,
      0.5,
      this.inputs.axis ? this.inputs.yMin : this.inputs.yMax
    );

    const xLineGeometry = new THREE.BoxGeometry(
      this.inputs.xMax - this.inputs.xMin,
      1,
      0.2
    );

    const xLineMaterial = new THREE.MeshBasicMaterial({
      color: 0x00ff00,
      depthWrite: false,
      transparent: true,
      // side: THREE.FrontSide,
      blending: THREE.AdditiveBlending,
      opacity: 1,
    });

    this.xline = new THREE.Mesh(xLineGeometry, xLineMaterial);
    this.xline.position.y = 1;
    this.xline.position
      .addVectors(
        new THREE.Vector3(this.inputs.xMin, 0, this.inputs.yMin),
        new THREE.Vector3(this.inputs.xMax, 0, this.inputs.yMin + 0.2)
      )
      .divideScalar(2);

    const yLineGeometry = new THREE.BoxGeometry(
      0.2,
      1,
      this.inputs.yMax - this.inputs.yMin
    );

    const yLineMaterial = new THREE.MeshBasicMaterial({
      color: 0x00ff00,
      depthWrite: false,
      transparent: true,
      side: THREE.DoubleSide,
      blending: THREE.AdditiveBlending,
      opacity: 1,
    });

    this.yline = new THREE.Mesh(yLineGeometry, yLineMaterial);
    this.yline.position.y = 1;
    this.yline.position
      .addVectors(
        new THREE.Vector3(this.inputs.xMin, 0, this.inputs.yMin),
        new THREE.Vector3(this.inputs.xMin + 0.2, 0, this.inputs.yMax)
      )
      .divideScalar(2);
    this.rotationPivot.add(this.box);
    this.rotationPivot.add(this.pivot);
    this.rotationPivot.add(this.xline);
    this.rotationPivot.add(this.yline);

    this.fixRotation();
    // // Test Pivot Point
    // this.rotationPivot.add(
    //   new THREE.Mesh(
    //     new THREE.SphereBufferGeometry(1),
    //     new THREE.MeshBasicMaterial({ color: 0xff0000 })
    //   )
    // );
  }

  onInputsUpdated(oldInputs: IInput) {
    if (oldInputs.comparedBackImage != this.inputs.comparedBackImage) {
      if (this.boxMaterial && this.inputs.comparedBackImage) {
        const THREE = this.context.three;

        const texture = new THREE.Texture(this.inputs.comparedBackImage.image);
        texture.encoding = THREE.sRGBEncoding;
        texture.needsUpdate = true;

        this.boxMaterial.color = new THREE.Color(0xffffff);
        this.boxMaterial.map = texture;
        this.boxMaterial.opacity = 0.7;
        this.boxMaterial.side = THREE.FrontSide;
      }
    }

    if (
      oldInputs.xMin !== this.inputs.xMin ||
      oldInputs.xMax !== this.inputs.xMax ||
      oldInputs.yMin !== this.inputs.yMin ||
      oldInputs.yMax !== this.inputs.yMax ||
      oldInputs.axis !== this.inputs.axis ||
      oldInputs.elevation !== this.inputs.elevation
    ) {
      this.fixRotation();
      this.makeDimension();

      return;
    }

    if (oldInputs.rotation !== this.inputs.rotation) {
      this.fixRotation();
    }
  }

  private fixRotation = () => {
    const centerX =
      (this.inputs.xMax - this.inputs.xMin) / 2 + this.inputs.xMin;
    const centerY =
      (this.inputs.yMax - this.inputs.yMin) / 2 + this.inputs.yMin;

    this.rotationPivot.position.x = -centerX;
    this.rotationPivot.position.z = -centerY;
    this.root.rotation.y = (this.inputs.rotation * Math.PI) / 180;
    this.root.position.x = centerX;
    this.root.position.z = centerY;
  };
}

export const dimensionType = "mp.dimension";

export const makeDimension = function () {
  return new DimensionComponent();
};
