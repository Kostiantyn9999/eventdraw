import {MpSdk, ShowcaseBundleWindow} from "bundle/sdk";
import _, { reject } from "lodash";
import {IFloor, IMatterportDimension, IShape} from "types/floor";
import {IModel} from "types/model"; // const SDK_VERSION = '3.2';

// const SDK_VERSION = '3.2';
const CONTROL_SHAPES = ["RECT_TABLE_DEFAULT", "ROUND_TABLE_DEFAULT"];
// const DISABLE_SHAPES = ["text", "Double_Door", "Single_Door", "Wall"];
const DISABLE_SHAPES = ["text", "Double_Door", "Single_Door"];

interface IComponent {
  type:
    | "mp.chair"
    | "mp.shape"
    | "mp.ambientLight"
    | "mp.directionalLight"
    | "mp.orientedBox"
    | "mp.tableNumber"
    | "mp.wall";
  inputs?: Record<string, any>;
  baseElevation?: number;
}

interface IMatterportItem {
  name: string;
  position: { x: number; y: number; z: number };
  rotation: { x: number; y: number; z: number };
  scale: { x: number; y: number; z: number };
  components: Array<IComponent>;
}

export const GetSDK = (
  elementId: string,
  applicationKey: string,
  sdk_version: string
): Promise<MpSdk> => {
  return new Promise((resolve, reject) => {
    const checkIframe = async () => {
      var iframe = document.getElementById(elementId);

      if (iframe !== undefined && iframe !== null) {
        const iWindow = (<HTMLIFrameElement>iframe)
          .contentWindow as ShowcaseBundleWindow;
        if (iWindow != null && iWindow.MP_SDK) {
          clearInterval(intervalId);

          const sdk = await iWindow.MP_SDK.connect(iWindow, {
            applicationKey: applicationKey,
          });
          resolve(sdk);
        }
      }
    };

    const intervalId = setInterval(checkIframe, 100);
  });
};

export const getImageDimension = (imageSrc: string) => {
  return new Promise((resolve, reject) => {
    const imageEl = document.createElement("img");
    imageEl.src = imageSrc;
    imageEl.onload = function () {
      resolve({
        width: imageEl.width,
        height: imageEl.height
      });
    }
  })
}

export const roundToNearestTarget = (number: number) => {
  const targets = [-360, -270, -180, -90, 0, 90, 180, 270, 360];
  const nearestTarget = targets.reduce((prev, curr) => {
    return (Math.abs(curr - number) < Math.abs(prev - number) ? curr : prev);
  });
  return nearestTarget;
};

export const processData = async (
  models: IModel[],
  floor: IFloor,
  rangeDimension: IMatterportDimension,
  startPosition: any
) => {
  const availableModels = _.filter(models, (model) =>
    _.some(
      floor.shapes,
      (shape) => model.shapeType.trim() == shape.name.trim() || model.shapeType == "chair"
    )
  );

  // Remove unuseful shapes from shape list
  const cleanedModels = _.filter(
    _.get(floor, "shapes"),
    (d) => d.name !== "Wall Horizontal" && d.name !== "Dimensions"
  );

  const rotM = rangeDimension.axis;
  const baseElevation = rangeDimension.baseElevation;
  let layout = floor.layout;
  
  if (floor.imageInformation) {
    // layout = floor.imageInformation.imageDimension;
  }
  
  const w = rotM
    ? rangeDimension.z.max - rangeDimension.z.min
    : rangeDimension.x.max - rangeDimension.x.min;
  const h = rotM
    ? rangeDimension.x.max - rangeDimension.x.min
    : rangeDimension.z.max - rangeDimension.z.min;

  let rateX = layout.width / w;
  let rateZ = layout.height / h;

  let scaleX = Math.abs(rateX);
  let scaleZ = Math.abs(rateZ);

  let rotation = rangeDimension.rotation;
  // if (startPosition.rotation && startPosition.rotation.y) {
  //   rotation -= roundToNearestTarget(startPosition.rotation.y)
  // }
  
  return createMatterportJson(
    cleanedModels,
    availableModels,
    scaleX,
    scaleZ,
    rateX,
    rateZ,
    rangeDimension,
    rotM,
    baseElevation,
    rotation
  );
};

