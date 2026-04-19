import React from "react";
import { Box, Text } from "grommet";

import ResetButton from "./ResetButton";
import PreColorPane from "src/components/PreColorPane";
import ColorPickerPane from "src/components/ColorPickerPane";

import preColors from "src/constants/pre-colors";

type Props = {
  title: string;
  color: string;
  setShapeColor: (color: string) => void;
  align: any;
  side?: string;
};

const ColorPane = ({
  color,
  setShapeColor,
  title,
  side = "top",
  align = { left: "right" },
}: Props) => {
  return (
    <Box flex={{ shrink: 0 }} border={{ side: side }}>
      <Box style={{ padding: "12px" }}>
        <Text
          style={{
            fontSize: "12px",
            fontFamily: "Open Sans",
            fontWeight: 700,
            color: "rgb(112, 112, 112)",
          }}
        >
          {title}
        </Text>
      </Box>
      <Box>
        <PreColorPane precolors={preColors} onSelect={setShapeColor} />
        <ColorPickerPane color={color} onChange={setShapeColor} align={align} />
        <ResetButton />
      </Box>
    </Box>
  );
};

export default ColorPane;
