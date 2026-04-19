import React from "react";
import { Box } from "grommet";
import Frame from "src/components/matterport/Frame";

type Props = {
  matterId: string;
};

const MatterportFrame = ({ matterId }: Props) => {
  return <Box fill>{matterId && <Frame src={matterId} />}</Box>;
};

export default MatterportFrame;
