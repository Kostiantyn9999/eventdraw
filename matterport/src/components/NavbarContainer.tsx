import React from "react";
import { Box, BoxProps } from "grommet";

type Props = {
  children: JSX.Element | JSX.Element[];
};

const NavbarContainer = ({ children, ...props }: Props & BoxProps) => {
  return (
    <Box
      flex={false}
      direction="row"
      justify="between"
      align="center"
      height={{ min: "60px", max: "60px" }}
      style={{ position: "absolute", zIndex: "100" }}
      {...props}
    >
      {children}
    </Box>
  );
};

export default NavbarContainer;
