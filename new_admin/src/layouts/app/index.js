import React, { useEffect } from "react";
import { Outlet } from "react-router-dom";
import * as Mui from "@mui/material";
// import { useSelector } from "react-redux";
import useWidth from "hooks/useWidth";
import { sideNavConfig } from "configs/LayoutConfig";
import Sidenav from "./sidenav";
import AppBar from "./appbar";

const Layout = () => {
  const theme = Mui.useTheme();
  const width = useWidth();

  // const { user } = useSelector((state) => state.auth);

  const [open, setOpen] = React.useState(Mui.useMediaQuery(theme.breakpoints.down(sideNavConfig.collapseStateBelow)));

  const handleDrawerState = () => {
    setOpen(!open);
  };

  const handleCloseDrawer = () => {
    setOpen(false);
  };

  useEffect(() => {
    if (width !== sideNavConfig.collapseStateBelow) {
      setOpen(false);
    } else {
      setOpen(true);
    }
  }, [width]);

  return (
    <Mui.Box display="flex">
      <Sidenav
        drawerOpen={open}
        handleOnCloseDrawer={handleCloseDrawer}
        handleDrawerCollapse={handleDrawerState}
      />

      <Mui.Box
        component="main"
        sx={{
          maxWidth: {
            xl: open
              ? `calc(100% - ${sideNavConfig.drawerWidth.large}px)`
              : `calc(100% - ${sideNavConfig.drawerWidth.small}px)`,
          },
          width: "100%",
          height: "100vh",
          flex: 1,
          ml: "auto",
        }}
      >
        <AppBar handleDrawerCollapse={handleDrawerState} />

        <Mui.Container maxWidth="xl" sx={{ pt: 4, pb: 8 }}>
          <Outlet />
        </Mui.Container>
      </Mui.Box>
    </Mui.Box>
  );
};

export default Layout;
