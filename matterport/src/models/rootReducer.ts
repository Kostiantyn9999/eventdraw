import { combineReducers } from "redux";

import models from "./reducers/models";
import floor from "./reducers/floor";
import plans from "./reducers/plans";
import auth from "./reducers/auth";
import toast from "./reducers/toast";

export default combineReducers({ models, floor, plans, auth, toast });
