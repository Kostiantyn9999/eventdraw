import React from "react";
import { Box, Button, Text , Select } from "grommet";
import LayoutAssign from "./LayoutAssign";
import MatterportDetail from "./MatterportDetail";
import MatterportInfo from "./MatterportInfo";
import CenterPointsDetails from "./CenterPointsDetails";

type Props = {
  item: IEditableMatterport;
  sceneId: string;
  clickSave: () => void;
  clickCancel: () => void;
  setItem: (v: IEditableMatterport) => void;
  initMatterport: () => void;
  comparedBackImage: { image: HTMLImageElement } | null;
  confirmBackgroundImage: (image: HTMLImageElement) => void;
  labels: any[];
  floors: any[];
};

const ControlPanel = ({
  item,
  setItem,
  sceneId,
  clickSave,
  clickCancel,
  initMatterport,
  comparedBackImage,
  confirmBackgroundImage,
  labels,
  floors,
}: Props) => {
  return (
    <Box flex={{ shrink: 0 }} basis="1/4" border="left">
      <Box flex={{ shrink: 0 }} border="bottom" pad="small" align="center">
        <Text size="large" weight={700} style={{ textTransform: "uppercase" }}>
          {sceneId ? "Edit Matterport" : "Add Matterport"}
        </Text>
      </Box>

      <Box flex overflow="auto">
        <LayoutAssign item={item} setItem={setItem} />
        <MatterportDetail
          item={item}
          setItem={setItem}
          initMatterport={initMatterport}
          floors={floors}
          labels={labels}
        />
        <CenterPointsDetails
          item={item}
          setItem={setItem}
          comparedBackImage={comparedBackImage}
          confirmBackgroundImage={confirmBackgroundImage}
        />
        {/* <MatterportMeasurement item={item} setItem={setItem} /> */}
        <MatterportInfo item={item} />
      </Box>

      <Box
        flex={{ shrink: 0 }}
        direction="row"
        justify="end"
        pad="small"
        gap="small"
      >
        <Button label="Cancel" secondary onClick={clickCancel} />
        <Button label="Save" primary onClick={clickSave} />
      </Box>
    </Box>
  );
};

export default ControlPanel;
