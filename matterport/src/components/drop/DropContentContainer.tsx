import React from "react";
import { Box, BoxProps } from "grommet";

type Props = {
  children: React.ReactNode;
  boxProps: BoxProps;
};

const DropContentContainer = ({
  children,
  boxProps,
  ...rest
}: Props & BoxProps) => {
  return (
    <Box width="medium" {...boxProps} {...rest}>
      {children}
    </Box>
  );
};

export default DropContentContainer;
