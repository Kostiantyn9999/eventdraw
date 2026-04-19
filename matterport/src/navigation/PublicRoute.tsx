import React from "react";
import { Route } from "react-router-dom";
import _ from "lodash";
import qs from "qs";

type Props = {
  componentProps?: any;
  component: any;
  exact: boolean;
  path: string;
};

const PublicRoute = ({
  component: Component,
  componentProps,
  ...rest
}: Props) => {
  if (!_.isNil(Component)) {
    return (
      <Route
        {...rest}
        render={(props) => (
          <Component
            {...componentProps}
            {...props}
            {..._.get(props, "match.params", {})}
            {..._.get(props, "history.location.state", {})}
            {...qs.parse(_.get(props, "history.location.search"), {
              ignoreQueryPrefix: true,
            })}
          />
        )}
      />
    );
  } else {
    return <Route {...rest} />;
  }
};

export default PublicRoute;
