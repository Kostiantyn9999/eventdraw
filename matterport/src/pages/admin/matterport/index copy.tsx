import React, { Component, MouseEvent, Suspense } from "react";
import { connect } from "react-redux";
import { bindActionCreators, Dispatch } from "redux";
import { Box, Main } from "grommet";
import { withRouter, RouteComponentProps } from "react-router-dom";
import _ from "lodash";

import store, { RootState } from "src/models/store";

import Frame from "components/matterport/Frame";
import { initComponents } from "components/matterport/admin-sdk-components";
import { SceneLoader, sidToScene } from "components/matterport/SceneLoader";
import { GetSDK } from "components/matterport/utils";

import Panel from "./panel";
import { fetchMatterport } from "src/controllers/matterport";

// import { saveMatterport, updateMatterport } from "models/actions/matterport";

import routes from "src/navigation/routes";
import { formatDate } from "./utils";
import { MpSdk } from "bundle/sdk";
import { scene_data } from "./module/scene";
import { IEvent, ITemplate } from "types/floor";
import ViewSwitch from "./ViewSwitch";
import ScreenLock from "./ScreenLock";
import { getDimension, mid } from "./panel/CenterPointsDetails";
import {
  ConfirmRatioModal,
  myPromiseConfirmModal,
} from "./panel/ConfirmRatioModal";

// import { IContentType, IContext } from "src/interfaces";
// import { AppContext } from "src/AppContext";

export type IEditableMatterport = {
  x: {
    min: number;
    max: number;
  };
  y: {
    min: number;
    max: number;
  };
  matterId: string;
  name: string;
  admin: string;
  regData: string;
  baseElevation: number;
  axis: boolean;
  events: Array<IEvent>;
  templates: Array<ITemplate>;
  rotation: number;
  // event: IEvent | null;
};

type Props = {
  sceneId: string;
};

type PageProps = ReturnType<typeof mapStateToProps> &
  ReturnType<typeof mapDispatchToProps> &
  Props &
  RouteComponentProps;

type State = {
  sdk: MpSdk | null;
  slot: MpSdk.Scene.IComponent | null;
  centerSlot: MpSdk.Scene.IComponent | null;
  item: IEditableMatterport;
  comparedBackImage: { image: HTMLImageElement } | null;
  lockScreen: boolean;
};

export enum Mode {
  INSIDE = "mode.inside",
  OUTSIDE = "mode.outside",
  DOLLHOUSE = "mode.dollhouse",
  FLOORPLAN = "mode.floorplan",
  TRANSITIONING = "mode.transitioning",
}
export class MatterportAdmin extends Component<PageProps, State> {
  // context: IContext;
  // static contextType = AppContext;

  sdk: MpSdk | null = null;
  applicationKey: string;

  state = {
    sdk: null,
    slot: null,
    centerSlot: null,
    comparedBackImage: null,
    item: {
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
      admin: this.props.user ? this.props.user.name : "",
      regData: formatDate(),
      baseElevation: 0,
      axis: false,
      events: [],
      templates: [],
      rotation: 0,
    },
    lockScreen: false,
  };

  previousX: number;
  previousZ: number;
  isDrag: boolean;

  constructor(props: PageProps) {
    super(props);

    this.setItem = this.setItem.bind(this);
    this.clickSave = this.clickSave.bind(this);
    this.clickCancel = this.clickCancel.bind(this);
    this.confirmBackgroundImage = this.confirmBackgroundImage.bind(this);

    this.applicationKey = process.env.MATTERPORT_APPLICATION_KEY as string;

    this.isDrag = false;

    this.toggleScreen = this.toggleScreen.bind(this);
    this.mouseDownLockScreen = this.mouseDownLockScreen.bind(this);
    this.mouseMoveLockScreen = this.mouseMoveLockScreen.bind(this);
    this.mouseLeaveLockScreen = this.mouseLeaveLockScreen.bind(this);
  }

  async componentDidMount() {
    if (this.props.sceneId) {
      const data = (await fetchMatterport(this.props.sceneId)) as any;
      const res = data.data;
      
      this.setState({
        item: {
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
          events:
            res.events === null || res.events === "null" ? [] : res.events,
          rotation: res.rotation,
          templates: res.templates.map((d: any) => ({
            id: d.id,
            templateActive: true,
            templateName: d.name,
          })),
        },
      });
    }

    this.initMatterport();
  }

  componentDidUpdate(prevProps: PageProps, prevState: State) {
    if (prevState.item !== this.state.item) {
      if (this.state.slot && this.state.item) {
        (this.state.slot as any).inputs.xMin = this.state.item.x.min;
        (this.state.slot as any).inputs.xMax = this.state.item.x.max;
        (this.state.slot as any).inputs.yMin = this.state.item.y.min;
        (this.state.slot as any).inputs.yMax = this.state.item.y.max;
        (this.state.slot as any).inputs.axis = this.state.item.axis;
        (this.state.slot as any).inputs.elevation =
          this.state.item.baseElevation;
        (this.state.slot as any).inputs.rotation = this.state.item.rotation;
      }

      if (this.state.centerSlot && this.state.item) {
        const centerX = mid(this.state.item.x.min, this.state.item.x.max);
        const centerY = mid(this.state.item.y.min, this.state.item.y.max);

        (this.state.centerSlot as any).inputs.x = centerX;
        (this.state.centerSlot as any).inputs.y = centerY;
      }
    }

    if (prevState.comparedBackImage !== this.state.comparedBackImage) {
      if (this.state.slot && this.state.comparedBackImage) {
        (this.state.slot as any).inputs.comparedBackImage =
          this.state.comparedBackImage;
      }
    }
  }

