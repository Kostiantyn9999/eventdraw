import sc from "./scene.json";

export class SceneLoader {
  nodes = [];
  sdk = null;

  constructor(_sdk) {
    this.sdk = _sdk;
  }

  async load(sid, callback = null) {
    const nodesToStop = this.nodes.splice(0);

    for (const node of nodesToStop) {
      node.stop();
    }

    const scene = sidToScene.get(sid);
    console.log(scene, "++++++++++++++==");
    if (!scene) {
      return;
    }

    const nodesToStart = await this.sdk.Scene.deserialize(
      JSON.stringify(scene)
    );
    if (callback) {
      for (const node of nodesToStart) {
        callback(node);
      }
    }

    for (const node of nodesToStart) {
      node.start();
      this.nodes.push(node);
    }

    // console.log(this.nodes)
  }
}

// Map<string, any>
export const sidToScene = new Map();
sidToScene.set("AAWs9eZ9ip6", sc);
