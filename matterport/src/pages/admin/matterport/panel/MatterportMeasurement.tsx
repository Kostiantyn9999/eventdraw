import React, { ChangeEvent } from "react";
import { Box, CheckBox, Text } from "grommet";
import _ from "lodash";
import NumberInput from "src/components/NumberInput";

import RowWithTitle from "./RowWithTitle";
import { IEditableMatterport } from "..";

type Props = {
  item: IEditableMatterport;
  setItem: (v: IEditableMatterport) => void;
};

const MatterportMeasurement = ({ item, setItem }: Props) => {
  const setMinX = (value: number) => {
    const x = _.get(item, "x");
    const newXRange = { ...x, min: value };

    setItem({ ...item, x: newXRange });
  };

  const setMaxX = (value: number) => {
    const x = _.get(item, "x");
    const newXRange = { ...x, max: value };

    setItem({ ...item, x: newXRange });
  };

  const setMinY = (value: number) => {
    const x = _.get(item, "y");
    const newXRange = { ...x, min: value };

    setItem({ ...item, y: newXRange });
  };

  const setMaxY = (value: number) => {
    const x = _.get(item, "y");
    const newXRange = { ...x, max: value };

    setItem({ ...item, y: newXRange });
  };

  const onChangeName = () => {};

  const setBaseElevation = (value: number) => {
    setItem({ ...item, baseElevation: value });
  };

  const setRevert = (e: ChangeEvent<HTMLInputElement>) => {
    setItem({ ...item, axis: e.target.checked });
  };

  const setRotation = (value: number) => {
    setItem({ ...item, rotation: value });
  };
  return (
    <Box border="bottom" flex={{ shrink: 0 }}>
      <Box pad="small" background="pale_grey">
        <Text color="light_navy_bright" weight={600}>
          Matterport Dimension
        </Text>
      </Box>
      <Box pad="small" gap="xsmall" flex={{ shrink: 0 }}>
        <RowWithTitle title="X Coordination">
          <Box flex direction="row" gap="medium">
            <NumberInput
              formatString={"0.[00]"}
              inc={0.01}
              value={_.get(item, ["x", "min"])}
              onChange={setMinX}
            />
            <NumberInput
              formatString={"0.[00]"}
              inc={0.01}
              value={_.get(item, ["x", "max"])}
              onChange={setMaxX}
            />
          </Box>
        </RowWithTitle>
        <RowWithTitle title="Y Coordination">
          <Box flex direction="row" gap="medium">
            <NumberInput
              formatString={"0.[00]"}
              inc={0.01}
              value={_.get(item, ["y", "min"])}
              onChange={setMinY}
            />
            <NumberInput
              formatString={"0.[00]"}
              inc={0.01}
              value={_.get(item, ["y", "max"])}
              onChange={setMaxY}
            />
          </Box>
        </RowWithTitle>

        <RowWithTitle title="Base Elevation">
          <Box flex>
            <NumberInput
              formatString={"0.[00]"}
              inc={0.01}
              value={_.get(item, "baseElevation")}
              onChange={setBaseElevation}
            />
          </Box>
        </RowWithTitle>

        <RowWithTitle title="Axis Revert">
          <Box flex pad={{ vertical: "xsmall" }}>
            <CheckBox checked={_.get(item, "axis")} onChange={setRevert} />
          </Box>
        </RowWithTitle>

        <RowWithTitle title="Rotation Angle">
          <Box flex>
            <NumberInput
              formatString={"0.[00]"}
              inc={0.01}
              value={_.get(item, "rotation")}
              onChange={setRotation}
            />
          </Box>
        </RowWithTitle>
        {/* <Box flex justify="end" direction="row">
          <Button label="Set Dimension" primary />
        </Box> */}
      </Box>
      <Box pad="small" gap="xsmall" flex={{ shrink: 0 }}>
        <RowWithTitle title="Width">
          <Box flex>
            <NumberInput
              formatString={"0.[00]"}
              value={_.get(item, ["x", "max"]) - _.get(item, ["x", "min"])}
              disabled
            />
          </Box>
        </RowWithTitle>
        <RowWithTitle title="Height">
          <Box flex>
            <NumberInput
              formatString={"0.[00]"}
              value={_.get(item, ["y", "max"]) - _.get(item, ["y", "min"])}
              disabled
            />
          </Box>
        </RowWithTitle>
      </Box>
    </Box>
  );
};

export default MatterportMeasurement;
