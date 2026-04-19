import { MpSdk } from "bundle/sdk";
import { GetSDK } from "src/utils/sdk";
import { initComponents } from "src/sdk-components";

export default class MatterPortAPI {
  sdk: MpSdk | null = null;

  async initSDK(model_id: string, loadedModels: () => void) {
    this.sdk = await GetSDK(
      "sdk-iframe",
      process.env.MATTERPORT_APPLICATION_KEY as string,
      process.env.MATTERPORT_INTERFACE_VERSION as string
    );
    await initComponents(this.sdk);
    await this.sdk.Scene.configure((renderer, three) => {
      // renderer.physicallyCorrectLights = true;
      // renderer.gammaFactor = 2.2;
      // renderer.outputEncoding = true;
      // renderer.shadowMap.enabled = true;
      // renderer.shadowMap.bias = 0.0001;
      // renderer.shadowMap.type = three.PCFSoftShadowMap;
    });

    function pointToString(point: { x: number; y: number; z: number }) {
      const x = point.x.toFixed(3);
      const y = point.y.toFixed(3);
      const z = point.z.toFixed(3);

      return `{ x: ${x}, y: ${y}, z: ${z} }`;
    }

    this.sdk.Pointer.intersection.subscribe(function (intersection) {
      // console.log(`position ${pointToString(intersection.position)}`);
    });
  }
}
