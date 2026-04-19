import React, { useState } from "react";
import { Box, Text } from "grommet";
import { Mode } from ".";
import { MpSdk } from "bundle/sdk";

type Props = {
  sdk: MpSdk | null;
};

const ViewSwitch = ({ sdk }: Props) => {
  const [viewMode, setViewMode] = useState<Mode>(Mode.INSIDE);

  const goFloorPlan = async () => {
    if (sdk) await sdk.Mode.moveTo(Mode.FLOORPLAN);
    // appContext.sdk.sdk.Mode.moveTo(appContext.sdk.sdk.Mode.Mode.FLOORPLAN);
  };

  const goInside = async () => {
    if (sdk) await sdk.Mode.moveTo(Mode.INSIDE);
    // appContext.sdk.sdk.Mode.moveTo(appContext.sdk.sdk.Mode.Mode.INSIDE);
  };

  return (
    <Box style={{ position: "absolute", left: "5px", bottom: "5px" }}>
      <Box flex direction="row">
        <Box
          pad={{ vertical: "6px", horizontal: "35px" }}
          background="#ecf2fa"
          onClick={goInside}
        >
          <Text size="small" color="#004069" weight={700}>
            Moving
          </Text>
        </Box>
        <Box
          pad={{ vertical: "6px", horizontal: "35px" }}
          background="white"
          onClick={goFloorPlan}
        >
          <Text size="small" color="#004069" weight={700}>
            FloorPlan
          </Text>
        </Box>
      </Box>
    </Box>
  );
};

export default ViewSwitch;
