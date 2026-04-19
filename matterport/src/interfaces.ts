import { MpSdk } from "bundle/sdk";
import { IModel } from "types/model";
import { InteractionEvent } from "./pages/main";

export interface IContext {
  scene: IScene;
  sdk: ISdk;
  isShared: boolean;
}

export type IContentType = {
  clickSpy: MpSdk.Scene.IComponentEventSpy | null;
};

export interface ISdk {
  init(applicationKey: string): void;

  sdk: MpSdk;

  onChanged(callback: (sdk: any) => void): void;
}

export interface IScene {
  /**
   * Serialize the entire scene to a string.
   */
  loaded: boolean;
  startPosition: any;
  availableModels: IModel[];
  preparedD: string;
  spy: MpSdk.Scene.IComponentEventSpy<InteractionEvent> | null;

  getObjects(): Array<MpSdk.Scene.INode>;

  serialize(): Promise<string>;

  deserialize(
    serialized: string,
    callback: (node: MpSdk.Scene.INode) => void
  ): Promise<void>;

  loadModels(mss: any, chairM: any): void;

  setAvailableModels(availableModels: IModel[]): void;

  setSavedData(data: string): void;

  setClickSpy(spy: MpSdk.Scene.IComponentEventSpy<InteractionEvent>): void;
}
