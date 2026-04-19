import React, { ChangeEvent, useContext, useRef } from "react";
import { Box, Button, Grid, Image } from "grommet";
import _ from "lodash";

import preArts from "src/constants/preArts";
import colors from "src/constants/color";
import { EventDrawContext } from "src/components/context";
import { MpSdk } from "bundle/sdk";
import { AppContext } from "src/AppContext";

type Props = {};

const ArtImporter = (props: Props) => {
  const imagePickerRef = useRef<HTMLInputElement | null>(null);
  const { data } = useContext(EventDrawContext);
  const appContext = useContext(AppContext);

  const createModel = () => {
    imagePickerRef.current?.click();
  };

  const onImageFilesPicked = (event: ChangeEvent<HTMLInputElement>) => {
    console.log(event.target.files);
  };

  const onCreateArt = async (mat: { path: string }) => {
    if (appContext.sdk && appContext.scene.spy) {
      const [sceneObject] = await appContext.sdk.sdk.Scene.createObjects(1);
      const node = sceneObject.addNode("node");
      let addedComponent: MpSdk.Scene.IComponent | null = node.addComponent(
        "mp.artImage",
        {
          url: mat.path,
        }
      );

      addedComponent.spyOnEvent(appContext.scene.spy);
      node.start();

      appContext.sdk.sdk.Pointer.intersection.subscribe((intersectionData) => {
        if (addedComponent && addedComponent.inputs) {
          addedComponent.inputs.x = intersectionData.position.x;
          addedComponent.inputs.y = intersectionData.position.y;
          addedComponent.inputs.z = intersectionData.position.z;
        }
      });

      window.addEventListener("blur", async () => {
        console.log(document.activeElement);
        if (document.activeElement === document.getElementById("sdk-iframe")) {
          addedComponent = null;
        }
      });
    }
  };

  const renderImages = () => {
    return _.map(preArts, (mat, index) => (
      <Box
        key={index}
        height={{ max: "50px" }}
        onClick={() => onCreateArt(mat)}
      >
        <Image fit="contain" src={mat.path} />
      </Box>
    ));
  };

  return (
    <Box pad={{ vertical: "small" }}>
      <Box pad={{ vertical: "small" }}>
        <Button
          label="Upload your Art Image"
          primary
          color={colors.AQUA_MARINE}
          onClick={createModel}
          style={{
            fontSize: "12px",
            color: "#888888",
            border: "solid 1px #888888",
            background: "transparent",
          }}
        />
        <input
          type="file"
          ref={imagePickerRef}
          style={{ display: "none" }}
          onChange={onImageFilesPicked}
          accept=".png"
        />
        <Box pad={{ vertical: "small" }}>
          <Grid
            columns={{
              count: 4,
              size: "auto",
            }}
          >
            {renderImages()}
          </Grid>
        </Box>
      </Box>
    </Box>
  );
};

export default ArtImporter;
