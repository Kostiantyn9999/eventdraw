import React from "react";
import { Box, Image } from "grommet";
import LogoImg from "assets/images/logo.png";

const NavBar = ({ pad }: { pad?: string }) => {
  return (
    <Box
      direction="row"
      align="center"
      justify="between"
      fill="horizontal"
      pad={pad}
    >
      <Box direction="row" height="70px" width="70px">
        <Image src={LogoImg} fit="contain" />
      </Box>
      <Box direction="row" gap="xsmall"></Box>
    </Box>
  );
};

export default NavBar;
