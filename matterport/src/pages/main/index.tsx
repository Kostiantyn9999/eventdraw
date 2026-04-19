import React from "react";
import { bindActionCreators, Dispatch } from "redux";
import { connect } from "react-redux";
import { Box } from "grommet";
import _ from "lodash";
//@components
import Frame from "components/matterport/Frame";
import ObjectControlPanel from "./panel";
import RightControlPanel from "./RightControlPanel";
import BottomControlPanel from "./BottomControlPanel";
import TopControlPanel from "./TopControlPanel";
import { AppContext } from "src/AppContext";
import { Version } from "src/pages/main/Version";
//@store
import { RootState } from "models/store";
import { getDimension } from "controllers/matterport";
import { addToast } from "models/actions/toastAction";
import { authToken } from "controllers/auth";
//@utils
import { processData } from "components/matterport/utils";
import { loadGltf, modelLoader } from "src/pages/main/utils";
import { getModels } from "controllers/models";
//@types
import { MpSdk } from "bundle/sdk";
import { SHAPE } from "types/shape";
import { IMatterportDimension, IShape } from "types/floor";
import { ComponentInteractionType } from "components/matterport/sdk-components/SceneComponent";
import { ChairComponent } from "components/matterport/sdk-components/ChairComponent";
import { WallComponent } from "components/matterport/sdk-components/WallComponent";
import chairModels from "src/constants/chair-models";

const testToken = "DRWUpr_a0EUcQzJg6xDU";
const testBuilding = "4kxHwSKD3Yg";

type IProps = {
  id?: string;
  building?: string;
  token?: string;
};

type PageProps = ReturnType<typeof mapStateToProps> &
  ReturnType<typeof mapDispatchToProps> &
  IProps;

type IState = {
  selection: MpSdk.Scene.INode | null;
  shapes: { [id: string]: IShape };
  rangeDimension: IMatterportDimension | null;
  matterId: string | null;
  isLock: boolean;
  gestureComponent: any;
  composerComponent: any;
};

export type InteractionEvent = {
  node: MpSdk.Scene.INode;
  type: SHAPE;
};

type PanelHandle = React.ElementRef<typeof ObjectControlPanel>;

class MainScreen extends React.Component<PageProps, IState> {
  // context: IContext;
  static contextType = AppContext;
  declare context: React.ContextType<typeof AppContext>;
  panelRef: React.RefObject<PanelHandle>;

  constructor(props: PageProps) {
    super(props);
    this.state = {
      rangeDimension: null,
      matterId: null,
      shapes: {},
      selection: null,
      isLock: false,
      gestureComponent: null,
      composerComponent: null,
    };
    // this.applicationKey = process.env.MATTERPORT_APPLICATION_KEY as string;

    this.panelRef = React.createRef();

    this.itemSelected = this.itemSelected.bind(this);
    this.openArt = this.openArt.bind(this);
    this.openShape = this.openShape.bind(this);
    this.openWall = this.openWall.bind(this);

    this.onClose = this.onClose.bind(this);
    this.setLock = this.setLock.bind(this);
    this.toggleTransparent = this.toggleTransparent.bind(this);
    this.toggleWall = this.toggleWall.bind(this);
    this.changeWallOpacity = this.changeWallOpacity.bind(this);
  }

  async componentDidMount() {
    const sid = this.props.building || testBuilding;
    const id = this.props.id;
    const userToken = this.props.token || testToken;

    const rangeDimension = ((await getDimension(sid, id)) as any).data;

    // Get Scene info from server
    const originFloor = await authToken(userToken);
    originFloor.shapes.forEach((shape) => {
      shape.name = shape.name.trim();
    });

    // Get Model data from server
    const models = await getModels();

    if (_.isNil(rangeDimension)) {
      this.props.addToast({
        content: {
          header: "Notification",
          message:
            "Your Matterport is not registered, Please contact EventDraw Support!",
        },
      });
    } else {
      this.setState({
        rangeDimension: rangeDimension,
        matterId: sid,
      });

      const availableModels = _.filter(models, (model) =>
        _.some(
          originFloor.shapes,
          (shape) => model.shapeType.trim() == shape.name.trim() || model.shapeType == "chair"
        )
      );

      class ClickSpy
        implements MpSdk.Scene.IComponentEventSpy<InteractionEvent>
      {
        public eventType = ComponentInteractionType.CLICK;

        constructor(private mainView: MainScreen) {}

        onEvent(payload: InteractionEvent) {
          this.mainView.itemSelected(payload.node, payload.type);
        }
      }

      const clickSpy = new ClickSpy(this);
      this.context.scene.setClickSpy(clickSpy);

      const loadCallback = (node: MpSdk.Scene.INode) => {
        const componentIterator = node.componentIterator();

        for (const component of componentIterator) {
          if (component.componentType === "mp.shape") {
            component.spyOnEvent(clickSpy);
          }

          if (component.componentType === "mp.chair") {
            component.spyOnEvent(clickSpy);
          }
        }
      };

      let d: any = null;

      const checkLoaded = async () => {
        if (this.context.scene.loaded) {
          clearInterval(intervalId);

          try {
            await this.context.sdk.sdk.Floor.moveTo(rangeDimension.floor - 1);
          } catch (error) {
            console.warn("Could not switch Matterport floor:", error);
          }
          
          if (!this.context.isShared) {
            d = await processData(models, originFloor, rangeDimension, this.context.scene.startPosition);
            this.context.scene.setAvailableModels(availableModels);
          }

          const ms = await loadGltf(
            this.context.scene.availableModels,
            4,
            window.THREE
          );

          const mss = _.keyBy(ms, "shapeType");

          (window as any).mms = mss;

          for (const item of chairModels) {
            const chairM = await modelLoader(item.url, (window as any).THREE);
            (window as any).mms[item.model] = chairM;
          }
          (window as any).isChairModelLoaded = true;
          
          if (this.context.isShared) {
            await this.context.scene.deserialize(
              this.context.scene.preparedD,
              loadCallback
            );
          } else {
            await this.context.scene.deserialize(
              JSON.stringify(d),
              loadCallback
            );
          }

          await this.context.scene.loadModels(mss, (window as any).mms[chairModels[0].model]);

          // Checking
          // const tours = await this.context.sdk.sdk.Tour.getData();
          // const floors = await this.context.sdk.sdk.Floor.getData();
          // const labels = await this.context.sdk.sdk.Label.getData();
          // const rooms = await this.context.sdk.sdk.Room.data;
          // console.log(tours);
          // console.log(floors);
          // console.log(labels);
          // console.log("Room", rooms);
          // await this.context.sdk.sdk.Tour.start("UcyEzDtUbwT");

          const [sceneObject] = await this.context.sdk.sdk.Scene.createObjects(
            1
          );
          const gestureNode = sceneObject.addNode();
          const inputComponent = gestureNode.addComponent("mp.input", {
            eventsEnabled: false,
            userNavigationEnabled: true,
          });
          gestureNode.start();

          const [sceneObject1] = await this.context.sdk.sdk.Scene.createObjects(
            1
          );
          const gestureNode1 = sceneObject1.addNode();
          const composerComponent = gestureNode1.addComponent("mp.composer");
          gestureNode1.start();

          this.setState({
            gestureComponent: inputComponent,
            composerComponent: composerComponent,
          });
        }
      };

      const intervalId = setInterval(checkLoaded, 100);
    }

    /** Test Code */
    // if (this.panelRef.current) this.panelRef.current.openObject("chair");
  }

