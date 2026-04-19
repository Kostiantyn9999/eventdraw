import React, { useEffect } from "react";
import * as Mui from "@mui/material";
import * as Yup from "yup";
import { useNavigate } from "react-router-dom";
import { useDispatch, useSelector } from "react-redux";
import { Formik, Form } from "formik";
import { login, resetAuth } from "reduxs/actions";
import { InputField, InputPasswordField } from "ui/form/field";
import { StyledButton } from "ui";
import FrontLoginImg from "assets/images/login-front.jpg";

const Login = () => {
  const dispatch = useDispatch();
  const navigate = useNavigate();

  const { loading } = useSelector((state) => state.auth);

  const schema = Yup.object().shape({
    email: Yup.string().email("Invalid email address").required("Email is required"),
    password: Yup.string().required("Password is required"),
  });

  const onLogin = (values) => {
    if (!loading) {
      dispatch(login(values, navigate));
    }
  };

  useEffect(() => {
    dispatch(resetAuth());
  }, []);

  return (
    <Mui.Container className="h-screen" maxWidth={false}>
      <div className="px-6 h-full text-gray-800">
        <div className="flex xl:justify-center lg:justify-between justify-center items-center flex-wrap h-full g-6">
          <div className="grow-0 shrink-1 md:shrink-0 basis-auto xl:w-6/12 lg:w-6/12 md:w-9/12 mb-12 md:mb-0" style={{ textAlign: "center" }}>
            <Mui.Typography component="h1" sx={{ mb: "30px" }} fontSize="2.5rem" fontWeight="600">
              Hi, Welcome back
            </Mui.Typography>

            <img
              src={FrontLoginImg}
              className="w-full"
              alt="Sample image"
            />
          </div>

          <Formik
            initialValues={{
              email: localStorage.getItem("email") ? localStorage.getItem("email") : "",
              password: localStorage.getItem("password") ? localStorage.getItem("password") : "",
              remember: localStorage.getItem("remember") ? parseInt(localStorage.getItem("remember")) : 0,
            }}
            validationSchema={schema}
            onSubmit={onLogin}
          >
            {({ values, setFieldValue }) => (
              <Form className="xl:ml-20 xl:w-3/12 lg:w-5/12 md:w-8/12">
                <Mui.Typography component="h1" fontSize="1.5rem" fontWeight="600">
                  Sign in to EventDraw 3D Admin
                </Mui.Typography>

                <InputField name="email" sx={{ mt: "20px" }} type="text" label="Email" />

                <InputPasswordField sx={{ mt: "20px" }} name="password" label="Password" />

                <StyledButton type="submit" color="#212b36" sx={{ width: "100%", mt: "20px", float: "right" }} isloading={loading}>
                  Sign In
                </StyledButton>
              </Form>
            )}
          </Formik>
        </div>
      </div>
    </Mui.Container>
  );
};

export default Login;
