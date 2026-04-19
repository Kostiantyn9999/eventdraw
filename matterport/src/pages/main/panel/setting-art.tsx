import React from "react";
import { Box, Text } from "grommet";
import PanelHeader from "src/components/panel/PanelHeader";

import ArtImporter from "./ArtImporter";
import VideoImporter from "./VideoImporter";

type Props = {
  onClose: () => void;
};

const SettingArt = ({ onClose }: Props) => {
  return (
    <Box fill>
      <PanelHeader onClose={onClose}>
        <Text
          weight={500}
          style={{
            textTransform: "uppercase",
            color: "#25302e",
            fontSize: "15px",
            fontWeight: 600,
          }}
        >
          Art Control
        </Text>
      </PanelHeader>
      <Box flex pad="small">
        <ArtImporter />
        <VideoImporter />
      </Box>
    </Box>
  );
};

export default SettingArt;
