import React from "react";
import { Box, Button } from "grommet";

import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faLock, faLockOpen } from "@fortawesome/free-solid-svg-icons";

type Props = {
  lockScreen: boolean;
  toggleScreen: VoidFunction;
};

const ScreenLock = ({ lockScreen, toggleScreen }: Props) => {
  return (
    <Box style={{ position: "absolute", right: "5px", bottom: "5px" }}>
      <Button tip={"Lock Screen"} onClick={toggleScreen}>
        <Box
          round="full"
          style={{ width: "30px", height: "30px", alignItems: "center" }}
          background="#004069"
          justify="center"
        >
          <FontAwesomeIcon
            icon={lockScreen ? (faLock as any) : (faLockOpen as any)}
            color="#FFFFFF"
            size="sm"
          />
        </Box>
      </Button>
    </Box>
  );
};

export default ScreenLock;
