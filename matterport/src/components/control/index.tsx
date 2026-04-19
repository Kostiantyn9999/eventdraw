import React, { useState } from "react";
import { Box, Button } from "grommet";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faChevronDown,
  faChevronLeft,
  faChevronRight,
  faChevronUp,
} from "@fortawesome/free-solid-svg-icons";

const DirectionButton = ({ icon }: { icon: any }) => {
  const [hover, setHover] = useState(false);
  return (
    <Button
      size="small"
      onMouseOver={() => setHover(true)}
      onMouseLeave={() => setHover(false)}
      plain
      icon={
        <FontAwesomeIcon
          icon={icon}
          size={"2x"}
          color={hover ? "#cf6ea9" : "#ffffff"}
        />
      }
    />
  );
};

const DirectionControl = ({}) => {
  return (
    <Box
      direction="column"
      style={{ alignItems: "center" }}
      background="#a4769699"
      plain
      round="full"
      pad={{ horizontal: "xxsmall" }}
    >
      <DirectionButton icon={faChevronUp} />
      <Box direction="row">
        <Box pad={{ right: "medium" }}>
          <DirectionButton icon={faChevronLeft} />
        </Box>
        <Box pad={{ left: "medium" }}>
          <DirectionButton icon={faChevronRight} />
        </Box>
      </Box>
      <DirectionButton icon={faChevronDown} />
    </Box>
  );
};

export default DirectionControl;
