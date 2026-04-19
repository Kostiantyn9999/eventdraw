import React, { MouseEvent, Suspense, useEffect, useState } from "react";
import { Box, Main } from "grommet";
import { connect } from "react-redux";
import { bindActionCreators, Dispatch } from "redux";
import _ from "lodash";
import { useHistory } from "react-router-dom";

import Frame from "components/matterport/Frame";
import Panel from "./panel";
import ScreenLock from "./ScreenLock";
import ViewSwitch from "./ViewSwitch";

import {
  useCreateMatterport,
  useMatterport,
  useUpdateMatterport,
} from "src/controllers/matterport";

import routes from "src/navigation/routes";
import { RootState } from "src/models/store";
import { formatDate } from "./utils";
import { getDimension, mid } from "./panel/CenterPointsDetails";

import { MpSdk } from "bundle/sdk";
import { initComponents } from "components/matterport/admin-sdk-components";
import { SceneLoader, sidToScene } from "components/matterport/SceneLoader";
import { GetSDK } from "components/matterport/utils";
import { scene_data } from "./module/scene";

export enum Mode {
  INSIDE = "mode.inside",
  OUTSIDE = "mode.outside",
  DOLLHOUSE = "mode.dollhouse",
  FLOORPLAN = "mode.floorplan",
  TRANSITIONING = "mode.transitioning",
}

type Props = {
  sceneId?: string;
};