export const createMatterportJson = (
  items: IShape[],
  gltfs: IModel[],
  scaleX: number,
  scaleZ: number,
  rateX: number,
  rateZ: number,
  rangeDimension: IMatterportDimension,
  rotM = true,
  baseElevation = 0,
  rotation = 0,
  xRevert = false,
  yRevert = false
) => {
  const result = {
    version: "1.0",
    payload: {
      objects: [],
    },
  };

  const wMin = rotM ? rangeDimension.z.min : rangeDimension.x.min;
  const wMax = rotM ? rangeDimension.z.max : rangeDimension.x.max;
  const zMin = rotM ? rangeDimension.x.min : rangeDimension.z.min;
  const zMax = rotM ? rangeDimension.x.max : rangeDimension.z.max;

  const filteredItems = _.filter(
    items,
    (item) => !_.includes(DISABLE_SHAPES, item.name)
  );

  const d = _.map(filteredItems, (item) => {
    let defaultValues = null;

    /** Load Model */
    const findIndex = gltfs.findIndex((gltf) => gltf.shapeType.trim() === item.name.trim());
    if (findIndex !== -1) {
      defaultValues = gltfs[findIndex].default;
    }

    const pX =
      typeof item.position.x === "string"
        ? parseFloat(item.position.x)
        : item.position.x;
    const pZ =
      typeof item.position.z === "string"
        ? parseFloat(item.position.z)
        : item.position.z;

    const itemCenterX = pX + item.dimension.width / 2;
    const itemCenterZ = pZ + item.dimension.height / 2;

    const posX = xRevert
      ? rangeDimension.x.max - itemCenterX / rateX
      : itemCenterX / rateX + wMin;

    const posZ = yRevert
      ? rangeDimension.z.max - itemCenterZ / rateZ
      : itemCenterZ / rateZ + zMin;

    // Center Point
    const centerW = (wMax - wMin) / 2 + wMin;
    const centerZ = (zMax - zMin) / 2 + zMin;

    const x = rotM ? posZ : posX;
    const y = baseElevation;
    const z = rotM ? posX : posZ;

    const staticRotationAngle = rotation * -1;
    const staticRotationRadian = (staticRotationAngle * Math.PI) / 180;
    const fX =
      (x - centerW) * Math.cos(staticRotationRadian) -
      (z - centerZ) * Math.sin(staticRotationRadian);
    const fY =
      y + (defaultValues ? Number(defaultValues["base"] || "0") / Math.min(rateX, rateZ) : 0);

    const fZ =
      (x - centerW) * Math.sin(staticRotationRadian) +
      (z - centerZ) * Math.cos(staticRotationRadian);

    const p: IMatterportItem = {} as IMatterportItem;
    _.set(p, "name", item.name);

    let componentD: Array<IComponent>;

    if (item.name === "Wall") {
      _.set(p, "rotation", {
        x: 0,
        y: 0,
        z: 0,
      });
      _.set(p, "position", {
        x: 0,
        y: 0,
        z: 0,
      });
      _.set(p, "scale", { x: 1, y: 1, z: 1 });

      componentD = [
        {
          type: "mp.wall",
          inputs: {
            points: item.points?.map((p) => {
              const centerW =
                (rangeDimension.x.max - rangeDimension.x.min) / 2 +
                rangeDimension.x.min;
              const centerZ =
                (rangeDimension.z.max - rangeDimension.z.min) / 2 +
                rangeDimension.z.min;

              const dx = rangeDimension.x.min + Number(p.x) / rateX - centerW;
              const dy = rangeDimension.z.min + Number(p.y) / rateZ - centerZ;

              const fX =
                dx * Math.cos(staticRotationRadian) -
                dy * Math.sin(staticRotationRadian);
              const fZ =
                dx * Math.sin(staticRotationRadian) +
                dy * Math.cos(staticRotationRadian);

              return {
                x: fX + centerW,
                y: fZ + centerZ,
              };
            }),
          },
        },
      ];
    } else {
      _.set(p, "rotation", {
        x: 0,
        y: rotM ? 90 - staticRotationAngle : -staticRotationAngle,
        z: 0,
      });
      _.set(p, "position", {
        x: centerW + fX,
        // y: -5.8,
        y: fY,
        z: centerZ + fZ,
      });
      _.set(p, "scale", { x: 1, y: 1, z: 1 });

      componentD = [
        {
          type: "mp.shape",
          inputs: {
            id: item.id,
            type: _.get(item, "name"),
            size: {
              x: _.get(item, ["dimension", "width"]) / scaleX,
              y:
                _.get(item, ["dimension", "depth"]) / Math.min(scaleX, scaleZ) === 0
                  ? defaultValues
                    ? defaultValues["height"]
                      ? parseFloat(defaultValues["height"]) / scaleZ
                      : 0
                    : 0
                  : _.get(item, ["dimension", "depth"]) / Math.min(scaleX, scaleZ),
              z: _.get(item, ["dimension", "height"]) / scaleZ,
            },
            rotation: _.get(item, "rotation"),
            baseElevation: Number(item.position.y),
            scaleY: Math.min(scaleX, scaleZ),
            // color: item.fill_color,
          },
        },
        {
          type: "mp.orientedBox",
        },
      ];

      if (CONTROL_SHAPES.includes(p.name)) {
        const chairs = _.get(item, "chairs", []);

        const chairD = _.map(chairs, (d) => {
          return {
            type: "mp.chair",
            inputs: {
              id: d.id,
              size: {
                x: _.get(d, ["dimension", "width"]) / scaleX,
                y: _.get(d, ["dimension", "depth"]) / Math.min(scaleX, scaleZ),
                z: _.get(d, ["dimension", "height"]) / scaleZ,
              },
              localLot: {
                x: _.get(d, ["rotation", "x"]),
                y: _.get(d, ["rotation", "y"]),
                z: _.get(d, ["rotation", "z"]),
              },
              localPos: {
                x: Number(_.get(d, ["position", "x"])) / scaleX,
                y:
                  Number(_.get(d, ["position", "y"])) / Math.min(scaleX, scaleZ) +
                  Number(item.position.y) / Math.min(scaleX, scaleZ),
                z: Number(_.get(d, ["position", "z"])) / scaleZ,
              },
              offset: {
                x: _.get(item, ["dimension", "width"]) / (scaleX * 2),
                y: _.get(item, ["dimension", "depth"]) / (Math.min(scaleX, scaleZ) * 2),
                z: _.get(item, ["dimension", "height"]) / (scaleZ * 2),
              },
              scale: Math.min(scaleX, scaleZ),
            },
          } as IComponent;
        });

        componentD = [
          ...componentD,
          ...chairD,
          {
            type: "mp.tableNumber",
            inputs: { scale: Math.min(scaleX, scaleZ), number: item.number },
          },
        ];
      }
    }

    _.set(p, "components", componentD);

    return p;
  });

  d.push({
    name: "lights",
    position: { x: 0, y: 0, z: 0 },
    rotation: { x: 0, y: 0, z: 0 },
    scale: { x: 1, y: 1, z: 1 },
    components: [
      {
        type: "mp.ambientLight",
        inputs: {
          r: 1.0,
          g: 1.0,
          b: 1.0,
          intensity: 2.5,
        },
      },
      {
        type: "mp.directionalLight",
        inputs: {
          intensity: 0.2,
          position: {
            x: 0,
            y: 20,
            z: -1,
          },
          target: {
            x: -10,
            y: -1,
            z: 2,
          },
          castShadow: false,
          debug: false,
        },
      },
      {
        type: "mp.directionalLight",
        inputs: {
          intensity: 0.2,
          position: {
            x: 20,
            y: 20,
            z: 20,
          },
          target: {
            x: 10,
            y: 0,
            z: 2,
          },
          castShadow: true,
          debug: false,
        },
      },
      {
        type: "mp.directionalLight",
        inputs: {
          intensity: 0.2,
          position: {
            x: -20,
            y: 20,
            z: -20,
          },
          target: {
            x: 10,
            y: 0,
            z: 2,
          },
          castShadow: true,
          debug: false,
        },
      },
    ],
  });

  // d.push({
  //   name: "gestures",
  //   components: [
  //     {
  //       type: "mp.input",
  //       inputs: {
  //         eventsEnabled: false,
  //         userNavigationEnabled: false,
  //         unfiltered: false,
  //       },
  //     },
  //   ],
  // });

  _.set(result, "payload.objects", d);
  return result;
};

function revertPosition(
  position: { x: string | number; y: string | number; z: string | number },
  size: { width: number; height: number },
  dimension1: THREE.Vector2,
  dimension2: THREE.Vector2
) {
  const x =
    typeof position.x === "string" ? parseFloat(position.x) : position.x;
  const z =
    typeof position.z === "string" ? parseFloat(position.z) : position.z;

  const centerItemX = x + size.width;
  const centerItemZ = z + size.height;
}
