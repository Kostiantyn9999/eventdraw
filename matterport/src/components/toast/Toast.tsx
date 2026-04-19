import React from "react";
import { connect } from "react-redux";
import { bindActionCreators, Dispatch } from "redux";
import { Box, Image, Layer, Text } from "grommet";
import _ from "lodash";

import { removeToast } from "models/actions/toastAction";

import LogoImg from "assets/images/logo.png";
import { RootState } from "src/models/store";

type Props = {};

const Toast = ({
  toast,
  removeToast,
}: Props &
  ReturnType<typeof mapStateToProps> &
  ReturnType<typeof mapDispatchToProps>) => {
  return (
    <Layer modal={false} position="top" style={{ background: "#ff000000" }}>
      <Box
        direction="column"
        justify="center"
        align="center"
        // Text="center"
        flex
      >
        {_.map(toast, (t, index) => {
          setTimeout(() => {
            // toastDispatch({ type: REMOVE, payload: { id: t.id } });
            removeToast(t.id);
          }, 100000);

          return (
            <Box
              key={index}
              background="#ad849f"
              pad={{ horizontal: "medium", vertical: "small" }}
              align="center"
              round="small"
              margin={{ vertical: "small" }}
              onClick={() => removeToast(t.id)}
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

const mapStateToProps = (state: RootState) => {
  return {
    toast: state.toast,
  };
};

const mapDispatchToProps = (dispatch: Dispatch) => {
  return bindActionCreators(
    {
      removeToast,
    },
    dispatch
  );
};

export default connect(mapStateToProps, mapDispatchToProps)(Toast);
