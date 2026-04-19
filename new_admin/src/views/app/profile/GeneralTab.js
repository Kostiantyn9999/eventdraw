import React from "react";
import * as Mui from "@mui/material";
import { Formik, Form } from "formik";
import * as Yup from "yup";
import { useDispatch, useSelector } from "react-redux";
import { editUser } from "reduxs/actions";
import { InputField, InputGoogleField } from "ui/form/field";
import { StyledCard, StyledButton } from "ui";

const GeneralTab = () => {
  const dispatch = useDispatch();

  const { user } = useSelector((state) => state.auth);
  const { loading } = useSelector((state) => state.user);

  const initialValues = {
    id: user?.id || "",
    firstname: user?.firstname || "",
    lastname: user?.lastname || "",
    email: user?.email || "",
    address: user?.address || "",
  };

  const schema = Yup.object().shape({
    firstname: Yup.string()
      .required("Please provide first name")
      .matches(/^[aA-zZ\s]+$/, "Only alphabets are allowed"),
    lastname: Yup.string()
      .required("Please provide last name")
      .matches(/^[aA-zZ\s]+$/, "Only alphabets are allowed"),
    email: Yup.string().email("Invalid email").required("Email is required"),
    address: Yup.string().required("Enter a valid address"),
  });

  const onSubmit = (values) => {
    if (!loading && values.id) dispatch(editUser(values.id, values));
  };

  return (
    <>
      <Formik enableReinitialize={true} initialValues={initialValues} validationSchema={schema} onSubmit={onSubmit}>
        {({ values, setFieldValue }) => (
          <Form>
            <Mui.Grid container spacing={4}>

              <Mui.Grid item xs={12} xl={12}>
                <StyledCard sx={{ p: "1.5rem" }}>
                  <Mui.Grid container spacing={2}>
                    <Mui.Grid item xs={6} md={6}>
                      <InputField name="firstname" type="text" label="First Name*" />
                    </Mui.Grid>

                    <Mui.Grid item xs={6} md={6}>
                      <InputField name="lastname" type="text" label="Last Name*" />
                    </Mui.Grid>

                    <Mui.Grid item xs={6} md={6}>
                      <InputField name="email" type="text" label="Email*" disabled />
                    </Mui.Grid>

                    <Mui.Grid item xs={6} md={6}>
                      <InputGoogleField name="address" label="Address*" placeholder="Enter a Location" />
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
                      Save Changes
                    </StyledButton>
                  </Mui.Box>
                </StyledCard>
              </Mui.Grid>
            </Mui.Grid>{" "}
          </Form>
        )}
      </Formik>
    </>
  );
};

export default GeneralTab;
