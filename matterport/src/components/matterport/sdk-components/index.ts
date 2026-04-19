import { MpSdk } from "bundle/sdk";
import { makeOrientedBox, orientedBoxType } from "./OrientedBoxComponent";
import { makeShape, shapeType } from "./ShapeComponent";
import { chairType, makeChair } from "./ChairComponent";
import { dimensionType, makeDimension } from "./DimensionComponent";
import { artImageType, makeArtImage } from "./ArtImageComponent";
import { artVideoType, makeArtVideo } from "./VideoComponent";
import { makePoint, pointType } from "./PointComponent";
import { composerType, makeComposer } from "./ComposerComponent";
import { makeTableNumber, tableNumberType } from "./TableNumberComponent";
import { makeWall, wallType } from "./WallComponent";

export const initComponents = async (sdk: MpSdk) => {
  await Promise.all([
    sdk.Scene.register(orientedBoxType, makeOrientedBox),
    sdk.Scene.register(shapeType, makeShape),
    sdk.Scene.register(chairType, makeChair),
    sdk.Scene.register(dimensionType, makeDimension),
    sdk.Scene.register(artImageType, makeArtImage),
    sdk.Scene.register(artVideoType, makeArtVideo),
    sdk.Scene.register(pointType, makePoint),
    sdk.Scene.register(composerType, makeComposer),
    sdk.Scene.register(tableNumberType, makeTableNumber),
    sdk.Scene.register(wallType, makeWall),
    // sdk.Scene.register(clickableType, makeClickable),
  ]);
};
