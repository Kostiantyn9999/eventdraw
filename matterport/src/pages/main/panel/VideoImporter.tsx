import React, { ChangeEvent, useContext, useRef } from "react";
import { Box, Button, Grid, Image } from "grommet";
import colors from "src/constants/color";
import { EventDrawContext } from "src/components/context";
import _ from "lodash";
import preVideos from "src/constants/preVideos";
import { AppContext } from "src/AppContext";
import { MpSdk } from "bundle/sdk";

type Props = {};

const VideoImporter = (props: Props) => {
  const { data } = useContext(EventDrawContext);
  const appContext = useContext(AppContext);

  const imagePickerRef = useRef<HTMLInputElement | null>(null);

  const createVideo = () => {
    imagePickerRef.current?.click();
  };

  const onVideoFilesPicked = (event: ChangeEvent<HTMLInputElement>) => {};

  const onCreateVideo = async (mat: { path: string; thumb: string }) => {
    if (appContext.sdk && appContext.scene.spy) {
      const [sceneObject] = await appContext.sdk.sdk.Scene.createObjects(1);
      const node = sceneObject.addNode("node");
      let addedComponent: MpSdk.Scene.IComponent | null = node.addComponent(
        "mp.artVideo",
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

  const renderVideos = () => {
    return _.map(preVideos, (mat, index) => (
      <Box
        key={index}
        height={{ max: "50px" }}
        onClick={() => onCreateVideo(mat)}
      >
        <Image fit="contain" src={mat.thumb} />
      </Box>
    ));
  };

  return (
    <Box pad={{ vertical: "small" }}>
      {/* <Box pad="small" background={colors.PALE_GREY}>
        <Text color={colors.LIGHT_NAVY_BRIGHT} weight={600}>
          Video
        </Text>
      </Box> */}
      <Box pad={{ vertical: "small" }}>
        <Button
          label="Upload Image"
          primary
          color={colors.AQUA_MARINE}
          onClick={createVideo}
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
          onChange={onVideoFilesPicked}
          accept=".mp4"
        />
        <Box pad={{ vertical: "small" }}>
          <Grid
            columns={{
              count: 4,
              size: "auto",
            }}
          >
            {renderVideos()}
          </Grid>
        </Box>
      </Box>
    </Box>
  );
};

export default VideoImporter;
