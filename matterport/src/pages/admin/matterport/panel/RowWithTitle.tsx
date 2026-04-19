import React from "react";
import { Box, Text } from "grommet";

type Props = {
  children: JSX.Element | JSX.Element[];
  title: string;
};

const RowWithTitle = ({ title, children, ...rest }: Props) => (
  <Box gap="xsmall" direction="row" align="center" {...rest}>
    <Box width="120px" style={{ minWidth: "120px" }}>
      <Text size="small" weight={600}>
        {title}:
      </Text>
    </Box>
    {children}
  </Box>
);

export default RowWithTitle;
