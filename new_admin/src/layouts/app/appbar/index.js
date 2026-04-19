import React, { useMemo } from "react";
import { Link } from "react-router-dom";
import * as Mui from "@mui/material";
import MenuIcon from "@mui/icons-material/Menu";
import { appBarConfig } from "configs/LayoutConfig";
import theme from "configs/Theme";

const Header = Mui.styled(Mui.AppBar)(({ theme }) => ({
  height: appBarConfig.height,
  flexDirection: "row",
  alignItems: "center",
  padding: "0 2.5rem",
  borderBottom: "1px solid #f5f5f5",
  boxShadow: "none",
  backgroundColor: Mui.alpha(theme.palette.body.light, 0.8),
  backdropFilter: "blur(6px)",
}));

const AppBar = (props) => {
  const { handleDrawerCollapse } = props;

  const user = useMemo(() => {
    return JSON.parse(localStorage.getItem("currentUser"));
  }, [localStorage.getItem("currentUser")]);

  return (
    <Header position={appBarConfig.style}>
      <Mui.IconButton
        onClick={handleDrawerCollapse}
        edge="start"
        sx={{
          display: { xl: "none" },
          marginRight: 5,
        }}
      >
        <MenuIcon />
      </Mui.IconButton>

      <Mui.Box display="flex" gap={1} ml="auto" sx={{ color: theme.palette.body.dark }}>
        {/* <Mui.MenuItem component={Link} to={`${process.env.PUBLIC_URL}/profile`}>
          Profile
        </Mui.MenuItem> */}
        <Mui.MenuItem component={Link} to={`${process.env.PUBLIC_URL}/logout`}>
          Logout
        </Mui.MenuItem>
        <Mui.Avatar
          src={user?.name}
          alt={user?.name}
          size="small"
          sx={{ width: 32, height: 32, bgcolor: "#038C4C", "& img": { objectFit: "contain" } }}
        />
      </Mui.Box>
    </Header>
  );
};

export default AppBar;
