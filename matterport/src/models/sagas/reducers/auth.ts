import {
  all,
  fork,
  call,
  put,
  takeEvery,
  takeLatest,
} from "redux-saga/effects";
// import { log } from '@common';
import { USER_ACTIONS } from "../../actionTypes";

import { login } from "src/controllers/auth";

import history from "src/navigation/history";
import routes from "src/navigation/routes";

type ILoginAction = {
  username: string;
  password: string;
};

export function* loginUser(action: ILoginAction) {
  const { username, password } = action;
  try {
    const res = yield call(login, ...[username, password]);
    localStorage.setItem("user", JSON.stringify(res));
    // console.log(localStorage.getItem("user"), JSON.stringify(res))
    history.push(routes.MATTERPORTS);
    yield put({ type: USER_ACTIONS.USER_LOGIN_SUCCESSED, user: res.data });
  } catch (e) {
    yield put({ type: USER_ACTIONS.USER_LOGIN_FAILED, message: e.message });
  }
}

// export function* fetchUser(action) {
//   try {
//     const user = yield call(login, ...[username, password]);
//     yield put({ type: types.USER_LOGIN_SUCCESSED, user: user.data });
//   } catch (e) {
//     yield put({ type: types.USER_LOGIN_FAILED, message: e.message });
//   }
// }

export function* loginUserRequest() {
  yield takeEvery(USER_ACTIONS.USER_LOGIN_REQUEST, loginUser);
}

export default function* rootSaga() {
  yield all([fork(loginUserRequest)]);
}
