import React, { useImperativeHandle, useRef, useState } from "react";
import { Box } from "grommet";

import SlidingLeftLayer from "src/components/panel";
import SettingShape from "./setting-shape";
import SettingArt from "./setting-art";
import SettingWall from "./setting-wall";

import { IShape } from "types/floor";
import { MpSdk } from "bundle/sdk";
import { SHAPE } from "types/shape";

export type PanelType = "shape" | "art" | "video" | "wall";

type Props = {
  node: MpSdk.Scene.INode | null;
  items?: { [id: string]: IShape };
  setItems?: (v: any) => void;
  onClose: () => void;
};

type PanelHandle = {
  openObject: (type: SHAPE) => void;
  openArt: () => void;
  openWall: () => void;
  close: () => void;
};

type SlidingLeftLayerHandle = React.ElementRef<typeof SlidingLeftLayer>;

const Panel = React.forwardRef<PanelHandle, Props>(
  ({ node, items, setItems, onClose }, ref) => {
    const panelRef = useRef<SlidingLeftLayerHandle>(null);
    const [color, setColor] = useState<string>("");

    const [mode, setMode] = useState<PanelType | null>(null);
    const [selectShapeType, setSelectShapeType] = useState<SHAPE>(SHAPE.COMMON);

    const renderContent = () => {
      switch (mode) {
        case "shape":
          return (
            <SettingShape node={node} type={selectShapeType} onClose={close} />
          );
        case "art":
          return <SettingArt onClose={close} />;
        case "wall":
          return <SettingWall onClose={close} />;
        default:
          return null;
      }
    };

    useImperativeHandle(ref, () => ({ openObject, openArt, openWall, close }));

    const openObject = (type: SHAPE) => {
      setMode("shape");
      setSelectShapeType(type);
      if (panelRef.current) panelRef.current.open();
    };

    const openArt = () => {
      setMode("art");
      if (panelRef.current) panelRef.current.open();
    };

    const openWall = () => {
      setMode("wall");
      if (panelRef.current) panelRef.current.open();
    };

    const close = () => {
      if (panelRef.current) panelRef.current.close();
      onClose();
    };

    return (
      <SlidingLeftLayer ref={panelRef} onClose={() => {}}>
        <Box
          width="250px"
          height="100%"
          id="slider__panel"
          background="#efefef"
        >
          {renderContent()}
        </Box>
      </SlidingLeftLayer>
    );
  }
);

export default Panel;