const MatterportAdmin = ({
  sceneId,
  user,
}: Props & ReturnType<typeof mapStateToProps>) => {
  const history = useHistory();
  const { data } = useMatterport(sceneId);
  const createMatterport = useCreateMatterport();
  const updateMatterport = useUpdateMatterport();

  const [item, setItem] = useState({
    x: {
      min: 0,
      max: 1,
    },
    y: {
      min: 0,
      max: 1,
    },
    // matterId: "tLkwcpyJ4vP",
    matterId: "",
    name: "",
    admin: user ? user.name : "",
    regData: formatDate(),
    baseElevation: 0,
    axis: false,
    events: [],
    templates: [],
    rotation: 0,
  });
  const [sdk, setSdk] = useState<MpSdk | null>(null);
  const [slot, setSlot] = useState<MpSdk.Scene.IComponent | null>(null);
  const [centerSlot, setCenterSlot] = useState<MpSdk.Scene.IComponent | null>(
    null
  );

  const [comparedBackImage, setComparedBackImage] = useState<{
    image: HTMLImageElement;
  } | null>(null);
  const [lockScreen, setLockScreen] = useState(false);

  const [previousX, setPreviousX] = useState<number>(0);
  const [previousZ, setPreviousY] = useState<number>(0);
  const [isDrag, setIsDrag] = useState(false);

  const [floors, setFloors] = useState([]);
  const [labels, setLabels] = useState([]);

  useEffect(() => {
    if (data) {
      const res = data.data;
      setItem({
        x: {
          min: res.minX,
          max: res.maxX,
        },
        y: {
          min: res.minY,
          max: res.maxY,
        },
        matterId: res.mat,
        name: res.name,
        admin: res.admin,
        regData: res.creation_date,
        baseElevation: res.baseElevation,
        axis: res.axis,
        events: res.events === null || res.events === "null" ? [] : res.events,
        rotation: res.rotation,
        templates: res.templates.map((d: any) => ({
          id: d.id,
          templateActive: true,
          templateName: d.name,
        })),
      });

      initMatterport();
    }
  }, [data]);

  useEffect(() => {
    if (slot) {
      (slot as any).inputs.xMin = item.x.min;
      (slot as any).inputs.xMax = item.x.max;
      (slot as any).inputs.yMin = item.y.min;
      (slot as any).inputs.yMax = item.y.max;
      (slot as any).inputs.axis = item.axis;
      (slot as any).inputs.elevation = item.baseElevation;
      (slot as any).inputs.rotation = item.rotation;
    }

    if (centerSlot) {
      const centerX = mid(item.x.min, item.x.max);
      const centerY = mid(item.y.min, item.y.max);

      (centerSlot as any).inputs.x = centerX;
      (centerSlot as any).inputs.y = centerY;
    }
  }, [item]);

  useEffect(() => {
    if (slot && comparedBackImage) {
      (slot as any).inputs.comparedBackImage = comparedBackImage;
    }
  }, [comparedBackImage]);

  const initMatterport = async () => {
    const applicationKey = process.env.MATTERPORT_APPLICATION_KEY as string;

    const sdk = await GetSDK("sdk-iframe", applicationKey, "3.5");
    setSdk(sdk);

    const res = data
      ? data.data
      : {
          minX: item.x.min,
          maxX: item.x.max,
          minY: item.y.max,
          maxY: item.y.max,
          axis: item.axis,
          baseElevation: item.baseElevation,
          rotation: item.rotation,
        };

    setTimeout(async () => {
      if (sdk) {
        await initComponents(sdk);
        const sceneLoader = new SceneLoader(sdk);
        sidToScene.set("AAWs9eZ9ip6_AA", scene_data);
        const loadCallback = (node: MpSdk.Scene.INode) => {
          const componentIterator = node.componentIterator();
          for (const component of componentIterator) {
            if (
              component.componentType === "mp.dimension" &&
              component.inputs
            ) {
              component.inputs.xMin = res.minX;
              component.inputs.xMax = res.maxX;
              component.inputs.yMin = res.minY;
              component.inputs.yMax = res.maxY;
              component.inputs.axis = res.axis;
              component.inputs.elevation = res.baseElevation;
              component.inputs.rotation = res.rotation;

              setSlot(component);
            }

            if (component.componentType === "mp.point" && component.inputs) {
              component.inputs.x = 0;
              component.inputs.y = 0;

              setCenterSlot(component);
            }
          }
        };

        await sceneLoader.load("AAWs9eZ9ip6_AA", loadCallback);

        const tourList = await sdk.Tour.getData();
        const floorList = await sdk.Floor.getData();
        const labelList = await sdk.Label.getData();
        const roomList = await sdk.Room.data;

        setFloors(floorList);
        setLabels(labelList);
      }
    }, 3000);
  };

  const toggleScreen = () => {
    setLockScreen(!lockScreen);
  };

  const mouseDownLockScreen = async (e: MouseEvent) => {
    const mousePos = {
      x: e.clientX,
      y: e.clientY,
    };
    const initPos = await sdk.Renderer.getWorldPositionData(mousePos, 0);
    setPreviousX(initPos.position.x);
    setPreviousY(initPos.position.z);
    setIsDrag(true);
  };

  const mouseMoveLockScreen = async (e: MouseEvent) => {
    if (!isDrag || !sdk) return;

    const mousePos = {
      x: e.clientX,
      y: e.clientY,
    };
    const initPos = await sdk.Renderer.getWorldPositionData(mousePos, 0);
    const offsetX = initPos.position.x - previousX;
    const offsetZ = initPos.position.z - previousZ;

    // const { item, comparedBackImage } = this.state;
    const width = item.x.max - item.x.min;
    const height = item.y.max - item.y.min;
    const centerX = mid(item.x.min, item.x.max);
    const centerY = mid(item.y.min, item.y.max);

    const { xMin, xMax, yMin, yMax } = getDimension(width, height, [
      centerX + offsetX,
      centerY + offsetZ,
    ]);

    setItem({
      ...item,
      x: {
        min: xMin,
        max: xMax,
      },
      y: {
        min: yMin,
        max: yMax,
      },
    });
    setPreviousX(initPos.position.x);
    setPreviousY(initPos.position.z);
  };

  const mouseLeaveLockScreen = () => {
    setIsDrag(false);
  };

  const clickSave = async () => {
    try {
      const params = _.omitBy(
        {
          mat: _.get(item, "matterId"),
          name: _.get(item, "name"),
          minX: _.get(item, ["x", "min"]),
          maxX: _.get(item, ["x", "max"]),
          minY: _.get(item, ["y", "min"]),
          maxY: _.get(item, ["y", "max"]),
          baseElevation: _.get(item, "baseElevation"),
          axis: _.get(item, "axis", false),
          rotation: _.get(item, "rotation", 0),
          events: item.events.filter((ev) => (ev ? true : false)),
          templates: item.templates.filter((ev) => (ev ? true : false)),
        },
        _.isNil
      );

      if (_.isNil(sceneId)) {
        params["reg_man"] = 5;

        // store.dispatch(saveMatterport(params));
        createMatterport.mutate(params);
      } else {
        params["id"] = sceneId;
        // store.dispatch(updateMatterport(sceneId, params));
        updateMatterport.mutate({
          id: sceneId,
          data: params,
        });
      }
    } catch (e) {}
  };

  const clickCancel = () => {
    history.push(routes.MATTERPORTS);
  };

  const confirmBackgroundImage = (image: HTMLImageElement) => {
    const width = item.x.max - item.x.min;
    const ratio = image.height / image.width;
    // const height = item.y.max - item.y.min;
    const newHeight = width * ratio;
    const centerX = mid(item.x.min, item.x.max);
    const centerY = mid(item.y.min, item.y.max);

    const { xMin, xMax, yMin, yMax } = getDimension(width, newHeight, [
      centerX,
      centerY,
    ]);

    setItem({
      ...item,
      x: {
        min: xMin,
        max: xMax,
      },
      y: {
        min: yMin,
        max: yMax,
      },
    });
    setComparedBackImage({ image: image });
  };

  return (
    <Suspense fallback={null}>
      <Main direction="row" pad="0px" background="white">
        <Box flex basis={"3/4"} fill style={{ position: "relative" }}>
          {lockScreen && (
            <Box
              fill
              style={{ position: "absolute" }}
              onMouseDown={mouseDownLockScreen}
              onMouseMove={mouseMoveLockScreen}
              onMouseUp={mouseLeaveLockScreen}
            />
          )}
          {item.matterId && <Frame src={item.matterId} />}
          <ScreenLock lockScreen={lockScreen} toggleScreen={toggleScreen} />
        </Box>

        <Panel
          sceneId={sceneId}
          item={item}
          clickSave={clickSave}
          clickCancel={clickCancel}
          setItem={setItem}
          initMatterport={initMatterport}
          comparedBackImage={comparedBackImage}
          confirmBackgroundImage={confirmBackgroundImage}
          labels={labels}
          floors={floors}
        />

        <ViewSwitch sdk={sdk} />
      </Main>
    </Suspense>
  );
};

const mapStateToProps = (state: RootState) => ({
  user: state.auth.user,
});

const mapDispatchToProps = (dispatch: Dispatch) => {
  return bindActionCreators({}, dispatch);
};

export default connect(mapStateToProps, mapDispatchToProps)(MatterportAdmin);
