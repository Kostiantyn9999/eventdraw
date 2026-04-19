import React from "react";
import { Box, Grid, Text } from "grommet";
import { BorderType } from "grommet/utils";

import preTextures from "src/constants/pre-textures";

type Props = {
  title: string;
  border?: BorderType;
  onSelectTexture: (data: { url: string; type: string }) => void;
};

export default function TexturePane({
  title,
  border = { side: "bottom" },
  onSelectTexture,
}: Props) {
  const renderPanes = (
    <>
      {preTextures.map((t, i) => (
        <Box
          key={i}
          background={t.url}
          style={{
            width: "40px",
            height: "40px",
            backgroundImage: `url(${t.url})`,
            backgroundSize: "contain",
          }}
          onClick={() => onSelectTexture(t)}
        />
      ))}
    </>
  );

  return (
    <Box flex={{ shrink: 0 }} border={border}>
      <Box style={{ padding: "12px" }}>
        <Text
          style={{
            fontSize: "12px",
            fontFamily: "Open Sans",
            fontWeight: 700,
            color: "rgb(112, 112, 112)",
          }}
        >
          {title}
        </Text>
      </Box>

      <Box>
        <Grid
          columns={{
            count: 4,
            size: "auto",
          }}
          gap="small"
          style={{ padding: "12px 24px" }}
        >
          {renderPanes}
        </Grid>
      </Box>
    </Box>
  );
}
