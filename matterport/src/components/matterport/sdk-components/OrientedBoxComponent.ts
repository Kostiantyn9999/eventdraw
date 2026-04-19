import { SceneComponent } from "./SceneComponent";

class OrientedBoxComponent extends SceneComponent {
  inputs = {
    size: { x: 1, y: 1, z: 1 },
  };

  onInit() {}
}

export const orientedBoxType = "mp.orientedBox";
export const makeOrientedBox = function () {
  return new OrientedBoxComponent();
};
