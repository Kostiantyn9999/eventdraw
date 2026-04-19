import { MpSdk } from "bundle/sdk";
import { IModel } from "types/model";
import { initComponents } from "./components/matterport/sdk-components";
import { IScene, ISdk } from "./interfaces";
import { InteractionEvent } from "./pages/main";

export const makeScene = (sdk: any): IScene => {
  return new Scene(sdk);
};

class Scene implements IScene {
  public loaded: boolean;
  public _objects: MpSdk.Scene.INode[] = [];
  public availableModels: IModel[] = [];
  public preparedD: string = "";
  public spy: MpSdk.Scene.IComponentEventSpy<InteractionEvent> | null = null;
  public startPosition: any;

  constructor(private sdk: ISdk) {
    this.loaded = false;

    this.setup = this.setup.bind(this);
    sdk.onChanged(this.setup);
  }

  private async setup(sdk: MpSdk) {
    sdk.Scene.configure(function (renderer, three, effectComposer) {
      window.THREE = three;
      window.effectComposer = effectComposer;
    });

    // setTimeout(async () => {
    await initComponents(sdk);
    this.startPosition = await sdk.Camera.getPose();
    const iframeElement = document.getElementById("sdk-iframe-overlay");
    iframeElement?.remove()
    const controlElement = document.getElementById("sdk-control-overlay")
    controlElement?.remove()

    this.loaded = true;
    // }, 2000);
  }

  // Fake API to serialize function
  public *nodeIterator(): IterableIterator<MpSdk.Scene.INode> {
    for (const node of this._objects) {
      yield node;
    }
  }

  public bindings(): [] {
    return [];
  }

  public pathIterator(): [] {
    return [];
  }

  async serialize() {
    return await this.sdk.sdk.Scene.serialize(this as any);
  }

  async deserialize(
    serialized: string,
    callback: (node: MpSdk.Scene.INode) => void
  ) {
    const nodesToStop = this._objects.splice(0);
    for (const node of nodesToStop) {
      node.stop();
    }

    // Updated Version
    const nodesToStart = (await this.sdk.sdk.Scene.deserialize(
      serialized
    )) as MpSdk.Scene.IObject;

    if (callback) {
      for (const node of nodesToStart.nodeIterator()) {
        callback(node);
      }
    }

    for (const node of nodesToStart.nodeIterator()) {
      node.start();
      this._objects.push(node);
    }
  }

  loadModels(mss: any, chairM: any) {
    this._objects.forEach((node) => {
      const componentIterator = node.componentIterator();

      for (const component of componentIterator) {
        if (component.componentType === "mp.shape") {
          (component.inputs as any).model = (component.inputs as any).type;
        } else if (component.componentType === "mp.chair") {
          (component.inputs as any).model = component.inputs.model || "table_chair";
        }
      }
      // for (const component of componentIterator) {
      //   if (component.componentType === "mp.shape") {
      //     const m = mss[component.inputs.type];
      //     component.inputs.model = m ? m.gltf.scene.clone() : null;
      //   } else if (component.componentType === "mp.chair") {
      //     component.inputs.model = chairM.scene.clone();
      //   }
      // }
    });
  }

  getObjects(): Array<MpSdk.Scene.INode> {
    return this._objects;
  }

  setAvailableModels(availableModels: IModel[]) {
    this.availableModels = availableModels;
  }

  setSavedData(data: string) {
    this.preparedD = data;
  }

  setClickSpy(spy: MpSdk.Scene.IComponentEventSpy<InteractionEvent>) {
    this.spy = spy;
  }
}
