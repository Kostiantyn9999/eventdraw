import { combineReducers } from "redux";
import auth from "./auth/reducer";
import shape from "./shape/reducer";
import realistic from "./realistic/reducer";
import matterport from "./matterport/reducer";
import user from "./user/reducer";

const reducers = combineReducers({
  auth,
  user,
  shape,
  realistic,
  matterport,
});

export default reducers;