  itemSelected(node: MpSdk.Scene.INode, type: SHAPE) {
    // console.log(this.state.isLock);
    if (this.state.isLock) {
      this.state.composerComponent.setActive(node.obj3D);

      this.setState({ selection: node });
      if (this.panelRef.current) this.panelRef.current.openObject(type);
    }
  }

  // Open Panels
  openArt() {
    if (this.panelRef.current) this.panelRef.current.openArt();
  }

  openShape() {}

  openWall() {
    if (this.panelRef.current) this.panelRef.current.openWall();
  }

  onClose() {
    this.state.composerComponent.setDeactive();
  }

  setLock(lock: boolean) {
    const { gestureComponent, composerComponent } = this.state;

    this.setState({ isLock: lock });

    if (this.panelRef.current) {
      this.panelRef.current.close();
      composerComponent.setDeactive();
    }

    if (gestureComponent) {
      gestureComponent.inputs.userNavigationEnabled = !lock;
    }
  }

  toggleTransparent(lock: boolean) {
    const nodes = this.context.scene.getObjects();

    nodes.forEach((node) => {
      const componentIterator = node.componentIterator();
      for (const component of componentIterator) {
        if (component.componentType === "mp.shape") {
          (component as ChairComponent).inputs.lock = lock;
        }
        if (component.componentType === "mp.chair") {
          (component as ChairComponent).inputs.lock = lock;
        }
      }
    });
  }

  toggleWall() {
    const nodes = this.context.scene.getObjects();

    nodes.forEach((node) => {
      const componentIterator = node.componentIterator();
      for (const component of componentIterator) {
        if (component.componentType === "mp.wall") {
          (component as WallComponent).inputs.hidden = !(
            component as WallComponent
          ).inputs.hidden;
        }
      }
    });
  }

  changeWallOpacity(opacity: number) {
    const nodes = this.context.scene.getObjects();

    nodes.forEach((node) => {
      const componentIterator = node.componentIterator();
      for (const component of componentIterator) {
        if (component.componentType === "mp.wall") {
          (component as WallComponent).inputs.opacity = opacity;
        }
      }
    });
  }

  render() {
    const { matterId, rangeDimension, shapes, isLock } = this.state;

    return (
      <Box fill>
        {matterId && rangeDimension && <Frame src={matterId} />}
        <ObjectControlPanel
          ref={this.panelRef}
          items={shapes}
          node={this.state.selection}
          onClose={this.onClose}
        />
        {isLock && (
          <>
            <RightControlPanel
              openArt={this.openArt}
              openShape={this.openShape}
              openWall={this.openWall}
              toggleTransparent={this.toggleTransparent}
            />
            <BottomControlPanel mat={this.props.building || "tLkwcpyJ4vP"} />
          </>
        )}
        <TopControlPanel
          lock={isLock}
          setLock={this.setLock}
          toggleWall={this.toggleWall}
          changeWallOpacity={this.changeWallOpacity}
        />
        <Version />
      </Box>
    );
  }
}

const mapStateToProps = (state: RootState) => ({
  // plans: state.plans,
});

const mapDispatchToProps = (dispatch: Dispatch) => {
  return bindActionCreators(
    {
      addToast,
    },
    dispatch
  );
};

export default connect(mapStateToProps, mapDispatchToProps)(MainScreen);
