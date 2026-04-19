import _ from "lodash";

import store from "models/store";
// import { receivedPlans } from "models/actions/planAction";
const testplans = [
  {
    sid: "tLkwcpyJ4vP",
    name: "Test Matterport",
    rangeDimension: {
      x: { min: -30, max: 0 },
      z: { min: -7, max: 7 },
    },
  },
  {
    sid: "11sAWCLGwSi",
    name: "Test venue",
    rangeDimension: {
      x: { min: -2.4, max: 9 },
      z: { min: 2, max: 19 },
    },
  },
];

export const fetchPlans = async () => {
  // store.dispatch(receivedPlans(_.keyBy(testplans, "sid")));
};
