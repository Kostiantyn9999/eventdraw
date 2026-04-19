import React from "react";
import * as Mui from "@mui/material";

const Frame = ({ src }) => {
    const createMatterportUrl = (matterId) => {
        return `${process.env.PUBLIC_URL}/bundle/showcase.html?m=${matterId}&applicationKey=${process.env.REACT_APP_MAT_APP_KEY}&qs=1&hr=0&play=1&wts=1`;
    };

    const removeElement = () => {
        const i = setInterval(() => {
            const frameElement = document.getElementById("sdk-iframe");
            if (frameElement && frameElement.contentWindow) {
                const ele = frameElement.contentWindow.document.getElementById("gui");
                try {
                    frameElement.contentWindow.document.getElementsByClassName("footer-ui").item(0).remove();
                } catch (error) {}
                if (ele) {
                    // ele.style.display = "none";
                    clearInterval(i);
                }
            }
        }, 200);
    };

    return (
        <Mui.Box sx={{ width: "100%", height: "100%" }}>
            <iframe
                id="sdk-iframe"
                src={createMatterportUrl(src) + "&title=0&qs=1&hr=0&brand=0&help=0&log=0"}
                allowFullScreen
                frameBorder={0}
                width={"100%"}
                height={"100%"}
                onLoad={removeElement}
            />
        </Mui.Box>
    );
};

export default Frame;
