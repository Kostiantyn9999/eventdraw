import React from "react";
import { Box } from "grommet";

const Frame = ({ src }: { src: string }) => {
  const removeElement = () => {
    const i = setInterval(() => {
      let frameElement = document.getElementById(
        "sdk-iframe"
      ) as HTMLIFrameElement;
      if (frameElement) {
        const ele =
          frameElement.contentWindow.document.getElementById("bottom-ui");
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
        src={src + "&title=0&qs=1&hr=0&brand=0&help=0"}
        frameBorder="0"
        style={{ width: "100%", height: "100%" }}
        onLoad={removeElement}
      />
    </Box>
  );
};

export default Frame;
