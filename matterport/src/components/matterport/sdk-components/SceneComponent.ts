import { ISubscription, Scene } from "bundle/sdk";

export enum ComponentInteractionType {
  CLICK = "INTERACTION.CLICK",
  HOVER = "INTERACTION.HOVER",
}

export interface ComponentContext extends Scene.IComponentContext {
  root: Scene.INode;
  three: any
  obj: THREE.Object3D
}

export class SceneComponent implements Scene.IComponent {
  declare componentType: string;
  declare inputs?: Record<string, unknown> | undefined;
  declare outputs: Record<string, unknown> & Scene.PredefinedOutputs;
  declare events: Record<string, boolean>;
  declare emits?: Record<string, boolean> | undefined;
  declare context: ComponentContext;

  declare bind: (prop: string, src: Scene.IComponent, srcProp: string) => void;
  declare bindEvent: (
    eventType: string,
    src: Scene.IComponent,
    srcEventType: string
  ) => void;
  declare notify: (eventType: string, eventData?: unknown) => void;
  declare spyOnEvent: (spy: Scene.IComponentEventSpy<unknown>) => ISubscription;
}

export default SceneComponent;
