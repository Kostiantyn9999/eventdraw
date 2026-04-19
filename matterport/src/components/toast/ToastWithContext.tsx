import React from "react";

import { Box, Layer, Text, Button, Heading, Image } from "grommet";
// import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
// import { faTimes } from "@fortawesome/pro-regular-svg-icons";

// import colors from "shared/constants/colors";

import { useToastContext, REMOVE } from "./ToastContext";
import LogoImg from "assets/images/logo.png";

const Toast = ({ toast }) => {
  const { toastDispatch } = useToastContext();

  return (
    <Layer modal={false} position="top" style={{ background: "#ff000000" }}>
      <Box
        direction="column"
        justify="center"
        align="center"
        Text="center"
        flex
      >
        {_.map(toast, (t, index) => {
          setTimeout(() => {
            toastDispatch({ type: REMOVE, payload: { id: t.id } });
          }, 100000);

          return (
            <Box
              key={index}
              background="#ad849f"
              pad={{ horizontal: "medium", vertical: "small" }}
              align="center"
              round="small"
              margin={{ vertical: "small" }}
            >
              <Box direction="row" gap="small">
                <Box direction="column" align="center" justify="between">
                  <Text size="large" color="white" weight="bold">
                    {t.content.header}
                  </Text>
                  {/* <Button
                    icon={
                      <FontAwesomeIcon
                        icon={faTimes}
                        size="2x"
                        color={colors.WHITE}
                      />
                    }
                    hoverIndicator
                    onClick={() =>
                      toastDispatch({ type: REMOVE, payload: { id: t.id } })
                    }
                  /> */}
                  <Text size="medium" color="white" textAlign="center">
                    {t.content.message}
                  </Text>
                </Box>
                <Box
                  width="60px"
                  height="100%"
                  background="white"
                  pad="xsmall"
                  round="small"
                >
                  <Image src={LogoImg} fit="cover" />
                </Box>
              </Box>
            </Box>
          );
        })}
      </Box>
    </Layer>
  );
};

export default Toast;
