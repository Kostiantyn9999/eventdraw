import React from "react";
import * as Mui from "@mui/material";
import { StyledButton } from "ui";
import { Mode } from "configs/constants";
import { roundToNearestTarget } from "helpers/util";

/* eslint-disable-next-line */
const ViewSwitch = ({ sdk, startPosition }) => {

	const goFloorPlan = async () => {
		if (sdk) {
			console.log("startPosition", startPosition.rotation.y, roundToNearestTarget(startPosition.rotation.y));
			
			// const rotation = {x: -90, y: roundToNearestTarget(startPosition.rotation.y)};
			// const transition = sdk.Mode.TransitionType.FLY;
			await sdk.Mode.moveTo(Mode.FLOORPLAN, {
				// transition: transition,
				// rotation: rotation,
			});
		}
	};

	const goInside = async () => {
		if (sdk) await sdk.Mode.moveTo(Mode.INSIDE);
	};

	return (
		startPosition ? 
			<Mui.Box
				width="100%"
				display="flex"
				flexDirection={{ xs: "column", sm: "row" }}
				justifyContent={{ sm: "flex-start" }}
				gap={2}
				mt={5}
			>
				<StyledButton onClick={goInside} >
					Moving
				</StyledButton>
				<StyledButton onClick={goFloorPlan} >
					FloorPlan
				</StyledButton>
			</Mui.Box>
		: <></>
	);
};

export default ViewSwitch;
