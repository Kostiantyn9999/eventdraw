import React from "react";
import { Box, Text } from "grommet";

type Props = {
  title: string;
  children: JSX.Element | JSX.Element[];
};

const RowWithTitle = ({ title, children, ...rest }: Props) => (
  <Box gap="xsmall" direction="row" align="center" {...rest}>
    <Box width="150px">
      <Text
        style={{
          fontSize: "12px",
          fontFamily: "Open Sans",
          fontWeight: "bold",
          color: "rgb(112, 112, 112)",
        }}
      >
        {title}
      </Text>
    </Box>
    {children}
  </Box>
);

export default RowWithTitle;
