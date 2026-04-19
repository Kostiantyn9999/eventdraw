import React, { useEffect } from "react";
import * as Mui from "@mui/material";
import { Link, useNavigate } from "react-router-dom";
import { Formik, Form } from "formik";
import * as Yup from "yup";
import { useDispatch, useSelector } from "react-redux";
import { getUser, addUser, editUser } from "reduxs/actions";
import { StyledCard, StyledButton } from "ui";
import { InputField, InputPasswordField } from "ui/form/field";

const UserForm = (props) => {
  const { editId } = props;

  const theme = Mui.useTheme();
  const dispatch = useDispatch();
  const navigate = useNavigate();

  const { loading, userData } = useSelector((state) => state.user);

  const initialValues = {
    firstname: editId ? userData?.firstname || "" : "",
    lastname: editId ? userData?.lastname || "" : "",
    email: editId ? userData?.email || "" : "",
    address: editId ? userData?.address || "" : "",
    password: "",
    passwordConfirmation: ""
  };

  const schema = Yup.object().shape({
    firstname: Yup.string()
      .required("Please provide first name")
      .matches(/^[aA-zZ\s]+$/, "Only alphabets are allowed"),
    lastname: Yup.string()
      .required("Please provide last name")
      .matches(/^[aA-zZ\s]+$/, "Only alphabets are allowed"),
    email: Yup.string().email("Invalid email").required("Email is required"),
    // address: Yup.string().required("Enter a valid address"),
    password: Yup.string()
      .required("Password is required"),
    // .matches(
    //   /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})/,
    //   "Password should be at least 8 digits including 1 number, 1 uppercase, 1 lowercase and 1 special character"
    // ),
    passwordConfirmation: Yup.string()
      .required("Confirm Password is required")
      .oneOf([Yup.ref("password"), null], "Passsword and Confirm Password didn't match"),
  });

  const onSubmit = (values) => {
    if (!loading) {
      if (editId) {
        dispatch(editUser(editId, values, navigate));
      } else {
        dispatch(addUser(values, navigate));
      }
    }
  };

  useEffect(() => {
    if (editId) dispatch(getUser(editId));
  }, []);

  return (
    <Formik enableReinitialize={true} initialValues={initialValues} validationSchema={schema} onSubmit={onSubmit}>
      {({ values, resetForm, setFieldValue }) => (
        <Form>
          <Mui.Grid container spacing={4}>
            <Mui.Grid item xs={12} xl={12}>
              <StyledCard sx={{ p: "1.5rem" }}>
                <Mui.Grid container spacing={2}>
                  <Mui.Grid item xs={4} md={4}>
                    <InputField name="firstname" type="text" label="First Name*" />
                  </Mui.Grid>

                  <Mui.Grid item xs={4} md={4}>
                    <InputField name="lastname" type="text" label="Last Name*" />
                  </Mui.Grid>

                  <Mui.Grid item xs={4} md={4}>
                    <InputField name="email" type="text" label="Email*" />
                  </Mui.Grid>

                  <Mui.Grid item xs={6} md={6}>
                    <InputPasswordField name="password" label="New Password" />
                  </Mui.Grid>

                  <Mui.Grid item xs={6} md={6}>
                    <InputPasswordField name="passwordConfirmation" label="Confirm Password" />
                  </Mui.Grid>

                </Mui.Grid>

                <Mui.Box
                  width="100%"
                  display="flex"
                  flexDirection={{ xs: "column", sm: "row" }}
                  justifyContent={{ sm: "flex-end" }}
                  gap={2}
                  mt={5}
                >
                  <StyledButton type="submit" isloading={loading}>
                    {editId ? "Update User" : "Create User"}
                  </StyledButton>

                  <StyledButton
                    type="button"
                    variant="outlined"
                    color={theme.palette.grey.main}
                    sx={{ color: theme.palette.text.primary }}
                    component={Link}
                    to={`${process.env.PUBLIC_URL}/user`}
                  >
                    Cancel
                  </StyledButton>
                </Mui.Box>
              </StyledCard>
            </Mui.Grid>
          </Mui.Grid>
        </Form>
      )}
    </Formik>
  );
};

export default UserForm;
