import React, { useImperativeHandle, useState } from "react";
import { Layer } from "grommet";

type Props = {
  children: JSX.Element | JSX.Element[];
  onClose?: () => void;
};

type SlidingLeftHandle = {
  open: VoidFunction;
  close: VoidFunction;
};

const SlidingLeftLayer = React.forwardRef<SlidingLeftHandle, Props>(
  ({ children, onClose = () => {} }, ref) => {
    const [show, setShow] = useState(false);

    const open = () => {
      setShow(true);
    };

    const close = () => {
      setShow(false);
      onClose();
    };

    useImperativeHandle(ref, () => ({
      open,
      close,
    }));

    if (show) {
      return (
        <Layer
          modal={false}
          position="left"
          full="vertical"
          onEsc={onClose}
          plain
          style={{ boxShadow: "rgb(0 0 0) -13px 1px 20px 11px" }}
        >
          {children}
        </Layer>
      );
    } else {
      return null;
    }
  }
);

export default SlidingLeftLayer;
