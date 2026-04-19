import React, { useMemo } from "react";
import * as Mui from "@mui/material";
import { Link } from "react-router-dom";
import PopoverDropdown from "ui/PopoverDropdown";

const UserDropdown = () => {
  const theme = Mui.useTheme();

  const user = useMemo(() => {
    return JSON.parse(localStorage.getItem("currentUser"));
  }, [localStorage.getItem("currentUser")]);

  return (
    <PopoverDropdown
      trigger={
        <Mui.Avatar
          src={"DefaultUserAatar"}
          alt="Amit Rajbhandari"
          size="small"
          sx={{ width: 32, height: 32, bgcolor: "#DFE2E7", "& img": { objectFit: "contain" } }}
        />
      }
    >
      <Mui.Typography
        component="span"
        variant="subtitle2"
        sx={{
          display: "block",
          mt: "-0.5rem",
          p: "0.75rem 1.25rem",
          color: "#000000",
        }}
      >
        {`${user?.firstname} ${user?.lastname}` || ""}
        <Mui.Typography component="small" variant="body2" display="block" color={theme.palette.grey.dark}>
          {user?.email || ""}
        </Mui.Typography>
      </Mui.Typography>

      <Mui.Divider sx={{ mb: "0.5rem", borderBottom: "thin dashed rgba(145, 158, 171, 0.24)" }} />

      <Mui.MenuItem component={Link} to={`${process.env.PUBLIC_URL}/profile`}>
        Profile
      </Mui.MenuItem>

      <Mui.Divider sx={{ mt: "0.5rem", borderBottom: "thin dashed rgba(145, 158, 171, 0.24)" }} />

      <Mui.MenuItem component={Link} to={`${process.env.PUBLIC_URL}/logout`}>
        Logout
      </Mui.MenuItem>
    </PopoverDropdown>
  );
};

export default UserDropdown;
