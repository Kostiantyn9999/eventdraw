import React from "react";
import styled from "styled-components";
import { render } from "react-dom";
import { IContext } from "./interfaces";
import { makeScene } from "./Scene";
import { makeSdk } from "./Sdk";
import { ContextProviderComponent as ContextProvider } from "src/components/context";
import App from "./App";
import { Provider } from "react-redux";
import { Grommet } from "grommet";

import store from "models/store";
import theme from "./theme/theme";
import { AppContext } from "./AppContext";

const rootElement = document.querySelector("react");

const Wrapper = styled.div`
  width: 100%;
  height: 100%;
  display: flex;
`;

const initialize = async () => {
  const sdk = await makeSdk("sdk-iframe");
  const scene = await makeScene(sdk);

  const applicationKey = process.env.MATTERPORT_APPLICATION_KEY as string;
  await sdk.init(applicationKey);

  const urlParams = new URLSearchParams(window.location.search);
  let isShared = false;

  if (urlParams.has("shared")) {
    isShared = true;

    const filenameToken = urlParams.get("shared");

    const res = await fetch(
      `https://3d.eventdraw.com.au/eventdraw_api/public/downloadcommon/uploads/${filenameToken}`,
      {
        headers: {
          "Content-Type": "text/plain",
        },
      }
    );

    const data = await res.text();

    const savedData = JSON.parse(data);
    scene.setAvailableModels(savedData.availableModels);

    const m = JSON.parse(savedData.matterport);
    m.version = "1.0";

    scene.setSavedData(JSON.stringify(m));
  }

  const context: IContext = {
    scene,
    sdk,
    isShared,
  };

  render(
    <Provider store={store}>
      <Grommet theme={theme} full>
        <Wrapper className="root">
          <AppContext.Provider value={context}>
            <ContextProvider>
              <App />
            </ContextProvider>
          </AppContext.Provider>
        </Wrapper>
      </Grommet>
    </Provider>,
    rootElement
  );
};

initialize();
