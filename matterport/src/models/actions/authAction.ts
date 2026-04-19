import { USER_ACTIONS } from "../actionTypes";


export function sendLogin(username: string, password: string) {
  return {
    type: USER_ACTIONS.USER_LOGIN_REQUEST,
    username,
    password,
  };
}
