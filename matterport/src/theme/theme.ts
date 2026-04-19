import { css } from "styled-components";

const theme: any = {
  global: {
    font: {
      family: "Epilogue",
      size: "14px",
    },
    focus: {
      border: {
        width: "0px",
        radius: "0px",
        color: "#00000000",
      },
    },
    colors: {
      pale_grey: "#ecf2fa",
      light_navy_bright: "#004069",
      light_navy_grey: "#cdced2",
    },
  },
  button: {
    border: {
      radius: "1px",
    },
    font: {
      size: "small",
    },
    default: {
      font: {
        size: "small",
      },
    },
    secondary: {
      font: {
        size: "small",
      },
      extend: ({}) => css`
        border-bottom: 1px solid #000000;
      `,
    },
    primary: {
      font: {
        size: "small",
      },
      border: {
        radius: "1px",
      },
      background: "#000000",
    },
  },
  rangeInput: {
    track: {
      color: "#FAFAFA",
      extend: css`
        border: 1px solid #d3d3d3;
      `,
    },
    thumb: {
      color: "#7f8f8c",
      extend: css`
        width: 20px;
        height: 20px;

        :hover {
          cursor: w-resize;
        }
      `,
    },
  },
  // checkBox: {
  //   size: "13px",
  //   border: {

  //   }
  //   check: {
  //     thickness: "1px",
  //   },
  //   icon: {
  //     size: "12px",
  //   },
  // },
};

export default theme;
