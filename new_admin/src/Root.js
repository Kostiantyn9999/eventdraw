import React, { Suspense } from "react";
import { BrowserRouter, Navigate, Routes, Route, Outlet } from "react-router-dom";
import { shallowEqual, useSelector } from "react-redux";
import { LayoutSplashScreen } from "configs/LayoutSplashScreen";
import { Layout, AuthLayout } from "layouts";
import { ToastWrapper } from "ui";
import {
  Register,
  VerifyUser,
  InitialChangePassword,
  Login,
  ForgotPassword,
  ResetPassword,
  VerifyEmail,
  Logout,
} from "views/auth";
import Dashboard from "views/app/dashboard";
// import Profile from "views/app/profile";
import { ShapeList, EditShape } from "views/app/shape";
import { RealisticList, EditRealistic } from "views/app/realistic";
import { MatterportList, EditMatterport } from "views/app/matterport";
import { UserList, EditUser } from "views/app/user";
import PageNotFound from "views/PageNotFound";

export default function Root() {
  const { isAuthorized } = useSelector(
    ({ auth }) => ({
      isAuthorized: auth.user != null,
    }),
    shallowEqual
  );

  return (
    <>
      <BrowserRouter>
        <Suspense fallback={<LayoutSplashScreen />}>
          <Routes>
            <Route path={`${process.env.PUBLIC_URL}/auth`} element={!isAuthorized ? <AuthLayout /> : <Navigate from={`${process.env.PUBLIC_URL}/auth`} to={`${process.env.PUBLIC_URL}`} />}>
              <Route index element={<Navigate to={`${process.env.PUBLIC_URL}/auth/login`} />} />
              <Route path={`${process.env.PUBLIC_URL}/auth/register`} element={<Register />} />
              <Route path={`${process.env.PUBLIC_URL}/auth/verify/:token`} element={<VerifyUser />} />
              <Route path={`${process.env.PUBLIC_URL}/auth/initial-change-password/:id/:token`} element={<InitialChangePassword />} />
              <Route path={`${process.env.PUBLIC_URL}/auth/login`} element={<Login />} />
              <Route path={`${process.env.PUBLIC_URL}/auth/forgot-password`} element={<ForgotPassword />} />
              <Route path={`${process.env.PUBLIC_URL}/auth/reset-password/:token`} element={<ResetPassword />} />
              <Route path={`${process.env.PUBLIC_URL}/auth/verify-email/:token`} element={<VerifyEmail />} />
              <Route path={`*`} element={<PageNotFound />} />
            </Route>

            <Route path={`${process.env.PUBLIC_URL}/logout`} element={<Logout />} />

            <Route path={`${process.env.PUBLIC_URL}/`} element={!isAuthorized ? <Navigate from={`${process.env.PUBLIC_URL}/`} to={`${process.env.PUBLIC_URL}/auth/login`} /> : <Layout />}>
              <Route index element={<Navigate to={`${process.env.PUBLIC_URL}/dashboard`} />} />
              <Route path={`${process.env.PUBLIC_URL}/dashboard`} element={<Dashboard />} />
              <Route path={`${process.env.PUBLIC_URL}/shape`} element={<Outlet />}>
                <Route index element={<ShapeList />} />
                <Route path={`${process.env.PUBLIC_URL}/shape/add`} element={<EditShape />} />
                <Route path={`${process.env.PUBLIC_URL}/shape/edit/:id`} element={<EditShape />} />
              </Route>
              <Route path={`${process.env.PUBLIC_URL}/realistic`} element={<Outlet />}>
                <Route index element={<RealisticList />} />
                <Route path={`${process.env.PUBLIC_URL}/realistic/add`} element={<EditRealistic />} />
                <Route path={`${process.env.PUBLIC_URL}/realistic/edit/:id`} element={<EditRealistic />} />
              </Route>
              <Route path={`${process.env.PUBLIC_URL}/matterport`} element={<Outlet />}>
                <Route index element={<MatterportList />} />
                <Route path={`${process.env.PUBLIC_URL}/matterport/add`} element={<EditMatterport />} />
                <Route path={`${process.env.PUBLIC_URL}/matterport/edit/:id`} element={<EditMatterport />} />
              </Route>
              <Route path={`${process.env.PUBLIC_URL}/user`} element={<Outlet />}>
                <Route index element={<UserList />} />
                <Route path={`${process.env.PUBLIC_URL}/user/add`} element={<EditUser />} />
                <Route path={`${process.env.PUBLIC_URL}/user/edit/:id`} element={<EditUser />} />
              </Route>
              {/* <Route path={`${process.env.PUBLIC_URL}/profile`} element={<Profile />} /> */}
              <Route path={`*`} element={<PageNotFound />} />
            </Route>
            <Route path={`*`} element={<PageNotFound />} />
          </Routes>
        </Suspense>
      </BrowserRouter>

      <ToastWrapper
        position="top-right"
        autoClose={5000}
        hideProgressBar
        newestOnTop={false}
        closeOnClick
        rtl={false}
        pauseOnFocusLoss={false}
        draggable={false}
        pauseOnHover={false}
        enableMultiContainer
        containerId={"default"}
      />
    </>
  );
}
