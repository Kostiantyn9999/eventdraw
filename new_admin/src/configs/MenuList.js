import React from "react";
import DashboardTwoToneIcon from "@mui/icons-material/DashboardTwoTone";
import ShapeIcon from "@mui/icons-material/HexagonOutlined";
import RealisticIcon from "@mui/icons-material/FormatShapesOutlined";
import MatterportIcon from "@mui/icons-material/ViewInArOutlined";
import UserIcon from "@mui/icons-material/PersonOutlineOutlined";

const iconSize = "small"; // small,medium,large,string

const MenuLists = [
  {
    name: "Dashboard",
    path: `${process.env.PUBLIC_URL}/dashboard`,
    icon: <DashboardTwoToneIcon fontSize={iconSize} />,
    available: true,
  },
  {
    name: "Shape",
    path: `${process.env.PUBLIC_URL}/shape`,
    icon: <ShapeIcon fontSize={iconSize} />,
    available: true,
  },
  {
    name: "Realistic",
    path: `${process.env.PUBLIC_URL}/realistic`,
    icon: <RealisticIcon fontSize={iconSize} />,
    available: true,
  },
  {
    name: "Matterport",
    path: `${process.env.PUBLIC_URL}/matterport`,
    icon: <MatterportIcon fontSize={iconSize} />,
    available: true,
  },
  {
    name: "User",
    path: `${process.env.PUBLIC_URL}/user`,
    icon: <UserIcon fontSize={iconSize} />,
    available: true,
  },
];

export default MenuLists;
