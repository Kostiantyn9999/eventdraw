import React from "react";
import { Box, Layer } from "grommet";

import DirectionControl from "src/components/control";

const MoveControl = ({}) => {
  return (
    <Layer modal={false} position="bottom-right" plain>
      <Box
        pad={{ bottom: "small", horizontal: "small" }}
        direction="row"
        gap="medium"
      >
        <DirectionControl />
        <DirectionControl />
      </Box>
    </Layer>
  );
};

export default MoveControl;
