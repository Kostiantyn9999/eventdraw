import React from "react";
import { Box, Grid } from "grommet";

type IColor = { r: number; g: number; b: number };

type Props = {
  precolors: Array<string>;
  onSelect: (v: string) => void;
};

const PreColorPane = ({ precolors, onSelect }: Props) => {
  const renderColorPans = () => {
    return precolors.map((color, i) => {
      return (
        <Box
          background={color}
          key={i}
          onClick={() => onSelect(color)}
          style={{ width: "40px", height: "40px" }}
        />
      );
    });
  };

  return (
    <Grid
      columns={{
        count: 4,
        size: "auto",
      }}
      gap="small"
      style={{ padding: "12px 24px" }}
      // border={{ side: "bottom" }}
    >
      {renderColorPans()}
    </Grid>
  );
};

export default PreColorPane;
