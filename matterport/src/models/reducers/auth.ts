import { USER_ACTIONS } from "../actionTypes";
import { IAuth, IUser } from "types/user";
import _ from "lodash";

let user: IUser = null;
if (localStorage) {
  const d = localStorage.getItem("user");
  if (!_.isNil(d) && d !== "undefined") {
    user = JSON.parse(d);
  }
}

const initialState: IAuth = user
  ? { auth: true, user: user }
  : { auth: false, user: null };

export default function auth(
  state: IAuth = initialState,
  action: { type: USER_ACTIONS; user: IUser }
) {
  switch (action.type) {
    case USER_ACTIONS.USER_LOGIN_SUCCESSED:
      return {
        auth: true,
        user: action.user,
      };
    default:
      return state;
  }
}
