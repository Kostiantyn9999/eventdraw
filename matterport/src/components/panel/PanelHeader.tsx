import React from "react";
import { Box, Button } from "grommet";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faTimes } from "@fortawesome/free-solid-svg-icons";

type Props = {
  children: JSX.Element | JSX.Element[];
  onClose?: () => void;
};

const PanelHeader = ({ children, onClose }: Props) => {
  return (
    <Box
      // border="bottom"
      justify="center"
      align="center"
      pad={{ vertical: "small", horizontal: "small" }}
      height={{ min: "50px" }}
      flex={{ shrink: 0 }}
      direction="row"
      style={{ position: "relative" }}
    >
      {children}
      {onClose && (
        <Box
          style={{
            position: "absolute",
            right: "15px",
            bottom: "15px",
            padding: "5px",
            background: "#d0d0d0",
            borderRadius: "6px",
          }}
        >
          <Button
            icon={<FontAwesomeIcon icon={faTimes as any} size="1x" />}
            onClick={onClose}
            style={{ width: "11px", height: "12px", padding: 0, margin: 0 }}
          />
        </Box>
      )}
    </Box>
  );
};

export default PanelHeader;
