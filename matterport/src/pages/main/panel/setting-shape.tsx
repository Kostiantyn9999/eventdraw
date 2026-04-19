import React, { useContext, useEffect, useState } from "react";
import { Box, Text } from "grommet";
import { MpSdk } from "bundle/sdk";

import PanelHeader from "src/components/panel/PanelHeader";

import InputRangePane from "src/components/InputRangePane";
import { SHAPE } from "types/shape";
import { ShapeComponent } from "src/components/matterport/sdk-components/ShapeComponent";
import { ChairComponent } from "src/components/matterport/sdk-components/ChairComponent";
import { ArtImageComponent } from "src/components/matterport/sdk-components/ArtImageComponent";

import ColorPane from "./color-pane";
import CheckboxPane from "./checkbox-pane";
import ResetButton from "./ResetButton";
import { AppContext } from "src/AppContext";
import TableChairModel from "./chair-model";

type Props = {
  node: MpSdk.Scene.INode | null;
  type: SHAPE;
  onClose: VoidFunction;
};

const SettingShape = ({ node, type, onClose }: Props) => {
  const { scene } = useContext(AppContext);

  const measKoeff = 39.37;
  const [isChangeAll, setIsChangeAll] = useState(false);
  const [isHideTableNumber, setIsHideTableNumber] = useState(false);
  const [height, setHeight] = useState<number>(0);
  const [baseElevation, setBaseElevation] = useState<number>(0);
  const [color, setColor] = useState<string>("#ffffff");
  const [chairColor, setChairColor] = useState<string>("#ffffff");
  const [rotation, setRotation] = useState<number>(0);

  useEffect(() => {
    if (node) {
      const componentIterator = node.componentIterator();
      for (const component of componentIterator) {
        if (type === SHAPE.TABLE) {
          if (component.componentType === "mp.shape") {
            const inputs = (component as ShapeComponent).inputs;
            setBaseElevation(inputs.baseElevation / measKoeff);
            setHeight(inputs.size.y * inputs.scaleY / measKoeff);
          }
        }

        if (type === SHAPE.CHAIR) {
          if (component.componentType === "mp.chair") {
            setBaseElevation(
              (component as ChairComponent).inputs.baseElevation / measKoeff
            );
          }
        }
      }
    }
  }, [node]);

  const setShapeHeight = (v: number) => {
    if (node) {
      const componentIterator = node.componentIterator();
      for (const component of componentIterator) {
        if (component.componentType === "mp.shape") {
          const inputs = (component as ShapeComponent).inputs;
          (component as ShapeComponent).inputs.size = {
            ...inputs.size,
            y: v / inputs.scaleY * measKoeff
          };
        }
      }

      setHeight(v);
    }
  };

  const setShapeBaseElevation = (v: number) => {
    if (node) {
      const componentIterator = node.componentIterator();
      for (const component of componentIterator) {
        if (component.componentType === "mp.shape") {
          (component as ShapeComponent).inputs.baseElevation = v * measKoeff;
        }
      }

      setBaseElevation(v);
    }
  };

  const setShapeColor = (v: string, selectedType: "shape" | "chair") => {
    if (node) {
      if (isChangeAll) {
        const nodes = scene.getObjects();
        const selectedName = node.name;

        nodes
          .filter((node) => node.name === selectedName)
          .forEach((node) => {
            const componentIterator = node.componentIterator();
            for (const component of componentIterator) {
              if (selectedType === "shape") {
                if (component.componentType === "mp.shape") {
                  (component as ChairComponent).inputs.color = v;
                }
              } else if (selectedType === "chair") {
                if (component.componentType === "mp.chair") {
                  (component as ChairComponent).inputs.color = v;
                }
              }
            }
          });

        if (selectedType === "shape") {
          setColor(v);
        } else if (selectedType === "chair") {
          setChairColor(v);
        }
      } else {
        const componentIterator = node.componentIterator();
        for (const component of componentIterator) {
          if (selectedType === "shape") {
            if (component.componentType === "mp.shape") {
              (component as ChairComponent).inputs.color = v;
            }
          } else if (selectedType === "chair") {
            if (component.componentType === "mp.chair") {
              (component as ChairComponent).inputs.color = v;
            }
          }
        }

        if (selectedType === "shape") {
          setColor(v);
        } else if (selectedType === "chair") {
          setChairColor(v);
        }
      }
    }
  };

  const setChairModel = (model: any) => {
    if (node) {
      if (isChangeAll) {
        const nodes = scene.getObjects();
        const selectedName = node.name;

        nodes
          .filter((node) => node.name === selectedName)
          .forEach((node) => {
            const componentIterator = node.componentIterator();
            for (const component of componentIterator) {
              if (component.componentType === "mp.chair") {
                (component as ChairComponent).inputs.model = model;
              }
            }
          });
      } else {
        const componentIterator = node.componentIterator();
        for (const component of componentIterator) {
          if (component.componentType === "mp.chair") {
            (component as ChairComponent).inputs.model = model;
          }
        }
      }
    }
  }

  const setRotationAngle = (v: number) => {
    if (node) {
      const componentIterator = node.componentIterator();

      for (const component of componentIterator) {
        if (type === SHAPE.ART) {
          if (component.componentType === "mp.artImage") {
            
            (component as ArtImageComponent).inputs.rotation = v;
          }
        }

        if (type === SHAPE.VIDEO) {
          if (component.componentType === "mp.artVideo") {
            
            (component as ArtImageComponent).inputs.rotation = v;
          }
        }
      }

      setRotation(v);
    }
  };

  const renderContent = () => {
    switch (type) {
      case SHAPE.TABLE:
      case SHAPE.CHAIR:
        return (
          <>
            <CheckboxPane
              label="Change settings for all same models?"
              border="bottom"
              checked={isChangeAll}
              onChange={setIsChangeAll}
            />

            <InputRangePane
              title="Base Elevation(m)"
              val={baseElevation}
              setVal={setShapeBaseElevation}
              maxVal={15}
            />

            <InputRangePane
              title="3D Height(m)"
              val={height}
              setVal={setShapeHeight}
              maxVal={15}
              minVal={0.1}
            />

            <ResetButton />

            <ColorPane
              title="Table Color"
              color={color}
              setShapeColor={(color) => setShapeColor(color, "shape")}
              align={{ left: "right" }}
            />

            <ColorPane
              title="Chair Color"
              color={chairColor}
              setShapeColor={(color) => setShapeColor(color, "chair")}
              align={{ left: "right", top: "top" }}
            />

            <TableChairModel setChairModel={setChairModel} />

            <CheckboxPane
              label="Hide Table Numbers"
              border="horizontal"
              checked={isHideTableNumber}
              onChange={setIsHideTableNumber}
            />
          </>
        );
      case SHAPE.ART:
        return (
          <>
            <InputRangePane
              title="Rotation Angle"
              val={rotation}
              setVal={setRotationAngle}
              maxVal={360}
            />
          </>
        );
      case SHAPE.VIDEO:
        return (
          <>
            <InputRangePane
              title="Rotation Angle"
              val={rotation}
              setVal={setRotationAngle}
              maxVal={360}
            />
          </>
        );
    }
  };

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
          {/* {type} Control */}
          Property
        </Text>
      </PanelHeader>
      <Box flex overflow="auto">
        {renderContent()}
      </Box>
    </Box>
  );
};
export default SettingShape;
