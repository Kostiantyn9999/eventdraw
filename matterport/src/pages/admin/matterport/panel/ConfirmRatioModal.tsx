import React from "react";
import { Box, Button, Layer, Text } from "grommet";

import { create } from "react-modal-promise";

type Props = {
  isOpen: boolean;
  onResolve: () => void;
  onReject: () => void;
};

export const ConfirmRatioModal = ({ isOpen, onResolve, onReject }: Props) => {
  return (
    <Layer
      position="center"
      onClickOutside={onReject}
      onEsc={onReject}
      // isOpen={isOpen}
    >
      <Box pad="20px">
        <Box>
          <Text weight={500}>Dimension will be change due to Image</Text>
        </Box>
        <Box
          flex
          direction="row"
          justify="end"
          pad={{ top: "10px" }}
          gap="10px"
        >
          <Button secondary label="Cancel" onClick={onReject} />
          <Button primary label="Okay" onClick={onResolve} />
        </Box>
      </Box>
    </Layer>
  );
};

export const myPromiseConfirmModal = create(ConfirmRatioModal);

// export default myPromiseConfirmModal;
