import { MpSdk } from "bundle/sdk";

export class SceneLoader {
  nodes = [];

  constructor(public sdk: MpSdk) {}

  async load(sid: string, callback: () => void = () => {}) {
    const nodesToStop = this.nodes.splice(0);

    for (const node of nodesToStop) {
      node.stop();
    }

    const scene = sidToScene.get(sid);
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
  }
}

export const sidToScene = new Map();
