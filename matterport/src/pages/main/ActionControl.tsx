import React, { useState } from "react";
import { Box, Button, Layer } from "grommet";

import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faBorderAll,
  faEye,
  faStreetView,
} from "@fortawesome/free-solid-svg-icons";

import DropContent from "src/components/drop/DropContent";

const Mode = [
  "mode.dollhouse",
  "mode.floorplan",
  "mode.inside",
  "mode.outside",
  "mode.transitioning",
];
const ActionControl = ({
  modeChange,
}: {
  modeChange: (mode: string) => void;
}) => {
  const [floorSelectorOpened, setFloorSelectorOpened] = useState(false);

  const goInside = () => {
    modeChange("mode.inside");
  };

  const goDollHouse = () => {
    modeChange("mode.dollhouse");
  };

  const goFloorplan = () => {
    modeChange("mode.floorplan");
  };

  // const dropContent = (
  //   <DropContent
  //     title="Select Floor:"
  //     // selectedId={selectedSlot.kind}
  //     // options={options}
  //     // onOptionClick={onSelectTypeSlot}
  //   />
  // );

  return (
    <Layer modal={false} position="bottom-left" plain>
      <Box
        pad={{ bottom: "small", horizontal: "small" }}
        direction="row"
        gap="small"
        background="#0000055"
        align="center"
      >
        <Button tip={"View Tour"} onClick={goInside}>
          <Box
            round="full"
            style={{ width: "30px", height: "30px", alignItems: "center" }}
            background="#a4769699"
            justify="center"
          >
            <FontAwesomeIcon
              icon={faStreetView as any}
              color="#FFFFFF"
              size="sm"
            />
          </Box>
        </Button>
        <Button onClick={goDollHouse}>
          <Box
            round="full"
            style={{ width: "30px", height: "30px", alignItems: "center" }}
            background="#a4769699"
            justify="center"
          >
            <FontAwesomeIcon
              icon={faBorderAll as any}
              color="#FFFFFF"
              size="sm"
            />
          </Box>
        </Button>
        <Button onClick={goFloorplan}>
          <Box
            round="full"
            style={{ width: "30px", height: "30px", alignItems: "center" }}
            background="#a4769699"
            justify="center"
          >
            <FontAwesomeIcon icon={faEye as any} color="#FFFFFF" size="sm" />
          </Box>
        </Button>
        {/* <DropButton
          icon={<FontAwesomeIcon icon={faEye} color="#FFFFFF" />}
          dropAlign={{ top: "bottom" }}
          dropContent={dropContent}
          open={floorSelectorOpened}
          onOpen={() => setFloorSelectorOpened(true)}
          onClose={() => setFloorSelectorOpened(false)}
        /> */}
      </Box>
    </Layer>
  );
};

ActionControl.propTypes = {};

export default ActionControl;
