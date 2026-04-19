import React, { useEffect, useRef, useState } from "react";
import PropTypes from "prop-types";
import _ from "lodash";
import { Keyboard, TextInput } from "grommet";
import numeral from "numeral";

type Props = {
  value: number;
  inc: number;
  formatString: string;
  placeholder: string;
  onChange: (v: number) => void;
  onFocus: () => void;
  onBlur: () => void;
  disabled?: boolean;
};

const NumberInput = ({
  value,
  onFocus,
  onBlur,
  onChange,
  placeholder,
  formatString,
  inc,
  ...rest
}: Props) => {
  const [v, setValue] = useState<string | null>(null);
  const inputRef = useRef<HTMLInputElement>(null);

  const setV = (newValue: string | null) => {
    const numV = numeral(newValue).value();
    const strNumV = _.toString(numV);
    if (_.toString(newValue) === strNumV && !_.isEmpty(strNumV)) {
      onChange(numV ? numV : 0);
    } else {
      setValue(newValue);
    }
  };

  useEffect(() => {
    setV(null);
  }, [value]);

  const handleOnChange = (event) => {
    // const valueRaw = event.target.value.replace(/[^0-9.,$]/g, "");
    // const newValue = valueRaw.replace(/[^0-9.,]/g, "");

    // if (!isNaN(event.target.value)) {
    //   setV(numeral(event.target.value).format(formatString));
    // }
    setV(numeral(event.target.value).format(formatString));
    // if (!_.isEmpty(_.toString(newValue))) {
    //   const newV = numeral(newValue).value();
    //   const curV = numeral(v).value();
    //   if (newV === curV || _.isNil(v)) {
    //     setV(valueRaw);
    //   } else {
    //     setV(numeral(newValue).format(formatString));
    //   }
    // } else {
    //   setV("");
    // }
  };

  let curValue = value;
  if (!_.isEmpty(_.toString(curValue))) {
    curValue = numeral(curValue).format(formatString);
  }
  if (!_.isNil(v)) curValue = v;
  if (_.isNil(curValue)) curValue = "";

  const handleOnBlur = () => {
    onBlur();
    if (_.isNil(v)) {
    } else {
      const cost = _.size(v) > 0 ? numeral(v).value() : null;
      onChange(cost);
    }
  };

  const onEnter = () => {
    if (inputRef.current) inputRef.current.blur();
  };

  const onUp = () => {
    const curV = numeral(curValue).value();
    const newV = curV + inc;
    setV(numeral(newV).format(formatString));
  };

  const onDown = () => {
    const curV = numeral(curValue).value();
    const newV = curV - inc;
    setV(numeral(newV).format(formatString));
  };

  const throttleOnUp = _.throttle(onUp, 100, { leading: false });
  const throttleOnDown = _.throttle(onDown, 100, { leading: false });

  return (
    <Keyboard onEnter={onEnter} onUp={throttleOnUp} onDown={throttleOnDown}>
      <TextInput
        type="number"
        value={curValue}
        alignSelf="center"
        onChange={handleOnChange}
        onFocus={onFocus}
        onBlur={handleOnBlur}
        placeholder={placeholder}
        ref={inputRef}
        step={0.1}
        {...rest}
      />
    </Keyboard>
  );
};

NumberInput.defaultProps = {
  placeholder: "",
  onFocus: () => null,
  onBlur: () => null,
  onChange: () => null,
  formatString: "$0,0.[00]",
  inc: 0.1,
};

NumberInput.propTypes = {
  value: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
  onFocus: PropTypes.func,
  onBlur: PropTypes.func,
  onChange: PropTypes.func,
  placeholder: PropTypes.string,
  inc: PropTypes.number,
};

export default NumberInput;
