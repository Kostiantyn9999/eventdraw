import React from "react";
import {Box, Text} from "grommet";
import _ from "lodash";
import styled from "styled-components";

import DropContentContainer from "./DropContentContainer";
import {BorderType} from "grommet/utils"; // import colors from "src/constants/colors";
// import colors from "src/constants/colors";

const StyledText = styled(Text)`
  line-height: 1;
  width: 100%;
`;

const DropContent = ({
  title,
  titleBorder,
  options,
  selectedId,
  onOptionClick,
  boxProps,
  optionFontColor,
  optionBorder,
  fontSize,
}: {
  title: string;
  fontSize: string;
  options: Array<any>;
  selectedId: number;
  boxProps: object;
  onOptionClick?: () => void;
  titleBorder?: object;
  optionBorder?: BorderType;
  optionFontColor?: string;
}) => {
  const renderOption = (option: any) => {
    const selected = option.id === selectedId;
    const disabled = option.disabled;
    const color = optionFontColor || (disabled ? "#FFFFFF" : "#FFFFFF");
    return (
      <Box
        flex={false}
        key={option.id}
        pad={{ horizontal: "small", vertical: "small" }}
        round="none"
        // onClick={disabled ? undefined : () => onOptionClick(option.id)}
        hoverIndicator={!selected}
        border={optionBorder || { side: "bottom" }}
        fill="horizontal"
      >
        <Box
          direction="row"
          align="center"
          gap="xsmall"
          pad="xsmall"
          width="100%"
        >
          <StyledText
            size={fontSize || "medium"}
            // color={selected ? colors.LIGHT_NAVY : color}
            weight={selected ? 600 : 400}
          >
            {option.label}
          </StyledText>
        </Box>
      </Box>
    );
  };

  const renderTitle = () => {
    if (!_.isEmpty(title)) {
      return (
        <Box border={titleBorder || { side: "bottom" }} flex={false}>
          <Text
            margin={{ vertical: "small", horizontal: "small" }}
            size={fontSize || "medium"}
            // color={colors.ANOTHER_GREY}
          >
            {title}
          </Text>
        </Box>
      );
    }
  };

  return (
    <DropContentContainer
      boxProps={{ pad: { top: "xsmall" }, width: "fill", ...boxProps }}
    >
      {renderTitle()}
      {_.map(options, renderOption)}
    </DropContentContainer>
  );
};

export default DropContent;
