import { orientedBoxType, makeOrientedBox } from "./OrientedBox"
import { shapeType, makeShape } from "./Shape"
import { chairType, makeChair } from "./Chair"
import { dimensionType, makeDimension } from "./Dimension";

export const initComponents = async (sdk) => {
  await Promise.all([
    sdk.Scene.register(orientedBoxType, makeOrientedBox),
    sdk.Scene.register(shapeType, makeShape),
    sdk.Scene.register(chairType, makeChair),
    sdk.Scene.register(dimensionType, makeDimension)
  ]);
};
