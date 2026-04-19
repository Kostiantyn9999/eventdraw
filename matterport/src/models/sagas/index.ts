import { all } from "redux-saga/effects";

// import matterportSagas from "./reducers/matterport";
import authSagas from "./reducers/auth";

export default function* rootSaga() {
  yield all([authSagas()]);
}
