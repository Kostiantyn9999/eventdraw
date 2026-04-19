import React, { ChangeEvent, useEffect, useMemo, useState } from "react";
import { Box, Button, Select, Text, TextInput } from "grommet";
import _ from "lodash";

import RowWithTitle from "./RowWithTitle";

type Props = {
  item: any;
  setItem: (v: any) => void;
  initMatterport: VoidFunction;
  labels: any[];
  floors: any[];
};
const MatterportDetail = ({
  item,
  setItem,
  initMatterport,
  floors,
  labels,
}: Props) => {
  const [tempMatterId, setTempMatterId] = useState("");

  const matterId = useMemo(() => _.get(item, "matterId"), [item]);

  useEffect(() => {
    if (matterId) {
      setTempMatterId(matterId);
    }

    return () => {};
  }, [matterId]);

  const setMatterPortId = () => {
    setItem({ ...item, matterId: tempMatterId });

    initMatterport();
  };

  const onChangeName = (e: ChangeEvent<HTMLInputElement>) => {
    setItem({ ...item, name: e.target.value });
  };

  return (
    <Box border="bottom" flex={{ shrink: 0 }}>
      <Box pad="small" background="pale_grey">
        <Text color="light_navy_bright" weight={600}>
          Matterport Detail
        </Text>
      </Box>
      <Box pad="small" gap="xsmall" flex={{ shrink: 0 }}>
        <RowWithTitle title="Name">
          <Box flex>
            <TextInput value={_.get(item, "name")} onChange={onChangeName} />
          </Box>
        </RowWithTitle>
        <RowWithTitle title="Matterport Id">
          <Box flex>
            <TextInput
              value={tempMatterId}
              onChange={(e) => setTempMatterId(e.target.value)}
            />
          </Box>
        </RowWithTitle>
        <RowWithTitle title="Room">
          <Box flex>
            <Select
              options={labels}
              labelKey="text"
              valueKey="sid"
              onChange={({ value, option }) => {
                
              }}
            />
          </Box>
        </RowWithTitle>
        <Box flex justify="end" direction="row">
          <Button
            label="Display Matterport"
            primary
            onClick={setMatterPortId}
            disabled={matterId === tempMatterId}
          />
        </Box>
      </Box>
    </Box>
  );
};

MatterportDetail.propTypes = {};

export default MatterportDetail;
