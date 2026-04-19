import React, { useRef, useState } from "react";
import ReactGPicker from "react-gcolor-picker";
import { Box, Drop } from "grommet";
import RowWithTitle from "./RowWithTitle";

type Props = {
  align: any;
  color: string;
  onChange: (v: string) => void;
};

const ColorPickerPane = ({ align, color, onChange }: Props) => {
  const targetRef = useRef<HTMLDivElement>(null);

  const [showColorLayer, setShowColorLayer] = useState<boolean>(false);

  return (
    <Box pad={{ vertical: "0px", horizontal: "small" }}>
      <Box>
        <RowWithTitle title="Fill:">
          <Box flex direction="row" justify="end">
            <Box
              // width="100%"
              // height={{ min: "20px" }}
              ref={targetRef}
              onClick={() => setShowColorLayer(true)}
              // border="all"
              style={{
                borderRadius: "4px",
                padding: "3px 4px",
                backgroundColor: "#f5f5f5",
                backgroundImage: "linear-gradient(#f5f5f5 0px, #e1e1e1 100%)",
                border: "1px solid rgba(0, 0, 0, 0.5)",
              }}
            >
              <Box
                style={{
                  width: "36px",
                  height: "16px",
                  border: "1px solid black",
                  background: `${color}`,
                }}
              />
              {showColorLayer && (
                <Drop
                  align={align}
                  target={targetRef.current}
                  onClickOutside={() => setShowColorLayer(false)}
                  overflow="visible"
                  // inline={true}
                  // stretch={false}
                >
                  <Box border fill align="center" justify="center">
                    <ReactGPicker
                      onChange={onChange}
                      debounceMS={100}
                      format="hex"
                    />
                  </Box>
                </Drop>
              )}
            </Box>
          </Box>
        </RowWithTitle>
      </Box>
    </Box>
  );
};

export default ColorPickerPane;
