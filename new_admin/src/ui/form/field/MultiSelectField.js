import React, { useState, useEffect } from "react";
import * as Mui from "@mui/material";
import Checkbox from "@mui/material/Checkbox";
import TextField from "@mui/material/TextField";
import Autocomplete from "@mui/material/Autocomplete";
import CheckBoxOutlineBlankIcon from "@mui/icons-material/CheckBoxOutlineBlank";
import CheckBoxIcon from "@mui/icons-material/CheckBox";
import { useField } from "formik";
import InputFieldStyles from "styles/form/InputField.style";
import SelectFieldStyles from "styles/form/SelectField.style";

const icon = <CheckBoxOutlineBlankIcon fontSize="small" />;
const checkedIcon = <CheckBoxIcon fontSize="small" />;

export default function MultiSelectField({ options, label, callback, filterEvent, disableClearable = true, ...props }) {
    const theme = Mui.useTheme();
    const FieldStyles = InputFieldStyles();
    const SelectStyles = SelectFieldStyles(theme);
    const { labelField = "name" } = props;
    const [field, meta, helpers] = useField(props);
    const [selectedVal, setSelectedVal] = useState([]);

    const [inputValue, setInputValue] = useState("");

    useEffect(() => {
        setSelectedVal(options?.filter((x) => field?.value.includes(x.id)) || []);
        if (callback) {
            callback(options?.filter((x) => {
                if (field?.value.includes(x.id)) {
                    return x;
                }
            }));
        }
    }, [options, field.value]);

    const handleSelectChange = (event, selectedOption) => {
        const ids = [];
        selectedOption.forEach((item) => {
            ids.push(item.id);
        });
        helpers.setValue(ids);
        if (ids.length > 0) {
            setInputValue("");
        } 
        if (callback) {
            callback(selectedOption);
        }
    };

    return (
        <>
            <Autocomplete
                noOptionsText={"Please enter search keyword."}
                multiple
                options={options || []}
                disableCloseOnSelect
                value={selectedVal}
                inputValue={inputValue}
                onChange={handleSelectChange}
                getOptionLabel={(option) => option[labelField]}
                renderOption={(props, option, { selected }) => (
                    <li {...props} key={option.id}>
                        <Checkbox
                            icon={icon}
                            checkedIcon={checkedIcon}
                            style={{ marginRight: 8 }}
                            checked={selected}
                        />
                        {option[labelField]}
                    </li>
                )}
                fullWidth
                sx={FieldStyles}
                componentsProps={{ popper: { sx: SelectStyles.paper } }}
                margin="dense"
                renderInput={(params) => (
                    <TextField
                        {...params}
                        label={label}
                        placeholder={props?.placeholder || ""}
                        onChange={(event) => {
                            setInputValue(event.target.value);
                            if (event.target.value) {
                                filterEvent(event.target.value);
                            }
                        }}
                    />
                )}
            />
            {meta.touched && meta.error && (
                <Mui.FormHelperText error>{meta.touched && meta.error ? meta.error : null}</Mui.FormHelperText>
            )}
        </>
    );
}
