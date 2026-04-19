import React, { useState } from "react";
import { Box, Button, Image, Layer, Text } from "grommet";
import { ReactComponent as ShapeIcon } from "assets/images/ectable_white.svg";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faEye } from "@fortawesome/free-solid-svg-icons";
import wallImg from "assets/images/wall_setting.svg";

const SettingButton = ({
  tip,
  onClick,
  children,
}: {
  tip: string;
  onClick: () => void;
  children: JSX.Element | JSX.Element[];
}) => {
  return (
    <Button
      tip={{
        plain: true,
        dropProps: { align: { right: "left" } },
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
        width="50px"
        height="50px"
        // background="#000000"
        background="grey"
        round="small"
        pad="xsmall"
        style={{
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
        }}
      >
        {children}
      </Box>
    </Button>
  );
};

type Props = {
  openArt: () => void;
  openShape: () => void;
  openWall: () => void;
  toggleTransparent: (v: boolean) => void;
};

const RightControlPanel = ({
  openArt,
  openShape,
  openWall,
  toggleTransparent,
}: Props) => {
  const [toggle, setToggle] = useState(true);

  const clickTransparent = () => {
    toggleTransparent(!toggle);
    setToggle(!toggle);
  };

  return (
    <Layer modal={false} plain position="right">
      <Box pad={{ right: "small" }}>
        <Box direction="column" align="center" gap="xsmall">
          <SettingButton tip="Transparent" onClick={clickTransparent}>
            <FontAwesomeIcon
              icon={faEye as any}
              style={{ width: "28px", height: "28px" }}
              color="#ffffff"
            />
          </SettingButton>
          <SettingButton tip="Shapes" onClick={openShape}>
            <ShapeIcon viewBox="0 0 28 28" width="auto" height="auto" />
          </SettingButton>

          <SettingButton tip="Shapes" onClick={openWall}>
            <Image src={wallImg} style={{ transform: "scale(0.6)" }} />
          </SettingButton>
        </Box>
      </Box>
    </Layer>
  );
};

export default RightControlPanel;
