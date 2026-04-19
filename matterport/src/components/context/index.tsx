import React, { useState } from "react";
import { MpSdk } from "bundle/sdk";
import { IContentType } from "src/interfaces";

const defaultContextValue = {
  sdk: null,
  clickSpy: null,
  scene: null,
};

const EventDrawContext = React.createContext<{
  data: IContentType;
  setClickSpy: (v: MpSdk.Scene.IComponentEventSpy) => void;
  // setSDK: (sdk: MpSdk) => void;
}>({
  data: defaultContextValue,
  setClickSpy: (v: MpSdk.Scene.IComponentEventSpy) => {},
  // setSDK: (sdk: MpSdk) => {},
});

const { Provider, Consumer } = EventDrawContext;

interface Props {
  children: JSX.Element | JSX.Element[];
  value: IContentType;
}

const ContextProviderComponent = ({ children }: Props) => {
  const [data, setData] = useState<IContentType>(defaultContextValue);

  const setClickSpy = (clickSpy: MpSdk.Scene.IComponentEventSpy) => {
    setData({ ...data, clickSpy });
  };

  return <Provider value={{ data: data, setClickSpy }}>{children}</Provider>;
};

export { Consumer as default, ContextProviderComponent, EventDrawContext };
