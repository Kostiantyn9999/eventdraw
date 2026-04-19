import React, { ChangeEvent } from "react";
import { Box, RangeInput, TextInput } from "grommet";
import _ from "lodash";
import RowWithTitle from "./RowWithTitle";

type Props = {
  title: string;
  val: number;
  setVal: (v: number) => void;
  dir?: "row" | "column";
  maxVal?: number;
  minVal?: number;
};

const InputRangePane = ({ title, val, setVal, dir, maxVal = 50, minVal = 0 }: Props) => {
  const mode = _.isNil(dir) ? "row" : "column";

  const onChanged = (event: ChangeEvent<HTMLInputElement>) => {
    setVal(parseFloat(event.target.value));
  };

  return (
    <Box
      pad={{ vertical: "small", horizontal: "small" }}
      border={{ side: "bottom" }}
      flex={{ shrink: 0 }}
    >
      <RowWithTitle title={title}>
        <Box
          direction={mode}
          justify="center"
          align="center"
          // pad={{ vertical: "small" }}
          gap="small"
          style={{ minWidth: "100px", padding: "0" }}
        >
          <TextInput
            type="number"
            value={val}
            onChange={onChanged}
            width="130"
            step={0.1}
            style={{
              padding: "2px 5px",
              fontSize: "14px",
              textAlign: "right",
              borderRadius: "1px",
              fontFamily: "Arial",
            }}
          />
        </Box>
      </RowWithTitle>
      <RangeInput
        value={val}
        onChange={onChanged}
        min={minVal}
        max={maxVal}
        step={0.1}
      />
    </Box>
  );
};

export default InputRangePane;
