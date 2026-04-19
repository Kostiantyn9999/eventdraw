import { dimensionType, makeDimension } from "./DimensionComponent";
import { makePoint, pointType } from "./PointComponent";

export const initComponents = async (sdk) => {
    await Promise.all([
        sdk.Scene.register(dimensionType, makeDimension),
        sdk.Scene.register(pointType, makePoint)
    ]);
};
