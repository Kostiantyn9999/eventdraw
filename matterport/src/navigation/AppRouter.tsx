import React from "react";
import { Route, Router, Switch } from "react-router-dom";

import history from "./history";

import routes from "./routes";

// pages
import { Login, MainPage, PlanList, RegisterMatterport } from "../pages";

import PublicRoute from "./PublicRoute";
import PrivateRoute from "./PrivateRoutes";

import Layout from "src/layout/Layout";

const AppRouter = ({}) => {
  return (
    <Router history={history}>
      <Switch>
        <Route path="/">
          {(props) => (
            <Layout>
              <Switch>
                <PublicRoute exact path={routes.SIGN_IN} component={Login} />
                <PrivateRoute
                  exact
                  path={routes.REGISTERMATTERPORT}
                  component={RegisterMatterport}
                />
                <PrivateRoute
                  exact
                  path={routes.EDITORMATTERPORT}
                  component={RegisterMatterport}
                />
                <PrivateRoute
                  exact
                  path={routes.MATTERPORTS}
                  component={PlanList}
                />
                <PublicRoute exact path={routes.APP} component={MainPage} />
              </Switch>
            </Layout>
          )}
        </Route>
      </Switch>
    </Router>
  );
};

export default AppRouter;
