import React, { useEffect, useState, useMemo, useRef } from "react";
import { Box, Button, Text, DropButton } from "grommet";
import chairModels from "src/constants/chair-models";

type Props = {
  setChairModel: (model: any) => void;
};

const TableChairModel = ({ setChairModel }: Props) => {
  const [isLoaded, setIsLoaded] = useState(false);

  // Load models in useEffect
  useEffect(() => {
    if ((window as any).isChairModelLoaded) {
      setIsLoaded(true);
    }
  }, [(window as any).isChairModelLoaded]);

  // Memoize the drop-content
  const dropContent = useMemo(() => {
    return (
      <Box pad="small" background="light-2">
        {chairModels.map((item) => (
          <Button
            key={item.model}
            label={item.label}
            onClick={() => {
              setChairModel(item.model)
            }}
          />
        ))}
      </Box>
    );
  }, [setChairModel]);

  return (
    <Box flex={{ shrink: 0 }} border={{ side: "top" }}>
      <Box fill align="left" justify="center">
        <DropButton
          label="Table Chair Model"
          dropAlign={{ top: "bottom", right: "right" }}
          dropContent={dropContent}
          disabled={!isLoaded}
        />
      </Box>
    </Box>
  );
};

export default TableChairModel;
