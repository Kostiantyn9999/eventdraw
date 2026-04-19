import React from "react";
import { Box, TextInput } from "grommet";

type Props = {
  type: string;
  icon: any;
  placeholder?: string;
  value: any;
  onChange: (v: any) => void;
};

const IconInputText = ({
  type = "text",
  icon,
  placeholder,
  value,
  onChange,
}: Props) => {
  return (
    <Box
      direction="row"
      align="center"
      width="medium"
      pad={{ horizontal: "small", vertical: "xsmall" }}
      round="small"
      border={{ side: "all" }}
    >
      {icon}
      <TextInput
        type={type}
        plain
        placeholder={placeholder}
        color="white"
        value={value}
        onChange={onChange}
      />
    </Box>
  );
};

export default IconInputText;
