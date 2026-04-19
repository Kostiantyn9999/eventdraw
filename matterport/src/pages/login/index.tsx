import React, { useState } from "react";
import { connect } from "react-redux";

import { Box, Button, Main, Text } from "grommet";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faKey, faUser } from "@fortawesome/free-solid-svg-icons";

import BG from "assets/images/BG.svg";
import IconInputText from "./IconInputText";

import { sendLogin } from "models/actions/authAction";
import { bindActionCreators, Dispatch } from "redux";

type PageProps = ReturnType<typeof mapStateToProps> &
  ReturnType<typeof mapDispatchToProps>;

const Login = ({ sendLogin }: PageProps) => {
  const [user, setUser] = useState({
    email: "sorinwebdev@outlook.com",
    password: "Test",
    // email: "",
    // password: "",
  });

  const submitLogin = () => {
    sendLogin(user.email, user.password);
  };

  return (
    <Main direction="column" pad="0px" background="#2148C0">
      <Box
        fill
        justify="center"
        style={{
          background: `url(${BG})`,
          backgroundSize: "cover",
          backgroundPosition: "center",
          alignItems: "center",
        }}
      >
        <Box gap="small">
          <IconInputText
            type="text"
            icon={<FontAwesomeIcon icon={faUser as any} />}
            placeholder="Email"
            value={user.email}
            onChange={(e) => setUser({ ...user, email: e.target.value })}
          />
          <IconInputText
            type="password"
            icon={<FontAwesomeIcon icon={faKey as any} />}
            placeholder="Password"
            value={user.password}
            onChange={(e) => setUser({ ...user, password: e.target.value })}
          />
          <Button onClick={submitLogin}>
            <Box
              align="center"
              background="white"
              pad={{ vertical: "small" }}
              round="xsmall"
            >
              <Text size="small" weight="bold" color="#2148C0">
                Enter System
              </Text>
            </Box>
          </Button>
        </Box>
      </Box>
    </Main>
  );
};

const mapStateToProps = () => ({});

const mapDispatchToProps = (dispatch: Dispatch) => {
  return bindActionCreators(
    {
      sendLogin,
    },
    dispatch
  );
};

export default connect(mapStateToProps, mapDispatchToProps)(Login);
