import React, {
  ChangeEvent,
  useCallback,
  useContext,
  useRef,
  useState,
} from "react";
import { Box, Button, Image, Text } from "grommet";
import PanelHeader from "components/panel/PanelHeader";
import InputRangePane from "components/InputRangePane";

import ColorPane from "./color-pane";
import TexturePane from "src/pages/main/panel/texture-pane";
import { AppContext } from "src/AppContext";
import { WallComponent } from "components/matterport/sdk-components/WallComponent";
import colors from "src/constants/color";
import {
  loadAsyncFile,
  loadAsyncImage,
} from "src/pages/admin/matterport/panel/UploadButton";

type Props = { onClose: VoidFunction };

export default function SettingWall({ onClose }: Props) {
  const imagePickerRef = useRef<HTMLInputElement | null>(null);

  const [previewImage, setPreviewImage] = useState("");
  const [wallType, setWallType] = useState("brick");

  const { scene } = useContext(AppContext);

  const [height, setHeight] = useState<number>(3);
  const [wallOpacity, setWallOpacity] = useState(0.2);

  const [wallColor, setWallColor] = useState("#ffffff");

  const setShapeHeight = (v: number) => {
    const nodes = scene.getObjects();

    nodes.forEach((node) => {
      const componentIterator = node.componentIterator();
      for (const component of componentIterator) {
        if (component.componentType === "mp.wall") {
          (component as WallComponent).inputs.height = v;
        }
      }
    });

    setHeight(v);
  };

  const onChangeOpacity = (opacity: number) => {
    const nodes = scene.getObjects();

    nodes.forEach((node) => {
      const componentIterator = node.componentIterator();
      for (const component of componentIterator) {
        if (component.componentType === "mp.wall") {
          (component as WallComponent).inputs.opacity = opacity;
        }
      }
    });

    setWallOpacity(opacity);
  };

  const onSelectColor = (color: string) => {
    const nodes = scene.getObjects();

    nodes.forEach((node) => {
      const componentIterator = node.componentIterator();
      for (const component of componentIterator) {
        if (component.componentType === "mp.wall") {
          (component as WallComponent).inputs.color = color;
        }
      }
    });

    setWallColor(color);
  };

  const onSelectTexture = (data: { url: string; type: string }) => {
    const { url, type } = data;

    const nodes = scene.getObjects();

    nodes.forEach((node) => {
      const componentIterator = node.componentIterator();
      for (const component of componentIterator) {
        if (component.componentType === "mp.wall") {
          (component as WallComponent).inputs.texture = url;
          (component as WallComponent).inputs.type = type;
        }
      }
    });
  };

  const openFilePicker = () => {
    imagePickerRef.current?.click();
  };

  const onImageFilesPicked = useCallback(
    async (event: ChangeEvent<HTMLInputElement>) => {
      if (event.target.files && event.target.files.length > 0) {
        const loadedFile = await loadAsyncFile(event.target.files[0]);

        if (loadedFile) {
          const convertedImage = await loadAsyncImage(loadedFile);

          onSelectTexture({ url: convertedImage.src, type: wallType });
          setPreviewImage(convertedImage.src);
        }
      }
    },
    [setPreviewImage, wallType, onSelectTexture]
  );

  const renderContent = (
    <>
      <InputRangePane
        title="3D Height(m)"
        val={height}
        setVal={setShapeHeight}
        maxVal={15}
        minVal={0.1}
      />

      <InputRangePane
        title="Opacity"
        val={wallOpacity}
        setVal={onChangeOpacity}
        maxVal={1}
      />

      <ColorPane
        title="Wall Color"
        color={wallColor}
        setShapeColor={onSelectColor}
        align={{ left: "right" }}
        side="bottom"
      />

      <TexturePane title="Wall Material" onSelectTexture={onSelectTexture} />

      <Box pad="small">
        <Button
          label="Upload your Wall Image"
          primary
          color={colors.AQUA_MARINE}
          onClick={openFilePicker}
          style={{
            fontSize: "12px",
            color: "#888888",
            border: "solid 1px #888888",
            background: "transparent",
          }}
        />

        <Box
          flex
          direction="row"
          style={{ alignItems: "center", justifyContent: "center" }}
          pad={{ top: "small" }}
        >
          <Box
            flex
            direction="row"
            style={{ alignItems: "center", justifyContent: "center" }}
          >
            <input
              type="radio"
              name="wallType"
              id="contactChoice1"
              value="brick"
              checked={wallType === "brick"}
              onChange={(e) => {
                setWallType(e.target.value);
              }}
            />
            <label htmlFor="contactChoice1">Brick Mode</label>
          </Box>

          <Box
            flex
            direction="row"
            style={{ alignItems: "center", justifyContent: "center" }}
          >
            <input
              type="radio"
              name="wallType"
              id="contactChoice2"
              value="wall"
              checked={wallType === "wall"}
              onChange={(e) => {
                setWallType(e.target.value);
              }}
            />
            <label htmlFor="contactChoice2">Particle Mode</label>
          </Box>
        </Box>

        <input
          type="file"
          ref={imagePickerRef}
          style={{ display: "none" }}
          onChange={onImageFilesPicked}
          accept=".png,.jpg,.jpeg"
        />

        {previewImage && (
          <Image
            src={previewImage}
            style={{ maxHeight: "100px", objectFit: "contain" }}
          />
        )}
      </Box>
    </>
  );

  return (
    <Box fill>
      <PanelHeader onClose={onClose}>
        <Text
          weight={500}
          style={{
            textTransform: "uppercase",
            color: "#25302e",
            fontSize: "15px",
            fontWeight: 600,
          }}
        >
          Wall
        </Text>
      </PanelHeader>

      <Box flex overflow="auto">
        {renderContent}
      </Box>
    </Box>
  );
}