  async initMatterport() {
    const data = (await fetchMatterport(this.props.sceneId)) as any;
    const res = data.data;

    this.sdk = await GetSDK("sdk-iframe", this.applicationKey, "3.5");
    this.setState({ sdk: this.sdk });

    setTimeout(async () => {
      if (this.sdk) {
        await initComponents(this.sdk);
        const sceneLoader = new SceneLoader(this.sdk);
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

              this.setState({ slot: component });
            }

            if (component.componentType === "mp.point" && component.inputs) {
              component.inputs.x = 0;
              component.inputs.y = 0;

              this.setState({ centerSlot: component });
            }
          }
        };
        await sceneLoader.load("AAWs9eZ9ip6_AA", loadCallback);

        // const [sceneObject] = await this.sdk.Scene.createObjects(1);
        // const node = sceneObject.addNode();
        // const inputComponent = node.addComponent("mp.input", {
        //   eventsEnabled: false,
        //   userNavigationEnabled: false,
        // });
        // node.start();

        // // Define a click event spy
        // class ClickSpy {
        //   public eventType = "INTERACTION.CLICK";
        //   public onEvent(payload: unknown) {
        //     console.log("received", payload);
        //   }
        // }

        // inputComponent.spyOnEvent(new ClickSpy());
        // inputComponent.inputs.userNavigationEnabled = false;
        // inputComponent.inputs.eventsEnabled = false;

        // this.sdk.Camera.pose.subscribe(function (pose) {
        //   // Changes to the Camera pose have occurred.
        //   console.log('Current position is ', pose.position);
        //   console.log('Rotation angle is ', pose.rotation);
        //   console.log('Sweep UUID is ', pose.sweep);
        //   console.log('View mode is ', pose.mode);
        // });

        // this.sdk.Measurements.toggleMode(true);

        this.sdk.Tour.stop().then(() => {
          console.log("stoped");
        });
      }
    }, 3000);
  }

  setItem(item: IEditableMatterport) {
    this.setState({ item: item });
  }

  async clickSave() {
    try {
      const { item } = this.state;
      const { sceneId } = this.props;

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
      } else {
        params["id"] = sceneId;
        // store.dispatch(updateMatterport(sceneId, params));
      }
    } catch (e) {
      
    }
  }

  clickCancel() {
    this.props.history.push(routes.MATTERPORTS);
  }

  confirmBackgroundImage(image: HTMLImageElement) {
    // ! Changing dimension here
    const item = this.state.item;
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

    this.setState({
      item: {
        ...item,
        x: {
          min: xMin,
          max: xMax,
        },
        y: {
          min: yMin,
          max: yMax,
        },
      },
      comparedBackImage: {
        image: image,
      },
    });
  }

  async mouseDownLockScreen(e: MouseEvent) {
    const mousePos = {
      x: e.clientX,
      y: e.clientY,
    };
    const initPos = await this.sdk.Renderer.getWorldPositionData(mousePos, 0);
    this.previousX = initPos.position.x;
    this.previousZ = initPos.position.z;

    this.isDrag = true;
  }
  async mouseMoveLockScreen(e: MouseEvent) {
    if (!this.isDrag) return;

    const mousePos = {
      x: e.clientX,
      y: e.clientY,
    };
    const initPos = await this.sdk.Renderer.getWorldPositionData(mousePos, 0);
    const offsetX = initPos.position.x - this.previousX;
    const offsetZ = initPos.position.z - this.previousZ;

    const { item, comparedBackImage } = this.state;
    const width = item.x.max - item.x.min;
    const height = item.y.max - item.y.min;
    const centerX = mid(item.x.min, item.x.max);
    const centerY = mid(item.y.min, item.y.max);

    const { xMin, xMax, yMin, yMax } = getDimension(width, height, [
      centerX + offsetX,
      centerY + offsetZ,
    ]);

    this.setState({
      item: {
        ...item,
        x: {
          min: xMin,
          max: xMax,
        },
        y: {
          min: yMin,
          max: yMax,
        },
      },
    });

    this.previousX = initPos.position.x;
    this.previousZ = initPos.position.z;
  }

  mouseLeaveLockScreen(e: MouseEvent) {
    this.isDrag = false;
  }

  toggleScreen() {
    const { lockScreen } = this.state;
    this.setState({ lockScreen: !lockScreen });
  }

  render() {
    const { item, sdk, comparedBackImage, lockScreen } = this.state;
    const { sceneId } = this.props;

    return (
      <Suspense fallback={null}>
        <Main direction="row" pad="0px" background="white">
          <Box flex basis={"3/4"} fill style={{ position: "relative" }}>
            {lockScreen && (
              <Box
                fill
                style={{ position: "absolute" }}
                onMouseDown={this.mouseDownLockScreen}
                onMouseMove={this.mouseMoveLockScreen}
                onMouseUp={this.mouseLeaveLockScreen}
              />
            )}
            {item.matterId && <Frame src={item.matterId} />}
            <ScreenLock
              lockScreen={lockScreen}
              toggleScreen={this.toggleScreen}
            />
          </Box>
          <Panel
            sceneId={sceneId}
            item={item}
            clickSave={this.clickSave}
            clickCancel={this.clickCancel}
            setItem={this.setItem}
            initMatterport={this.initMatterport}
            comparedBackImage={comparedBackImage}
            confirmBackgroundImage={this.confirmBackgroundImage}
          />
          <ViewSwitch sdk={sdk} />
        </Main>
      </Suspense>
    );
  }
}

const mapStateToProps = (state: RootState) => ({
  user: state.auth.user,
});

const mapDispatchToProps = (dispatch: Dispatch) => {
  return bindActionCreators({}, dispatch);
};

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(withRouter(MatterportAdmin));
