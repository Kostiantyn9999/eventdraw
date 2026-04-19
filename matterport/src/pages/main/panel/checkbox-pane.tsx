import React from "react";
import { Box, Text } from "grommet";

type Props = {
  checked: boolean;
  onChange: (checked: boolean) => void;
  label: string;
  border:
    | "top"
    | "left"
    | "bottom"
    | "right"
    | "start"
    | "end"
    | "horizontal"
    | "vertical"
    | "all"
    | "between";
};

const CheckboxPane = ({ label, border, checked, onChange }: Props) => {
  return (
    <Box
      flex={{ shrink: 0 }}
      direction="row"
      pad={{ horizontal: "12px", vertical: "12px" }}
      border={{ side: border }}
      gap="3px"
    >
      <Box flex={{ shrink: 0 }} alignContent="center" justify="center">
        <input
          type="checkbox"
          checked={checked}
          onChange={(e) => onChange(e.target.checked)}
        />
      </Box>
      <Box>
        <Text
          style={{
            fontSize: "0.9rem",
            fontFamily: "Open Sans",
            color: "rgb(112, 112, 112)",
          }}
        >
          {label}
        </Text>
      </Box>
    </Box>
  );
};

export default CheckboxPane;
