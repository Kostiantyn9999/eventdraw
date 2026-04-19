import React, { useContext } from "react";
import { Box, Button, Image, Layer, RangeInput, Text } from "grommet";
import MovingControl from "./MovingControl";

import lockImg from "assets/images/lock.png";
import unlockImg from "assets/images/unlock.png";
import tourImg from "assets/images/tour.png";
import viewImg from "assets/images/360.png";
import dollhouseImg from "assets/images/dollhouse.png";
import wallImg from "assets/images/wall_setting.svg";

import { AppContext } from "src/AppContext";

const SettingButton = ({
  tip,
  onClick,
  children,
}: {
  tip: string;
  children: JSX.Element | JSX.Element[];
  onClick: () => void;
}) => {
  return (
    <Button
      tip={{
        plain: true,
        dropProps: { align: { top: "bottom" } },
        content: (
          <Box
            pad="xxsmall"
            margin="xsmall"
            elevation="small"
            // background="#000000"
            background="grey"
            round="xsmall"
            overflow="hidden"
            align="center"
          >
            <Text color="#ffffff" size="small">
              {tip}
            </Text>
          </Box>
        ),
      }}
      onClick={onClick}
    >
      <Box
        width="30px"
        height="30px"
        // background="#000000"
        background="grey"
        round="xsmall"
        pad="xsmall"
      >
        {children}
      </Box>
    </Button>
  );
};

type Props = {
  lock: boolean;
  setLock: (lock: boolean) => void;
  toggleWall: VoidFunction;
  changeWallOpacity: (opacity: number) => void;
};

const TopControlPanel = ({
  lock,
  setLock,
  toggleWall,
  changeWallOpacity,
}: Props) => {
  const { sdk } = useContext(AppContext);

  const goDollHouseMode = () => {
    if (sdk.sdk) {
      sdk.sdk.Mode.moveTo(sdk.sdk.Mode.Mode.DOLLHOUSE);
    }
  };

  const goWalkMode = () => {
    if (sdk.sdk) {
      sdk.sdk.Mode.moveTo(sdk.sdk.Mode.Mode.INSIDE);
    }
  };

  const goFloorMode = () => {
    if (sdk.sdk) {
      sdk.sdk.Mode.moveTo(sdk.sdk.Mode.Mode.FLOORPLAN);
    }
  };

  const onForward = () => {
    // console.log(data);
    if (sdk.sdk) {
      sdk.sdk.Camera.moveInDirection(sdk.sdk.Camera.Direction.FORWARD);
    }
  };

  const onBack = () => {
    if (sdk.sdk) {
      sdk.sdk.Camera.moveInDirection(sdk.sdk.Camera.Direction.BACK);
    }
  };

  const onLeft = () => {
    if (sdk.sdk) {
      sdk.sdk.Camera.moveInDirection(sdk.sdk.Camera.Direction.LEFT);
    }
  };

  const onRight = () => {
    if (sdk.sdk) {
      sdk.sdk.Camera.moveInDirection(sdk.sdk.Camera.Direction.RIGHT);
    }
  };

  const onLeftRotate = () => {
    if (sdk.sdk) {
      // data.sdk.Camera.setRotation({ x: 0, y: 2, z: 0 });
      sdk.sdk.Camera.rotate(-15, 0);
    }
  };

  const onRightRotate = () => {
    if (sdk.sdk) {
      sdk.sdk.Camera.rotate(15, 0);
    }
  };

  const onZoomIn = () => {
    if (sdk.sdk) {
      sdk.sdk.Camera.zoomBy(0.1);
    }
  };

  const onZoomOut = () => {
    if (sdk.sdk) {
      sdk.sdk.Camera.zoomBy(-0.1);
    }
  };

  const toggleLock = () => {
    setLock(!lock);
  };

  const clickToggleWall = () => {
    toggleWall();
  };

  const renderAction = () => {
    return (
      <Box direction="row" align="center" gap="xsmall">
        <Box id="sdk-control-overlay" style={{ position: "absolute", zIndex: 10000  }} width={"100%"} height={"500px"}></Box>
        <SettingButton tip={lock ? "Lock" : "Unlock"} onClick={toggleLock}>
          <Image
            src={lock ? lockImg : unlockImg}
            style={{ transform: "scale(1.5)" }}
          />
        </SettingButton>

        <SettingButton tip="Toggle Wall" onClick={clickToggleWall}>
          <Image
            src={wallImg}
            style={{ transform: "scale(1.0)", marginTop: "3px" }}
          />
        </SettingButton>

        <SettingButton tip="Walking Tour" onClick={goWalkMode}>
          <Image src={tourImg} style={{ transform: "scale(1.5)" }} />
        </SettingButton>

        <SettingButton tip="Birds's Eye" onClick={goFloorMode}>
          <Image src={viewImg} style={{ transform: "scale(1.5)" }} />
        </SettingButton>

        <SettingButton tip="DollHouse" onClick={goDollHouseMode}>
          <Image src={dollhouseImg} style={{ transform: "scale(1.5)" }} />
        </SettingButton>
      </Box>
    );
  };

  const renderControl = () => {
    return (
      <Box flex justify="center" style={{ alignItems: "center" }}>
        <MovingControl
          onForward={onForward}
          onBack={onBack}
          onLeft={onLeft}
          onRight={onRight}
          onLeftRotate={onLeftRotate}
          onRightRotate={onRightRotate}
          onZoomIn={onZoomIn}
          onZoomOut={onZoomOut}
        />
      </Box>
    );
  };

  return (
    <Layer modal={false} position="top-right" plain>
      <Box
        flex
        gap="small"
        pad={{ right: "small", top: "small" }}
        justify="center"
      >
        {renderAction()}

        {/*{isWallOpen && (*/}
        {/*  <Box style={{ backgroundColor: "#ffffff", padding: "5px" }}>*/}
        {/*    <Text style={{ fontWeight: 400, fontSize: 12 }}>Wall Opacity</Text>*/}
        {/*    <RangeInput*/}
        {/*      onChange={(e) => {*/}
        {/*        changeWallOpacity(parseFloat(e.target.value));*/}
        {/*      }}*/}
        {/*      defaultValue={0.5}*/}
        {/*      min={0}*/}
        {/*      max={1.0}*/}
        {/*      step={0.01}*/}
        {/*    />*/}
        {/*  </Box>*/}
        {/*)}*/}

        {renderControl()}
      </Box>
    </Layer>
  );
};

export default TopControlPanel;
