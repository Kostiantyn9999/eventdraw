import { SceneComponent } from "./SceneComponent";

type IInput = {};

export class ComposerComponent extends SceneComponent {
  outlinePassSelected: any;
  composer: any;
  effectFXAA: any;
  renderPass: any;
  effectVignette: any;

  onInit() {
    const THREE = this.context.three;
    // console.log(this.context);
    // console.log(THREE);

    // const pmremGenerator = new THREE.PMREMGenerator(this.context.renderer);
    // // console.log(THREE.RoomEnvironment)
    // this.context.scene.environment = pmremGenerator.fromScene(
    //   new THREE.RoomEnvironment(this.context.renderer)
    // ).texture;

    this.composer = window.effectComposer;

    this.renderPass = new THREE.RenderPass(
      this.context.scene,
      this.context.camera
    );
    this.composer.addPass(this.renderPass);

    this.outlinePassSelected = new THREE.OutlinePass(
      new THREE.Vector2(window.innerWidth, window.innerHeight),
      this.context.scene,
      this.context.camera
    );
    this.outlinePassSelected.edgeStrength = 4;
    this.outlinePassSelected.edgeGlow = 0.5;
    this.outlinePassSelected.edgeThickness = 1.5;
    this.outlinePassSelected.pulsePeriod = 2;
    this.outlinePassSelected.visibleEdgeColor = new THREE.Color(0x85ff54);
    this.outlinePassSelected.hiddenEdgeColor = new THREE.Color(0x0c1708);
    this.composer.addPass(this.outlinePassSelected);
  }

  setActive(obj: any) {
    if (this.outlinePassSelected) {
      this.outlinePassSelected.selectedObjects = [obj];
    }
  }

  setDeactive() {
    if (this.outlinePassSelected) {
      this.outlinePassSelected.selectedObjects = [];
    }
  }

  onInputsUpdated(previousInputs: IInput) {}

  onEvent(eventType: string, eventData: unknown) {}

  onTick(delta: number) {
    if (this.composer) {
      // console.log("tick");
      // this.composer.render();
    }
  }
}

export const composerType = "mp.composer";
export const makeComposer = function () {
  return new ComposerComponent();
};
