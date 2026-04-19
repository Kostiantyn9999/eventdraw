import React from "react";
import * as Mui from "@mui/material";
import LockIcon from "@mui/icons-material/LockOutlined";
import LockOpenIcon from "@mui/icons-material/LockOpenOutlined";

const ScreenLock = ({ lockScreen, toggleScreen }) => {
	return (
		<Mui.Box sx={{ position: "absolute", right: "-12px", bottom: "-15px" }}>
			{lockScreen ? <LockIcon onClick={toggleScreen} sx={{ color: "#E36049", cursor: "pointer" }} /> : <LockOpenIcon sx={{ color: "#038C4C", cursor: "pointer" }} onClick={toggleScreen} />}
		</Mui.Box>
	);
};

export default ScreenLock;
