import React from "react";
import { Route, Redirect } from "react-router-dom";
import { connect } from "react-redux";
import _ from "lodash";

import routes from "./routes";
import { RootState } from "models/store";

type Props = {
  component: any;
  componentProps?: any;
  exact: boolean;
  path: string;
} & ReturnType<typeof mapStateToProps>;

const PrivateRoute = ({
  component: Component,
  componentProps,
  authorized,
  ...rest
}: Props) => {
  if (!authorized.auth) {
    return <Redirect to={routes.SIGN_IN} />;
  }

  return (
    <Route
      {...rest}
      render={(props) => (
        <Component
          {...componentProps}
          {...props}
          {..._.get(props, "match.params", {})}
          {..._.get(props, "history.location.state", {})}
        />
      )}
    />
  );
};

const mapStateToProps = (state: RootState) => {
  return {
    authorized: state.auth,
  };
};

export default connect(mapStateToProps)(PrivateRoute);
