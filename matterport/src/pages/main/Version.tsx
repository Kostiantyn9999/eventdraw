import * as React from "react";
import { Box } from "grommet";
import { PROJECT_VERSION } from "src/global-config";

type Props = {};

export const Version = (props: Props) => {
  return (
    <Box
      style={{
        position: "absolute",
        right: "20px",
        bottom: "20px",
        fontSize: "20px",
        fontWeight: "bold",
        color: "white",
        letterSpacing: "1px",
      }}
    >
      {PROJECT_VERSION}
    </Box>
  );
};
