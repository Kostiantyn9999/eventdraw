import React from "react";
import { Box } from "grommet";

type Props = {
  src: string;
};

const Frame = ({ src }: Props) => {
  const createMatterportUrl = (matterId: string): string => {
    return `../bundle/showcase.html?m=${matterId}&applicationKey=${process.env.MATTERPORT_APPLICATION_KEY}&qs=1&hr=0&play=1&wts=1`;
  };

  const removeElement = () => {
    const i = setInterval(() => {
      let frameElement = document.getElementById(
        "sdk-iframe"
      ) as HTMLIFrameElement;
      if (frameElement && frameElement.contentWindow) {
        const ele = frameElement.contentWindow.document.getElementById("gui");
        if (ele) {
          ele.style.display = "none";
          clearInterval(i);
        }
      }
    }, 200);
  };

  return (
    <Box fill>
      <iframe
        id="sdk-iframe"
        src={
          createMatterportUrl(src) + "&title=0&qs=1&hr=0&brand=0&help=0&log=0"
        }
        frameBorder="0"
        style={{ width: "100%", height: "100%" }}
        onLoad={removeElement}
      />
      <Box id="sdk-iframe-overlay" style={{ position: "absolute" }} width={"100%"} height={"100%"}></Box>
    </Box>
  );
};

export default Frame;
