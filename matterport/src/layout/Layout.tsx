import React, { useEffect } from "react";
import { ContextProviderComponent as ContextProvider } from "src/components/context";

import AOS from "aos";
import "aos/dist/aos.css";

import { Box, Main } from "grommet";
import NavbarContainer from "src/components/NavbarContainer";
import NavBar from "./NavBar";

import GlobalStyles from "assets/globalStyles";
import "assets/globalStyles/reset.css";

const Layout = ({ children }: { children: React.ReactNode }) => {
  useEffect(() => {
    AOS.init({
      once: true,
    });
  }, []);

  return (
    <ContextProvider>
      <div
        data-aos="fade-in"
        data-aos-duration="600"
        style={{ width: "100%", display: "flex", height: "100%" }}
      >
        <GlobalStyles />
        <Main direction="row" pad="none">
          <NavbarContainer pad="large">
            <NavBar />
          </NavbarContainer>
          <Box width="full" alignSelf="stretch" flex>
            {children}
          </Box>
        </Main>
      </div>
    </ContextProvider>
  );
};

export default Layout;
