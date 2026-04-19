import React from "react";
import { Box, Button, Text } from "grommet";

type Props = {};

const ResetButton = (props: Props) => {
  return (
    <Box
      // border={{ side: "bottom" }}
      flex={{ shrink: 0 }}
      style={{
        display: "flex",
        alignItems: "flex-end",
        padding: "10px",
      }}
    >
      <Button
        style={{
          background: "#a8518a",
          backgroundImage: "linear-gradient(#a8518a 0px, #a8518a 100%)",
          borderRadius: "4px",
          height: "29px",
          width: "100px",
        }}
      >
        <Box flex style={{ alignItems: "center", justifyContent: "center" }}>
          <Text color="white" style={{ fontSize: "11px" }}>
            Reset
          </Text>
        </Box>
      </Button>
    </Box>
  );
};

export default ResetButton;
