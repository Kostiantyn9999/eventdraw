import React, { useMemo } from "react";
import _ from "lodash";
import { Box, Text } from "grommet";

import NumberInput from "src/components/NumberInput";
import RowWithTitle from "./RowWithTitle";

import { IEditableMatterport } from "..";
import UploadButton from "./UploadButton";

type Props = {
  item: IEditableMatterport;
  setItem: (v: IEditableMatterport) => void;
  comparedBackImage: { image: HTMLImageElement } | null;
  confirmBackgroundImage: (image: HTMLImageElement) => void;
};

const DimensionControl = ({
  item,
  setItem,
  confirmBackgroundImage,
}: {
  item: IEditableMatterport;
  setItem: (v: IEditableMatterport) => void;
  confirmBackgroundImage: (image: HTMLImageElement) => void;
}) => {
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

  const setRotation = (value: number) => {
    setItem({ ...item, rotation: value });
  };

  return (
    <>
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
      <RowWithTitle title="Rotation Angle">
        <NumberInput
          formatString={"0.[00]"}
          inc={0.01}
          value={_.get(item, "rotation")}
          onChange={setRotation}
        />
      </RowWithTitle>
      <RowWithTitle title="Compare Image">
        <UploadButton onChange={confirmBackgroundImage} />
      </RowWithTitle>
    </>
  );
};

export const mid = (min: number, max: number) => {
  return (max - min) / 2 + min;
};

export const getDimension = (
  width: number,
  height: number,
  center: number[]
) => {
  const xMin = center[0] - width / 2;
  const xMax = center[0] + width / 2;
  const yMin = center[1] - height / 2;
  const yMax = center[1] + height / 2;

  return {
    xMin: xMin,
    xMax: xMax,
    yMin: yMin,
    yMax: yMax,
  };
};

const CenterDimentionConntrol = ({
  item,
  setItem,
  comparedBackImage,
}: {
  item: IEditableMatterport;
  setItem: (v: IEditableMatterport) => void;
  comparedBackImage: {
    image: HTMLImageElement;
  } | null;
}) => {
  const { width, height, center } = useMemo(() => {
    if (!item)
      return {
        width: 0,
        height: 0,
        center: [0, 0],
      };

    const width = item.x.max - item.x.min;
    const height = item.y.max - item.y.min;
    const centerX = mid(item.x.min, item.x.max);
    const centerY = mid(item.y.min, item.y.max);

    return {
      width: width,
      height: height,
      center: [centerX, centerY],
    };
  }, [item]);

  const setWidth = (width: number) => {
    let autoHeight = height;

    if (comparedBackImage) {
      const ratio =
        comparedBackImage.image.height / comparedBackImage.image.width;
      autoHeight = width * ratio;
    }

    const { xMin, xMax, yMin, yMax } = getDimension(width, autoHeight, center);

    setItem({
      ...item,
      x: {
        min: xMin,
        max: xMax,
      },
      y: {
        min: yMin,
        max: yMax,
      },
    });
  };

  const setHeight = (height: number) => {
    let autoWidth = width;

    if (comparedBackImage) {
      const ratio =
        comparedBackImage.image.height / comparedBackImage.image.width;
      autoWidth = height * ratio;
    }

    const { xMin, xMax, yMin, yMax } = getDimension(autoWidth, height, center);

    setItem({
      ...item,
      x: {
        min: xMin,
        max: xMax,
      },
      y: {
        min: yMin,
        max: yMax,
      },
    });
  };

  const setCenterX = (centerX: number) => {
    const { xMin, xMax, yMin, yMax } = getDimension(width, height, [
      centerX,
      center[1],
    ]);

    setItem({
      ...item,
      x: {
        min: xMin,
        max: xMax,
      },
      y: {
        min: yMin,
        max: yMax,
      },
    });
  };

  const setCenterY = (centerY: number) => {
    const { xMin, xMax, yMin, yMax } = getDimension(width, height, [
      center[0],
      centerY,
    ]);

    setItem({
      ...item,
      x: {
        min: xMin,
        max: xMax,
      },
      y: {
        min: yMin,
        max: yMax,
      },
    });
  };

  return (
    <>
      <RowWithTitle title="Size">
        <Box flex direction="row" gap="medium">
          <NumberInput
            formatString={"0.[00]"}
            inc={0.01}
            value={width}
            onChange={setWidth}
          />
          <NumberInput
            formatString={"0.[00]"}
            inc={0.01}
            value={height}
            onChange={setHeight}
          />
        </Box>
      </RowWithTitle>
      <RowWithTitle title="Center Points">
        <Box flex direction="row" gap="medium">
          <NumberInput
            formatString={"0.[00]"}
            inc={0.01}
            value={center[0]}
            onChange={setCenterX}
          />
          <NumberInput
            formatString={"0.[00]"}
            inc={0.01}
            value={center[1]}
            onChange={setCenterY}
          />
        </Box>
      </RowWithTitle>
    </>
  );
};

const CenterPointsDetails = ({
  item,
  setItem,
  comparedBackImage,
  confirmBackgroundImage,
}: Props) => {
  return (
    <Box border="bottom" flex={{ shrink: 0 }}>
      <Box pad="small" background="pale_grey">
        <Text color="light_navy_bright" weight={600}>
          Matterport Dimension
        </Text>
      </Box>
      <Box pad="small" gap="xsmall" flex={{ shrink: 0 }}>
        <CenterDimentionConntrol
          item={item}
          setItem={setItem}
          comparedBackImage={comparedBackImage}
        />
        <DimensionControl
          item={item}
          setItem={setItem}
          confirmBackgroundImage={confirmBackgroundImage}
        />
      </Box>
    </Box>
  );
};

export default CenterPointsDetails;
