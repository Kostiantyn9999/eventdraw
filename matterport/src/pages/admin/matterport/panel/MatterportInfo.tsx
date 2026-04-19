import React from "react";
import PropTypes from "prop-types";
import { Box, Button, Text, TextInput } from "grommet";
import _ from "lodash";

import RowWithTitle from "./RowWithTitle";
import { IEditableMatterport } from "..";


type Props = {
  item: IEditableMatterport;
};

const MatterportInfo = ({ item }: Props) => {
  const onChangeName = () => {};

  return (
    <Box border="bottom" flex={{ shrink: 0 }}>
      <Box pad="small" background="pale_grey">
        <Text color="light_navy_bright" weight={600}>
          Additional Info
        </Text>
      </Box>
      <Box pad="small" gap="xsmall" flex={{ shrink: 0 }}>
        <RowWithTitle title="Register Date">
          <Box flex>
            <TextInput
              value={_.get(item, "regData")}
              onChange={onChangeName}
              disabled
            />
          </Box>
        </RowWithTitle>
        <RowWithTitle title="Operator">
          <Box flex>
            <TextInput
              value={_.get(item, "admin")}
              onChange={onChangeName}
              disabled
            />
          </Box>
        </RowWithTitle>
      </Box>
    </Box>
  );
};

MatterportInfo.propTypes = {};

export default MatterportInfo;
