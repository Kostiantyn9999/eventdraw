import { MpSdk } from "bundle/sdk";

export class SceneLoader {
  private nodes: MpSdk.Scene.INode[] = [];

  constructor(private sdk: MpSdk) {}

  public async load(sid: string, callback: (node: MpSdk.Scene.INode) => void) {
    const nodesToStop = this.nodes.splice(0);

    for (const node of nodesToStop) {
      node.stop();
    }

    const scene = sidToScene.get(sid);
    if (!scene) {
      return;
    }

    const nodesToStart = (await this.sdk.Scene.deserialize(
      JSON.stringify(scene)
    )) as MpSdk.Scene.IObject;

    if (callback) {
      for (const node of nodesToStart.nodeIterator()) {
        callback(node);
      }
    }

    for (const node of nodesToStart.nodeIterator()) {
      node.start();
      this.nodes.push(node);
    }
  }

  loadModels(mss, chairM) {
    this.nodes.forEach((node) => {
      const componentIterator = node.componentIterator();

      for (const component of componentIterator) {
        if (component.componentType === "mp.shape") {
          const m = mss[component.inputs.type];
          component.inputs.model = m ? m.gltf.scene.clone() : null;
        } else if (component.componentType === "mp.chair") {
          component.inputs.model = chairM.scene.clone();
        }
      }
    });
  }
}

export const sidToScene: Map<string, any> = new Map();
